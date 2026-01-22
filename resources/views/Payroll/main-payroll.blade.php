@extends('layout')

@section('header')
    <x-header title="Sistem Penggajian" subtitle="Ringkasan statistik dan daftar unit kerja."
        breadcrumbs="Payroll Manajemen" />
@endsection

@section('content')
    <style>
        /* Custom Date Input Styling */
        input[type="date"] {
            appearance: none;
            -webkit-appearance: none;
            min-height: 42px;
        }

        /* Custom Scrollbar untuk Modal Body */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #10b981;
            /* Emerald 500 */
        }

        /* Fix Icon Tanggal agar tidak berantakan */
        input[type="date"]::-webkit-calendar-picker-indicator {
            background-color: #f8fafc;
            padding: 7px;
            border-radius: 6px;
            cursor: pointer;
            color: #059669;
        }
    </style>

    {{-- ================================
        1. STATS OVERVIEW CARD
    ================================= --}}
    {{-- 1. HEADER & DATE INDICATOR --}}

    <div x-data="{ baseModal: false }" @open-base-modal.window="baseModal = true">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">

            {{-- CARD 1: TOTAL UNIT --}}
            <div
                class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="flex justify-between items-start z-10 relative">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Unit</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ $totalUnit }}</h3>
                    </div>
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center text-blue-600 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
                {{-- Decorative bottom line --}}
                <div
                    class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-blue-300 opacity-0 group-hover:opacity-100 transition-opacity">
                </div>
            </div>

            {{-- CARD 2: UNIT BARU --}}
            <div
                class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="flex justify-between items-start z-10 relative">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Unit Baru</p>
                        <div class="flex items-baseline gap-2">
                            <h3 class="text-3xl font-extrabold text-gray-900">{{ $unitBaru }}</h3>
                            <span
                                class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-md">Bulan
                                Ini</span>
                        </div>
                    </div>
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                </div>
                <div
                    class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-emerald-300 opacity-0 group-hover:opacity-100 transition-opacity">
                </div>
            </div>

            {{-- CARD 3: HARIAN --}}
            <div
                class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="flex justify-between items-start z-10 relative">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Harian</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ $totalHarian ?? 0 }}</h3>
                    </div>
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 flex items-center justify-center text-purple-600 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div
                    class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-purple-500 to-purple-300 opacity-0 group-hover:opacity-100 transition-opacity">
                </div>
            </div>

            {{-- CARD 4: BORONGAN --}}
            <div
                class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
                <div class="flex justify-between items-start z-10 relative">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Borongan</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ $totalBorongan ?? 0 }}</h3>
                    </div>
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-50 to-orange-100 flex items-center justify-center text-orange-600 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
                <div
                    class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-orange-500 to-orange-300 opacity-0 group-hover:opacity-100 transition-opacity">
                </div>
            </div>

            {{-- CARD 5: TIDAK AKTIF --}}
            {{-- On small screens, span full width to fill gap --}}
            <div
                class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group sm:col-span-2 lg:col-span-1">
                <div class="flex justify-between items-start z-10 relative">
                    <div>
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Tidak Aktif</p>
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ $tidakAktif }}</h3>
                    </div>
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-50 to-red-100 flex items-center justify-center text-red-600 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                </div>
                <div
                    class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-red-500 to-red-300 opacity-0 group-hover:opacity-100 transition-opacity">
                </div>
            </div>

        </div>

        {{-- ================================
        3. TOOLBAR & FILTERS
    ================================= --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 relative">

            {{-- Left: View Switcher (Visual only) --}}
            <div class="bg-gray-100 p-1 rounded-lg inline-flex self-start sm:self-center">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 rounded-full shadow-sm text-sm font-medium text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
                </div>
            </div>

            {{-- Right: Actions --}}
            <div class="flex flex-1 justify-end items-center gap-3">

                {{-- SEARCH BAR --}}
                <div class="relative w-full max-w-xs">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input id="searchInput" type="text" data-url="{{ route('view.unit') }}"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-white text-sm placeholder-gray-400
                        focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm"
                        placeholder="Cari unit...">
                </div>

                {{-- FILTER TOGGLE BUTTON --}}
                <div class="relative">
                    <button id="filterToggleBtn"
                        class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                        {{-- Badge indicator (Visible only if filters are active - handled by JS) --}}
                        <span id="activeFilterBadge" class="hidden flex h-2 w-2 relative">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                        </span>
                    </button>

                    {{-- FILTER DROPDOWN POPUP --}}
                    <div id="filterDropdown"
                        class="hidden absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 ring-1 ring-black ring-opacity-5 p-5 origin-top-right transform transition-all">

                        {{-- Header --}}
                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-sm font-bold text-gray-800">Filter Data</h3>
                            <button id="resetFilters"
                                class="text-xs font-medium text-gray-400 hover:text-red-500 hover:bg-red-50 px-2 py-1 rounded transition">
                                Reset Filter
                            </button>
                        </div>

                        <div class="space-y-5">

                            {{-- Status Input (Alpine Custom Dropdown) --}}
                            <div x-data="{
                                open: false,
                                selected: '',
                                list: [
                                    { val: '', label: 'Semua Status' },
                                    { val: '1', label: 'Aktif' },
                                    { val: '0', label: 'Tidak Aktif' }
                                ]
                            }" x-init="$watch('selected', value => {
                                // This bridges Alpine to your existing jQuery Search script
                                $('#statusFilter').val(value).trigger('change');
                            })" class="relative group">

                                <label class="block text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1.5">
                                    Status Keaktifan
                                </label>

                                {{-- Hidden Input for the jQuery Script to read --}}
                                <input type="hidden" id="statusFilter" :value="selected">

                                {{-- Trigger Button --}}
                                <div @click="open = !open" @click.outside="open = false"
                                    class="relative block w-full pl-9 pr-3 py-2.5 text-sm bg-gray-50 border border-transparent rounded-xl text-gray-700
                                        cursor-pointer hover:bg-gray-100 transition flex justify-between items-center group-focus-within:ring-2 group-focus-within:ring-blue-100 group-focus-within:bg-white">

                                    {{-- Left Icon (User) --}}
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400 group-hover:text-blue-500 transition"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>

                                    {{-- Selected Text --}}
                                    <span class="truncate font-medium"
                                        x-text="list.find(x => x.val == selected)?.label || 'Semua Status'">
                                    </span>

                                    {{-- Right Chevron --}}
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                        :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>

                                {{-- Dropdown List --}}
                                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute w-full mt-1 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-[60]">

                                    <ul class="max-h-60 overflow-y-auto py-1">
                                        <template x-for="item in list" :key="item.val">
                                            <li @click="selected = item.val; open = false"
                                                class="px-4 py-2.5 text-sm cursor-pointer transition flex items-center gap-2"
                                                :class="selected == item.val ? 'bg-blue-50 text-blue-700 font-semibold' :
                                                    'text-gray-700 hover:bg-gray-50 hover:text-gray-900'">

                                                {{-- Checkmark Icon (Visible if selected) --}}
                                                <svg x-show="selected == item.val" class="w-4 h-4 text-blue-600"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                {{-- Spacer if not selected --}}
                                                <span x-show="selected != item.val" class="w-4 h-4"></span>

                                                <span x-text="item.label"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>

                            <div x-data="{
                                open: false,
                                selected: '',
                                list: [
                                    { val: '', label: 'Semua Tipe Pengajian' },
                                    { val: '1', label: 'Harian' },
                                    { val: '2', label: 'Borongan' }
                                ]
                            }" x-init="$watch('selected', value => {
                                // This bridges Alpine to your existing jQuery Search script
                                $('#pengajianFilter').val(value).trigger('change');
                            })" class="relative group">

                                <label class="block text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1.5">
                                    Tipe Pengajian
                                </label>

                                {{-- Hidden Input for the jQuery Script to read --}}
                                <input type="hidden" id="pengajianFilter" :value="selected">

                                {{-- Trigger Button --}}
                                <div @click="open = !open" @click.outside="open = false"
                                    class="relative block w-full pl-9 pr-3 py-2.5 text-sm bg-gray-50 border border-transparent rounded-xl text-gray-700
                                        cursor-pointer hover:bg-gray-100 transition flex justify-between items-center group-focus-within:ring-2 group-focus-within:ring-blue-100 group-focus-within:bg-white">

                                    {{-- Left Icon (User) --}}
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400 group-hover:text-blue-500 transition"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>

                                    {{-- Selected Text --}}
                                    <span class="truncate font-medium"
                                        x-text="list.find(x => x.val == selected)?.label || 'Semua Status'">
                                    </span>

                                    {{-- Right Chevron --}}
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                        :class="{ 'rotate-180': open }" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>

                                {{-- Dropdown List --}}
                                <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute w-full mt-1 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-[60]">

                                    <ul class="max-h-60 overflow-y-auto py-1">
                                        <template x-for="item in list" :key="item.val">
                                            <li @click="selected = item.val; open = false"
                                                class="px-4 py-2.5 text-sm cursor-pointer transition flex items-center gap-2"
                                                :class="selected == item.val ? 'bg-blue-50 text-blue-700 font-semibold' :
                                                    'text-gray-700 hover:bg-gray-50 hover:text-gray-900'">

                                                {{-- Checkmark Icon (Visible if selected) --}}
                                                <svg x-show="selected == item.val" class="w-4 h-4 text-blue-600"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                {{-- Spacer if not selected --}}
                                                <span x-show="selected != item.val" class="w-4 h-4"></span>

                                                <span x-text="item.label"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>

                        </div>

                        {{-- Footer/Info (Optional) --}}
                        <div class="mt-6 pt-4 border-t border-gray-50 text-center">
                            <p class="text-[10px] text-gray-400">Filter akan diterapkan otomatis</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================================
            4. MAIN TABLE
        ================================= --}}
        <div id="table-wrapper">
            @include('Payroll.partials.payroll-table')
        </div>

        <!-- Modal Wrapper -->
        <div x-data="{
            exclusions: [{ worker_id: '', date: '', open: false }],
            addExclusion() { this.exclusions.push({ worker_id: '', date: '', open: false }); },
            removeExclusion(index) { this.exclusions.splice(index, 1); }
        }" x-show="$store.payslip.isOpen" x-cloak class="relative z-[100]">

            <!-- Backdrop -->
            <div x-show="$store.payslip.isOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"></div>

            <!-- Layout Container -->
            <div class="fixed inset-0 z-10 overflow-hidden flex items-center justify-center p-4">

                <!-- MODAL BOX: Pastikan rounded-2xl dan overflow-hidden ada di sini -->
                <div @click.away="$store.payslip.close()" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:scale-[0.98]"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200 flex flex-col max-h-[90vh] overflow-hidden">

                    <form action="#" method="POST" class="flex flex-col overflow-hidden">
                        @csrf

                        <!-- 1. FIXED HEADER -->
                        <div
                            class="px-8 py-6 border-b border-slate-100 flex justify-between items-center shrink-0 bg-white">
                            <div>
                                <h3 class="text-xl font-black text-slate-800 tracking-tight">Proses Penggajian Unit</h3>
                                <div class="flex items-center gap-2 mt-1">
                                    <span
                                        class="text-[9px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded uppercase tracking-wider border border-emerald-100">Unit
                                        Aktif</span>
                                    <p class="text-xs font-bold text-slate-500" x-text="$store.payslip.unitName"></p>
                                </div>
                            </div>
                            <button type="button" @click="$store.payslip.close()"
                                class="p-2 text-slate-300 hover:text-slate-600 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- 2. SCROLLABLE BODY -->
                        <div class="p-8 space-y-10 overflow-y-auto custom-scrollbar flex-1 bg-white">

                            <!-- STEP 1: PERIODE -->
                            <div class="space-y-4">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-600 text-white text-[10px] font-black flex items-center justify-center shadow-lg shadow-emerald-200">1</span>
                                    <label
                                        class="block text-[11px] font-black text-slate-700 uppercase tracking-widest">Tentukan
                                        Periode Penggajian</label>
                                </div>
                                <div class="bg-emerald-50/20 p-6 rounded-xl border border-emerald-100/50 ml-9">
                                    <div class="grid grid-cols-2 gap-6">
                                        <div class="space-y-1.5">
                                            <span class="text-[9px] font-bold text-slate-400 uppercase ml-1">Tanggal
                                                Mulai</span>
                                            <input type="date" name="start_date" required
                                                class="w-full bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-700 py-2.5 px-3 focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm">
                                        </div>
                                        <div class="space-y-1.5">
                                            <span class="text-[9px] font-bold text-slate-400 uppercase ml-1">Tanggal
                                                Selesai</span>
                                            <input type="date" name="end_date" required
                                                class="w-full bg-white border border-slate-200 rounded-lg text-sm font-bold text-slate-700 py-2.5 px-3 focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 2: PEKERJA DIBAYAR (DEFAULT TERPILIH SEMUA) -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-600 text-white text-[10px] font-black flex items-center justify-center shadow-lg shadow-emerald-200">2</span>
                                        <label
                                            class="block text-[11px] font-black text-slate-700 uppercase tracking-widest">Pilih
                                            Pekerja yang Akan Dibayar</label>
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="button"
                                            @click="$store.payslip.selectedWorkers = $store.payslip.workers.map(w => w.id)"
                                            class="text-[9px] font-black text-emerald-600 hover:underline uppercase tracking-tighter">Pilih
                                            Semua</button>
                                        <span class="text-slate-300 text-[9px]">|</span>
                                        <button type="button" @click="$store.payslip.selectedWorkers = []"
                                            class="text-[9px] font-black text-slate-400 hover:underline uppercase tracking-tighter">Hapus
                                            Semua</button>
                                    </div>
                                </div>
                                <div class="ml-9">
                                    <div
                                        class="grid grid-cols-2 gap-3 max-h-56 overflow-y-auto p-4 border border-slate-200 rounded-xl bg-slate-50/50 custom-scrollbar">
                                        <template x-for="worker in $store.payslip.workers" :key="worker.id">
                                            <label
                                                class="flex items-center gap-3 p-3 bg-white border rounded-xl cursor-pointer transition-all shadow-sm"
                                                :class="$store.payslip.selectedWorkers.includes(worker.id) ?
                                                    'border-emerald-500 bg-emerald-50/50' :
                                                    'border-slate-100 opacity-60'">
                                                <input type="checkbox" name="paid_workers[]" :value="worker.id"
                                                    x-model="$store.payslip.selectedWorkers"
                                                    class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                                                <div class="flex flex-col">
                                                    <span class="text-[11px] font-bold text-slate-700 leading-tight"
                                                        x-text="worker.nama"></span>
                                                    <span class="text-[9px] font-black text-slate-300 uppercase mt-0.5"
                                                        x-text="'ID: ' + worker.id"></span>
                                                </div>
                                            </label>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <!-- STEP 3: POTONGAN SPESIFIK -->
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-600 text-white text-[10px] font-black flex items-center justify-center shadow-lg shadow-emerald-200">3</span>
                                        <label
                                            class="block text-[11px] font-black text-slate-700 uppercase tracking-widest">Potongan
                                            Tanggal Spesifik</label>
                                    </div>
                                    <button type="button" @click="addExclusion()"
                                        class="text-[10px] font-black text-emerald-600 hover:text-emerald-800 bg-emerald-50 px-3 py-1.5 rounded-lg border border-emerald-100 transition-all uppercase">+
                                        Tambah Baris</button>
                                </div>

                                <div class="ml-9 space-y-3">
                                    <template x-for="(item, index) in exclusions" :key="index">
                                        <div class="flex gap-3 items-start relative animate-in slide-in-from-top-1">
                                            <!-- Custom Dropdown Style -->
                                            <div class="flex-1 relative">
                                                <input type="hidden" :name="'specific_workers[' + index + '][id]'"
                                                    x-model="item.worker_id">
                                                <div @click="item.open = !item.open"
                                                    class="w-full bg-white border border-slate-200 rounded-xl text-[11px] font-bold text-slate-700 py-3 px-4 flex justify-between items-center cursor-pointer hover:border-emerald-500 transition-all shadow-sm">
                                                    <span
                                                        x-text="$store.payslip.workers.find(w => w.id == item.worker_id)?.nama || 'Pilih Pekerja'"></span>
                                                    <svg class="w-4 h-4 text-slate-300 transition-transform duration-200"
                                                        :class="item.open ? 'rotate-180' : ''" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path d="M19 9l-7 7-7-7" stroke-width="3" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                                <ul x-show="item.open" @click.outside="item.open = false"
                                                    class="absolute w-full mt-2 border border-slate-200 bg-white rounded-xl shadow-2xl overflow-y-auto max-h-48 z-[120] p-1 custom-scrollbar">
                                                    <template x-for="worker in $store.payslip.workers"
                                                        :key="worker.id">
                                                        <li @click="item.worker_id = worker.id; item.open = false"
                                                            class="px-4 py-2.5 text-[11px] font-bold text-slate-600 hover:bg-emerald-600 hover:text-white rounded-lg cursor-pointer transition flex items-center justify-between">
                                                            <span x-text="worker.nama"></span>
                                                            <span class="text-[9px] font-black opacity-30"
                                                                x-text="'ID: ' + worker.id"></span>
                                                        </li>
                                                    </template>
                                                </ul>
                                            </div>
                                            <div class="flex-1">
                                                <input type="date" :name="'specific_workers[' + index + '][date]'" required
                                                    class="w-full bg-white border border-slate-200 rounded-xl text-[11px] font-bold text-slate-700 py-2.5 px-3 focus:ring-2 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all shadow-sm">
                                            </div>
                                            <button type="button" @click="removeExclusion(index)"
                                                class="mt-3 p-1 text-slate-300 hover:text-red-500 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- STEP 4: GLOBAL ADJUSTMENT (DENGAN RUPIAH FORMAT) -->
                            <div class="space-y-4" x-data="{
                                rawVal: '',
                                // Fungsi pembantu untuk format di dalam komponen jika belum ada di parent
                                formatRupiah(val) {
                                    if (!val) return '';
                                    return new Intl.NumberFormat('id-ID').format(val);
                                }
                            }">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="flex-shrink-0 w-6 h-6 rounded-full bg-emerald-600 text-white text-[10px] font-black flex items-center justify-center shadow-lg shadow-emerald-200">4</span>
                                    <label
                                        class="block text-[11px] font-black text-slate-700 uppercase tracking-widest">Penyesuaian
                                        Biaya Lain-lain</label>
                                </div>
                                <div class="ml-9">
                                    <div class="relative flex items-center group">
                                        <span
                                            class="absolute left-5 font-black text-emerald-500 text-xs border-r border-slate-100 pr-3">Rp.</span>

                                        {{-- Input yang dilihat User --}}
                                        <input type="text" placeholder="0" :value="formatRupiah(rawVal)"
                                            @input="rawVal = $event.target.value.replace(/\D/g, '')"
                                            class="w-full bg-white border border-slate-200 rounded-2xl pl-16 py-4 text-sm font-black text-slate-700 focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500 transition-all shadow-sm">

                                        {{-- Hidden Input untuk dikirim ke Controller (Angka murni tanpa titik) --}}
                                        <input type="hidden" name="global_adjustment" x-model="rawVal">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. FIXED FOOTER (Rounded Bottom Corners) -->
                        <div
                            class="px-8 py-6 bg-slate-50 border-t border-slate-100 flex justify-between items-center shrink-0 bg-white">
                            <button type="button" @click="$store.payslip.close()"
                                class="text-xs font-black text-slate-400 hover:text-slate-600 uppercase tracking-widest transition-colors">Batalkan</button>

                            <button type="submit"
                                class="group flex items-center gap-4 bg-emerald-600 hover:bg-emerald-500 text-white px-10 py-4 rounded-xl font-black text-[11px] uppercase tracking-[0.2em] shadow-lg shadow-emerald-500/30 transition-all active:scale-95">
                                <span>Execute Payroll</span>
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M13 7l5 5-5 5M6 7l5 5-5 5" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection



@section('scripts')
    <script src="/js/main-payroll.js"></script>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('payslip', {
                isOpen: false,
                unitName: '',
                workers: [],
                selectedWorkers: [], // Array untuk menampung pekerja yang dicentang

                open(unitName, workerList) {
                    this.unitName = unitName;
                    this.workers = workerList;
                    // OTOMATIS CENTANG SEMUA:
                    // Ambil semua ID dari workerList dan masukkan ke selectedWorkers
                    this.selectedWorkers = workerList.map(worker => worker.id);
                    this.isOpen = true;
                },
                close() {
                    this.isOpen = false;
                }
            })
        })
    </script>
@endsection
