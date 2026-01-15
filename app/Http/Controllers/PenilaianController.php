<?php

namespace App\Http\Controllers;

use App\Models\Pekerja;
use App\Models\Penilaian_Pkwt;
use App\Models\PKWT;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenilaianController extends Controller
{
    public function viewPenilaianMain(Request $request, $id_unit)
    {
        $unit = Unit::findOrFail($id_unit);
        $query = PKWT::with(['pekerja'])->where('id_unit', $id_unit);

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('pekerja', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status_aktif', $request->status);
        }

        $pkwtPekerja = $query->latest()->paginate(25);

        if ($request->ajax()) {
            return view('Penilaian.partials.main-penilaian-table', compact('pkwtPekerja', 'unit'))->render();
        }

        return view('Penilaian.main-penilaian', compact('pkwtPekerja', 'unit'));
    }

    function viewBuatPenilaian(Request $request, $id_unit)
    {
        // 1. Ambil ID dari URL
        $ids = explode(',', $request->query('ids'));

        // 2. Query dengan VALIDASI GANDA: ID harus ada di list DAN id_unit harus cocok
        $pkwtList = PKWT::with('pekerja')
            ->whereIn('id', $ids)
            ->where('id_unit', $id_unit) // KUNCI KEAMANAN: Harus dalam unit yang sama
            ->get();

        // 3. PROTEKSI: Jika jumlah data yang didapat tidak sama dengan jumlah ID yang diminta,
        // berarti ada ID ilegal atau ID dari unit lain.
        if ($pkwtList->count() !== count($ids)) {
            return redirect()->back()->with('error', 'Akses ditolak. Beberapa pekerja bukan bagian dari unit ini.');
        }

        // Data tambahan untuk dropdown (jika masih diperlukan)
        $unitSelected = Unit::with('namaMitra')->findOrFail($id_unit);
        $pekerjaList = Pekerja::select('id', 'nama', 'nik')->get();

        return view('Penilaian.CRUD.tambah-penilaian', compact('unitSelected', 'pkwtList', 'pekerjaList'));
    }

    public function buatPenilaian(Request $request)
    {
        $request->validate([
            'id_unit' => 'required|exists:unit,id',
            'pekerja' => 'required|array|min:1',
            'pekerja.*.id_pekerja' => 'required|exists:pekerja,id',
        ]);

        try {
            DB::transaction(function () use ($request) {

                foreach ($request->pekerja as $data) {

                    $pkwt = PKWT::where('id_pekerja', $data['id_pekerja'])
                        ->where('id_unit', $request->id_unit)
                        ->where('status_aktif', 1)
                        ->first();

                    if (!$pkwt) {
                        throw new \DomainException(
                            'PKWT aktif tidak ditemukan untuk pekerja ID: ' . $data['id_pekerja']
                        );
                    }

                    Penilaian_Pkwt::updateOrCreate(
                        [
                            'id_pekerja' => $data['id_pekerja'],
                            'id_unit' => $request->id_unit,
                        ],
                        [
                            'mk' => $data['mk'],
                            'absensi' => $data['absensi'],
                            'pengetahuan' => $data['pengetahuan'],
                            'kualitas' => $data['kualitas'],
                            'sikap' => $data['sikap'],
                            'total' => $data['total_skor'],
                            'status_staff' => 0,
                            'status_hrd' => 0,
                            'status_aktif' => 1,
                            'keterangan' => $data['keterangan'],
                            'updated_by' => Auth::user()->staff->id,
                            'created_by' => Auth::user()->staff->id,
                        ]
                    );
                }
            });

            return redirect()
                ->route('view.penilaian', $request->id_unit)
                ->with('success', 'Penilaian berhasil disimpan.');

        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

}
