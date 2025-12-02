@props([
    'title',
    'subtitle' => null,
])

<div class="py-6">

    {{-- TOP SECTION: Title + Subtitle --}}
    <div class="flex items-start justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">{{ $title }}</h1>

            @if ($subtitle)
                <p class="text-sm text-gray-500 mt-1">{{ $subtitle }}</p>
            @endif
        </div>

        {{-- Add Button --}}
        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow hover:bg-blue-700 flex items-center gap-2">
            <span>+ Tambah</span>
        </button>
    </div>

    {{-- SPACING --}}
    <div class="h-6"></div>

    {{-- FILTERS, SEARCH, PERIOD SECTION --}}
    <div class="flex flex-wrap items-center justify-between gap-4">

        {{-- LEFT: Dropdown Periode --}}
        <div class="flex items-center gap-2">
            <label class="text-gray-600 text-sm">Periode:</label>
            <select class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                <option>Januari 2025</option>
                <option>Februari 2025</option>
                <option>Maret 2025</option>
            </select>
        </div>

        {{-- RIGHT: Search + Filter --}}
        <div class="flex items-center gap-3">

            {{-- Search Bar --}}
            <div class="relative">
                <input
                    type="text"
                    placeholder="Cari..."
                    class="border rounded-lg pl-10 pr-3 py-2 text-sm w-64 focus:outline-none focus:ring-1 focus:ring-blue-500"
                >
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-400 absolute left-3 top-2.5"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="11" cy="11" r="7" stroke-width="2"/>
                    <path d="M20 20L17 17" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>

            {{-- Filter Button --}}
            <button class="border px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-100 transition flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L14 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 018 21v-7.586L3.293 6.707A1 1 0 013 6V4z"
                    />
                </svg>
                Filter
            </button>

        </div>
    </div>

</div>
