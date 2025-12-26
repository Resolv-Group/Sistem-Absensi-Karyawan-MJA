<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\JabatanPKWT;
use App\Models\Pekerja;
use App\Models\PKWT;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PKWTController extends Controller
{
    function viewTambahUnitHarian($id_unit)
    {
        // Assuming you have Unit and Pekerja models
        $units = Unit::select('id', 'nama_unit as nama')->get();
        $unitSelected = Unit::with('namaMitra')->where('id', $id_unit)->firstOrFail();
        $pekerjaList = Pekerja::select('id', 'nama', 'nik')->where('status_aktif', 1)->get();
        $divisiList = Divisi::select('id', 'nama')->get();
        $jabatanList = JabatanPKWT::select('id', 'nama')->get();

        return view('Unit.CRUD.tambah-unit-pekerja', compact('unitSelected', 'units', 'pekerjaList', 'divisiList', 'jabatanList'));
    }

    function tambahPekerjaUnit(Request $request)
    {
        // dd($request->all()); // aktifkan hanya untuk debug

        try {
            DB::beginTransaction();

            // ✅ VALIDASI SESUAI ARRAY
            $request->validate(
                [
                    'id_unit' => 'required|string',

                    'pekerja' => 'required|array|min:1',

                    'pekerja.*.id_pekerja' => 'required|integer|exists:pekerja,id',
                    'pekerja.*.divisi_id' => 'required|string',
                    'pekerja.*.jabatan_id' => 'required|string',

                    'pekerja.*.tgl_mulai_pkwt' => 'required|date',
                    'pekerja.*.tgl_akhir_pkwt' => 'required|date|after_or_equal:pekerja.*.tgl_mulai_pkwt',

                    'pekerja.*.gaji_harian' => 'required|integer|min:0',

                    'pekerja.*.dokumen_pkwt' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:2048',
                ],
                [
                    'id_unit.required' => 'ID Unit wajib diisi',
                    'pekerja.required' => 'Data pekerja wajib diisi',
                    'pekerja.*.id_pekerja.required' => 'Pekerja wajib dipilih',
                    'pekerja.*.divisi_id.required' => 'Divisi wajib dipilih',
                    'pekerja.*.jabatan_id.required' => 'Jabatan wajib dipilih',
                    'pekerja.*.gaji_harian.required' => 'Gaji harian wajib diisi',
                ],
            );

            // ✅ LOOP PEKERJA
            foreach ($request->pekerja as $index => $data) {
                // Upload file per pekerja
                $dokumen = null;
                $dokumenMime = null;
                if ($request->hasFile("pekerja.$index.dokumen_pkwt")) {
                    $file = $request->file("pekerja.$index.dokumen_pkwt");

                    $dokumen = file_get_contents($file->getRealPath());
                    $dokumenMime = $file->getMimeType();
                }

                $pkwt = PKWT::create([
                    'id_unit' => $request->id_unit,
                    'id_pekerja' => $data['id_pekerja'],
                    'divisi_id' => $data['divisi_id'],
                    'jabatan_id' => $data['jabatan_id'],
                    'tgl_mulai_pkwt' => $data['tgl_mulai_pkwt'],
                    'tgl_akhir_pkwt' => $data['tgl_akhir_pkwt'],
                    'gaji_harian' => $data['gaji_harian'],
                    'dokumen_pkwt' => $dokumen,
                    'dokumen_mime' => $dokumenMime,
                    'status_aktif' => 1,
                ]);
            }

            DB::commit();

            return redirect()->route('view.detail.unit', $request->id_unit)->with('success', 'Pekerja berhasil ditambahkan ke unit.');
        } catch (QueryException $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['database' => $e->getMessage()]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['general' => $e->getMessage()]);
        }
    }

    function ubahUnitPekerja(Request $request, $unitId, $pekerjaId)
    {
        $unitSelected = Unit::with('namaMitra')->findOrFail($unitId);

        // 🔑 Ambil PKWT yang mau diedit
        $pkwt = PKWT::with(['pekerja', 'divisi', 'jabatan'])
            ->where('id', $pekerjaId)
            ->where('id_unit', $unitId)
            ->firstOrFail();

        // List dropdown
        $pekerjaList = Pekerja::select('id', 'nama', 'nik')->where('status_aktif', 1)->get();

        $divisiList = Divisi::select('id', 'nama')->get();
        $jabatanList = JabatanPKWT::select('id', 'nama')->get();

        return view('Unit.CRUD.ubah-unit-pekerja', compact('unitSelected', 'pkwt', 'pekerjaList', 'divisiList', 'jabatanList'));
    }

    public function updateUnitPekerja(Request $request, $unitId, $pkwtId)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'pekerja.*.id_pekerja' => 'required|exists:pekerja,id',
                'pekerja.*.gaji_harian' => 'required|numeric|min:0',
                'pekerja.*.divisi_id' => 'required|exists:divisi,id',
                'pekerja.*.jabatan_id' => 'required|exists:jabatan_pkwt,id',
                'pekerja.*.tgl_mulai_pkwt' => 'required|date',
                'pekerja.*.tgl_akhir_pkwt' => 'required|date|after_or_equal:pekerja.*.tgl_mulai_pkwt',
                'pekerja.*.dokumen_pkwt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);

            // Ambil PKWT
            $pkwt = PKWT::where('id', $pkwtId)->where('id_unit', $unitId)->firstOrFail();

            // Karena edit 1 PKWT → ambil row pertama
            $data = $request->pekerja[0];

            // Update field utama
            $pkwt->update([
                'id_pekerja' => $data['id_pekerja'],
                'gaji_harian' => $data['gaji_harian'],
                'divisi_id' => $data['divisi_id'],
                'jabatan_id' => $data['jabatan_id'],
                'tgl_mulai_pkwt' => $data['tgl_mulai_pkwt'],
                'tgl_akhir_pkwt' => $data['tgl_akhir_pkwt'],
            ]);

            // =============================
            // DOKUMEN PKWT (OPTIONAL)
            // =============================
            if (isset($data['dokumen_pkwt']) && $data['dokumen_pkwt'] instanceof \Illuminate\Http\UploadedFile) {
                $pkwt->dokumen_pkwt = file_get_contents($data['dokumen_pkwt']->getRealPath());
                $pkwt->dokumen_mime = $data['dokumen_pkwt']->getClientMimeType();
                $pkwt->save();
            }

            DB::commit();

            return redirect()->route('view.detail.unit', $unitId)->with('success', 'Data PKWT berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors([
                    'error' => $e->getMessage(),
                ]);
        }
    }
}
