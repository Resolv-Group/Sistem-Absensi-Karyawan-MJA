{{-- SECTION: HISTORI PENILAIAN --}}
<div x-data="{
    showModal: false,
    selected: {},
    getGrade(score) {
        if (score >= 50) return { label: 'A', color: 'emerald', desc: 'Sangat Baik' };
        if (score >= 41) return { label: 'B', color: 'blue', desc: 'Baik' };
        if (score >= 29) return { label: 'C', color: 'amber', desc: 'Cukup' };
        return { label: 'D', color: 'red', desc: 'Kurang' };
    },
    openDetail(item) {
        this.selected = item;
        this.showModal = true;
    }
}">
    <div class="flex items-center gap-4 mb-6">
        <h3 class="text-lg font-bold text-gray-900 whitespace-nowrap">Histori Penilaian PKWT</h3>
        <div class="h-px bg-gray-200 w-full"></div>
    </div>

    <div class="grid grid-cols-1 gap-3">
        @forelse($historiPenilaian as $n)
            <div
                class="flex items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl hover:shadow-lg hover:shadow-gray-200/40 transition-all group">
                <div class="flex items-center gap-4">
                    {{-- Score & Grade Badge --}}
                    <div class="relative flex-shrink-0">
                        <div
                            class="w-14 h-14 rounded-2xl flex flex-col items-center justify-center border-2 border-gray-50 bg-white shadow-sm group-hover:border-emerald-100 transition-colors">
                            <span class="text-[8px] font-black text-gray-400 uppercase tracking-tighter">Total</span>
                            <span class="text-lg font-black text-gray-900 leading-none">{{ $n->total }}</span>
                        </div>
                        {{-- Grade Overlay --}}
                        <div :class="getGrade({{ $n->total }}).color === 'emerald' ? 'bg-emerald-500' :
                            getGrade({{ $n->total }}).color === 'blue' ? 'bg-blue-500' :
                            getGrade({{ $n->total }}).color === 'amber' ? 'bg-amber-500' : 'bg-red-500'"
                            class="absolute -top-1 -right-1 w-6 h-6 rounded-lg border-2 border-white flex items-center justify-center shadow-sm">
                            <span class="text-[10px] font-black text-white"
                                x-text="getGrade({{ $n->total }}).label"></span>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-black text-gray-900 leading-tight">Evaluasi Kinerja Berkala</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                {{ $n->created_at->translatedFormat('d M Y') }}
                            </span>
                            <span class="text-gray-200">|</span>
                            <span :class="'text-' + getGrade({{ $n->total }}).color + '-600'"
                                class="text-[10px] font-black uppercase italic"
                                x-text="getGrade({{ $n->total }}).desc"></span>
                        </div>
                    </div>
                </div>

                <button @click="openDetail({{ $n->toJson() }})"
                    class="px-5 py-2.5 bg-gray-50 text-gray-600 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-gray-900 hover:text-white transition-all active:scale-95">
                    View
                </button>
            </div>
        @empty
            <div class="py-12 text-center border-2 border-dashed border-gray-100 rounded-[2rem]">
                <p class="text-sm font-bold text-gray-400 italic">Belum ada data penilaian.</p>
            </div>
        @endforelse
    </div>

    {{-- MODAL DETAIL --}}
    <div x-show="showModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak>
        <div x-show="showModal" x-transition.opacity @click="showModal = false"
            class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>

        <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-8"
            class="relative w-full max-w-lg bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-white">

            <div class="p-10">
                {{-- Header with Large Grade --}}
                <div class="flex items-center justify-between mb-10">
                    <div class="flex items-center gap-4">
                        <div :class="'bg-' + getGrade(selected.total).color + '-600'"
                            class="w-16 h-16 rounded-[1.5rem] flex items-center justify-center text-white shadow-xl transform -rotate-3">
                            <span class="text-3xl font-black" x-text="getGrade(selected.total).label"></span>
                        </div>
                        <div>
                            <h4 class="text-2xl font-black text-gray-900 tracking-tighter leading-none">Hasil Evaluasi
                            </h4>
                            <p class="text-xs font-bold text-gray-400 mt-2 uppercase tracking-[0.2em]"
                                x-text="getGrade(selected.total).desc"></p>
                        </div>
                    </div>
                    <button @click="showModal = false"
                        class="p-3 bg-gray-50 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-2xl transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M6 18L18 6M6 6l12 12" stroke-width="3" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>

                {{-- Metrics Grid (Horizontal Scrolling on mobile, Grid on desktop) --}}
                <div class="grid grid-cols-5 gap-2 mb-10">
                    <template
                        x-for="field in [
                        {key: 'mk', label: 'MK'},
                        {key: 'absensi', label: 'ABS'},
                        {key: 'pengetahuan', label: 'PNG'},
                        {key: 'kualitas', label: 'KLT'},
                        {key: 'sikap', label: 'SKP'}
                    ]">
                        <div class="text-center p-3 bg-gray-50/50 rounded-2xl border border-gray-100">
                            <p class="text-[8px] font-black text-gray-400 uppercase mb-1.5" x-text="field.label"></p>
                            <p class="text-sm font-black text-gray-900" x-text="selected[field.key]"></p>
                        </div>
                    </template>
                </div>
                {{-- Summary & Verification Section --}}
                <div class="space-y-6 mb-10">

                    {{-- 1. Hero Score Card --}}
                    <div class="relative overflow-hidden bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-2xl">
                        {{-- Background Pattern Decoration --}}
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16 blur-2xl">
                        </div>

                        <div class="relative flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-1">Skor
                                    Akumulasi</p>
                                <div class="flex items-baseline gap-2">
                                    <span class="text-6xl font-black tracking-tighter" x-text="selected.total"></span>
                                    <span class="text-xl font-bold text-gray-500">/ 56</span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-2">Predikat
                                </p>
                                <div :class="'bg-' + getGrade(selected.total).color + '-500'"
                                    class="px-4 py-2 rounded-2xl inline-block shadow-lg shadow-black/20">
                                    <span class="text-2xl font-black" x-text="getGrade(selected.total).label"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Verification Status Row --}}
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Staff Verification --}}
                        <div class="p-4 rounded-[1.5rem] border border-gray-100 transition-all"
                            :class="selected.status_staff ? 'bg-emerald-50/50 border-emerald-100' : 'bg-gray-50/50'">
                            <div class="flex items-center gap-3">
                                <div :class="selected.status_staff ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-400'"
                                    class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors">
                                    <svg x-show="selected.status_staff" class="w-5 h-5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg x-show="!selected.status_staff" class="w-4 h-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 8v4l3 3" />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-none mb-1">
                                        Staff Verify</p>
                                    <p class="text-[11px] font-bold"
                                        :class="selected.status_staff ? 'text-emerald-700' : 'text-gray-400'"
                                        x-text="selected.status_staff ? 'Verified' : 'Pending'"></p>
                                </div>
                            </div>
                        </div>

                        {{-- HRD Verification --}}
                        <div class="p-4 rounded-[1.5rem] border border-gray-100 transition-all"
                            :class="selected.status_hrd ? 'bg-emerald-50/50 border-emerald-100' : 'bg-gray-50/50'">
                            <div class="flex items-center gap-3">
                                <div :class="selected.status_hrd ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-400'"
                                    class="w-8 h-8 rounded-xl flex items-center justify-center transition-colors">
                                    <svg x-show="selected.status_hrd" class="w-5 h-5" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    <svg x-show="!selected.status_hrd" class="w-4 h-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 8v4l3 3" />
                                    </svg>
                                </div>
                                <div>
                                    <p
                                        class="text-[9px] font-black uppercase tracking-widest text-gray-400 leading-none mb-1">
                                        HRD Verify</p>
                                    <p class="text-[11px] font-bold"
                                        :class="selected.status_hrd ? 'text-emerald-700' : 'text-gray-400'"
                                        x-text="selected.status_hrd ? 'Verified' : 'Pending'"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Comments Box --}}
                    <div class="p-6 bg-blue-50/50 border border-blue-100/50 rounded-[2rem]">
                        <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-3">Catatan Penilai
                        </p>
                        <p class="text-[13px] text-blue-900 font-medium italic leading-relaxed"
                            x-text="selected.keterangan || 'Tidak ada catatan khusus untuk periode ini.'"></p>
                    </div>
                </div>


                <button @click="showModal = false"
                    class="w-full py-5 bg-white border-2 border-gray-100 text-gray-900 text-xs font-black uppercase tracking-[0.2em] rounded-[1.5rem] hover:bg-gray-50 hover:border-gray-200 transition-all">
                    Tutup Rincian
                </button>
            </div>
        </div>
    </div>
</div>
