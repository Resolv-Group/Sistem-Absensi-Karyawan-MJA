<?php

namespace App\Http\Controllers;

use App\Models\BidangUsaha;
use App\Models\History;
use App\Models\MitraKerja;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class MitraKerjaController extends Controller
{
    function viewMitraKerjaMain(Request $request) {

        $totalMitra = MitraKerja::count(); // total pekerja
        $mitraBaru = MitraKerja::where('created_at', '>=', now()->subMonth())->count(); // pekerja baru dari bulan lalu
        $tidakAktif = MitraKerja::where('status_aktif', '!=', '1')->count(); // pekerja tidak aktif

        // 1. Capture the search query
        $q = $request->input('q');

        // 2. Query the database with the filter
        $mitraKerja = MitraKerja::when($q, function ($query) use ($q) {
            $query->where('nama_mitra', 'LIKE', "%$q%");
        })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); // Keeps the search term in the pagination links

        // 3. If it's an AJAX request (from JS), return ONLY the table partial
        if ($request->ajax()) {
            return view('Mitra Kerja.partials.mitra-kerja-table', compact('mitraKerja'))->render();
        }

        // 4. Otherwise, return the full page (header, sidebar, etc)
        return view('Mitra Kerja.main-mitra-kerja', compact('mitraKerja', 'totalMitra', 'mitraBaru', 'tidakAktif'));
    }

    function viewTambahMitraKerja() {

        $bidangUsahaList = BidangUsaha::select('id as val', 'nama as label')->get();

        return view('Mitra Kerja.CRUD.tambah-mitra-kerja', compact('bidangUsahaList'));
    }

    function viewDetailMitraKerja($id) {

        $mitraKerja = MitraKerja::where('id', $id)->first();

        $mitraKerja->image_base64 = 'data:image/jpeg;base64,' . base64_encode($mitraKerja->image_blob);

        $historiMitra = History::where('foreign_id', $id)->where('nama_tabel', 'pekerja')->get();

        return view('Mitra Kerja.detail-mitra-kerja', compact('mitraKerja', 'historiMitra'));
    }

    function tambahMitraKerja(request $request)
    {
        // dd($request->all());
        try {
            $request->validate([
                'nama_mitra'          => 'required|string|max:255',
                'bidang_usaha_id'     => 'required|integer',
                'pimpinan'            => 'required|string|max:255',
                'telp_perusahaan'     => 'required|string|max:20',
                'status_pajak'        => 'required|string',
                'alamat'              => 'required|string',
                'tgl_mulai_kerjasama' => 'required|date',
                'tgl_akhir_mou'       => 'nullable|date|after_or_equal:tgl_mulai_kerjasama',
                'status_mou'          => 'required|string',
                'foto'                => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            ], [
                'nama_mitra.required' => 'Nama mitra wajib diisi',
                'foto.image'          => 'foto harus berupa gambar',
                'foto.max'            => 'Ukuran foto maksimal 2MB',
            ]);

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


    function ubahMitraKerja(Request $request, $id) {

        $mitraKerja = MitraKerja::findOrFail($id);

        $bidangUsahaList = BidangUsaha::select('id', 'nama')
            ->orderBy('nama')
            ->get()
            ->map(fn ($b) => [
                'val'   => $b->id,
                'label' => $b->nama,
            ]);

        return view('Mitra Kerja.CRUD.ubah-mitra-kerja', compact('mitraKerja', 'bidangUsahaList'));
    }

    function updateMitraKerja(Request $request, $id) {
        // dd($request->all());

        $mitraKerja = MitraKerja::findOrFail($id);


            $request->validate([
                'nama_mitra'          => 'required|string|max:255',
                'bidang_usaha_id'     => 'required|integer',
                'pimpinan'            => 'required|string|max:255',
                'telp_perusahaan'     => 'required|string|max:20',
                'status_pajak'        => 'required|string',
                'alamat'              => 'required|string',
                'tgl_mulai_kerjasama' => 'required|date',
                'tgl_akhir_mou'       => 'nullable|date|after_or_equal:tgl_mulai_kerjasama',
                'status_mou'          => 'required|string',
                'foto'                => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
            ], [
                'nama_mitra.required' => 'Nama mitra wajib diisi',
                'foto.image'          => 'foto harus berupa gambar',
                'foto.max'            => 'Ukuran foto maksimal 2MB',
            ]);

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
}
