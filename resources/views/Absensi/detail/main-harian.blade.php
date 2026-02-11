@extends('layout')

@section('content')
    <style>
        [x-cloak] {
            display: none !important;
        }

        @keyframes float-harian-main {

            0%,
            100% {
                transform: translateY(0px) rotate(3deg);
            }

            50% {
                transform: translateY(-12px) rotate(6deg);
            }
        }

        /* Modal Absen */
        .animate-float-harian-main {
            animation: float-harian-main 4s ease-in-out infinite;
        }

        /* Premium scrollbar for the modal body */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #E5E7EB;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #3B82F6;
        }

        /* End of Modal Absen */
    </style>

    {{-- Error Alert Section --}}
    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div
                class="bg-red-50 border border-red-100 rounded-[2rem] p-5 shadow-sm shadow-red-100/50 flex items-start gap-4">
                {{-- Icon Container --}}
                <div
                    class="flex-shrink-0 w-10 h-10 bg-white rounded-2xl shadow-sm border border-red-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                {{-- Message Content --}}
                <div class="flex-1 pt-0.5">
                    <h3 class="text-md font-black text-red-900 uppercase tracking-tight mb-1">Terjadi Kesalahan</h3>
                    <ul class="space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li class="text-[13px] font-bold text-red-600/80 leading-relaxed">
                                • {{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Close Button (Optional) --}}
                <button onclick="this.parentElement.parentElement.remove()"
                    class="text-red-400 hover:text-red-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <div x-data="{
        selectedItems: [],
        showFilterDropdown: false,
        showAbsenModal: false,
        showAbsenStatusModal: false,
        searchQuery: '',
        filterStatus: '',
        filterVerifikasi: '',
        workerMap: @js($workerMap),

        // --- Input Jam Kerja State ---
        rowJam: {},
        rowHBN: {},
        rowCatatan: {},
        globalJam: '',
        globalHBN: false,

        // --- Status Absen State ---
        rowStatus: {},
        rowPaidLeave: {},
        globalStatus: '1',

        currentPage: {{ $pkwtPekerja->currentPage() }},
        allIds: {{ json_encode($pkwtPekerja->pluck('id')) }},

        // Logic to calculate Overtime
        calculateOvertime(id) {
            const worker = this.workerMap[id];
            if (!worker) return 0;

            const normal = parseFloat(worker.pkwt_hari_kerja || 0);
            const realita = parseFloat(this.rowJam[id] || 0);
            const isHbn = this.rowHBN[id]; // Mengambil status checkbox HBN

            let ot = 0;

            if (isHbn) {
                // Jika HBN dicentang, semua jam realita dihitung sebagai lembur
                ot = realita;
            } else {
                // Jika hari normal, lembur = realita - normal
                ot = realita - normal;
            }

            // Kembalikan 0 jika hasilnya negatif, dan format desimal jika perlu
            const result = ot > 0 ? ot : 0;
            return Number.isInteger(result) ? result : result.toFixed(1);
        },

        // Inside your main x-data object
        initAbsenModal() {
            this.selectedItems.forEach(id => {
                const worker = this.workerMap[id];
                if (worker) {
                    // Jika ada data jam di DB, tampilkan. Jika null, kosongkan.
                    this.rowJam[id] = worker.existing_jam !== null ? worker.existing_jam : '';

                    // PAKSA KE BOOLEAN: Jika existing_hbn == 1 maka TRUE (Centang)
                    this.rowHBN[id] = worker.existing_hbn == 1;

                    this.rowCatatan[id] = worker.existing_catatan || '';
                }
            });
            this.showAbsenModal = true;
        },

        toggleHbnGlobal(id) {
            // Get the new state of the checkbox that was just clicked
            const newState = this.rowHBN[id];

            // Apply this state to every worker currently in the modal
            this.selectedItems.forEach(itemId => {
                this.rowHBN[itemId] = newState;
            });

            // Optional: Sync the global header checkbox if you have one
            this.globalHBN = newState;
        },

        initStatusModal() {
            this.selectedItems.forEach(id => {
                const worker = this.workerMap[id];
                if (worker) {
                    this.rowStatus[id] = worker.existing_status || '0';
                    this.rowCatatan[id] = worker.existing_catatan || '';

                    // PERBAIKAN DI SINI:
                    // Paksa menjadi true jika nilainya 1, dan false jika 0
                    this.rowPaidLeave[id] = (worker.existing_paid == 1);
                }
            });
            this.showAbsenStatusModal = true;
        },

        applyGlobalValues() {
            this.selectedItems.forEach(id => {
                // Hanya timpa jika globalJam diisi
                if (this.globalJam !== '') {
                    this.rowJam[id] = this.globalJam;
                }
                this.rowHBN[id] = this.globalHBN;
            });
        },

        toggleAll() {
            this.selectedItems = this.selectedItems.length === this.allIds.length ? [] : [...this.allIds];
        },

        async updateTable(targetUrl = null) {
            let url = targetUrl ? new URL(targetUrl) : new URL(window.location.href);
            if (!targetUrl) {
                if (!this.searchQuery && !this.filterVerifikasi && !this.filterStatus) {
                    url.searchParams.set('page', this.currentPage);
                } else {
                    url.searchParams.set('page', '1');
                }
            }
            url.searchParams.set('search', this.searchQuery);
            url.searchParams.set('status', this.filterStatus);
            url.searchParams.set('statusVerif', this.filterVerifikasi);

            try {
                const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const html = await response.text();
                document.getElementById('main-table-body').innerHTML = html;
                const provider = document.getElementById('new-ids-provider-full');
                if (provider) this.allIds = JSON.parse(provider.dataset.ids);
            } catch (error) { console.error(error); }
        },

        resetFilters() {
            this.searchQuery = '';
            this.filterStatus = '';
            this.filterVerifikasi = '';
        },

        confirmSubmit(formRef) {
            Swal.fire({
                title: 'Simpan Data?',
                text: 'Pastikan data sudah benar.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563EB',
                confirmButtonText: 'Ya, Simpan',
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.showLoading();
                    this.$refs[formRef].submit();
                }
            });
        }
    }" x-init="$watch('searchQuery', () => updateTable());
    $watch('filterStatus', () => updateTable());
    $watch('filterVerifikasi', () => updateTable());">

        {{-- HEADER SECTION --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
            <div
                class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-gray-100 relative overflow-hidden">

                {{-- Surprise Element: Background Pattern Decoration --}}
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-blue-50 rounded-full blur-3xl opacity-40">
                </div>
                <div
                    class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 bg-emerald-50 rounded-full blur-3xl opacity-40">
                </div>

                <div class="relative z-10">
                    {{-- Top Row: Breadcrumb & Date Capsule --}}
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                        <a href="{{ route('view.absensi') }}"
                            class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-blue-600 transition group">
                            <svg class="w-3.5 h-3.5 transform group-hover:-translate-x-1 transition" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                            </svg>
                            Kembali ke Unit
                        </a>

                        {{-- DYNAMIC DATE PILL --}}
                        <div
                            class="flex items-center gap-3 bg-gray-50 px-4 py-1.5 rounded-full border border-gray-100 shadow-inner">
                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-[11px] font-black text-gray-600 uppercase tracking-widest">
                                Periode: {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
                            </span>
                            @if (\Carbon\Carbon::parse($date)->isToday())
                                <span class="flex h-2 w-2 relative">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8">
                        {{-- Left Side: Identity & Branding --}}
                        <div class="space-y-4">
                            <div class="flex items-center gap-5">
                                <div class="h-16 w-2 bg-blue-600 rounded-full shadow-[0_0_20px_rgba(37,99,235,0.4)]"></div>
                                <div>
                                    <div class="flex items-center gap-3 mb-1">
                                        <h1 class="text-5xl font-black text-gray-900 tracking-tight leading-none">
                                            Pengelolaan
                                            Absensi<span class="text-blue-600">.</span>
                                        </h1>
                                    </div>

                                    <div class="flex items-center gap-3 mt-4">
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

                            <p class="text-base text-gray-500 flex items-center gap-2 ml-7">
                                Unit Kerja:
                                <span
                                    class="font-bold text-gray-800 italic underline decoration-blue-200 underline-offset-8 decoration-2">
                                    {{ $unit->nama_unit }}
                                </span>
                            </p>
                        </div>

                        {{-- Right Side: Grid Stats Cards --}}
                        <div class="grid grid-cols-2 gap-3">
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
                                    Total Hadir</p>
                                <div class="flex items-end gap-1">
                                    <span
                                        class="text-3xl font-black text-emerald-600 leading-none">{{ $totalHadir }}</span>
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
                                <span x-show="filterStatus" class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                <span x-show="filterVerifikasi" class="w-2 h-2 bg-blue-500 rounded-full"></span>
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
                                        class="text-md font-medium text-gray-400 hover:text-red-500 hover:bg-red-50 px-2 py-1 rounded transition">
                                        Reset Filter
                                    </button>
                                </div>

                                <div class="space-y-5">

                                    {{-- STATUS FILTER --}}
                                    <div x-data="{
                                        open: false,
                                        list: [{ val: '', label: 'Semua Status' },
                                            { val: '1', label: 'Hadir' },
                                            { val: '3', label: 'Cuti' },
                                        ]
                                    }" class="relative">
                                        <label
                                            class="block text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1.5">Status
                                            Absen</label>
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
                                <div class="space-y-5 mt-7">

                                    {{-- STATUS FILTER --}}
                                    <div x-data="{
                                        open: false,
                                        list: [{ val: '', label: 'Semua Status' },
                                            { val: '1', label: 'Disetujui' },
                                            { val: '0', label: 'Menunggu Persetujuan' }
                                        ]
                                    }" class="relative mb-2">
                                        <label
                                            class="block text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1.5">Status
                                            Verifikasi Absen</label>
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
                                                x-text="list.find(x => x.val == filterVerifikasi)?.label || 'Semua Status'"></span>
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
                                                    <li @click="filterVerifikasi = item.val; open = false"
                                                        class="px-4 py-2.5 text-sm cursor-pointer transition flex items-center gap-2"
                                                        :class="filterVerifikasi == item.val ?
                                                            'bg-blue-50 text-blue-700 font-semibold' :
                                                            'text-gray-700 hover:bg-gray-50'">
                                                        <svg x-show="filterVerifikasi == item.val"
                                                            class="w-4 h-4 text-blue-600" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        <span x-show="filterVerifikasi != item.val"
                                                            class="w-4 h-4"></span>
                                                        <span x-text="item.label"></span>
                                                    </li>
                                                </template>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Info Helper for Preview Limitation --}}
                                <div class="mt-6 pt-4 border-t border-gray-50 text-center">
                                    <p class="text-[10px] text-gray-400">Filter akan diterapkan otomatis</p>
                                </div>
                            </div>

                            {{-- Floating Action Bar --}}
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
                                            class="px-3 py-2 text-md font-bold text-gray-500 hover:text-gray-700 transition">Batal</button>
                                        <div class="h-6 w-px bg-gray-200 mx-1"></div>

                                        {{-- Trigger Modal Button --}}
                                        <button @click="initAbsenModal()"
                                            class="flex items-center gap-1.5 px-4 py-2 bg-blue-50 text-blue-700 border border-blue-100 rounded-xl text-xs font-bold hover:bg-blue-600 hover:text-white transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Absen
                                        </button>

                                        {{-- Trigger Modal Button --}}
                                        <button @click="initStatusModal()"
                                            class="flex items-center gap-1.5 px-4 py-2 bg-blue-50 text-cyan-700 border border-blue-100 rounded-xl text-xs font-bold hover:bg-cyan-600 hover:text-white transition-all">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Status Absen
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- MODAL: INPUT JAM KERJA -->
                            <div x-show="showAbsenModal"
                                class="fixed inset-0 z-[100] flex items-center justify-center p-6 sm:p-6" x-cloak>
                                {{-- Glass Backdrop --}}
                                <div x-show="showAbsenModal" x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                    x-transition:leave="ease-in duration-200" @click="showAbsenModal = false"
                                    class="fixed inset-0 bg-gray-900/60 backdrop-blur-md"></div>

                                {{-- Modal Content - Increased max-width to 4xl to accommodate the note field --}}
                                <div x-show="showAbsenModal" x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0 scale-95 translate-y-8"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    class="relative bg-white rounded-[2.5rem] shadow-[0_30px_100px_rgba(0,0,0,0.25)] w-full max-w-6xl overflow-hidden flex flex-col max-h-[85vh] border border-white/20">

                                    {{-- Header Section --}}
                                    <div
                                        class="px-10 py-8 border-b border-gray-100 bg-gradient-to-b from-gray-50 to-white relative">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex items-center gap-3 mb-1">
                                                    <span
                                                        class="px-3 py-1 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-lg">Presensi
                                                        Harian</span>
                                                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">Input Jam
                                                        Kerja<span class="text-blue-600">.</span></h3>
                                                </div>
                                                <p class="text-sm text-gray-500 font-medium">Mengatur kehadiran untuk <span
                                                        x-text="selectedItems.length"
                                                        class="text-blue-600 font-black"></span> pekerja terpilih.</p>
                                            </div>
                                            <button @click="showAbsenModal = false"
                                                class="group p-3 bg-gray-100 hover:bg-red-50 rounded-2xl transition-all">
                                                <svg class="w-6 h-6 text-gray-400 group-hover:text-red-500 transition-colors"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div
                                            class="mt-6 p-4 bg-blue-50/50 rounded-2xl border border-blue-100 flex flex-wrap items-center justify-between gap-4">
                                            <div class="flex items-center gap-3">
                                                <div class="p-2 bg-blue-600 rounded-xl shadow-lg shadow-blue-200">
                                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                    </svg>
                                                </div>
                                                <span
                                                    class="text-[11px] font-black text-blue-800 uppercase tracking-wider">Terapkan
                                                    ke semua</span>
                                            </div>

                                            <div class="flex items-center gap-6">
                                                {{-- Global Jam Kerja --}}
                                                <div class="flex items-center gap-2">
                                                    <span class="text-[10px] font-bold text-blue-400 uppercase">Jam
                                                        Kerja:</span>
                                                    <input type="number" x-model="globalJam" placeholder="0"
                                                        class="w-20 px-3 py-2 bg-white border border-blue-200 rounded-xl text-md font-bold text-blue-700 outline-none focus:ring-2 focus:ring-blue-100">
                                                </div>

                                                {{-- Global HBN --}}
                                                <label class="flex items-center gap-2 cursor-pointer group">
                                                    <input type="checkbox" x-model="globalHBN"
                                                        class="w-4 h-4 rounded border-blue-300 text-blue-600 focus:ring-blue-500">
                                                    <span
                                                        class="text-[10px] font-bold text-blue-400 uppercase group-hover:text-blue-600 transition-colors">HBN
                                                        (Libur)</span>
                                                </label>

                                                <button type="button" @click="applyGlobalValues()"
                                                    class="px-6 py-2.5 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-200 active:scale-95">
                                                    Terapkan
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Scrollable List Area --}}
                                    <form
                                        action="{{ route('absensi.bulk.update', ['id_unit' => $unit->id, 'date' => $date]) }}"
                                        method="POST" x-ref="absenForm" x-data="absenFormHandler()"
                                        class="flex-1 overflow-y-auto custom-scrollbar bg-white">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="date" value="{{ $date }}">

                                        {{-- Table Header --}}
                                        <div
                                            class="px-10 py-4 bg-gray-50/80 border-b border-gray-100 hidden lg:flex items-center text-[11px] font-black text-gray-400 uppercase tracking-[0.15em]">
                                            <div class="w-12 text-center">No.</div>
                                            <div class="w-72">Informasi Pekerja</div>
                                            <div class="w-24 text-center">Normal</div>
                                            <div class="w-32 text-center">Jam Realita</div>
                                            <div class="w-24 text-center">Overtime</div>
                                            <div class="w-24 text-center">HBN</div>
                                            <div class="flex-1 px-4">Keterangan / Catatan</div>
                                        </div>

                                        <div class="divide-y divide-gray-100">
                                            <template x-for="(id, index) in selectedItems" :key="id">
                                                <div
                                                    class="group flex flex-col lg:flex-row lg:items-center px-10 py-4 hover:bg-blue-50/30 transition-all duration-300 gap-y-4 lg:gap-y-0">

                                                    {{-- No. Column --}}
                                                    <div class="w-12 flex-shrink-0 text-center">
                                                        <span
                                                            class="text-[11px] font-bold text-gray-300 group-hover:text-blue-400 transition-colors"
                                                            x-text="index + 1 + '.'"></span>
                                                    </div>

                                                    {{-- 1. Identity Column --}}
                                                    <div class="w-72 flex-shrink-0 flex items-center gap-3 pr-4">
                                                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center text-[10px] font-black text-gray-400 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300"
                                                            x-text="workerMap[id]?.initials"></div>
                                                        <div class="min-w-0">
                                                            <p class="text-sm font-bold text-gray-800 truncate"
                                                                x-text="workerMap[id]?.nama"></p>
                                                            <p class="text-[10px] font-medium text-gray-400 tracking-tight"
                                                                x-text="workerMap[id]?.nik"></p>
                                                        </div>
                                                    </div>

                                                    {{-- 2. Jam Normal Column --}}
                                                    <div class="w-24 flex-shrink-0 text-center">
                                                        <div
                                                            class="inline-block px-3 py-1 bg-gray-50 border border-gray-100 rounded-lg">
                                                            <span class="text-xs font-black text-gray-500"
                                                                x-text="(workerMap[id]?.pkwt_hari_kerja || 0) + ' /jam'"></span>
                                                        </div>
                                                        <input type="hidden" :name="'data[' + id + '][jam_normal]'"
                                                            :value="workerMap[id]?.pkwt_hari_kerja || 0">
                                                    </div>

                                                    {{-- 3. Jam Realita Column --}}
                                                    <div class="w-32 flex-shrink-0 px-4">
                                                        <div class="relative group/input flex items-center">
                                                            <input type="number" step="0.5" min="0"
                                                                :name="'data[' + id + '][jam_aktual]'" x-model="rowJam[id]"
                                                                class="w-full h-9 pl-2 pr-10 text-center bg-white border border-gray-200 rounded-xl text-sm font-black text-blue-600 focus:ring-4 focus:ring-blue-50 focus:border-blue-400 outline-none transition-all">
                                                            {{-- Label /jam di dalam input --}}
                                                            <span
                                                                class="absolute right-3 text-[10px] font-bold text-blue-300 pointer-events-none">/jam</span>
                                                        </div>
                                                    </div>

                                                    {{-- 4. Overtime Column --}}
                                                    <div class="w-24 flex-shrink-0 text-center">
                                                        <span class="text-sm font-black transition-colors"
                                                            :class="calculateOvertime(id) > 0 ? 'text-orange-500' :
                                                                'text-gray-200'"
                                                            x-text="'+' + calculateOvertime(id) + ' /jam'"></span>
                                                        <input type="hidden" :name="'data[' + id + '][overtime]'"
                                                            :value="calculateOvertime(id)">
                                                    </div>

                                                    {{-- 5. HBN Column --}}
                                                    <div class="w-24 flex-shrink-0 flex justify-center">
                                                        <div class="relative flex items-center justify-center">
                                                            <input type="hidden" :name="'data[' + id + '][is_hbn]'" value="0">

                                                            <input type="checkbox"
                                                                :name="'data[' + id + '][is_hbn]'"
                                                                value="1"
                                                                x-model="rowHBN[id]"
                                                                {{-- This is the key change: --}}
                                                                @change="toggleHbnGlobal(id)"
                                                                class="w-5 h-5 rounded-md border-gray-300 text-blue-600 focus:ring-blue-100 transition-all cursor-pointer shadow-sm">
                                                        </div>
                                                    </div>

                                                    {{-- 6. Catatan Column --}}
                                                    <div class="flex-1 min-w-[200px] px-4">
                                                        <input type="text" :name="'data[' + id + '][catatan]'"
                                                            x-model="rowCatatan[id]"
                                                            placeholder="Tambah catatan harian..."
                                                            class="w-full h-9 bg-transparent border-b border-transparent hover:border-gray-100 focus:border-blue-400 focus:bg-blue-50/30 rounded-lg px-3 text-[11px] font-medium text-gray-600 transition-all outline-none italic placeholder:text-gray-300">
                                                    </div>
                                                </div>
                                            </template>
                                        </div>

                                        {{-- STICKY FOOTER --}}
                                        <div
                                            class="sticky bottom-0 px-10 py-5 bg-white/95 backdrop-blur-md border-t border-gray-100 flex items-center justify-end gap-4">
                                            <button type="button" @click="showAbsenModal = false"
                                                class="px-6 py-2.5 text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition">Batal</button>
                                            <button type="button" @click="confirmSubmit()"
                                                class="px-8 py-3 bg-blue-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-xl hover:bg-blue-700 shadow-xl shadow-blue-200 transition-all active:scale-95">Simpan
                                                Semua</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- MODAL: UPDATE STATUS ABSENSI -->
                            <div x-show="showAbsenStatusModal"
                                class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-cloak>
                                {{-- Glass Backdrop --}}
                                <div x-show="showAbsenStatusModal" x-transition.opacity
                                    @click="showAbsenStatusModal = false"
                                    class="fixed inset-0 bg-gray-900/60 backdrop-blur-md"></div>

                                {{-- Modal Content --}}
                                <div x-show="showAbsenStatusModal" x-transition:enter="ease-out duration-300"
                                    x-transition:enter-start="opacity-0 scale-95 translate-y-8"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    class="relative bg-white rounded-[2.5rem] shadow-[0_30px_100px_rgba(0,0,0,0.25)] w-full max-w-5xl overflow-hidden flex flex-col max-h-[85vh] border border-white/20">

                                    {{-- Header Section --}}
                                    <div
                                        class="px-10 py-8 border-b border-gray-100 bg-gradient-to-b from-gray-50 to-white relative">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <div class="flex items-center gap-3 mb-1">
                                                    <span
                                                        class="px-3 py-1 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-lg">Keterangan
                                                        Khusus</span>
                                                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">Status &
                                                        Alasan<span class="text-blue-600">.</span></h3>
                                                </div>
                                                <p class="text-sm text-gray-500 font-medium">Memperbarui alasan
                                                    ketidakhadiran untuk <span x-text="selectedItems.length"
                                                        class="text-blue-600 font-black"></span> pekerja.</p>
                                            </div>
                                            <button @click="showAbsenStatusModal = false"
                                                class="group p-3 bg-gray-100 hover:bg-red-50 rounded-2xl transition-all">
                                                <svg class="w-6 h-6 text-gray-400 group-hover:text-red-500 transition-colors"
                                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    {{-- Scrollable Table Area --}}
                                    <form
                                        action="{{ route('absensi.bulk.update-status', ['id_unit' => $unit->id, 'date' => $date]) }}"
                                        method="POST" x-ref="absenForm" x-data="absenFormHandler()"
                                        class="flex-1 overflow-y-auto custom-scrollbar bg-white p-8 pt-6">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="date" value="{{ $date }}">

                                        <table class="w-full border-separate border-spacing-y-4">
                                            <thead>
                                                <tr
                                                    class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                                    <th class="w-12 text-center pb-2">No.</th>
                                                    <th class="text-left pb-2 pl-4">Informasi Pekerja</th>
                                                    <th class="text-left pb-2 w-56">Tipe Absensi</th>
                                                    <th class="text-center pb-2 w-40">Cuti <br>Berbayar</th>
                                                    <th class="text-left pb-2 pr-6">Catatan <br>/ Keterangan</th>
                                                </tr>
                                            </thead>
                                            </thead>
                                            <tbody>
                                                <template x-for="(id, index) in selectedItems" :key="id">
                                                    <tr class="group">
                                                        {{-- 1. Nomor Urut --}}
                                                        <td
                                                            class="py-4 text-center bg-gray-50/50 rounded-l-2xl border-y border-l border-gray-100">
                                                            <span class="text-xs font-black text-gray-300"
                                                                x-text="index + 1 + '.'"></span>
                                                        </td>

                                                        {{-- 2. Informasi Pekerja --}}
                                                        <td class="py-4 pl-4 bg-gray-50/50 border-y border-gray-100">
                                                            <div class="min-w-0 w-64">
                                                                <p class="text-sm font-black text-gray-900 leading-tight truncate"
                                                                    x-text="workerMap[id]?.nama"
                                                                    :title="workerMap[id]?.nama"></p>
                                                                <p class="text-[10px] font-bold text-gray-400 mt-0.5 tracking-widest truncate"
                                                                    x-text="workerMap[id]?.nik"></p>
                                                            </div>
                                                        </td>

                                                        {{-- 3. Tipe Absensi (Dropdown) --}}
                                                        <td class="py-4 px-2 bg-gray-50/50 border-y border-gray-100">
                                                            <div x-data="{
                                                                open: false,
                                                                list: [
                                                                    { val: '2', label: 'Izin' },
                                                                    { val: '3', label: 'Cuti' },
                                                                    { val: '4', label: 'Sakit' },
                                                                    { val: '5', label: 'Rencana Cuti' },
                                                                    { val: '6', label: 'Absen' },
                                                                ]
                                                            }" class="relative">
                                                                <input type="hidden"
                                                                    :name="'data[' + id + '][status_kehadiran]'"
                                                                    x-model="rowStatus[id]">

                                                                <div @click="open = !open" @click.outside="open = false"
                                                                    class="flex items-center justify-between px-4 py-2.5 bg-white border border-gray-200 rounded-xl cursor-pointer hover:border-blue-400 transition-all shadow-sm">
                                                                    <span class="text-xs font-black text-gray-700"
                                                                        x-text="list.find(x => x.val == rowStatus[id])?.label || 'Hadir'">
                                                                    </span>
                                                                    <svg class="w-4 h-4 text-blue-500 transition-transform duration-300"
                                                                        :class="open ? 'rotate-180' : ''" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="3"
                                                                            d="M19 9l-7 7-7-7" />
                                                                    </svg>
                                                                </div>

                                                                <div x-show="open" x-transition.origin.top
                                                                    class="absolute w-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-xl z-[110] overflow-hidden">
                                                                    <template x-for="item in list" :key="item.val">
                                                                        <div @click="rowStatus[id] = item.val; open = false"
                                                                            class="px-4 py-3 text-xs font-bold cursor-pointer transition-colors"
                                                                            :class="rowStatus[id] == item.val ?
                                                                                'bg-blue-50 text-blue-600' :
                                                                                'text-gray-600 hover:bg-gray-50'">
                                                                            <span x-text="item.label"></span>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </td>

                                                        {{-- 4. Cuti Berbayar (Checkbox) --}}
                                                        <td
                                                            class="py-4 bg-gray-50/50 border-y border-gray-100 text-center">
                                                            <div class="flex items-center justify-center">
                                                                <input type="hidden"
                                                                    :name="'data[' + id + '][is_paid_leave]'"
                                                                    value="0">
                                                                <input type="checkbox"
                                                                    :name="'data[' + id + '][is_paid_leave]'"
                                                                    value="1" x-model="rowPaidLeave[id]"
                                                                    class="w-5 h-5 rounded-lg border-gray-300 text-blue-600 focus:ring-blue-100 transition-all cursor-pointer shadow-sm">
                                                            </div>
                                                        </td>

                                                        {{-- 5. Catatan --}}
                                                        <td
                                                            class="py-4 pr-6 bg-gray-50/50 rounded-r-2xl border-y border-r border-gray-100">
                                                            <div class="flex items-center">
                                                                <input type="text" :name="'data[' + id + '][catatan]'"
                                                                    x-model="rowCatatan[id]"
                                                                    placeholder="Tambahkan keterangan..."
                                                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-bold text-gray-700 focus:ring-4 focus:ring-blue-50 focus:border-blue-400 transition-all placeholder:text-gray-300 shadow-sm outline-none">
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>

                                        {{-- Sticky Footer --}}
                                        <div
                                            class="sticky bottom-0 mt-10 py-6 bg-white/90 backdrop-blur-md border-t border-gray-100 flex items-center justify-end gap-4">
                                            <button type="button" @click="showAbsenStatusModal = false"
                                                class="px-6 py-3 text-xs font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition">Batal</button>
                                            <button type="button" @click="confirmSubmit()"
                                                class="px-10 py-4 bg-blue-600 text-white text-xs font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-blue-700 shadow-2xl shadow-blue-200 transition-all active:scale-95">
                                                Simpan Keterangan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TABLE --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50 border-b border-gray-100">
                            <tr>
                                <th class="pl-8 py-4 w-10">
                                    <input type="checkbox" @click="toggleAll()"
                                        :checked="selectedItems.length === allIds.length && allIds.length > 0"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-100">
                                </th>
                                <th class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest">
                                    Profil Pekerja
                                </th>
                                <th class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    Durasi Kerja (Realita/Normal)
                                </th>
                                <th class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    Akumulasi OT
                                </th>
                                {{-- Kolom Baru --}}
                                <th class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    HBN
                                </th>
                                <th class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    Cuti Berbayar
                                </th>
                                <th class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    Kondisi Absensi
                                </th>
                                <th class="px-4 py-4 text-[11px] font-black text-center text-gray-400 uppercase tracking-widest">
                                    Validasi Data
                                </th>
                                <th class="px-4 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest">
                                    Memo
                                </th>
                            </tr>
                        </thead>
                        <tbody id="main-table-body" class="divide-y divide-gray-50">
                            @include('Absensi.partials.main-harian-table')
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
    </div>
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

        function absenFormHandler() {
            return {
                confirmSubmit() {
                    Swal.fire({
                        title: 'Simpan Data Absensi?',
                        text: 'Pastikan semua data sudah benar sebelum disimpan.',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#2563EB',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Simpan',
                        cancelButtonText: 'Batal',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: 'Menyimpan...',
                                text: 'Mohon tunggu',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading()
                                }
                            })

                            this.$refs.absenForm.submit()
                        }
                    })
                }
            }
        }
    </script>
@endsection
