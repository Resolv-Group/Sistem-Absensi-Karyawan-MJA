@forelse ($mitraKerja as $mk)
    <tr class="hover:bg-gray-50 transition-colors duration-150 group">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 align-top">
            {{ ($mitraKerja->currentPage() - 1) * $mitraKerja->perPage() + $loop->iteration }}.
        </td>

        <td class="px-6 py-4 whitespace-nowrap align-top max-w-[450px]">
            <div class="flex items-start">
                <div class="flex-shrink-0 h-10 w-10">
                    @if ($mk->image_base64)
                        <img class="h-10 w-10 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600 font-bold"
                            src="{{ $mk->image_base64 }}" alt="{{ $mk->nama_mitra }}">
                    @else
                        <img class="h-10 w-10 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600 font-bold"
                            src="https://ui-avatars.com/api/?name={{ urlencode($mk->nama_mitra) }}&background=random&color=fff&size=128"
                            alt="">
                    @endif
                </div>
                <div class="ml-4 min-w-0">
                    <div class="text-sm font-bold text-gray-900 truncate max-w-xs" title="{{ $mk->nama_mitra }}">
                        {{ $mk->nama_mitra }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Telp : {{ $mk->telp_perusahaan }}</div>
                    {{-- <div class="text-xs text-gray-500">Sidoarjo, Jawa Timur</div> --}}
                </div>
            </div>
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-bold bg-gray-100 text-gray-800">
                {{ $mk->units_count}}
            </span>
            <div class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider">Orang</div>
        </td>

        {{-- <td class="px-6 py-4 whitespace-nowrap align-top">
                    <div class="flex flex-col gap-4">
                        <!-- PIC 1 -->
                        <div class="flex gap-3">
                            <img class="h-8 w-8 rounded-full bg-gray-200" src="https://ui-avatars.com/api/?name=Siti+A&background=random&color=fff" alt="">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-gray-900">Siti Aminah</span>
                                <span class="text-xs text-gray-500 mb-0.5">Direktur Utama</span>
                                <div class="flex items-center gap-2 text-xs text-gray-500">
                                    <span class="flex items-center gap-1 hover:text-blue-600 cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        Email
                                    </span>
                                    <span class="text-gray-300">|</span>
                                    <span class="flex items-center gap-1 hover:text-blue-600 cursor-pointer">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        0813-9876...
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </td> --}}

        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                {{ $mk->bidangUsaha->nama }}
            </span>
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600 align-top">
            {{ \Carbon\Carbon::parse($mk->tgl_mulai_kerjasama)->translatedFormat('d F Y') }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-red-600  align-top">
            {{-- {{ \Carbon\Carbon::parse($mk->tgl_akhir_mou )->translatedFormat('d F Y') }} --}}
            <span
                class="
                    {{ $mk->tgl_mou_auto === 'Segera Habis' ? 'text-yellow-700 font-semibold' : '' }}
                    {{ $mk->tgl_mou_auto === 'Expired' ? 'text-red-700 font-semibold' : 'text-gray-600' }}
                ">
                {{ \Carbon\Carbon::parse($mk->tgl_akhir_mou)->format('d M Y') }}
            </span>
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
            @php
                $status = $mk->status_mou_auto;

                $statusClass = match ($status) {
                    'Aktif' => 'bg-green-100 text-green-800 border-green-200',
                    'Segera Habis' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                    'Kadaluarsa' => 'bg-red-100 text-red-800 border-red-200',
                    'Tanpa MoU' => 'bg-gray-100 text-gray-600 border-gray-200',
                    default => 'bg-gray-100 text-gray-800 border-gray-200',
                };
            @endphp

            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $statusClass }}">
                {{ $status }}
            </span>
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex justify-end gap-2">
                <!-- Edit -->
                <a href="{{ route('view.ubah.mitra-kerja', $mk->id) }}"
                    class="text-blue-600 hover:text-blue-900 border border-blue-200 hover:bg-blue-50
               rounded-lg p-2 transition"
                    title="Edit">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                    </svg>
                </a>

                <!-- Detail -->
                <a href="{{ route('view.detail.mitra-kerja', $mk->id) }}"
                    class="text-blue-600 hover:text-blue-900 border border-blue-200 hover:bg-blue-50
               rounded-lg p-2 transition"
                    title="Detail">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                </a>
            </div>

        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
            <div class="flex flex-col items-center justify-center">
                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                <p class="font-medium">Belum ada data mitra kerja.</p>
                <p class="text-sm mt-1">Silakan tambah mitra kerja baru.</p>
            </div>
        </td>
    </tr>
@endforelse

