<div>
    <div class="flex items-center gap-4 mb-6">
        <h3 class="text-lg font-bold text-gray-900 whitespace-nowrap">Daftar Tenaga Kerja</h3>
        <div class="h-px bg-gray-200 w-full"></div>
    </div>

    <div class="bg-blue-50 rounded-xl p-6 border border-blue-100 mb-6">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-white rounded-lg text-blue-600 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-blue-800">Status MoU Saat Ini</p>
                <p class="text-lg font-bold text-blue-900">Aktif - Valid</p>
            </div>
        </div>
    </div>

    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-6">
        <div>
            <dt class="text-sm font-medium text-gray-500">Tanggal Mulai Kerjasama</dt>
            <dd class="mt-1 text-sm text-gray-900 font-semibold">10 Januari 2023</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Tanggal Berakhir MoU</dt>
            <dd class="mt-1 text-sm text-gray-900 font-semibold">10 Januari 2026</dd>
        </div>
        <div>
            <dt class="text-sm font-medium text-gray-500">Sisa Masa Berlaku</dt>
            <dd class="mt-1 text-sm text-emerald-600 font-bold">1 Tahun 2 Bulan</dd>
        </div>
    </dl>

    {{-- Progress Bar for Contract --}}
    <div class="mt-8">
        <div class="flex justify-between text-xs font-medium text-gray-500 mb-1">
            <span>Mulai</span>
            <span>Berakhir</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2.5">
            <div class="bg-blue-600 h-2.5 rounded-full" style="width: 45%"></div>
        </div>
    </div>
</div>
