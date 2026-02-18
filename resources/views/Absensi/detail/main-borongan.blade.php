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
            border-radius: 12px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #3B82F6;
        }


        /* End of Modal Absen */
    </style>

    {{-- Error Alert Section --}}
    @if ($errors->any())
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
            <div class="bg-red-50 border border-red-100 rounded-[2rem] p-5 shadow-sm shadow-red-100/50 flex items-start gap-4">
                {{-- Icon Container --}}
                <div class="flex-shrink-0 w-10 h-10 bg-white rounded-2xl shadow-sm border border-red-100 flex items-center justify-center">
                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                <button onclick="this.parentElement.parentElement.remove()" class="text-red-400 hover:text-red-600 transition-colors">
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
        statusValue: '1',
        workerMap: @js($workerMap),
        rowItems: @js($existingBorongan),
        barangLookup: @js($barangLookup),

        currentIndex: 0,
        // Computed property to get current worker ID
        get currentWorkerId() {
            return this.selectedItems[this.currentIndex];
        },

        formatRibuan(number) {
            if (!number) return '0';
            return new Intl.NumberFormat('id-ID').format(number);
        },

        updatePrices(workerId, rIdx) {
            const row = this.rowItems[workerId][rIdx];
            const item = this.barangLookup[row.id_barang];

            if (item && (row.max_rej_subkon === undefined || row.max_rej_subkon === null)) {
                row.max_rej_subkon = item.max_rej_subkon;
            }

            // 1. Calculate the current Total QTY
            const totalQTY = (parseInt(row.FD) || 0) +
                (parseInt(row.act_rej) || 0) +
                (parseInt(row.good_mc) || 0);
            row.totalQTY = totalQTY;

            // 2. Hitung Max Reject yang Diizinkan (F9 di rumus Anda)
            row.act_rej_max = Math.round((row.max_rej_subkon / 100) * totalQTY);

            // 3. Hitung Rej. MC Dibebankan (Rumus: =IF(F9>=G9;0;G9-F9))
            // F9 = row.act_rej_max | G9 = row.act_rej
            if (row.act_rej_max >= row.act_rej) {
                row.rej_mc_dibebankan = 0;
            } else {
                row.rej_mc_dibebankan = row.act_rej - row.act_rej_max;
            }

            const totalBayar = row.FD + row.good_mc - row.rej_mc_dibebankan;

            if (item && totalQTY > 0) {
                // rumus: totalQTY * harga_unit
                row.bayaranPerusahaan = totalBayar * item.harga_unit;

                // rumus: totalQTY * harga_pekerja
                row.bayaranItem = totalBayar * item.harga_pekerja;
            } else {
                // Reset to 0 if no item selected or QTY is 0
                row.bayaranPerusahaan = 0;
                row.bayaranItem = 0;
            }
        },

        calculateAllExistingPrices() {
            if (!this.rowItems) return;

            // Loop through every worker in the existing data
            Object.keys(this.rowItems).forEach(workerId => {
                // Loop through every row for that worker
                this.rowItems[workerId].forEach((row, rIdx) => {
                    this.updatePrices(workerId, rIdx);
                });
            });
        },

        // Function to add a new row for a worker
        addBoronganRow(workerId) {
            if (!this.rowItems) this.rowItems = {};
            if (!this.rowItems[workerId]) this.rowItems[workerId] = [];

            // Batasi jumlah baris berdasarkan jumlah barang yang tersedia
            const totalBarangTersedia = {{ count($barangs) }};

            if (this.rowItems[workerId].length >= totalBarangTersedia) {
                // Opsional: Anda bisa mengganti alert ini dengan toast notification yang lebih cantik
                alert('Semua jenis barang sudah ditambahkan untuk pekerja ini.');
                return;
            }

            this.rowItems[workerId].push({
                id_barang: '',
                FD: 0,
                act_rej_max: 0,
                good_mc: 0,
                act_rej: 0,
                rej_mc: 0,
                max_rej_subkon: 0,
                rej_mc_dibebankan: 0,
                bayaranPerusahaan: 0,
                bayaranItem: 0,
                totalQTY: 0,
                catatan: '',
                fileName: null // Pastikan variabel ini konsisten dengan input file Anda
            });
        },

        // Track the current page number
        currentPage: {{ $pkwtPekerja->currentPage() }},

        allIds: {{ json_encode($pkwtPekerja->pluck('id')) }},

        globalMasuk: '08:00',
        globalKeluar: '17:00',
        globalStatus: '6', // default absen
        rowStatus: {},
        rowCatatan: {},

        applyGlobalTime() {
            this.selectedItems.forEach(id => {
                const rowMasuk = document.getElementById('masuk-' + id);
                const rowKeluar = document.getElementById('keluar-' + id);
                if (rowMasuk) rowMasuk.value = this.globalMasuk;
                if (rowKeluar) rowKeluar.value = this.globalKeluar;
            });
        },

        initStatusModal() {
            // Buat salinan objek baru untuk memicu reaktivitas
            let newStatus = { ...this.rowStatus };
            let newCatatan = { ...this.rowCatatan };

            this.selectedItems.forEach(id => {
                // Set default ke '2' (Cuti) jika datanya belum ada
                if (!newStatus[id]) newStatus[id] = '2';
                if (!newCatatan[id]) newCatatan[id] = '';
            });

            this.rowStatus = newStatus;
            this.rowCatatan = newCatatan;
            this.showAbsenStatusModal = true;
        },

        applyGlobalStatus() {
            this.selectedItems.forEach(id => {
                this.rowStatus[id] = this.globalStatus;
            });
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
                if (!this.searchQuery && !this.filterVerifikasi && !this.filterStatus) {
                    this.currentPage = url.searchParams.get('page') || 1;
                }
            } else {
                // If called from Typing Search/Filter
                url = new URL(window.location.href);

                // Logic: If user is typing, we force page 1 to find results.
                // If user cleared everything, we restore the saved currentPage.
                if (!this.searchQuery && !this.filterVerifikasi && !this.filterStatus) {
                    url.searchParams.set('page', this.currentPage);
                } else {
                    url.searchParams.set('page', '1');
                }
            }

            // Apply all filters to the URL
            url.searchParams.set('search', this.searchQuery);
            url.searchParams.set('status', this.filterStatus);
            url.searchParams.set('statusVerif', this.filterVerifikasi);

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
            this.filterVerifikasi = '';
            // This will trigger the $watch which calls updateTable()
            // Our logic inside updateTable will see filters are empty and restore Page 2
        },

    }" x-init="calculateAllExistingPrices();
    $watch('searchQuery', () => updateTable());
    $watch('filterStatus', () => updateTable());
    $watch('filterVerifikasi', () => updateTable());">

        {{-- HEADER SECTION --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10">
            <div
                class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-[0_20px_50px_rgba(0,0,0,0.05)] border border-gray-100 relative overflow-hidden">

                {{-- Surprise Element: Background Pattern Decoration --}}
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-orange-50 rounded-full blur-3xl opacity-40">
                </div>
                <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 bg-yellow-50 rounded-full blur-3xl opacity-40">
                </div>

                <div class="relative z-10">
                    {{-- Top Row: Breadcrumb & Date Capsule --}}
                    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
                        <a href="{{ route('view.absensi') }}"
                            class="inline-flex items-center gap-2 text-[12px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-orange-600 transition group">
                            <svg class="w-3.5 h-3.5 transform group-hover:-translate-x-1 transition" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                            </svg>
                            Kembali ke Unit
                        </a>

                        {{-- DYNAMIC DATE PILL --}}
                        <div
                            class="flex items-center gap-3 bg-gray-50 px-4 py-1.5 rounded-full border border-gray-100 shadow-inner">
                            <svg class="w-3.5 h-3.5 text-orange-500" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="text-[12px] font-black text-gray-600 uppercase tracking-widest">
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
                                <div class="h-16 w-2 bg-orange-600 rounded-full shadow-[0_0_20px_rgba(37,99,235,0.4)]">
                                </div>
                                <div>
                                    <div class="flex items-center gap-3 mb-1">
                                        <h1 class="text-5xl font-black text-gray-900 tracking-tight leading-none">
                                            Pengelolaan
                                            Absensi<span class="text-orange-600">.</span>
                                        </h1>
                                    </div>

                                    <div class="flex items-center gap-3 mt-4">
                                        <div
                                            class="px-3 py-1 bg-gray-900 text-white text-[12px] font-black uppercase tracking-widest rounded-lg shadow-sm">
                                            {{ $unit->namaMitra->nama_mitra ?? 'Mitra Perusahaan' }}
                                        </div>
                                        <div
                                            class="px-3 py-1 bg-orange-50 text-orange-700 text-[12px] font-black uppercase tracking-widest rounded-lg border border-orange-100 italic">
                                            Sistem {{ $unit->sistem_pengajian == 1 ? 'Harian' : 'Borongan' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <p class="text-base text-gray-500 flex items-center gap-2 ml-7">
                                Unit Kerja:
                                <span
                                    class="font-bold text-gray-800 italic underline decoration-orange-200 underline-offset-8 decoration-2">
                                    {{ $unit->nama_unit }}
                                </span>
                            </p>
                        </div>

                        {{-- Right Side: Grid Stats Cards --}}
                        <div class="grid grid-cols-2 gap-3">
                            {{-- Total Card --}}
                            <div
                                class="bg-gray-50/50 border border-gray-100 rounded-3xl p-5 min-w-[140px] hover:bg-white hover:shadow-xl hover:shadow-orange-900/5 transition-all duration-300 group">
                                <p class="text-[12px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total</p>
                                <div class="flex items-end gap-1">
                                    <span
                                        class="text-3xl font-black text-gray-900 leading-none">{{ $pkwtPekerja->total() }}</span>
                                    <span class="text-[12px] font-bold text-gray-400 mb-1">Pekerja</span>
                                </div>
                            </div>

                            {{-- Active Card --}}
                            <div
                                class="bg-gray-50/50 border border-gray-100 rounded-3xl p-5 min-w-[140px] hover:bg-white hover:shadow-xl hover:shadow-emerald-900/5 transition-all duration-300 group">
                                <p
                                    class="text-[12px] font-bold text-gray-400 uppercase tracking-widest mb-1 text-emerald-500">
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
                            <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 group-focus-within:text-orange-500 transition"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" x-model.debounce.500ms="searchQuery"
                                placeholder="Cari nama atau NIK pekerja..."
                                class="w-full pl-12 pr-4 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-orange-100 focus:bg-white transition text-sm">
                        </div>

                        <div class="relative">
                            <button @click="showFilterDropdown = !showFilterDropdown"
                                class="flex items-center gap-2 px-5 py-3 bg-gray-50 rounded-2xl text-sm font-bold text-gray-600 hover:bg-gray-100 transition">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                </svg>
                                Filter
                                <span x-show="filterStatus" class="w-2 h-2 bg-orange-500 rounded-full"></span>
                                <span x-show="filterVerifikasi" class="w-2 h-2 bg-orange-500 rounded-full"></span>
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
                                    <div x-data="{
                                        open: false,
                                        list: [{ val: '', label: 'Semua Status' },
                                            { val: '1', label: 'Hadir' },
                                            { val: '2', label: 'Cuti' }
                                        ]
                                    }" class="relative">
                                        <label
                                            class="block text-[12px] uppercase tracking-wider font-bold text-gray-400 mb-1.5">Status
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
                                                            'bg-orange-50 text-orange-700 font-semibold' :
                                                            'text-gray-700 hover:bg-gray-50'">
                                                        <svg x-show="filterStatus == item.val"
                                                            class="w-4 h-4 text-orange-600" fill="none"
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
                                            class="block text-[12px] uppercase tracking-wider font-bold text-gray-400 mb-1.5">Status
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
                                                            'bg-orange-50 text-orange-700 font-semibold' :
                                                            'text-gray-700 hover:bg-gray-50'">
                                                        <svg x-show="filterVerifikasi == item.val"
                                                            class="w-4 h-4 text-orange-600" fill="none"
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
                                    <p class="text-[12px] text-gray-400">Filter akan diterapkan otomatis</p>
                                </div>
                            </div>

                            {{-- Floating Action Bar --}}
                            <div x-show="selectedItems.length > 0"
                                x-transition:enter="transition ease-out duration-300"
                                x-transition:enter-start="opacity-0 translate-y-10"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-10"
                                class="fixed bottom-8 left-1/2 -translate-x-1/2 z-[60] w-[95%] max-w-4xl" x-cloak>

                                <div class="bg-white/70 backdrop-blur-xl border border-white/80 shadow-[0_20px_50px_rgba(0,0,0,0.1)] rounded-[2rem] px-6 py-4 flex items-center justify-between">

                                    <!-- Left Side: Selection Count & Reset -->
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <div class="bg-orange-600 text-white text-[11px] font-black h-8 w-8 rounded-xl shadow-lg shadow-orange-200 flex items-center justify-center">
                                                <span x-text="selectedItems.length"></span>
                                            </div>
                                            <div class="absolute -top-1 -right-1 flex h-3 w-3">
                                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-black text-slate-800 leading-none">Pekerja Dipilih</span>
                                            <button type="button" @click="selectedItems = []" class="text-[10px] font-bold text-slate-400 hover:text-rose-500 uppercase tracking-widest mt-1 transition-colors text-left outline-none">
                                                Batalkan Semua
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Right Side: Semantic Actions Group -->
                                    <div class="flex items-center gap-2">

                                        <!-- 1. Absen (Primary Orange) -->
                                        <button @click="showAbsenModal = true"
                                            class="group flex items-center gap-2 px-4 py-2.5 bg-orange-50 hover:bg-orange-600 border border-orange-100 text-orange-700 hover:text-white rounded-2xl text-[11px] font-black uppercase tracking-wider transition-all active:scale-95 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Absen
                                        </button>

                                        <!-- 2. Tunjangan (Emerald) -->
                                        <button class="group flex items-center gap-2 px-4 py-2.5 bg-emerald-50 hover:bg-emerald-600 border border-emerald-100 text-emerald-700 hover:text-white rounded-2xl text-[11px] font-black uppercase tracking-wider transition-all active:scale-95 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Tunjangan
                                        </button>

                                        <!-- 3. Potongan (Rose) -->
                                        <button class="group flex items-center gap-2 px-4 py-2.5 bg-rose-50 hover:bg-rose-600 border border-rose-100 text-rose-700 hover:text-white rounded-2xl text-[11px] font-black uppercase tracking-wider transition-all active:scale-95 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 12H6" />
                                            </svg>
                                            Potongan
                                        </button>

                                        <div class="h-8 w-px bg-slate-200 mx-2"></div>

                                        <!-- 4. Status Absen (Indigo) -->
                                        <button @click="initStatusModal()"
                                            class="group flex items-center gap-2 px-4 py-2.5 bg-indigo-50 hover:bg-indigo-600 border border-indigo-100 text-indigo-700 hover:text-white rounded-2xl text-[11px] font-black uppercase tracking-wider transition-all active:scale-95 shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                            </svg>
                                            Status
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- MODAL: INPUT HASIL PRODUKSI (STEPPER) -->
                            <div x-show="showAbsenModal"
                                class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6" x-cloak>
                                <div x-show="showAbsenModal" x-transition.opacity @click="showAbsenModal = false"
                                    class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm"></div>

                                <form
                                    action="{{ route('absensi.borongan.bulk.update', ['id_unit' => $unit->id, 'date' => $date]) }}"
                                    method="POST" enctype="multipart/form-data" x-ref="absenForm" x-data="absenFormHandler()"
                                    class="relative bg-white rounded-[2.5rem] shadow-[0_20px_60px_rgba(0,0,0,0.1)] w-full max-w-6xl overflow-hidden flex flex-col max-h-[90vh] border border-gray-100">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="date" value="{{ $date }}">

                                    {{-- 1. CONTEXT HEADER (SOFT & PROFESSIONAL) --}}
                                    <div
                                        class="px-10 py-5 bg-slate-50/80 border-b border-gray-100 flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            {{-- Soft Accent Icon --}}
                                            <div
                                                class="w-10 h-10 bg-white rounded-xl shadow-sm border border-gray-100 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <h1 class="text-base font-black text-slate-800 tracking-tight">Presensi
                                                        Borongan</h1>
                                                    <span
                                                        class="px-2 py-0.5 bg-orange-100 text-orange-700 text-[12px] font-black uppercase tracking-widest rounded-md">Sistem
                                                        Aktif
                                                    </span>
                                                </div>
                                                <p
                                                    class="text-[12px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">
                                                    Input Data Produksi Harian</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-4">
                                            {{-- Unit Info Pill --}}
                                            <div
                                                class="flex items-center gap-3 px-4 py-2 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                                <div class="text-right">
                                                    <p
                                                        class="text-[12px] font-black text-slate-400 uppercase tracking-widest leading-none">
                                                        Unit Kerja</p>
                                                    <p class="text-xs font-bold text-slate-700 mt-1">
                                                        {{ $unit->nama_unit }}</p>
                                                </div>
                                            </div>
                                            {{-- Date Info Pill --}}
                                            <div
                                                class="flex items-center gap-3 px-4 py-2 bg-white rounded-2xl border border-gray-100 shadow-sm">
                                                <div class="text-right">
                                                    <p
                                                        class="text-[12px] font-black text-slate-400 uppercase tracking-widest leading-none">
                                                        Periode</p>
                                                    <p class="text-xs font-bold text-slate-700 mt-1">
                                                        {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 2. STEPPER NAVIGATION (MINIMALIST) --}}
                                    <div class="px-10 py-6 bg-white border-b border-gray-100">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-6">
                                                {{-- Avatar with soft glow --}}
                                                <div class="relative">
                                                    <div
                                                        class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 text-lg font-black transition-all">
                                                        <span x-text="workerMap[currentWorkerId]?.initials"></span>
                                                    </div>
                                                    <div
                                                        class="absolute -bottom-1 -right-1 w-6 h-6 bg-white border border-gray-100 rounded-full flex items-center justify-center shadow-sm">
                                                        <div class="w-3 h-3 bg-emerald-500 rounded-full"></div>
                                                    </div>
                                                </div>

                                                <div>
                                                    <div class="flex items-center gap-3">
                                                        <h3 class="text-xl font-black text-slate-800 tracking-tight"
                                                            x-text="workerMap[currentWorkerId]?.nama"></h3>
                                                        <span class="text-xs font-mono text-slate-400"
                                                            x-text="workerMap[currentWorkerId]?.nik"></span>
                                                    </div>
                                                    <div class="flex items-center gap-3 mt-1.5">
                                                        <div
                                                            class="flex items-center gap-2 px-2.5 py-1 bg-slate-100 rounded-lg">
                                                            <span
                                                                class="text-[12px] font-black text-slate-500 uppercase tracking-widest">Antrean</span>
                                                            <span class="text-[12px] font-black text-orange-600"
                                                                x-text="(currentIndex + 1) + ' / ' + selectedItems.length"></span>
                                                        </div>
                                                        <div class="h-1 w-32 bg-slate-100 rounded-full overflow-hidden">
                                                            <div class="h-full bg-orange-400 transition-all duration-500"
                                                                :style="'width: ' + (((currentIndex + 1) / selectedItems
                                                                    .length) * 100) + '%'">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Clean Navigation Actions --}}
                                            <div class="flex items-center gap-2">
                                                <button type="button" @click="currentIndex > 0 ? currentIndex-- : ''"
                                                    :disabled="currentIndex === 0"
                                                    class="p-3 rounded-xl border border-gray-100 text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-all disabled:opacity-20">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M15 19l-7-7 7-7" />
                                                    </svg>
                                                </button>

                                                <button type="button"
                                                    @click="currentIndex < selectedItems.length - 1 ? currentIndex++ : ''"
                                                    :disabled="currentIndex === selectedItems.length - 1"
                                                    class="p-3 rounded-xl border border-gray-100 text-slate-400 hover:text-slate-600 hover:bg-slate-50 transition-all disabled:opacity-20">
                                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2.5" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </button>

                                                <div class="w-px h-6 bg-gray-100 mx-2"></div>

                                                <button @click="showAbsenModal = false" type="button"
                                                    class="p-3 text-slate-300 hover:text-red-500 transition-colors">
                                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- 2. CONTENT: MULTIPLE ITEMS PER WORKER --}}
                                    <div class="flex-1 overflow-y-auto custom-scrollbar bg-gray-50/30 p-10">
                                        <div class="max-w-5xl mx-auto space-y-6">

                                            {{-- Loop through workers, but only show current index --}}
                                            <template x-for="(workerId, wIdx) in selectedItems" :key="workerId">
                                                <div x-show="currentIndex === wIdx" class="space-y-4">

                                                    {{-- Add Item Button --}}
                                                    <div class="flex justify-between items-center mb-4">
                                                        <h4
                                                            class="text-xs font-black text-gray-400 uppercase tracking-widest">
                                                            Daftar Hasil Borongan</h4>
                                                        <button type="button" @click="addBoronganRow(workerId)"
                                                            :disabled="rowItems[workerId]?.length >= {{ count($barangs) }}"
                                                            :class="rowItems[workerId]?.length >= {{ count($barangs) }} ?
                                                                'opacity-50 cursor-not-allowed bg-gray-100 text-gray-400' :
                                                                'bg-orange-50 text-orange-600 hover:bg-orange-600 hover:text-white'"
                                                            class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2.5" d="M12 4v16m8-8H4" />
                                                            </svg>
                                                            <span
                                                                x-text="rowItems[workerId]?.length >= {{ count($barangs) }} ? 'Batas Barang Tercapai' : 'Tambah Baris Barang'"></span>
                                                        </button>
                                                    </div>

                                                    {{-- The Rows --}}
                                                    <template
                                                        x-for="(row, rIdx) in (rowItems && rowItems[workerId] ? rowItems[workerId] : [])"
                                                        :key="rIdx">
                                                        <div
                                                            class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm space-y-6 relative group">
                                                            <button type="button"
                                                                @click="rowItems[workerId].splice(rIdx, 1)"
                                                                class="absolute top-4 right-4 p-2 text-gray-300 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-all">
                                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                                    stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>

                                                            <div class="grid grid-cols-12 gap-6">
                                                                {{-- Col 1: Item & Quantities --}}
                                                                <div class="col-span-12 lg:col-span-7 space-y-4">
                                                                    <div class="flex flex-col gap-1.5"
                                                                        x-data="{
                                                                            open: false,
                                                                            search: '',
                                                                            options: {{ json_encode($barangs) }}
                                                                        }">
                                                                        <label
                                                                            class="text-[12px] font-black text-slate-400 uppercase tracking-widest ml-1">Nama
                                                                            Barang</label>

                                                                        <div class="relative">
                                                                            {{-- Hidden Input for Form Submission --}}
                                                                            <input type="hidden"
                                                                                :name="'data[' + workerId + '][' + rIdx +
                                                                                    '][id_barang]'"
                                                                                x-model="row.id_barang">

                                                                            {{-- Dropdown Trigger --}}
                                                                            <button type="button" @click="open = !open"
                                                                                class="w-full flex items-center justify-between bg-slate-50 border border-slate-100 rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-white hover:border-orange-200 transition-all outline-none shadow-sm"
                                                                                :class="open ?
                                                                                    'bg-white border-orange-400 ring-4 ring-orange-50' :
                                                                                    ''">

                                                                                {{-- THE FIX: Direct lookup in the trigger label --}}
                                                                                <span
                                                                                    :class="row.id_barang ? 'text-slate-700' :
                                                                                        'text-slate-400'"
                                                                                    x-text="options.find(b => b.id == row.id_barang)?.nama_item || '-- Pilih Barang --'">
                                                                                </span>

                                                                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-300"
                                                                                    :class="open ?
                                                                                        'rotate-180 text-orange-500' :
                                                                                        ''"
                                                                                    fill="none" viewBox="0 0 24 24"
                                                                                    stroke="currentColor">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round"
                                                                                        stroke-width="2.5"
                                                                                        d="M19 9l-7 7-7-7" />
                                                                                </svg>
                                                                            </button>

                                                                            {{-- Dropdown List --}}
                                                                            <div x-show="open"
                                                                                x-transition:enter="transition ease-out duration-200"
                                                                                x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                                                                                x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                                                                x-transition:leave="transition ease-in duration-100"
                                                                                @click.outside="open = false"
                                                                                class="absolute z-[120] w-full mt-2 bg-white border border-slate-100 rounded-2xl shadow-2xl overflow-hidden shadow-slate-200/50"
                                                                                x-cloak>

                                                                                {{-- Search Bar --}}
                                                                                <div
                                                                                    class="p-2 border-b border-slate-50 bg-slate-50/50">
                                                                                    <input type="text" x-model="search"
                                                                                        placeholder="Cari barang..."
                                                                                        class="w-full bg-white border-none rounded-lg px-3 py-2 text-xs font-medium focus:ring-1 focus:ring-orange-200 outline-none">
                                                                                </div>

                                                                                {{-- Cari bagian Dropdown List di dalam Modal --}}
                                                                                <ul
                                                                                    class="max-h-48 overflow-y-auto custom-scrollbar">
                                                                                    <template
                                                                                        x-for="item in options.filter(b =>
                                                                                            b.nama_item.toLowerCase().includes(search.toLowerCase()) &&
                                                                                            !rowItems[workerId]?.some(r => r.id_barang == b.id)
                                                                                        )"
                                                                                        :key="item.id">
                                                                                        <li>
                                                                                            <button type="button"
                                                                                                @click="
                                                                                                    row.id_barang = item.id;
                                                                                                    row.bayaranPerusahaan = item.harga_unit; {{-- Direct mapping --}}
                                                                                                    row.bayaranItem = item.harga_pekerja;   {{-- Direct mapping --}}
                                                                                                    row.max_rej_subkon = item.max_rej_subkon; {{-- Direct mapping --}}

                                                                                                    row.FD = 0;
                                                                                                    row.act_rej = 0;
                                                                                                    row.good_mc = 0;
                                                                                                    row.rej_mc_dibebankan = 0;
                                                                                                    row.totalQTY = 0;

                                                                                                    open = false;
                                                                                                    search = ''

                                                                                                    updatePrices(workerId, rIdx);
                                                                                                "
                                                                                                class="w-full text-left px-4 py-3 text-xs font-semibold transition-colors flex items-center justify-between group"
                                                                                                :class="row.id_barang == item
                                                                                                    .id ?
                                                                                                    'bg-orange-50 text-orange-700' :
                                                                                                    'text-slate-600 hover:bg-slate-50 hover:text-orange-600'">

                                                                                                <span
                                                                                                    x-text="item.nama_item"></span>

                                                                                                <svg x-show="row.id_barang == item.id"
                                                                                                    class="w-4 h-4 text-orange-500"
                                                                                                    fill="none"
                                                                                                    viewBox="0 0 24 24"
                                                                                                    stroke="currentColor">
                                                                                                    <path
                                                                                                        stroke-linecap="round"
                                                                                                        stroke-linejoin="round"
                                                                                                        stroke-width="3"
                                                                                                        d="M5 13l4 4L19 7" />
                                                                                                </svg>
                                                                                            </button>
                                                                                        </li>
                                                                                    </template>

                                                                                    {{-- Tampilkan pesan jika semua barang sudah dipilih atau tidak ditemukan --}}
                                                                                    <div x-show="options.filter(b => b.nama_item.toLowerCase().includes(search.toLowerCase()) && !rowItems[workerId].some(r => r.id_barang == b.id && r !== row)).length === 0"
                                                                                        class="px-4 py-8 text-center">
                                                                                        <p
                                                                                            class="text-[12px] font-bold text-slate-300 uppercase tracking-widest">
                                                                                            Tidak ada barang tersedia
                                                                                        </p>
                                                                                    </div>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Grid Input: 4 Kolom untuk field produksi --}}
                                                                    <div class="space-y-4">
                                                                        <!-- Baris Pertama: 3 Kolom -->
                                                                        <div class="grid grid-cols-3 gap-3">
                                                                            <!-- FD -->
                                                                            <div class="flex flex-col gap-1.5">
                                                                                <label class="text-[12px] font-black text-slate-400 uppercase tracking-widest">FD</label>
                                                                                <input type="number" min="0"
                                                                                    :name="'data[' + workerId + '][' + rIdx + '][FD]'"
                                                                                    :disabled="!row.id_barang"
                                                                                    x-model.number="row.FD"
                                                                                    @input="updatePrices(workerId, rIdx)"
                                                                                    class="bg-slate-50 border-transparent rounded-xl px-3 py-2 text-xs font-bold focus:bg-white focus:ring-2 focus:ring-slate-200 outline-none transition-all">
                                                                            </div>

                                                                            <!-- Good MC -->
                                                                            <div class="flex flex-col gap-1.5">
                                                                                <label class="text-[12px] font-black text-emerald-500 uppercase tracking-widest">Good MC</label>
                                                                                <input type="number" min="0"
                                                                                    :name="'data[' + workerId + '][' + rIdx + '][good_mc]'"
                                                                                    :disabled="!row.id_barang"
                                                                                    x-model.number="row.good_mc"
                                                                                    @input="updatePrices(workerId, rIdx)"
                                                                                    class="bg-slate-50 border-transparent rounded-xl px-3 py-2 text-xs font-bold focus:bg-white focus:ring-2 focus:ring-emerald-200 outline-none transition-all">
                                                                            </div>


                                                                            <!-- Act/Rej -->
                                                                            <div class="flex flex-col gap-1.5">
                                                                                <label class="text-[12px] font-black text-slate-400 uppercase tracking-widest">Act/Rej</label>
                                                                                <input type="number" min="0"
                                                                                    :name="'data[' + workerId + '][' + rIdx + '][act_rej]'"
                                                                                    :disabled="!row.id_barang"
                                                                                    x-model.number="row.act_rej"
                                                                                    @input="updatePrices(workerId, rIdx)"
                                                                                    class="bg-slate-50 border-transparent rounded-xl px-3 py-2 text-xs font-bold focus:bg-white focus:ring-2 focus:ring-slate-200 outline-none transition-all">
                                                                            </div>
                                                                        </div>

                                                                        <!-- Baris Kedua: 2 Kolom (Dibuat Setara/Simetris) -->
                                                                        <div class="grid grid-cols-2 gap-3">
                                                                            <!-- Max Rej. Subkon -->
                                                                            <div class="flex flex-col gap-1.5">
                                                                                <label class="text-[12px] font-black text-gray-500 uppercase tracking-widest">
                                                                                    Max Rej. Subkon ( <span x-text="row.max_rej_subkon"></span>% )
                                                                                </label>

                                                                                <div class="relative flex items-center">
                                                                                    {{-- Input ini akan otomatis menampilkan hasil Math.round dari updatePrices --}}
                                                                                    <input
                                                                                        type="number"
                                                                                        @input="updatePrices(workerId, rIdx)"
                                                                                        readonly
                                                                                        :name="'data[' + workerId + '][' + rIdx + '][act_rej_max]'"
                                                                                        x-model.number="row.act_rej_max"
                                                                                        :value="row.act_rej_max"
                                                                                        class="w-full bg-slate-100 border-transparent rounded-xl px-3 py-2 text-xs font-black text-slate-700 outline-none transition-all"
                                                                                        placeholder="0"
                                                                                    >
                                                                                    <span class="absolute right-3 text-[10px] font-black text-slate-400 uppercase">Pcs</span>
                                                                                </div>
                                                                            </div>


                                                                            <!-- Rej. MC Dibebankan -->
                                                                            <div class="flex flex-col gap-1.5">
                                                                                <label class="text-[12px] font-black text-red-500 uppercase tracking-widest">
                                                                                    Rej. MC Dibebankan
                                                                                </label>
                                                                                <input
                                                                                    type="number"
                                                                                    @input="updatePrices(workerId, rIdx)"
                                                                                    readonly
                                                                                    {{-- Pastikan name sesuai dengan field di database, misal: rej_mc_dibebankan --}}
                                                                                    :name="'data[' + workerId + '][' + rIdx + '][rej_mc_dibebankan]'"
                                                                                    {{-- Hubungkan ke variabel hasil kalkulasi --}}
                                                                                    x-model.number="row.rej_mc_dibebankan"
                                                                                    class="bg-red-50 border-transparent rounded-xl px-3 py-2 text-xs font-black text-red-700 outline-none transition-all"
                                                                                    placeholder="0"
                                                                                >

                                                                                {{-- Info tambahan untuk user --}}
                                                                                <span class="text-[10px] text-slate-400 mt-1 italic" x-show="row.rej_mc_dibebankan > 0">
                                                                                    *Melebihi batas toleransi <span x-text="row.act_rej_max"></span> Pcs
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    {{-- 2. TOTAL QUANTITY DISPLAY (Matches the UI Vibe) --}}
                                                                    <div class="mt-4 p-4 rounded-2xl border-2 border-dashed border-slate-100 bg-slate-50/30">
                                                                        <div class="grid grid-cols-2 gap-4 divide-x divide-slate-200">

                                                                            {{-- Sisi Kiri: Total Produksi --}}
                                                                            <div class="flex items-center justify-between pr-4">
                                                                                <div class="flex items-center gap-3">
                                                                                    <div class="p-2 bg-orange-100 rounded-lg">
                                                                                        <svg class="w-4 h-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                                                        </svg>
                                                                                    </div>
                                                                                    <div>
                                                                                        <p class="text-[10px] font-black text-orange-400 uppercase tracking-widest leading-none">Total Produksi</p>
                                                                                        <p class="text-[11px] font-bold text-slate-400 mt-1">QTY Akumulasi</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex items-baseline gap-1">
                                                                                    <span class="text-2xl font-black text-slate-700 tracking-tighter"
                                                                                        x-text="(parseInt(row.FD) || 0) + (parseInt(row.act_rej) || 0) + (parseInt(row.good_mc) || 0)">
                                                                                    </span>
                                                                                    <span class="text-[10px] font-black text-slate-400 uppercase">Pcs</span>
                                                                                </div>
                                                                            </div>

                                                                            {{-- Sisi Kanan: Total yang Dibayar --}}
                                                                            <div class="flex items-center justify-between pl-4">
                                                                                <div class="flex items-center gap-3">
                                                                                    <div class="p-2 bg-emerald-100 rounded-lg">
                                                                                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                        </svg>
                                                                                    </div>
                                                                                    <div>
                                                                                        <p class="text-[10px] font-black text-emerald-500 uppercase tracking-widest leading-none">Yang Dibayar</p>
                                                                                        <p class="text-[11px] font-bold text-slate-400 mt-1">Barang Disetujui</p>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="flex items-baseline gap-1">
                                                                                    <span class="text-2xl font-black text-emerald-600 tracking-tighter"
                                                                                        x-text="(row.good_mc + row.FD - row.rej_mc_dibebankan)  || 0">
                                                                                    </span>
                                                                                    <span class="text-[10px] font-black text-emerald-500 uppercase">Pcs</span>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        {{-- Hidden inputs for form submission --}}
                                                                        <input type="hidden" :name="'data[' + workerId + '][' + rIdx + '][totalQTY]'"
                                                                            :value="(parseInt(row.FD) || 0) + (parseInt(row.act_rej) || 0) + (parseInt(row.good_mc) || 0)">

                                                                        <input type="hidden" :name="'data[' + workerId + '][' + rIdx + '][totalBayar]'"
                                                                            :value="((parseInt(row.good_mc) || 0) + (parseInt(row.FD) || 0) - (parseInt(row.rej_mc_dibebankan) || 0))">
                                                                    </div>



                                                                    {{-- Baris Bawah: 3 Kolom (Reject MC & Hasil Akhir) --}}
                                                                    <div class="grid grid-cols-2 gap-3 mt-4">
                                                                        {{-- FIELD: TAGIHAN PERUSAHAAN --}}
                                                                        <div class="flex flex-col gap-1.5">
                                                                            <label
                                                                                class="text-[11px] font-black text-slate-400 uppercase tracking-widest ml-1">Tagihan
                                                                                Perusahaan</label>
                                                                            <div class="relative flex items-center">
                                                                                <span
                                                                                    class="absolute left-3 text-[10px] font-black text-slate-400">Rp.</span>

                                                                                {{-- Display Only Input (With Dots) --}}
                                                                                <input type="text" readonly
                                                                                    :value="formatRibuan(row.bayaranPerusahaan)"
                                                                                    class="w-full bg-slate-100 border-transparent rounded-xl pl-10 pr-3 py-2 text-xs font-black text-slate-700 outline-none">

                                                                                {{-- Actual Data Input (Sent to Database) --}}
                                                                                <input type="hidden"
                                                                                    :name="'data[' + workerId + '][' + rIdx +
                                                                                        '][bayaranPerusahaan]'"
                                                                                    x-model="row.bayaranPerusahaan">
                                                                            </div>
                                                                        </div>

                                                                        {{-- FIELD: BAYARAN PEKERJA --}}
                                                                        <div class="flex flex-col gap-1.5">
                                                                            <label
                                                                                class="text-[11px] font-black text-orange-600 uppercase tracking-widest ml-1">Bayaran
                                                                                Pekerja</label>
                                                                            <div class="relative flex items-center">
                                                                                <span
                                                                                    class="absolute left-3 text-[10px] font-black text-orange-300">Rp.</span>

                                                                                {{-- Display Only Input (With Dots) --}}
                                                                                <input type="text" readonly
                                                                                    :value="formatRibuan(row.bayaranItem)"
                                                                                    class="w-full bg-orange-50 border border-orange-100 rounded-xl pl-10 pr-3 py-2 text-xs font-black text-orange-700 outline-none">

                                                                                {{-- Actual Data Input (Sent to Database) --}}
                                                                                <input type="hidden"
                                                                                    :name="'data[' + workerId + '][' + rIdx +
                                                                                        '][bayaranItem]'"
                                                                                    x-model="row.bayaranItem">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                {{-- Col 2: Image & Note --}}
                                                                <div class="col-span-12 lg:col-span-5 space-y-4">
                                                                    {{-- Bukti Surat Jalan (File Name Approach) --}}
                                                                    <div class="flex flex-col gap-1.5">
                                                                        <label
                                                                            class="text-[12px] font-black text-slate-400 uppercase tracking-widest ml-1">Bukti
                                                                            Surat Jalan</label>

                                                                        <div class="relative group/file">
                                                                            {{-- Hidden Actual Input --}}
                                                                            <input type="file"
                                                                                :name="'data[' + workerId + '][' + rIdx +
                                                                                    '][buktiSuratJalan]'"
                                                                                @change="const file = $event.target.files[0]; if(file) { row.fileName = file.name; }"
                                                                                class="absolute inset-0 w-full h-full opacity-0 z-10 cursor-pointer">

                                                                            {{-- Visual UI --}}
                                                                            <div class="flex items-center gap-3 px-4 py-3 bg-slate-50 border border-slate-100 rounded-xl transition-all group-hover/file:bg-white group-hover/file:border-orange-200 shadow-sm"
                                                                                :class="row.fileName ?
                                                                                    'border-orange-100 bg-orange-50/30' :
                                                                                    ''">

                                                                                {{-- Icon --}}
                                                                                <div class="flex-shrink-0">
                                                                                    <template x-if="!row.fileName">
                                                                                        <svg class="w-5 h-5 text-slate-400"
                                                                                            fill="none"
                                                                                            viewBox="0 0 24 24"
                                                                                            stroke="currentColor">
                                                                                            <path stroke-linecap="round"
                                                                                                stroke-linejoin="round"
                                                                                                stroke-width="2"
                                                                                                d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                                                        </svg>
                                                                                    </template>
                                                                                    <template x-if="row.fileName">
                                                                                        <svg class="w-5 h-5 text-orange-500"
                                                                                            fill="none"
                                                                                            viewBox="0 0 24 24"
                                                                                            stroke="currentColor">
                                                                                            <path stroke-linecap="round"
                                                                                                stroke-linejoin="round"
                                                                                                stroke-width="2"
                                                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                                                        </svg>
                                                                                    </template>
                                                                                </div>

                                                                                {{-- Text Content --}}
                                                                                <div class="min-w-0 flex-1">
                                                                                    <p class="text-xs font-semibold truncate"
                                                                                        :class="row.fileName ?
                                                                                            'text-slate-700' :
                                                                                            'text-slate-400'"
                                                                                        x-text="row.fileName || 'Pilih atau drop file surat jalan...'">
                                                                                    </p>
                                                                                </div>

                                                                                {{-- Badge for "Selected" --}}
                                                                                <template x-if="row.fileName">
                                                                                    <span
                                                                                        class="text-[12px] font-black text-orange-600 bg-white px-2 py-1 rounded-md border border-orange-100 shadow-sm uppercase tracking-tighter">Terpilih</span>
                                                                                </template>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    {{-- Catatan (Consistent with vibe) --}}
                                                                    <div class="flex flex-col gap-1.5">
                                                                        <label
                                                                            class="text-[12px] font-black text-slate-400 uppercase tracking-widest ml-1">Catatan</label>
                                                                        <textarea :name="'data[' + workerId + '][' + rIdx + '][catatan]'" rows="1" x-model="row.catatan"
                                                                            placeholder="Tambahkan keterangan tambahan..."
                                                                            class="w-full bg-slate-50 border border-slate-100 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-700 focus:bg-white focus:ring-2 focus:ring-orange-100 focus:border-orange-400 outline-none transition-all resize-none shadow-sm placeholder:text-slate-300"></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>

                                                    {{-- Empty State for Rows --}}
                                                    <div x-show="!rowItems[workerId] || rowItems[workerId].length === 0"
                                                        class="py-20 border-2 border-dashed border-gray-100 rounded-[2.5rem] flex flex-col items-center justify-center text-gray-300">
                                                        <svg class="w-12 h-12 mb-2" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="1.5"
                                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                                        </svg>
                                                        <p class="text-sm font-bold">Belum ada item ditambahkan</p>
                                                        <button type="button" @click="addBoronganRow(workerId)"
                                                            class="mt-4 text-xs font-black text-orange-600 uppercase tracking-widest hover:underline">Tambah
                                                            Sekarang</button>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- 3. FOOTER: ACTIONS --}}
                                    <div
                                        class="sticky bottom-0 px-10 py-6 bg-white border-t border-gray-100 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <template x-for="(dot, dIdx) in selectedItems.length" :key="dIdx">
                                                <div class="h-1.5 rounded-full transition-all duration-300"
                                                    :class="currentIndex === dIdx ? 'w-8 bg-orange-600' : 'w-1.5 bg-gray-200'">
                                                </div>
                                            </template>
                                        </div>
                                        <div class="flex items-center gap-4">
                                            <button type="button" @click="showAbsenModal = false"
                                                class="px-6 py-3 text-xs font-black text-gray-400 uppercase tracking-widest hover:text-gray-600 transition">Batal</button>
                                            <button type="button" @click="confirmSubmit()"
                                                class="px-10 py-4 bg-orange-600 text-white text-xs font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-orange-700 shadow-xl shadow-orange-100 transition-all active:scale-95">
                                                Simpan Semua Data
                                            </button>
                                        </div>
                                    </div>
                                </form>
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
                                                        class="px-3 py-1 bg-orange-600 text-white text-[12px] font-black uppercase tracking-widest rounded-lg">Keterangan
                                                        Khusus</span>
                                                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">Status &
                                                        Alasan<span class="text-orange-600">.</span></h3>
                                                </div>
                                                <p class="text-sm text-gray-500 font-medium">Memperbarui alasan
                                                    ketidakhadiran untuk <span x-text="selectedItems.length"
                                                        class="text-orange-600 font-black"></span> pekerja.</p>
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
                                        action="{{ route('absensi.borongan.bulk.update', ['id_unit' => $unit->id, 'date' => $date]) }}"
                                        method="POST"
                                        x-ref="absenForm"
                                        x-data="absenFormHandler()" class="flex-1 overflow-y-auto custom-scrollbar bg-white p-10 pt-6">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="date" value="{{ $date }}">

                                        <table class="w-full border-separate border-spacing-y-4">
                                            <thead>
                                                <tr
                                                    class="text-[12px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                                    <th class="text-left pb-2 pl-6">Informasi Pekerja</th>
                                                    <th class="text-left pb-2 w-64">Tipe Absensi</th>
                                                    {{-- <th class="text-left pb-2 pr-6">Catatan / Keterangan</th> --}}
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <template x-for="id in selectedItems" :key="id">
                                                    <tr class="group">
                                                        {{-- Side Label: Pekerja --}}
                                                        <td
                                                            class="py-4 pl-6 bg-gray-50/50 rounded-l-2xl border-y border-l border-gray-100">
                                                            {{-- Left: Identity Section --}}
                                                            <div
                                                                class="flex items-center gap-4 min-w-0 flex-1 lg:flex-none lg:w-64">
                                                                {{-- Avatar Circle (Keep as is) --}}
                                                                <div
                                                                    class="flex-shrink-0 w-12 h-12 rounded-2xl bg-white shadow-sm border border-gray-100 flex items-center justify-center text-orange-600 text-xs font-black group-hover:bg-orange-600 group-hover:text-white transition-all duration-500">
                                                                    <span x-text="workerMap[id]?.initials"></span>
                                                                </div>

                                                                {{-- Name & NIK Container --}}
                                                                <div class="min-w-0"> {{-- This allows the children to truncate --}}
                                                                    <p class="text-sm font-black text-gray-900 leading-tight truncate"
                                                                        x-text="workerMap[id]?.nama"
                                                                        :title="workerMap[id]?.nama">
                                                                        {{-- Added :title so full name shows on hover --}}
                                                                    </p>
                                                                    <p class="text-[12px] font-bold text-gray-400 mt-0.5 tracking-widest truncate"
                                                                        x-text="workerMap[id]?.nik">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </td>

                                                        {{-- Alpine Dropdown (Balanced Height) --}}
                                                        <td
                                                            class="py-4 pl-0 pr-10 bg-gray-50/50 border-y border-gray-100 ">
                                                            <div x-data="{
                                                                open: false,
                                                                list: [
                                                                    { val: '2', label: 'Cuti' }
                                                                ]
                                                            }" class="relative">

                                                                <input type="hidden" :name="'data[' + id + '][status]'"
                                                                    x-model="rowStatus[id]">

                                                                <div @click="open = !open" @click.outside="open = false"
                                                                    class="flex items-center justify-between px-4 py-3 bg-white border border-gray-200 rounded-xl cursor-pointer hover:border-orange-400 transition-all shadow-sm">

                                                                    <span class="text-xs font-black"
                                                                        :class="rowStatus[id] == 1 ? 'text-blue-600' : 'text-gray-700'"
                                                                        x-text="rowStatus[id] == 1 ? 'Hadir (Ubah ke...)' : (list.find(x => x.val == rowStatus[id])?.label || 'Pilih Status')">
                                                                    </span>

                                                                    <svg class="w-4 h-4 text-orange-500 transition-transform duration-300"
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
                                                                                'bg-orange-50 text-orange-600' :
                                                                                'text-gray-600 hover:bg-gray-50'">
                                                                            <span x-text="item.label"></span>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                                <div x-show="rowStatus[id] == 1" class="mt-1">
                                                                    <span class="text-[9px] font-bold text-amber-600 uppercase tracking-tighter">
                                                                        ⚠️ Saat ini berstatus Hadir
                                                                    </span>
                                                                </div>
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
                                                class="px-10 py-4 bg-orange-600 text-white text-xs font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-orange-700 shadow-2xl shadow-orange-200 transition-all active:scale-95">
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
                <div class="overflow-x-auto max-h-[600px] custom-scrollbar">
                    <table class="w-full text-left border-separate border-spacing-0">
                        <thead class="sticky top-0 z-30 bg-gray-50 border-b border-gray-100 shadow-sm">
                            <tr>
                                <th class="pl-8 py-4 w-10"><input type="checkbox" @click="toggleAll()"
                                        :checked="selectedItems.length === allIds.length && allIds.length > 0"
                                        class="rounded border-gray-300 text-orange-600 focus:ring-orange-100"></th>
                                <th class="px-4 py-4 text-[12px] font-black text-gray-400 uppercase tracking-widest">
                                    Pekerja
                                </th>
                                <th class="px-4 py-4 text-[12px] font-black text-gray-400 uppercase tracking-widest">
                                    Rincian Item & Produksi
                                </th>
                                <th
                                    class="px-4 py-4 text-[12px] font-black text-gray-400 uppercase tracking-widest text-center">
                                    Status Absen</th>
                                <th
                                    class="px-4 py-4 text-[12px] font-black text-center text-gray-400 uppercase tracking-widest">
                                    Status Verifikasi</th>
                                <th
                                    class="px-4 py-4 text-[12px] font-black text-center text-gray-400 uppercase tracking-widest">
                                    Catatan</th>
                            </tr>
                        </thead>
                        <tbody id="main-table-body" class="divide-y divide-gray-50">
                            @include('Absensi.partials.main-borongan-table')
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

                const hasStatusHadir = this.selectedItems.some(id => this.rowStatus[id] == 1);

                    if (hasStatusHadir) {
                        Swal.fire({
                            title: 'Gagal!',
                            text: 'Terdapat pekerja dengan status "Hadir". Silakan pilih tipe absensi terlebih dahulu.',
                            icon: 'error',
                            confirmButtonColor: '#EF4444',
                        });
                        return; // Berhenti di sini, jangan submit
                    }

                Swal.fire({
                    title: 'Simpan Data Absensi?',
                    text: 'Pastikan semua data sudah benar sebelum disimpan.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#EA580C',
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
