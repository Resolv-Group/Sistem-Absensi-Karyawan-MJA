@extends('layout')

@section('header')
@endsection

@section('content')

    <div x-data="{
        showModal: false,
        showApprovalModal: false,
        selected: {
            pekerja: { nama: '' },
            unit: { nama_unit: '' },
            total: 0,
            mk: 0,
            absensi: 0,
            pengetahuan: 0,
            kualitas: 0,
            sikap: 0,
            keterangan: ''
        },
        getGrade(score) {
            if (!score) return { label: '-', color: 'gray', desc: '-' };
            if (score >= 50) return { label: 'A', color: 'emerald', desc: 'Sangat Baik' };
            if (score >= 41) return { label: 'B', color: 'blue', desc: 'Baik' };
            if (score >= 29) return { label: 'C', color: 'amber', desc: 'Cukup' };
            return { label: 'D', color: 'red', desc: 'Kurang' };
        },
        openDetail(item) {
            this.selected = item;
            this.showModal = true;
        }
    }">

        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>

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
                <div
                    class="hidden md:flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg shadow-sm">
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

            {{-- 2. GENERAL COMPANY STATS --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
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

                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Unit Aktif</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">12</h3>
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

                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Mitra Kerja</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $totalMitra }}</h3>
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

            {{-- 3. CHART SECTION --}}
            <div class="mb-8">
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-gray-900">Pertumbuhan Karyawan</h3>
                        <div x-data="{
                            open: false,
                            selected: '{{ $selectedYear }}',
                            list: [
                                { val: '2028', label: 'Tahun 2028' },
                                { val: '2027', label: 'Tahun 2027' },
                                { val: '2026', label: 'Tahun 2026' },
                                { val: '2025', label: 'Tahun 2025' },
                            ]
                        }" class="relative w-40 z-10">
                            <div @click="open = !open"
                                class="border border-gray-300 bg-white text-sm rounded-lg py-2 px-3 cursor-pointer flex justify-between items-center hover:border-blue-500 transition-colors">
                                <span x-text="list.find(x => x.val == selected)?.label || 'Pilih Tahun'"
                                    class="text-gray-600 font-medium"></span>
                                <svg class="w-4 h-4 text-gray-400" :class="{ 'rotate-180': open }" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" style="transition: transform 0.2s">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <ul x-show="open" @click.outside="open = false" x-transition
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
                    </div>
                    <div id="employeeGrowthChart" class="w-full h-80" data-chart='@json($employeeChartData)'></div>
                </div>
            </div>

            {{-- 4. DAILY ATTENDANCE STATS --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Hadir Hari Ini</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $hadirHariIni }}</h3>
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

                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Cuti / Izin</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $izinSakitHariIni }}</h3>
                        <span class="text-xs text-orange-600 font-medium mt-1">Status tidak hadir / cuti</span>
                    </div>
                    <div class="p-3 bg-orange-50 text-orange-600 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-start justify-between">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Lembur</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $overtimeHariIni }}</h3>
                        <p class="text-[11px] text-amber-600 font-semibold mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5-5 5M6 7l5 5-5 5" />
                            </svg>
                            Pekerja lembur hari ini
                        </p>
                    </div>
                    <div class="p-3 bg-amber-50 text-amber-600 rounded-lg">
                        <!-- Icon jam dengan simbol plus/ekstra -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 5v4M12 5V1M12 5h4M12 5H8" class="opacity-0" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- 5. SPLIT VIEW --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                        <!-- Header -->
                        <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-white">
                            <div>
                                <h3 class="text-lg font-bold text-slate-800">Kehadiran Terbaru</h3>
                                <p class="text-xs text-slate-400 font-medium">Aktivitas harian unit harian</p>
                            </div>
                            <div
                                class="flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-100 rounded-lg">
                                <span class="relative flex h-2 w-2">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                                <span class="text-xs font-black text-emerald-700 uppercase tracking-wider">
                                    {{ $totalAbsensiHarian }} Total Absensi
                                </span>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100">
                                <thead class="bg-slate-50/50">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Pegawai</th>
                                        <th
                                            class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Unit</th>
                                        <th
                                            class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Jam Aktual / Normal</th>
                                        <th
                                            class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Overtime</th>
                                        <th
                                            class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-slate-50">
                                    @forelse ($kehadiranTerbaru as $hadir)
                                        @php
                                            $pekerja = $hadir->absensi->pekerja;
                                            $unitName = $hadir->absensi->unit->nama_unit ?? 'N/A';
                                        @endphp
                                        <tr class="hover:bg-slate-50/80 transition-colors group">
                                            <!-- Pegawai -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-3">
                                                    <div>
                                                        <p class="text-sm font-bold text-slate-800 group-hover:text-emerald-600 transition-colors
                                            max-w-[180px] truncate"
                                                            title="{{ $pekerja->nama }}">
                                                            {{ $pekerja->nama ?? 'N/A' }}
                                                        </p>
                                                        <p class="text-[10px] text-slate-400 font-bold uppercase">NIK:
                                                            {{ $pekerja->nik ?? '-' }}</p>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Unit -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-md
                                    inline-block max-w-[140px] truncate"
                                                    title="{{ $unitName }}">
                                                    {{ $unitName }}
                                                </span>
                                            </td>

                                            <!-- Jam Kerja -->
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="inline-flex flex-col">
                                                    <span class="text-sm font-black text-slate-700">
                                                        {{ number_format($hadir->jam_kerja_harian, 1) }} /
                                                        {{ number_format($hadir->jam_kerja_normal, 0) }}
                                                    </span>
                                                    <span
                                                        class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Total
                                                        Jam</span>
                                                </div>
                                            </td>

                                            <!-- Overtime -->
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if ($hadir->overtime > 0)
                                                    <span
                                                        class="text-sm font-black text-amber-600">+{{ number_format($hadir->overtime, 1) }}</span>
                                                    <span
                                                        class="text-[10px] font-bold text-amber-400 uppercase block">Jam</span>
                                                @else
                                                    <span class="text-slate-300 text-xs">-</span>
                                                @endif
                                            </td>

                                            <!-- Status -->
                                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                                @switch($hadir->status_kehadiran)
                                                    @case(1)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-wider">Hadir</span>
                                                    @break

                                                    @case(2)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-wider">Izin</span>
                                                    @break

                                                    @case(3)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black bg-indigo-50 text-indigo-600 border border-indigo-100 uppercase tracking-wider">Cuti</span>
                                                    @break

                                                    @case(4)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black bg-rose-50 text-rose-600 border border-rose-100 uppercase tracking-wider">Sakit</span>
                                                    @break

                                                    @default
                                                        <span class="text-xs text-slate-400 italic">Unknown</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-6 py-12 text-center">
                                                    <div class="flex flex-col items-center">
                                                        <svg class="w-10 h-10 text-slate-200 mb-3" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="1.5"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <p class="text-sm text-slate-400 font-medium">Belum ada aktivitas hari
                                                            ini.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                            <!-- Header -->
                            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-white">
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800">Absensi Terbaru (Borongan)</h3>
                                    <p class="text-xs text-slate-400 font-medium">Aktivitas produksi unit borongan</p>
                                </div>
                                <!-- Badge Count di Pojok Kanan Atas -->
                                <div class="flex items-center gap-2 px-3 py-1.5 bg-blue-50 border border-blue-100 rounded-lg">
                                    <span class="relative flex h-2 w-2">
                                        <span
                                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                                    </span>
                                    <span class="text-xs font-black text-blue-700 uppercase tracking-wider">
                                        {{ $totalAbsensiBorongan }} Pekerja Aktif
                                    </span>
                                </div>
                            </div>

                            <!-- Table -->
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-100">
                                    <thead class="bg-slate-50/50">
                                        <tr>
                                            <th
                                                class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                Pegawai</th>
                                            <th
                                                class="px-6 py-4 text-left text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                Unit</th>
                                            <th
                                                class="px-6 py-4 text-center text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                Total Quantity</th>
                                            <th
                                                class="px-6 py-4 text-right text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-50">
                                        @forelse ($boronganTerbaru as $hadir)
                                            <tr class="hover:bg-slate-50/80 transition-colors group">
                                                <!-- Pegawai -->
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center gap-3">
                                                        <div>
                                                            <p class="text-sm font-bold text-slate-800 truncate max-w-[180px]"
                                                                title="{{ $hadir->nama_pekerja }}">
                                                                {{ $hadir->nama_pekerja }}
                                                            </p>
                                                            <p class="text-[10px] text-slate-400 font-bold uppercase">NIK:
                                                                {{ $hadir->nik_pekerja ?? '-' }}</p>
                                                        </div>
                                                    </div>
                                                </td>

                                                <!-- Unit -->
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span
                                                        class="text-xs font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-md inline-block max-w-[140px] truncate"
                                                        title="{{ $hadir->nama_unit }}">
                                                        {{ $hadir->nama_unit }}
                                                    </span>
                                                </td>

                                                <!-- Total Quantity -->
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <div class="inline-flex flex-col">
                                                        <span class="text-sm font-black text-slate-700">
                                                            {{ number_format($hadir->total_sum_qty, 0, ',', '.') }}
                                                        </span>
                                                        <span
                                                            class="text-[10px] font-bold text-blue-500 uppercase tracking-tighter">Total
                                                            PCS Hari Ini</span>
                                                    </div>
                                                </td>

                                                <!-- Status -->
                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                    @if ($hadir->status_kehadiran == 1)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase tracking-wider">Produksi/Masuk</span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black bg-blue-50 text-blue-600 border border-blue-100 uppercase tracking-wider">Izin</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-6 py-12 text-center">
                                                    <div class="flex flex-col items-center">
                                                        <svg class="w-10 h-10 text-slate-200 mb-3" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="1.5"
                                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                        </svg>
                                                        <p class="text-sm text-slate-400 font-medium">Belum ada data produksi
                                                            hari ini.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Penilaian PKWT Table --}}
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                                <h3 class="text-lg font-bold text-gray-900">Penilaian PKWT Terbaru</h3>
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
                                                Skor</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                Grade</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                Status Verif</th>
                                            <th
                                                class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">
                                                Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse ($penilaianTerbaru as $nilai)
                                            @php
                                                $score = $nilai->total;
                                                if ($score >= 50) {
                                                    $grade = 'A';
                                                    $color = 'green';
                                                } elseif ($score >= 41) {
                                                    $grade = 'B';
                                                    $color = 'blue';
                                                } elseif ($score >= 29) {
                                                    $grade = 'C';
                                                    $color = 'yellow';
                                                } else {
                                                    $grade = 'D';
                                                    $color = 'red';
                                                }
                                            @endphp
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-8 w-8">
                                                            <img class="h-8 w-8 rounded-full object-cover"
                                                                src="{{ $nilai->pekerja->foto ? asset('storage/' . $nilai->pekerja->foto) : 'https://ui-avatars.com/api/?name=' . urlencode($nilai->pekerja->nama) . '&background=random' }}">
                                                        </div>
                                                        <div class="ml-3">
                                                            <p class="text-sm font-bold text-gray-900">
                                                                {{ $nilai->pekerja->nama }}</p>
                                                            <p class="text-[10px] text-gray-400 uppercase font-bold">
                                                                {{ $nilai->unit->nama_unit ?? 'Unit N/A' }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <span class="text-sm font-black text-gray-900">{{ $nilai->total }}</span>
                                                    <span class="text-[10px] text-gray-400">/ 56</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <span
                                                        class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-black text-xs bg-{{ $color }}-50 text-{{ $color }}-700 border border-{{ $color }}-100">{{ $grade }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <div class="flex items-center justify-center gap-1.5">
                                                        <div title="Staff Verify"
                                                            class="w-2 h-2 rounded-full {{ $nilai->status_staff ? 'bg-green-500' : 'bg-gray-200' }}">
                                                        </div>
                                                        <div title="HRD Verify"
                                                            class="w-2 h-2 rounded-full {{ $nilai->status_hrd ? 'bg-green-500' : 'bg-gray-200' }}">
                                                        </div>
                                                    </div>
                                                    <p class="text-[8px] text-gray-400 uppercase mt-1 font-bold">
                                                        {{ $nilai->status_hrd ? 'Verified' : 'In Progress' }}</p>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <button @click="openDetail({{ Js::from($nilai) }})"
                                                        class="group inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-600 transition-all active:scale-95 shadow-md shadow-gray-200"><span>View</span></button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5"
                                                    class="px-6 py-10 text-center text-sm text-gray-400 italic">Belum ada
                                                    penilaian yang dicatat.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4"
                                    x-cloak>
                                    <div x-show="showModal" x-transition.opacity @click="showModal = false"
                                        class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>

                                    <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 scale-95 translate-y-8"
                                        class="relative w-full max-w-lg bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-white">

                                        <div class="p-10">
                                            {{-- Header with Large Grade --}}
                                            <div class="flex items-center justify-between mb-10">
                                                <div class="flex items-center gap-4">
                                                    <div :class="'bg-' + getGrade(selected.total).color + '-600'"
                                                        class="w-16 h-16 rounded-[1.5rem] flex items-center justify-center text-white shadow-xl transform -rotate-3">
                                                        <span class="text-3xl font-black"
                                                            x-text="getGrade(selected.total).label"></span>
                                                    </div>
                                                    <div>
                                                        <h4
                                                            class="text-2xl font-black text-gray-900 tracking-tighter leading-none">
                                                            Hasil Evaluasi
                                                        </h4>
                                                        <p class="text-xs font-bold text-gray-400 mt-2 uppercase tracking-[0.2em]"
                                                            x-text="getGrade(selected.total).desc"></p>
                                                    </div>
                                                </div>
                                                <button @click="showModal = false"
                                                    class="p-3 bg-gray-50 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-2xl transition-all">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </button>
                                            </div>

                                            {{-- Metrics Grid (Horizontal Scrolling on mobile, Grid on desktop) --}}
                                            <div class="grid grid-cols-5 gap-2 mb-10">
                                                <template
                                                    x-for="field in [
                                            {key: 'mk', label: 'MK'},
                                            {key: 'absensi', label: 'ABS'},
                                            {key: 'pengetahuan', label: 'PNG'},
                                            {key: 'kualitas', label: 'KLT'},
                                            {key: 'sikap', label: 'SKP'}
                                        ]">
                                                    <div
                                                        class="text-center p-3 bg-gray-50/50 rounded-2xl border border-gray-100">
                                                        <p class="text-[8px] font-black text-gray-400 uppercase mb-1.5"
                                                            x-text="field.label"></p>
                                                        <p class="text-sm font-black text-gray-900"
                                                            x-text="selected[field.key]"></p>
                                                    </div>
                                                </template>
                                            </div>
                                            {{-- Summary & Verification Section --}}
                                            <div class="space-y-6 mb-10">

                                                {{-- 1. Hero Score Card --}}
                                                <div
                                                    class="relative overflow-hidden bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-2xl">
                                                    {{-- Background Pattern Decoration --}}
                                                    <div
                                                        class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16 blur-2xl">
                                                    </div>

                                                    <div class="relative flex items-center justify-between">
                                                        <div>
                                                            <p
                                                                class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-1">
                                                                Skor
                                                                Akumulasi</p>
                                                            <div class="flex items-baseline gap-2">
                                                                <span class="text-6xl font-black tracking-tighter"
                                                                    x-text="selected.total"></span>
                                                                <span class="text-xl font-bold text-gray-500">/ 56</span>
                                                            </div>
                                                        </div>
                                                        <div class="text-right">
                                                            <p
                                                                class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2">
                                                                Predikat
                                                            </p>
                                                            <div :class="'bg-' + getGrade(selected.total).color + '-500'"
                                                                class="px-4 py-2 rounded-2xl inline-block shadow-lg shadow-black/20">
                                                                <span class="text-2xl font-black"
                                                                    x-text="getGrade(selected.total).label"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- 2. Verification Status Row --}}
                                                <div class="grid grid-cols-2 gap-4">
                                                    {{-- Staff Verification --}}
                                                    <div class="p-4 rounded-[1.5rem] border border-gray-100 transition-all"
                                                        :class="selected.status_staff ? 'bg-emerald-50/50 border-emerald-100' :
                                                            'bg-gray-50/50'">
                                                        <div class="flex items-center gap-3">
                                                            <div :class="selected.status_staff ? 'bg-emerald-500 text-white' :
                                                                'bg-gray-200 text-gray-400'"
                                                                class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors">
                                                                <svg x-show="selected.status_staff" class="w-5 h-5"
                                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="3" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                                <svg x-show="!selected.status_staff" class="w-4 h-4"
                                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2.5" d="M12 8v4l3 3" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p
                                                                    class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-none mb-1">
                                                                    Staff Verify</p>
                                                                <p class="text-[11px] font-bold"
                                                                    :class="selected.status_staff ? 'text-emerald-700' :
                                                                        'text-gray-400'"
                                                                    x-text="selected.status_staff ? 'Verified' : 'Pending'">
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- HRD Verification --}}
                                                    <div class="p-4 rounded-[1.5rem] border border-gray-100 transition-all"
                                                        :class="selected.status_hrd ? 'bg-emerald-50/50 border-emerald-100' :
                                                            'bg-gray-50/50'">
                                                        <div class="flex items-center gap-3">
                                                            <div :class="selected.status_hrd ? 'bg-emerald-500 text-white' :
                                                                'bg-gray-200 text-gray-400'"
                                                                class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors">
                                                                <svg x-show="selected.status_hrd" class="w-5 h-5"
                                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="3" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                                <svg x-show="!selected.status_hrd" class="w-4 h-4"
                                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2.5" d="M12 8v4l3 3" />
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <p
                                                                    class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-none mb-1">
                                                                    HRD Verify</p>
                                                                <p class="text-[11px] font-bold"
                                                                    :class="selected.status_hrd ? 'text-emerald-700' :
                                                                        'text-gray-400'"
                                                                    x-text="selected.status_hrd ? 'Verified' : 'Pending'"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- 3. Comments Box --}}
                                                <div class="p-6 bg-blue-50/50 border border-blue-100/50 rounded-[2rem]">
                                                    <p
                                                        class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-3">
                                                        Catatan Penilai
                                                    </p>
                                                    <p class="text-[13px] text-blue-900 font-medium italic leading-relaxed"
                                                        x-text="selected.keterangan || 'Tidak ada catatan khusus untuk periode ini.'">
                                                    </p>
                                                </div>
                                            </div>


                                            <button @click="showModal = false"
                                                class="w-full py-5 bg-white border-2 border-gray-100 text-gray-900 text-xs font-black uppercase tracking-[0.2em] rounded-[1.5rem] hover:bg-gray-50 hover:border-gray-200 transition-all">
                                                Tutup Rincian
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    {{-- RIGHT: SIDEBAR --}}
                    <div class="space-y-6">

                        {{-- Quick Actions --}}
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Pintasan Cepat</h3>
                            <div class="grid grid-cols-2 gap-3">
                                @if(in_array(Auth::user()->role, ['admin', 'hrd', 'head_supervisor', 'pic']))
                                <a href="{{ route('view.tambah.pekerja') }}"
                                    class="flex flex-col items-center justify-center p-4 bg-gray-50 border border-gray-100 rounded-xl hover:bg-blue-50 hover:border-blue-200 hover:text-blue-700 transition group">
                                    <div
                                        class="p-2 bg-white rounded-full shadow-sm group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z">
                                            </path>
                                        </svg>
                                    </div>
                                    <span class="text-xs font-semibold mt-2 text-center">Tambah Pegawai</span>
                                </a>
                                @endif

                                @if(in_array(Auth::user()->role, ['admin', 'hrd', 'akuntan']))
                                <a href="{{ route('view.tambah.staff') }}"
                                    class="flex flex-col items-center justify-center p-4 bg-gray-50 border border-gray-100 rounded-xl hover:bg-violet-50 hover:border-violet-200 hover:text-violet-700 transition group">
                                    <div
                                        class="p-2 bg-white rounded-full shadow-sm group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 11a4 4 0 100-8 4 4 0 000 8z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 15l1 2-1 2-1-2 1-2z" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-semibold mt-2 text-center">Tambah Staff</span>
                                </a>
                                @endif

                                @if(in_array(Auth::user()->role, ['admin', 'hrd', 'head_supervisor']))
                                <a href="{{ route('view.tambah.mitra-kerja') }}"
                                    class="flex flex-col items-center justify-center p-4 bg-gray-50 border border-gray-100 rounded-xl hover:bg-indigo-50 hover:border-indigo-200 hover:text-indigo-700 transition group">
                                    <div
                                        class="p-2 bg-white rounded-full shadow-sm group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v3m0 0v3m0-3h3m-3 0H9"></path>
                                        </svg>
                                    </div>
                                    <span class="text-xs font-semibold mt-2 text-center">Tambah Mitra</span>
                                </a>

                                <a href="{{ route('view.tambah.unit') }}"
                                    class="flex flex-col items-center justify-center p-4 bg-gray-50 border border-gray-100 rounded-xl hover:bg-amber-50 hover:border-amber-200 hover:text-amber-700 transition group">
                                    <div
                                        class="p-2 bg-white rounded-full shadow-sm group-hover:scale-110 transition-transform">
                                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 14v3m-1.5-1.5h3" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-semibold mt-2 text-center">Tambah Unit</span>
                                </a>
                                @endif

                                @if(in_array(Auth::user()->role, ['head_supervisor', 'hrd']))
                                <div class="col-span-2">
                                    <button @click="showApprovalModal = true"
                                        class="w-full relative flex flex-col items-center justify-center p-4 bg-gray-50 border border-gray-100 rounded-xl hover:bg-green-50 hover:border-green-200 hover:text-green-700 transition group">
                                        @if ($penilaianPending->count() > 0)
                                            <span class="absolute top-2 right-2 flex h-5 w-5">
                                                <span
                                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                <span
                                                    class="relative inline-flex rounded-full h-5 w-5 bg-green-600 text-white text-[10px] font-black items-center justify-center">{{ $penilaianPending->count() }}</span>
                                            </span>
                                        @endif
                                        <div
                                            class="p-2 bg-white rounded-full shadow-sm group-hover:scale-110 transition-transform">
                                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <span class="text-xs font-semibold mt-2 text-center">Approval Penilaian</span>
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Important Alerts --}}
                        @php
                            $totalPerhatian =
                                ($totalExpiredKontrak > 0 ? 1 : 0) +
                                ($urgentKontrak ? 1 : 0) +
                                ($totalExpiredMitra > 0 ? 1 : 0) +  
                                ($totalMitraMendekati > 0 ? 1 : 0) +
                                ($absensiPendingCount > 0 ? 1 : 0);
                        @endphp

                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Perlu Perhatian</h3>
                                <span
                                    class="bg-red-100 text-red-800 text-xs font-bold px-2 py-0.5 rounded-full">{{ $totalPerhatian }}</span>
                            </div>

                            <div class="space-y-3">
                                {{-- ALERT 1: PKWT EXPIRED (KRITIS - MERAH TEGAS) --}}
                                @if ($totalExpiredKontrak > 0)
                                    <div
                                        class="flex items-start gap-3 p-3 bg-red-100 rounded-lg border border-red-200 mb-3 shadow-sm">
                                        <div class="flex-shrink-0 mt-0.5 text-red-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between mb-1">
                                                <p class="text-sm font-black text-red-900">Kontrak Pegawai Expired</p>
                                                @if ($totalExpiredKontrak > 1)
                                                    <div x-data="{ open: false }" class="relative" @mouseenter="open = true"
                                                        @mouseleave="open = false">
                                                        <span
                                                            class="cursor-help px-1.5 py-0.5 bg-red-600 text-white text-[10px] font-black rounded-md uppercase tracking-tighter transition-colors hover:bg-red-700">
                                                            +{{ $totalExpiredKontrak - 1 }} Lainnya
                                                        </span>
                                                        <div x-show="open" x-transition.opacity
                                                            class="absolute right-0 mt-2 w-72 bg-white border border-red-100 shadow-xl rounded-2xl z-50 p-3"
                                                            x-cloak>
                                                            <p
                                                                class="text-[9px] font-black text-red-400 uppercase tracking-widest mb-2 px-1 text-left">
                                                                Daftar Expired (Masih Aktif)</p>
                                                            <div class="space-y-1 max-h-48 overflow-y-auto custom-scrollbar">
                                                                @foreach ($othersExpiredKontrak as $other)
                                                                    @php $diff = abs(\Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($other->tgl_akhir_pkwt), false)); @endphp
                                                                    <div
                                                                        class="flex items-center justify-between p-2 rounded-xl hover:bg-red-50 gap-2">
                                                                        <div class="flex flex-col min-w-0 text-left">
                                                                            <span
                                                                                class="text-[11px] font-bold text-gray-700 truncate capitalize">{{ $other->pekerja->nama }}</span>
                                                                            <span
                                                                                class="text-[9px] font-medium text-gray-400 uppercase">{{ $other->unit->nama_unit ?? 'No Unit' }}</span>
                                                                        </div>
                                                                        <span
                                                                            class="shrink-0 text-[10px] font-black text-red-600 bg-red-100 px-2 py-0.5 rounded-lg">
                                                                            {{ $diff > 30 ? '> 30 hari' : $diff . ' hari' }}
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <p class="text-xs text-red-800">
                                                Kontrak <strong>{{ $urgentExpiredKontrak->pekerja->nama }}</strong>
                                                ({{ $urgentExpiredKontrak->unit->nama_unit ?? 'N/A' }})
                                                sudah lewat <strong
                                                    class="decoration-2">{{ $lewatHariKontrak > 30 ? 'lebih dari 30' : $lewatHariKontrak }}
                                                    hari</strong>.
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                {{-- ALERT 2: PKWT AKAN BERAKHIR (EXISTING - DISESUAIKAN) --}}
                                @if ($urgentKontrak)
                                    <div class="flex items-start gap-3 p-3 bg-red-50 rounded-lg border border-red-100 mb-3">
                                        <div class="flex-shrink-0 mt-0.5 text-red-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-bold text-red-900">Kontrak Berakhir</p>
                                                @if ($totalKontrakMendekati > 1)
                                                    <div x-data="{ open: false }" class="relative" @mouseenter="open = true"
                                                        @mouseleave="open = false">
                                                        <span
                                                            class="cursor-help px-1.5 py-0.5 bg-red-600 text-white text-[10px] font-black rounded-md uppercase tracking-tighter transition-colors hover:bg-red-700">
                                                            +{{ $totalKontrakMendekati - 1 }} Lainnya
                                                        </span>

                                                        <div x-show="open" x-transition.opacity
                                                            class="absolute right-0 mt-2 w-72 bg-white border border-red-100 shadow-xl rounded-2xl z-50 p-3"
                                                            x-cloak>
                                                            <p
                                                                class="text-[9px] font-black text-red-400 uppercase tracking-widest mb-2 px-1 text-left">
                                                                Daftar Pegawai & Unit</p>
                                                            <div class="space-y-1 max-h-48 overflow-y-auto custom-scrollbar">
                                                                @foreach ($othersKontrak as $other)
                                                                    @php $diff = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($other->tgl_akhir_pkwt), false); @endphp
                                                                    <div
                                                                        class="flex items-center justify-between p-2 rounded-xl hover:bg-red-50 transition-colors gap-2">
                                                                        <div class="flex flex-col min-w-0 text-left">
                                                                            <span
                                                                                class="text-[11px] font-bold text-gray-700 truncate capitalize">{{ $other->pekerja->nama }}</span>
                                                                            <span
                                                                                class="text-[9px] font-medium text-gray-500 uppercase tracking-tight truncate">
                                                                                {{ $other->unit->nama_unit ?? 'No Unit' }}
                                                                            </span>
                                                                        </div>
                                                                        <span
                                                                            class="shrink-0 text-[10px] font-black text-red-600 bg-red-100/50 px-2 py-0.5 rounded-lg">
                                                                            {{ $diff > 30 ? '> 30 hari' : ($diff <= 0 ? 'Hari Ini' : $diff . ' hari') }}
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <p class="text-xs text-red-700 mt-1 text-left">
                                                Kontrak <strong>{{ $urgentKontrak->pekerja->nama }}</strong> pada
                                                <span
                                                    class="bg-red-100 text-red-800 px-1 rounded text-[10px] font-bold uppercase tracking-wide">
                                                    {{ $urgentKontrak->unit->nama_unit ?? 'Unit Unknown' }}
                                                </span>
                                                berakhir dalam
                                                <strong>{{ $sisaHari > 30 ? '> 30 hari' : ($sisaHari <= 0 ? 'Hari Ini' : $sisaHari . ' hari') }}</strong>.
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                {{-- ALERT 1: EXPIRED (CRITICAL - MERAH) --}}
                                @if ($totalExpiredMitra > 0)
                                    <div class="flex items-start gap-3 p-3 bg-red-50 rounded-lg border border-red-100 mb-3">
                                        <div class="flex-shrink-0 mt-0.5 text-red-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-grow">
                                            <div class="flex items-center justify-between mb-1">
                                                <p class="text-sm font-bold text-red-900">Kontrak Mitra Expired</p>
                                                @if ($totalExpiredMitra > 1)
                                                    <div x-data="{ open: false }" class="relative" @mouseenter="open = true"
                                                        @mouseleave="open = false">
                                                        <span
                                                            class="cursor-help px-1.5 py-0.5 bg-red-600 text-white text-[10px] font-black rounded-md uppercase tracking-tighter transition-colors hover:bg-red-700">
                                                            +{{ $totalExpiredMitra - 1 }} Lainnya
                                                        </span>
                                                        <div x-show="open" x-transition.opacity
                                                            class="absolute right-0 mt-2 w-64 bg-white border border-red-100 shadow-xl rounded-2xl z-50 p-3"
                                                            x-cloak>
                                                            <p
                                                                class="text-[9px] font-black text-red-400 uppercase tracking-widest mb-2 px-1">
                                                                Daftar Expired</p>
                                                            <div class="space-y-1 max-h-48 overflow-y-auto custom-scrollbar">
                                                                @foreach ($othersExpiredMitra as $other)
                                                                    @php $diff = abs(\Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($other->tgl_akhir_mou), false)); @endphp
                                                                    <div
                                                                        class="flex items-center justify-between p-2 rounded-xl hover:bg-red-50">
                                                                        <span
                                                                            class="text-[11px] font-bold text-gray-700 truncate w-32">{{ $other->nama_mitra }}</span>
                                                                        <span
                                                                            class="text-[10px] font-black text-red-600 bg-red-100 px-2 py-0.5 rounded-lg">
                                                                            {{ $diff > 30 ? '> 30 hari' : $diff . ' hari' }}
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <p class="text-xs text-red-700">
                                                MOU <strong>{{ $urgentExpiredMitra->nama_mitra }}</strong> sudah lewat
                                                <strong>{{ $lewatHariMitra > 30 ? 'lebih dari 30' : $lewatHariMitra }}
                                                    hari</strong>, namun status masih Aktif.
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                {{-- ALERT 2: MENDEKATI HABIS (WARNING - ORANYE) --}}
                                @if ($totalMitraMendekati > 0)
                                    <div class="flex items-start gap-3 p-3 bg-orange-50 rounded-lg border border-orange-100">
                                        <div class="flex-shrink-0 mt-0.5 text-orange-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                        <div class="flex-grow">
                                            <div class="flex items-center justify-between mb-1">
                                                <p class="text-sm font-bold text-orange-900">Masa Mitra Kerja</p>
                                                @if ($totalMitraMendekati > 1)
                                                    <div x-data="{ open: false }" class="relative" @mouseenter="open = true"
                                                        @mouseleave="open = false">
                                                        <span
                                                            class="cursor-help px-1.5 py-0.5 bg-red-600 text-white text-[10px] font-black rounded-md uppercase tracking-tighter transition-colors hover:bg-red-700">
                                                            +{{ $totalMitraMendekati - 1 }} Lainnya
                                                        </span>
                                                        <div x-show="open" x-transition.opacity
                                                            class="absolute right-0 mt-2 w-64 bg-white border border-orange-100 shadow-xl rounded-2xl z-50 p-3"
                                                            x-cloak>
                                                            <p
                                                                class="text-[9px] font-black text-orange-400 uppercase tracking-widest mb-2 px-1">
                                                                Daftar Mitra Mendekati</p>
                                                            <div class="space-y-1 max-h-48 overflow-y-auto custom-scrollbar">
                                                                @foreach ($othersMitra as $other)
                                                                    @php $diff = \Carbon\Carbon::today()->diffInDays(\Carbon\Carbon::parse($other->tgl_akhir_mou), false); @endphp
                                                                    <div
                                                                        class="flex items-center justify-between p-2 rounded-xl hover:bg-orange-50">
                                                                        <span
                                                                            class="text-[11px] font-bold text-gray-700 truncate w-32 text-left">{{ $other->nama_mitra }}</span>
                                                                        <span
                                                                            class="text-[10px] font-black text-orange-600 bg-orange-100/50 px-2 py-0.5 rounded-lg">
                                                                            {{ $diff > 30 ? '> 30 hari' : ($diff <= 0 ? 'Hari Ini' : $diff . ' hari') }}
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <p class="text-xs text-orange-700">
                                                Kontrak <strong>{{ $urgentMitra->nama_mitra }}</strong> berakhir dalam
                                                <strong>{{ $sisaHariMitra > 30 ? 'lebih dari 30' : ($sisaHariMitra <= 0 ? 'Hari Ini' : $sisaHariMitra) }}
                                                    hari</strong>.
                                            </p>
                                        </div>
                                    </div>
                                @endif

                                @if ($absensiPendingCount > 0)
                                    <div class="flex items-start gap-3 p-3 bg-yellow-50 rounded-lg border border-yellow-100">
                                        <div class="flex-shrink-0 mt-0.5 text-yellow-600"><svg class="w-5 h-5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg></div>
                                        <div>
                                            <p class="text-sm font-bold text-yellow-900">Menunggu Approval</p>
                                            <p class="text-xs text-yellow-700 mt-0.5">Ada <strong>{{ $absensiPendingCount }}
                                                    data absensi</strong> menunggu verifikasi.</p>
                                        </div>
                                    </div>
                                @endif

                                @if ($totalPerhatian == 0)
                                    <div class="flex flex-col items-center justify-center py-6">
                                        <div class="p-3 bg-green-50 rounded-full mb-3"><svg class="w-6 h-6 text-green-500"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg></div>
                                        <p class="text-xs text-gray-400 font-medium italic text-center px-4">Semua sistem
                                            berjalan normal. Tidak ada peringatan saat ini.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div> {{-- End Sidebar --}}
                </div>
                {{-- End Split View --}}

            </div> {{-- End Container --}}

            {{-- MODALS --}}

            {{-- MODAL DETAIL EVALUASI --}}
            <div x-show="showModal" class="fixed inset-0 z-[200] flex items-center justify-center p-4" x-cloak>
                <div x-show="showModal" x-transition.opacity @click="showModal = false"
                    class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
                <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-8"
                    class="relative w-full max-w-lg bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-white">
                    <div class="p-10">
                        <div class="flex items-center justify-between mb-10">
                            <div class="flex items-center gap-4">   
                                <div :class="'bg-' + getGrade(selected.total).color + '-600'"
                                    class="w-16 h-16 rounded-[1.5rem] flex items-center justify-center text-white shadow-xl transform -rotate-3">
                                    <span class="text-3xl font-black" x-text="getGrade(selected.total).label"></span>
                                </div>
                                <div>
                                    <h4 class="text-2xl font-black text-gray-900 tracking-tighter leading-none">Hasil Evaluasi
                                    </h4>
                                    <p class="text-xs font-bold text-gray-400 mt-2 uppercase tracking-[0.2em]"
                                        x-text="getGrade(selected.total).desc"></p>
                                </div>
                            </div>
                            <button @click="showModal = false"
                                class="p-3 bg-gray-50 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-2xl transition-all">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>

                        <div class="grid grid-cols-4 gap-2 mb-10">
                            <template
                                x-for="field in [{key: 'absensi', label: 'ABS'}, {key: 'pengetahuan', label: 'PNG'}, {key: 'kualitas', label: 'KLT'}, {key: 'sikap', label: 'SKP'}]">
                                <div class="text-center p-3 bg-gray-50/50 rounded-2xl border border-gray-100">
                                    <p class="text-[8px] font-black text-gray-400 uppercase mb-1.5" x-text="field.label"></p>
                                    <p class="text-sm font-black text-gray-900" x-text="selected[field.key]"></p>
                                </div>
                            </template>
                        </div>

                        <div class="space-y-6 mb-10">
                            <div class="relative overflow-hidden bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-2xl">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16 blur-2xl">
                                </div>
                                <div class="relative flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-1">Skor
                                            Akumulasi</p>
                                        <div class="flex items-baseline gap-2">
                                            <span class="text-6xl font-black tracking-tighter" x-text="selected.total"></span>
                                            <span class="text-xl font-bold text-gray-500">/ 56</span>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2">
                                            Predikat</p>
                                        <div :class="'bg-' + getGrade(selected.total).color + '-500'"
                                            class="px-4 py-2 rounded-2xl inline-block shadow-lg shadow-black/20">
                                            <span class="text-2xl font-black" x-text="getGrade(selected.total).label"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="p-4 rounded-[1.5rem] border border-gray-100 transition-all"
                                    :class="selected.status_staff ? 'bg-emerald-50/50 border-emerald-100' : 'bg-gray-50/50'">
                                    <div class="flex items-center gap-3">
                                        <div :class="selected.status_staff ? 'bg-emerald-500 text-white' :
                                            'bg-gray-200 text-gray-400'"
                                            class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors">
                                            <svg x-show="selected.status_staff" class="w-5 h-5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            <svg x-show="!selected.status_staff" class="w-4 h-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M12 8v4l3 3" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p
                                                class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-none mb-1">
                                                Staff Verify</p>
                                            <p class="text-[11px] font-bold"
                                                :class="selected.status_staff ? 'text-emerald-700' : 'text-gray-400'"
                                                x-text="selected.status_staff ? 'Verified' : 'Pending'"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-4 rounded-[1.5rem] border border-gray-100 transition-all"
                                    :class="selected.status_hrd ? 'bg-emerald-50/50 border-emerald-100' : 'bg-gray-50/50'">
                                    <div class="flex items-center gap-3">
                                        <div :class="selected.status_hrd ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-400'"
                                            class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors">
                                            <svg x-show="selected.status_hrd" class="w-5 h-5" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            <svg x-show="!selected.status_hrd" class="w-4 h-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M12 8v4l3 3" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p
                                                class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-none mb-1">
                                                HRD Verify</p>
                                            <p class="text-[11px] font-bold"
                                                :class="selected.status_hrd ? 'text-emerald-700' : 'text-gray-400'"
                                                x-text="selected.status_hrd ? 'Verified' : 'Pending'"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 bg-blue-50/50 border border-blue-100/50 rounded-[2rem]">
                                <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-3">Catatan Penilai
                                </p>
                                <p class="text-[13px] text-blue-900 font-medium italic leading-relaxed"
                                    x-text="selected.keterangan || 'Tidak ada catatan khusus untuk periode ini.'"></p>
                            </div>
                        </div>

                        <button @click="showModal = false"
                            class="w-full py-5 bg-white border-2 border-gray-100 text-gray-900 text-xs font-black uppercase tracking-[0.2em] rounded-[1.5rem] hover:bg-gray-50 hover:border-gray-200 transition-all">Tutup
                            Rincian</button>
                    </div>
                </div>
            </div>

            {{-- MODAL APPROVAL PENILAIAN --}}
            <div x-show="showApprovalModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-cloak>
                <div x-show="showApprovalModal" x-transition.opacity @click="showApprovalModal = false"
                    class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
                <div x-show="showApprovalModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-8"
                    class="relative bg-white rounded-[2.5rem] shadow-2xl w-full max-w-2xl overflow-hidden border border-white flex flex-col max-h-[85vh]">
                    <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                        <div>
                            <h3 class="text-xl font-black text-gray-900 tracking-tight">Verifikasi Penilaian</h3>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Total:
                                {{ $penilaianPending->count() }} Perlu Disetujui</p>
                        </div>
                        <button @click="showApprovalModal = false"
                            class="p-2 text-gray-400 hover:text-red-500 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="p-6 overflow-y-auto custom-scrollbar flex-1 bg-white">
                        <div class="space-y-4">
                            @forelse($penilaianPending as $pending)
                                <div
                                    class="group p-4 bg-gray-50 border border-gray-100 rounded-3xl flex items-center justify-between hover:bg-white hover:border-green-200 hover:shadow-xl hover:shadow-green-900/5 transition-all duration-300">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 rounded-2xl bg-white shadow-sm flex items-center justify-center border border-gray-100 group-hover:border-green-500 transition-colors">
                                            <span class="text-xl font-black text-gray-900">{{ $pending->total }}</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-gray-900 leading-tight">
                                                {{ $pending->pekerja->nama }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <span
                                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $pending->unit->nama_unit ?? 'Unit N/A' }}</span>
                                                <span class="text-gray-300">•</span>
                                                <span
                                                    class="text-[10px] font-black text-blue-600 uppercase">{{ \Carbon\Carbon::parse($pending->created_at)->translatedFormat('d M Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <button @click="openDetail({{ json_encode($pending) }})"
                                            class="px-5 py-2.5 bg-gray-50 text-gray-600 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-gray-900 hover:text-white transition-all active:scale-95">View</button>
                                        <form action="{{ route('penilaian.verify.hrd', $pending->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="px-4 py-2 bg-green-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-green-200 hover:bg-green-700 transition-all active:scale-95">Verify</button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="py-12 text-center">
                                    <p class="text-sm font-bold text-gray-400 italic">Semua penilaian sudah diverifikasi.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    <div class="px-8 py-5 border-t border-gray-100 bg-gray-50/50 flex justify-end">
                        <button @click="showApprovalModal = false"
                            class="px-6 py-2.5 text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-900 transition-colors">Tutup</button>
                    </div>
                </div>
            </div>

        </div>
    @endsection

    @section('scripts')
        <script src="/js/dashboard.js"></script>
    @endsection
