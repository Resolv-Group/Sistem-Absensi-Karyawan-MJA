@forelse ($staff as $s)
    <tr class="hover:bg-gray-50 transition-colors duration-150 group">

        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 align-top">
            {{ ($staff->currentPage() - 1) * $staff->perPage() + $loop->iteration }}.
        </td>

        <td class="px-6 py-4 whitespace-nowrap max-w-[450px]">
            <div class="flex items-center">
                <div class="flex-shrink-0 h-10 w-10">
                    @if ($s->image_base64)
                        <img class="h-10 w-10 rounded-full object-cover border border-gray-200"
                            src="{{ $s->image_base64 }}" alt="{{ $s->nama }}">
                    @else
                        <img class="h-10 w-10 rounded-full bg-gray-200"
                            src="https://ui-avatars.com/api/?name={{ urlencode($s->nama) }}&background=random&color=fff&size=128"
                            alt="">
                    @endif
                </div>
                <div class="ml-4 min-w-0">
                    <div class="text-sm font-bold text-gray-900 truncate max-w-xs" title="{{ $s->nama }}">{{ $s->nama }}</div>
                    <div class="text-xs text-gray-500 font-mono mt-0.5">
                        Nik: {{ $s->nik }}
                    </div>
                    <div class="text-xs text-gray-500 font-mono mt-0.5">
                        Telp: {{ $s->telp }}
                    </div>
                </div>
            </div>
        </td>

        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex flex-col">
                <div class="flex items-center text-sm text-gray-700">
                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 21h18M5 21V9a2 2 0 012-2h4v14M13 21V5l6 4v12M9 12h2M9 16h2">
                        </path>
                    </svg>
                    {{ $s->perusahaan }}
                </div>
            </div>
        </td>

        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex flex-col">
                <div class="flex items-center text-xs text-gray-700">
                <svg class="w-4 h-4 text-blue-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h10M4 18h6" />
                </svg>


                <span class="font-medium text-sm">
                    {{ $s->kpj ?? '-' }}
                </span>
            </div>
            </div>
        </td>

        <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex flex-col gap-1">

                {{-- Tanggal Bergabung (Icon Kalender) --}}
                <div class="flex items-center text-sm text-gray-900 font-medium">
                    <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    {{-- Ganti dengan variabel dinamis jika perlu: --}}
                    {{ \Carbon\Carbon::parse($s->tgl_bergabung)->translatedFormat('d F Y') }}
                </div>

                {{-- Durasi Kerja (Icon Jam) --}}
                <div class="flex items-center text-xs text-gray-500">
                    <svg class="w-4 h-4 text-gray-400 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{-- Ganti dengan variabel dinamis jika perlu: --}}
                    {{ \Carbon\Carbon::parse($s->tgl_bergabung)->diffForHumans(null, true) }} bekerja

                </div>

            </div>
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-center">
            @if ($s->status_aktif == 1)
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

        <td class="px-6 py-4 whitespace-nowrap text-center">
            @if ($s->status_aktif == 1)
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
                <a href="{{ route('view.ubah.staff', $s->id) }}"
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
                <a href="{{ route('view.detail.staff', $s->id) }}"
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
            <div class="flex flex-col items-center justify-center">
                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                    </path>
                </svg>
                <p class="font-medium">Belum ada data staff.</p>
                <p class="text-sm mt-1">Silakan tambah staff baru.</p>
            </div>
        </td>
    </tr>
@endforelse
