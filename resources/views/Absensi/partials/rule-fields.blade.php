<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    @include('Absensi.partials.searchable-dropdown', [
        'prefix' => $prefix.'-status', 'label' => 'Status kehadiran', 'model' => $model.'.status_kehadiran',
        'options' => "Object.entries(statuses).map(([value, label]) => ({value: Number(value), label}))",
        'placeholder' => 'Pilih status', 'searchPlaceholder' => 'Cari status...',
        'onSelect' => $prefix === 'default' ? 'changed()' : '',
    ])
    <div x-show="{{ $model }}.status_kehadiran == 1">
        @include('Absensi.partials.searchable-dropdown', [
            'prefix' => $prefix.'-hours-mode', 'label' => 'Jam kerja', 'model' => $model.'.hours_mode',
            'options' => "[{value: 'schedule', label: 'Sesuai jadwal masing-masing pekerja'}, {value: 'custom', label: 'Tentukan jumlah jam'}]",
            'placeholder' => 'Pilih jam kerja', 'searchPlaceholder' => 'Cari jam kerja...',
            'onSelect' => $prefix === 'default' ? 'changed()' : '',
        ])
    </div>
    <div x-show="{{ $model }}.status_kehadiran == 1 && {{ $model }}.hours_mode === 'custom'">
        <label for="{{ $prefix }}-hours" class="block text-xs font-semibold text-gray-600 mb-2">Jumlah jam per hari</label>
        <input id="{{ $prefix }}-hours" type="number" min="0" max="24" step="0.1" x-model="{{ $model }}.jam_aktual" placeholder="Contoh: 8"
            class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm">
    </div>
    <div class="flex flex-wrap items-center gap-4 text-sm" x-show="{{ $model }}.status_kehadiran == 1">
        <label class="flex items-center gap-2"><input type="checkbox" x-model="{{ $model }}.is_paid" class="accent-blue-600"> Dibayar</label>
        <label class="flex items-center gap-2"><input type="checkbox" x-model="{{ $model }}.is_hbn" class="accent-blue-600"> HBN</label>
    </div>
    <label class="flex items-center gap-2 text-sm" x-show="{{ $model }}.status_kehadiran != 1">
        <input type="checkbox" x-model="{{ $model }}.is_paid_leave" class="accent-blue-600"> Cuti / izin berbayar
    </label>
    <div class="sm:col-span-2">
        <label for="{{ $prefix }}-note" class="block text-xs font-semibold text-gray-600 mb-2">Catatan <span class="font-normal text-gray-400">(opsional)</span></label>
        <input id="{{ $prefix }}-note" type="text" maxlength="255" x-model="{{ $model }}.catatan" placeholder="Catatan absensi..." class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-sm">
    </div>
</div>
