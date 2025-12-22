<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\History;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    // function viewStaffMain(Request $request)
    // {
    //     $totalStaff = Staff::count(); // total pekerja
    //     $staffBaru = Staff::where('created_at', '>=', now()->subMonth())->count(); // pekerja baru dari bulan lalu
    //     $tidakAktif = Staff::where('status_aktif', '!=', '1')->count(); // pekerja tidak aktif

    //     // 1. Capture the search query
    //     $q = $request->input('q');

    //     // 2. Query the database with the filter
    //     $staff = Staff::when($q, function ($query) use ($q) {
    //         $query
    //             ->where('nama', 'LIKE', "%$q%")
    //             ->orWhere('nik', 'LIKE', "%$q%")
    //             ->orWhere('kpj', 'LIKE', "%$q%")
    //             ->Where('status_aktif', 1);
    //     })
    //         ->orderBy('created_at', 'desc')
    //         ->paginate(10)
    //         ->withQueryString(); // Keeps the search term in the pagination links

    //     // 3. If it's an AJAX request (from JS), return ONLY the table partial
    //     if ($request->ajax()) {
    //         return view('staff.partials.staff-table', compact('staff'))->render();
    //     }
    //     return view('Staff.main-staff', compact('staff', 'totalStaff', 'staffBaru', 'tidakAktif'));
    // }
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
            return view('staff.partials.staff-table', compact('staff'))->render();
        }

        // Otherwise return the full page
        return view('Staff.main-staff', compact('staff', 'totalStaff', 'staffBaru', 'tidakAktif'));

    }

    function viewTambahStaff()
    {
        return view('Staff.CRUD.tambah-staff');
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
                    'rt' => 'nullable|integer',
                    'rw' => 'nullable|integer',
                    'kota' => 'required|string',
                    'kecamatan' => 'required|string',
                    'provinsi' => 'required|string',

                    'email' => 'nullable|email',
                    'telp' => 'nullable|string|max:16',
                    'kpj' => 'required|string|max:13',

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

                    'ibu_kandung' => 'string|max:255',

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
                    'rt.integer' => 'RT harus berupa angka.',
                    'rw.integer' => 'RW harus berupa angka.',
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

            $roleMapping = [
                'PIC' => 'pic',
                'Akuntan' => 'akuntan',
                'HRD' => 'hrd',
                'Staff' => 'staff',
                'IT' => 'it',
                'Supervisor' => 'supervisor',
                'Manager' => 'manager',
                'Direktur' => 'direktur'
            ];

            // Cek apakah jabatan masuk daftar role login
            if (array_key_exists($request->jabatan, $roleMapping)) {
                $plainPassword = Carbon::parse($request->tgl_lahir)->format('d-m-Y');

                $password = Hash::make($plainPassword);

                $user = User::create([
                    'name' => $staff->nama,
                    'email' => $staff->email,
                    'password' => $password,
                    'role' => $roleMapping[$request->jabatan],
                    'staff_id' => $staff->id,
                ]);

                // (OPSIONAL) FLASH VIEW
                session()->flash('akun_info', 'Akun dibuat! Username: ' . $user->email . ' | Password: ' . $plainPassword);
            }

            return redirect()
                ->route('view.tambah.staff')
                ->with('success', 'Data Staff ' . $staff->nama . ' berhasil ditambahkan.');
        } catch (QueryException $e) {
            // If the DB fails, we catch it here so Laravel doesn't try to render the blob
            // We only return the text message, not the binary data
            dd('Database Error Occurred:', $e->getMessage());
        } catch (\Exception $e) {
            dd('General Error:', $e->getMessage());
        }
    }

    function ubahStaff(request $request, $id)
    {
        $staff = Staff::findOrFail($id);
        return view('Staff.CRUD.ubah-staff', compact('staff'));
    }

    function updateStaff(Request $request, $id)
    {
        try {
            $staff = Staff::findOrFail($id);

            $request->validate(
                [
                    'nama' => 'required|string|max:255',
                    'id_staff' => 'nullable|string',

                    'nik' => ['required', 'digits:16', Rule::unique('staff', 'nik')->ignore($id)],

                    'no_kk' => ['required', 'digits:16', Rule::unique('staff', 'no_kk')->ignore($id)],

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
                    'rt' => 'nullable|integer',
                    'rw' => 'nullable|integer',
                    'kota' => 'required|string',
                    'kecamatan' => 'required|string',
                    'provinsi' => 'required|string',

                    'email' => 'nullable|email',
                    'telp' => 'nullable|string|max:16',
                    'kpj' => 'required|string|max:13',

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

                    'ibu_kandung' => 'string|max:255',

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
                    'rt.integer' => 'RT harus berupa angka.',
                    'rw.integer' => 'RW harus berupa angka.',
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
            // If the DB fails, we catch it here so Laravel doesn't try to render the blob
            // We only return the text message, not the binary data
            dd('Database Error Occurred:', $e->getMessage());
        } catch (\Exception $e) {
            dd('General Error:', $e->getMessage());
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
            // If the DB fails, we catch it here so Laravel doesn't try to render the blob
            // We only return the text message, not the binary data
            dd('Database Error Occurred:', $e->getMessage());
        } catch (\Exception $e) {
            dd('General Error:', $e->getMessage());
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
}
