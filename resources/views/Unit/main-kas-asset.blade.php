@extends('layout')

@section('header')
    <x-header title="Daftar Unit (Kas Kecil & Asset)" subtitle="Ringkasan statistik dan daftar unit kerja." breadcrumbs="Kas Kecil & Asset Manajemen" />
@endsection

@section('content')
    <style>
        /* 1. Hide the default icon in Chrome/Edge/Safari for filter date inputs */
        .filter-date-input::-webkit-calendar-picker-indicator {
            background: transparent;
            bottom: 0;
            color: transparent;
            cursor: pointer;
            height: auto;
            left: 0;
            position: absolute;
            right: 0;
            top: 0;
            width: auto;
        }

        /* 2. Fix for some browsers adding extra spacing */
        .filter-date-input {
            -webkit-appearance: none;
            min-height: 2.5rem;
            /* Ensure consistent height */
        }
    </style>

    {{-- ================================
        1. STATS OVERVIEW CARD
    ================================= --}}
    {{-- 1. HEADER & DATE INDICATOR --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
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

        {{-- CARD 2: TOTAL KAS --}}
        <div
            class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Grand Total Kas Kecil</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-2xl font-extrabold text-gray-900">Rp {{ number_format($grandTotalKasValue, 0, ',', '.') }}</h3>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 font-semibold">{{ $totalKasCount }} Transaksi</p>
                </div>
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-50 to-emerald-100 flex items-center justify-center text-emerald-600 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div
                class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-emerald-300 opacity-0 group-hover:opacity-100 transition-opacity">
            </div>
        </div>

        {{-- CARD 3: TOTAL ASSET --}}
        <div
            class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Grand Total Asset</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-2xl font-extrabold text-gray-900">Rp {{ number_format($grandTotalAssetValue, 0, ',', '.') }}</h3>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 font-semibold">{{ $totalAssetCount }} Barang</p>
                </div>
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-50 to-purple-100 flex items-center justify-center text-purple-600 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                    </svg>
                </div>
            </div>
            <div
                class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-purple-500 to-purple-300 opacity-0 group-hover:opacity-100 transition-opacity">
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
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
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
                                    <svg class="h-4 w-4 text-gray-400 group-hover:text-blue-500 transition" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
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
                                    <svg class="h-4 w-4 text-gray-400 group-hover:text-blue-500 transition" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
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

                        {{-- Date Range (Visual Grouping) --}}
                        {{-- <div>
                            <label
                                class="block text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1.5">Tanggal
                                Masa Berakhir</label>

                            <div class="flex items-center gap-2">

                                <div class="relative flex-1 min-w-0 group">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none z-10">
                                        <svg class="h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="date" id="startDate"
                                        class="filter-date-input block w-full pl-8 pr-2 py-2 text-xs font-medium bg-gray-50 border-transparent rounded-xl text-gray-600
                       focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 hover:bg-gray-100 transition placeholder-gray-400 relative z-0">
                                </div>

                                <span class="text-gray-300">-</span>

                                <div class="relative flex-1 min-w-0 group">
                                    <div
                                        class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none z-10">
                                        <svg class="h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="date" id="endDate"
                                        class="filter-date-input block w-full pl-8 pr-2 py-2 text-xs font-medium bg-gray-50 border-transparent rounded-xl text-gray-600
                       focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 hover:bg-gray-100 transition placeholder-gray-400 relative z-0">
                                </div>
                            </div>
                        </div> --}}

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
        @include('Unit.partials.kas-asset-table')
    </div>

    {{-- DYNAMIC SHARED CRUD MODAL --}}
    <div x-data="unitManager" @open-unit-modal.window="openModal($event.detail)" x-cloak>
        <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6">
            {{-- Overlay --}}
            <div x-show="showModal" x-transition.opacity @click="showModal = false"
                class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>

            <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                class="relative w-full max-w-6xl bg-white rounded-[2.5rem] shadow-2xl overflow-hidden flex flex-col min-h-[600px] max-h-[90vh]">

                {{-- HEADER --}}
                <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between"
                    :class="activeType === 'kas' ? 'bg-emerald-50/50' : 'bg-blue-50/50'">
                    <div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                            <span x-text="activeType === 'kas' ? 'Manajemen Kas Kecil - ' + unitName : 'Manajemen Asset Unit - ' + unitName"></span>
                        </h3>
                        <p class="text-xs text-slate-500 font-bold uppercase mt-1 tracking-tighter" x-show="view === 'list'">
                            periode <span x-text="formatPeriode()"></span> tertentu
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        {{-- Toggle Button --}}
                        <button @click="view === 'list' ? openForm() : view = 'list'"
                            class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all"
                            :class="view === 'list' ? 'bg-slate-800 text-white shadow-lg' : 'bg-slate-100 text-slate-500'">
                            <span x-text="view === 'list' ? '+ Tambah Baru' : '← Lihat Daftar'"></span>
                        </button>
                        {{-- Close Button --}}
                        <button @click="showModal = false"
                            class="p-2 text-slate-400 hover:text-rose-500 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- FILTERS BLOCK --}}
                <div x-show="view === 'list'" class="px-8 py-3 bg-slate-50 border-b border-slate-100 flex flex-wrap gap-4 items-center">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Filter:</span>
                    
                    <!-- Month Selector -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" type="button" class="flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-lg text-[10px] font-bold hover:bg-slate-50 transition shadow-sm">
                            <span>Bulan</span>
                            <span class="bg-blue-100 text-blue-600 px-1.5 py-0.2 rounded-full text-[9px]" x-show="selectedMonths.length > 0" x-text="selectedMonths.length"></span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" class="absolute left-0 mt-2 w-56 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-2 space-y-1 max-h-60 overflow-y-auto">
                            <template x-for="m in monthsList" :key="m.value">
                                <label class="flex items-center gap-2 px-2 py-1 hover:bg-slate-50 rounded-md cursor-pointer">
                                    <input type="checkbox" :value="m.value" x-model="selectedMonths" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-[11px] font-semibold text-slate-700" x-text="m.label"></span>
                                </label>
                            </template>
                        </div>
                    </div>

                    <!-- Year Selector -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" type="button" class="flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-lg text-[10px] font-bold hover:bg-slate-50 transition shadow-sm">
                            <span>Tahun</span>
                            <span class="bg-blue-100 text-blue-600 px-1.5 py-0.2 rounded-full text-[9px]" x-show="selectedYears.length > 0" x-text="selectedYears.length"></span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" class="absolute left-0 mt-2 w-48 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-2 space-y-1 max-h-60 overflow-y-auto">
                            <template x-for="y in getYears()" :key="y">
                                <label class="flex items-center gap-2 px-2 py-1 hover:bg-slate-50 rounded-md cursor-pointer">
                                    <input type="checkbox" :value="y" x-model="selectedYears" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-[11px] font-semibold text-slate-700" x-text="y"></span>
                                </label>
                            </template>
                        </div>
                    </div>

                    <!-- Status Selector -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" type="button" class="flex items-center gap-2 px-3 py-1.5 bg-white border border-slate-200 text-slate-700 rounded-lg text-[10px] font-bold hover:bg-slate-50 transition shadow-sm">
                            <span>Status</span>
                            <span class="bg-blue-100 text-blue-600 px-1.5 py-0.2 rounded-full text-[9px]" x-show="selectedStatus !== ''" x-text="selectedStatus === 'approved' ? 'Approved' : 'Pending'"></span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" class="absolute left-0 mt-2 w-40 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-2 space-y-1">
                            <button type="button" @click="selectedStatus = ''; open = false" class="w-full text-left px-2 py-1.5 hover:bg-slate-50 rounded-md text-[11px] font-semibold text-slate-700 flex items-center justify-between">
                                <span>Semua Status</span>
                                <svg x-show="selectedStatus === ''" class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                            <button type="button" @click="selectedStatus = 'pending'; open = false" class="w-full text-left px-2 py-1.5 hover:bg-slate-50 rounded-md text-[11px] font-semibold text-slate-700 flex items-center justify-between">
                                <span>Pending</span>
                                <svg x-show="selectedStatus === 'pending'" class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                            <button type="button" @click="selectedStatus = 'approved'; open = false" class="w-full text-left px-2 py-1.5 hover:bg-slate-50 rounded-md text-[11px] font-semibold text-slate-700 flex items-center justify-between">
                                <span>Approved</span>
                                <svg x-show="selectedStatus === 'approved'" class="w-3.5 h-3.5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <button @click="selectedMonths = []; selectedYears = []; selectedStatus = ''" x-show="selectedMonths.length > 0 || selectedYears.length > 0 || selectedStatus !== ''" type="button" class="text-[10px] text-rose-500 hover:text-rose-700 font-black uppercase tracking-wider transition ml-2">
                        Reset Filter
                    </button>
                </div>

                {{-- CONTENT: LIST VIEW (KAS) --}}
                <div x-show="view === 'list' && activeType === 'kas'"
                    class="flex-1 overflow-y-auto p-4 bg-slate-50/30" x-transition>
                    <div class="overflow-x-auto w-full mb-4">
                        <table class="w-full text-left border-separate border-spacing-y-3">
                            <thead>
                                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    <th class="px-4 py-2 w-10 text-center">
                                        <input type="checkbox" @click="toggleSelectAll(allData.map(d => d.id))"
                                            :checked="selectedRows.length === allData.length && allData.length > 0"
                                            class="rounded-md border-slate-200 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                    </th>
                                    <th class="px-4 py-2 w-32">Tanggal</th>
                                    <th class="px-4 py-2 w-32">Akun</th>
                                    <th class="px-4 py-2">Deskripsi</th>
                                    <th class="px-4 py-2 text-right">Debit</th>
                                    <th class="px-4 py-2 text-right">Kredit</th>
                                    <th class="px-4 py-2 text-right">Saldo</th>
                                    <th class="px-4 py-2 text-center w-28">Status</th>
                                    <th class="px-4 py-2 text-center w-28">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(kas, index) in filteredKasKecil()" :key="kas.id">
                                    <tr class="group bg-white hover:shadow-xl hover:shadow-slate-200/50 transition-all">
                                        <td class="px-4 py-4 rounded-l-2xl border-l border-y border-slate-100 text-center">
                                            <input type="checkbox" :value="kas.id" x-model="selectedRows"
                                                class="rounded-md border-slate-200 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                        </td>
                                        <td class="px-4 py-4 border-y border-slate-100 text-xs font-bold text-slate-500 tracking-tighter" x-text="formatDate(kas.tanggal)"></td>
                                        <td class="px-4 py-4 border-y border-slate-100 text-sm font-black text-slate-800" x-text="kas.akun"></td>
                                        <td class="px-4 py-4 border-y border-slate-100">
                                            <p class="text-sm font-black text-slate-800" x-text="kas.keterangan"></p>
                                            <template x-if="kas.nota">
                                                <a :href="`/unit/${kas.id}/kas-kecil/nota`" target="_blank"
                                                    class="text-[9px] text-blue-500 font-bold uppercase hover:underline">📂 Lihat Lampiran</a>
                                            </template>
                                            <template x-if="!kas.nota">
                                                <span class="text-[9px] text-slate-300 font-bold uppercase italic">Tanpa Nota</span>
                                            </template>
                                        </td>
                                        <td class="px-4 py-4 border-y border-slate-100 text-right text-sm font-black"
                                            :class="kas.debit > 0 ? 'text-emerald-600' : 'text-slate-300'"
                                            x-text="kas.debit > 0 ? formatRupiah(kas.debit) : '-'"></td>
                                        <td class="px-4 py-4 border-y border-slate-100 text-right text-sm font-black"
                                            :class="kas.kredit > 0 ? 'text-rose-600' : 'text-slate-300'"
                                            x-text="kas.kredit > 0 ? formatRupiah(kas.kredit) : '-'"></td>
                                        <td class="px-4 py-4 border-y border-slate-100 text-right text-sm font-black text-slate-800 italic" x-text="formatRupiah(kas.runningSaldo)"></td>
                                        <td class="px-4 py-4 border-y border-slate-100 text-center">
                                            <template x-if="kas.status == 2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-50 text-emerald-600 border border-emerald-100">✓ Approved</span>
                                            </template>
                                            <template x-if="kas.status != 2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-50 text-amber-600 border border-amber-100">Pending</span>
                                            </template>
                                        </td>
                                        <td class="px-4 py-4 rounded-r-2xl border-r border-y border-slate-100 text-center">
                                            <template x-if="kas.status == 2">
                                                <span class="text-xs text-slate-400 italic">Locked</span>
                                            </template>
                                            <template x-if="kas.status != 2">
                                                <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all transform group-hover:scale-100 scale-90">
                                                    <button @click="editEntries([kas.id])" title="Edit Transaksi"
                                                        class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <button @click="confirmDeleteKas(kas.id, unitId)" title="Hapus"
                                                        class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white transition">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="allData.length === 0">
                                    <tr>
                                        <td colspan="9" class="py-20 text-center">
                                            <div class="flex flex-col items-center opacity-20">
                                                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                <p class="font-black uppercase tracking-widest text-sm">Belum ada transaksi</p>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="allData.length > 0 && filteredKasKecil().length === 0">
                                    <tr>
                                        <td colspan="9" class="py-20 text-center">
                                            <div class="flex flex-col items-center opacity-20">
                                                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                                </svg>
                                                <p class="font-black uppercase tracking-widest text-sm">Data tidak tersedia dengan filter saat ini</p>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- CONTENT: LIST VIEW (ASSET) --}}
                <div x-show="view === 'list' && activeType === 'asset'"
                    class="flex-1 overflow-y-auto p-6 bg-slate-50/30" x-transition>
                    <div class="overflow-x-auto w-full mb-4">
                        <table class="w-full text-left border-separate border-spacing-y-3">
                            <thead>
                                <tr class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                                    <th class="px-4 py-2 w-10 text-center">
                                        <input type="checkbox" @click="toggleSelectAll(allDataAsset.map(d => d.id))"
                                            :checked="selectedRows.length === allDataAsset.length && allDataAsset.length > 0"
                                            class="rounded-md border-slate-200 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                    </th>
                                    <th class="px-4 py-2 w-12 text-center">No.</th>
                                    <th class="px-4 py-2">Informasi Barang</th>
                                    <th class="px-4 py-2 text-center">Qty</th>
                                    <th class="px-4 py-2 text-center">Perolehan</th>
                                    <th class="px-4 py-2 text-right">Nilai Asset (Rp)</th>
                                    <th class="px-4 py-2 text-center">Lokasi</th>
                                    <th class="px-4 py-2 text-center w-28">Status</th>
                                    <th class="px-4 py-2 text-center w-28">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(a, idx) in filteredAssets()" :key="a.id">
                                    <tr class="group bg-white hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300">
                                        <td class="px-4 py-4 rounded-l-2xl border-l border-y border-slate-100 text-center">
                                            <input type="checkbox" :value="a.id" x-model="selectedRows"
                                                class="rounded-md border-slate-200 text-blue-600 focus:ring-blue-500 cursor-pointer">
                                        </td>
                                        <td class="px-4 py-4 border-y border-slate-100 text-center">
                                            <span class="text-xs font-bold text-slate-300" x-text="'#' + (idx + 1)"></span>
                                        </td>
                                        <td class="px-4 py-4 border-y border-slate-100">
                                            <p class="text-sm font-black text-slate-800 leading-tight" x-text="a.nama_barang"></p>
                                            <template x-if="a.keterangan">
                                                <p class="text-[9px] text-slate-400 font-bold uppercase mt-1 tracking-tighter" x-text="a.keterangan"></p>
                                            </template>
                                            <template x-if="!a.keterangan">
                                                <p class="text-[9px] text-slate-300 italic mt-1 uppercase tracking-tighter">No Description</p>
                                            </template>
                                        </td>
                                        <td class="px-4 py-4 border-y border-slate-100 text-center">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black bg-blue-50 text-blue-600 border border-blue-100" x-text="a.jumlah"></span>
                                        </td>
                                        <td class="px-4 py-4 border-y border-slate-100 text-center">
                                            <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest bg-slate-100 px-2 py-1 rounded-md inline-block" x-text="formatDate(a.tahun_perolehan)"></div>
                                        </td>
                                        <td class="px-4 py-4 border-y border-slate-100 text-right">
                                            <p class="text-sm font-black text-slate-700" x-text="formatRupiah(a.harga_perolehan)"></p>
                                        </td>
                                        <td class="px-4 py-4 border-y border-slate-100 text-center">
                                            <span class="text-[10px] font-black uppercase text-slate-600">
                                                <svg class="w-3 h-3 inline-block mb-0.5 mr-0.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span x-text="a.lokasi"></span>
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 border-y border-slate-100 text-center">
                                            <template x-if="a.status == 2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-50 text-emerald-600 border border-emerald-100">✓ Approved</span>
                                            </template>
                                            <template x-if="a.status != 2">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-50 text-amber-600 border border-amber-100">Pending</span>
                                            </template>
                                        </td>
                                        <td class="px-4 py-4 rounded-r-2xl border-r border-y border-slate-100 text-center">
                                            <template x-if="a.status == 2">
                                                <span class="text-xs text-slate-400 italic">Locked</span>
                                            </template>
                                            <template x-if="a.status != 2">
                                                <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all transform group-hover:scale-100 scale-90">
                                                    <button @click="editEntries([a.id])" title="Edit Asset"
                                                        class="p-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition shadow-sm">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <button @click="confirmDeleteAsset(a.id, unitId)" title="Hapus Asset"
                                                        class="p-2 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition shadow-sm">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="allDataAsset.length === 0">
                                    <tr>
                                        <td colspan="9" class="py-24 text-center">
                                            <div class="flex flex-col items-center opacity-20">
                                                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                                <p class="font-black uppercase tracking-widest text-sm">Belum ada asset terdaftar</p>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="allDataAsset.length > 0 && filteredAssets().length === 0">
                                    <tr>
                                        <td colspan="9" class="py-24 text-center">
                                            <div class="flex flex-col items-center opacity-20">
                                                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                                </svg>
                                                <p class="font-black uppercase tracking-widest text-sm">Data tidak tersedia dengan filter saat ini</p>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- CONTENT: FORM VIEW --}}
                <div x-show="view === 'form'" class="flex flex-col h-full overflow-hidden bg-slate-50/50">
                    <div class="px-8 py-4 bg-white border-b border-slate-100 flex justify-between items-center shadow-sm z-10">
                        <div class="flex items-center gap-4">
                            <span class="px-3 py-1 bg-slate-100 text-slate-500 rounded-lg text-[10px] font-black uppercase tracking-widest">
                                Draft: <span x-text="entries.length" class="text-slate-800"></span> Transaksi
                            </span>
                        </div>
                        <button type="button" @click="addRow()"
                            class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Baris Baru
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-8 custom-scrollbar">
                        <form :action="activeType === 'kas'
                            ? (isEdit ? `/unit/detail/${unitId}/kas-kecil` : `/unit/detail/${unitId}/kas-kecil`)
                            : (isEdit ? `/unit/detail/${unitId}/asset` : `/unit/detail/${unitId}/asset`)"
                            method="POST" enctype="multipart/form-data" id="bulkForm">
                            @csrf
                            <template x-if="isEdit">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <template x-for="(entry, index) in entries" :key="index">
                                <div class="relative p-8 bg-white rounded-[2.5rem] border border-slate-200 mb-8 transition-all hover:shadow-2xl hover:shadow-slate-200/50 group">
                                    <input type="hidden" :name="activeType + '[' + index + '][id]'" x-model="entry.id">
                                    <div class="absolute -top-3 left-8 px-4 py-1 bg-slate-800 text-white text-[9px] font-black uppercase tracking-widest rounded-full shadow-lg">
                                        <span x-text="isEdit ? 'Update Item ID: ' + entry.id : 'Item #' + (index + 1)"></span>
                                    </div>
                                    <button type="button" x-show="entries.length > 1" @click="removeRow(index)"
                                        class="absolute -top-3 -right-3 p-2 bg-rose-500 text-white rounded-full shadow-lg hover:scale-110 transition group-hover:rotate-90">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    {{-- FORM FIELDS: KAS --}}
                                    <template x-if="activeType === 'kas'">
                                        <div class="space-y-6">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div>
                                                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Tanggal</label>
                                                    <input type="date" :name="'kas[' + index + '][tgl]'" x-model="entry.tgl"
                                                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:bg-white transition-all">
                                                </div>
                                                <div>
                                                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1 text-blue-600">File Lampiran / Nota</label>
                                                    <input type="file" :name="'kas[' + index + '][nota]'" x-model="entry.nota"
                                                        class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-600">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Akun</label>
                                                <input type="text" :name="'kas[' + index + '][akun]'" x-model="entry.akun" placeholder="Ketik akun di sini..."
                                                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:bg-white transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Deskripsi Transaksi</label>
                                                <input type="text" :name="'kas[' + index + '][ket]'" x-model="entry.keterangan" placeholder="Ketik keterangan di sini..."
                                                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:bg-white transition-all">
                                            </div>
                                            <div class="grid grid-cols-2 gap-6">
                                                <div class="p-6 bg-emerald-50/50 rounded-3xl border border-emerald-100 transition-all focus-within:border-emerald-400 focus-within:bg-white">
                                                    <span class="text-[9px] font-black text-emerald-600 block mb-2 uppercase tracking-widest">Debit (Uang Masuk)</span>
                                                    <div class="relative flex items-center">
                                                        <span class="text-sm font-black text-emerald-400 mr-2">Rp</span>
                                                        <input type="text" x-model="entry.debit_display" @input="handleRupiahInput(index, 'debit')" placeholder="0"
                                                            class="w-full bg-transparent border-none p-0 text-xl font-black text-emerald-700 focus:ring-0">
                                                        <input type="hidden" :name="'kas[' + index + '][debit]'" :value="entry.debit">
                                                    </div>
                                                </div>
                                                <div class="p-6 bg-rose-50/50 rounded-3xl border border-rose-100 transition-all focus-within:border-rose-400 focus-within:bg-white">
                                                    <span class="text-[9px] font-black text-rose-600 block mb-2 uppercase tracking-widest">Kredit (Uang Keluar)</span>
                                                    <div class="relative flex items-center">
                                                        <span class="text-sm font-black text-rose-400 mr-2">Rp</span>
                                                        <input type="text" x-model="entry.kredit_display" @input="handleRupiahInput(index, 'kredit')" placeholder="0"
                                                            class="w-full bg-transparent border-none p-0 text-xl font-black text-rose-700 focus:ring-0">
                                                        <input type="hidden" :name="'kas[' + index + '][kredit]'" :value="entry.kredit">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- FORM FIELDS: ASSET --}}
                                    <template x-if="activeType === 'asset'">
                                        <div class="space-y-6">
                                            <div>
                                                <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nama Asset / Barang</label>
                                                <input type="text" :name="'asset[' + index + '][nama_barang]'" x-model="entry.nama_barang" placeholder="Contoh: Laptop MacBook Pro..." required
                                                    class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:bg-white transition-all">
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                                <div>
                                                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Jumlah (Qty)</label>
                                                    <input type="number" :name="'asset[' + index + '][jumlah]'" x-model="entry.jumlah" min="1"
                                                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:bg-white transition-all">
                                                </div>
                                                <div>
                                                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Tgl Perolehan</label>
                                                    <input type="date" :name="'asset[' + index + '][tgl_perolehan]'" x-model="entry.tgl_perolehan" required
                                                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:bg-white transition-all">
                                                </div>
                                                <div>
                                                    <label class="block text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Keterangan</label>
                                                    <input type="text" :name="'asset[' + index + '][keterangan]'" x-model="entry.keterangan"
                                                        class="w-full px-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-blue-500/10 focus:bg-white transition-all">
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                                <div class="p-6 bg-blue-50/30 rounded-3xl border border-blue-100 focus-within:bg-white transition-all">
                                                    <span class="text-[9px] font-black text-blue-600 block mb-2 uppercase tracking-widest">Harga Perolehan</span>
                                                    <div class="flex items-center">
                                                        <span class="text-sm font-black text-blue-400 mr-2">Rp</span>
                                                        <input type="text" x-model="entry.harga_display" @input="handleRupiahInput(index, 'harga')" placeholder="0"
                                                            class="w-full bg-transparent border-none p-0 text-xl font-black text-blue-700 focus:ring-0">
                                                    </div>
                                                    <input type="hidden" :name="'asset[' + index + '][harga]'" :value="entry.harga">
                                                </div>
                                                <div class="p-6 bg-slate-50 rounded-3xl border border-slate-100 focus-within:bg-white transition-all">
                                                    <span class="text-[9px] font-black text-slate-400 block mb-2 uppercase tracking-widest">Lokasi Penempatan</span>
                                                    <input type="text" :name="'asset[' + index + '][lokasi]'" x-model="entry.lokasi" placeholder="Contoh: Gudang Barat..."
                                                        class="w-full bg-transparent border-none p-0 text-sm font-bold text-slate-700 focus:ring-0">
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </form>
                    </div>

                    {{-- FIXED FOOTER SUMMARY DASHBOARD --}}
                    <div class="px-8 py-6 bg-white border-t border-slate-100 flex flex-col md:flex-row items-center justify-between gap-6 shadow-[0_-10px_30px_rgba(0,0,0,0.03)]">
                        <div class="flex flex-wrap gap-8">
                            <div class="text-left">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1" x-text="footerTotals.label1"></p>
                                <p class="text-base font-black" :class="activeType === 'kas' ? 'text-emerald-600' : 'text-blue-600'">
                                    <span x-show="footerTotals.isMoney1">Rp </span>
                                    <span x-text="formatRupiah(footerTotals.val1)"></span>
                                </p>
                            </div>
                            <div class="text-left border-l border-slate-100 pl-8">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1" x-text="footerTotals.label2"></p>
                                <p class="text-base font-black" :class="activeType === 'kas' ? 'text-rose-600' : 'text-slate-800'">
                                    Rp <span x-text="formatRupiah(footerTotals.val2)"></span>
                                </p>
                            </div>
                            <div class="text-left border-l border-slate-100 pl-8" x-show="footerTotals.label3 !== null">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1" x-text="footerTotals.label3"></p>
                                <p class="text-base font-black text-slate-800" :class="footerTotals.val3 < 0 ? 'text-rose-500' : 'text-slate-800'">
                                    Rp <span x-text="formatRupiah(footerTotals.val3)"></span>
                                </p>
                            </div>
                        </div>
                        <div>
                            <button type="submit" form="bulkForm"
                                class="flex items-center gap-3 px-10 py-5 bg-slate-900 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-black transition active:scale-95 shadow-xl shadow-slate-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                <span x-text="isEdit ? 'Update Data' : 'Simpan ' + (activeType === 'kas' ? 'Transaksi' : 'Asset')"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- CONTENT: EXPORT SETTINGS --}}
                <div x-show="view === 'export_settings'" class="flex-1 flex flex-col h-full overflow-hidden bg-slate-50/50" x-transition>
                    <div class="px-8 py-6 bg-white border-b border-slate-100 flex items-center justify-between shadow-sm z-20">
                        <div class="flex items-center gap-4">
                            <button @click="view = 'list'" class="group p-2 bg-slate-50 rounded-full hover:bg-slate-900 transition-all duration-300">
                                <svg class="w-5 h-5 text-slate-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <div>
                                <h4 class="font-black text-slate-800 uppercase tracking-widest text-[11px]">Konfigurasi Laporan</h4>
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">Langkah Terakhir sebelum Generate</p>
                            </div>
                        </div>
                        <div class="px-3 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-blue-100">
                            Format: <span x-text="exportFormat"></span>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto p-10 custom-scrollbar">
                        <div class="max-w-md mx-auto">
                            <div class="text-center mb-12">
                                <div class="relative inline-block">
                                    <div class="w-24 h-24 bg-blue-600 text-white rounded-[2.5rem] flex items-center justify-center mx-auto mb-4 shadow-2xl shadow-blue-200 transform rotate-3">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </div>
                                    <div class="absolute -bottom-2 -right-2 bg-emerald-500 text-white p-2 rounded-full shadow-lg border-4 border-white">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-xl font-black text-slate-800 mt-4">Tanda Tangan Laporan</h3>
                                <p class="text-xs text-slate-400 font-bold leading-relaxed max-w-[280px] mx-auto uppercase tracking-tighter mt-2">Lengkapi nama penanggung jawab untuk bagian pengesahan dokumen.</p>
                            </div>

                            <form :action="`/unit/detail/${unitId}/kas-kecil/export`" method="post" enctype="multipart/form-data" id="exportFinalForm" target="_blank">
                                @csrf
                                <template x-for="id in selectedRows" :key="id">
                                    <input type="hidden" name="kasKecilIds[]" :value="id">
                                </template>
                                <input type="hidden" name="format" :value="exportFormat">

                                <div class="space-y-8">
                                    <div class="group">
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1 group-focus-within:text-blue-500 transition-colors">Diajukan Oleh (Staff)</label>
                                        <input type="text" name="diajukan" x-model="approvals.diajukan" placeholder="Masukkan nama pengaju..." required
                                            class="w-full px-7 py-5 bg-white border border-slate-100 rounded-[1.5rem] text-sm font-bold shadow-sm focus:ring-[12px] focus:ring-blue-500/5 focus:border-blue-400 focus:bg-white transition-all outline-none">
                                    </div>
                                    <div class="group">
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1 group-focus-within:text-blue-500 transition-colors">Diperiksa Oleh (Manager)</label>
                                        <input type="text" name="diperiksa" x-model="approvals.diperiksa" placeholder="Masukkan nama atasan..." required
                                            class="w-full px-7 py-5 bg-white border border-slate-100 rounded-[1.5rem] text-sm font-bold shadow-sm focus:ring-[12px] focus:ring-blue-500/5 focus:border-blue-400 focus:bg-white transition-all outline-none">
                                    </div>
                                    <div class="group">
                                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 ml-1 group-focus-within:text-blue-500 transition-colors">Disetujui Oleh (Accounting)</label>
                                        <input type="text" name="disetujui" x-model="approvals.disetujui" placeholder="Masukkan nama penyetuju..." required
                                            class="w-full px-7 py-5 bg-white border border-slate-100 rounded-[1.5rem] text-sm font-bold shadow-sm focus:ring-[12px] focus:ring-blue-500/5 focus:border-blue-400 focus:bg-white transition-all outline-none">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="px-10 py-10 bg-white border-t border-slate-100 flex items-center justify-between shadow-[0_-15px_50px_rgba(0,0,0,0.04)] z-20">
                        <div class="hidden md:block">
                            <p class="text-[10px] font-black text-slate-300 uppercase tracking-[0.2em]">Dokumen Siap</p>
                            <p class="text-xs font-bold text-slate-500">Laporan Kas Kecil Unit</p>
                        </div>
                        <button type="submit" form="exportFinalForm" @click="setTimeout(() => showModal = false, 800)"
                            class="group relative flex items-center gap-4 px-14 py-6 bg-slate-900 text-white text-[12px] font-black uppercase tracking-[0.3em] rounded-[2rem] hover:bg-black hover:px-16 transition-all duration-300 shadow-2xl shadow-slate-300 active:scale-95">
                            <span class="relative z-10">Generate Report</span>
                            <svg class="w-5 h-5 relative z-10 group-hover:translate-x-2 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
                        </button>
                    </div>
                </div>

                <form id="exportAssetForm" :action="`/unit/detail/${unitId}/asset/export`" method="POST" target="_blank" style="display:none;">
                    @csrf
                    <template x-for="id in selectedRows">
                        <input type="hidden" name="assetIds[]" :value="id">
                    </template>
                    <input type="hidden" name="format" value="excel">
                </form>
            </div>
        </div>

        {{-- FLOATING MULTI-ACTION TOOLBAR --}}
        <div x-show="showModal && view === 'list' && selectedRows.length > 0"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-20" x-transition:enter-end="opacity-100 translate-y-0"
            class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[200] w-max max-w-[90vw] bg-white rounded-2xl shadow-2xl text-slate-600 px-6 py-4 border border-slate-100 flex items-center gap-6 backdrop-blur-xl shadow-[0_20px_50px_rgba(0,0,0,0.15)]">
            <div class="flex items-center gap-4 border-r border-slate-200 pr-6">
                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center font-black text-sm text-white shadow-lg shadow-blue-500/20" x-text="selectedRows.length"></div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Data Terpilih</p>
            </div>
            <div class="flex items-center gap-3 whitespace-nowrap">
                <button type="button" @click="openExportSettings('excel')"
                    class="px-6 py-2 bg-green-500/10 text-green-500 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-green-500 hover:text-white transition">Export Excel (<span x-text="selectedRows.length"></span>)</button>

                @if(in_array(auth()->user()->role, ['admin', 'hrd', 'akuntan']))
                <button type="button" @click="approveSelected()" x-show="!hasApprovedSelected()"
                    class="px-6 py-2 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-emerald-700 transition shadow-lg">Approve (<span x-text="selectedRows.length"></span>)</button>
                <div x-show="hasApprovedSelected()" title="Terdapat data yang sudah disetujui dalam pilihan Anda" class="relative group">
                    <button type="button" disabled
                        class="px-6 py-2 bg-slate-200 text-slate-400 text-[10px] font-black uppercase tracking-widest rounded-xl cursor-not-allowed opacity-70 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        Approve
                    </button>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-80 px-3 py-2 bg-white rounded-2xl shadow-2xl text-slate-600 text-[11px] font-semibold text-center leading-relaxed opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                        Ada data yang sudah <span class="text-emerald-400">disetujui</span> dalam pilihan Anda.
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="/js/main-unit.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('unitManager', () => ({
                showModal: false,
                activeType: 'kas',
                view: 'list',
                unitId: null,
                unitName: '',
                entries: [],
                selectedRows: [],
                isEdit: false,

                allData: [],
                allDataAsset: [],

                selectedMonths: [],
                selectedYears: [],
                selectedStatus: '',
                monthsList: [
                    { value: '01', label: 'Januari' },
                    { value: '02', label: 'Februari' },
                    { value: '03', label: 'Maret' },
                    { value: '04', label: 'April' },
                    { value: '05', label: 'Mei' },
                    { value: '06', label: 'Juni' },
                    { value: '07', label: 'Juli' },
                    { value: '08', label: 'Agustus' },
                    { value: '09', label: 'September' },
                    { value: '10', label: 'Oktober' },
                    { value: '11', label: 'November' },
                    { value: '12', label: 'Desember' }
                ],

                getYears() {
                    let dates = this.activeType === 'kas' 
                        ? this.allData.map(d => d.tanggal) 
                        : this.allDataAsset.map(d => d.tahun_perolehan);
                    let years = dates.map(d => d ? d.substring(0, 4) : '').filter((v, i, a) => v && a.indexOf(v) === i);
                    if (years.length === 0) {
                        years = [new Date().getFullYear().toString()];
                    }
                    return years.sort();
                },

                formatPeriode() {
                    let monthsStr = '';
                    if (this.selectedMonths.length > 0) {
                        monthsStr = this.selectedMonths.map(m => {
                            const found = this.monthsList.find(item => item.value === m);
                            return found ? found.label : '';
                        }).join(', ');
                    } else {
                        monthsStr = 'Semua Bulan';
                    }

                    let yearsStr = '';
                    if (this.selectedYears.length > 0) {
                        yearsStr = this.selectedYears.join(', ');
                    } else {
                        yearsStr = 'Semua Tahun';
                    }

                    return `${monthsStr} ${yearsStr}`;
                },

                shouldShowRow(dateString) {
                    if (!dateString) return true;
                    let parts = dateString.split('-');
                    if (parts.length < 3) return true;
                    let year = parts[0];
                    let month = parts[1];

                    if (this.selectedMonths.length > 0 && !this.selectedMonths.includes(month)) {
                        return false;
                    }
                    if (this.selectedYears.length > 0 && !this.selectedYears.includes(year)) {
                        return false;
                    }
                    return true;
                },

                hasApprovedSelected() {
                    return this.selectedRows.some(id => {
                        let item = this.activeType === 'kas'
                            ? this.allData.find(d => d.id == id)
                            : this.allDataAsset.find(d => d.id == id);
                        return item && item.status == 2;
                    });
                },

                filteredKasKecil() {
                    let saldo = 0;
                    const mapped = this.allData.map(kas => {
                        saldo += (Number(kas.debit) || 0) - (Number(kas.kredit) || 0);
                        return {
                            ...kas,
                            runningSaldo: saldo
                        };
                    });
                    return mapped.filter(kas => {
                        if (!this.shouldShowRow(kas.tanggal)) return false;
                        if (this.selectedStatus === 'approved' && kas.status != 2) return false;
                        if (this.selectedStatus === 'pending' && kas.status == 2) return false;
                        return true;
                    });
                },

                filteredAssets() {
                    return this.allDataAsset.filter(asset => {
                        if (!this.shouldShowRow(asset.tahun_perolehan)) return false;
                        if (this.selectedStatus === 'approved' && asset.status != 2) return false;
                        if (this.selectedStatus === 'pending' && asset.status == 2) return false;
                        return true;
                    });
                },

                approveSelected() {
                    if (this.selectedRows.length === 0) return;

                    const typeLabel = this.activeType === 'kas' ? 'Kas Kecil' : 'Asset';
                    Swal.fire({
                        title: 'Approve Data?',
                        text: `Apakah Anda yakin ingin menyetujui ${this.selectedRows.length} ${typeLabel} terpilih?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#10b981',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Setujui',
                        cancelButtonText: 'Batal',
                        customClass: {
                            popup: 'rounded-[2rem]',
                            confirmButton: 'rounded-xl px-6 py-3',
                            cancelButton: 'rounded-xl px-6 py-3'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const url = this.activeType === 'kas' 
                                ? `/unit/${this.unitId}/kas-kecil/approve`
                                : `/unit/${this.unitId}/asset/approve`;

                            fetch(url, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    ids: this.selectedRows
                                })
                            })
                            .then(res => res.json())
                            .then(data => {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 1500,
                                    customClass: {
                                        popup: 'rounded-[2rem]'
                                    }
                                }).then(() => {
                                    location.reload();
                                });
                            })
                            .catch(() => {
                                Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                            });
                        }
                    });
                },

                openModal(detail) {
    this.unitId = detail.unitId;
    this.unitName = detail.unitName;
    this.activeType = detail.type;
    this.allData = [];
    this.allDataAsset = [];
    this.view = 'list';
    this.showModal = true;
    this.selectedStatus = '';

    fetch(`/unit/detail/${detail.unitId}/data`)
        .then(res => {
            if (!res.ok) throw new Error('Server returned ' + res.status);
            return res.json();
        })
        .then(data => {
            this.allData = data.kasKecil || [];
            this.allDataAsset = data.assets || [];
        })
        .catch(err => {
            console.error('Fetch Error:', err);
            Swal.fire({
                icon: 'error',
                title: 'Gagal memuat data',
                text: 'Terjadi kesalahan pada server (Error 500). Periksa log Laravel.'
            });
        });
},

                openExportSettings(format) {
                    this.exportFormat = format;

                    if (this.activeType === 'kas') {
                        this.view = 'export_settings';
                    } else {
                        this.$nextTick(() => {
                            document.getElementById('exportAssetForm').submit();
                        });
                    }
                },

                approvals: {
                    diajukan: '',
                    diperiksa: '',
                    disetujui: ''
                },
                exportFormat: 'excel',

                openForm() {
                    this.isEdit = false;
                    this.view = 'form';
                    this.entries = [this.getEmptyRow()];
                },

                editEntries(ids) {
                    this.isEdit = true;
                    this.view = 'form';

                    this.entries = ids.map(id => {
                        if (this.activeType === 'kas') {
                            const item = this.allData.find(d => d.id == id);
                            return {
                                id: item.id,
                                tgl: item.tanggal,
                                akun: item.akun,
                                keterangan: item.keterangan,
                                debit: item.debit,
                                kredit: item.kredit,
                                debit_display: this.formatRupiah(item.debit),
                                kredit_display: this.formatRupiah(item.kredit),
                            };
                        } else {
                            const item = this.allDataAsset.find(d => d.id == id);
                            return {
                                id: item.id,
                                nama_barang: item.nama_barang,
                                jumlah: item.jumlah,
                                tgl_perolehan: item.tahun_perolehan,
                                keterangan: item.keterangan,
                                harga: item.harga_perolehan,
                                harga_display: this.formatRupiah(item.harga_perolehan),
                                lokasi: item.lokasi
                            };
                        }
                    });
                },

                getEmptyRow() {
                    if (this.activeType === 'kas') {
                        return {
                            tgl: '',
                            keterangan: '',
                            debit: 0,
                            kredit: 0,
                            debit_display: '',
                            kredit_display: '',
                            akun: '',
                            nota: ''
                        };
                    } else {
                        return {
                            id: null,
                            nama_barang: '',
                            jumlah: 1,
                            harga: 0,
                            harga_display: '',
                            tgl_perolehan: '',
                            keterangan: '',
                            lokasi: '',
                            status: 0,
                        };
                    }
                },

                addRow() {
                    this.entries.push(this.getEmptyRow());
                },
                removeRow(index) {
                    if (this.entries.length > 1) this.entries.splice(index, 1);
                },

                handleRupiahInput(index, field) {
                    let rawValue = this.entries[index][field + '_display'].replace(/\D/g, '');
                    this.entries[index][field] = Number(rawValue) || 0;
                    this.entries[index][field + '_display'] = new Intl.NumberFormat('id-ID').format(rawValue);
                    if (rawValue === '') this.entries[index][field + '_display'] = '';
                },

                toggleSelectAll(allIds) {
                    if (this.selectedRows.length === allIds.length) {
                        this.selectedRows = [];
                    } else {
                        this.selectedRows = allIds;
                    }
                },

                get footerTotals() {
                    if (this.activeType === 'kas') {
                        let d = this.entries.reduce((sum, e) => sum + (e.debit || 0), 0);
                        let k = this.entries.reduce((sum, e) => sum + (e.kredit || 0), 0);
                        return {
                            label1: 'Total Debit',
                            val1: d,
                            label2: 'Total Kredit',
                            val2: k,
                            label3: 'Saldo Baru',
                            val3: d - k,
                            isMoney1: true
                        };
                    } else {
                        let q = this.entries.reduce((sum, e) => sum + (Number(e.jumlah) || 0), 0);
                        let v = this.entries.reduce((sum, e) => sum + (e.harga || 0), 0);
                        return {
                            label1: 'Total Barang',
                            val1: q,
                            label2: 'Total Perolehan',
                            val2: v,
                            label3: null,
                            val3: 0,
                            isMoney1: false
                        };
                    }
                },

                formatRupiah(number) {
                    return new Intl.NumberFormat('id-ID').format(number);
                },

                formatDate(dateString) {
                    if (!dateString) return '-';
                    const date = new Date(dateString);
                    if (isNaN(date.getTime())) return dateString;
                    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    return `${String(date.getDate()).padStart(2, '0')} ${months[date.getMonth()]} ${date.getFullYear()}`;
                }
            }))
        });

        function confirmDeleteKas(ids, unitId) {
            const isBulk = Array.isArray(ids);
            const count = isBulk ? ids.length : 1;

            Swal.fire({
                title: 'Hapus Data?',
                text: `Apakah Anda yakin ingin menghapus ${count} transaksi ini?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-[2rem]',
                    confirmButton: 'rounded-xl px-6 py-3',
                    cancelButton: 'rounded-xl px-6 py-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/unit/${unitId}/kas-kecil/destroy`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ids: ids
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    popup: 'rounded-[2rem]'
                                }
                            }).then(() => {
                                location.reload();
                            });
                        })
                        .catch(() => {
                            Swal.fire('Error', 'Terjadi kesalahan sistem', 'error');
                        });
                }
            });
        }

        function confirmDeleteAsset(ids, unitId) {
            const isBulk = Array.isArray(ids);
            const count = isBulk ? ids.length : 1;

            Swal.fire({
                title: 'Hapus Asset?',
                text: `Hapus ${count} barang dari inventaris unit?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-[2rem]',
                    confirmButton: 'rounded-xl px-6 py-3',
                    cancelButton: 'rounded-xl px-6 py-3'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/unit/${unitId}/asset/destroy`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                ids: ids
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    popup: 'rounded-[2rem]'
                                }
                            }).then(() => location.reload());
                        });
                }
            });
        }
    </script>
@endsection
