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
    public function viewBoronganMain(Request $request, $id_unit)
    {
        $user = auth()->user(); // staff login

        // CEK PIC PUNYA UNIT INI ATAU TIDAK
        $isAllowed = Unit::where('id', $id_unit)
            ->whereHas('picUnit', function ($q) use ($user) {
                $q->where('id_pic', $user->id);
            })
            ->exists();

        if(in_array($user->role, ['admin', 'hrd']))
        {}
        elseif(! $isAllowed ) {
            abort(403, 'Anda tidak memiliki akses ke unit ini');
        }

        $unit = Unit::findOrFail($id_unit);

        // Base Query
        $query = Borongan::with('kategoriRel')->where('id_unit', $id_unit);

        // Live Search & Filter Logic
        if ($request->filled('search')) {
            $query->where('nama_item', 'like', "%{$request->search}%");
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('status')) {
            $query->where('status_aktif', $request->status);
        }

        $borongan = $query->latest()->paginate(3)->withQueryString();
        $boronganKategori = Kategori::all();

        // Jika Request AJAX (Live Update)
        if ($request->ajax()) {
            return view('Unit.partials.main-borongan-table', compact('borongan', 'unit'))->render();
        }

        return view('Unit.Pengajian.main-borongan', compact('borongan', 'unit', 'boronganKategori'));
    }

    function viewTambahBorongan($id_unit)
    {
        $user = auth()->user(); // staff login

        // CEK PIC PUNYA UNIT INI ATAU TIDAK
        $isAllowed = Unit::where('id', $id_unit)
            ->whereHas('picUnit', function ($q) use ($user) {
                $q->where('id_pic', $user->id);
            })
            ->exists();

        if(in_array($user->role, ['admin', 'hrd']))
        {}
        elseif(! $isAllowed ) {
            abort(403, 'Anda tidak memiliki akses ke unit ini');
        }


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

                    'borongan.*.harga_unit' => 'required',
                    'borongan.*.harga_pekerja' => 'required',

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
                'borongan.0.nama_item' => 'required|string|max:255',
                'borongan.0.kategori' => 'required|exists:kategori,id',
                'borongan.0.harga_unit' => 'required|numeric|min:0',
                'borongan.0.harga_pekerja' => 'required|numeric|min:0',
                'borongan.0.satuan' => 'required|string|max:10',
            ]);

            $data = $validated['borongan'][0];

            // ========================
            // 2. UPDATE (ID DARI ROUTE)
            // ========================
            $updated = DB::table('borongan')
                ->where('id', $boronganId)
                ->where('id_unit', $unitId) // 🔐 safety
                ->update([
                    'nama_item' => $data['nama_item'],
                    'kategori' => $data['kategori'],
                    'harga_unit' => $data['harga_unit'],
                    'harga_pekerja' => $data['harga_pekerja'],
                    'satuan' => $data['satuan'],
                    'updated_at' => now(),
                ]);

            if ($updated === 0) {
                throw new \Exception('Data borongan tidak ditemukan atau tidak berubah');
            }

            DB::commit();

            return redirect()->route('view.detail.unit', $unitId)->with('success', 'Borongan berhasil diperbarui');
        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Gagal update borongan unit', [
                'borongan_id' => $boronganId,
                'unit_id' => $unitId,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui borongan');
        }
    }

    public function bulkUpdateBorongan(Request $request)
    {
        try {
            DB::beginTransaction();

            $ids = json_decode($request->ids, true);
            $action = $request->action;

            if (!is_array($ids) || count($ids) === 0) {
                throw new \Exception('Tidak ada data borongan yang dipilih.');
            }

            switch ($action) {
                case 'update_category':
                    if (!$request->kategori_id) {
                        throw new \Exception('Kategori wajib dipilih.');
                    }

                    Borongan::whereIn('id', $ids)->update(['kategori' => $request->kategori_id]);
                    break;

                case 'update_status':
                    if (!isset($request->status)) {
                        throw new \Exception('Status wajib dipilih.');
                    }

                    Borongan::whereIn('id', $ids)->update(['status_aktif' => $request->status]);
                    break;

                case 'delete':
                    Borongan::whereIn('id', $ids)->delete();
                    break;

                default:
                    throw new \Exception('Aksi tidak valid.');
            }

            DB::commit();

            return back()->with('success', 'Data borongan berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->withErrors([
                    'error' => $e->getMessage(),
                ]);
        }
    }

    function bulkUpdateKategori(Request $request) {
        $ids = json_decode($request->ids);
        $kategoriId = $request->kategori;

        // Validate that IDs and Divisi exist
        Borongan::whereIn('id', $ids)->update([
            'kategori' => $kategoriId,
            // You can handle 'apply_immediately' logic here if needed
        ]);

        return back()->with('success', count($ids) . ' kategori berhasil diperbarui');
    }

    function bulkUpdateStatus(Request $request)
    {
        // 1. Validasi input
        $request->validate([
            'ids' => 'required', // String JSON dari Alpine.js
            'action' => 'required|string',
            'status' => 'required_if:action,update_status|in:0,1',
            'reason' => 'nullable|string|max:500',
        ]);

        // 2. Decode IDs dari string JSON menjadi array PHP
        $ids = json_decode($request->ids);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada item yang dipilih.');
        }

        // 3. Eksekusi berdasarkan Action
        try {
            DB::beginTransaction();

            if ($request->action === 'update_status') {
                $statusLabel = $request->status == '1' ? 'Aktif' : 'Nonaktif';

                // Update massal menggunakan whereIn
                Borongan::whereIn('id', $ids)->update([
                    'status_aktif' => $request->status,
                    // Jika Anda punya kolom 'keterangan' atau 'log_perubahan'
                    'updated_at' => now(),
                ]);

                $message = "Berhasil mengubah " . count($ids) . " item menjadi $statusLabel.";

            } elseif ($request->action === 'delete') {
                Borongan::whereIn('id', $ids)->delete();
                $message = "Berhasil menghapus " . count($ids) . " data borongan.";
            }

            DB::commit();
            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data: ' . $e->getMessage());
        }
    }
}
