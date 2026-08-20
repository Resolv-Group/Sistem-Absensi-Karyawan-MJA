<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use App\Models\History;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Imports\StaffImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use App\Models\Jabatan;

class StaffController extends Controller
{
    /**
     * Terjemahkan error code MySQL ke pesan ramah pengguna.
     */
    private function translateDbError(QueryException $e): string
    {
        $errorCode = $e->errorInfo[1] ?? null;

        return match ($errorCode) {
            1062 => 'Data yang Anda masukkan sudah ada di sistem. Mohon periksa kembali NIK, KPJ, atau data lainnya yang bersifat unik(tidak boleh sama).',
            1452 => 'Data referensi tidak valid. Pastikan data terkait masih terdaftar di sistem.',
            1048 => 'Terdapat kolom wajib yang belum diisi. Mohon lengkapi seluruh data yang diperlukan.',
            1406 => 'Data yang dimasukkan terlalu panjang. Mohon persingkat input Anda.',
            1264 => 'Nilai angka yang dimasukkan di luar batas yang diizinkan. Mohon periksa kembali.',
            default => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi atau hubungi administrator.',
        };
    }

    function viewStaffMain(Request $request)
    {
        // --- 1. CALCULATE STATS (Top Cards) ---
        $totalStaff = Staff::count();
        $staffBaru  = Staff::whereMonth('created_at', Carbon::now()->month)
                            ->whereYear('created_at', Carbon::now()->year)
                            ->count();
        $tidakAktif   = Staff::where('status_aktif', '!=', '1')->count();


        // --- 2. BUILD QUERY ---
        $query = Staff::query();

        // A. Filter by Search (Name, NIK, KPJ)
        // We check for 'search' (from new JS) or 'q' (fallback)
        $search = $request->input('search') ?? $request->input('q');

        $query->when($search, function ($q) use ($search) {
            $q->where(function ($sub) use ($search) {
                $sub->where('nama', 'LIKE', "%{$search}%")
                    ->orWhere('nik', 'LIKE', "%{$search}%")
                    ->orWhere('kpj', 'LIKE', "%{$search}%"); // Ensure column name is 'no_kpj' or 'kpj' based on your DB
            });
        });

        // B. Filter by Status (Exact Match)
        // We use $request->filled() to ensure we don't filter if value is empty/null
        $query->when($request->filled('status'), function ($q) use ($request) {
            $q->where('status_aktif', $request->status);
        });

        // C. Filter by Date Range (Tanggal Bergabung)
        $query->when($request->start_date, function ($q) use ($request) {
            $q->whereDate('tgl_bergabung', '>=', $request->start_date);
        });

        $query->when($request->end_date, function ($q) use ($request) {
            $q->whereDate('tgl_bergabung', '<=', $request->end_date);
        });

        // --- 3. FETCH DATA ---
        $staff = $query->orderBy('created_at', 'desc')
                        ->paginate(10)
                        ->withQueryString();


        // --- 4. RETURN RESPONSE ---

        // If AJAX request (from the search/filter script), return ONLY the table partial
        if ($request->ajax()) {
            return view('Staff.partials.staff-table', compact('staff'))->render();
        }

        // Otherwise return the full page
        return view('Staff.main-staff', compact('staff', 'totalStaff', 'staffBaru', 'tidakAktif'));

    }

    function viewTambahStaff()
    {
        $lastStaffId = Staff::max('id'); // asumsi primary key = id

        // Jika belum ada data
        $nextNumber = ($lastStaffId ?? 0) + 1;

        // Format 3 digit (001, 002, dst)
        $numberFormatted = str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        // Format tanggal: DDMMYY
        $dateFormatted = Carbon::now()->format('dmy');

        // Gabungkan
        $defaultIdStaff = "{$numberFormatted}-{$dateFormatted}";

        $jabatanList = Jabatan::orderBy('nama')->pluck('nama');

        return view('Staff.CRUD.tambah-staff', compact('defaultIdStaff', 'jabatanList'));
    }

    /**
     * AJAX: Simpan jabatan baru ke tabel jabatan.
     */
    function storeJabatan(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:100']);

        $nama = trim($request->nama);

        $jabatan = Jabatan::firstOrCreate(['nama' => $nama]);

        return response()->json([
            'success' => true,
            'nama'    => $jabatan->nama,
        ]);
    }

    function viewDetailStaff($id)
    {
        $staff = Staff::where('id', $id)->first();

        $staff->image_base64 = 'data:image/jpeg;base64,' . base64_encode($staff->image_blob);

        $historiStaff = History::where('foreign_id', $id)->where('nama_tabel', 'staff')->get();

        return view('Staff.detail-staff', compact('staff', 'historiStaff'));
    }

    function tambahStaff(request $request)
    {
        // dd($request->all());
        try {
            $request->validate(
                [
                    'nama' => 'required|string|max:255',
                    'id_staff' => 'nullable|string',
                    'nik' => 'required|digits:16|unique:staff,nik',
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

                    'email' => 'required|email',
                    'telp' => 'nullable|string|max:16',
                    'kpj' => 'nullable|string|max:13',
                    'naker' => 'nullable|string|max:13',

                    'nama_rek' => 'nullable|string',
                    'rekening' => 'nullable|string|max:30',

                    'masa_berlaku_pkwt' => 'nullable|date',
                    'perusahaan' => 'required|string',
                    'unit_kerja' => 'required|string',
                    'status_perjanjian_kerja' => 'required|string',
                    'jabatan' => 'required|string',

                    'nama_emergency' => 'required|string|max:255',
                    'telp_emergency' => 'required|string|max:16',
                    'hubungan_emergency' => 'required|string',

                    'ibu_kandung' => 'required|string|max:255',

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

                    //Deskripsi Pekerjaan
                    'masa_berlaku_pkwt' => 'Tanggal PKWT tidak valid',
                    'perusahaan' => 'Nama perusahaan wajib diisi',
                    'unit_kerja' => 'Unit Kerja wajib diisi',
                    'status_perjanjian_kerja' => 'Status Perjanjian Kerja wajib dipilih',
                    'jabatan' => 'Jabatan wajib dipilih',

                    // Alamat
                    'alamat.required' => 'Alamat wajib diisi.',
                    'desa.required' => 'Desa wajib diisi.',
                    'rt.string' => 'RT harus berupa angka.',
                    'rw.string' => 'RW harus berupa angka.',
                    'kota.required' => 'Kota wajib diisi.',
                    'kecamatan.required' => 'Kecamatan wajib diisi.',
                    'provinsi.required' => 'Provinsi wajib diisi.',
                    'kpj.required' => 'KPJ wajib diisi.',

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

                    'ibu_kandung.required' => 'Nama Ibu Kandung wajib diisi.',  

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

            // ✅ Simpan ke database
            $staff = Staff::create([
                'nama' => $request->nama,
                'id_staff' => $request->id_staff,
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

                'email' => $request->email,
                'telp' => $request->telp,
                'kpj' => $request->kpj,
                'naker' => $request->naker,

                'rekening' => $request->rekening,
                'nama_rek' => $request->nama_rek,

                'masa_berlaku_pkwt' => $request->masa_berlaku_pkwt,
                'perusahaan' => $request->perusahaan,
                'unit_kerja' => $request->unit_kerja,
                'status_perjanjian_kerja' => $request->status_perjanjian_kerja,
                'jabatan' => $request->jabatan,

                'nama_emergency' => $request->nama_emergency,
                'telp_emergency' => $request->telp_emergency,
                'hubungan_emergency' => $request->hubungan_emergency,
                'ibu_kandung' => $request->ibu_kandung,

                'foto' => $fotoBlob,

                'status_aktif' => 1,
            ]);

            $rawJabatan = trim($request->jabatan ?? '');
            $lower = strtolower($rawJabatan);
            $role = !empty($lower) ? $lower : 'staff';
            $userStatusAkun = 0;

            if (str_contains($lower, 'hrd')) {
                $role = 'hrd';
                $userStatusAkun = 1;
            } elseif (str_contains($lower, 'pic')) {
                $role = 'pic';
                $userStatusAkun = 1;
            } elseif (str_contains($lower, 'akuntan') || str_contains($lower, 'akuntansi') || str_contains($lower, 'finance') || str_contains($lower, 'accounting')) {
                $role = 'akuntan';
                $userStatusAkun = 1;
            } elseif (str_contains($lower, 'admin')) {
                $role = 'admin';
                $userStatusAkun = 1;
            } elseif (str_contains($lower, 'head') || str_contains($lower, 'supervisor')) {
                $role = 'head_supervisor';
                $userStatusAkun = 0;
            } else {
                $role = !empty($lower) ? $lower : 'staff';
                $userStatusAkun = 0;
            }

            $existingUser = User::where('email', $staff->email)->first();
            
            // Manual override: if admin explicitly ticked akses_login, use that
            if ($request->has('akses_login')) {
                $userStatusAkun = 1;
            } else {
                $userStatusAkun = 0;
            }

            if (!$existingUser) {
                $plainPassword = Carbon::parse($request->tgl_lahir)->format('d-m-Y');
                $password = Hash::make($plainPassword);

                $user = User::create([
                    'name' => $staff->nama,
                    'email' => $staff->email,
                    'password' => $password,
                    'role' => $role,
                    'staff_id' => $staff->id,
                    'status_akun' => $userStatusAkun,
                ]);

                if ($userStatusAkun == 1) {
                    session()->flash('akun_info', 'Akun dibuat! Username: ' . $user->email . ' | Password: ' . $plainPassword);
                }
            } else {
                $existingUser->update([
                    'role' => $role,
                    'status_akun' => $userStatusAkun,
                ]);
            }
            
            return redirect()
                ->route('view.tambah.staff')
                ->with('success', 'Data Staff ' . $staff->nama . ' berhasil ditambahkan.');
        } catch (QueryException $e) {
            \Log::error('TambahStaff DB Error: ' . $e->getMessage());
            return back()->withInput()->with('error', $this->translateDbError($e));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('TambahStaff General Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    function ubahStaff(request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        $userAkun = User::where('email', $staff->email)->orWhere('staff_id', $staff->id)->first();
        $jabatanList = Jabatan::orderBy('nama')->pluck('nama');
        return view('Staff.CRUD.ubah-staff', compact('staff', 'userAkun', 'jabatanList'));
    }

    function updateStaff(Request $request, $id)
    {
        try {
            $staff = Staff::findOrFail($id);

            $request->validate(
                [
                    'nama' => 'required|string|max:255',
                    'id_staff' => 'nullable|string',

                    'nik' => 'required|digits:16',
                    'no_kk' => 'required|digits:16',

                    'tempat_lahir' => 'required|string|max:100',
                    'tgl_lahir' => 'required|date',
                    'kelamin' => 'required|boolean',
                    'pendidikan' => 'required|string',
                    'status_kawin' => 'required|string',
                    'anak' => 'nullable|integer|min:0',
                    'tgl_bergabung' => 'required|date',
                    'tgl_resign' => 'nullable|date|after_or_equal:tgl_bergabung',

                    'alamat' => 'required|string',
                    'desa' => 'required|string',
                    'rt' => 'nullable|string|max:3',
                    'rw' => 'nullable|string|max:3',
                    'kota' => 'required|string',
                    'kecamatan' => 'required|string',
                    'provinsi' => 'required|string',

                    'email' => 'required|email',
                    'telp' => 'nullable|string|max:16',
                    'kpj' => 'nullable|string|max:13',
                    'naker' => 'nullable|string|max:13',

                    'nama_rek' => 'nullable|string',
                    'rekening' => 'nullable|string|max:30',

                    'masa_berlaku_pkwt' => 'nullable|date',
                    'perusahaan' => 'required|string',
                    'unit_kerja' => 'required|string',
                    'status_perjanjian_kerja' => 'required|string',
                    'jabatan' => 'required|string',

                    'nama_emergency' => 'required|string|max:255',
                    'telp_emergency' => 'required|string|max:16',
                    'hubungan_emergency' => 'required|string',

                    'ibu_kandung' => 'required|string|max:255',

                    'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

                    'password' => 'nullable|min:8|confirmed',
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

                    //Deskripsi Pekerjaan
                    'masa_berlaku_pkwt' => 'Tanggal PKWT tidak valid',
                    'perusahaan' => 'Nama perusahaan tidak valid',
                    'unit_kerja' => 'Unit Kerja wajib diisi',
                    'status_perjanjian_kerja' => 'Status Perjanjian Kerja wajib dipilih',
                    'jabatan' => 'Jabatan wajib dipilih',

                    // Alamat
                    'alamat.required' => 'Alamat wajib diisi.',
                    'desa.required' => 'Desa wajib diisi.',
                    'rt.string' => 'RT harus berupa angka.',
                    'rw.string' => 'RW harus berupa angka.',
                    'kota.required' => 'Kota wajib diisi.',
                    'kecamatan.required' => 'Kecamatan wajib diisi.',
                    'provinsi.required' => 'Provinsi wajib diisi.',

                    'kpj.required' => 'KPJ wajib diisi.',
                    'kpj' => 'KPJ maksimal 13 karakter',

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

                    'password.min' => 'Password minimal 8 karakter.',
                    'password.confirmed' => 'Konfirmasi password tidak sesuai.',
                ],
            );

            $data = $request->except('foto', '_token', '_method');

            if ($request->remove_foto == '1') {
                $staff->foto = null;
            }

            // ✅ JIKA FOTO DIGANTI
            if ($request->hasFile('foto')) {
                $foto = file_get_contents($request->file('foto')->getRealPath());
                $staff->foto = $foto;
            }

            // ✅ Cari user berdasarkan staff_id (LEBIH AMAN)
            $user = User::where('email', $staff->email)->first();

            // dd($user);

            if (!$user) {
                return back()->with('error', 'User login staff tidak ditemukan.');
            }

            // ✅ Update akun user dengan fuzzy matching jabatan
            $rawJabatan = trim($request->jabatan ?? '');
            $lower = strtolower($rawJabatan);
            $role = !empty($lower) ? $lower : 'staff';
            $userStatusAkun = 0;

            if (str_contains($lower, 'hrd')) {
                $role = 'hrd';
                $userStatusAkun = 1;
            } elseif (str_contains($lower, 'pic')) {
                $role = 'pic';
                $userStatusAkun = 1;
            } elseif (str_contains($lower, 'akuntan') || str_contains($lower, 'akuntansi') || str_contains($lower, 'finance') || str_contains($lower, 'accounting')) {
                $role = 'akuntan';
                $userStatusAkun = 1;
            } elseif (str_contains($lower, 'admin')) {
                $role = 'admin';
                $userStatusAkun = 1;
            } elseif (str_contains($lower, 'head') || str_contains($lower, 'supervisor')) {
                $role = 'head_supervisor';
                $userStatusAkun = 0;
            } else {
                $role = !empty($lower) ? $lower : 'staff';
                $userStatusAkun = 0;
            }

            // Manual override: if admin explicitly ticked akses_login, use that
            if ($request->has('akses_login')) {
                $userStatusAkun = 1;
            } else {
                $userStatusAkun = 0;
            }

            $user = User::updateOrCreate(
                // 1. KUNCI PENCARIAN (Cari user berdasarkan email lama staff)
                ['email' => $staff->email], 
                
                // 2. DATA YANG MAU DIBUAT / DIUPDATE
                [
                    'name'        => $request->nama,
                    'email'       => $request->email, // Akan terupdate jika email di form diganti
                    'role'        => $role,
                    'status_akun' => $userStatusAkun,
                ]
            );

            // ✅ UPDATE PASSWORD
            // Cek apakah kolom password di form diisi. 
            // Jika diisi, gunakan password baru dari form.
            // Jika form kosong TAPI ini user baru dibuat, set default password (misal NIK).
            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            } elseif ($user->wasRecentlyCreated) {
                // Berikan password default (misal 12345678 atau menggunakan NIK) jika staff baru dibuatkan akun
                $user->update([
                    'password' => Hash::make('12345678'), // Ganti sesuai kebijakan perusahaan Anda
                ]);
            }

            // ✅ UPDATE DATA
            $staff->update($data);

            History::create([
                'foreign_id' => $staff->id,
                'nama_tabel' => 'staff', // konsisten
                'updated_by' => auth()->id() ?? 0,
                'jabatan' => optional(auth()->user()->staff)->jabatan ?? 'system',
                'when' => now(),
            ]);

            // ✅ KEMBALI KE DETAIL STAFF (LEBIH BAGUS DARIPADA KE LIST)

            return redirect()->route('view.detail.staff', $id)->with('success', 'Data staff berhasil diperbarui');
        } catch (QueryException $e) {
            \Log::error('UpdateStaff DB Error: ' . $e->getMessage());
            return back()->withInput()->with('error', $this->translateDbError($e));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('UpdateStaff General Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }

    }

    function updateProfilStaff(Request $request, $id) {
        try {
            $staff = Staff::findOrFail($id);

            $request->validate(
                [
                    'nama' => 'required|string|max:255',

                    'email' => 'nullable|email',
                    'telp' => 'nullable|string|max:16',

                    'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

                    'password' => 'nullable|min:8|confirmed',
                ],
                [
                    // Identitas
                    'nama.required' => 'Nama tidak boleh kosong.',
                    'nama.max' => 'Nama maksimal 255 karakter.',

                    // Kontak
                    'email.email' => 'Format email tidak valid.',
                    'telp.max' => 'No telepon maksimal 16 karakter.',

                    // Foto
                    'foto.image' => 'File foto harus berupa gambar.',
                    'foto.mimes' => 'Format foto harus jpg/jpeg/png.',
                    'foto.max' => 'Ukuran foto maksimal 2MB.',

                    'password.min' => 'Password minimal 8 karakter.',
                    'password.confirmed' => 'Konfirmasi password tidak sesuai.',
                ],
            );

            $data = $request->except('foto', '_token', '_method');

            if ($request->remove_foto == '1') {
                $staff->foto = null;
            }

            // ✅ JIKA FOTO DIGANTI
            if ($request->hasFile('foto')) {
                $foto = file_get_contents($request->file('foto')->getRealPath());
                $staff->foto = $foto;
            }

            // ✅ Cari user berdasarkan staff_id (LEBIH AMAN)
            $user = User::where('email', $staff->email)->first();

            // dd($user);

            if (!$user) {
                return back()->with('error', 'User login staff tidak ditemukan.');
            }

            // ✅ Update akun user
            $user->update([
                'name' => $request->nama,
                'email' => $request->email,
            ]);

            // ✅ Jika password diisi
            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($request->password),
                ]);
            }

            // ✅ UPDATE DATA
            $staff->update($data);

            History::create([
                'foreign_id' => $staff->id,
                'nama_tabel' => 'staff', // konsisten
                'updated_by' => auth()->id() ?? 0,
                'jabatan' => optional(auth()->user()->staff)->jabatan ?? 'system',
                'when' => now(),
            ]);

            // ✅ KEMBALI KE DETAIL STAFF (LEBIH BAGUS DARIPADA KE LIST)

            return redirect()->route('view.detail.staff', $id)->with('success', 'Data staff berhasil diperbarui');
        } catch (QueryException $e) {
            \Log::error('UpdateProfilStaff DB Error: ' . $e->getMessage());
            return back()->withInput()->with('error', $this->translateDbError($e));
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Log::error('UpdateProfilStaff General Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.');
        }
    }

    public function toggleStatus($id)
    {
        $staff = Staff::findOrFail($id);

        $staff->status_aktif = !$staff->status_aktif;
        $staff->save();

        History::create([
            'foreign_id' => $staff->id,
            'nama_tabel' => 'staff', // konsisten
            'updated_by' => auth()->id() ?? 0,
            'jabatan' => optional(auth()->user()->staff)->jabatan ?? 'system',
            'when' => now(),
        ]);

        return response()->json([
            'message' => $staff->status_aktif
                ? 'Staff berhasil diaktifkan'
                : 'Staff berhasil dinonaktifkan'
        ]);
    }

    public function importExcel(Request $request)
    {
        // 1. Validasi File Excel
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:10240'
        ], [
            'file_excel.required' => 'Pilih file Excel terlebih dahulu!',
            'file_excel.mimes'    => 'Format file harus berupa .xlsx, .xls, atau .csv!',
            'file_excel.max'      => 'Ukuran file maksimal 10MB.',
        ]);

        DB::beginTransaction();
        try {
            // 2. Eksekusi Import
            // Kita pass object request ke import class, karena kita mungkin butuh session untuk flash akun_info
            Excel::import(new StaffImport, $request->file('file_excel'));
            
            DB::commit(); 
            
            return redirect()->back()->with('success', 'Data Staff beserta Akun berhasil di-import ke sistem!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            
            $failures = [];
            foreach ($e->failures() as $failure) {
                $failures[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            $errorString = 'Kesalahan format: ' . implode(' | ', $failures);    

            return redirect()->back()->with('error', $errorString);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('ImportExcel General Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat import data. Silakan coba lagi atau hubungi administrator.');
        }
    }
}
