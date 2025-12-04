@extends('layout')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- HEADER SECTION --}}
    <div class="mb-8">
        <nav class="flex text-sm font-medium text-gray-500 mb-2">
            <a href="{{route('view.staff')}}" class="hover:text-gray-700 transition">Staff</a>
            <span class="mx-2 text-gray-400">/</span>
            <span class="text-blue-600">Tambah</span>
        </nav>

        <div class="flex items-center gap-4">
            <a href="{{route('view.staff')}}" class="group p-2 rounded-full border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 transform group-hover:-translate-x-0.5 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Staff</h1>
                <p class="text-sm text-gray-500 mt-1">Isi formulir di bawah untuk mendaftarkan staff baru.</p>
            </div>
        </div>
    </div>

    {{-- FORM CARD --}}
    <form action="" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        @csrf

        {{-- SECTION 1: Identitas Pribadi --}}
        <div class="p-8">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Identitas Pribadi</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                {{-- Foto Profil (Left Side) --}}
                <div class="md:col-span-3">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Foto Profil</label>
                    <div class="relative w-full aspect-square bg-gray-50 rounded-xl border-2 border-dashed border-gray-400 hover:border-blue-500 hover:bg-blue-50 transition flex flex-col items-center justify-center text-center cursor-pointer group">
                        <input type="file" name="foto_profil" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                        <svg class="mx-auto h-10 w-10 text-gray-400 group-hover:text-blue-500 transition" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="mt-2 text-xs text-gray-600 group-hover:text-gray-800">
                            <span class="font-bold text-blue-600">Upload Foto</span>
                            <p class="mt-1 font-medium">Max 2MB</p>
                        </div>
                    </div>
                </div>

                {{-- Fields (Right Side) --}}
                <div class="md:col-span-9 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">

                    {{-- Nama Lengkap --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" maxlength="255" autocomplete="off"
                               class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                               placeholder="Sesuai KTP">
                    </div>

                    {{-- NIK --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">NIK</label>
                        <input type="text" name="nik" maxlength="17" autocomplete="off"
                               class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                               placeholder="16 Digit Angka">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Kartu Keluarga</label>
                        <input type="text" name="no_kk" maxlength="17" autocomplete="off"
                               class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                               placeholder="16 Digit Angka">
                    </div>

                    {{-- Tempat Lahir --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" maxlength="100" autocomplete="off"
                               class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                               placeholder="Kota Kelahiran">
                    </div>

                    {{-- Tanggal Lahir --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir"
                               class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                    </div>

                    {{-- Jenis Kelamin --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Jenis Kelamin</label>
                        <select name="jenis_kelamin"
                                class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                            <option value="1">Laki-laki</option>
                            <option value="0">Perempuan</option>
                        </select>
                    </div>

                    {{-- Pendidikan --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Pendidikan</label>
                        <select name="pendidikan"
                                class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                            <option value="TK">TK</option>
                            <option value="SD">SD</option>
                            <option value="SMP">SMP</option>
                            <option value="SMA">SMA/SMK</option>
                            <option value="D3">D3</option>
                            <option value="S1">S1</option>
                        </select>
                    </div>

                    {{-- Status Perkawinan --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Status Perkawinan</label>
                        <select name="status_perkawinan"
                                class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                            <option value="Belum Menikah">Belum Menikah</option>
                            <option value="Menikah">Menikah</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Jumlah Anak</label>
                        <input type="text" name="anak" maxlength="2" autocomplete="off"
                               class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                               placeholder="Kota Kelahiran" value="0">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Bergabung</label>
                        <input type="date" name="tanggal_gabung"
                               class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Resign</label>
                        <input type="date" name="tanggal_resign"
                               class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- SECTION 2: Alamat Domisili --}}
        <div class="p-8">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-900">Alamat Domisili</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                {{-- Jalan --}}
                <div class="sm:col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-1">Jalan / Nama Gedung</label>
                    <textarea name="jalan" rows="2" maxlength="255" autocomplete="off"
                              class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 min-h-16 max-h-40 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                              placeholder="Jl. ABC No. 10, Blok A"></textarea>
                </div>

                {{-- Kelurahan --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kelurahan / Desa</label>
                    <input type="text" name="kelurahan" maxlength="100" autocomplete="off"
                           class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                </div>

                {{-- RT / RW --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">RT</label>
                        <input type="text" name="rt" maxlength="3" autocomplete="off"
                               class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">RW</label>
                        <input type="text" name="rw" maxlength="3" autocomplete="off"
                               class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                    </div>
                </div>

                {{-- Kota --}}
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-1">Kota / Kabupaten</label>
                    <input type="text" name="kota" maxlength="100" autocomplete="off"
                           class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                </div>

                {{-- Kecamatan & Provinsi --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kecamatan</label>
                        <input type="text" name="kecamatan" maxlength="100" autocomplete="off"
                            class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Provinsi</label>
                        <input type="text" name="provinsi" maxlength="100" autocomplete="off"
                            class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                    </div>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100"></div>

        {{-- SECTION 3 & 4 Combined Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2">

            {{-- Kontak & Rekening --}}
            <div class="p-8 border-b md:border-b-0 md:border-r border-gray-100">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Kontak & Rekening</h2>
                </div>

                <div class="space-y-5">
                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Email Pribadi</label>
                        <input type="email" name="email" maxlength="255" autocomplete="off"
                               class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                    </div>

                    {{-- No Telepon --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="no_telepon" maxlength="16" autocomplete="off"
                               class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                    </div>

                    {{-- Bank Info --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Bank</label>
                            <select name="nama_bank"
                                    class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                                <option value="BCA">BCA</option>
                                <option value="Mandiri">Mandiri</option>
                                <option value="BRI">BRI</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">No. Rekening</label>
                            <input type="text" name="no_rekening" maxlength="20" autocomplete="off"
                                   class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kontak Darurat --}}
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Kontak Darurat</h2>
                </div>

                <div class="space-y-5">
                    {{-- Nama Kontak --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Kontak</label>
                        <input type="text" name="kontak_darurat_nama" maxlength="255" autocomplete="off"
                               class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                    </div>

                    {{-- No Kontak --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="kontak_darurat_telepon" maxlength="16" autocomplete="off"
                               class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                    </div>

                    {{-- Hubungan --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Hubungan</label>
                        <select name="kontak_darurat_hubungan"
                                class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                            <option value="Orang Tua">Orang Tua</option>
                            <option value="Saudara">Saudara</option>
                            <option value="Pasangan">Pasangan</option>
                        </select>
                    </div>

                    {{-- Ibu Nama --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Ibu Kandung</label>
                        <input type="text" name="atas_nama" maxlength="255" autocomplete="off"
                               class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                    </div>
                </div>
            </div>
        </div>

        {{-- FOOTER / ACTIONS --}}
        <div class="bg-gray-50 px-8 py-5 flex items-center justify-end gap-3 border-t border-gray-200">
            <button type="button" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition shadow-sm">
                Batalkan
            </button>
            <button type="submit" name="action" value="add_more" class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah
            </button>
            <button type="submit" name="action" value="save" class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Simpan
            </button>
        </div>

    </form>
</div>
@endsection
