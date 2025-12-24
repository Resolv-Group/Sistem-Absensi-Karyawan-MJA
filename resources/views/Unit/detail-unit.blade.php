@extends('layout')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- 1. HEADER SECTION (Unchanged) --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <nav class="flex text-sm font-medium text-gray-500 mb-2">
                    <a href="{{ route('view.unit') }}" class="hover:text-gray-700 transition">Unit</a>
                    <span class="mx-2 text-gray-400">/</span>
                    <span class="text-blue-600">Detail</span>
                </nav>
                <div class="flex items-center gap-4">
                    <a href="{{ route('view.unit') }}"
                        class="group p-2 rounded-full border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 transform group-hover:-translate-x-0.5 transition" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Detail Unit</h1>
                        <p class="text-sm text-gray-500 mt-1">Informasi lengkap profil unit, PIC, dan kontrak.</p>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-3">
                <button onclick="confirmToggleStatus({{ $unit->id }}, {{ $unit->status_aktif }})"
                    class="px-4 py-2 text-sm font-medium border rounded-lg transition shadow-sm flex items-center gap-2
                    {{ $unit->status_aktif
                        ? 'text-red-600 bg-red-50 border-red-100 hover:bg-red-100'
                        : 'text-emerald-600 bg-emerald-50 border-emerald-100 hover:bg-emerald-100' }}">
                    @if ($unit->status_aktif)
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Nonaktifkan
                    @else
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Aktifkan
                    @endif
                </button>
                <a href="{{ route('view.ubah.unit', $unit->id) }}"
                    class="px-4 py-2 text-sm font-medium text-white bg-black border border-black rounded-lg hover:bg-gray-800 transition shadow-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Ubah Data
                </a>
            </div>
        </div>

        {{-- 2. TOP SECTION: IDENTITY & CONTRACT (Grid Layout) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">

            {{-- LEFT: Unit Profile --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden h-full flex flex-col">
                    <div class="h-24 bg-gradient-to-br from-gray-900 to-gray-800 relative">
                        <div class="absolute inset-0 opacity-20"
                            style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 16px 16px;">
                        </div>
                    </div>
                    <div class="px-6 pb-6 relative text-center flex-1">
                        <div class="relative -mt-12 inline-block">
                            <div
                                class="h-24 w-24 rounded-2xl border-4 border-white shadow-xl bg-white overflow-hidden flex items-center justify-center text-3xl font-black text-gray-800 tracking-tighter">
                                {{ substr($unit->nama_unit, 0, 2) }}
                            </div>
                        </div>
                        <h2 class="mt-4 text-xl font-bold text-gray-900 leading-tight">{{ $unit->nama_unit }}</h2>

                        <div class="mt-3 flex justify-center gap-2">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $unit->status_aktif ? 'bg-emerald-50 text-emerald-600 border border-emerald-100' : 'bg-red-50 text-red-600 border border-red-100' }}">
                                <span
                                    class="w-2 h-2 {{ $unit->status_aktif ? 'bg-emerald-500' : 'bg-red-500' }} rounded-full mr-2"></span>
                                {{ $unit->status_aktif ? 'Status Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>

                        <div class="mt-8 text-left space-y-4 border-t border-gray-100 pt-6">
                            <div class="flex justify-between items-center group cursor-default">
                                <span
                                    class="text-xs text-gray-400 uppercase font-bold tracking-wide group-hover:text-blue-600 transition">ID
                                    Unit</span>
                                <span
                                    class="font-mono text-xs font-bold text-gray-700 bg-gray-100 px-2.5 py-1 rounded-md group-hover:bg-blue-50 group-hover:text-blue-700 transition">{{ $unit->id }}</span>
                            </div>
                            <div class="flex justify-between items-start group">
                                <span
                                    class="text-xs text-gray-400 uppercase font-bold tracking-wide group-hover:text-blue-600 transition">Induk
                                    Mitra</span>
                                <span
                                    class="text-sm font-bold text-gray-900 text-right max-w-[60%] leading-snug group-hover:text-blue-700 transition">{{ $unit->namaMitra->nama_mitra ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-start group">
                                <span
                                    class="text-xs text-gray-400 uppercase font-bold tracking-wide group-hover:text-blue-600 transition">Mulai
                                    Perjanjian</span>
                                <span
                                    class="text-sm font-bold text-gray-900 text-right max-w-[60%] leading-snug group-hover:text-blue-700 transition">{{ formatTanggal($unit->mulai_perjanjian) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 grid grid-cols-1 divide-x divide-gray-100">
                        <a href="{{ route('stream.mou', $unit->id) }}" target="_blank"
                            class="flex items-center justify-center gap-2 py-4 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition group">

                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 transition" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M4 6h11a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2z" />
                            </svg>

                            <span>Lihat MOU</span>
                        </a>

                    </div>
                </div>
            </div>

            {{-- RIGHT: Contract & PIC --}}
            <div class="lg:col-span-2 flex flex-col gap-6">

                {{-- A. PIC Section (Interactive) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <div class="p-1.5 bg-blue-50 text-blue-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            Penanggung Jawab (PIC)
                        </h3>
                        @if ($unit->picUnit->count() > 0)
                            <span
                                class="text-xs font-bold bg-gray-100 text-gray-600 px-2 py-1 rounded-md">{{ $unit->picUnit->count() }}
                                Orang</span>
                        @endif
                    </div>

                    @if ($unit->picUnit->count() > 0)
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($unit->picUnit as $pic)
                                <div
                                    class="group flex items-center gap-4 p-4 rounded-xl border border-gray-100 bg-white hover:border-blue-200 hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 relative">
                                    {{-- Avatar --}}
                                    <div
                                        class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center text-blue-600 font-bold text-base shadow-sm group-hover:scale-110 transition-transform">
                                        {{ substr(optional($pic->staff)->nama, 0, 1) }}
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <p
                                            class="text-sm font-bold text-gray-900 truncate group-hover:text-blue-700 transition">
                                            {{ optional($pic->staff)->nama ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500 truncate">
                                            {{ optional($pic->staff)->jabatan ?? 'Staff' }}</p>
                                    </div>

                                    {{-- Call Action (Appears on Hover) --}}
                                    @if (optional($pic->staff)->telp)
                                        <a href="tel:{{ optional($pic->staff)->telp }}"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 opacity-0 group-hover:opacity-100 p-2 bg-blue-600 text-white rounded-lg shadow-lg hover:bg-blue-700 transition-all duration-200"
                                            title="Hubungi PIC">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                        </a>
                                    @endif


                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                            <p class="text-sm text-gray-500 font-medium">Belum ada PIC yang ditugaskan.</p>
                        </div>
                    @endif
                </div>

                {{-- B. Contract Details (Colorful & Visual) --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 flex-1">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <div class="p-1.5 bg-purple-50 text-purple-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 011.414.586l5.414 5.414a1 1 0 01.586 1.414V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            Detail Kontrak & Legalitas
                        </h3>
                        @if ($unit->dokumen)
                            <a href="{{ asset('storage/' . $unit->dokumen) }}" target="_blank"
                                class="text-xs font-bold text-blue-600 hover:underline flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                Lihat Dokumen
                            </a>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        {{-- 1. Pengajian --}}
                        <div
                            class="p-4 rounded-xl bg-gray-50 border border-gray-100 hover:border-purple-200 hover:bg-purple-50/30 transition group">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="p-1 rounded bg-white shadow-sm text-gray-500 group-hover:text-purple-600">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Sistem</span>
                            </div>
                            <p class="font-bold text-gray-900">
                                {{ $unit->sistem_pengajian == 1 ? 'Harian' : 'Borongan' }}
                            </p>
                        </div>

                        {{-- 2. Fee --}}
                        <div
                            class="p-4 rounded-xl bg-gray-50 border border-gray-100 hover:border-orange-200 hover:bg-orange-50/30 transition group">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="p-1 rounded bg-white shadow-sm text-gray-500 group-hover:text-orange-600">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                    </svg>
                                </div>
                                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Mgmt Fee</span>
                            </div>
                            <p class="font-bold text-gray-900">{{ $unit->persentase_management_fee }}%</p>
                        </div>

                        {{-- 3. Expiry Date --}}
                        <div
                            class="p-4 rounded-xl bg-gray-50 border border-gray-100 hover:border-emerald-200 hover:bg-emerald-50/30 transition group">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="p-1 rounded bg-white shadow-sm text-gray-500 group-hover:text-emerald-600">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <span class="text-[10px] uppercase font-bold text-gray-400 tracking-wider">Berakhir</span>
                            </div>
                            <p class="font-bold text-gray-900">
                                {{ \Carbon\Carbon::parse($unit->tgl_akhir_mou)->format('d M Y') }}</p>

                            {{-- Days Left Indicator --}}
                            @php $days = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($unit->tgl_akhir_mou), false); @endphp
                            <div class="mt-2 h-1 w-full bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full {{ $days < 30 ? 'bg-red-500' : 'bg-emerald-500' }}"
                                    style="width: {{ min(100, max(0, ($days / 365) * 100)) }}%"></div>
                            </div>
                        </div>


                    </div>

                </div>

            </div>

        </div>

        {{-- 3. BOTTOM SECTION: WORKER TABLE (Full Width) --}}
        <div class="flex items-center gap-4 mb-6">
            <h2 class="text-xl font-bold text-gray-900">Daftar Pekerja Unit</h2>
            <div class="h-px bg-gray-200 flex-1"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            {{-- Toolbar --}}
            <div
                class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/30">
                <div class="flex items-center gap-2">
                    <span
                        class="px-2.5 py-0.5 rounded-md bg-white border border-gray-200 text-gray-700 text-xs font-bold shadow-sm">
                        Total: {{ $totalPekerja }} Orang
                    </span>
                </div>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <div class="relative w-full sm:w-64">
                        <input type="text" placeholder="Cari pekerja..."
                            class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition bg-white">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    {{-- <a href="{{ route('view.tambah.unit-pekerja', $unit->id) }}"
                        class="px-4 py-2 bg-black text-white text-xs font-bold rounded-lg hover:bg-gray-800 transition flex items-center gap-2 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg> --}}

                    <a href="{{ route('view.tambah.unit-borongan', $unit->id) }}"
                    class="px-4 py-2 bg-black text-white text-xs font-bold rounded-lg hover:bg-gray-800 transition flex items-center gap-2 shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Tambah
                    </a>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto rounded-b-2xl">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-gray-50/50 border-b border-gray-100">
                <th class="pl-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider w-[250px]">Nama & NIK</th>
                <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Jabatan & Divisi</th>
                <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Gaji Pokok & Lembur</th>
                <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Dokumen</th>
                <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Periode PKWT</th>
                <th class="pr-6 py-4 text-right"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 bg-white">
            @forelse($pkwtPekerja as $pkwt)
                <tr class="hover:bg-blue-50/20 transition group">

                    {{-- 1. Name & NIK (Wider column) --}}
                    <td class="pl-6 py-5 align-top">
                        <div class="flex items-start gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition truncate max-w-[180px]" title="{{ $pkwt->pekerja->nama }}">
                                    {{ $pkwt->pekerja->nama }}
                                </p>
                                <div class="flex items-center gap-1 mt-0.5">
                                    <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0c0 .884-.956 1.65-2.123 2.155" /></svg>
                                    <p class="text-xs text-gray-500 font-mono tracking-tight">{{ $pkwt->pekerja->nik }}</p>
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- 2. Jabatan & Divisi (Stacked for clarity) --}}
                    <td class="px-4 py-5 align-top">
                        <div class="flex flex-col gap-1.5">
                            <span class="inline-flex items-center text-xs font-semibold text-gray-800 bg-gray-100 px-2.5 py-1 rounded-md border border-gray-200 w-fit max-w-[200px] truncate" title="{{ $pkwt->jabatan->nama  }}">
                                {{ $pkwt->jabatan->nama }}
                            </span>
                            <div class="flex items-center gap-1.5 text-xs text-gray-500 pl-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                <span class="truncate max-w-[180px]" title="{{ $pkwt->divisi->nama  }}">{{ $pkwt->divisi->nama  }}</span>
                            </div>
                        </div>
                    </td>

                    {{-- 3. Gaji & Lembur (Visual Separation) --}}
                    <td class="px-4 py-5 align-top">
                        <div class="space-y-2">
                            {{-- Gaji Pokok --}}
                            <div>
                                {{-- <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-0.5">Pokok</p> --}}
                                <p class="text-sm font-bold text-gray-900">
                                    Rp {{ number_format($pkwt->gaji_harian, 0, ',', '.') }}
                                    <span class="text-[10px] text-gray-400 font-normal">/hr</span>
                                </p>
                                <p class="text-sm font-bold text-gray-900">
                                    Rp {{ number_format($pkwt->gaji_harian, 0, ',', '.') }}
                                    <span class="text-[10px] text-gray-400 font-normal">/hr</span>
                                </p>
                            </div>

                            {{-- Lembur (Assuming variable exists, example logic) --}}
                            @if(isset($pkwt->gaji_lembur))
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wide mb-0.5">Lembur</p>
                                <p class="text-xs font-medium text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded w-fit">
                                    Rp {{ number_format($pkwt->gaji_lembur, 0, ',', '.') }}
                                    <span class="text-emerald-400 font-normal">/jam</span>
                                </p>
                            </div>
                            @endif
                        </div>
                    </td>

                    {{-- 4. Dokumen (Clean Button) --}}
                    @php $mime = $pkwt->dokumen_mime; @endphp
                    <td class="px-4 py-5 align-middle text-center">
                        @if ($mime)
                            <a href="{{ route('stream.pkwt', $pkwt->id) }}" target="_blank"
                               class="inline-flex flex-col items-center justify-center gap-1 p-2 rounded-lg hover:bg-gray-50 transition group/doc border border-transparent hover:border-gray-200">
                                @if(Str::startsWith($mime, 'application/pdf'))
                                    <svg class="w-6 h-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                    <span class="text-[10px] font-bold text-gray-500 group-hover/doc:text-red-600">PDF</span>
                                @else
                                    <svg class="w-6 h-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span class="text-[10px] font-bold text-gray-500 group-hover/doc:text-blue-600">IMG</span>
                                @endif
                            </a>
                        @else
                            <span class="text-xs text-gray-300 italic">-</span>
                        @endif
                    </td>

                    {{-- 5. Periode PKWT (Combined Date) --}}
                    <td class="px-4 py-5 align-middle text-center">
                        <div class="flex flex-col items-center gap-1">
                            <span class="text-xs font-medium text-gray-700 bg-gray-50 px-2 py-1 rounded">
                                {{ \Carbon\Carbon::parse($pkwt->tgl_mulai_pkwt)->format('d M Y') }}
                            </span>
                            <svg class="w-3 h-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                            <span class="text-xs font-bold {{ $pkwt->status_pkwt['color'] === 'red' ? 'text-red-600 bg-red-50' : 'text-emerald-600 bg-emerald-50' }} px-2 py-1 rounded">
                                {{ \Carbon\Carbon::parse($pkwt->tgl_akhir_pkwt)->format('d M Y') }}
                            </span>
                        </div>
                    </td>

                    {{-- 6. Actions --}}
                    <td class="pr-6 py-5 align-middle text-right">
                        <div class="flex justify-end gap-1 opacity-60 group-hover:opacity-100 transition-opacity">
                            <a href="#" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            </a>
                            <a href="#" class="p-2 text-gray-400 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition" title="Detail">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <div class="h-12 w-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                            </div>
                            <p class="font-medium text-sm">Belum ada pekerja terdaftar.</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

            {{-- <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 text-center">
               <button class="text-xs font-bold text-gray-500 hover:text-gray-900 transition">Lihat Selengkapnya &rarr;</button>
            </div> --}}
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function confirmToggleStatus(id, statusAktif) {
            const isAktif = statusAktif == 1;

            Swal.fire({
                title: isAktif ? 'Nonaktifkan Unit?' : 'Aktifkan Unit?',
                text: isAktif ?
                    'Unit ini tidak akan muncul di daftar aktif.' : 'Unit ini akan kembali aktif.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: isAktif ? '#dc2626' : '#059669',
                cancelButtonColor: '#6b7280',
                confirmButtonText: isAktif ? 'Ya, Nonaktifkan' : 'Ya, Aktifkan',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-2xl',
                    confirmButton: 'rounded-lg px-4 py-2',
                    cancelButton: 'rounded-lg px-4 py-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Adjust route to match your defined route name
                    fetch(`/unit/toggle-status/${id}`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false,
                                customClass: {
                                    popup: 'rounded-2xl'
                                }
                            }).then(() => location.reload());
                        })
                        .catch(() => {
                            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                        });
                }
            });
        }
    </script>
@endpush
