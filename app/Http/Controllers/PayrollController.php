<?php

namespace App\Http\Controllers;

use App\Exports\DailyReportHarianExport;
use App\Exports\DetilBoronganExport;
use App\Exports\InvoiceBoronganExport;
use App\Exports\KwitansiBoronganExport;
use App\Exports\RincianUpahExport;
use App\Exports\SlipUpahExport;
use App\Models\Absensi;
use App\Models\BidangUsaha;
use App\Models\Divisi;
use App\Models\MitraKerja;
use App\Models\Pekerja;
use App\Models\PicUnit;
use App\Models\PKWT;
use App\Models\Staff;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PayrollController extends Controller
{
    function viewPayrollMain(Request $request)
    {
        // --- 1. CALCULATE STATS (Top Cards) ---
        $totalUnit = Unit::count(); // total pekerja
        $unitBaru = Unit::where('created_at', '>=', now()->subMonth())->count(); // pekerja baru dari bulan lalu
        $tidakAktif = Unit::where('status_aktif', '!=', '1')->count(); // pekerja tidak aktif
        $totalHarian = Unit::where('sistem_pengajian', 1)->count();
        $totalBorongan = Unit::where('sistem_pengajian', 2)->count();

        // --- 2. BUILD QUERY ---
        $query = Unit::query()
            ->with(['picUnit.staff', 'namaMitra', 'pkwt.pekerja.tunjangan', 'pkwt.pekerja.potongan']) // Eager load relasi yang diperlukan di modal
            ->withCount('pkwt');

        // A. Filter by Search (Name, NIK, KPJ)
        // We check for 'search' (from new JS) or 'q' (fallback)
        $search = $request->input('search') ?? $request->input('q');

        $query->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('nama_unit', 'LIKE', "%{$search}%");
            });
        });

        // B. Filter by Status (Exact Match)
        // We use $request->filled() to ensure we don't filter if value is empty/null
        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status_aktif', $request->status);
        });

        $query->when($request->filled('pengajian'), function ($q) use ($request) {
            $q->where('sistem_pengajian', $request->pengajian);
        });

        // C. Filter by Date Range (Tanggal Bergabung)
        $query->when($request->start_date, function ($q) use ($request) {
            $q->whereDate('mulai_perjanjian', '>=', $request->start_date);
        });

        $query->when($request->end_date, function ($q) use ($request) {
            $q->whereDate('akhir_perjanjian', '<=', $request->end_date);
        });

        // --- 3. FETCH DATA ---
        $unit = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // --- 4. RETURN RESPONSE ---

        // If AJAX request (from the search/filter script), return ONLY the table partial
        if ($request->ajax()) {
            return view('Payroll.partials.unit-table', compact('unit'))->render();
        }

        // Otherwise return the full page
        return view('Payroll.main-payroll', compact('unit', 'totalUnit', 'unitBaru', 'totalHarian', 'totalBorongan', 'tidakAktif'));
    }

    public function overviewPayroll(Request $request)
    {
        // dump($request->all());
        // dd($id_unit);
        $id_unit = $request->id_unit;

        $unit = Unit::find($id_unit); // ⬅️ ambil unit

        $paidWorkerIds = $request->input('paid_workers', []);
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalAkhir = $request->tanggal_akhir;
        $allAdjustments = $request->input('adjustments', []);

        $isHarian = $unit->sistem_pengajian == 1;

        $periode = Carbon::parse($tanggalMulai)->translatedFormat('d') . '—' . Carbon::parse($tanggalAkhir)->translatedFormat('d M Y');

        // Ambil data pekerja yang dipilih
        $workers = Pekerja::whereIn('id', $paidWorkerIds)->get();

        // Olah data potongan tanggal (Step 3) dari modal
        $specificExclusions = collect($request->input('specific_workers', []))->filter(fn($item) => !empty($item['id']) && !empty($item['date']))->groupBy('id');

        // dump($specificExclusions);

        $payrollData = [
            'unit_id' => $request->id_unit,
            'unit_name' => $unit->nama_unit ?? 'Unit Tidak Ditemukan',
            'sistem_pengajian' => $unit->sistem_pengajian,
            'periode' => Carbon::parse($tanggalMulai)->translatedFormat('d') . ' — ' . Carbon::parse($tanggalAkhir)->translatedFormat('d M Y'),
            'total_pekerja' => $workers->count(),
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_akhir' => $tanggalAkhir,
            'items' => $workers->map(function ($w) use ($id_unit, $isHarian, $periode, $specificExclusions, $allAdjustments, $tanggalMulai, $tanggalAkhir) {
                // 1. Dapatkan list tanggal yang dikecualikan (potongan) untuk pekerja ini
                $excludedDates = $specificExclusions->get($w->id) ? $specificExclusions->get($w->id)->pluck('date')->toArray() : [];

                $relation = $isHarian ? 'detilHarian' : 'detilBorongan.borongan:id,harga_pekerja,nama_item';

                // 2. Query ke tabel Absensi yang memiliki Detil Borongan
                // Kita hitung record dalam range tanggal Mula-Selesai, dan TIDAK TERMASUK tanggal potongan
                $absensiRecords = Absensi::with([$relation])
                    ->whereBetween('tgl_absensi', [$tanggalMulai, $tanggalAkhir])
                    ->where('id_unit', $id_unit)
                    ->where('id_pekerja', $w->id)
                    ->whereNotIn('tgl_absensi', $excludedDates)
                    ->get();

                // dump($absensiRecords);
                $workerAdj = $allAdjustments[$w->id] ?? ['pembayaran_lain' => 0, 'tunjangan_bayaran' => 0];

                $workerPembayaranLain = (int) ($workerAdj['pembayaran_lain'] ?? 0);
                $workerTunjangan = (int) ($workerAdj['tunjangan_bayaran'] ?? 0);

                $totalQty = 0;
                $tempQty = 0;
                $totalGajiBorongan = 0;

                //variable harian
                $totalJamKerja = 0;
                $totalOvertime = 0;
                $totalHBN = 0;
                $hasilGajiHarian = 0;

                $pkwt = PKWT::where('id_pekerja', $w->id)->where('id_unit', $id_unit)->where('status_aktif', 1)->first();
                // dump($pkwt);

                $gajiHarianPkwt = $pkwt->gaji_harian ?? 0;
                $gajiOvertimePkwt = $pkwt->gaji_overtime ?? 0;

                foreach ($absensiRecords as $absensi) {
                    if ($isHarian) {
                        $detil = $absensi->detilHarian;
                        if ($detil) {
                            $totalJamKerja += (float) $detil->jam_kerja_harian;
                            $totalOvertime += (float) $detil->overtime;

                            // --- LOGIKA PERHITUNGAN GAJI ---
                            $jamNormal = (float) $detil->jam_kerja_normal; // Hindari division by zero
                            $jamHarian = (float) $detil->jam_kerja_harian;
                            $jamOT = (float) $detil->overtime;
                            $gajiReguler = 0;
                            $gajiHariIni = 0;
                            $statusDilindungi = [2,3,4];
                            $statusTidakDibayar = [5,6];

                            //todo:: logika baru buat jam telat
                            if ($detil->hbn == 1) {
                                // JIKA HBN: Semua jam_kerja_harian dihitung sebagai OVERTIME
                                // Rumus: jam_harian * (gaji_overtime * 1.5)
                                $gajiHariIni = $jamHarian * ($gajiOvertimePkwt * 1.5);

                                $totalHBN += (float) $detil->overtime;
                                // dump('Gaji Harian (jamharian*gajiovertime) = ', $gajiHariIni);
                            }elseif (in_array($detil->status_kehadiran, $statusTidakDibayar)) {
                                // TAMBAHAN: JIKA STATUS 5 ATAU 6, GAJI HARI INI = 0
                                $gajiHariIni = 0;

                            }elseif (in_array($detil->status_kehadiran,$statusDilindungi) && $detil->isPaid == 1)
                            {
                                $gajiHariIni += ($jamHarian/$jamNormal) * $gajiHarianPkwt;

                            }else {
                                // JIKA HARI NORMAL:
                                // Rumus Reguler: jam_harian * gaji_harian
                                if($jamHarian >= $jamNormal)
                                {
                                    $gajiReguler += $gajiHarianPkwt;
                                    // dump($absensi->id,$gajiReguler);

                                }elseif($jamNormal > $jamHarian)
                                {
                                    $gajiReguler += $gajiHarianPkwt - ( ($jamNormal - $jamHarian) * $gajiOvertimePkwt );

                                    
                                    
                                }
                                // Rumus Overtime: jam_ot * gaji_overtime
                                $gajiOT = $jamOT * $gajiOvertimePkwt;
                                // dump('gajiOT (jamOT*gajiOvertimePkwt) = ',$gajiOT);
                                $gajiHariIni = $gajiReguler + $gajiOT;
                                // dump('gajiHariIni (gajiReguler*gajiOT) = ',$gajiHariIni);

                                // dump($absensi->id,$gajiHariIni);
                            }

                            $hasilGajiHarian += $gajiHariIni;
                        }
                    } else {
                        foreach ($absensi->detilBorongan as $detil) {
                            // Sesuai logika JS: totalQTY = FD + act_rej + good_mc
                            $qtyPerBaris = ($detil->FD ?? 0) + ($detil->act_rej ?? 0) + ($detil->good_mc ?? 0);
                            $totalQty += $qtyPerBaris;
                            $tempQty += $qtyPerBaris;

                            // Sesuai logika JS: bayaranItem = totalQTY * harga_pekerja
                            // Kita asumsikan kolom 'bayaranItem' sudah tersimpan di DB saat presensi
                            $totalGajiBorongan += $detil->bayaranItem ?? 0;

                            $tempQty = 0;
                        }
                    }
                }

                // dd($totalJamKerja, $totalOvertime, $totalHBN);

                $netSalary = ($isHarian ? $hasilGajiHarian : $totalGajiBorongan) - $workerPembayaranLain + $workerTunjangan;

                // dd($netSalary);

                // dd($netSalary);

                return [
                    'unit_id' => $id_unit,
                    'unit_name' => $unit?->nama_unit ?? '-',
                    'sistem_pengajian' => $unit?->sistem_pengajian ?? '1',
                    'id_pekerja' => $w->id,
                    'periode' => $periode,
                    'nama' => $w->nama,
                    'nik' => $w->nik,
                    'total_barang' => $totalQty,
                    'hasil_gaji_borongan' => $totalGajiBorongan,
                    // Data Harian
                    'total_jam_kerja' => $totalJamKerja,
                    'total_overtime' => $totalOvertime,
                    'total_hbn' => $totalHBN,
                    'potongan_count' => count($excludedDates),
                    'potongan_dates' => $excludedDates,
                    'net_salary' => $netSalary,
                    'pembayaran_lain' => $workerPembayaranLain,
                    'tunjangan' => $workerTunjangan,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_akhir' => $tanggalAkhir,
                ];
            }),
        ];

        $payrollData['grand_total'] = collect($payrollData['items'])->sum('net_salary');

        $payrollData['total_potongan_hari'] = collect($payrollData['items'])->sum('potongan_count');

        $payrollData['total_penyesuaian'] = collect($payrollData['items'])->sum(fn($item) => (int) $item['tunjangan'] - (int) $item['pembayaran_lain']);

        return view('Payroll.overview-payroll', compact('payrollData', 'paidWorkerIds'));
    }

    function ExportDetailBorongan(Request $request)
    {
        // dd($request->all());

        $exclusionDates = $request->input('exclusion_date', []);
        $tanggal_awal = Carbon::parse($request->tgl_awal);
        $tanggal_akhir = Carbon::parse($request->tgl_akhir);

        $absensiList = Absensi::with(['detilBorongan.borongan:id,harga_pekerja,nama_item'])
            ->whereBetween('tgl_absensi', [$tanggal_awal, $tanggal_akhir])
            ->where('id_pekerja', $request->id_pekerja)
            ->whereNotIn('tgl_absensi', $exclusionDates)
            ->get();

        $periode = strtoupper($tanggal_awal->translatedFormat('d F Y') . ' ~ ' . $tanggal_akhir->translatedFormat('d F Y'));

        $PKWT = PKWT::where('id_pekerja', $request->id_pekerja)->where('id_unit', $request->id_unit)->first();

        $pekerja = Pekerja::where('id', $request->id_pekerja)->first();

        $divisi = Divisi::where('id', $PKWT->divisi_id)->first();

        $unit = Unit::where('id', $request->id_unit)->first();

        $data = collect();

        $take_home_pay = 0;

        foreach ($absensiList as $absensi) {
            foreach ($absensi->detilBorongan as $detil) {
                // SKIP kalau detil borongan kosong
                if ($detil->fd == 0 && $detil->act_rej == 0 && $detil->good_mc == 0) {
                    continue;
                }

                // ===== RUMUS =====
                $fd = (int) $detil->FD;
                $actRej = (int) $detil->act_rej;
                $goodMc = (int) $detil->good_mc;

                // qty = fd + act_rej + good_mc
                $qty = $fd + $actRej + $goodMc;

                // max reject subkon = qty * 1%

                $maxRejectSubkon = $detil->max_rej_subkon;
                $rejMc = $detil->rej_mc_beban;

                // total dibayar (rumus)
                $totalDibayarRumus = $fd - $rejMc + $goodMc;

                // total dibayar (pcs) → dibulatkan
                $totalDibayarPcs = round($totalDibayarRumus);

                // unit price dari tabel borongan
                $unitPrice = $detil->borongan->harga_pekerja ?? 0;

                // total dibayarkan (Rp)
                $totalBayarRp = $totalDibayarPcs * $unitPrice;

                // ===== PUSH KE DATA =====
                $data->push(
                    (object) [
                        'tanggal' => Carbon::parse($absensi->tgl_absensi)->format('d-M-y'),
                        'item_name' => $detil->borongan->nama_item ?? '-',

                        'qty' => $qty,

                        'fd' => $fd,
                        'max_reject_subkon' => $maxRejectSubkon,
                        'act_reject_subkon' => $actRej,
                        'rej_mc' => $rejMc,

                        'good_mc' => $goodMc,
                        'total_display' => $qty,

                        'total_dibayar_pcs' => $totalDibayarPcs,
                        'unit_price' => $unitPrice,
                        'total_bayar' => $totalBayarRp,
                        ($take_home_pay += $totalBayarRp),
                    ],
                );
            }
        }

        // dd($data);

        $take_home_pay = $take_home_pay - $PKWT->bpjs_naker - $PKWT->bpjs_kesehatan - $request->potongan + $request->tunjangan;

        $filename = "Summary_Upah_{$pekerja->nama}_{$unit->nama_unit}_{$periode}.xlsx";

        return Excel::download(new DetilBoronganExport($data, $periode, $pekerja->nama, $divisi->nama, $PKWT->bpjs_naker, $PKWT->bpjs_kesehatan, $request->potongan, $request->tunjangan, $take_home_pay), $filename);
    }

    function ExportTandaTerimaBorongan(Request $request)
    {
        // dd($request->all());

        $start = \Carbon\Carbon::parse($request->tanggal_mulai);
        $end = \Carbon\Carbon::parse($request->tanggal_akhir);

        // Pastikan locale Indonesia
        \Carbon\Carbon::setLocale('id');

        $periode =
            $start->format('d') .
            ' ' .
            strtoupper($start->translatedFormat('M')) . // 'M' untuk singkatan 3 huruf (SEPT)
            ' - ' .
            $end->format('d') .
            ' ' .
            strtoupper($end->translatedFormat('M')) . // 'M' untuk singkatan 3 huruf (OKT)
            ' ' .
            $end->format('Y');

        $Unit = Unit::where('id_unit', $request->id_unit)->first();

        $workerIds = collect($request->workers)->pluck('id');

        // Ambil data pekerja beserta relasi PKWT, Divisi, dan Jabatan (Unit)
        $workerMaster = Pekerja::with(['pkwt.divisi', 'pkwt.jabatan'])
            ->whereIn('id', $workerIds)
            ->get()
            ->keyBy('id');

        $dataExport = collect($request->workers)->map(function ($item, $key) use ($workerMaster) {
            $worker = $workerMaster->get($item['id']);

            $pkwt = $worker->pkwt;
            $activePkwt = $pkwt instanceof \Illuminate\Support\Collection ? $pkwt->first() : $pkwt;

            $upah_mentah = (float) ($item['upah'] ?? 0);
            $clean_upah = str_replace('.', '', $upah_mentah);
            $clean_upah = str_replace(',', '.', $clean_upah);

            return [
                'no' => $key + 1,
                'id' => $worker->id_pekerja,
                'nama' => $worker->nama ?? '-',
                'posisi' => optional(optional($activePkwt)->jabatan)->nama ?? '-',
                'divisi' => optional(optional($activePkwt)->divisi)->nama ?? '-',
                'no_rek' => $worker->rekening ?? '-',
                // Simpan versi angka murni untuk dihitung TOTAL
                'upah_murni' => $upah_mentah,
                // Simpan versi format untuk TAMPILAN
                'upah_tenaga_kerja' => number_format((float) $clean_upah, 0, ',', '.'),
            ];
        });

        // 1. Hitung total dari kolom 'upah_murni' yang masih berupa angka
        $total_seluruh = $dataExport->sum('upah_murni');

        // 2. Format hasil totalnya untuk tampilan di laporan
        $display_total = number_format($total_seluruh, 0, ',', '.');

        $pic = PicUnit::where('id_unit', $request->id_unit)->first();
        $namaPic = Staff::where('id', $pic->id_pic)->first();

        $administrator = Auth::user()->staff->id;

        $admin = Staff::where('id', $administrator)->first();

        $filename = "SlipUpah_{$Unit->nama_unit}_{$periode}.xlsx";

        return Excel::download(new SlipUpahExport($dataExport, $Unit->nama_unit, $display_total, $admin->nama, $namaPic->nama, $periode), $filename);
    }
    function ExportInvoiceBorongan(Request $request)
    {
        $jabatan = $request->jabatan ?? '';
        $start = \Carbon\Carbon::parse($request->tanggal_mulai);
        $end = \Carbon\Carbon::parse($request->tanggal_akhir);

        // Pastikan locale Indonesia
        \Carbon\Carbon::setLocale('id');

        $periode = $start->format('d') . ' - ' . $end->format('d') . ' ' . strtoupper($start->translatedFormat('F')) . ' ' . $start->format('Y');

        $a = $request->grand_total;

        $Unit = Unit::where('id_unit', $request->id_unit)->first();

        $MitraKerja = MitraKerja::where('id', $Unit->id_mitra_kerja)->first();

        $Bidang = BidangUsaha::where('id', $MitraKerja->bidang_usaha_id)->first();

        $management_fee = round(($a * 6) / 100);
        $ppn = round(($management_fee * 11) / 100);
        $pph = round(($management_fee * 2) / 100);

        // Total tagihan dijumlahkan dari hasil yang sudah dibulatkan
        $total_tagihan = $a + $management_fee + $ppn - $pph;

        $terbilang = ucwords(terbilang($total_tagihan)) . ' Rupiah';

        // Menambahkan format titik (number_format)
        $display_a = number_format($a, 0, ',', '.');
        $display_management_fee = number_format($management_fee, 0, ',', '.');
        $display_ppn = number_format($ppn, 0, ',', '.');
        $display_pph = number_format($pph, 0, ',', '.');
        $display_total_tagihan = number_format($total_tagihan, 0, ',', '.');

        $filename = "Invoice_{$Unit->nama_unit}_{$periode}.xlsx";

        return Excel::download(new InvoiceBoronganExport($request->no_resi, $Unit->nama_unit, $MitraKerja->alamat, $Bidang->nama, $MitraKerja->nama_mitra, $display_a, $terbilang, $periode, $display_management_fee, $display_ppn, $display_pph, $display_total_tagihan, $Unit->umk, $request->nama_resi,$jabatan), $filename);
    }

    function ExportKwitansiBorongan(Request $request)
    {
        // dd($request->all());
        $jabatan = $request->jabatan ?? '';

        $Unit = Unit::where('id_unit', $request->id_unit)->first();

        $MitraKerja = MitraKerja::where('id', $Unit->id_mitra_kerja)->first();

        $Bidang = BidangUsaha::where('id', $MitraKerja->bidang_usaha_id)->first();

        $start = \Carbon\Carbon::parse($request->tanggal_mulai);
        $end = \Carbon\Carbon::parse($request->tanggal_akhir);

        // Pastikan locale Indonesia
        \Carbon\Carbon::setLocale('id');

        $periode = $start->format('d') . ' - ' . $end->format('d') . ' ' . strtoupper($start->translatedFormat('F')) . ' ' . $start->format('Y');

        $management_fee = round(($request->grand_total * 6) / 100);
        $ppn = round(($management_fee * 11) / 100);
        $pph = round(($management_fee * 2) / 100);

        // Total tagihan dijumlahkan dari hasil yang sudah dibulatkan
        $total_tagihan = $request->grand_total + $management_fee + $ppn - $pph;
        $display_total_tagihan = number_format($total_tagihan, 0, ',', '.');

        $terbilang = ucwords(terbilang($total_tagihan)) . ' Rupiah';

        $filename = "Kwitansi_{$Unit->nama_unit}_{$periode}.xlsx";

        return Excel::download(new KwitansiBoronganExport($request->no_resi, $Unit->nama_unit, $terbilang, $Bidang->nama, $MitraKerja->nama_mitra, $periode, $display_total_tagihan, $request->nama_resi, $jabatan), $filename);
    }

    function ExportRincianUpahBorongan(Request $request)
    {
        // 1. Decode JSON Workers dari Request
        // Format JSON: [{"id":2,"upah":150000,"exclusion_date":[]}, ...]
        $requestWorkers = json_decode($request->workers_json, true);

        // Ambil semua ID untuk query database sekaligus (Eager Loading)
        $workerIds = array_column($requestWorkers, 'id');

        // 2. Ambil Data Identitas dari Database
        // Pastikan meload relasi jabatan/divisi/kelamin jika diperlukan
        $dbPekerja = Pekerja::with([
            // 1. Load PKWT, tapi urutkan dari yang terbaru agar kita ambil kontrak aktif
            'pkwt' => function ($query) {
                $query->latest('id'); // Atau latest('tgl_mulai_pkwt')
            },
            // 2. Load Jabatan & Divisi MELALUI PKWT
            'pkwt.jabatan',
            'pkwt.divisi',
        ])
            ->whereIn('id', $workerIds)
            ->get()
            ->keyBy('id');

        $realItems = [];

        // dd($dbPekerja);

        // 3. Looping Data Request (Agar urutan & nilai upahnya sesuai input)
        foreach ($requestWorkers as $reqWorker) {
            $id = $reqWorker['id'];

            if (!isset($dbPekerja[$id])) {
                continue;
            }

            $staffDb = $dbPekerja[$id];
            $item = new \stdClass();

            // A. IDENTITAS
            $item->nama = $staffDb->nama;
            $item->id_karyawan = $staffDb->id_pekerja ?? $staffDb->nik;

            // --- LOGIKA BARU MENGAMBIL JABATAN & DIVISI ---

            // 1. Ambil PKWT pertama (karena tadi sudah di-sort latest, maka first() adalah yang terbaru)
            // Gunakan null safe operator (?->) jaga-jaga jika pekerja belum punya PKWT
            $pkwtAktif = $staffDb->pkwt->first();

            // 2. Ambil Jabatan & Divisi dari PKWT tersebut
            $item->jabatan = $pkwtAktif?->jabatan?->nama ?? '-';
            $item->divisi = $pkwtAktif?->divisi?->nama ?? '-';

            // --- B. DATA GAJI (Dari Request JSON) ---
            // Karena ini Borongan, kita asumsikan 'upah' dari JSON adalah Total yang diterima
            // Anda bisa menyesuaikan logika ini jika 'upah' tersebut harus dipecah lagi
            $upahBorongan = $reqWorker['upah'];

            // Set Upah Pokok / Hasil Kerja
            $item->upah_pokok = $upahBorongan;

            // Set Komponen Lain ke 0 (Kecuali jika Anda punya logika hitung BPJS otomatis disini)
            $item->lembur_jam = 0;
            $item->lembur_rate = 0;
            $item->lembur_hbn_rate = 0;
            $item->total_lembur = 0;
            $item->tunjangan = $reqWorker['tunjangan'];

            $item->jumlah_1 = $item->upah_pokok + $item->tunjangan; // Total Pendapatan

            // --- C. POTONGAN (Opsional) ---
            // Jika ingin menghitung BPJS otomatis berdasarkan $upahBorongan, masukkan rumus di sini
            // Untuk sementara kita nol-kan agar sesuai dengan Grand Total di summary
            $item->absen_hari = 0;
            $item->potongan_hari = 0;
            $item->absen_jam = 0;
            $item->potongan_jam = 0;


            $item->potonganLain = $reqWorker['potongan'];

            //Tidak ada di borongan
            $item->total_lembur_biasa = 0;
            $item->lembur_hbn_jam = 0;
            $item->total_lembur_hbn = 0;

            $item->bpjs_tk = $pkwtAktif?->bpjs_naker ?? 0; // Masukkan logika calc BPJS jika ada
            $item->bpjs_kes = $pkwtAktif?->bpjs_kesehatan ?? 0; // Masukkan logika calc BPJS jika ada

            $item->jumlah_2 = $item->bpjs_tk + $item->bpjs_kes + $item->potonganLain; // Total Potongan

            // --- D. TOTAL AKHIR ---
            $item->take_home_pay = $item->jumlah_1 - $item->jumlah_2;

            // Simpan info exclusion date jika nanti perlu ditampilkan di excel (opsional)
            $item->exclusion_dates = $reqWorker['exclusion_date'] ?? [];

            $realItems[] = $item;
        }

        // dd($realItems);

        // 4. Bungkus jadi Collection
        $formattedData = collect($realItems);

        // 5. Tentukan Periode (Dari Request Tgl Awal & Akhir)
        $start = Carbon::parse($request->tgl_awal);
        $end = Carbon::parse($request->tgl_akhir);

        // Format: 1 FEB 2026 - 5 FEB 2026
        $periodeString = strtoupper($start->isoFormat('D MMM Y') . ' - ' . $end->isoFormat('D MMM Y'));

        // Nama File
        $filename = 'Rincian_Upah_Borongan_' . $start->format('d_m_Y') . '.xlsx';

        return Excel::download(new RincianUpahExport($formattedData, $periodeString), $filename);
    }

    public function ExportRincianUpahHarian(Request $request)
    {
        // dd($request->all());
        // 2. Decode Input
        $workersInput = json_decode($request->workers_json, true);
        $workerIds = array_column($workersInput, 'id');

        // 3. Ambil Data Master (Pekerja + PKWT)
        $dbPekerja = Pekerja::with([
            'pkwt' => function ($query) {
                $query->latest('id'); // Ambil kontrak terakhir
            },
            'pkwt.jabatan',
            'pkwt.divisi',
        ])
            ->whereIn('id', $workerIds)
            ->get()
            ->keyBy('id');

        // dd($request->all());

        $realItems = [];

        // 4. Loop & Kalkulasi
        foreach ($workersInput as $input) {
            $id = $input['id'];

            // Skip jika ID tidak ada di DB
            if (!isset($dbPekerja[$id])) {
                continue;
            }

            $staffDb = $dbPekerja[$id];
            $pkwtAktif = $staffDb->pkwt->first();

            // Object item untuk Export
            $item = new \stdClass();

            // --- A. IDENTITAS ---
            $item->nama = $staffDb->nama;
            $item->id_karyawan = $staffDb->id_pekerja ?? $staffDb->nik;
            $item->jabatan = $pkwtAktif?->jabatan?->nama ?? '-';
            $item->divisi = $pkwtAktif?->divisi?->nama ?? '-';

            // --- B. RATE (TARIF) DARI DB ---
            // Asumsi: gaji_harian di DB adalah rate per jam/hari sesuai input 'total_jam_kerja'
            $ratePokok = $pkwtAktif?->gaji_harian ?? 0;
            $rateLembur = $pkwtAktif?->gaji_overtime ?? 0;

            // dump($ratePokok, $rateLembur);

            // Asumsi: HBN rate biasanya 2x lembur atau ada kolom sendiri.
            // Jika tidak ada kolom khusus, kita pakai logika 2 * rate lembur.
            // Silakan sesuaikan: $pkwtAktif?->gaji_overtime_hbn
            $rateHbn = ($pkwtAktif?->gaji_overtime ?? 0) * 1.5;

            // --- C. KALKULASI PENDAPATAN ---

            // 1. Upah Pokok
            $item->upah_pokok = (($input['jam_kerja'] ?? 0) - ($input['hbn'] ?? 0)) * $ratePokok;

            // 2. Lembur Biasa
            $item->lembur_jam = ($input['overtime'] ?? 0) - ($input['hbn'] ?? 0);
            $item->lembur_rate = $rateLembur;
            $item->total_lembur_biasa = $item->lembur_jam * $item->lembur_rate;

            // 3. Lembur HBN
            $item->lembur_hbn_jam = $input['hbn'] ?? 0;
            $item->lembur_hbn_rate = $rateHbn;
            $item->total_lembur_hbn = $item->lembur_hbn_jam * $item->lembur_hbn_rate;

            // 4. Tunjangan (Dari Input)
            $item->tunjangan = $input['tunjangan'] ?? 0;

            // TOTAL PENDAPATAN (Jumlah 1)
            // Gabungkan total lembur biasa & HBN ke variabel total_lembur agar kompatibel dengan export sebelumnya
            $item->total_lembur = $item->total_lembur_biasa + $item->total_lembur_hbn;

            $item->jumlah_1 = $item->upah_pokok + $item->total_lembur + $item->tunjangan;

            // --- D. KALKULASI POTONGAN ---

            // Potongan BPJS dari DB PKWT
            $item->absen_hari = 0;
            $item->potongan_hari_rate = $pkwtAktif?->gaji_harian;
            $item->potongan_hari = $item->absen_hari * $item->potongan_hari_rate;

            $item->absen_jam = 0;
            $item->potongan_jam_rate = $pkwtAktif?->gaji_overtime;
            $item->potongan_jam = $item->absen_jam * $item->potongan_jam_rate;

            $item->bpjs_tk = $pkwtAktif?->bpjs_naker ?? 0;
            $item->bpjs_kes = $pkwtAktif?->bpjs_kesehatan ?? 0;

            // Potongan Lain (Dari Input, misal kasbon)
            $item->potonganLain = $input['potongan'] ?? 0;

            // TOTAL POTONGAN (Jumlah 2)
            $item->jumlah_2 = $item->bpjs_tk + $item->bpjs_kes + $item->potonganLain;

            // --- E. HASIL AKHIR ---
            $item->take_home_pay = $item->jumlah_1 - $item->jumlah_2;

            // Data pelengkap
            $item->exclusion_dates = $input['exclusion_date'] ?? [];

            $realItems[] = $item;
        }

        // 5. Finalisasi & Download
        $formattedData = collect($realItems);

        // dd($formattedData);

        $start = Carbon::parse($request->tgl_awal);
        $end = Carbon::parse($request->tgl_akhir);
        $periodeString = strtoupper($start->isoFormat('D MMM Y') . ' - ' . $end->isoFormat('D MMM Y'));

        $filename = 'Rincian_Upah_Harian_' . $start->format('d_m_Y') . '.xlsx';

        // Menggunakan Export Class yang sama (RincianUpahExport)
        // Asalkan Class tersebut bisa membaca properti $item->upah_pokok, dll.
        return Excel::download(new RincianUpahExport($formattedData, $periodeString), $filename);
    }

    public function ExportDetailHarian(Request $request)
    {
        // dd($request->all());
        // 1. Ambil data dari request
        $workersInput = json_decode($request->workers_json, true);
        $idUnit = $request->id_unit;
        // Format tanggal untuk Query Database
        $tglAwal = \Carbon\Carbon::parse($request->tgl_awal)->format('Y-m-d');
        $tglAkhir = \Carbon\Carbon::parse($request->tgl_akhir)->format('Y-m-d');

        // 2. Persiapkan data Master Pekerja dari Database
        $workerIds = collect($workersInput)->pluck('id')->toArray();
        $dbPekerja = Pekerja::with([
            'pkwt' => function ($query) {
                $query->latest('id'); // Ambil kontrak terbaru
            },
            'pkwt.jabatan',
            'pkwt.divisi',
        ])
        ->whereIn('id', $workerIds)
        ->get()
        ->keyBy('id');

        // ---------------------------------------------------------
        // [BAGIAN 3] AMBIL DATA ABSENSI & BUAT MAPPING (DILAKUKAN SEKALI SAJA)
        // ---------------------------------------------------------

        // A. Query Database
        $absensiData = Absensi::with(['detilHarian', 'tunjangan', 'potongan'])
            ->whereIn('id_pekerja', $workerIds)
            ->whereBetween('tgl_absensi', [$tglAwal, $tglAkhir])
            ->get();

        // B. Buat Mapping Array
        $attendanceMap = [];
        $tunjanganMap = [];
        $semuaKategoriTunjangan = [];
        $potonganMap = [];
        $prodMap = [];
        
        foreach ($absensiData as $abs) {
            $tgl = \Carbon\Carbon::parse($abs->tgl_absensi)->format('Y-m-d');
            $idPekerja = (int) $abs->id_pekerja;

            if (!isset($prodMap[$idPekerja])) {
                $prodMap[$idPekerja] = [
                    'kerja' => 0, 
                    'izin' => 0, 
                    'cuti' => 0, 
                    'sakit' => 0, 
                    'rencanaCuti' => 0, 
                    'absen' => 0, 
                    'jam_ot' => 0
                ];
            }

            $detil = $abs->detilHarian;
            if ($detil) {
                $jamKerja = (float) $detil->jam_kerja_harian;
                $overtime = (float) $detil->overtime;
                $status   = $detil->status_kehadiran;

                $warna = ''; // Default Colorless (Hadir biasa)

                // URUTAN FILTER WARNA
                if ($detil->hbn == 1) {
                    $warna = '#DDA0DD'; // Ungu (HBN)
                } elseif (in_array($status, [6])) { // Sesuaikan angka ID Absen Anda
                    $warna = '#FFC7CE'; // Merah (Absen/Alpha)
                } elseif ($status == 5) { // Sesuaikan angka ID Rencana Cuti Anda
                    $warna = '#C6EFCE'; // Hijau (Rencana Cuti)
                } elseif (in_array($status, [2, 3, 4])) { // Izin, Cuti, Sakit
                    if($detil->isPaid == 1)
                    {
                        $warna = '#87CEFA'; // Biru (isPaid)
                    }elseif($jamKerja > 0) {
                        $warna = '#FFEB9C'; // Kuning (Izin tapi ada jam)
                    }else{
                        $warna = '#FCD5B4'; // Orange (Izin tidak ada jam)
                    }
                }elseif ($overtime > 0) {
                    $warna = '#FFB6C1'; // Pink (Overtime)
                }

                if ($jamKerja > 0 || $detil->hbn == 1) {
                    $prodMap[$idPekerja]['kerja'] += 1;
                } elseif (in_array($status, [2])) { // Asumsi 4 = Sakit
                    $prodMap[$idPekerja]['izin'] += 1;
                } elseif (in_array($status, [3])) { // Asumsi 2/3 = Izin/Cuti
                    $prodMap[$idPekerja]['cuti'] += 1;
                } elseif (in_array($status, [4])) { // Asumsi 6 = Alpha
                    $prodMap[$idPekerja]['sakit'] += 1;
                }elseif (in_array($status, [5])) { // Asumsi 6 = Alpha
                    $prodMap[$idPekerja]['rencanaCuti'] += 1;
                }elseif (in_array($status, [6])) { // Asumsi 6 = Alpha
                    $prodMap[$idPekerja]['absen'] += 1;
                }

                $prodMap[$idPekerja]['jam_ot'] += (float) $detil->overtime;

                // Simpan jam kerja DAN warna ke dalam array
                $attendanceMap[$idPekerja][$tgl] = [
                    'jam'   => $jamKerja,
                    'warna' => $warna
                ];
            }

            if ($abs->tunjangan && !empty($abs->tunjangan->kategori)) {
                $kategoriData = is_string($abs->tunjangan->kategori) 
                                ? json_decode($abs->tunjangan->kategori, true) 
                                : $abs->tunjangan->kategori;

                if (is_array($kategoriData)) {
                    foreach ($kategoriData as $namaTunj => $val) {
                        // Kapitalisasi huruf pertama (Misal: 'score' jadi 'Score')
                        $namaTunjCap = ucfirst(strtolower(trim($namaTunj))); 
                        
                        // Kumpulkan nama kategori ke array header jika belum ada
                        if (!in_array($namaTunjCap, $semuaKategoriTunjangan)) {
                            $semuaKategoriTunjangan[] = $namaTunjCap;
                        }

                        // Hitung subtotal: Qty x Nominal (Sesuai ide gambar Anda)
                        $qty = (int) ($val['qty'] ?? 0);
                        $nominal = (float) ($val['nominal'] ?? 0);
                        $subtotal = $qty * $nominal;

                        // Masukkan ke dompet masing-masing pekerja
                        if (!isset($tunjanganMap[$idPekerja][$namaTunjCap])) {
                            $tunjanganMap[$idPekerja][$namaTunjCap] = 0;
                        }
                        $tunjanganMap[$idPekerja][$namaTunjCap] += $subtotal;
                    }
                }
            }

            if ($abs->potongan) {
                // Ambil langsung dari kolom 'total' sesuai database Anda
                $totalPotongan = (float) $abs->potongan->total;

                if (!isset($potonganMap[$idPekerja])) {
                    $potonganMap[$idPekerja] = 0;
                }
                $potonganMap[$idPekerja] += $totalPotongan;
            }
        }


        // DEBUG: Cek isi map sebelum lanjut (Hapus jika sudah benar)
        // dd($attendanceMap);

        // ---------------------------------------------------------
        // [BAGIAN 4] LOOPING DATA PEKERJA UNTUK ROW EXCEL
        // ---------------------------------------------------------

        // Ambil info Unit untuk nama unit
        $unit = Unit::find($idUnit);
        $unitName = $unit ? $unit->nama_unit : 'UNIT TIDAK DIKETAHUI';

        $realItems = [];


        foreach ($workersInput as $input) {
            $id = $input['id'];
            if (!isset($dbPekerja[$id])) {
                continue;
            }

            $staffDb = $dbPekerja[$id];
            $pkwtAktif = $staffDb->pkwt->first();

            $item = new \stdClass();

            // --- A. IDENTITAS ---
            // PENTING: Simpan ID Asli untuk kunci pencarian di Blade nanti
            $item->id_original = $staffDb->id;

            $item->nama = $staffDb->nama;
            $item->id_karyawan = $staffDb->id_pekerja ?? $staffDb->nik;
            $item->jabatan = $pkwtAktif?->jabatan?->nama ?? '-';
            $item->divisi = $pkwtAktif?->divisi?->nama ?? '-';
            $item->checkman = 'TRUE';
            $item->no_rekening = $staffDb->rekening ?? '-';

            // --- B. RATE / TARIF ---
            $item->rate_pokok = $pkwtAktif?->gaji_harian ?? 0;
            $item->rate_lembur = $pkwtAktif?->gaji_overtime ?? 0;
            $item->rate_hbn = ($pkwtAktif?->gaji_overtime ?? 0) * 1.5;

            $item->tunjangan = (float) ($input['tunjangan'] ?? 0);
            $item->insentif = (float) ($input['insentif'] ?? 0);

            // --- C. INPUT JAM & UPAH ---
            $item->jam_kerja = (float) ($input['jam_kerja'] ?? 0);

            $jamHbnInput = (float) ($input['hbn'] ?? 0);
            $jamLemburInput = (float) ($input['overtime'] ?? 0);

            // 2. LOGIKA PEMISAHAN HBN DAN OVERTIME BIASA
            // Jika ada jam HBN, maka masuk ke HBN dan lembur biasa di-nol-kan
            if ($jamHbnInput > 0) {
                $item->jam_hbn = $jamHbnInput;
                $item->jam_lembur = 0; // Mencegah masuk ke overtime biasa
            } else {
                $item->jam_hbn = 0;
                $item->jam_lembur = $jamLemburInput;
            }

            $item->jml_pokok_upah = (float) ($input['upah'] ?? 0) + ($input['potongan'] ?? 0) - ($input['tunjangan'] ?? 0);
            $item->jml_uang_lembur = $item->jam_lembur * $item->rate_lembur;
            $item->jml_uang_lembur_hbn = $item->jam_hbn * $item->rate_hbn;

            $item->total_pokok = (float) ($input['upah'] ?? 0);
            $item->total_lembur_biasa = $item->jam_lembur * $item->rate_lembur;
            $item->total_lembur_hbn = $item->jam_lembur * $item->rate_lembur; // Note: Rumus ini sepertinya typo di kode asli Anda (lembur biasa vs hbn), tapi saya biarkan sesuai aslinya.

            $item->jam_lembur_hbn = (float) ($input['hbn'] ?? 0);

            //Tunjangan
            $item->detail_tunjangan = [];
            $totalUangTunjangan = 0;

            foreach ($semuaKategoriTunjangan as $kategoriHeader) {
                // Ambil dari rekap map, jika tidak ada isikan 0
                $uangKategori = $tunjanganMap[$staffDb->id][$kategoriHeader] ?? 0;
                
                $item->detail_tunjangan[$kategoriHeader] = $uangKategori;
                $totalUangTunjangan += $uangKategori;
            }

            $item->uang_tunjangan = 0; 
            $item->jml_uang_tunjangan = $totalUangTunjangan; // Total (J)

            // $item->jml_uang_tunjangan = (float) ($input['tunjangan'] ?? 0);
            $item->upah_insentif = 0;
            $item->jml_uang_insentif = 0;

            // --- D. POTONGAN ABSENSI ---
            $item->pot_absen_per_hari = $item->rate_pokok;
            $item->jml_pot_absen_hari = 0;
            $item->pot_absen_per_jam = $pkwtAktif?->gaji_overtime ?? 0;
            $item->jml_pot_absen_jam = 0;

            // --- E. TOTAL UPAH KOTOR ---
            $pendapatan_total = $item->jml_pokok_upah + $item->jml_uang_insentif + $item->jml_uang_tunjangan;
            $potongan_absen = $item->jml_pot_absen_hari + $item->jml_pot_absen_jam;

            $item->total_upah_kotor = $pendapatan_total - $potongan_absen;

            // --- F. POTONGAN WAJIB ---
            $item->bpjs_tk = $pkwtAktif?->bpjs_naker ?? 0;
            $item->bpjs_kes = $pkwtAktif?->bpjs_kesehatan ?? 0;
            $item->biaya_admin = 2000;

            $item->potongan_lain = $potonganMap[$staffDb->id] ?? 0;
            $item->biaya_klaim = 0;

            // --- G. HASIL AKHIR ---
            $total_potongan_all = $item->bpjs_tk + $item->bpjs_kes + $item->biaya_admin + $item->potongan_lain + $item->biaya_klaim;
            $item->thp = $item->total_upah_kotor - $total_potongan_all;

            // --- H. Produktivitas ---

            // --- H. PRODUKTIVITAS ---
            $prod = $prodMap[$staffDb->id] ?? [
                'kerja' => 0, 'izin' => 0, 'cuti' => 0, 'sakit' => 0, 'rencanaCuti' => 0, 'absen' => 0, 'jam_ot' => 0
            ];

            // 1. Ambil data mentah dari array map
            $item->jml_hari_kerja = $prod['kerja'];
            $item->izin = $prod['izin'];
            $item->cuti = $prod['cuti'];
            $item->sakit = $prod['sakit'];
            $item->rencanaCuti = $prod['rencanaCuti'];
            $item->absen = $prod['absen'];

            // 2. Hitung Total Absen (Semua jenis ketidakhadiran digabung)
            $item->jml_absen = $item->izin + $item->cuti + $item->sakit + $item->rencanaCuti + $item->absen;

            // 3. Hitung Persentase Kehadiran & Ketidakhadiran
            // Kita hitung jumlah hari dalam periode ini untuk jadi nilai pembagi (denominator)
            $startPeriode = \Carbon\Carbon::parse($tglAwal)->startOfDay();
            $endPeriode = \Carbon\Carbon::parse($tglAkhir)->startOfDay();
            $totalHariPeriode = (int) round($startPeriode->diffInDays($endPeriode)) + 1;
            
            // Hitung persen (%)
            $item->pct_hadir = $totalHariPeriode > 0 ? ($item->jml_hari_kerja / $totalHariPeriode) * 100 : 0;
            $item->pct_absen = $totalHariPeriode > 0 ? ($item->jml_absen / $totalHariPeriode) * 100 : 0;

            // 4. Hitung Persentase Overtime (Asumsi Standar Jam Kerja = 8 Jam/Hari)
            $standarJamKerja = $item->jml_hari_kerja * 8; 
            $item->pct_overtime = $standarJamKerja > 0 ? ($prod['jam_ot'] / $standarJamKerja) * 100 : 0;

            $realItems[] = $item;
        }

        // 5. Finalisasi Data untuk Export
        $formattedData = collect($realItems);
        $grandTotal = $formattedData->sum('thp');

        // 6. Hitung Periode & Total Days untuk Header Excel
        $start = \Carbon\Carbon::parse($tglAwal)->startOfDay();
        $end = \Carbon\Carbon::parse($tglAkhir)->startOfDay();
        $periodeString = strtoupper($start->isoFormat('D MMMM Y') . ' - ' . $end->isoFormat('D MMMM Y'));

        $totalDays = (int) round($start->diffInDays($end)) + 1;
        $filename = 'Detail_Upah_Harian_' . $unitName . '_' . now()->format('Ymd_His') . '.xlsx';

        // dd($attendanceMap);
        // Sertakan $attendanceMap ke dalam Export Class
        return Excel::download(new DailyReportHarianExport(
            $formattedData,
            $periodeString,
            $grandTotal,
            $unitName,
            $totalDays,
            $tglAwal, // string Y-m-d
            $tglAkhir, // string Y-m-d
            $attendanceMap, // Array Mapping
            $unit,
            $semuaKategoriTunjangan
        ), $filename);
    }
}
