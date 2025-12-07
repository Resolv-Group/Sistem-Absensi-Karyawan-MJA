@extends('layout')

@section('header')
    <x-header title="Daftar Pekerja" subtitle="List semua karyawan" />
@endsection

@section('content')
    {{-- ================================
         1. STATS OVERVIEW CARD
         (Mimics the "Overview of period" section)
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
                        <th scope="col" class="px-6 py-3 text-left">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nama Pekerja
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            ID Pekerja
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            KTP / KK
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nomor Rekening
                        </th>
                        <th scope="col"
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($pekerja as $p)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            {{-- Checkbox --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </td>

                            {{-- Name & Avatar --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-9 w-9">

                                        {{-- Logic: Check if Blob Image exists --}}
                                        @if ($p->image_base64)
                                            {{-- Show Real Image --}}
                                            {{-- Added 'object-cover' so the image doesn't get squashed --}}
                                            <img class="h-9 w-9 rounded-full object-cover border border-gray-200"
                                                src="{{ $p->image_base64 }}" alt="{{ $p->nama }}">
                                        @else
                                            {{-- Show Placeholder (UI Avatars) --}}
                                            <img class="h-9 w-9 rounded-full bg-gray-200"
                                                src="https://ui-avatars.com/api/?name={{ urlencode($p->nama) }}&background=random&color=fff&size=128"
                                                alt="{{ $p->nama }}">
                                        @endif

                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $p->nama }}</div>
                                        <div class="text-xs text-gray-500">Bergabung:
                                            {{ formatTanggal($p->tgl_bergabung) }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- ID & Job --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900 font-semibold">{{ $p->id }}</div>
                            </td>

                            {{-- KTP / NIK --}}
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-xs text-gray-500">
                                    <span class="block">NIK: <span
                                            class="text-gray-700 font-medium">{{ $p->nik }}</span></span>
                                    <span class="block mt-0.5">KK : <span
                                            class="text-gray-700 font-medium">{{ $p->no_kk }}</span></span>
                                </div>
                            </td>

                            {{-- Rekening --}}
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $p->rekening ?? '-' }}
                            </td>

                            {{-- Status Pill --}}
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if ($p->status_aktif == 1)
                                    <span
                                        class="inline-flex px-3 py-1 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                        Aktif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex px-3 py-1 text-xs leading-5 font-semibold rounded-full bg-red-50 text-red-600 border border-red-200">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('view.detail.pekerja', $p->id) }}"
                                    class="text-blue-600 hover:text-blue-900 border border-blue-200 hover:bg-blue-50 rounded-lg px-3 py-1.5 transition">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    <p>Data Pekerja Saat Ini Tidak Tersedia...</p>
                                </div>
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
