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
            ->with(['picUnit.staff', 'namaMitra'])
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

        $isHarian = ($unit->sistem_pengajian == 1);

        $periode = Carbon::parse($tanggalMulai)->translatedFormat('d')
        . '—'
        . Carbon::parse($tanggalAkhir)->translatedFormat('d M Y');

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
            'items' => $workers->map(function($w) use ($id_unit, $isHarian, $periode, $specificExclusions, $allAdjustments, $tanggalMulai, $tanggalAkhir) {

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

                $pkwt = PKWT::where('id_pekerja', $w->id)
                        ->where('id_unit', $id_unit)
                        ->where('status_aktif', 1)
                        ->first();
                // dump($pkwt);

                $gajiHarianPkwt   = $pkwt->gaji_harian ?? 0;
                $gajiOvertimePkwt = $pkwt->gaji_overtime ?? 0;

                foreach ($absensiRecords as $absensi) {
                    if($isHarian) {
                        $detil = $absensi->detilHarian;
                        if ($detil) {
                            $totalJamKerja += (float) $detil->jam_kerja_harian;
                            $totalOvertime += (float) $detil->overtime;
                            $totalHBN      += (float) $detil->hbn;

                            // --- LOGIKA PERHITUNGAN GAJI ---
                            $jamNormal = (float) ($detil->jam_kerja_normal); // Hindari division by zero
                            $jamHarian = (float) $detil->jam_kerja_harian;
                            $jamOT     = (float) $detil->overtime;

                            if ($detil->hbn == 1) {
                                // JIKA HBN: Semua jam_kerja_harian dihitung sebagai OVERTIME
                                // Rumus: jam_harian * gaji_overtime
                                $gajiHariIni = ($jamHarian * $gajiOvertimePkwt);
                                // dump('Gaji Harian (jamharian*gajiovertime) = ', $gajiHariIni);
                            } else {
                                // JIKA HARI NORMAL:
                                // Rumus Reguler: jam_harian * gaji_harian
                                $gajiReguler = $jamHarian * $gajiHarianPkwt;
                                // dump('gajiReguler (jamHarian*gajiHarianPkwt) = ',$gajiReguler);

                                // Rumus Overtime: jam_ot * gaji_overtime
                                $gajiOT = $jamOT * $gajiOvertimePkwt;
                                // dump('gajiOT (jamOT*gajiOvertimePkwt) = ',$gajiOT);
                                $gajiHariIni = $gajiReguler + $gajiOT;
                                // dump('gajiHariIni (gajiReguler*gajiOT) = ',$gajiHariIni);
                            }

                            $hasilGajiHarian += $gajiHariIni;
                        }
                    } else {
                        foreach ($absensi->detilBorongan as $detil)
                        {
                            // Sesuai logika JS: totalQTY = FD + act_rej + good_mc
                            $qtyPerBaris = ($detil->FD ?? 0) + ($detil->act_rej ?? 0) + ($detil->good_mc ?? 0);
                            $totalQty += $qtyPerBaris;
                            $tempQty += $qtyPerBaris;

                            // Sesuai logika JS: bayaranItem = totalQTY * harga_pekerja
                            // Kita asumsikan kolom 'bayaranItem' sudah tersimpan di DB saat presensi
                            $totalGajiBorongan += ($detil->bayaranItem ?? 0);

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
                    'total_overtime'  => $totalOvertime,
                    'total_hbn'       => $totalHBN,
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

    function ExportDetailHarian(Request $request){
        dd($request->all());
    }

    function ExportDetailBorongan(Request $request)
    {
        dd($request->all());

        $exclusionDates = $request->input('exclusion_date', []);
        $tanggal_awal = Carbon::parse($request->tgl_awal);
        $tanggal_akhir = Carbon::parse($request->tgl_akhir);

        $absensiList = Absensi::with([
            'detilBorongan.borongan:id,harga_pekerja,nama_item'
        ])
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

        return Excel::download(new InvoiceBoronganExport($request->no_resi, $Unit->nama_unit, $MitraKerja->alamat, $Bidang->nama, $MitraKerja->nama_mitra, $display_a, $terbilang, $periode, $display_management_fee, $display_ppn, $display_pph, $display_total_tagihan, $Unit->umk), $filename);
    }

    function ExportKwitansiBorongan(Request $request)
    {
        // dd($request->all());

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

        return Excel::download(new KwitansiBoronganExport($request->no_resi, $Unit->nama_unit, $terbilang, $Bidang->nama, $MitraKerja->nama_mitra, $periode, $display_total_tagihan), $filename);
    }

    function ExportRincianUpahBorongan(Request $request)
    {
        dd($request->all());
        // 1. Decode JSON Workers dari Request
        // Format JSON: [{"id":2,"upah":150000,"exclusion_date":[]}, ...]
        $requestWorkers = $request->workers;

        // dd($requestWorkers);

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

        dd($dbPekerja);

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
            $item->jumlah_1 = $item->upah_pokok; // Total Pendapatan

            // --- C. POTONGAN (Opsional) ---
            // Jika ingin menghitung BPJS otomatis berdasarkan $upahBorongan, masukkan rumus di sini
            // Untuk sementara kita nol-kan agar sesuai dengan Grand Total di summary
            $item->absen_hari = 0;
            $item->potongan_hari = 0;
            $item->absen_jam = 0;
            $item->potongan_jam = 0;

            $item->bpjs_tk = $pkwtAktif?->bpjs_naker ?? 0; // Masukkan logika calc BPJS jika ada
            $item->bpjs_kes = $pkwtAktif?->bpjs_kesehatan ?? 0; // Masukkan logika calc BPJS jika ada
            $item->jumlah_2 = $item->bpjs_tk + $item->bpjs_kes; // Total Potongan

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

    // {
    //     dd($request->all());
    //     // 1. Decode JSON Workers dari Request
    //     // Format JSON: [{"id":2,"upah":150000,"exclusion_date":[]}, ...]
    //     $requestWorkers = json_decode($request->workers_json, true);

    //     // Ambil semua ID untuk query database sekaligus (Eager Loading)
    //     $workerIds = array_column($requestWorkers, 'id');

    //     // 2. Ambil Data Identitas dari Database
    //     // Pastikan meload relasi jabatan/divisi/kelamin jika diperlukan
    //     $dbPekerja = Pekerja::with([
    //         // 1. Load PKWT, tapi urutkan dari yang terbaru agar kita ambil kontrak aktif
    //         'pkwt' => function($query) {
    //             $query->latest('id'); // Atau latest('tgl_mulai_pkwt')
    //         },
    //         // 2. Load Jabatan & Divisi MELALUI PKWT
    //         'pkwt.jabatan',
    //         'pkwt.divisi',
    //     ])
    //     ->whereIn('id', $workerIds)
    //     ->get()
    //     ->keyBy('id');

    //     $realItems = [];

    //     dd($dbPekerja);

    //     // 3. Looping Data Request (Agar urutan & nilai upahnya sesuai input)
    //     foreach ($requestWorkers as $reqWorker) {
    //         $id = $reqWorker['id'];

    //         if (!isset($dbPekerja[$id])) continue;

    //         $staffDb = $dbPekerja[$id];
    //         $item = new \stdClass();

    //         // A. IDENTITAS
    //         $item->nama = $staffDb->nama;
    //         $item->id_karyawan = $staffDb->id_pekerja ?? $staffDb->nik;

    //         // --- LOGIKA BARU MENGAMBIL JABATAN & DIVISI ---

    //         // 1. Ambil PKWT pertama (karena tadi sudah di-sort latest, maka first() adalah yang terbaru)
    //         // Gunakan null safe operator (?->) jaga-jaga jika pekerja belum punya PKWT
    //         $pkwtAktif = $staffDb->pkwt->first();

    //         // 2. Ambil Jabatan & Divisi dari PKWT tersebut
    //         $item->jabatan = $pkwtAktif?->jabatan?->nama ?? '-';
    //         $item->divisi  = $pkwtAktif?->divisi?->nama  ?? '-';

    //         // --- B. DATA GAJI (Dari Request JSON) ---
    //         // Karena ini Borongan, kita asumsikan 'upah' dari JSON adalah Total yang diterima
    //         // Anda bisa menyesuaikan logika ini jika 'upah' tersebut harus dipecah lagi
    //         $upahBorongan = $reqWorker['upah'];

    //         // Set Upah Pokok / Hasil Kerja
    //         $item->upah_pokok = $upahBorongan;

    //         // Set Komponen Lain ke 0 (Kecuali jika Anda punya logika hitung BPJS otomatis disini)
    //         $item->lembur_jam = 0;
    //         $item->lembur_rate = 0;
    //         $item->lembur_hbn_rate = 0;
    //         $item->total_lembur = 0;
    //         $item->jumlah_1 = $item->upah_pokok; // Total Pendapatan

    //         // --- C. POTONGAN (Opsional) ---
    //         // Jika ingin menghitung BPJS otomatis berdasarkan $upahBorongan, masukkan rumus di sini
    //         // Untuk sementara kita nol-kan agar sesuai dengan Grand Total di summary
    //         $item->absen_hari = 0;
    //         $item->potongan_hari = 0;
    //         $item->absen_jam = 0;
    //         $item->potongan_jam = 0;

    //         $item->bpjs_tk = $pkwtAktif?->bpjs_naker ?? 0;  // Masukkan logika calc BPJS jika ada
    //         $item->bpjs_kes = $pkwtAktif?->bpjs_kesehatan ?? 0; // Masukkan logika calc BPJS jika ada
    //         $item->jumlah_2 = $item->bpjs_tk + $item->bpjs_kes; // Total Potongan

    //         // --- D. TOTAL AKHIR ---
    //         $item->take_home_pay = $item->jumlah_1 - $item->jumlah_2;

    //         // Simpan info exclusion date jika nanti perlu ditampilkan di excel (opsional)
    //         $item->exclusion_dates = $reqWorker['exclusion_date'];

    //         $realItems[] = $item;
    //     }

    //     // dd($realItems);

    //     // 4. Bungkus jadi Collection
    //     $formattedData = collect($realItems);

    //     // 5. Tentukan Periode (Dari Request Tgl Awal & Akhir)
    //     $start = Carbon::parse($request->tgl_awal);
    //     $end   = Carbon::parse($request->tgl_akhir);

    //     // Format: 1 FEB 2026 - 5 FEB 2026
    //     $periodeString = strtoupper($start->isoFormat('D MMM Y') . ' - ' . $end->isoFormat('D MMM Y'));

    //     // Nama File
    //     $filename = "Rincian_Upah_Borongan_" . $start->format('d_m_Y') . ".xlsx";

    //     return Excel::download(
    //         new RincianUpahExport($formattedData, $periodeString),
    //         $filename
    //     );
    // }

    public function ExportRincianUpahHarian(Request $request)
    {
        // ==========================================
        // 1. SETUP DUMMY DATA (HANYA UNTUK TESTING)
        // ==========================================
        // Hapus/Comment blok ini jika sudah siap menerima data dari Frontend

        // Simulasi Request dari Frontend
        $request->merge([
            'id_unit' => 2,
            'tgl_awal' => '2026-02-01',
            'tgl_akhir' => '2026-02-06',
            'workers_json' => json_encode([
                [
                    'id' => 1, // ID Pekerja di DB
                    'total_jam_kerja' => 176, // Total jam/hari kerja
                    'total_jam_overtime' => 10, // Jam Lembur Biasa
                    'total_kerja_hbn' => 5, // Jam Lembur Libur (HBN)
                    'tunjangan' => 50000, // Tunjangan Makan/Transport (jika ada)
                    'potongan' => 25000, // Kasbon/Lainnya
                    'exclusion_date' => ['2026-02-03'],
                ],
                [
                    'id' => 2,
                    'total_jam_kerja' => 160,
                    'total_jam_overtime' => 0,
                    'total_kerja_hbn' => 0,
                    'tunjangan' => 0,
                    'potongan' => 0,
                    'exclusion_date' => [],
                ],
            ]),
        ]);
        // ==========================================
        // END DUMMY DATA
        // ==========================================

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

            // Asumsi: HBN rate biasanya 2x lembur atau ada kolom sendiri.
            // Jika tidak ada kolom khusus, kita pakai logika 2 * rate lembur.
            // Silakan sesuaikan: $pkwtAktif?->gaji_overtime_hbn
            $rateHbn = ($pkwtAktif?->gaji_overtime ?? 0) * 1.5;

            // --- C. KALKULASI PENDAPATAN ---

            // 1. Upah Pokok
            $item->upah_pokok = ($input['total_jam_kerja'] ?? 0) * $ratePokok;

            // 2. Lembur Biasa
            $item->lembur_jam = $input['total_jam_overtime'] ?? 0;
            $item->lembur_rate = $rateLembur;
            $item->total_lembur_biasa = $item->lembur_jam * $item->lembur_rate;

            // 3. Lembur HBN
            $item->lembur_hbn_jam = $input['total_kerja_hbn'] ?? 0;
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

    public function ExportDailyReportHarian(Request $request)
    {
        $dummyItems = [];

        for ($i = 1; $i <= 5; $i++) {
            $item = new \stdClass();

            // ---------------------------------------------------------
            // [BAGIAN 1] VARIABEL ASLI ANDA (TIDAK DIUBAH)
            // ---------------------------------------------------------

            // A. Identitas Dummy
            $item->nama = 'Budi Santoso ' . $i;
            $item->id_karyawan = 'KARY-' . str_pad($i, 3, '0', STR_PAD_LEFT);
            $item->jabatan = 'Operator Produksi';
            $item->divisi = 'Gudang Logistik';

            // B. Rate Dummy (Tarif)
            $item->rate_pokok = 25000;
            $item->rate_lembur = 35000;
            $item->rate_hbn = 50000;

            // C. Input Jam Dummy (Variasi)
            $item->jam_kerja = 170 + $i * 2;
            $item->jam_lembur = rand(0, 10);
            $item->jam_hbn = rand(0, 5);

            // D. Kalkulasi Pendapatan
            $item->total_pokok = $item->jam_kerja * $item->rate_pokok;
            $item->total_lembur_biasa = $item->jam_lembur * $item->rate_lembur;
            $item->total_lembur_hbn = $item->jam_hbn * $item->rate_hbn;
            $item->tunjangan = $i % 2 == 0 ? 100000 : 0;

            // Bruto Lama
            $item->gross_salary = $item->total_pokok + $item->total_lembur_biasa + $item->total_lembur_hbn + $item->tunjangan;

            // E. Kalkulasi Potongan Dummy Awal
            $item->bpjs_tk = 97338;
            $item->bpjs_kes = 48669;
            $item->biaya_admin = 2000;
            $item->potongan_lain = $i == 3 ? 50000 : 0;

            // ---------------------------------------------------------
            // [BAGIAN 2] TAMBAHAN VARIABEL BARU (UNTUK KOLOM B - N)
            // ---------------------------------------------------------
            // Kita map (hubungkan) variabel lama ke variabel baru yang dibutuhkan Excel

            // CHECKMAN
            $item->checkman = 'TRUE';

            // (B) JML. POKOK UPAH -> Ambil dari total_pokok
            $item->jml_pokok_upah = $item->total_pokok;

            // (C) JAM LEMBUR -> Sudah ada ($item->jam_lembur)

            // (D) JML. UANG LEMBUR -> Ambil dari total_lembur_biasa
            $item->jml_uang_lembur = $item->total_lembur_biasa;

            // (E) LEMBUR HBN/JAM -> Ambil dari jam_hbn
            $item->jam_lembur_hbn = $item->jam_hbn;

            // (F) JML UANG LEMBUR HBN -> Ambil dari total_lembur_hbn
            $item->jml_uang_lembur_hbn = $item->total_lembur_hbn;

            // (G & H) INSENTIF (Data Baru)
            $item->upah_insentif = 0;
            $item->jml_uang_insentif = 0;

            // (I) UANG TUNJ. (Rate Tunjangan - anggap flat atau 0)
            $item->uang_tunjangan = 0;

            // (J) JML. UANG TUNJ. -> Ambil dari tunjangan lama
            $item->jml_uang_tunjangan = $item->tunjangan;

            // (K & L) POTONGAN ABSEN HARI (Data Baru)
            $item->pot_absen_per_hari = 150000; // Contoh rate potong gaji
            $item->jml_pot_absen_hari = 0; // Contoh 0 hari alpha

            // (M & N) POTONGAN ABSEN JAM (Data Baru)
            $item->pot_absen_per_jam = 25000; // Contoh rate potong telat
            $item->jml_pot_absen_jam = 0; // Contoh 0 jam telat

            // TOTAL UPAH (HEADER K) - Rumus Baru
            // (B + D + F + H + J) - (L + N)
            // Ini pada dasarnya sama dengan gross_salary dikurangi potongan absen baru
            $pendapatan_total = $item->jml_pokok_upah + $item->jml_uang_lembur + $item->jml_uang_lembur_hbn + $item->jml_uang_insentif + $item->jml_uang_tunjangan;
            $potongan_absen = $item->jml_pot_absen_hari + $item->jml_pot_absen_jam;

            $item->total_upah_kotor = $pendapatan_total - $potongan_absen;

            // Tambahan Biaya Klaim (Data Baru)
            $item->biaya_klaim = 0;

            // RE-CALCULATE THP (Update variable thp lama dengan komponen baru)
            // THP = Total Upah Kotor - (BPJS + Admin + Potongan Lain + Klaim)
            $total_potongan_all = $item->bpjs_tk + $item->bpjs_kes + $item->biaya_admin + $item->potongan_lain + $item->biaya_klaim;

            $item->thp = $item->total_upah_kotor - $total_potongan_all;

            // NO REKENING (Data Baru)
            $item->no_rekening = '12345678' . $i;

            $dummyItems[] = $item;
        }

        // =======================================================================
        // 2. FINALISASI DATA
        // =======================================================================

        // Bungkus array dummy menjadi Collection
        $formattedData = collect($dummyItems);

        // Hitung Grand Total dari data dummy
        $grandTotal = $formattedData->sum('thp');

        // Set Periode & Unit Dummy
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $periodeString = strtoupper($start->isoFormat('D MMMM Y') . ' - ' . $end->isoFormat('D MMMM Y'));
        $unitName = 'UNIT DUMMY TESTING';

        $filename = 'Test_Rincian_Harian_' . now()->format('His') . '.xlsx';

        $diff = $start->diffInDays($end);

        // 3. Tambah 1 (Inklusif) dan Paksa jadi Integer
        $totalDays = (int) round($diff) + 1;

        // 3. DOWNLOAD
        return Excel::download(new DailyReportHarianExport($formattedData, $periodeString, $grandTotal, $unitName, $totalDays), $filename);
    }
}
