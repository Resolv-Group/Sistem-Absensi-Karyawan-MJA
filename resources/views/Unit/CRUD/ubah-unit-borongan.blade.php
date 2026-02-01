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
                    <span class="text-blue-600">Ubah Borongan Unit</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Ubah Borongan Unit</h1>
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

        <form action="{{ route('update.unit-borongan', ['unitId' => $unitSelected->id, 'boronganId' => $borongan->id]) }}" method="POST" enctype="multipart/form-data"
            x-data="boronganForm()" class="space-y-6">
            @csrf
            @method("put")

            {{-- CARD 1: INFORMASI UNIT --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 relative overflow-hidden group">

                {{-- Hidden Input --}}
                <input type="hidden" name="id_unit" value="{{ $unitSelected->id ?? '' }}">

                {{-- Background Decoration (Subtle) --}}
                <div
                    class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-50 rounded-full blur-3xl opacity-50 pointer-events-none">
                </div>

                <div class="flex items-start sm:items-center gap-5 relative z-10">

                    {{-- 1. Large Icon / Logo --}}
                    <div
                        class="flex-shrink-0 h-16 w-16 bg-gradient-to-br from-blue-50 to-white border border-blue-100 rounded-2xl flex items-center justify-center shadow-sm">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>

                    {{-- 2. Information Details --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span
                                class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-100 uppercase tracking-wide">
                                Unit Terpilih
                            </span>
                        </div>

                        <h2 class="text-xl font-bold text-gray-900 leading-tight truncate">
                            {{ $unitSelected->nama_unit ?? 'Nama Unit' }}
                        </h2>

                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1.5 text-sm text-gray-700">
                            <div class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                <span
                                    class="font-medium text-gray-700">{{ $unitSelected->namaMitra->nama_mitra ?? '-' }}</span>
                            </div>

                            <span class="text-gray-300">|</span>

                            <div class="flex items-center gap-1.5">
                                <span class="text-gray-400">ID:</span>
                                <span
                                    class="font-mono font-bold text-gray-900 bg-gray-100 px-1.5 rounded">{{ $unitSelected->id ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Lock Icon (Visual indicator that this is fixed) --}}
                    <div class="hidden sm:flex h-10 w-10 text-gray-300 justify-center items-center">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- CARD 2: ALOKASI PEKERJA (Redesigned) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-900">Daftar Barang</h3>
                </div>

                <div class="p-6 space-y-6">
                    <template x-for="(row, index) in borongan" :key="row.id">
                        <div
                            class="bg-white rounded-2xl border border-gray-200 p-5 relative group transition hover:border-orange-300 hover:shadow-lg hover:shadow-orange-500/10">

                            {{-- Row Number Badge --}}
                            <div
                                class="absolute -top-2 -left-2 h-6 w-6 bg-orange-600 text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-md">
                                <span x-text="index + 1"></span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">

                                {{-- 1. Nama Barang --}}
                                <div class="sm:col-span-2">
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Nama
                                        Item
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" :name="`borongan[${index}][nama_item]`" x-model="row.nama_item"
                                        placeholder="Contoh: Besi A"
                                        class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 py-3 px-4 text-sm" />
                                </div>

                                {{-- 2. Kategori (Searchable Combobox) --}}
                                <div x-data="kategoriCombobox(row, index)" x-init="init()" class="relative">

                                    <div class="flex justify-between items-center mb-1">
                                        <label class="block text-xs font-bold text-gray-700">Kategori</label>

                                        <button type="button" @click="openModal()"
                                            class="text-xs flex items-center gap-1 text-blue-600 hover:text-blue-800 font-semibold transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Tambah Baru
                                        </button>
                                    </div>

                                    <input
                                        type="hidden"
                                        :name="`borongan[${index}][kategori]`"
                                        x-model="row.kategoriId"
                                    />

                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>

                                        <input type="text" x-model="search" @input="open = true; selectedId = ''"
                                            @click="open = true" @click.outside="closeDropdown()"
                                            @keydown.escape="open = false" placeholder="Cari atau pilih kategori..."
                                            class="w-full pl-10 pr-10 rounded-lg border-gray-300 bg-gray-50 text-gray-900
                                        focus:bg-white focus:border-blue-500 focus:ring-blue-200 transition py-2.5 px-4 text-sm font-medium"
                                            autocomplete="off">

                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer"
                                            @click="toggleDropdown()">
                                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <ul x-show="open" x-transition.opacity
                                        class="absolute w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto z-40 py-1">

                                        <template x-for="item in filteredList" :key="item.val">
                                            <li @click="selectOption(item)"
                                                class="px-4 py-2.5 text-sm hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition flex justify-between">

                                                <span x-text="item.label"
                                                    :class="selectedId == item.val ? 'font-bold text-blue-600' : 'text-gray-700'"></span>

                                                <svg x-show="selectedId == item.val" class="w-4 h-4 text-blue-600"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </li>
                                        </template>

                                        <li x-show="filteredList.length === 0"
                                            class="px-4 py-3 text-sm text-gray-700 text-center">
                                            <p>Tidak ditemukan "<span x-text="search" class="font-bold"></span>"</p>
                                            <button type="button" @click="openModalWithSearch()"
                                                class="mt-1 text-blue-600 hover:underline font-semibold text-xs">
                                                + Tambah Baru
                                            </button>
                                        </li>
                                    </ul>

                                    {{-- MODAL: PERSIS, CUMA TEXT DIGANTI --}}
                                    <div x-show="showModal" style="display: none;" class="relative z-50"
                                        aria-labelledby="modal-title" role="dialog" aria-modal="true">

                                        {{-- BACKDROP: Fades in/out only (No scaling) --}}
                                        <div x-show="showModal" x-transition:enter="ease-out duration-300"
                                            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                            x-transition:leave="ease-in duration-200"
                                            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                            class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"></div>

                                        {{-- MODAL POSITIONING WRAPPER --}}
                                        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                            <div
                                                class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">

                                                {{-- ACTUAL MODAL CARD: Scales and Pops up --}}
                                                <div x-show="showModal" @click.outside="showModal = false"
                                                    x-transition:enter="ease-out duration-300"
                                                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                    x-transition:leave="ease-in duration-200"
                                                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 w-full max-w-md border border-gray-100">

                                                    {{-- Modal Header --}}
                                                    <div
                                                        class="px-6 py-5 border-b border-gray-100 flex items-center gap-3 bg-gray-50/50">
                                                        <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <h3 class="text-lg font-bold text-gray-900">Tambah Kategori
                                                                Baru</h3>
                                                            <p class="text-xs text-gray-700">Masukkan nama kategori baru.
                                                            </p>
                                                        </div>
                                                    </div>

                                                    {{-- Modal Body --}}
                                                    <div class="p-6">
                                                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama
                                                            Kategori</label>

                                                        <div class="relative">
                                                            <div
                                                                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                <svg class="w-5 h-5 text-gray-400" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                                    </path>
                                                                </svg>
                                                            </div>

                                                            <input type="text" x-model="newKategoriName"
                                                                @keydown.enter.prevent="saveKategori()"
                                                                class="w-full pl-10 rounded-lg border-gray-300 bg-gray-50 text-gray-900
                                                        focus:bg-white focus:border-blue-500 focus:ring-blue-200 transition py-2.5 px-3 text-sm font-medium placeholder-gray-400"
                                                                placeholder="Contoh: Logistik & Transportasi"
                                                                autocomplete="off">
                                                        </div>

                                                        {{-- Error Message --}}
                                                        <p x-show="errorMessage" x-transition
                                                            class="mt-3 text-red-600 text-xs flex items-center gap-1 font-medium bg-red-50 p-2.5 rounded-lg border border-red-100">
                                                            <svg class="w-4 h-4 flex-shrink-0" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                </path>
                                                            </svg>
                                                            <span x-text="errorMessage"></span>
                                                        </p>
                                                    </div>

                                                    {{-- Modal Footer --}}
                                                    <div
                                                        class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
                                                        <button type="button" @click="showModal = false"
                                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-2 focus:ring-gray-200 transition shadow-sm">
                                                            Batalkan
                                                        </button>

                                                        <button type="button" @click="saveKategori()"
                                                            :disabled="isLoading"
                                                            class="px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition shadow-md hover:shadow-lg flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">

                                                            <span x-show="!isLoading">Simpan Data</span>

                                                            <div x-show="isLoading" class="flex items-center gap-2">
                                                                <svg class="animate-spin h-4 w-4 text-white"
                                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                    viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12"
                                                                        cy="12" r="10" stroke="currentColor"
                                                                        stroke-width="4">
                                                                    </circle>
                                                                    <path class="opacity-75" fill="currentColor"
                                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                    </path>
                                                                </svg>
                                                                <span>Menyimpan...</span>
                                                            </div>
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Max Rej Subkon</label>
                                    <div class="relative">
                                        <input type="number"
                                            :name="`borongan[${index}][max_reject]`"
                                            x-model.number="row.max_reject"
                                            min="0"
                                            step="1"
                                            oninput="this.value = !!this.value && Math.abs(this.value) >= 0 ? Math.abs(Math.round(this.value)) : 0"
                                            placeholder="0"
                                            class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 focus:bg-white focus:border-blue-500 focus:ring-blue-200 transition py-2.5 px-4 text-sm font-medium" />
                                    </div>
                                </div>

                                <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-3 gap-6">
                                {{-- 3. Gaji Unit --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Harga
                                        B.Unit</label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">Rp</span>
                                        <input type="number" :name="`borongan[${index}][harga_unit]`"
                                            x-model.number="row.harga_unit" @input="autoHitung(row)"
                                            class="w-full pl-10 rounded-xl border-gray-200 bg-gray-50 text-sm font-bold text-gray-900 focus:bg-white focus:border-blue-500 py-3 px-4"
                                            placeholder="0">
                                    </div>
                                </div>

                                {{-- 4. Gaji Pekerja --}}
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Harga B.Pekerja (Pot.{{ $unitSelected->persentase_management_fee ?? 0 }}%)</label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-bold">Rp</span>
                                        <input type="number" :name="`borongan[${index}][harga_pekerja]`"
                                            x-model.number="row.harga_pekerja" @change="row.manual = true"
                                            class="w-full pl-10 rounded-xl border-gray-200 bg-gray-50 text-sm font-bold text-gray-900 focus:bg-white focus:border-blue-500 py-3 px-4"
                                            placeholder="0">
                                    </div>
                                </div>

                                {{-- 4. Satuan  --}}
                                <div x-data="satuanCombobox(row, index)" x-init="init()" class="relative">

                                        <div class="flex justify-between items-center mb-1">
                                            <label class="block text-sm font-bold text-gray-700">Satuan</label>

                                            <button type="button" @click="openModal()"
                                                class="text-xs flex items-center gap-1 text-blue-600 hover:text-blue-800 font-semibold transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4"></path>
                                                </svg>
                                                Tambah Baru
                                            </button>
                                        </div>

                                        <input
                                            type="hidden"
                                            :name="`borongan[${index}][satuan]`"
                                            x-model="row.satuanId"
                                        />

                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                </svg>
                                            </div>

                                            <input type="text" x-model="search" @input="open = true; selectedId = ''"
                                                @click="open = true" @click.outside="closeDropdown()"
                                                @keydown.escape="open = false" placeholder="Cari atau pilih satuan..."
                                                class="w-full pl-10 pr-10 rounded-lg border-gray-300 bg-gray-50 text-gray-900
                                            focus:bg-white focus:border-blue-500 focus:ring-blue-200 transition py-2.5 px-4 text-sm font-medium"
                                                autocomplete="off">

                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer"
                                                @click="toggleDropdown()">
                                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                                    :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <ul x-show="open" x-transition.opacity
                                            class="absolute w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto z-40 py-1">

                                            <template x-for="item in filteredList" :key="item.val">
                                                <li @click="selectOption(item)"
                                                    class="px-4 py-2.5 text-sm hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition flex justify-between">

                                                    <span x-text="item.label"
                                                        :class="selectedId == item.val ? 'font-bold text-blue-600' : 'text-gray-700'"></span>

                                                    <svg x-show="selectedId == item.val" class="w-4 h-4 text-blue-600"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </li>
                                            </template>

                                            <li x-show="filteredList.length === 0"
                                                class="px-4 py-3 text-sm text-gray-700 text-center">
                                                <p>Tidak ditemukan "<span x-text="search" class="font-bold"></span>"</p>
                                                <button type="button" @click="openModalWithSearch()"
                                                    class="mt-1 text-blue-600 hover:underline font-semibold text-xs">
                                                    + Tambah Baru
                                                </button>
                                            </li>
                                        </ul>

                                        {{-- MODAL: PERSIS, CUMA TEXT DIGANTI --}}
                                        <div x-show="showModal" style="display: none;" class="relative z-50"
                                            aria-labelledby="modal-title" role="dialog" aria-modal="true">

                                            {{-- BACKDROP: Fades in/out only (No scaling) --}}
                                            <div x-show="showModal" x-transition:enter="ease-out duration-300"
                                                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                                x-transition:leave="ease-in duration-200"
                                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                                                class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"></div>

                                            {{-- MODAL POSITIONING WRAPPER --}}
                                            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                                                <div
                                                    class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">

                                                    {{-- ACTUAL MODAL CARD: Scales and Pops up --}}
                                                    <div x-show="showModal" @click.outside="showModal = false"
                                                        x-transition:enter="ease-out duration-300"
                                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                        x-transition:leave="ease-in duration-200"
                                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                        class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 w-full max-w-md border border-gray-100">

                                                        {{-- Modal Header --}}
                                                        <div
                                                            class="px-6 py-5 border-b border-gray-100 flex items-center gap-3 bg-gray-50/50">
                                                            <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                                </svg>
                                                            </div>
                                                            <div>
                                                                <h3 class="text-lg font-bold text-gray-900">Tambah Satuan
                                                                    Baru</h3>
                                                                <p class="text-xs text-gray-700">Masukkan nama satuan baru.
                                                                </p>
                                                            </div>
                                                        </div>

                                                        {{-- Modal Body --}}
                                                        <div class="p-6">
                                                            <label class="block text-sm font-bold text-gray-700 mb-2">Nama
                                                                Satuan</label>

                                                            <div class="relative">
                                                                <div
                                                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                                    <svg class="w-5 h-5 text-gray-400" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                                            stroke-width="2"
                                                                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                                                        </path>
                                                                    </svg>
                                                                </div>

                                                                <input type="text" x-model="newSatuanName"
                                                                    @keydown.enter.prevent="saveSatuan()"
                                                                    class="w-full pl-10 rounded-lg border-gray-300 bg-gray-50 text-gray-900
                                                            focus:bg-white focus:border-blue-500 focus:ring-blue-200 transition py-2.5 px-3 text-sm font-medium placeholder-gray-400"
                                                                    placeholder="Contoh: Logistik & Transportasi"
                                                                    autocomplete="off">
                                                            </div>

                                                            {{-- Error Message --}}
                                                            <p x-show="errorMessage" x-transition
                                                                class="mt-3 text-red-600 text-xs flex items-center gap-1 font-medium bg-red-50 p-2.5 rounded-lg border border-red-100">
                                                                <svg class="w-4 h-4 flex-shrink-0" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                                    </path>
                                                                </svg>
                                                                <span x-text="errorMessage"></span>
                                                            </p>
                                                        </div>

                                                        {{-- Modal Footer --}}
                                                        <div
                                                            class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
                                                            <button type="button" @click="showModal = false"
                                                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-2 focus:ring-gray-200 transition shadow-sm">
                                                                Batalkan
                                                            </button>

                                                            <button type="button" @click="saveSatuan()"
                                                                :disabled="isLoading"
                                                                class="px-4 py-2 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition shadow-md hover:shadow-lg flex items-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">

                                                                <span x-show="!isLoading">Simpan Data</span>

                                                                <div x-show="isLoading" class="flex items-center gap-2">
                                                                    <svg class="animate-spin h-4 w-4 text-white"
                                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                        viewBox="0 0 24 24">
                                                                        <circle class="opacity-25" cx="12"
                                                                            cy="12" r="10" stroke="currentColor"
                                                                            stroke-width="4">
                                                                        </circle>
                                                                        <path class="opacity-75" fill="currentColor"
                                                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                                        </path>
                                                                    </svg>
                                                                    <span>Menyimpan...</span>
                                                                </div>
                                                            </button>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- FOOTER ACTION BAR --}}
            <div
                class="bg-white rounded-2xl shadow-[0_-8px_30px_-10px_rgba(0,0,0,0.1)] border border-gray-200 p-5 flex flex-col sm:flex-row items-center justify-between gap-4 sticky bottom-6 z-20">

                {{-- Left: Cancel Button & Info --}}
                <div class="flex items-center gap-4 w-full sm:w-auto order-2 sm:order-1">

                    {{-- Cancel Button --}}
                    <a href="{{ route('view.detail.unit', $unitSelected->id) }}"
                        class="px-6 py-3 text-sm font-bold text-gray-600 bg-white border border-gray-200 rounded-xl hover:bg-gray-50 hover:text-gray-900 transition shadow-sm w-full sm:w-auto text-center">
                        Batalkan
                    </a>
                    <div class="h-4 w-px bg-gray-300 hidden sm:block"></div>
                    {{-- Row Counter (Replaces the empty space) --}}
                    <div
                        class="hidden sm:flex items-center gap-2 text-xs font-medium text-gray-400 bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <span x-text="borongan.length + ' borongan akan diperbarui'"></span>
                    </div>
                </div>

                {{-- Right: Save Button --}}
                <div class="w-full sm:w-auto order-1 sm:order-2">
                    <button type="submit"
                        class="w-full sm:w-auto px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-200/50 transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <span>Simpan Data</span>
                        <svg class="w-5 h-5 text-emerald-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </div>

            </div>

        </form>
    </div>
@endsection

@section('scripts')
    <script>
        window.oldBorongan = [{
            id: {{ $borongan->id }},
            nama_item: @json($borongan->nama_item),
            kategoriId: {{ $borongan->kategori }},
            harga_unit: {{ $borongan->harga_unit }},
            max_reject: {{ $borongan->max_rej_subkon }},
            harga_pekerja: {{ $borongan->harga_pekerja }},
            satuanId: @json($borongan->satuan),
            manual: false
        }];

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
        window.satuanData = @json($satuanList);
        window.workersData = @json($pekerjaList ?? []);

        // 2. FORM LOGIC
        function boronganForm() {
            return {
                borongan: window.oldBorongan && window.oldBorongan.length ?
                    window.oldBorongan : [{
                        id: Date.now(),
                        kategoriId: null,
                        nama_item: '',
                        max_reject: '',
                        harga_unit: 0,
                        harga_pekerja: 0,
                        satuanId: null,
                        manual: false,
                    }],

                addRow() {
                    this.borongan.push({
                        id: Date.now(),
                        kategoriId: null,
                        nama_item: '',
                        max_reject: '',
                        harga_unit: 0,
                        harga_pekerja: 0,
                        satuanId: null,
                        manual: false,
                    });
                },

                removeRow(index) {
                    this.borongan.splice(index, 1);
                },

                autoHitung(row) {
                    if (row.manual) return;

                    const unit = Number(row.harga_unit) || 0;
                    row.harga_pekerja = Math.round(unit * 0.82);
                }
            }
        }


        // 3.

        function kategoriCombobox(row, index) {
            return {
                row,
                index,
                list: window.kategoriData || [],
                selectedId: row.kategoriId ?? null,
                search: '',
                open: false,
                showModal: false,
                newKategoriName: '',
                isLoading: false,
                errorMessage: '',

                init() {
                    if (this.row.kategoriId) {
                        this.selectedId = this.row.kategoriId;

                        const found = this.list.find(
                            item => String(item.val) === String(this.row.kategoriId)
                        );

                        if (found) {
                            this.search = found.label;
                        }
                    }
                },

                get filteredList() {
                    if (this.search === '') {
                        return this.list;
                    }
                    return this.list.filter(item =>
                        item.label.toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                toggleDropdown() {
                    this.open = !this.open;
                },

                closeDropdown() {
                    this.open = false;
                    const found = this.list.find(item => item.val == this.selectedId);
                    this.search = found ? found.label : '';
                },

                selectOption(item) {
                    this.selectedId = item.val;
                    this.row.kategoriId = item.val
                    this.search = item.label;
                    this.open = false;
                },

                /* MODAL */
                openModal() {
                    this.newKategoriName = '';
                    this.errorMessage = '';
                    this.showModal = true;
                },

                openModalWithSearch() {
                    this.newKategoriName = this.search;
                    this.errorMessage = '';
                    this.showModal = true;
                    this.open = false;
                },

                saveKategori() {
                    if (!this.newKategoriName) {
                        this.errorMessage = 'Nama tidak boleh kosong.';
                        return;
                    }

                    this.isLoading = true;
                    this.errorMessage = '';

                    fetch("{{ route('tambah.kategori.post') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            body: JSON.stringify({
                                nama: this.newKategoriName
                            })
                        })
                        .then(res => res.ok ? res.json() : Promise.reject(res))
                        .then(data => {
                            this.list = [...this.list, {
                                val: data.val,
                                label: data.label
                            }];

                            this.selectOption({
                                val: data.val,
                                label: data.label
                            });

                            this.showModal = false;
                            this.newKategoriName = '';

                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: {
                                    type: 'success',
                                    message: 'Kategori baru berhasil ditambahkan!'
                                }
                            }));
                        })
                        .catch(() => {
                            this.errorMessage = 'Gagal menyimpan. Nama mungkin sudah ada.';
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                }
            }
        }

        function satuanCombobox(row, index) {
            return {
                row,
                index,
                list: window.satuanData || [],
                selectedId: row.satuanId ?? null,
                search: '',
                open: false,
                showModal: false,
                newSatuanName: '',
                isLoading: false,
                errorMessage: '',

                init() {
                    if (this.row.satuanId) {
                        this.selectedId = this.row.satuanId;

                        const found = this.list.find(
                            item => String(item.val) === String(this.row.satuanId)
                        );

                        if (found) {
                            this.search = found.label;
                        }
                    }
                },

                get filteredList() {
                    if (this.search === '') {
                        return this.list;
                    }
                    return this.list.filter(item =>
                        item.label.toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                toggleDropdown() {
                    this.open = !this.open;
                },

                closeDropdown() {
                    this.open = false;
                    const found = this.list.find(item => item.val == this.selectedId);
                    this.search = found ? found.label : '';
                },

                selectOption(item) {
                    this.selectedId = item.val;
                    this.row.satuanId = item.val
                    this.search = item.label;
                    this.open = false;
                },

                /* MODAL */
                openModal() {
                    this.newSatuanName = '';
                    this.errorMessage = '';
                    this.showModal = true;
                },

                openModalWithSearch() {
                    this.newSatuanName = this.search;
                    this.errorMessage = '';
                    this.showModal = true;
                    this.open = false;
                },

                saveSatuan() {
                    if (!this.newSatuanName) {
                        this.errorMessage = 'Nama tidak boleh kosong.';
                        return;
                    }

                    this.isLoading = true;
                    this.errorMessage = '';

                    fetch("{{ route('tambah.satuan.post') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content')
                            },
                            body: JSON.stringify({
                                nama: this.newSatuanName
                            })
                        })
                        .then(res => res.ok ? res.json() : Promise.reject(res))
                        .then(data => {
                            this.list = [...this.list, {
                                val: data.val,
                                label: data.label
                            }];

                            this.selectOption({
                                val: data.val,
                                label: data.label
                            });

                            this.showModal = false;
                            this.newSatuanName = '';

                            window.dispatchEvent(new CustomEvent('notify', {
                                detail: {
                                    type: 'success',
                                    message: 'Satuan baru berhasil ditambahkan!'
                                }
                            }));
                        })
                        .catch(() => {
                            this.errorMessage = 'Gagal menyimpan. Nama mungkin sudah ada.';
                        })
                        .finally(() => {
                            this.isLoading = false;
                        });
                }
            }
        }

        // (Combobox logic for Unit remains the same...)
        document.addEventListener('alpine:init', () => {
            Alpine.data('combobox', (listData) => ({
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
