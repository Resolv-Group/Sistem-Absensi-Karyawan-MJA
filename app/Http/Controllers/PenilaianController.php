<?php

namespace App\Http\Controllers;

use App\Exports\PenilaianExport;
use App\Models\MitraKerja;
use App\Models\Pekerja;
use App\Models\Penilaian_Pkwt;
use App\Models\PKWT;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class PenilaianController extends Controller
{
    //Ambil periode PKWT hampir habis → filter → pagination → tandai yang sudah dinilai → tampilkan (AJAX / full view)
    public function viewPenilaianMain(Request $request, $id_unit)
    {
        $unit = Unit::findOrFail($id_unit);

        $today = Carbon::today();
        $oneMonthLater = Carbon::today()->addDays(30);

        $query = PKWT::with([
            'pekerja',
            'penilaian' => function ($q) {
                $q->latest();
            },
        ])
            ->withCount([
                'penilaian as penilaian_count' => function ($q) use ($id_unit) {
                    $q->where('id_unit', $id_unit);
                },
            ])
            ->where('id_unit', $id_unit)

            // 🔥 PKWT AKAN HABIS ≤ 1 BULAN
            ->whereDate('tgl_akhir_pkwt', '>=', $today)
            ->whereDate('tgl_akhir_pkwt', '<=', $oneMonthLater)

            // 🔝 PRIORITAS: yang BELUM DINILAI
            ->orderBy('penilaian_count', 'asc') // 0 dulu, baru >0
            ->latest();

        // 🔍 Filter Search
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

        //buat munculin button buat penilaian
        $alreadyAssessedIds = PKWT::where('id_unit', $id_unit)
            ->whereHas('penilaian', function ($q) use ($id_unit) {
                $q->where('id_unit', $id_unit);
            })
            ->pluck('id')
            ->toArray();

        if ($request->ajax()) {
            return view('Penilaian.partials.main-penilaian-table', compact('pkwtPekerja', 'unit', 'alreadyAssessedIds'))->render();
        }

        return view('Penilaian.main-penilaian', compact('pkwtPekerja', 'unit', 'alreadyAssessedIds'));
    }

    //Ambil ID → validasi unit → cek manipulasi URL → ambil data pendukung → tampilkan form penilaian
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

    //Validasi → transaksi → cek PKWT aktif → simpan penilaian → commit / rollback
    public function buatPenilaian(Request $request)
    {
        // 1. Buat Validator
        $validator = Validator::make(
            $request->all(),
            [
                'id_unit' => 'required|exists:unit,id',
                'pekerja' => 'required|array|min:1',
                'pekerja.*.id_pekerja' => 'required|exists:pekerja,id',
                'pekerja.*.mk' => 'required|numeric',
                'pekerja.*.absensi' => 'required|numeric',
                'pekerja.*.pengetahuan' => 'required|numeric',
                'pekerja.*.kualitas' => 'required|numeric',
                'pekerja.*.sikap' => 'required|numeric',
                'pekerja.*.total_skor' => 'required|numeric',
            ],
            [
                // Custom Messages
                'required' => ':attribute wajib diisi!',
                'numeric' => ':attribute harus berupa angka!',
                'min' => 'Minimal harus ada :min pekerja yang dinilai.',
            ],
            [
                // Custom Attribute Names (Agar pesan error rapi)
                'pekerja.*.mk' => 'Masa Kerja (MK)',
                'pekerja.*.absensi' => 'Poin Absensi',
                'pekerja.*.pengetahuan' => 'Poin Pengetahuan',
                'pekerja.*.kualitas' => 'Poin Kualitas',
                'pekerja.*.sikap' => 'Poin Sikap',
            ],
        );

        // 2. Cek jika Validasi Gagal
        if ($validator->fails()) {
            // Ambil pesan error pertama saja agar notifikasi tetap simplistic
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::transaction(function () use ($request) {
                foreach ($request->pekerja as $data) {
                    // Pastikan PKWT aktif ada
                    $pkwt = PKWT::where('id_pekerja', $data['id_pekerja'])->where('id_unit', $request->id_unit)->where('status_aktif', 1)->first();

                    if (!$pkwt) {
                        throw new \DomainException('PKWT aktif tidak ditemukan untuk pekerja ID: ' . $data['id_pekerja']);
                    }

                    // Simpan penilaian (always CREATE)
                    Penilaian_Pkwt::create([
                        'id_pekerja' => $data['id_pekerja'],
                        'id_unit' => $request->id_unit,

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
                        'created_by' => Auth::user()->staff->id,
                        'updated_by' => Auth::user()->staff->id,
                    ]);
                }
            });

            return redirect()->route('view.penilaian', $request->id_unit)->with('success', 'Penilaian berhasil disimpan.');
        } catch (\Throwable $e) {
            report($e);

            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function ExportExcel(Request $request, $id_unit)
    {
        $ids = json_decode($request->worker_ids, true);

        $pkwtList = PKWT::with('pekerja')
            ->whereIn('id', $ids)
            ->where('id_unit', $id_unit) // KUNCI KEAMANAN: Harus dalam unit yang sama
            ->get();

        $idPekerja = $pkwtList->pluck('id_pekerja')->unique()->toArray();

        $data = Penilaian_Pkwt::with(['pekerja:id,nama,tgl_lahir'])
            ->where('id_unit', $id_unit)
            ->whereIn('id_pekerja', $idPekerja)
            ->where('status_aktif', 1)
            ->get();

        $unit = Unit::select('id', 'nama_unit', 'id_mitra_kerja')->where('id', $id_unit)->first();

        $divisi = MitraKerja::with('bidangUsaha:id,nama')->where('id', $unit->id_mitra_kerja)->first()->bidangUsaha->nama ?? '-';

        return Excel::download(new PenilaianExport($data, $unit, $divisi), 'PPK_Karyawan.xlsx');
    }

    //Validasi penilaian → ambil PKWT aktif → ambil unit → tampilkan form ubah
    public function viewUbahPenilaian(Request $request, $id_penilaian, $id_unit, $id_pekerja)
    {
        // 1. Ambil data PENILAIAN yang akan diubah
        $penilaian = Penilaian_Pkwt::with('pekerja')->where('id', $id_penilaian)->where('id_unit', $id_unit)->where('id_pekerja', $id_pekerja)->firstOrFail();

        // 2. Ambil data PKWT aktif (untuk mendapatkan NIK & Nama terbaru)
        // Gunakan first() karena kita hanya butuh satu identitas pekerja
        $pkwt = PKWT::where('id_pekerja', $id_pekerja)->where('id_unit', $id_unit)->where('status_aktif', 1)->first();

        // 3. Data unit
        $unitSelected = Unit::with('namaMitra')->findOrFail($id_unit);

        return view('Penilaian.CRUD.ubah-penilaian', compact('penilaian', 'pkwt', 'unitSelected'));
    }

    //Validasi → transaksi → cek penilaian → update data → reset status → commit / rollback
    function ubahPenilaian(Request $request, $id_penilaian, $id_unit, $id_pekerja)
    {
        // 1. Validasi Input
        $validator = Validator::make(
            $request->all(),
            [
                'pekerja.0.mk' => 'required|numeric',
                'pekerja.0.absensi' => 'required|numeric',
                'pekerja.0.pengetahuan' => 'required|numeric',
                'pekerja.0.kualitas' => 'required|numeric',
                'pekerja.0.sikap' => 'required|numeric',
            ],
            [
                'pekerja.0.mk.required' => 'Masa Kerja (MK) harus diisi!',
                'pekerja.0.absensi.required' => 'Poin absensi harus diisi!',
                'pekerja.0.pengetahuan.required' => 'Poin pengetahuan harus diisi!',
                'pekerja.0.kualitas.required' => 'Poin kualitas harus diisi!',
                'pekerja.0.sikap.required' => 'Poin sikap harus diisi!',
            ],
        );

        if ($validator->fails()) {
            return back()
                ->withErrors($validator) // Tetap kirim error bag untuk x-model/old input
                ->withInput(); // Ambil pesan error PERTAMA untuk notifikasi
        }

        DB::beginTransaction();

        try {
            // 2. Ambil data dari array pekerja indeks 0 (berdasarkan dd Anda)
            $data = $request->pekerja[0];

            // 3. Cari data penilaian yang dimaksud
            // Kita gunakan triple check (ID, Unit, dan Pekerja) untuk keamanan (Security)
            $penilaian = Penilaian_Pkwt::where('id', $id_penilaian)->where('id_unit', $id_unit)->where('id_pekerja', $id_pekerja)->firstOrFail();

            // 4. Proses Update
            $penilaian->update([
                'mk' => $data['mk'],
                'absensi' => $data['absensi'],
                'pengetahuan' => $data['pengetahuan'],
                'kualitas' => $data['kualitas'],
                'sikap' => $data['sikap'],
                'total' => $data['total_skor'],
                'keterangan' => $data['keterangan'],

                // Audit Trail
                'updated_by' => Auth::user()->staff->id ?? Auth::id(),
                'updated_at' => now(),

                // Reset status verifikasi jika data diubah (Opsional, agar HRD meninjau ulang)
                'status_hrd' => 0,
                'status_staff' => 0,
            ]);

            DB::commit();

            return redirect()->route('view.penilaian', $id_unit)->with('success', 'Data penilaian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
