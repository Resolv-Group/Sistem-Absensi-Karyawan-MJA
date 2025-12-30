@forelse($pkwtPekerja as $pkwt)
    <tr class="hover:bg-blue-50/30 transition-colors group cursor-pointer"
        @click="selectedItems.includes({{ $pkwt->id }}) ? selectedItems = selectedItems.filter(id => id !== {{ $pkwt->id }}) : selectedItems.push({{ $pkwt->id }})"
        :class="selectedItems.includes({{ $pkwt->id }}) ? 'bg-blue-50' : ''">

        <td class="pl-8 py-5">
            <input type="checkbox" value="{{ $pkwt->id }}" x-model.number="selectedItems" @click.stop
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-100">
        </td>

        <td class="px-4 py-5">
            <div class="flex items-center gap-4">
                {{-- Avatar Initials --}}
                <div
                    class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-black text-xs">
                    {{ strtoupper(substr($pkwt->pekerja->nama, 0, 2)) }}
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition">
                        {{ $pkwt->pekerja->nama }}</p>
                    <p class="text-xs font-mono text-gray-400">{{ $pkwt->pekerja->nik }}</p>
                </div>
            </div>
        </td>

        <td class="px-4 py-5">
            <span class="block text-sm font-bold text-gray-700">{{ $pkwt->jabatan->nama }}</span>
            <span class="text-xs text-gray-400">{{ $pkwt->divisi->nama }}</span>
        </td>

        <td class="px-4 py-5 text-center">
            <div class="inline-flex flex-col items-center">
                <span
                    class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($pkwt->tgl_mulai_pkwt)->format('d/m/Y') }}</span>
                <div class="h-4 w-px bg-gray-200 my-1"></div>
                <span
                    class="text-xs font-bold {{ $pkwt->status_pkwt['color'] === 'red' ? 'text-red-600' : 'text-blue-600' }}">
                    {{ \Carbon\Carbon::parse($pkwt->tgl_akhir_pkwt)->format('d M Y') }}
                </span>
            </div>
        </td>

        <td class="px-4 py-5 text-center">
            @if ($pkwt->status_aktif == 1)
                <span
                    class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-[10px] font-black uppercase tracking-widest">Aktif</span>
            @else
                <span
                    class="px-3 py-1 bg-gray-100 text-gray-400 rounded-full text-[10px] font-black uppercase tracking-widest">Nonaktif</span>
            @endif
        </td>

        <td class="pr-8 py-5 text-right">
            <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <a href="{{ route('view.ubah.unit-pekerja', ['unitId' => $unit->id, 'pekerjaId' => $pkwt->id]) }}"
                    @click.stop
                    class="p-2 bg-gray-100 text-gray-500 hover:bg-blue-600 hover:text-white rounded-xl transition">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                            stroke-width="2" />
                    </svg>
                </a>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="px-6 py-32 text-center bg-white">
            <div x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="flex flex-col items-center justify-center">

                @if(request()->anyFilled(['search', 'divisi', 'jabatan', 'status']))
                    {{-- SKENARIO A: FILTER AKTIF TAPI TIDAK ADA HASIL --}}
                    <div class="relative mb-10">
                        {{-- Blue Glow --}}
                        <div class="absolute inset-0 bg-blue-400 rounded-full blur-[50px] opacity-20 animate-pulse"></div>

                        {{-- Animated Icon Card (Blue) --}}
                        <div class="relative w-32 h-32 bg-gradient-to-br from-blue-500 to-blue-600 rounded-[2.5rem] shadow-2xl shadow-blue-200 flex items-center justify-center animate-float-harian-main">
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

                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">Pekerja Tidak Ditemukan</h3>
                    <p class="text-sm text-gray-500 max-w-[400px] mx-auto mt-3 leading-relaxed">
                        Kami tidak menemukan pekerja di unit <span class="font-bold text-gray-800">{{ $unit->nama_unit }}</span> yang sesuai dengan kriteria pencarian Anda.
                    </p>

                    <div class="mt-10">
                        <button type="button" @click="resetFilters()"
                            class="inline-flex items-center gap-2 px-8 py-3.5 bg-white border-2 border-gray-100 text-gray-700 text-sm font-black rounded-2xl hover:bg-gray-50 hover:border-blue-200 transition-all active:scale-95 shadow-sm">
                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset Semua Filter
                        </button>
                    </div>

                @else
                    {{-- SKENARIO B: DATABASE BENAR-BENAR KOSONG --}}
                    <div class="relative mb-10">
                        <div class="absolute inset-0 bg-gray-200 rounded-full blur-[50px] opacity-30"></div>

                        <div class="relative w-32 h-32 bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-200 flex items-center justify-center group hover:border-blue-400 transition-colors duration-500">
                            <svg class="w-16 h-16 text-gray-300 group-hover:text-blue-200 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>

                    <h3 class="text-2xl font-black text-gray-400 tracking-tight">Belum Ada Data Pekerja</h3>
                    <p class="text-sm text-gray-400 max-w-[350px] mx-auto mt-3 leading-relaxed">
                        Daftar pekerja harian (PKWT) untuk unit ini masih kosong. Daftarkan pekerja pertama Anda sekarang.
                    </p>

                    <div class="mt-10">
                        <a href="{{ route('view.tambah.unit-pekerja', $unit->id) }}"
                            class="inline-flex items-center gap-3 px-10 py-4 bg-blue-600 text-white text-sm font-black rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all hover:-translate-y-1 active:scale-95">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Daftarkan Pekerja Baru
                        </a>
                    </div>
                @endif
            </div>
        </td>
    </tr>
@endforelse
