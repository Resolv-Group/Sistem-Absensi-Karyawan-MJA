@forelse ($unit as $u)
    <tr class="hover:bg-gray-50 transition-colors duration-200 group border-b border-gray-100 last:border-0">

        {{-- 1. INDEX --}}
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 font-medium w-1">
            {{ ($unit->currentPage() - 1) * $unit->perPage() + $loop->iteration }}.
        </td>

        {{-- 2. UNIT / MITRA NAME --}}
        <td class="px-6 py-4 align-middle">
            <div class="flex items-center gap-4">
                {{-- Generated Avatar / Icon --}}
                <div
                    class="flex-shrink-0 h-10 w-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-sm shadow-sm border border-blue-100">
                    {{ substr($u->namaMitra->nama_mitra ?? 'U', 0, 1) }}
                </div>

                <div class="min-w-0">
                    {{-- Main Text (Mitra Name) --}}
                    <div class="text-sm font-bold text-gray-900 truncate max-w-[200px]" title="{{ $u->nama_unit }}}">
                        {{ $u->nama_unit }}
                    </div>
                    {{-- Sub Text (Unit Name or ID) --}}
                    <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                        <span class="truncate max-w-[150px]" title="{{ $u->namaMitra->nama_mitra }}">MK:
                            {{ $u->namaMitra->nama_mitra }}</span>
                    </div>
                </div>
            </div>
        </td>

        {{-- 3. ID (Monospace for technical look) --}}
        <td class="px-6 py-4 whitespace-nowrap text-center">
            <span class="font-mono text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded border border-gray-200">
                #{{ $u->id }}
            </span>
        </td>

        {{-- 4. PIC (Clean look with icon) --}}
        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center justify-center gap-2 text-gray-600 relative">
                @php
                    $pics = $u->picUnit ?? collect([]);
                    $count = $pics->count();
                @endphp

                @if ($count > 0)
                    {{-- 1. DISPLAY FIRST PIC --}}
                    <div class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span class="text-sm font-medium truncate max-w-[120px]">
                            {{ optional($pics->first()->staff)->nama ?? '-' }}
                        </span>
                    </div>

                    {{-- 2. DISPLAY "+X MORE" BADGE --}}
                    @if ($count > 1)
                        <div class="relative group/badge cursor-pointer ml-1">

                            {{-- The Badge --}}
                            <span
                                class="inline-flex items-center justify-center h-5 px-1.5 text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-100 rounded-full hover:bg-blue-100 transition">
                                +{{ $count - 1 }}
                            </span>

                            {{-- Tooltip Container (Positioned to the RIGHT) --}}
                            <div
                                class="absolute left-full top-1/2 transform -translate-y-1/2 ml-3 hidden group-hover/badge:block z-50">

                                {{-- Main Content Box --}}
                                <div
                                    class="bg-white border border-gray-200 text-sm rounded-xl py-3 px-4 shadow-xl whitespace-nowrap min-w-[140px] relative">

                                    {{-- Arrow pointing LEFT (towards badge) --}}
                                    {{-- We use border-b and border-l so the bottom-left corner points left when rotated --}}
                                    <div
                                        class="absolute top-1/2 -left-1.5 transform -translate-y-1/2 w-3 h-3 bg-white border-b border-l border-gray-200 rotate-45">
                                    </div>

                                    {{-- Header --}}
                                    <p
                                        class="text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-2 pb-2 border-b border-gray-100">
                                        PIC Lainnya
                                    </p>

                                    {{-- List --}}
                                    <div class="space-y-2">
                                        @foreach ($pics->skip(1) as $p)
                                            <div class="flex items-center gap-2">
                                                {{-- Simple Dot Avatar --}}
                                                <div class="w-1.5 h-1.5 rounded-full bg-blue-400"></div>

                                                {{-- Name --}}
                                                <span class="text-xs font-medium text-gray-700">
                                                    {{ optional($p->staff)->nama ?? '-' }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <span class="text-xs text-gray-400 italic">Belum diset</span>
                @endif
            </div>
        </td>


        {{-- 5. PAYMENT SYSTEM --}}
        @php
            $sistemPengajian = [1 => 'Harian', 2 => 'Borongan'];
            $colors = [1 => 'text-purple-600 bg-purple-50', 2 => 'text-orange-600 bg-orange-50'];
        @endphp
        <td class="px-6 py-4 whitespace-nowrap text-center">
            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium {{ $colors[$u->sistem_pengajian] ?? 'text-gray-600 bg-gray-50' }}">
                {{ $sistemPengajian[$u->sistem_pengajian] ?? '-' }}
            </span>
        </td>

        {{-- 6. STAT (Total Workers) --}}
        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600 font-medium">
            {{ $u->pkwt_count }}


        </td>

        {{-- 7. STATUS --}}
        <td class="px-6 py-4 whitespace-nowrap text-center">
            @if ($u->status_aktif == 1)
                <div
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-100">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full mr-1.5 animate-pulse"></span>
                    Aktif
                </div>
            @else
                <div
                    class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                    <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>
                    Non-Aktif
                </div>
            @endif
        </td>

        {{-- 8. ACTIONS --}}
        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex justify-end gap-2">
                {{-- Tambah Pekerja --}}
                <a href="{{ route('view.tambah.unit-pekerja', $u->id) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg transition-all hover:shadow-sm group"
                    title="Tambah Pekerja">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-5-5a3 3 0 11-6 0 3 3 0 016 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                    <span class="hidden sm:inline">Pekerja</span>
                </a>

                {{-- Tambah Borongan (hanya sistem_pengajian = 2) --}}
                @if ($u->sistem_pengajian == 2)
                    <a href="{{ route('view.tambah.unit-borongan', $u->id) }}"
                        class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-purple-700 bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg transition-all hover:shadow-sm group"
                        title="Tambah Borongan">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        <span class="hidden sm:inline">Borongan</span>
                    </a>
                @endif

                {{-- Edit --}}
                <a href="{{ route('view.ubah.unit', $u->id) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-all hover:shadow-sm group"
                    title="Edit Unit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                    <span class="hidden sm:inline">Edit</span>
                </a>

                {{-- Detail --}}
                <a href="{{ route('view.detail.unit', $u->id) }}"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-700 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg transition-all hover:shadow-sm group"
                    title="Lihat Detail">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span class="hidden sm:inline">Detail</span>
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
            <div class="flex flex-col items-center justify-center">
                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                <p class="font-medium">Belum ada data mitra kerja.</p>
                <p class="text-sm mt-1">Silakan tambah mitra kerja baru.</p>
            </div>
        </td>
    </tr>
@endforelse
