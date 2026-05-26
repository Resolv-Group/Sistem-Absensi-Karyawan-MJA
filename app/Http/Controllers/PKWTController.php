<?php

namespace App\Http\Controllers;

use App\Models\Divisi;
use App\Models\JabatanPKWT;
use App\Models\Pekerja;
use App\Models\PKWT;
use App\Models\PKWT_Hari_Kerja;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use function Symfony\Component\Clock\now;

class PKWTController extends Controller
{
    public function viewPKWTMain(Request $request, $id_unit)
    {
        $user = auth()->user(); // staff login

        // CEK PIC PUNYA UNIT INI ATAU TIDAK
        $isAllowed = Unit::where('id', $id_unit)
            ->whereHas('picUnit', function ($q) use ($user) {
                $q->where('id_pic', $user->id);
            })
            ->exists();

        if (in_array($user->role, ['admin', 'hrd'])) {
        } elseif (! $isAllowed) {
            abort(403, 'Anda tidak memiliki akses ke unit ini');
        }

        $unit = Unit::findOrFail($id_unit);
        $query = PKWT::with(['pekerja', 'jabatan', 'divisi'])->where('id_unit', $id_unit);

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pekerja', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%");
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

        $pkwtPekerja = $query->latest()->paginate(25);

        // Data untuk Filter Dropdown
        $divisions = Divisi::all();
        $jabatan = JabatanPKWT::all();

        if ($request->ajax()) {
            return view('Unit.partials.main-harian-table', compact('pkwtPekerja', 'unit'))->render();
        }

        return view('Unit.Pengajian.main-harian', compact('pkwtPekerja', 'unit', 'divisions', 'jabatan'));
    }

    public function viewTambahUnitHarian($id_unit)
    {
        $user = auth()->user(); // staff login

        // CEK PIC PUNYA UNIT INI ATAU TIDAK
        $isAllowed = Unit::where('id', $id_unit)
            ->whereHas('picUnit', function ($q) use ($user) {
                $q->where('id_pic', $user->id);
            })
            ->exists();

        if (in_array($user->role, ['admin', 'hrd'])) {
        } elseif (! $isAllowed) {
            abort(403, 'Anda tidak memiliki akses ke unit ini');
        }

        // 1. Get current unit info
        $units = Unit::select('id', 'nama_unit as nama')->get();
        $unitSelected = Unit::with('namaMitra')->where('id', $id_unit)->firstOrFail();

        // 2. Get IDs of workers who ALREADY have an active contract (in any unit)
        $assignedWorkerIds = PKWT::where('id_unit', $id_unit)->where('status_aktif', 1)->pluck('id_pekerja')->toArray();

        // 3. Get workers who are active but NOT in the assigned list
        // This handles the requirement: "cannot add worker if already in this or other unit"
        $pekerjaList = Pekerja::select('id', 'nama', 'nik', 'kpj', 'naker')->whereNotIn('id', $assignedWorkerIds)->get();
        $divisiList = Divisi::select('id', 'nama')->get();
        $jabatanList = JabatanPKWT::select('id', 'nama')->get();

        return view('Unit.CRUD.tambah-unit-pekerja', compact('unitSelected', 'units', 'pekerjaList', 'divisiList', 'jabatanList'));
    }

    public function tambahPekerjaUnit(Request $request)
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

                    'pekerja.*.gaji_bulanan' => 'required|numeric|min:0',
                    'pekerja.*.gaji_harian' => 'required|numeric|min:0',
                    'pekerja.*.gaji_overtime' => 'required|numeric|min:0',
                    'pekerja.*.rate_hbn' => 'required|numeric|min:0',

                    'pekerja.*.bpjs_kesehatan' => 'required|numeric|min:0',
                    'pekerja.*.bpjs_naker' => 'required|numeric|min:0',

                    'pekerja.*.tunjangan' => 'required|json',

                    'pekerja.*.days' => 'required|array|size:7',
                    'pekerja.*.days.*' => 'nullable|numeric|min:0|max:24',

                    'pekerja.*.dokumen_pkwt' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:2048',
                ],
                [
                    'id_unit.required' => 'ID Unit wajib diisi',
                    'pekerja.required' => 'Data pekerja wajib diisi',
                    'pekerja.*.id_pekerja.required' => 'Pekerja wajib dipilih',
                    'pekerja.*.divisi_id.required' => 'Divisi wajib dipilih',
                    'pekerja.*.jabatan_id.required' => 'Jabatan wajib dipilih',
                    'pekerja.*.gaji_bulanan.required' => 'Gaji bulanan wajib diisi',
                    'pekerja.*.gaji_harian.required' => 'Gaji harian wajib diisi',
                    'pekerja.*.gaji_overtime.required' => 'Gaji Overtime harian wajib diisi',
                    'pekerja.*.rate_hbn.required' => 'Rate HBN wajib diisi',
                    'pekerja.*.bpjs_kesehatan.required' => 'BPJS Kesehatan wajib diisi',
                    'pekerja.*.bpjs_naker.required' => 'BPJS Naker wajib diisi',

                    'pekerja.*.tunjangan.required' => 'Tunjangan wajib diisi',
                    'pekerja.*.tunjangan.json' => 'Format tunjangan tidak valid',

                    'pekerja.*.tgl_mulai_pkwt.required' => 'Tanggal mulai PKWT wajib diisi',
                    'pekerja.*.tgl_mulai_pkwt.date' => 'Tanggal mulai PKWT harus berupa tanggal yang valid',
                    'pekerja.*.tgl_akhir_pkwt.required' => 'Tanggal akhir PKWT wajib diisi',

                    'pekerja.*.days.*.numeric' => 'Jam kerja harus berupa angka',
                    'pekerja.*.days.*.min' => 'Jam kerja tidak boleh kurang dari 0',
                    'pekerja.*.days.*.max' => 'Jam kerja tidak boleh lebih dari 24',
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
                    'gaji_bulanan' => $data['gaji_bulanan'],
                    'gaji_harian' => $data['gaji_harian'],
                    'gaji_overtime' => $data['gaji_overtime'],
                    'rate_hbn' => $data['rate_hbn'],
                    'bpjs_kesehatan' => $data['bpjs_kesehatan'],
                    'bpjs_naker' => $data['bpjs_naker'],
                    'tunjangan' => json_decode($data['tunjangan'], true),
                    'dokumen_pkwt' => $dokumen,
                    'dokumen_mime' => $dokumenMime,
                    'status_aktif' => 1,
                ]);

                foreach ($data['days'] as $hari => $jam) {
                    // Gunakan Model PkwtHariKerja
                    PKWT_Hari_Kerja::create([
                        'pkwt_id' => $pkwt->id, // Ambil ID PKWT yang baru saja dibuat di atas
                        'hari' => $hari,      // 'mon', 'tue', dst (sesuai key array)
                        'jam_kerja' => $jam ?? 0,        // nilainya (0 - 24)
                    ]);
                }
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

    public function ubahUnitPekerja(Request $request, $unitId, $pekerjaId)
    {
        $unitSelected = Unit::with('namaMitra')->findOrFail($unitId);

        // 🔑 Ambil PKWT yang mau diedit
        $pkwt = PKWT::with(['pekerja', 'divisi', 'jabatan'])
            ->where('id', $pekerjaId)
            ->where('id_unit', $unitId)
            ->firstOrFail();

        // List dropdown
        $pekerjaList = Pekerja::select('id', 'nama', 'nik', 'kpj', 'naker')->where('status_aktif', 1)->get();

        // dd($pekerjaList);

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
                'pekerja.*.gaji_bulanan' => 'required|numeric|min:0',
                'pekerja.*.gaji_harian' => 'required|numeric|min:0',
                'pekerja.*.gaji_overtime' => 'required|numeric|min:0',
                'pekerja.*.rate_hbn' => 'required|numeric|min:0',
                'pekerja.*.bpjs_kesehatan' => 'required|numeric|min:0',
                'pekerja.*.bpjs_naker' => 'required|numeric|min:0',
                'pekerja.*.divisi_id' => 'required|exists:divisi,id',
                'pekerja.*.jabatan_id' => 'required|exists:jabatan_pkwt,id',
                'pekerja.*.tgl_mulai_pkwt' => 'required|date',
                'pekerja.*.tgl_akhir_pkwt' => 'required|date|after_or_equal:pekerja.*.tgl_mulai_pkwt',
                'pekerja.*.tunjangan' => 'required|json',
                'pekerja.*.days' => 'required|array|size:7',
                'pekerja.*.days.*' => 'nullable|numeric|min:0|max:24',
                'pekerja.*.dokumen_pkwt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);
            
            // Ambil PKWT
            $pkwt = PKWT::where('id', $pkwtId)->where('id_unit', $unitId)->firstOrFail();

            // Karena edit 1 PKWT → ambil row pertama
            $data = $request->pekerja[0];

            // Update field utama
            $pkwt->update([
                'id_pekerja' => $data['id_pekerja'],
                'gaji_bulanan' => $data['gaji_bulanan'],
                'gaji_harian' => $data['gaji_harian'],
                'gaji_overtime' => $data['gaji_overtime'],
                'rate_hbn' => $data['rate_hbn'],
                'bpjs_kesehatan' => $data['bpjs_kesehatan'],
                'bpjs_naker' => $data['bpjs_naker'],
                'divisi_id' => $data['divisi_id'],
                'jabatan_id' => $data['jabatan_id'],
                'tgl_mulai_pkwt' => $data['tgl_mulai_pkwt'],
                'tgl_akhir_pkwt' => $data['tgl_akhir_pkwt'],
                'tunjangan' => json_decode($data['tunjangan'], true),
            ]);

            foreach ($data['days'] as $hari => $jam) {
                PKWT_Hari_Kerja::updateOrCreate(
                    [
                        'pkwt_id' => $pkwt->id, // Cari yang PKWT ID-nya ini
                        'hari' => $hari,      // Dan harinya ini (mon, tue, dst)
                    ],
                    [
                        'jam_kerja' => $jam ?? 0, // Update jam kerjanya
                        'updated_at' => now(),
                    ]
                );
            }

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

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'ids' => 'required',
            'action' => 'required|string',
            'status' => 'required_if:action,update_status|in:0,1',
            'reason' => 'nullable|string|max:500',
        ]);

        $ids = json_decode($request->ids, true);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada pekerja yang dipilih.');
        }

        try {
            DB::beginTransaction();

            if ($request->action === 'update_status') {
                // 🔥 JIKA MAU AKTIFKAN
                if ((int) $request->status === 1) {
                    // 1️⃣ Ambil daftar pekerja dari PKWT yang dipilih
                    $pekerjaIds = PKWT::whereIn('id', $ids)->pluck('id_pekerja')->unique();

                    // 2️⃣ Cek apakah masih ada PKWT aktif lain
                    $conflict = PKWT::whereIn('id_pekerja', $pekerjaIds)
                        ->where('status_aktif', 1)
                        ->whereNotIn('id', $ids) // ⬅️ selain yang sedang dipilih
                        ->exists();

                    if ($conflict) {
                        DB::rollBack();

                        return back()->with('error', 'Gagal mengaktifkan. Pastikan pekerja tidak memiliki PKWT aktif di unit lain.');
                    }
                }

                // 3️⃣ UPDATE AMAN
                PKWT::whereIn('id', $ids)->update([
                    'status_aktif' => $request->status,
                    'updated_at' => now(),
                ]);

                $statusLabel = $request->status == 1 ? 'Aktif' : 'Nonaktif';
                $message = 'Berhasil mengubah '.count($ids)." pekerja menjadi $statusLabel.";
            } elseif ($request->action === 'delete') {
                PKWT::whereIn('id', $ids)->delete();
                $message = 'Berhasil menghapus '.count($ids).' data pekerja.';
            }

            DB::commit();

            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Terjadi kesalahan: '.$e->getMessage());
        }
    }

    public function bulkUpdateDivisi(Request $request)
    {
        $ids = json_decode($request->ids);
        $divisiId = $request->divisi_id;

        // Validate that IDs and Divisi exist
        PKWT::whereIn('id', $ids)->update([
            'divisi_id' => $divisiId,
            // You can handle 'apply_immediately' logic here if needed
        ]);

        return back()->with('success', count($ids).' pekerja berhasil mendapatkan divisi baru.');
    }

    public function bulkUpdateJabatan(Request $request)
    {
        $ids = json_decode($request->ids);
        $jabatanId = $request->jabatan_id;

        // Validate that IDs and Divisi exist
        PKWT::whereIn('id', $ids)->update([
            'jabatan_id' => $jabatanId,
            // You can handle 'apply_immediately' logic here if needed
        ]);

        return back()->with('success', count($ids).' pekerja berhasil mendapatkan jabatan baru.');
    }

    public function quickStore(Request $request, $type)
    {
        $request->validate(['nama' => 'required|string|max:255']);

        if ($type === 'divisi') {
            $item = Divisi::create(['nama' => $request->nama]);
        } else {
            $item = JabatanPKWT::create(['nama' => $request->nama]);
        }

        return response()->json([
            'success' => true,
            'data' => $item,
        ]);
    }
}
