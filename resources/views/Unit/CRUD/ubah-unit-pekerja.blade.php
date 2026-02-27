@extends('layout')

@section('content')
    <style>
        /* Menghilangkan panah di Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Menghilangkan panah di Firefox */
        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER --}}
        <div class="mb-8 flex items-center gap-4">
            <a href="/unit"
                class="p-2 rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <nav class="flex text-sm font-medium text-gray-500 mb-1">
                    <span class="hover:text-gray-700">Unit</span>
                    <span class="mx-2 text-gray-300">/</span>
                    <span class="text-blue-600">Ubah Pekerja Unit</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Ubah Pekerja Unit</h1>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-100 text-red-600 p-4 rounded-xl mb-6">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('update.unit-pekerja', ['unitId' => $unitSelected->id, 'pekerjaId' => $pkwt->id]) }}"
            method="POST" enctype="multipart/form-data" x-ref="updateForm" x-data="workerForm()" @submit.prevent="confirmSubmit" class="space-y-6">
            @csrf
            @method('put')

            {{-- CARD 1: INFORMASI UNIT --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 relative overflow-hidden group">

                {{-- Hidden Input --}}
                <input type="hidden" name="id_unit" value="{{ $unitSelected->id ?? '' }}">

                {{-- Background Decoration (Subtle) --}}
                <div
                    class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-50 rounded-full blur-3xl opacity-50 pointer-events-none">
                </div>

                <div class="flex items-start sm:items-center gap-5 relative z-10">

                    {{-- 1. Large Icon / Logo --}}
                    <div
                        class="flex-shrink-0 h-16 w-16 bg-gradient-to-br from-blue-50 to-white border border-blue-100 rounded-2xl flex items-center justify-center shadow-sm">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>

                    {{-- 2. Information Details --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span
                                class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 uppercase tracking-wide">
                                Unit Terpilih
                            </span>
                        </div>

                        <h2 class="text-xl font-bold text-gray-900 leading-tight truncate">
                            {{ $unitSelected->nama_unit ?? 'Nama Unit' }}
                        </h2>

                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1.5 text-sm text-gray-500">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span
                                    class="font-medium text-gray-700">{{ $unitSelected->namaMitra->nama_mitra ?? '-' }}</span>
                            </div>

                            <span class="text-gray-300">|</span>

                            <div class="flex items-center gap-1.5">
                                <span class="text-gray-400">ID:</span>
                                <span
                                    class="font-mono font-bold text-gray-900 bg-gray-100 px-1.5 rounded">{{ $unitSelected->id ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Lock Icon (Visual indicator that this is fixed) --}}
                    <div class="hidden sm:flex h-10 w-10 text-gray-300 justify-center items-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- CARD 2: ALOKASI PEKERJA (Redesigned) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-900">Daftar Pekerja & Kontrak</h3>
                </div>

                <div class="p-6 space-y-6">
                    <template x-for="(row, index) in rows" :key="row.id">
                        <div
                            class="bg-white rounded-2xl border border-gray-200 p-5 relative group transition hover:border-blue-300 hover:shadow-lg hover:shadow-blue-500/10">


                            {{-- HEADER BARIS: Memisahkan Nomor & Tombol Hapus agar tidak menabrak input --}}
                            <div class="flex items-center justify-between mb-6">
                                <div
                                    class="absolute -top-2 -left-2 h-6 w-6 bg-blue-600 text-white text-[13px] font-bold rounded-full flex items-center justify-center shadow-md">
                                    <span x-text="index + 1"></span>
                                </div>

                                <div class="flex items-center gap-3">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Data
                                        Pekerja ke-<span x-text="index + 1"></span></span>
                                </div>

                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">

                                {{-- 1. Worker Combobox --}}
                                <div x-data="workerCombobox(row)" x-init="init()" class="relative sm:col-span-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nama
                                        Pekerja</label>
                                    <input type="hidden" :name="`pekerja[${index}][id_pekerja]`" x-model="selectedId">

                                    <div class="relative">
                                        <input type="text" x-model="search" @focus="open = true" @click.outside="close()"
                                            placeholder="Cari nama atau NIK..." readonly
                                            class="w-full pl-4 pr-10 py-3 text-sm font-medium text-gray-800 bg-gray-50 rounded-xl border-gray-200 focus:bg-white focus:border-blue-500 transition cursor-pointer">
                                    </div>
                                </div>

                                {{-- 2. Divisi (Searchable Combobox) --}}
                                <div x-data="idCombobox(row, 'divisiId', window.divisiData, d => d.nama)" x-init="init()" class="relative">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                        Divisi
                                    </label>

                                    <div class="relative">
                                        <!-- Visible input (search/display only) -->
                                        <input type="text" x-model="search" @focus="open = true" @click="open = true"
                                            @click.outside="close()" @keydown.escape="open = false"
                                            placeholder="Pilih divisi..."
                                            class="w-full pl-4 pr-10 py-3 text-sm font-medium text-gray-800 bg-gray-50 rounded-xl focus:bg-white focus:border-blue-500 transition"
                                            autocomplete="off">

                                        <!-- Chevron -->
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Dropdown -->
                                    <ul x-show="open" x-transition.opacity
                                        class="absolute z-50 w-full mt-1 bg-white border border-gray-100 rounded-xl shadow-xl max-h-48 overflow-y-auto py-1">
                                        <template x-for="item in filtered" :key="item.id">
                                            <li @click="select(item)"
                                                class="px-4 py-2.5 text-sm cursor-pointer hover:bg-blue-50 hover:text-blue-700 font-medium text-gray-700">
                                                <span x-text="item.nama"></span>
                                            </li>
                                        </template>

                                        <li x-show="filtered.length === 0" class="px-4 py-2 text-xs text-gray-400 italic">
                                            Tidak ada hasil
                                        </li>
                                    </ul>

                                    <!-- Hidden input (actual submitted value) -->
                                    <input type="hidden" :name="`pekerja[${index}][divisi_id]`" :value="row.divisiId">
                                </div>


                                {{-- 3. Jabatan (Searchable Combobox) --}}
                                <div x-data="idCombobox(row, 'jabatanId', window.jabatanData, j => j.nama)" x-init="init()" class="relative">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                        Jabatan
                                    </label>

                                    <div class="relative">
                                        <!-- Visible input -->
                                        <input type="text" x-model="search" @focus="open = true" @click="open = true"
                                            @click.outside="close()" @keydown.escape="open = false"
                                            placeholder="Pilih jabatan..."
                                            class="w-full pl-4 pr-10 py-3 text-sm font-medium text-gray-800 bg-gray-50 rounded-xl focus:bg-white focus:border-blue-500 transition"
                                            autocomplete="off">

                                        <!-- Chevron -->
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Dropdown -->
                                    <ul x-show="open" x-transition.opacity
                                        class="absolute z-50 w-full mt-1 bg-white border border-gray-100 rounded-xl shadow-xl max-h-48 overflow-y-auto py-1">
                                        <template x-for="item in filtered" :key="item.id">
                                            <li @click="select(item)"
                                                class="px-4 py-2.5 text-sm cursor-pointer hover:bg-blue-50 hover:text-blue-700 font-medium text-gray-700">
                                                <span x-text="item.nama"></span>
                                            </li>
                                        </template>

                                        <li x-show="filtered.length === 0" class="px-4 py-2 text-xs text-gray-400 italic">
                                            Tidak ada hasil
                                        </li>
                                    </ul>

                                    <!-- Hidden input (submitted value) -->
                                    <input type="hidden" :name="`pekerja[${index}][jabatan_id]`" :value="row.jabatanId">
                                </div>


                                {{-- 4. Gaji Harian (Update ke Format Rupiah) --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Gaji
                                        Harian</label>
                                    <div class="relative">
                                        {{-- Input Tampilan --}}
                                        <input type="text" :value="formatRupiah(row.gaji)"
                                            @input="row.gaji = Number($event.target.value.replace(/\D/g, ''))"
                                            class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm font-bold text-gray-900 focus:bg-white focus:border-blue-500 py-3 px-4"
                                            placeholder="Rp 0">

                                        {{-- Hidden Input untuk kirim angka murni ke Controller --}}
                                        <input type="hidden" :name="`pekerja[${index}][gaji_harian]`"
                                            :value="row.gaji">
                                    </div>
                                </div>
                                {{-- 5. Gaji Harian (Update ke Format Rupiah) --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Gaji
                                        Overtime (Lembur) / Jam</label>
                                    <div class="relative">
                                        {{-- Input Tampilan --}}
                                        <input type="text" :value="formatRupiah(row.gajiOvertime)"
                                            @input="row.gajiOvertime = Number($event.target.value.replace(/\D/g, ''))"
                                            class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm font-bold text-gray-900 focus:bg-white focus:border-blue-500 py-3 px-4"
                                            placeholder="Rp 0">

                                        {{-- Hidden Input untuk kirim angka murni ke Controller --}}
                                        <input type="hidden" :name="`pekerja[${index}][gaji_overtime]`"
                                            :value="row.gajiOvertime">
                                    </div>
                                </div>

                                {{-- HASIL BPJS KESEHATAN --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">
                                        Iuran BPJS Kes
                                        <span x-show="row.kpj && row.kpj.trim() !== ''"
                                            class="text-green-500 text-[10px] ml-1">● Editable</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" {{-- Conditional readonly based on KPJ existence --}}
                                            :readonly="!row.kpj || row.kpj.trim() === ''"
                                            :value="formatRupiah(row.bpjsKesehatan)"
                                            @input="row.bpjsKesehatan = Number($event.target.value.replace(/\D/g, ''))"
                                            {{-- Dynamic classes based on KPJ and value --}}
                                            :class="{
                                                'bg-blue-50 text-blue-700 border-blue-200 cursor-text': row.kpj && row
                                                    .kpj.trim() !== '' && row.bpjsKesehatan > 0,
                                                'bg-white text-gray-700 border-gray-300 cursor-text hover:border-blue-400': row
                                                    .kpj && row.kpj.trim() !== '' && row.bpjsKesehatan === 0,
                                                'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed': !row
                                                    .kpj || row.kpj.trim() === ''
                                            }"
                                            class="w-full rounded-xl border text-sm font-bold py-3 px-4 transition-colors focus:ring-2 focus:ring-blue-300 focus:outline-none"
                                            placeholder="Rp 0">

                                        <input type="hidden" :name="`pekerja[${index}][bpjs_kesehatan]`"
                                            :value="row.bpjsKesehatan">

                                        {{-- Icon indicator --}}
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg x-show="row.kpj && row.kpj.trim() !== ''" class="w-4 h-4 text-green-500"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <svg x-show="!row.kpj || row.kpj.trim() === ''" class="w-4 h-4 text-gray-300"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- HASIL BPJS NAKER --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">
                                        Iuran BPJS Naker
                                        <span x-show="row.naker && row.naker.trim() !== ''"
                                            class="text-green-500 text-[10px] ml-1">● Editable</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" {{-- Conditional readonly based on Naker existence --}}
                                            :readonly="!row.naker || row.naker.trim() === ''"
                                            :value="formatRupiah(row.bpjsNaker)"
                                            @input="row.bpjsNaker = Number($event.target.value.replace(/\D/g, ''))"
                                            {{-- Dynamic classes based on Naker and value --}}
                                            :class="{
                                                'bg-blue-50 text-blue-700 border-blue-200 cursor-text': row.naker && row
                                                    .naker.trim() !== '' && row.bpjsNaker > 0,
                                                'bg-white text-gray-700 border-gray-300 cursor-text hover:border-blue-400': row
                                                    .naker && row.naker.trim() !== '' && row.bpjsNaker === 0,
                                                'bg-gray-100 text-gray-400 border-gray-200 cursor-not-allowed': !row
                                                    .naker || row.naker.trim() === ''
                                            }"
                                            class="w-full rounded-xl border text-sm font-bold py-3 px-4 transition-colors focus:ring-2 focus:ring-blue-300 focus:outline-none"
                                            placeholder="Rp 0">

                                        <input type="hidden" :name="`pekerja[${index}][bpjs_naker]`"
                                            :value="row.bpjsNaker">

                                        {{-- Icon indicator --}}
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg x-show="row.naker && row.naker.trim() !== ''"
                                                class="w-4 h-4 text-green-500" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            <svg x-show="!row.naker || row.naker.trim() === ''"
                                                class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- 8. Dates --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Mulai
                                        PKWT</label>
                                    <input type="date" :name="`pekerja[${index}][tgl_mulai_pkwt]`"
                                        x-model="row.tglMulai"
                                        class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm py-3 px-4 text-gray-600 focus:bg-white focus:border-blue-500">
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Akhir
                                        PKWT</label>
                                    <input type="date" :name="`pekerja[${index}][tgl_akhir_pkwt]`"
                                        x-model="row.tglAkhir"
                                        class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm py-3 px-4 text-gray-600 focus:bg-white focus:border-blue-500">
                                </div>

                                <!-- TEMPATKAN INI DI DALAM TEMPLATE WORKER ROW -->
                                <div
                                    class="sm:col-span-2 mt-2 p-5 bg-emerald-50/50 rounded-2xl border border-emerald-100/50">
                                    <div class="flex items-center gap-2 mb-4">
                                        <div class="p-1.5 bg-emerald-100 text-emerald-600 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <h4 class="text-[11px] font-black text-emerald-700 uppercase tracking-[0.2em]">
                                            Tunjangan Spesifik Unit</h4>
                                    </div>

                                    {{-- Hidden Input Utama: Mengirim data sebagai String JSON murni ke Laravel --}}
                                    <input type="hidden" :name="`pekerja[${index}][tunjangan]`"
                                        :value="JSON.stringify(row.tunjangan || {})">

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <template x-for="(val, key) in (row.tunjangan || {})" :key="key">
                                            <div class="space-y-1.5">
                                                <label
                                                    class="block text-[10px] font-bold text-emerald-600 uppercase tracking-wider ml-1"
                                                    x-text="key.replace(/_/g, ' ')"></label>
                                                <div class="relative">
                                                    <span
                                                        class="absolute left-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-emerald-400">Rp</span>

                                                    {{-- Input Tampilan: TIDAK memiliki atribut 'name' agar tidak terkirim secara terpisah --}}
                                                    <input type="text"
                                                        :value="formatRupiah(row.tunjangan[key]).replace('Rp', '').trim()"
                                                        @input="row.tunjangan[key] = Number($event.target.value.replace(/\D/g, ''))"
                                                        class="w-full pl-8 pr-3 py-2 text-sm font-black text-slate-700 bg-white border border-emerald-100 rounded-xl focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition-all shadow-sm"
                                                        placeholder="0">
                                                </div>
                                            </div>
                                        </template>

                                        <div x-show="Object.keys(row.tunjangan || {}).length === 0"
                                            class="sm:col-span-3 text-center py-2">
                                            <p class="text-xs text-emerald-400 italic font-medium">Unit ini tidak memiliki
                                                konfigurasi tunjangan.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="sm:col-span-2 mt-4">
                                    <div class="flex items-center justify-between mb-3 px-1">
                                        <label
                                            class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Alokasi
                                            Jam Harian</label>
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-[10px] font-bold text-gray-300 uppercase tracking-widest">Total</span>
                                            <span class="font-mono text-sm font-black text-gray-900"
                                                x-text="(Object.values(row.days).reduce((a, b) => (parseFloat(a) || 0) + (parseFloat(b) || 0), 0)).toFixed(1)">
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex bg-gray-50/80 p-1 rounded-2xl border border-gray-100 overflow-hidden">
                                        <template
                                            x-for="(dayName, dayKey) in {mon:'M', tue:'T', wed:'W', thu:'T', fri:'F', sat:'S', sun:'S'}"
                                            :key="dayKey">
                                            <div class="flex-1 relative group/day">
                                                <div
                                                    class="absolute top-2.5 left-0 right-0 text-center pointer-events-none z-20">
                                                    <span class="text-[9px] font-black tracking-tighter"
                                                        :class="row.days[dayKey] !== '' && parseFloat(row.days[
                                                            dayKey]) > 0 ? (dayKey === 'sun' ?
                                                            'text-red-500' :
                                                            'text-blue-500') : 'text-gray-300'"
                                                        x-text="dayName"></span>
                                                </div>

                                                <input type="number" step="0.1" placeholder="0"
                                                    :name="`pekerja[${index}][days][${dayKey}]`" :value="row.days[dayKey]"
                                                    @input="validateDayInput($event, row, dayKey)"
                                                    @blur="cleanupDayInput(row, dayKey)"
                                                    class="w-full pt-7 pb-3 text-center text-base font-bold bg-transparent border-none outline-none ring-0 focus:ring-0 focus:outline-none placeholder:text-gray-200 z-10 relative transition-all"
                                                    :class="row.days[dayKey] !== '' && row.days[dayKey] != 0 ?
                                                        'text-gray-900' : 'text-gray-400'" />

                                                <div
                                                    class="absolute inset-0 rounded-xl transition-all duration-300 opacity-0 group-hover/day:opacity-100 group-focus-within/day:opacity-100 group-focus-within/day:bg-white group-focus-within/day:shadow-sm">
                                                </div>
                                                <div x-show="dayKey !== 'sun'"
                                                    class="absolute right-0 top-4 bottom-4 w-px bg-gray-200/60 group-focus-within/day:opacity-0 transition-opacity">
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- 9. File Upload --}}
                                <div class="sm:col-span-2" x-data="{ fileName: '' }">
                                    <label
                                        class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Perbarui
                                        Dokumen
                                        PKWT</label>
                                    <div class="flex items-center gap-3">
                                        <label
                                            class="flex-shrink-0 cursor-pointer inline-flex items-center gap-2 px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold text-gray-700 hover:bg-gray-50 transition shadow-sm">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                            </svg>
                                            <span>Pilih File</span>
                                            <input type="file" class="hidden"
                                                :name="`pekerja[${index}][dokumen_pkwt]`"
                                                @change="fileName = $event.target.files[0]?.name || ''">
                                        </label>
                                        <div class="text-sm text-gray-500 font-medium truncate" x-show="fileName"
                                            x-text="fileName"></div>
                                        <div class="text-xs text-gray-400 italic" x-show="!fileName">Belum ada file
                                            dipilih.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- FOOTER / TOTAL --}}<div
                class="bg-white rounded-2xl shadow-[0_-8px_30px_-15px_rgba(0,0,0,0.1)] border border-gray-200 p-5 sticky bottom-6 z-30">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6">

                    {{-- LEFT: Meta Info & Cancel --}}
                    <div
                        class="flex items-center gap-4 w-full sm:w-auto order-2 sm:order-1 justify-center sm:justify-start">
                        <a href="{{ route('view.detail.unit', $unitSelected->id) }}"
                            class="px-6 py-3 text-sm font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition shadow-sm w-full sm:w-auto text-center">
                            Batalkan
                        </a>

                        <div class="h-4 w-px bg-gray-300 hidden sm:block"></div>

                        {{-- Row Counter Badge --}}
                        <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg">

                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span class="text-xs font-medium text-gray-400">
                                <span x-text="rows.length + ' pekerja akan ditambahkan'"></span>
                            </span>
                        </div>
                    </div>

                    {{-- RIGHT: Total Price & Save --}}
                    <div class="flex flex-col sm:flex-row items-center gap-4 w-full sm:w-auto order-1 sm:order-2">

                        {{-- Total Label --}}
                        <div class="text-center sm:text-right">
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Estimasi</p>
                            <p class="text-2xl font-black text-gray-900 leading-none mt-0.5"
                                x-text="formatRupiah(totalAllocation)"></p>
                        </div>

                        {{-- Save Button --}}
                        <button type="button" @click="confirmSubmit"
                            class="w-full sm:w-auto px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200/50 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                            <span>Simpan</span>
                            <svg class="w-5 h-5 text-emerald-100" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </button>

                    </div>

                </div>
            </div>

        </form>
    </div>
@endsection

@section('scripts')
    <script>
        window.oldRows = [{
            id: {{ $pkwt->id }},
            workerId: {{ $pkwt->id_pekerja }},
            gaji: {{ $pkwt->gaji_harian }},
            gajiOvertime: {{ $pkwt->gaji_overtime }},
            bpjsKesehatan: {{ $pkwt->bpjs_kesehatan }},
            bpjsNaker: {{ $pkwt->bpjs_naker }},
            divisiId: {{ $pkwt->divisi_id }},
            jabatanId: {{ $pkwt->jabatan_id }},
            tglMulai: '{{ $pkwt->tgl_mulai_pkwt }}',
            tglAkhir: '{{ $pkwt->tgl_akhir_pkwt }}',
            kpj: '{{ $pkwt->pekerja->kpj ?? '' }}',
            naker: '{{ $pkwt->pekerja->naker ?? '' }}',
            days: (() => {
                const rawDays = @json($pkwt->hariKerja->pluck('jam_kerja', 'hari'));
                const formatted = {};
                ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'].forEach(day => {
                    const val = rawDays[day];
                    formatted[day] = (val !== undefined && val !== null) ? parseFloat(val)
                    .toString() : '';
                });
                return formatted;
            })(),
            tunjangan: @js($pkwt->tunjangan ?? []),
        }];

        function workerCombobox(row) {
            return {
                open: false,
                search: '',
                selectedId: row.workerId || null,

                init() {
                    // If an ID is already set (e.g., from old() data), populate search field
                    if (this.selectedId) {
                        const p = window.workersData.find(w => w.id == this.selectedId);
                        if (p) {
                            this.search = `${p.nama} (${p.nik})`;
                        }
                    }
                },
                close() {
                    if (this.selectedId) {
                        const p = window.workersData.find(w => w.id == this.selectedId);
                        if (p) {
                            this.search = `${p.nama} (${p.nik})`;
                        }
                    } else {
                        this.search = '';
                    }
                    this.open = false;
                }
            }
        }

        // 1. DATA SOURCES (You can pass these from Controller later)
        window.divisiData = @json($divisiList);
        window.jabatanData = @json($jabatanList);
        window.workersData = @json($pekerjaList ?? []);

        window.unitInfo = {
            umk: {{ $unitSelected->umk ?? 0 }},
            pct_kesehatan: {{ $unitSelected->bpjs_kesehatan ?? 0 }},
            pct_naker: {{ $unitSelected->bpjs_naker ?? 0 }},
            tunjanganConfig: @js($unitSelected->tunjangan ?? []),
        };

        // 2. FORM LOGIC
        function workerForm() {
            const config = window.unitInfo.tunjanganConfig || {};
            let initialTunjangan = {};
            Object.keys(config).forEach(key => {
                initialTunjangan[key] = config[key];
            });
            return {
                rows: window.oldRows && window.oldRows.length ?
                    window.oldRows : [{
                        id: Date.now(),
                        gaji: 0,
                        gajiOvertime: 0,
                        workerId: '',
                        divisiId: null,
                        jabatanId: null,
                        tglMulai: '',
                        tglAkhir: '',
                        bpjsKesehatan: 0,
                        bpjsNaker: 0,
                        kpj: '',
                        naker: '',
                        days: {
                            mon: '',
                            tue: '',
                            wed: '',
                            thu: '',
                            fri: '',
                            sat: '',
                            sun: ''
                        },
                        tunjangan: initialTunjangan,
                    }],
                init() {
                    // Recalculate BPJS for existing data in case UMK/percentages changed
                    this.rows.forEach(row => {
                        if (row.kpj || row.naker) {
                            this.calculateBpjs(row);
                        }
                    });
                },
                get totalAllocation() {
                    return this.rows.reduce((sum, row) => sum + (parseInt(row.gaji) || 0), 0);
                },

                calculateBpjs(row) {
                    const umk = Number(window.unitInfo.umk) || 0;
                    const pctKesehatan = Number(window.unitInfo.pct_kesehatan) || 0;
                    const pctNaker = Number(window.unitInfo.pct_naker) || 0;

                    // Kalkulasi BPJS Kesehatan jika ada KPJ
                    if (row.kpj && row.kpj.toString().trim() !== '') {
                        row.bpjsKesehatan = Math.round(umk * (pctKesehatan / 100));
                        // console.log('✓ BPJS Kesehatan calculated:', row.bpjsKesehatan);
                    } else {
                        row.bpjsKesehatan = 0;
                        // console.log('✗ BPJS Kesehatan = 0 (no KPJ)');
                    }

                    // Kalkulasi BPJS Naker jika ada Naker
                    if (row.naker && row.naker.toString().trim() !== '') {
                        row.bpjsNaker = Math.round(umk * (pctNaker / 100));
                        // console.log('✓ BPJS Naker calculated:', row.bpjsNaker);
                    } else {
                        row.bpjsNaker = 0;
                        // console.log('✗ BPJS Naker = 0 (no Naker)');
                    }

                },

                validateDayInput(e, row, dayKey) {
                    let input = e.target;
                    let val = input.value;

                    if (val === '') {
                        row.days[dayKey] = '';
                        return;
                    }

                    let num = parseFloat(val);

                    if (num > 24) {
                        num = 24;
                        input.value = 24;
                    }
                    if (num < 0) {
                        num = 0;
                        input.value = 0;
                    }

                    row.days[dayKey] = input.value;
                },

                cleanupDayInput(row, dayKey) {
                    if (row.days[dayKey] !== '' && row.days[dayKey] !== null) {
                        let n = parseFloat(row.days[dayKey]);
                        row.days[dayKey] = isNaN(n) ? '' : parseFloat(n.toFixed(1)).toString();
                    }
                },
                formatRupiah(amount) {
                    const value = Number(amount) || 0;
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(value);
                },

                confirmSubmit() {
                    Swal.fire({
                        title: 'Konfirmasi Perbarui Data',
                        text: 'Pastikan semua data yang Anda ubah sudah benar. Lanjutkan menyimpan?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981', // emerald-600
                        cancelButtonColor: '#6b7280', // gray-500
                        confirmButtonText: 'Ya, Perbarui',
                        cancelButtonText: 'Batal',
                        reverseButtons: false,
                        customClass: {
                            popup: 'rounded-2xl',
                            confirmButton: 'rounded-xl px-6 py-2.5 font-bold',
                            cancelButton: 'rounded-xl px-6 py-2.5 font-bold'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Menyimpan...',
                                text: 'Mohon tunggu',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading()
                                }
                            })

                            this.$refs.updateForm.submit()
                        }
                    });
                }
            }
        }

        // 3. GENERIC STRING COMBOBOX (For Divisi & Jabatan)
        // This allows selecting from list OR typing a new value (Hybrid)
        function idCombobox(row, field, dataSource, labelFn) {
            return {
                open: false,
                search: '',
                selectedId: row[field],

                init() {
                    if (this.selectedId) {
                        const found = dataSource.find(i => i.id == this.selectedId);
                        if (found) this.search = labelFn(found);
                    }
                },

                get filtered() {
                    if (!this.search) return dataSource;
                    return dataSource.filter(item =>
                        labelFn(item).toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                select(item) {
                    this.selectedId = item.id;
                    row[field] = item.id;
                    this.search = labelFn(item);
                    this.open = false;
                },

                close() {
                    if (this.selectedId) {
                        const found = dataSource.find(i => i.id == this.selectedId);
                        if (found) this.search = labelFn(found);
                    } else {
                        this.search = '';
                    }
                    this.open = false;
                }
            }
        }

        // (Combobox logic for Unit remains the same...)
        document.addEventListener('alpine:init', () => {
            Alpine.data('combobox', (listData) => ({
                // ... existing unit combobox code ...
                list: listData,
                search: '',
                selectedId: '',
                open: false,
                get filteredList() {
                    if (this.search === '') return this.list;
                    return this.list.filter(i => i.nama.toLowerCase().includes(this.search
                        .toLowerCase()));
                },
                select(item) {
                    this.selectedId = item.id;
                    this.search = item.nama;
                    this.open = false;
                }
            }))
        })
    </script>
@endsection
