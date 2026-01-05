@forelse ($units as $un)
    <tr class="hover:bg-gray-50 transition-colors duration-150 group">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 align-top">
            {{ ($units->currentPage() - 1) * $units->perPage() + $loop->iteration }}.
        </td>

        <td class="px-6 py-4 whitespace-nowrap align-top max-w-[450px]">
            <div class="flex items-start">

                <div class="ml-4 min-w-0">
                    <div class="text-sm font-bold text-gray-900 truncate max-w-xs" title="{{ $un->nama_unit ?? '-' }}">
                        {{ $un->nama_unit ?? '-' }}
                    </div>
                    {{-- <div class="text-xs text-gray-500">Sidoarjo, Jawa Timur</div> --}}
                </div>
            </div>
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-bold bg-gray-100 text-gray-800">
                {{ $un->pkwt_pekerja_count ?? '-' }}
            </span>
            <div class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider">Orang</div>
        </td>

        @php
            $sistemPengajian = [1 => 'Harian', 2 => 'Borongan'];
            $colors = [
                1 => 'text-purple-600 bg-purple-50',
                2 => 'text-orange-600 bg-orange-50'
            ];

            $value = $un->sistem_pengajian;
        @endphp

        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
            <span
                class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-bold
                    {{ $colors[$value] ?? 'text-gray-600 bg-gray-100' }}">
                {{ $sistemPengajian[$value] ?? '-' }}
            </span>
        </td>


        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                {{ $un->namaMitra->nama_mitra ?? '-' }}
            </span>
            <div class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider">Orang</div>
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                {{ $un->namaMitra->nama_mitra ?? '-' }}
            </span>
            <div class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider">Orang</div>
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex justify-end gap-2">
                <!-- Detail -->
                <a href="{{ 
                        $un->sistem_pengajian == 1 
                            ? route('view.absensiharian', [$un->id, request('date') ?? now()->toDateString()])
                            : route('view.absensiborongan', [$un->id, request('date') ?? now()->toDateString()])
                    }}"
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
                <p class="font-medium">Belum ada absen.</p>
                <p class="text-sm mt-1">Silakan tambah mitra kerja baru.</p>
            </div>
        </td>
    </tr>
@endforelse

