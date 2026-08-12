<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pekerja;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\History;
use App\Models\Penilaian_Pkwt;
use App\Models\PKWT;
use App\Models\MitraKerja;
use App\Models\Unit;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Imports\PekerjaImport;
use App\Models\PKWT_Hari_Kerja;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PekerjaController extends Controller
{
    /**
     * Terjemahkan error code MySQL ke pesan ramah pengguna.
     */
    private function translateDbError(QueryException $e): string
    {
        $errorCode = $e->errorInfo[1] ?? null;

        return match ($errorCode) {
            1062 => 'Data yang Anda masukkan sudah ada di sistem. Mohon periksa kembali NIK atau data lainnya yang bersifat unik(tidak boleh sama).',
            1452 => 'Data referensi tidak valid. Pastikan data terkait masih terdaftar di sistem.',
            1048 => 'Terdapat kolom wajib yang belum diisi. Mohon lengkapi seluruh data yang diperlukan.',
            1406 => 'Data yang dimasukkan terlalu panjang. Mohon persingkat input Anda.',
            1264 => 'Nilai angka yang dimasukkan di luar batas yang diizinkan. Mohon periksa kembali.',
            default => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi atau hubungi administrator.',
        };
    }
    public function viewPekerjaMain(Request $request)
    {
        $user = Auth::user();
        $pic = $user->staff; // Your Staff model

        // --- 1. DETERMINE ACCESS SCOPE ---
        $isGlobalUser = false;
        $assignedUnitIds = [];

        // Check if user has global access (Adjust 'admin' or 'superadmin' to match your role system)
        if (in_array($user->role, ['admin', 'superadmin', 'hrd'])) {
            $isGlobalUser = true;
        }else {
            // If not a global user, they MUST have a staff profile
            if (!$pic) {
                return redirect()->back()->with('error', 'Profil Staff tidak ditemukan.');
            }

            // Fetch unit IDs assigned to this PIC from 'pic_unit'
            $assignedUnitIds = DB::table('pic_unit')
                                ->where('id_pic', $pic->id) // Adjust if PK is id_pic or id_staff
                                ->pluck('id_unit')
                                ->toArray();

            // If a restricted user has no assigned units, block access
            if (empty($assignedUnitIds)) {
                return redirect()->back()->with('error', 'Anda belum ditugaskan ke unit manapun.');
            }
        }


        // --- 2. CALCULATE STATS (Top Cards) ---
        // Start base queries for counters
        $totalQuery   = Pekerja::query();
        $baruQuery    = Pekerja::whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
        $pendingQuery = Pekerja::where('status_aktif', 2);
        $aktifQuery   = Pekerja::where('status_aktif', 0);

        // Apply unit scope if user is a restricted PIC
        if (!$isGlobalUser) {
            $totalQuery->whereHas('pkwtAktif', function ($q) use ($assignedUnitIds) {
                $q->whereIn('id_unit', $assignedUnitIds);
            });
            $baruQuery->whereHas('pkwtAktif', function ($q) use ($assignedUnitIds) {
                $q->whereIn('id_unit', $assignedUnitIds);
            });
            $pendingQuery->whereHas('pkwtAktif', function ($q) use ($assignedUnitIds) {
                $q->whereIn('id_unit', $assignedUnitIds);
            });
            $aktifQuery->whereHas('pkwtAktif', function ($q) use ($assignedUnitIds) {
                $q->whereIn('id_unit', $assignedUnitIds);
            });
        }

        $totalPekerja        = $totalQuery->count();
        $pekerjaBaru         = $baruQuery->count();
        $pekerjaPendingCount = $pendingQuery->count();
        $tidakAktif          = $aktifQuery->count();

        $pendingPekerjaList = Pekerja::with(['pkwtAktif.unit'])
            ->where('status_aktif', 2)
            ->when(!$isGlobalUser, function ($q) use ($assignedUnitIds) {
                $q->whereHas('pkwtAktif', function ($qq) use ($assignedUnitIds) {
                    $qq->whereIn('id_unit', $assignedUnitIds);
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();


        // --- 3. BUILD MAIN QUERY ---
        $query = Pekerja::with(['pkwtAktif.unit'])->where('status_aktif', '!=', 2);

        // Enforce unit scope only if NOT a global user
        if (!$isGlobalUser) {
            $query->whereHas('pkwtAktif', function ($q) use ($assignedUnitIds) {
                $q->whereIn('id_unit', $assignedUnitIds);
            });
        }

        // A. Filter by Search (Name, NIK, KPJ)
        $search = $request->input('search') ?? $request->input('q');
        $query->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'LIKE', "%{$search}%")
                    ->orWhere('nik', 'LIKE', "%{$search}%")
                    ->orWhere('kpj', 'LIKE', "%{$search}%");
            });
        });

        

        // B. Filter by Status (Exact Match)
        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status_aktif', $request->status);
        });

        // C. Filter by Unit
        $query->when($request->filled('unit'), function ($q) use ($request) {
            $q->whereHas('pkwtAktif', function ($sub) use ($request) {
                $sub->where('id_unit', $request->unit);
            });
        });


        // D. Filter by Date Range (Tanggal Bergabung)
        $query->when($request->start_date, function ($q) use ($request) {
            $q->whereDate('tgl_bergabung', '>=', $request->start_date);
        });

        $query->when($request->end_date, function ($q) use ($request) {
            $q->whereDate('tgl_bergabung', '<=', $request->end_date);
        });


        // --- 4. FETCH DATA ---
        $pekerja = $query->orderBy('created_at', 'desc')
                        ->paginate(10)
                        ->withQueryString();


        // --- 5. FETCH UNITS FOR FILTER DROPDOWN ---
        $unitQuery = Unit::where('status_aktif', 1)->orderBy('nama_unit');
        if (!$isGlobalUser) {
            $unitQuery->whereIn('id', $assignedUnitIds);
        }
        $units = $unitQuery->get(['id', 'nama_unit']);

        // --- 6a. PIC-SPECIFIC: their own pending submissions ---
        $myPendingList = collect();
        if (!$isGlobalUser) {
            $myPendingList = Pekerja::with(['pkwtAktif.unit', 'user'])
                ->where('status_aktif', 2)
                ->where('created_by', Auth::id()) // Ensure this matches your foreign key for the inputter
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // --- 6. RETURN RESPONSE ---
        if ($request->ajax()) {
            return view('Pekerja.partials.pekerja-table', compact('pekerja'))->render();
        }

        return view('Pekerja.main-pekerja', compact('pekerja', 'totalPekerja', 'pekerjaBaru', 'tidakAktif', 'pekerjaPendingCount', 'pendingPekerjaList', 'myPendingList', 'units'));
    }

    public function viewTambahPekerja()
    {
        $user = auth()->user();
        $role = strtolower(trim($user->role ?? ''));
        $isGlobalUser = in_array($role, ['admin', 'superadmin', 'hrd']);
        $staffId = $user->staff?->id ?? $user->staff_id;

        $query = MitraKerja::query()
            ->where('status_aktif', 1)
            ->with(['units' => function ($q) use ($isGlobalUser, $staffId) {
                $q->where('status_aktif', 1);

                if (!$isGlobalUser && $staffId) {
                    $q->whereHas('picUnit', function ($queryPic) use ($staffId) {
                        $queryPic->where('id_pic', $staffId);
                    });
                }
            }]);

        // Only restrict MitraKerja to assigned units if user is NOT a global user (e.g. PIC)
        if (!$isGlobalUser && $staffId) {
            $query->whereHas('units', function ($q) use ($staffId) {
                $q->where('status_aktif', 1)
                  ->whereHas('picUnit', function ($queryPic) use ($staffId) {
                      $queryPic->where('id_pic', $staffId);
                  });
            });
        }

        $mitras = $query->get();

        dd($mitras);

        \Log::info('viewTambahPekerja debug', [
            'role' => $role,
            'isGlobalUser' => $isGlobalUser,
            'staffId' => $staffId,
            'mitras_count' => $mitras->count(),
        ]);

        return view('Pekerja.CRUD.tambah-pekerja', compact('mitras'));
    }

    public function showDokumen($id, Request $request)
    {
        // 1. Find the record
        $data = Pekerja::findOrFail($id);

        // 2. Check if blob exists
        if (!$data->dokumen) {
            abort(404, 'Dokumen tidak ditemukan.');
        }

        // 3. Detect the MIME type (PDF, JPG, PNG) from the binary data
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($data->dokumen_pkwt);

        // 4. Determine if it's "View" (inline) or "Download" (attachment)
        // If URL has ?download=true, we force download.
        $disposition = $request->has('download') ? 'attachment' : 'inline';

        // Generate a filename
        $filename = 'pkwt-pekerja-mitra-' . $id;

        // 5. Return the binary data as a proper HTTP response
        return response($data->dokumen)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', $disposition . '; filename="' . $filename . '"');
    }

    // For Current & History PKWT (Blob from pkwt_pekerja table)
    public function showPkwtDokumen($id, Request $request)
    {
        $data = PKWT::findOrFail($id);

        if (!$data->dokumen_pkwt) {
            abort(404, 'Dokumen PKWT tidak ditemukan.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($data->dokumen_pkwt);
        $disposition = $request->has('download') ? 'attachment' : 'inline';
        $filename = 'pkwt-pekerja-' . $id;

        return response($data->dokumen_pkwt)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', "$disposition; filename=\"$filename\"");
    }

    // function viewDetailPekerja($id)
    // {
    //     $pekerja = Pekerja::where('id', $id)->first();

    //     $pekerja->image_base64 = 'data:image/jpeg;base64,' . base64_encode($pekerja->image_blob);

    //     $historiPekerja = History::where('foreign_id', $id)->where('nama_tabel', 'pekerja')->get();

    //     return view('Pekerja.detail-pekerja', compact('pekerja', 'historiPekerja'));
    // }

    public function viewDetailPekerja($id)
    {
        // 1. Ambil data pekerja
        $pekerja = Pekerja::findOrFail($id);

        // 2. Konversi foto blob ke base64
        if ($pekerja->foto) {
            $pekerja->image_base64 = 'data:image/jpeg;base64,' . base64_encode($pekerja->foto);
        } else {
            $pekerja->image_base64 = null;
        }

        // 3. Ambil PKWT yang paling terbaru (Aktif)
        $currentPkwt = PKWT::with('unit')
                            ->where('id_pekerja', $id)
                            ->latest('tgl_mulai_pkwt')
                            ->first();

        // 4. Ambil Histori PKWT (Semua kontrak kecuali yang aktif jika perlu difilter di view)
        $historiPkwt = PKWT::where('id_pekerja', $id)
                            ->orderBy('tgl_mulai_pkwt', 'desc')
                            ->get();

        // 5. Ambil Histori Penilaian (Lengkap dengan data Staff/Penilai)
        // Kita ambil semua kolom agar detailnya bisa langsung muncul di popup tanpa fetch lagi
        $historiPenilaian = Penilaian_Pkwt::where('id_pekerja', $id)
                                ->orderBy('created_at', 'desc')
                                ->get();

        // 6. Ambil Histori Log/Audit
        $historiPekerja = History::where('foreign_id', $id)
                                ->where('nama_tabel', 'pekerja')
                                ->get();

        return view('Pekerja.detail-pekerja', compact(
            'pekerja',
            'currentPkwt',
            'historiPkwt',
            'historiPenilaian', // Variabel baru
            'historiPekerja'
        ));
    }

    function tambahPekerja(request $request)
    {
        // dd($request->all());
        try {
            $request->validate(
                [
                    'nama' => 'required|string|max:255',
                    'id_pekerja' => 'required|string',
                    'nik' => 'required|digits:16|unique:pekerja,nik',
                    'no_kk' => 'required|digits:16',
                    'tempat_lahir' => 'required|string|max:100',
                    'tgl_lahir' => 'required|date',
                    'kelamin' => 'required|boolean',
                    'pendidikan' => 'required|string',
                    'status_kawin' => 'required|string',
                    'anak' => 'nullable|integer|min:0',
                    'tgl_bergabung' => 'required|date',
                    'tgl_resign' => 'nullable|date',

                    'alamat' => 'required|string',
                    'desa' => 'required|string',
                    'rt' => 'nullable|string|max:3',
                    'rw' => 'nullable|string|max:3',
                    'kota' => 'required|string',
                    'kecamatan' => 'required|string',
                    'provinsi' => 'required|string',

                    'email' => 'nullable|email',
                    'telp' => 'nullable|string|max:16',
                    'kpj' => 'nullable|string|max:11',
                    'naker' => 'nullable|string|max:13',

                    'nama_rek' => 'nullable|string',
                    'rekening' => 'nullable|string|max:30',

                    'nama_emergency' => 'required|string|max:255',
                    'telp_emergency' => 'required|string|max:16',
                    'hubungan_emergency' => 'required|string',

                    'ibu_kandung' => 'nullable|string|max:255',

                    'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                    // 'dokumen' => 'nullable|image|mimes:png,jpg,jpeg,pdf|max:2048',
                    'dokumen' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
                ],
                [
                    // Identitas
                    'nama.required' => 'Nama tidak boleh kosong.',
                    'nama.max' => 'Nama maksimal 255 karakter.',

                    'nik.required' => 'NIK wajib diisi.',
                    'nik.digits' => 'NIK harus 16 digit angka.',
                    'nik.unique' => 'NIK sudah terdaftar, gunakan NIK lain.',

                    'no_kk.required' => 'No KK wajib diisi.',
                    'no_kk.digits' => 'No KK harus 16 digit angka.',

                    'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
                    'tempat_lahir.max' => 'Tempat lahir maksimal 100 karakter.',

                    'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
                    'tgl_lahir.date' => 'Tanggal lahir tidak valid.',

                    'kelamin.required' => 'Jenis kelamin wajib dipilih.',
                    'kelamin.boolean' => 'Format jenis kelamin tidak valid.',

                    'pendidikan.required' => 'Pendidikan wajib diisi.',
                    'status_kawin.required' => 'Status perkawinan wajib diisi.',

                    'anak.integer' => 'Jumlah anak harus angka.',
                    'anak.min' => 'Minimal nilai anak adalah 0.',

                    'tgl_bergabung.required' => 'Tanggal bergabung wajib diisi.',
                    'tgl_bergabung.date' => 'Tanggal bergabung tidak valid.',

                    'tgl_resign.date' => 'Tanggal resign tidak valid.',

                    // Alamat
                    'alamat.required' => 'Alamat wajib diisi.',
                    'desa.required' => 'Desa wajib diisi.',
                    'rt.string' => 'RT harus berupa angka.',
                    'rw.string' => 'RW harus berupa angka.',
                    'kota.required' => 'Kota wajib diisi.',
                    'kecamatan.required' => 'Kecamatan wajib diisi.',
                    'provinsi.required' => 'Provinsi wajib diisi.',

                    // Kontak
                    'email.email' => 'Format email tidak valid.',
                    'telp.max' => 'No telepon maksimal 16 karakter.',

                    // Bank
                    'rekening.max' => 'No rekening maksimal 30 karakter.',

                    // Emergency
                    'nama_emergency.required' => 'Nama kontak darurat wajib diisi.',
                    'telp_emergency.required' => 'No telepon darurat wajib diisi.',
                    'telp_emergency.max' => 'No telepon darurat maksimal 16 karakter.',
                    'hubungan_emergency.required' => 'Hubungan dengan kontak darurat wajib diisi.',

                    // Foto
                    'foto.image' => 'File foto harus berupa gambar.',
                    'foto.mimes' => 'Format foto harus jpg/jpeg/png.',
                    'foto.max' => 'Ukuran foto maksimal 2MB.',
                ],
            );

            // dd($request->all());

            // ✅ Upload foto
            $fotoBlob = null;
            if ($request->hasFile('foto')) {
                $fotoBlob = file_get_contents($request->file('foto')->getRealPath());
            }

            // $dokumenBlob = null;
            // if ($request->hasFile('dokumen')) {
            //     $dokumenBlob = file_get_contents($request->file('dokumen')->getRealPath());
            // }

            $userRole = strtolower(trim(auth()->user()->role ?? ''));
            $initialStatus = in_array($userRole, ['hrd', 'admin', 'superadmin']) ? 1 : 2;

            // ✅ Simpan ke database
            $pekerja = Pekerja::create([
                'nama' => $request->nama,
                'id_pekerja' => $request->id_pekerja,
                'nik' => $request->nik,
                'no_kk' => $request->no_kk,
                'tempat_lahir' => $request->tempat_lahir,
                'tgl_lahir' => $request->tgl_lahir,
                'kelamin' => $request->kelamin,
                'pendidikan' => $request->pendidikan,
                'status_kawin' => $request->status_kawin,
                'anak' => $request->anak ?? 0,
                'tgl_bergabung' => $request->tgl_bergabung,
                'tgl_resign' => $request->tgl_resign,

                'alamat' => $request->alamat,
                'desa' => $request->desa,
                'rt' => $request->rt,
                'rw' => $request->rw,
                'kecamatan' => $request->kecamatan,
                'kota' => $request->kota,
                'provinsi' => $request->provinsi,
                'kpj' => $request->kpj,
                'naker' => $request->naker,

                'email' => $request->email,
                'telp' => $request->telp,

                'rekening' => $request->rekening,
                'nama_rek' => $request->nama_rek,

                'nama_emergency' => $request->nama_emergency,
                'telp_emergency' => $request->telp_emergency,
                'hubungan_emergency' => $request->hubungan_emergency,
                'ibu_kandung' => $request->ibu_kandung,

                'foto' => $fotoBlob,
                // 'dokumen' => $dokumenBlob,

                'status_aktif' => $initialStatus,

                'created_by' => Auth::id(),
            ]);

            if ($request->has('penempatan_unit') && $request->penempatan_unit == '1' && $request->filled('id_unit')) {
                return redirect()
                    ->route('view.tambah.unit-pekerja', ['id' => $request->id_unit, 'pekerja_id' => $pekerja->id])
                    ->with('success', 'Data Pekerja ' . $pekerja->nama . ' berhasil ditambahkan. Silakan lanjutkan penempatan unit.');
            }

            return redirect()
                ->route('view.tambah.pekerja')
                ->with('success', 'Data Pekerja ' . $pekerja->nama . ' berhasil ditambahkan.');
        } catch (QueryException $e) {
            \Log::error('TambahPekerja DB Error: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['database' => $this->translateDbError($e)]);
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('TambahPekerja General Error: ' . $e->getMessage());
            return back()
                ->withInput()
                ->withErrors(['general' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.']);
        }
    }

    function ubahPekerja(request $request, $id)
    {
        $pekerja = Pekerja::findOrFail($id);

        return view('Pekerja.CRUD.ubah-pekerja', compact('pekerja'));
    }

    public function updatePekerja(Request $request, $id)
    {
        try {
            $pekerja = Pekerja::findOrFail($id);

            $request->validate(
                [
                    'nama' => 'required|string|max:255',
                    'id_pekerja' => 'required|string',

                    'nik' => 'required|digits:16',
                    'no_kk' => 'required|digits:16',
                    
                    'tempat_lahir' => 'required|string|max:100',
                    'tgl_lahir' => 'required|date',
                    'kelamin' => 'required|boolean',
                    'pendidikan' => 'required|string',
                    'status_kawin' => 'required|string',
                    'anak' => 'nullable|integer|min:0',
                    'tgl_bergabung' => 'required|date',
                    'tgl_resign' => 'nullable|date',

                    'alamat' => 'required|string',
                    'desa' => 'required|string',
                    'rt' => 'nullable|string|max:3',
                    'rw' => 'nullable|string|max:3',
                    'kota' => 'required|string',
                    'kecamatan' => 'required|string',
                    'provinsi' => 'required|string',

                    'email' => 'nullable|email',
                    'telp' => 'nullable|string|max:16',

                    'nama_rek' => 'nullable|string',
                    'rekening' => 'nullable|string|max:30',
                    'kpj' => 'nullable|string|max:11',
                    'naker' => 'nullable|string|max:13',

                    'nama_emergency' => 'required|string|max:255',
                    'telp_emergency' => 'required|string|max:16',
                    'hubungan_emergency' => 'required|string',

                    'ibu_kandung' => 'nullable|string|max:255',

                    'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                ],
                [
                    // Identitas
                    'nama.required' => 'Nama tidak boleh kosong.',
                    'nama.max' => 'Nama maksimal 255 karakter.',

                    'nik.required' => 'NIK wajib diisi.',
                    'nik.digits' => 'NIK harus 16 digit angka.',
                    'nik.unique' => 'NIK sudah terdaftar, gunakan NIK lain.',

                    'no_kk.required' => 'No KK wajib diisi.',
                    'no_kk.digits' => 'No KK harus 16 digit angka.',

                    'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
                    'tempat_lahir.max' => 'Tempat lahir maksimal 100 karakter.',

                    'tgl_lahir.required' => 'Tanggal lahir wajib diisi.',
                    'tgl_lahir.date' => 'Tanggal lahir tidak valid.',

                    'kelamin.required' => 'Jenis kelamin wajib dipilih.',
                    'kelamin.boolean' => 'Format jenis kelamin tidak valid.',

                    'pendidikan.required' => 'Pendidikan wajib diisi.',
                    'status_kawin.required' => 'Status perkawinan wajib diisi.',

                    'anak.integer' => 'Jumlah anak harus angka.',
                    'anak.min' => 'Minimal nilai anak adalah 0.',

                    'tgl_bergabung.required' => 'Tanggal bergabung wajib diisi.',
                    'tgl_bergabung.date' => 'Tanggal bergabung tidak valid.',

                    'tgl_resign.date' => 'Tanggal resign tidak valid.',

                    // Alamat
                    'alamat.required' => 'Alamat wajib diisi.',
                    'desa.required' => 'Desa wajib diisi.',
                    'rt.string' => 'RT harus berupa angka.',
                    'rw.string' => 'RW harus berupa angka.',
                    'kota.required' => 'Kota wajib diisi.',
                    'kecamatan.required' => 'Kecamatan wajib diisi.',
                    'provinsi.required' => 'Provinsi wajib diisi.',

                    // Kontak
                    'email.email' => 'Format email tidak valid.',
                    'telp.max' => 'No telepon maksimal 16 karakter.',
                    'kpj' => 'KPJ maksimal 13 karakter',

                    // Bank
                    'rekening.max' => 'No rekening maksimal 30 karakter.',

                    // Emergency
                    'nama_emergency.required' => 'Nama kontak darurat wajib diisi.',
                    'telp_emergency.required' => 'No telepon darurat wajib diisi.',
                    'telp_emergency.max' => 'No telepon darurat maksimal 16 karakter.',
                    'hubungan_emergency.required' => 'Hubungan dengan kontak darurat wajib diisi.',

                    // Foto
                    'foto.image' => 'File foto harus berupa gambar.',
                    'foto.mimes' => 'Format foto harus jpg/jpeg/png.',
                    'foto.max' => 'Ukuran foto maksimal 2MB.',
                ],
            );

            $data = $request->except('foto', '_token', '_method');

            if ($request->remove_foto == '1') {
                $pekerja->foto = null;
            }

            // ✅ JIKA FOTO DIGANTI
            if ($request->hasFile('foto')) {
                $foto = file_get_contents($request->file('foto')->getRealPath());
                $pekerja->foto = $foto;
            }

            // ✅ UPDATE DATA
            $pekerja->update($data);

            History::create([
                'foreign_id' => $pekerja->id,
                'nama_tabel' => 'pekerja', // konsisten
                'updated_by' => auth()->id() ?? 0,
                'jabatan' => optional(auth()->user()->staff)->jabatan ?? 'system',
                'when' => now(),
            ]);

            // ✅ KEMBALI KE DETAIL PEKERJA (LEBIH BAGUS DARIPADA KE LIST)
            return redirect()->route('view.detail.pekerja', $id)->with('success', 'Data pekerja berhasil diperbarui');
        } catch (QueryException $e) {
            \Log::error('UpdatePekerja DB Error: ' . $e->getMessage());
            return back()->withInput()->with('error', $this->translateDbError($e));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('UpdatePekerja General Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function toggleStatus($id)
    {
        $pekerja = Pekerja::findOrFail($id);

        $pekerja->status_aktif = !$pekerja->status_aktif;
        $pekerja->save();

        History::create([
            'foreign_id' => $pekerja->id,
            'nama_tabel' => 'pekerja', // konsisten
            'updated_by' => auth()->id() ?? 0,
            'jabatan' => optional(auth()->user()->staff)->jabatan ?? 'system',
            'when' => now(),
        ]);

        return response()->json([
            'message' => $pekerja->status_aktif
                ? 'Pekerja berhasil diaktifkan'
                : 'Pekerja berhasil dinonaktifkan'
        ]);
    }

    public function importExcel(Request $request)
    {
        // 1. Validasi File
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:10240' // Max 10MB
        ], [
            'file_excel.required' => 'Pilih file Excel terlebih dahulu!',
            'file_excel.mimes'    => 'Format file harus berupa .xlsx, .xls, atau .csv!',
        ]);

        DB::beginTransaction();
        try {
            // 2. Eksekusi proses Import menggunakan class PekerjaImport
            Excel::import(new PekerjaImport, $request->file('file_excel'));

            DB::commit(); // Simpan permanen ke database jika sukses semua

            return redirect()->back()->with('success', 'Data Pekerja berhasil di-import ke sistem!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();

            // Grab excel row errors and merge them into a clear string
            $failures = [];
            foreach ($e->failures() as $failure) {
                $failures[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            $errorString = 'Kesalahan format: ' . implode(' | ', $failures);

            return redirect()->back()->with('error', $errorString);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('ImportExcelPekerja General Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import data. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function TambahHistoriPKWT(Request $request)
    {
        // dd($request->all());
        // 1. Validasi Input Dasar
        $request->validate([
            'id_pekerja'     => 'required|string', // Sesuaikan jika id_pekerja Anda char/string/integer
            'tgl_mulai_pkwt' => 'required|date',
            'tgl_akhir_pkwt' => 'required|date|after_or_equal:tgl_mulai_pkwt',

            // Dokumen bersifat opsional untuk masa lalu, sesuaikan jika ingin diwajibkan
            'dokumen_pkwt'   => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:2048',

            // Boleh diisi jika HRD ingat data lama, jika tidak akan null
            'id_unit'        => 'nullable|string',
            'divisi_id'      => 'nullable|integer',
            'jabatan_id'     => 'nullable|integer',
        ], [
            'id_pekerja.required'     => 'ID Pekerja tidak ditemukan.',
            'tgl_mulai_pkwt.required' => 'Tanggal mulai PKWT wajib diisi.',
            'tgl_akhir_pkwt.required' => 'Tanggal akhir PKWT wajib diisi.',
            'tgl_akhir_pkwt.after_or_equal' => 'Tanggal akhir tidak boleh lebih kecil dari tanggal mulai.',
            'dokumen_pkwt.mimes'      => 'Format dokumen harus berupa PDF, JPG, atau PNG.',
            'dokumen_pkwt.max'        => 'Ukuran dokumen maksimal 2MB.',
        ]);

        try {
            DB::beginTransaction();

            // 2. Pemrosesan File Dokumen (Bila Ada)
            $dokumen = null;
            $dokumenMime = null;
            if ($request->hasFile('dokumen_pkwt')) {
                $file = $request->file('dokumen_pkwt');
                $dokumen = file_get_contents($file->getRealPath());
                $dokumenMime = $file->getMimeType();
            }

            // 3. Simpan sebagai History (Status Aktif = 0)
            PKWT::create([
                'id_pekerja'     => $request->id_pekerja,
                'id_unit'        => $request->id_unit ?? null,
                'divisi_id'      => $request->divisi_id ?? null,
                'jabatan_id'     => $request->jabatan_id ?? null,

                'tgl_mulai_pkwt' => $request->tgl_mulai_pkwt,
                'tgl_akhir_pkwt' => $request->tgl_akhir_pkwt,

                'dokumen_pkwt'   => $dokumen,
                'dokumen_mime'   => $dokumenMime,

                // KUNCI UTAMA: 0 menandakan ini adalah History/Log masa lalu
                'status_aktif'   => 0,

                // Field gaji akan otomatis terisi 0 berdasarkan nilai default di migration
                // atau Anda bisa mendefinisikannya eksplisit di sini jika perlu
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Histori PKWT pekerja berhasil ditambahkan.');

        } catch (QueryException $e) {
            DB::rollBack();
            \Log::error('TambahHistoriPKWT DB Error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['database' => $this->translateDbError($e)]);
        } catch (ValidationException $e) {
            return back()->withInput()->withErrors(['general' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.']);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('TambahHistoriPKWT General Error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['general' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.']);
        }
    }

    public function approvePekerjaBulk(Request $request)
    {
        try {
            $userRole = strtolower(trim(auth()->user()->role ?? ''));
            if (!in_array($userRole, ['admin', 'hrd', 'superadmin'])) {
                return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
            }

            // Get the IDs (works for 1 ID or 100 IDs)
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data terpilih.'], 400);
            }

            // Mass update
            Pekerja::whereIn('id', $ids)->update(['status_aktif' => 1]);
            PKWT::whereIn('id_pekerja', $ids)->update(['status_aktif' => 1]);

            // Dynamic message based on count
            $count = count($ids);
            $message = $count > 1 ? "$count pekerja berhasil disetujui." : "Pekerja berhasil disetujui.";

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function cancelPekerja($id)
    {
        try {
            // Start transaction
            DB::beginTransaction();

            $pekerja = \App\Models\Pekerja::findOrFail($id);
            
            // 1. Safety Check: Ensure it's still pending
            if ($pekerja->status_aktif != 2) {
                return response()->json(['success' => false, 'message' => 'Hanya pengajuan pending yang bisa dibatalkan.'], 403);
            }

            // 2. Delete associated PKWT and PKWT_Hari_Kerja
            // We find the PKWTs first to clean up the dependent Hari Kerja records
            $pkwtIds = PKWT::where('id_pekerja', $id)->pluck('id');

            if ($pkwtIds->isNotEmpty()) {
                // Delete Working Hours linked to these PKWTs
                PKWT_Hari_Kerja::whereIn('pkwt_id', $pkwtIds)->delete();
                
                // Delete the PKWT records themselves
                PKWT::whereIn('id', $pkwtIds)->delete();
            }

            // 3. Delete the Pekerja profile
            $pekerja->delete();

            // Commit all deletions
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan pekerja dan penempatan unit berhasil dibatalkan.'
            ]);

        } catch (\Exception $e) {
            // If anything goes wrong, undo all deletions
            DB::rollBack();
            
            \Log::error('CancelPekerja Error: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Gagal membatalkan pengajuan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportPdf($id)
    {
        // Ambil data pekerja beserta relasi (opsional jika penempatan unit ada di tabel terpisah)
        $pekerja = Pekerja::with([])->findOrFail($id);

        // Load view khusus PDF dan kirim data pekerja
        $pdf = Pdf::loadView('Pekerja.pdf-export', compact('pekerja'));

        // Atur ukuran kertas (A4) dan orientasi (portrait)
        $pdf->setPaper('A4', 'portrait');

        // Nama file yang akan didownload
        $fileName = 'Detail_Pekerja_' . str_replace(' ', '_', $pekerja->nama) . '.pdf';

        return $pdf->download($fileName);
        
        // Catatan: Jika ingin melihat preview di browser (tidak langsung download), 
        // gunakan return $pdf->stream($fileName);
    }
}
