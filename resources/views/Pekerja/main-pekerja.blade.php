@extends('layout')

@section('header')
    <x-header title="Daftar Pekerja" subtitle="List semua karyawan" />
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
            <a href="#" class="text-sm text-blue-600 hover:underline flex items-center gap-1">
                Lihat detail <span>&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            {{-- Stat Item --}}
            <div class="border-r border-gray-100 last:border-0">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Pekerja</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $totalPekerja }}</p>
            </div>
            <div class="border-r border-gray-100 last:border-0">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Pekerja Baru</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $pekerjaBaru }}</p>
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
                Directory
            </button>
        </div>

        {{-- Right: Search & Actions --}}
        <div class="flex flex-1 justify-end items-center gap-3">

            {{-- Search Bar --}}
            <div class="relative w-full max-w-xs">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text"
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    placeholder="Search pekerja...">
            </div>

            {{-- Add Button --}}
            <a href="{{ route('view.tambah.pekerja') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Pekerja
            </a>
        </div>
    </div>

    {{-- ================================
        4. MAIN TABLE
    ================================= --}}
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left w-10">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Identitas Pekerja
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Kontak
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Domisili
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pekerja as $p)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 group">

                            {{-- CHECKBOX --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>

                            {{-- COL 1: IDENTITAS --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if ($p->image_base64)
                                            <img class="h-10 w-10 rounded-full object-cover border border-gray-200"
                                                src="{{ $p->image_base64 }}" alt="{{ $p->nama }}">
                                        @else
                                            <img class="h-10 w-10 rounded-full bg-gray-200"
                                                src="https://ui-avatars.com/api/?name={{ urlencode($p->nama) }}&background=random&color=fff&size=128"
                                                alt="">
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $p->nama }}</div>
                                        <div class="text-xs text-gray-500 font-mono mt-0.5 tracking-wide">
                                            NIK: {{ $p->nik }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- COL 2: KONTAK --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    {{-- Email --}}
                                    <div class="flex items-center text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        <span class="truncate max-w-[150px]"
                                            title="{{ $p->email }}">{{ $p->email ?? '-' }}</span>
                                    </div>
                                    {{-- Phone --}}
                                    <div class="flex items-center text-xs text-gray-500">
                                        <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                            </path>
                                        </svg>
                                        {{ $p->telp ?? '-' }}
                                    </div>
                                </div>
                            </td>

                            {{-- COL 3: DOMISILI & MASA KERJA (UPDATED) --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">
                                    {{-- Domisili (Icon Map) --}}
                                    <div class="flex items-center text-sm text-gray-900 font-medium">
                                        <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        {{ ucwords(strtolower($p->kota)) }}
                                    </div>

                                    {{-- Masa Kerja (Icon Jam/Waktu) --}}
                                    <div class="flex items-center text-xs text-gray-500">
                                        <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($p->tgl_bergabung)->diffForHumans(null, true) }} bergabung
                                    </div>
                                </div>
                            </td>

                            {{-- COL 4: STATUS --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if ($p->status_aktif)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                        Non-Aktif
                                    </span>
                                @endif
                            </td>

                            {{-- COL 5: ACTIONS --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('view.ubah.pekerja', $p->id) }}"
                                        class="text-blue-600 hover:text-blue-900 border border-blue-200 hover:bg-blue-50 rounded-lg px-3 py-1.5 transition text-xs font-semibold">
                                        Edit
                                    </a>
                                    <a href="{{ route('view.detail.pekerja', $p->id) }}"
                                        class="text-blue-600 hover:text-blue-900 border border-blue-200 hover:bg-blue-50 rounded-lg px-3 py-1.5 transition text-xs font-semibold">
                                        Detail
                                    </a>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                Belum ada data pekerja.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Footer / Pagination --}}
        {{ $pekerja->links('vendor.pagination.custom') }}
    </div>
@endsection
