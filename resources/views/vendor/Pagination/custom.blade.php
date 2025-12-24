@if ($paginator->hasPages())
    <div class="bg-gray-50 px-6 py-4 border-gray-200 flex flex-col md:flex-row items-center justify-between gap-4">

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
            @foreach ($elements as $element)

                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="px-3 py-1 border border-gray-300 bg-white rounded-md text-xs font-medium text-gray-500">
                        {{ $element }}
                    </span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            {{-- Active Page (Highlighted) --}}
                            <span class="px-3 py-1 border border-blue-600 bg-blue-50 text-blue-600 rounded-md text-xs font-medium">
                                {{ $page }}
                            </span>
                        @else
                            {{-- Normal Page --}}
                            <a href="{{ $url }}"
                               class="px-3 py-1 border border-gray-300 bg-white rounded-md text-xs font-medium text-gray-700 hover:bg-gray-50 transition">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

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
