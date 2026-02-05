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
                    <p class="text-xs font-mono text-gray-400">NIK: {{ $pkwt->pekerja->nik }}</p>
                </div>
            </div>
        </td>

        <td class="px-4 py-5" x-data="{ openPenilaian: false }">
            <div class="flex flex-col gap-2 min-w-[240px]">
                @php
                    $penilaians = $pkwt->penilaian->sortByDesc('updated_at')->take(3);
                    $first = $penilaians->first();
                    $count = $penilaians->count();

                    // Helper Fungsi untuk Grade & Warna (Premium Palette)
                    $getGradeInfo = function ($score) {
                        if ($score >= 50) {
                            return [
                                'label' => 'A',
                                'class' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                'accent' => 'bg-emerald-500',
                            ];
                        } elseif ($score >= 41) {
                            return [
                                'label' => 'B',
                                'class' => 'bg-blue-50 text-blue-600 border-blue-100',
                                'accent' => 'bg-blue-500',
                            ];
                        } elseif ($score >= 29) {
                            return [
                                'label' => 'C',
                                'class' => 'bg-amber-50 text-amber-600 border-amber-100',
                                'accent' => 'bg-amber-500',
                            ];
                        } else {
                            return [
                                'label' => 'D',
                                'class' => 'bg-red-50 text-red-600 border-red-100',
                                'accent' => 'bg-red-500',
                            ];
                        }
                    };
                @endphp

                @if ($first)
                    @php $grade = $getGradeInfo($first->total); @endphp

                    {{-- Check if HRD has reviewed; if yes, use a DIV (locked), if no, use an A tag (editable) --}}
                    @if ($first->status_hrd > 0 && $first->status_staff > 0)
                        {{-- LOCKED VERSION: Cannot click, no hover effects --}}
                        <div
                            class="flex items-center justify-between bg-gray-50/50 border border-gray-100 rounded-[1.25rem] p-4 relative overflow-hidden cursor-not-allowed">
                            {{-- Left Accent Line (Static opacity since it's locked) --}}
                            <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $grade['accent'] }} opacity-40"></div>
                        @else
                            {{-- EDITABLE VERSION: Original Link --}}
                            <a href="{{ route('view.ubah.penilaian', ['penilaianId' => $first->id, 'unitId' => $pkwt->id_unit, 'pekerjaId' => $first->id_pekerja]) }}"
                                @click.stop
                                class="group/item flex items-center justify-between bg-white border border-gray-100 rounded-[1.25rem] p-4 shadow-sm hover:border-blue-300 hover:shadow-md transition-all relative overflow-hidden">

                                {{-- Left Accent Line --}}
                                <div
                                    class="absolute left-0 top-0 bottom-0 w-1.5 {{ $grade['accent'] }} opacity-20 group-hover/item:opacity-100 transition-opacity">
                                </div>
                    @endif

                    {{-- SHARED CONTENT START --}}
                    <div class="pl-2">
                        <div class="flex items-center gap-2">
                            <p class="text-[11px] font-black text-gray-400 uppercase tracking-widest leading-none">
                                Evaluasi Terakhir</p>

                            @if ($first->status_hrd > 0 && $first->status_staff > 0)
                                <div
                                    class="flex items-center gap-1 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100">
                                    <svg class="w-2.5 h-2.5 text-emerald-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span class="text-[10px] font-black text-emerald-600 uppercase">Sudah
                                        Ditinjau</span>
                                </div>
                                {{-- Small Lock Icon to indicate it's read-only --}}
                                <svg class="w-3 h-3 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            @else
                                <div
                                    class="flex items-center gap-1 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-100">
                                    <span class="w-1 h-1 rounded-full bg-gray-300 animate-pulse"></span>
                                    <span class="text-[10px] font-black text-gray-400 uppercase">Belum Ditinjau</span>
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span
                                class="text-lg font-black text-gray-900 tracking-tighter">{{ number_format($first->total, 0) }}</span>
                            <span class="text-[10px] font-bold text-gray-400 uppercase">Poin</span>
                        </div>
                    </div>

                    {{-- Grade Badge --}}
                    <div
                        class="w-10 h-10 rounded-xl border {{ $grade['class'] }} flex items-center justify-center font-black text-lg shadow-sm">
                        {{ $grade['label'] }}
                    </div>
                    {{-- SHARED CONTENT END --}}

                    @if ($first->status_hrd > 0 && $first->status_staff > 0)
            </div> {{-- Close DIV --}}
        @else
            </a> {{-- Close A --}}
@endif

{{-- DROPDOWN ITEM LAINNYA --}}
@if ($count > 1)
    <div class="relative">
        <button @click.stop="openPenilaian = !openPenilaian"
            class="w-full flex items-center justify-between px-4 py-2.5 bg-gray-50/50 hover:bg-blue-50 rounded-xl transition-all group/btn border border-transparent hover:border-blue-100">
            <div class="flex items-center gap-2">
                <div class="flex -space-x-2">
                    @foreach ($penilaians->skip(1)->take(3) as $p)
                        <div
                            class="w-5 h-5 rounded-full border-2 border-white {{ $getGradeInfo($p->total)['accent'] }} opacity-60">
                        </div>
                    @endforeach
                </div>
                <span class="text-xs font-bold text-gray-500 group-hover/btn:text-blue-600">
                    +{{ $count - 1 }} Riwayat Lainnya
                </span>
            </div>
            <svg class="w-4 h-4 text-gray-400 group-hover/btn:text-blue-500 transition-transform"
                :class="openPenilaian ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        {{-- LIST DROPDOWN --}}
        <div x-show="openPenilaian" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0" @click.outside="openPenilaian = false"
            class="absolute z-30 top-full mt-2 w-full bg-white border border-gray-100 rounded-[1.5rem] shadow-2xl p-2 space-y-1"
            x-cloak>

            @foreach ($penilaians->skip(1) as $p)
                @php $subGrade = $getGradeInfo($p->total); @endphp
                <a href="{{ route('view.ubah.penilaian', ['penilaianId' => $p->id, 'unitId' => $pkwt->id_unit, 'pekerjaId' => $first->id_pekerja]) }}"
                    @click.stop
                    class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-xl transition-colors group/sub">
                    <div class="flex items-center gap-3">
                        <div class="w-1.5 h-8 {{ $subGrade['accent'] }} rounded-full"></div>
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">
                                    Evaluasi Ke {{ $loop->iteration + 1 }}</p>
                                {{-- STATUS HRD: Card Utama --}}
                                @if ($first->status_hrd == 1)
                                    <div
                                        class="flex items-center gap-1 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100">
                                        <svg class="w-2.5 h-2.5 text-emerald-500" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M5 13l4 4L19 7" />
                                        </svg>
                                        <span class="text-[10px] font-black text-emerald-600 uppercase">Sudah
                                            Ditinjau</span>
                                    </div>
                                @else
                                    <div
                                        class="flex items-center gap-1 bg-gray-50 px-1.5 py-0.5 rounded border border-gray-100">
                                        <span class="w-1 h-1 rounded-full bg-gray-300 animate-pulse"></span>
                                        <span class="text-[10px] font-black text-gray-400 uppercase">Belum
                                            Ditinjau</span>
                                    </div>
                                @endif
                            </div>
                            <p class="text-sm font-bold text-gray-700 mt-1">{{ $p->total }}
                                Poin</p>
                        </div>
                    </div>
                    <div class="px-2.5 py-1 rounded-lg border {{ $subGrade['class'] }} font-black text-xs">
                        {{ $subGrade['label'] }}
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif
@else
{{-- STATE KOSONG --}}
<div
    class="group flex items-center gap-3 py-3 px-4 border-2 border-dashed border-gray-100 rounded-[1.25rem] hover:border-blue-200 transition-colors">
    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center">
        <svg class="w-4 h-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
    </div>
    <span class="text-[11px] font-black text-gray-300 uppercase tracking-widest">Belum Ada
        Penilaian</span>
</div>
@endif
</div>
</td>

{{-- 3. KOLOM: URGENSI NILAI (Indikator Kebutuhan Penilaian) --}}
<td class="px-4 py-5 text-center">
    @if ($pkwt->penilaian->isEmpty())
        {{-- Jika belum ada penilaian sama sekali --}}
        <span
            class="inline-flex items-center gap-1.5 px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-[12px] font-black uppercase tracking-widest border border-orange-100 shadow-sm">
            <span class="relative flex h-2 w-2">
                <span
                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
            </span>
            Butuh Penilaian
        </span>
    @else
        {{-- Jika sudah dinilai --}}
        <span
            class="inline-flex items-center gap-1 px-3 py-1 bg-emerald-50 text-emerald-600 rounded-full text-[12px] font-black uppercase tracking-widest border border-emerald-100">
            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
            </svg>
            Selesai
        </span>
    @endif
</td>

<td class="px-4 py-5 text-center">
    <div class="inline-flex flex-col items-center">
        <span
            class="text-[12px] text-gray-400">{{ \Carbon\Carbon::parse($pkwt->tgl_mulai_pkwt)->format('d/m/Y') }}</span>
        <div class="h-4 w-px bg-gray-200 my-1"></div>
        <span
            class="text-[14px] font-bold {{ $pkwt->status_pkwt['color'] === 'red' ? 'text-red-600' : 'text-blue-600' }}">
            {{ \Carbon\Carbon::parse($pkwt->tgl_akhir_pkwt)->format('d M Y') }}
        </span>
    </div>
</td>
</tr>
@empty
<tr>
    <td colspan="6" class="px-6 py-32 text-center bg-white">
        <div x-transition:enter="transition ease-out duration-500" x-transition:enter-start="opacity-0 translate-y-8"
            x-transition:enter-end="opacity-100 translate-y-0" class="flex flex-col items-center justify-center">

            @if (request()->anyFilled(['search', 'divisi', 'jabatan', 'status']))
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
                    Kami tidak menemukan pekerja di unit<span class="font-bold text-gray-800"> {{ $unit->nama_unit }}
                    </span>yang sesuai dengan kriteria
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
                {{-- SKENARIO B: DATABASE BENAR-BENAR KOSONG --}}
                <div class="relative mb-10">
                    <div class="absolute inset-0 bg-gray-200 rounded-full blur-[50px] opacity-30"></div>

                    <div
                        class="relative w-32 h-32 bg-gray-50 rounded-[2.5rem] border-2 border-dashed border-gray-200 flex items-center justify-center group hover:border-blue-400 transition-colors duration-500">
                        <svg class="w-16 h-16 text-gray-300 group-hover:text-blue-200 transition-colors"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>

                <h3 class="text-2xl font-black text-gray-400 tracking-tight">Belum Ada Pekerja Untuk Dinilai</h3>
                <p class="text-sm text-gray-400 max-w-[350px] mx-auto mt-3 leading-relaxed">
                    Daftar penilaian pekerja untuk unit ini masih kosong...
                </p>
            @endif
        </div>
    </td>
</tr>
@endforelse
