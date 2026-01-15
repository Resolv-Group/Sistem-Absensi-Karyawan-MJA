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
        filterStatus: '',
        statusValue: '1',

        // Track the current page number
        currentPage: {{ $pkwtPekerja->currentPage() }},

        allIds: {{ json_encode($pkwtPekerja->pluck('id')) }},

        toggleAll() {
            this.selectedItems = this.selectedItems.length === this.allIds.length ? [] : [...this.allIds];
        },

        async updateTable(targetUrl = null) {
            let url;

            if (targetUrl) {
                // If called from Pagination Link
                url = new URL(targetUrl);
                // If no search is active, save this as our current 'base' page
                if (!this.searchQuery && !this.filterDivisi && !this.filterJabatan && !this.filterStatus) {
                    this.currentPage = url.searchParams.get('page') || 1;
                }
            } else {
                // If called from Typing Search/Filter
                url = new URL(window.location.href);

                // Logic: If user is typing, we force page 1 to find results.
                // If user cleared everything, we restore the saved currentPage.
                if (!this.searchQuery && !this.filterDivisi && !this.filterJabatan && !this.filterStatus) {
                    url.searchParams.set('page', this.currentPage);
                } else {
                    url.searchParams.set('page', '1');
                }
            }

            // Apply all filters to the URL
            url.searchParams.set('search', this.searchQuery);
            url.searchParams.set('status', this.filterStatus);

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

    }" x-init="$watch('searchQuery', () => updateTable());
    $watch('filterStatus', () => updateTable());">

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
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">

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
                                        class="text-3xl font-black text-emerald-600 leading-none">{{ $unit->pkwt()->where('status_aktif', 1)->count() }}</span>
                                    <svg class="w-4 h-4 text-emerald-400 mb-1" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </div>

                            {{-- NEW WORKERS CARD (Surprise) --}}
                            <div
                                class="bg-gray-50/50 border border-gray-100 rounded-3xl p-5 min-w-[140px] hover:bg-white hover:shadow-xl hover:shadow-purple-900/5 transition-all duration-300 group">
                                @php
                                    $newCount = $unit
                                        ->pkwt()
                                        ->where('created_at', '>=', now()->subDays(30))
                                        ->count();
                                @endphp
                                <p
                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 text-purple-500">
                                    Butuh Penilaian</p>
                                <div class="flex items-end gap-1">
                                    <span
                                        class="text-3xl font-black text-purple-600 leading-none">+{{ $newCount }}</span>
                                    <span class="flex h-2 w-2 rounded-full bg-purple-400 mb-2 animate-bounce"></span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8 ">
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
                            <button @click="showFilterDropdown = !showFilterDropdown"
                                class="flex items-center gap-2 px-5 py-3 bg-gray-50 rounded-2xl text-sm font-bold text-gray-600 hover:bg-gray-100 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filter
                                <span x-show="filterDivisi || filterJabatan || filterStatus"
                                    class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            </button>

                            <div x-show="showFilterDropdown" x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-150"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                                @click.outside="showFilterDropdown = false" x-cloak
                                class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 z-[70] p-5 origin-top-right">

                                {{-- Header --}}
                                <div class="flex justify-between items-center mb-5">
                                    <h3 class="text-sm font-bold text-gray-800">Filter Data</h3>
                                    <button @click="resetFilters()"
                                        class="text-xs font-medium text-gray-400 hover:text-red-500 hover:bg-red-50 px-2 py-1 rounded transition">
                                        Reset Filter
                                    </button>
                                </div>

                                <div class="space-y-5">

                                    {{-- STATUS FILTER --}}
                                    <div x-data="{ open: false, list: [{ val: '', label: 'Semua Status' }, { val: '1', label: 'Aktif' }, { val: '0', label: 'Nonaktif' }] }" class="relative">
                                        <label
                                            class="block text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1.5">Status
                                            Keaktifan</label>
                                        <div @click="open = !open"
                                            class="relative block w-full pl-9 pr-3 py-2.5 text-sm bg-gray-50 border border-transparent rounded-xl text-gray-700 cursor-pointer hover:bg-gray-100 transition flex justify-between items-center">
                                            <div
                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <span class="truncate font-medium"
                                                x-text="list.find(x => x.val == filterStatus)?.label || 'Semua Status'"></span>
                                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </div>

                                        {{-- Inner List Dropdown --}}
                                        <div x-show="open" @click.outside="open = false"
                                            class="absolute w-full mt-1 bg-white rounded-xl shadow-xl border border-gray-100 z-[80] overflow-hidden">
                                            <ul class="max-h-60 overflow-y-auto py-1">
                                                <template x-for="item in list" :key="item.val">
                                                    <li @click="filterStatus = item.val; open = false"
                                                        class="px-4 py-2.5 text-sm cursor-pointer transition flex items-center gap-2"
                                                        :class="filterStatus == item.val ?
                                                            'bg-blue-50 text-blue-700 font-semibold' :
                                                            'text-gray-700 hover:bg-gray-50'">
                                                        <svg x-show="filterStatus == item.val"
                                                            class="w-4 h-4 text-blue-600" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <span x-show="filterStatus != item.val" class="w-4 h-4"></span>
                                                        <span x-text="item.label"></span>
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>

                                </div>

                                {{-- Info Helper for Preview Limitation --}}
                                <div class="mt-4 p-3 bg-orange-50 rounded-xl border border-orange-100 flex gap-3">
                                    <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-[10px] text-orange-700 leading-relaxed">
                                        <span class="font-bold">Mode Preview:</span> Pencarian ini hanya memindai <span
                                            class="font-bold">5 data terbaru</span>. Jika tidak ditemukan, silakan cek di
                                        halaman
                                        <span class="italic font-bold">Master Borongan</span>.
                                    </p>
                                </div>
                                <div class="mt-6 pt-4 border-t border-gray-50 text-center">
                                    <p class="text-[10px] text-gray-400">Filter akan diterapkan otomatis</p>
                                </div>
                            </div>

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

                                        {{-- Trigger Modal Button --}}
                                        <button type="button"
                                            @click="window.location.href = '{{ route('view.buat.penilaian', $unit->id) }}?ids=' + selectedItems.join(',')"
                                            class="flex items-center gap-1.5 px-4 py-2 bg-blue-50 text-blue-700 border border-blue-100 rounded-xl text-xs font-bold hover:bg-blue-600 hover:text-white transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            Buat Penilaian
                                        </button>

                                        {{-- Status Update Form --}}
                                        <form action="{{ route('export.excel', $unit->id) }}" method="POST" class="inline">
                                            @csrf
                                            {{-- Alpine.js otomatis mengisi value ini dengan ID yang dicentang dalam format JSON --}}
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

                    <a href="#"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3
                            bg-emerald-600 text-white font-bold rounded-2xl
                            hover:bg-emerald-700 shadow-lg shadow-emerald-100
                            transition active:scale-95">

                        <!-- Download Icon -->
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v12m0 0l4-4m-4 4l-4-4M4 17h16" />
                        </svg>

                        Download Panduan Indikator Penilaian
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
                                    Validasi HRD</th>
                                <th
                                    class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    Masa Kontrak</th>
                                <th class="pr-8 py-4"></th>
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
