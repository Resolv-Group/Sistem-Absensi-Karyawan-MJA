@php
    // 1. Ambil PKWT yang status_aktif nya 1 untuk bagian atas
    $currentPkwt = $historiPkwt->where('status_aktif', 1)->first();
    
    $percentage = 0;
    $sisaWaktu = 'Tidak ada kontrak aktif';
    $statusColor = 'blue';

    if ($currentPkwt) {
        $start = \Carbon\Carbon::parse($currentPkwt->tgl_mulai_pkwt)->startOfDay();
        $end = \Carbon\Carbon::parse($currentPkwt->tgl_akhir_pkwt)->startOfDay();
        $now = \Carbon\Carbon::today();

        // Hitung Persentase
        $totalDurasi = $start->diffInDays($end);
        $berjalan = $start->diffInDays($now);

        if ($now->greaterThan($end)) {
            $percentage = 100;
            $statusColor = 'red';
            $sisaWaktu = 'Kontrak Berakhir';
        } elseif ($now->lessThan($start)) {
            $percentage = 0;
            $sisaWaktu = 'Belum Mulai';
        } else {
            $percentage = ($totalDurasi > 0) ? ($berjalan / $totalDurasi) * 100 : 0;
            $diff = $now->diff($end);
            $parts = [];
            if ($diff->y > 0) $parts[] = $diff->y . ' Tahun';
            if ($diff->m > 0) $parts[] = $diff->m . ' Bulan';
            if ($diff->d > 0) $parts[] = $diff->d . ' Hari';
            $sisaWaktu = empty($parts) && $now->equalTo($end) ? 'Hari Terakhir' : implode(' ', $parts);
        }
    }
@endphp

<div>
    <div class="flex items-center gap-4 mb-6">
        <h3 class="text-lg font-bold text-gray-900 whitespace-nowrap">Daftar Tenaga Kerja</h3>
        <div class="h-px bg-gray-200 w-full"></div>
    </div>

    @if($currentPkwt)
        {{-- TAMPILKAN BOX JIKA ADA PKWT AKTIF (status_aktif = 1) --}}
        <div class="bg-{{ $statusColor }}-50 rounded-xl p-6 border border-{{ $statusColor }}-100 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-white rounded-lg text-{{ $statusColor }}-600 shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-{{ $statusColor }}-800">Status PKWT Saat Ini</p>
                    <p class="text-lg font-bold text-{{ $statusColor }}-900">
                        {{ $statusColor == 'red' ? 'Kadaluarsa / Tidak Valid' : 'Aktif - Valid' }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                @if ($currentPkwt->dokumen_pkwt)
                    <a href="{{ route('pkwt.dokumen.show', $currentPkwt->id) }}" target="_blank"
                        class="flex items-center gap-2 px-4 py-2 bg-white text-{{ $statusColor }}-600 text-sm font-semibold rounded-lg border border-{{ $statusColor }}-200 shadow-sm hover:bg-{{ $statusColor }}-600 hover:text-white transition group">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Lihat Dokumen
                    </a>
                @endif
            </div>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-6">
            <div>
                <dt class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Tanggal Mulai PKWT</dt>
                <dd class="mt-1 text-sm text-gray-900 font-bold">{{ \Carbon\Carbon::parse($currentPkwt->tgl_mulai_pkwt)->translatedFormat('d F Y') }}</dd>
            </div>
            <div>
                <dt class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Tanggal Berakhir PKWT</dt>
                <dd class="mt-1 text-sm text-gray-900 font-bold">{{ \Carbon\Carbon::parse($currentPkwt->tgl_akhir_pkwt)->translatedFormat('d F Y') }}</dd>
            </div>
            <div>
                <dt class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Sisa Masa Berlaku</dt>
                <dd class="mt-1 text-sm {{ $statusColor == 'red' ? 'text-red-600' : 'text-emerald-600' }} font-black">{{ $sisaWaktu }}</dd>
            </div>
        </dl>

        <div class="mt-8">
            <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
                <span>Progress Kontrak</span>
                <span>{{ round($percentage) }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="bg-{{ $statusColor == 'red' ? 'red' : 'blue' }}-600 h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
            </div>
        </div>
    @else
        {{-- JIKA TIDAK ADA STATUS_AKTIF = 1 --}}
        <div class="bg-gray-50 rounded-2xl p-8 border-2 border-dashed border-gray-200 text-center">
            <p class="text-gray-500 font-bold italic">Tidak ada kontrak PKWT yang sedang aktif.</p>
        </div>
    @endif
</div>

{{-- SECTION: HISTORI PKWT --}}
<div class="mt-12" x-data="{ openModal: false, fileName: '' }">
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4 flex-1">
            <h3 class="text-lg font-bold text-gray-900 whitespace-nowrap">Histori PKWT</h3>
            <div class="h-px bg-gray-200 w-full"></div>
        </div>
        <button @click="openModal = true; fileName = ''" class="ml-4 flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah
        </button>
    </div>

    <div class="space-y-3">
        @php
            // 2. Filter hanya data yang status_aktif nya 0 untuk histori
            $inactiveHistory = $historiPkwt->where('status_aktif', 0);
        @endphp

        @forelse($inactiveHistory as $histori)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl hover:bg-gray-50 transition-colors shadow-sm gap-4 group">
                <div class="flex items-center gap-4">
                    <div class="p-2.5 bg-gray-50 text-gray-400 rounded-xl group-hover:bg-white group-hover:text-blue-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 leading-none mb-1.5">Masa Berlaku Kontrak (Arsip)</p>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-gray-700">{{ \Carbon\Carbon::parse($histori->tgl_mulai_pkwt)->translatedFormat('d M Y') }}</span>
                            <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                            <span class="text-sm font-bold text-gray-700">{{ \Carbon\Carbon::parse($histori->tgl_akhir_pkwt)->translatedFormat('d M Y') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between sm:justify-end gap-6">
                    <span class="px-3 py-1 bg-gray-50 text-gray-500 text-[9px] font-black uppercase tracking-widest rounded-lg border border-gray-100">Arsip</span>
                    @if($histori->dokumen_pkwt)
                        <a href="{{ route('pkwt.dokumen.show', $histori->id) }}" target="_blank" class="flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24 text-blue-500">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View File
                        </a>
                    @endif
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-12 border-2 border-dashed border-gray-100 rounded-[2rem]">
                <p class="text-sm font-bold text-gray-400 italic">Tidak ada histori kontrak non-aktif.</p>
            </div>
        @endforelse
    </div>
    
    {{-- MODAL TETAP SAMA SEPERTI SEBELUMNYA --}}
    {{-- ... (kode modal yang tadi) ... --}}
</div>