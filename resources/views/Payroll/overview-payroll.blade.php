@extends('layout')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8 space-y-6 animate-in fade-in duration-500">

        {{-- 1. HEADER & INTEGRATED SUMMARY SECTION --}}
        <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm">
            {{-- Top Part: Header with Back Button --}}
            <div class="px-8 py-7 flex flex-col lg:flex-row justify-between items-center gap-8 border-b border-slate-100">
                <div class="flex items-center gap-5">
                    {{-- Back Button --}}
                    <a href="{{ url()->previous() }}"
                        class="w-10 h-10 flex items-center justify-center bg-white border border-slate-200 rounded-xl text-slate-400 hover:text-emerald-600 hover:border-emerald-100 transition-all shadow-sm group">
                        <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                        </svg>
                    </a>

                    <div
                        class="w-12 h-12 bg-emerald-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-200 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-slate-800 tracking-tight leading-none">Review Penggajian<span
                                class="text-emerald-500">.</span></h1>
                        <p class="text-xs font-bold text-slate-400 mt-1.5 uppercase tracking-widest">
                            Unit: <span class="text-slate-700"> {{ $payrollData['unit_name'] ?? 'Unit 0' }}</span>
                            <span class="mx-2 text-slate-200">|</span>
                            Periode: <span class="text-slate-700">{{ $payrollData['periode'] }}</span>
                        </p>
                    </div>
                </div>
                {{-- Export Action Cards --}}
                <div class="flex gap-3">
                    <div class="flex gap-4" x-data="resiModal()">
                        {{-- Tanda Terima --}}
                        @php
                            // Siapkan array dasar
                            $queryParameters = [
                                'id_unit' => $payrollData['unit_id'],
                                'tgl_awal' => $payrollData['tanggal_mulai'],
                                'tgl_akhir' => $payrollData['tanggal_akhir'],
                                'grand_total' => $payrollData['grand_total'],
                                'workers' => [], // Inisialisasi array workers
                            ];

                            // Isi array workers secara berpasangan
                            foreach ($payrollData['items'] as $index => $item) {
                                $queryParameters['workers'][$index] = [
                                    'id' => $item['id_pekerja'],
                                    'upah' => $item['net_salary'],
                                ];
                            }
                        @endphp
                        <a href="{{ route('export.tanda-terima.borongan', $queryParameters) }}" target="_blank"
                            class="flex flex-col items-center justify-center w-20 h-20 bg-slate-50 border border-slate-200 rounded-2xl hover:bg-white hover:border-emerald-500 group transition-all shadow-sm">

                            <svg class="w-5 h-5 text-slate-400 group-hover:text-emerald-600 transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-[9px] font-black uppercase tracking-tighter text-slate-500 mt-1">Tanda
                                Terima</span>
                        </a>

                        {{-- Invoice --}}
                        @php
                            // Siapkan data dasar
                            $payloadInvoice = [
                                'id_unit' => $payrollData['unit_id'],
                                'grand_total' => $payrollData['grand_total'],
                                'tanggal_mulai' => $payrollData['tanggal_mulai'],
                                'tanggal_akhir' => $payrollData['tanggal_akhir'],
                            ];

                            // Loop data pekerja untuk membuat key: workers[0][id], workers[0][upah], dst.
                            foreach ($payrollData['items'] as $index => $item) {
                                $payloadInvoice["workers[{$index}][id]"] = $item['id_pekerja'];
                                $payloadInvoice["workers[{$index}][upah]"] = $item['net_salary'];
                            }
                        @endphp
                        <button
                            @click="open('Invoice', '{{ route('export.invoice.borongan') }}', {{ json_encode($payloadInvoice) }})"
                            class="flex flex-col items-center justify-center w-20 h-20 bg-slate-50 border border-slate-200 rounded-2xl hover:bg-white hover:border-emerald-500 group transition-all shadow-sm">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-emerald-600 transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span
                                class="text-[9px] font-black uppercase tracking-tighter text-slate-500 mt-1">Invoice</span>
                        </button>

                        {{-- Kwitansi --}}
                        @php
                            // Siapkan data dasar
                            $payloadKwitansi = [
                                'id_unit' => $payrollData['unit_id'],
                                'grand_total' => $payrollData['grand_total'],
                                'tanggal_mulai' => $payrollData['tanggal_mulai'],
                                'tanggal_akhir' => $payrollData['tanggal_akhir'],
                            ];

                            // Loop data pekerja untuk membuat key: workers[0][id], workers[0][upah], dst.
                            foreach ($payrollData['items'] as $index => $item) {
                                $payloadKwitansi["workers[{$index}][id]"] = $item['id_pekerja'];
                                $payloadKwitansi["workers[{$index}][upah]"] = $item['net_salary'];
                            }
                        @endphp
                        <button
                            @click="open('Kwitansi', '{{ route('export.kwitansi.borongan') }}', {{ json_encode($payloadKwitansi) }})"
                            class="flex flex-col items-center justify-center w-20 h-20 bg-slate-50 border border-slate-200 rounded-2xl hover:bg-white hover:border-emerald-500 group transition-all shadow-sm">
                            <svg class="w-5 h-5 text-slate-400 group-hover:text-emerald-600 transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            <span
                                class="text-[9px] font-black uppercase tracking-tighter text-slate-500 mt-1">Kwitansi</span>
                        </button>

                        {{-- MODAL STRUCTURE --}}
                        <div x-show="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak>
                            {{-- Overlay --}}
                            <div x-show="show" x-transition.opacity @click="show = false"
                                class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

                            {{-- Modal Content --}}
                            <div x-show="show" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                class="relative w-full max-w-sm bg-white rounded-[2rem] shadow-2xl overflow-hidden border border-slate-100">

                                <form :action="actionUrl" method="get" enctype="multipart/form-data" target="_blank"
                                    class="p-8">
                                    @csrf
                                    @method('get')

                                    <template x-for="(value, key) in extraData" :key="key">
                                        <div>
                                            <template x-if="Array.isArray(value)">
                                                <template x-for="item in value">
                                                    <input type="hidden" :name="key" :value="item">
                                                </template>
                                            </template>
                                            <template x-if="!Array.isArray(value)">
                                                <input type="hidden" :name="key" :value="value">
                                            </template>
                                        </div>
                                    </template>

                                    <div class="text-center mb-6">
                                        <div
                                            class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 mb-4">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-black text-slate-800 tracking-tight"
                                            x-text="'Generate ' + title"></h3>
                                        <p class="text-xs font-bold text-slate-400 mt-1">Masukkan Nomor Resi/Referensi untuk
                                            dokumen ini.</p>
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <label
                                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Nomor
                                                Resi / No. Ref</label>
                                            <input type="text" name="no_resi" required
                                                placeholder="Silahkan masukkan No Resi Disini.."
                                                class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-emerald-500/10 focus:bg-white transition-all duration-200">

                                            <p class="mt-2 text-[10px] text-slate-400 italic font-medium ml-1">
                                                * Contoh: 021 RD / MJA - BISI / INVOICE / XI / 2025
                                            </p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3 mt-8">
                                        <button type="button" @click="show = false"
                                            class="px-5 py-3.5 text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition">
                                            Batal
                                        </button>
                                        <button type="submit"
                                            class="px-5 py-3.5 bg-emerald-600 text-white text-xs font-black uppercase tracking-widest rounded-2xl shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition active:scale-95">
                                            Generate
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Cards Grid --}}
            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 divide-y sm:divide-y-0 sm:divide-x divide-slate-100 bg-slate-50/30">
                <div class="p-6">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span> Total Pekerja
                    </p>
                    <p class="text-xl font-black text-slate-800">{{ $payrollData['total_pekerja'] }} <span
                            class="text-sm font-bold text-slate-400 uppercase">Orang</span></p>
                </div>
                <div class="p-6">
                    <p class="text-xs font-black text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span> Potongan Hari
                    </p>
                    <p class="text-xl font-black text-slate-800">{{ $payrollData['total_potongan_hari'] }} <span
                            class="text-sm font-bold text-slate-400 uppercase">Hari</span></p>
                </div>
                <div class="p-6">
                    <p class="text-xs font-black text-yellow-600 uppercase tracking-widest mb-2 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                        Penyesuaian ({{ $payrollData['total_pekerja'] }} Pekerja)
                    </p>
                    <p class="text-xl font-black text-yellow-600">Rp
                        {{ number_format($payrollData['total_penyesuaian'], 0, ',', '.') }}</p>
                </div>
                <div class="p-6 bg-emerald-50/20 lg:bg-transparent">
                    <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-2 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-slate-900"></span> Total Payroll
                    </p>
                    <p class="text-xl font-black text-slate-900">Rp
                        {{ number_format($payrollData['grand_total'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- 2. MAIN TABLE SECTION --}}
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden flex flex-col">
            {{-- Table Title Bar --}}
            <div class="px-8 py-5 border-b border-slate-100 flex items-center gap-3 bg-white">
                <div class="w-8 h-8 bg-slate-50 rounded-lg flex items-center justify-center text-emerald-600 shadow-inner">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h2 class="text-sm font-black text-slate-800 uppercase tracking-widest">Rincian Perhitungan Payroll Pekerja
                </h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        @switch($payrollData['sistem_pengajian'])
                            @case(1)
                                <tr
                                    class="text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 bg-slate-50/20">
                                    <th class="px-4 py-4 text-center">No.</th>
                                    <th class="px-8 py-4 text-left">Nama Pekerja</th>
                                    <th class="px-4 py-4 text-center">Total Jam kerja</th>
                                    <th class="px-4 py-4 text-center">Total Overtime</th>
                                    <th class="px-4 py-4 text-center">Total HBN</th>
                                    <th class="px-6 py-4 text-center">Hasil Gaji</th>
                                    <th class="px-6 py-4 text-right">Penyesuaian (Pot/Tunj)</th>
                                    <th class="px-8 py-4 text-right">Detail</th>
                                </tr>
                            @break

                            @case(2)
                                <tr
                                    class="text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 bg-slate-50/20">
                                    <th class="px-4 py-4 text-center">No.</th>
                                    <th class="px-8 py-5 text-left">Nama Pekerja</th>
                                    <th class="px-6 py-5">Total Barang</th>
                                    <th class="px-6 py-5 text-center">Hasil Gaji</th>
                                    <th class="px-6 py-5 text-right">Potongan</th>
                                    <th class="px-6 py-5 text-right">Tunjangan</th>
                                    <th class="px-8 py-5 text-right">Detail</th>
                                </tr>
                            @break
                        @endswitch
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm">

                        @foreach ($payrollData['items'] as $item)
                            @php
                                $netSalary = max(0, $item['net_salary']);
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                @switch($payrollData['sistem_pengajian'])
                                    @case(1)
                                        <!-- No. Column -->
                                        <td class="px-4 py-4 text-center">
                                            <span class="text-xs font-bold text-slate-400">{{ $loop->iteration }}.</span>
                                        </td>

                                        <!-- Nama -->
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <div>
                                                    <p class="text-sm font-bold text-slate-800">{{ $item['nama'] }}</p>
                                                    <p class="text-[11px] font-bold text-slate-400 mt-0.5 uppercase">NIK:
                                                        {{ $item['nik'] }}</p>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Total Jam Kerja -->
                                        <td class="px-4 py-4 text-center border-x border-slate-50/50">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-black text-slate-700">{{ $item['total_jam_kerja'] }}</span>
                                                <span class="text-[11px] font-bold text-blue-500 uppercase">Jam</span>
                                            </div>
                                        </td>

                                        <!-- Total Overtime -->
                                        <td class="px-4 py-4 text-center">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-black text-amber-600">{{ $item['total_overtime'] }}</span>
                                                <span class="text-[11px] font-bold text-amber-500/80 uppercase">Jam OT</span>
                                            </div>
                                        </td>

                                        <!-- Total HBN -->
                                        <td class="px-4 py-4 text-center border-x border-slate-50/50">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-black text-indigo-600">{{ $item['total_hbn'] }}</span>
                                                <span class="text-[11px] font-bold text-indigo-500/80 uppercase">HBN</span>
                                            </div>
                                        </td>

                                        <!-- Hasil Gaji -->
                                        <td class="px-6 py-4 text-center">
                                            <p class="text-sm font-black text-slate-800">
                                                Rp {{ number_format(max(0, $item['net_salary']), 0, ',', '.') }}
                                            </p>
                                        </td>

                                        <!-- Potongan / Tunjangan -->
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col items-end gap-1.5">
                                                <!-- Tunjangan -->
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-bold text-slate-700">
                                                        {{ number_format($item['tunjangan'], 0, ',', '.') }}
                                                    </span>
                                                    <div
                                                        class="w-5 h-5 rounded-md bg-emerald-100 flex items-center justify-center">
                                                        <svg class="w-3 h-3 text-emerald-600" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                                d="M12 4v16m8-8H4" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <!-- Potongan -->
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-bold text-rose-500">
                                                        {{ number_format($item['pembayaran_lain'], 0, ',', '.') }}
                                                    </span>
                                                    <div class="w-5 h-5 rounded-md bg-rose-100 flex items-center justify-center">
                                                        <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                                d="M20 12H4" />
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-8 py-5 text-right">
                                            <a href="{{ route('export.detail.harian', [
                                                'id_unit' => $payrollData['unit_id'],
                                                'id_pekerja' => $item['id_pekerja'],
                                                'tgl_awal' => $payrollData['tanggal_mulai'],
                                                'tgl_akhir' => $payrollData['tanggal_akhir'],
                                                'potongan' => $item['pembayaran_lain'],
                                                'tunjangan' => $item['tunjangan'],
                                                'exclusion_date' => $item['potongan_dates'],
                                            ]) }}"
                                                target="_blank" {{-- buka di tab baru untuk slip gaji --}}
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-[10px] uppercase tracking-widest hover:border-emerald-500 hover:text-emerald-600 transition-all shadow-sm active:scale-95 group">

                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-500 transition-colors"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span>Report</span>
                                            </a>
                                        </td>
                                    @break

                                    @case(2)
                                        <!-- No. Column -->
                                        <td class="px-4 py-4 text-center">
                                            <span class="text-xs font-bold text-slate-400">{{ $loop->iteration }}.</span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <div>
                                                    <p class="text-sm font-bold text-slate-800">{{ $item['nama'] }}</p>
                                                    <p class="text-[11px] font-bold text-slate-400 mt-0.5 uppercase">NIK:
                                                        {{ $item['nik'] }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-6 ">
                                            <p class="text-sm font-black text-slate-700">
                                                {{ number_format($item['total_barang'], 0, ',', '.') }}
                                                <span class="text-[10px] text-slate-400 uppercase tracking-widest ml-1">Pcs</span>
                                            </p>
                                        </td>
                                        <td class="px-6 py-6 text-center">
                                            @if ($item['total_barang'] === 0)
                                                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">
                                                    Tidak Ada Produksi
                                                </span>
                                            @else
                                                <p class="text-xs font-bold text-slate-400 uppercase mb-1">Hasil Produksi:</p>
                                                <p class="text-sm font-black text-slate-800">
                                                    Rp {{ number_format(max(0, $item['net_salary']), 0, ',', '.') }}
                                                </p>
                                            @endif

                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <div class="flex flex-col items-end">
                                                <p class="text-base font-black text-orange-600 tracking-tight">
                                                    Rp {{ number_format($item['pembayaran_lain'], 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <div class="flex flex-col items-end">
                                                <p class="text-base font-black text-green-600 tracking-tight">
                                                    Rp {{ number_format($item['tunjangan'], 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <a href="{{ route('export.detail.borongan', [
                                                'id_unit' => $payrollData['unit_id'],
                                                'id_pekerja' => $item['id_pekerja'],
                                                'tgl_awal' => $payrollData['tanggal_mulai'],
                                                'tgl_akhir' => $payrollData['tanggal_akhir'],
                                                'potongan' => $item['pembayaran_lain'],
                                                'tunjangan' => $item['tunjangan'],
                                                'exclusion_date' => $item['potongan_dates'],
                                            ]) }}"
                                                target="_blank" {{-- buka di tab baru untuk slip gaji --}}
                                                class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-[10px] uppercase tracking-widest hover:border-emerald-500 hover:text-emerald-600 transition-all shadow-sm active:scale-95 group">

                                                <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-500 transition-colors"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <span>Report</span>
                                            </a>
                                        </td>
                                    @break
                                @endswitch
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- INTEGRATED COMPACT FOOTER --}}
            <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-100 flex justify-between items-center">
                <a href="{{ url()->previous() }}"
                    class="flex items-center gap-2 text-xs font-black text-slate-400 hover:text-slate-700 uppercase tracking-widest transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali Edit
                </a>

                <div class="flex gap-3">
                    <a href="#"
                        class="px-8 py-3.5 bg-white border border-slate-200 text-slate-600 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-slate-50 transition-all shadow-sm">
                        Save Draft
                    </a>
                    @php
                        // Siapkan array dasar
                        $queryParameters = [
                            'id_unit' => $payrollData['unit_id'],
                            'tgl_awal' => $payrollData['tanggal_mulai'],
                            'tgl_akhir' => $payrollData['tanggal_akhir'],
                            'grand_total' => $payrollData['grand_total'],
                            'exclusion_date' => $item['potongan_dates'],
                            'workers' => [], // Inisialisasi array workers
                        ];

                        if($payrollData['sistem_pengajian'] == 1)
                        {
                            foreach ($payrollData['items'] as $index => $item) {
                                $queryParameters['workers'][$index] = [
                                    'id' => $item['id_pekerja'],
                                    'jam_kerja' => $item['total_jam_kerja'],
                                    'overtime' => $item['total_overtime'],
                                    'hbn' => $item['total_hbn'],
                                    'upah' => $item['net_salary'],
                                    'exclusion_date' => $item['potongan_dates'] ?? [],
                                    'potongan' => $item['pembayaran_lain'],
                                    'tunjangan' => $item['tunjangan']
                                ];
                            }
                        }

                        if($payrollData['sistem_pengajian'] == 2)
                        {
                             // Isi array workers secara berpasangan
                            foreach ($payrollData['items'] as $index => $item) {
                                $queryParameters['workers'][$index] = [
                                    'id' => $item['id_pekerja'],
                                    'upah' => $item['net_salary'],
                                    'exclusion_date' => $item['potongan_dates'] ?? [],
                                    'potongan' => $item['pembayaran_lain'],
                                    'tunjangan' => $item['tunjangan']
                                ];
                            }
                        }

                        $jsonWorkers = json_encode($queryParameters['workers']);
                    @endphp

                    {{-- <form action="{{ route('export.rincian.upah.borongan') }}" method="POST" target="_blank"
                        style="display:inline;">
                        @csrf
                        <input type="hidden" name="id_unit" value="{{ $queryParameters['id_unit'] }}">
                        <input type="hidden" name="tgl_awal" value="{{ $queryParameters['tgl_awal'] }}">
                        <input type="hidden" name="tgl_akhir" value="{{ $queryParameters['tgl_akhir'] }}">
                        <input type="hidden" name="grand_total" value="{{ $queryParameters['grand_total'] }}">

                        <input type="hidden" name="workers_json" value="{{ $jsonWorkers }}">

                        <button type="submit"
                            class="text-blue-600 hover:text-blue-800 underline bg-transparent border-0 p-0 cursor-pointer">
                            Export Excel
                        </button>
                    </form> --}}
                    @switch($payrollData['sistem_pengajian'])
                        @case(1)

                            <a href="{{ route('export.rincian.upah.harian', $queryParameters) }}" target="_blank"
                                class="group flex items-center gap-3 bg-emerald-600 hover:bg-emerald-500 text-white px-8 py-3.5 rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-emerald-500/20 transition-all active:scale-95">
                                Generate Rincian Upah
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M13 7l5 5-5 5M6 7l5 5-5 5" />
                                </svg>
                            </a>

                        @break

                        @case(2)
                            <a href="{{ route('export.rincian.upah.borongan', $queryParameters) }}" target="_blank"
                                class="group flex items-center gap-3 bg-emerald-600 hover:bg-emerald-500 text-white px-8 py-3.5 rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-emerald-500/20 transition-all active:scale-95">
                                Generate Rincian Upah
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M13 7l5 5-5 5M6 7l5 5-5 5" />
                                </svg>
                            </a>
                        @break
                    @endswitch


                </div>
            </div>
        </div>
    </div>

    <style>
        body {
            background-color: #fcfcfc;
        }

        .animate-in {
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection

@section('scripts')
    <script>
        function resiModal() {
            return {
                show: false,
                title: '',
                actionUrl: '',
                extraData: {}, // Objek penampung payload

                /**
                 * @param title - Judul modal
                 * @param url - URL Action Form
                 * @param payload - Objek berisi { key: value } untuk input hidden
                 */
                open(title, url, payload = {}) {
                    this.title = title;
                    this.actionUrl = url;
                    this.extraData = payload; // Masukkan semua data yang ingin dipass ke sini
                    this.show = true;

                    setTimeout(() => {
                        const input = document.querySelector('input[name="no_resi"]');
                        if (input) input.focus();
                    }, 100);
                }
            }
        }
    </script>
@endsection
