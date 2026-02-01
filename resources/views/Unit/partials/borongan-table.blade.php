<style>
    @keyframes float {
        0% { transform: translateY(0px) rotate(3deg); }
        50% { transform: translateY(-10px) rotate(6deg); }
        100% { transform: translateY(0px) rotate(3deg); }
    }
    .animate-float { animation: float 3s ease-in-out infinite; }
</style>

@forelse ($borongan as $b)
    <tr @click="selectedItems.includes({{ $b->id }}) ? selectedItems = selectedItems.filter(id => id !== {{ $b->id }}) : selectedItems.push({{ $b->id }})"
        :class="selectedItems.includes({{ $b->id }}) ? 'bg-orange-50/50' : 'hover:bg-orange-50/20'"
        class="transition-colors cursor-pointer group">

        <td class="pl-6 py-5 align-top">
            <input type="checkbox" value="{{ $b->id }}" x-model.number="selectedItems" @click.stop
                class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-200 cursor-pointer mt-1">
        </td>

        <td class="px-2 py-5 align-top text-center"><span
                class="text-xs font-bold text-gray-400 font-mono">{{ $loop->iteration }}</span></td>

        <td class="px-4 py-5 align-top">
            <p class="text-sm font-bold text-gray-900 group-hover:text-orange-700 transition truncate max-w-[200px]">
                {{ $b->nama_item }}</p>
            <span class="text-[10px] font-mono text-gray-400">ID: {{ $b->id }}</span>
        </td>

        <td class="px-4 py-5 align-top">
            <span
                class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                {{ $b->kategoriRel->nama }}
            </span>
        </td>

        <td class="px-4 py-5 align-top">
            <span class="text-sm font-bold text-gray-900">{{ $b->max_rej_subkon}}</span>
        </td>

        <td class="px-4 py-5 align-top">
            <span class="text-sm font-bold text-gray-900">Rp {{ number_format($b->harga_unit, 0, ',', '.') }}</span>
        </td>

        <td class="px-4 py-5 align-top">
            <span class="text-sm font-bold text-emerald-600">Rp
                {{ number_format($b->harga_pekerja, 0, ',', '.') }}</span>
        </td>

        <td class="px-4 py-5 align-top">
            <span
                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $b->status_aktif ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">
                {{ $b->status_aktif ? 'Aktif' : 'Nonaktif' }}
            </span>
        </td>

        <td class="pr-6 py-5 align-top text-right">
            <a href="{{ route('view.ubah.unit-borongan', ['unitId' => $unit->id, 'boronganId' => $b->id]) }}"
                @click.stop class="p-2 text-gray-400 hover:text-orange-600 transition">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-6 py-24 text-center bg-white">
            <div x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="flex flex-col items-center justify-center">

                {{-- Animated Icon --}}
                <div class="relative mb-8">
                    <div class="absolute inset-0 bg-orange-200 rounded-full blur-3xl opacity-30 animate-pulse"></div>
                    <div class="relative w-24 h-24 bg-gradient-to-br from-orange-50 to-white rounded-3xl flex items-center justify-center border border-orange-100 shadow-xl animate-float">
                        <svg class="w-12 h-12 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>

                <h3 class="text-xl font-black text-gray-900 tracking-tight">Item Borongan Kosong</h3>
                <p class="text-sm text-gray-500 max-w-[320px] mx-auto mt-3 leading-relaxed">
                    Item tidak ditemukan dalam unit ini. Silahkan hapus pencarian atau cek daftar master borongan.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 mt-10">
                    {{-- Action 1: Reset --}}
                    <button type="button" @click="resetFilters()"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-white text-gray-700 text-xs font-bold rounded-2xl border border-gray-200 hover:bg-gray-50 transition-all active:scale-95 shadow-sm hover:-translate-y-1">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Reset Filter
                    </button>

                    {{-- Action 2: Navigate (Dedicated Page) --}}
                    <a href="{{ route('view.borongan', $unit->id) }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 bg-orange-600 text-white text-xs font-bold rounded-2xl hover:bg-orange-700 shadow-lg shadow-orange-100 transition-all hover:-translate-y-1 active:scale-95">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        Master Borongan
                    </a>
                </div>
            </div>
        </td>
    </tr>
@endforelse

{{-- ID Sync Provider --}}
<div id="borongan-ids-provider" data-ids="{{ json_encode($borongan->pluck('id')) }}" class="hidden"></div>
