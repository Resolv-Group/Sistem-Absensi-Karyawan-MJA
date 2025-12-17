@extends('layout')

@section('header')
    <x-header title="Daftar Staff" subtitle="List semua staff" breadcrumbs="Staff Manajemen"/>
@endsection

@section('content')
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
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Staff</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $totalStaff }}</p>
            </div>
            <div class="border-r border-gray-100 last:border-0">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Staff Baru</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $staffBaru }}</p>
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
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">

        {{-- Left: View Switcher (Visual only) --}}
        <div class="bg-gray-100 p-1 rounded-lg inline-flex">
            <button
                class="bg-white shadow-sm px-3 py-1.5 rounded-md text-sm font-medium text-gray-800 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                </svg>
                List Daftar
            </button>
        </div>

        {{-- Right: Search & Actions --}}
        <div class="flex flex-1 justify-end items-center gap-3">

            {{-- Search Bar --}}
            <div class="relative inline-block w-full max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <input id="searchInput" type="text" data-url="{{ route('view.staff') }}"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg bg-white
                focus:outline-none focus:ring-1 focus:ring-blue-500"
                    placeholder="Cari staff - Nama, NIK...">
            </div>

            {{-- Add Button --}}
            <a href="{{ route('view.tambah.staff') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Staff
            </a>
        </div>
    </div>

    {{-- ================================
        4. MAIN TABLE
    ================================= --}}

    <div id="table-wrapper">
        @include('staff.partials.staff-table')
    </div>
@endsection

@section('scripts')
    <script src="/js/main-staff.js"></script>
@endsection
