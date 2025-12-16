<?php

namespace App\Http\Controllers;

use App\Models\BidangUsaha;
use App\Models\MitraKerja;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class MitraKerjaController extends Controller
{
    function viewMitraKerjaMain() {
        return view('Mitra Kerja.main-mitra-kerja');
    }

    function viewTambahMitraKerja() {

        $bidangUsahaList = BidangUsaha::select('id as val', 'nama as label')->get();

        return view('Mitra Kerja.CRUD.tambah-mitra-kerja', compact('bidangUsahaList'));
    }

    function viewDetailMitraKerja(Request $request) {
        return view('Mitra Kerja.detail-mitra-kerja');
    }

    function tambahMitraKerja(Request $request) {
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

            $data = $request->except('foto');

            // =========================
            // UPLOAD foto
            // =========================
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = 'foto_mitra_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('mitra-foto', $filename, 'public');

                $data['foto'] = $path; // simpan path
            }

            $data['status_aktif'] = 1;

            MitraKerja::create($data);

            return redirect()
                ->route('view.tambah.mitra-kerja')
                ->with('success', 'Mitra kerja berhasil ditambahkan');

            } catch (QueryException $e) {
            // If the DB fails, we catch it here so Laravel doesn't try to render the blob
            // We only return the text message, not the binary data
            dd('Database Error Occurred:', $e->getMessage());
        } catch (\Exception $e) {
            dd('General Error:', $e->getMessage());
        }
    }
    

    function ubahMitraKerja(Request $request) {

    }

    function updateMitraKerja(Request $request) {

    }
}
