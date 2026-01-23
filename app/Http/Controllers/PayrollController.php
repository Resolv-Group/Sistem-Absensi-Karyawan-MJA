<?php

namespace App\Http\Controllers;

use App\Exports\DetilBoronganExport;
use App\Models\Absensi;
use App\Models\Pekerja;
use App\Models\Unit;
use Carbon\Carbon;
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
        // dd($request->all());
        // dd($id_unit);
        $id_unit = $request->id_unit;

        $unit = Unit::find($id_unit); // ⬅️ ambil unit

        $paidWorkerIds = $request->input('paid_workers', []);
        $tanggalMulai = $request->tanggal_mulai;
        $tanggalAkhir = $request->tanggal_akhir;
        $pembayaranLain = (int) $request->pembayaran_lain;
        $tunjangan = (int) $request->tunjangan_bayaran;

        $periode = Carbon::parse($tanggalMulai)->translatedFormat('d')
        . '—'
        . Carbon::parse($tanggalAkhir)->translatedFormat('d M Y');

        // Ambil data pekerja yang dipilih
        $workers = Pekerja::whereIn('id', $paidWorkerIds)
                    ->orWhereIn('id_pekerja', $paidWorkerIds)
                    ->get();

        // Olah data potongan tanggal (Step 3) dari modal
        $specificExclusions = collect($request->input('specific_workers', []))
            ->filter(fn($item) => !empty($item['id']) && !empty($item['date']))
            ->groupBy('id');

        $payrollData = [
            'unit_name' => $request->unit_name ?? 'Unit Borongan',
            'periode' => Carbon::parse($tanggalMulai)->translatedFormat('d') . ' — ' . Carbon::parse($tanggalAkhir)->translatedFormat('d M Y'),
            'pembayaran_lain' => $pembayaranLain,
            'total_pekerja' => $workers->count(),
            'items' => $workers->map(function($w) use ($id_unit, $periode, $specificExclusions, $pembayaranLain, $tunjangan, $tanggalMulai, $tanggalAkhir) {

                // 1. Dapatkan list tanggal yang dikecualikan (potongan) untuk pekerja ini
                $excludedDates = $specificExclusions->get($w->id_pekerja)
                    ? $specificExclusions->get($w->id_pekerja)->pluck('date')->toArray()
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
                        $totalGajiBorongan += $tempQty * ($detil->bayaranItem ?? 0);

                        $tempQty = 0;
                    }
                }
                // Gaji Bersih = Total Hasil Borongan + Penyesuaian Global
                $netSalary = $totalGajiBorongan - $pembayaranLain + $tunjangan;

                // dd($netSalary);

                return [
                    'unit_id'   => $id_unit,
                    'unit_name' => $unit?->nama_unit ?? '-',
                    'id_pekerja' => $w->id_pekerja,
                    'periode' => $periode,
                    'nama' => $w->nama,
                    'nik' => $w->nik,
                    'total_barang' => $totalQty,
                    'hasil_gaji_borongan' => $totalGajiBorongan,
                    'potongan_count' => count($excludedDates),
                    'net_salary' => $netSalary,
                    'pembayaran_lain' => $pembayaranLain,
                    'tunjangan' => $tunjangan,
                ];
            }),
        ];

        $payrollData['grand_total'] = collect($payrollData['items'])->sum('net_salary');

        $payrollData['total_potongan_hari'] = collect($payrollData['items'])
            ->sum('potongan_count');

        $payrollData['total_penyesuaian'] = collect($payrollData['items'])
            ->sum(fn ($item) => $item['tunjangan'] - $item['pembayaran_lain']);

        return view('Payroll.overview-payroll', compact('payrollData'));
    }

    function ExportDetailBorongan()
    {
        $tanggal_awal  = '2026-01-20';
        $tanggal_akhir = '2026-01-23';

        $absensiList = Absensi::with([
            'detilBorongan.borongan:id,harga_pekerja,nama_item'
        ])
        ->whereBetween('tgl_absensi', [$tanggal_awal, $tanggal_akhir])
        ->get();

        // dd($absensiList);

        $data = collect();

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
                $maxRejectSubkon = (int) ceil($qty * 0.01);

                // rej mc dibebankan = 0
                $rejMc = 0;

                // total dibayar (rumus)
                $totalDibayarRumus = $fd + $rejMc + $goodMc;

                // total dibayar (pcs) → dibulatkan
                $totalDibayarPcs = round($totalDibayarRumus);

                // unit price dari tabel borongan
                $unitPrice = $detil->borongan->harga_pekerja ?? 0;

                // total dibayarkan (Rp)
                $totalBayarRp = $totalDibayarPcs * $unitPrice;

                // ===== PUSH KE DATA =====
                $data->push((object) [
                    'tanggal' => $absensi->tgl_absensi,
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
                ]);
            }
        }



        // dd($data);

        // $dateawal = date;
        // $dateakhir = date();
        // $id_unit;
        // $id_pekerja;
        // $divisi;
        // $absensi;
        // $detil_borongan;
        // $potongan_bpjs_naker;
        // $potongan_bpjs_kesehatan;
        // $potongan_lain;
        // $take_home;

        return Excel::download(
        new DetilBoronganExport(
            $data,
            '16 OKTOBER 2025 - 31 OKTOBER 2025',
            'AGUSTINO TITAN ISKANDAR',
            'OPERATOR INSPEKSI',
            48705,
            0,
            2596854
        ),
        'summary_upah.xlsx'
    );
    }
}
