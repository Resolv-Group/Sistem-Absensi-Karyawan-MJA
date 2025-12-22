@extends('layout')

@section('header')
    <x-header title="Daftar Unit" subtitle="List semua unit" breadcrumbs="Unit Manajemen"/>
@endsection

@section('content')
    <style>
        /* 1. Hide the default icon in Chrome/Edge/Safari */
        input[type="date"]::-webkit-calendar-picker-indicator {
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
        input[type="date"] {
            -webkit-appearance: none;
            min-height: 2.5rem;
            /* Ensure consistent height */
        }
    </style>

    {{-- ================================
        1. STATS OVERVIEW CARD
    ================================= --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-8 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-800">
                Periode: <span class="text-gray-500 font-normal">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
            </h2>
            {{-- <a href="#" class="text-sm text-blue-600 hover:underline flex items-center gap-1">
                Lihat detail <span>&rarr;</span>
            </a> --}}
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            {{-- Stat Item --}}
            <div class="border-r border-gray-100 last:border-0">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Mitra Kerja</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $totalUnit }}</p>
            </div>
            <div class="border-r border-gray-100 last:border-0">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Mitra Baru</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $unitBaru }}</p>
            </div>
            <div class="border-r border-gray-100 last:border-0">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tidak Aktif</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $tidakAktif }}</p>
            </div>
        </div>
    </div>

    {{-- ================================
    3. TOOLBAR & FILTERS
================================= --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 relative">

        {{-- Left: View Switcher (Visual only) --}}
        <div class="bg-gray-100 p-1 rounded-lg inline-flex self-start sm:self-center">
            <button
                class="bg-white shadow-sm px-3 py-1.5 rounded-md text-sm font-medium text-gray-800 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                List Daftar
            </button>
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

                        {{-- Date Range (Visual Grouping) --}}
                        <div>
                            <label
                                class="block text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1.5">Tanggal
                                Masa Berakhir</label>

                            <div class="flex items-center gap-2">

                                {{-- Start Date --}}
                                <div class="relative flex-1 min-w-0 group"> {{-- Added min-w-0 here --}}
                                    <div
                                        class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none z-10">
                                        <svg class="h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <input type="date" id="startDate"
                                        class="block w-full pl-8 pr-2 py-2 text-xs font-medium bg-gray-50 border-transparent rounded-xl text-gray-600
                       focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 hover:bg-gray-100 transition placeholder-gray-400 relative z-0">
                                </div>

                                <span class="text-gray-300">-</span>

                                {{-- End Date --}}
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
                                        class="block w-full pl-8 pr-2 py-2 text-xs font-medium bg-gray-50 border-transparent rounded-xl text-gray-600
                       focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-100 hover:bg-gray-100 transition placeholder-gray-400 relative z-0">
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Footer/Info (Optional) --}}
                    <div class="mt-6 pt-4 border-t border-gray-50 text-center">
                        <p class="text-[10px] text-gray-400">Filter akan diterapkan otomatis</p>
                    </div>
                </div>
            </div>

            {{-- ADD BUTTON --}}
            <a href="#"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Unit
            </a>
        </div>
    </div>

    {{-- ================================
        4. MAIN TABLE
    ================================= --}}
    <div id="table-wrapper">
        @include('Unit.partials.unit-table')
    </div>
@endsection

@section('scripts')
    <script src="/js/main-unit.js"></script>
@endsection
