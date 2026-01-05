<?php

namespace App\Http\Controllers;

use App\Models\Absensi;
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

    function ViewHarian(Request $request, $id_unit, $date)
    {
        $picId = Auth::user()->staff->id;

        // ambil unit + pekerjanya
        $unit = Unit::with('pkwt.pekerja')->findOrFail($id_unit);

        foreach ($unit->pkwt as $pkwt) {

            $sudahAda = Absensi::where('id_pekerja', $pkwt->id_pekerja)
                ->where('tgl_absensi', $date)
                ->exists();

            if ($sudahAda) {
                continue;
            }

            Absensi::create([
                'id_pekerja' => $pkwt->id_pekerja,
                'id_pic'     => $picId,
                'id_unit'    => $unit->id,
                'tgl_absensi'=> $date,
                'tipe'       => $unit->sistem_pengajian,
                'verifikasi' => 0,
            ]);
        }
    }

    function ViewBorongan(Request $request, $id_unit, $date)
    {
        $picId = Auth::user()->staff->id;

        // ambil unit + pekerjanya
        $unit = Unit::with('pkwt.pekerja')->findOrFail($id_unit);

        foreach ($unit->pkwt as $pkwt) {

            $sudahAda = Absensi::where('id_pekerja', $pkwt->id_pekerja)
                ->where('id_unit', $id_unit)
                ->where('tgl_absensi', $date)
                ->exists();

            if ($sudahAda) {
                continue;
            }

            Absensi::create([
                'id_pekerja' => $pkwt->id_pekerja,
                'id_pic'     => $picId,
                'id_unit'    => $unit->id,
                'tgl_absensi'=> $date,
                'tipe'       => $unit->sistem_pengajian,
                'verifikasi' => 0,
            ]);
        }
    }
}
