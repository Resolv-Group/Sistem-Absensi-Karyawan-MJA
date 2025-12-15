@forelse ($pekerja as $p)
    <tr class="hover:bg-gray-50 transition-colors duration-150 group">
        <td class="px-6 py-4 whitespace-nowrap">
            {{ ($pekerja->currentPage() - 1) * $pekerja->perPage() + $loop->iteration }}.
        </td>

        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                    @if ($p->image_base64)
                        <img class="h-10 w-10 rounded-full object-cover border border-gray-200"
                            src="{{ $p->image_base64 }}" alt="{{ $p->nama }}">
                    @else
                        <img class="h-10 w-10 rounded-full bg-gray-200"
                            src="https://ui-avatars.com/api/?name={{ urlencode($p->nama) }}&background=random&color=fff&size=128"
                            alt="">
                    @endif
                </div>
                <div class="ml-4">
                    <div class="text-sm font-bold text-gray-900">{{ $p->nama }}</div>
                    <div class="text-xs text-gray-500 font-mono mt-0.5 tracking-wide">
                        NIK: {{ $p->nik }}
                    </div>
                </div>
            </div>
        </td>

        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex flex-col gap-1">
                <div class="flex items-center text-sm text-gray-700">
                    <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                    <span class="truncate max-w-[150px]">{{ $p->email ?? '-' }}</span>
                </div>

                <div class="flex items-center text-xs text-gray-500">
                    <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                        </path>
                    </svg>
                    {{ $p->telp ?? '-' }}
                </div>
            </div>
        </td>

        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex flex-col gap-1">
                <div class="flex items-center text-sm text-gray-900 font-medium">
                    <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    {{ ucwords(strtolower($p->kota)) }}
                </div>

                <div class="flex items-center text-sm text-gray-900">
                    <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    {{-- Ganti dengan variabel dinamis jika perlu: --}}
                    {{ \Carbon\Carbon::parse($p->tgl_bergabung)->translatedFormat('d F Y') }}
                </div>
            </div>
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-center">
            @if ($p->status_aktif)
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                    Aktif
                </span>
            @else
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                    Non-Aktif
                </span>
            @endif
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex justify-end gap-2">
                <!-- Edit -->
                <a href="{{ route('view.ubah.pekerja', $p->id) }}"
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
                <a href="{{ route('view.detail.pekerja', $p->id) }}"
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
        <td colspan="6" class="px-6 py-10 text-center text-gray-500">
            Tidak ditemukan.
        </td>
    </tr>
@endforelse
