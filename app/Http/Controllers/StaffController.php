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
    function viewStaffMain(){
        $staff = Staff::where('status_aktif', 1)->paginate(5);
        $totalStaff = Staff::count(); // total pekerja
        $staffBaru  = Staff::where('created_at', '>=', now()->subMonth())->count(); // pekerja baru dari bulan lalu
        $tidakAktif   = Staff::where('status_aktif', '!=', '1')->count(); // pekerja tidak aktif

        return view('Staff.main-staff', compact('staff', 'totalStaff', 'staffBaru', 'tidakAktif'));
    }

    function viewTambahStaff() {
        return view('Staff.CRUD.tambah-staff');
    }

    function viewDetailStaff() {
        return view('Staff.detail-staff');
    }

        function tambahStaff(request $request)
    {
        // dd($request->all());
        try {
            $request->validate(
            [
                'nama' => 'required|string|max:255',
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

                'nama_rek' => 'nullable|string',
                'rekening' => 'nullable|string|max:30',

                'masa_berlaku_pkwt' => 'nullable|date',
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

        // ✅ Simpan ke database
        $staff = Staff::create([
            'nama' => $request->nama,
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

            'rekening' => $request->rekening,
            'nama_rek' => $request->nama_rek,

            'masa_berlaku_pkwt' => $request->masa_berlaku_pkwt,
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
        ];

        // Cek apakah jabatan masuk daftar role login
        if (array_key_exists($request->jabatan, $roleMapping)) {

            $plainPassword = Carbon::parse($request->tgl_lahir)->format('d-m-Y');

            $password = Hash::make($plainPassword);

            $user = User::create([
                'name'     => $staff->nama,
                'email'    => $staff->email,
                'password' => $password,
                'role'     => $roleMapping[$request->jabatan],
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
            // dd('Database Error Occurred:', $e->getMessage());
        } catch (\Exception $e) {
            dd('General Error:', $e->getMessage());
        }
    }
}
