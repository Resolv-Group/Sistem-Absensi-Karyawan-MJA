@forelse($pkwtPekerja as $u)
    @php
        $absensi = $u->pekerja->absensiPekerja->where('tgl_absensi', $date)->where('id_unit', $u->id_unit)->first();
        $detil = $absensi?->detilHarian;
        $dataMap = $workerMap[$u->id] ?? null;
    @endphp

    <tr class="hover:bg-blue-50/30 transition-colors group cursor-pointer"
        @click="selectedItems.includes({{ $u->id }}) ? selectedItems = selectedItems.filter(id => id !== {{ $u->id }}) : selectedItems.push({{ $u->id }})"
        :class="selectedItems.includes({{ $u->id }}) ? 'bg-blue-50' : ''">

        <td class="pl-8 py-5">
            <input type="checkbox" value="{{ $u->id }}" x-model.number="selectedItems" @click.stop
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-100">
        </td>

        {{-- 1. Profil Pekerja --}}
        <td class="px-4 py-5">
            <div class="flex items-center gap-4">
                <div
                    class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-black text-xs group-hover:bg-blue-600 group-hover:text-white transition-all">
                    {{ strtoupper(substr($u->pekerja->nama, 0, 2)) }}
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition">
                        {{ $u->pekerja->nama }}</p>
                    <p class="text-xs font-mono text-gray-400">NIK: {{ $u->pekerja->nik }}</p>
                </div>
            </div>
        </td>

        {{-- 2. Durasi Kerja (Merged) --}}
        <td class="px-4 py-5 text-center">
            @if ($detil)
                @php
                    $pkwtJam = $dataMap['pkwt_hari_kerja'] ?? 0;
                    $realJam = $detil->jam_kerja_harian ?? 0;
                    $isHbn = $detil->hbn == 1;

                    // 1. Logic: Only check "Jam Kurang" on normal days (HBN = 0)
                    // because on holidays, any hour worked is usually considered extra/OT.
                    $isShortShift = !$isHbn && $realJam < $pkwtJam;

                    // 2. Determine Color Priority: Red (Warning) > Indigo (Holiday) > Blue (Normal)
                    $mainColor = $isShortShift ? 'text-red-600' : ($isHbn ? 'text-indigo-600' : 'text-blue-600');

                    // 3. Determine Label
                    $subLabel = $isShortShift ? 'Jam Kurang' : ($isHbn ? 'Kerja Hari Libur' : 'Total Durasi');
                    $subColor = $isShortShift ? 'text-red-400' : ($isHbn ? 'text-indigo-300' : 'text-gray-300');
                @endphp

                <div class="flex flex-col">
                    {{-- Main Hour Display --}}
                    <span class="text-sm font-black {{ $mainColor }}">
                        {{ $realJam }}

                        @if (!$isHbn)
                            {{-- Normal Day: Show divisor --}}
                            <span class="text-gray-400 font-bold text-xs">/ {{ $pkwtJam }} Jam</span>
                        @else
                            {{-- Holiday (HBN): Hide divisor --}}
                            <span class="text-indigo-400 font-bold text-[10px] ml-1 uppercase tracking-tighter">/
                                Jam</span>
                        @endif
                    </span>

                    {{-- Sub-label (Dynamic: Total Durasi / Kerja Hari Libur / Jam Kurang) --}}
                    <span class="text-[10px] font-black uppercase tracking-tighter {{ $subColor }}">
                        {{ $subLabel }}
                    </span>

                    {{-- Optional: Small Payment Indicator if isPaid is active --}}
                    @if ($detil->isPaid == 1)
                        <span
                            class="text-[9px] font-bold text-emerald-500 uppercase leading-none mt-0.5 tracking-widest">Dibayar
                            Penuh</span>
                    @endif
                </div>
            @else
                <span class="text-xs font-bold text-gray-300 italic">Belum Input</span>
            @endif
        </td>

        {{-- 3. Akumulasi OT --}}
        <td class="px-4 py-5 text-center">
            @if ($detil && $detil->overtime > 0)
                <span class="text-sm font-black text-orange-500">
                    +{{ $detil->overtime }} <span class="text-[10px] font-bold">Jam</span>
                </span>
            @else
                <span class="text-xs font-bold text-gray-200">-</span>
            @endif
        </td>

        {{-- 5. HBN (Kolom Baru) --}}
        <td class="px-4 py-5 text-center">
            @if ($detil && $detil->hbn == 1)
                <div class="flex justify-center" title="Hari Besar Nasional">
                    <div
                        class="flex items-center gap-1 px-2 py-1 bg-indigo-50 text-indigo-600 rounded-md border border-indigo-100 shadow-sm">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" />
                        </svg>
                        <span class="text-[9px] font-black uppercase">HBN</span>
                    </div>
                </div>
            @else
                <span class="text-[10px] font-bold text-gray-200">-</span>
            @endif
        </td>

        {{-- 4. Cuti Berbayar (New Column) --}}
        <td class="px-4 py-5 text-center">
            @if ($detil && $detil->isPaid == 1)
                <div class="flex justify-center">
                    <div
                        class="w-6 h-6 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center shadow-sm border border-emerald-200">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
            @else
                <div class="flex justify-center opacity-20">
                    <div
                        class="w-6 h-6 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center border border-gray-200">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
            @endif
        </td>

        {{-- 5. Kondisi Absensi --}}
        <td class="px-4 py-5 text-center">
            @if ($detil)
                @php
                    $colors = [
                        1 => 'bg-emerald-50 text-emerald-600 border-emerald-100', // Hadir
                        2 => 'bg-blue-50 text-blue-600 border-blue-100', // Izin
                        3 => 'bg-purple-50 text-purple-600 border-purple-100', // Cuti
                        4 => 'bg-amber-50 text-amber-600 border-amber-100', // Sakit
                        5 => 'bg-cyan-50 text-cyan-600 border-cyan-100', // Rencana Cuti
                        6 => 'bg-red-50 text-red-600 border-red-100', // Absen
                    ];

                    $labels = [
                        1 => 'Hadir',
                        2 => 'Izin',
                        3 => 'Cuti',
                        4 => 'Sakit',
                        5 => 'Rencana Cuti',
                        6 => 'Absen',
                    ];

                    $statusVal = $detil->status_kehadiran;

                    // Logika Izin (Status 1 tapi jam 0)
                    if ($statusVal == 1 && $detil->jam_kerja_harian <= 0) {
                        $statusLabel = 'Izin';
                        $statusColor = 'bg-blue-50 text-blue-600 border-blue-100';
                    } else {
                        $statusLabel = $labels[$statusVal] ?? 'Unknown';
                        $statusColor = $colors[$statusVal] ?? 'bg-gray-50 text-gray-500 border-gray-100';
                    }
                @endphp

                {{-- Gunakan inline-flex dan whitespace-nowrap agar teks tetap satu baris --}}
                <span
                    class="inline-flex items-center justify-center whitespace-nowrap px-3 py-1.5 rounded-lg text-[9px] font-black uppercase tracking-widest shadow-sm border {{ $statusColor }}">
                    {{ $statusLabel }}
                </span>
            @else
                <span class="text-[10px] font-bold text-gray-300 italic uppercase tracking-widest">Kosong</span>
            @endif
        </td>

        {{-- 6. Validasi Data --}}
        <td class="px-4 py-5 text-center">
            @if ($absensi)
                @if ($absensi->verifikasi == 1)
                    <div class="flex items-center justify-center gap-1 text-emerald-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                        </svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Valid</span>
                    </div>
                @else
                    <div class="flex items-center justify-center gap-1 text-amber-600">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2.5">
                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Review</span>
                    </div>
                @endif
            @else
                <span class="text-[10px] font-black text-gray-200">-</span>
            @endif
        </td>

        {{-- 7. Memo --}}
        <td class="px-4 py-5 align-top">
            <div class="mt-1">
                @if ($detil && $detil->catatan)
                    <div class="flex flex-col min-w-0 border-l-2 border-orange-200 pl-3 py-0.5">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">Memo</span>
                        <p class="text-[11px] font-semibold text-slate-600 italic leading-tight truncate max-w-[150px]"
                            title="{{ $detil->catatan }}">
                            {{ $detil->catatan }}
                        </p>
                    </div>
                @else
                    <span class="text-[12px] font-bold text-slate-200 italic">-</span>
                @endif
            </div>
        </td>

        <td class="px-4 py-3 text-center">
            <div class="flex flex-col items-center gap-1.5">
                {{-- Row 1: Tunjangan Indicator --}}
                @if ($absensi && $absensi->tunjangan)
                    <div class="flex items-center gap-1 px-1.5 py-0.5 bg-emerald-50 text-emerald-600 rounded-md border border-emerald-100 shadow-sm"
                        title="Ada Tunjangan">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="4"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-[8px] font-black uppercase tracking-tighter">Plus</span>
                    </div>
                @else
                    <div class="h-4 flex items-center">
                        <span class="text-[10px] font-bold text-gray-200">-</span>
                    </div>
                @endif

                {{-- Row 2: Potongan Indicator --}}
                @if ($absensi && $absensi->potongan)
                    <div class="flex items-center gap-1 px-1.5 py-0.5 bg-rose-50 text-rose-600 rounded-md border border-rose-100 shadow-sm"
                        title="Ada Potongan">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" stroke-width="4"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-[8px] font-black uppercase tracking-tighter">Min</span>
                    </div>
                @else
                    <div class="h-4 flex items-center">
                        <span class="text-[10px] font-bold text-gray-200">-</span>
                    </div>
                @endif
            </div>
        </td>

    </tr>
@empty
    <tr>
        <td colspan="10" class="px-6 py-32 text-center bg-white">
            <div x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
                class="flex flex-col items-center justify-center">

                @if (request()->anyFilled(['search', 'status', 'statusVerif']))
                    {{-- SKENARIO A: FILTER AKTIF TAPI TIDAK ADA HASIL --}}
                    <div class="relative mb-10">
                        {{-- Blue Glow --}}
                        <div class="absolute inset-0 bg-blue-400 rounded-full blur-[50px] opacity-20 animate-pulse">
                        </div>

                        {{-- Animated Icon Card (Blue) --}}
                        <div
                            class="relative w-32 h-32 bg-gradient-to-br from-blue-500 to-blue-600 rounded-[2.5rem] shadow-2xl shadow-blue-200 flex items-center justify-center animate-float-harian-main">
                            <svg class="w-16 h-16 text-white" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>

                            {{-- Cross Badge --}}
                            <div
                                class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 rounded-full border-4 border-white flex items-center justify-center shadow-lg">
                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">Pekerja Tidak Ditemukan</h3>
                    <p class="text-sm text-gray-500 max-w-[400px] mx-auto mt-3 leading-relaxed">
                        Kami tidak menemukan pekerja di unit <span
                            class="font-bold text-gray-800">{{ $unit->nama_unit }}</span> yang sesuai dengan kriteria
                        pencarian Anda.
                    </p>

                    <div class="mt-10">
                        <button type="button" @click="resetFilters()"
                            class="inline-flex items-center gap-2 px-8 py-3.5 bg-white border-2 border-gray-100 text-gray-700 text-sm font-black rounded-2xl hover:bg-gray-50 hover:border-blue-200 transition-all active:scale-95 shadow-sm">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset Semua Filter
                        </button>
                    </div>
                @else
                    {{-- SKENARIO A: FILTER AKTIF TAPI TIDAK ADA HASIL --}}
                    {{-- STATE: DATABASE KOSONG (Sekarang sudah Animasi & Konsisten) --}}
                    <div class="relative mb-8">
                        {{-- Pulse Glow Abu-abu --}}
                        <div class="absolute inset-0 bg-gray-200 rounded-full blur-3xl opacity-50 animate-pulse"></div>

                        {{-- Floating Card Abu-abu --}}
                        <div
                            class="relative w-24 h-24 bg-gradient-to-br from-gray-50 to-white rounded-3xl flex items-center justify-center border border-gray-100 shadow-xl animate-float-harian">
                            <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>

                    <h3 class="text-xl font-black text-gray-400 tracking-tight">Belum Ada Pekerja</h3>
                    <p class="text-sm text-gray-400 max-w-[280px] mx-auto mt-3 leading-relaxed">
                        Unit ini belum memiliki daftar pekerja harian yang terdaftar di sistem.
                    </p>
                @endif
            </div>
        </td>
    </tr>
@endforelse

<div id="new-ids-provider-full" data-ids="{{ json_encode($pkwtPekerja->pluck('id')) }}" class="hidden"></div>
<div id="new-pagination-provider" class="hidden">
    @if ($pkwtPekerja->hasPages())
        {{ $pkwtPekerja->links('vendor.Pagination.custom') }}
    @endif
</div>
