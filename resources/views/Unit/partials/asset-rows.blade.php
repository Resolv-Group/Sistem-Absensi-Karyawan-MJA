{{-- Partial: Asset table rows for AJAX pagination --}}
@forelse($assets as $a)
    <tr class="group bg-white hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300">
        {{-- 1. Checkbox --}}
        <td class="px-4 py-4 rounded-l-2xl border-l border-y border-slate-100 text-center">
            <input type="checkbox" value="{{ $a->id }}"
                x-model="selectedRows"
                class="rounded-md border-slate-200 text-blue-600 focus:ring-blue-500 cursor-pointer">
        </td>

        {{-- 2. Index --}}
        <td class="px-4 py-4 border-y border-slate-100 text-center">
            <span class="text-xs font-bold text-slate-300">#{{ ($assets->currentPage() - 1) * $assets->perPage() + $loop->iteration }}</span>
        </td>

        {{-- 3. Nama & Keterangan (Grouped) --}}
        <td class="px-4 py-4 border-y border-slate-100">
            <p class="text-sm font-black text-slate-800 leading-tight">{{ $a->nama_barang }}</p>
            @if ($a->keterangan)
                <p class="text-[9px] text-slate-400 font-bold uppercase mt-1 tracking-tighter">{{ $a->keterangan }}</p>
            @else
                <p class="text-[9px] text-slate-300 italic mt-1 uppercase tracking-tighter">No Description</p>
            @endif
        </td>

        {{-- 4. Jumlah (Badge Style) --}}
        <td class="px-4 py-4 border-y border-slate-100 text-center">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black bg-blue-50 text-blue-600 border border-blue-100">
                {{ $a->jumlah }}
            </span>
        </td>

        {{-- 5. Tahun Perolehan --}}
        <td class="px-4 py-4 border-y border-slate-100 text-center">
            <div class="text-[10px] font-black text-slate-500 uppercase tracking-widest bg-slate-100 px-2 py-1 rounded-md inline-block">
                {{ \Carbon\Carbon::parse($a->tahun_perolehan)->format('d M Y') }}
            </div>
        </td>

        {{-- 6. Harga --}}
        <td class="px-4 py-4 border-y border-slate-100 text-right">
            <p class="text-sm font-black text-slate-700">
                {{ number_format($a->harga_perolehan, 0, ',', '.') }}
            </p>
        </td>

        {{-- 7. Lokasi (Badge Style) --}}
        <td class="px-4 py-4 border-y border-slate-100 text-center">
            <span class="text-[10px] font-black uppercase text-slate-600">
                <svg class="w-3 h-3 inline-block mb-0.5 mr-0.5 text-slate-400"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ $a->lokasi }}
            </span>
        </td>

        {{-- 8. Status Badge --}}
        <td class="px-4 py-4 border-y border-slate-100 text-center">
            @if($a->status == 2)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-50 text-emerald-600 border border-emerald-100">
                    ✓ Approved
                </span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-50 text-amber-600 border border-amber-100">
                    Pending
                </span>
            @endif
        </td>

        {{-- 9. Actions (Hover Reveal) --}}
        <td class="px-4 py-4 rounded-r-2xl border-r border-y border-slate-100 text-center">
            @if($a->status == 2)
                <span class="text-xs text-slate-400 italic">Locked</span>
            @else
                <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all transform group-hover:scale-100 scale-90">
                    <button @click="editEntries([{{ $a->id }}])"
                        title="Edit Asset"
                        class="p-2 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-600 hover:text-white transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    <button onclick="confirmDeleteAsset({{ $a->id }}, {{ $unit->id }})"
                        title="Hapus Asset"
                        class="p-2 bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            @endif
        </td>
    </tr>
@empty
    <tr>
        <td colspan="9" class="py-24 text-center">
            <div class="flex flex-col items-center opacity-20">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <p class="font-black uppercase tracking-widest text-sm">Belum ada asset terdaftar</p>
            </div>
        </td>
    </tr>
@endforelse
