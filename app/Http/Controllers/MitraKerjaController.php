<?php

namespace App\Http\Controllers;

use App\Models\BidangUsaha;
use App\Models\History;
use App\Models\MitraKerja;
use App\Models\PKWT;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MitraKerjaController extends Controller
{
    function viewMitraKerjaMain(Request $request)
    {
        // --- 1. CALCULATE STATS (Top Cards) ---
        $totalMitra = MitraKerja::count(); // total pekerja
        $mitraBaru = MitraKerja::where('created_at', '>=', now()->subMonth())->count(); // pekerja baru dari bulan lalu
        $mitraMendekati = MitraKerja::where('status_aktif', 1)
            ->whereNotNull('tgl_akhir_mou')
            ->whereBetween('tgl_akhir_mou', [
                now(),
                now()->addDays(30)
            ])
            ->count();
        $tidakAktif = MitraKerja::where('status_aktif', '!=', '1')->count(); // pekerja tidak aktif

        // --- 2. BUILD QUERY ---
        $query = MitraKerja::query();

        // A. Filter by Search (Name, NIK, KPJ)
        // We check for 'search' (from new JS) or 'q' (fallback)
        $search = $request->input('search') ?? $request->input('q');

        $query->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('nama_mitra', 'LIKE', "%{$search}%"); // Ensure column name is 'no_kpj' or 'kpj' based on your DB
            });
        });

        // B. Filter by Status (Exact Match)
        // We use $request->filled() to ensure we don't filter if value is empty/null
        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status_aktif', $request->status);
        });

        // C. Filter by Date Range (Tanggal Bergabung)
        $query->when($request->start_date, function ($q) use ($request) {
            $q->whereDate('tgl_akhir_mou', '>=', $request->start_date);
        });

        $query->when($request->end_date, function ($q) use ($request) {
            $q->whereDate('tgl_akhir_mou', '<=', $request->end_date);
        });

        // --- 3. FETCH DATA ---
        $mitraKerja = $query->withCount('units')
        ->orderBy('created_at', 'desc')
                        ->paginate(10)
                        ->withQueryString();

        // --- 4. RETURN RESPONSE ---

        // If AJAX request (from the search/filter script), return ONLY the table partial
        if ($request->ajax()) {
            return view('Mitra Kerja.partials.mitra-kerja-table', compact('mitraKerja'))->render();
        }

        // Otherwise return the full page
        return view('Mitra Kerja.main-mitra-kerja', compact('mitraKerja', 'totalMitra', 'mitraBaru', 'mitraMendekati', 'tidakAktif'));
    }

    function viewTambahMitraKerja()
    {
        $bidangUsahaList = BidangUsaha::select('id as val', 'nama as label')->get();

        return view('Mitra Kerja.CRUD.tambah-mitra-kerja', compact('bidangUsahaList'));
    }

    function viewDetailMitraKerja($id)
    {
        $mitraKerja = MitraKerja::where('id', $id)->first();

        $mitraKerja->image_base64 = 'data:image/jpeg;base64,' . base64_encode($mitraKerja->image_blob);

        $historiMitra = History::where('foreign_id', $id)->where('nama_tabel', 'pekerja')->get();

        // dd($mitraKerja);

        return view('Mitra Kerja.detail-mitra-kerja', compact('mitraKerja', 'historiMitra'));
    }

    function tambahMitraKerja(request $request)
    {
        // dd($request->all());
        try {
            $request->validate(
                [
                    'nama_mitra' => 'required|string|max:255',
                    'bidang_usaha_id' => 'required|integer',
                    'pimpinan' => 'required|string|max:255',
                    'telp_perusahaan' => 'required|string|max:20',
                    'status_pajak' => 'required|string',
                    'kota' => 'required|string',
                    'alamat' => 'required|string',
                    'tgl_mulai_kerjasama' => 'required|date',
                    'tgl_akhir_mou' => 'nullable|date|after_or_equal:tgl_mulai_kerjasama',
                    'status_mou' => 'required|string',
                    'foto' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
                ],
                [
                    'nama_mitra.required' => 'Nama mitra wajib diisi',
                    'kota.required' => 'Kota wajib diisi',
                    'foto.image' => 'foto harus berupa gambar',
                    'foto.max' => 'Ukuran foto maksimal 2MB',
                ],
            );

            // dd($request->all());

            // ✅ Upload foto
            $fotoBlob = null;
            if ($request->hasFile('foto')) {
                $fotoBlob = file_get_contents($request->file('foto')->getRealPath());
            }

            // ✅ Simpan ke database
            $mitraKerja = MitraKerja::create([
                'nama_mitra' => $request->nama_mitra,
                'bidang_usaha_id' => $request->bidang_usaha_id,
                'pimpinan' => $request->pimpinan,
                'telp_perusahaan' => $request->telp_perusahaan,
                'status_pajak' => $request->status_pajak,
                'kota' => $request->kota,
                'alamat' => $request->alamat,
                'tgl_mulai_kerjasama' => $request->tgl_mulai_kerjasama,
                'tgl_akhir_mou' => $request->tgl_akhir_mou,
                'status_mou' => $request->status_mou,

                'foto' => $fotoBlob,

                'status_aktif' => 1,
            ]);

            return redirect()
                ->route('view.tambah.mitra-kerja')
                ->with('success', 'Data Mitra Kerja ' . $mitraKerja->nama_mitra . ' berhasil ditambahkan.');
        } catch (QueryException $e) {
            // Tangani error database dan kirim ke front-end melalui session error
            return back()
                ->withInput()
                ->withErrors(['database' => $e->getMessage()]);
        } catch (\Exception $e) {
            // Tangani error umum dan kirim ke front-end melalui session error
            return back()
                ->withInput()
                ->withErrors(['general' => $e->getMessage()]);
        }
    }

    function ubahMitraKerja(Request $request, $id)
    {
        $mitraKerja = MitraKerja::findOrFail($id);

        $bidangUsahaList = BidangUsaha::select('id', 'nama')->orderBy('nama')->get()->map(
            fn($b) => [
                'val' => $b->id,
                'label' => $b->nama,
            ],
        );

        return view('Mitra Kerja.CRUD.ubah-mitra-kerja', compact('mitraKerja', 'bidangUsahaList'));
    }

    function updateMitraKerja(Request $request, $id)
    {
        // dd($request->all());

        $mitraKerja = MitraKerja::findOrFail($id);

        $request->validate(
            [
                'nama_mitra' => 'required|string|max:255',
                'bidang_usaha_id' => 'required|integer',
                'pimpinan' => 'required|string|max:255',
                'telp_perusahaan' => 'required|string|max:20',
                'status_pajak' => 'required|string',
                'kota' => 'required|string',
                'alamat' => 'required|string',
                'tgl_mulai_kerjasama' => 'required|date',
                'tgl_akhir_mou' => 'nullable|date|after_or_equal:tgl_mulai_kerjasama',
                'status_mou' => 'required|string',
                'foto' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            ],
            [
                'nama_mitra.required' => 'Nama mitra wajib diisi',
                'kota.required' => 'Kota wajib diisi',
                'foto.image' => 'foto harus berupa gambar',
                'foto.max' => 'Ukuran foto maksimal 2MB',
            ],
        );

        // dd($request->all());
        try {
            $data = $request->except('foto', '_token', '_method');

            if ($request->remove_foto == '1') {
                $mitraKerja->foto = null;
            }

            // ✅ JIKA FOTO DIGANTI
            if ($request->hasFile('foto')) {
                $foto = file_get_contents($request->file('foto')->getRealPath());
                $mitraKerja->foto = $foto;
            }

            // ✅ UPDATE DATA
            $mitraKerja->update($data);

            // ✅ UPDATE DATA
            History::create([
                'foreign_id' => $mitraKerja->id,
                'nama_tabel' => 'mitra kerja', // konsisten
                'updated_by' => auth()->id() ?? 0,
                'jabatan' => optional(auth()->user()->staff)->jabatan ?? 'system',
                'when' => now(),
            ]);

            return redirect()
                ->route('view.detail.mitra-kerja', $id)
                ->with('success', 'Data Mitra Kerja ' . $mitraKerja->nama_mitra . ' berhasil diperbarui.');
        } catch (QueryException $e) {
            // Tangani error database dan kirim ke front-end melalui session error
            return back()
                ->withInput()
                ->withErrors(['database' => $e->getMessage()]);
        } catch (\Exception $e) {
            // Tangani error umum dan kirim ke front-end melalui session error
            return back()
                ->withInput()
                ->withErrors(['general' => $e->getMessage()]);
        }
    }

    public function toggleStatus($id)
    {
        $mitraKerja = MitraKerja::findOrFail($id);

        $mitraKerja->status_aktif = !$mitraKerja->status_aktif;
        $mitraKerja->save();

        return response()->json([
            'message' => $mitraKerja->status_aktif
                ? 'Mitra Kerja berhasil diaktifkan'
                : 'Mitra Kerja berhasil dinonaktifkan'
        ]);
    }
}
