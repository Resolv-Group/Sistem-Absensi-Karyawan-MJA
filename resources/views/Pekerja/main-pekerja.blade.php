@extends('layout')

@section('header')
    <x-header title="Daftar Pekerja" subtitle="List semua karyawan" />
@endsection

@section('content')

    {{-- ================================
        ACTION BAR (Search + Filter + Add)
    ================================= --}}
    <div class="flex flex-wrap items-center justify-between mb-4">

        {{-- LEFT: Search Bar --}}
        <div class="relative">
            <input
                type="text"
                placeholder="Cari pekerja..."
                class="border rounded-lg pl-10 pr-3 py-2 text-sm w-64 focus:outline-none focus:ring-1 focus:ring-blue-500"
            >
            <svg xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5 text-gray-400 absolute left-3 top-2.5"
                viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="11" cy="11" r="7" stroke-width="2"/>
                <path d="M20 20L17 17" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>

        {{-- RIGHT: Filter + Add Button --}}
        <div class="flex items-center gap-3">
            {{-- Filter Button --}}
            <button class="border px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 018 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Filter
            </button>

            {{-- Add Button --}}
            <a href="/t/pekerja"
               class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 flex items-center gap-2">
                <span>+ Tambah</span>
            </a>
        </div>

    </div>


    {{-- ================================
        TABLE WRAPPER
    ================================= --}}
    <div class="bg-white border rounded-xl p-4 shadow-sm">

        {{-- Stats --}}
        <div class="grid grid-cols-4 gap-4 mb-4">
            <div>
                <p class="text-xs text-gray-500">Periode</p>
                <p class="font-semibold text-gray-800">November 2025</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Total Karyawan</p>
                <p class="font-semibold text-gray-800">100</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Karyawan Baru</p>
                <p class="font-semibold text-gray-800">20</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Non-aktif</p>
                <p class="font-semibold text-gray-800">10</p>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-gray-600 border-b bg-gray-50">
                    <tr>
                        <th class="py-2 px-2 text-left">Nama</th>
                        <th class="py-2 px-2">ID Karyawan</th>
                        <th class="py-2 px-2">No Rekening</th>
                        <th class="py-2 px-2">No KK</th>
                        <th class="py-2 px-2">No KTP/NIK</th>
                        <th class="py-2 px-2">Status</th>
                        <th class="py-2 px-2">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @for ($i = 1; $i <= 10; $i++)
                        <tr class="border-b">
                            <td class="py-3 px-2">Rina Kartikasari</td>
                            <td class="py-3 px-2 text-center">4220242420011</td>
                            <td class="py-3 px-2 text-center">720811234567</td>
                            <td class="py-3 px-2 text-center">35168709210011</td>
                            <td class="py-3 px-2 text-center">351618802891001</td>
                            <td class="py-3 px-2 text-center text-green-600">Aktif</td>
                            <td class="py-3 px-2 text-center">
                                <button class="px-3 py-1 border rounded-lg text-xs hover:bg-gray-50">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="flex justify-between items-center mt-4 text-xs text-gray-600">
            <p>Menampilkan 1–10 dari 10.430 hasil</p>

            <div class="flex items-center gap-1">
                <button class="px-2 py-1 border rounded-md">Sebelumnya</button>
                <button class="px-3 py-1 border rounded-md bg-gray-200">1</button>
                <button class="px-3 py-1 border rounded-md">2</button>
                <button class="px-3 py-1 border rounded-md">3</button>
                <button class="px-3 py-1 border rounded-md">...</button>
                <button class="px-3 py-1 border rounded-md">12</button>
                <button class="px-2 py-1 border rounded-md">Selanjutnya</button>
            </div>
        </div>

    </div>

@endsection


