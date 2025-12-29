@if ($unit->sistem_pengajian === 2)
{{-- 1. Root Alpine Component for Borongan --}}
<div x-data="{
    selectedItems: [],
    showCategoryModal: false,
    showStatusModal: false,
    statusValue: '1',
    allIds: {{ $borongan->pluck('id') }},
    toggleAll() {
        if (this.selectedItems.length === this.allIds.length) {
            this.selectedItems = [];
        } else {
            this.selectedItems = [...this.allIds];
        }
    }
}" class="relative">

    {{-- Toolbar Borongan --}}
    <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/30">
        <div class="flex items-center gap-2">
            <p class="text-sm text-gray-500">Menampilkan daftar paket borongan.</p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <input type="text" placeholder="Cari item..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-100 focus:border-orange-400 transition bg-white">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <a href="{{ route('view.tambah.unit-borongan', $unit->id) }}"
                class="px-4 py-2 bg-orange-600 text-white text-xs font-bold rounded-lg hover:bg-orange-700 transition flex items-center gap-2 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
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
                        <input type="checkbox" @click="toggleAll()" :checked="selectedItems.length === allIds.length && allIds.length > 0"
                            class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-200 cursor-pointer">
                    </th>
                    <th class="px-2 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider w-10 text-center">#</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider w-[250px]">Item Borongan</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Kategori</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Harga Client</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Upah Pekerja</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="pr-6 py-4 text-right"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 bg-white">
                @forelse ($borongan as $b)
                    <tr @click="selectedItems.includes({{ $b->id }}) ? selectedItems = selectedItems.filter(id => id !== {{ $b->id }}) : selectedItems.push({{ $b->id }})"
                        :class="selectedItems.includes({{ $b->id }}) ? 'bg-orange-50/50' : 'hover:bg-orange-50/20'"
                        class="transition-colors cursor-pointer group">

                        <td class="pl-6 py-5 align-top">
                            <input type="checkbox" value="{{ $b->id }}" x-model.number="selectedItems" @click.stop
                                class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-200 cursor-pointer mt-1">
                        </td>

                        <td class="px-2 py-5 align-top text-center"><span class="text-xs font-bold text-gray-400 font-mono">{{ $loop->iteration }}</span></td>

                        <td class="px-4 py-5 align-top">
                            <p class="text-sm font-bold text-gray-900 group-hover:text-orange-700 transition truncate max-w-[200px]">{{ $b->nama_item }}</p>
                            <span class="text-[10px] font-mono text-gray-400">ID: {{ $b->id }}</span>
                        </td>

                        <td class="px-4 py-5 align-top">
                            <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                {{ $b->kategoriRel->nama }}
                            </span>
                        </td>

                        <td class="px-4 py-5 align-top">
                            <span class="text-sm font-bold text-gray-900">Rp {{ number_format($b->harga_unit, 0, ',', '.') }}</span>
                        </td>

                        <td class="px-4 py-5 align-top">
                            <span class="text-sm font-bold text-emerald-600">Rp {{ number_format($b->harga_pekerja, 0, ',', '.') }}</span>
                        </td>

                        <td class="px-4 py-5 align-top">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $b->status_aktif ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">
                                {{ $b->status_aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>

                        <td class="pr-6 py-5 align-top text-right">
                             <a href="{{ route('view.ubah.unit-borongan', ['unitId' => $unit->id, 'boronganId' => $b->id]) }}" @click.stop class="p-2 text-gray-400 hover:text-orange-600 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                             </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-6 py-12 text-center text-gray-500 italic">Belum ada data borongan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Floating Action Bar (Orange Theme) --}}
    <div x-show="selectedItems.length > 0" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" class="fixed bottom-8 left-1/2 -translate-x-1/2 z-40 w-[95%] max-w-2xl">
        <div class="bg-white/80 backdrop-blur-md border border-orange-100 shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-2xl px-5 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="flex items-center justify-center bg-orange-600 text-white text-[11px] font-black h-6 w-6 rounded-full" x-text="selectedItems.length"></span>
                <span class="text-sm font-bold text-gray-900">Item Dipilih</span>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="selectedItems = []" class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-700 transition">Batal</button>
                <div class="h-6 w-px bg-gray-200 mx-1"></div>
                <button @click="showCategoryModal = true" class="px-4 py-2 bg-orange-50 text-orange-700 border border-orange-100 rounded-xl text-xs font-bold hover:bg-orange-600 hover:text-white transition-all">Ubah Kategori</button>
                <button @click="showStatusModal = true" class="px-4 py-2 bg-gray-50 text-gray-600 border border-gray-200 rounded-xl text-xs font-bold hover:bg-gray-800 hover:text-white transition-all">Update Status</button>

                <form action="{{ route('bulk.update.borongan') }}" method="POST" class="inline">
                    @csrf @method('PUT')
                    <input type="hidden" name="ids" :value="JSON.stringify(selectedItems)">
                    <button name="action" value="delete" onclick="return confirm('Hapus item terpilih?')" class="p-2 bg-red-50 text-red-600 border border-red-100 rounded-xl hover:bg-red-600 hover:text-white transition-all">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL: Ubah Kategori --}}
    <div x-show="showCategoryModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4" x-cloak>
        <div x-show="showCategoryModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" @click="showCategoryModal = false" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>
        <div x-show="showCategoryModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-t-2xl">
                <h3 class="text-lg font-bold text-gray-900">Ubah Kategori Massal</h3>
                <button @click="showCategoryModal = false" class="text-gray-400 hover:text-gray-600 transition"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>
            <form action="{{ route('bulk.update.borongan') }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="ids" :value="JSON.stringify(selectedItems)">
                <input type="hidden" name="action" value="update_category">
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-6">Ubah kategori untuk <span class="font-bold text-orange-600" x-text="selectedItems.length"></span> item borongan.</p>

                    <div x-data="{ open: false, selected: '', list: {{ $boronganKategori->map(fn($cat) => ['val' => (string) $cat->id, 'label' => $cat->nama])->toJson() }} }" class="relative">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Pilih Kategori Baru</label>
                        <input type="hidden" name="kategori_id" x-model="selected" required>
                        <div @click="open = !open" class="bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-4 cursor-pointer flex justify-between items-center text-sm transition-all hover:border-orange-300">
                            <span x-text="list.find(x => x.val == selected)?.label || 'Pilih Kategori...'"></span>
                            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                        <ul x-show="open" @click.outside="open = false" class="absolute w-full mt-2 bg-white border border-gray-100 rounded-xl shadow-xl max-h-40 overflow-y-auto z-[70] p-1.5">
                            <template x-for="item in list" :key="item.val">
                                <li @click="selected = item.val; open = false" class="px-3 py-2 text-sm rounded-lg hover:bg-orange-50 hover:text-orange-700 cursor-pointer transition" x-text="item.label"></li>
                            </template>
                        </ul>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end gap-3 rounded-b-2xl">
                    <button type="button" @click="showCategoryModal = false" class="text-sm font-bold text-gray-500">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-orange-600 text-white text-sm font-bold rounded-xl hover:bg-orange-700 transition">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL: Update Status --}}
    <div x-show="showStatusModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4" x-cloak>
        <div x-show="showStatusModal" @click="showStatusModal = false" class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm"></div>
        <div x-show="showStatusModal" class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-t-2xl">
                <h3 class="text-lg font-bold text-gray-900">Update Status Borongan</h3>
                <button @click="showStatusModal = false" class="text-gray-400 hover:text-gray-600 transition"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
            </div>
            <form action="{{ route('bulk.update.borongan') }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="ids" :value="JSON.stringify(selectedItems)">
                <input type="hidden" name="action" value="update_status">
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-6">Update status untuk <span class="font-bold text-orange-600" x-text="selectedItems.length"></span> item.</p>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition-all" :class="statusValue === '1' ? 'border-emerald-500 bg-emerald-50/50 ring-1 ring-emerald-500' : 'border-gray-100'">
                            <input type="radio" name="status" value="1" x-model="statusValue" class="sr-only">
                            <span class="flex items-center gap-2 mb-1">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                <span class="text-sm font-bold" :class="statusValue === '1' ? 'text-emerald-700' : 'text-gray-700'">Aktif</span>
                            </span>
                            <span class="text-[10px] text-gray-500">Item aktif dalam sistem</span>
                        </label>
                        <label class="relative flex flex-col p-4 border rounded-xl cursor-pointer transition-all" :class="statusValue === '0' ? 'border-gray-400 bg-gray-50 ring-1 ring-gray-400' : 'border-gray-100'">
                            <input type="radio" name="status" value="0" x-model="statusValue" class="sr-only">
                            <span class="flex items-center gap-2 mb-1">
                                <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                <span class="text-sm font-bold" :class="statusValue === '0' ? 'text-gray-900' : 'text-gray-700'">Nonaktif</span>
                            </span>
                            <span class="text-[10px] text-gray-500">Nonaktifkan item dalam sistem</span>
                        </label>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end gap-3 rounded-b-2xl">
                    <button type="button" @click="showStatusModal = false" class="text-sm font-bold text-gray-500">Batal</button>
                    <button type="submit" class="px-6 py-2 bg-orange-600 text-white text-sm font-bold rounded-xl hover:bg-orange-700 transition">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endif
