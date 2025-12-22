@extends('layout')

@section('header')
@endsection

@section('content')
    {{-- Load ApexCharts via CDN (You can also install via npm if preferred) --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- 1. HEADER SECTION --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Halo, {{ Auth::user()->name }}! 👋
                </h1>
                <p class="text-sm text-gray-500 mt-1">
                    Berikut adalah ringkasan aktivitas hari ini, <span
                        class="font-medium text-gray-700">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>.
                </p>
            </div>
            <div class="hidden md:flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-semibold text-gray-700">
                    {{ \Carbon\Carbon::now()->format('H:i') }} WIB
                </span>
            </div>
        </div>

        {{-- 2. GENERAL COMPANY STATS (Pegawai, Unit, Mitra) --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            {{-- Total Pegawai --}}
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Pegawai</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $totalPekerja }}</h3>
                    <span class="text-xs text-green-600 font-medium flex items-center mt-1">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                        +{{ $pegawaiBulanIni }} bulan ini
                    </span>
                </div>
                <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>

            {{-- NEW: Total Unit Aktif --}}
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Unit Aktif</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">12</h3> {{-- Replace with $totalUnit --}}
                    <span class="text-xs text-gray-500 mt-1">Unit Operasional</span>
                </div>
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>

            {{-- NEW: Total Mitra Kerja --}}
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Mitra Kerja</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $totalMitra }}</h3> {{-- Replace with $totalMitra --}}
                    <span class="text-xs text-gray-500 mt-1">Vendor & Klien</span>
                </div>
                <div class="p-3 bg-teal-50 text-teal-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- 3. NEW: CHART SECTION (DIAGRAM PERTUMBUHAN) --}}
        <div class="mb-8">
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Pertumbuhan Karyawan</h3>

                    {{-- Custom Dropdown UI --}}
                    <div x-data="{
                        open: false,
                        selected: '{{ $selectedYear }}',
                        list: [
                            { val: '2025', label: 'Tahun 2025' },
                            { val: '2024', label: 'Tahun 2024' },
                            { val: '2023', label: 'Tahun 2023' }
                        ]
                    }" class="relative w-40 z-10">

                        {{-- Trigger Button --}}
                        <div @click="open = !open"
                            class="border border-gray-300 bg-white text-sm rounded-lg py-2 px-3 cursor-pointer flex justify-between items-center hover:border-blue-500 transition-colors">
                            <span x-text="list.find(x => x.val == selected)?.label || 'Pilih Tahun'"
                                class="text-gray-600 font-medium"></span>
                            <svg class="w-4 h-4 text-gray-400" :class="{ 'rotate-180': open }" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24" style="transition: transform 0.2s">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>

                        {{-- Dropdown List --}}
                        <ul x-show="open" @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute w-full mt-1 border border-gray-200 bg-white rounded-lg shadow-lg overflow-y-auto max-h-40 z-50">

                            <template x-for="item in list" :key="item.val">
                                <li @click="selected = item.val; open = false; window.location.href='?year=' + item.val"
                                    class="px-3 py-2 text-sm cursor-pointer transition"
                                    :class="selected == item.val ? 'bg-blue-50 text-blue-700 font-semibold' :
                                        'text-gray-600 hover:bg-blue-600 hover:text-white'"
                                    x-text="item.label">
                                </li>
                            </template>
                        </ul>
                    </div>
                    {{-- End Custom Dropdown --}}

                </div>
                <div id="employeeGrowthChart" class="w-full h-80" data-chart='@json($employeeChartData)'></div>
            </div>
        </div>

        {{-- 4. DAILY ATTENDANCE STATS (Grid 3) --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
            {{-- Hadir Hari Ini --}}
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Hadir Hari Ini</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">112</h3>
                    <span class="text-xs text-gray-500 mt-1">Absensi masuk tercatat</span>
                </div>
                <div class="p-3 bg-green-50 text-green-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            {{-- Izin / Sakit --}}
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Izin / Sakit</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">4</h3>
                    <span class="text-xs text-orange-600 font-medium mt-1">Perlu review</span>
                </div>
                <div class="p-3 bg-orange-50 text-orange-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
            </div>

            {{-- Terlambat --}}
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-start justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Terlambat</p>
                    <h3 class="text-2xl font-bold text-gray-900 mt-1">12</h3>
                    <span class="text-xs text-red-600 font-medium mt-1">+3 dari kemarin</span>
                </div>
                <div class="p-3 bg-red-50 text-red-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- 5. SPLIT VIEW (Table Left, Sidebar Right) --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- LEFT: Live Attendance Table --}}
            <div class="lg:col-span-2">
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Kehadiran Terbaru</h3>
                        <a href="#"
                            class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">Lihat Semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Pegawai</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Jam Masuk</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ([1, 2, 3, 4, 5] as $i)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <img class="h-8 w-8 rounded-full bg-gray-200"
                                                    src="https://ui-avatars.com/api/?name=User+{{ $i }}&background=random"
                                                    alt="">
                                                <div class="ml-3">
                                                    <p class="text-sm font-bold text-gray-900">Nama Pegawai
                                                        {{ $i }}</p>
                                                    <p class="text-xs text-gray-500">Divisi IT</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium text-gray-900">
                                            07:{{ 45 + $i }} WIB
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if ($i % 2 == 0)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Tepat
                                                    Waktu</span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Terlambat</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- RIGHT: Quick Actions & Alerts --}}
            <div class="space-y-6">

                {{-- Quick Actions --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Pintasan Cepat</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('view.tambah.pekerja') }}"
                            class="flex flex-col items-center justify-center p-4 bg-gray-50 border border-gray-100 rounded-xl hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition group">
                            <div class="p-2 bg-white rounded-full shadow-sm group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                    </path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold mt-2 text-center">Tambah Pegawai</span>
                        </a>
                        <a href="#"
                            class="flex flex-col items-center justify-center p-4 bg-gray-50 border border-gray-100 rounded-xl hover:bg-green-50 hover:border-green-200 hover:text-green-700 transition group">
                            <div class="p-2 bg-white rounded-full shadow-sm group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold mt-2 text-center">Approval Izin</span>
                        </a>
                        <a href="#"
                            class="flex flex-col items-center justify-center p-4 bg-gray-50 border border-gray-100 rounded-xl hover:bg-purple-50 hover:border-purple-200 hover:text-purple-700 transition group">
                            <div class="p-2 bg-white rounded-full shadow-sm group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold mt-2 text-center">Laporan Absen</span>
                        </a>
                        <a href="#"
                            class="flex flex-col items-center justify-center p-4 bg-gray-50 border border-gray-100 rounded-xl hover:bg-orange-50 hover:border-orange-200 hover:text-orange-700 transition group">
                            <div class="p-2 bg-white rounded-full shadow-sm group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <span class="text-xs font-semibold mt-2 text-center">Jadwal Shift</span>
                        </a>
                    </div>
                </div>

                {{-- Important Alerts --}}
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Perlu Perhatian</h3>
                        <span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-0.5 rounded-full">3</span>
                    </div>

                    <div class="space-y-3">
                        {{-- Alert 1: Kontrak Pegawai --}}
                        <div class="flex items-start gap-3 p-3 bg-red-50 rounded-lg border border-red-100">
                            <div class="flex-shrink-0 mt-0.5 text-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-red-900">Kontrak Berakhir</p>
                                <p class="text-xs text-red-700 mt-0.5">Kontrak <strong>Siti Aminah</strong> berakhir dalam
                                    7 hari.</p>
                            </div>
                        </div>

                        {{-- Alert 2 (NEW): Mendekati Masa Kerja Sama (Mitra) --}}
                        <div class="flex items-start gap-3 p-3 bg-orange-50 rounded-lg border border-orange-100">
                            <div class="flex-shrink-0 mt-0.5 text-orange-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-orange-900">Masa Mitra Kerja</p>
                                <p class="text-xs text-orange-700 mt-0.5"><strong> {{ $mitraMendekati }} Kontrak </strong>
                                    akan habis bulan depan.</p>
                            </div>
                        </div>

                        {{-- Alert 3: Approval --}}
                        <div class="flex items-start gap-3 p-3 bg-yellow-50 rounded-lg border border-yellow-100">
                            <div class="flex-shrink-0 mt-0.5 text-yellow-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-yellow-900">Menunggu Approval</p>
                                <p class="text-xs text-yellow-700 mt-0.5">3 Pengajuan cuti menunggu persetujuan Anda.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection

@section('scripts')
    <script src="/js/dashboard.js"></script>
@endsection
