@php
    $percentage = 0;
    $sisaWaktu = 'Tidak ada kontrak aktif';
    $statusColor = 'blue';

    if ($currentPkwt) {
        // Gunakan startOfDay agar jam tidak mempengaruhi perhitungan hari
        $start = \Carbon\Carbon::parse($currentPkwt->tgl_mulai_pkwt)->startOfDay();
        $end = \Carbon\Carbon::parse($currentPkwt->tgl_akhir_pkwt)->startOfDay();
        $now = \Carbon\Carbon::today(); // Ini sama dengan Carbon::now()->startOfDay()

        // 1. Hitung Persentase Progress Bar
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
            // Gunakan max 1 agar tidak pembagian nol
            $percentage = ($totalDurasi > 0) ? ($berjalan / $totalDurasi) * 100 : 0;

            // 2. Hitung Sisa Waktu yang Lebih Akurat
            $diff = $now->diff($end);

            $parts = [];
            if ($diff->y > 0) $parts[] = $diff->y . ' Tahun';
            if ($diff->m > 0) $parts[] = $diff->m . ' Bulan';
            if ($diff->d > 0) $parts[] = $diff->d . ' Hari';

            // Jika sisa hari kurang dari 1 hari tapi belum expired (hari yang sama)
            if (empty($parts) && $now->equalTo($end)) {
                $sisaWaktu = 'Hari Terakhir (Hari Ini)';
            } else {
                $sisaWaktu = implode(' ', $parts);
            }
        }
    }
@endphp

<div>
    <div class="flex items-center gap-4 mb-6">
        <h3 class="text-lg font-bold text-gray-900 whitespace-nowrap">Daftar Tenaga Kerja</h3>
        <div class="h-px bg-gray-200 w-full"></div>
    </div>

    {{-- Highlight Box Status --}}
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
            {{-- Mengambil dokumen dari PKWT aktif terbaru --}}
            @if ($currentPkwt && $currentPkwt->dokumen_pkwt)
                <a href="{{ route('pkwt.dokumen.show', $currentPkwt->id) }}" target="_blank"
                    class="flex items-center gap-2 px-4 py-2 bg-white text-{{ $statusColor }}-600 text-sm font-semibold rounded-lg border border-{{ $statusColor }}-200 shadow-sm hover:bg-{{ $statusColor }}-600 hover:text-white transition group">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Lihat Dokumen
                </a>
            @else
                <span class="text-xs text-gray-400 italic font-medium">Tidak ada dokumen terlampir</span>
            @endif
        </div>
    </div>

    <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-6">
        <div>
            <dt class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Tanggal Mulai PKWT</dt>
            <dd class="mt-1 text-sm text-gray-900 font-bold">
                {{ $currentPkwt ? \Carbon\Carbon::parse($currentPkwt->tgl_mulai_pkwt)->translatedFormat('d F Y') : '-' }}
            </dd>
        </div>
        <div>
            <dt class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Tanggal Berakhir PKWT</dt>
            <dd class="mt-1 text-sm text-gray-900 font-bold">
                {{ $currentPkwt ? \Carbon\Carbon::parse($currentPkwt->tgl_akhir_pkwt)->translatedFormat('d F Y') : '-' }}
            </dd>
        </div>
        <div>
            <dt class="text-[11px] uppercase tracking-wider font-bold text-gray-400">Sisa Masa Berlaku</dt>
            <dd class="mt-1 text-sm {{ $statusColor == 'red' ? 'text-red-600' : 'text-emerald-600' }} font-black">
                {{ $sisaWaktu }}
            </dd>
        </div>
    </dl>

    {{-- Progress Bar --}}
    <div class="mt-8">
        <div class="flex justify-between text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2">
            <span>Progress Kontrak</span>
            <span>{{ round($percentage) }}%</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-2">
            <div class="bg-{{ $statusColor == 'red' ? 'red' : 'blue' }}-600 h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
        </div>
    </div>
</div>
{{-- SECTION: HISTORI PKWT --}}
<div class="mt-12">
    <div class="flex items-center gap-4 mb-6">
        <h3 class="text-lg font-bold text-gray-900 whitespace-nowrap">Histori PKWT</h3>
        <div class="h-px bg-gray-200 w-full"></div>
    </div>

    <div class="space-y-3">
        @php
            // Filter: Hanya tampilkan kontrak yang ID-nya tidak sama dengan kontrak aktif saat ini
            $filteredHistory = $historiPkwt->filter(function($item) use ($currentPkwt) {
                return $currentPkwt ? $item->id !== $currentPkwt->id : true;
            });
        @endphp

        @forelse($filteredHistory as $histori)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-white border border-gray-100 rounded-2xl hover:bg-gray-50 transition-colors shadow-sm gap-4 group">

                {{-- Info Kontrak --}}
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

                {{-- Action --}}
                <div class="flex items-center justify-between sm:justify-end gap-6">
                    {{-- Label Arsip --}}
                    <span class="px-3 py-1 bg-gray-50 text-gray-500 text-[9px] font-black uppercase tracking-widest rounded-lg border border-gray-100">
                        Arsip
                    </span>

                    @if($histori->dokumen_pkwt)
                        <a href="{{ route('pkwt.dokumen.show', $histori->id) }}" target="_blank"
                            class="flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-800 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View File
                        </a>
                    @else
                        <span class="text-[10px] text-gray-400 font-bold italic">No File</span>
                    @endif
                </div>
            </div>
        @empty
            {{-- Tampilan jika tidak ada kontrak lama (histori) --}}
            <div class="flex flex-col items-center justify-center py-12 border-2 border-dashed border-gray-100 rounded-[2rem]">
                <svg class="w-12 h-12 text-gray-100 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-bold text-gray-400 italic">Tidak ada histori kontrak sebelumnya.</p>
            </div>
        @endforelse
    </div>
</div>
