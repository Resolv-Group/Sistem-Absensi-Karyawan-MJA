<?php

namespace App\Http\Controllers;

use App\Exports\DetilBoronganExport;
use App\Models\Absensi;
use App\Models\Unit;
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
