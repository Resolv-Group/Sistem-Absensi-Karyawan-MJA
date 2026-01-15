<div
    x-data="{
        show: false,
        type: 'success',
        message: ''
    }"

    {{-- 1. Initialize from PHP Session (Page Reloads) --}}
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

    {{-- 2. Listen for JavaScript Events (AJAX) --}}
    @notify.window="
        show = true;
        type = $event.detail.type || 'success';
        message = $event.detail.message;
        setTimeout(() => show = false, 7000);
    "

    class="fixed top-5 right-5 z-50 flex flex-col gap-2"
    style="display: none;"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-x-8"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-8"
>
    <div class="flex items-center w-full max-w-xs p-4 text-gray-500 bg-white rounded-lg shadow-lg border-l-4"
         :class="type === 'success' ? 'border-green-500' : 'border-red-500'">

        {{-- Success Icon --}}
        <div x-show="type === 'success'" class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/></svg>
        </div>

        {{-- Error Icon --}}
        <div x-show="type === 'error'" class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/></svg>
        </div>

        <div class="ml-3 text-sm font-normal" x-text="message"></div>

        <button type="button" @click="show = false" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>
</div>
