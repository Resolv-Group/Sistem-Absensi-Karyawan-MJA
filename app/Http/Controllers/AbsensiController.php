<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Absensi_Borongan;
use App\Models\Borongan;
use App\Models\Detil_Borongan;
use App\Models\Detil_Harian;
use App\Models\Pekerja;
use App\Models\PKWT;
use App\Models\Potongan;
use App\Models\Tunjangan;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AbsensiController extends Controller
{
    public function viewAbsensiMain(Request $request)
    {
        $user = Auth::user();
        $staff = $user->staff;
        $today = Carbon::today();
        $limit = Carbon::today()->addDays(30);

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
            ->whereHas('picUnit', fn ($q) => $q->where('id_pic', Auth::user()->staff->id))
            ->paginate(10)
            ->withQueryString();

        // 🔥 3. TOTAL Unit (jumlah unit PIC)
        $totalUnit = $units->total();

        // 🔥 4. TOTAL HADIR (detil_harian dari absensi PIC + tanggal)
        $filterAbsensi = function ($q) use ($staff, $date) {
            $q->where('id_pic', $staff->id)->whereDate('tgl_absensi', $date);
        };

        $totalHadir = Detil_Harian::whereHas('absensi', $filterAbsensi)->where('status_kehadiran', 1)->count() + Detil_Borongan::whereHas('absensi', $filterAbsensi)->where('status_kehadiran', 1)->distinct('id_absensi')->count();

        // 🔥 5. TOTAL ABSEN
        $totalPekerja = PKWT::whereHas('unit.picUnit', function ($q) use ($staff) {
            $q->where('id_pic', $staff->id);
        })->count();

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
        $unit = Unit::with(['namaMitra'])->findOrFail($id_unit);
        $dayName = strtolower(\Carbon\Carbon::parse($date)->format('D'));

        // 1. Sync Attendance Records (Tetap pertahankan ini agar record absensi utama tercipta)
        $allPkwt = PKWT::with('pekerja', 'hariKerja')->where('id_unit', $id_unit)->get();

        $existingAbsensi = Absensi::where('id_unit', $id_unit)->where('tgl_absensi', $date)->with('detilHarian')->get()->keyBy('id_pekerja'); // This makes searching instant

        // 2. Query Utama: Ambil PKWT + Pekerja + Absensi (pada tgl tsb) + DetilHarian + status aktif PKWT
        $pkwtQuery = PKWT::with([
            'pekerja.absensi' => function ($q) use ($date, $id_unit) {
                $q->where('tgl_absensi', $date)->where('id_unit', $id_unit)->with('detilHarian');
            },
        ])
            ->where('id_unit', $id_unit)
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
        $workerMap = $allPkwt->mapWithKeys(function ($item) use ($dayName, $existingAbsensi) {
            $hariSesuai = $item->hariKerja->where('hari', $dayName)->first();

            $jamNormal = $hariSesuai ? $hariSesuai->jam_kerja : 0;

            $savedAbsensi = $existingAbsensi->get($item->id_pekerja);
            $savedDetail = $savedAbsensi ? $savedAbsensi->detilHarian : null;

            $savedTunjangan = $savedAbsensi ? $savedAbsensi->tunjangan : null;

            // Decode tunjangan PKWT secara manual karena tidak menggunakan Casting di Model
            $pkwtTunjanganRaw = $item->tunjangan;
            $pkwtTunjanganParsed = is_string($pkwtTunjanganRaw)
                ? json_decode($pkwtTunjanganRaw, true)
                : $pkwtTunjanganRaw;

            $savedPotongan = $savedAbsensi ? $savedAbsensi->potongan : null;

            // Transformasi data: {"telat": 5000} -> [{nama: "telat", nominal: 5000}]
            $uiPotongan = [];
            if ($savedPotongan && $savedPotongan->kategori) {
                $kategoriArr = is_array($savedPotongan->kategori) ? $savedPotongan->kategori : json_decode($savedPotongan->kategori, true);
                foreach ($kategoriArr as $key => $val) {
                    $uiPotongan[] = [
                        'nama' => str_replace('_', ' ', $key), // Balikkan ke nama asli (tanpa underscore)
                        'nominal' => (int) $val,
                    ];
                }
            }

            return [
                $item->id => [
                    'nama' => $item->pekerja->nama,
                    'nik' => $item->pekerja->nik,
                    'initials' => strtoupper(substr($item->pekerja->nama, 0, 2)),
                    'pkwt_hari_kerja' => (float) $jamNormal,

                    'has_absen' => $savedAbsensi ? true : false,

                    // Existing Values from saved attendance
                    'existing_jam' => $savedDetail ? (float) $savedDetail->jam_kerja_harian : null,
                    'existing_hbn' => $savedDetail ? (int) $savedDetail->hbn : 0,
                    'existing_paid' => $savedDetail ? (int) $savedDetail->isPaid : 0,
                    'existing_status' => $savedDetail ? (int) $savedDetail->status_kehadiran : 0,
                    'existing_catatan' => $savedDetail ? $savedDetail->catatan : '',
                    'existing_potongan' => $uiPotongan,
                    'existing_keterangan_potongan' => $savedPotongan?->keterangan ?? '',
                    'pkwt_tunjangan' => $pkwtTunjanganParsed,
                    'existing_tunjangan' => $savedTunjangan ? $savedTunjangan->kategori : null,
                    'existing_keterangan_tunjangan' => $savedTunjangan?->keterangan ?? '',
                ],
            ];
        });

        // dd($date,$dayName, $workerMap);

        $totalHadir = Absensi::where('id_unit', $unit->id)
            ->where('tgl_absensi', $date)
            ->whereHas('detilHarian', function ($q) {
                $q->where('status_kehadiran', 1);
            })
            ->count();

        return view('Absensi.detail.main-harian', compact('unit', 'date', 'workerMap', 'pkwtPekerja', 'totalHadir'));
    }

    public function ViewBorongan(Request $request, $id_unit, $date)
    {
        $unit = Unit::with(['namaMitra'])->findOrFail($id_unit);

        $allPkwt = PKWT::with('pekerja')->where('id_unit', $id_unit)->get();

        // Key by id_pekerja for quick lookup — with absensiBorongan eager loaded
        $existingAbsensi = Absensi::where('id_unit', $id_unit)
            ->where('tgl_absensi', $date)
            ->with(['detilBorongan', 'tunjangan', 'potongan', 'absensiBorongan'])
            ->get()
            ->keyBy('id_pekerja');

        $barangs = Borongan::where('id_unit', $id_unit)->orderBy('nama_item', 'asc')->get();
        $barangLookup = $barangs->keyBy('id');

        // In ViewBorongan, update the eager load:
        $pkwtQuery = PKWT::with([
            'pekerja.absensiMany' => function ($q) use ($date, $id_unit) {
                $q->where('tgl_absensi', $date)
                    ->where('id_unit', $id_unit)
                    ->with([
                        'detilBorongan',
                        'absensiBorongan.detilBorongan', // pivot → shared detil
                        'tunjangan',
                        'potongan',
                    ]);
            },
        ])
            ->where('id_unit', $id_unit)
            ->where('status_aktif', 1);

        if ($request->filled('search')) {
            $search = $request->search;
            $pkwtQuery->whereHas('pekerja', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%");
            });
        }

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
                $q->where('tgl_absensi', $date)
                    ->where('id_unit', $id_unit)
                    ->where('verifikasi', $status);
            });
        }

        $pkwtPekerja = $pkwtQuery->paginate(25)->withQueryString();

        // Build existingBorongan for Alpine rowItems pre-fill
        $existingBorongan = [];
        foreach ($pkwtPekerja as $pkwt) {
            // ✅ Use the already-loaded $existingAbsensi collection — no extra query
            $absensi = $existingAbsensi->get($pkwt->pekerja->id);

            if ($absensi) {
                // Check if this worker has GROUP absen — detil lives on the pivot absensi
                if ($absensi->absensiBorongan->isNotEmpty()) {
                    $groupAbsensi = $absensi->absensiBorongan->first();
                    $rawDetil = $groupAbsensi->detilBorongan;

                    // Handle both hasOne (single model) and hasMany (collection)
                    $detilSource = $rawDetil instanceof \Illuminate\Database\Eloquent\Collection
                        ? $rawDetil
                        : collect($rawDetil ? [$rawDetil] : []);
                } else {
                    $detilSource = $absensi->detilBorongan;
                }

                if ($detilSource->isNotEmpty()) {
                    $existingBorongan[$pkwt->id] = $detilSource
                        ->map(function ($d) {
                            return [
                                'id_barang' => $d->id_barang,
                                'FD' => $d->FD,
                                'act_rej' => $d->act_rej,
                                'good_mc' => $d->good_mc,
                                'max_rej_subkon' => $d->max_rej_subkon ?? 0,
                                'act_rej_max' => $d->max_rej_subkon ?? 0,
                                'rej_mc_dibebankan' => $d->rej_mc_beban ?? 0,
                                'bayaranPerusahaan' => $d->bayaranPerusahaan,
                                'bayaranItem' => $d->bayaranItem,
                                'catatan' => $d->catatan,
                                'fileName' => null,
                            ];
                        })
                        ->values();
                }
            }

            // if ($absensi && $absensi->detilBorongan->isNotEmpty()) {
            //     $existingBorongan[$pkwt->id] = $absensi->detilBorongan
            //         ->map(function ($d) {
            //             return [
            //                 'id_barang' => $d->id_barang,
            //                 'FD' => $d->FD,
            //                 'act_rej' => $d->act_rej,
            //                 'good_mc' => $d->good_mc,
            //                 'max_rej_subkon' => $d->max_rej_subkon ?? 0,
            //                 'act_rej_max' => $d->max_rej_subkon ?? 0,
            //                 'rej_mc_dibebankan' => $d->rej_mc_beban ?? 0,
            //                 'bayaranPerusahaan' => $d->bayaranPerusahaan,
            //                 'bayaranItem' => $d->bayaranItem,
            //                 'catatan' => $d->catatan,
            //                 'fileName' => null,
            //             ];
            //         })
            //         ->values();
            // }
        }

        // Handle AJAX
        if ($request->ajax()) {
            return view('Absensi.partials.main-borongan-table', compact(
                'pkwtPekerja', 'unit', 'date', 'barangs', 'barangLookup', 'existingBorongan'
            ))->render();
        }

        // ✅ workerMap — $absensi now correctly resolved from $existingAbsensi
        $workerMap = $allPkwt->mapWithKeys(function ($item) use ($existingAbsensi) {
            // ✅ FIX: resolve from the keyed collection, not undefined $absensi
            $savedAbsensi = $existingAbsensi->get($item->id_pekerja);

            $savedTunjangan = $savedAbsensi?->tunjangan;

            $pkwtTunjanganRaw = $item->tunjangan;
            $pkwtTunjanganParsed = is_string($pkwtTunjanganRaw)
                ? json_decode($pkwtTunjanganRaw, true)
                : $pkwtTunjanganRaw;

            $uiPotongan = [];
            $savedPotongan = $savedAbsensi?->potongan;
            if ($savedPotongan && $savedPotongan->kategori) {
                $kategoriArr = is_array($savedPotongan->kategori)
                    ? $savedPotongan->kategori
                    : json_decode($savedPotongan->kategori, true);
                foreach ($kategoriArr as $key => $val) {
                    $uiPotongan[] = [
                        'nama' => str_replace('_', ' ', $key),
                        'nominal' => (int) $val,
                    ];
                }
            }

            // ✅ FIX: is_group now correctly uses $savedAbsensi (not undefined $absensi)
            $isGroup = $savedAbsensi
                ? $savedAbsensi->absensiBorongan->isNotEmpty()
                : false;

            return [
                $item->id => [
                    'nama' => $item->pekerja->nama,
                    'nik' => $item->pekerja->nik,
                    'initials' => strtoupper(substr($item->pekerja->nama, 0, 2)),

                    'has_absen' => $savedAbsensi !== null,
                    'is_group' => $isGroup,
                    'is_individual' => $savedAbsensi !== null && ! $isGroup,

                    'existing_tunjangan' => $savedAbsensi?->tunjangan?->kategori ?? null,
                    'existing_keterangan_tunjangan' => $savedTunjangan?->keterangan ?? '',
                    'pkwt_tunjangan' => $pkwtTunjanganParsed,

                    'existing_potongan' => $uiPotongan,
                    'existing_keterangan_potongan' => $savedPotongan?->keterangan ?? '',

                    'existing_status' => $savedAbsensi?->detilBorongan->first()?->status_kehadiran ?? null,
                ],
            ];
        });

        $totalHadir = Absensi::where('id_unit', $unit->id)
            ->where('tgl_absensi', $date)
            ->whereHas('detilBorongan', fn ($q) => $q->where('status_kehadiran', 1))
            ->count();

        return view('Absensi.detail.main-borongan', compact(
            'unit', 'date', 'workerMap', 'pkwtPekerja',
            'totalHadir', 'barangs', 'barangLookup', 'existingBorongan'
        ));
    }

    public function bulkAbsensiUpdate(Request $request)
    {
        // 1. Validasi
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'data' => 'required|array',
            'data.*.jam_aktual' => 'required|numeric|min:0', // Pastikan jam diisi angka
        ]);

        // Custom validation untuk pesan error yang lebih user-friendly
        $validator->after(function ($validator) use ($request) {
            foreach ($request->data as $pkwtId => $values) {
                if (! isset($values['jam_aktual']) || $values['jam_aktual'] === '') {
                    $pkwt = PKWT::with('pekerja')->find($pkwtId);
                    $nama = $pkwt?->pekerja?->nama ?? "Pekerja #$pkwtId";
                    $validator->errors()->add("data.$pkwtId", "Jam kerja $nama belum diisi.");
                }
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $date = $request->date;
            $errors = [];

            foreach ($request->data as $pkwtId => $values) {
                $pkwt = PKWT::with('unit')->find($pkwtId);
                if (! $pkwt) {
                    continue;
                }

                $absensi = Absensi::where([
                    'id_pekerja' => $pkwt->id_pekerja,
                    'id_unit' => $pkwt->id_unit,
                    'tgl_absensi' => $date,
                ])->first();

                // 2. Jika Absensi belum ada, kita buat Parent-nya
                if (! $absensi) {
                    // 1. Dapatkan atau Buat Parent Absensi
                    $absensi = Absensi::firstOrCreate(
                        ['id_pekerja' => $pkwt->id_pekerja, 'id_unit' => $pkwt->id_unit, 'tgl_absensi' => $date],
                        ['id_pic' => Auth::user()->staff->id ?? Auth::id(), 'tipe' => $pkwt->unit->sistem_pengajian, 'verifikasi' => 0]
                    );
                }

                $detil = $absensi->detilHarian;
                $statusTerlarang = [5, 6]; // 5: Rencana Cuti, 6: Absen
                $statusDilindungi = [2, 3, 4]; // 2: Izin, 3: Cuti, 4: Sakit

                // 2. Siapkan data jam yang akan disimpan
                $dataToSave = [
                    'jam_kerja_normal' => $values['jam_normal'] ?? 0,
                    'jam_kerja_harian' => $values['jam_aktual'] ?? 0,
                    'overtime' => $values['overtime'] ?? 0,
                    'hbn' => $values['is_hbn'] ?? 0,
                    'catatan' => $values['catatan'] ?? null,
                    'updated_by' => Auth::id(),
                ];

                /**
                 * LOGIKA BARU:
                 */
                // KONDISI 1: JIKA STATUS TERLARANG (5 atau 6)
                if ($detil && in_array($detil->status_kehadiran, $statusTerlarang)) {
                    $statusLabel = $detil->status_kehadiran == 5 ? 'Rencana Cuti' : 'Absen';

                    return back()->with('error', "Gagal! Pekerja [{$pkwt->pekerja->nama}] berstatus {$statusLabel}. Ubah status kehadiran secara manual terlebih dahulu sebelum mengisi jam.")->withInput();
                }

                // KONDISI 2: JIKA STATUS IZIN/CUTI/SAKIT (2, 3, 4)
                if ($detil && in_array($detil->status_kehadiran, $statusDilindungi)) {

                    if ($detil->isPaid == 1) {
                        // Izin Dibayar: Hanya update jam, status asli tetap (Izin/Cuti/Sakit)
                        $detil->update($dataToSave);
                    } else {
                        // Izin Tidak Dibayar: Karena ada input jam, ubah jadi HADIR (1)
                        $dataToSave['status_kehadiran'] = 1;
                        $detil->update($dataToSave);
                    }

                } else {
                    // KONDISI 3: INPUT NORMAL (Data Baru, Status 0, atau Status 1)
                    $dataToSave['status_kehadiran'] = 1; // Pastikan jadi Hadir

                    $absensi->detilHarian()->updateOrCreate(
                        ['id_absensi' => $absensi->id],
                        $dataToSave
                    );
                }

                // Reset verifikasi
                $absensi->update(['verifikasi' => 0]);
            }

            DB::commit();

            return back()->with('success', 'Data jam kerja berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menyimpan data: '.$e->getMessage());
        }
    }

    public function bulkAbsensiUpdateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $date = $request->date;

            foreach ($request->data as $pkwtId => $values) {
                $pkwt = PKWT::with('unit', 'hariKerja')->find($pkwtId);

                $dayName = strtolower(\Carbon\Carbon::parse($date)->format('D'));
                $jamKerja = $pkwt->hariKerja->firstWhere('hari', $dayName)->jam_kerja ?? 0;

                // dd($jamKerja);
                if (! $pkwt) {
                    continue;
                }

                /**
                 * ✅ CREATE / GET ABSENSI
                 */
                $absensi = Absensi::firstOrCreate(
                    [
                        'id_pekerja' => $pkwt->id_pekerja,
                        'id_unit' => $pkwt->id_unit,
                        'tgl_absensi' => $date,
                    ],
                    [
                        'id_pic' => Auth::user()->staff->id,
                        'tipe' => $pkwt->unit->sistem_pengajian,
                        'verifikasi' => 0,
                    ],
                );

                /**
                 * ✅ UPDATE OR CREATE DETIL (TANPA DELETE)
                 */
                Detil_Harian::updateOrCreate(
                    ['id_absensi' => $absensi->id],
                    [
                        'jam_kerja_normal' => $jamKerja,
                        'jam_kerja_harian' => 0,
                        'overtime' => 0,
                        'hbn' => 0,
                        'status_kehadiran' => $values['status_kehadiran'],
                        'isPaid' => $values['is_paid_leave'],
                        'catatan' => $values['catatan'] ?? null,
                        'updated_by' => Auth::id(),
                    ],
                );

                /**
                 * 🔄 RESET VERIFIKASI
                 */
                $absensi->update(['verifikasi' => 0]);
            }

            DB::commit();

            return back()->with('success', 'Status presensi berhasil diperbarui.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function bulkAbsensiUpdateTunjangan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            foreach ($request->data as $pkwtId => $values) {
                $pkwt = PKWT::with(['unit', 'pekerja'])->find($pkwtId);
                if (! $pkwt) {
                    continue;
                }

                $absensi = Absensi::where([
                    'id_pekerja' => $pkwt->id_pekerja,
                    'id_unit' => $pkwt->id_unit,
                    'tgl_absensi' => $request->date,
                ])->first();

                if (! $absensi) {
                    throw new \Exception('Absensi untuk '.($pkwt->pekerja->nama ?? 'Pekerja').' belum dibuat pada tanggal tersebut.');
                }

                $kategoriArray = json_decode($values['kategori'], true);

                // ============================================
                // CHECK IF RECORD EXISTS FIRST
                // ============================================
                $existingTunjangan = Tunjangan::where([
                    'id_pekerja' => $pkwt->id_pekerja,
                    'id_unit' => $pkwt->id_unit,
                    'id_absensi' => $absensi->id,
                ])->first();

                if ($existingTunjangan) {
                    // ============================================
                    // UPDATE CASE - Only update updated_by
                    // ============================================
                    $existingTunjangan->update([
                        'kategori' => $kategoriArray,
                        'total' => (float) $values['total'],
                        'keterangan' => $values['keterangan'] ?? null,
                        'updated_by' => Auth::id(),
                    ]);
                    // created_by remains unchanged!
                } else {
                    // ============================================
                    // CREATE CASE - Set both created_by & updated_by
                    // ============================================
                    Tunjangan::create([
                        'id_pekerja' => $pkwt->id_pekerja,
                        'id_unit' => $pkwt->id_unit,
                        'id_absensi' => $absensi->id,
                        'kategori' => $kategoriArray,
                        'total' => (float) $values['total'],
                        'keterangan' => $values['keterangan'] ?? null,
                        'updated_by' => Auth::id(),
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Data tunjangan berhasil disimpan.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menyimpan: '.$e->getMessage())->withInput();
        }
    }

    public function bulkAbsensiUpdatePotongan(Request $request)
    {
        // dd($request->all());
        $validator = Validator::make($request->all(), [
            'date' => 'required|date',
            'data' => 'required|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            foreach ($request->data as $pkwtId => $values) {
                $pkwt = PKWT::with(['unit', 'pekerja'])->find($pkwtId);
                if (! $pkwt) {
                    continue;
                }

                $absensi = Absensi::where([
                    'id_pekerja' => $pkwt->id_pekerja,
                    'id_unit' => $pkwt->id_unit,
                    'tgl_absensi' => $request->date,
                ])->first();

                if (! $absensi) {
                    throw new \Exception('Absensi untuk '.($pkwt->pekerja->nama ?? 'Pekerja').' belum dibuat pada tanggal tersebut.');
                }

                $kategoriArray = json_decode($values['kategori'], true);

                // ============================================
                // CHECK IF RECORD EXISTS FIRST
                // ============================================
                $existingPotongan = Potongan::where([
                    'id_pekerja' => $pkwt->id_pekerja,
                    'id_unit' => $pkwt->id_unit,
                    'id_absensi' => $absensi->id,
                ])->first();

                if ($existingPotongan) {
                    // ============================================
                    // UPDATE CASE - Only update updated_by
                    // ============================================
                    $existingPotongan->update([
                        'kategori' => $kategoriArray,
                        'total' => (float) $values['total'],
                        'keterangan' => $values['keterangan'] ?? null,
                        'updated_by' => Auth::id(),
                    ]);
                    // created_by remains unchanged!
                } else {
                    // ============================================
                    // CREATE CASE - Set both created_by & updated_by
                    // ============================================
                    Potongan::create([
                        'id_pekerja' => $pkwt->id_pekerja,
                        'id_unit' => $pkwt->id_unit,
                        'id_absensi' => $absensi->id,
                        'kategori' => $kategoriArray,
                        'total' => (float) $values['total'],
                        'keterangan' => $values['keterangan'] ?? null,
                        'updated_by' => Auth::id(),
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Data potongan berhasil disimpan.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menyimpan: '.$e->getMessage())->withInput();
        }
    }

    public function bulkAbsensiBoronganUpdate(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'date' => 'required|date',
                'data' => 'required|array|min:1',

                'data.*.*.buktiSuratJalan' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:2048',
            ],
            [
                'date.required' => 'Tanggal tidak boleh kosong.',
                'data.required' => 'Data tidak boleh kosong.',

                'data.*.*.buktiSuratJalan.mimes' => 'Bukti surat jalan harus bentuk PDF / JPG / PNG.',
                'data.*.*.buktiSuratJalan.max' => 'Ukuran bukti surat jalan maksimal 2MB.',
            ],
        );

        $validator->after(function ($validator) use ($request) {
            if (! $request->has('data') || ! is_array($request->data)) {
                return back()
                    ->withErrors(['data' => 'Data absensi tidak ditemukan atau format tidak valid.'])
                    ->withInput();
            }

            foreach ($request->data as $pkwtId => $payload) {
                // 🔴 JIKA ADA STATUS → LEWATI VALIDASI PRODUKSI
                if (isset($payload['status'])) {
                    continue;
                }

                // 🔒 PASTIKAN PAYLOAD PRODUKSI ADALAH ARRAY
                if (! is_array($payload)) {
                    continue;
                }

                $missing = [];

                foreach ($payload as $rowIndex => $row) {
                    // ⛔ skip key non-produksi (jaga-jaga)
                    if (! is_array($row)) {
                        continue;
                    }

                    if (empty($row['id_barang'])) {
                        $missing['barang'] = 'barang';
                    }
                    if (! isset($row['FD'])) {
                        $missing['FD'] = 'FD';
                    }
                    if (! isset($row['act_rej'])) {
                        $missing['act_rej'] = 'ACT Reject';
                    }
                    if (! isset($row['good_mc'])) {
                        $missing['good_mc'] = 'Good MC';
                    }
                    if (! isset($row['totalQTY'])) {
                        $missing['totalQTY'] = 'Total QTY';
                    }
                    if (! isset($row['bayaranPerusahaan'])) {
                        $missing['bayaranPerusahaan'] = 'bayaran perusahaan';
                    }
                    if (! isset($row['bayaranItem'])) {
                        $missing['bayaranItem'] = 'bayaran item';
                    }
                    if (! isset($row['act_rej_max'])) {
                        $missing['act_rej_max'] = 'act rej max';
                    }
                    if (! isset($row['rej_mc_dibebankan'])) {
                        $missing['rej_mc_dibebankan'] = 'rej mc dibebankan';
                    }
                }

                if (! empty($missing)) {
                    $pkwt = PKWT::with('pekerja')->find($pkwtId);
                    $nama = $pkwt?->pekerja?->nama ?? "Pekerja #$pkwtId";

                    $fields = array_values($missing);
                    $last = array_pop($fields);
                    $fieldText = $fields ? implode(', ', $fields).' dan '.$last : $last;

                    $validator->errors()->add("data.$pkwtId", "Data borongan $nama belum lengkap. Mohon isi $fieldText.");
                }
            }
        });

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $date = $request->date;

            foreach ($request->data as $pkwtId => $payload) {
                $pkwt = PKWT::with('unit')->findOrFail($pkwtId);

                /**
                 * ✅ CREATE OR GET ABSENSI FIRST (FIX UTAMA)
                 */
                $absensi = Absensi::firstOrCreate(
                    [
                        'id_pekerja' => $pkwt->id_pekerja,
                        'id_unit' => $pkwt->id_unit,
                        'tgl_absensi' => $date,
                    ],
                    [
                        'id_pic' => Auth::user()->staff->id,
                        'tipe' => $pkwt->unit->sistem_pengajian,
                        'verifikasi' => 0,
                    ],
                );

                /**
                 * 🔥 CLEAR OLD BORONGAN DETAIL
                 * (status ↔ produksi switch safety)
                 */
                Detil_Borongan::where('id_absensi', $absensi->id)->delete();

                /**
                 * PATH A — STATUS KHUSUS (Sakit, Izin, Cuti, Alpha)
                 */
                if (isset($payload['status'])) {
                    Detil_Borongan::create([
                        'id_absensi' => $absensi->id,
                        'id_barang' => 0, // non-produksi
                        'status_kehadiran' => $payload['status'],
                        'FD' => 0,
                        'act_rej' => 0,
                        'good_mc' => 0,
                        'max_rej_subkon' => 0,
                        'rej_mc_beban' => 0,
                        'bayaranPerusahaan' => 0,
                        'bayaranItem' => 0,
                        'catatan' => $payload['catatan'] ?? null,
                        'updated_by' => Auth::id(),
                    ]);
                } /**
                 * PATH B — PRODUKSI (BARANG)
                 */ else {
                    foreach ($payload as $index => $row) {
                        $fileContent = null;

                        if ($request->hasFile("data.$pkwtId.$index.buktiSuratJalan")) {
                            $fileContent = file_get_contents($request->file("data.$pkwtId.$index.buktiSuratJalan")->getRealPath());
                        }

                        Detil_Borongan::create([
                            'id_absensi' => $absensi->id,
                            'id_barang' => $row['id_barang'],
                            'status_kehadiran' => 1, // hadir otomatis
                            'FD' => $row['FD'] ?? 0,
                            'act_rej' => $row['act_rej'] ?? 0,
                            'good_mc' => $row['good_mc'] ?? 0,
                            'max_rej_subkon' => $row['act_rej_max'] ?? 0,
                            'rej_mc_beban' => $row['rej_mc_dibebankan'] ?? 0,
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

            return back()->with('error', 'Gagal menyimpan: '.$e->getMessage());
        }
    }

    public function bulkKelompokAbsensiBoronganUpdate(Request $request, $id_unit, $date)
    {
        // dd($request->all());
        // Validate incoming data
        $validator = Validator::make(
            $request->all(),
            [
                'date' => 'required|date',
                'selected_ids' => 'required|array|min:1',
                'group_data' => 'required|array|min:1',
                'group_data.*.id_barang' => 'required',
                'group_data.*.buktiSuratJalan' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:2048',
            ],
            [
                'date.required' => 'Tanggal tidak boleh kosong.',
                'selected_ids.required' => 'Minimal pilih satu pekerja.',
                'group_data.required' => 'Data produksi tidak boleh kosong.',
                'group_data.*.id_barang.required' => 'Nama barang wajib dipilih untuk setiap baris.',
                'group_data.*.buktiSuratJalan.mimes' => 'Bukti surat jalan harus berupa PDF / JPG / PNG.',
                'group_data.*.buktiSuratJalan.max' => 'Ukuran bukti surat jalan maksimal 2MB.',
            ],
        );

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $date = $request->date;
            $selectedIds = $request->selected_ids; // array of PKWT IDs
            $groupData = $request->group_data;    // shared production rows

            // ─────────────────────────────────────────────────────────────
            // STEP 1: Pre-build the borongan detail rows (shared data)
            //         We'll create ONE Detil_Borongan per barang row,
            //         then attach it to EACH worker's Absensi via pivot.
            // ─────────────────────────────────────────────────────────────

            // Collect the created Detil_Borongan IDs to attach later
            // Shape: [ [detil_borongan_id, ...], ... ] — one set per barang row
            $detilBoronganIds = [];

            foreach ($groupData as $rowIndex => $row) {
                // Read the uploaded file (if any) for this row
                $fileContent = null;
                if ($request->hasFile("group_data.$rowIndex.buktiSuratJalan")) {
                    $fileContent = file_get_contents(
                        $request->file("group_data.$rowIndex.buktiSuratJalan")->getRealPath()
                    );
                }

                // Create ONE shared Detil_Borongan row per barang
                // Note: id_absensi is NULL here — we link via pivot, not FK
                $detil = Detil_Borongan::create([
                    'id_absensi' => null, // will be linked via pivot
                    'id_barang' => $row['id_barang'],
                    'status_kehadiran' => 1,    // hadir otomatis
                    'FD' => $row['FD'] ?? 0,
                    'act_rej' => $row['act_rej'] ?? 0,
                    'good_mc' => $row['good_mc'] ?? 0,
                    'max_rej_subkon' => $row['act_rej_max'] ?? 0,
                    'rej_mc_beban' => $row['rej_mc_dibebankan'] ?? 0,
                    'bayaranPerusahaan' => $row['bayaranPerusahaan'] ?? 0,
                    'bayaranItem' => $row['bayaranItem'] ?? 0,
                    'buktiSuratJalan' => $fileContent,
                    'catatan' => $row['catatan'] ?? null,
                    'updated_by' => Auth::id(),
                ]);

                $detilBoronganIds[] = $detil->id;
            }

            // ─────────────────────────────────────────────────────────────
            // STEP 2: For each selected worker, create/get their Absensi,
            //         then attach all Detil_Borongan via pivot.
            // ─────────────────────────────────────────────────────────────

            foreach ($selectedIds as $pkwtId) {
                $pkwt = PKWT::with('unit')->findOrFail($pkwtId);

                // Create or get the Absensi record for this worker + date
                $absensi = Absensi::firstOrCreate(
                    [
                        'id_pekerja' => $pkwt->id_pekerja,
                        'id_unit' => $pkwt->id_unit,
                        'tgl_absensi' => $date,
                    ],
                    [
                        'id_pic' => Auth::user()->staff->id,
                        'tipe' => $pkwt->unit->sistem_pengajian,
                        'verifikasi' => 0,
                    ],
                );

                // Clear old pivot links for this absensi (to allow re-submission)
                Absensi_Borongan::where('id_absensi', $absensi->id)->delete();

                // Attach each shared Detil_Borongan to this worker's Absensi
                // This inserts rows into the pivot table: absensi_borongan
                foreach ($detilBoronganIds as $detilId) {
                    Absensi_Borongan::create([
                        'id_absensi' => $absensi->id,
                        'id_detil_borongan' => $detilId,
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Data absensi kelompok berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menyimpan: '.$e->getMessage());
        }
    }

    public function getAdjustments(Request $request)
    {
        try {
            $workerIds = $request->worker_ids;
            $start = $request->tanggal_mulai;
            $end = $request->tanggal_akhir;

            $results = [];

            foreach ($workerIds as $id) {
                // Kita hitung berdasarkan relasi tgl_absensi di tabel absensi
                $totalTunjangan = Tunjangan::where('id_pekerja', $id)
                    ->whereHas('absensi', function ($q) use ($start, $end) {
                        $q->whereBetween('tgl_absensi', [$start, $end]);
                    })->sum('total');

                $totalPotongan = Potongan::where('id_pekerja', $id)
                    ->whereHas('absensi', function ($q) use ($start, $end) {
                        $q->whereBetween('tgl_absensi', [$start, $end]);
                    })->sum('total');

                $results[$id] = [
                    'pembayaran_lain' => (int) $totalPotongan,
                    'tunjangan_bayaran' => (int) $totalTunjangan,
                ];
            }

            return response()->json($results);
        } catch (\Exception $e) {
            // Jika error, kirim pesan error dalam format JSON (bukan HTML)
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
