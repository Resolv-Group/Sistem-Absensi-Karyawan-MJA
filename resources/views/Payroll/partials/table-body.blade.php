
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
                123123123
            </span>
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
            <div class="flex justify-end">
                <button type="button"
                    @click="$store.payslip.open(
                        '{{ $u->nama_unit }}',
                        {{
                            $u->pkwt->map(fn($p) => [
                                'id' => $p->id_pekerja,
                                'nama' => $p->pekerja->nama ?? 'Unknown'
                            ])->values()->toJson()
                        }}
                    )"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl font-bold text-[11px] uppercase tracking-widest hover:bg-slate-50 hover:border-emerald-500 hover:text-emerald-600 transition-all duration-200 shadow-sm active:scale-95 group">

                    <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Payslip</span>
                </button>
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
