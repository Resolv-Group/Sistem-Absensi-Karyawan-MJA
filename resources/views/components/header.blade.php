<div class="w-full bg-white shadow-sm rounded-lg mt-4 px-8 py-6">

    {{-- Title + filter --}}
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-semibold text-gray-800">Daftar Pekerja</h1>

        <div class="flex items-center gap-3">
            <div class="relative">
                <input type="text" placeholder="Filter"
                    class="border rounded-lg px-4 py-2 text-sm focus:ring focus:ring-gray-200" />
                <div class="absolute inset-y-0 right-3 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 14.414V20l-4 2v-7.586L3.293 6.707A1 1 0 013 6V4z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-4 gap-6 text-sm mb-4">

        <div>
            <p class="text-gray-500">Periode</p>
            <p class="text-lg font-semibold text-gray-800">November 2025</p>
        </div>

        <div>
            <p class="text-gray-500">Total Karyawan</p>
            <p class="text-lg font-semibold text-gray-800">100</p>
        </div>

        <div>
            <p class="text-gray-500">Karyawan Baru</p>
            <p class="text-lg font-semibold text-gray-800">20</p>
        </div>

        <div>
            <p class="text-gray-500">Non-aktif</p>
            <p class="text-lg font-semibold text-gray-800">10</p>
        </div>
    </div>
</div>
