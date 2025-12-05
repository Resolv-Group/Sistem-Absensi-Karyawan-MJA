@props([
    'title',
    'subtitle' => null,
])

<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 py-8">

    {{-- Left Side: Breadcrumb + Title --}}
    <div>
        {{-- 1. Breadcrumb (Adds the "App" feel) --}}
        <nav class="flex items-center text-xs text-gray-500 mb-2 space-x-2">
            <a href="#" class="hover:text-blue-600 transition-colors">Dashboard</a>
            <span class="text-gray-300">/</span>
            <span class="font-medium text-gray-700">HR Management</span>
        </nav>

        {{-- 2. Main Title --}}
        <h1 class="text-3xl font-bold text-gray-900 tracking-tight">
            {{ $title }}
        </h1>

        {{-- 3. Subtitle --}}
        @if ($subtitle)
            <p class="mt-1.5 text-sm text-gray-500">
                {{ $subtitle }}
            </p>
        @endif
    </div>

    {{-- Right Side: Secondary Actions (The "Surprise") --}}
    {{-- This fills the empty space on the right and aligns with the professional vibe --}}
    <div class="flex items-center gap-3">
        <span class="text-xs text-gray-400 hidden md:inline-block">Last sync: {{ now()->format('H:i') }}</span>

        <button class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300 transition shadow-sm">
            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
            </svg>
            Export Report
        </button>
    </div>
</div>
