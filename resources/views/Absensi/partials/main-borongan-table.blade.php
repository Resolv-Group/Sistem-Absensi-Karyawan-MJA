@forelse($pkwtPekerja as $u)
    @php
        $absensi = $u->pekerja->absensiByUnitTanggal($u->id_unit, $date)->first();

        $detil = $absensi?->detilBorongan;

        // Grouping berdasarkan nama_item (pastikan key ini sesuai dengan model/database Anda)
        $boronganItems = collect($absensi?->detilBorongan)->groupBy('barang.nama_item');
    @endphp

    <tr class="hover:bg-orange-50/30 transition-colors group cursor-pointer border-b border-gray-100"
        @click="selectedItems.includes({{ $u->id }}) ? selectedItems = selectedItems.filter(id => id !== {{ $u->id }}) : selectedItems.push({{ $u->id }})"
        :class="selectedItems.includes({{ $u->id }}) ? 'bg-orange-50' : ''">

        {{-- 1. CHECKBOX --}}
        <td class="pl-8 py-5 align-top">
            <div class="mt-2">
                <input type="checkbox" value="{{ $u->id }}" x-model.number="selectedItems" @click.stop
                    class="rounded border-gray-300 text-orange-600 focus:ring-orange-100">
            </div>
        </td>

        {{-- 2. PEKERJA --}}
        <td class="px-4 py-5 align-top min-w-[180px]">
            <div class="flex items-center gap-3 mt-1">
                <div
                    class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 font-black text-xs">
                    {{ strtoupper(substr($u->pekerja->nama, 0, 2)) }}
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-slate-800 group-hover:text-orange-600 transition truncate"
                        title="{{ $u->pekerja->nama }}">
                        {{ $u->pekerja->nama }}
                    </p>
                    <p class="text-[12px] font-medium text-slate-400 uppercase tracking-tighter">NIK:
                        {{ $u->pekerja->nik }}
                    </p>
                </div>
            </div>
        </td>

        {{-- 3. RINCIAN BORONGAN (MENGGANTIKAN JAM MASUK) --}}
        <td class="px-4 py-5 align-top" x-data="{ openExtra: false }">
            @php
                $allBorongan = collect($absensi?->detilBorongan);
                $limit = 1;
                $visibleItems = $allBorongan->take($limit);
                $hiddenItems = $allBorongan->slice($limit);
            @endphp

            @if ($allBorongan->isNotEmpty())
                <div class="space-y-2 relative">
                    {{-- 1. DISPLAY ITEM (HORIZONTAL UI) --}}
                    @foreach ($visibleItems as $item)
                        @if ($item->id_barang === 0)
                            <div
                                class="py-4 flex flex-col items-center justify-center border border-dashed border-slate-100 rounded-2xl bg-slate-50/30">
                                <span class="text-[12px] font-black text-slate-300 uppercase tracking-[0.2em]">Belum Ada
                                    Output</span>
                            </div>

                        @else
                            <div
                                class="flex items-center justify-between p-3 bg-white border border-slate-100 rounded-2xl shadow-sm hover:border-orange-200 transition-all group/item">
                                <div class="flex items-center gap-3 min-w-0">
                                    {{-- Accent Bar --}}
                                    <div
                                        class="w-1 h-6 {{ $item->buktiSuratJalan ? 'bg-emerald-400' : 'bg-slate-300' }} rounded-full flex-shrink-0">
                                    </div>

                                    <div class="flex flex-col min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span
                                                class="text-[13px] font-black text-slate-700 uppercase tracking-widest truncate max-w-[120px]">
                                                {{ $barangs->firstWhere('id', $item->id_barang)->nama_item ?? 'Item Tanpa Nama' }}
                                            </span>

                                            @if ($item->buktiSuratJalan)
                                                <div
                                                    class="flex items-center gap-1 px-1.5 py-0.5 bg-emerald-50 text-emerald-600 rounded-md border border-emerald-100/50">
                                                    <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5"
                                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                    </svg>
                                                    <span class="text-[7px] font-black uppercase tracking-tighter">SJ
                                                        Attached</span>
                                                </div>
                                            @endif
                                        </div>
                                        <span
                                            class="text-[10px] font-bold text-slate-300 uppercase tracking-tighter mt-0.5">Produksi
                                            Borongan</span>
                                    </div>
                                </div>

                                {{-- <div class="flex flex-col items-end flex-shrink-0 ml-4">
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-[15px] font-black text-orange-600 leading-none">
                                            {{ number_format($item->totalQTY ?? 0) }}
                                        </span>
                                        <span
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Qty</span>
                                    </div>
                                </div> --}}
                            </div>
                        @endif
                    @endforeach

                    {{-- 2. "SHOW MORE" TRIGGER --}}
                    @if ($hiddenItems->count() > 0)
                        <div class="relative mt-2" @mouseenter="openExtra = true" @mouseleave="openExtra = false">
                            {{-- TOMBOL TRIGGER --}}
                            <button type="button"
                                class="w-full py-2.5 flex items-center justify-center gap-2 bg-slate-50 border border-slate-100 rounded-xl text-[12px] font-black text-slate-400 uppercase tracking-widest hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 transition-all shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                                {{ $hiddenItems->count() }} Item Lainnya
                            </button>

                            {{-- MODAL POPUP (POPS BELOW) --}}
                            <div x-show="openExtra" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                x-transition:leave="transition ease-in duration-100" {{-- FIX: Menggunakan top-full dan mt-3 untuk muncul di bawah --}}
                                class="absolute top-full left-0 mt-3 w-[320px] z-[200] p-5 bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-slate-100 origin-top-left"
                                x-cloak>

                                {{-- Panah Dekorasi (Dipindah ke Atas) --}}
                                <div
                                    class="absolute -top-2 left-8 w-4 h-4 bg-white border-l border-t border-slate-100 rotate-45">
                                </div>

                                <div class="flex items-center justify-between mb-4 px-1 relative z-10">
                                    <span class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Item
                                        Tambahan</span>
                                    <div class="h-1.5 w-1.5 bg-orange-500 rounded-full animate-pulse"></div>
                                </div>

                                {{-- List Item (Identik dengan Horizontal UI luar) --}}
                                <div
                                    class="space-y-2 max-h-[300px] overflow-y-auto custom-scrollbar pr-1 relative z-10">
                                    @foreach ($hiddenItems as $item)
                                        <div
                                            class="flex items-center justify-between p-3 bg-slate-50 border border-slate-100 rounded-2xl hover:bg-white hover:border-orange-200 transition-all group/pop">
                                            <div class="flex items-center gap-3 min-w-0">
                                                {{-- Aksen Hijau/Slate --}}
                                                <div
                                                    class="w-1 h-5 {{ $item->buktiSuratJalan ? 'bg-emerald-400' : 'bg-slate-300' }} rounded-full flex-shrink-0">
                                                </div>

                                                <div class="min-w-0">
                                                    <span
                                                        class="block text-[12px] font-black text-slate-700 uppercase tracking-widest truncate max-w-[140px]">
                                                        {{ $barangs->firstWhere('id', $item->id_barang)->nama_item ?? 'Item' }}
                                                    </span>
                                                    @if ($item->buktiSuratJalan)
                                                        <div class="flex items-center gap-1 mt-0.5">
                                                            <svg class="w-2.5 h-2.5 text-emerald-500" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="3"
                                                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                            </svg>
                                                            <span
                                                                class="text-[7px] font-black text-emerald-600 uppercase tracking-tighter">SJ
                                                                Attached</span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            {{-- <div class="text-right ml-4">
                                                <div class="flex items-baseline gap-1">
                                                    <span
                                                        class="text-[13px] font-black text-orange-600 leading-none">{{ number_format($item->totalQTY ?? 0) }}</span>
                                                    <span
                                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Qty</span>
                                                </div>
                                            </div> --}}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div
                    class="py-4 flex flex-col items-center justify-center border border-dashed border-slate-100 rounded-2xl bg-slate-50/30">
                    <span class="text-[12px] font-black text-slate-300 uppercase tracking-[0.2em]">Belum Ada
                        Output</span>
                </div>
            @endif
        </td>

        {{-- 4. STATUS ABSEN --}}
        <td class="px-4 py-5 text-center align-top">
            <div class="mt-1">
                @php $statusKehadiran = $detil?->first()?->status_kehadiran; @endphp

                @if ($statusKehadiran)
                    @switch($statusKehadiran)
                        @case(1)
                            <span
                                class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-[12px] font-black uppercase tracking-widest border border-emerald-200 shadow-sm">Hadir</span>
                        @break

                        @case(2)
                            <span
                                class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-[12px] font-black uppercase tracking-widest border border-purple-200 shadow-sm">Cuti</span>
                        @break
                    @endswitch
                @else
                    <span class="text-[12px] font-bold text-slate-200">NO STATUS</span>
                @endif
            </div>
        </td>

        {{-- 5. STATUS VERIFIKASI --}}
        <td class="px-4 py-5 text-center align-top">
            <div class="mt-1">
                @if ($absensi)
                    @switch($absensi->verifikasi)
                        @case(1)
                            <span
                                class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-[12px] font-black uppercase tracking-widest shadow-sm border border-emerald-200">Disetujui</span>
                        @break

                        @case(0)
                            <span
                                class="px-3 py-1 bg-amber-100 text-amber-700 rounded-lg text-[12px] font-black uppercase tracking-widest shadow-sm border border-amber-200">Menunggu
                                Persetujuan</span>
                        @break
                    @endswitch
                @else
                    <span class="text-[12px] font-bold text-slate-200 tracking-widest">Status Tidak Diketahui</span>
                @endif
            </div>
        </td>

        <td class="px-4 py-5 align-top" x-data="{ openNotes: false }">
            <div class="mt-1">
                @if ($absensi && $absensi->detilBorongan->isNotEmpty())
                    @php
                        // Ambil semua baris yang memiliki catatan
                        $allNotes = $absensi->detilBorongan->filter(fn($item) => !empty($item->catatan));
                        $noteLimit = 1;
                        $visibleNotes = $allNotes->take($noteLimit);
                        $hiddenNotes = $allNotes->slice($noteLimit);
                    @endphp

                    @if ($allNotes->isNotEmpty())
                        <div class="flex flex-col gap-3 relative">
                            {{-- 1. DISPLAY 2 CATATAN PERTAMA --}}
                            @foreach ($visibleNotes as $noteItem)
                                <div class="flex flex-col min-w-0 border-l-2 border-orange-200 pl-3 py-0.5">
                                    <span class="text-[12px] font-black text-slate-400 uppercase tracking-tighter">
                                        {{ $barangs->firstWhere('id', $noteItem->id_barang)->nama_item ?? 'Umum' }}
                                    </span>
                                    <p
                                        class="text-[12px] font-semibold text-slate-600 italic leading-tight break-words">
                                        {{ $noteItem->catatan }}
                                    </p>
                                </div>
                            @endforeach

                            {{-- 2. TOMBOL UNTUK SISA CATATAN --}}
                            @if ($hiddenNotes->count() > 0)
                                <div class="relative" @mouseenter="openNotes = true" @mouseleave="openNotes = false">
                                    <button type="button"
                                        class="flex items-center gap-1.5 px-2 py-1 bg-slate-50 border border-slate-100 rounded-lg text-[8px] font-black text-orange-600 uppercase tracking-widest hover:bg-orange-50 transition-all">
                                        <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        {{ $hiddenNotes->count() }} Catatan Lainnya
                                    </button>

                                    {{-- 3. POPOVER SEMUA CATATAN --}}
                                    <div x-show="openNotes" x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                        x-transition:leave="transition ease-in duration-100"
                                        class="absolute top-full right-0 mt-2 w-[280px] z-[150] p-5 bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-slate-100"
                                        x-cloak>

                                        <div class="flex items-center justify-between mb-4 px-1">
                                            <span
                                                class="text-[12px] font-black text-slate-400 uppercase tracking-[0.2em]">Daftar
                                                Catatan</span>
                                            <span
                                                class="px-2 py-0.5 bg-orange-50 text-orange-600 text-[10px] font-black rounded-md">{{ $allNotes->count() }}
                                                Total</span>
                                        </div>

                                        <div class="space-y-4 max-h-[250px] overflow-y-auto custom-scrollbar pr-2">
                                            @foreach ($allNotes as $item)
                                                <div class="flex flex-col border-l-2 border-orange-100 pl-3">
                                                    <span class="text-[10px] font-black text-slate-400 uppercase">
                                                        {{ $barangs->firstWhere('id', $item->id_barang)->nama_item ?? 'Catatan Umum' }}
                                                    </span>
                                                    <p class="text-[12px] font-medium text-slate-700 italic">
                                                        {{ $item->catatan }}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- Panah Dekorasi --}}
                                        <div
                                            class="absolute -top-2 right-10 w-4 h-4 bg-white border-l border-t border-slate-100 rotate-45">
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <span class="text-[12px] font-bold text-slate-200 italic uppercase tracking-widest">-</span>
                    @endif
                @else
                    <span class="text-[12px] font-bold text-slate-200 italic uppercase tracking-widest">-</span>
                @endif
            </div>
        </td>
    </tr>
    @empty
        <tr>
            <td colspan="6" class="px-6 py-32 text-center bg-white">
                <div x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
                    class="flex flex-col items-center justify-center">

                    @if (request()->anyFilled(['search', 'status', 'statusVerif']))
                        {{-- SKENARIO A: FILTER AKTIF TAPI TIDAK ADA HASIL --}}
                        <div class="relative mb-10">
                            {{-- orange Glow --}}
                            <div class="absolute inset-0 bg-orange-400 rounded-full blur-[50px] opacity-20 animate-pulse">
                            </div>

                            {{-- Animated Icon Card (orange) --}}
                            <div
                                class="relative w-32 h-32 bg-gradient-to-br from-orange-500 to-orange-600 rounded-[2.5rem] shadow-2xl shadow-orange-200 flex items-center justify-center animate-float-harian-main">
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
                                class="inline-flex items-center gap-2 px-8 py-3.5 bg-white border-2 border-gray-100 text-gray-700 text-sm font-black rounded-2xl hover:bg-gray-50 hover:border-orange-200 transition-all active:scale-95 shadow-sm">
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
                            Unit ini belum memiliki daftar pekerja yang terdaftar di sistem.
                        </p>
                    @endif
                </div>
            </td>
        </tr>
    @endforelse

    <div id="new-ids-provider-full" data-ids="{{ json_encode($pkwtPekerja->pluck('id')) }}" class="hidden"></div>
    <div id="new-pagination-provider" class="hidden">
        @if ($pkwtPekerja->hasPages())
            {{ $pkwtPekerja->links('vendor.pagination.custom') }}
        @endif
    </div>
