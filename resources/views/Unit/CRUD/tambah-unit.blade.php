@extends('layout')

@section('content')
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER SECTION --}}
        <div class="mb-8">
            <nav class="flex text-sm font-medium text-gray-500 mb-2">
                <a href="/unit" class="hover:text-gray-700 transition">Unit</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-blue-600">Tambah</span>
            </nav>

            <div class="flex items-center gap-4">
                <a href="/unit"
                    class="group p-2 rounded-full border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 transform group-hover:-translate-x-0.5 transition" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Unit</h1>
                    <p class="text-sm text-gray-500 mt-1">Isi formulir di bawah untuk mendaftarkan unit baru.</p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-100 text-red-600 p-4 rounded-xl mb-6 flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mt-0.5 flex-shrink-0" viewBox="0 0 20 20"
                    fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                <ul class="text-sm list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM CARD --}}
        <form id="formTambahUnit" action="{{ route('tambah.unit.post') }}" method="POST" enctype="multipart/form-data"
            class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            @csrf

            <div class="p-8 space-y-8">

                {{-- SECTION 1: INFORMASI UNIT --}}
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-8 w-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Informasi Unit</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- ID Unit --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">ID Unit <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="id_unit" placeholder="Contoh: 71923"
                                class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-blue-100 transition py-3 px-4 text-sm font-medium placeholder-gray-400"
                                value="{{ old('id_unit') }}" maxlength="20">
                        </div>

                        <div x-data="mitraKerjaCombobox()" x-init="init()" class="relative">

                            {{-- Label & Add Button --}}
                            <div class="flex justify-between items-center mb-1">
                                <label class="block text-sm font-bold text-gray-700">Mitra Kerja</label>

                            </div>

                            {{-- 1. HIDDEN INPUT (Stores the ID for the backend) --}}
                            <input type="hidden" name="id_mitra_kerja" x-model="selectedId">

                            {{-- 2. SEARCHABLE INPUT (Visible to user) --}}
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    {{-- Search Icon when typing, Briefcase icon when empty --}}
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>

                                <input type="text" x-model="search" @input="open = true; selectedId = ''"
                                    @click="open = true" @click.outside="closeDropdown()" @keydown.escape="open = false"
                                    placeholder="Cari atau pilih bidang usaha..."
                                    class="w-full pl-10 pr-10 rounded-lg border-gray-300 bg-gray-50 text-gray-900 placeholder-gray-400
                                focus:bg-white focus:border-blue-500 focus:ring-blue-200 transition py-2.5 px-4 text-sm font-medium"
                                    autocomplete="off">

                                {{-- Chevron Icon (Visual cue that it is a list) --}}
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer"
                                    @click="toggleDropdown()">
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                        :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>

                            {{-- 3. DROPDOWN LIST --}}
                            <ul x-show="open" x-transition.opacity
                                class="absolute w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto z-40 py-1">

                                {{-- Loop through FILTERED items --}}
                                <template x-for="item in filteredList" :key="item.val">
                                    <li @click="selectOption(item)"
                                        class="px-4 py-2.5 text-sm hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition flex items-center justify-between group">

                                        {{-- Highlight matching text logic can go here, but simple text is fine --}}
                                        <span x-text="item.label"
                                            :class="selectedId == item.val ? 'font-bold text-blue-600' : 'text-gray-700'"></span>

                                        <svg x-show="selectedId == item.val" class="w-4 h-4 text-blue-600" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </li>
                                </template>

                                {{-- No Results Found --}}
                                <li x-show="filteredList.length === 0"
                                    class="px-4 py-3 text-sm text-gray-500 text-center">
                                    <p>Tidak ditemukan "<span x-text="search" class="font-bold"></span>"</p>
                                    <button type="button" @click="openModalWithSearch()"
                                        class="mt-1 text-blue-600 hover:underline font-semibold text-xs">
                                        + Tambah Baru
                                    </button>
                                </li>
                            </ul>

                        </div>

                        {{-- Nama Unit --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nama Unit
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_unit" placeholder="Contoh: Unit Produksi A"
                                class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-blue-100 transition py-3 px-4 text-sm font-medium placeholder-gray-400"
                                value="{{ old('nama_unit') }}">
                        </div>

                        {{-- PIC Name --}}
                        <div x-data="picCombobox()" x-init="init()" class="group"> {{-- Hapus relative di sini --}}

                            {{-- Label --}}
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Penanggung Jawab (PIC)
                            </label>

                            {{-- Hidden Inputs for Form Submission --}}
                            <template x-for="item in selectedItems" :key="item.val">
                                <input type="hidden" name="pic_ids[]" :value="item.val">
                            </template>

                            {{-- WRAPPER BARU: Ini yang membuat dropdown nempel dengan input --}}
                            <div class="relative">

                                {{-- Main Container (Input Box) --}}
                                <div class="relative w-full min-h-[50px] rounded-xl border border-gray-200 bg-gray-50 px-2 py-1.5 flex flex-wrap gap-2 transition-all duration-200
                                    cursor-text"
                                    @click="$refs.searchInput.focus()">

                                    {{-- A. Selected Chips --}}
                                    <template x-for="(item, index) in selectedItems" :key="item.val">
                                        <div
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-white border border-gray-200 shadow-sm animate-fadeIn">

                                            {{-- User Icon --}}
                                            <div
                                                class="w-5 h-5 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>

                                            <span x-text="item.label" class="text-xs font-bold text-gray-700"></span>

                                            <button type="button" @click.stop="removeItem(index)"
                                                class="p-0.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-md transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>

                                    {{-- B. Search Input --}}
                                    <div class="flex-1 min-w-[150px] relative">
                                        <input x-ref="searchInput" type="text" x-model="search" @input="open = true"
                                            @click="open = true" @click.outside="open = false"
                                            @keydown.escape="open = false" @keydown.backspace="handleBackspace()"
                                            placeholder="Cari atau pilih staff..."
                                            class="w-full h-full bg-transparent border-none focus:ring-0 p-2 text-sm font-medium text-gray-900 placeholder-gray-400"
                                            autocomplete="off">
                                    </div>

                                    {{-- Right Chevron --}}
                                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400 group-focus-within:text-blue-500 transition-colors"
                                            :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>
                                </div>

                                {{-- 3. Dropdown List (Sekarang di dalam relative wrapper yang sama dengan input) --}}
                                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute w-full mt-1 bg-white border border-gray-100 rounded-xl shadow-xl z-50 overflow-hidden">

                                    <ul class="max-h-60 overflow-y-auto py-2">
                                        <template x-for="item in filteredList" :key="item.val">
                                            <li @click="selectOption(item)"
                                                class="px-4 py-2.5 text-sm cursor-pointer transition flex items-center gap-3 hover:bg-blue-50 group">

                                                {{-- Avatar Placeholder --}}
                                                <div
                                                    class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 group-hover:bg-blue-200 group-hover:text-blue-700 transition">
                                                    <span class="text-xs font-bold"
                                                        x-text="item.label.substring(0,1)"></span>
                                                </div>

                                                <span x-text="item.label"
                                                    class="text-gray-700 font-medium group-hover:text-blue-700"></span>

                                                {{-- Plus Icon --}}
                                                <svg class="w-4 h-4 ml-auto text-gray-300 group-hover:text-blue-500"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                            </li>
                                        </template>

                                        {{-- No Results --}}
                                        <li x-show="filteredList.length === 0" class="px-4 py-4 text-center">
                                            <p class="text-sm text-gray-500">Tidak ditemukan "<span x-text="search"
                                                    class="font-bold text-gray-900"></span>"</p>
                                        </li>
                                    </ul>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>

                {{-- Divider --}}
                <div class="h-px bg-gray-100 w-full my-8"></div>

                {{-- SECTION 3: PENGATURAN SHIFT --}}
                <div x-data="shiftManager()">
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-8 w-8 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h2 class="text-lg font-bold text-gray-900">Pengaturan Shift</h2>
                        </div>
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-bold rounded-full"
                            x-text="shifts.length + ' Shift Terdaftar'"></span>
                    </div>

                    <div
                        class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end bg-gray-50/50 p-6 rounded-2xl border border-gray-100 mb-6">
                        {{-- Nama Shift --}}
                        <div class="md:col-span-5">
                            <label
                                class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Nama
                                Shift</label>
                            <input type="text" x-model="newShift.nama" placeholder="Contoh: Pagi / Shift 1 / Malam"
                                class="w-full rounded-xl border-gray-200 bg-white focus:border-purple-500 focus:ring-purple-100 transition py-3 px-4 text-sm font-medium">
                        </div>

                        {{-- Jam Mulai --}}
                        <div class="md:col-span-3">
                            <label
                                class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Jam
                                Mulai</label>
                            <input type="time" x-model="newShift.waktu_masuk"
                                class="w-full rounded-xl border-gray-200 bg-white focus:border-purple-500 transition py-3 px-4 text-sm font-medium">
                        </div>

                        {{-- Jam Selesai --}}
                        <div class="md:col-span-3">
                            <label
                                class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2 ml-1">Jam
                                Selesai</label>
                            <input type="time" x-model="newShift.waktu_keluar"
                                class="w-full rounded-xl border-gray-200 bg-white focus:border-purple-500 transition py-3 px-4 text-sm font-medium">
                        </div>

                        {{-- Button Add --}}
                        <div class="md:col-span-1">
                            <button type="button" @click="addShift()"
                                class="w-full h-[46px] flex items-center justify-center bg-purple-600 hover:bg-purple-700 text-white rounded-xl transition shadow-md shadow-purple-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- LIST PREVIEW --}}
                    <div class="space-y-3">
                        {{-- Hidden Inputs for Backend --}}
                        <template x-for="(s, index) in shifts" :key="index">
                            <div>
                                <input type="hidden" :name="'shifts[' + index + '][nama]'" :value="s.nama">
                                <input type="hidden" :name="'shifts[' + index + '][waktu_masuk]'"
                                    :value="s.waktu_masuk">
                                <input type="hidden" :name="'shifts[' + index + '][waktu_keluar]'"
                                    :value="s.waktu_keluar">
                            </div>
                        </template>

                        <template x-for="(s, index) in shifts" :key="index">
                            <div
                                class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-2xl hover:border-purple-300 transition group animate-fadeIn">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm"
                                        x-text="index + 1"></div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900" x-text="s.nama"></h4>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="text-xs font-medium text-gray-500" x-text="s.waktu_masuk"></span>
                                            <svg class="w-3 h-3 text-gray-300" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path d="M14 5l7 7m0 0l-7 7m7-7H3" stroke-width="2" />
                                            </svg>
                                            <span class="text-xs font-medium text-gray-500"
                                                x-text="s.waktu_keluar"></span>
                                        </div>
                                    </div>
                                </div>
                                <button type="button" @click="removeShift(index)"
                                    class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </template>

                        {{-- Empty State --}}
                        <div x-show="shifts.length === 0"
                            class="py-12 border-2 border-dashed border-gray-100 rounded-2xl flex flex-col items-center justify-center">
                            <div class="p-3 bg-gray-50 rounded-full mb-3">
                                <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <p class="text-sm text-gray-400 font-medium">Belum ada shift yang ditambahkan</p>
                        </div>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="h-px bg-gray-100 w-full"></div>

                {{-- SECTION 2: KONTRAK & LEGALITAS --}}
                <div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-8 w-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l5.414 5.414a1 1 0 01.586 1.414V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Kontrak & Legalitas</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Sistem Pengajian --}}
                        <div x-data="{ open: false, selected: @js(old('sistem_pengajian')) || '', list: [{ val: '1', label: 'Harian' }, { val: '2', label: 'Borongan' }] }" class="relative">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Sistem Pengajian</label>

                            <input type="hidden" name="sistem_pengajian" x-model="selected">

                            <div @click="open=!open"
                                class=" bg-gray-50 rounded-lg py-2.5 px-3 cursor-pointer flex justify-between items-center">
                                <span x-text="list.find(x=>x.val==selected)?.label || 'Pilih Tipe Pengajian'"></span>
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

                        {{-- UMK Unit --}}
                        <div x-data="{ umk: '{{ old('umk') }}' }">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                Nominal Pembayaran (UMK)
                            </label>

                            <div class="relative group">
                                {{-- Suffix Symbol (Rp) dengan desain yang lebih menyatu --}}
                                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                    <span
                                        class="text-gray-400 font-black text-sm group-focus-within:text-blue-600 transition-colors duration-200 border-r border-gray-200 pr-2 mr-1">
                                        Rp
                                    </span>
                                </div>

                                {{-- Visual Input (Masking) --}}
                                <input type="text" x-model="umk"
                                    @input="umk = $event.target.value.replace(/\D/g, '').replace(/\B(?=(\d{3})+(?!\d))/g, '.')"
                                    placeholder="0"
                                    class="block w-full rounded-xl border-gray-200 bg-gray-50
                                        text-gray-900 font-bold text-sm placeholder:text-gray-300
                                        focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10
                                        transition-all duration-200 py-3 pl-14 pr-4 shadow-sm">

                                {{-- Hidden Input untuk Database (nama: umk_unit) --}}
                                <input type="hidden" name="umk" :value="umk.replace(/\./g, '')">
                            </div>

                            {{-- Helper Text --}}
                            <p class="mt-1.5 ml-1 text-[10px] text-gray-400 italic">
                                *Contoh: 3.500.000 (Otomatis diformat)
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:col-span-2">
                            {{-- Management Fee --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                    Management Fee (%)
                                </label>

                                <div class="relative group">
                                    {{-- Input Field --}}
                                    <input type="number" name="persentase_management_fee" placeholder="0"
                                        min="0" max="100" step="0.01"
                                        class="block w-full rounded-xl border-gray-200 bg-gray-50
                   text-gray-900 font-bold text-sm placeholder-gray-400
                   focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10
                   transition-all duration-200 py-3 pl-4 pr-10">

                                    {{-- Suffix Symbol --}}
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                        <span
                                            class="text-gray-400 font-bold text-sm group-focus-within:text-blue-600 transition-colors duration-200">
                                            %
                                        </span>
                                    </div>
                                </div>

                                {{-- Helper Text --}}
                                <p class="mt-1.5 ml-1 text-[10px] text-gray-400">
                                    Masukkan presentase fee (Ex: 2.5)
                                </p>
                            </div>

                            {{-- BPJS Naker Fee --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                    BPJS Kesehatan Fee (%)
                                </label>

                                <div class="relative group">
                                    {{-- Input Field --}}
                                    <input type="number" name="bpjs_kesehatan" placeholder="0" min="0"
                                        max="100" step="0.01"
                                        class="block w-full rounded-xl border-gray-200 bg-gray-50
                   text-gray-900 font-bold text-sm placeholder-gray-400
                   focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10
                   transition-all duration-200 py-3 pl-4 pr-10">

                                    {{-- Suffix Symbol --}}
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                        <span
                                            class="text-gray-400 font-bold text-sm group-focus-within:text-blue-600 transition-colors duration-200">
                                            %
                                        </span>
                                    </div>
                                </div>

                                {{-- Helper Text --}}
                                <p class="mt-1.5 ml-1 text-[10px] text-gray-400">
                                    Masukkan presentase BPJS Kesehatan fee (Ex: 2.5)
                                </p>
                            </div>

                            {{-- BPJS Naker Fee --}}
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                    BPJS Naker Fee (%)
                                </label>

                                <div class="relative group">
                                    {{-- Input Field --}}
                                    <input type="number" name="bpjs_naker" placeholder="0" min="0"
                                        max="100" step="0.01"
                                        class="block w-full rounded-xl border-gray-200 bg-gray-50
                   text-gray-900 font-bold text-sm placeholder-gray-400
                   focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10
                   transition-all duration-200 py-3 pl-4 pr-10">

                                    {{-- Suffix Symbol --}}
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                                        <span
                                            class="text-gray-400 font-bold text-sm group-focus-within:text-blue-600 transition-colors duration-200">
                                            %
                                        </span>
                                    </div>
                                </div>

                                {{-- Helper Text --}}
                                <p class="mt-1.5 ml-1 text-[10px] text-gray-400">
                                    Masukkan presentase BPJS Naker fee (Ex: 2.5)
                                </p>
                            </div>
                        </div>
                        {{-- Dates Row --}}
                        <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Mulai
                                    Perjanjian</label>
                                <input type="date" name="mulai_perjanjian"
                                    class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-blue-100 transition py-3 px-4 text-sm font-medium text-gray-700"
                                    value="{{ old('mulai_perjanjian', date('Y-m-d')) }}">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Akhir
                                    Perjanjian</label>
                                <input type="date" name="akhir_perjanjian" min="{{ date('Y-m-d') }}"
                                    class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-blue-100 transition py-3 px-4 text-sm font-medium text-gray-700"
                                    value="{{ old('akhir_perjanjian') }}">
                            </div>
                        </div>

                        {{-- File Upload --}}
                        <div class="md:col-span-2" x-data="{ fileName: '' }">
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Dokumen
                                Kontrak (PDF/IMG)</label>

                            <label
                                class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 hover:bg-blue-50 hover:border-blue-400 transition group relative overflow-hidden">

                                {{-- Empty State --}}
                                <div x-show="!fileName" class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <svg class="w-8 h-8 mb-3 text-gray-400 group-hover:text-blue-500 transition"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                        </path>
                                    </svg>
                                    <p class="mb-1 text-sm text-gray-500 font-medium"><span
                                            class="font-bold text-blue-600">Klik upload</span> atau drag file</p>
                                    <p class="text-xs text-gray-400">PDF, PNG, JPG (Max. 5MB)</p>
                                </div>

                                {{-- Filled State --}}
                                <div x-show="fileName" class="flex items-center gap-3 z-10">
                                    <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l5.414 5.414a1 1 0 01.586 1.414V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <span class="text-sm font-bold text-gray-800" x-text="fileName"></span>
                                </div>

                                <input type="file" name="dokumen_mou" class="hidden"
                                    @change="fileName = $event.target.files[0] ? $event.target.files[0].name : ''" />
                            </label>
                        </div>

                    </div>
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="bg-gray-50/50 px-8 py-6 border-t border-gray-100 flex items-center justify-end gap-3">
                <a href="{{ route('view.mitra-kerja') }}"
                    class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-800 transition shadow-sm">
                    Batal
                </a>
                <button type="button" id="save-btn"
                    class="px-5 py-2.5 text-sm font-bold text-white bg-black border border-black rounded-xl hover:bg-gray-800 transition shadow-lg shadow-gray-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Unit
                </button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="/js/tambah-unit.js"></script>

    <script>
        function mitraKerjaCombobox() {
            return {
                // Data passed from Laravel
                list: @json($mitraKerjaList ?? []),
                selectedId: '{{ old('id_mitra_kerja') }}',
                search: '',
                open: false,
                showModal: false,
                newBidangName: '',
                isLoading: false,
                errorMessage: '',

                init() {
                    // If there is an old ID (validation error or edit), fill the search box with the label
                    if (this.selectedId) {
                        const found = this.list.find(item => item.val == this.selectedId);
                        if (found) {
                            this.search = found.label;
                        }
                    }
                },

                // Computed property for filtering
                get filteredList() {
                    if (this.search === '') {
                        return this.list;
                    }
                    return this.list.filter(item => {
                        return item.label.toLowerCase().includes(this.search.toLowerCase());
                    });
                },

                toggleDropdown() {
                    this.open = !this.open;
                    // If opening, maybe clear search if it doesn't match an ID? (Optional)
                },

                closeDropdown() {
                    this.open = false;
                    // UX Polish: If user typed something but didn't select, and it doesn't match perfectly, reset or clear?
                    // For now, let's ensure text matches ID.
                    const found = this.list.find(item => item.val == this.selectedId);
                    if (found) {
                        this.search = found.label;
                    } else {
                        this.search = ''; // Clear text if no valid selection was made
                    }
                },

                selectOption(item) {
                    this.selectedId = item.val;
                    this.search = item.label; // Set text to label
                    this.open = false;
                },

                // --- Modal Logic ---
                openModal() {
                    this.newBidangName = '';
                    this.errorMessage = '';
                    this.showModal = true;
                },

                openModalWithSearch() {
                    this.newBidangName = this.search; // Pre-fill with what they typed
                    this.errorMessage = '';
                    this.showModal = true;
                    this.open = false;
                },

                saveBidang() {
                    if (!this.newBidangName) {
                        this.errorMessage = 'Nama tidak boleh kosong.';
                        return;
                    }
                    this.isLoading = true;
                    this.errorMessage = '';

                    // AJAX Call
                    fetch("{{ route('tambah.bidang-usaha.post') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content')
                            },
                            body: JSON.stringify({
                                nama: this.newBidangName
                            })
                        })
                        .then(res => res.ok ? res.json() : Promise.reject(res))
                        .then(data => {
                            // 1. Update List
                            this.list.push({
                                val: data.val,
                                label: data.label
                            });
                            this.selectOption({
                                val: data.val,
                                label: data.label
                            });
                            this.showModal = false;
                            this.newBidangName = '';

                            // 2. TRIGGER THE TOAST NOTIFICATION HERE
                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: {
                                    type: 'success',
                                    message: 'Bidang usaha baru berhasil ditambahkan!'
                                }
                            }));
                        })
                        .catch(() => {
                            this.errorMessage = 'Gagal menyimpan. Nama mungkin sudah ada.';
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                }
            }
        }

        function picCombobox() {
            return {
                // Data passed from Controller
                list: @json($picList ?? []),
                selectedItems: [],
                search: '',
                open: false,

                init() {
                    // Handle Old Data (Validation Errors)
                    let oldIds = @json(old('pic_ids', []));

                    // Ensure oldIds is an array (Laravel sometimes sends single string if only 1 selected)
                    if (!Array.isArray(oldIds)) {
                        oldIds = [oldIds];
                    }

                    if (oldIds.length > 0) {
                        // Re-map IDs to full objects
                        this.selectedItems = this.list.filter(item => oldIds.includes(item.val.toString()));
                    }
                },

                get filteredList() {
                    // Show items that match search AND are not already selected
                    return this.list.filter(item => {
                        const matchesSearch = item.label.toLowerCase().includes(this.search.toLowerCase());
                        const notSelected = !this.selectedItems.some(selected => selected.val === item.val);
                        return matchesSearch && notSelected;
                    });
                },

                selectOption(item) {
                    this.selectedItems.push(item);
                    this.search = ''; // Clear search
                    this.$refs.searchInput.focus(); // Keep focus for rapid selection
                },

                removeItem(index) {
                    this.selectedItems.splice(index, 1);
                },

                handleBackspace() {
                    // If search is empty, remove the last tag
                    if (this.search === '' && this.selectedItems.length > 0) {
                        this.selectedItems.pop();
                    }
                },

                // Placeholder for modal logic
                openModalWithSearch() {
                    alert("Logic Modal Tambah PIC (Sama seperti sebelumnya)");
                }
            }
        }

        function shiftManager() {
            return {
                shifts: [],
                newShift: {
                    nama: 'Pagi',
                    waktu_masuk: '09:00', // Ganti nama key
                    waktu_keluar: '17:00' // Ganti nama key
                },

                init() {
                    let oldShifts = @json(old('shifts', []));
                    if (oldShifts.length > 0) {
                        this.shifts = oldShifts;
                    }
                },

                addShift() {
                    if (this.shifts.length >= 3) {
                        this.sendNotification('error', 'Maksimal hanya boleh 3 shift!');
                        return;
                    }

                    // Gunakan key baru waktu_masuk & waktu_keluar
                    if (!this.newShift.nama || !this.newShift.waktu_masuk || !this.newShift.waktu_keluar) {
                        this.sendNotification('error', 'Mohon isi semua field shift!');
                        return;
                    }

                    const isDuplicateName = this.shifts.some(s =>
                        s.nama.toLowerCase().trim() === this.newShift.nama.toLowerCase().trim()
                    );
                    if (isDuplicateName) {
                        this.sendNotification('error', 'Nama shift "' + this.newShift.nama + '" sudah ada!');
                        return;
                    }

                    const isDuplicateTime = this.shifts.some(s =>
                        s.waktu_masuk === this.newShift.waktu_masuk &&
                        s.waktu_keluar === this.newShift.waktu_keluar
                    );
                    if (isDuplicateTime) {
                        this.sendNotification('error', 'Shift dengan jam kerja tersebut sudah terdaftar!');
                        return;
                    }

                    this.shifts.push({
                        ...this.newShift
                    });

                    // Reset dengan key yang konsisten
                    this.newShift.nama = '';
                    this.newShift.waktu_masuk = '09:00';
                    this.newShift.waktu_keluar = '17:00';

                    this.sendNotification('success', 'Shift berhasil ditambahkan.');
                },

                sendNotification(type, message) {
                    window.dispatchEvent(new CustomEvent('notify', {
                        detail: {
                            type: type,
                            message: message
                        }
                    }));
                },

                removeShift(index) {
                    this.shifts.splice(index, 1);
                }
            }
        }
    </script>

    @if (session('success'))
        <script>
            Swal.fire({
                title: "{{ session('success') }}",
                text: "Apakah Anda mau menambah data lagi?",
                icon: 'success',
                showDenyButton: true,
                confirmButtonText: "Tambah lagi",
                denyButtonText: "Ke daftar unit",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('view.tambah.unit') }}";
                } else {
                    window.location.href = "{{ route('view.unit') }}";
                }
            });
        </script>
    @endif
@endsection
