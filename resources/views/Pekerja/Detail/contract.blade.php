<div>
    <div class="flex items-center gap-4 mb-6">
        <h3 class="text-lg font-bold text-gray-900 whitespace-nowrap">Daftar Tenaga Kerja</h3>
        <div class="h-px bg-gray-200 w-full"></div>
    </div>

    <div
        class="bg-blue-50 rounded-xl p-6 border border-blue-100 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">

        {{-- LEFT SIDE --}}
        <div class="flex items-center gap-4">
            <div class="p-3 bg-white rounded-lg text-blue-600 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                    </path>
                </svg>
            </div>
            <div>
                <p class="text-sm font-medium text-blue-800">Status PKWT Saat Ini</p>
                <p class="text-lg font-bold text-blue-900">Aktif - Valid</p>
            </div>
        </div>

        {{-- RIGHT SIDE: BUTTONS --}}
        <div class="flex items-center gap-3">
            @if ($pekerja->dokumen)
                {{-- 1. STREAM / VIEW BUTTON (Opens in new tab) --}}
                <a href="{{ route('pekerja.dokumen.show', $pekerja->id) }}" target="_blank"
                    class="flex items-center gap-2 px-4 py-2 bg-white text-blue-600 text-sm font-semibold rounded-lg border border-blue-200 shadow-sm hover:bg-blue-600 hover:text-white hover:border-blue-600 transition group">

                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Lihat Dokumen
                </a>
            @else
                <span class="text-xs text-gray-400 italic">Tidak ada dokumen</span>
            @endif
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
