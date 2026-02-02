@forelse($borongan as $b)
<tr class="hover:bg-orange-50/30 transition-colors group cursor-pointer"
    @click="selectedItems.includes({{ $b->id }}) ? selectedItems = selectedItems.filter(id => id !== {{ $b->id }}) : selectedItems.push({{ $b->id }})"
    :class="selectedItems.includes({{ $b->id }}) ? 'bg-orange-50' : ''">

    <td class="pl-8 py-5">
        <input type="checkbox" value="{{ $b->id }}" x-model.number="selectedItems" @click.stop class="rounded border-gray-300 text-orange-600 focus:ring-orange-100">
    </td>

    <td class="px-4 py-5">
        <p class="text-sm font-bold text-gray-900 group-hover:text-orange-700 transition">{{ $b->nama_item }}</p>
        <p class="text-[10px] font-mono text-gray-400 tracking-tighter">ID: {{ $b->id }}</p>
    </td>

    <td class="px-4 py-5 text-center">
        <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-gray-200">
            {{ $b->kategoriRel->nama }}
        </span>
    </td>

    <td class="px-4 py-5 text-center">
        <span class="font-mono text-sm font-bold text-gray-900 tracking-tight">
            {{ $b->max_rej_subkon }}%
        </span>
    </td>

    <td class="px-4 py-5 text-right">
        <span class="font-mono text-sm font-bold text-gray-900 tracking-tight">
            Rp {{ number_format($b->harga_unit, 0, ',', '.') }}
        </span>
    </td>

    <td class="px-4 py-5 text-right">
        <span class="font-mono text-sm font-bold text-emerald-600 tracking-tight">
            Rp {{ number_format($b->harga_pekerja, 0, ',', '.') }}
        </span>
    </td>

    <td class="px-4 py-5 text-center">
        @if ($b->status_aktif == 1)
            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black uppercase tracking-widest">Aktif</span>
        @else
            <span class="px-3 py-1 bg-gray-100 text-gray-400 rounded-full text-[10px] font-black uppercase tracking-widest">Nonaktif</span>
        @endif
    </td>

    <td class="pr-8 py-5 text-right">
        <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
            <a href="{{ route('view.ubah.unit-borongan', ['unitId' => $unit->id, 'boronganId' => $b->id]) }}"
               @click.stop
               class="p-2 bg-gray-100 text-gray-500 hover:bg-orange-600 hover:text-white rounded-xl transition shadow-sm">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-width="2" />
                </svg>
            </a>
        </div>
    </td>
</tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-32 text-center bg-white">
            <div x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="flex flex-col items-center justify-center">

                @if(request()->anyFilled(['search', 'kategori', 'status']))
                    {{-- SKENARIO A: FILTER AKTIF TAPI TIDAK ADA HASIL --}}
                    <div class="relative mb-10">
                        {{-- Orange Glow --}}
                        <div class="absolute inset-0 bg-orange-400 rounded-full blur-[50px] opacity-20 animate-pulse"></div>

                        {{-- Animated Icon Card (Orange) --}}
                        <div class="relative w-32 h-32 bg-gradient-to-br from-orange-500 to-orange-600 rounded-[2.5rem] shadow-2xl shadow-orange-200 flex items-center justify-center animate-float-borongan">
                            <svg class="w-16 h-16 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>

                            {{-- Cross Badge --}}
                            <div class="absolute -top-2 -right-2 w-8 h-8 bg-red-500 rounded-full border-4 border-white flex items-center justify-center shadow-lg">
                                <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">Item Tidak Ditemukan</h3>
                    <p class="text-sm text-gray-500 max-w-[400px] mx-auto mt-3 leading-relaxed">
                        Tidak ada item borongan di unit <span class="font-bold text-gray-800">{{ $unit->nama_unit }}</span> yang cocok dengan filter pencarian Anda.
                    </p>

                    <div class="mt-10">
                        <button type="button" @click="resetFilters()"
                            class="inline-flex items-center gap-2 px-8 py-3.5 bg-white border-2 border-gray-100 text-gray-700 text-sm font-black rounded-2xl hover:bg-gray-50 hover:border-orange-200 transition-all active:scale-95 shadow-sm">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset Filter & Pencarian
                        </button>
                    </div>

                @else
                    {{-- SKENARIO B: DATABASE BENAR-BENAR KOSONG --}}
                    <div class="relative mb-10">
                        <div class="absolute inset-0 bg-gray-200 rounded-full blur-[50px] opacity-30"></div>

                        <div class="relative w-32 h-32 bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-200 flex items-center justify-center group hover:border-orange-400 transition-colors duration-500">
                            <svg class="w-16 h-16 text-gray-300 group-hover:text-orange-200 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                    </div>

                    <h3 class="text-2xl font-black text-gray-400 tracking-tight">Belum Ada Item Borongan</h3>
                    <p class="text-sm text-gray-400 max-w-[350px] mx-auto mt-3 leading-relaxed">
                        Daftar harga dan item borongan untuk unit ini belum dikonfigurasi. Mulai dengan membuat item baru.
                    </p>

                    <div class="mt-10">
                        <a href="{{ route('view.tambah.unit-borongan', $unit->id) }}"
                            class="inline-flex items-center gap-3 px-10 py-4 bg-orange-600 text-white text-sm font-black rounded-2xl hover:bg-orange-700 shadow-xl shadow-orange-200 transition-all hover:-translate-y-1 active:scale-95">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Buat Item Borongan Baru
                        </a>
                    </div>
                @endif
            </div>
        </td>
    </tr>
@endforelse
