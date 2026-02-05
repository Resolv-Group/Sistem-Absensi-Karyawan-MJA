<?php

namespace App\Http\Controllers;

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

        $periode = Carbon::parse($tanggalMulai)->translatedFormat('d')
        . '—'
        . Carbon::parse($tanggalAkhir)->translatedFormat('d M Y');

        // Ambil data pekerja yang dipilih
        $workers = Pekerja::whereIn('id', $paidWorkerIds)
                    ->get();

        // Olah data potongan tanggal (Step 3) dari modal
        $specificExclusions = collect($request->input('specific_workers', []))
            ->filter(fn($item) => !empty($item['id']) && !empty($item['date']))
            ->groupBy('id');

            // dump($specificExclusions);

        $payrollData = [
            'unit_id' => $request->id_unit,
            'unit_name' => $request->unit_name ?? 'Unit Borongan',
            'sistem_pengajian' => $unit->sistem_pengajian,
            'periode' => Carbon::parse($tanggalMulai)->translatedFormat('d') . ' — ' . Carbon::parse($tanggalAkhir)->translatedFormat('d M Y'),
            'total_pekerja' => $workers->count(),
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_akhir' => $tanggalAkhir,
            'items' => $workers->map(function($w) use ($id_unit, $periode, $specificExclusions, $allAdjustments, $tanggalMulai, $tanggalAkhir) {

                // 1. Dapatkan list tanggal yang dikecualikan (potongan) untuk pekerja ini
                $excludedDates = $specificExclusions->get($w->id)
                    ? $specificExclusions->get($w->id)->pluck('date')->toArray()
                    : [];

                // 2. Query ke tabel Absensi yang memiliki Detil Borongan
                // Kita hitung record dalam range tanggal Mula-Selesai, dan TIDAK TERMASUK tanggal potongan
                $absensiRecords = Absensi::with([
                        'detilBorongan.borongan:id,harga_pekerja,nama_item'
                    ])
                    ->whereBetween('tgl_absensi', [$tanggalMulai, $tanggalAkhir])
                    ->where('id_unit', $id_unit)
                    ->where('id_pekerja', $w->id)
                    ->whereNotIn('tgl_absensi', $excludedDates)
                    ->get();

                // dd($absensiRecords, $w->id);
                $workerAdj = $allAdjustments[$w->id] ?? ['pembayaran_lain' => 0, 'tunjangan_bayaran' => 0];

                $workerPembayaranLain = (int) ($workerAdj['pembayaran_lain'] ?? 0);
                $workerTunjangan = (int) ($workerAdj['tunjangan_bayaran'] ?? 0);

                $totalQty = 0;
                $tempQty = 0;
                $totalGajiBorongan = 0;

                foreach ($absensiRecords as $absensi) {
                    foreach ($absensi->detilBorongan as $detil) {
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

                // Gaji Bersih = Total Hasil Borongan + Penyesuaian Global
                $netSalary = $totalGajiBorongan - $workerPembayaranLain  + $workerTunjangan;

                // dd($netSalary);

                return [
                    'unit_id'   => $id_unit,
                    'unit_name' => $unit?->nama_unit ?? '-',
                    'sistem_pengajian' => $unit?->sistem_pengajian ?? '1',
                    'id_pekerja' => $w->id,
                    'periode' => $periode,
                    'nama' => $w->nama,
                    'nik' => $w->nik,
                    'total_barang' => $totalQty,
                    'hasil_gaji_borongan' => $totalGajiBorongan,
                    'potongan_count' => count($excludedDates),
                    'potongan_dates' => $excludedDates,
                    'net_salary' => $netSalary,
                    'pembayaran_lain' => $workerPembayaranLain,
                    'tunjangan' => $workerTunjangan,
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_akhir' => $tanggalAkhir
                ];
            }),
        ];

        $payrollData['grand_total'] = collect($payrollData['items'])->sum('net_salary');

        $payrollData['total_potongan_hari'] = collect($payrollData['items'])
            ->sum('potongan_count');

        $payrollData['total_penyesuaian'] = collect($payrollData['items'])
            ->sum(fn ($item) => (int)$item['tunjangan'] - (int)$item['pembayaran_lain']);

        return view('Payroll.overview-payroll', compact('payrollData', 'paidWorkerIds'));
    }

    function ExportDetailBorongan(Request $request)
    {
        dd($request->all());

        $exclusionDates = $request->input('exclusion_date', []);
        $tanggal_awal  = Carbon::parse($request->tgl_awal);
        $tanggal_akhir = Carbon::parse($request->tgl_akhir);

        $absensiList = Absensi::with([
            'detilBorongan.borongan:id,harga_pekerja,nama_item'
        ])
        ->whereBetween('tgl_absensi', [$tanggal_awal, $tanggal_akhir])
        ->where('id_pekerja', $request->id_pekerja)
        ->whereNotIn('tgl_absensi', $exclusionDates) 
        ->get();

        $periode = strtoupper(
    $tanggal_awal->translatedFormat('d F Y') .
            ' ~ ' .
            $tanggal_akhir->translatedFormat('d F Y')
        );

        $PKWT = PKWT::where('id_pekerja', $request->id_pekerja)
            ->where('id_unit', $request->id_unit)
            ->first();

        $pekerja = Pekerja::where('id', $request->id_pekerja)->first();

        $divisi = Divisi::where('id', $PKWT->divisi_id)->first();

        $unit = Unit::where('id', $request->id_unit)->first();

        $data = collect();

        $take_home_pay = 0;

        foreach ($absensiList as $absensi) {
            foreach ($absensi->detilBorongan as $detil) {

                // SKIP kalau detil borongan kosong
                if (
                    $detil->fd == 0 &&
                    $detil->act_rej == 0 &&
                    $detil->good_mc == 0
                ) {
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
                $data->push((object) [
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
                    $take_home_pay += $totalBayarRp
                ]);
            }
        }

        // dd($data);

        $take_home_pay = $take_home_pay - $PKWT->bpjs_naker - $PKWT->bpjs_kesehatan - $request->potongan + $request->tunjangan;

        $filename = "Summary_Upah_{$pekerja->nama}_{$unit->nama_unit}_{$periode}.xlsx";

        return Excel::download(
        new DetilBoronganExport(
            $data,
            $periode,
            $pekerja->nama,
            $divisi->nama,
            $PKWT->bpjs_naker,
            $PKWT->bpjs_kesehatan,
            $request->potongan,
            $request->tunjangan,
            $take_home_pay
        ),
        $filename
    );
    }

    function ExportTandaTerimaBorongan(Request $request) {
        // dd($request->all());

        $start = \Carbon\Carbon::parse($request->tanggal_mulai);
        $end   = \Carbon\Carbon::parse($request->tanggal_akhir);

        // Pastikan locale Indonesia
        \Carbon\Carbon::setLocale('id');

        $periode =
        $start->format('d') .
        ' ' .
        strtoupper($start->translatedFormat('M')) . // 'M' untuk singkatan 3 huruf (SEPT)
        ' - ' .
        $end->format('d') .
        ' ' .
        strtoupper($end->translatedFormat('M')) .    // 'M' untuk singkatan 3 huruf (OKT)
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
            $activePkwt = ($pkwt instanceof \Illuminate\Support\Collection) ? $pkwt->first() : $pkwt;

            $upah_mentah = (float) ($item['upah'] ?? 0);
            $clean_upah = str_replace('.', '', $upah_mentah);
            $clean_upah = str_replace(',', '.', $clean_upah);

            return [
                'no'                => $key + 1,
                'id'                => $worker->id_pekerja,
                'nama'              => $worker->nama ?? '-',
                'posisi'            => optional(optional($activePkwt)->jabatan)->nama ?? '-',
                'divisi'            => optional(optional($activePkwt)->divisi)->nama ?? '-',
                'no_rek'            => $worker->rekening ?? '-',
                // Simpan versi angka murni untuk dihitung TOTAL
                'upah_murni'        => $upah_mentah,
                // Simpan versi format untuk TAMPILAN
                'upah_tenaga_kerja' => number_format((float)$clean_upah, 0, ',', '.'),
            ];
        });

        // 1. Hitung total dari kolom 'upah_murni' yang masih berupa angka
        $total_seluruh = $dataExport->sum('upah_murni');

        // 2. Format hasil totalnya untuk tampilan di laporan
        $display_total = number_format($total_seluruh, 0, ',', '.');

        $pic = PicUnit::where('id_unit', $request->id_unit)->first();
        $namaPic = Staff::where('id', $pic->id_pic)->first();

        $administrator =  Auth::user()->staff->id;

        $admin = Staff::where('id', $administrator)->first();

        $filename = "SlipUpah_{$Unit->nama_unit}_{$periode}.xlsx";

        return Excel::download(
            new SlipUpahExport(
                $dataExport,
                $Unit->nama_unit,
                $display_total,
                $admin->nama,
                $namaPic->nama,
                $periode
            ),
            $filename
        );
    }
    function ExportInvoiceBorongan(Request $request) {

        $start = \Carbon\Carbon::parse($request->tanggal_mulai);
        $end   = \Carbon\Carbon::parse($request->tanggal_akhir);

        // Pastikan locale Indonesia
        \Carbon\Carbon::setLocale('id');

        $periode =
            $start->format('d') .
            ' - ' .
            $end->format('d') .
            ' ' .
            strtoupper($start->translatedFormat('F')) .
            ' ' .
            $start->format('Y');

        $a = $request->grand_total;

        $Unit = Unit::where('id_unit', $request->id_unit)->first();

        $MitraKerja = MitraKerja::where('id', $Unit->id_mitra_kerja)->first();

        $Bidang = BidangUsaha::where('id', $MitraKerja->bidang_usaha_id)->first();

        $management_fee = round($a * 6 / 100);
        $ppn            = round($management_fee * 11 / 100);
        $pph            = round($management_fee * 2 / 100);

        // Total tagihan dijumlahkan dari hasil yang sudah dibulatkan
        $total_tagihan  = $a + $management_fee + $ppn - $pph;

        $terbilang = ucwords(terbilang($total_tagihan)). ' Rupiah';

        // Menambahkan format titik (number_format)
        $display_a              = number_format($a, 0, ',', '.');
        $display_management_fee = number_format($management_fee, 0, ',', '.');
        $display_ppn            = number_format($ppn, 0, ',', '.');
        $display_pph            = number_format($pph, 0, ',', '.');
        $display_total_tagihan  = number_format($total_tagihan, 0, ',', '.');

        $filename = "Invoice_{$Unit->nama_unit}_{$periode}.xlsx";
        
        return Excel::download(
        new InvoiceBoronganExport(
            $request->no_resi,
            $Unit->nama_unit,
            $MitraKerja->alamat,
            $Bidang->nama,
            $MitraKerja->nama_mitra,
            $display_a,
            $terbilang,
            $periode,
            $display_management_fee,
            $display_ppn,
            $display_pph,
            $display_total_tagihan,
            $Unit->umk
        ),
        $filename
        );
    }

    function ExportKwitansiBorongan(Request $request) {
        // dd($request->all());

        $Unit = Unit::where('id_unit', $request->id_unit)->first();

        $MitraKerja = MitraKerja::where('id', $Unit->id_mitra_kerja)->first();

        $Bidang = BidangUsaha::where('id', $MitraKerja->bidang_usaha_id)->first();

        $start = \Carbon\Carbon::parse($request->tanggal_mulai);
        $end   = \Carbon\Carbon::parse($request->tanggal_akhir);

        // Pastikan locale Indonesia
        \Carbon\Carbon::setLocale('id');

        $periode =
            $start->format('d') .
            ' - ' .
            $end->format('d') .
            ' ' .
            strtoupper($start->translatedFormat('F')) .
            ' ' .
            $start->format('Y');

        $management_fee = round($request->grand_total * 6 / 100);
        $ppn            = round($management_fee * 11 / 100);
        $pph            = round($management_fee * 2 / 100);

        // Total tagihan dijumlahkan dari hasil yang sudah dibulatkan
        $total_tagihan  = $request->grand_total + $management_fee + $ppn - $pph;
        $display_total_tagihan  = number_format($total_tagihan, 0, ',', '.');

        $terbilang = ucwords(terbilang($total_tagihan)). ' Rupiah';

        $filename = "Kwitansi_{$Unit->nama_unit}_{$periode}.xlsx";

        return Excel::download(
            new KwitansiBoronganExport(
            $request->no_resi,
            $Unit->nama_unit,
            $terbilang,
            $Bidang->nama,
            $MitraKerja->nama_mitra,
            $periode,
            $display_total_tagihan,
            ),
            $filename
        );
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
            'pkwt' => function($query) {
                $query->latest('id'); // Atau latest('tgl_mulai_pkwt')
            }, 
            // 2. Load Jabatan & Divisi MELALUI PKWT
            'pkwt.jabatan', 
            'pkwt.divisi'
        ]) 
        ->whereIn('id', $workerIds)
        ->get()
        ->keyBy('id');

        $realItems = [];

        // 3. Looping Data Request (Agar urutan & nilai upahnya sesuai input)
        foreach ($requestWorkers as $reqWorker) {
            $id = $reqWorker['id'];
    
            if (!isset($dbPekerja[$id])) continue; 

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
            $item->divisi  = $pkwtAktif?->divisi?->nama  ?? '-';

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
            
            $item->bpjs_tk = 0;  // Masukkan logika calc BPJS jika ada
            $item->bpjs_kes = 0; // Masukkan logika calc BPJS jika ada
            $item->jumlah_2 = $item->bpjs_tk + $item->bpjs_kes; // Total Potongan

            // --- D. TOTAL AKHIR ---
            $item->take_home_pay = $item->jumlah_1 - $item->jumlah_2;

            // Simpan info exclusion date jika nanti perlu ditampilkan di excel (opsional)
            $item->exclusion_dates = $reqWorker['exclusion_date'];

            $realItems[] = $item;
        }

        // 4. Bungkus jadi Collection
        $formattedData = collect($realItems);

        // 5. Tentukan Periode (Dari Request Tgl Awal & Akhir)
        $start = Carbon::parse($request->tgl_awal);
        $end   = Carbon::parse($request->tgl_akhir);
        
        // Format: 1 FEB 2026 - 5 FEB 2026
        $periodeString = strtoupper($start->isoFormat('D MMM Y') . ' - ' . $end->isoFormat('D MMM Y'));
        
        // Nama File
        $filename = "Rincian_Upah_Borongan_" . $start->format('d_m_Y') . ".xlsx";

        return Excel::download(
            new RincianUpahExport($formattedData, $periodeString),
            $filename
        );
    }
}
