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

                                    <div class="space-y-5">
                                        <!-- Group 1: Nomor Resi -->
                                        <div>
                                            <label for="no_resi"
                                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">
                                                Nomor Resi / No. Ref
                                            </label>
                                            <input type="text" id="no_resi" name="no_resi" required
                                                placeholder="Silahkan masukkan No Resi Disini.."
                                                class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-emerald-500/10 focus:bg-white transition-all duration-200">
                                            <p class="mt-2 text-[10px] text-slate-400 italic font-medium ml-1">
                                                * Contoh: 021 RD / MJA - BISI / INVOICE / XI / 2025
                                            </p>
                                        </div>

                                        <!-- Group 2: Nama Penanggungjawab -->
                                        <div>
                                            <label for="nama_resi"
                                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">
                                                Penanggungjawab
                                            </label>
                                            <input type="text" id="nama_resi" name="nama_resi" required
                                                placeholder="Silahkan masukkan nama disini.."
                                                class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-emerald-500/10 focus:bg-white transition-all duration-200">
                                        </div>

                                        <!-- Group 3: Jabatan -->
                                        <div>
                                            <label for="jabatan"
                                                class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">
                                                Jabatan
                                            </label>
                                            <input type="text" id="jabatan" name="jabatan"
                                                placeholder="Silahkan masukkan jabatan disini.."
                                                class="w-full px-5 py-3.5 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-700 focus:ring-4 focus:ring-emerald-500/10 focus:bg-white transition-all duration-200">
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
                                                <span
                                                    class="text-sm font-black text-slate-700">{{ $item['total_jam_kerja'] }}</span>
                                                <span class="text-[11px] font-bold text-blue-500 uppercase">Jam</span>
                                            </div>
                                        </td>

                                        <!-- Total Overtime -->
                                        <td class="px-4 py-4 text-center">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-sm font-black text-amber-600">{{ $item['total_overtime'] }}</span>
                                                <span class="text-[11px] font-bold text-amber-500/80 uppercase">Jam OT</span>
                                            </div>
                                        </td>

                                        <!-- Total HBN -->
                                        <td class="px-4 py-4 text-center border-x border-slate-50/50">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-black text-indigo-600">{{ $item['total_hbn'] }}</span>
                                                <span class="text-[11px] font-bold text-indigo-500/80 uppercase">Jam HBN</span>
                                            </div>
                                        </td>

                                        <!-- Hasil Gaji -->
                                        <td class="px-6 py-4 text-center">
                                            <p class="text-sm font-black text-slate-800">
                                                Rp.{{ number_format(max(0, $item['net_salary']), 0, ',', '.') }}
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

                @php
                    // 1. Siapkan data workers (Sama seperti sebelumnya)
                    $workers = [];
                    foreach ($payrollData['items'] as $item) {
                        $workerData = [
                            'id' => $item['id_pekerja'],
                            'upah' => $item['net_salary'],
                            'exclusion_date' => $item['potongan_dates'] ?? [],
                            'potongan' => $item['pembayaran_lain'],
                            'tunjangan' => $item['tunjangan'],
                        ];

                        if ($payrollData['sistem_pengajian'] == 1) {
                            $workerData['jam_kerja'] = $item['total_jam_kerja'];
                            $workerData['overtime'] = $item['total_overtime'];
                            $workerData['overtime_salary'] = $item['overtime_salary'];
                            $workerData['hbn'] = $item['total_hbn'];
                            $workerData['hbn_salary'] = $item['hbn_salary'];
                        }
                        $workers[] = $workerData;
                    }
                    $jsonWorkers = json_encode($workers);
                @endphp

                <div class="flex gap-3" x-data="reportModal()">
                    {{-- Form Utama dengan target _blank --}}
                    <form method="POST" target="_blank" class="flex gap-3">
                        @csrf
                        {{-- Data Hidden yang dibutuhkan oleh semua report --}}
                        <input type="hidden" name="id_unit" value="{{ $payrollData['unit_id'] }}">
                        <input type="hidden" name="tgl_awal" value="{{ $payrollData['tanggal_mulai'] }}">
                        <input type="hidden" name="tgl_akhir" value="{{ $payrollData['tanggal_akhir'] }}">
                        <input type="hidden" name="grand_total" value="{{ $payrollData['grand_total'] }}">
                        <input type="hidden" name="workers_json" value="{{ $jsonWorkers }}">
                        <input type="hidden" name="biaya_admin" value="{{ $payrollData['biaya_admin'] }}">
                        <input type="hidden" name="penanggung_jawab" value="{{ $payrollData['penanggung_jawab'] }}">
                        <input type="hidden" name="jabatan_pj" value="{{ $payrollData['jabatan_pj'] }}">

                
                        @if ($payrollData['sistem_pengajian'] == 1)
                            <div class="flex gap-3">
                                @php
                                    $payloadReportHarian = [
                                        'id_unit' => $payrollData['unit_id'],
                                        'tgl_awal' => $payrollData['tanggal_mulai'],
                                        'tgl_akhir' => $payrollData['tanggal_akhir'],
                                        'grand_total' => $payrollData['grand_total'],
                                        'workers_json' => $jsonWorkers, // Data JSON pekerja
                                    ];
                                @endphp

                                <!-- Tombol Summary Upah -->
                                <button type="button"
                                    @click="open('Summary Upah', '{{ route('export.summary.upah.harian') }}', {{ json_encode($payloadReportHarian) }})"
                                    class="inline-flex items-center gap-2.5 px-6 py-3.5 bg-slate-50 border border-slate-200 text-slate-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-white hover:border-emerald-500 hover:text-emerald-600 group transition-all shadow-sm active:scale-95">
                                    
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-500 transition-colors" 
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <span>Summary Upah</span>
                                </button>

                                <!-- Tombol Report Harian -->
                                <button type="button"
                                    @click="open('Report Harian', '{{ route('export.detail.harian') }}', {{ json_encode($payloadReportHarian) }})"
                                    class="inline-flex items-center gap-2.5 px-6 py-3.5 bg-slate-50 border border-slate-200 text-slate-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-white hover:border-emerald-500 hover:text-emerald-600 group transition-all shadow-sm active:scale-95">
                                    
                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-500 transition-colors" 
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Report Harian</span>
                                </button>
   
                            </div>
                        @endif

                        @if ($payrollData['sistem_pengajian'] == 2)
                            <div class="flex gap-3">
                                
                                @php
                                    $payloadReportBorongan = [
                                        'id_unit' => $payrollData['unit_id'],
                                        'tgl_awal' => $payrollData['tanggal_mulai'],
                                        'tgl_akhir' => $payrollData['tanggal_akhir'],
                                        'grand_total' => $payrollData['grand_total'],
                                        'workers_json' => $jsonWorkers, // Data JSON pekerja
                                    ];
                                @endphp

                                <!-- Tombol Report Harian -->
                                <button type="button"

                                @click="open('', '{{ route('export.borongan.kelompok') }}', {{ json_encode($payloadReportBorongan) }})"
                                    
                                    class="inline-flex items-center gap-2.5 px-6 py-3.5 bg-slate-50 border border-slate-200 text-slate-500 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-white hover:border-emerald-500 hover:text-emerald-600 group transition-all shadow-sm active:scale-95">
                                    
                                    

                                    <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-500 transition-colors" 
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span>Report Bulk Borongan</span>
                                </button>
   
                            </div>
                        @endif
                        

                        {{-- MULTI-STEP GENERATE COMPONENT (PREMIUM MODAL) --}}
                        <div x-data="generatePayroll({
                            workers: {{ json_encode(collect($payrollData['items'])->map(fn($item) => ['nama' => $item['nama'], 'status' => 'pending'])->values()->all()) }},
                            url: '{{ $payrollData['sistem_pengajian'] == 1 ? route('export.rincian.upah.harian') : route('export.rincian.upah.borongan') }}'
                             })" class="flex items-center">

                            <button type="button" @click="startGenerate()"
                                class="group flex items-center gap-3 bg-emerald-600 hover:bg-emerald-500 text-white px-8 py-3.5 rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-emerald-500/20 transition-all active:scale-95">
                                Generate Rincian Upah
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M13 7l5 5-5 5M6 7l5 5-5 5" />
                                </svg>
                            </button>
                            

                            <!-- Modal Overlay -->
                            <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-0" x-cloak>
                                <!-- Backdrop -->
                                <div x-show="showModal" x-transition.opacity @click="closeModal()"
                                    class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

                                <!-- Modal Content -->
                                <div x-show="showModal" @click.outside="closeModal()"
                                    x-transition:enter="transition ease-out duration-300 transform"
                                    x-transition:enter-start="opacity-0 translate-y-8 sm:scale-95 sm:translate-y-0"
                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                    class="relative w-full max-w-md bg-white rounded-[2rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.1)] p-8 border border-slate-100 overflow-hidden flex flex-col text-center mt-auto sm:mt-0 mb-4 sm:mb-0 mx-4">
                                    
                                    <!-- Close Button -->
                                    <button type="button" @click="closeModal()" class="absolute top-5 right-5 w-8 h-8 flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-400 hover:text-slate-600 rounded-full transition-all active:scale-95">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>

                                    <!-- Generating State -->
                                    <template x-if="state === 'generating'">
                                        <div class="w-full flex flex-col items-center animate-in fade-in zoom-in duration-300">
                                            <div class="relative w-20 h-20 mb-6 flex items-center justify-center mt-4">
                                                <!-- Outer Spin -->
                                                <svg class="absolute inset-0 w-full h-full text-emerald-100 animate-[spin_3s_linear_infinite]" viewBox="0 0 100 100">
                                                    <circle cx="50" cy="50" r="48" fill="none" stroke="currentColor" stroke-width="4" stroke-dasharray="80 20" stroke-linecap="round"></circle>
                                                </svg>
                                                <!-- Inner Element -->
                                                <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center shadow-inner">
                                                    <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                    </svg>
                                                </div>
                                            </div>
                                            
                                            <h3 class="text-xl font-black text-slate-800 tracking-tight">Memproses Data Payroll</h3>
                                            <p class="text-[11px] font-bold text-slate-400 mt-2 mb-8 leading-relaxed max-w-[250px]">
                                                Sistem sedang memilah data dan mengenerate dokumen untuk masing-masing unit.
                                            </p>
                                            
                                            <div class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-5 flex flex-col gap-3">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
                                                        <span class="text-[11px] font-black text-slate-600 uppercase tracking-widest mt-0.5">Master Excel</span>
                                                    </div>
                                                    <span class="text-[10px] font-bold text-slate-500 bg-white px-2 py-1 rounded shadow-sm border border-slate-100 uppercase tracking-widest">Generating...</span>
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse" style="animation-delay: 400ms"></div>
                                                        <span class="text-[11px] font-black text-slate-600 uppercase tracking-widest mt-0.5">PDF Pekerja</span>
                                                    </div>
                                                    <span class="text-[10px] font-bold text-slate-500 bg-white px-2 py-1 rounded shadow-sm border border-slate-100 uppercase tracking-widest" x-text="`${totalPekerja} Files`"></span>
                                                </div>
                                            </div>  
                                        </div>
                                    </template>

                                    <!-- Done State -->
                                    <template x-if="state === 'done'">
                                        <div class="w-full flex flex-col items-center animate-in fade-in zoom-in duration-300">
                                            <div class="w-20 h-20 bg-gradient-to-tr from-emerald-500 to-teal-400 text-white rounded-[1.5rem] flex items-center justify-center mb-6 mt-4 shadow-xl shadow-emerald-500/30">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <h3 class="text-2xl font-black text-slate-800 tracking-tight">Generate Selesai!</h3>
                                            <p class="text-xs font-bold text-slate-400 mt-2 mb-8 max-w-[260px] leading-relaxed">
                                                Semua file rekapitulasi gaji telah berhasil disusun secara otomatis.
                                            </p>
                                            
                                            <div class="w-full flex flex-col gap-3">
                                                <button type="button" @click="downloadExcel($event)" 
                                                    class="group relative w-full flex items-center justify-between p-4 bg-white border-2 border-emerald-500 text-emerald-600 hover:bg-emerald-50 rounded-2xl transition-all shadow-sm active:scale-95">
                                                    <div class="flex items-center gap-4">
                                                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 group-hover:bg-white border border-emerald-200/50 transition-colors shadow-sm">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                            </svg>
                                                        </div>
                                                        <div class="text-left">
                                                            <span class="block font-black text-sm text-emerald-700">Download Excel</span>
                                                            <span class="block font-bold text-[11px] uppercase tracking-widest text-emerald-600 mt-0.5">Report Rekapitulasi</span>
                                                        </div>
                                                    </div>
                                                </button>

                                                <button type="button" @click="startSending()"
                                                    class="group relative w-full flex items-center justify-between p-4 bg-white border-2 border-blue-500 text-blue-600 hover:bg-blue-50 rounded-2xl transition-all shadow-sm active:scale-95">
                                                    <div class="flex items-center gap-4">
                                                        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 group-hover:bg-white border border-blue-200/50 transition-colors shadow-sm">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                                            </svg>
                                                        </div>
                                                        <div class="text-left">
                                                            <span class="block font-black text-sm text-blue-700">Distribusi Rincian Upah</span>
                                                            <span class="block font-bold text-[11px] uppercase tracking-widest text-blue-600 mt-0.5" x-text="`Kirim ke ${totalPekerja} Email`"></span>
                                                        </div>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Sending Email State -->
                                    <template x-if="state === 'sending'">
                                        <div class="w-full flex flex-col animate-in fade-in duration-300 text-left">
                                            <div class="w-full flex items-center justify-between border-b border-slate-100 pb-5 mb-5 relative mt-4">
                                                <div class="flex items-center gap-4">
                                                    <div class="w-12 h-12 bg-slate-100 text-slate-600 rounded-2xl flex items-center justify-center relative shadow-inner">
                                                        <div class="absolute inset-0 rounded-2xl border-2 border-slate-600/20" :class="!doneSending ? 'animate-ping opacity-20' : 'opacity-0'" style="animation-duration: 2s;"></div>
                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <h4 class="text-lg font-black text-slate-800 tracking-tight" x-text="doneSending ? 'Distribusi Selesai!' : 'Mengirim Dokumen...'"></h4>
                                                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mt-1">Status Pengiriman Digital</p>
                                                    </div>
                                                </div>
                                                <div class="px-3 py-1.5 bg-slate-900 text-white rounded-xl text-[10px] font-black shadow-lg shadow-slate-900/20 mt-1" x-text="progressText()"></div>
                                            </div>
                                            
                                            <div class="w-full max-h-60 overflow-y-auto space-y-2.5 pr-2 custom-scrollbar">
                                                <template x-for="(worker, index) in workers" :key="index">
                                                    <div class="flex items-center justify-between p-3.5 rounded-2xl border transition-all shadow-sm"
                                                        :class="worker.status === 'sent' ? 'bg-emerald-50/50 border-emerald-100' : (worker.status === 'failed' ? 'bg-rose-50/50 border-rose-100' : 'bg-slate-50 border-slate-100')">
                                                        
                                                        <div class="flex items-center gap-3">
                                                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-black uppercase tracking-widest shadow-inner border border-white/50"
                                                                :class="worker.status === 'sent' ? 'bg-emerald-200 text-emerald-700' : (worker.status === 'failed' ? 'bg-rose-200 text-rose-700' : 'bg-white text-slate-400')">
                                                                <span x-text="worker.nama.substring(0,2)"></span>
                                                            </div>
                                                            <span class="text-xs font-black text-slate-700" x-text="worker.nama"></span>
                                                        </div>
                                                        
                                                        <template x-if="worker.status === 'pending'">
                                                            <span class="text-[9px] font-bold text-slate-400 flex items-center gap-1.5 uppercase tracking-widest bg-white px-2.5 py-1.5 rounded-lg border border-slate-100 shadow-sm">
                                                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                                Memproses
                                                            </span>
                                                        </template>
                                                        <template x-if="worker.status === 'sent'">
                                                            <div class="flex items-center gap-1.5 bg-emerald-500 text-white px-2.5 py-1.5 rounded-lg shadow-md shadow-emerald-500/20">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                                <span class="text-[9px] font-black uppercase tracking-widest mt-0.5">Sukses</span>
                                                            </div>
                                                        </template>
                                                        <template x-if="worker.status === 'failed'">
                                                            <div class="flex items-center gap-1.5 bg-rose-500 text-white px-2.5 py-1.5 rounded-lg shadow-md shadow-rose-500/20">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                <span class="text-[9px] font-black uppercase tracking-widest mt-0.5">Gagal</span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>

                                            <div class="mt-6 w-full pt-4 border-t border-slate-100" x-show="doneSending" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                                                <button type="button" @click="closeModal()" class="w-full flex items-center justify-center gap-2 py-4 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-colors border border-slate-200 active:scale-95 shadow-sm">
                                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                    <span class="mt-0.5">Selesai & Tutup</span>
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <style>
                                .custom-scrollbar::-webkit-scrollbar { width: 5px; }
                                .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.02); border-radius: 10px; }
                                .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
                                .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
                            </style>
                        </div>
                    </form>

                    <div x-show="show" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak>

                        <!-- Overlay -->
                        <div x-show="show" x-transition.opacity @click="show = false"
                            class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm">
                        </div>

                        <!-- Modal -->
                        <div x-show="show" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            class="relative w-full max-w-2xl bg-white rounded-[2rem] shadow-2xl border border-slate-100 p-8">

                            <form :action="actionUrl" method="POST" class="space-y-10">
                                @csrf

                                <!-- Hidden Inputs -->
                                <template x-for="(value, key) in extraData" :key="key">
                                    <input type="hidden" :name="key" :value="value">
                                </template>

                                <!-- Header -->
                                <div class="text-center space-y-3">
                                    <div
                                        class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 mx-auto">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3" />
                                        </svg>
                                    </div>

                                    <h3 class="text-xl font-black text-slate-800" x-text="'Generate ' + title"></h3>

                                    <p class="text-xs text-slate-400">
                                        Masukkan biaya administrasi untuk memproses dokumen ini.
                                    </p>
                                </div>

                                <!-- Input -->
                                <div class="space-y-10" x-data="rupiahInput()">
    
                                    <!-- 1. FINANCIAL TOP BAR (Increased Gap & Refined Alignment) -->
                                    <div class="flex items-center justify-between p-6 bg-emerald-50/50 rounded-2xl border border-emerald-100/50 shadow-sm">
                                        <div class="flex items-center gap-5"> <!-- Increased gap here -->
                                            <div class="p-2.5 bg-emerald-100 text-emerald-600 rounded-xl shadow-sm border border-emerald-200/50">
                                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="flex flex-col">
                                                <label class="text-[11px] font-black text-emerald-800 uppercase tracking-[0.25em] leading-none">Biaya Administrasi</label>
                                                <span class="text-[9px] font-bold text-emerald-600/60 uppercase tracking-widest mt-1">Input Nominal Unit</span>
                                            </div>
                                        </div>
                                        
                                        <!-- Input area with more breathing room -->
                                        <div class="relative w-56 group">
                                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[11px] font-black text-emerald-400 group-focus-within:text-emerald-600 transition-colors">Rp</span>
                                            <input type="text" x-model="display" @input="format" inputmode="numeric" placeholder="0"
                                                class="w-full pl-12 pr-5 py-3.5 bg-white border border-emerald-100 rounded-2xl text-base font-black text-slate-700 focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 transition-all outline-none shadow-sm">
                                        </div>
                                    </div>

                                    <!-- 2. PJ SECTION: TABULAR REGISTRY STYLE -->
                                    <div class="space-y-3">
                                        {{-- Section Header with Blue Accent --}}
                                        <div class="flex items-center gap-3 px-1 mb-4">
                                            <div class="w-1 h-5 bg-blue-600 rounded-full"></div>
                                            <h4 class="text-[11px] font-black text-slate-800 uppercase tracking-[0.2em]">Otorisasi Pejabat Penanggung Jawab</h4>
                                        </div>

                                        {{-- Table Container --}}
                                        <div class="bg-white border border-slate-100 rounded-[1.5rem] overflow-hidden shadow-sm">
                                            {{-- Column Headers --}}
                                            <div class="grid grid-cols-12 gap-4 px-6 py-4 bg-slate-50/80 border-b border-slate-100 text-[10px] font-black text-slate-400 uppercase tracking-widest">
                                                <div class="col-span-1 text-center">No.</div>
                                                <div class="col-span-6 ml-2">Pejabat (Nama Lengkap)</div>
                                                <div class="col-span-5">Jabatan / Posisi</div>
                                            </div>

                                            {{-- Table Rows --}}
                                            <div class="divide-y divide-slate-50">
                                                <template x-for="i in [1, 2, 3]" :key="i">
                                                    <div class="grid grid-cols-12 gap-4 px-6 py-4 items-center group hover:bg-blue-50/30 transition-colors">
                                                        {{-- No --}}
                                                        <div class="col-span-1 text-center">
                                                            <span class="text-xs font-black text-slate-300 group-hover:text-blue-500 transition-colors" x-text="i + '.'"></span>
                                                        </div>
                                                        
                                                        {{-- Nama Input --}}
                                                        <div class="col-span-6 relative">
                                                            <input type="text" name="pj_nama[]" 
                                                                :placeholder="['Contoh: Ir. Budi Santoso', 'Contoh: Siti Aminah, SE', 'Contoh: Ahmad Fauzi'][i-1]"
                                                                class="w-full px-4 py-2.5 bg-slate-50/50 border-none rounded-xl text-[12px] font-bold text-slate-700 placeholder:text-slate-300 focus:ring-2 focus:ring-blue-100 focus:bg-white outline-none transition-all">
                                                        </div>

                                                        {{-- Jabatan Input --}}
                                                        <div class="col-span-5 relative">
                                                            <input type="text" name="pj_jabatan[]" 
                                                                :placeholder="['Jabatan: Head of Ops', 'Jabatan: Manager', 'Jabatan: Direktur'][i-1]"
                                                                class="w-full px-4 py-2.5 bg-slate-50/50 border-none rounded-xl text-[12px] font-bold text-slate-700 placeholder:text-slate-300 focus:ring-2 focus:ring-blue-100 focus:bg-white outline-none transition-all">
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 3. ACTIONS (Side-by-side Balanced) -->
                                    <div class="flex items-center gap-4 pt-4 border-t border-slate-50">
                                        <button type="button" @click="show = false" 
                                            class="flex-1 py-4 text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] hover:text-rose-500 transition-all active:scale-95">
                                            Batalkan
                                        </button>
                                        <button type="submit" 
                                            class="flex-[2] py-4 bg-slate-900 text-white rounded-2xl text-[11px] font-black uppercase tracking-[0.3em] shadow-xl shadow-slate-200 hover:bg-emerald-600 hover:shadow-emerald-100 transition-all active:scale-95 flex items-center justify-center gap-3 group">
                                            Finalisasi Report
                                            <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5-5 5M6 7l5 5-5 5" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>


                            </form>
                        </div>
                    </div>

                </div>
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

        function reportModal() {
            return {
                show: false,
                title: '',
                actionUrl: '',
                extraData: {},

                open(title, url, payload = {}) {
                    this.title = title;
                    this.actionUrl = url;
                    this.extraData = payload;
                    this.show = true;
                }
            }
        }

        function rupiahInput() {
            return {
                raw: '',
                display: '',

                format() {
                    // Remove non digits
                    this.raw = this.display.replace(/\D/g, '');

                    // Format as Indonesian Rupiah
                    this.display = new Intl.NumberFormat('id-ID').format(this.raw);
                }
            }
        }

        function generatePayroll(config) {
            return {
                showModal: false,
                doneSending: false,
                state: 'idle', // idle, generating, done, sending
                workers: config.workers || [],
                url: config.url || '',

                get totalPekerja() {
                    return this.workers.length;
                },

                progressText() {
                    let sent = this.workers.filter(w => w.status === 'sent' || w.status === 'failed').length;
                    return `${sent} / ${this.totalPekerja}`;
                },

                startGenerate() {
                    this.state = 'generating';
                    this.showModal = true;
                    this.doneSending = false;
                    // Simulate generation process 
                    setTimeout(() => {
                        this.state = 'done';
                    }, 2500); 
                },

                closeModal() {
                    this.showModal = false;
                    setTimeout(() => {
                        this.state = 'idle';
                    }, 300);
                },

                downloadExcel(e) {
                    // Find the parent form and submit it to the url
                    const form = e.target.closest('form');
                    if(form) {
                        form.action = this.url;
                        form.submit();
                    }
                },

                async startSending() {
                    this.state = 'sending';
                    this.doneSending = false;
                    
                    // Reset statuses
                    this.workers.forEach(w => w.status = 'pending');

                    // Find closest form relative to this Alpine component
                    const form = this.$el.closest('form');
                    const formData = new FormData(form);

                    try {
                        console.log('Sending Payload:', Object.fromEntries(formData.entries()));

                        // Send request to dispatch background jobs
                        let response = await fetch("{{ route('payroll.dispatch.emails') }}", {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        console.log('Response status:', response.status);
                        let result = await response.json();
                        console.log('Server result:', result);
                        
                        if(result.success) {
                            // Jobs dispatched successfully. 
                            // Simulate UI processing for UX
                            for (let i = 0; i < this.workers.length; i++) {
                                await new Promise(r => setTimeout(r, 150));
                                this.workers[i].status = 'sent';
                            }
                        } else {
                            console.error('Job dispatch failed:', result);
                            throw new Error(result.error || 'Gagal');
                        }
                    } catch (error) {
                        console.error('Error dispatching emails:', error);
                        alert('Error: ' + error.message);
                        for (let i = 0; i < this.workers.length; i++) {
                            this.workers[i].status = 'failed';
                        }
                    }

                    this.doneSending = true;
                }
            }
        }
    </script>
@endsection
