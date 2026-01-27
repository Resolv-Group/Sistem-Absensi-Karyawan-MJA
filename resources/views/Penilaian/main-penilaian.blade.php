@extends('layout')

@section('content')
    <style>
        @keyframes float-harian-main {
            0%, 100% { transform: translateY(0px) rotate(3deg); }
            50% { transform: translateY(-12px) rotate(6deg); }
        }
        .animate-float-harian-main { animation: float-harian-main 4s ease-in-out infinite; }
    </style>

    <div x-data="{
        selectedItems: [],
        showFilterDropdown: false,
        showStatusModal: false,
        searchQuery: '',
        statusValue: '1',
        hrdStatuses: {{ json_encode($pkwtPekerja->mapWithKeys(fn($item) => [$item->id => ($item->penilaian->first()->status_hrd ?? 0)])) }},

        // Track the current page number
        currentPage: {{ $pkwtPekerja->currentPage() }},

        allIds: {{ json_encode($pkwtPekerja->pluck('id')) }},
        alreadyAssessed: @js($alreadyAssessedIds),

        get hasSelectedAssessed() {
            return this.selectedItems.some(id => this.alreadyAssessed.includes(id));
        },
        get isSelectionClean() {
            return this.selectedItems.length > 0 && this.selectedItems.every(id => !this.alreadyAssessed.includes(id));
        },

        toggleAll() {
            this.selectedItems = this.selectedItems.length === this.allIds.length ? [] : [...this.allIds];
        },

        async updateTable(targetUrl = null) {
            let url;

            if (targetUrl) {
                // If called from Pagination Link
                url = new URL(targetUrl);
                // If no search is active, save this as our current 'base' page
                if (!this.searchQuery) {
                    this.currentPage = url.searchParams.get('page') || 1;
                }
            } else {
                // If called from Typing Search/Filter
                url = new URL(window.location.href);

                // Logic: If user is typing, we force page 1 to find results.
                // If user cleared everything, we restore the saved currentPage.
                if (!this.searchQuery ) {
                    url.searchParams.set('page', this.currentPage);
                } else {
                    url.searchParams.set('page', '1');
                }
            }

            // Apply all filters to the URL
            url.searchParams.set('search', this.searchQuery);

            try {
                const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const html = await response.text();

                document.getElementById('main-table-body').innerHTML = html;

                // Update the pagination links at the bottom
                const newPagination = document.getElementById('new-pagination-provider');
                const paginationContainer = document.getElementById('search-pagination');
                if (newPagination && paginationContainer) {
                    paginationContainer.innerHTML = newPagination.innerHTML;
                }

                // Sync IDs for Bulk Actions
                const provider = document.getElementById('new-ids-provider-full');
                if (provider) this.allIds = JSON.parse(provider.dataset.ids);
            } catch (error) { console.error(error); }
        },

        resetFilters() {
            this.searchQuery = '';
            this.filterStatus = '';
            // This will trigger the $watch which calls updateTable()
            // Our logic inside updateTable will see filters are empty and restore Page 2
        },

    }" x-init="$watch('searchQuery', () => updateTable());">

        {{-- HEADER SECTION --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
            <div
                class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-gray-100 relative overflow-hidden">

                {{-- Surprise Element: Background Pattern Decoration --}}
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-blue-50 rounded-full blur-3xl opacity-40">
                </div>
                <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 bg-emerald-50 rounded-full blur-3xl opacity-40">
                </div>

                <div class="relative z-10">
                    {{-- Top Row: Breadcrumb & Meta --}}
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <div
                            class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-blue-600 transition group">
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="flex h-2 w-2 relative">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                            </span>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Penilaian PKWT</span>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                        {{-- Left Side: Identity & Branding --}}
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="h-14 w-2 bg-blue-600 rounded-full shadow-[0_0_20px_rgba(37,99,235,0.4)]"></div>
                                <div>
                                    <h1 class="text-5xl font-black text-gray-900 tracking-tight leading-none">
                                        Penilaian PKWT<span class="text-blue-600">.</span>
                                    </h1>
                                    <div class="flex items-center gap-3 mt-3">
                                        <div
                                            class="px-3 py-1 bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-sm">
                                            {{ $unit->namaMitra->nama_mitra ?? 'Mitra Perusahaan' }}
                                        </div>
                                        <div
                                            class="px-3 py-1 bg-blue-50 text-blue-700 text-[10px] font-black uppercase tracking-widest rounded-lg border border-blue-100 italic">
                                            Sistem {{ $unit->sistem_pengajian == 1 ? 'Harian' : 'Borongan' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p class="text-base text-gray-500 flex items-center gap-2 ml-6">
                                Unit Kerja:
                                <span
                                    class="font-bold text-gray-800 italic underline decoration-blue-200 underline-offset-8 decoration-2">
                                    {{ $unit->nama_unit }}
                                </span>
                            </p>
                        </div>

                        {{-- Right Side: Grid Stats Cards --}}
                        <div class="grid grid-cols-2 sm:grid-cols-2 gap-3">

                            {{-- Total Card --}}
                            <div
                                class="bg-gray-50/50 border border-gray-100 rounded-3xl p-5 min-w-[140px] hover:bg-white hover:shadow-xl hover:shadow-blue-900/5 transition-all duration-300 group">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total</p>
                                <div class="flex items-end gap-1">
                                    <span
                                        class="text-3xl font-black text-gray-900 leading-none">{{ $pkwtPekerja->total() }}</span>
                                    <span class="text-[10px] font-bold text-gray-400 mb-1">Pekerja</span>
                                </div>
                            </div>

                            {{-- Active Card --}}
                            <div
                                class="bg-gray-50/50 border border-gray-100 rounded-3xl p-5 min-w-[140px] hover:bg-white hover:shadow-xl hover:shadow-emerald-900/5 transition-all duration-300 group">
                                <p
                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 text-emerald-500">
                                    Ternilai</p>
                                <div class="flex items-end gap-1">
                                    <span
                                        class="text-3xl font-black text-emerald-600 leading-none">{{ $unit->pkwt()
                                            ->where('status_aktif', 1)
                                            ->whereHas('penilaian', function ($q) use ($unit) {
                                                $q->where('id_unit', $unit->id);
                                            })
                                            ->count()
                                        }}</span>
                                    <svg class="w-4 h-4 text-emerald-400 mb-1" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 mb-8">
            <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-200">

                {{-- TOOLBAR --}}
                <div
                    class="px-6 py-6 rounded-3xl border-b border-gray-100 flex flex-col md:flex-row justify-between gap-4 bg-white">
                    <div class="flex items-center gap-4 flex-1">
                        <div class="relative w-full max-w-md group">
                            <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 group-focus-within:text-blue-500 transition"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" x-model.debounce.500ms="searchQuery"
                                placeholder="Cari nama atau NIK pekerja..."
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-100 focus:bg-white transition text-sm">
                        </div>

                        <div class="relative">
                            {{-- Dropdown Filter UI (Gunakan yang sudah kita buat sebelumnya di sini) --}}
                            <div x-show="selectedItems.length > 0" x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-10"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-10"
                                class="fixed bottom-8 left-1/2 -translate-x-1/2 z-40 w-[95%] max-w-3xl"x-cloak>

                                <div
                                    class="bg-white/80 backdrop-blur-md border border-blue-100 shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-2xl px-5 py-3 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="relative flex items-center justify-center bg-blue-600 text-white text-[11px] font-black h-6 w-6 rounded-full shadow-sm"
                                            x-text="selectedItems.length"></span>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900 leading-none">Pekerja
                                                Dipilih</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <button type="button" @click="selectedItems = []"
                                            class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-700 transition">Batal</button>
                                        <div class="h-6 w-px bg-gray-200 mx-1"></div>

                                        {{-- 1. TOMBOL BUAT PENILAIAN (Hanya muncul jika SEMUA pilihan belum dinilai) --}}
                                        <template x-if="isSelectionClean">
                                            <button type="button"
                                                @click="window.location.href = '{{ route('view.buat.penilaian', $unit->id) }}?ids=' + selectedItems.join(',')"
                                                class="flex items-center gap-1.5 px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Buat Penilaian Baru
                                            </button>
                                        </template>

                                        {{-- 2. PESAN PERINGATAN (Muncul jika ada campuran atau semua sudah dinilai) --}}
                                        <template x-if="selectedItems.length > 0 && hasSelectedAssessed">
                                            <div class="flex items-center gap-3 px-4 py-2 bg-orange-50 border border-orange-100 rounded-xl shadow-sm">
                                                {{-- Animated Icon --}}
                                                <div class="flex-shrink-0 relative flex h-2 w-2">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                                                </div>

                                                <div class="flex flex-col">
                                                    <span class="text-[10px] font-black text-orange-700 uppercase tracking-tight">Pilihan Tidak Valid</span>
                                                    <p class="text-[9px] font-bold text-orange-500 leading-none mt-0.5">
                                                        Beberapa pekerja yang Anda pilih sudah memiliki data penilaian.
                                                    </p>
                                                </div>

                                                {{-- Action to fix --}}
                                                <button @click="selectedItems = selectedItems.filter(id => !alreadyAssessed.includes(id))"
                                                    class="ml-2 px-2 py-1 bg-white border border-orange-200 text-[9px] font-black text-orange-600 rounded-lg hover:bg-orange-600 hover:text-white transition-all uppercase">
                                                    batalkan pilihan yang sudah dinilai
                                                </button>
                                            </div>
                                        </template>

                                        {{-- Status Update Form --}}
                                        <form action="{{ route('export.excel', $unit->id) }}"
                                            method="POST"
                                            class="inline"
                                            {{-- Tombol hanya muncul jika:
                                                1. Ada item yang dipilih (length > 0)
                                                2. SEMUA id di selectedItems memiliki status_hrd == 1 --}}
                                            x-show="selectedItems.length > 0 && selectedItems.every(id => hrdStatuses[id] == 1)"
                                            x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 scale-95"
                                            x-transition:enter-end="opacity-100 scale-100"
                                            x-cloak>

                                            @csrf
                                            <input type="hidden" name="worker_ids" :value="JSON.stringify(selectedItems)">

                                            <button type="submit"
                                                class="flex items-center gap-1.5 px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-xl text-xs font-bold hover:bg-emerald-600 hover:text-white transition-all shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                Stream Excel
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            {{-- MODAL: Change Status --}}
                            <div x-show="showStatusModal"
                                class="fixed inset-0 z-[30] flex items-center justify-center p-4 sm:p-6" x-cloak>
                                {{-- Backdrop --}}
                                <div x-show="showStatusModal" x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0" @click="showStatusModal = false"
                                    class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>

                                {{-- Modal Content --}}
                                <div x-show="showStatusModal" x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave="ease-in duration-200"
                                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                                    class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

                                    {{-- Header --}}
                                    <div
                                        class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                                        <h3 class="text-lg font-bold text-gray-900">Ubah Status Pekerja</h3>
                                        <button @click="showStatusModal = false"
                                            class="text-gray-400 hover:text-gray-600 transition">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <form action="{{ route('bulk.update.pekerja') }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="ids" :value="JSON.stringify(selectedItems)">
                                        <input type="hidden" name="action" value="update_status">

                                        <div class="p-6">
                                            {{-- Info Text --}}
                                            <div class="mb-6">
                                                <p class="text-sm text-gray-600">
                                                    Anda akan mengubah status untuk
                                                    <span class="font-bold text-blue-600"
                                                        x-text="selectedItems.length"></span> pekerja.
                                                    Tindakan ini akan berdampak pada akses dan keaktifan pekerja.
                                                </p>
                                            </div>

                                            {{-- Status Selection (Styled as Cards) --}}
                                            <div class="grid grid-cols-2 gap-3 mb-6">
                                                <label
                                                    class="relative flex flex-col p-4 border rounded-xl cursor-pointer focus:outline-none transition-all"
                                                    :class="statusValue === '1' ?
                                                        'border-emerald-500 bg-emerald-50/50 ring-1 ring-emerald-500' :
                                                        'border-gray-100 hover:bg-gray-50'">
                                                    <input type="radio" name="status" value="1"
                                                        x-model="statusValue" class="sr-only">
                                                    <span class="flex items-center gap-2 mb-1">
                                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                                        <span class="text-sm font-bold"
                                                            :class="statusValue === '1' ? 'text-emerald-700' :
                                                                'text-gray-700'">Aktif</span>
                                                    </span>
                                                    <span class="text-[10px] text-gray-500">Pekerja aktif dalam
                                                        sistem</span>
                                                </label>

                                                <label
                                                    class="relative flex flex-col p-4 border rounded-xl cursor-pointer focus:outline-none transition-all"
                                                    :class="statusValue === '0' ?
                                                        'border-gray-400 bg-gray-50 ring-1 ring-gray-400' :
                                                        'border-gray-100 hover:bg-gray-50'">
                                                    <input type="radio" name="status" value="0"
                                                        x-model="statusValue" class="sr-only">
                                                    <span class="flex items-center gap-2 mb-1">
                                                        <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                                        <span class="text-sm font-bold"
                                                            :class="statusValue === '0' ? 'text-gray-900' :
                                                                'text-gray-700'">Nonaktif</span>
                                                    </span>
                                                    <span class="text-[10px] text-gray-500">Nonaktifkan akses
                                                        pekerja</span>
                                                </label>
                                            </div>
                                        </div>

                                        {{-- Footer Buttons --}}
                                        <div class="px-6 py-4 bg-gray-50 flex items-center justify-end gap-3">
                                            <button type="button" @click="showStatusModal = false"
                                                class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700 transition">
                                                Batal
                                            </button>
                                            <button type="submit"
                                                class="px-6 py-2 bg-blue-600 text-white text-sm font-bold rounded-xl hover:bg-blue-700 shadow-md shadow-blue-100 transition">
                                                Simpan Perubahan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ asset('panduan/INDIKATOR PENILAIAN KINERJA.xls') }}"
                        download target="_blank"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3
                                bg-emerald-600 text-white font-bold rounded-2xl
                                hover:bg-emerald-700 shadow-lg shadow-emerald-100
                                transition active:scale-95">

                        <!-- Download Icon -->
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v12m0 0l4-4m-4 4l-4-4M4 17h16" />
                        </svg>

                            Unduh Panduan Penilaian
                    </a>

                </div>

                {{-- TABLE --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50 border-b border-gray-100">
                            <tr>
                                <th class="pl-8 py-4 w-10"><input type="checkbox" @click="toggleAll()"
                                        :checked="selectedItems.length === allIds.length && allIds.length > 0"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-100"></th>
                                <th class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest">
                                    Pekerja
                                </th>
                                <th class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest">
                                    Hasil Evaluasi</th>
                                <th
                                    class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    Urgensi Nilai</th>
                                <th
                                    class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    Masa Kontrak</th>
                            </tr>
                        </thead>
                        <tbody id="main-table-body" class="divide-y divide-gray-50">
                            @include('Penilaian.partials.main-penilaian-table')
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                <div id="new-ids-provider-full" data-ids="{{ json_encode($pkwtPekerja->pluck('id')) }}" class="hidden">
                </div>

                <div id="new-pagination-provider" class="rounded-3xl">
                    @if ($pkwtPekerja->hasPages())
                        {{ $pkwtPekerja->links('vendor.pagination.custom') }}
                    @endif
                </div>
            </div>
        </div>

        {{-- MODALS & FLOATING BAR --}}

    </div>

    <style>
        [x-cloak] {
            display: none !important;
        }

        /* Simpan CSS floating animation Anda di sini */
    </style>
@endsection

@section('scripts')
    <script>
        document.addEventListener("click", function(e) {
            // Find the closest anchor tag inside the pagination container
            const anchor = e.target.closest("#search-pagination a");

            if (anchor) {
                e.preventDefault();

                // Get the Alpine instance
                const alpineElement = document.querySelector('[x-data]');
                if (alpineElement) {
                    const alpineData = Alpine.$data(alpineElement);
                    // Call updateTable with the URL from the clicked page link
                    alpineData.updateTable(anchor.href);
                }
            }
        });
    </script>
@endsection
