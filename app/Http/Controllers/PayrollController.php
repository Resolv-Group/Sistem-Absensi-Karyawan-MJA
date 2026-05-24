<?php

namespace App\Http\Controllers;

use App\Exports\SummaryUpahExport;
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
use App\Models\PayrollHistory;
use App\Models\PayrollHistory_Detail;
use App\Jobs\GeneratePayrollPdfJob;
use App\Jobs\SendPayrollEmailsJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
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

        $penanggungJawab = $request->input('penanggung_jawab'); 
        $jabatanPJ = $request->input('jabatan_pj'); 
        $biayaAdmin = $request->input('biaya_admin', 0); 

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
            'penanggung_jawab' => $penanggungJawab,
            'jabatan_pj' => $jabatanPJ,
            'biaya_admin' => $biayaAdmin,
            'items' => $workers->map(function ($w) use ($id_unit, $isHarian, $periode, $specificExclusions, $allAdjustments, $tanggalMulai, $tanggalAkhir, $biayaAdmin, $penanggungJawab, $jabatanPJ) {
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
                $totalHBN_Salary = 0;
                $totalOT_Salary = 0;
                $hasilGajiHarian = 0;

                // $pkwt = PKWT::where('id_pekerja', $w->id)->where('id_unit', $id_unit)->where('status_aktif', 1)->first();
                // dump($pkwt);

                $semuaPkwt = PKWT::where('id_pekerja', $w->id)->where('id_unit', $id_unit)->get();

                // $gajiHarianPkwt = $pkwt->gaji_harian ?? 0;
                // $gajiOvertimePkwt = $pkwt->gaji_overtime ?? 0;

                foreach ($absensiRecords as $absensi) {
                    $tglAbsenSekarang = $absensi->tgl_absensi;
                    
                    $pkwtHariIni = $semuaPkwt->first(function ($p) use ($tglAbsenSekarang) {
                        // CATATAN: Sesuaikan 'tanggal_mulai' dan 'tanggal_selesai' dengan nama kolom di DB Anda
                        $mulai = $p->tgl_mulai_pkwt; 
                        $selesai = $p->tgl_akhir_pkwt; 
                        
                        // Jika PKWT memiliki tanggal selesai (kontrak lama)
                        if ($selesai) {
                            return $tglAbsenSekarang >= $mulai && $tglAbsenSekarang <= $selesai;
                        }
                        // Jika PKWT tidak ada tanggal selesai (kontrak berjalan / yang terbaru)
                        return $tglAbsenSekarang >= $mulai;
                    });

                    // Fallback (Jaga-jaga): Jika ternyata di tanggal tersebut tidak ada PKWT yg cocok, 
                    // gunakan PKWT yg statusnya aktif saat ini
                    if (!$pkwtHariIni) {
                        $pkwtHariIni = $semuaPkwt->where('status_aktif', 1)->first();
                    }

                    // Ambil rate gaji sesuai PKWT yang berlaku pada hari tersebut
                    $gajiHarianPkwt = $pkwtHariIni->gaji_harian ?? 0;
                    $gajiOvertimePkwt = $pkwtHariIni->gaji_overtime ?? 0;
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
                                $totalHBN_Salary += $gajiHariIni;
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

                                $totalOT_Salary += $gajiOT;
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
                    'overtime_salary' => $totalOT_Salary,
                    'hbn_salary' => $totalHBN_Salary,
                    'potongan_count' => count($excludedDates),
                    'potongan_dates' => $excludedDates,
                    'net_salary' => $netSalary,
                    'biaya_admin' => $biayaAdmin,
                    'penanggung_jawab' => $penanggungJawab,
                    'jabatan_pj' => $jabatanPJ,
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
                // if ($detil->fd == 0 && $detil->act_rej == 0 && $detil->good_mc == 0) {
                //     continue;
                // }

                // ===== RUMUS =====
                $fd = $detil->FD;
                $actRej = $detil->act_rej;
                $goodMc = $detil->good_mc;

                // qty = fd + act_rej + good_mc
                $qty = $fd + $actRej + $goodMc;

                // max reject subkon = qty * 1%

                $maxRejectSubkon = $detil->max_rej_subkon;
                $rejMc = $detil->rej_mc_beban;

                // total dibayar (rumus)
                $totalDibayarRumus = $fd - $rejMc + $goodMc;

                // total dibayar (pcs) → dibulatkan
                $totalDibayarPcs = $totalDibayarRumus;

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

        $Unit = Unit::where('id', $request->id_unit)->first();

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
        // dd($request->all());
        $jabatan = $request->jabatan ?? '';
        $start = \Carbon\Carbon::parse($request->tanggal_mulai);
        $end = \Carbon\Carbon::parse($request->tanggal_akhir);

        // Pastikan locale Indonesia
        \Carbon\Carbon::setLocale('id');

        $periode = $start->format('d') . ' - ' . $end->format('d') . ' ' . strtoupper($start->translatedFormat('F')) . ' ' . $start->format('Y');

        $workersInput = $request->input('workers', []); 
        
        // Ambil semua ID pekerja untuk query PKWT
        $workerIds = array_column($workersInput, 'id');

        // 1. Ambil data PKWT aktif untuk semua pekerja di array ini sekaligus
        // (Asumsi nama model Anda adalah PKWT dan status_aktif = 1)
        $semuaPkwt = \App\Models\PKWT::whereIn('id_pekerja', $workerIds)
            ->where('status_aktif', 1) // Sesuaikan jika Anda menggunakan filter unit juga
            ->get()
            ->keyBy('id_pekerja'); // Kunci array dengan id_pekerja agar mudah dicari

        // 2. Siapkan variabel penghitung (counter)
        $countBpjsKesehatan = 0;
        $countBpjsNaker = 0;

        foreach ($workersInput as $input) {
            $id = $input['id'];

            // 3. Ambil data PKWT untuk pekerja ini dari koleksi yang sudah ditarik di atas
            $pkwtPekerja = $semuaPkwt->get($id);

            if ($pkwtPekerja) {
                // Jika nominal bpjs_kesehatan lebih dari 0, tambah 1 ke hitungan
                if ((float) $pkwtPekerja->bpjs_kesehatan > 0) {
                    $countBpjsKesehatan++;
                }

                // Jika nominal bpjs_naker lebih dari 0, tambah 1 ke hitungan
                if ((float) $pkwtPekerja->bpjs_naker > 0) {
                    $countBpjsNaker++;
                }
            }
        }

        // Cek hasil hitungannya (Hapus / Comment kode dump ini jika sudah benar)
        // dump("Total BPJS Kes: " . $countBpjsKesehatan);
        // dump("Total BPJS Naker: " . $countBpjsNaker);

        $a = $request->grand_total;

        $Unit = Unit::where('id', $request->id_unit)->first();
        // dd($Unit);

        $MitraKerja = MitraKerja::where('id', $Unit->id_mitra_kerja)->first();

        $Bidang = BidangUsaha::where('id', $MitraKerja->bidang_usaha_id)->first();

        $naker = $Unit->umk * $countBpjsNaker * 4.24/100;
        $kesehatan = $Unit->umk * $countBpjsKesehatan * 4/100;;

        $management_fee = round(($a * $Unit->persentase_management_fee) / 100);
        $ppn = round(($management_fee * 11) / 100);
        $pph = round(($management_fee * 2) / 100);

        // Total tagihan dijumlahkan dari hasil yang sudah dibulatkan
        $total_tagihan = $naker + $kesehatan + $a + $management_fee + $ppn + $pph;

        $terbilang = ucwords(terbilang($total_tagihan)) . ' Rupiah';

        // Menambahkan format titik (number_format)
        $display_a = number_format($a);
        $display_management_fee = number_format($management_fee);
        $display_ppn = number_format($ppn);
        $display_pph = number_format($pph);
        $display_total_tagihan = number_format($total_tagihan);

        $filename = "Invoice_{$Unit->nama_unit}_{$periode}.xlsx";

        return Excel::download(new InvoiceBoronganExport($request->no_resi, 
        $Unit->nama_unit, $MitraKerja->alamat, $Bidang->nama, $MitraKerja->nama_mitra, 
        $display_a, $terbilang, $periode, 
        $display_management_fee, $display_ppn, $display_pph, 
        $display_total_tagihan, $Unit->umk, $request->nama_resi,$jabatan, $MitraKerja->kota,
        $countBpjsKesehatan,$countBpjsNaker, $Unit->persentase_management_fee, $naker, $kesehatan), $filename);
    }

    function ExportKwitansiBorongan(Request $request)
    {
        // dd($request->all());
        $jabatan = $request->jabatan ?? '';

        $Unit = Unit::where('id', $request->id_unit)->first();

        $MitraKerja = MitraKerja::where('id', $Unit->id_mitra_kerja)->first();

        $Bidang = BidangUsaha::where('id', $MitraKerja->bidang_usaha_id)->first();

        $start = \Carbon\Carbon::parse($request->tanggal_mulai);
        $end = \Carbon\Carbon::parse($request->tanggal_akhir);

        // Pastikan locale Indonesia
        \Carbon\Carbon::setLocale('id');

        $periode = $start->format('d') . ' - ' . $end->format('d') . ' ' . strtoupper($start->translatedFormat('F')) . ' ' . $start->format('Y');

        $workersInput = $request->input('workers', []); 
        
        // Ambil semua ID pekerja untuk query PKWT
        $workerIds = array_column($workersInput, 'id');

        // 1. Ambil data PKWT aktif untuk semua pekerja di array ini sekaligus
        // (Asumsi nama model Anda adalah PKWT dan status_aktif = 1)
        $semuaPkwt = \App\Models\PKWT::whereIn('id_pekerja', $workerIds)
            ->where('status_aktif', 1) // Sesuaikan jika Anda menggunakan filter unit juga
            ->get()
            ->keyBy('id_pekerja'); // Kunci array dengan id_pekerja agar mudah dicari

        // 2. Siapkan variabel penghitung (counter)
        $countBpjsKesehatan = 0;
        $countBpjsNaker = 0;

        foreach ($workersInput as $input) {
            $id = $input['id'];

            // 3. Ambil data PKWT untuk pekerja ini dari koleksi yang sudah ditarik di atas
            $pkwtPekerja = $semuaPkwt->get($id);

            if ($pkwtPekerja) {
                // Jika nominal bpjs_kesehatan lebih dari 0, tambah 1 ke hitungan
                if ((float) $pkwtPekerja->bpjs_kesehatan > 0) {
                    $countBpjsKesehatan++;
                }

                // Jika nominal bpjs_naker lebih dari 0, tambah 1 ke hitungan
                if ((float) $pkwtPekerja->bpjs_naker > 0) {
                    $countBpjsNaker++;
                }
            }
        }

        // Cek hasil hitungannya (Hapus / Comment kode dump ini jika sudah benar)
        // dump("Total BPJS Kes: " . $countBpjsKesehatan);
        // dump("Total BPJS Naker: " . $countBpjsNaker);

        $a = $request->grand_total;

        $Unit = Unit::where('id', $request->id_unit)->first();
        // dd($Unit);

        $MitraKerja = MitraKerja::where('id', $Unit->id_mitra_kerja)->first();

        $Bidang = BidangUsaha::where('id', $MitraKerja->bidang_usaha_id)->first();

        $naker = $Unit->umk * $countBpjsNaker * 4.24/100;
        $kesehatan = $Unit->umk * $countBpjsKesehatan * 4/100;;

        $management_fee = round(($a * $Unit->persentase_management_fee) / 100);
        $ppn = round(($management_fee * 11) / 100);
        $pph = round(($management_fee * 2) / 100);

        // Total tagihan dijumlahkan dari hasil yang sudah dibulatkan
        $total_tagihan = $naker + $kesehatan + $a + $management_fee + $ppn + $pph;
        $display_total_tagihan = number_format($total_tagihan);

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

        $absensiData = \App\Models\Absensi::with(['potongan'])
        ->whereIn('id_pekerja', $workerIds)
        ->whereBetween('tgl_absensi', [$request->tgl_awal, $request->tgl_akhir])
        ->get();

        $potonganMap = [];
        $semuaKategoriPotongan = []; // Array untuk menyimpan semua header potongan (Misal: Kasbon, Koperasi)

        foreach ($absensiData as $abs) {
            $idPekerja = (int) $abs->id_pekerja;

            if ($abs->potongan && !empty($abs->potongan->kategori)) {
                // Decode string JSON dari database
                $kategoriData = is_string($abs->potongan->kategori) 
                                ? json_decode($abs->potongan->kategori, true) 
                                : $abs->potongan->kategori;

                if (is_array($kategoriData)) {
                    foreach ($kategoriData as $namaPot => $val) {
                        // Rapikan nama kategori (Huruf depan kapital)
                        $namaPotCap = ucfirst(strtolower(trim($namaPot))); 

                        // Simpan ke daftar header global jika belum ada
                        if (!in_array($namaPotCap, $semuaKategoriPotongan)) {
                            $semuaKategoriPotongan[] = $namaPotCap;
                        }

                        // ==========================================
                        // PERBAIKAN LOGIKA HITUNGAN:
                        // Cek apakah data berupa array atau langsung angka
                        // ==========================================
                        if (is_array($val)) {
                            $qty = (int) ($val['qty'] ?? 1);
                            $nominal = (float) ($val['nominal'] ?? 0);
                            $subtotal = $qty * $nominal;
                        } else {
                            // Jika format JSON-nya langsung {"Makanan": 15000}
                            $subtotal = (float) $val;
                        }

                        // Tambahkan ke dompet potongan masing-masing pekerja
                        if (!isset($potonganMap[$idPekerja][$namaPotCap])) {
                            $potonganMap[$idPekerja][$namaPotCap] = 0;
                        }
                        $potonganMap[$idPekerja][$namaPotCap] += $subtotal;
                    }
                }
            }
        }

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

        $absensiData = \App\Models\Absensi::with(['potongan'])
        ->whereIn('id_pekerja', $workerIds)
        ->whereBetween('tgl_absensi', [$request->tgl_awal, $request->tgl_akhir])
        ->get();

        $potonganMap = [];
        $semuaKategoriPotongan = []; // Array untuk menyimpan semua header potongan (Misal: Kasbon, Koperasi)

        foreach ($absensiData as $abs) {
            $idPekerja = (int) $abs->id_pekerja;

            if ($abs->potongan && !empty($abs->potongan->kategori)) {
                // Decode string JSON dari database
                $kategoriData = is_string($abs->potongan->kategori) 
                                ? json_decode($abs->potongan->kategori, true) 
                                : $abs->potongan->kategori;

                if (is_array($kategoriData)) {
                    foreach ($kategoriData as $namaPot => $val) {
                        // Rapikan nama kategori (Huruf depan kapital)
                        $namaPotCap = ucfirst(strtolower(trim($namaPot))); 

                        // Simpan ke daftar header global jika belum ada
                        if (!in_array($namaPotCap, $semuaKategoriPotongan)) {
                            $semuaKategoriPotongan[] = $namaPotCap;
                        }

                        // ==========================================
                        // PERBAIKAN LOGIKA HITUNGAN:
                        // Cek apakah data berupa array atau langsung angka
                        // ==========================================
                        if (is_array($val)) {
                            $qty = (int) ($val['qty'] ?? 1);
                            $nominal = (float) ($val['nominal'] ?? 0);
                            $subtotal = $qty * $nominal;
                        } else {
                            // Jika format JSON-nya langsung {"Makanan": 15000}
                            $subtotal = (float) $val;
                        }

                        // Tambahkan ke dompet potongan masing-masing pekerja
                        if (!isset($potonganMap[$idPekerja][$namaPotCap])) {
                            $potonganMap[$idPekerja][$namaPotCap] = 0;
                        }
                        $potonganMap[$idPekerja][$namaPotCap] += $subtotal;
                    }
                }
            }
        }

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
            $rateHbn = ($pkwtAktif?->gaji_overtime ?? 0) * $pkwtAktif->rate_hbn;

            $item->detail_potongan = [];
            $totalPotonganDinamis = 0;
            
            foreach ($semuaKategoriPotongan as $kategoriHeader) {
                $uangPotongan = $potonganMap[$id][$kategoriHeader] ?? 0;
                $item->detail_potongan[$kategoriHeader] = $uangPotongan; // Ini yang nanti di-loop di PDF
                $totalPotonganDinamis += $uangPotongan;
            }

            // --- C. KALKULASI PENDAPATAN ---

            // 1. Upah Pokok
            // $item->upah_pokok = (($input['jam_kerja'] ?? 0) - ($input['hbn'] ?? 0)) * $ratePokok;

            $item->upah_pokok = (($input['upah'] ?? 0) - ($input['tunjangan'] ?? 0)) + (($input['potongan'] ?? 0));

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
            $item->potonganLain = $totalPotonganDinamis;

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
        return Excel::download(new RincianUpahExport($formattedData, $periodeString, $semuaKategoriPotongan), $filename);
    }

    public function ExportDetailHarian(Request $request)
    {
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
            'pkwt.hariKerja',
        ])
        ->whereIn('id', $workerIds)
        ->get()
        ->keyBy('id');

        $penanggung_jawab = $request->input('pj_nama', []);
        $jabatan_pj = $request->input('pj_jabatan', []);

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
        $semuaKategoriPotongan = []; // <-- Array baru untuk header potongan
        $prodMap = [];
        
        foreach ($absensiData as $abs) {
            $tgl = \Carbon\Carbon::parse($abs->tgl_absensi)->format('Y-m-d');
            $idPekerja = (int) $abs->id_pekerja;

            if (!isset($prodMap[$idPekerja])) {
                $prodMap[$idPekerja] = [
                    'kerja' => 0, 'izin' => 0, 'cuti' => 0, 'sakit' => 0, 'rencanaCuti' => 0, 'absen' => 0, 'jam_ot' => 0
                ];
            }

            $detil = $abs->detilHarian;
            if ($detil) {
                $jamKerja = (float) $detil->jam_kerja_harian;
                $overtime = (float) $detil->overtime;
                $status   = $detil->status_kehadiran;

                $warna = ''; 

                if ($detil->hbn == 1) {
                    $warna = '#DDA0DD'; 
                } elseif (in_array($status, [6])) { 
                    $warna = '#FFC7CE'; 
                } elseif ($status == 5) { 
                    $warna = '#C6EFCE'; 
                } elseif (in_array($status, [2, 3, 4])) { 
                    if($detil->isPaid == 1) {
                        $warna = '#87CEFA'; 
                    } elseif($jamKerja > 0) {
                        $warna = '#FFEB9C'; 
                    } else {
                        $warna = '#FCD5B4'; 
                    }
                } elseif ($overtime > 0) {
                    $warna = '#FFB6C1'; 
                }
                
                if ($jamKerja > 0 || $detil->hbn == 1) {
                    $prodMap[$idPekerja]['kerja'] += 1;
                } elseif (in_array($status, [2])) { 
                    $prodMap[$idPekerja]['izin'] += 1;
                } elseif (in_array($status, [3])) { 
                    $prodMap[$idPekerja]['cuti'] += 1;
                } elseif (in_array($status, [4])) { 
                    $prodMap[$idPekerja]['sakit'] += 1;
                } elseif (in_array($status, [5])) { 
                    $prodMap[$idPekerja]['rencanaCuti'] += 1;
                } elseif (in_array($status, [6])) { 
                    $prodMap[$idPekerja]['absen'] += 1;
                }

                $prodMap[$idPekerja]['jam_ot'] += (float) $detil->overtime;

                $attendanceMap[$idPekerja][$tgl] = [
                    'jam'   => $jamKerja,
                    'warna' => $warna
                ];
            }

            // --- EKSTRAK JSON TUNJANGAN ---
            if ($abs->tunjangan && !empty($abs->tunjangan->kategori)) {
                $kategoriData = is_string($abs->tunjangan->kategori) 
                                ? json_decode($abs->tunjangan->kategori, true) 
                                : $abs->tunjangan->kategori;

                if (is_array($kategoriData)) {
                    foreach ($kategoriData as $namaTunj => $val) {
                        $namaTunjCap = ucfirst(strtolower(trim($namaTunj))); 
                        
                        if (!in_array($namaTunjCap, $semuaKategoriTunjangan)) {
                            $semuaKategoriTunjangan[] = $namaTunjCap;
                        }

                        $qty = (int) ($val['qty'] ?? 0);
                        $nominal = (float) ($val['nominal'] ?? 0);
                        $subtotal = $qty * $nominal;

                        if (!isset($tunjanganMap[$idPekerja][$namaTunjCap])) {
                            $tunjanganMap[$idPekerja][$namaTunjCap] = 0;
                        }
                        $tunjanganMap[$idPekerja][$namaTunjCap] += $subtotal;
                    }
                }
            }

            // --- EKSTRAK JSON POTONGAN ---
            if ($abs->potongan && !empty($abs->potongan->kategori)) {
                $kategoriData = is_string($abs->potongan->kategori) 
                                ? json_decode($abs->potongan->kategori, true) 
                                : $abs->potongan->kategori;

                if (is_array($kategoriData)) {
                    foreach ($kategoriData as $namaPot => $val) {
                        $namaPotCap = ucfirst(strtolower(trim($namaPot))); 

                        if (!in_array($namaPotCap, $semuaKategoriPotongan)) {
                            $semuaKategoriPotongan[] = $namaPotCap;
                        }

                        if (is_array($val)) {
                            $qty = (int) ($val['qty'] ?? 1);
                            $nominal = (float) ($val['nominal'] ?? 0);
                            $subtotal = $qty * $nominal;
                        } else {
                            $subtotal = (float) $val;
                        }

                        if (!isset($potonganMap[$idPekerja][$namaPotCap])) {
                            $potonganMap[$idPekerja][$namaPotCap] = 0;
                        }
                        $potonganMap[$idPekerja][$namaPotCap] += $subtotal;
                    }
                }
            }
        }


        // ---------------------------------------------------------
        // [BAGIAN 4] LOOPING DATA PEKERJA UNTUK ROW EXCEL
        // ---------------------------------------------------------

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

            // ===================================================================
            // --- LOGIKA TARGET JAM KERJA & CEK ABSEN BOLOS ---
            // ===================================================================
            $jadwalKerja = []; 
            if ($pkwtAktif && $pkwtAktif->hariKerja) {
                foreach ($pkwtAktif->hariKerja as $jadwal) {
                    $namaHari = strtolower($jadwal->hari); 
                    $jadwalKerja[$namaHari] = (float) $jadwal->jam_kerja; 
                }
            }

            $startPeriode = \Carbon\Carbon::parse($tglAwal)->startOfDay();
            $endPeriode = \Carbon\Carbon::parse($tglAkhir)->startOfDay();
            
            $totalTargetJam = 0;       
            $totalHariAbsenBetulan = 0; 
            $totalJamAbsenBetulan  = 0; 

            for ($date = $startPeriode->copy(); $date->lte($endPeriode); $date->addDay()) {
                $hariStr = strtolower($date->format('D')); 
                $tglStr = $date->format('Y-m-d');
                
                $targetJam = $jadwalKerja[$hariStr] ?? 0;
                $totalTargetJam += $targetJam; 

                if ($targetJam > 0) {
                    if (isset($attendanceMap[$staffDb->id][$tglStr])) {
                        $absensiHarian = $attendanceMap[$staffDb->id][$tglStr];
                        $aktualJamKerja = (float) $absensiHarian['jam'];

                        if ($aktualJamKerja == 0) {
                            $totalHariAbsenBetulan++;
                            $totalJamAbsenBetulan += $targetJam; 
                        } else if ($aktualJamKerja < $targetJam) {
                            $selisihJam = $targetJam - $aktualJamKerja;
                            $totalJamAbsenBetulan += $selisihJam;
                        }
                    } else {
                        $totalHariAbsenBetulan++;
                        $totalJamAbsenBetulan += $targetJam; 
                    }
                }
            }

            $item->hitung_hari_absen = $totalHariAbsenBetulan;
            $item->hitung_jam_absen = $totalJamAbsenBetulan;
            $item->target_jam_kerja = $totalTargetJam;

            // --- A. IDENTITAS ---
            $item->id_original = $staffDb->id;
            $item->nama = $staffDb->nama;
            $item->id_karyawan = $staffDb->id_pekerja ?? $staffDb->nik;
            $item->jabatan = $pkwtAktif?->jabatan?->nama ?? '-';
            $item->divisi = $pkwtAktif?->divisi?->nama ?? '-';
            $item->checkman = 'TRUE';
            $item->no_rekening = $staffDb->rekening ?? '-';

            // --- B. RATE / TARIF ---
            $item->rate_pokok = $pkwtAktif?->gaji_bulanan ?? 0;
            $item->rate_harian = $pkwtAktif?->gaji_harian ?? 0;
            $item->rate_lembur = $pkwtAktif?->gaji_overtime ?? 0;
            $item->rate_hbn = ($pkwtAktif?->gaji_overtime ?? 0) * ($pkwtAktif?->rate_hbn ?? 0);

            $item->tunjangan = (float) ($input['tunjangan'] ?? 0);
            $item->insentif = (float) ($input['insentif'] ?? 0);
            $item->jam_kerja = (float) ($input['jam_kerja'] ?? 0);

            $jamHbnInput = (float) ($input['hbn'] ?? 0);
            $jamLemburInput = (float) ($input['overtime'] ?? 0);

            if ($jamHbnInput > 0) {
                $item->jam_hbn = $jamHbnInput;
                $item->jam_lembur = 0; 
            } else {
                $item->jam_hbn = 0;
                $item->jam_lembur = $jamLemburInput;
            }
            
            $item->jml_uang_lembur = $item->jam_lembur * $item->rate_lembur;
            $item->jml_uang_lembur_hbn = $item->jam_hbn * $item->rate_hbn;

            $item->total_pokok = (float) ($input['upah'] ?? 0);
            $item->total_lembur_biasa = $item->jam_lembur * $item->rate_lembur;
            $item->total_lembur_hbn = $item->jam_hbn * $item->rate_hbn; 
            $item->jam_lembur_hbn = (float) ($input['hbn'] ?? 0);

            // --- C. MEMISAHKAN TUNJANGAN DINAMIS ---
            $item->detail_tunjangan = [];
            $totalUangTunjangan = 0;

            foreach ($semuaKategoriTunjangan as $kategoriHeader) {
                $uangKategori = $tunjanganMap[$staffDb->id][$kategoriHeader] ?? 0;
                $item->detail_tunjangan[$kategoriHeader] = $uangKategori;
                $totalUangTunjangan += $uangKategori;
            }

            $item->uang_tunjangan = 0; 
            $item->jml_uang_tunjangan = $totalUangTunjangan; 
            $item->upah_insentif = 0;
            $item->jml_uang_insentif = 0;

            // --- D. MEMISAHKAN POTONGAN DINAMIS ---
            $item->detail_potongan = [];
            $totalPotonganDinamis = 0;

            foreach ($semuaKategoriPotongan as $kategoriHeader) {
                $uangPotongan = $potonganMap[$staffDb->id][$kategoriHeader] ?? 0;
                $item->detail_potongan[$kategoriHeader] = $uangPotongan;
                $totalPotonganDinamis += $uangPotongan;
            }

            // Gantikan $input['potongan'] lama dengan total dari database
            $item->potongan_lain = $totalPotonganDinamis;

            // --- E. KALKULASI UPAH KOTOR ---
            $item->jml_pokok_upah = (float) ($input['upah'] ?? 0) + $totalPotonganDinamis - $totalUangTunjangan - $item->total_lembur_biasa - $item->total_lembur_hbn;

            $item->pot_absen_per_hari = $item->rate_pokok;
            $item->jml_pot_absen_hari = 0;
            $item->pot_absen_per_jam = $pkwtAktif?->gaji_overtime ?? 0;
            $item->jml_pot_absen_jam = 0;

            $pendapatan_total = $item->jml_pokok_upah + $item->jml_uang_insentif + $item->jml_uang_tunjangan + $item->total_lembur_biasa + $item->total_lembur_hbn;
            $potongan_absen = $item->jml_pot_absen_hari + $item->jml_pot_absen_jam;

            $item->total_upah_kotor = $pendapatan_total - $potongan_absen;

            // --- F. POTONGAN WAJIB ---
            $item->bpjs_tk = $pkwtAktif?->bpjs_naker ?? 0;
            $item->bpjs_kes = $pkwtAktif?->bpjs_kesehatan ?? 0;
            $item->biaya_admin = 2000;
            $item->biaya_klaim = 0;

            // --- G. HASIL AKHIR ---
            $total_potongan_all = $item->bpjs_tk + $item->bpjs_kes + $item->biaya_admin + $item->potongan_lain + $item->biaya_klaim;
            $item->thp = $item->total_upah_kotor - $total_potongan_all;

            // --- H. PRODUKTIVITAS ---
            $prod = $prodMap[$staffDb->id] ?? [
                'kerja' => 0, 'izin' => 0, 'cuti' => 0, 'sakit' => 0, 'rencanaCuti' => 0, 'absen' => 0, 'jam_ot' => 0
            ];

            $item->jml_hari_kerja = $prod['kerja'];
            $item->izin = $prod['izin'];
            $item->cuti = $prod['cuti'];
            $item->sakit = $prod['sakit'];
            $item->rencanaCuti = $prod['rencanaCuti'];
            
            $item->absen = $prod['absen'] + $item->hitung_hari_absen;
            $item->jml_absen = $item->izin + $item->cuti + $item->sakit + $item->rencanaCuti + $item->absen;

            $totalHariPeriode = (int) round($startPeriode->diffInDays($endPeriode)) + 1;
            
            $item->pct_hadir = $totalHariPeriode > 0 ? ($item->jml_hari_kerja / $totalHariPeriode) * 100 : 0;
            $item->pct_absen = $totalHariPeriode > 0 ? ($item->jml_absen / $totalHariPeriode) * 100 : 0;
            $item->pct_jam_kerja = $item->target_jam_kerja > 0 ? ($item->jam_kerja / $item->target_jam_kerja) * 100 : 0;

            $standarJamKerja = $item->target_jam_kerja * 8; 
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

        // Sertakan array $semuaKategoriPotongan ke dalam Export Class
        return \Maatwebsite\Excel\Facades\Excel::download(new DailyReportHarianExport(
            $formattedData,
            $periodeString,
            $grandTotal,
            $unitName,
            $totalDays,
            $tglAwal,
            $tglAkhir,
            $attendanceMap,
            $unit,
            $semuaKategoriTunjangan,
            $penanggung_jawab,
            $jabatan_pj,
            $semuaKategoriPotongan // <-- ARRAY KATEGORI POTONGAN DIKIRIM DI SINI
        ), $filename);
    }

    // public function ExportDetailHarian(Request $request)
    // {
    //     // 1. Ambil data dari request

    //     $workersInput = json_decode($request->workers_json, true);
    //     $idUnit = $request->id_unit;
    //     // Format tanggal untuk Query Database
    //     $tglAwal = \Carbon\Carbon::parse($request->tgl_awal)->format('Y-m-d');
    //     $tglAkhir = \Carbon\Carbon::parse($request->tgl_akhir)->format('Y-m-d');

    //     // 2. Persiapkan data Master Pekerja dari Database
    //     $workerIds = collect($workersInput)->pluck('id')->toArray();
    //     $dbPekerja = Pekerja::with([
    //         'pkwt' => function ($query) {
    //             $query->latest('id'); // Ambil kontrak terbaru
    //         },
    //         'pkwt.jabatan',
    //         'pkwt.divisi',
    //         'pkwt.hariKerja',
    //     ])
    //     ->whereIn('id', $workerIds)
    //     ->get()
    //     ->keyBy('id');

    //     $penanggung_jawab = $request->input('pj_nama', []);
    //     $jabatan_pj = $request->input('pj_jabatan', []);

    //     // ---------------------------------------------------------
    //     // [BAGIAN 3] AMBIL DATA ABSENSI & BUAT MAPPING (DILAKUKAN SEKALI SAJA)
    //     // ---------------------------------------------------------
        
    //     // A. Query Database
    //     $absensiData = Absensi::with(['detilHarian', 'tunjangan', 'potongan'])
    //         ->whereIn('id_pekerja', $workerIds)
    //         ->whereBetween('tgl_absensi', [$tglAwal, $tglAkhir])
    //         ->get();

    //     // B. Buat Mapping Array
    //     $attendanceMap = [];
    //     $tunjanganMap = [];
    //     $semuaKategoriTunjangan = [];
    //     $potonganMap = [];
    //     $prodMap = [];
        
    //     foreach ($absensiData as $abs) {
    //         $tgl = \Carbon\Carbon::parse($abs->tgl_absensi)->format('Y-m-d');
    //         $idPekerja = (int) $abs->id_pekerja;

    //         if (!isset($prodMap[$idPekerja])) {
    //             $prodMap[$idPekerja] = [
    //                 'kerja' => 0, 
    //                 'izin' => 0, 
    //                 'cuti' => 0, 
    //                 'sakit' => 0, 
    //                 'rencanaCuti' => 0, 
    //                 'absen' => 0, 
    //                 'jam_ot' => 0
    //             ];
    //         }

    //         $detil = $abs->detilHarian;
    //         if ($detil) {
    //             $jamKerja = (float) $detil->jam_kerja_harian;
    //             $overtime = (float) $detil->overtime;
    //             $status   = $detil->status_kehadiran;

    //             $warna = ''; // Default Colorless (Hadir biasa)

    //             // URUTAN FILTER WARNA
    //             if ($detil->hbn == 1) {
    //                 $warna = '#DDA0DD'; // Ungu (HBN)
    //             } elseif (in_array($status, [6])) { // Sesuaikan angka ID Absen Anda
    //                 $warna = '#FFC7CE'; // Merah (Absen/Alpha)
    //             } elseif ($status == 5) { // Sesuaikan angka ID Rencana Cuti Anda
    //                 $warna = '#C6EFCE'; // Hijau (Rencana Cuti)
    //             } elseif (in_array($status, [2, 3, 4])) { // Izin, Cuti, Sakit
    //                 if($detil->isPaid == 1)
    //                 {
    //                     $warna = '#87CEFA'; // Biru (isPaid)
    //                 }elseif($jamKerja > 0) {
    //                     $warna = '#FFEB9C'; // Kuning (Izin tapi ada jam)
    //                 }else{
    //                     $warna = '#FCD5B4'; // Orange (Izin tidak ada jam)
    //                 }
    //             }elseif ($overtime > 0) {
    //                 $warna = '#FFB6C1'; // Pink (Overtime)
    //             }
                
    //             if ($jamKerja > 0 || $detil->hbn == 1) {
    //                 $prodMap[$idPekerja]['kerja'] += 1;
    //             } elseif (in_array($status, [2])) { // Asumsi 4 = Sakit
    //                 $prodMap[$idPekerja]['izin'] += 1;
    //             } elseif (in_array($status, [3])) { // Asumsi 2/3 = Izin/Cuti
    //                 $prodMap[$idPekerja]['cuti'] += 1;
    //             } elseif (in_array($status, [4])) { // Asumsi 6 = Alpha
    //                 $prodMap[$idPekerja]['sakit'] += 1;
    //             }elseif (in_array($status, [5])) { // Asumsi 6 = Alpha
    //                 $prodMap[$idPekerja]['rencanaCuti'] += 1;
    //             }elseif (in_array($status, [6])) { // Asumsi 6 = Alpha
    //                 $prodMap[$idPekerja]['absen'] += 1;
    //             }

    //             $prodMap[$idPekerja]['jam_ot'] += (float) $detil->overtime;

    //             // Simpan jam kerja DAN warna ke dalam array
    //             $attendanceMap[$idPekerja][$tgl] = [
    //                 'jam'   => $jamKerja,
    //                 'warna' => $warna
    //             ];
    //         }

    //         if ($abs->tunjangan && !empty($abs->tunjangan->kategori)) {
    //             $kategoriData = is_string($abs->tunjangan->kategori) 
    //                             ? json_decode($abs->tunjangan->kategori, true) 
    //                             : $abs->tunjangan->kategori;

    //             if (is_array($kategoriData)) {
    //                 foreach ($kategoriData as $namaTunj => $val) {
    //                     // Kapitalisasi huruf pertama (Misal: 'score' jadi 'Score')
    //                     $namaTunjCap = ucfirst(strtolower(trim($namaTunj))); 
                        
    //                     // Kumpulkan nama kategori ke array header jika belum ada
    //                     if (!in_array($namaTunjCap, $semuaKategoriTunjangan)) {
    //                         $semuaKategoriTunjangan[] = $namaTunjCap;
    //                     }

    //                     // Hitung subtotal: Qty x Nominal (Sesuai ide gambar Anda)
    //                     $qty = (int) ($val['qty'] ?? 0);
    //                     $nominal = (float) ($val['nominal'] ?? 0);
    //                     $subtotal = $qty * $nominal;

    //                     // Masukkan ke dompet masing-masing pekerja
    //                     if (!isset($tunjanganMap[$idPekerja][$namaTunjCap])) {
    //                         $tunjanganMap[$idPekerja][$namaTunjCap] = 0;
    //                     }
    //                     $tunjanganMap[$idPekerja][$namaTunjCap] += $subtotal;
    //                 }
    //             }
    //         }

    //         if ($abs->potongan) {
    //             // Ambil langsung dari kolom 'total' sesuai database Anda
    //             $totalPotongan = (float) $abs->potongan->total;

    //             if (!isset($potonganMap[$idPekerja])) {
    //                 $potonganMap[$idPekerja] = 0;
    //             }
    //             $potonganMap[$idPekerja] += $totalPotongan;
    //         }
    //     }


    //     // DEBUG: Cek isi map sebelum lanjut (Hapus jika sudah benar)
    //     // dd($attendanceMap);

    //     // ---------------------------------------------------------
    //     // [BAGIAN 4] LOOPING DATA PEKERJA UNTUK ROW EXCEL
    //     // ---------------------------------------------------------

    //     // Ambil info Unit untuk nama unit
    //     $unit = Unit::find($idUnit);
    //     $unitName = $unit ? $unit->nama_unit : 'UNIT TIDAK DIKETAHUI';

    //     $realItems = [];


    //     foreach ($workersInput as $input) {
    //         $id = $input['id'];
    //         if (!isset($dbPekerja[$id])) {
    //             continue;
    //         }   

    //         $staffDb = $dbPekerja[$id];
    //         $pkwtAktif = $staffDb->pkwt->first();

    //         $item = new \stdClass();

    //         // ===================================================================
    //         // --- LOGIKA TARGET JAM KERJA & CEK ABSEN BOLOS (TIDAK CHECK-IN) ---
    //         // ===================================================================
    //         $jadwalKerja = []; 
    //         if ($pkwtAktif && $pkwtAktif->hariKerja) {
    //             foreach ($pkwtAktif->hariKerja as $jadwal) {
    //                 // Sesuai gambar DB Anda: 'mon', 'tue', 'wed', dst.
    //                 $namaHari = strtolower($jadwal->hari); 
    //                 $jadwalKerja[$namaHari] = (float) $jadwal->jam_kerja; 
    //             }
    //         }

    //         $startPeriode = \Carbon\Carbon::parse($tglAwal)->startOfDay();
    //         $endPeriode = \Carbon\Carbon::parse($tglAkhir)->startOfDay();
            
    //         $totalTargetJam = 0;       // Untuk menyimpan Total Jam Seharusnya (Contoh: 90 Jam)
    //         $totalHariAbsenBetulan = 0; // Menghitung total hari bolos murni
    //         $totalJamAbsenBetulan  = 0; // Menghitung total jam hilang (bolos / pulang cepat)

    //         for ($date = $startPeriode->copy(); $date->lte($endPeriode); $date->addDay()) {
    //             // Gunakan format 'D' untuk menghasilkan singkatan bahasa inggris (Mon, Tue, Wed)
    //             $hariStr = strtolower($date->format('D')); 
    //             $tglStr = $date->format('Y-m-d');
                
    //             // Ambil target jam dari PKWT pada hari tersebut
    //             $targetJam = $jadwalKerja[$hariStr] ?? 0;
    //             $totalTargetJam += $targetJam; // Tambahkan ke akumulasi total jam periode ini

    //             // Cek Absen HANYA JIKA di hari tersebut dia memiliki kewajiban jadwal kerja (targetJam > 0)
    //             if ($targetJam > 0) {
    //                 if (isset($attendanceMap[$staffDb->id][$tglStr])) {
    //                     $absensiHarian = $attendanceMap[$staffDb->id][$tglStr];
    //                     $aktualJamKerja = (float) $absensiHarian['jam'];

    //                     if ($aktualJamKerja == 0) {
    //                         // Kasus 1: Ada data absen tapi jam kerjanya 0
    //                         $totalHariAbsenBetulan++;
    //                         $totalJamAbsenBetulan += $targetJam; 
    //                     } else if ($aktualJamKerja < $targetJam) {
    //                         // Kasus 2: Pulang cepat / telat (Target 8 jam, tapi kerja 5 jam)
    //                         $selisihJam = $targetJam - $aktualJamKerja;
    //                         $totalJamAbsenBetulan += $selisihJam;
    //                     }
    //                 } else {
    //                     // Kasus 3: Tidak ada data di database (Mangkir/Bolos Murni)
    //                     $totalHariAbsenBetulan++;
    //                     $totalJamAbsenBetulan += $targetJam; 
    //                 }
    //             }
    //         }

    //         // Simpan hasil absen siluman
    //         $item->hitung_hari_absen = $totalHariAbsenBetulan;
    //         $item->hitung_jam_absen = $totalJamAbsenBetulan;
            
    //         // Simpan total target jam untuk rumus persentase
    //         $item->target_jam_kerja = $totalTargetJam;

    //         // --- A. IDENTITAS ---
    //         // PENTING: Simpan ID Asli untuk kunci pencarian di Blade nanti
    //         $item->id_original = $staffDb->id;

    //         $item->nama = $staffDb->nama;
    //         $item->id_karyawan = $staffDb->id_pekerja ?? $staffDb->nik;
    //         $item->jabatan = $pkwtAktif?->jabatan?->nama ?? '-';
    //         $item->divisi = $pkwtAktif?->divisi?->nama ?? '-';
    //         $item->checkman = 'TRUE';
    //         $item->no_rekening = $staffDb->rekening ?? '-';

    //         // --- B. RATE / TARIF ---
    //         $item->rate_pokok = $pkwtAktif?->gaji_bulanan ?? 0;
    //         $item->rate_harian = $pkwtAktif?->gaji_harian ?? 0;
    //         $item->rate_lembur = $pkwtAktif?->gaji_overtime ?? 0;
    //         $item->rate_hbn = ($pkwtAktif?->gaji_overtime ?? 0) * ($pkwtAktif?->rate_hbn ?? 0);

    //         $item->tunjangan = (float) ($input['tunjangan'] ?? 0);
    //         $item->insentif = (float) ($input['insentif'] ?? 0);

    //         // --- C. INPUT JAM & UPAH ---
    //         $item->jam_kerja = (float) ($input['jam_kerja'] ?? 0);

    //         $jamHbnInput = (float) ($input['hbn'] ?? 0);
    //         $jamLemburInput = (float) ($input['overtime'] ?? 0);

    //         // 2. LOGIKA PEMISAHAN HBN DAN OVERTIME BIASA
    //         // Jika ada jam HBN, maka masuk ke HBN dan lembur biasa di-nol-kan
    //         if ($jamHbnInput > 0) {
    //             $item->jam_hbn = $jamHbnInput;
    //             $item->jam_lembur = 0; // Mencegah masuk ke overtime biasa
    //         } else {
    //             $item->jam_hbn = 0;
    //             $item->jam_lembur = $jamLemburInput;
    //         }

            
    //         $item->jml_uang_lembur = $item->jam_lembur * $item->rate_lembur;
    //         $item->jml_uang_lembur_hbn = $item->jam_hbn * $item->rate_hbn;

    //         $item->total_pokok = (float) ($input['upah'] ?? 0);
    //         $item->total_lembur_biasa = $item->jam_lembur * $item->rate_lembur;
    //         $item->total_lembur_hbn = $item->jam_hbn * $item->rate_hbn; 

    //         $item->jam_lembur_hbn = (float) ($input['hbn'] ?? 0);

    //         // dump($item->rate_hbn,$item->jam_hbn);

    //         // dump(($input['upah'] ?? 0), ($input['potongan'] ?? 0), ($input['tunjangan'] ?? 0), $item->total_lembur_biasa , $item->total_lembur_hbn);

    //         $item->jml_pokok_upah = (float) ($input['upah'] ?? 0) + ($input['potongan'] ?? 0) - ($input['tunjangan'] ?? 0)
    //         - $item->total_lembur_biasa - $item->total_lembur_hbn ;

    //         //Tunjangan
    //         $item->detail_tunjangan = [];
    //         $totalUangTunjangan = 0;

    //         foreach ($semuaKategoriTunjangan as $kategoriHeader) {
    //             // Ambil dari rekap map, jika tidak ada isikan 0
    //             $uangKategori = $tunjanganMap[$staffDb->id][$kategoriHeader] ?? 0;
                
    //             $item->detail_tunjangan[$kategoriHeader] = $uangKategori;
    //             $totalUangTunjangan += $uangKategori;
    //         }

    //         $item->uang_tunjangan = 0; 
    //         $item->jml_uang_tunjangan = $totalUangTunjangan; // Total (J)

    //         // $item->jml_uang_tunjangan = (float) ($input['tunjangan'] ?? 0);
    //         $item->upah_insentif = 0;
    //         $item->jml_uang_insentif = 0;

    //         // --- D. POTONGAN ABSENSI ---
    //         $item->pot_absen_per_hari = $item->rate_pokok;
    //         $item->jml_pot_absen_hari = 0;

    //         $item->pot_absen_per_jam = $pkwtAktif?->gaji_overtime ?? 0;
    //         $item->jml_pot_absen_jam = 0;

    //         // --- E. TOTAL UPAH KOTOR ---
    //         $pendapatan_total = $item->jml_pokok_upah + $item->jml_uang_insentif + $item->jml_uang_tunjangan + $item->total_lembur_biasa + $item->total_lembur_hbn;
    //         $potongan_absen = $item->jml_pot_absen_hari + $item->jml_pot_absen_jam;

    //         $item->total_upah_kotor = $pendapatan_total - $potongan_absen;

    //         // --- F. POTONGAN WAJIB ---
    //         $item->bpjs_tk = $pkwtAktif?->bpjs_naker ?? 0;
    //         $item->bpjs_kes = $pkwtAktif?->bpjs_kesehatan ?? 0;
    //         $item->biaya_admin = 2000;

            
    //         $item->potongan_lain = $potonganMap[$staffDb->id] ?? 0;


    //         $item->biaya_klaim = 0;

    //         // --- G. HASIL AKHIR ---
    //         $total_potongan_all = $item->bpjs_tk + $item->bpjs_kes + $item->biaya_admin + $item->potongan_lain + $item->biaya_klaim;
    //         $item->thp = $item->total_upah_kotor - $total_potongan_all;

    //         // --- H. PRODUKTIVITAS ---
    //         $prod = $prodMap[$staffDb->id] ?? [
    //             'kerja' => 0, 'izin' => 0, 'cuti' => 0, 'sakit' => 0, 'rencanaCuti' => 0, 'absen' => 0, 'jam_ot' => 0
    //         ];

    //         // 1. Ambil data mentah dari array map
    //         $item->jml_hari_kerja = $prod['kerja'];
    //         $item->izin = $prod['izin'];
    //         $item->cuti = $prod['cuti'];
    //         $item->sakit = $prod['sakit'];
    //         $item->rencanaCuti = $prod['rencanaCuti'];
    //         $item->absen = $prod['absen'];

    //         $item->absen = $prod['absen'] + $item->hitung_hari_absen;

    //         // 2. Hitung Total Absen (Semua jenis ketidakhadiran digabung)
    //         $item->jml_absen = $item->izin + $item->cuti + $item->sakit + $item->rencanaCuti + $item->absen;

    //         // 3. Hitung Persentase Kehadiran & Ketidakhadiran
    //         // Kita hitung jumlah hari dalam periode ini untuk jadi nilai pembagi (denominator)
    //         $startPeriode = \Carbon\Carbon::parse($tglAwal)->startOfDay();
    //         $endPeriode = \Carbon\Carbon::parse($tglAkhir)->startOfDay();
    //         $totalHariPeriode = (int) round($startPeriode->diffInDays($endPeriode)) + 1;
            
    //         // Hitung persen (%)
    //         $item->pct_hadir = $totalHariPeriode > 0 ? ($item->jml_hari_kerja / $totalHariPeriode) * 100 : 0;
    //         $item->pct_absen = $totalHariPeriode > 0 ? ($item->jml_absen / $totalHariPeriode) * 100 : 0;

    //         $item->pct_jam_kerja = $item->target_jam_kerja > 0 ? ($item->jam_kerja / $item->target_jam_kerja) * 100 : 0;

    //         // 4. Hitung Persentase Overtime (Asumsi Standar Jam Kerja = 8 Jam/Hari)
    //         $standarJamKerja = $item->target_jam_kerja * 8; 
    //         $item->pct_overtime = $standarJamKerja > 0 ? ($prod['jam_ot'] / $standarJamKerja) * 100 : 0;

    //         $realItems[] = $item;
    //     }

    //     // 5. Finalisasi Data untuk Export
    //     $formattedData = collect($realItems);
    //     $grandTotal = $formattedData->sum('thp');

    //     // 6. Hitung Periode & Total Days untuk Header Excel
    //     $start = \Carbon\Carbon::parse($tglAwal)->startOfDay();
    //     $end = \Carbon\Carbon::parse($tglAkhir)->startOfDay();
    //     $periodeString = strtoupper($start->isoFormat('D MMMM Y') . ' - ' . $end->isoFormat('D MMMM Y'));

    //     $totalDays = (int) round($start->diffInDays($end)) + 1;
    //     $filename = 'Detail_Upah_Harian_' . $unitName . '_' . now()->format('Ymd_His') . '.xlsx';

    //     // dd($attendanceMap);
    //     // Sertakan $attendanceMap ke dalam Export Class
    //     return Excel::download(new DailyReportHarianExport(
    //         $formattedData,
    //         $periodeString,
    //         $grandTotal,
    //         $unitName,
    //         $totalDays,
    //         $tglAwal, // string Y-m-d
    //         $tglAkhir, // string Y-m-d
    //         $attendanceMap, // Array Mapping
    //         $unit,
    //         $semuaKategoriTunjangan,
    //         $penanggung_jawab,
    //         $jabatan_pj,
    //     ), $filename);
    // }

    public function SummaryUpahHarian(Request $request)
    {
        // dd($request->all());
        // 1. Ambil Data Dasar dari Reques
        $workersInput = is_string($request->workers_json) ? json_decode($request->workers_json, true) : $request->workers_json;
        
        $idUnit = $request->id_unit;
        $tglAwal = \Carbon\Carbon::parse($request->tgl_awal)->format('Y-m-d');
        $tglAkhir = \Carbon\Carbon::parse($request->tgl_akhir)->format('Y-m-d');
        
        $biayaAdminGlobal = (float) $request->biaya_admin;

        // 2. Ambil Data Master Pekerja & PKWT
        $workerIds = array_column($workersInput, 'id');
        $dbPekerja = Pekerja::with(['pkwt.divisi'])->whereIn('id', $workerIds)->get()->keyBy('id');

        // 3. Olah Data untuk Summary
        $processedData = [];
        $no = 1;

        foreach ($workersInput as $input) {
            $id = $input['id'];
            if (!isset($dbPekerja[$id])) continue;

            $staff = $dbPekerja[$id];
            $pkwt = $staff->pkwt->sortByDesc('id')->first(); 
            
            // --- PENDAPATAN ---
            $gapok = (float) ($input['upah'] ?? 0);
            $rateLembur = $pkwt->gaji_overtime ?? 0;
            $rateHbn = $rateLembur * ($pkwt->rate_hbn ?? 1.5);
            $lembur = ((float) ($input['overtime'] ?? 0) * $rateLembur) + ((float) ($input['hbn'] ?? 0) * $rateHbn);
            
            $koreksi = (float) ($input['potongan'] ?? 0);
            $lainnya = (float) ($input['tunjangan'] ?? 0);
            
            $total_pendapatan = $gapok + $lembur - $koreksi + $lainnya;

            // --- BPJS ---
            $bpjstk = $pkwt->bpjs_naker ?? 0;
            $bpjskes = $pkwt->bpjs_kesehatan ?? 0;
            $total_gaji = $total_pendapatan - $bpjstk - $bpjskes;

            // --- INVOICE / PAJAK ---
            $management_fee = $gapok * ($biayaAdminGlobal / 100); // Misal biaya_admin diinput sbg persen (cth: 5)
            
            $dpp = $management_fee / 1.0909; 
            $ppn = $dpp * 0.12;              
            $pph = $management_fee * 0.02;   
            
            $invoice = $total_gaji + $management_fee + $ppn - $pph;

            \Carbon\Carbon::setLocale('id');
            $joinDate = $pkwt->tgl_mulai_pkwt ? strtoupper(\Carbon\Carbon::parse($pkwt->tgl_mulai_pkwt)->translatedFormat('d M Y')) : '-';
            $exitDate = $pkwt->tgl_akhir_pkwt ? strtoupper(\Carbon\Carbon::parse($pkwt->tgl_akhir_pkwt)->translatedFormat('d M Y')) : '-';

            $processedData[] = [
                'no' => $no++,
                'nik' => $staff->nik ?? $staff->id_pekerja ?? '-',
                'nama' => $staff->nama,
                'section' => $pkwt->divisi->nama ?? '-',
                'join' => $joinDate,
                'exit' => $exitDate,
                'status' => 'HARIAN', // Sesuaikan jika ada logika khusus
                'gapok' => round($gapok),
                'lembur' => round($lembur),
                'koreksi' => round($koreksi),
                'lainnya' => round($lainnya),
                'total_pendapatan' => round($total_pendapatan),
                'management_fee' => round($management_fee),
                'bpjstk' => round($bpjstk),
                'bpjskes' => round($bpjskes),
                'total_gaji' => round($total_gaji),
                'dpp' => round($dpp),
                'ppn' => round($ppn),
                'pph' => round($pph),
                'invoice' => round($invoice)
            ];
        }

        // 4. Hitung Totals
        $totals = [
            'gapok' => collect($processedData)->sum('gapok'),
            'lembur' => collect($processedData)->sum('lembur'),
            'koreksi' => collect($processedData)->sum('koreksi'),
            'lainnya' => collect($processedData)->sum('lainnya'),
            'total_pendapatan' => collect($processedData)->sum('total_pendapatan'),
            'management_fee' => collect($processedData)->sum('management_fee'),
            'bpjstk' => collect($processedData)->sum('bpjstk'),
            'bpjskes' => collect($processedData)->sum('bpjskes'),
            'total_gaji' => collect($processedData)->sum('total_gaji'),
            'dpp' => collect($processedData)->sum('dpp'),
            'ppn' => collect($processedData)->sum('ppn'),
            'pph' => collect($processedData)->sum('pph'),
            'invoice' => collect($processedData)->sum('invoice'),
        ];

        // 5. Finalisasi
        $start = \Carbon\Carbon::parse($tglAwal)->startOfDay();
        $end = \Carbon\Carbon::parse($tglAkhir)->startOfDay();
        $periodeString = strtoupper($start->translatedFormat('d F Y') . ' - ' . $end->translatedFormat('d F Y'));
        
        $unit = Unit::find($idUnit);
        $unitName = $unit ? $unit->nama_unit : 'UNIT TIDAK DIKETAHUI';
        
        $filename = 'Summary_Upah_' . $unitName . '_' . now()->format('Ymd_His') . '.xlsx';
        // 6. Return ke Export Class
        return Excel::download(new \App\Exports\SummaryUpahExport(
            $processedData, 
            $totals, 
            $periodeString, 
            $unitName,
            $request->pj_nama,
            $request->pj_jabatan
        ), $filename);
    }



    public function dispatchPayrollEmails(Request $request)
    {
        \Log::info('Dispatching Payroll Request:', $request->all());
        
        // 1. Validation
        $request->validate([
            'id_unit' => 'required',
            'tgl_awal' => 'required|date',
            'tgl_akhir' => 'required|date',
            'workers_json' => 'required',
        ]);

        $id_unit = $request->id_unit;
        $tgl_awal = Carbon::parse($request->tgl_awal)->format('Y-m-d');
        $tgl_akhir = Carbon::parse($request->tgl_akhir)->format('Y-m-d');
        $grand_total = $request->grand_total;
        $workers = json_decode($request->workers_json, true) ?? [];

        \Log::info('Decoded Workers:', ['count' => count($workers)]);

        if (empty($workers)) {
            return response()->json([
                'success' => false,
                'error' => 'Data pekerja kosong. Silahkan periksa kembali inputan anda.'
            ], 400);
        }

        return DB::transaction(function () use ($id_unit, $tgl_awal, $tgl_akhir, $grand_total, $workers) {
            // Create History header
            $history = PayrollHistory::create([
                'id_unit' => $id_unit,
                'period_start' => $tgl_awal,
                'period_end' => $tgl_akhir,
                'total_payroll' => $grand_total,
            ]);

            // Fetch Workers exact info for email mapping if missing
            $workerIds = array_column($workers, 'id');
            $dbPekerja = Pekerja::with(['pkwtAktif.divisi', 'pkwtAktif.jabatan'])
                ->whereIn('id', $workerIds)
                ->get()
                ->keyBy('id');

            // Insert Details
            foreach ($workers as $w) {
                $dbWorker = $dbPekerja[$w['id']] ?? null;
                $email = $dbWorker ? $dbWorker->email : null;
                
                // Get Divisi & Jabatan Names from Active PKWT
                $activePkwt = $dbWorker ? $dbWorker->pkwtAktif : null;
                $divisi = $activePkwt && $activePkwt->divisi ? $activePkwt->divisi->nama : '-';
                $jabatan = $activePkwt && $activePkwt->jabatan ? $activePkwt->jabatan->nama : '-';

                // PAYLOAD DATA (ensure they are numeric)
                $upah = (float) ($w['upah'] ?? 0);
                $potongan = (float) ($w['potongan'] ?? 0);
                $tunjangan = (float) ($w['tunjangan'] ?? 0);
                $lembur = (float) ($w['overtime_salary'] ?? 0);
                $lembur_hbn = (float) ($w['hbn_salary'] ?? 0);
                $insentif = (float) ($w['insentif'] ?? 0);

                // Note: From frontend, 'upah' usually already includes adjustments (net_salary).
                // However, to keep it consistent with the table structure:
                // If 'upah' IS the final amount, then take_home_pay = upah.
                // We'll trust the 'upah' key from payload as the final calculated rate for now to avoid logic mismatches.
                $take_home_pay = $upah;

                PayrollHistory_Detail::create([
                    'payroll_history_id' => $history->id,
                    'id_pekerja'       => $w['id'],
                    'nama'             => $dbWorker ? $dbWorker->nama : 'Unknown',
                    'email'            => $email,
                    'divisi'           => $divisi,
                    'jabatan'          => $jabatan,
                    'upah_pokok'       => $upah, 
                    'lembur'           => $lembur, 
                    'lembur_hbn'       => $lembur_hbn, 
                    'insentif'         => $insentif,
                    'tunjangan'        => $tunjangan,
                    'potongan'         => $potongan,
                    'take_home_pay'    => $take_home_pay,
                    'email_status'     => 'pending',
                ]);
            }

            // Dispatch Jobs sequentially
            Bus::chain([
                new GeneratePayrollPdfJob($history->id),
                new SendPayrollEmailsJob($history->id)
            ])->dispatch();

            return response()->json([
                'success' => true,
                'message' => 'Jobs dispatched successfully',
                'history_id' => $history->id
            ]);
        });
    }
}
