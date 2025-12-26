<?php

namespace App\Http\Controllers;

use App\Models\Borongan;
use App\Models\Kategori;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BoronganController extends Controller
{
    function viewTambahBorongan($id_unit)
    {
        // Assuming you have Unit and Pekerja models
        $units = \App\Models\Unit::select('id', 'nama_unit as nama')->get();
        $unitSelected = Unit::with('namaMitra')->where('id', $id_unit)->firstOrFail();
        $kategoriList = Kategori::select('id', 'nama')->get();

        return view('Unit.CRUD.tambah-unit-borongan', compact('unitSelected', 'units', 'kategoriList'));
    }

    function tambahBoronganUnit(Request $request)
    {
        // dd($request->all()); // aktifkan hanya untuk debug

        try {
            DB::beginTransaction();

            // ✅ VALIDASI SESUAI ARRAY
            $request->validate(
                [
                    'id_unit' => 'required|string',

                    'borongan' => 'required|array|min:1',

                    'borongan.*.harga_unit' => 'required|integer',
                    'borongan.*.harga_pekerja' => 'required|integer',

                    'borongan.*.kategori' => 'required|integer',
                    'borongan.*.nama_item' => 'required|string',

                    'borongan.*.satuan' => 'required|integer',
                ],
                [
                    'id_unit.required' => 'ID Unit wajib diisi',
                    'borongan.required' => 'Data borongan wajib diisi',
                    'borongan.*.harga_unit.required' => 'Harga Unit wajib dipilih',
                    'borongan.*.harga_pekerja.required' => 'Harga Pekerja wajib dipilih',
                    'borongan.*.kategori.required' => 'Kategori wajib dipilih',
                    'borongan.*.nama_item.required' => 'Nama Item wajib diisi',
                    'borongan.*.satuan.required' => 'Satuan wajib diisi',
                ],
            );

            // ✅ LOOP PEKERJA
            foreach ($request->borongan as $index => $data) {
                Borongan::create([
                    'id_unit' => $request->id_unit,
                    'harga_unit' => $data['harga_unit'],
                    'harga_pekerja' => $data['harga_pekerja'],
                    'kategori' => $data['kategori'],
                    'nama_item' => $data['nama_item'],
                    'satuan' => $data['satuan'],
                    'status_aktif' => 1,
                ]);
            }

            DB::commit();

            return redirect()->route('view.detail.unit', $request->id_unit)->with('success', 'Borongan berhasil ditambahkan ke unit.');
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

    function ubahUnitBorongan(Request $request, $unitId, $boronganId)
    {
        $unitSelected = Unit::with('namaMitra')->findOrFail($unitId);

        $borongan = Borongan::with('kategoriRel')->where('id', $boronganId)->where('id_unit', $unitId)->firstOrFail();

        $kategoriList = Kategori::select('id', 'nama')->get();

        return view('Unit.CRUD.ubah-unit-borongan', compact('unitSelected', 'borongan', 'kategoriList'));
    }

    public function updateUnitBorongan(Request $request, $unitId, $boronganId)
    {
        DB::beginTransaction();

        try {
            // ========================
            // 1. VALIDASI
            // ========================
            $validated = $request->validate([
                'borongan.0.nama_item'     => 'required|string|max:255',
                'borongan.0.kategori'      => 'required|exists:kategori,id',
                'borongan.0.harga_unit'    => 'required|numeric|min:0',
                'borongan.0.harga_pekerja' => 'required|numeric|min:0',
                'borongan.0.satuan'        => 'required|string|max:10',
            ]);

            $data = $validated['borongan'][0];

            // ========================
            // 2. UPDATE (ID DARI ROUTE)
            // ========================
            $updated = DB::table('borongan')
                ->where('id', $boronganId)
                ->where('id_unit', $unitId) // 🔐 safety
                ->update([
                    'nama_item'     => $data['nama_item'],
                    'kategori'      => $data['kategori'],
                    'harga_unit'    => $data['harga_unit'],
                    'harga_pekerja' => $data['harga_pekerja'],
                    'satuan'        => $data['satuan'],
                    'updated_at'    => now(),
                ]);

            if ($updated === 0) {
                throw new \Exception('Data borongan tidak ditemukan atau tidak berubah');
            }

            DB::commit();

            return redirect()
                ->route('view.detail.unit', $unitId)
                ->with('success', 'Borongan berhasil diperbarui');

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Gagal update borongan unit', [
                'borongan_id' => $boronganId,
                'unit_id' => $unitId,
                'error' => $e->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat memperbarui borongan');
        }
    }
}
