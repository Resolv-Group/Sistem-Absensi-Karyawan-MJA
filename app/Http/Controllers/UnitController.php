<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use App\Models\Unit;
use App\Models\MitraKerja;

class UnitController extends Controller
{

    public function viewTambahUnit()
    {
        $picList = Staff::select('id as val', 'nama as label')->get();

        $mitraKerjaList = MitraKerja::select('id as val', 'nama_mitra as label')->get();

        return view('Unit.CRUD.tambah-unit', compact('picList', 'mitraKerjaList'));
    }
    
    function tambahUnit(Request $request)
    {
        try {
            $request->validate(
                [
                    'id_unit' => 'nullable|string|max:255',
                    'id_mitra_kerja' => 'required|char',
                    'mulai_perjanjian' => 'required|date',
                    'akhir_perjanjian' => 'required|date|after_or_equal:mulai_perjanjian',
                    'nama_unit' => 'required|char',
                    'dokumen_mou' => 'nullable|image|mimes:png,jpg,jpeg,pdf|max:2048',
                    'persentase_management_fee' => 'required|int',
                    'sistem_pengajian' => 'required|int',
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
                ],
            );

            // dd($request->all());

            // ✅ Upload dokumen
            $dokumen = null;
            if ($request->hasFile('dokumen_mou')) {
                $dokumen = file_get_contents($request->file('dokumen')->getRealPath());
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
}