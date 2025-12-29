{{-- 1. Wrap everything in an Alpine component --}}
<div x-data="{
    selectedItems: [],
    showJabatanModal: false,
    showDivisionModal: false,
    showStatusModal: false,
    statusValue: '1',
    allIds: {{ $pkwtPekerja->pluck('id') }},
    toggleAll() {
        if (this.selectedItems.length === this.allIds.length) {
            this.selectedItems = [];
        } else {
            this.selectedItems = [...this.allIds];
        }
    }
}" class="relative">

    <div
        class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/30">
        <div class="flex items-center gap-2">
            <p class="text-sm text-gray-500">Menampilkan daftar pekerja harian/kontrak.</p>
        </div>
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-64">
                <input type="text" placeholder="Cari pekerja..."
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition bg-white">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <a href="{{ route('view.tambah.unit-pekerja', $unit->id) }}"
                class="px-4 py-2 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition flex items-center gap-2 shadow-sm">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Pekerja
            </a>
        </div>
    </div>

    {{-- Table Pekerja --}}
    <div class="overflow-x-auto rounded-b-2xl">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 border-b border-gray-100">
                    <th class="pl-6 py-4 w-10">
                        <input type="checkbox" @click="toggleAll()"
                            :checked="selectedItems.length === allIds.length && allIds.length > 0"
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-200 cursor-pointer">
                    </th>
                    <th class="px-2 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider w-10 text-center">
                        #</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider w-[250px]">Nama &
                        NIK</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Jabatan & Divisi
                    </th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">PKWT
                    </th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">
                        Periode PKWT</th>
                    <th class="px-4 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-wider text-center">
                        Status</th>
                    <th class="pr-6 py-4 text-right"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 bg-white">
                @forelse($pkwtPekerja as $pkwt)
                    <tr {{-- Row Click Logic --}}
                        @click="selectedItems.includes({{ $pkwt->id }}) ? selectedItems = selectedItems.filter(id => id !== {{ $pkwt->id }}) : selectedItems.push({{ $pkwt->id }})"
                        {{-- Conditional Background --}}
                        :class="selectedItems.includes({{ $pkwt->id }}) ? 'bg-blue-50/50' : 'hover:bg-gray-50/80'"
                        class="transition-colors cursor-pointer group">

                        <td class="pl-6 py-5 align-top">
                            <input type="checkbox" value="{{ $pkwt->id }}" x-model.number="selectedItems"
                                @click.stop {{-- Prevents double toggle when clicking the checkbox itself --}}
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-200 cursor-pointer mt-1">
                        </td>

                        <td class="px-2 py-5 align-top text-center">
                            <span class="text-xs font-bold text-gray-400 font-mono">{{ $loop->iteration }}</span>
                        </td>

                        <td class="px-4 py-5 align-top">
                            <div class="flex flex-col gap-0.5">
                                <span
                                    class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition truncate max-w-[200px]"
                                    title="{{ $pkwt->pekerja->nama }}">
                                    {{ $pkwt->pekerja->nama }}
                                </span>
                                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                    <span class="font-mono tracking-tight">{{ $pkwt->pekerja->nik }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-5 align-top">
                            <div class="flex flex-col gap-2">
                                <span
                                    class="inline-flex items-center text-xs font-semibold text-gray-800 bg-gray-100 px-2.5 py-1 rounded-md border border-gray-200 w-fit max-w-[180px] truncate">
                                    {{ $pkwt->jabatan->nama }}
                                </span>
                                <span
                                    class="text-xs text-gray-500 pl-1 truncate max-w-[180px]">{{ $pkwt->divisi->nama }}</span>
                            </div>
                        </td>

                        {{-- PKWT Document --}}
                        <td class="px-4 py-5 align-top text-center">
                            @if ($pkwt->dokumen_mime)
                                <a href="{{ route('stream.pkwt', $pkwt->id) }}" target="_blank" @click.stop
                                    class="inline-flex flex-col items-center justify-center gap-1.5 p-2 rounded-lg
                   hover:bg-white transition border border-transparent hover:border-gray-200">

                                    @if (Str::startsWith($pkwt->dokumen_mime, 'application/pdf'))
                                        {{-- PDF --}}
                                        <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-[9px] font-bold tracking-wide text-gray-500">
                                            PDF
                                        </span>
                                    @else
                                        {{-- IMAGE --}}
                                        <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-[9px] font-bold tracking-wide text-gray-500">
                                            IMG
                                        </span>
                                    @endif

                                </a>
                            @else
                                <span class="text-xs text-gray-300 italic">-</span>
                            @endif
                        </td>


                        <td class="px-4 py-5 align-top text-center">
                            <div class="flex flex-col items-center gap-1">
                                <span
                                    class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($pkwt->tgl_mulai_pkwt)->format('d/m/y') }}</span>
                                <div class="h-3 w-px bg-gray-200"></div>
                                <span
                                    class="text-xs font-bold {{ $pkwt->status_pkwt['color'] === 'red' ? 'text-red-600' : 'text-emerald-600' }}">
                                    {{ \Carbon\Carbon::parse($pkwt->tgl_akhir_pkwt)->format('d M Y') }}
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-5 align-top text-center">
                            @if ($pkwt->status_aktif == 1)
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">
                                    Aktif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">
                                    Nonaktif
                                </span>
                            @endif
                        </td>

                        <td class="pr-6 py-5 align-top text-right">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('view.ubah.unit-pekerja', ['unitId' => $unit->id, 'pekerjaId' => $pkwt->id]) }}"
                                    @click.stop
                                    class="p-2 text-gray-400 hover:text-blue-600 hover:bg-white rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500">Belum ada pekerja terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($pkwtPekerja->hasPages())
            <div id="search-pagination" class="border-t border-gray-200 bg-gray-50 px-4 py-3 sm:px-6">
                {{ $pkwtPekerja->links('vendor.pagination.custom') }}
            </div>
        @endif
    </div>

    {{-- Floating Bulk Action Bar --}}
    <div x-show="selectedItems.length > 0" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-10" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-10"
        class="fixed bottom-8 left-1/2 -translate-x-1/2 z-40 w-[95%] max-w-3xl">

        <div
            class="bg-white/80 backdrop-blur-md border border-blue-100 shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-2xl px-5 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span
                    class="relative flex items-center justify-center bg-blue-600 text-white text-[11px] font-black h-6 w-6 rounded-full shadow-sm"
                    x-text="selectedItems.length"></span>
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-gray-900 leading-none">Pekerja Dipilih</span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="button" @click="selectedItems = []"
                    class="px-3 py-2 text-xs font-bold text-gray-500 hover:text-gray-700 transition">Batal</button>
                <div class="h-6 w-px bg-gray-200 mx-1"></div>

                {{-- Trigger Modal Button --}}
                <button @click="showJabatanModal = true"
                    class="flex items-center gap-1.5 px-4 py-2 bg-blue-50 text-blue-700 border border-blue-100 rounded-xl text-xs font-bold hover:bg-blue-600 hover:text-white transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2m3 0h1a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h1m12 0H6" />
                    </svg>
                    Ubah Jabatan
                </button>

                {{-- Trigger Modal Button --}}
                <button @click="showDivisionModal = true"
                    class="flex items-center gap-1.5 px-4 py-2 bg-blue-50 text-blue-700 border border-blue-100 rounded-xl text-xs font-bold hover:bg-blue-600 hover:text-white transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    Ubah Divisi
                </button>

                {{-- Status Update Form --}}
                <form action="{{ route('bulk.update.pekerja') }}" method="POST" class="flex gap-2">
                    @csrf @method('put')
                    <input type="hidden" name="ids" :value="JSON.stringify(selectedItems)">
                    <button type="button" @click="showStatusModal = true"
                        class="px-4 py-2 bg-gray-50 text-gray-600 border border-gray-200 rounded-xl text-xs font-bold hover:bg-gray-800 hover:text-white transition-all">
                        Update Status
                    </button>
                    <button name="action" value="delete" onclick="return confirm('Hapus data?')"
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

    {{-- MODAL: Change Division --}}
    <div x-show="showDivisionModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6" x-cloak>

        {{-- Backdrop --}}
        <div x-show="showDivisionModal" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="showDivisionModal = false"
            class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm">
        </div>

        {{-- Modal Content --}}
        <div x-show="showDivisionModal" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">

            <div
                class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-t-2xl">
                <h3 class="text-lg font-bold text-gray-900">Change Division</h3>
                <button @click="showDivisionModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('bulk.update.divisi') }}" method="POST">
                @csrf @method('PUT')
                {{-- Pass selected IDs --}}
                <input type="hidden" name="ids" :value="JSON.stringify(selectedItems)">

                <div class="p-6">
                    <div class="mb-6">
                        <p class="text-sm text-gray-600">
                            Anda akan mengubah divisi untuk
                            <span class="font-bold text-blue-600" x-text="selectedItems.length"></span> pekerja.
                            Tindakan ini akan menggantikan divisi mereka saat ini.
                        </p>
                    </div>

                    <div class="space-y-4">
                        {{-- Dropdown --}}
                        <div x-data="{
                            open: false,
                            selected: '',
                            {{-- Convert PHP collection to JSON for Alpine --}}
                            list: {{ $divisions->map(fn($div) => ['val' => (string) $div->id, 'label' => $div->nama])->toJson() }}
                        }" class="relative">

                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Pilih
                                Divisi Baru</label>

                            {{-- Hidden input for form submission --}}
                            <input type="hidden" name="divisi_id" x-model="selected" required>

                            {{-- Dropdown Trigger --}}
                            <div @click="open = !open"
                                class="bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-4 cursor-pointer flex justify-between items-center hover:border-blue-300 transition-all focus:ring-2 focus:ring-blue-100">
                                <span class="text-sm" :class="selected ? 'text-gray-900 font-medium' : 'text-gray-400'"
                                    x-text="list.find(x => x.val == selected)?.label || 'Pilih Divisi...'">
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
                                        :class="selected == item.val ? 'bg-blue-50 text-blue-700 font-bold' :
                                            'text-gray-600 hover:bg-gray-50 hover:text-blue-600'"
                                        x-text="item.label">
                                    </li>
                                </template>

                                {{-- Empty State for List --}}
                                <template x-if="list.length === 0">
                                    <li class="px-3 py-2 text-sm text-gray-400 italic">Tidak ada divisi tersedia</li>
                                </template>
                            </ul>
                        </div>

                        {{-- Checkbox --}}
                        {{-- <label
                            class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50 transition cursor-pointer">
                            <input type="checkbox" name="apply_immediately" value="1" checked
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-200 h-4 w-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-700">Apply immediately</span>
                                <span class="text-[11px] text-gray-500">Update worker profiles as soon as you click
                                    apply.</span>
                            </div>
                        </label> --}}
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end gap-3 rounded-b-2xl">
                    <button type="button" @click="showDivisionModal = false"
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

    <div x-show="showJabatanModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6" x-cloak>

        {{-- Backdrop --}}
        <div x-show="showJabatanModal" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="showJabatanModal = false"
            class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm">
        </div>

        {{-- Modal Content --}}
        <div x-show="showJabatanModal" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-4"
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md">

            <div
                class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50 rounded-t-2xl">
                <h3 class="text-lg font-bold text-gray-900">Ganti Jabatan</h3>
                <button @click="showJabatanModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('bulk.update.jabatan') }}" method="POST">
                @csrf @method('PUT')
                {{-- Pass selected IDs --}}
                <input type="hidden" name="ids" :value="JSON.stringify(selectedItems)">

                <div class="p-6">
                    <div class="mb-6">
                        <p class="text-sm text-gray-600">
                            Anda akan mengubah jabatan untuk
                            <span class="font-bold text-blue-600" x-text="selectedItems.length"></span> pekerja.
                            Tindakan ini akan menggantikan jabatan mereka saat ini.
                        </p>
                    </div>

                    <div class="space-y-4">
                        {{-- Dropdown --}}
                        <div x-data="{
                            open: false,
                            selected: '',
                            {{-- Convert PHP collection to JSON for Alpine --}}
                            list: {{ $jabatan->map(fn($div) => ['val' => (string) $div->id, 'label' => $div->nama])->toJson() }}
                        }" class="relative">

                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Pilih
                                Jabatan Baru</label>

                            {{-- Hidden input for form submission --}}
                            <input type="hidden" name="jabatan_id" x-model="selected" required>

                            {{-- Dropdown Trigger --}}
                            <div @click="open = !open"
                                class="bg-gray-50 border border-gray-200 rounded-xl py-2.5 px-4 cursor-pointer flex justify-between items-center hover:border-blue-300 transition-all focus:ring-2 focus:ring-blue-100">
                                <span class="text-sm" :class="selected ? 'text-gray-900 font-medium' : 'text-gray-400'"
                                    x-text="list.find(x => x.val == selected)?.label || 'Pilih Jabatan...'">
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
                                        :class="selected == item.val ? 'bg-blue-50 text-blue-700 font-bold' :
                                            'text-gray-600 hover:bg-gray-50 hover:text-blue-600'"
                                        x-text="item.label">
                                    </li>
                                </template>

                                {{-- Empty State for List --}}
                                <template x-if="list.length === 0">
                                    <li class="px-3 py-2 text-sm text-gray-400 italic">Tidak ada jabatan tersedia</li>
                                </template>
                            </ul>
                        </div>

                        {{-- Checkbox --}}
                        {{-- <label
                            class="flex items-center gap-3 p-3 rounded-xl border border-gray-100 hover:bg-gray-50 transition cursor-pointer">
                            <input type="checkbox" name="apply_immediately" value="1" checked
                                class="rounded border-gray-300 text-blue-600 focus:ring-blue-200 h-4 w-4">
                            <div class="flex flex-col">
                                <span class="text-sm font-semibold text-gray-700">Apply immediately</span>
                                <span class="text-[11px] text-gray-500">Update worker profiles as soon as you click
                                    apply.</span>
                            </div>
                        </label> --}}
                    </div>
                </div>

                <div class="px-6 py-4 bg-gray-50 flex items-center justify-end gap-3 rounded-b-2xl">
                    <button type="button" @click="showDivisionModal = false"
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

    {{-- MODAL: Change Status --}}
    <div x-show="showStatusModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 sm:p-6" x-cloak>
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
                <h3 class="text-lg font-bold text-gray-900">Ubah Status Pekerja</h3>
                <button @click="showStatusModal = false" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                            <span class="font-bold text-blue-600" x-text="selectedItems.length"></span> pekerja.
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
                            <input type="radio" name="status" value="1" x-model="statusValue"
                                class="sr-only">
                            <span class="flex items-center gap-2 mb-1">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                <span class="text-sm font-bold"
                                    :class="statusValue === '1' ? 'text-emerald-700' : 'text-gray-700'">Aktif</span>
                            </span>
                            <span class="text-[10px] text-gray-500">Pekerja aktif dalam sistem</span>
                        </label>

                        <label
                            class="relative flex flex-col p-4 border rounded-xl cursor-pointer focus:outline-none transition-all"
                            :class="statusValue === '0' ? 'border-gray-400 bg-gray-50 ring-1 ring-gray-400' :
                                'border-gray-100 hover:bg-gray-50'">
                            <input type="radio" name="status" value="0" x-model="statusValue"
                                class="sr-only">
                            <span class="flex items-center gap-2 mb-1">
                                <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                <span class="text-sm font-bold"
                                    :class="statusValue === '0' ? 'text-gray-900' : 'text-gray-700'">Nonaktif</span>
                            </span>
                            <span class="text-[10px] text-gray-500">Nonaktifkan akses pekerja</span>
                        </label>
                    </div>

                    {{-- Optional Reason --}}
                    {{-- <div class="space-y-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Alasan Perubahan
                            (Opsional)</label>
                        <textarea name="reason" rows="3" placeholder="Contoh: Pemutusan kontrak atau mutasi..."
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition outline-none resize-none"></textarea>
                    </div> --}}
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
