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
            @click="selectedItems.includes({{ $u->id }})
            ? selectedItems = selectedItems.filter(id => id !== {{ $u->id }})
            : selectedItems.push({{ $u->id }})"
            :class="selectedItems.includes({{ $u->id }}) ? 'bg-orange-50' : 'bg-white'">

            {{-- CHECKBOX --}}
            <td class="pl-6 py-4 w-10">
                <input type="checkbox" value="{{ $u->id }}" x-model.number="selectedItems" @click.stop
                    class="rounded border-gray-200 text-orange-600 focus:ring-orange-100">
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
            <td class="px-4 py-4 min-w-[260px]">
                @if ($absensi && $effectiveDetil->isNotEmpty() && $statusKehadiran == 1)
                    <div class="flex items-start gap-3">

                        {{-- Type Badge (vertical pill) --}}
                        <div class="flex-shrink-0 flex flex-col items-center gap-1 pt-0.5">
                            @if ($isGroup)
                                <div class="w-6 h-6 rounded-lg bg-violet-100 flex items-center justify-center"
                                    title="Absen Kelompok">
                                    <svg class="w-3.5 h-3.5 text-violet-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-[8px] font-black text-violet-500 uppercase tracking-wider leading-none">Grup</span>
                            @else
                                <div class="w-6 h-6 rounded-lg bg-orange-100 flex items-center justify-center"
                                    title="Absen Individual">
                                    <svg class="w-3.5 h-3.5 text-orange-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <span
                                    class="text-[8px] font-black text-orange-500 uppercase tracking-wider leading-none">Indiv</span>
                            @endif
                        </div>

                        {{-- Summary Content --}}
                        <div class="flex-1 min-w-0 space-y-2">

                            {{-- Item list (compact) --}}
                            @foreach ($effectiveDetil->take(2) as $detilItem)
                                @if ($detilItem->id_barang != 0)
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="text-[12px] font-bold text-slate-700 truncate max-w-[130px]">
                                            {{ $barangs->firstWhere('id', $detilItem->id_barang)->nama_item ?? '—' }}
                                        </span>
                                        <div class="flex items-center gap-1 flex-shrink-0">
                                            <span class="text-[11px] font-black text-orange-600">
                                                {{ number_format($detilItem->FD + $detilItem->act_rej + $detilItem->good_mc) }}
                                            </span>
                                            <span class="text-[9px] text-slate-400">qty</span>
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            {{-- More items indicator --}}
                            @if ($itemCount > 2)
                                <span class="text-[10px] font-bold text-slate-400">
                                    +{{ $itemCount - 2 }} item lainnya
                                </span>
                            @endif

                            {{-- Total footer --}}
                            <div class="flex items-center justify-between pt-1 border-t border-slate-100 mt-1">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total
                                    Bayar</span>
                                <span class="text-[12px] font-black text-emerald-600">
                                    Rp {{ number_format($totalBayar, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                @elseif ($absensi && $statusKehadiran && $statusKehadiran != 1)
                    {{-- Non-hadir status --}}
                    <div class="flex items-center gap-2">
                        <div
                            class="w-1.5 h-1.5 rounded-full
                        @if ($statusKehadiran == 2) bg-purple-400
                        @elseif($statusKehadiran == 3) bg-amber-400
                        @else bg-slate-300 @endif">
                        </div>
                        <span class="text-[12px] font-bold text-slate-500 italic">
                            @switch($statusKehadiran)
                                @case(2)
                                    Cuti
                                @break

                                @case(3)
                                    Sakit
                                @break

                                @case(4)
                                    Izin
                                @break

                                @default
                                    Tidak Hadir
                            @endswitch
                        </span>
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
                    @if ($absensi?->tunjangan)
                        <span
                            class="px-1.5 py-0.5 bg-emerald-50 text-emerald-600 rounded text-[9px] font-black border border-emerald-100 uppercase tracking-wider"
                            title="Ada Tunjangan">+T</span>
                    @endif
                    @if ($absensi?->potongan)
                        <span
                            class="px-1.5 py-0.5 bg-rose-50 text-rose-600 rounded text-[9px] font-black border border-rose-100 uppercase tracking-wider"
                            title="Ada Potongan">-P</span>
                    @endif
                    @if (!$absensi?->tunjangan && !$absensi?->potongan)
                        <span class="text-[11px] font-bold text-slate-200">—</span>
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
