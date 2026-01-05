<?php

namespace App\Http\Controllers;

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

class AbsensiController extends Controller
{
    public function viewAbsensiMain(Request $request)
    {
        $user = Auth::user();
        $staff = $user->staff;

        // 🔥 1. Ambil tanggal (default: hari ini)
        $date = $request->date ?? now()->toDateString();

        // 🔥 2. Unit yang dipegang PIC
        $units = Unit::with(['namaMitra'])
            ->withCount('pkwtPekerja')
            ->whereHas('picUnit', function ($q) use ($staff) {
                $q->where('id_pic', $staff->id);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // 🔥 3. TOTAL ABSENSI (jumlah unit PIC)
        $totalAbsensi = $units->total();

        // 🔥 4. TOTAL HADIR (detil_harian dari absensi PIC + tanggal)
        $totalHadir = Detil_Harian::whereHas('absensi', function ($q) use ($staff, $date) {
                $q->where('id_pic', $staff->id)
                ->whereDate('tgl_absensi', $date);
            })
            ->where('status_kehadiran', 1)
            ->count();

        // 🔥 5. TOTAL ABSEN
        $totalAbsen = Detil_Harian::whereHas('absensi', function ($q) use ($staff, $date) {
                $q->where('id_pic', $staff->id)
                ->whereDate('tgl_absensi', $date);
            })
            ->where('status_kehadiran', 0)
            ->count();

        // 🔥 6. Mitra Kerja (search tetap jalan)
        $query = MitraKerja::query();

        $search = $request->input('search') ?? $request->input('q');

        $query->when($search, function ($q) use ($search) {
            $q->where('nama_mitra', 'LIKE', "%{$search}%");
        });

        $mitraKerja = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // 🔥 7. AJAX support
        if ($request->ajax()) {
            return view('Absensi.partials.absensi-table', compact('mitraKerja'))->render();
        }

        // 🔥 8. Return view
        return view('Absensi.main-absensi', compact(
            'mitraKerja',
            'totalAbsensi',
            'totalHadir',
            'totalAbsen',
            'units',
            'date'
        ));
    }

    function ViewHarian(Request $request, $id_unit)
    {
        $unit = Unit::findOrFail($id_unit);
        $query = PKWT::with(['pekerja', 'jabatan', 'divisi'])->where('id_unit', $id_unit);

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pekerja', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%");
            });
        }
        if ($request->filled('divisi')) $query->where('divisi_id', $request->divisi);
        if ($request->filled('jabatan')) $query->where('jabatan_pkwt_id', $request->jabatan);
        if ($request->filled('status')) $query->where('status_aktif', $request->status);

        $pkwtPekerja = $query->latest()->paginate(2);

        // Data untuk Filter Dropdown
        $divisions = Divisi::all();
        $jabatan = JabatanPKWT::all();

        if ($request->ajax()) {
            return view('Unit.partials.main-harian-table', compact('pkwtPekerja', 'unit'))->render();
        }

        return view('Unit.Pengajian.main-harian', compact('pkwtPekerja', 'unit', 'divisions', 'jabatan'));
    }

        function ViewBorongan(Request $request, $id_unit)
    {
        $unit = Unit::findOrFail($id_unit);
        $query = PKWT::with(['pekerja', 'jabatan', 'divisi'])->where('id_unit', $id_unit);

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pekerja', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%");
            });
        }
        if ($request->filled('divisi')) $query->where('divisi_id', $request->divisi);
        if ($request->filled('jabatan')) $query->where('jabatan_pkwt_id', $request->jabatan);
        if ($request->filled('status')) $query->where('status_aktif', $request->status);

        $pkwtPekerja = $query->latest()->paginate(2);

        // Data untuk Filter Dropdown
        $divisions = Divisi::all();
        $jabatan = JabatanPKWT::all();

        if ($request->ajax()) {
            return view('Unit.partials.main-harian-table', compact('pkwtPekerja', 'unit'))->render();
        }

        return view('Unit.Pengajian.main-harian', compact('pkwtPekerja', 'unit', 'divisions', 'jabatan'));
    }
}
