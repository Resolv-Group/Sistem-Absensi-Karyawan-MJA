<div x-data="{
        open: false,
        search: '',
        get items() { return {{ $options }}; },
        get filtered() {
            const keyword = this.search.trim().toLocaleLowerCase('id-ID');
            return keyword ? this.items.filter(item => item.label.toLocaleLowerCase('id-ID').includes(keyword)) : this.items;
        },
        get selectedLabel() { return this.items.find(item => String(item.value) === String({{ $model }}))?.label || '{{ $placeholder }}'; }
    }" @click.outside="open = false" @keydown.escape.stop="open = false"
    :class="open ? 'z-[110]' : 'z-0'" class="relative min-w-0">
    <label id="{{ $prefix }}-label" class="block text-[10px] uppercase tracking-wider font-bold text-gray-400 mb-1.5">{{ $label }}</label>
    <button type="button" @click="open = !open; search = ''; if (open) $nextTick(() => $refs.search.focus())"
        :aria-expanded="open" aria-haspopup="listbox" aria-labelledby="{{ $prefix }}-label"
        class="w-full min-h-11 px-3.5 py-2.5 flex items-center justify-between gap-2 rounded-xl bg-gray-50/80 border border-transparent text-sm text-gray-700 hover:bg-white hover:border-blue-100 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-100 transition">
        <span class="flex items-center gap-2 min-w-0">
            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8"><path d="M5 7h14M5 12h14M5 17h14"/></svg>
            <span class="truncate text-left font-medium" x-text="selectedLabel"></span>
        </span>
        <svg class="w-4 h-4 text-gray-400 shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
    </button>
    <div x-show="open" x-cloak x-transition.origin.top @keydown.enter.prevent="if (filtered.length === 1) { {{ $model }} = filtered[0].value; open = false; {{ $onSelect ?? '' }} }"
        class="absolute left-0 right-0 top-full mt-1 z-[120] overflow-hidden rounded-xl border border-gray-100 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.18)]">
        <div class="p-2 border-b border-gray-100">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2"><path d="m21 21-4.35-4.35m1.35-5.65a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z"/></svg>
                <input type="search" x-ref="search" x-model="search" @click.stop placeholder="{{ $searchPlaceholder }}"
                    aria-label="Cari {{ strtolower($label) }}" class="w-full pl-9 pr-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700 placeholder:text-gray-400 focus:outline-none focus:border-blue-300 focus:ring-2 focus:ring-blue-100">
            </div>
        </div>
        <ul role="listbox" aria-labelledby="{{ $prefix }}-label" class="max-h-56 overflow-y-auto py-1">
            <template x-for="item in filtered" :key="item.value">
                <li role="option" :aria-selected="String(item.value) === String({{ $model }})">
                    <button type="button" @click="{{ $model }} = item.value; open = false; search = ''; {{ $onSelect ?? '' }}"
                        class="w-full px-4 py-2.5 text-sm text-left flex items-center gap-2 transition"
                        :class="String(item.value) === String({{ $model }}) ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'">
                        <svg x-show="String(item.value) === String({{ $model }})" class="w-4 h-4 text-blue-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2"><path d="m5 12 4 4L19 6"/></svg>
                        <span x-show="String(item.value) !== String({{ $model }})" class="w-4 h-4 shrink-0"></span>
                        <span x-text="item.label" class="truncate"></span>
                    </button>
                </li>
            </template>
            <li x-show="filtered.length === 0" class="px-4 py-3 text-sm text-center text-gray-400 italic">Tidak ditemukan</li>
        </ul>
    </div>
</div>
