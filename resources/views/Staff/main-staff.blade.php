@extends('layout')

@section('header')
    <x-header title="Daftar Staff" subtitle="List semua staff" />
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
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Staff</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{$totalStaff}}</p>
            </div>
            <div class="border-r border-gray-100 last:border-0">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Staff Baru</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{$staffBaru}}</p>
            </div>
            <div class="border-r border-gray-100 last:border-0">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Tidak Aktif</p>
                <p class="mt-1 text-2xl font-bold text-gray-900">{{$tidakAktif}}</p>
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
                    placeholder="Search staff...">
            </div>

            {{-- Add Button --}}
            <a href="{{ route('view.tambah.staff') }}"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Staff
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
                        <th scope="col" class="px-6 py-3 text-left w-10">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>

                        {{-- COLUMN 1: IDENTITY  --}}
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Pegawai
                        </th>

                        {{-- COLUMN 2: CONTACT --}}
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Kontak
                        </th>

                        {{-- COLUMN 3: TENURE --}}
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Masa Kerja
                        </th>

                        {{-- COLUMN 4: STATUS  --}}
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                            Status
                        </th>

                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    {{-- Loop through data --}}
                    @forelse ($staff as $s)
                        <tr class="hover:bg-gray-50 transition-colors duration-150 group">

                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        @if ($s->image_base64)
                                            <img class="h-10 w-10 rounded-full object-cover border border-gray-200"
                                                src="{{ $s->image_base64 }}" alt="{{ $s->nama }}">
                                        @else
                                            <img class="h-10 w-10 rounded-full bg-gray-200"
                                                src="https://ui-avatars.com/api/?name={{ urlencode($s->nama) }}&background=random&color=fff&size=128"
                                                alt="">
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $s->nama }}</div>
                                        <div class="text-xs text-gray-500 font-mono mt-0.5">
                                            ID: {{ $s->nik }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <div class="flex items-center text-sm text-gray-700">
                                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        {{ $s->email ?? '-' }}
                                    </div>
                                    <div class="flex items-center text-xs text-gray-500 mt-1">
                                        <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                            </path>
                                        </svg>
                                        {{ $s->telp ?? '-' }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1">

                                    {{-- Tanggal Bergabung (Icon Kalender) --}}
                                    <div class="flex items-center text-sm text-gray-900 font-medium">
                                        <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        {{-- Ganti dengan variabel dinamis jika perlu: --}}
                                        {{ \Carbon\Carbon::parse($s->tgl_bergabung)->translatedFormat('d F Y') }}
                                    </div>

                                    {{-- Durasi Kerja (Icon Jam) --}}
                                    <div class="flex items-center text-xs text-gray-500">
                                        <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{-- Ganti dengan variabel dinamis jika perlu: --}}
                                        {{ \Carbon\Carbon::parse($s->tgl_bergabung)->diffForHumans(null, true) }} kerja
                                    </div>

                                </div>
                            </td>
        </div>
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-center">
            @if ($s->status_aktif == 1)
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

        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex justify-end gap-2">
                <a href=""
                    class="text-blue-600 hover:text-blue-900 border border-blue-200 hover:bg-blue-50 rounded-lg px-3 py-1.5 transition text-xs font-semibold">
                    Edit
                </a>
                <a href="{{ route('view.detail.staff') }}"
                    class="text-blue-600 hover:text-blue-900 border border-blue-200 hover:bg-blue-50 rounded-lg px-3 py-1.5 transition text-xs font-semibold">
                    Detail
                </a>
            </div>
        </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                <div class="flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <p class="font-medium">Belum ada data staff.</p>
                    <p class="text-sm mt-1">Silakan tambah staff baru.</p>
                </div>
            </td>
        </tr>
        @endforelse
        <tr class="hover:bg-gray-50 transition-colors duration-150 group">

            {{-- 4. STATUS --}}
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                    Aktif
                </span>
            </td>

            {{-- Actions --}}
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end gap-2">

                </div>
            </td>
        </tr>
        </tbody>
        </table>
    </div>
</td>

                            {{-- 4. STATUS --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                        Aktif
                                    </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href=""
                                        class="text-blue-600 hover:text-blue-900 border border-blue-200 hover:bg-blue-50 rounded-lg px-3 py-1.5 transition text-xs font-semibold">
                                        Edit
                                    </a>
                                    <a href=""
                                        class="text-blue-600 hover:text-blue-900 border border-blue-200 hover:bg-blue-50 rounded-lg px-3 py-1.5 transition text-xs font-semibold">
                                        Detail
                                    </a>
                                </div>
                            </td>
                        </tr>
                </tbody>
            </table>
        </div>

    {{-- Footer / Pagination --}}
    {{-- {{ $sekerja->links('vendor.pagination.custom') }} --}}
    </div>
@endsection
