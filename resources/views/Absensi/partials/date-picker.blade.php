<div x-data="attendanceDatePicker(@js($dates))" class="relative inline-block"
    @click.outside="open = false" @keydown.escape.stop.prevent="open = false; $refs.dateButton.focus()">
    <button type="button" x-ref="dateButton" @click="show()" :aria-expanded="open" aria-haspopup="dialog"
        class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg shadow-sm text-sm hover:border-blue-400 focus:ring-2 focus:ring-blue-200">
        <svg class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path d="M8 3v4m8-4v4M3 11h18M5 5h14a2 2 0 012 2v12a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/>
        </svg>
        <span x-text="label"></span>
        <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 20 20" stroke="currentColor"><path d="m5 7 5 5 5-5"/></svg>
    </button>
    <form x-show="open" x-cloak x-transition role="dialog" aria-label="Pilih tanggal absensi"
        method="GET" action="{{ route('view.absensi') }}"
        class="absolute left-0 top-full mt-2 z-[80] w-[296px] max-w-[calc(100vw-2rem)] rounded-2xl border border-gray-200 bg-white p-4 shadow-xl">
        <div class="text-center pb-4 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-900" aria-live="polite" x-text="draft.length + ' tanggal dipilih'"></p>
            <p class="text-xs text-gray-400 mt-1">Pilih hingga 7 tanggal</p>
        </div>
        <div class="flex items-center justify-between py-3">
            <span class="text-sm font-semibold text-gray-800" x-text="monthLabel"></span>
            <div class="flex gap-1">
                <button type="button" @click="moveMonth(-1)" aria-label="Bulan sebelumnya" class="w-8 h-8 rounded-full text-blue-600 hover:bg-blue-50 text-xl">&#8249;</button>
                <button type="button" @click="moveMonth(1)" aria-label="Bulan berikutnya" class="w-8 h-8 rounded-full text-blue-600 hover:bg-blue-50 text-xl">&#8250;</button>
            </div>
        </div>
        <div class="grid grid-cols-7 text-center text-[10px] uppercase text-gray-400 mb-2">
            @foreach (['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $weekday)<span>{{ $weekday }}</span>@endforeach
        </div>
        <div class="grid grid-cols-7 gap-y-1">
            <template x-for="day in days" :key="day.iso">
                <button type="button" @click="toggle(day.iso)" :aria-label="format(day.iso)" :aria-pressed="draft.includes(day.iso)"
                    :aria-current="day.today ? 'date' : null" class="mx-auto w-8 h-8 rounded-full text-xs transition focus:ring-2 focus:ring-blue-300 focus:outline-none"
                    :class="draft.includes(day.iso) ? 'bg-blue-600 text-white font-semibold' : (day.today ? 'text-blue-600 font-bold hover:bg-blue-50' : (day.current ? 'text-gray-800 hover:bg-gray-100' : 'text-gray-300 hover:bg-gray-50'))"
                    x-text="day.number"></button>
            </template>
        </div>
        <p x-show="message" x-text="message" role="alert" class="text-xs text-red-600 mt-3"></p>
        <template x-for="day in draft" :key="day"><input type="hidden" name="dates[]" :value="day"></template>
        <div class="flex items-center justify-between border-t border-gray-100 pt-3 mt-3">
            <button type="button" @click="draft = []; message = ''" class="text-xs text-gray-500 hover:text-gray-900">Hapus pilihan</button>
            <button type="submit" :disabled="!draft.length" class="rounded-lg bg-blue-600 text-white text-xs font-semibold px-4 py-2 disabled:opacity-40">Terapkan</button>
        </div>
    </form>
</div>
