@forelse($pkwtPekerja as $u)
    @php
        $absensi = $u->pekerja->absensiPekerja->firstWhere('tgl_absensi', $date);
        $detil = $absensi?->detilHarian;
    @endphp

    <tr class="hover:bg-blue-50/30 transition-colors group cursor-pointer"
        @click="selectedItems.includes({{ $u->id }}) ? selectedItems = selectedItems.filter(id => id !== {{ $u->id }}) : selectedItems.push({{ $u->id }})"
        :class="selectedItems.includes({{ $u->id }}) ? 'bg-blue-50' : ''">

        <td class="pl-8 py-5">
            <input type="checkbox" value="{{ $u->id }}" x-model.number="selectedItems" @click.stop
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-100">
        </td>

        <td class="px-4 py-5">
            <div class="flex items-center gap-4">
                {{-- Avatar Initials --}}
                <div
                    class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-black text-xs">
                    {{ strtoupper(substr($u->pekerja->nama, 0, 2)) }}
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition">
                        {{ $u->pekerja->nama }}</p>
                    <p class="text-xs font-mono text-gray-400">NIK: {{ $u->pekerja->nik }}</p>
                </div>
            </div>
        </td>

        {{-- STATUS ABSENSI --}}

        <td class="px-4 py-5 text-left">
            {{-- Tampilkan jam hanya jika statusnya Hadir (1) --}}
            @if ($detil && $detil->status_kehadiran == 1 && $detil->waktu_masuk)
                <div class="flex items-center gap-2">
                    <span
                        class="text-xs font-black text-gray-700 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">
                        {{ \Carbon\Carbon::parse($detil->waktu_masuk)->format('H:i') }}
                    </span>
                    <span class="text-gray-300">—</span>
                    <span
                        class="text-xs font-black text-gray-700 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">
                        {{ \Carbon\Carbon::parse($detil->waktu_keluar)->format('H:i') }}
                    </span>
                </div>
            @else
                <span class="text-[10px] font-bold text-gray-200 tracking-widest">-- : --</span>
            @endif
        </td>

        <td class="px-4 py-5 text-center">
            @if ($detil)
                {{-- PERBAIKAN: Switch pada properti status_kehadiran --}}
                @switch($detil->status_kehadiran)
                    @case(1)
                        <span
                            class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border border-emerald-200">Hadir</span>
                    @break

                    @case(2)
                        <span
                            class="px-3 py-1 bg-amber-100 text-amber-700 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border border-amber-200">Sakit</span>
                    @break

                    @case(3)
                        <span
                            class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border border-blue-200">Izin</span>
                    @break

                    @case(4)
                        <span
                            class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border border-purple-200">Cuti</span>
                    @break

                    @case(5)
                        <span
                            class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border border-red-200">Alpha</span>
                    @break

                    @default
                        <span class="text-[10px] font-bold text-gray-300 italic">Status Unknown</span>
                @endswitch
            @else
                <span class="text-[10px] font-bold text-gray-300 italic uppercase tracking-widest">Belum Absen</span>
            @endif
        </td>
        <td class="px-4 py-5">
            <p class="text-[11px] font-medium text-gray-500 italic truncate max-w-[180px]" title="{{ $detil->catatan ?? '' }}">
                {{ $detil->catatan ?? '-' }}
            </p>
        </td>

        <td class="px-4 py-5 text-center">
            @if ($absensi)
                @switch($absensi->verifikasi)
                    @case(1)
                        <span
                            class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border border-emerald-200">Disetujui</span>
                    @break

                    @case(0)
                        <span
                            class="px-3 py-1 bg-amber-100 text-amber-700 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-sm border border-amber-200">Menunggu Persetujuan</span>
                    @break

                    @default
                        <span class="text-[10px] font-bold text-gray-300 italic">Status Unknown</span>
                @endswitch
            @else
                <span class="text-[10px] font-bold text-gray-300 italic uppercase tracking-widest">Belum Terverifikasi</span>
            @endif
        </td>
    </tr>
    @empty
        <tr>
            <td colspan="6" class="px-6 py-32 text-center bg-white">
                <div x-transition:enter="transition ease-out duration-500"
                    x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0"
                    class="flex flex-col items-center justify-center">

                    @if (request()->anyFilled(['search', 'status']))
                        {{-- SKENARIO A: FILTER AKTIF TAPI TIDAK ADA HASIL --}}
                        <div class="relative mb-10">
                            {{-- Blue Glow --}}
                            <div class="absolute inset-0 bg-blue-400 rounded-full blur-[50px] opacity-20 animate-pulse">
                            </div>

                            {{-- Animated Icon Card (Blue) --}}
                            <div
                                class="relative w-32 h-32 bg-gradient-to-br from-blue-500 to-blue-600 rounded-[2.5rem] shadow-2xl shadow-blue-200 flex items-center justify-center animate-float-harian-main">
                                <svg class="w-16 h-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Reset Semua Filter
                            </button>
                        </div>
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
