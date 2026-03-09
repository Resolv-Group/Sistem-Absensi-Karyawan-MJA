@extends('layout')

@section('content')
    <style>
        /* Prevent default number input UI */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
            font-family: inherit;
        }

        /* Chrome/Safari focus fix */
        input:focus {
            outline: none !important;
            box-shadow: none !important;
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
                    <span class="text-blue-600">Tambah Pekerja Unit</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Pekerja Unit</h1>
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

        <form action="{{ route('tambah.unit-pekerja.post') }}" method="POST" enctype="multipart/form-data"
            x-ref="workerForm" x-data="workerForm()" @submit.prevent="confirmSubmit" class="space-y-6">
            @csrf

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
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900">Daftar Pekerja & Kontrak</h3>
                    <button type="button" @click="addRow()"
                        class="text-xs font-bold text-blue-600 bg-white hover:bg-blue-50 px-3 py-1.5 rounded-lg transition flex items-center gap-1 border border-gray-200 shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Baris
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    <template x-for="(row, index) in rows" :key="row.id">
                        <div
                            class="bg-white rounded-xl border border-gray-200 p-8 md:p-10 relative group transition-all duration-300 hover:border-blue-300 hover:shadow-[0_20px_50px_rgba(0,0,0,0.04)] space-y-12 mb-10">

                            {{-- 1. HEADER BARIS --}}
                            <div class="flex items-center justify-between border-b border-gray-50 pb-6">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 bg-blue-600 text-white text-sm font-black rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200"
                                        x-text="index + 1"></div>
                                    <div>
                                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Konfigurasi
                                            Kontrak</h3>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">
                                            Entry Data Pekerja & Alokasi Unit</p>
                                    </div>
                                </div>
                                <button type="button" @click="removeRow(index)"
                                    class="flex items-center gap-2 px-4 py-2 text-[10px] font-black text-red-500 uppercase tracking-widest hover:bg-red-50 rounded-xl transition-all active:scale-95">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2.5">
                                        <path
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus Baris
                                </button>
                            </div>

                            {{-- SECTION 1: PERSONALIA --}}
                            <div class="space-y-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-1.5 h-6 bg-blue-500 rounded-full"></div>
                                    <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-[0.2em]">Informasi
                                        Personalia</h4>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                                    {{-- Nama Pekerja --}}
                                    <div x-data="workerCombobox(row)" x-init="init()" class="relative sm:col-span-2">
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama
                                            Pekerja</label>
                                        <input type="hidden" :name="`pekerja[${index}][id_pekerja]`"
                                            x-model="selectedId">
                                        <div class="relative group">
                                            <input type="text" x-model="search" @focus="open = true"
                                                @click.outside="close()" placeholder="Cari berdasarkan Nama atau NIK..."
                                                class="w-full pl-5 pr-20 py-4 text-sm font-bold text-slate-700 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-blue-100 focus:bg-white transition-all shadow-sm">

                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 gap-2">
                                                <button type="button" x-show="selectedId"
                                                    @click.stop="selectedId = null; row.workerId = ''; search = ''; row.kpj = ''; row.naker = ''; row.bpjsKesehatan = 0; row.bpjsNaker = 0;"
                                                    class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" stroke-width="3">
                                                        <path d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                                <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2.5">
                                                    <path d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </div>
                                        <ul x-show="open" x-transition.opacity
                                            class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-3xl shadow-2xl max-h-64 overflow-y-auto py-3 custom-scrollbar">
                                            <template x-for="p in filtered" :key="p.id">
                                                <li @click="select(p)"
                                                    class="px-6 py-3.5 text-sm cursor-pointer hover:bg-blue-50 transition-colors flex items-center justify-between group">
                                                    <div class="flex flex-col">
                                                        <span class="font-bold text-slate-800" x-text="p.nama"></span>
                                                        <span
                                                            class="text-[10px] text-slate-400 font-mono tracking-widest uppercase mt-0.5"
                                                            x-text="p.nik"></span>
                                                    </div>
                                                    <svg class="w-4 h-4 text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity"
                                                        fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                                        stroke-width="3">
                                                        <path d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>

                                    {{-- Divisi --}}
                                    <div x-data="idCombobox(row, 'divisiId', window.divisiData, d => d.nama)" x-init="init()" class="relative">
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Divisi
                                            Pekerja</label>
                                        <input type="text" x-model="search" @focus="open = true"
                                            @click.outside="close()" placeholder="Pilih Divisi..."
                                            class="w-full px-5 py-4 text-sm font-bold text-slate-700 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-blue-100 focus:bg-white transition-all shadow-sm">
                                        <ul x-show="open"
                                            class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl max-h-48 overflow-y-auto py-2 custom-scrollbar">
                                            <template x-for="item in filtered" :key="item.id">
                                                <li @click="select(item)"
                                                    class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-blue-50 cursor-pointer"
                                                    x-text="item.nama"></li>
                                            </template>
                                        </ul>
                                        <input type="hidden" :name="`pekerja[${index}][divisi_id]`"
                                            :value="row.divisiId">
                                    </div>

                                    {{-- Jabatan --}}
                                    <div x-data="idCombobox(row, 'jabatanId', window.jabatanData, j => j.nama)" x-init="init()" class="relative">
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Jabatan
                                            Pekerja</label>
                                        <input type="text" x-model="search" @focus="open = true"
                                            @click.outside="close()" placeholder="Pilih Jabatan..."
                                            class="w-full px-5 py-4 text-sm font-bold text-slate-700 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-blue-100 focus:bg-white transition-all shadow-sm">
                                        <ul x-show="open"
                                            class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl max-h-48 overflow-y-auto py-2 custom-scrollbar">
                                            <template x-for="item in filtered" :key="item.id">
                                                <li @click="select(item)"
                                                    class="px-5 py-2.5 text-sm font-bold text-slate-600 hover:bg-blue-50 cursor-pointer"
                                                    x-text="item.nama"></li>
                                            </template>
                                        </ul>
                                        <input type="hidden" :name="`pekerja[${index}][jabatan_id]`"
                                            :value="row.jabatanId">
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 2: STANDAR WAKTU & JADWAL (hidden when sistem_pengajian == 2) --}}
                            <div class="space-y-6" x-show="window.unitInfo.sistem_pengajian != 2">
                                <div class="flex items-center gap-3">
                                    <div class="w-1.5 h-6 bg-indigo-500 rounded-full"></div>
                                    <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-[0.2em]">Standar
                                        Waktu & Alokasi</h4>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                                    {{-- Weekly Schedule --}}
                                    <div class="sm:col-span-2">
                                        <div class="flex items-center justify-between mb-4 px-1">
                                            <label
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Prakiraan
                                                Jam Kerja Harian</label>
                                            <div
                                                class="flex items-center gap-2 px-3 py-1 bg-slate-50 rounded-lg border border-slate-100">
                                                <span
                                                    class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total:</span>
                                                <span class="font-mono text-xs font-black text-blue-600"
                                                    x-text="(Object.values(row.days).reduce((a, b) => (parseFloat(a) || 0) + (parseFloat(b) || 0), 0)).toFixed(1) + ' Jam'"></span>
                                            </div>
                                        </div>
                                        <div
                                            class="flex bg-slate-50 p-1.5 rounded-[1.5rem] border border-slate-100 overflow-hidden shadow-inner">
                                            <template
                                                x-for="(dayName, dayKey) in {mon:'Senin', tue:'Selasa', wed:'Rabu', thu:'Kamis', fri:'Jumat', sat:'Sabtu', sun:'Minggu'}"
                                                :key="dayKey">
                                                <div class="flex-1 relative group/day">
                                                    <div
                                                        class="absolute top-3 left-0 right-0 text-center pointer-events-none z-20">
                                                        <span
                                                            class="text-[9px] font-black uppercase tracking-tighter transition-colors"
                                                            :class="row.days[dayKey] !== '' && parseFloat(row.days[
                                                                dayKey]) > 0 ? (dayKey === 'sun' ?
                                                                'text-red-500' : 'text-blue-600') : 'text-slate-300'"
                                                            x-text="dayName"></span>
                                                    </div>
                                                    <input type="number" step="0.1" placeholder="0"
                                                        :name="`pekerja[${index}][days][${dayKey}]`"
                                                        :value="row.days[dayKey]"
                                                        @input="validateDayInput($event, row, dayKey)"
                                                        @blur="cleanupDayInput(row, dayKey)"
                                                        class="w-full pt-8 pb-4 text-center text-sm font-black bg-transparent border-none outline-none ring-0 focus:ring-0 z-10 relative transition-all"
                                                        :class="row.days[dayKey] !== '' ? 'text-slate-800' :
                                                            'text-slate-300'" />
                                                    <div x-show="dayKey !== 'sun'"
                                                        class="absolute right-0 top-4 bottom-4 w-px bg-slate-200/60 group-focus-within/day:opacity-0 transition-opacity">
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 3: STRUKTUR GAJI --}}
                            <div class="space-y-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-1.5 h-6 bg-emerald-500 rounded-full"></div>
                                    <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-[0.2em]">Struktur
                                        Kompensasi</h4>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                                    {{-- Gaji Bulanan (hidden when sistem_pengajian == 2) --}}
                                    <div class="space-y-2" x-show="window.unitInfo.sistem_pengajian != 2">
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Gaji
                                            Bulanan (Pokok)</label>
                                        <div class="relative group">
                                            <span
                                                class="absolute left-5 top-1/2 -translate-y-1/2 text-xs font-black text-slate-300 group-focus-within:text-emerald-500 transition-colors">Rp</span>
                                            <input type="text" :value="formatRupiah(row.gaji_bulanan)"
                                                @input="row.gaji_bulanan = Number($event.target.value.replace(/\D/g, '')); calculateSalaryComponents(row)"
                                                class="w-full pl-12 pr-5 py-4 text-sm font-black text-slate-700 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-emerald-100 focus:bg-white transition-all shadow-sm"
                                                placeholder="0">
                                            <input type="hidden" :name="`pekerja[${index}][gaji_bulanan]`"
                                                :value="row.gaji_bulanan">
                                        </div>
                                    </div>
                                    {{-- Gaji Harian (hidden when sistem_pengajian == 2) --}}
                                    <div class="space-y-2" x-show="window.unitInfo.sistem_pengajian != 2">
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Gaji
                                            Harian (Proporsional)</label>
                                        <div class="relative group">
                                            <span
                                                class="absolute left-5 top-1/2 -translate-y-1/2 text-xs font-black text-slate-300 group-focus-within:text-emerald-500 transition-colors">Rp</span>
                                            <input type="text" :value="formatRupiah(row.gaji)"
                                                @input="calculateSalaryComponents(row)"
                                                class="w-full pl-12 pr-5 py-4 text-sm font-black text-slate-700 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-emerald-100 focus:bg-white transition-all shadow-sm"
                                                placeholder="0">
                                            <input type="hidden" :name="`pekerja[${index}][gaji_harian]`"
                                                :value="row.gaji">
                                        </div>
                                    </div>
                                    {{-- Overtime (hidden when sistem_pengajian == 2) --}}
                                    <div class="space-y-2" x-show="window.unitInfo.sistem_pengajian != 2">
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Upah
                                            Overtime / Jam</label>
                                        <div class="relative group">
                                            <span
                                                class="absolute left-5 top-1/2 -translate-y-1/2 text-xs font-black text-slate-300 group-focus-within:text-emerald-500 transition-colors">Rp</span>
                                            <input type="text" :value="formatRupiah(row.overtime)"
                                                @input="row.overtime = Number($event.target.value.replace(/\D/g, ''))"
                                                class="w-full pl-12 pr-5 py-4 text-sm font-black text-slate-700 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-emerald-100 focus:bg-white transition-all shadow-sm"
                                                placeholder="0">
                                            <input type="hidden" :name="`pekerja[${index}][gaji_overtime]`"
                                                :value="row.overtime">
                                        </div>
                                    </div>
                                    {{-- HBN (hidden when sistem_pengajian == 2) --}}
                                    <div class="space-y-2" x-show="window.unitInfo.sistem_pengajian != 2">
                                        {{-- Header: Judul & Hasil dalam satu baris --}}
                                        <div class="flex items-center justify-between px-1">
                                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                Rate HBN (OT × <span class="text-blue-600"
                                                    x-text="row.rate_hbn || 0"></span>)
                                            </label>

                                            {{-- Hasil Kalkulasi (Compact beside title) --}}
                                            <div class="flex items-center gap-1.5">
                                                <span
                                                    class="text-[11px] font-bold text-slate-300 uppercase tracking-tighter">Hasil:</span>
                                                <span class="text-[12px] font-black text-blue-600 tracking-tight"
                                                    x-text="formatRupiah(row.gaji_hbn, true)"></span>
                                            </div>
                                        </div>

                                        <div class="relative group">
                                            {{-- Ikon Multiplier --}}
                                            <span
                                                class="absolute left-5 top-1/2 -translate-y-1/2 text-xs font-black text-slate-300 group-focus-within:text-blue-500 transition-colors">
                                                ×
                                            </span>

                                            {{-- Input Multiplier (Rate) --}}
                                            <input type="number" step="0.1" min="0" x-model="row.rate_hbn"
                                                @input="calculateSalaryComponents(row)"
                                                class="w-full pl-10 pr-5 py-4 text-sm font-black text-slate-700 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-blue-100 focus:bg-white transition-all shadow-sm outline-none"
                                                placeholder="0.0">

                                            {{-- Hidden Inputs --}}
                                            <input type="hidden" :name="`pekerja[${index}][rate_hbn]`"
                                                :value="row.rate_hbn">
                                        </div>
                                    </div>

                                    {{-- BPJS Section --}}
                                    <div class="space-y-2">
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Iuran
                                            BPJS Kesehatan
                                            <span x-show="row.kpj"
                                                class="ml-2 px-1.5 py-0.5 bg-emerald-50 text-emerald-600 text-[8px] rounded uppercase font-black">Editable</span>
                                        </label>
                                        <div class="relative">
                                            <input type="text" :readonly="!row.kpj"
                                                :value="formatRupiah(row.bpjsKesehatan, true)"
                                                @input="row.bpjsKesehatan = Number($event.target.value.replace(/\D/g, ''))"
                                                :class="{
                                                    'bg-blue-50 text-blue-700 border border-blue-100 cursor-text': row
                                                        .kpj && row.bpjsKesehatan > 0,
                                                    'bg-white border border-slate-200': row.kpj && row.bpjsKesehatan ===
                                                        0,
                                                    'bg-slate-100 text-slate-400 border-none cursor-not-allowed': !row
                                                        .kpj
                                                }"
                                                class="w-full px-5 py-4 text-sm font-black rounded-xl transition-all outline-none">
                                            <input type="hidden" :name="`pekerja[${index}][bpjs_kesehatan]`"
                                                :value="row.bpjsKesehatan">
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Iuran
                                            BPJS Ketenagakerjaan
                                            <span x-show="row.naker"
                                                class="ml-2 px-1.5 py-0.5 bg-emerald-50 text-emerald-600 text-[8px] rounded uppercase font-black">Editable</span>
                                        </label>
                                        <div class="relative">
                                            <input type="text" :readonly="!row.naker"
                                                :value="formatRupiah(row.bpjsNaker, true)"
                                                @input="row.bpjsNaker = Number($event.target.value.replace(/\D/g, ''))"
                                                :class="{
                                                    'bg-blue-50 text-blue-700 border border-blue-100 cursor-text': row
                                                        .naker && row.bpjsNaker > 0,
                                                    'bg-white border border-slate-200': row.naker && row.bpjsNaker ===
                                                        0,
                                                    'bg-slate-100 text-slate-400 border-none cursor-not-allowed': !row
                                                        .naker
                                                }"
                                                class="w-full px-5 py-4 text-sm font-black rounded-xl transition-all outline-none">
                                            <input type="hidden" :name="`pekerja[${index}][bpjs_naker]`"
                                                :value="row.bpjsNaker">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- SECTION 4: TUNJANGAN UNIT (Emerald Glass Box) --}}
                            <div class="p-8 bg-emerald-50/40 rounded-[1.5rem] border border-emerald-100 space-y-6">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="p-2 bg-white rounded-xl shadow-sm text-emerald-600 border border-emerald-50">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                            stroke-width="2.5">
                                            <path
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h4 class="text-[11px] font-black text-emerald-800 uppercase tracking-[0.2em]">
                                        Tunjangan Spesifik Unit</h4>
                                </div>

                                <input type="hidden" :name="`pekerja[${index}][tunjangan]`"
                                    :value="JSON.stringify(row.tunjangan || {})">

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                                    <template x-for="(val, key) in (row.tunjangan || {})" :key="key">
                                        <div class="space-y-2">
                                            <label
                                                class="block text-[10px] font-black text-emerald-600 uppercase tracking-widest ml-1"
                                                x-text="key.replace(/_/g, ' ')"></label>
                                            <div class="relative group/t">
                                                <span
                                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-emerald-300 group-focus-within/t:text-emerald-600 transition-colors">Rp</span>
                                                <input type="text"
                                                    :value="formatRupiah(row.tunjangan[key]).replace('Rp', '').trim()"
                                                    @input="row.tunjangan[key] = Number($event.target.value.replace(/\D/g, ''))"
                                                    class="w-full pl-10 pr-4 py-3 text-sm font-black text-emerald-800 bg-white border border-emerald-100 rounded-2xl focus:ring-4 focus:ring-emerald-200/50 outline-none transition-all shadow-sm"
                                                    placeholder="0">
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <div x-show="Object.keys(row.tunjangan || {}).length === 0"
                                    class="text-center py-4 text-emerald-400 italic text-xs font-bold uppercase tracking-widest">
                                    Tidak ada konfigurasi tunjangan tambahan.
                                </div>
                            </div>

                            {{-- SECTION 5: ADMINISTRASI --}}
                            <div class="space-y-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-1.5 h-6 bg-slate-400 rounded-full"></div>
                                    <h4 class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em]">
                                        Administrasi & Dokumen PKWT</h4>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-6">
                                    <div class="space-y-2">
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mulai
                                            Berlaku PKWT</label>
                                        <input type="date" :name="`pekerja[${index}][tgl_mulai_pkwt]`"
                                            value="{{ now()->toDateString() }}"
                                            class="w-full px-5 py-4 text-sm font-bold text-slate-600 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-slate-100 focus:bg-white transition-all shadow-sm">
                                    </div>
                                    <div class="space-y-2">
                                        <label
                                            class="block text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Berakhir
                                            Berlaku PKWT</label>
                                        <input type="date" :name="`pekerja[${index}][tgl_akhir_pkwt]`"
                                            class="w-full px-5 py-4 text-sm font-bold text-slate-600 bg-slate-50 border-none rounded-xl focus:ring-4 focus:ring-slate-100 focus:bg-white transition-all shadow-sm">
                                    </div>

                                    {{-- File Upload --}}
                                    <div class="sm:col-span-2 mt-2" x-data="{ fileName: '' }">
                                        <label
                                            class="flex items-center gap-5 p-6 bg-slate-50 border-2 border-dashed border-slate-200 rounded-xl cursor-pointer hover:bg-white hover:border-blue-400 transition-all group shadow-sm">
                                            <div
                                                class="h-14 w-14 bg-white rounded-2xl shadow-sm border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-blue-500 transition-colors">
                                                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" stroke-width="2">
                                                    <path
                                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-black text-slate-700 group-hover:text-blue-600 transition-colors truncate"
                                                    x-text="fileName || 'Upload Dokumen PKWT Resmi'"></p>
                                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1"
                                                    x-show="!fileName">Format yang didukung: PDF, PNG, JPG (Maks. 2MB)</p>
                                                <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mt-1"
                                                    x-show="fileName">File siap diunggah</p>
                                            </div>
                                            <input type="file" class="hidden"
                                                :name="`pekerja[${index}][dokumen_pkwt]`"
                                                @change="fileName = $event.target.files[0]?.name || ''">
                                        </label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </template>

                    {{-- Empty State --}}
                    <div x-show="rows.length === 0" class="text-center py-12 text-gray-400 text-sm italic bg-gray-50/50">
                        Klik "Tambah Baris" untuk memasukkan data pekerja.
                    </div>
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
                        <button type="button" @click="confirmSubmit()"
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
        function unitCombobox() {
            return {
                list: @json($units ?? []),
                selectedId: '',
                search: '',
                open: false,

                init() {
                    // If validation failed and old ID exists, repopulate text
                    let oldId = '{{ old('id_unit') }}';
                    if (oldId) {
                        let found = this.list.find(item => item.id == oldId);
                        if (found) {
                            this.selectedId = found.id;
                            this.search = found.nama;
                        }
                    }
                },

                get filteredList() {
                    if (this.search === '') return this.list;
                    return this.list.filter(item =>
                        item.nama.toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                toggleDropdown() {
                    this.open = !this.open;
                },

                closeDropdown() {
                    this.open = false;
                    // If user typed but didn't select, revert to selected or clear
                    let found = this.list.find(item => item.id == this.selectedId);
                    if (found) {
                        this.search = found.nama;
                    } else {
                        this.search = '';
                        this.selectedId = '';
                    }
                },

                selectOption(item) {
                    this.selectedId = item.id;
                    this.search = item.nama;
                    this.open = false;
                }
            }
        }

        function workerCombobox(row) {
            return {
                open: false,
                search: '',
                selectedId: row.workerId || null,

                init() {
                    if (this.selectedId) {
                        const p = window.workersData.find(w => w.id == this.selectedId);
                        if (p) this.search = `${p.nama} (${p.nik})`;
                    }
                },

                get filtered() {
                    const allSelected = this.$data.getSelectedWorkerIds();
                    return window.workersData.filter(p => {
                        const matchesSearch = !this.search ||
                            p.nama.toLowerCase().includes(this.search.toLowerCase()) ||
                            p.nik.includes(this.search);
                        const isAvailable = !allSelected.includes(p.id) || p.id == row.workerId;
                        return matchesSearch && isAvailable;
                    });
                },

                select(p) {
                    // console.log('Selected worker:', p);

                    this.selectedId = p.id;
                    row.workerId = p.id;
                    this.search = `${p.nama} (${p.nik})`;

                    // ✅ ASSIGN KPJ and NAKER from worker data to row
                    row.kpj = p.kpj || '';
                    row.naker = p.naker || '';

                    this.open = false;

                    // ✅ Now calculate BPJS
                    this.$data.calculateBpjs(row);
                },
                close() {
                    // Only restore if there's a valid selection
                    if (this.selectedId) {
                        const p = window.workersData.find(w => w.id == this.selectedId);
                        if (p) this.search = `${p.nama} (${p.nik})`;
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
            sistem_pengajian: {{ $unitSelected->sistem_pengajian ?? 1 }},
        };

        // 2. FORM LOGIC
        function workerForm() {
            const createFreshTunjangan = () => {
                const config = window.unitInfo.tunjanganConfig || {};
                return JSON.parse(JSON.stringify(config)); // Deep clone agar referensi memori berbeda
            };

            return {
                rows: [{
                        id: 1,
                        gaji: 0,
                        gaji_bulanan: 0, // Inisialisasi
                        overtime: 0,
                        rate_hbn: 0,
                        gaji_hbn: 0, // Inisialisasi
                        workerId: '',
                        divisiId: null,
                        jabatanId: null,
                        kpj: '',
                        naker: '',
                        bpjsKesehatan: 0,
                        bpjsNaker: 0,
                        days: {
                            mon: 0,
                            tue: 0,
                            wed: 0,
                            thu: 0,
                            fri: 0,
                            sat: 0,
                            sun: 0
                        },
                        tunjangan: createFreshTunjangan(),
                    } // Added fields
                ],

                calculateSalaryComponents(row) {
                    const bulanan = parseFloat(row.gaji_bulanan) || 0;
                    const rateHbn = parseFloat(row.rate_hbn) || 0;

                    // 1. Gaji Harian
                    row.gaji = Math.round(bulanan / 25);

                    // 2. Upah Overtime per Jam (Base)
                    const otBase = bulanan / 173;
                    row.overtime = Math.round(otBase);

                    // 3. Gaji HBN (Hasil Otomatis)
                    // Rumus: Base Overtime x Angka Pengali (Rate)
                    row.gaji_hbn = Math.round(row.overtime * rateHbn);
                },

                getSelectedWorkerIds() {
                    return this.rows.map(row => row.workerId).filter(id => id !== '');
                },

                validateDayInput(e, row, dayKey) {
                    let input = e.target;
                    let val = input.value;

                    // 1. Allow empty for a clean UI
                    if (val === '') {
                        row.days[dayKey] = '';
                        return;
                    }

                    // 2. Convert to float for numeric comparison
                    let num = parseFloat(val);

                    // 3. Strict Cap at 24
                    if (num > 24) {
                        num = 24;
                        input.value = 24; // Force the physical input box to change
                    }

                    // 4. Strict Min at 0
                    if (num < 0) {
                        num = 0;
                        input.value = 0;
                    }

                    // 5. Update Alpine data
                    row.days[dayKey] = input.value;
                },

                cleanupDayInput(row, dayKey) {
                    // Clean up trailing dots (e.g., "7." becomes "7") on blur
                    if (row.days[dayKey] !== '') {
                        let n = parseFloat(row.days[dayKey]);
                        row.days[dayKey] = isNaN(n) ? '' : parseFloat(n.toFixed(1)).toString();
                    }
                },

                addRow() {

                    if (this.rows.length >= 5) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Batas Maksimum Tercapai',
                            text: 'Untuk menjaga performa dan ketelitian, Anda hanya dapat menambah maksimal 5 pekerja dalam satu sesi simpan.',
                            confirmButtonColor: '#3B82F6', // Sesuai tema biru Anda
                        });
                        return; // Berhenti di sini, tidak mengeksekusi baris bawahnya
                    }

                    const row = {
                        id: Date.now(),
                        gaji: 0,
                        gaji_bulanan: 0,
                        overtime: 0,
                        rate_hbn: 1.5,
                        gaji_hbn: 0,
                        workerId: '',
                        divisiId: '',
                        jabatanId: '',
                        kpj: '',
                        naker: '',
                        bpjsKesehatan: 0,
                        bpjsNaker: 0,
                        days: {
                            mon: '',
                            tue: '',
                            wed: '',
                            thu: '',
                            fri: '',
                            sat: '',
                            sun: ''
                        },
                        tunjangan: createFreshTunjangan(),
                    };

                    this.calculateBpjs(row);
                    this.rows.push(row);
                },

                removeRow(index) {
                    if (this.rows.length <= 1) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Minimal 1 Pekerja',
                            text: 'Anda harus memiliki minimal 1 pekerja dalam daftar.',
                            confirmButtonColor: '#3B82F6',
                        });
                        return;
                    }
                    this.rows.splice(index, 1);
                },
                get totalAllocation() {
                    return this.rows.reduce((sum, row) => sum + (parseInt(row.gaji) || 0), 0);
                },

                calculateBpjs(row) {
                    const umk = Number(window.unitInfo.umk) || 0;
                    const pctKesehatan = Number(window.unitInfo.pct_kesehatan) || 0;
                    const pctNaker = Number(window.unitInfo.pct_naker) || 0;

                    // Kalkulasi BPJS Kesehatan jika pekerja PUNYA nomor KPJ
                    if (row.kpj && row.kpj.toString().trim() !== '') {
                        row.bpjsKesehatan = Math.round(umk * (pctKesehatan / 100));
                    } else {
                        row.bpjsKesehatan = 0;
                    }

                    // Kalkulasi BPJS Naker jika pekerja PUNYA nomor Naker
                    if (row.naker && row.naker.toString().trim() !== '') {
                        row.bpjsNaker = Math.round(umk * (pctNaker / 100));
                    } else {
                        row.bpjsNaker = 0;
                    }
                },

                formatRupiah(amount, withCurrency = false) {
                    const value = Number(amount) || 0;

                    const formatted = new Intl.NumberFormat('id-ID', {
                        style: 'decimal',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(value);

                    return withCurrency ? `Rp ${formatted}` : formatted;
                },

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
