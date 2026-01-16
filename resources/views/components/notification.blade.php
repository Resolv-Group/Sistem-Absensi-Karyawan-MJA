<div
    x-data="{
        show: false,
        type: 'success',
        message: ''
    }"

    {{-- 1. Initialize from PHP Session --}}
    x-init="
        @if(session('success'))
            show = true;
            type = 'success';
            message = '{{ session('success') }}';
            setTimeout(() => show = false, 7000);
        @elseif(session('error'))
            show = true;
            type = 'error';
            message = '{{ session('error') }}';
            setTimeout(() => show = false, 7000);
        @endif
    "

    {{-- 2. Listen for JavaScript Events --}}
    @notify.window="
        show = true;
        type = $event.detail.type || 'success';
        message = $event.detail.message;
        setTimeout(() => show = false, 7000);
    "

    {{-- POSISI: Tengah Atas (left-1/2 -translate-x-1/2) --}}
    class="fixed top-6 left-1/2 -translate-x-1/2 z-[100] w-full max-w-xs sm:max-w-sm pointer-events-none"
    style="display: none;"
    x-show="show"

    {{-- ANIMASI: Dari Atas (-translate-y-8) --}}
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 -translate-y-12 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 -translate-y-12 scale-95"
>
    {{-- Glassmorphism & Shadow Style --}}
    <div class="pointer-events-auto flex items-center p-4 bg-white/90 backdrop-blur-xl border border-gray-100 rounded-[1.5rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] relative overflow-hidden group">

        {{-- Accent Line Top --}}
        <div class="absolute top-0 left-0 right-0 h-1"
             :class="type === 'success' ? 'bg-emerald-500' : 'bg-red-500'"></div>

        {{-- Success Icon --}}
        <div x-show="type === 'success'" class="inline-flex items-center justify-center flex-shrink-0 w-10 h-10 text-emerald-600 bg-emerald-50 rounded-xl shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        {{-- Error Icon --}}
        <div x-show="type === 'error'" class="inline-flex items-center justify-center flex-shrink-0 w-10 h-10 text-red-600 bg-red-50 rounded-xl shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </div>

        <div class="ml-4 flex-1">
            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-0.5"
               x-text="type === 'success' ? 'Berhasil' : 'Terjadi Kesalahan'"></p>
            <p class="text-xs font-bold text-gray-700 leading-tight" x-text="message"></p>
        </div>

        <button type="button" @click="show = false" class="ml-2 p-2 text-gray-300 hover:text-gray-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>
