@extends('layout')

@section('header')
    <x-header title="Daftar Pekerja" subtitle="List semua karyawan" breadcrumbs="Pekerja Manajemen" />
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

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        {{-- CARD 1: TOTAL PEKERJA --}}
        <div
            class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Pekerja</p>
                    <h3 class="text-3xl font-extrabold text-gray-900">{{ $totalPekerja }}</h3>
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

        {{-- CARD 2: PEKERJA BARU --}}
        <div
            class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Pekerja Baru</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ $pekerjaBaru }}</h3>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-md">Bulan
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

        {{-- CARD 3: PEKERJA PENDING --}}
        <div
            class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Pekerja Pending</p>
                    <h3 class="text-3xl font-extrabold text-gray-900">{{ $pekerjaPendingCount ?? 0 }}</h3>
                </div>
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-amber-50 to-amber-100 flex items-center justify-center text-amber-600 group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div
                class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-amber-500 to-amber-300 opacity-0 group-hover:opacity-100 transition-opacity">
            </div>
        </div>

        {{-- CARD 4: TIDAK AKTIF --}}
        <div
            class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
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
                <input id="searchInput" type="text" data-url="{{ route('view.pekerja') }}"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-white text-sm placeholder-gray-400
                       focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm"
                    placeholder="Cari pekerja...">
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
                                { val: '1', label: 'Aktif Bekerja' },
                                { val: '2', label: 'Pending Approval' },
                                { val: '0', label: 'Tidak Aktif / Resign' }
                            ]
                        }" x-init="$watch('selected', value => {
                            document.getElementById('statusFilter').value = value;
                            if (typeof window.fetchPekerja === 'function') window.fetchPekerja();
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

                        {{-- Unit data for Alpine (safe encoding via script tag) --}}
                        <script>
                            window.__unitList = @json($units->map(fn($u) => ['val' => (string)$u->id, 'label' => $u->nama_unit])->values());
                        </script>

                        {{-- Unit Filter (Alpine Custom Dropdown) --}}
                        <div x-data="{
                            open: false,
                            selected: '',
                            search: '',
                            list: [{ val: '', label: 'Semua Unit' }].concat(window.__unitList || []),
                            get filteredList() {
                                if (!this.search) return this.list;
                                return this.list.filter(item => 
                                    item.label.toLowerCase().includes(this.search.toLowerCase())
                                );
                            }
                        }" x-init="$watch('selected', value => {
                            document.getElementById('unitFilter').value = value;
                            if (typeof window.fetchPekerja === 'function') window.fetchPekerja();
                        }); $watch('open', value => { if (!value) search = ''; })" class="relative group">

                            <label class="block text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1.5">
                                Unit / Penempatan
                            </label>

                            {{-- Hidden Input for the jQuery Script to read --}}
                            <input type="hidden" id="unitFilter" :value="selected">

                            {{-- Trigger Button --}}
                            <div @click="open = !open" @click.outside="open = false"
                                class="relative block w-full pl-9 pr-3 py-2.5 text-sm bg-gray-50 border border-transparent rounded-xl text-gray-700
               cursor-pointer hover:bg-gray-100 transition flex justify-between items-center group-focus-within:ring-2 group-focus-within:ring-blue-100 group-focus-within:bg-white">

                                {{-- Left Icon (Building) --}}
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400 group-hover:text-blue-500 transition" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>

                                {{-- Selected Text --}}
                                <span class="truncate font-medium"
                                    x-text="list.find(x => x.val == selected)?.label || 'Semua Unit'">
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
                                class="absolute w-full mt-1 bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden z-[60] flex flex-col">

                                {{-- Search Input Field --}}
                                <div class="p-2 border-b border-gray-100 sticky top-0 bg-white" @click.stop>
                                    <div class="relative">
                                        <input type="text"
                                            x-model="search"
                                            placeholder="Cari unit..."
                                            class="w-full pl-8 pr-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white transition">
                                        <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none">
                                            <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <ul class="max-h-60 overflow-y-auto py-1">
                                    <template x-for="item in filteredList" :key="item.val">
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
                                    
                                    {{-- Empty State --}}
                                    <li x-show="filteredList.length === 0" class="px-4 py-3 text-xs text-center text-gray-400">
                                        Unit tidak ditemukan
                                    </li>
                                </ul>
                            </div>
                        </div>

                        {{-- Date Range (Visual Grouping) --}}
                        <div>
                            <label
                                class="block text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1.5">Tanggal
                                Bergabung</label>

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

            {{-- PEKERJA PENDING BUTTON (RIGHT NEXT TO FILTER BUTTON) --}}
            @if (in_array(strtolower(trim(Auth::user()->role ?? '')), ['admin', 'hrd', 'superadmin']))
                <button type="button" id="pendingPekerjaBtn" onclick="openPendingModal()"
                    class="flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg text-sm font-bold text-amber-800 shadow-sm hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Pekerja Pending</span>
                    <span id="pendingCountBadge" class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-black text-white bg-amber-600 rounded-full">
                        {{ $pekerjaPendingCount ?? 0 }}
                    </span>
                </button>
            @elseif (strtolower(trim(Auth::user()->role ?? '')) === 'pic')
                <button type="button" onclick="openPendingModal()"
                    class="flex items-center gap-2 px-3 py-2 bg-amber-50 border border-amber-200 rounded-lg text-sm font-bold text-amber-800 shadow-sm hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-500 transition">
                    <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Pengajuan Saya</span>
                    <span class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-black text-white bg-amber-600 rounded-full">
                        {{ $myPendingList->count() ?? 0 }}
                    </span>
                </button>
            @endif

            {{-- ADD BUTTON --}}
            @if (auth()->check() && auth()->user()->role === 'admin')
                <form action="{{ route('pekerja.import') }}" method="POST" enctype="multipart/form-data"
                    class="inline-block">
                    @csrf

                    <label
                        class="cursor-pointer inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">

                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>

                        Import Excel

                        <input type="file" name="file_excel" class="hidden" accept=".xlsx, .xls, .csv"
                            onchange="this.form.submit()">
                    </label>
                </form>
            @endif

            <a href="{{ route('view.tambah.pekerja') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Pekerja
            </a>
        </div>
    </div>

    {{-- ================================
        4. MAIN TABLE
    ================================= --}}
    <div id="table-wrapper">
        @include('Pekerja.partials.pekerja-table')
    </div>
@php
    $isAdmin = in_array(strtolower(trim(Auth::user()->role ?? '')), ['admin', 'hrd', 'superadmin']);
    $list = $isAdmin ? ($pendingPekerjaList ?? []) : ($myPendingList ?? []);
    $count = count($list);
@endphp

<div id="pendingPekerjaModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-gray-900/40 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white/90 backdrop-blur-md rounded-2xl max-w-3xl w-full shadow-2xl overflow-hidden border border-gray-200/80 transform transition-all">
        
        <div class="px-6 py-4 bg-amber-500 text-white flex justify-between items-center">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-white/20 rounded-xl">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    {{-- DYNAMIC TITLE --}}
                    <h3 class="font-bold text-base tracking-tight">
                        {{ $isAdmin ? 'Daftar Approval Pekerja' : 'Pengajuan Saya (Pending Approval)' }}
                    </h3>
                    <p class="text-amber-100 text-xs font-medium">
                        Total: <span class="font-bold text-white underline decoration-amber-300/60" id="total-count">{{ $count }}</span> Data
                    </p>
                </div>
            </div>
            <button type="button" onclick="closePendingModal()" class="text-white/80 hover:text-white p-1.5 rounded-xl hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Bulk Actions Bar (Subtle Amber Tint) -->
        @if($isAdmin && $count > 0)
        <div class="bg-amber-50/70 border-b border-amber-100/80 px-6 py-3 flex justify-between items-center">
            <label class="flex items-center gap-2.5 cursor-pointer select-none">
                <input type="checkbox" id="selectAllPekerja" class="w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500 transition cursor-pointer">
                <span class="text-xs font-bold text-gray-700 uppercase tracking-wider">Pilih Semua</span>
            </label>
            <button id="bulkApproveBtn" disabled onclick="bulkApprove()" class="px-4 py-1.5 bg-emerald-600 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed text-white rounded-xl text-xs font-bold shadow-sm transition-all hover:bg-emerald-700 flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Setujui Terpilih (<span id="selectedCount">0</span>)
            </button>
        </div>
        @endif

        <!-- Content -->
        <div class="p-6 max-h-[55vh] overflow-y-auto custom-scrollbar">
            @if($count > 0)
                <div class="space-y-3">
                    @foreach($list as $item)
                        <div id="pending-row-{{ $item->id }}" class="group bg-white/70 hover:bg-white border border-gray-100 hover:border-amber-200/70 p-3.5 rounded-xl flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition-all">
                            <div class="flex items-center gap-3">
                                @if($isAdmin)
                                <input type="checkbox" name="pekerja_ids[]" value="{{ $item->id }}" class="pekerja-checkbox w-4 h-4 rounded border-gray-300 text-amber-600 focus:ring-amber-500 transition cursor-pointer mt-0.5 sm:mt-0">
                                @endif
                                
                                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 font-bold flex items-center justify-center text-sm border border-amber-100 shrink-0">
                                    {{ strtoupper(substr($item->nama, 0, 1)) }}
                                </div>
                                
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm group-hover:text-amber-700 transition-colors">{{ $item->nama ?? 'Belum Diset' }}</h4>
                                    <p class="text-xs text-gray-500 font-mono">NIK: {{ $item->nik ?? 'Belum Diset' }} | KK: {{ $item->no_kk ?? 'Belum Diset' }}</p>
                                    <p class="text-xs text-gray-500 font-mono">Telp: {{ $item->no_telp ?? 'Belum Diset' }}</p>
                                    <p class="text-xs text-amber-700 font-medium mt-0.5">Diajukan Oleh : {{ $item->user->name ?? 'Belum Diset' }}</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 self-end sm:self-center">
                                <a href="{{ route('view.detail.pekerja', $item->id) }}" target="_blank" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-bold transition">
                                    Detail
                                </a>
                                @if($isAdmin)
                                <button type="button" onclick="approvePekerjaDirect({{ $item->id }}, '{{ addslashes($item->nama) }}')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold shadow-sm transition flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Setujui
                                </button>
                                @else
                                <button type="button" onclick="cancelPekerjaDirect({{ $item->id }}, '{{ addslashes($item->nama) }}')"
                                    class="px-3 py-1.5 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white rounded-lg text-xs font-bold shadow-sm transition flex items-center gap-1 border border-red-200 hover:border-red-600">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                    Batalkan
                                </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-10 text-gray-500">
                    <svg class="w-12 h-12 text-amber-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="font-bold text-gray-700">Tidak ada pekerja pending</p>
                    <p class="text-xs text-gray-400 mt-1">Semua pekerja yang diinput telah disetujui.</p>
                </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="px-6 py-3 bg-gray-50/80 border-t border-gray-100 flex justify-end">
            <button type="button" onclick="closePendingModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-bold rounded-xl transition">Tutup</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script src="/js/main-pekerja.js"></script>

    <script>
    function openPendingModal() {
        document.getElementById('pendingPekerjaModal')?.classList.remove('hidden');
    }
    function closePendingModal() {
        document.getElementById('pendingPekerjaModal')?.classList.add('hidden');
    }

    // --- REUSABLE SWEETALERT HANDLER ---
    function handleAjaxAction(url, method, bodyData, confirmTitle, confirmText, successMessage) {
    Swal.fire({
        title: confirmTitle,
        text: confirmText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Lanjutkan!',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: method === 'GET' ? null : JSON.stringify(bodyData)
            })
            .then(async response => {
                const isJson = response.headers.get('content-type')?.includes('application/json');
                const data = isJson ? await response.json() : null;

                if (!response.ok) {
                    // If not OK, show the error message from JSON or the status text
                    throw new Error(data?.message || `Server Error: ${response.status}`);
                }
                return data;
            })
            .catch(error => {
                Swal.showValidationMessage(`Gagal: ${error.message}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            Swal.fire({
                title: 'Berhasil!',
                text: result.value.message || successMessage,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        }
    });
}

    // --- SINGLE ACTIONS ---

    function approvePekerjaDirect(id, nama) {
        handleAjaxAction(
            '{{ route("pekerja.approve.bulk") }}',
            'POST',
            { ids: [id] },
            'Setujui Pekerja?',
            `Aktifkan data pekerja: ${nama}?`,
            'Pekerja telah diaktifkan.'
        );
    }

    function cancelPekerjaDirect(id, nama) {
        // Generate the URL using the route name, replacing the placeholder with the actual ID
        let url = '{{ route("pekerja.cancel", ":id") }}'.replace(':id', id);

        handleAjaxAction(
            url, 
            'POST', 
            {}, // No need for _method: DELETE since we changed the route to POST
            'Batalkan Pengajuan?',
            `Hapus pengajuan untuk: ${nama}? Tindakan ini permanen.`,
            'Pengajuan telah dihapus.'
        );
    }

    // --- BULK ACTIONS ---

    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAllPekerja');
        const bulkBtn = document.getElementById('bulkApproveBtn');
        const selectedCountSpan = document.getElementById('selectedCount');

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.pekerja-checkbox');
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateBulkUI();
            });
        }

        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('pekerja-checkbox')) {
                updateBulkUI();
            }
        });

        function updateBulkUI() {
            const checkedCount = document.querySelectorAll('.pekerja-checkbox:checked').length;
            if (bulkBtn) bulkBtn.disabled = checkedCount === 0;
            if (selectedCountSpan) selectedCountSpan.textContent = checkedCount;
        }
    });

    function bulkApprove() {
        const ids = Array.from(document.querySelectorAll('.pekerja-checkbox:checked')).map(cb => cb.value);
        if (ids.length === 0) return;

        handleAjaxAction(
            '{{ route("pekerja.approve.bulk") }}',
            'POST',
            { ids: ids },
            'Approve Massal?',
            `Setujui ${ids.length} pekerja terpilih sekaligus?`,
            'Semua pekerja terpilih telah aktif.'
        );
    }
</script>
@endsection
