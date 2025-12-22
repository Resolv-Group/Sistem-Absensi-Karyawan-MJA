@forelse ($unit as $u)
    <tr class="hover:bg-gray-50 transition-colors duration-150 group">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 align-top">
            {{ ($unit->currentPage() - 1) * $unit->perPage() + $loop->iteration }}.
        </td>

        <td class="px-6 py-4 whitespace-nowrap align-top max-w-[450px]">
            <div class="flex items-start">
                {{-- <div class="flex-shrink-0 h-10 w-10">
                    @if ($u->image_base64)
                        <img class="h-10 w-10 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600 font-bold"
                            src="{{ $u->image_base64 }}" alt="{{ $u->nama_unit }}">
                    @else
                        <img class="h-10 w-10 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600 font-bold"
                            src="https://ui-avatars.com/api/?name={{ urlencode($u->nama_unit) }}&background=random&color=fff&size=128"
                            alt="">
                    @endif
                </div> --}}
                <div class="ml-4 min-w-0">
                    <div class="text-sm font-bold text-gray-900 truncate max-w-xs" title="{{ $u->nama_unit }}">
                        {{ $u->namaMitra->nama_mitra }}</div>
                    {{-- <div class="text-xs text-gray-500 mt-0.5">Telp : {{ $mk->telp_perusahaan }}</div> --}}
                    {{-- <div class="text-xs text-gray-500">Sidoarjo, Jawa Timur</div> --}}
                </div>
            </div>
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-bold bg-gray-100 text-gray-800">
                {{ $u->id }}
            </span>
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                {{ optional(optional($u->picUnit)->staff)->nama ?? '-' }}
            </span>
        </td>

        @php
            $sistemPengajian = [
                1 => 'Harian',
                2 => 'Borongan',
            ];
        @endphp

        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600 align-top">
            {{ $sistemPengajian[$u->sistem_pengajian] ?? '-' }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600 align-top">
            100
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-center">
            @if ($u->status_aktif == 1)
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
                <a href="{{ route('view.ubah.unit', $u->id) }}"
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
                <a href="{{ route('view.detail.unit', $u->id) }}"
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
