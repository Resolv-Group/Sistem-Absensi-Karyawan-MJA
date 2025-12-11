@extends('layout')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER SECTION --}}
        <div class="mb-8">
            <nav class="flex text-sm font-medium text-gray-500 mb-2">
                <a href="/daftar-staff" class="hover:text-gray-700 transition">Staff</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-blue-600">Tambah</span>
            </nav>

            <div class="flex items-center gap-4">
                <a href="/daftar-staff"
                    class="group p-2 rounded-full border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 transform group-hover:-translate-x-0.5 transition" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Staff</h1>
                    <p class="text-sm text-gray-500 mt-1">Isi formulir di bawah untuk mendaftarkan staff baru.</p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 p-4 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM CARD --}}
        <form id="formTambahStaff" action="{{ route('tambah.staff.post') }}" method="POST" enctype="multipart/form-data"
            class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            @csrf

            {{-- SECTION 1: Identitas Pribadi --}}
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Identitas Pribadi</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-8">
                    {{-- Foto Profil (Left Side) --}}
                    <div class="md:col-span-3">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Foto Profil</label>

                        <div class="relative w-full aspect-square rounded-xl border-2 border-dashed border-gray-400 hover:border-blue-500 hover:bg-blue-50 transition overflow-hidden flex items-center justify-center bg-gray-50 cursor-pointer group"
                            onclick="document.getElementById('fotoInput').click()">

                            <!-- INPUT FILE -->
                            <input type="file" id="fotoInput" name="foto" accept="image/*" class="hidden"
                                onchange="previewPhoto(event)">

                            <!-- PLACEHOLDER -->
                            <div id="placeholder" class="text-center pointer-events-none">
                                <svg class="mx-auto h-10 w-10 text-gray-400 group-hover:text-blue-500 transition"
                                    stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>

                                <p class="mt-2 text-xs text-gray-600">
                                    <span class="font-bold text-blue-600">Upload Foto</span><br>
                                    Max 2MB
                                </p>
                            </div>

                            <!-- PREVIEW -->
                            <img id="previewImage" class="absolute inset-0 w-full h-full object-cover hidden"
                                alt="Preview Foto">

                            <!-- DELETE BUTTON -->
                            <button type="button" id="removeBtn" onclick="removePhoto(event)"
                                class="absolute top-2 right-2 bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center shadow-md hover:bg-red-700 transition hidden">
                                ✕
                            </button>

                        </div>
                    </div>

                    {{-- Fields (Right Side) --}}
                    <div class="md:col-span-9 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">

                        {{-- Nama Lengkap --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama" maxlength="255" autocomplete="off"
                                class="nama-input w-full rounded-lg shadow-sm border
                            @error('nama') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                        text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400
                        focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                placeholder="Sesuai KTP" value="{{ old('nama') }}">
                            @error('nama')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- NIK --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">NIK</label>

                            <input type="text" name="nik" maxlength="16" autocomplete="off"
                                class="nik-input w-full rounded-lg shadow-sm border
                                @error('nik') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                                text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400
                                focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition"
                                placeholder="16 Digit Angka" value="{{ old('nik') }}">

                            @error('nik')
                                <p class="error-nik text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Kartu Keluarga</label>
                            <input type="text" name="no_kk" maxlength="16" autocomplete="off"
                                class="no_kk-input w-full rounded-lg shadow-sm border
                            @error('no_kk') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                            border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                placeholder="16 Digit Angka" value="{{ old('no_kk') }}">
                            @error('no_kk')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tempat Lahir --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" maxlength="100" autocomplete="off"
                                class="w-full rounded-lg shadow-sm
                            @error('tempat_lahir') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                            border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                placeholder="Kota Kelahiran" value="{{ old('tempat_lahir') }}">
                            @error('tempat_lahir')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir"
                                class="tanggal-input w-full rounded-lg shadow-sm cursor-pointer
                            @error('tgl_lahir') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                            border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                placeholder="Kota Kelahiran" value="{{ old('tgl_lahir') }}">
                            @error('tgl_lahir')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div x-data="{ open: false, selected: '{{ old('kelamin') }}' || '', list: [{ val: '1', label: 'Laki-laki' }, { val: '0', label: 'Perempuan' }] }" class="relative">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Jenis Kelamin</label>

                            <input type="hidden" name="kelamin" x-model="selected">

                            <div @click="open=!open"
                                class="border border-gray-500 bg-gray-50 rounded-lg py-2.5 px-3 cursor-pointer flex justify-between items-center">
                                <span x-text="list.find(x=>x.val==selected)?.label || 'Pilih Jenis Kelamin'"></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            <ul x-show="open" @click.outside="open=false"
                                class="absolute w-full mt-1 border border-gray-300 bg-white rounded-lg shadow-md overflow-y-auto max-h-40 z-50">
                                <template x-for="item in list" :key="item.val">
                                    <li @click="selected=item.val; open=false"
                                        class="px-3 py-2 hover:bg-blue-600 hover:text-white cursor-pointer transition"
                                        x-text="item.label">
                                    </li>
                                </template>
                            </ul>
                        </div>


                        {{-- Pendidikan --}}
                        <div x-data="{ open: false, selected: '{{ old('pendidikan') }}' || '', list: ['TK', 'SD', 'SMP', 'SMA/SMK', 'D3', 'S1'] }" class="relative">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Pendidikan</label>

                            <input type="hidden" name="pendidikan" x-model="selected">

                            <div @click="open=!open"
                                class="border border-gray-500 bg-gray-50 rounded-lg py-2.5 px-3 cursor-pointer flex justify-between items-center">
                                <span x-text="selected || 'Pilih Pendidikan'"></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            <ul x-show="open" @click.outside="open=false"
                                class="absolute w-full mt-1 border border-gray-300 bg-white rounded-lg shadow-md overflow-y-auto max-h-40 z-50">
                                <template x-for="item in list" :key="item">
                                    <li @click="selected=item; open=false"
                                        class="px-3 py-2 hover:bg-blue-600 hover:text-white cursor-pointer transition"
                                        x-text="item">
                                    </li>
                                </template>
                            </ul>
                        </div>


                        {{-- Status Perkawinan --}}
                        <div x-data="{ open: false, selected: '{{ old('status_kawin') }}' || '', list: ['Belum Menikah', 'Menikah'] }" class="relative">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Status Perkawinan</label>

                            <input type="hidden" name="status_kawin" x-model="selected">

                            <div @click="open=!open"
                                class="border border-gray-500 bg-gray-50 rounded-lg py-2.5 px-3 cursor-pointer flex justify-between items-center">
                                <span x-text="selected || 'Pilih Status'"></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            <ul x-show="open" @click.outside="open=false"
                                class="absolute w-full mt-1 border border-gray-300 bg-white rounded-lg shadow-md overflow-y-auto max-h-40 z-50">
                                <template x-for="item in list" :key="item">
                                    <li @click="selected=item; open=false"
                                        class="px-3 py-2 hover:bg-blue-600 hover:text-white cursor-pointer transition"
                                        x-text="item">
                                    </li>
                                </template>
                            </ul>
                        </div>


                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Jumlah Anak</label>
                            <input type="text" name="anak" maxlength="2" autocomplete="off"
                                class="anak-input w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                placeholder="0" value="{{ old('anak') }}">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Bergabung</label>
                            <input type="date" name="tgl_bergabung"
                                class="tanggal-input w-full rounded-lg shadow-sm cursor-pointer
                            @error('tgl_bergabung') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                            border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                placeholder="Kota Kelahiran" value="{{ old('tgl_bergabung', date('Y-m-d')) }}">
                            @error('tgl_bergabung')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Resign</label>
                            <input type="date" name="tgl_resign"
                                class="tanggal-input w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition cursor-pointer"
                                value="{{ old('tgl_resign') }}">
                        </div>

                    </div>
                </div>
            </div>
            <div class="border-t border-gray-100"></div>

            {{-- SECTION 2: Deskripsi Pekerjaan --}}
            <div class="p-8">
                {{-- Section Header --}}
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Deskripsi Pekerjaan</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">

                    {{-- 1. Jabatan --}}
                    <div x-data="{ open: false, selected: '{{ old('jabatan') }}' || '', list: ['Staff', 'Supervisor', 'Manager', 'Direktur', 'HRD', 'IT', 'Akuntan'] }" class="relative">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Jabatan</label>

                        <input type="hidden" name="jabatan" x-model="selected">

                        <div @click="open=!open"
                            class="border border-gray-500 bg-gray-50 rounded-lg py-2.5 px-3 cursor-pointer flex justify-between items-center select-none shadow-sm hover:border-blue-500 hover:ring-1 hover:ring-blue-200 transition">
                            <span x-text="selected || 'Pilih Jabatan'"
                                :class="selected ? 'text-gray-900' : 'text-gray-400'"></span>
                            <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': open }" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>

                        <ul x-show="open" @click.outside="open=false" x-transition.opacity.duration.200ms
                            class="absolute w-full mt-1 border border-gray-200 bg-white rounded-lg shadow-xl overflow-y-auto max-h-60 z-50">
                            <template x-for="item in list" :key="item">
                                <li @click="selected=item; open=false"
                                    class="px-3 py-2.5 text-sm hover:bg-blue-50 hover:text-blue-700 cursor-pointer transition border-b border-gray-50 last:border-0"
                                    x-text="item">
                                </li>
                            </template>
                        </ul>
                        @error('jabatan')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 2. Unit Kerja & Nama Perusahaan --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Penempatan PT (Unit Kerja)</label>
                            <input type="text" name="unit_kerja" maxlength="100" autocomplete="off"
                                class="w-full rounded-lg shadow-sm
                                @error('unit_kerja') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                                border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                placeholder="-" value="{{ old('unit_kerja') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Perusahaan</label>
                            <input type="text" name="perusahaan" maxlength="100" autocomplete="off"
                                class="w-full rounded-lg shadow-sm
                                @error('perusahaan') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                                border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                placeholder="cth: PT.MJA" value="{{ old('perusahaan') }}">
                        </div>
                    </div>

                    {{-- 3. Masa Berlaku PKWT --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Masa Berlaku PKWT</label>
                        <input type="date" name="masa_berlaku_pkwt"
                            class="tanggal-input w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition cursor-pointer"
                            value="{{ old('masa_berlaku_pkwt') }}">
                    </div>

                    {{-- 4. Status Karyawan --}}
                    <div x-data="{ open: false, selected: '{{ old('status_perjanjian_kerja') }}' || '', list: ['Tetap (Permanent)', 'Kontrak (PKWT)', 'Probation', 'Magang / Internship'] }" class="relative">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Status Karyawan</label>

                        <input type="hidden" name="status_perjanjian_kerja" x-model="selected">

                        <div @click="open=!open"
                            class="border border-gray-500 bg-gray-50 rounded-lg py-2.5 px-3 cursor-pointer flex justify-between items-center select-none shadow-sm hover:border-blue-500 hover:ring-1 hover:ring-blue-200 transition">
                            <span x-text="selected || 'Pilih Status'"
                                :class="selected ? 'text-gray-900' : 'text-gray-400'"></span>
                            <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': open }" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>

                        <ul x-show="open" @click.outside="open=false" x-transition.opacity.duration.200ms
                            class="absolute w-full mt-1 border border-gray-200 bg-white rounded-lg shadow-xl overflow-y-auto max-h-60 z-50">
                            <template x-for="item in list" :key="item">
                                <li @click="selected=item; open=false"
                                    class="px-3 py-2.5 text-sm hover:bg-blue-50 hover:text-blue-700 cursor-pointer transition border-b border-gray-50 last:border-0"
                                    x-text="item">
                                </li>
                            </template>
                        </ul>
                    </div>

                </div>
            </div>

            <div class="border-t border-gray-100"></div>

            {{-- SECTION 3: Alamat Domisili --}}
            <div class="p-8">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                    <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900">Alamat Domisili</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                    {{-- Jalan --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Jalan / Nama Gedung</label>
                        <textarea name="alamat" rows="2" maxlength="255" autocomplete="off"
                            class="w-full rounded-lg shadow-sm
                            @error('alamat') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                            border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 min-h-16 max-h-40 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                            placeholder="Jl. ABC No. 10, Blok A">{{ old('alamat') }}</textarea>
                        @error('alamat')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Desa --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kelurahan / Desa</label>
                        <input type="text" name="desa" maxlength="100" autocomplete="off"
                            class="w-full rounded-lg shadow-sm
                        @error('desa') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                        border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                            value="{{ old('desa') }}" placeholder="cth: Merjosari">
                        @error('desa')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- RT / RW --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">RT</label>
                            <input type="text" name="rt" maxlength="3" autocomplete="off"
                                class="rt-input w-full rounded-lg shadow-sm border
                                border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                value="{{ old('rt') }}" placeholder="cth: 1">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">RW</label>
                            <input type="text" name="rw" maxlength="3" autocomplete="off"
                                class="rw-input w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                value="{{ old('rw') }}" placeholder="cth: 2">
                        </div>
                    </div>

                    {{-- Kota --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kota / Kabupaten</label>
                        <input type="text" name="kota" maxlength="100" autocomplete="off"
                            class="w-full rounded-lg shadow-sm
                            @error('kota') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                            border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                            value="{{ old('kota') }}" placeholder="cth: Malang">
                        @error('kota')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Kecamatan & Provinsi --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Kecamatan</label>
                            <input type="text" name="kecamatan" maxlength="100" autocomplete="off"
                                class="w-full rounded-lg shadow-sm @error('kecamatan') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                                border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                value="{{ old('kecamatan') }}" placeholder="cth: Lowokwaru">
                            @error('kecamatan')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Provinsi</label>
                            <input type="text" name="provinsi" maxlength="100" autocomplete="off"
                                class="w-full rounded-lg shadow-sm
                            @error('provinsi') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                            border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                value="{{ old('provinsi') }}" placeholder="cth: Jawa Timur">
                            @error('provinsi')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
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
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">Kontak & Rekening</h2>
                    </div>

                    <div class="space-y-5">
                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Email Pribadi</label>
                            <input type="email" name="email" maxlength="255" autocomplete="off"
                                class="email-input w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 transition"
                                value="{{ old('email') }}" placeholder="emailpribadi@example.com">
                            <p class="email-error text-red-600 text-xs mt-1 hidden">Format email tidak valid</p>
                        </div>


                        {{-- No Telepon --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Telepon Pribadi</label>
                            <input type="text" name="telp" maxlength="13" autocomplete="off"
                                placeholder="08123xxxx"
                                class="telp_pribadi-input w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                value="{{ old('telp') }}">
                        </div>

                        {{-- Bank Info --}}
                        <div class="grid grid-cols-2 gap-4">

                            <!-- CUSTOM DROPDOWN BANK -->
                            <div x-data="{ open: false, selected: '', banks: ['BCA', 'BRI', 'BNI', 'Mandiri', 'CIMB Niaga', 'BTN', 'Bank Permata', 'Bank Danamon', 'Bank Mega', 'Panin Bank', 'OCBC NISP', 'Maybank Indonesia', 'BSI', 'Bank Jago', 'SeaBank', 'Bank Neo Commerce'] }" class="relative">

                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Bank</label>

                                <!-- Input Hidden untuk submit k ke backend -->
                                <input type="hidden" name="nama_rek" x-model="selected">

                                <!-- Trigger dropdown -->
                                <div @click="open=!open"
                                    class="border border-gray-500 bg-gray-50 rounded-lg py-2.5 px-3 cursor-pointer flex justify-between items-center select-none">
                                    <span x-text="selected || 'Pilih Bank'"></span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>

                                <!-- Dropdown List -->
                                <ul x-show="open" @click.outside="open=false"
                                    class="absolute w-full mt-1 border border-gray-300 bg-white rounded-lg shadow-md overflow-y-auto max-h-40 z-50">
                                    <template x-for="bank in banks" :key="bank">
                                        <li @click="selected = bank; open=false"
                                            class="px-3 py-2 hover:bg-blue-600 hover:text-white cursor-pointer transition">
                                            <span x-text="bank"></span>
                                        </li>
                                    </template>
                                </ul>

                            </div>



                            <!-- NOMOR REKENING (tetap seperti punyamu) -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">No. Rekening</label>
                                <input type="text" name="rekening" maxlength="16" autocomplete="off"
                                    placeholder="7823xxxxx"
                                    class="rekening-input w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                    placeholder="Nomor Rekening" value="{{ old('rekening') }}">
                            </div>

                        </div>

                    </div>
                </div>

                {{-- Kontak Darurat --}}
                <div class="p-8">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-100">
                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-semibold text-gray-900">Kontak Darurat</h2>
                    </div>

                    <div class="space-y-5">
                        {{-- Nama Kontak --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Kontak</label>
                            <input type="text" name="nama_emergency" maxlength="255" autocomplete="off"
                                placeholder="Nama Kontak Emergency"
                                class="w-full rounded-lg shadow-sm
                                @error('nama_emergency') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                                border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                value="{{ old('nama_emergency') }}">
                            @error('nama_emergency')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- No Kontak --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Telepon</label>
                            <input type="text" name="telp_emergency" maxlength="13" autocomplete="off"
                                placeholder="Nomor Telepon Kontak Emergency"
                                class="telp_emergency-input w-full rounded-lg shadow-sm
                                @error('telp_emergency') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                                border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                value="{{ old('telp_emergency') }}">
                            @error('telp_emergency')
                                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Hubungan --}}
                        <div x-data="{ open: false, selected: '{{ old('hubungan_emergency') }}' || '', list: ['Orang Tua', 'Saudara', 'Pasangan', 'Wali'] }" class="relative">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Hubungan</label>

                            <input type="hidden" name="hubungan_emergency" x-model="selected">

                            <div @click="open=!open"
                                class="border border-gray-500 bg-gray-50 rounded-lg py-2.5 px-3 cursor-pointer flex justify-between items-center">
                                <span x-text="selected || 'Pilih Hubungan'"></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            <ul x-show="open" @click.outside="open=false"
                                class="absolute w-full mt-1 border border-gray-300 bg-white rounded-lg shadow-md overflow-y-auto max-h-40 z-50">
                                <template x-for="item in list" :key="item">
                                    <li @click="selected=item; open=false"
                                        class="px-3 py-2 hover:bg-blue-600 hover:text-white cursor-pointer transition"
                                        x-text="item">
                                    </li>
                                </template>
                            </ul>
                        </div>


                        {{-- Ibu Nama --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Ibu Kandung</label>
                            <input type="text" name="ibu_kandung" maxlength="255" autocomplete="off"
                                placeholder="Nama Ibu Kandung"
                                class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                value="{{ old('ibu_kandung') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER / ACTIONS --}}
            <div class="bg-gray-50 px-8 py-5 flex items-center justify-end gap-3 border-t border-gray-200">
                <a href="{{ route('view.pekerja') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition shadow-sm">
                    Batalkan
                </a>
                <button type="button" name="action" value="save" id="save-btn"
                    class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan
                </button>
            </div>

        </form>
    </div>
@endsection

@section('scripts')
    <script src="/js/tambah-staff.js"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                title: "{{ session('success') }}",
                text: "Apakah Anda mau menambah data lagi?",
                icon: 'success',
                showDenyButton: true,
                confirmButtonText: "Tambah lagi",
                denyButtonText: "Ke daftar staff",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('view.tambah.staff') }}";
                } else {
                    window.location.href = "{{ route('view.staff') }}";
                }
            });
        </script>
    @endif
@endsection
