@if ($paginator->hasPages())
    <div class="px-6 py-4  flex flex-col md:flex-row items-center justify-between gap-4">

        {{-- Left Side: Text Info (Indonesian) --}}
        <span class="text-xs text-gray-500">
            Menampilkan
            <span class="font-medium">{{ $paginator->firstItem() }}</span>
            sampai
            <span class="font-medium">{{ $paginator->lastItem() }}</span>
            dari
            <span class="font-medium">{{ $paginator->total() }}</span>
            hasil
        </span>

        {{-- Right Side: Pagination Links --}}
        <div class="flex items-center gap-1">

            {{-- Previous Button --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1 border border-gray-300 bg-gray-100 rounded-md text-xs font-medium text-gray-400 cursor-not-allowed">
                    Sebelumnya
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="px-3 py-1 border border-gray-300 bg-white rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                    Sebelumnya
                </a>
            @endif

            {{-- Pagination Elements (The Numbers) --}}
            @php
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();
                $start = max(2, $current - 1);
                $end = min($last - 1, $current + 1);
            @endphp

            {{-- Page 1 --}}
            @if ($current == 1)
                <span class="px-3 py-1 border border-blue-600 bg-blue-50 text-blue-600 rounded-md text-xs font-medium">
                    1
                </span>
            @else
                <a href="{{ $paginator->url(1) }}"
                class="px-3 py-1 border border-gray-300 bg-white rounded-md text-xs font-medium hover:bg-gray-50">
                    1
                </a>
            @endif

            {{-- Left Dots --}}
            @if ($start > 2)
                <span class="px-3 py-1 text-gray-400 text-xs">…</span>
            @endif

            {{-- Middle Pages --}}
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $current)
                    <span class="px-3 py-1 border border-blue-600 bg-blue-50 text-blue-600 rounded-md text-xs font-medium">
                        {{ $page }}
                    </span>
                @else
                    <a href="{{ $paginator->url($page) }}"
                    class="px-3 py-1 border border-gray-300 bg-white rounded-md text-xs font-medium hover:bg-gray-50">
                        {{ $page }}
                    </a>
                @endif
            @endfor

            {{-- Right Dots --}}
            @if ($end < $last - 1)
                <span class="px-3 py-1 text-gray-400 text-xs">…</span>
            @endif

            {{-- Last Page --}}
            @if ($last > 1)
                @if ($current == $last)
                    <span class="px-3 py-1 border border-blue-600 bg-blue-50 text-blue-600 rounded-md text-xs font-medium">
                        {{ $last }}
                    </span>
                @else
                    <a href="{{ $paginator->url($last) }}"
                    class="px-3 py-1 border border-gray-300 bg-white rounded-md text-xs font-medium hover:bg-gray-50">
                        {{ $last }}
                    </a>
                @endif
            @endif


            {{-- Next Button --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="px-3 py-1 border border-gray-300 bg-white rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                    Selanjutnya
                </a>
            @else
                <span class="px-3 py-1 border border-gray-300 bg-gray-100 rounded-md text-xs font-medium text-gray-400 cursor-not-allowed">
                    Selanjutnya
                </span>
            @endif

        </div>
    </div>
@endif
