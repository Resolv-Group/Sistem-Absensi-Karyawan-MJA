<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    function viewUnitMain(Request $request) {

        // --- 1. CALCULATE STATS (Top Cards) ---
        $totalUnit = Unit::count(); // total pekerja
        $unitBaru = Unit::where('created_at', '>=', now()->subMonth())->count(); // pekerja baru dari bulan lalu
        // $mitraMendekati = Unit::where('status_aktif', 1)
        //     ->whereNotNull('tgl_akhir_mou')
        //     ->whereBetween('tgl_akhir_mou', [
        //         now(),
        //         now()->addDays(30)
        //     ])
        //     ->count();
        $tidakAktif = Unit::where('status_aktif', '!=', '1')->count(); // pekerja tidak aktif

        // --- 2. BUILD QUERY ---
        $query = Unit::query();

        // A. Filter by Search (Name, NIK, KPJ)
        // We check for 'search' (from new JS) or 'q' (fallback)
        $search = $request->input('search') ?? $request->input('q');

        $query->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('nama_unit', 'LIKE', "%{$search}%"); // Ensure column name is 'no_kpj' or 'kpj' based on your DB
            });
        });

        // B. Filter by Status (Exact Match)
        // We use $request->filled() to ensure we don't filter if value is empty/null
        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status_aktif', $request->status);
        });

        // C. Filter by Date Range (Tanggal Bergabung)
        $query->when($request->start_date, function ($q) use ($request) {
            $q->whereDate('akhir_perjanjian', '>=', $request->start_date);
        });

        $query->when($request->end_date, function ($q) use ($request) {
            $q->whereDate('akhir_perjanjian', '<=', $request->end_date);
        });

        // --- 3. FETCH DATA ---
        $unit = $query->orderBy('created_at', 'desc')
                        ->paginate(10)
                        ->withQueryString();


        // --- 4. RETURN RESPONSE ---

        // If AJAX request (from the search/filter script), return ONLY the table partial
        if ($request->ajax()) {
            return view('Unit.partials.unit-table', compact('unit'))->render();
        }

        // Otherwise return the full page
        return view('Unit.main-unit', compact('unit', 'totalUnit', 'unitBaru', 'tidakAktif'));
    }
}
