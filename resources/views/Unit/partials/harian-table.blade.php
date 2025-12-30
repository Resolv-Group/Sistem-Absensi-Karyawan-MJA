<style>
    @keyframes float-harian {
        0% { transform: translateY(0px) rotate(-3deg); }
        50% { transform: translateY(-10px) rotate(-6deg); }
        100% { transform: translateY(0px) rotate(-3deg); }
    }
    .animate-float-harian { animation: float-harian 3s ease-in-out infinite; }
</style>

@forelse($pkwtPekerja as $pkwt)
    <tr @click="selectedItems.includes({{ $pkwt->id }}) ? selectedItems = selectedItems.filter(id => id !== {{ $pkwt->id }}) : selectedItems.push({{ $pkwt->id }})"
        :class="selectedItems.includes({{ $pkwt->id }}) ? 'bg-blue-50/50' : 'hover:bg-gray-50/80'"
        class="transition-colors cursor-pointer group">

        <td class="pl-6 py-5 align-top">
            <input type="checkbox" value="{{ $pkwt->id }}" x-model.number="selectedItems" @click.stop
                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-200 cursor-pointer mt-1">
        </td>
        <td class="px-2 py-5 align-top text-center">
            <span class="text-xs font-bold text-gray-400 font-mono">{{ $loop->iteration }}</span>
        </td>
        <td class="px-4 py-5 align-top">
            <div class="flex flex-col gap-0.5">
                <span
                    class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition truncate max-w-[200px]"
                    title="{{ $pkwt->pekerja->nama }}">
                    {{ $pkwt->pekerja->nama }}
                </span>
                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                    <span class="font-mono tracking-tight">{{ $pkwt->pekerja->nik }}</span>
                </div>
            </div>
        </td>
        <td class="px-4 py-5 align-top">
            <div class="flex flex-col gap-2">
                <span
                    class="inline-flex items-center text-xs font-semibold text-gray-800 bg-gray-100 px-2.5 py-1 rounded-md border border-gray-200 w-fit max-w-[180px] truncate">
                    {{ $pkwt->jabatan->nama }}
                </span>
                <span class="text-xs text-gray-500 pl-1 truncate max-w-[180px]">{{ $pkwt->divisi->nama }}</span>
            </div>
        </td>

        <td class="px-4 py-5 align-top text-center">
            @if ($pkwt->dokumen_mime)
                <a href="{{ route('stream.pkwt', $pkwt->id) }}" target="_blank" @click.stop
                    class="inline-flex flex-col items-center justify-center gap-1.5 p-2 rounded-lg hover:bg-white transition border border-transparent hover:border-gray-200">
                    @if (Str::startsWith($pkwt->dokumen_mime, 'application/pdf'))
                        <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        <span class="text-[9px] font-bold tracking-wide text-gray-500">PDF</span>
                    @else
                        <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span class="text-[9px] font-bold tracking-wide text-gray-500">IMG</span>
                    @endif
                </a>
            @else
                <span class="text-xs text-gray-300 italic">-</span>
            @endif
        </td>

        <td class="px-4 py-5 align-top text-center">
            <div class="flex flex-col items-center gap-1">
                <span
                    class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($pkwt->tgl_mulai_pkwt)->format('d/m/y') }}</span>
                <div class="h-3 w-px bg-gray-200"></div>
                <span
                    class="text-xs font-bold {{ $pkwt->status_pkwt['color'] === 'red' ? 'text-red-600' : 'text-emerald-600' }}">
                    {{ \Carbon\Carbon::parse($pkwt->tgl_akhir_pkwt)->format('d M Y') }}
                </span>
            </div>
        </td>

        <td class="px-4 py-5 align-top text-center">
            @if ($pkwt->status_aktif == 1)
                <span
                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Aktif</span>
            @else
                <span
                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">Nonaktif</span>
            @endif
        </td>

        <td class="pr-6 py-5 align-top text-right">
            <div class="flex justify-end gap-1">
                <a href="{{ route('view.ubah.unit-pekerja', ['unitId' => $unit->id, 'pekerjaId' => $pkwt->id]) }}"
                    @click.stop class="p-2 text-gray-400 hover:text-blue-600 hover:bg-white rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-6 py-24 text-center bg-white">
            <div x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="flex flex-col items-center justify-center">

                @if(request()->filled('search') || request()->filled('divisi') || request()->filled('jabatan') || request()->filled('status'))
                    {{-- STATE: SEARCH TIDAK DITEMUKAN (Warna Biru) --}}
                    <div class="relative mb-8">
                        <div class="absolute inset-0 bg-blue-200 rounded-full blur-3xl opacity-30 animate-pulse"></div>
                        <div class="relative w-24 h-24 bg-gradient-to-br from-blue-50 to-white rounded-3xl flex items-center justify-center border border-blue-100 shadow-xl animate-float-harian">
                            <svg class="w-12 h-12 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>

                    <h3 class="text-xl font-black text-gray-900 tracking-tight">Pekerja Tidak Ditemukan</h3>
                    <p class="text-sm text-gray-500 max-w-[320px] mx-auto mt-3 leading-relaxed">
                        Nama atau NIK tersebut tidak ada dalam <br><span class="font-bold text-blue-600">5 data terbaru</span>. Silakan reset filter atau cek database lengkap.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mt-10">
                        <button type="button" @click="resetFilters()" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-gray-700 text-xs font-bold rounded-2xl border border-gray-200 hover:bg-gray-50 transition-all shadow-sm hover:-translate-y-1">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                            Reset Filter
                        </button>
                        <a href="{{ route('view.pkwt', $unit->id) }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white text-xs font-bold rounded-2xl hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all hover:-translate-y-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                            Kelola Semua Pekerja
                        </a>
                    </div>
                @else
                    {{-- STATE: DATABASE KOSONG (Sekarang sudah Animasi & Konsisten) --}}
                    <div class="relative mb-8">
                        {{-- Pulse Glow Abu-abu --}}
                        <div class="absolute inset-0 bg-gray-200 rounded-full blur-3xl opacity-50 animate-pulse"></div>

                        {{-- Floating Card Abu-abu --}}
                        <div class="relative w-24 h-24 bg-gradient-to-br from-gray-50 to-white rounded-3xl flex items-center justify-center border border-gray-100 shadow-xl animate-float-harian">
                            <svg class="w-12 h-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>

                    <h3 class="text-xl font-black text-gray-400 tracking-tight">Belum Ada Pekerja</h3>
                    <p class="text-sm text-gray-400 max-w-[280px] mx-auto mt-3 leading-relaxed">
                        Unit ini belum memiliki daftar pekerja harian yang terdaftar di sistem.
                    </p>
                    <a href="{{ route('view.tambah.unit-pekerja', $unit->id) }}"
                        class="mt-8 group inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-600 border border-gray-200 text-xs font-bold rounded-2xl hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all shadow-sm hover:-translate-y-1 active:scale-95">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Pekerja Pertama
                    </a>
                @endif
            </div>
        </td>
    </tr>
@endforelse



{{-- SECRET SAUCE: Update Alpine allIds so Toggle All still works --}}
<div id="new-ids-provider" data-ids="{{ json_encode($pkwtPekerja->pluck('id')) }}" class="hidden"></div>
