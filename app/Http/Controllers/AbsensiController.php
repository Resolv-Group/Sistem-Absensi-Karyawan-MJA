<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Borongan;
use App\Models\Detil_Borongan;
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
        $today = Carbon::today();
        $limit = Carbon::today()->addDays(7);

        // 🔥 1. Ambil tanggal (default: hari ini)
        $date = $request->date ?? now()->toDateString();

        // 🔥 2. Unit yang dipegang PIC
        $unitsQuery = Unit::with(['namaMitra'])
            ->withCount('pkwt')
            ->orderBy('created_at', 'desc');

        // 🔐 JIKA BUKAN ADMIN → batasi unit
        if ($staff->jabatan !== 'admin') {
            $unitsQuery->whereHas('picUnit', function ($q) use ($staff) {
                $q->where('id_pic', $staff->id);
            });
        }

        // $units = $unitsQuery->paginate(10)->withQueryString();

        $units = Unit::with(['namaMitra'])
            ->whereHas('picUnit', fn ($q) =>
                $q->where('id_pic', Auth::user()->staff->id)
            )
            ->paginate(10)
            ->withQueryString();


        // 🔥 3. TOTAL Unit (jumlah unit PIC)
        $totalUnit = $units->total();

        // 🔥 4. TOTAL HADIR (detil_harian dari absensi PIC + tanggal)
        $filterAbsensi = function ($q) use ($staff, $date) {
            $q->where('id_pic', $staff->id)
            ->whereDate('tgl_absensi', $date);
        };

        $totalHadir =
        Detil_Harian::whereHas('absensi', $filterAbsensi)
            ->where('status_kehadiran', 1)
            ->count()
        +
        Detil_Borongan::whereHas('absensi', $filterAbsensi)
            ->where('status_kehadiran', 1)
            ->distinct('id_absensi')
            ->count();



        // 🔥 5. TOTAL ABSEN
        $totalPekerja = PKWT::whereHas('unit.picUnit', function ($q) use ($staff) {
                $q->where('id_pic', $staff->id);
            })
            ->count();

        $totalAbsen = max(0, $totalPekerja - $totalHadir);

        $totalPenilaian = PKWT::whereBetween('tgl_akhir_pkwt', [$today, $limit])
            ->whereHas('unit.picUnit', function ($q) use ($staff) {
                $q->where('id_pic', $staff->id);
            })
            ->count();


        // 🔥 7. AJAX support
        if ($request->ajax()) {
            return view('Absensi.partials.absensi-table', compact('units'))->render();
        }

        // 🔥 8. Return view
        return view('Absensi.main-absensi', compact('totalUnit', 'totalHadir', 'totalAbsen', 'totalPenilaian', 'units', 'date'));
    }

    public function ViewHarian(Request $request, $id_unit, $date)
    {
        $picId = Auth::user()->staff->id;
        $unit = Unit::with(['namaMitra'])->findOrFail($id_unit);

        // 1. Sync Attendance Records (Tetap pertahankan ini agar record absensi utama tercipta)
        $allPkwt = PKWT::with('pekerja')->where('id_unit', $id_unit)->get();
        foreach ($allPkwt as $pkwt) {
            Absensi::firstOrCreate(
                [
                    'id_pekerja' => $pkwt->id_pekerja,
                    'tgl_absensi' => $date,
                    'id_unit' => $unit->id,
                ],
                [
                    'id_pic' => $picId,
                    'tipe' => $unit->sistem_pengajian,
                    'verifikasi' => 0,
                ],
            );
        }

        // 2. Query Utama: Ambil PKWT + Pekerja + Absensi (pada tgl tsb) + DetilHarian + status aktif PKWT
        $pkwtQuery = PKWT::with([
            'pekerja.absensi' => function ($q) use ($date, $id_unit) {
                $q->where('tgl_absensi', $date)->where('id_unit', $id_unit)->with('detilHarian');
            },
        ])->where('id_unit', $id_unit)->where('status_aktif', 1);



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
                $q->where('tgl_absensi', $date)->where('id_unit', $id_unit)->where('verifikasi', $status);
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
        $unit = Unit::with(['namaMitra'])->findOrFail($id_unit);

        // 1. Sync Attendance Records (Tetap pertahankan ini agar record absensi utama tercipta)
        $allPkwt = PKWT::with('pekerja')->where('id_unit', $id_unit)->get();
        foreach ($allPkwt as $pkwt) {
            Absensi::firstOrCreate(
                [
                    'id_pekerja' => $pkwt->id_pekerja,
                    'tgl_absensi' => $date,
                    'id_unit' => $unit->id,
                ],
                [
                    'id_pic' => $picId,
                    'tipe' => $unit->sistem_pengajian,
                    'verifikasi' => 0,
                ],
            );
        }

        $barangs = Borongan::where('id_unit', $id_unit)->orderBy('nama_item', 'asc')->get();
        $barangLookup = $barangs->keyBy('id');

        // 2. Query Utama: Ambil PKWT + Pekerja + Absensi (pada tgl tsb) + detilBorongan

        $pkwtQuery = PKWT::with([
            'pekerja.absensi' => function ($q) use ($date, $id_unit) {
                $q->where('tgl_absensi', $date)
                ->where('id_unit', $id_unit)
                ->with('detilBorongan');
            },
        ])->where('id_unit', $id_unit)
        ->where('status_aktif', 1);

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
                    ->whereHas('detilBorongan', function ($q2) use ($status) {
                        $q2->where('status_kehadiran', $status);
                    });
            });
        }

        if ($request->filled('statusVerif')) {
            $status = $request->statusVerif;
            $pkwtQuery->whereHas('pekerja.absensiMany', function ($q) use ($status, $date, $id_unit) {
                $q->where('tgl_absensi', $date)->where('id_unit', $id_unit)->where('verifikasi', $status);
            });
        }

        $pkwtPekerja = $pkwtQuery->paginate(25)->withQueryString();;

        $existingBorongan = [];

        foreach ($pkwtPekerja as $pkwt) {
            $absensi = Absensi::where('id_pekerja', $pkwt->pekerja->id)
                ->whereDate('tgl_absensi', $date)
                ->where('id_unit', $pkwt->id_unit)
                ->first();
            if ($absensi && $absensi->detilBorongan->isNotEmpty()) {
                $existingBorongan[$pkwt->id] = $absensi->detilBorongan->map(function ($d) {
                    return [
                        'id_barang' => $d->id_barang,
                        'FD' => $d->FD,
                        'act_rej' => $d->act_rej,
                        'good_mc' => $d->good_mc,
                        'bayaranPerusahaan' => $d->bayaranPerusahaan,
                        'bayaranItem' => $d->bayaranItem,
                        'catatan' => $d->catatan,
                        'fileName' => null,
                    ];
                })->values();
            }
        }

        // 4. Handle AJAX Response
        if ($request->ajax()) {
            return view('Absensi.partials.main-borongan-table', compact('pkwtPekerja', 'unit', 'date', 'barangs', 'barangLookup', 'existingBorongan'))->render();
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
            ->whereHas('detilBorongan', function ($q) {
                $q->where('status_kehadiran', 1);
            })
            ->count();

        return view('Absensi.detail.main-borongan', compact('unit', 'date', 'workerMap', 'pkwtPekerja', 'totalHadir', 'barangs', 'barangLookup', 'existingBorongan'));
    }

    // public function bulkAbsensiUpdate(Request $request)
    // {
    //     // 1. Validasi Input
    //     $request->validate([
    //         'date' => 'required|date',
    //         'data' => 'required|array',
    //     ]);

    //     $date = $request->date;
    //     $inputData = $request->data;

    //     // 2. Mulai Transaksi Database
    //     DB::beginTransaction();

    //     try {
    //         foreach ($inputData as $pkwtId => $values) {
    //             // Cari data PKWT
    //             $pkwt = PKWT::find($pkwtId);
    //             if (!$pkwt) {
    //                 continue;
    //             }

    //             // Cari record Absensi utama (yang dibuat di ViewHarian)
    //             $absensi = Absensi::where('id_pekerja', $pkwt->id_pekerja)->where('tgl_absensi', $date)->first();

    //             if ($absensi) {
    //                 // Tentukan Status (Hadir=1, Cuti=2)
    //                 $status = isset($values['status']) ? (int) $values['status'] : 1;

    //                 $oldDetil = $absensi->detilHarian; // relasi hasOne

    //                 /**
    //                  * LOGIKA PEMBATASAN WAKTU (Karena DB tidak boleh NULL)
    //                  * Jika status Hadir (1), ambil input jam atau set 00:00:00 jika kosong.
    //                  * Jika status BUKAN Hadir (2-5), paksa jam jadi 00:00:00 agar DB tidak error.
    //                  */
    //                 $waktuMasuk = $status === 1 ? $values['masuk'] ?? '00:00:00' : '00:00:00';
    //                 $waktuKeluar = $status === 1 ? $values['keluar'] ?? '00:00:00' : '00:00:00';

    //                 $statusChanged = $oldDetil && $oldDetil->status_kehadiran != $status;

    //                 // Simpan atau Update ke tabel detil_harian
    //                 Detil_Harian::updateOrCreate(
    //                     ['id_absensi' => $absensi->id], // Kunci pencarian (mencegah duplikat)
    //                     [
    //                         'status_kehadiran' => $status,
    //                         'waktu_masuk' => $waktuMasuk,
    //                         'waktu_keluar' => $waktuKeluar,
    //                         'catatan' => $values['catatan'] ?? null,
    //                         'updated_by' => Auth::id(),
    //                     ],
    //                 );

    //                 // 🔁 RESET VERIFIKASI JIKA STATUS BERUBAH
    //                 if ($statusChanged) {
    //                     $absensi->update([
    //                         'verifikasi' => 0,
    //                     ]);
    //                 }
    //             }
    //         }

    //         // 3. Simpan perubahan permanen jika semua loop sukses
    //         DB::commit();

    //         return redirect()->back()->with('success', 'Data presensi pekerja berhasil diperbarui.');
    //     } catch (\Exception $e) {
    //         // 4. Batalkan semua perubahan jika ada satu saja yang error
    //         DB::rollBack();

    //         return redirect()
    //             ->back()
    //             ->with('error', 'Gagal menyimpan presensi: ' . $e->getMessage());
    //     }
    // }


    public function bulkAbsensiUpdate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'data' => 'required|array',
        ]);

        $date = $request->date;
        $inputData = $request->data;

        DB::beginTransaction();

        try {
            foreach ($inputData as $pkwtId => $values) {
                $pkwt = PKWT::find($pkwtId);
                if (!$pkwt) continue;

                $absensi = Absensi::where('id_pekerja', $pkwt->id_pekerja)
                    ->where('tgl_absensi', $date)
                    ->first();

                if (!$absensi) continue;

                // Status baru (default HADIR)
                $status = isset($values['status']) ? (int) $values['status'] : 1;

                $oldDetil = $absensi->detilHarian; // hasOne

                $statusChanged = $oldDetil && $oldDetil->status_kehadiran != $status;

                /**
                 * 🔥 HARD DELETE JIKA STATUS BERUBAH
                 */
                if ($statusChanged) {
                    $oldDetil->delete();
                }

                /**
                 * LOGIKA WAKTU
                 */
                $waktuMasuk = $status === 1 ? ($values['masuk'] ?? '00:00:00') : '00:00:00';
                $waktuKeluar = $status === 1 ? ($values['keluar'] ?? '00:00:00') : '00:00:00';

                /**
                 * SIMPAN DATA BARU
                 */
                Detil_Harian::updateOrCreate(
                    ['id_absensi' => $absensi->id],
                    [
                        'status_kehadiran' => $status,
                        'waktu_masuk' => $waktuMasuk,
                        'waktu_keluar' => $waktuKeluar,
                        'catatan' => $values['catatan'] ?? null,
                        'updated_by' => Auth::id(),
                    ]
                );

                /**
                 * RESET VERIFIKASI JIKA STATUS BERUBAH
                 */
                if ($statusChanged) {
                    $absensi->update([
                        'verifikasi' => 0,
                    ]);
                }
            }

            DB::commit();
            return back()->with('success', 'Data presensi pekerja berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan presensi: ' . $e->getMessage());
        }
    }

    public function bulkAbsensiBoronganUpdate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'data' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $date = $request->date;

            foreach ($request->data as $pkwtId => $payload) {
                $pkwt = PKWT::findOrFail($pkwtId);
                $absensi = Absensi::where('id_pekerja', $pkwt->id_pekerja)->where('tgl_absensi', $date)->first();

                if (!$absensi) {
                    continue;
                }

                // --- PERBAIKAN UTAMA: Hapus semua detail borongan lama untuk tanggal ini ---
                // Ini memastikan jika sebelumnya "Sakit", datanya dibersihkan total sebelum ganti ke "Produksi"
                // Dan sebaliknya.
                Detil_Borongan::where('id_absensi', $absensi->id)->delete();

                if (isset($payload['status'])) {
                    // PATH A: INPUT STATUS KHUSUS (Sakit, Izin, Cuti, Alpha)
                    $status = $payload['status'];

                    Detil_Borongan::create([
                        'id_absensi' => $absensi->id,
                        'id_barang' => 0, // Gunakan 0 secara konsisten untuk non-produksi
                        'status_kehadiran' => $status,
                        'FD' => 0,
                        'act_rej' => 0,
                        'good_mc' => 0,
                        'bayaranPerusahaan' => 0,
                        'bayaranItem' => 0,
                        'catatan' => $payload['catatan'] ?? null,
                        'updated_by' => Auth::id(),
                    ]);
                } else {
                    // PATH B: INPUT PRODUKSI (Barang)
                    foreach ($payload as $index => $row) {
                        $fileContent = null;

                        // Handle File Upload
                        if ($request->hasFile("data.$pkwtId.$index.buktiSuratJalan")) {
                            $fileContent = file_get_contents($request->file("data.$pkwtId.$index.buktiSuratJalan")->getRealPath());
                        }

                        Detil_Borongan::create([
                            'id_absensi' => $absensi->id,
                            'id_barang' => $row['id_barang'],
                            'status_kehadiran' => 1, // Otomatis Hadir jika ada input barang
                            'FD' => $row['FD'] ?? 0,
                            'act_rej' => $row['act_rej'] ?? 0,
                            'good_mc' => $row['good_mc'] ?? 0,
                            'bayaranPerusahaan' => $row['bayaranPerusahaan'] ?? 0,
                            'bayaranItem' => $row['bayaranItem'] ?? 0,
                            'buktiSuratJalan' => $fileContent,
                            'catatan' => $row['catatan'] ?? null,
                            'updated_by' => Auth::id(),
                        ]);
                    }
                }
            }

            DB::commit();
            return back()->with('success', 'Data absensi berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}
