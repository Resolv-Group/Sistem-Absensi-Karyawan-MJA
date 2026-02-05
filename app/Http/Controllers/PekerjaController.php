<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pekerja;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\History;
use App\Models\Penilaian_Pkwt;
use App\Models\PKWT;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PekerjaController extends Controller
{
    public function viewPekerjaMain(Request $request)
    {
        // --- 1. CALCULATE STATS (Top Cards) ---
        $totalPekerja = Pekerja::count();
        $pekerjaBaru  = Pekerja::whereMonth('created_at', Carbon::now()->month)
                            ->whereYear('created_at', Carbon::now()->year)
                            ->count();
        $tidakAktif   = Pekerja::where('status_aktif', '!=', '1')->count();


        // --- 2. BUILD QUERY ---
        $query = Pekerja::with(['pkwtAktif']);

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
        $pekerja = $query->orderBy('created_at', 'desc')
                        ->paginate(10)
                        ->withQueryString();


        // --- 4. RETURN RESPONSE ---

        // If AJAX request (from the search/filter script), return ONLY the table partial
        if ($request->ajax()) {
            return view('pekerja.partials.pekerja-table', compact('pekerja'))->render();
        }

        // Otherwise return the full page
        return view('pekerja.main-pekerja', compact('pekerja', 'totalPekerja', 'pekerjaBaru', 'tidakAktif'));
    }

    function viewTambahPekerja()
    {
        return view('Pekerja.CRUD.tambah-pekerja');
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
                    'id_pekerja' => 'nullable|string',
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
                    'kpj' => 'nullable|string|max:11',
                    'naker' => 'nullable|string|max:13',

                    'nama_rek' => 'nullable|string',
                    'rekening' => 'nullable|string|max:30',

                    'nama_emergency' => 'required|string|max:255',
                    'telp_emergency' => 'required|string|max:16',
                    'hubungan_emergency' => 'required|string',

                    'ibu_kandung' => 'string|max:255',

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

            // $dokumenBlob = null;
            // if ($request->hasFile('dokumen')) {
            //     $dokumenBlob = file_get_contents($request->file('dokumen')->getRealPath());
            // }

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
                'id_pekerja' => 'nullable|string',

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
                'kpj' => 'nullable|string|max:11',
                'naker' => 'nullable|string|max:13',

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




}
