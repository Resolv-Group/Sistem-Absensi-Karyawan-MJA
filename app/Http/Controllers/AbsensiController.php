<?php

namespace App\Http\Controllers;

use App\Models\Detil_Harian;
use App\Models\MitraKerja;
use App\Models\Pekerja;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    function viewAbsensiMain(Request $request)
    {
        $absensi = Auth::user()->staff->picUnits;

        foreach ($absensi as $item) 
        {
            $unit = Unit::find($item->id_unit);
        }

        $totalAbsensi = Auth::user()
        ->units()
        ->count();

        $totalHadir = Detil_Harian::whereHas('absensi', function ($q) {
            $q->where('id_pic', Auth::user()->staff->id);
        })->count();

        $totalAbsen = Detil_Harian::where('status_kehadiran', 0)
            ->whereHas('absensi', fn ($q) =>
                $q->where('id_pic', Auth::user()->staff->id)
            )
            ->count();

        $today = Carbon::today();
        $limit = Carbon::today()->addDays(10);

        // $pekerjaDekatPenilaian = Pekerja::whereBetween('tgl_akhir_pkwt', [$today, $limit])
        //     ->whereHas('unit.picUnit', function ($q) {
        //         $q->where('id_pic', Auth::user()->staff->id);
        //     })
        //     ->count();
        
        // $pekerjaDekatPenilaian = Pekerja::whereBetween('tgl_akhir_pkwt', [$today, $limit])->get();
            

        // --- 2. BUILD QUERY ---
        $query = MitraKerja::query();

        // A. Filter by Search (Name, NIK, KPJ)
        // We check for 'search' (from new JS) or 'q' (fallback)
        $search = $request->input('search') ?? $request->input('q');

        $query->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('nama_mitra', 'LIKE', "%{$search}%"); // Ensure column name is 'no_kpj' or 'kpj' based on your DB
            });
        });

    
        // --- 3. FETCH DATA ---
        $mitraKerja = $query->orderBy('created_at', 'desc')
                        ->paginate(10)
                        ->withQueryString();

        $units = Unit::with('namaMitra') // 🔥 eager load
            ->whereHas('picUnit', function ($q) {
                $q->where('id_pic', Auth::user()->staff->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();


        // --- 4. RETURN RESPONSE ---

        // If AJAX request (from the search/filter script), return ONLY the table partial
        if ($request->ajax()) {
            return view('Absensi.partials.absensi-table', compact('mitraKerja'))->render();
        }
        
        // Otherwise return the full page
        return view('Absensi.main-absensi', compact('mitraKerja', 'totalAbsensi', 'totalHadir', 
        'totalAbsen', 'units'));
    }

    function KelolaAbsen()
    {
        //buat absen
        //buat detil absen masing-masing pekerja

        //footer > edit masing-masing detil pekerja
    }
}