<?php

namespace App\Http\Controllers;

use App\Models\Borongan;
use App\Models\Divisi;
use App\Models\History;
use App\Models\JabatanPKWT;
use App\Models\Kategori;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Models\Staff;
use Illuminate\Database\QueryException;
use App\Models\MitraKerja;
use App\Models\Pekerja;
use App\Models\PicUnit;
use App\Models\PKWT;
use Illuminate\Support\Facades\DB;

class UnitController extends Controller
{
    function viewUnitMain(Request $request)
    {
        // --- 1. CALCULATE STATS (Top Cards) ---
        $totalUnit = Unit::count(); // total pekerja
        $unitBaru = Unit::where('created_at', '>=', now()->subMonth())->count(); // pekerja baru dari bulan lalu
        $tidakAktif = Unit::where('status_aktif', '!=', '1')->count(); // pekerja tidak aktif
        $totalHarian = Unit::where('sistem_pengajian', 1)->count();
        $totalBorongan = Unit::where('sistem_pengajian', 2)->count();
        
        // --- 2. BUILD QUERY ---
        $query = Unit::query()
            ->with(['picUnit.staff', 'namaMitra'])
            ->withCount('pkwt');

        // A. Filter by Search (Name, NIK, KPJ)
        // We check for 'search' (from new JS) or 'q' (fallback)
        $search = $request->input('search') ?? $request->input('q');

        $query->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('nama_unit', 'LIKE', "%{$search}%");
            });
        });

        // B. Filter by Status (Exact Match)
        // We use $request->filled() to ensure we don't filter if value is empty/null
        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status_aktif', $request->status);
        });

        $query->when($request->filled('pengajian'), function ($q) use ($request) {
            $q->where('sistem_pengajian', $request->pengajian);
        });

        // C. Filter by Date Range (Tanggal Bergabung)
        $query->when($request->start_date, function ($q) use ($request) {
            $q->whereDate('mulai_perjanjian', '>=', $request->start_date);
        });

        $query->when($request->end_date, function ($q) use ($request) {
            $q->whereDate('akhir_perjanjian', '<=', $request->end_date);
        });

        // --- 3. FETCH DATA ---
        $unit = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // --- 4. RETURN RESPONSE ---

        // If AJAX request (from the search/filter script), return ONLY the table partial
        if ($request->ajax()) {
            return view('Unit.partials.unit-table', compact('unit'))->render();
        }

        // Otherwise return the full page
        return view('Unit.main-unit', compact('unit', 'totalUnit', 'unitBaru', 'totalHarian', 'totalBorongan', 'tidakAktif'));
    }

    public function viewTambahUnit()
    {
        $picList = Staff::select('id as val', 'nama as label')->where('jabatan', 'PIC')->get();

        $mitraKerjaList = MitraKerja::select('id as val', 'nama_mitra as label')->get();

        return view('Unit.CRUD.tambah-unit', compact('picList', 'mitraKerjaList'));
    }

    function tambahUnit(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate(
                [
                    'id_unit' => 'nullable|string|max:255',
                    'id_mitra_kerja' => 'required|string',
                    'mulai_perjanjian' => 'required|date',
                    'akhir_perjanjian' => 'required|date|after_or_equal:mulai_perjanjian',
                    'nama_unit' => 'required|string',
                    'dokumen_mou' => 'file|mimes:png,jpg,jpeg,pdf|max:2048',
                    'persentase_management_fee' => 'required|int',
                    'sistem_pengajian' => 'required|int',

                    'pic_ids' => 'required|array|min:1',
                    'pic_ids.*' => 'exists:staff,id',
                ],
                [
                    'id_unit.required' => 'ID Unit wajib diisi',
                    'id_mitra_kerja.required' => 'ID Mitra Kerja wajib diisi',

                    'mulai_perjanjian.required' => 'Tanggal mulai perjanjian wajib diisi',
                    'akhir_perjanjian.after_or_equal' => 'Tanggal akhir harus setelah tanggal mulai',

                    'nama_unit.required' => 'Nama unit wajib diisi',

                    'dokumen_mou.mimes' => 'Dokumen MOU harus berupa PDF atau gambar',
                    'dokumen_mou.max' => 'Ukuran dokumen maksimal 2MB',

                    'persentase_management_fee.required' => 'Persentase management fee wajib diisi',
                    'persentase_management_fee.max' => 'Persentase maksimal 100%',

                    'sistem_pengajian.required' => 'Sistem pengajian wajib dipilih',

                    'pic_ids.required' => 'PIC wajib dipilih minimal 1',
                ],
            );

            // dd($request->all());

            // ✅ Upload dokumen
            $dokumen = null;
            if ($request->hasFile('dokumen_mou')) {
                $dokumen = file_get_contents($request->file('dokumen_mou')->getRealPath());
            }

            // ✅ Simpan ke database
            $unit = Unit::create([
                'id_unit' => $request->id_unit,
                'id_mitra_kerja' => $request->id_mitra_kerja,
                'mulai_perjanjian' => $request->mulai_perjanjian,
                'akhir_perjanjian' => $request->akhir_perjanjian,
                'nama_unit' => $request->nama_unit,
                'persentase_management_fee' => $request->persentase_management_fee,
                'sistem_pengajian' => $request->sistem_pengajian,

                'dokumen_mou' => $dokumen,

                'status_aktif' => 1,
            ]);

            // dd($unit);
            $picIds = $request->pic_ids;

            foreach ($picIds as $picId) {
                DB::table('pic_unit')->insert([
                    'id_unit' => $unit->id, // atau $unit->id_unit
                    'id_pic' => $picId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return redirect()
                ->route('view.tambah.unit')
                ->with('success', 'Data Unit ' . $unit->nama_mitra . ' berhasil ditambahkan.');
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

    public function viewDetailUnit(Request $request, $id)
    {
        $unit = Unit::with(['picUnit.staff', 'namaMitra'])->findOrFail($id);

        if ($request->ajax()) {
            // --- HANDLE BORONGAN AJAX ---
            if ($request->target === 'borongan') {
                // 1. Lock the preview set to the latest 5 IDs only
                $latestIds = Borongan::where('id_unit', $id)->latest()->limit(5)->pluck('id');

                // 2. Start query strictly within those IDs
                $query = Borongan::with('kategoriRel')->whereIn('id', $latestIds);

                // 3. Apply search filters INSIDE the boundary
                if ($request->filled('search')) {
                    $query->where('nama_item', 'like', "%{$request->search}%");
                }
                if ($request->filled('kategori')) {
                    $query->where('kategori', $request->kategori);
                }
                if ($request->filled('status')) {
                    $query->where('status_aktif', $request->status);
                }

                $borongan = $query->get();
                return view('Unit.partials.borongan-table', compact('borongan', 'unit'))->render();
            }

            // --- HANDLE HARIAN (PKWT) AJAX ---
            // 1. Lock the preview set to the latest 5 IDs only
            $latestIds = PKWT::where('id_unit', $id)->latest()->limit(5)->pluck('id');

            // 2. Start query strictly within those IDs
            $query = PKWT::with(['pekerja', 'jabatan', 'divisi'])->whereIn('id', $latestIds);

            // 3. Apply search filters INSIDE the boundary
            if ($request->filled('search')) {
                $search = $request->search;
                // We use a nested where function so the 'OR' doesn't break the 'whereIn'
                $query->where(function ($q) use ($search) {
                    $q->whereHas('pekerja', function ($sq) use ($search) {
                        $sq->where('nama', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%");
                    });
                });
            }

            if ($request->filled('divisi')) {
                $query->where('divisi_id', $request->divisi);
            }
            if ($request->filled('jabatan')) {
                $query->where('jabatan_pkwt_id', $request->jabatan);
            }
            if ($request->filled('status')) {
                $query->where('status_aktif', $request->status);
            }

            $pkwtPekerja = $query->get();
            return view('Unit.partials.harian-table', compact('pkwtPekerja', 'unit'))->render();
        }

        // --- NORMAL PAGE LOAD (Latest 5 only) ---
        $historiUnit = History::where('foreign_id', $id)->where('nama_tabel', 'unit')->get();
        $pekerja = Pekerja::all();

        $pkwtPekerja = PKWT::with(['pekerja', 'jabatan', 'divisi'])
            ->where('id_unit', $id)
            ->latest()
            ->limit(5)
            ->get();

        $borongan = Borongan::with('kategoriRel')->where('id_unit', $id)->latest()->limit(5)->get();

        $divisions = Divisi::all();
        $boronganKategori = Kategori::all();
        $jabatan = JabatanPKWT::all();

        return view('Unit.detail-unit', compact('unit', 'historiUnit', 'pekerja', 'pkwtPekerja', 'borongan', 'divisions', 'boronganKategori', 'jabatan'));
    }

    public function showDokumenMOU($id, Request $request)
    {
        // 1. Find the record
        $data = Unit::findOrFail($id);

        // 2. Check if blob exists
        if (!$data->dokumen_mou) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        // 3. Detect the MIME type (PDF, JPG, PNG) from the binary data
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($data->dokumen_mou);

        // 4. Determine if it's "View" (inline) or "Download" (attachment)
        // If URL has ?download=true, we force download.
        $disposition = $request->has('download') ? 'attachment' : 'inline';

        // Generate a filename
        $filename = 'dokumen-mitra-' . $id;

        // 5. Return the binary data as a proper HTTP response
        return response($data->dokumen_mou)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', $disposition . '; filename="' . $filename . '"');
    }

    public function showDokumenPKWT($id, Request $request)
    {
        $data = PKWT::findOrFail($id);

        if (!$data->dokumen_pkwt) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        // PAKAI MIME YANG DISIMPAN (LEBIH CEPAT & AMAN)
        $mimeType = $data->dokumen_mime ?? 'application/octet-stream';

        $disposition = $request->has('download') ? 'attachment' : 'inline';

        $filename = 'dokumen-pkwt-' . $id;

        return response($data->dokumen_pkwt)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', $disposition . '; filename="' . $filename . '"');
    }

    function ubahUnit(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $mitraKerjaList = MitraKerja::select('id as val', 'nama_mitra as label')->get();

        $selectedPicIds = $unit->picUnit?->pluck('id_pic')->toArray() ?? [];

        $picList = Staff::select('id as val', 'nama as label')->where('jabatan', 'PIC')->get();

        return view('Unit.CRUD.ubah-unit', compact('unit', 'mitraKerjaList', 'selectedPicIds', 'picList'));
    }

    function updateUnit(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();

            // ===============================
            // VALIDATION
            // ===============================
            $validated = $request->validate(
                [
                    'id_mitra_kerja' => 'required|exists:mitra_kerja,id',
                    'nama_unit' => 'required|string|max:255',
                    'sistem_pengajian' => 'required|in:1,2',
                    'persentase_management_fee' => 'required|numeric|min:0|max:100',

                    'mulai_perjanjian' => 'required|date',
                    'akhir_perjanjian' => 'required|date|after_or_equal:mulai_perjanjian',

                    'pic_ids' => 'required|array|min:1',
                    'pic_ids.*' => 'exists:staff,id',

                    // FILE OPTIONAL
                    'dokumen_mou' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
                ],
                [
                    'id_mitra_kerja.required' => 'Mitra kerja wajib dipilih.',
                    'nama_unit.required' => 'Nama unit wajib diisi.',
                    'pic_ids.required' => 'Minimal 1 PIC harus dipilih.',
                    'dokumen_mou.mimes' => 'Dokumen harus PDF / JPG / PNG.',
                    'dokumen_mou.max' => 'Ukuran dokumen maksimal 5MB.',
                ],
            );

            // ===============================
            // FIND UNIT
            // ===============================
            $unit = Unit::findOrFail($id);

            // ===============================
            // HANDLE FILE (OPTIONAL)
            // ===============================
            if ($request->hasFile('dokumen_mou')) {
                $unit->update([
                    'dokumen_mou' => file_get_contents($request->file('dokumen_mou')->getRealPath()),
                ]);
            }

            // ===============================
            // UPDATE UNIT DATA
            // ===============================
            $unit->update([
                'id_mitra_kerja' => $validated['id_mitra_kerja'],
                'nama_unit' => $validated['nama_unit'],
                'sistem_pengajian' => $validated['sistem_pengajian'],
                'persentase_management_fee' => $validated['persentase_management_fee'],
                'mulai_perjanjian' => $validated['mulai_perjanjian'],
                'akhir_perjanjian' => $validated['akhir_perjanjian'],
            ]);

            // ===============================
            // SYNC PIC
            // ===============================
            // Hapus PIC lama
            PicUnit::where('id_unit', $unit->id)->delete();

            // Insert PIC baru
            foreach ($validated['pic_ids'] as $picId) {
                PicUnit::create([
                    'id_unit' => $unit->id,
                    'id_pic' => $picId,
                ]);
            }

            // dd($unit);
            DB::commit();

            return redirect()->route('view.unit')->with('success', 'Data unit berhasil diperbarui.');
        } catch (QueryException $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors([
                    'database' => 'Terjadi kesalahan database: ' . $e->getMessage(),
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors([
                    'general' => 'Terjadi kesalahan sistem: ' . $e->getMessage(),
                ]);
        }
    }

    public function toggleStatus($id)
    {
        $unit = Unit::findOrFail($id);

        $unit->status_aktif = !$unit->status_aktif;
        $unit->save();

        return response()->json([
            'message' => $unit->status_aktif ? 'Unit berhasil diaktifkan' : 'Unit berhasil dinonaktifkan',
        ]);
    }
}
