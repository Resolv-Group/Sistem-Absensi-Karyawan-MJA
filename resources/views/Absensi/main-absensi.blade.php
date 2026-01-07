@extends('layout')

@section('header')
    <x-header title="Daftar Absensi" subtitle="List semua unit absensi" breadcrumbs="Absensi Manajemen" />
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

        {{-- CARD 1: TOTAL Unit --}}
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

        {{-- CARD 2: TOTAL HADIR PEKERJA --}}
        <div
            class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Hadir Pekerja</p>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-extrabold text-gray-900">{{ $totalHadir }}</h3>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-md">Hari
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

        {{-- CARD 3: TOTAL ABSEN PEKERJA --}}
        <div
            class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Absen Pekerja</p>
                    <h3 class="text-3xl font-extrabold text-gray-900">{{ $totalAbsen ?? 0 }}</h3>
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

        {{-- CARD 4: TOTAL PEKERJA DEKAT PENILAIAN --}}
        {{-- On small screens, span full width to fill gap --}}
        <div
            class="bg-white rounded-2xl p-5 border border-gray-100 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group sm:col-span-2 lg:col-span-1">
            <div class="flex justify-between items-start z-10 relative">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Perkerja dekat penilaian</p>
                    <h3 class="text-3xl font-extrabold text-gray-900">{{ $totalPenilaian }}</h3>
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
    {{-- Left: View Switcher (Visual only) --}}
        {{-- <div class="bg-gray-100 p-1 rounded-lg inline-flex self-start sm:self-center">
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
        </div> --}}
    <div 
        x-data="{
            date: '{{ request('date') ?? now()->toDateString() }}',
            go() {
                window.location.href = '{{ route('view.absensi') }}?date=' + this.date
            }
        }"
        class="mb-6 relative inline-flex items-center"
    >
        <div class="inline-flex items-center gap-2 px-3 py-2 bg-white border rounded-lg shadow-sm text-sm">
            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1z"/>
            </svg>
            <span x-text="new Date(date).toLocaleDateString('id-ID', { day:'2-digit', month:'long', year:'numeric' })"></span>
        </div>

        <input
            type="date"
            x-model="date"
            @change="go()"
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
        >
    </div>


    {{-- ================================
        4. MAIN TABLE
    ================================= --}}
    <div id="table-wrapper">
        @include('Absensi.partials.absensi-table')
    </div>
@endsection

@section('scripts')
    <script src="/js/main-absensi.js"></script>
@endsection
