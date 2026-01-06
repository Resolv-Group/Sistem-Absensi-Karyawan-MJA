<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Detil_Harian;
use App\Models\Divisi;
use App\Models\JabatanPKWT;
use App\Models\MitraKerja;
use App\Models\Pekerja;
use App\Models\PKWT;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    public function viewAbsensiMain(Request $request)
    {
        $user = Auth::user();
        $staff = $user->staff;
        
        // 🔥 1. Ambil tanggal (default: hari ini)
        $date = $request->date ?? now()->toDateString();

        // 🔥 2. Unit yang dipegang PIC
        $units = Unit::with(['namaMitra'])
            ->withCount('pkwt')
            ->whereHas('picUnit', function ($q) use ($staff) {
                $q->where('id_pic', $staff->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // 🔥 3. TOTAL ABSENSI (jumlah unit PIC)
        $totalAbsensi = $units->total();

        // 🔥 4. TOTAL HADIR (detil_harian dari absensi PIC + tanggal)
        $totalHadir = Detil_Harian::whereHas('absensi', function ($q) use ($staff, $date) {
            $q->where('id_pic', $staff->id)->whereDate('tgl_absensi', $date);
        })
            ->where('status_kehadiran', 1)
            ->count();

        // 🔥 5. TOTAL ABSEN
        $totalAbsen = Detil_Harian::whereHas('absensi', function ($q) use ($staff, $date) {
            $q->where('id_pic', $staff->id)->whereDate('tgl_absensi', $date);
        })
            ->where('status_kehadiran', 0)
            ->count();

        // 🔥 7. AJAX support
        if ($request->ajax()) {
            return view('Absensi.partials.absensi-table', compact('units'))->render();
        }

        // 🔥 8. Return view
        return view('Absensi.main-absensi', compact('totalAbsensi', 'totalHadir', 'totalAbsen', 'units', 'date'));
    }

    public function ViewHarian(Request $request, $id_unit, $date)
    {
        $picId = Auth::user()->staff->id;
        $unit = Unit::with(['namaMitra'])->findOrFail($id_unit);

        // 1. Sync Attendance Records (Tetap pertahankan ini agar record absensi utama tercipta)
        $allPkwt = PKWT::with('pekerja')->where('id_unit', $id_unit)->get();
        foreach ($allPkwt as $pkwt) {
            Absensi::firstOrCreate([
                'id_pekerja' => $pkwt->id_pekerja,
                'tgl_absensi' => $date,
                'id_unit'    => $unit->id,
            ], [
                'id_pic'     => $picId,
                'tipe'       => $unit->sistem_pengajian,
                'verifikasi' => 0,
            ]);
        }

        // 2. Query Utama: Ambil PKWT + Pekerja + Absensi (pada tgl tsb) + DetilHarian
        $pkwtQuery = PKWT::with([
            'pekerja.absensi' => function($q) use ($date, $id_unit) {
                $q->where('tgl_absensi', $date)
                ->where('id_unit', $id_unit)
                ->with('detilHarian');
            },

        ])->where('id_unit', $id_unit);

        // 3. Filter Pencarian (Nama/NIK)
        if ($request->filled('search')) {
            $search = $request->search;
            $pkwtQuery->whereHas('pekerja', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Filter Status Aktif PKWT (bukan status absen)
        if ($request->filled('status')) {
            $status = $request->status;
                $pkwtQuery->whereHas('pekerja.absensiMany', function ($q) use ($status, $date, $id_unit) {
                    $q->where('tgl_absensi', $date)
                    ->where('id_unit', $id_unit)
                    ->whereHas('detilHarian', function ($q2) use ($status) {
                        $q2->where('status_kehadiran', $status);
                    });
                });
        }

        if ($request->filled('statusVerif')) {
            $status = $request->statusVerif;
                $pkwtQuery->whereHas('pekerja.absensiMany', function ($q) use ($status, $date, $id_unit) {
                    $q->where('tgl_absensi', $date)
                    ->where('id_unit', $id_unit)
                    ->where('verifikasi', $status);
                });
        }

        $pkwtPekerja = $pkwtQuery->paginate(25);

        // 4. Handle AJAX Response
        if ($request->ajax()) {
            return view('Absensi.partials.main-harian-table', compact('pkwtPekerja', 'unit', 'date'))->render();
        }

        // 5. Worker Map untuk Modal
        $workerMap = $allPkwt->mapWithKeys(function ($item) {
            return [
                $item->id => [
                    'nama' => $item->pekerja->nama,
                    'nik' => $item->pekerja->nik,
                    'initials' => strtoupper(substr($item->pekerja->nama, 0, 2)),
                ],
            ];
        });

        $totalHadir = Absensi::where('id_unit', $unit->id)
            ->where('tgl_absensi', $date)
            ->whereHas('detilHarian', function ($q) {
                $q->where('status_kehadiran', 1);
            })
            ->count();

        return view('Absensi.detail.main-harian', compact('unit', 'date', 'workerMap', 'pkwtPekerja', 'totalHadir'));
    }

    function ViewBorongan(Request $request, $id_unit, $date)
    {
        $picId = Auth::user()->staff->id;

        // ambil unit + pekerjanya
        $unit = Unit::with('pkwt.pekerja')->findOrFail($id_unit);

        foreach ($unit->pkwt as $pkwt) {
            $sudahAda = Absensi::where('id_pekerja', $pkwt->id_pekerja)->where('id_unit', $id_unit)->where('tgl_absensi', $date)->exists();

            if ($sudahAda) {
                continue;
            }

            Absensi::create([
                'id_pekerja' => $pkwt->id_pekerja,
                'id_pic' => $picId,
                'id_unit' => $unit->id,
                'tgl_absensi' => $date,
                'tipe' => $unit->sistem_pengajian,
                'verifikasi' => 0,
            ]);
        }
    }

    public function bulkAbsensiUpdate(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'date' => 'required|date',
            'data' => 'required|array',
        ]);

        $date = $request->date;
        $inputData = $request->data;

        // 2. Mulai Transaksi Database
        DB::beginTransaction();

        try {
            foreach ($inputData as $pkwtId => $values) {
                // Cari data PKWT
                $pkwt = PKWT::find($pkwtId);
                if (!$pkwt) {
                    continue;
                }

                // Cari record Absensi utama (yang dibuat di ViewHarian)
                $absensi = Absensi::where('id_pekerja', $pkwt->id_pekerja)->where('tgl_absensi', $date)->first();

                if ($absensi) {
                    // Tentukan Status (Hadir=1, Sakit=2, Izin=3, Cuti=4, Alpha=5)
                    $status = isset($values['status']) ? (int) $values['status'] : 1;

                    $oldDetil = $absensi->detilHarian; // relasi hasOne

                    /**
                     * LOGIKA PEMBATASAN WAKTU (Karena DB tidak boleh NULL)
                     * Jika status Hadir (1), ambil input jam atau set 00:00:00 jika kosong.
                     * Jika status BUKAN Hadir (2-5), paksa jam jadi 00:00:00 agar DB tidak error.
                     */
                    $waktuMasuk = $status === 1 ? $values['masuk'] ?? '00:00:00' : '00:00:00';
                    $waktuKeluar = $status === 1 ? $values['keluar'] ?? '00:00:00' : '00:00:00';

                    $statusChanged = $oldDetil && $oldDetil->status_kehadiran != $status;

                    // Simpan atau Update ke tabel detil_harian
                    Detil_Harian::updateOrCreate(
                        ['id_absensi' => $absensi->id], // Kunci pencarian (mencegah duplikat)
                        [
                            'status_kehadiran' => $status,
                            'waktu_masuk' => $waktuMasuk,
                            'waktu_keluar' => $waktuKeluar,
                            'catatan' => $values['catatan'] ?? null,
                        ],
                    );

                     // 🔁 RESET VERIFIKASI JIKA STATUS BERUBAH
                    if ($statusChanged) {
                        $absensi->update([
                            'verifikasi' => 0
                        ]);
                    }
                }
            }

            // 3. Simpan perubahan permanen jika semua loop sukses
            DB::commit();

            return redirect()->back()->with('success', 'Data presensi pekerja berhasil diperbarui.');
        } catch (\Exception $e) {
            // 4. Batalkan semua perubahan jika ada satu saja yang error
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Gagal menyimpan presensi: ' . $e->getMessage());
        }
    }
}
