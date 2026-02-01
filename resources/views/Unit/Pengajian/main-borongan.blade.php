@extends('layout')

@section('content')
    <style>
        @keyframes float-borongan {

            0%,
            100% {
                transform: translateY(0px) rotate(3deg);
            }

            50% {
                transform: translateY(-12px) rotate(6deg);
            }
        }

        .animate-float-borongan {
            animation: float-borongan 4s ease-in-out infinite;
        }
    </style>

    <div id="borongan-main-container" x-data="{
        selectedItems: [],
        showKategoriModal: false,
        showStatusModal: false,
        showFilterDropdown: false,
        searchQuery: '',
        filterKategori: '',
        filterStatus: '',
        statusValue: '1',

        currentPage: {{ $borongan->currentPage() }},
        allIds: {{ json_encode($borongan->pluck('id')) }},

        toggleAll() {
            this.selectedItems = this.selectedItems.length === this.allIds.length ? [] : [...this.allIds];
        },

        async updateTable(targetUrl = null) {
            // 1. Tentukan URL dasar (dari klik paginasi ATAU URL browser saat ini)
            let url = new URL(targetUrl || window.location.href);

            // 2. Logika Penentuan Halaman
            if (targetUrl) {
                // Jika dari klik paginasi, ambil angka page dari link tersebut
                const pageParam = url.searchParams.get('page') || 1;

                // Simpan ke state Alpine agar diingat saat filter dihapus nanti
                if (!this.searchQuery && !this.filterKategori && !this.filterStatus) {
                    this.currentPage = pageParam;
                }
            } else {
                // Jika dari mengetik/filter, putuskan: reset ke hal 1 atau balik ke hal awal
                if (!this.searchQuery && !this.filterKategori && !this.filterStatus) {
                    url.searchParams.set('page', this.currentPage);
                } else {
                    url.searchParams.set('page', '1');
                }
            }

            // 3. Sinkronisasi semua filter ke objek URL
            if (this.searchQuery) url.searchParams.set('search', this.searchQuery);
            else url.searchParams.delete('search');

            if (this.filterKategori) url.searchParams.set('kategori', this.filterKategori);
            else url.searchParams.delete('kategori');

            if (this.filterStatus) url.searchParams.set('status', this.filterStatus);
            else url.searchParams.delete('status');

            // 4. UPDATE URL DI ADDRESS BAR BROWSER
            window.history.pushState({}, '', url);

            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await response.text();

                // 5. Update Isi Tabel
                document.getElementById('main-table-body').innerHTML = html;

                // 6. UPDATE PAGINASI (PENTING!)
                const newPagination = document.getElementById('new-pagination-provider');
                const paginationContainer = document.getElementById('search-pagination');
                if (newPagination && paginationContainer) {
                    paginationContainer.innerHTML = newPagination.innerHTML;
                }

                // 7. UPDATE STATE CURRENT PAGE DARI DATA TERBARU
                // Cari elemen ID provider yang baru di-load untuk sinkronisasi state Alpine
                const provider = document.getElementById('new-ids-provider-full');
                if (provider) {
                    this.allIds = JSON.parse(provider.dataset.ids);
                    // Perbarui currentPage di Alpine berdasarkan atribut data yang dikirim server
                    this.currentPage = provider.dataset.currentPage;
                }

            } catch (error) { console.error('Gagal memuat tabel:', error); }
        },

        resetFilters() {
            this.searchQuery = '';
            this.filterKategori = '';
            this.filterStatus = '';
            this.updateTable();
        }
    }" x-init="$watch('searchQuery', () => updateTable());
    $watch('filterKategori', () => updateTable());
    $watch('filterStatus', () => updateTable());">

        {{-- HEADER SECTION --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
            <div
                class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-gray-100 relative overflow-hidden">

                {{-- Decorative Glow --}}
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-orange-50 rounded-full blur-3xl opacity-40">
                </div>
                <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 bg-yellow-50 rounded-full blur-3xl opacity-40">
                </div>

                <div class="relative z-10">
                    {{-- Breadcrumb --}}
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
                        <a href="{{ route('view.detail.unit', $unit->id) }}"
                            class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-orange-600 transition group">
                            <svg class="w-3.5 h-3.5 transform group-hover:-translate-x-1 transition" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                            </svg>
                            Kembali ke Unit
                        </a>
                        <div class="flex items-center gap-2">
                            <span class="flex h-2 w-2 relative">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                            </span>
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Master Data</span>
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                        {{-- Identity --}}
                        <div class="space-y-4">
                            <div class="flex items-center gap-4">
                                <div class="h-14 w-2 bg-orange-500 rounded-full shadow-[0_0_20px_rgba(249,115,22,0.4)]">
                                </div>
                                <div>
                                    <h1 class="text-5xl font-black text-gray-900 tracking-tight leading-none">
                                        Master Borongan<span class="text-orange-500">.</span>
                                    </h1>
                                    <div class="flex items-center gap-3 mt-3">
                                        <div
                                            class="px-3 py-1 bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-sm">
                                            {{ $unit->namaMitra->nama_mitra ?? 'Mitra Perusahaan' }}
                                        </div>
                                        <div
                                            class="px-3 py-1 bg-orange-50 text-orange-700 text-[10px] font-black uppercase tracking-widest rounded-lg border border-orange-100 italic">
                                            Sistem Borongan
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-base text-gray-500 flex items-center gap-2 ml-6">
                                Unit Kerja: <span
                                    class="font-bold text-gray-800 italic underline decoration-orange-200 underline-offset-8 decoration-2">{{ $unit->nama_unit }}</span>
                            </p>
                        </div>

                        {{-- Stats --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div
                                class="bg-gray-50/50 border border-gray-100 rounded-3xl p-5 min-w-[140px] hover:bg-white hover:shadow-xl hover:shadow-orange-900/5 transition-all duration-300 group">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Item</p>
                                <span class="text-3xl font-black text-gray-900 leading-none">{{ $borongan->total() }}</span>
                            </div>

                            <div
                                class="bg-gray-50/50 border border-gray-100 rounded-3xl p-5 min-w-[140px] hover:bg-white hover:shadow-xl hover:shadow-emerald-900/5 transition-all duration-300 group">
                                <p
                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 text-emerald-500">
                                    Aktif</p>
                                <span
                                    class="text-3xl font-black text-emerald-600 leading-none">{{ $unit->borongan()->where('status_aktif', 1)->count() }}</span>
                            </div>

                            <div
                                class="bg-gray-50/50 border border-gray-100 rounded-3xl p-5 min-w-[140px] hover:bg-white hover:shadow-xl hover:shadow-orange-900/5 transition-all duration-300 group">
                                @php
                                    $newCount = $unit
                                        ->borongan()
                                        ->where('created_at', '>=', now()->subDays(30))
                                        ->count();
                                @endphp
                                <p
                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1 text-orange-500">
                                    Baru (30d)</p>
                                <div class="flex items-end gap-1">
                                    <span
                                        class="text-3xl font-black text-orange-600 leading-none">+{{ $newCount }}</span>
                                    <span class="flex h-2 w-2 rounded-full bg-orange-400 mb-2 animate-bounce"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MAIN CONTENT --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
            <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-200 overflow-hidden">

                {{-- TOOLBAR --}}
                <div class="px-6 py-6 border-b border-gray-100 flex flex-col md:flex-row justify-between gap-4 bg-white">
                    <div class="flex items-center gap-4 flex-1">
                        <div class="relative w-full max-w-md group" x-data="{ showSearchTooltip: false }">
                            <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 group-focus-within:text-orange-500 transition"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" x-model.debounce.500ms="searchQuery"
                                placeholder="Cari nama item borongan..."
                                class="w-full pl-12 pr-10 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-orange-100 focus:bg-white transition text-sm">

                            {{-- Info Icon --}}
                            <div class="absolute right-3 top-1/2 -translate-y-1/2 cursor-help text-gray-300 hover:text-orange-500 transition-colors"
                                @mouseenter="showSearchTooltip = true" @mouseleave="showSearchTooltip = false">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>

                            <div x-show="showSearchTooltip" x-cloak
                                class="absolute right-0 top-full mt-2 w-64 p-3 bg-white border border-orange-100 rounded-xl shadow-xl z-[100] pointer-events-none">
                                <p class="text-[10px] text-orange-700 leading-relaxed">
                                    <span class="font-bold">Live Search:</span> Menampilkan seluruh hasil yang cocok dari
                                    database borongan unit ini.
                                </p>
                            </div>
                        </div>

                        <div class="relative">
                            <button @click="showFilterDropdown = !showFilterDropdown"
                                class="flex items-center gap-2 px-5 py-3 bg-gray-50 rounded-2xl text-sm font-bold text-gray-600 hover:bg-gray-100 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filter
                                <span x-show="filterKategori || filterStatus"
                                    class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></span>
                            </button>

                            {{-- Dropdown Filter UI --}}
                            <div x-show="showFilterDropdown" @click.outside="showFilterDropdown = false" x-cloak
                                class="absolute left-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 z-[70] p-5 origin-top-right">
                                <div class="flex justify-between items-center mb-5">
                                    <h3 class="text-sm font-bold text-gray-800">Filter Borongan</h3>
                                    <button @click="resetFilters()"
                                        class="text-xs font-medium text-gray-400 hover:text-red-500 transition">Reset</button>
                                </div>
                                <div class="space-y-5">
                                    {{-- Kategori --}}
                                    <div x-data="{ open: false, list: [{ val: '', label: 'Semua Kategori' }, @foreach ($boronganKategori as $c) { val: '{{ $c->id }}', label: '{{ $c->nama }}' }, @endforeach] }" class="relative">
                                        <label
                                            class="block text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1.5">Kategori</label>
                                        <div @click="open = !open"
                                            class="relative block w-full pl-3 pr-3 py-2.5 text-sm bg-gray-50 rounded-xl text-gray-700 cursor-pointer hover:bg-gray-100 transition flex justify-between items-center">
                                            <span class="truncate font-medium"
                                                x-text="list.find(x => x.val == filterKategori)?.label || 'Semua Kategori'"></span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path d="M19 9l-7 7-7-7" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div x-show="open" @click.outside="open = false"
                                            class="absolute w-full mt-1 bg-white rounded-xl shadow-xl border border-gray-100 z-[80] overflow-hidden">
                                            <ul class="max-h-40 overflow-y-auto py-1">
                                                <template x-for="item in list" :key="item.val">
                                                    <li @click="filterKategori = item.val; open = false"
                                                        class="px-4 py-2 text-sm cursor-pointer hover:bg-orange-50 transition flex items-center gap-2"
                                                        :class="filterKategori == item.val ? 'text-orange-600 font-bold' : ''">
                                                        <span x-text="item.label"></span>
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                    {{-- Status --}}
                                    <div x-data="{ open: false, list: [{ val: '', label: 'Semua Status' }, { val: '1', label: 'Aktif' }, { val: '0', label: 'Nonaktif' }] }" class="relative">
                                        <label
                                            class="block text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1.5">Status</label>
                                        <div @click="open = !open"
                                            class="relative block w-full pl-3 pr-3 py-2.5 text-sm bg-gray-50 rounded-xl text-gray-700 cursor-pointer hover:bg-gray-100 transition flex justify-between items-center">
                                            <span class="truncate font-medium"
                                                x-text="list.find(x => x.val == filterStatus)?.label || 'Semua Status'"></span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path d="M19 9l-7 7-7-7" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div x-show="open" @click.outside="open = false"
                                            class="absolute w-full mt-1 bg-white rounded-xl shadow-xl border border-gray-100 z-[80] overflow-hidden">
                                            <ul class="py-1">
                                                <template x-for="item in list" :key="item.val">
                                                    <li @click="filterStatus = item.val; open = false"
                                                        class="px-4 py-2 text-sm cursor-pointer hover:bg-orange-50 transition"
                                                        :class="filterStatus == item.val ? 'text-orange-600 font-bold' : ''">
                                                        <span x-text="item.label"></span>
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('view.tambah.unit-borongan', $unit->id) }}"
                        class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-orange-600 text-white font-bold rounded-2xl hover:bg-orange-700 shadow-lg shadow-orange-100 transition active:scale-95">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Borongan
                    </a>
                </div>

                {{-- TABLE --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50 border-b border-gray-100">
                            <tr>
                                <th class="pl-8 py-4 w-10"><input type="checkbox" @click="toggleAll()"
                                        :checked="selectedItems.length === allIds.length && allIds.length > 0"
                                        class="rounded border-gray-300 text-orange-600"></th>
                                <th class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest">Item
                                    Borongan</th>
                                <th
                                    class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    Kategori</th>
                                <th
                                    class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    Max Rej Subkon</th>
                                <th
                                    class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-right">
                                    Harga Client</th>
                                <th
                                    class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-right">
                                    Upah Pekerja</th>
                                <th
                                    class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    Status</th>
                                <th class="pr-8 py-4 w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="main-table-body" class="divide-y divide-gray-50 bg-white">
                            @include('Unit.partials.main-borongan-table')
                        </tbody>
                    </table>
                </div>

                <div id="new-ids-provider-full" data-ids="{{ json_encode($borongan->pluck('id')) }}"
                    data-current-page="{{ $borongan->currentPage() }}" class="hidden">
                </div>

                <div id="new-pagination-provider">
                    @if ($borongan->hasPages())
                        {{ $borongan->links('vendor.pagination.custom') }}
                    @endif
                </div>
            </div>
        </div>

        {{-- Floating Bulk Actions (Orange Theme) --}}
        <div x-show="selectedItems.length > 0" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-10"
            class="fixed bottom-8 left-1/2 -translate-x-1/2 z-40 w-[95%] max-w-3xl" x-cloak>
            <div
                class="bg-white/80 backdrop-blur-md border border-orange-100 shadow-2xl rounded-2xl px-5 py-3 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span
                        class="flex items-center justify-center bg-orange-600 text-white text-[11px] font-black h-6 w-6 rounded-full"
                        x-text="selectedItems.length"></span>
                    <span class="text-sm font-bold text-gray-900">Item Dipilih</span>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="selectedItems = []"
                        class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-700 transition">Batal</button>
                    <div class="h-6 w-px bg-gray-200 mx-1"></div>
                    <button @click="showKategoriModal = true"
                        class="px-4 py-2 bg-orange-50 text-orange-700 border border-orange-100 rounded-xl text-xs font-bold hover:bg-orange-600 hover:text-white transition-all">Ubah
                        Kategori</button>
                    <button @click="showStatusModal = true"
                        class="px-4 py-2 bg-gray-50 text-gray-600 border border-gray-200 rounded-xl text-xs font-bold hover:bg-gray-800 hover:text-white transition-all">Update
                        Status</button>
                    <form action="{{ route('bulk.update.borongan') }}" method="POST" class="inline">
                        @csrf @method('PUT')
                        <input type="hidden" name="ids" :value="JSON.stringify(selectedItems)">
                        <button name="action" value="delete" onclick="return confirm('Hapus item?')"
                            class="p-2 bg-red-50 text-red-600 border border-red-100 rounded-xl hover:bg-red-600 hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    stroke-width="2" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="showKategoriModal" class="fixed inset-0 z-[30] flex items-center justify-center p-4 sm:p-6" x-cloak>

            {{-- Backdrop --}}
            <div x-show="showKategoriModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="showKategoriModal = false"
                class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm">
            </div>

            {{-- Modal Content --}}
            <div x-show="showKategoriModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">

                <div
                    class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-t-2xl">
                    <h3 class="text-lg font-bold text-gray-900">Ganti Kategori</h3>
                    <button @click="showKategoriModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('bulk.update.kategori') }}" method="POST">
                    @csrf @method('PUT')
                    {{-- Pass selected IDs --}}
                    <input type="hidden" name="ids" :value="JSON.stringify(selectedItems)">

                    <div class="p-6">
                        <div class="mb-6">
                            <p class="text-sm text-gray-600">
                                Anda akan mengubah kategori untuk
                                <span class="font-bold text-orange-600" x-text="selectedItems.length"></span> item.
                                Tindakan ini akan menggantikan kategori item saat ini.
                            </p>
                        </div>

                        <div class="space-y-4">
                            {{-- Dropdown --}}
                            <div x-data="{
                                open: false,
                                selected: '',
                                {{-- Convert PHP collection to JSON for Alpine --}}
                                list: {{ $boronganKategori->map(fn($div) => ['val' => (string) $div->id, 'label' => $div->nama])->toJson() }}
                            }" class="relative">

                                <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Pilih
                                    Kategori Baru</label>

                                {{-- Hidden input for form submission --}}
                                <input type="hidden" name="kategori" x-model="selected" required>

                                {{-- Dropdown Trigger --}}
                                <div @click="open = !open"
                                    class="bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-4 cursor-pointer flex justify-between items-center hover:border-orange-300 transition-all focus:ring-2 focus:ring-orange-100">
                                    <span class="text-sm" :class="selected ? 'text-gray-900 font-medium' : 'text-gray-400'"
                                        x-text="list.find(x => x.val == selected)?.label || 'Pilih Kategori...'">
                                    </span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                        :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>

                                {{-- Dropdown Menu --}}
                                <ul x-show="open" x-transition:enter="transition ease-out duration-100"
                                    x-transition:enter-start="opacity-0 transform scale-95"
                                    x-transition:enter-end="opacity-100 transform scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="opacity-100 transform scale-100"
                                    x-transition:leave-end="opacity-0 transform scale-95" @click.outside="open = false"
                                    class="absolute w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl overflow-y-auto max-h-60 z-[70] p-1.5">

                                    <template x-for="item in list" :key="item.val">
                                        <li @click="selected = item.val; open = false"
                                            class="px-3 py-2 text-sm rounded-lg cursor-pointer transition-colors mb-0.5 last:mb-0"
                                            :class="selected == item.val ?
                                                'bg-orange-50 text-orange-700 font-bold' :
                                                'text-gray-600 hover:bg-gray-50 hover:text-orange-600'"
                                            x-text="item.label">
                                        </li>
                                    </template>

                                    {{-- Empty State for List --}}
                                    <template x-if="list.length === 0">
                                        <li class="px-3 py-2 text-sm text-gray-400 italic">Tidak ada
                                            kategori tersedia</li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button type="button" @click="showDivisionModal= false"
                            class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2 bg-orange-600 text-white text-sm font-bold rounded-xl hover:bg-orange-700 shadow-md shadow-orange-100 transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
        {{-- MODAL: Change Status --}}
        <div x-show="showStatusModal" class="fixed inset-0 z-[30] flex items-center justify-center p-4 sm:p-6" x-cloak>
            {{-- Backdrop --}}
            <div x-show="showStatusModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                @click="showStatusModal = false" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>

            {{-- Modal Content --}}
            <div x-show="showStatusModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">

                {{-- Header --}}
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="text-lg font-bold text-gray-900">Ubah Status Item</h3>
                    <button @click="showStatusModal = false" class="text-gray-400 hover:text-gray-600 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('bulk.update.borongan') }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="ids" :value="JSON.stringify(selectedItems)">
                    <input type="hidden" name="action" value="update_status">

                    <div class="p-6">
                        {{-- Info Text --}}
                        <div class="mb-6">
                            <p class="text-sm text-gray-600">
                                Anda akan mengubah status untuk
                                <span class="font-bold text-orange-600" x-text="selectedItems.length"></span> item.
                                Tindakan ini akan berdampak pada akses dan keaktifan item.
                            </p>
                        </div>

                        {{-- Status Selection (Styled as Cards) --}}
                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <label
                                class="relative flex flex-col p-4 border rounded-xl cursor-pointer focus:outline-none transition-all"
                                :class="statusValue === '1' ?
                                    'border-emerald-500 bg-emerald-50/50 ring-1 ring-emerald-500' :
                                    'border-gray-100 hover:bg-gray-50'">
                                <input type="radio" name="status" value="1" x-model="statusValue"
                                    class="sr-only">
                                <span class="flex items-center gap-2 mb-1">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    <span class="text-sm font-bold"
                                        :class="statusValue === '1' ? 'text-emerald-700' :
                                            'text-gray-700'">Aktif</span>
                                </span>
                                <span class="text-[10px] text-gray-500">Borongan aktif dalam
                                    sistem</span>
                            </label>

                            <label
                                class="relative flex flex-col p-4 border rounded-xl cursor-pointer focus:outline-none transition-all"
                                :class="statusValue === '0' ?
                                    'border-gray-400 bg-gray-50 ring-1 ring-gray-400' :
                                    'border-gray-100 hover:bg-gray-50'">
                                <input type="radio" name="status" value="0" x-model="statusValue"
                                    class="sr-only">
                                <span class="flex items-center gap-2 mb-1">
                                    <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                    <span class="text-sm font-bold"
                                        :class="statusValue === '0' ? 'text-gray-900' :
                                            'text-gray-700'">Nonaktif</span>
                                </span>
                                <span class="text-[10px] text-gray-500">Nonaktifkan item</span>
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
                            class="px-6 py-2 bg-orange-600 text-white text-sm font-bold rounded-xl hover:bg-orange-700 shadow-md shadow-orange-100 transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener("click", function(e) {
            // Mencari apakah yang diklik adalah link di dalam pagination
            const anchor = e.target.closest("#search-pagination a");

            if (anchor) {
                e.preventDefault();

                // AMBIL ELEMEN BERDASARKAN ID (Sangat Penting agar tidak tertukar)
                const alpineElement = document.getElementById('borongan-main-container');

                if (alpineElement) {
                    const alpineData = Alpine.$data(alpineElement);

                    // Cek apakah fungsi ada sebelum dijalankan untuk menghindari crash
                    if (alpineData && typeof alpineData.updateTable === 'function') {
                        alpineData.updateTable(anchor.href);

                        // Opsional: Scroll ke atas tabel agar user tahu konten berubah
                        window.scrollTo({
                            top: alpineElement.offsetTop - 100,
                            behavior: 'smooth'
                        });
                    }
                }
            }
        });
    </script>
@endsection
