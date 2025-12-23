<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\Unit;
use Illuminate\Http\Request;
use App\Models\Staff;
use Illuminate\Database\QueryException;
use App\Models\MitraKerja;
use App\Models\Pekerja;
use Illuminate\Support\Facades\DB;

class UnitController extends Controller
{
    function viewUnitMain(Request $request)
    {
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
        $query = Unit::query()->with(['picUnit.staff', 'namaMitra']);

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
            return view('Unit.partials.unit-table', compact('unit'))->render();
        }

        // Otherwise return the full page
        return view('Unit.main-unit', compact('unit', 'totalUnit', 'unitBaru', 'tidakAktif'));
    }

    public function viewTambahUnit()
    {
        $picList = Staff::select('id as val', 'nama as label')->where('jabatan', 'PIC')->get();

        $mitraKerjaList = MitraKerja::select('id as val', 'nama_mitra as label')->get();

        return view('Unit.CRUD.tambah-unit', compact('picList', 'mitraKerjaList'));
    }

    function tambahUnit(Request $request)
    {
        // dd($request->all());
        try {
            $request->validate(
                [
                    'id_unit' => 'nullable|string|max:255',
                    'id_mitra_kerja' => 'required|string',
                    'mulai_perjanjian' => 'required|date',
                    'akhir_perjanjian' => 'required|date|after_or_equal:mulai_perjanjian',
                    'nama_unit' => 'required|string',
                    'dokumen_mou' => 'file|mimes:png,jpg,jpeg,pdf|max:2048',
                    'persentase_management_fee' => 'required|int',
                    'sistem_pengajian' => 'required|int',

                    'pic_ids' => 'required|array|min:1',
                    'pic_ids.*' => 'exists:staff,id',
                ],
                [
                    'id_unit.required' => 'ID Unit wajib diisi',
                    'id_mitra_kerja.required' => 'ID Mitra Kerja wajib diisi',

                    'mulai_perjanjian.required' => 'Tanggal mulai perjanjian wajib diisi',
                    'akhir_perjanjian.after_or_equal' => 'Tanggal akhir harus setelah tanggal mulai',

                    'nama_unit.required' => 'Nama unit wajib diisi',

                    'dokumen_mou.mimes' => 'Dokumen MOU harus berupa PDF atau gambar',
                    'dokumen_mou.max' => 'Ukuran dokumen maksimal 2MB',

                    'persentase_management_fee.required' => 'Persentase management fee wajib diisi',
                    'persentase_management_fee.max' => 'Persentase maksimal 100%',

                    'sistem_pengajian.required' => 'Sistem pengajian wajib dipilih',

                    'pic_ids.required' => 'PIC wajib dipilih minimal 1',
                ],
            );

            // dd($request->all());

            // ✅ Upload dokumen
            $dokumen = null;
            if ($request->hasFile('dokumen_mou')) {
                $dokumen = file_get_contents($request->file('dokumen_mou')->getRealPath());
            }

            // ✅ Simpan ke database
            $unit = Unit::create([
                'id_unit' => $request->id_unit,
                'id_mitra_kerja' => $request->id_mitra_kerja,
                'mulai_perjanjian' => $request->mulai_perjanjian,
                'akhir_perjanjian' => $request->akhir_perjanjian,
                'nama_unit' => $request->nama_unit,
                'persentase_management_fee' => $request->persentase_management_fee,
                'sistem_pengajian' => $request->sistem_pengajian,

                'dokumen_mou' => $dokumen,

                'status_aktif' => 1,
            ]);

            // dd($unit);
            $picIds = $request->pic_ids;

            foreach ($picIds as $picId) {
                DB::table('pic_unit')->insert([
                    'id_unit' => $unit->id, // atau $unit->id_unit
                    'id_pic' => $picId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            return redirect()
                ->route('view.tambah.unit')
                ->with('success', 'Data Unit ' . $unit->nama_mitra . ' berhasil ditambahkan.');
        } catch (QueryException $e) {
            // Tangani error database dan kirim ke front-end melalui session error
            return back()
                ->withInput()
                ->withErrors(['database' => $e->getMessage()]);
        } catch (\Exception $e) {
            // Tangani error umum dan kirim ke front-end melalui session error
            return back()
                ->withInput()
                ->withErrors(['general' => $e->getMessage()]);
        }
    }

    function viewDetailUnit($id)
    {
        $unit = Unit::with(['picUnit.staff', 'namaMitra'])->findOrFail($id);

        $historiUnit = History::where('foreign_id', $id)->where('nama_tabel', 'unit')->get();

        $pekerja = Pekerja::get();

        // dd($mitraKerja);

        return view('Unit.detail-unit', compact('unit', 'historiUnit', 'pekerja'));
    }

    function viewTambahUnitHarian($id_unit)
    {
        // Assuming you have Unit and Pekerja models
        $units = \App\Models\Unit::select('id', 'nama_unit as nama')->get();
        $unitSelected = Unit::with('namaMitra')
        ->where('id', $id_unit)
        ->firstOrFail();
        $pekerjaList = \App\Models\Pekerja::select('id', 'nama', 'nik')->where('status_aktif', 1)->get();


        return view('Unit.CRUD.tambah-unit-harian', compact('unitSelected', 'units', 'pekerjaList'));
    }

}
