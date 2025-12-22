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
                    <div class="text-sm font-bold text-gray-900 truncate max-w-[200px]"
                        title="{{ $u->namaMitra->nama_mitra }}">
                        {{ $u->namaMitra->nama_mitra }}
                    </div>
                    {{-- Sub Text (Unit Name or ID) --}}
                    <div class="text-xs text-gray-500 mt-0.5 flex items-center gap-1">
                        <span class="truncate max-w-[150px]">{{ $u->nama_unit ?? 'Unit Umum' }}</span>
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
            <div class="flex items-center justify-center gap-2 text-gray-600">
                @if (optional(optional($u->picUnit)->staff)->nama)
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-sm font-medium">{{ optional($u->picUnit->staff)->nama }}</span>
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
            100
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
                <!-- Edit -->
                <a href="{{ route('view.ubah.unit', $u->id) }}"
                    class="text-blue-600 hover:text-blue-900 border border-blue-200 hover:bg-blue-50
               rounded-lg p-2 transition"
                    title="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                </a>

                <!-- Detail -->
                <a href="{{ route('view.detail.unit', $u->id) }}"
                    class="text-blue-600 hover:text-blue-900 border border-blue-200 hover:bg-blue-50
               rounded-lg p-2 transition"
                    title="Detail">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
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
