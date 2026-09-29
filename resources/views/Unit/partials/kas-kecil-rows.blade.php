{{-- Partial: Kas Kecil table rows for AJAX pagination --}}
@php $runningSaldo = $carryForwardSaldo ?? 0; @endphp
@forelse($kasKecil as $kas)
    @php $runningSaldo += ($kas->debit - $kas->kredit); @endphp
    <tr class="group bg-white hover:shadow-xl hover:shadow-slate-200/50 transition-all">
        {{-- Checkbox --}}
        <td class="px-4 py-4 rounded-l-2xl border-l border-y border-slate-100 text-center">
            <input type="checkbox" value="{{ $kas->id }}"
                x-model="selectedRows"
                class="rounded-md border-slate-200 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
        </td>

        {{-- Tanggal --}}
        <td class="px-4 py-4 border-y border-slate-100 text-xs font-bold text-slate-500 tracking-tighter">
            {{ \Carbon\Carbon::parse($kas->tanggal)->format('d M Y') }}
        </td>

        {{-- Akun --}}
        <td class="px-4 py-4 border-y border-slate-100">
            <p class="text-sm font-black text-slate-800">{{ $kas->akun }}</p>
        </td>

        {{-- Deskripsi --}}
        <td class="px-4 py-4 border-y border-slate-100">
            <p class="text-sm font-black text-slate-800">{{ $kas->keterangan }}</p>
            @if ($kas->has_nota)
                <a href="{{ route('kas-kecil.nota', $kas->id) }}" target="_blank"
                    class="text-[9px] text-blue-500 font-bold uppercase hover:underline">
                    📂 Lihat Lampiran
                </a>
            @else
                <span class="text-[9px] text-slate-300 font-bold uppercase italic">Tanpa Nota</span>
            @endif
        </td>

        {{-- Debit --}}
        <td class="px-4 py-4 border-y border-slate-100 text-right text-sm font-black {{ $kas->debit > 0 ? 'text-emerald-600' : 'text-slate-300' }}">
            {{ $kas->debit > 0 ? number_format($kas->debit, 0, ',', '.') : '-' }}
        </td>

        {{-- Kredit --}}
        <td class="px-4 py-4 border-y border-slate-100 text-right text-sm font-black {{ $kas->kredit > 0 ? 'text-rose-600' : 'text-slate-300' }}">
            {{ $kas->kredit > 0 ? number_format($kas->kredit, 0, ',', '.') : '-' }}
        </td>

        {{-- Running Saldo --}}
        <td class="px-4 py-4 border-y border-slate-100 text-right text-sm font-black text-slate-800 italic">
            {{ number_format($runningSaldo, 0, ',', '.') }}
        </td>

        {{-- Status --}}
        <td class="px-4 py-4 border-y border-slate-100 text-center">
            @if($kas->status == 2)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-emerald-50 text-emerald-600 border border-emerald-100">
                    ✓ Approved
                </span>
            @else
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-50 text-amber-600 border border-amber-100">
                    Pending
                </span>
            @endif
        </td>

        {{-- Actions --}}
        <td class="px-4 py-4 rounded-r-2xl border-r border-y border-slate-100 text-center">
            @if($kas->status == 2)
                <span class="text-xs text-slate-400 italic">Locked</span>
            @else
                <div class="flex items-center justify-center gap-2 opacity-0 group-hover:opacity-100 transition-all transform group-hover:scale-100 scale-90">
                    {{-- Edit Button --}}
                    <button @click="editEntries([{{ $kas->id }}])"
                        title="Edit Transaksi"
                        class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </button>
                    {{-- Delete Button --}}
                    <button onclick="confirmDeleteKas({{ $kas->id }}, {{ $unit->id }})"
                        title="Hapus"
                        class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white transition">
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
        <td colspan="9" class="py-20 text-center">
            <div class="flex flex-col items-center opacity-20">
                <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <p class="font-black uppercase tracking-widest text-sm">Belum ada transaksi</p>
            </div>
        </td>
    </tr>
@endforelse
