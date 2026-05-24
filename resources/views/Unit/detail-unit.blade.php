@extends('layout')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- 1. HEADER SECTION (Unchanged) --}}
        @php
            $isComplete =
                $unit->persentase_management_fee !== null &&
                $unit->bpjs_kesehatan !== null &&
                $unit->bpjs_naker !== null &&
                $unit->umk !== null &&
                $unit->tunjangan !== null;
        @endphp
        <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">
            <div>
                <nav class="flex text-sm font-medium text-gray-500 mb-2">
                    <span class="hover:text-gray-700">Unit</a>
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
                        Nonaktifkan Unit
                    @else
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Aktifkan Unit
                    @endif
                </button>
            </div>


        </div>

        {{-- Note --}}
        @if (!$isComplete)
            <div class="mt-4 bg-yellow-50 border border-yellow-100 p-4 rounded-xl">
                <p class="text-sm text-yellow-600 font-medium">*Harap perbarui unit ini sebelum menambahkan karyawan.</p>

                {{-- //list yang belum di isi  --}}
                <p class="mt-2 text-sm text-yellow-600 font-medium">List yang belum diisi:</p>
                <ul class="list-disc pl-6">
                    @if ($unit->persentase_management_fee == null)
                        <li class="text-sm text-yellow-600">Persentase Management Fee</li>
                    @endif
                    @if ($unit->bpjs_kesehatan == null)
                        <li class="text-sm text-yellow-600">BPJS Kesehatan</li>
                    @endif
                    @if ($unit->bpjs_naker == null)
                        <li class="text-sm text-yellow-600">BPJS Naker</li>
                    @endif
                    @if ($unit->umk == null)
                        <li class="text-sm text-yellow-600">UMK</li>
                    @endif
                    @if ($unit->tunjangan == null)
                        <li class="text-sm text-yellow-600">Tunjangan</li>
                    @endif
                </ul>
            </div>
        @endif

        {{-- 2. TOP SECTION: IDENTITY & CONTRACT (Grid Layout) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10 mt-4">

            {{-- LEFT: Unit Profile --}}
            <div class="lg:col-span-1" x-data="unitManager">
                <div
                    class="bg-white rounded-[1.5rem] shadow-sm border border-gray-200 overflow-hidden h-full flex flex-col">
                    {{-- Banner Section --}}
                    <div class="h-28 bg-slate-800 relative">
                        <div class="absolute inset-0 opacity-10"
                            style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 16px 16px;">
                        </div>

                        {{-- System Badge Overlay --}}
                        <div class="absolute top-4 left-1/2 -translate-x-1/2 w-max">
                            <span
                                class="px-3 py-1 {{ $unit->sistem_pengajian == 1 ? 'bg-purple-500' : 'bg-orange-500' }} text-white text-[12px] font-black uppercase tracking-[0.2em] rounded-full shadow-lg">
                                Sistem {{ $unit->sistem_pengajian == 1 ? 'Harian' : 'Borongan' }}
                            </span>
                        </div>
                    </div>

                    <div class="px-8 pb-8 relative text-center flex-1">
                        {{-- Logo Initials --}}
                        <div class="relative -mt-14 inline-block">
                            <div
                                class="h-28 w-28 rounded-2xl border-[6px] border-white shadow-2xl bg-white flex items-center justify-center text-4xl font-black text-slate-800 tracking-tighter">
                                {{ substr($unit->nama_unit, 0, 2) }}
                            </div>
                        </div>

                        <h2 class="mt-4 text-2xl font-black text-slate-900 tracking-tight leading-none">
                            {{ $unit->nama_unit }}</h2>

                        <div class="mt-3 flex justify-center">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest {{ $unit->status_aktif ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} border {{ $unit->status_aktif ? 'border-emerald-100' : 'border-rose-100' }}">
                                <span
                                    class="w-1.5 h-1.5 {{ $unit->status_aktif ? 'bg-emerald-500' : 'bg-rose-500' }} rounded-full mr-2"></span>
                                {{ $unit->status_aktif ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        {{-- 1. OPERATIONAL STATS GRID (Pekerja & PKWT Expiring) --}}
                        <div class="mt-8 grid grid-cols-2 gap-3">
                            {{-- Total Pekerja --}}
                            <div class="p-4 bg-slate-50 border border-slate-100 rounded-[1.25rem] text-center">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Pekerja
                                </p>
                                <div class="flex items-center justify-center gap-1.5">
                                    <span
                                        class="text-xl font-black text-slate-800">{{ $unit->pkwt_count ?? $unit->pkwt->count() }}</span>
                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                            </div>

                            {{-- PKWT Expiring dalam 30 hari --}}
                            {{-- PKWT Expiring dalam 30 hari --}}
                            @php
                                $expiringSoon = $unit->pkwt
                                    ->filter(function ($p) {
                                        return \Carbon\Carbon::parse($p->tgl_akhir_pkwt)->diffInDays(now(), false) >=
                                            -30 && \Carbon\Carbon::parse($p->tgl_akhir_pkwt)->isFuture();
                                    })
                                    ->count();
                            @endphp
                            <div
                                class="p-4 {{ $expiringSoon > 0 ? 'bg-orange-50 border-orange-100' : 'bg-slate-50 border-slate-100' }} border rounded-[1.25rem] text-center">
                                {{-- Teks diganti ke Bahasa Indonesia --}}
                                <p
                                    class="text-[9px] font-black {{ $expiringSoon > 0 ? 'text-orange-400' : 'text-slate-400' }} uppercase tracking-widest mb-1">
                                    Segera Berakhir
                                </p>
                                <div class="flex items-center justify-center gap-1.5">
                                    <span
                                        class="text-xl font-black {{ $expiringSoon > 0 ? 'text-orange-600' : 'text-slate-800' }}">
                                        {{ $expiringSoon }}
                                    </span>
                                    <span class="text-[9px] text-slate-400 font-bold uppercase">PKWT</span>

                                </div>
                            </div>
                        </div>

                        {{-- 1. CONTRACT TIMELINE (New Value Element) --}}
                        <div class="mt-8 text-left">
                            @php
                                $start = \Carbon\Carbon::parse($unit->mulai_perjanjian);
                                $end = \Carbon\Carbon::parse($unit->tgl_akhir_mou);
                                $now = \Carbon\Carbon::now();
                                $totalDays = $start->diffInDays($end);
                                $elapsedDays = $start->diffInDays($now);
                                $percentage = $totalDays > 0 ? min(100, max(0, ($elapsedDays / $totalDays) * 100)) : 0;
                            @endphp
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Masa
                                    Kontrak</span>
                                <span class="text-[11px] font-bold text-slate-700">{{ round($percentage) }}%</span>
                            </div>
                            <div class="h-2 w-full bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 transition-all duration-1000"
                                    style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="flex justify-between mt-2">
                                <span
                                    class="text-[9px] text-slate-400 font-bold uppercase">{{ $start->format('M Y') }}</span>
                                <span
                                    class="text-[9px] text-slate-400 font-bold uppercase">{{ $end->format('M Y') }}</span>
                            </div>
                        </div>

                        {{-- 2. METADATA LIST --}}
                        <div class="mt-8 text-left space-y-4 border-t border-gray-100 pt-6">
                            <div class="flex justify-between items-center group">
                                <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest">Induk
                                    Mitra</span>
                                <span
                                    class="text-xs font-bold text-slate-700 truncate max-w-[150px]">{{ $unit->namaMitra->nama_mitra ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center group">
                                <span class="text-[10px] text-slate-400 font-black uppercase tracking-widest">ID Unit</span>
                                <span
                                    class="font-mono text-[11px] font-black text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md">{{ $unit->id }}</span>
                            </div>
                        </div>

                        {{-- 3. FINANCIAL SNAPSHOT (New Element) --}}
                        <div
                            class="mt-6 p-4 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                            <div class="text-left">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Rate UMK</p>
                                <p class="text-sm font-black text-slate-800">
                                    Rp{{ number_format($unit->umk, 0, ',', '.') }}</p>
                            </div>
                            <div class="h-8 w-px bg-slate-200"></div>
                            <div class="text-right">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Mgmt Fee</p>
                                <p class="text-sm font-black text-emerald-600">
                                    {{ $unit->persentase_management_fee ?? 0 }}%</p>
                            </div>
                        </div>

                        {{-- 4. SAFE ACTIONS --}}
                        <div class="mt-8">
                            @if ($unit->sistem_pengajian == 1)
                                {{-- Layout untuk Sistem Harian (2 Tombol) --}}
                                <div class="grid grid-cols-2 gap-3">
                                    <button type="button"
                                        onclick="checkUnitRequirements('{{ route('view.tambah.unit-pekerja', $unit->id) }}')"
                                        class="flex items-center justify-center gap-2 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-blue-500 hover:text-blue-600 transition active:scale-95 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Pekerja
                                    </button>
                                    <a href="{{ route('view.ubah.unit', $unit->id) }}"
                                        class="flex items-center justify-center gap-2 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-blue-500 hover:text-blue-600 transition active:scale-95 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Unit
                                    </a>

                                    {{-- Kas Kecil Trigger --}}
                                    <button type="button" @click="open('kas')"
                                        class="flex items-center justify-center gap-2 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-emerald-500 hover:text-emerald-600 transition shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Kas Kecil
                                    </button>

                                    {{-- Asset Trigger --}}
                                    <button type="button" @click="open('asset')"
                                        class="flex items-center justify-center gap-2 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-blue-500 hover:text-blue-600 transition shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        Asset Unit
                                    </button>
                                </div>
                            @else
                                {{-- Layout untuk Sistem Borongan (3 Tombol) --}}
                                <div class="space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <button type="button"
                                            onclick="checkUnitRequirements('{{ route('view.tambah.unit-pekerja', $unit->id) }}')"
                                            class="flex items-center justify-center gap-2 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-blue-500 hover:text-blue-600 transition active:scale-95 shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            Pekerja
                                        </button>
                                        {{-- Tombol Borongan Baru --}}
                                        <button type="button"
                                            onclick="checkUnitRequirements('{{ route('view.tambah.unit-borongan', $unit->id) }}')"
                                            class="flex items-center justify-center gap-2 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-orange-500 hover:text-orange-600 transition active:scale-95 shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                            Borongan
                                        </button>

                                        {{-- Kas Kecil Button --}}
                                        <button type="button" @click="open('kas')"
                                            class="flex items-center justify-center gap-2 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-emerald-500 hover:text-emerald-600 transition shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Kas Kecil
                                        </button>

                                        <button type="button" @click="open('asset')"
                                            class="flex items-center justify-center gap-2 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-blue-500 hover:text-blue-600 transition shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                            Asset Unit
                                        </button>
                                    </div>
                                    <a href="{{ route('view.ubah.unit', $unit->id) }}"
                                        class="w-full flex items-center justify-center gap-2 py-3 bg-white border border-slate-200 text-slate-700 rounded-xl text-[10px] font-black uppercase tracking-widest hover:border-blue-500 hover:text-blue-600 transition active:scale-95 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit Data Unit
                                    </a>
                                </div>
                            @endif

                        </div>
                    </div>

                    {{-- Footer: Document Stream --}}
                    <a href="{{ route('stream.mou', $unit->id) }}" target="_blank"
                        class="group flex items-center justify-center gap-3 py-5 bg-slate-50 border-t border-slate-100 hover:bg-white transition-colors">
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-600" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span
                            class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] group-hover:text-blue-600">Lihat
                            MOU</span>
                    </a>
                </div>

                {{-- DYNAMIC CRUD MODAL --}}
                <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-cloak>
                    {{-- Overlay --}}
                    <div x-show="showModal" x-transition.opacity @click="showModal = false"
                        class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>

                    <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        class="relative w-full max-w-4xl bg-white rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">

                        {{-- HEADER --}}
                        <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between"
                            :class="activeType === 'kas' ? 'bg-emerald-50/50' : 'bg-blue-50/50'">
                            <div>
                                <h3 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                                    <span
                                        x-text="activeType === 'kas' ? 'Manajemen Kas Kecil' : 'Manajemen Asset Unit'"></span>
                                </h3>
                            </div>

                            <div class="flex items-center gap-2">
                                {{-- Toggle Button --}}
                                <button @click="view === 'list' ? openForm() : view = 'list'"
                                    class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                                    :class="view === 'list' ? 'bg-slate-800 text-white shadow-lg' :
                                        'bg-slate-100 text-slate-500'">
                                    <span x-text="view === 'list' ? '+ Tambah Baru' : '← Lihat Daftar'"></span>
                                </button>
                                <button @click="showModal = false"
                                    class="p-2 text-slate-400 hover:text-rose-500 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        {{-- CONTENT: LIST VIEW --}}
                        <div x-show="view === 'list'" class="flex-1 overflow-y-auto p-8">
                            <p class="text-xs text-slate-400 italic mb-4">Menampilkan riwayat data terakhir...</p>
                            {{-- (Table List Code Here) --}}
                        </div>

                        {{-- CONTENT: FORM VIEW (Multiple Rows) --}}
                        <!-- CONTENT: FORM VIEW (Scrollable with Fixed Dashboard Footer) -->
                        <div x-show="view === 'form'" class="flex flex-col h-full overflow-hidden bg-slate-50/50">

                            {{-- FIXED SUB-HEADER --}}
                            <div
                                class="px-8 py-4 bg-white border-b border-slate-100 flex justify-between items-center shadow-sm z-10">
                                <div class="flex items-center gap-4">
                                    <span
                                        class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                        Draft: <span x-text="entries.length" class="text-slate-800"></span> Transaksi
                                    </span>
                                </div>
                                <button type="button" @click="addRow()"
                                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M12 4v16m8-8H4" />
                                    </svg>
                                    Tambah Baris Baru
                                </button>
                            </div>

                            {{-- SCROLLABLE FORM AREA --}}
                            <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                                <form
                                    :action="activeType === 'kas' ? '{{ route('tambah.kas-kecil.post', $unit->id) }}' :
                                        '#'"
                                    method="POST" enctype="multipart/form-data" id="bulkForm">
                                    @csrf
                                    <template x-for="(entry, index) in entries" :key="index">
                                        <div
                                            class="relative p-8 bg-white rounded-[2.5rem] border border-slate-200 mb-8 transition-all hover:shadow-2xl hover:shadow-slate-200/50 group">

                                            {{-- Badge & Floating Remove Button --}}
                                            <div
                                                class="absolute -top-3 left-8 px-4 py-1 bg-slate-800 text-white text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg">
                                                Item #<span x-text="index + 1"></span>
                                            </div>

                                            <button type="button" x-show="entries.length > 1" @click="removeRow(index)"
                                                class="absolute -top-3 -right-3 p-2 bg-rose-500 text-white rounded-full shadow-lg hover:scale-110 transition group-hover:rotate-90">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>

                                            {{-- FORM FIELDS --}}
                                            <div class="space-y-6">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                    <div>
                                                        <label
                                                            class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Tanggal</label>
                                                        <input type="date" :name="'kas[' + index + '][tgl]'"
                                                            x-model="entry.tgl"
                                                            class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:bg-white transition-all">
                                                    </div>
                                                    <div>
                                                        <label
                                                            class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1 text-blue-600">File
                                                            Lampiran / Nota</label>
                                                        <input type="file"
                                                            class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-600">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label
                                                        class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi
                                                        Transaksi</label>
                                                    <input type="text" :name="'kas[' + index + '][ket]'"
                                                        x-model="entry.keterangan"
                                                        placeholder="Ketik keterangan di sini..."
                                                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:bg-white transition-all">
                                                </div>
                                                <div class="grid grid-cols-2 gap-6">
                                                    {{-- DEBIT INPUT --}}
                                                    <div
                                                        class="p-6 bg-emerald-50/50 rounded-3xl border border-emerald-100 transition-all focus-within:border-emerald-400 focus-within:bg-white">
                                                        <span
                                                            class="text-[9px] font-black text-emerald-600 block mb-2 uppercase tracking-widest">Debit
                                                            (Uang Masuk)</span>
                                                        <div class="relative flex items-center">
                                                            <span
                                                                class="text-sm font-black text-emerald-400 mr-2">Rp</span>
                                                            <input type="text" x-model="entry.debit_display"
                                                                @input="handleRupiahInput(index, 'debit')" placeholder="0"
                                                                class="w-full bg-transparent border-none p-0 text-xl font-black text-emerald-700 focus:ring-0">
                                                            {{-- Hidden input to send raw number to Laravel --}}
                                                            <input type="hidden" :name="'kas[' + index + '][debit]'"
                                                                :value="entry.debit">
                                                        </div>
                                                    </div>

                                                    {{-- KREDIT INPUT --}}
                                                    <div
                                                        class="p-6 bg-rose-50/50 rounded-3xl border border-rose-100 transition-all focus-within:border-rose-400 focus-within:bg-white">
                                                        <span
                                                            class="text-[9px] font-black text-rose-600 block mb-2 uppercase tracking-widest">Kredit
                                                            (Uang Keluar)</span>
                                                        <div class="relative flex items-center">
                                                            <span class="text-sm font-black text-rose-400 mr-2">Rp</span>
                                                            <input type="text" x-model="entry.kredit_display"
                                                                @input="handleRupiahInput(index, 'kredit')"
                                                                placeholder="0"
                                                                class="w-full bg-transparent border-none p-0 text-xl font-black text-rose-700 focus:ring-0">
                                                            {{-- Hidden input to send raw number to Laravel --}}
                                                            <input type="hidden" :name="'kas[' + index + '][kredit]'"
                                                                :value="entry.kredit">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </form>
                            </div>

                            {{-- FIXED FOOTER SUMMARY DASHBOARD --}}
                            <div
                                class="px-8 py-6 bg-white border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-6 shadow-[0_-10px_30px_rgba(0,0,0,0.03)]">

                                {{-- Totals Area --}}
                                <div class="flex flex-wrap gap-8">
                                    <div class="text-left">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">
                                            Total Debit</p>
                                        <p class="text-base font-black text-emerald-600">Rp <span
                                                x-text="formatRupiah(totalDebit)"></span></p>
                                    </div>
                                    <div class="text-left border-l border-slate-100 pl-8">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">
                                            Total Kredit</p>
                                        <p class="text-base font-black text-rose-600">Rp <span
                                                x-text="formatRupiah(totalKredit)"></span></p>
                                    </div>
                                    <div class="text-left border-l border-slate-100 pl-8">
                                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">
                                            Estimasi Saldo Baru</p>
                                        <p class="text-base font-black text-slate-800"
                                            :class="totalSaldo < 0 ? 'text-rose-500' : 'text-slate-800'">
                                            Rp <span x-text="formatRupiah(totalSaldo)"></span>
                                        </p>
                                    </div>
                                </div>

                                {{-- Submit Action --}}
                                <div>
                                    <button type="submit" form="bulkForm"
                                        class="flex items-center gap-3 px-10 py-5 bg-slate-900 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-black transition active:scale-95 shadow-xl shadow-slate-200">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        Simpan Transaksi
                                    </button>
                                </div>
                            </div>
                        </div>
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


                {{-- C. Tunjangan Spesifik Unit (New Section) --}}
                <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-200 p-6 flex flex-col h-full">
                    {{-- Header Section --}}
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-black text-slate-800 tracking-tight">Pengaturan Tunjangan</h3>
                        </div>
                        <span
                            class="text-[9px] font-black text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md uppercase tracking-[0.15em] border border-emerald-100">
                            {{ count($unit->tunjangan ?? []) }} Kategori
                        </span>
                    </div>

                    {{-- Content Area --}}
                    @if (!empty($unit->tunjangan) && count($unit->tunjangan) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3">
                            @foreach ($unit->tunjangan as $kategori => $nominal)
                                <div
                                    class="flex items-center justify-between p-3.5 bg-slate-50/50 border border-slate-100 rounded-2xl hover:border-emerald-200 hover:bg-white hover:shadow-md hover:shadow-emerald-900/5 transition-all group">
                                    {{-- Left Side: Icon & Name --}}
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div
                                            class="w-1.5 h-1.5 rounded-full bg-emerald-400 group-hover:scale-150 transition-transform">
                                        </div>
                                        <span
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest group-hover:text-emerald-600 transition-colors truncate">
                                            {{ str_replace('_', ' ', $kategori) }}
                                        </span>
                                    </div>

                                    {{-- Right Side: Nominal --}}
                                    <div class="text-right ml-4">
                                        <span class="text-sm font-black text-slate-800">
                                            <span
                                                class="text-[10px] text-slate-400 font-bold mr-0.5">Rp</span>{{ number_format($nominal, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Empty State --}}
                        <div
                            class="flex-1 flex flex-col items-center justify-center py-10 bg-slate-50/50 rounded-[1.25rem] border border-dashed border-slate-200">
                            <svg class="w-8 h-8 text-slate-200 mb-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                            </svg>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest italic">Belum Ada
                                Tunjangan
                            </p>
                        </div>
                    @endif
                </div>

                {{-- B. Contract Details (Colorful & Visual) --}}
                {{-- B. Aturan Payroll & Masa Berlaku (Optimized Design) --}}
                <div class="bg-white rounded-[1.5rem] shadow-sm border border-gray-200 p-6">
                    {{-- Header --}}
                    <div class="flex items-center gap-3 mb-8">
                        <div class="p-2 bg-indigo-50 text-indigo-600 rounded-xl border border-indigo-100">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-black text-slate-800 tracking-tight text-left">Aturan Payroll & Masa
                            Berlaku</h3>
                    </div>

                    <div class="space-y-6">
                        {{-- 1. Masa Berlaku Kontrak (Full Width Highlight) --}}
                        <div
                            class="p-5 bg-slate-50 rounded-[1.25rem] border border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-center gap-4 text-left">
                                <div
                                    class="h-10 w-10 bg-white rounded-xl flex items-center justify-center text-slate-400 border border-slate-100 shadow-sm">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-0.5">
                                        Kontrak Berakhir Pada</p>
                                    <p class="text-base font-black text-slate-800">
                                        {{ \Carbon\Carbon::parse($unit->akhir_perjanjian)->format('d F Y') }}</p>
                                </div>
                            </div>

                            {{-- Progress Bar Inline --}}
                            <div class="flex-1 max-w-xs">
                                @php
                                    $start = \Carbon\Carbon::parse($unit->mulai_perjanjian);
                                    $end = \Carbon\Carbon::parse($unit->tgl_akhir_mou);
                                    $percentage = min(
                                        100,
                                        max(0, ($start->diffInDays(now()) / $start->diffInDays($end)) * 100),
                                    );
                                @endphp
                                <div class="flex justify-between items-center mb-1.5">
                                    <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest">Masa
                                        Berlaku</span>
                                    <span class="text-[10px] font-bold text-slate-500">{{ round($percentage) }}%</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-indigo-500 rounded-full transition-all duration-1000 shadow-[0_0_8px_rgba(99,102,241,0.4)]"
                                        style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Payroll Configuration (Grid of Horizontal Rows) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-10 gap-y-6 px-2">
                            {{-- BPJS Kesehatan --}}
                            <div class="flex items-center justify-between border-b border-slate-50 pb-4">
                                <div class="flex items-center gap-3 text-left">
                                    <div class="p-2 bg-rose-50 text-rose-500 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest">BPJS
                                        Kesehatan</span>
                                </div>
                                <p class="text-sm font-black text-slate-800">{{ $unit->bpjs_kesehatan ?? 0 }}% <span
                                        class="text-[9px] text-slate-300 font-bold ml-1 uppercase">UMK</span></p>
                            </div>

                            {{-- BPJS Naker --}}
                            <div class="flex items-center justify-between border-b border-slate-50 pb-4 text-left">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-amber-50 text-amber-500 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest">BPJS
                                        Tenaga Kerja</span>
                                </div>
                                <p class="text-sm font-black text-slate-800 text-right">{{ $unit->bpjs_naker ?? 0 }}%
                                    <span class="text-[9px] text-slate-300 font-bold ml-1 uppercase">UMK</span>
                                </p>
                            </div>

                            {{-- Mulai Kerjasama --}}
                            <div class="flex items-center justify-between border-b border-slate-50 pb-4 text-left">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-blue-50 text-blue-500 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                    </div>
                                    <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Mulai
                                        Kerjasama</span>
                                </div>
                                <p class="text-sm font-black text-slate-800 text-right">
                                    {{ \Carbon\Carbon::parse($unit->mulai_perjanjian)->format('d M Y') }}</p>
                            </div>

                            {{-- Status Pengajian --}}
                            <div class="flex items-center justify-between border-b border-slate-50 pb-4 text-left">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-purple-50 text-purple-500 rounded-lg">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Metode
                                        Gaji</span>
                                </div>
                                <p class="text-sm font-black text-indigo-600 text-right uppercase tracking-tighter">
                                    {{ $unit->sistem_pengajian == 1 ? 'Harian' : 'Borongan' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        {{-- WRAPPER FOR TABS LOGIC --}}
        <div x-data="{ activeTab: 'pekerja' }"
            class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-visible flex flex-col h-full">

            {{-- 1. TAB HEADER --}}
            <div class="px-6 border-b border-gray-200 bg-white rounded-tl-2xl rounded-tr-2xl">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    {{-- Tab 1: Pekerja --}}
                    <button @click="activeTab = 'pekerja'"
                        :class="activeTab === 'pekerja'
                            ?
                            'border-blue-600 text-blue-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="group inline-flex items-center py-4 px-1 border-b-2 font-bold text-sm transition-all duration-200 outline-none">
                        <svg :class="activeTab === 'pekerja' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'"
                            class="-ml-0.5 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Daftar Pekerja</span>
                        <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium md:inline-block hidden"
                            :class="activeTab === 'pekerja' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-900'">
                            {{ $pkwtPekerja->count() }}
                        </span>
                    </button>

                    {{-- Tab 2: Borongan --}}
                    @if ($unit->sistem_pengajian === 2)
                        <button @click="activeTab = 'borongan'"
                            :class="activeTab === 'borongan'
                                ?
                                'border-orange-500 text-orange-600' :
                                'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="group inline-flex items-center py-4 px-1 border-b-2 font-bold text-sm transition-all duration-200 outline-none">
                            <svg :class="activeTab === 'borongan' ? 'text-orange-500' : 'text-gray-400 group-hover:text-gray-500'"
                                class="-ml-0.5 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span>Daftar Borongan</span>
                            {{-- Replace '0' with actual count variable if available --}}
                            <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium md:inline-block hidden"
                                :class="activeTab === 'borongan' ? 'bg-orange-100 text-orange-600' :
                                    'bg-gray-100 text-gray-900'">
                                {{ $borongan->count() }}
                            </span>
                        </button>
                    @endif

                    {{-- Tab 1: Pekerja --}}
                    {{-- <button @click="activeTab = 'Inventaris'"
                        :class="activeTab === 'Inventaris'
                            ?
                            'border-blue-600 text-blue-600' :
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="group inline-flex items-center py-4 px-1 border-b-2 font-bold text-sm transition-all duration-200 outline-none">
                        <svg :class="activeTab === 'Inventaris' ? 'text-blue-500' : 'text-gray-400 group-hover:text-gray-500'"
                            class="-ml-0.5 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span>Inventaris Unit</span>
                        <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium md:inline-block hidden"
                            :class="activeTab === 'Inventaris' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-900'">
                            {{ $pkwtPekerja->count() }}
                        </span>
                    </button> --}}
                </nav>
            </div>

            {{-- 2. CONTENT AREA --}}

            {{-- A. TAB CONTENT: PEKERJA --}}
            <div x-show="activeTab === 'pekerja'" x-transition:enter.opacity.duration.300ms>
                @include('Unit.Detail.harian')
            </div>

            {{-- B. TAB CONTENT: BORONGAN --}}
            <div x-show="activeTab === 'borongan'" x-transition:enter.opacity.duration.300ms style="display: none;">
                @include('Unit.Detail.borongan')
            </div>

            {{-- C. TAB CONTENT: BORONGAN --}}
            {{-- <div x-show="activeTab === 'Inventaris'" x-transition:enter.opacity.duration.300ms style="display: none;">
                @include('Unit.Detail.borongan')
            </div> --}}

        </div>

    </div>



@endsection

@section('scripts')
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

        function checkUnitRequirements(url) {
            const isComplete = @js($isComplete);

            if (!isComplete) {
                Swal.fire({
                    title: 'Unit Belum Lengkap',
                    text: 'Harap perbarui unit ini sebelum menambahkan PKWT / karyawan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3b82f6',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Update Sekarang',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-2xl',
                        confirmButton: 'rounded-lg px-4 py-2',
                        cancelButton: 'rounded-lg px-4 py-2'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('view.ubah.unit', $unit->id) }}";
                    }
                });
            } else {
                window.location.href = url;
            }
        }

        document.addEventListener('alpine:init', () => {
            Alpine.data('unitManager', () => ({
                showModal: false,
                activeType: 'kas',
                view: 'list',
                entries: [],

                open(type) {
                    this.activeType = type;
                    this.view = 'list';
                    this.showModal = true;
                },

                openForm() {
                    this.view = 'form';
                    this.entries = [this.getEmptyRow()];
                },

                getEmptyRow() {
                    if (this.activeType === 'kas') {
                        return {
                            tgl: '',
                            keterangan: '',
                            debit: 0,
                            kredit: 0,
                            debit_display: '',
                            kredit_display: ''
                        };
                    } else {
                        return {
                            nama: '',
                            jumlah: 1,
                            harga: 0,
                            lokasi: '',
                            harga_display: ''
                        };
                    }
                },

                addRow() {
                    this.entries.push(this.getEmptyRow());
                },
                removeRow(index) {
                    if (this.entries.length > 1) this.entries.splice(index, 1);
                },

                // REAL-TIME RUPIAH FORMATTER FOR INPUTS
                handleRupiahInput(index, field) {
                    // 1. Get the value and strip everything except numbers
                    let rawValue = this.entries[index][field + '_display'].replace(/\D/g, '');

                    // 2. Store the raw number for calculations
                    this.entries[index][field] = Number(rawValue) || 0;

                    // 3. Format the display with dots
                    this.entries[index][field + '_display'] = new Intl.NumberFormat('id-ID').format(
                        rawValue);

                    // 4. If input is empty, reset display
                    if (rawValue === '') this.entries[index][field + '_display'] = '';
                },

                // FOOTER TOTALS
                get totalDebit() {
                    return this.entries.reduce((sum, entry) => sum + (entry.debit || 0), 0);
                },
                get totalKredit() {
                    return this.entries.reduce((sum, entry) => sum + (entry.kredit || 0), 0);
                },
                get totalSaldo() {
                    return this.totalDebit - this.totalKredit;
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID').format(number);
                }
            }))
        });
    </script>
@endsection
