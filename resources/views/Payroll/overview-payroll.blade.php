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
                            Unit: <span class="text-slate-700"> {{ $payrollData['unit_name'] ?? 'Unit 0'}}</span>
                            <span class="mx-2 text-slate-200">|</span>
                            Periode: <span class="text-slate-700">{{ $payrollData['periode'] }}</span>
                        </p>
                    </div>
                </div>

                {{-- Export Action Cards --}}
                <div class="flex gap-3">
                    <a href="#"
                        class="flex flex-col items-center justify-center w-20 h-20 bg-slate-50 border border-slate-200 rounded-2xl hover:bg-white hover:border-emerald-500 group transition-all shadow-sm">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-emerald-600 transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-[9px] font-black uppercase tracking-tighter text-slate-500 mt-1">Tanda Terima</span>
                    </a>
                    <a href="#"
                        class="flex flex-col items-center justify-center w-20 h-20 bg-slate-50 border border-slate-200 rounded-2xl hover:bg-white hover:border-emerald-500 group transition-all shadow-sm">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-emerald-600 transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span class="text-[9px] font-black uppercase tracking-tighter text-slate-500 mt-1">Invoice</span>
                    </a>
                    <a href="#"
                        class="flex flex-col items-center justify-center w-20 h-20 bg-slate-50 border border-slate-200 rounded-2xl hover:bg-white hover:border-emerald-500 group transition-all shadow-sm">
                        <svg class="w-5 h-5 text-slate-400 group-hover:text-emerald-600 transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <span class="text-[9px] font-black uppercase tracking-tighter text-slate-500 mt-1">Kwitansi</span>
                    </a>
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
                    <p class="text-xl font-black text-yellow-600">Rp {{ number_format($payrollData['total_penyesuaian'], 0, ',', '.') }}</p>
                </div>
                <div class="p-6 bg-emerald-50/20 lg:bg-transparent">
                    <p class="text-xs font-black text-slate-500 uppercase tracking-widest mb-2 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-slate-900"></span> Total Payroll
                    </p>
                    <p class="text-xl font-black text-slate-900">Rp {{ number_format($payrollData['grand_total'], 0, ',', '.') }}</p>
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
                        <tr
                            class="text-xs font-black text-slate-400 uppercase tracking-widest border-b border-slate-50 bg-slate-50/20">
                            <th class="px-8 py-5">Nama Pekerja</th>
                            <th class="px-6 py-5">Total Barang</th>
                            <th class="px-6 py-5 text-center">Hasil Gaji</th>
                            <th class="px-6 py-5 text-right">Potongan</th>
                            <th class="px-6 py-5 text-right">Tunjangan</th>
                            <th class="px-8 py-5 text-right">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm">

                        @foreach ($payrollData['items'] as $item)

                            @php
                                $netSalary = max(0, $item['net_salary']);
                            @endphp

                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-11 h-11 rounded-xl bg-slate-100 flex items-center justify-center text-xs font-black text-slate-500 border border-slate-200">
                                            {{ strtoupper(substr($item['nama'], 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800">{{ $item['nama'] }}</p>
                                            <p class="text-[11px] font-bold text-slate-400 mt-0.5 uppercase">ID:
                                                {{ $item['id_pekerja'] }}</p>
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
                                    @if($item['total_barang'] === 0)
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
                                    <a href=""
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-[10px] uppercase tracking-widest hover:border-emerald-500 hover:text-emerald-600 transition-all shadow-sm">
                                        <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Payslip
                                    </a>
                                </td>
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
                    <a href="#"
                        class="group flex items-center gap-3 bg-emerald-600 hover:bg-emerald-500 text-white px-8 py-3.5 rounded-xl font-black text-xs uppercase tracking-widest shadow-xl shadow-emerald-500/20 transition-all active:scale-95">
                        Generate Payroll
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M13 7l5 5-5 5M6 7l5 5-5 5" />
                        </svg>
                    </a>
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
