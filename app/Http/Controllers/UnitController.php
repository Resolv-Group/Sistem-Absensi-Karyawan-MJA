<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Borongan;
use App\Models\Divisi;
use App\Models\History;
use App\Models\JabatanPKWT;
use App\Models\Kas_Kecil;
use App\Models\Kategori;
use App\Models\MitraKerja;
use App\Models\Pekerja;
use App\Models\PicUnit;
use App\Models\PKWT;
use App\Models\Staff;
use App\Models\Unit;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Exports\KasKecilExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssetExport;
use App\Exports\PekerjaUnitExport;
use App\Exports\BoronganUnitExport;
use App\Exports\MonitoringPkwtExport;
use App\Exports\PerputaranPekerjaExport;

class UnitController extends Controller
{
    /**
     * Terjemahkan error code MySQL ke pesan ramah pengguna.
     */
    private function translateDbError(QueryException $e): string
    {
        $errorCode = $e->errorInfo[1] ?? null;

        return match ($errorCode) {
            1062 => 'Data yang Anda masukkan sudah ada di sistem. Mohon periksa kembali data yang bersifat unik(tidak boleh sama).',
            1452 => 'Data referensi tidak valid. Pastikan data terkait masih terdaftar di sistem.',
            1048 => 'Terdapat kolom wajib yang belum diisi. Mohon lengkapi seluruh data yang diperlukan.',
            1406 => 'Data yang dimasukkan terlalu panjang. Mohon persingkat input Anda.',
            1264 => 'Nilai angka yang dimasukkan di luar batas yang diizinkan. Mohon periksa kembali.',
            default => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi atau hubungi administrator.',
        };
    }
    public function viewUnitMain(Request $request)
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
            return view('Unit.partials.unit-table', compact('unit'))->render();
        }

        // Otherwise return the full page
        return view('Unit.main-unit', compact('unit', 'totalUnit', 'unitBaru', 'totalHarian', 'totalBorongan', 'tidakAktif'));
    }

    public function viewKasKecilAssetMain(Request $request)
    {
        // --- 1. CALCULATE STATS (Top Cards) ---
        $totalUnit = Unit::count();
        $activeKas = \App\Models\Kas_Kecil::where('status', '!=', 0);
        $totalKasCount = $activeKas->count();
        $grandTotalKasValue = $activeKas->sum('debit') - $activeKas->sum('kredit');

        $activeAsset = \App\Models\Asset::where('status', '!=', 0);
        $totalAssetCount = $activeAsset->count();
        $grandTotalAssetValue = $activeAsset->get()->sum(function ($a) {
            return $a->jumlah * $a->harga_perolehan;
        });

        // --- 2. BUILD QUERY ---
        $query = Unit::query()
            ->with(['picUnit.staff', 'namaMitra', 'kasKecil', 'asset']);

        $search = $request->input('search') ?? $request->input('q');

        $query->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('nama_unit', 'LIKE', "%{$search}%");
            });
        });

        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status_aktif', $request->status);
        });

        $unit = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        if ($request->ajax()) {
            return view('Unit.partials.kas-asset-table', compact('unit'))->render();
        }

        return view('Unit.main-kas-asset', compact('unit', 'totalUnit', 'totalKasCount', 'grandTotalKasValue', 'totalAssetCount', 'grandTotalAssetValue'));
    }

    public function viewTambahUnit()
    {
        $picList = Staff::select('id as val', 'nama as label')->where('jabatan', 'PIC')->get();

        $mitraKerjaList = MitraKerja::select('id as val', 'nama_mitra as label')->get();

        return view('Unit.CRUD.tambah-unit', compact('picList', 'mitraKerjaList'));
    }

    public function tambahUnit(Request $request)
    {
        try {
            $request->validate(
                [
                    'id_unit' => 'nullable|string|max:255',
                    'id_mitra_kerja' => 'required|string',
                    'mulai_perjanjian' => 'required|date',
                    'akhir_perjanjian' => 'required|date|after_or_equal:mulai_perjanjian',
                    'nama_unit' => 'required|string',
                    'dokumen_mou' => 'file|mimes:png,jpg,jpeg,pdf|max:2048',
                    'persentase_management_fee' => 'nullable|numeric|min:0|max:100',
                    'umk' => 'nullable|numeric|min:0',
                    'bpjs_kesehatan' => 'nullable|numeric|min:0',
                    'bpjs_naker' => 'nullable|numeric|min:0',
                    'sistem_pengajian' => 'required|int',

                    'pic_ids' => 'required|array|min:1',
                    'pic_ids.*' => 'exists:staff,id',
                    'tunjangan' => 'nullable|json',
                ],
                [
                    'id_unit.required' => 'ID Unit wajib diisi',
                    'id_mitra_kerja.required' => 'ID Mitra Kerja wajib diisi',

                    'mulai_perjanjian.required' => 'Tanggal mulai perjanjian wajib diisi',
                    'akhir_perjanjian.after_or_equal' => 'Tanggal akhir harus setelah tanggal mulai',
                    'akhir_perjanjian.required' => 'Tanggal akhir perjanjian wajib diisi',

                    'nama_unit.required' => 'Nama unit wajib diisi',

                    'dokumen_mou.mimes' => 'Dokumen MOU harus berupa PDF atau gambar',
                    'dokumen_mou.max' => 'Ukuran dokumen maksimal 2MB',

                    'persentase_management_fee.required' => 'Persentase management fee wajib diisi',
                    'persentase_management_fee.max' => 'Persentase maksimal 100%',

                    'umk.required' => 'Umk wajib diisi',
                    'bpjs_kesehatan.required' => 'BPJS Kesehatan fee wajib diisi',
                    'bpjs_naker.required' => 'BPJS Naker fee wajib diisi',

                    'sistem_pengajian.required' => 'Sistem pengajian wajib dipilih',

                    'pic_ids.required' => 'PIC wajib dipilih minimal 1',
                    'tunjangan.json' => 'Format data tunjangan tidak valid.',
                ],
            );

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
                'bpjs_kesehatan' => $request->bpjs_kesehatan,
                'bpjs_naker' => $request->bpjs_naker,
                'umk' => $request->umk,
                'sistem_pengajian' => $request->sistem_pengajian,

                'dokumen_mou' => $dokumen,

                'status_aktif' => 1,
                'tunjangan' => json_decode($request->tunjangan, true),
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
                ->with('success', 'Data Unit '.$unit->nama_mitra.' berhasil ditambahkan.');
        } catch (QueryException $e) {
            // Log error asli untuk debugging di server
            \Log::error('TambahUnit DB Error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['database' => $this->translateDbError($e)]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            // Log error asli untuk debugging di server
            \Log::error('TambahUnit General Error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['general' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.']);
        }
    }

    public function viewDetailUnit(Request $request, $id)
    {

        $user = auth()->user(); // staff login

        // CEK PIC PUNYA UNIT INI ATAU TIDAK
        $isAllowed = Unit::where('id', $id)
            ->whereHas('picUnit', function ($q) use ($user) {
                // Ganti $user->id menjadi $user->staff_id
                $q->where('id_pic', $user->staff_id); 
            })
            ->exists();

        if (in_array($user->role, ['admin', 'hrd', 'akuntan'])) {
        } elseif (! $isAllowed) {
            abort(403, 'Anda tidak memiliki akses ke unit ini');
        }

        $unit = Unit::with(['picUnit.staff', 'namaMitra'])->findOrFail($id);

        if ($request->ajax()) {
            // --- HANDLE BORONGAN AJAX ---
            if ($request->target === 'borongan') {
                // 1. Lock the preview set to the latest 5 IDs only
                $latestIds = Borongan::where('id_unit', $id)->latest()->limit(5)->pluck('id');

                // 2. Start query strictly within those IDs
                $query = Borongan::with('kategoriRel')->whereIn('id', $latestIds);

                // 3. Apply search filters INSIDE the boundary
                if ($request->filled('search')) {
                    $query->where('nama_item', 'like', "%{$request->search}%");
                }
                if ($request->filled('kategori')) {
                    $query->where('kategori', $request->kategori);
                }
                if ($request->filled('status')) {
                    $query->where('status_aktif', $request->status);
                }

                $borongan = $query->get();

                return view('Unit.partials.borongan-table', compact('borongan', 'unit'))->render();
            }

            // --- HANDLE HARIAN (PKWT) AJAX ---
            // 1. Lock the preview set to the latest 5 IDs only
            $latestIds = PKWT::where('id_unit', $id)->latest()->limit(5)->pluck('id');

            // 2. Start query strictly within those IDs
            $query = PKWT::with(['pekerja', 'jabatan', 'divisi'])->whereIn('id', $latestIds);

            // 3. Apply search filters INSIDE the boundary
            if ($request->filled('search')) {
                $search = $request->search;
                // We use a nested where function so the 'OR' doesn't break the 'whereIn'
                $query->where(function ($q) use ($search) {
                    $q->whereHas('pekerja', function ($sq) use ($search) {
                        $sq->where('nama', 'like', "%{$search}%")->orWhere('nik', 'like', "%{$search}%");
                    });
                });
            }

            if ($request->filled('divisi')) {
                $query->where('divisi_id', $request->divisi);
            }
            if ($request->filled('jabatan')) {
                $query->where('jabatan_id', $request->jabatan);
            }
            if ($request->filled('status')) {
                $query->where('status_aktif', $request->status);
            }

            $pkwtPekerja = $query->get();

            return view('Unit.partials.harian-table', compact('pkwtPekerja', 'unit'))->render();
        }

        // --- NORMAL PAGE LOAD (Latest 5 only) ---
        $historiUnit = History::where('foreign_id', $id)->where('nama_tabel', 'unit')->get();
        $pekerja = Pekerja::all();

        $pkwtPekerja = PKWT::with(['pekerja', 'jabatan', 'divisi'])
            ->where('id_unit', $id)
            ->latest()
            ->limit(5)
            ->get();

        // dd($pkwtPekerja);

        $borongan = Borongan::with('kategoriRel')->where('id_unit', $id)->latest()->limit(5)->get();

        $divisions = Divisi::all();
        $boronganKategori = Kategori::all();
        $jabatan = JabatanPKWT::all();

        $kasKecil = Kas_Kecil::where('id_unit', $id)->whereIn('status', [1, 2])->orderBy('tanggal', 'desc')->get();
        $kasIds = $kasKecil->pluck('id')->toArray();

        $assets = Asset::where('id_unit', $id)->whereIn('status', [1, 2])->orderBy('tahun_perolehan', 'desc')->get();
        $assetIds = $assets->pluck('id')->toArray();

        return view('Unit.detail-unit', compact('unit', 'historiUnit', 'pekerja', 'pkwtPekerja', 'borongan', 'divisions', 'boronganKategori', 'jabatan', 'kasKecil', 'kasIds', 'assets', 'assetIds'));
    }

    public function showDokumenMOU($id, Request $request)
    {
        // 1. Find the record
        $data = Unit::findOrFail($id);

        // 2. Check if blob exists
        if (! $data->dokumen_mou) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        // 3. Detect the MIME type (PDF, JPG, PNG) from the binary data
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($data->dokumen_mou);

        // 4. Determine if it's "View" (inline) or "Download" (attachment)
        // If URL has ?download=true, we force download.
        $disposition = $request->has('download') ? 'attachment' : 'inline';

        // Generate a filename
        $filename = 'dokumen-mitra-'.$id;

        // 5. Return the binary data as a proper HTTP response
        return response($data->dokumen_mou)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', $disposition.'; filename="'.$filename.'"');
    }

    public function showDokumenPKWT($id, Request $request)
    {
        $data = PKWT::findOrFail($id);

        if (! $data->dokumen_pkwt) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        // PAKAI MIME YANG DISIMPAN (LEBIH CEPAT & AMAN)
        $mimeType = $data->dokumen_mime ?? 'application/octet-stream';

        $disposition = $request->has('download') ? 'attachment' : 'inline';

        $filename = 'dokumen-pkwt-'.$id;

        return response($data->dokumen_pkwt)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', $disposition.'; filename="'.$filename.'"');
    }

    public function ubahUnit(Request $request, $id)
    {
        $user = auth()->user(); // staff login

        // CEK PIC PUNYA UNIT INI ATAU TIDAK
        // $isAllowed = Unit::where('id', $id)
        //     ->whereHas('picUnit', function ($q) use ($user) {
        //         $q->where('id_pic', $user->id);
        //     })
        //     ->exists();

        $isAllowed = Unit::where('id', $id)
            ->whereHas('picUnit', function ($q) use ($user) {
                // Ganti $user->id menjadi $user->staff_id
                $q->where('id_pic', $user->staff_id); 
            })
            ->exists();

        if (in_array($user->role, ['admin', 'hrd'])) {
        } elseif (! $isAllowed) {
            abort(403, 'Anda tidak memiliki akses ke unit ini');
        }

        $unit = Unit::findOrFail($id);

        $mitraKerjaList = MitraKerja::select('id as val', 'nama_mitra as label')->get();

        $selectedPicIds = $unit->picUnit?->pluck('id_pic')->toArray() ?? [];

        $picList = Staff::select('id as val', 'nama as label')->where('jabatan', 'PIC')->get();

        return view('Unit.CRUD.ubah-unit', compact('unit', 'mitraKerjaList', 'selectedPicIds', 'picList'));
    }

    public function updateUnit(Request $request, $id)
    {
        // dd($request->all());
        try {
            DB::beginTransaction();

            // ===============================
            // VALIDATION
            // ===============================
            $validated = $request->validate(
                [
                    'id_mitra_kerja' => 'required|exists:mitra_kerja,id',
                    'nama_unit' => 'required|string|max:255',
                    'sistem_pengajian' => 'required|in:1,2',
                    'persentase_management_fee' => 'nullable|numeric|min:0|max:100',
                    'umk' => 'nullable|numeric|min:0',
                    'bpjs_kesehatan' => 'nullable|numeric|min:0',
                    'bpjs_naker' => 'nullable|numeric|min:0',

                    'mulai_perjanjian' => 'required|date',
                    'akhir_perjanjian' => 'required|date|after_or_equal:mulai_perjanjian',

                    'pic_ids' => 'required|array|min:1',
                    'pic_ids.*' => 'exists:staff,id',

                    'tunjangan' => 'nullable|json',

                    // FILE OPTIONAL
                    'dokumen_mou' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:2048',
                ],
                [
                    'id_mitra_kerja.required' => 'Mitra kerja wajib dipilih.',
                    'nama_unit.required' => 'Nama unit wajib diisi.',
                    'pic_ids.required' => 'Minimal 1 PIC harus dipilih.',
                    'umk.required' => 'Umk wajib diisi',

                    'mulai_perjanjian.required' => 'Tanggal mulai perjanjian wajib diisi',
                    'akhir_perjanjian.after_or_equal' => 'Tanggal akhir harus setelah tanggal mulai',
                    'akhir_perjanjian.required' => 'Tanggal akhir perjanjian wajib diisi',

                    'dokumen_mou.mimes' => 'Dokumen harus PDF / JPG / PNG.',
                    'dokumen_mou.max' => 'Ukuran dokumen maksimal 2MB.',
                    'tunjangan.required' => 'Tunjangan wajib diisi.',
                    'tunjangan.json' => 'Format tunjangan tidak valid.',
                    'mulai_perjanjian.after_or_equal' => 'Tanggal mulai perjanjian tidak boleh setelah tanggal akhir perjanjian.',
                    'akhir_perjanjian.after_or_equal' => 'Tanggal akhir perjanjian tidak boleh sebelum tanggal mulai perjanjian.',
                ],
            );

            // ===============================
            // FIND UNIT
            // ===============================
            $unit = Unit::findOrFail($id);

            // ===============================
            // HANDLE FILE (OPTIONAL)
            // ===============================
            if ($request->hasFile('dokumen_mou')) {
                $unit->update([
                    'dokumen_mou' => file_get_contents($request->file('dokumen_mou')->getRealPath()),
                ]);
            }

            // ===============================
            // UPDATE UNIT DATA
            // ===============================
            $unit->update([
                'id_mitra_kerja' => $validated['id_mitra_kerja'],
                'nama_unit' => $validated['nama_unit'],
                'sistem_pengajian' => $validated['sistem_pengajian'],
                'persentase_management_fee' => $validated['persentase_management_fee'],
                'bpjs_kesehatan' => $validated['bpjs_kesehatan'],
                'bpjs_naker' => $validated['bpjs_naker'],
                'umk' => $validated['umk'],
                'mulai_perjanjian' => $validated['mulai_perjanjian'],
                'akhir_perjanjian' => $validated['akhir_perjanjian'],
                'tunjangan' => json_decode($validated['tunjangan'], true),
            ]);

            // ===============================
            // SYNC PIC
            // ===============================
            // Hapus PIC lama
            PicUnit::where('id_unit', $unit->id)->delete();

            // Insert PIC baru
            foreach ($validated['pic_ids'] as $picId) {
                PicUnit::create([
                    'id_unit' => $unit->id,
                    'id_pic' => $picId,
                ]);
            }

            // dd($unit);
            DB::commit();

            return redirect()->route('view.detail.unit', $unit->id)->with('success', 'Data unit berhasil diperbarui.');
        } catch (QueryException $e) {
            DB::rollBack();
            \Log::error('UpdateUnit DB Error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors([
                    'database' => $this->translateDbError($e),
                ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('UpdateUnit General Error: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors([
                    'general' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.',
                ]);
        }
    }

    public function toggleStatus($id)
    {
        $unit = Unit::findOrFail($id);

        $unit->status_aktif = ! $unit->status_aktif;
        $unit->save();

        return response()->json([
            'message' => $unit->status_aktif ? 'Unit berhasil diaktifkan' : 'Unit berhasil dinonaktifkan',
        ]);
    }

    public function storeBulkKas(Request $request, $id)
    {
        try {
            $dataEntries = $request->input('kas');

            if (! $dataEntries || ! is_array($dataEntries)) {
                return back()->with('error', 'Tidak ada data untuk disimpan.');
            }

            $savedCount = 0; // Better to count what is actually saved

            foreach ($dataEntries as $index => $entry) {
                // FIX: Match the key 'ket' from your dd()
                if (empty($entry['tgl']) || empty($entry['ket'])) {
                    continue;
                }

                $filePath = null;
                if ($request->hasFile("kas.$index.nota")) {
                    $file = $request->file("kas.$index.nota");

                    $filePath = file_get_contents($file->getRealPath());
                }

                $userRole = strtolower(trim(auth()->user()->role ?? ''));
                $initialStatus = in_array($userRole, ['hrd', 'admin', 'superadmin']) ? 2 : 1;

                // Use the correct keys here too
                Kas_Kecil::create([
                    'id_unit' => $id,
                    'akun' => $entry['akun'],
                    'tanggal' => $entry['tgl'],
                    'keterangan' => $entry['ket'],   // Match 'ket' from form
                    'debit' => $entry['debit'],
                    'kredit' => $entry['kredit'],
                    'nota' => $filePath,
                    'status' => $initialStatus,
                ]);

                $savedCount++;
            }

            if ($savedCount === 0) {
                return back()->with('error', 'Gagal menyimpan data. Pastikan semua kolom terisi.');
            }

            return back()->with('success', "Berhasil menyimpan $savedCount transaksi.");
        } catch (QueryException $e) {
            \Log::error('StoreBulkKas DB Error: ' . $e->getMessage());
            return back()->with('error', $this->translateDbError($e));
        } catch (\Exception $e) {
            \Log::error('StoreBulkKas General Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function updateBulkKas(Request $request, $id_unit)
    {
        try {
            // dump($request->all());
            $dataEntries = $request->input('kas');
            // dd($dataEntries);

            if (! $dataEntries || ! is_array($dataEntries)) {
                return back()->with('error', 'Tidak ada data untuk diperbarui.');
            }

            foreach ($dataEntries as $index => $entry) {
                // Find the specific record by the ID sent in the hidden input
                $kas = Kas_Kecil::where('id', $entry['id'])
                    ->where('id_unit', $id_unit)
                    ->first();

                if ($kas && $kas->status != 2) {
                    $updateData = [
                        'tanggal' => $entry['tgl'],
                        'akun' => $entry['akun'],
                        'keterangan' => $entry['ket'],
                        'debit' => $entry['debit'],
                        'kredit' => $entry['kredit'],
                        'updated_at' => now(),
                    ];

                    // Handle file upload only if a new file is selected
                    if ($request->hasFile("kas.$index.nota")) {
                        $file = $request->file("kas.$index.nota");
                        $updateData['nota'] = file_get_contents($file->getRealPath());
                    }

                    $kas->update($updateData);
                }
            }

            return back()->with('success', 'Berhasil memperbarui '.count($dataEntries).' transaksi.');
        } catch (QueryException $e) {
            \Log::error('UpdateBulkKas DB Error: ' . $e->getMessage());
            return back()->with('error', $this->translateDbError($e));
        } catch (\Exception $e) {
            \Log::error('UpdateBulkKas General Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function showKasNota($id, Request $request)
    {
        $data = Kas_Kecil::findOrFail($id);

        if (! $data->nota) {
            abort(404, 'Nota tidak ditemukan.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($data->nota);
        $disposition = $request->has('download') ? 'attachment' : 'inline';
        $filename = 'nota-kecil-'.$id;

        return response($data->nota)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', "$disposition; filename=\"$filename\"");
    }

    public function destroyKasKecil(Request $request, $id_unit)
    {
        try {
            // Ensure ids is always an array
            $ids = is_array($request->ids) ? $request->ids : [$request->ids];

            if (empty($ids)) {
                return response()->json(['message' => 'Tidak ada data terpilih'], 400);
            }

            // Update status to 0 for the selected IDs belonging to this unit
            Kas_Kecil::whereIn('id', $ids)
                ->where('id_unit', $id_unit)
                ->where('status', '!=', 2)
                ->update(['status' => 0]);

            return response()->json(['message' => 'Data berhasil dihapus']);
        } catch (QueryException $e) {
            \Log::error('DestroyKasKecil DB Error: ' . $e->getMessage());
            return response()->json(['message' => $this->translateDbError($e)], 500);
        } catch (\Exception $e) {
            \Log::error('DestroyKasKecil General Error: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.'], 500);
        }
    }

    public function exportKasKecil(Request $request, $id)
    {
        // 1. Validasi Input (Disesuaikan dengan form HTML yang baru)
        $request->validate([
            'kasKecilIds' => 'required|array|min:1',
            'format'      => 'required|in:excel,pdf',
            'kepada'      => 'required|string',
            'pembukuan'   => 'nullable|string',
            'mengetahui'  => 'nullable|string',
            'kasir'       => 'nullable|string',
            'penerima'    => 'nullable|string',
            'catatan'     => 'nullable|string', // Boleh kosong (opsional)
        ]);

        // 2. Ambil data dari database yang ID-nya dikirim dari checkbox/tabel
        $dataKasKecil = Kas_Kecil::whereIn('id', $request->kasKecilIds)
                                ->orderBy('tanggal', 'asc') // Urutkan dari terlama ke terbaru
                                ->get();

        // 3. Eksekusi Export Excel
        if ($request->format === 'excel') {
            $namaFile = 'Bukti_Kas_Keluar_' . date('d_M_Y_Hi') . '.xlsx';

            return Excel::download(
                new \App\Exports\KasKecilExport(
                    $dataKasKecil,
                    $request->kepada,
                    $request->pembukuan,
                    $request->mengetahui,
                    $request->kasir,
                    $request->penerima,
                    $request->catatan
                ), 
                $namaFile
            );
        }
        
        // Tambahan: Jika nantinya Anda ingin membuat kondisi untuk PDF
        // elseif ($request->format === 'pdf') {
        //     ... logika export PDF ...
        // }
    }

    public function storeBulkAsset(Request $request, $id)
    {
        try {
            $dataEntries = $request->input('asset');

            if (! $dataEntries || ! is_array($dataEntries)) {
                return back()->with('error', 'Tidak ada data untuk disimpan.');
            }

            $savedCount = 0;

            foreach ($dataEntries as $index => $entry) {
                // FIX: Match the key 'tgl_perolehan' from your Blade form
                if (empty($entry['tgl_perolehan']) || empty($entry['nama_barang'])) {
                    continue;
                }
                $userRole = strtolower(trim(auth()->user()->role ?? ''));
                $initialStatus = in_array($userRole, ['hrd', 'admin', 'superadmin']) ? 2 : 1;

                // Map the form data to your database columns
                Asset::create([
                    'id_unit' => $id,
                    'nama_barang' => $entry['nama_barang'],
                    'keterangan' => $entry['keterangan'] ?? '-',
                    'jumlah' => $entry['jumlah'] ?? 1,
                    'tahun_perolehan' => $entry['tgl_perolehan'], // Form uses 'tgl_perolehan'
                    'harga_perolehan' => $entry['harga'] ?? 0,    // Form uses 'harga'
                    'lokasi' => $entry['lokasi'] ?? '-',
                    'status' => $initialStatus,
                ]);

                $savedCount++;
            }

            if ($savedCount === 0) {
                return back()->with('error', 'Gagal menyimpan data. Pastikan Nama Barang dan Tanggal terisi.');
            }

            return back()->with('success', "Berhasil menyimpan $savedCount asset.");
        } catch (QueryException $e) {
            \Log::error('StoreBulkAsset DB Error: ' . $e->getMessage());
            return back()->with('error', $this->translateDbError($e));
        } catch (\Exception $e) {
            \Log::error('StoreBulkAsset General Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function updateBulkAsset(Request $request, $id)
    {
        try {
            $dataEntries = $request->input('asset');

            if (! $dataEntries || ! is_array($dataEntries)) {
                return back()->with('error', 'Tidak ada data asset untuk diperbarui.');
            }

            foreach ($dataEntries as $index => $entry) {
                $asset = Asset::where('id', $entry['id'])
                    ->where('id_unit', $id)
                    ->first();

                if ($asset) {
                    $updateData = [
                        'nama_barang' => $entry['nama_barang'],
                        'jumlah' => $entry['jumlah'],
                        'tahun_perolehan' => $entry['tgl_perolehan'],
                        'keterangan' => $entry['keterangan'],
                        'harga_perolehan' => $entry['harga'],
                        'lokasi' => $entry['lokasi'],
                        'updated_at' => now(),
                    ];

                    $asset->update($updateData);
                }
            }

            return back()->with('success', 'Berhasil memperbarui '.count($dataEntries).' data asset.');
        } catch (QueryException $e) {
            \Log::error('UpdateBulkAsset DB Error: ' . $e->getMessage());
            return back()->with('error', $this->translateDbError($e));
        } catch (\Exception $e) {
            \Log::error('UpdateBulkAsset General Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function destroyAsset(Request $request, $id_unit)
    {
        try {
            $ids = is_array($request->ids) ? $request->ids : [$request->ids];

            \App\Models\Asset::whereIn('id', $ids)
                ->where('id_unit', $id_unit)
                ->update(['status' => 0]); // Soft delete logic

            return response()->json(['message' => 'Asset berhasil dihapus']);
        } catch (QueryException $e) {
            \Log::error('DestroyAsset DB Error: ' . $e->getMessage());
            return response()->json(['message' => $this->translateDbError($e)], 500);
        } catch (\Exception $e) {
            \Log::error('DestroyAsset General Error: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.'], 500);
        }
    }

    public function approveKasKecil(Request $request, $id_unit)
    {
        try {
            if (!in_array(auth()->user()->role, ['admin', 'hrd', 'akuntan'])) {
                return response()->json(['message' => 'Anda tidak memiliki hak akses untuk menyetujui data ini.'], 403);
            }

            $ids = is_array($request->ids) ? $request->ids : [$request->ids];
            if (empty($ids)) {
                return response()->json(['message' => 'Tidak ada data terpilih untuk disetujui.'], 400);
            }

            Kas_Kecil::whereIn('id', $ids)
                ->where('id_unit', $id_unit)
                ->update(['status' => 2]);

            return response()->json(['message' => 'Transaksi kas kecil berhasil disetujui.']);
        } catch (QueryException $e) {
            \Log::error('ApproveKasKecil DB Error: ' . $e->getMessage());
            return response()->json(['message' => $this->translateDbError($e)], 500);
        } catch (\Exception $e) {
            \Log::error('ApproveKasKecil General Error: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.'], 500);
        }
    }

    public function approveAsset(Request $request, $id_unit)
    {
        try {
            if (!in_array(auth()->user()->role, ['admin', 'hrd', 'akuntan'])) {
                return response()->json(['message' => 'Anda tidak memiliki hak akses untuk menyetujui data ini.'], 403);
            }

            $ids = is_array($request->ids) ? $request->ids : [$request->ids];
            if (empty($ids)) {
                return response()->json(['message' => 'Tidak ada data terpilih untuk disetujui.'], 400);
            }

            Asset::whereIn('id', $ids)
                ->where('id_unit', $id_unit)
                ->update(['status' => 2]);

            return response()->json(['message' => 'Asset berhasil disetujui.']);
        } catch (QueryException $e) {
            \Log::error('ApproveAsset DB Error: ' . $e->getMessage());
            return response()->json(['message' => $this->translateDbError($e)], 500);
        } catch (\Exception $e) {
            \Log::error('ApproveAsset General Error: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.'], 500);
        }
    }

    public function exportAsset(Request $request, $id_unit)
    {
        // 1. Validasi request parameter
        $request->validate([
            'assetIds' => 'required|array|min:1',
            'format'   => 'required|in:excel',
        ]);

        // 2. Ambil data asset yang dicentang oleh user dan pastikan sesuai dengan id_unit saat ini
        $dataAsset = Asset::whereIn('id', $request->assetIds)
                        ->where('id_unit', $id_unit)
                        ->orderBy('nama_barang', 'asc')
                        ->get();

        // 3. Eksekusi file download Excel
        if ($request->format === 'excel') {
            $namaFile = 'Laporan_Asset_Unit_' . $id_unit . '_' . date('d_M_Y_Hi') . '.xlsx';

            return Excel::download(new AssetExport($dataAsset), $namaFile);
        }
    }

       public function getUnitData($id)
    {
        try {
            // It is safer to use the exact model names
            $kasKecil = Kas_Kecil::where('id_unit', $id)
                ->whereIn('status', [1, 2])
                ->select('id', 'id_unit', 'akun', 'tanggal', 'keterangan', 'debit', 'kredit', 'status')
                ->selectRaw('CASE WHEN nota IS NULL THEN 0 ELSE 1 END as nota')
                ->orderBy('tanggal', 'asc')
                ->get();

            $assets = Asset::where('id_unit', $id)
                ->whereIn('status', [1, 2])
                ->orderBy('tahun_perolehan', 'asc')
                ->get();
            
            return response()->json([
                'kasKecil' => $kasKecil,
                'assets' => $assets
            ]);
        } catch (QueryException $e) {
            \Log::error('GetUnitData DB Error: ' . $e->getMessage());
            return response()->json(['error' => $this->translateDbError($e)], 500);
        } catch (\Exception $e) {
            \Log::error('GetUnitData General Error: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.'], 500);
        }
    }

        public function exportExcelPekerja($id)
    {
        $unit = Unit::findOrFail($id);
        
        // Format nama file: Data_Pekerja_Aktif_NamaUnit.xlsx
        $fileName = 'Data_Pekerja_Aktif_' . str_replace(' ', '_', $unit->nama_unit) . '.xlsx';
        
        return Excel::download(new PekerjaUnitExport($id), $fileName);
    }

        public function exportExcelBorongan($id)
    {
        $unit = Unit::findOrFail($id);
        
        // Format nama file: Data_Borongan_Aktif_NamaUnit.xlsx
        $fileName = 'Data_Borongan_Aktif_' . str_replace(' ', '_', $unit->nama_unit) . '.xlsx';
        
        return Excel::download(new BoronganUnitExport($id), $fileName);
    }

    public function monitoring_pkwt(Request $request, $unitId)
    {
        $bulanTahun = $request->input('bulan_tahun', now()->format('Y-m')); 
        $durasi     = (int) $request->input('durasi', 3);                   
        
        $fileName = 'Laporan Monitoring Masa Berlaku PKWT - Unit ' . $unitId . ' (' . $bulanTahun . ').xlsx';

        return Excel::download(new MonitoringPkwtExport($unitId, $bulanTahun, $durasi), $fileName);
    }

    public function perputaran_pekerja(Request $request, $unitId)
    {
        $bulanTahun = $request->input('bulan_tahun', now()->format('Y-m'));
        $durasi     = (int) $request->input('durasi', 3);
        
        $fileName   = 'Laporan_Perputaran_Pekerja_Unit_' . $unitId . ' (' . $bulanTahun . ').xlsx';

        return Excel::download(new PerputaranPekerjaExport($unitId, $bulanTahun, $durasi), $fileName);
    }
}
