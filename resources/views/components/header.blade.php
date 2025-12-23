
@props([
    'title',
    'subtitle' => null,
    'breadcrumbs' => null,
])

<div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 mt-6">

    {{-- Left Side: Identity --}}
    <div>
        {{-- 1. Breadcrumb (Modern Pill Style) --}}
        <nav class="flex items-center text-xs font-medium text-gray-500 mb-3 space-x-2">
            <a href="#" class="hover:text-blue-600 transition-colors flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>
            <svg class="w-3 h-3 text-gray-300 mx-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
            <span class="text-gray-900 font-semibold">{{ $breadcrumbs }}</span>
        </nav>

        {{-- 2. Title & Subtitle --}}
        <div class="relative">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight leading-none">
                {{ $title }}
            </h1>
            @if ($subtitle)
                <p class="mt-2 text-sm text-gray-500">
                    {{ $subtitle }}
                </p>
            @endif
        </div>
    </div>

    {{-- Right Side: Meta / Actions --}}
    <div class="flex items-center gap-3">

        {{-- Last Update Badge (Replaces the floating text) --}}
        <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 rounded-lg shadow-sm">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
            </span>
            <span class="text-xs font-medium text-gray-600">
                Update: {{ now()->format('d M H:i') }}
            </span>
        </div>

        {{-- Optional: Export Button (Commented out but styled ready for use) --}}
        {{--
        <button class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 bg-white text-gray-500 hover:text-blue-600 hover:border-blue-200 transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
            </svg>
        </button>
        --}}
    </div>
</div>
