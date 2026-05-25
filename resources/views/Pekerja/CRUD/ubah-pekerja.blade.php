@extends('layout')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER SECTION --}}
        <div class="mb-8">
            <nav class="flex text-sm font-medium text-gray-500 mb-2">
                <a href="/daftar-pekerja" class="hover:text-gray-700 transition">Pekerja</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-blue-600">Ubah</span>
            </nav>

            <div class="flex items-center gap-4">
                <a href="{{ url()->previous() }}"
                    class="group p-2 rounded-full border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition shadow-sm">

                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 transform group-hover:-translate-x-0.5 transition" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Ubah Pekerja</h1>
                    <p class="text-sm text-gray-500 mt-1">Isi formulir di bawah untuk mengubah data pekerja.</p>
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
        <form action="{{ route('update.pekerja', $pekerja->id) }}" method="POST" enctype="multipart/form-data"
            x-ref="workerForm" x-data="workerForm()" @submit.prevent="confirmSubmit"
            class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

            @csrf
            @method('PUT')

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
                            <input type="hidden" name="remove_foto" id="removeFotoFlag" value="0">


                            <!-- PLACEHOLDER -->
                            <div id="placeholder"
                                class="text-center pointer-events-none {{ $pekerja->foto ? 'hidden' : '' }}">
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
                            <img id="previewImage"
                                class="absolute inset-0 w-full h-full object-cover {{ $pekerja->foto ? '' : 'hidden' }}"
                                src="{{ $pekerja->foto ? 'data:image/jpeg;base64,' . base64_encode($pekerja->foto) : '' }}"
                                alt="Preview Foto" />


                            <!-- DELETE BUTTON -->
                            <button type="button" id="removeBtn" onclick="removePhoto(event)"
                                class="absolute top-2 right-2 bg-red-600 text-white w-8 h-8 rounded-full flex items-center justify-center shadow-md hover:bg-red-700 transition {{ $pekerja->foto ? '' : 'hidden' }}">
                                ✕
                            </button>


                        </div>
                    </div>

                    {{-- Fields (Right Side) --}}
                    <div class="md:col-span-9 grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">ID Pekerja</label>

                            <input type="text" name="id_pekerja" maxlength="16" autocomplete="off"
                                class="id-input w-full rounded-lg shadow-sm border
                                @error('id_pekerja') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                                text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400
                                focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition"
                                placeholder="Input ID Karyawan" value="{{ old('id_pekerja', $pekerja->id_pekerja) }}">

                            @error('id_pekerja')
                                <p class=" text-red-600 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- NIK --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">NIK</label>
                            <input type="text" name="nik" maxlength="16" autocomplete="off"
                                value="{{ old('nik', $pekerja->nik) }}"
                                class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                placeholder="16 Digit Angka">
                        </div>

                        {{-- Nama Lengkap --}}
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Lengkap</label>
                            <input type="text" name="nama" maxlength="255" autocomplete="off"
                                value="{{ old('nama', $pekerja->nama) }}"
                                class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                placeholder="Sesuai KTP">
                        </div>

                        {{-- kpj --}}
                        <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">BPJS Ketenagakerjaan</label>

                                <input type="text" name="kpj" maxlength="11" autocomplete="off"
                                    class="kpj-input w-full rounded-lg shadow-sm border
                                    @error('kpj') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                                    text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400
                                    focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 transition"
                                    placeholder="11 Digit Angka" value="{{ old('kpj', $pekerja->kpj) }}">

                                @error('kpj')
                                    <p class="error-nik text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">BPJS Kesehatan</label>
                                <input type="text" name="naker" maxlength="13" autocomplete="off"
                                    class="naker-input w-full rounded-lg shadow-sm border
                                @error('naker') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                                border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                    placeholder="13 Digit Angka" value="{{ old('naker', $pekerja->naker) }}">
                                @error('naker')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Kartu Keluarga</label>
                                <input type="text" name="no_kk" maxlength="16" autocomplete="off"
                                    class="no_kk-input w-full rounded-lg shadow-sm border
                                @error('no_kk') border-red-500 bg-red-50 @else border-gray-500 bg-gray-50 @enderror
                                border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                    placeholder="16 Digit Angka" value="{{ old('no_kk', $pekerja->no_kk) }}">
                                @error('no_kk')
                                    <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Tempat Lahir --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" maxlength="100" autocomplete="off"
                                value="{{ old('tempat_lahir', $pekerja->tempat_lahir) }}"
                                class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                placeholder="Kota Kelahiran">
                        </div>

                        {{-- Tanggal Lahir --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Lahir</label>
                            <input type="date" name="tgl_lahir" value="{{ old('tgl_lahir', $pekerja->tgl_lahir) }}"
                                class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                        </div>

                        {{-- Jenis Kelamin --}}
                        <div x-data="{
                            open: false,
                            selected: '{{ old('kelamin', $pekerja->kelamin ?? '') }}',
                            list: [
                                { value: '1', label: 'Laki-laki' },
                                { value: '0', label: 'Perempuan' }
                            ]
                        }" class="relative">

                            <label class="block text-sm font-bold text-gray-700 mb-1">Jenis Kelamin</label>

                            <input type="hidden" name="kelamin" x-model="selected">

                            <div @click="open=!open"
                                class="border border-gray-500 bg-gray-50 rounded-lg py-2.5 px-3 cursor-pointer flex justify-between items-center select-none shadow-sm hover:border-blue-500 hover:ring-1 hover:ring-blue-200 transition">

                                <span x-text="list.find(x=>x.value==selected)?.label || 'Pilih Jenis Kelamin'"
                                    :class="selected ? 'text-gray-900' : 'text-gray-400'"></span>

                                <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': open }" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            <ul x-show="open" @click.outside="open=false" x-transition.opacity.duration.200ms
                                class="absolute w-full mt-1 border border-gray-200 bg-white rounded-lg shadow-xl overflow-y-auto max-h-60 z-50">
                                <template x-for="item in list">
                                    <li @click="selected=item.value; open=false"
                                        class="px-3 py-2.5 text-sm cursor-pointer hover:bg-blue-50 hover:text-blue-700 transition border-b border-gray-50 last:border-0"
                                        :class="{ 'bg-blue-50 text-blue-700': selected == item.value }"
                                        x-text="item.label">
                                    </li>
                                </template>
                            </ul>
                        </div>

                        {{-- Pendidikan --}}
                        <div x-data="{
                            open: false,
                            selected: '{{ old('pendidikan', $pekerja->pendidikan ?? '') }}',
                            list: ['TK', 'SD', 'SMP', 'SMA/SMK', 'D3', 'S1']
                        }" class="relative">

                            <label class="block text-sm font-bold text-gray-700 mb-1">Pendidikan</label>

                            <input type="hidden" name="pendidikan" x-model="selected">

                            <div @click="open=!open"
                                class="border border-gray-500 bg-gray-50 rounded-lg py-2.5 px-3 cursor-pointer flex justify-between items-center select-none shadow-sm hover:border-blue-500 hover:ring-1 hover:ring-blue-200 transition">

                                <span x-text="selected || 'Pilih Pendidikan'"
                                    :class="selected ? 'text-gray-900' : 'text-gray-400'"></span>

                                <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': open }" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            <ul x-show="open" @click.outside="open=false" x-transition.opacity.duration.200ms
                                class="absolute w-full mt-1 border border-gray-200 bg-white rounded-lg shadow-xl overflow-y-auto max-h-60 z-50">
                                <template x-for="item in list">
                                    <li @click="selected=item; open=false"
                                        class="px-3 py-2.5 text-sm cursor-pointer hover:bg-blue-50 hover:text-blue-700 transition border-b border-gray-50 last:border-0"
                                        :class="{ 'bg-blue-50 text-blue-700': selected == item }" x-text="item">
                                    </li>
                                </template>
                            </ul>
                        </div>


                        {{-- Status Perkawinan --}}
                        <div x-data="{
                            open: false,
                            selected: '{{ old('status_kawin', $pekerja->status_kawin ?? '') }}',
                            list: ['TK', 'K']
                        }" class="relative">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Status Perkawinan</label>

                            <input type="hidden" name="status_kawin" x-model="selected">

                            <div @click="open=!open"
                                class="border border-gray-500 bg-gray-50 rounded-lg py-2.5 px-3 cursor-pointer flex justify-between items-center select-none shadow-sm hover:border-blue-500 hover:ring-1 hover:ring-blue-200 transition">

                                <span x-text="selected || 'Pilih Status'"
                                    :class="selected ? 'text-gray-900' : 'text-gray-400'"></span>

                                <svg class="w-4 h-4 text-gray-500 transition-transform" :class="{ 'rotate-180': open }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            <ul x-show="open" @click.outside="open=false" x-transition.opacity.duration.200ms
                                class="absolute w-full mt-1 border border-gray-200 bg-white rounded-lg shadow-xl overflow-y-auto max-h-60 z-50">

                                <template x-for="item in list" :key="item">
                                    <li @click="selected=item; open=false"
                                        :class="{ 'bg-blue-50 text-blue-700': selected === item }"
                                        class="px-3 py-2.5 text-sm hover:bg-blue-50 hover:text-blue-700 cursor-pointer transition border-b border-gray-50 last:border-0"
                                        x-text="item">
                                    </li>
                                </template>
                            </ul>
                        </div>


                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Jumlah Anak</label>
                            <input type="text" name="anak" maxlength="2" autocomplete="off"
                                value="{{ old('anak', $pekerja->anak) }}"
                                class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                                placeholder="Kota Kelahiran" value="0">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Bergabung</label>
                            <input type="date" name="tgl_bergabung"
                                value="{{ old('tgl_bergabung', $pekerja->tgl_bergabung) }}"
                                class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Tanggal Resign</label>
                            <input type="date" name="tgl_resign" value="{{ old('tgl_resign', $pekerja->tgl_resign) }}"
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
                            class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 min-h-16 max-h-40 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition"
                            placeholder="Jl. ABC No. 10, Blok A">{{ old('alamat', $pekerja->alamat) }}</textarea>
                    </div>


                    {{-- Desa --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Desa</label>
                        <input type="text" name="desa" maxlength="100" autocomplete="off"
                            value="{{ old('desa', $pekerja->desa) }}"
                            class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                    </div>

                    {{-- RT / RW --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">RT</label>
                            <input type="text" name="rt" maxlength="3" autocomplete="off"
                                value="{{ old('rt', $pekerja->rt) }}"
                                class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">RW</label>
                            <input type="text" name="rw" maxlength="3" autocomplete="off"
                                value="{{ old('rw', $pekerja->rw) }}"
                                class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                        </div>
                    </div>

                    {{-- Kota --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Kota / Kabupaten</label>
                        <input type="text" name="kota" maxlength="100" autocomplete="off"
                            value="{{ old('kota', $pekerja->kota) }}"
                            class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                    </div>

                    {{-- Kecamatan & Provinsi --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Kecamatan</label>
                            <input type="text" name="kecamatan" maxlength="100" autocomplete="off"
                                value="{{ old('kecamatan', $pekerja->kecamatan) }}"
                                class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Provinsi</label>
                            <input type="text" name="provinsi" maxlength="100" autocomplete="off"
                                value="{{ old('provinsi', $pekerja->provinsi) }}"
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
                                value="{{ old('email', $pekerja->email) }}"
                                class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                        </div>

                        {{-- No Telepon --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Telepon</label>
                            <input type="text" name="telp" maxlength="13" autocomplete="off"
                                value="{{ old('telp', $pekerja->telp) }}"
                                class="telp-input w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                        </div>

                        {{-- Bank Info --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div x-data="{
                                open: false,
                                selected: '{{ old('nama_rek', $pekerja->nama_rek ?? '') }}',
                                list: ['BCA', 'BRI', 'BNI', 'Mandiri', 'CIMB Niaga', 'BTN', 'Bank Permata', 'Bank Danamon', 'Bank Mega', 'Panin Bank', 'OCBC NISP', 'Maybank Indonesia', 'BSI', 'Bank Jago', 'SeaBank', 'Bank Neo Commerce']
                            }" class="relative">
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Bank</label>

                                <input type="hidden" name="nama_rek" x-model="selected">

                                <div @click="open=!open"
                                    class="border border-gray-500 bg-gray-50 rounded-lg py-2.5 px-3 cursor-pointer flex justify-between items-center shadow-sm hover:border-blue-500 hover:ring-1 hover:ring-blue-200 transition">

                                    <span x-text="selected || 'Pilih Bank'"
                                        :class="selected ? 'text-gray-900' : 'text-gray-400'"></span>

                                    <svg class="w-4 h-4 text-gray-500 transition-transform"
                                        :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>

                                <ul x-show="open" @click.outside="open=false" x-transition.opacity.duration.200ms
                                    class="absolute w-full mt-1 border border-gray-200 bg-white rounded-lg shadow-xl overflow-y-auto max-h-60 z-50">

                                    <template x-for="item in list" :key="item">
                                        <li @click="selected=item; open=false"
                                            :class="{ 'bg-blue-50 text-blue-700': selected === item }"
                                            class="px-3 py-2.5 text-sm hover:bg-blue-50 hover:text-blue-700 cursor-pointer transition border-b border-gray-50 last:border-0"
                                            x-text="item">
                                        </li>
                                    </template>
                                </ul>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">No. Rekening</label>
                                <input type="text" name="rekening" maxlength="20" autocomplete="off"
                                    value="{{ old('rekening', $pekerja->rekening) }}"
                                    class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
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
                                value="{{ old('nama_emergency', $pekerja->nama_emergency) }}"
                                class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                        </div>

                        {{-- No Kontak --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nomor Telepon</label>
                            <input type="text" name="telp_emergency" maxlength="13" autocomplete="off"
                                value="{{ old('telp_emergency', $pekerja->telp_emergency) }}"
                                class="telp_emergency-input w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                        </div>

                        {{-- Hubungan --}}
                        <div x-data="{
                            open: false,
                            selected: '{{ old('hubungan_emergency', $pekerja->hubungan_emergency ?? '') }}',
                            list: ['Orang Tua', 'Saudara', 'Pasangan', 'Wali']
                        }" class="relative">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Hubungan</label>

                            <input type="hidden" name="hubungan_emergency" x-model="selected">

                            <div @click="open=!open"
                                class="border border-gray-500 bg-gray-50 rounded-lg py-2.5 px-3 cursor-pointer flex justify-between items-center shadow-sm hover:border-blue-500 hover:ring-1 hover:ring-blue-200 transition">

                                <span x-text="selected || 'Pilih Hubungan'"
                                    :class="selected ? 'text-gray-900' : 'text-gray-400'"></span>

                                <svg class="w-4 h-4 text-gray-500 transition-transform" :class="{ 'rotate-180': open }"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>

                            <ul x-show="open" @click.outside="open=false" x-transition.opacity.duration.200ms
                                class="absolute w-full mt-1 border border-gray-200 bg-white rounded-lg shadow-xl overflow-y-auto max-h-60 z-50">

                                <template x-for="item in list" :key="item">
                                    <li @click="selected=item; open=false"
                                        :class="{ 'bg-blue-50 text-blue-700': selected === item }"
                                        class="px-3 py-2.5 text-sm hover:bg-blue-50 hover:text-blue-700 cursor-pointer transition border-b border-gray-50 last:border-0"
                                        x-text="item">
                                    </li>
                                </template>
                            </ul>
                        </div>


                        {{-- Ibu Nama --}}
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Ibu Kandung</label>
                            <input type="text" name="ibu_kandung" maxlength="255" autocomplete="off"
                                value="{{ old('ibu_kandung', $pekerja->ibu_kandung) }}"
                                class="w-full rounded-lg shadow-sm border border-gray-500 bg-gray-50 text-gray-900 py-2.5 px-3 sm:text-sm font-medium placeholder-gray-400 focus:bg-white focus:border-blue-600 focus:ring-2 focus:ring-blue-100 focus:ring-offset-0 transition">
                        </div>
                    </div>
                </div>
            </div>

            {{-- FOOTER / ACTIONS --}}
            <div class="bg-gray-50 px-8 py-5 flex items-center justify-end gap-3 border-t border-gray-200">
                <a href="{{ route('view.detail.pekerja', $pekerja->id) }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg
                hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition shadow-sm">
                    Batalkan
                </a>

                <button type="button" @click="confirmSubmit()"
                    class="flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-white bg-emerald-600 rounded-lg
                    hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan
                </button>

            </div>


        </form>
    </div>

    <!-- PREVIEW SCRIPT -->
    <script>
        function phoneFieldHandler(inputClass, fieldName = "Nomor Telepon", min = 10, max = 15) {
            const input = document.querySelector(inputClass);
            if (!input) return;

            input.addEventListener('input', (e) => {
                const oldValue = e.target.value;
                const newValue = oldValue.replace(/[^0-9]/g, '');
                const errorId = `error-${fieldName.replace(/\s+/g, '').toLowerCase()}`;

                // 1. GET THE PARENT WRAPPER (The <div class="relative">)
                const wrapper = input.parentElement;

                let errorEl = document.getElementById(errorId);

                // Sanitize value (numbers only)
                if (oldValue !== newValue) e.target.value = newValue;

                // Validate length
                if (newValue.length < min || newValue.length > max) {
                    // Style the Input
                    input.classList.add('border-red-500', 'bg-red-50');
                    input.classList.remove('border-gray-500', 'bg-gray-50');

                    // Create Error Message if it doesn't exist
                    if (!errorEl) {
                        errorEl = document.createElement('p');
                        errorEl.id = errorId;
                        errorEl.className = "text-red-600 text-xs mt-1 ml-1";
                        errorEl.textContent = `${fieldName} harus terdiri dari ${min}-${max} angka`;

                        // 2. INSERT ERROR AFTER THE WRAPPER (Outside the relative div)
                        wrapper.insertAdjacentElement('afterend', errorEl);
                    }
                } else {
                    // Clear Error
                    input.classList.remove('border-red-500', 'bg-red-50');
                    input.classList.add('border-gray-500', 'bg-gray-50');

                    if (errorEl) {
                        errorEl.remove();
                    }
                }
            });
        }

        // Initialize
        phoneFieldHandler('.telp-input', "Nomor Telepon Pribadi", 10, 13);
        phoneFieldHandler('.telp_emergency-input', "Nomor Telepon Emergency", 10, 13);

        function showToast(message, type = 'error') {
            Swal.fire({
                toast: true,
                position: 'top',
                icon: type, // success | error | warning | info
                title: message,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        }

        function previewPhoto(event) {
            const input = event.target;
            const preview = document.getElementById('previewImage');
            const placeholder = document.getElementById('placeholder');
            const removeButton = document.getElementById('removeBtn');

            const file = input.files[0];
            if (!file) return;

            // Validasi ukuran max 2MB
            if (file.size > 2 * 1024 * 1024) {
                showToast('Ukuran foto maksimal 2MB');
                input.value = "";
                return;
            }

            // Preview image
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
                removeButton.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        function removePhoto(event) {
            event.stopPropagation(); // Mencegah klik area upload

            document.getElementById('fotoInput').value = "";
            document.getElementById('previewImage').src = "";
            document.getElementById('previewImage').classList.add('hidden');
            document.getElementById('placeholder').classList.remove('hidden');
            document.getElementById('removeBtn').classList.add('hidden');
            document.getElementById('removeFotoFlag').value = "1";

        }

        function workerForm() {
            return {
                confirmSubmit() {
                    Swal.fire({
                        title: 'Konfirmasi Simpan Data',
                        text: 'Pastikan semua data yang Anda input sudah benar. Lanjutkan menyimpan?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Simpan',
                        cancelButtonText: 'Cek lagi',
                        customClass: {
                            popup: 'rounded-2xl',
                            confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
                            cancelButton: 'rounded-xl px-6 py-2.5 font-bold'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Sedang Menyimpan...',
                                text: 'Mohon tunggu sebentar.',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            this.$refs.workerForm.submit();
                        }
                    });
                }
            }
        }
    </script>
@endsection
