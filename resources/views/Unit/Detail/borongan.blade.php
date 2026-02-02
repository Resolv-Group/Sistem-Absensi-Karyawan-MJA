@if ($unit->sistem_pengajian === 2)
    {{-- 1. Root Alpine Component for Borongan --}}
    <div x-data="{
        selectedItems: [],
        showCategoryModal: false,
        showStatusModal: false,
        showFilterDropdown: false,
        statusValue: '1',

        // States for Search & Filter
        searchQuery: '',
        filterKategori: '',
        filterStatus: '',

        allIds: {{ $borongan->pluck('id') }},

        toggleAll() {
            if (this.selectedItems.length === this.allIds.length) {
                this.selectedItems = [];
            } else {
                this.selectedItems = [...this.allIds];
            }
        },

        async updateTable() {
            const url = new URL(window.location.href);
            url.searchParams.set('target', 'borongan'); // <--- THIS ENSURES IT HITS THE CORRECT CONTROLLER IF
            url.searchParams.set('search', this.searchQuery);
            url.searchParams.set('kategori', this.filterKategori);
            url.searchParams.set('status', this.filterStatus);

            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await response.text();
                document.getElementById('borongan-table-body').innerHTML = html;

                const provider = document.getElementById('borongan-ids-provider');
                if (provider) { this.allIds = JSON.parse(provider.dataset.ids); }
            } catch (error) {
                console.error('Error:', error);
            }
        },

        resetFilters() {
            this.searchQuery = '';
            this.filterKategori = '';
            this.filterStatus = '';
            this.updateTable();
        }
    }" x-init="$watch('searchQuery', () => updateTable());
    $watch('filterKategori', () => updateTable());
    $watch('filterStatus', () => updateTable());" class="relative">

        {{-- Toolbar Borongan --}}
        <div
            class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/30">
            <div class="flex items-center gap-2">
                <p class="text-sm text-gray-500">Menampilkan daftar borongan <span class="text-orange-600 font-medium"> 5 terbaru. </span></p>
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                {{-- SEARCH INPUT --}}
                <div class="relative w-full sm:w-64" x-data="{ showSearchTooltip: false }">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>

                    <input type="text" x-model.debounce.500ms="searchQuery" placeholder="Cari Nama Item.." title="Cari (Preview 5 data)"
                        class="w-full pl-9 pr-10 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-100 focus:border-orange-400 transition bg-white">

                    <div class="absolute right-3 top-1/2 -translate-y-1/2 cursor-help text-gray-300 hover:text-orange-500 transition-colors"
                        @mouseenter="showSearchTooltip = true" @mouseleave="showSearchTooltip = false">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    {{-- Floating Tooltip --}}
                    <div x-show="showSearchTooltip" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0" x-cloak
                        class="absolute right-0 top-full mt-2 w-64 p-3 bg-white border border-orange-100 rounded-xl shadow-xl z-[100] pointer-events-none">
                        <div class="flex gap-2">
                            <svg class="w-4 h-4 text-orange-500 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-[10px] text-orange-700 leading-relaxed">
                                <span class="font-bold">Mode Preview:</span> Pencarian terbatas pada <span
                                    class="font-bold">5 item terbaru</span>. Gunakan Master Borongan untuk melihat semua
                                data.
                            </p>
                        </div>
                        <div
                            class="absolute -top-1 right-4 w-2 h-2 bg-white border-t border-l border-orange-100 rotate-45">
                        </div>
                    </div>
                </div>

                {{-- FILTER BUTTON --}}
                <div class="relative z-[60]">
                    <button @click="showFilterDropdown = !showFilterDropdown"
                        class="flex items-center gap-2 px-3 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none transition">
                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filter
                        <span x-show="filterKategori || filterStatus" class="flex h-2 w-2 relative">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-orange-500"></span>
                        </span>
                    </button>

                    {{-- FILTER DROPDOWN POPUP --}}
                    <div x-show="showFilterDropdown" x-cloak @click.outside="showFilterDropdown = false"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                        class="absolute right-0 mt-3 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 z-[70] p-5 origin-top-right">

                        <div class="flex justify-between items-center mb-5">
                            <h3 class="text-sm font-bold text-gray-800">Filter Borongan</h3>
                            <button @click="resetFilters()"
                                class="text-xs font-medium text-gray-400 hover:text-red-500 hover:bg-red-50 px-2 py-1 rounded transition">Reset</button>
                        </div>

                        <div class="space-y-5">
                            {{-- STATUS FILTER (Orange Themed) --}}
                            <div x-data="{ open: false, list: [{ val: '', label: 'Semua Status' }, { val: '1', label: 'Aktif' }, { val: '0', label: 'Nonaktif' }] }" class="relative">
                                <label
                                    class="block text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1.5">Status
                                    Keaktifan</label>
                                <div @click="open = !open"
                                    class="relative block w-full pl-9 pr-3 py-2.5 text-sm bg-gray-50 rounded-xl text-gray-700 cursor-pointer hover:bg-gray-100 transition flex justify-between items-center">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <span class="truncate font-medium"
                                        x-text="list.find(x => x.val == filterStatus)?.label || 'Semua Status'"></span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform"
                                        :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" stroke-width="2" />
                                    </svg>
                                </div>
                                <div x-show="open" @click.outside="open = false"
                                    class="absolute w-full mt-1 bg-white rounded-xl shadow-xl border z-[80] overflow-hidden">
                                    <ul class="py-1">
                                        <template x-for="item in list" :key="item.val">
                                            <li @click="filterStatus = item.val; open = false"
                                                class="px-4 py-2.5 text-sm cursor-pointer transition flex items-center gap-2"
                                                :class="filterStatus == item.val ?
                                                    'bg-orange-50 text-orange-700 font-semibold' :
                                                    'text-gray-700 hover:bg-gray-50'">
                                                <svg x-show="filterStatus == item.val" class="w-4 h-4 text-orange-600"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path d="M5 13l4 4L19 7" stroke-width="2" />
                                                </svg>
                                                <span x-show="filterStatus != item.val" class="w-4 h-4"></span>
                                                <span x-text="item.label"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>

                            {{-- KATEGORI FILTER (Orange Themed) --}}
                            <div x-data="{ open: false, list: [{ val: '', label: 'Semua Kategori' }, @foreach ($boronganKategori as $cat) { val: '{{ $cat->id }}', label: '{{ $cat->nama }}' }, @endforeach] }" class="relative">
                                <label
                                    class="block text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1.5">Kategori</label>
                                <div @click="open = !open"
                                    class="relative block w-full pl-9 pr-3 py-2.5 text-sm bg-gray-50 rounded-xl text-gray-700 cursor-pointer hover:bg-gray-100 transition flex justify-between items-center">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path d="M4 6h16M4 12h16M4 18h7" stroke-width="2" />
                                        </svg>
                                    </div>
                                    <span class="truncate font-medium"
                                        x-text="list.find(x => x.val == filterKategori)?.label || 'Semua Kategori'"></span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform"
                                        :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path d="M19 9l-7 7-7-7" stroke-width="2" />
                                    </svg>
                                </div>
                                <div x-show="open" @click.outside="open = false"
                                    class="absolute w-full mt-1 bg-white rounded-xl shadow-xl border z-[80] overflow-hidden">
                                    <ul class="max-h-60 overflow-y-auto py-1">
                                        <template x-for="item in list" :key="item.val">
                                            <li @click="filterKategori = item.val; open = false"
                                                class="px-4 py-2.5 text-sm cursor-pointer transition flex items-center gap-2"
                                                :class="filterKategori == item.val ?
                                                    'bg-orange-50 text-orange-700 font-semibold' :
                                                    'text-gray-700 hover:bg-gray-50'">
                                                <svg x-show="filterKategori == item.val"
                                                    class="w-4 h-4 text-orange-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path d="M5 13l4 4L19 7" stroke-width="2" />
                                                </svg>
                                                <span x-show="filterKategori != item.val" class="w-4 h-4"></span>
                                                <span x-text="item.label"></span>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            {{-- Info Helper for Preview Limitation --}}
                            <div class="mt-4 p-3 bg-orange-50 rounded-xl border border-orange-100 flex gap-3">
                                <svg class="w-5 h-5 text-orange-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-[10px] text-orange-700 leading-relaxed">
                                    <span class="font-bold">Mode Preview:</span> Pencarian ini hanya memindai <span class="font-bold">5 data terbaru</span>. Jika tidak ditemukan, silakan cek di halaman <span class="italic font-bold">Master Borongan</span>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="{{ route('view.tambah.unit-borongan', $unit->id) }}"
                    class="px-4 py-2 bg-orange-600 text-white text-xs font-bold rounded-lg hover:bg-orange-700 transition flex items-center gap-2 shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Borongan
                </a>
            </div>
        </div>

        {{-- Table Borongan --}}
        <div class="overflow-x-auto rounded-b-2xl">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="pl-6 py-4 w-10">
                            <input type="checkbox" @click="toggleAll()"
                                :checked="selectedItems.length === allIds.length && allIds.length > 0"
                                class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-200 cursor-pointer">
                        </th>
                        <th
                            class="px-2 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider w-10 text-center">
                            #</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider w-[250px]">
                            Item Borongan</th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Kategori
                        </th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">Max Rej Subkon (%)
                        </th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Harga Client
                        </th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Upah Pekerja
                        </th>
                        <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="pr-6 py-4 text-right"></th>
                    </tr>
                </thead>
                <tbody id="borongan-table-body" class="divide-y divide-gray-50 bg-white">
                    @include('Unit.partials.borongan-table')
                </tbody>
            </table>
            {{-- DEDICATED PAGE NAVIGATION FOOTER --}}
            <div
                class="px-6 py-4 bg-gray-50/50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">

                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                        {{-- BORONGAN / PACKAGE ICON --}}
                        <svg class="w-5 h-5 text-orange-600" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-gray-900 leading-tight">
                            Lihat Semua Borongan
                        </h4>
                        <p class="text-[10px] text-gray-500 mt-0.5">
                            Buka halaman lengkap untuk melihat dan mengelola seluruh data borongan pada unit ini.
                        </p>
                    </div>
                </div>

                <a href="{{ route('view.borongan', $unit->id) }}"
                    class="group inline-flex items-center gap-2 px-4 py-2 bg-white border border-orange-200 text-orange-600 text-xs font-bold rounded-xl hover:bg-orange-600 hover:text-white hover:border-orange-600 transition-all shadow-sm active:scale-95">
                    Lihat Semua Borongan
                    <svg class="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </a>
            </div>

        </div>

        {{-- Floating Action Bar (Orange Theme) --}}
        <div x-show="selectedItems.length > 0" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            class="fixed bottom-8 left-1/2 -translate-x-1/2 z-40 w-[95%] max-w-2xl">
            <div
                class="bg-white/80 backdrop-blur-md border border-orange-100 shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-2xl px-5 py-3 flex items-center justify-between">
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
                    <button @click="showCategoryModal = true"
                        class="px-4 py-2 bg-orange-50 text-orange-700 border border-orange-100 rounded-xl text-xs font-bold hover:bg-orange-600 hover:text-white transition-all">Ubah
                        Kategori</button>
                    <button @click="showStatusModal = true"
                        class="px-4 py-2 bg-gray-50 text-gray-600 border border-gray-200 rounded-xl text-xs font-bold hover:bg-gray-800 hover:text-white transition-all">Update
                        Status</button>

                    <form action="{{ route('bulk.update.borongan') }}" method="POST" class="inline">
                        @csrf @method('PUT')
                        <input type="hidden" name="ids" :value="JSON.stringify(selectedItems)">
                        <button name="action" value="delete" onclick="return confirm('Hapus item terpilih?')"
                            class="p-2 bg-red-50 text-red-600 border border-red-100 rounded-xl hover:bg-red-600 hover:text-white transition-all">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL: Ubah Kategori --}}
        <div x-show="showCategoryModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4" x-cloak>
            <div x-show="showCategoryModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                @click="showCategoryModal = false" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>
            <div x-show="showCategoryModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div
                    class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-t-2xl">
                    <h3 class="text-lg font-bold text-gray-900">Ubah Kategori Massal</h3>
                    <button @click="showCategoryModal = false"
                        class="text-gray-400 hover:text-gray-600 transition"><svg class="w-5 h-5" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <form action="{{ route('bulk.update.borongan') }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="ids" :value="JSON.stringify(selectedItems)">
                    <input type="hidden" name="action" value="update_category">
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-6">Ubah kategori untuk <span
                                class="font-bold text-orange-600" x-text="selectedItems.length"></span> item borongan.
                        </p>

                        <div x-data="{ open: false, selected: '', list: {{ $boronganKategori->map(fn($cat) => ['val' => (string) $cat->id, 'label' => $cat->nama])->toJson() }} }" class="relative">
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Pilih
                                Kategori Baru</label>
                            <input type="hidden" name="kategori_id" x-model="selected" required>
                            <div @click="open = !open"
                                class="bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-4 cursor-pointer flex justify-between items-center text-sm transition-all hover:border-orange-300">
                                <span x-text="list.find(x => x.val == selected)?.label || 'Pilih Kategori...'"></span>
                                <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <ul x-show="open" @click.outside="open = false"
                                class="absolute w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl max-h-40 overflow-y-auto z-[70] p-1.5">
                                <template x-for="item in list" :key="item.val">
                                    <li @click="selected = item.val; open = false"
                                        class="px-3 py-2 text-sm rounded-lg hover:bg-orange-50 hover:text-orange-700 cursor-pointer transition"
                                        x-text="item.label"></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button type="button" @click="showCategoryModal = false"
                            class="text-sm font-bold text-gray-500">Batal</button>
                        <button type="submit"
                            class="px-6 py-2 bg-orange-600 text-white text-sm font-bold rounded-xl hover:bg-orange-700 transition">Simpan
                            Kategori</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL: Update Status --}}
        <div x-show="showStatusModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4" x-cloak>
            <div x-show="showStatusModal" @click="showStatusModal = false"
                class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>
            <div x-show="showStatusModal"
                class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
                <div
                    class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-t-2xl">
                    <h3 class="text-lg font-bold text-gray-900">Update Status Borongan</h3>
                    <button @click="showStatusModal = false" class="text-gray-400 hover:text-gray-600 transition"><svg
                            class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <form action="{{ route('bulk.update.borongan') }}" method="POST">
                    @csrf @method('PUT')
                    <input type="hidden" name="ids" :value="JSON.stringify(selectedItems)">
                    <input type="hidden" name="action" value="update_status">
                    <div class="p-6">
                        <p class="text-sm text-gray-600 mb-6">Update status untuk <span
                                class="font-bold text-orange-600" x-text="selectedItems.length"></span> item.</p>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition-all"
                                :class="statusValue === '1' ?
                                    'border-emerald-500 bg-emerald-50/50 ring-1 ring-emerald-500' : 'border-gray-100'">
                                <input type="radio" name="status" value="1" x-model="statusValue"
                                    class="sr-only">
                                <span class="flex items-center gap-2 mb-1">
                                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                    <span class="text-sm font-bold"
                                        :class="statusValue === '1' ? 'text-emerald-700' : 'text-gray-700'">Aktif</span>
                                </span>
                                <span class="text-[10px] text-gray-500">Item aktif dalam sistem</span>
                            </label>
                            <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition-all"
                                :class="statusValue === '0' ? 'border-gray-400 bg-gray-50 ring-1 ring-gray-400' :
                                    'border-gray-100'">
                                <input type="radio" name="status" value="0" x-model="statusValue"
                                    class="sr-only">
                                <span class="flex items-center gap-2 mb-1">
                                    <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                    <span class="text-sm font-bold"
                                        :class="statusValue === '0' ? 'text-gray-900' : 'text-gray-700'">Nonaktif</span>
                                </span>
                                <span class="text-[10px] text-gray-500">Nonaktifkan item dalam sistem</span>
                            </label>
                        </div>
                    </div>
                    <div class="px-6 py-4 bg-gray-50 flex items-center justify-end gap-3 rounded-b-2xl">
                        <button type="button" @click="showStatusModal = false"
                            class="text-sm font-bold text-gray-500">Batal</button>
                        <button type="submit"
                            class="px-6 py-2 bg-orange-600 text-white text-sm font-bold rounded-xl hover:bg-orange-700 transition">Simpan
                            Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endif
