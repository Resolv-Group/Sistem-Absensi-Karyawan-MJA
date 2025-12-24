@extends('layout')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER --}}
        <div class="mb-8 flex items-center gap-4">
            <a href="/unit"
                class="p-2 rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <nav class="flex text-sm font-medium text-gray-500 mb-1">
                    <span class="hover:text-gray-700">Unit</span>
                    <span class="mx-2 text-gray-300">/</span>
                    <span class="text-blue-600">Tambah Borongan Unit</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Borongan Unit</h1>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-100 text-red-600 p-4 rounded-xl mb-6">
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tambah.unit-borongan.post') }}" method="POST" enctype="multipart/form-data"
            x-data="workerForm()" class="space-y-6">
            @csrf

            {{-- CARD 1: INFORMASI UNIT --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <div
                    class="bg-blue-50/50 border-b border-blue-100 p-4 flex flex-col items-center justify-center text-center">
                    <div
                        class="h-10 w-10 bg-white rounded-full flex items-center justify-center shadow-sm text-blue-600 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide">Informasi Penambahan Item (Borongan)
                    </h2>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- 1. Unit Information (Readonly) --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                            Unit Kerja Terpilih
                        </label>

                        {{-- Hidden Input to ensure ID is submitted --}}
                        <input type="hidden" name="id_unit" value="{{ $unitSelected->id ?? '' }}">

                        <div
                            class="w-full rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 flex items-center justify-between">
                            <div>
                                {{-- Nama Unit --}}
                                <p class="text-sm font-bold text-gray-900">
                                    {{ $unitSelected->nama_unit ?? 'Nama Unit Belum Dimuat' }}
                                </p>

                                {{-- Nama Perusahaan & ID --}}
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="flex items-center gap-1.5 text-xs text-gray-500 font-medium">
                                        <svg class="w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                        {{ $unitSelected->namaMitra->nama_mitra ?? 'Nama Perusahaan' }}
                                    </div>

                                    <span class="text-gray-300">•</span>

                                    <div
                                        class="flex items-center gap-1 text-xs text-gray-500 font-mono bg-gray-200/50 px-1.5 py-0.5 rounded">
                                        #{{ $unitSelected->id ?? 'ID' }}
                                    </div>
                                </div>
                            </div>

                            {{-- Lock Icon to indicate Readonly --}}
                            <div class="p-2 bg-gray-200/50 rounded-lg text-gray-400">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Status Aktif (Readonly Style) --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                            Status Penambahan
                        </label>
                        <div
                            class="w-full py-3.5 px-4 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-700 text-sm font-bold flex items-center gap-2 h-[66px]">
                            {{-- Fixed height to match neighbor --}}
                            <span class="relative flex h-2.5 w-2.5">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                            </span>
                            Aktif
                        </div>
                    </div>
                </div>
            </div>

            {{-- CARD 2: ALOKASI PEKERJA (Redesigned) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-900">Daftar Barang</h3>
                    <button type="button" @click="addRow()"
                        class="text-xs font-bold text-blue-600 bg-white hover:bg-blue-50 px-3 py-1.5 rounded-lg transition flex items-center gap-1 border border-gray-200 shadow-sm">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Baris
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    <template x-for="(row, index) in rows" :key="row.id">
                        <div
                            class="bg-white rounded-2xl border border-gray-200 p-5 relative group transition hover:border-blue-300 hover:shadow-lg hover:shadow-blue-500/10">

                            {{-- Row Number Badge --}}
                            <div
                                class="absolute -top-2 -left-2 h-6 w-6 bg-blue-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-md">
                                <span x-text="index + 1"></span>
                            </div>

                            {{-- Delete Button --}}
                            <button type="button" @click="removeRow(index)"
                                class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition bg-white p-1.5 rounded-lg shadow-sm border border-gray-100 opacity-50 group-hover:opacity-100">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">

                                {{-- 1. Nama Barang --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Nama Item
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="nama_item" placeholder="Contoh: Besi A"
                                        class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-blue-100 transition py-3 px-4 text-sm font-medium placeholder-gray-400"
                                        value="{{ old('') }}">
                                </div>

                                {{-- 2. Kategori (Searchable Combobox) --}}
                                <div
                                    x-data="idCombobox(row, 'kategoriId', window.kategoriData, k => k.nama)"
                                    x-init="init()"
                                    class="relative"
                                >
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">
                                        Kategori
                                    </label>

                                    <div class="relative">
                                        <!-- Visible input (search / display only) -->
                                        <input
                                            type="text"
                                            x-model="search"
                                            @focus="open = true"
                                            @click="open = true"
                                            @click.outside="close()"
                                            @keydown.escape="open = false"
                                            placeholder="Pilih kategori..."
                                            class="w-full pl-4 pr-10 py-3 text-sm font-medium text-gray-800 bg-gray-50 rounded-xl focus:bg-white focus:border-blue-500 transition"
                                            autocomplete="off"
                                        >

                                        <!-- Chevron -->
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Dropdown -->
                                    <ul
                                        x-show="open"
                                        x-transition.opacity
                                        class="absolute z-50 w-full mt-1 bg-white border border-gray-100 rounded-xl shadow-xl max-h-48 overflow-y-auto py-1"
                                    >
                                        <template x-for="item in filtered" :key="item.id">
                                            <li
                                                @click="select(item)"
                                                class="px-4 py-2.5 text-sm cursor-pointer hover:bg-blue-50 hover:text-blue-700 font-medium text-gray-700"
                                            >
                                                <span x-text="item.nama"></span>
                                            </li>
                                        </template>

                                        <li
                                            x-show="filtered.length === 0"
                                            class="px-4 py-2 text-xs text-gray-400 italic"
                                        >
                                            Tidak ada hasil
                                        </li>
                                    </ul>

                                    <!-- Hidden input (submitted value) -->
                                    <input
                                        type="hidden"
                                        :name="`pekerja[${index}][kategori_id]`"
                                        :value="row.kategoriId"
                                    >
                                </div>

                                {{-- 3. Gaji Unit --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Harga Barang Unit</label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">Rp</span>
                                        <input type="number" :name="`harga_unit`"
                                            x-model.number="row.harga_unit"
                                            @input="autoHitung(row)"
                                            class="w-full pl-10 rounded-xl border-gray-200 bg-gray-50 text-sm font-bold text-gray-900 focus:bg-white focus:border-blue-500 py-3 px-4"
                                            placeholder="0">
                                    </div>
                                </div>

                                {{-- 4. Gaji Pekerja --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Harga Barang Pekerja</label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">Rp</span>
                                        <input type="number" :name="`harga_pekerja`"
                                            x-model.number="row.harga_pekerja"
                                            @input="row.manual = true"
                                            class="w-full pl-10 rounded-xl border-gray-200 bg-gray-50 text-sm font-bold text-gray-900 focus:bg-white focus:border-blue-500 py-3 px-4"
                                            placeholder="0">
                                    </div>
                                </div>

                                {{-- 4. Satuan  --}}
                                <div x-data="{ open: false, selected: '{{ old('satuan') }}' || '', list: [{ val: '1', label: 'Rue' }, { val: '2', label: 'M' }, { val: '3', label: 'Biji' }, { val: '4', label: 'Unit' }] }" class="relative">
                                    <label class="block text-sm font-bold text-gray-700 mb-1">Satuan</label>

                                    <input type="hidden" name="satuan" x-model="selected">

                                    <div @click="open=!open"
                                        class=" bg-gray-50 rounded-lg py-2.5 px-3 cursor-pointer flex justify-between items-center">
                                        <span x-text="list.find(x=>x.val==selected)?.label || 'Pilih Tipe Satuan'"></span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>

                                    <ul x-show="open" @click.outside="open=false"
                                        class="absolute w-full mt-1 border border-gray-300 bg-white rounded-lg shadow-md overflow-y-auto max-h-40 z-50">
                                        <template x-for="item in list" :key="item.val">
                                            <li @click="selected=item.val; open=false"
                                                class="px-3 py-2 hover:bg-blue-600 hover:text-white cursor-pointer transition"
                                                x-text="item.label">
                                            </li>
                                        </template>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    </template>

                    {{-- Empty State --}}
                    <div x-show="rows.length === 0" class="text-center py-12 text-gray-400 text-sm italic bg-gray-50/50">
                        Klik "Tambah Baris" untuk memasukkan data pekerja.
                    </div>
                </div>
            </div>

            {{-- FOOTER / TOTAL --}}
            <div
                class="bg-white rounded-2xl shadow-[0_-10px_40px_-15px_rgba(0,0,0,0.1)] border border-gray-200 p-6 flex flex-col sm:flex-row items-center justify-between gap-6 sticky bottom-4 z-10">
                {{-- <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Total Alokasi Gaji</p>
                    <p class="text-3xl font-black text-gray-900" x-text="formatRupiah(totalAllocation)"></p>
                </div> --}}

                <button type="submit"
                    class="w-full sm:w-auto px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200 transition transform hover:-translate-y-0.5">
                    Simpan Data
                </button>
            </div>

        </form>
    </div>
@endsection

@section('scripts')
    <script>
        function unitCombobox() {
            return {
                list: @json($units ?? []),
                selectedId: '',
                search: '',
                open: false,

                init() {
                    // If validation failed and old ID exists, repopulate text
                    let oldId = '{{ old('id_unit') }}';
                    if (oldId) {
                        let found = this.list.find(item => item.id == oldId);
                        if (found) {
                            this.selectedId = found.id;
                            this.search = found.nama;
                        }
                    }
                },

                get filteredList() {
                    if (this.search === '') return this.list;
                    return this.list.filter(item =>
                        item.nama.toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                toggleDropdown() {
                    this.open = !this.open;
                },

                closeDropdown() {
                    this.open = false;
                    // If user typed but didn't select, revert to selected or clear
                    let found = this.list.find(item => item.id == this.selectedId);
                    if (found) {
                        this.search = found.nama;
                    } else {
                        this.search = '';
                        this.selectedId = '';
                    }
                },

                selectOption(item) {
                    this.selectedId = item.id;
                    this.search = item.nama;
                    this.open = false;
                }
            }
        }

        function workerCombobox(row) {
            return {
                open: false,
                search: '',
                selectedId: row.workerId || null,

                init() {
                    // If an ID is already set (e.g., from old() data), populate search field
                    if (this.selectedId) {
                        const p = window.workersData.find(w => w.id == this.selectedId);
                        if (p) {
                            this.search = `${p.nama} (${p.nik})`;
                        }
                    }
                },

                get filtered() {
                    if (!this.search) {
                        return window.workersData;
                    }
                    return window.workersData.filter(p =>
                        p.nama.toLowerCase().includes(this.search.toLowerCase()) ||
                        p.nik.includes(this.search)
                    );
                },

                select(p) {
                    this.selectedId = p.id;
                    row.workerId = p.id;
                    this.search = `${p.nama} (${p.nik})`;
                    this.open = false;
                },

                close() {
                    // UX: If user clicks away without selecting, revert text to match ID or clear it
                    if (this.selectedId) {
                        const p = window.workersData.find(w => w.id == this.selectedId);
                        if (p) this.search = `${p.nama} (${p.nik})`;
                    } else {
                        this.search = '';
                    }
                    this.open = false;
                }
            }
        }

        // 1. DATA SOURCES (You can pass these from Controller later)
        window.kategoriData = @json($kategoriList);
        window.workersData = @json($pekerjaList ?? []);

        // 2. FORM LOGIC
        function workerForm() {
            return {
                rows: [{
                        id: 1,
                        harga_unit: 0,
                        harga_pekerja: 0,
                        workerId: '',
                        kategoriId: null,
                    } // Added fieldss
                ],

                addRow() {
                    this.rows.push({
                        id: Date.now(),
                        harga_unit: 0,
                        harga_pekerja: 0,
                        workerId: '',
                        kategoriId: '', 
                    });
                },
                // ... (removeRow, totals, etc remain the same) ...
                removeRow(index) {
                    this.rows.splice(index, 1);
                },

                autoHitung(row) {
                    if (row.manual) return;

                    if (!row.harga_unit || row.harga_unit <= 0) {
                        row.harga_pekerja = 0;
                        return;
                    }

                    row.harga_pekerja = Math.round(row.harga_unit * 0.82);
                },

                get totalAllocation() {
                    return this.rows.reduce((sum, row) => sum + (parseInt(row.gaji) || 0), 0);
                },
                formatRupiah(amount) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(amount);
                }
                
                
            }
        }

        // 3. GENERIC STRING COMBOBOX (For Divisi & Jabatan)
        // This allows selecting from list OR typing a new value (Hybrid)
        function idCombobox(row, field, dataSource, labelFn) {
            return {
                open: false,
                search: '',
                selectedId: row[field],

                init() {
                    if (this.selectedId) {
                        const found = dataSource.find(i => i.id == this.selectedId);
                        if (found) this.search = labelFn(found);
                    }
                },

                get filtered() {
                    if (!this.search) return dataSource;
                    return dataSource.filter(item =>
                        labelFn(item).toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                select(item) {
                    this.selectedId = item.id;
                    row[field] = item.id;
                    this.search = labelFn(item);
                    this.open = false;
                },

                close() {
                    if (this.selectedId) {
                        const found = dataSource.find(i => i.id == this.selectedId);
                        if (found) this.search = labelFn(found);
                    } else {
                        this.search = '';
                    }
                    this.open = false;
                }
            }
        }

        // (Combobox logic for Unit remains the same...)
        document.addEventListener('alpine:init', () => {
            Alpine.data('combobox', (listData) => ({
                // ... existing unit combobox code ...
                list: listData,
                search: '',
                selectedId: '',
                open: false,
                get filteredList() {
                    if (this.search === '') return this.list;
                    return this.list.filter(i => i.nama.toLowerCase().includes(this.search
                        .toLowerCase()));
                },
                select(item) {
                    this.selectedId = item.id;
                    this.search = item.nama;
                    this.open = false;
                }
            }))
        })
    </script>
@endsection
