@forelse($pkwtPekerja as $u)
    @php
        $absensi = $u->pekerja->absensiPekerja->where('tgl_absensi', $date)->where('id_unit', $u->id_unit)->first();

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
                <span class="text-[12px] font-bold text-gray-200 tracking-widest">-- : --</span>
            @endif
        </td>

        <td class="px-4 py-5 text-center">
            @if ($detil)
                {{-- PERBAIKAN: Switch pada properti status_kehadiran --}}
                @switch($detil->status_kehadiran)
                    @case(1)
                        <span
                            class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-lg text-[12px] font-black uppercase tracking-widest shadow-sm border border-emerald-200">Hadir</span>
                    @break

                    @case(2)
                        <span
                            class="px-3 py-1 bg-purple-100 text-purple-700 rounded-lg text-[12px] font-black uppercase tracking-widest shadow-sm border border-purple-200">Cuti</span>
                    @break

                    @default
                        <span class="text-[12px] font-bold text-gray-300 italic">Status Unknown</span>
                @endswitch
            @else
                <span class="text-[12px] font-bold text-gray-300 italic uppercase tracking-widest">Belum Absen</span>
            @endif
        </td>

        <td class="px-4 py-5 text-center">
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

                    @default
                        <span class="text-[12px] font-bold text-gray-300 italic">Status Diketahui</span>
                @endswitch
            @else
                <span class="text-[12px] font-bold text-gray-300 italic uppercase tracking-widest">Belum
                    Terverifikasi</span>
            @endif
        </td>

        <td class="px-4 py-5 align-top">
            <div class="mt-1">
                @if ($detil && $detil->catatan)
                    {{-- Wrapper dengan aksen garis vertikal orange --}}
                    <div class="flex flex-col min-w-0 border-l-2 border-orange-200 pl-3 py-0.5">
                        {{-- Label Header Kecil --}}
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-tighter">
                            Keterangan
                        </span>

                        {{-- Isi Catatan --}}
                        <p class="text-[11px] font-semibold text-slate-600 italic leading-tight break-words max-w-[180px]"
                            title="{{ $detil->catatan }}">
                            {{ $detil->catatan }}
                        </p>
                    </div>
                @else
                    {{-- Tampilan jika kosong --}}
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
            {{ $pkwtPekerja->links('vendor.pagination.custom') }}
        @endif
    </div>
