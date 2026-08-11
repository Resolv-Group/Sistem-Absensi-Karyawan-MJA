    @forelse($pkwtPekerja as $u)
        @php
            $absensi = $u->pekerja->absensiMany->first();
            $isGroup = $absensi && $absensi->absensiBorongan->isNotEmpty();
            $effectiveDetil = $absensi ? $absensi->getEffectiveDetilBorongan() : collect();
            $firstDetil = $effectiveDetil->first();
            $statusKehadiran = $firstDetil?->status_kehadiran;
            $totalQty = $effectiveDetil->sum(fn($d) => $d->FD + $d->act_rej + $d->good_mc);
            $totalBayar = $effectiveDetil->sum('bayaranItem');
            $itemCount = $effectiveDetil->count();
        @endphp

    <tr class="hover:bg-orange-50/20 transition-colors group cursor-pointer border-b border-gray-50"
        @click="toggleWithGroup({{ $u->id }})"
        :class="selectedItems.includes({{ (int)$u->id }}) ? 'bg-orange-50' : 'bg-white'">

        {{-- CHECKBOX COLUMN --}}
        <td class="pl-6 py-4 w-10">
            <input type="checkbox" 
                :checked="selectedItems.includes({{ (int)$u->id }})"
                @click.stop="toggleWithGroup({{ $u->id }})"
                class="rounded border-gray-200 text-orange-600 focus:ring-orange-100 cursor-pointer">
        </td>

            {{-- PEKERJA --}}
            <td class="px-4 py-4 min-w-[200px]">
                <div class="flex items-center gap-3">
                    <div
                        class="flex-shrink-0 w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-500 font-black text-xs">
                        {{ strtoupper(substr($u->pekerja->nama, 0, 2)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-800 truncate" title="{{ $u->pekerja->nama }}">
                            {{ $u->pekerja->nama }}
                        </p>
                        <p class="text-[11px] text-slate-400 font-mono tracking-tight">{{ $u->pekerja->nik }}</p>
                    </div>
                </div>
            </td>

            {{-- TIPE & RINGKASAN PRODUKSI --}}
            <td class="px-4 py-4 min-w-[280px] relative" x-data="{ openItems: false }" 
                {{-- This part ensures the row stays on top of other rows when hovered --}}
                @mouseenter="openItems = true" @mouseleave="openItems = false"
                :class="openItems ? 'z-[100]' : ''">

                @if ($absensi && $effectiveDetil->isNotEmpty() && $statusKehadiran == 1)
                    <div class="flex items-start gap-3">
                        
                        {{-- Type Badge --}}
                        <div class="flex-shrink-0 flex flex-col items-center gap-1.5 pt-0.5">
                            @php $isGroup = $absensi && $absensi->absensiBorongan->isNotEmpty(); @endphp
                            <div class="w-7 h-7 rounded-xl flex items-center justify-center border shadow-sm {{ $isGroup ? 'bg-violet-50 border-violet-100' : 'bg-orange-50 border-orange-100' }}">
                                @if($isGroup)
                                    <svg class="w-4 h-4 text-violet-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                @else
                                    <svg class="w-4 h-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                @endif
                            </div>
                            <span class="text-[8px] font-black uppercase tracking-widest leading-none {{ $isGroup ? 'text-violet-500' : 'text-orange-500' }}">
                                {{ $isGroup ? 'Grup' : 'Indiv' }}
                            </span>
                        </div>

                        {{-- Summary Content --}}
                        <div class="flex-1 min-w-0">
                            {{-- Show only the FIRST item --}}
                            @php $firstItem = $effectiveDetil->first(); @endphp
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="text-[12px] font-bold text-slate-800 truncate max-w-[140px]">
                                    {{ $barangs->firstWhere('id', $firstItem->id_barang)->nama_item ?? '—' }}
                                </span>
                                <div class="flex items-center gap-1 shrink-0">
                                    <span class="text-[12px] font-black text-slate-900">{{ number_format($firstItem->FD + $firstItem->act_rej + $firstItem->good_mc) }}</span>
                                    <span class="text-[9px] font-bold text-slate-400">QTY</span>
                                </div>
                            </div>

                            {{-- The Dropdown Trigger (Visible if > 1 item) --}}
                            @if ($itemCount > 1)
                                <div class="relative">
                                    <button type="button" 
                                        class="flex items-center gap-1.5 px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-[10px] font-black text-orange-600 uppercase tracking-tighter hover:bg-orange-100 transition-all">
                                        +{{ $itemCount - 1 }} Item Lainnya
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" /></svg>
                                    </button>

                                    {{-- Dropdown Card: The absolute fix --}}
                                    <div x-show="openItems" 
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                        class="absolute left-0 top-full mt-2 w-[280px] z-[200] p-5 bg-white rounded-[1.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-slate-100 origin-top-left"
                                        x-cloak>
                                        
                                        <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-50">
                                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Daftar Item Tambahan</span>
                                            <span class="px-2 py-0.5 bg-orange-50 text-orange-600 text-[9px] font-bold rounded">{{ $itemCount }} Total</span>
                                        </div>

                                        <div class="space-y-3 max-h-[220px] overflow-y-auto custom-scrollbar pr-1">
                                            @foreach ($effectiveDetil->slice(1) as $detilItem)
                                                <div class="flex items-center justify-between gap-3 group/item pb-2 border-b border-slate-50 last:border-0 last:pb-0">
                                                    <span class="text-[12px] font-bold text-slate-700 group-hover/item:text-orange-600 transition-colors truncate">
                                                        {{ $barangs->firstWhere('id', $detilItem->id_barang)->nama_item ?? '—' }}
                                                    </span>
                                                    <div class="text-right shrink-0">
                                                        <span class="text-[12px] font-black text-slate-900">{{ number_format($detilItem->FD + $detilItem->act_rej + $detilItem->good_mc) }}</span>
                                                        <span class="text-[9px] font-bold text-slate-400">QTY</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Total Bayar --}}
                            <div class="flex items-center justify-between pt-1.5 border-t border-slate-100 mt-2">
                                <span class="text-[9px] font-black text-slate-400 uppercase">Total Bayar</span>
                                <span class="text-[12px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-100">
                                    Rp {{ number_format($totalBayar, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @else
                    <span class="text-[11px] font-bold text-slate-300 italic">Belum diabsen</span>
                @endif
            </td>

            {{-- STATUS KEHADIRAN --}}
            <td class="px-4 py-4 text-center">
                @if ($statusKehadiran)
                    @switch($statusKehadiran)
                        @case(1)
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-[11px] font-black uppercase tracking-wider border border-emerald-100">
                                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                                Hadir
                            </span>
                        @break

                        @case(2)
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-purple-50 text-purple-700 rounded-lg text-[11px] font-black uppercase tracking-wider border border-purple-100">
                                <span class="w-1.5 h-1.5 bg-purple-500 rounded-full"></span>
                                Cuti
                            </span>
                        @break

                        @default
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-50 text-slate-500 rounded-lg text-[11px] font-black uppercase tracking-wider border border-slate-100">
                                <span class="w-1.5 h-1.5 bg-slate-400 rounded-full"></span>
                                Lainnya
                            </span>
                    @endswitch
                @else
                    <span class="text-[11px] font-bold text-slate-200">—</span>
                @endif
            </td>

            {{-- VERIFIKASI --}}
            <td class="px-4 py-4 text-center">
                @if ($absensi)
                    @if ($absensi->verifikasi == 1)
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-[11px] font-black uppercase tracking-wider border border-emerald-100">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            OK
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-700 rounded-lg text-[11px] font-black uppercase tracking-wider border border-amber-100">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Pending
                        </span>
                    @endif
                @else
                    <span class="text-[11px] font-bold text-slate-200">—</span>
                @endif
            </td>

            <td class="px-4 py-5 align-top" x-data="{ openNotes: false }">
                <div class="mt-1">
                    @if ($absensi && $effectiveDetil->isNotEmpty())
                        @php
                            $allNotes = $effectiveDetil->filter(fn($i) => !empty($i->catatan));
                            $noteLimit = 1;
                            $visibleNotes = $allNotes->take($noteLimit);
                            $hiddenNotes = $allNotes->slice($noteLimit);
                        @endphp

                        @if ($allNotes->isNotEmpty())
                            <div class="flex flex-col gap-3 relative">
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

                                @if ($hiddenNotes->count() > 0)
                                    <div class="relative" @mouseenter="openNotes = true"
                                        @mouseleave="openNotes = false">
                                        <button type="button"
                                            class="flex items-center gap-1.5 px-2 py-1 bg-slate-50 border border-slate-100 rounded-lg text-[8px] font-black text-orange-600 uppercase tracking-widest hover:bg-orange-50 transition-all">
                                            <svg class="w-2.5 h-2.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            {{ $hiddenNotes->count() }} Catatan Lainnya
                                        </button>

                                        <div x-show="openNotes" x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                            x-transition:leave="transition ease-in duration-100"
                                            class="absolute top-full right-0 mt-2 w-[280px] z-[150] p-5 bg-white rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.15)] border border-slate-100"
                                            x-cloak>

                                            <div
                                                class="absolute -top-2 right-10 w-4 h-4 bg-white border-l border-t border-slate-100 rotate-45">
                                            </div>

                                            <div class="flex items-center justify-between mb-4 px-1">
                                                <span
                                                    class="text-[12px] font-black text-slate-400 uppercase tracking-[0.2em]">Daftar
                                                    Catatan</span>
                                                <span
                                                    class="px-2 py-0.5 bg-orange-50 text-orange-600 text-[10px] font-black rounded-md">{{ $allNotes->count() }}
                                                    Total</span>
                                            </div>

                                            <div class="space-y-4 max-h-[250px] overflow-y-auto custom-scrollbar pr-2">
                                                @foreach ($allNotes as $noteItem)
                                                    <div class="flex flex-col border-l-2 border-orange-100 pl-3">
                                                        <span class="text-[10px] font-black text-slate-400 uppercase">
                                                            {{ $barangs->firstWhere('id', $noteItem->id_barang)->nama_item ?? 'Catatan Umum' }}
                                                        </span>
                                                        <p class="text-[12px] font-medium text-slate-700 italic">
                                                            {{ $noteItem->catatan }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <span
                                class="text-[12px] font-bold text-slate-200 italic uppercase tracking-widest">-</span>
                        @endif
                    @else
                        <span class="text-[12px] font-bold text-slate-200 italic uppercase tracking-widest">-</span>
                    @endif
                </div>
            </td>

            {{-- TUNJ / POT --}}
            <td class="px-4 py-4 text-center">
                <div class="flex items-center justify-center gap-1.5">
                    @php
                        $valTunjangan = $absensi?->tunjangan->total ?? 0;
                        $valPotongan = $absensi?->potongan->total ?? 0;
                    @endphp
                    @if ($valTunjangan > 0)
                        <div class="flex items-center gap-1.5 px-2 py-0.5 bg-emerald-50 border border-emerald-100 rounded-md w-full justify-between group-hover:bg-white transition-colors" title="Total Tunjangan">
                            <span class="text-[9px] font-black text-emerald-500 uppercase tracking-tighter">Tunj</span>
                            <span class="text-[11px] font-black text-emerald-700">
                                +{{ number_format($valTunjangan, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif

                    @if ($valPotongan > 0)
                        <div class="flex items-center gap-1.5 px-2 py-0.5 bg-rose-50 border border-rose-100 rounded-md w-full justify-between group-hover:bg-white transition-colors" title="Total Potongan">
                            <span class="text-[9px] font-black text-rose-400 uppercase tracking-tighter">Pot</span>
                            <span class="text-[11px] font-black text-rose-700">
                                -{{ number_format($valPotongan, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif

                    @if ($valTunjangan == 0 && $valPotongan == 0)
                        <span class="text-[11px] font-bold text-slate-200 tracking-widest">—</span>
                    @endif
                </div>
            </td>

        </tr>

        @empty
            <tr>
                <td colspan="7" class="px-6 py-32 text-center bg-white">
                    <div class="flex flex-col items-center justify-center">
                        @if (request()->anyFilled(['search', 'status', 'statusVerif']))
                            <div
                                class="w-16 h-16 bg-orange-50 rounded-2xl flex items-center justify-center mb-4 border border-orange-100">
                                <svg class="w-8 h-8 text-orange-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-black text-gray-700 tracking-tight">Tidak Ditemukan</h3>
                            <p class="text-sm text-gray-400 mt-1 mb-6">Tidak ada pekerja yang sesuai filter.</p>
                            <button type="button" @click="resetFilters()"
                                class="px-6 py-2.5 bg-white border border-gray-200 text-gray-600 text-sm font-bold rounded-xl hover:border-orange-300 transition-all">
                                Reset Filter
                            </button>
                        @else
                            <div
                                class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 border border-gray-100">
                                <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-black text-gray-300 tracking-tight">Belum Ada Pekerja</h3>
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
