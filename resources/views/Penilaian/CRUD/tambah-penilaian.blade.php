@extends('layout')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER --}}
        <div class="mb-8 flex items-center gap-4">
            <a href={{ url()->previous() }}
                class="p-2 rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <nav class="flex text-sm font-medium text-gray-500 mb-1">
                    <span class="hover:text-gray-700">Unit</span>
                    <span class="mx-2 text-gray-300">/</span>
                    <span class="text-blue-600">Penilaian Pekerja</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Buat Penilaian Pekerja</h1>
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

        {{-- Grid Wrapper --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            {{-- SISI KIRI: FORM INPUT (8 Kolom) --}}
            <div class="lg:col-span-7 space-y-6">
                <form action="{{ route('buat.penilaian') }}" method="POST" enctype="multipart/form-data"
                    x-data="workerForm()" class="space-y-6">
                    @csrf
                    @method('POST')

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
                                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
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
                            <h3 class="font-bold text-gray-900">Daftar Penilaian Pekerja</h3>
                        </div>

                        <div class="p-6 space-y-6">
                            <template x-for="(row, index) in rows" :key="row.id">
                                <div
                                    class="bg-white rounded-[2.5rem] border border-gray-100 p-8 relative group transition hover:border-blue-200 hover:shadow-xl hover:shadow-blue-900/5">

                                    {{-- HEADER: Identity & Live Score --}}
                                    <div
                                        class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-10 pb-6 border-b border-gray-50">

                                        {{-- Left: Worker Info --}}
                                        <div class="flex items-center gap-5">
                                            {{-- Number Badge --}}
                                            <div
                                                class="flex-shrink-0 h-12 w-12 bg-blue-50 text-blue-600 text-sm font-black rounded-2xl flex items-center justify-center border border-blue-100 shadow-sm">
                                                <span x-text="index + 1"></span>
                                            </div>
                                            <div>
                                                <h3 class="text-xl font-black text-gray-900 tracking-tight"
                                                    x-text="row.nama"></h3>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span
                                                        class="text-[10px] font-bold text-gray-400 uppercase tracking-widest"
                                                        x-text="row.nik"></span>
                                                    <span class="h-1 w-1 rounded-full bg-gray-300"></span>
                                                    <span
                                                        class="text-[10px] font-black text-blue-500 uppercase tracking-widest">Penilaian
                                                        PKWT</span>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Right: Technical Score Display (Simplistic Vibe) --}}
                                        <div class="flex items-center gap-6">
                                            <div class="h-10 w-px bg-gray-100 hidden sm:block"></div>

                                            <div class="text-right flex items-center gap-4">
                                                <!-- Grade Badge (Simplistic & Premium) -->
                                                <div x-show="row.grade"
                                                    class="h-12 w-12 rounded-full flex items-center justify-center text-xl font-black border-2 transition-all duration-500 shadow-sm"
                                                    :class="{
                                                        'bg-emerald-50 border-emerald-200 text-emerald-600': row
                                                            .grade === 'A',
                                                        'bg-blue-50 border-blue-200 text-blue-600': row
                                                            .grade === 'B',
                                                        'bg-amber-50 border-amber-200 text-amber-600': row
                                                            .grade === 'C',
                                                        'bg-red-50 border-red-200 text-red-600': row
                                                            .grade === 'D'
                                                    }">
                                                    <span x-text="row.grade"></span>
                                                </div>

                                                <div>
                                                    <div class="flex items-center justify-end gap-1.5 mb-1">
                                                        <span class="flex h-1.5 w-1.5 rounded-full"
                                                            :class="row.grade === 'A' ? 'bg-emerald-500' : 'bg-blue-500'"></span>
                                                        <p
                                                            class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] leading-none">
                                                            Skor Evaluasi</p>
                                                    </div>
                                                    <div class="flex items-baseline justify-end gap-1">
                                                        <span
                                                            class="text-4xl font-black text-gray-900 tracking-tighter leading-none"
                                                            x-text="calculateTotal(row)"></span>
                                                        <span
                                                            class="text-[10px] font-black text-blue-500 uppercase">Poin</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- CONTENT: Input Grid --}}
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                        {{-- 1. Worker Combobox --}}
                                        <div x-data="workerCombobox(row)" x-init="init()" class="relative">
                                            <label
                                                class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nama
                                                Pekerja</label>
                                            <input type="hidden" :name="`pekerja[${index}][id_pekerja]`"
                                                x-model="selectedId">

                                            <div class="relative">
                                                <input type="text" x-model="search" @focus="open = true"
                                                    @click.outside="close()" placeholder="Cari nama atau NIK..." readonly
                                                    class="w-full pl-4 pr-10 py-3 text-sm font-medium text-gray-800 bg-gray-50 rounded-xl border-gray-200 focus:bg-white focus:border-blue-500 transition cursor-pointer">
                                            </div>
                                        </div>

                                        {{-- 2. MK --}}
                                        <div>
                                            <label
                                                class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">MK</label>
                                            <div class="relative">
                                                <input type="number" :name="`pekerja[${index}][mk]`"
                                                    x-model.number="row.mk"
                                                    class="w-full rounded-xl border-gray-200 bg-gray-50 text-sm font-bold text-gray-900 focus:bg-white focus:border-blue-500 py-3 px-4"
                                                    placeholder="0">
                                            </div>
                                        </div>

                                        {{-- 3. Poin Penilaian Dropdown --}}
                                        <div class="flex flex-col gap-1.5" x-data="{ open: false, points: [80, 60, 40, 20, 10, 0] }">
                                            <label
                                                class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1 ml-1">
                                                Absensi Kehadiran (25%)
                                            </label>

                                            <div class="relative">
                                                {{-- Hidden Input untuk Simpan Data (Pastikan field backend-nya sesuai, misal: absensi) --}}
                                                <input type="hidden" :name="`pekerja[${index}][absensi]`"
                                                    x-model="row.absensi">

                                                {{-- Button Trigger --}}
                                                <button type="button" @click="open = !open"
                                                    @click.outside="open = false"
                                                    class="w-full flex items-center justify-between bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-800 hover:bg-white hover:border-blue-300 transition-all shadow-sm outline-none focus:ring-2 focus:ring-blue-100">

                                                    {{-- Tampilkan Poin atau Placeholder --}}
                                                    <div class="flex items-center gap-2">
                                                        <template x-if="row.absensi !== undefined && row.absensi !== ''">
                                                            <span class="flex h-2 w-2 rounded-full bg-blue-500"></span>
                                                        </template>
                                                        <span
                                                            :class="row.absensi !== undefined && row.absensi !== '' ?
                                                                'text-gray-900' : 'text-gray-400'"
                                                            x-text="row.absensi !== undefined && row.absensi !== '' ? row.absensi + ' Poin' : 'Pilih absensi...'">
                                                        </span>
                                                    </div>

                                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-300"
                                                        :class="open ? 'rotate-180 text-blue-500' : ''" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>

                                                {{-- Dropdown List --}}
                                                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                    class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl shadow-blue-900/10 overflow-hidden"
                                                    x-cloak>

                                                    <div class="py-1">
                                                        <template x-for="p in points" :key="p">
                                                            <button type="button" @click="row.absensi = p; open = false"
                                                                class="w-full text-left px-4 py-3 text-sm font-bold transition-colors flex items-center justify-between group"
                                                                :class="row.absensi == p ? 'bg-blue-50 text-blue-700' :
                                                                    'text-gray-600 hover:bg-gray-50 hover:text-blue-600'">

                                                                <div class="flex items-center gap-3">
                                                                    <span class="text-xs text-gray-400 font-normal"
                                                                        x-show="row.absensi != p">#</span>
                                                                    <span x-text="p + ' Poin'"></span>
                                                                </div>

                                                                {{-- Ikon Checkmark jika terpilih --}}
                                                                <svg x-show="row.absensi == p"
                                                                    class="w-4 h-4 text-blue-600" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="3" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 4. Poin Pengetahuan Dropdown --}}
                                        <div class="flex flex-col gap-1.5" x-data="{ open: false, points: [50, 30, 20, 0] }">
                                            <label
                                                class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1 ml-1">
                                                Pengetahuan (25%)
                                            </label>

                                            <div class="relative">
                                                {{-- Hidden Input untuk Simpan Data (Pastikan field backend-nya sesuai, misal: pengetahuan) --}}
                                                <input type="hidden" :name="`pekerja[${index}][pengetahuan]`"
                                                    x-model="row.pengetahuan">

                                                {{-- Button Trigger --}}
                                                <button type="button" @click="open = !open"
                                                    @click.outside="open = false"
                                                    class="w-full flex items-center justify-between bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-800 hover:bg-white hover:border-blue-300 transition-all shadow-sm outline-none focus:ring-2 focus:ring-blue-100">

                                                    {{-- Tampilkan Poin atau Placeholder --}}
                                                    <div class="flex items-center gap-2">
                                                        <template
                                                            x-if="row.pengetahuan !== undefined && row.absensi !== ''">
                                                            <span class="flex h-2 w-2 rounded-full bg-blue-500"></span>
                                                        </template>
                                                        <span
                                                            :class="row.pengetahuan !== undefined && row
                                                                .pengetahuan !== '' ?
                                                                'text-gray-900' : 'text-gray-400'"
                                                            x-text="row.pengetahuan !== undefined && row.pengetahuan !== '' ? row.pengetahuan + ' Poin' : 'Pilih pengetahuan...'">
                                                        </span>
                                                    </div>

                                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-300"
                                                        :class="open ? 'rotate-180 text-blue-500' : ''" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>

                                                {{-- Dropdown List --}}
                                                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                    class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl shadow-blue-900/10 overflow-hidden"
                                                    x-cloak>

                                                    <div class="py-1">
                                                        <template x-for="p in points" :key="p">
                                                            <button type="button"
                                                                @click="row.pengetahuan = p; open = false"
                                                                class="w-full text-left px-4 py-3 text-sm font-bold transition-colors flex items-center justify-between group"
                                                                :class="row.pengetahuan == p ? 'bg-blue-50 text-blue-700' :
                                                                    'text-gray-600 hover:bg-gray-50 hover:text-blue-600'">

                                                                <div class="flex items-center gap-3">
                                                                    <span class="text-xs text-gray-400 font-normal"
                                                                        x-show="row.pengetahuan != p">#</span>
                                                                    <span x-text="p + ' Poin'"></span>
                                                                </div>

                                                                {{-- Ikon Checkmark jika terpilih --}}
                                                                <svg x-show="row.pengetahuan == p"
                                                                    class="w-4 h-4 text-blue-600" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="3" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- 5. Poin Kualitas Kerja & Kinerja Dropdown --}}
                                        <div class="flex flex-col gap-1.5" x-data="{ open: false, points: [55, 35, 25, 15] }">
                                            <label
                                                class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1 ml-1">
                                                Kualitas Kerja & Kinerja (30%)
                                            </label>

                                            <div class="relative">
                                                {{-- Hidden Input untuk Simpan Data (Pastikan field backend-nya sesuai, misal: kualitas) --}}
                                                <input type="hidden" :name="`pekerja[${index}][kualitas]`"
                                                    x-model="row.kualitas">

                                                {{-- Button Trigger --}}
                                                <button type="button" @click="open = !open"
                                                    @click.outside="open = false"
                                                    class="w-full flex items-center justify-between bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-800 hover:bg-white hover:border-blue-300 transition-all shadow-sm outline-none focus:ring-2 focus:ring-blue-100">

                                                    {{-- Tampilkan Poin atau Placeholder --}}
                                                    <div class="flex items-center gap-2">
                                                        <template x-if="row.kualitas !== undefined && row.kualitas !== ''">
                                                            <span class="flex h-2 w-2 rounded-full bg-blue-500"></span>
                                                        </template>
                                                        <span
                                                            :class="row.kualitas !== undefined && row.kualitas !== '' ?
                                                                'text-gray-900' : 'text-gray-400'"
                                                            x-text="row.kualitas !== undefined && row.kualitas !== '' ? row.kualitas + ' Poin' : 'Pilih kualitas...'">
                                                        </span>
                                                    </div>

                                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-300"
                                                        :class="open ? 'rotate-180 text-blue-500' : ''" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>

                                                {{-- Dropdown List --}}
                                                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                    class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl shadow-blue-900/10 overflow-hidden"
                                                    x-cloak>

                                                    <div class="py-1">
                                                        <template x-for="p in points" :key="p">
                                                            <button type="button" @click="row.kualitas = p; open = false"
                                                                class="w-full text-left px-4 py-3 text-sm font-bold transition-colors flex items-center justify-between group"
                                                                :class="row.kualitas == p ? 'bg-blue-50 text-blue-700' :
                                                                    'text-gray-600 hover:bg-gray-50 hover:text-blue-600'">

                                                                <div class="flex items-center gap-3">
                                                                    <span class="text-xs text-gray-400 font-normal"
                                                                        x-show="row.kualitas != p">#</span>
                                                                    <span x-text="p + ' Poin'"></span>
                                                                </div>

                                                                {{-- Ikon Checkmark jika terpilih --}}
                                                                <svg x-show="row.kualitas == p"
                                                                    class="w-4 h-4 text-blue-600" fill="none"
                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="3" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- 6. Poin Sikap Kerja & Loyalitas Dropdown --}}
                                        <div class="flex flex-col gap-1.5" x-data="{ open: false, points: [35, 25, 15, 5] }">
                                            <label
                                                class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1 ml-1">
                                                Sikap Kerja & Loyalitas (20%)
                                            </label>

                                            <div class="relative">
                                                {{-- Hidden Input untuk Simpan Data (Pastikan field backend-nya sesuai, misal: sikap) --}}
                                                <input type="hidden" :name="`pekerja[${index}][sikap]`"
                                                    x-model="row.sikap">

                                                {{-- Button Trigger --}}
                                                <button type="button" @click="open = !open"
                                                    @click.outside="open = false"
                                                    class="w-full flex items-center justify-between bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm font-bold text-gray-800 hover:bg-white hover:border-blue-300 transition-all shadow-sm outline-none focus:ring-2 focus:ring-blue-100">

                                                    {{-- Tampilkan Poin atau Placeholder --}}
                                                    <div class="flex items-center gap-2">
                                                        <template x-if="row.sikap !== undefined && row.absensi !== ''">
                                                            <span class="flex h-2 w-2 rounded-full bg-blue-500"></span>
                                                        </template>
                                                        <span
                                                            :class="row.sikap !== undefined && row.sikap !== '' ?
                                                                'text-gray-900' : 'text-gray-400'"
                                                            x-text="row.sikap !== undefined && row.sikap !== '' ? row.sikap + ' Poin' : 'Pilih poin sikap...'">
                                                        </span>
                                                    </div>

                                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-300"
                                                        :class="open ? 'rotate-180 text-blue-500' : ''" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>

                                                {{-- Dropdown List --}}
                                                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                                    x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                                    class="absolute z-50 w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl shadow-blue-900/10 overflow-hidden"
                                                    x-cloak>

                                                    <div class="py-1">
                                                        <template x-for="p in points" :key="p">
                                                            <button type="button" @click="row.sikap = p; open = false"
                                                                class="w-full text-left px-4 py-3 text-sm font-bold transition-colors flex items-center justify-between group"
                                                                :class="row.sikap == p ? 'bg-blue-50 text-blue-700' :
                                                                    'text-gray-600 hover:bg-gray-50 hover:text-blue-600'">

                                                                <div class="flex items-center gap-3">
                                                                    <span class="text-xs text-gray-400 font-normal"
                                                                        x-show="row.sikap != p">#</span>
                                                                    <span x-text="p + ' Poin'"></span>
                                                                </div>

                                                                {{-- Ikon Checkmark jika terpilih --}}
                                                                <svg x-show="row.sikap == p" class="w-4 h-4 text-blue-600"
                                                                    fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="3" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            </button>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- 7. Field Keterangan (Full Width) --}}
                                        <div class="sm:col-span-2 flex flex-col gap-1.5">
                                            <label
                                                class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1 ml-1">
                                                Keterangan / Alasan Evaluasi
                                            </label>

                                            <div class="relative group">
                                                <textarea :name="`pekerja[${index}][keterangan]`" x-model="row.keterangan" rows="3"
                                                    placeholder="Tuliskan detail performa atau alasan pemberian skor di sini..."
                                                    class="w-full bg-gray-50 border border-gray-200 rounded-[1.5rem] px-5 py-4 text-sm font-medium text-gray-800 focus:bg-white focus:border-blue-400 focus:ring-4 focus:ring-blue-50 outline-none transition-all shadow-sm placeholder:text-gray-300 resize-none"></textarea>

                                                {{-- Dekorasi Ikon Kecil di pojok bawah --}}
                                                <div
                                                    class="absolute bottom-4 right-4 text-gray-300 pointer-events-none group-focus-within:text-blue-400 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <input type="hidden" :name="`pekerja[${index}][total_skor]`" :value="row.totalPoin">
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- FOOTER / TOTAL --}}
                    <div
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
                                <div
                                    class="flex items-center gap-2 px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg">

                                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
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

                                {{-- Save Button --}}
                                <button type="submit"
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

            <div class="lg:col-span-5 sticky top-8">
                <div
                    class="bg-white rounded-[2.5rem] border border-gray-200 shadow-sm overflow-hidden flex flex-col max-h-[85vh]">

                    {{-- Header Panduan (Teks Diperbesar) --}}
                    <div class="p-8 bg-gray-50 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="p-2.5 bg-blue-600 rounded-xl shadow-lg shadow-blue-100">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-black text-gray-900 uppercase tracking-widest">Parameter
                                    Penilaian</h3>
                                <p class="text-xs text-gray-400 font-bold uppercase mt-0.5 tracking-tighter">Standar Acuan
                                    Evaluasi Pekerja</p>
                            </div>
                        </div>
                    </div>

                    {{-- Scrollable Content (Teks & Spasi Diperbesar) --}}
                    <div class="p-8 overflow-y-auto custom-scrollbar space-y-10">

                        {{-- 1. ABSENSI --}}
                        <section>
                            <div class="flex items-center justify-between mb-5">
                                <h4 class="text-sm font-black text-blue-600 uppercase tracking-widest">1. Absensi Kehadiran
                                </h4>
                                <span
                                    class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-black rounded-lg border border-blue-100">BOBOT
                                    25%</span>
                            </div>
                            <div class="grid grid-cols-1 gap-3">
                                @foreach ([['p' => 80, 'd' => 'Selalu Hadir (kecuali cuti)'], ['p' => 60, 'd' => 'Tidak hadir 1 - 2 kali (kecuali cuti)'], ['p' => 40, 'd' => 'Tidak hadir 3 - 4 kali (kecuali cuti)'], ['p' => 20, 'd' => 'Tidak hadir 5 - 6 kali (kecuali cuti)'], ['p' => 10, 'd' => 'Tidak hadir 7 - 8 kali (kecuali cuti)'], ['p' => 0, 'd' => 'Tidak hadir > 8 kali (kecuali cuti)']] as $item)
                                    <div
                                        class="flex items-center gap-4 p-3 rounded-2xl border border-transparent hover:border-blue-50 hover:bg-blue-50/30 transition-all">
                                        <span
                                            class="text-xs font-black text-blue-700 bg-white shadow-sm border border-blue-100 w-10 h-8 flex items-center justify-center rounded-lg flex-shrink-0">{{ $item['p'] }}</span>
                                        <p class="text-sm text-gray-600 font-medium">{{ $item['d'] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </section>

                        {{-- 2. PENGETAHUAN --}}
                        <section>
                            <div class="flex items-center justify-between mb-5">
                                <h4 class="text-sm font-black text-indigo-600 uppercase tracking-widest">2. Pengetahuan &
                                    SOP</h4>
                                <span
                                    class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-black rounded-lg border border-indigo-100">BOBOT
                                    25%</span>
                            </div>
                            <div class="space-y-4">
                                @foreach ([['p' => 50, 't' => 'Sangat Baik', 'd' => 'Tidak ada penyimpangan dalam proses kerja.'], ['p' => 30, 't' => 'Baik', 'd' => 'Pernah terjadi penyimpangan namun tidak mempengaruhi kualitas dan kuantitas kerja.'], ['p' => 20, 't' => 'Kurang Baik', 'd' => 'Pernah terjadi penyimpangan dan mempengaruhi kualitas dan kuantitas kerja.'], ['p' => 0, 't' => 'Tidak Baik', 'd' => 'Sering terjadi penyimpangan.']] as $item)
                                    <div class="flex items-start gap-4">
                                        <span
                                            class="text-xs font-black text-indigo-700 bg-indigo-50 w-10 h-8 flex items-center justify-center rounded-lg flex-shrink-0 mt-1">{{ $item['p'] }}</span>
                                        <div>
                                            <span
                                                class="text-[11px] font-black text-indigo-900 uppercase tracking-wider">{{ $item['t'] }}</span>
                                            <p class="text-sm text-gray-500 font-medium leading-relaxed mt-1">
                                                {{ $item['d'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>

                        {{-- 3. KUALITAS KERJA --}}
                        <section>
                            <div class="flex items-center justify-between mb-5">
                                <h4 class="text-sm font-black text-emerald-600 uppercase tracking-widest">3. Kualitas &
                                    Kinerja</h4>
                                <span
                                    class="px-3 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-lg border border-emerald-100">BOBOT
                                    30%</span>
                            </div>
                            <div class="space-y-5">
                                @foreach ([['p' => 55, 't' => 'Sangat Baik', 'd' => 'Hasil pekerjaan baik dan terpuji, patut dijadikan contoh dan memiliki ide positif untuk kemajuan perusahaan.'], ['p' => 35, 't' => 'Baik', 'd' => 'Hasil pekerjaan cukup baik dan bisa dijadikan contoh dan menciptakan cara kerja yang lebih baik, walaupun untuk itu perlu motivasi tertentu.'], ['p' => 25, 't' => 'Kurang Baik', 'd' => 'Sekedar dapat bekerja dan memperoleh hasil dan tidak memiliki ide pada langkah pelaksanaan kerja.'], ['p' => 15, 't' => 'Tidak Baik', 'd' => 'Hasil kerja kacau dan tidak memenuhi syarat serta memiliki sikap acuh tak acuh dan tidak memiliki inisiatif kerja.']] as $item)
                                    <div class="flex items-start gap-4">
                                        <span
                                            class="text-xs font-black text-emerald-700 bg-emerald-50 w-10 h-8 flex items-center justify-center rounded-lg flex-shrink-0 mt-1">{{ $item['p'] }}</span>
                                        <div>
                                            <span
                                                class="text-[11px] font-black text-emerald-900 uppercase tracking-wider">{{ $item['t'] }}</span>
                                            <p class="text-sm text-gray-500 font-medium leading-relaxed mt-1">
                                                {{ $item['d'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>

                        {{-- 4. SIKAP KERJA --}}
                        <section>
                            <div class="flex items-center justify-between mb-5">
                                <h4 class="text-sm font-black text-orange-600 uppercase tracking-widest">4. Sikap &
                                    Loyalitas</h4>
                                <span
                                    class="px-3 py-1 bg-orange-50 text-orange-600 text-[10px] font-black rounded-lg border border-orange-100">BOBOT
                                    20%</span>
                            </div>
                            <div class="space-y-5">
                                @foreach ([['p' => 35, 't' => 'Sangat Baik', 'd' => 'Tidak pernah mendapat surat peringatan / teguran lisan dan selalu berhasil menyelesaikan tugas dengan penuh percaya diri.'], ['p' => 25, 't' => 'Baik', 'd' => 'Dapat teguran lisan max 2 kali, tanpa Surat Peringatan, mampu bertahan serta ada usaha mengatasi kesukaran dan memperbaiki kinerja.'], ['p' => 15, 't' => 'Kurang Baik', 'd' => 'Dapat teguran lisan max 1 kali, Surat Peringatan max 1 kali dan mampu bertahan dan memperbaiki kinerja dengan usaha sekedarnya.'], ['p' => 5, 't' => 'Tidak Baik', 'd' => 'Dapat teguran lisan > 1 kali, Surat Peringatan > 1 kali dan mengalami putus asa serta menimbulkan efek negatif pada kelompok kerja.']] as $item)
                                    <div class="flex items-start gap-4">
                                        <span
                                            class="text-xs font-black text-orange-700 bg-orange-50 w-10 h-8 flex items-center justify-center rounded-lg flex-shrink-0 mt-1">{{ $item['p'] }}</span>
                                        <div>
                                            <span
                                                class="text-[11px] font-black text-orange-900 uppercase tracking-wider">{{ $item['t'] }}</span>
                                            <p class="text-sm text-gray-500 font-medium leading-relaxed mt-1">
                                                {{ $item['d'] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>

                    </div>

                    {{-- Footer Info --}}
                    <div class="p-5 bg-gray-900 flex items-center justify-center gap-3">
                        <div class="h-2 w-2 rounded-full bg-blue-500 animate-pulse"></div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Total Akumulasi
                            Maksimal: 56.0 Poin</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        window.oldRows = @js(
    old('pekerja')
        ? // Jika ada data 'old' dari session (setelah gagal validasi)
        collect(old('pekerja'))->map(function ($item, $index) use ($pkwtList) {
            // Cari data asli pekerja dari pkwtList untuk mendapatkan Nama & NIK
            $original = $pkwtList->firstWhere('id_pekerja', $item['id_pekerja']);
            return [
                'id' => $item['id'] ?? ($original->id ?? null),
                'workerId' => $item['id_pekerja'],
                'absensi' => $item['absensi'] ?? '',
                'pengetahuan' => $item['pengetahuan'] ?? '',
                'kualitas' => $item['kualitas'] ?? '',
                'sikap' => $item['sikap'] ?? '',
                'mk' => $item['mk'] ?? 0,
                'keterangan' => $item['keterangan'] ?? '',
                'nama' => $original->pekerja->nama ?? 'Tidak Ditemukan',
                'nik' => $original->pekerja->nik ?? '-',
            ];
        })
        : $pkwtList->map(function ($item) {
            return [
                'id' => $item->id,
                'workerId' => $item->id_pekerja,
                'gaji' => $item->gaji_harian,
                // Tambahkan field lain jika perlu
                'tglMulai' => $item->tgl_mulai_pkwt,
                'tglAkhir' => $item->tgl_akhir_pkwt,
                // Kita simpan nama & nik untuk tampilan combobox readonly
                'nama' => $item->pekerja->nama,
                'nik' => $item->pekerja->nik,
            ];
        }),
);

        function workerCombobox(row) {
            return {
                open: false,
                search: '',
                selectedId: row.workerId || null,

                init() {
                    // If an ID is already set (e.g., from old() data), populate search field
                    // if (this.selectedId) {
                    //     const p = window.workersData.find(w => w.id == this.selectedId);
                    //     if (p) {
                    //         this.search = `${p.nama} (${p.nik})`;
                    //     }
                    // }
                    if (row.nama && row.nik) {
                        this.search = `${row.nama} (${row.nik})`;
                    }
                },
            }
        }

        // 1. DATA SOURCES (You can pass these from Controller later)
        window.workersData = @json($pekerjaList ?? []);

        // 2. FORM LOGIC
        function workerForm() {
            return {
                rows: window.oldRows && window.oldRows.length ?
                    window.oldRows : [{
                        id: Date.now(),
                        workerId: '',
                        pengetahuan: 0,
                        kualitas: 0,
                        sikap: 0,
                        totalPoin: 0
                    }],

                addRow() {
                    this.rows.push({
                        id: Date.now(),
                        gaji: 0,
                        workerId: '',
                        tglMulai: '',
                        tglAkhir: '',
                    });
                },

                // Fungsi Kalkulasi
                calculateTotal(row) {
                    const a = parseInt(row.absensi) || 0;
                    const p = parseInt(row.pengetahuan) || 0;
                    const k = parseInt(row.kualitas) || 0;
                    const s = parseInt(row.sikap) || 0;

                    // Rumus Bobot: (A*25%) + (P*25%) + (K*30%) + (S*20%)
                    const score = (a * 0.25) + (p * 0.25) + (k * 0.3) + (s * 0.2);
                    row.totalPoin = score.toFixed(0);

                    if (score >= 50) {
                        row.grade = 'A';
                    } else if (score >= 41) {
                        row.grade = 'B';
                    } else if (score >= 29) {
                        row.grade = 'C';
                    } else {
                        row.grade = 'D';
                    }

                    return row.totalPoin;
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
