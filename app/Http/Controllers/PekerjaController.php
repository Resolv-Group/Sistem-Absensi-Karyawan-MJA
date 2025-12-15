<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pekerja;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\History;
use Illuminate\Support\Facades\Auth;

class PekerjaController extends Controller
{
    // function viewPekerjaMain()
    // {
    //     $pekerja = Pekerja::where('status_aktif', 1)->orderby('created_at', 'desc')->paginate(3);
    //     $totalPekerja = Pekerja::count(); // total pekerja
    //     $pekerjaBaru = Pekerja::where('created_at', '>=', now()->subMonth())->count(); // pekerja baru dari bulan lalu
    //     $tidakAktif = Pekerja::where('status_aktif', '!=', '1')->count(); // pekerja tidak aktif

    //     return view('Pekerja.main-pekerja', compact('pekerja', 'totalPekerja', 'pekerjaBaru', 'tidakAktif'));
    // }

    public function viewPekerjaMain(Request $request)
    {
        $totalPekerja = Pekerja::count(); // total pekerja
        $pekerjaBaru = Pekerja::where('created_at', '>=', now()->subMonth())->count(); // pekerja baru dari bulan lalu
        $tidakAktif = Pekerja::where('status_aktif', '!=', '1')->count(); // pekerja tidak aktif

        // 1. Capture the search query
        $q = $request->input('q');

        // 2. Query the database with the filter
        $pekerja = Pekerja::when($q, function ($query) use ($q) {
            $query->where('nama', 'LIKE', "%$q%")->orWhere('nik', 'LIKE', "%$q%");
        })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString(); // Keeps the search term in the pagination links

        // 3. If it's an AJAX request (from JS), return ONLY the table partial
        if ($request->ajax()) {
            return view('pekerja.partials.pekerja-table', compact('pekerja'))->render();
        }

        // 4. Otherwise, return the full page (header, sidebar, etc)
        return view('pekerja.main-pekerja', compact('pekerja', 'totalPekerja', 'pekerjaBaru', 'tidakAktif'));
    }

    function viewTambahPekerja()
    {
        return view('Pekerja.CRUD.tambah-pekerja');
    }

    function viewDetailPekerja($id)
    {
        $pekerja = Pekerja::where('id', $id)->first();

        $pekerja->image_base64 = 'data:image/jpeg;base64,' . base64_encode($pekerja->image_blob);

        $historiPekerja = History::where('foreign_id', $id)->where('nama_tabel', 'pekerja')->get();

        return view('Pekerja.detail-pekerja', compact('pekerja', 'historiPekerja'));
    }

    function tambahPekerja(request $request)
    {
        // dd($request->all());
        try {
            $request->validate(
                [
                    'nama' => 'required|string|max:255',
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
                    'rt' => 'nullable|integer',
                    'rw' => 'nullable|integer',
                    'kota' => 'required|string',
                    'kecamatan' => 'required|string',
                    'provinsi' => 'required|string',

                    'email' => 'nullable|email',
                    'telp' => 'nullable|string|max:16',

                    'nama_rek' => 'nullable|string',
                    'rekening' => 'nullable|string|max:30',

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
            $pekerja = Pekerja::create([
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

                'nama_emergency' => $request->nama_emergency,
                'telp_emergency' => $request->telp_emergency,
                'hubungan_emergency' => $request->hubungan_emergency,
                'ibu_kandung' => $request->ibu_kandung,

                'foto' => $fotoBlob,

                'status_aktif' => 1,
            ]);

            return redirect()
                ->route('view.tambah.pekerja')
                ->with('success', 'Data Pekerja ' . $pekerja->nama . ' berhasil ditambahkan.');
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

    function ubahPekerja(request $request, $id)
    {
        $pekerja = Pekerja::findOrFail($id);

        return view('Pekerja.CRUD.ubah-pekerja', compact('pekerja'));
    }

    public function updatePekerja(Request $request, $id)
    {
        $pekerja = Pekerja::findOrFail($id);

        $request->validate(
            [
                'nama' => 'required|string|max:255',

                'nik' => ['required', 'digits:16', Rule::unique('pekerja', 'nik')->ignore($id)],

                'no_kk' => ['required', 'digits:16', Rule::unique('pekerja', 'no_kk')->ignore($id)],

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
    }
}
