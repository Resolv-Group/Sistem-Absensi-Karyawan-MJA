@forelse ($units as $un)
    <tr class="hover:bg-gray-50 transition-colors duration-150 group">
        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 align-top">
            {{ ($units->currentPage() - 1) * $units->perPage() + $loop->iteration }}.
        </td>

        <td class="px-6 py-4 whitespace-nowrap align-top max-w-[450px]">
            <div class="flex items-start">
                <div class="min-w-0">
                    <div class="text-sm font-bold text-gray-900 truncate max-w-xs" title="{{ $un->nama_unit ?? '-' }}">
                        {{ $un->nama_unit ?? '-' }}
                    </div>
                    {{-- <div class="text-xs text-gray-500">Sidoarjo, Jawa Timur</div> --}}
                </div>
            </div>
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-sm font-bold bg-gray-100 text-gray-800">
                {{ $un->total_pekerja ?? '-' }}
            </span>
            <div class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider">Orang</div>
        </td>

        @php
            $sistemPengajian = [1 => 'Harian', 2 => 'Borongan'];
            $colors = [
                1 => 'text-blue-600 bg-blue-50',
                2 => 'text-orange-600 bg-orange-50',
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
                {{ $un->total_belum_absen ?? '-' }}
            </span>
            <div class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider">Orang</div>
        </td>

        <td class="px-6 py-4 whitespace-nowrap text-center align-top">
            <span
                class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-red-700 border border-blue-100">
                {{ $un->total_penilaian ?? '-' }}
            </span>
            <div class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider">Orang</div>
        </td>


        @if (Auth::user()->staff?->jabatan === 'PIC')
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <div class="flex justify-end">
                    @php
                        // Logika Penentuan Tema
                        $isHarian = $un->sistem_pengajian == 1;

                        $themeClasses = $isHarian
                            ? "bg-blue-50 text-blue-700 border-blue-200 hover:bg-blue-600 hover:text-white hover:border-blue-600 shadow-blue-100"
                            : "bg-orange-50 text-orange-700 border-orange-200 hover:bg-orange-600 hover:text-white hover:border-orange-600 shadow-orange-100";

                        $labelText = $isHarian ? "Kelola Absensi" : "Kelola Absensi";
                    @endphp

                    <a href="{{ $isHarian
                        ? route('view.absensi.harian', [$un->id, request('date') ?? now()->toDateString()])
                        : route('view.absensi.borongan', [$un->id, request('date') ?? now()->toDateString()]) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 border rounded-xl transition-all duration-200 group shadow-sm {{ $themeClasses }}">

                        {{-- Icon Dinamis (Opsional: Ikon Harian vs Borongan bisa beda sedikit) --}}
                        @if($isHarian)
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        @endif

                        <span class="font-black uppercase text-[10px] tracking-widest">
                            {{ $labelText }}
                        </span>
                    </a>
                </div>
            </td>
        @endif
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
            </div>
        </td>
    </tr>
@endforelse
