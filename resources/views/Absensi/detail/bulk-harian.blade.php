@extends('layout')

@section('content')
    <style>dialog::backdrop { background: rgb(15 23 42 / .42); backdrop-filter: blur(6px); } dialog[open] { display: block; }</style>
    <div class="max-w-7xl mx-auto py-8" x-data="bulkDailyAttendance(@js([
        'dates' => $dates, 'workerPage' => $workerPage,
        'listUrl' => route('view.absensi.harian', ['id_unit' => $unit->id, 'date' => $dates[0], 'dates' => $dates]),
        'previewUrl' => route('absensi.rules.preview', ['id_unit' => $unit->id]),
        'saveUrl' => route('absensi.rules.store', ['id_unit' => $unit->id]), 'csrf' => csrf_token(),
        'redirectUrl' => route('view.absensi'),
    ]))">
        <header class="relative isolate overflow-hidden rounded-[2.5rem] border border-white/80 bg-white/75 backdrop-blur-xl p-5 sm:p-8 md:p-10 shadow-[0_20px_50px_rgba(15,23,42,0.08)] mb-7">
            <div class="absolute -top-24 -right-16 w-72 h-72 rounded-full bg-blue-100/70 blur-3xl -z-10"></div>
            <div class="absolute -bottom-24 -left-16 w-72 h-72 rounded-full bg-emerald-100/50 blur-3xl -z-10"></div>
            <div class="relative z-10">
                <div class="flex flex-wrap items-start justify-between gap-4 mb-8">
                    <a href="{{ route('view.absensi', ['dates' => $dates]) }}"
                        class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 hover:text-blue-600 transition group">
                        <svg class="w-3.5 h-3.5 transform group-hover:-translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="m15 19-7-7 7-7"/></svg>
                        Kembali ke Unit
                    </a>
                    <div class="flex flex-wrap items-center gap-2 rounded-2xl border border-white/80 bg-white/65 backdrop-blur-lg px-3 py-2 shadow-inner max-w-full">
                        <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M8 3v4m8-4v4M3 11h18M5 5h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2z"/></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-500 mr-1">{{ count($dates) }} tanggal</span>
                        @foreach ($dates as $selectedDate)
                            <span class="rounded-full border border-blue-100 bg-blue-50/90 px-2.5 py-1 text-[10px] font-bold text-blue-700">{{ \Carbon\Carbon::parse($selectedDate)->translatedFormat('d M') }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-7">
                    <div class="space-y-4 min-w-0">
                        <div class="flex items-center gap-5">
                            <div class="h-14 w-2 rounded-full bg-blue-600 shadow-[0_0_20px_rgba(37,99,235,0.4)] shrink-0"></div>
                            <div class="min-w-0">
                                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black text-gray-900 tracking-tight leading-tight">Absensi <span class="text-blue-600">Massal.</span></h1>
                                <div class="flex flex-wrap items-center gap-2 mt-3">
                                    <span class="px-3 py-1 rounded-lg bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest shadow-sm">{{ $unit->namaMitra->nama_mitra ?? 'Mitra Perusahaan' }}</span>
                                    <span class="px-3 py-1 rounded-lg border border-blue-100 bg-blue-50/75 text-blue-700 text-[10px] font-black uppercase tracking-widest italic">Sistem Harian</span>
                                </div>
                            </div>
                        </div>
                        <p class="ml-7 text-sm sm:text-base text-gray-500">Unit Kerja: <span class="font-bold italic text-gray-800 underline decoration-blue-200 underline-offset-4">{{ $unit->nama_unit }}</span></p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <div class="min-w-[130px] rounded-3xl border border-white/80 bg-white/55 backdrop-blur-lg p-4 shadow-[0_10px_30px_rgba(37,99,235,0.06)]">
                            <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400">Tanggal</p>
                            <p class="text-3xl font-black text-blue-600 mt-1">{{ count($dates) }}</p>
                            <p class="text-[10px] text-gray-400">Terpilih</p>
                        </div>
                        <div class="min-w-[130px] rounded-3xl border border-white/80 bg-white/55 backdrop-blur-lg p-4 shadow-[0_10px_30px_rgba(37,99,235,0.06)]">
                            <p class="text-[10px] uppercase tracking-widest font-bold text-gray-400">Pekerja</p>
                            <p class="text-3xl font-black text-gray-900 mt-1" x-text="selected.length"></p>
                            <p class="text-[10px] text-gray-400">Dari maksimal 25</p>
                        </div>
                    </div>
                </div>
                <p class="ml-7 mt-5 text-sm text-gray-500">Tentukan aturan, pilih pekerja, lalu sesuaikan pengecualian jika diperlukan.</p>
            </div>
        </header>
        <div x-show="result" x-cloak role="status" class="mb-5 p-5 rounded-[1.5rem] border border-white/80 bg-emerald-50/75 backdrop-blur-xl shadow-[0_12px_32px_rgba(16,185,129,0.08)] text-emerald-800">
            <p class="font-semibold">Absensi selesai diproses</p>
            <p class="text-sm mt-1" x-text="result ? `${result.created} dibuat · ${result.updated} diperbarui · ${result.skipped} dilewati` : ''"></p>
        </div>
        <p x-show="error" x-cloak x-text="error" role="alert" class="mb-4 p-4 rounded-[1.5rem] border border-red-100 bg-red-50/80 backdrop-blur-xl text-red-700 text-sm whitespace-pre-line"></p>

        <fieldset :disabled="busy" class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
            <div class="lg:col-span-2 space-y-6">
                <section class="relative z-30 overflow-visible bg-white/75 backdrop-blur-xl border border-white/80 rounded-[2rem] p-5 sm:p-7 shadow-[0_18px_45px_rgba(15,23,42,0.07)]">
                    <div class="flex gap-3 items-center mb-5">
                        <span class="w-9 h-9 flex items-center justify-center rounded-xl border border-blue-100 bg-blue-50/80 text-blue-600 text-sm font-black shadow-sm">1</span>
                        <div><h2 class="font-semibold text-gray-900">Aturan default</h2><p class="text-xs text-gray-500 mt-1">Berlaku untuk setiap kombinasi pekerja dan tanggal terpilih.</p></div>
                    </div>
                    <div @input="changed()" @change="changed()">
                        @include('Absensi.partials.rule-fields', ['model' => 'rule', 'prefix' => 'default'])
                    </div>
                    <p class="mt-4 text-xs text-gray-400">Jam normal dan lembur dihitung mengikuti jadwal pada masing-masing tanggal.</p>
                </section>
                <section class="bg-white/75 backdrop-blur-xl border border-white/80 rounded-[2rem] overflow-hidden shadow-[0_18px_45px_rgba(15,23,42,0.07)] z-0">
                    <div class="p-5 sm:p-6 border-b border-gray-100">
                        <div class="flex justify-between gap-3 items-center mb-4">
                            <div class="flex items-center gap-3"><span class="w-9 h-9 flex items-center justify-center rounded-xl border border-blue-100 bg-blue-50/80 text-blue-600 text-sm font-black shadow-sm">2</span><h2 class="font-semibold text-gray-900">Pilih pekerja</h2></div>
                            <span class="text-xs text-gray-500" x-text="selected.length + ' / 25 dipilih'"></span>
                        </div>
                        <input type="search" x-model.debounce.350ms="search" @input.debounce.400ms="loadWorkers()" aria-label="Cari pekerja" placeholder="Cari nama atau NIK..." class="w-full border border-white/80 bg-white/65 shadow-inner rounded-xl px-4 py-2.5 text-sm focus:border-blue-300 focus:ring-2 focus:ring-blue-100">
                        <div class="flex justify-between mt-3 text-xs text-gray-400"><span>Pilihan tetap tersimpan saat pindah halaman.</span><button type="button" x-show="selected.length" @click="clearWorkers()" class="text-blue-600">Hapus pilihan</button></div>
                    </div>
                    <div :class="loading ? 'opacity-50 pointer-events-none' : ''" :aria-busy="loading">
                        <div class="px-5 sm:px-6 py-3 bg-blue-50/30 text-xs font-semibold text-gray-500">
                            <label class="flex gap-3 items-center"><input type="checkbox" @change="togglePage()" :checked="workerPage.workers.length > 0 && workerPage.workers.every(worker => selected.includes(worker.id))" class="accent-blue-600"> Pilih semua di halaman ini</label>
                        </div>
                        <template x-for="worker in workerPage.workers" :key="worker.id">
                            <label class="flex items-center gap-3 px-5 sm:px-6 py-3 border-t border-white/75 cursor-pointer hover:bg-blue-50/60 transition" :class="selected.includes(worker.id) ? 'bg-blue-50/60' : ''">
                                <input type="checkbox" :checked="selected.includes(worker.id)" @change="toggleWorker(worker.id)" class="accent-blue-600">
                                <span class="w-9 h-9 bg-gray-100 rounded-full flex items-center justify-center font-semibold text-xs text-gray-500" x-text="worker.name.slice(0, 2).toUpperCase()"></span>
                                <span><span class="block text-sm font-medium text-gray-800" x-text="worker.name"></span><span class="block text-xs text-gray-400 mt-0.5" x-text="worker.nik"></span></span>
                            </label>
                        </template>
                        <p x-show="!workerPage.workers.length" class="p-8 text-center text-sm text-gray-400">Tidak ada pekerja yang sesuai.</p>
                    </div>
                    <div class="border-t border-gray-100 px-5 py-4 flex justify-between items-center text-xs">
                        <span class="text-gray-400" x-text="`Halaman ${workerPage.page} · ${workerPage.total} pekerja`"></span>
                        <div class="flex gap-2">
                            <button type="button" @click="loadWorkers(workerPage.previous)" :disabled="!workerPage.previous || loading" class="px-3 py-2 border rounded-lg disabled:opacity-30">Sebelumnya</button>
                            <button type="button" @click="loadWorkers(workerPage.next)" :disabled="!workerPage.next || loading" class="px-3 py-2 border rounded-lg disabled:opacity-30">Berikutnya</button>
                        </div>
                    </div>
                </section>
            </div>
            <aside class="space-y-4 lg:sticky lg:top-24">
                <section class="bg-white/75 backdrop-blur-xl rounded-[2rem] border border-white/80 p-6 shadow-[0_18px_45px_rgba(15,23,42,0.07)]">
                    <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Ringkasan absensi</p>
                    <div class="py-5 my-4 border-y border-gray-100">
                        <p class="text-sm text-gray-600" x-text="`${dates.length} tanggal × ${selected.length} pekerja`"></p>
                        <p class="text-3xl font-bold text-gray-900 mt-2"><span x-text="total"></span> <span class="text-base font-medium text-gray-500">absensi</span></p>
                        <p class="text-xs text-gray-400 mt-2">Data yang sudah ada akan diperiksa sebelum disimpan.</p>
                    </div>
                    <p class="text-xs font-semibold text-gray-500">Default</p>
                    <p class="text-sm text-gray-800 mt-1" x-text="ruleLabel(rule)"></p>
                    <p class="text-xs text-gray-400 mt-2" x-text="exceptions.length ? exceptions.length + ' pengecualian akan diterapkan.' : 'Semua mengikuti aturan default.'"></p>
                    <button type="button" @click="showExceptions = !showExceptions" :disabled="!selected.length" class="w-full mt-5 px-4 py-3 border border-white/80 bg-white/65 shadow-sm rounded-xl text-sm font-semibold text-gray-600 hover:bg-white disabled:opacity-40 transition" x-text="showExceptions ? 'Tutup Pengecualian' : 'Atur Pengecualian'"></button>
                    <button type="button" @click="review()" :disabled="!total || busy" class="w-full mt-2 px-4 py-3 rounded-xl bg-blue-600 text-white text-sm font-semibold shadow-lg shadow-blue-200/60 hover:bg-blue-700 disabled:opacity-40 transition" x-text="busy ? 'Memeriksa...' : `Simpan ${total} Absensi`"></button>
                </section>
                <section x-show="showExceptions" x-cloak class="bg-white/75 backdrop-blur-xl rounded-[2rem] border border-white/80 p-5 shadow-[0_18px_45px_rgba(15,23,42,0.07)]">
                    <div class="flex justify-between items-center"><h2 class="text-sm font-semibold text-gray-900">Pengecualian</h2><button type="button" @click="openException()" :disabled="!selected.length" class="text-xs text-blue-600 font-semibold disabled:opacity-40">+ Tambah</button></div>
                    <p class="text-xs text-gray-400 mt-2 mb-4">Aturan khusus hanya untuk pekerja dan tanggal yang dipilih.</p>
                    <p x-show="!exceptions.length" class="text-sm text-gray-400 py-3">Belum ada pengecualian.</p>
                    <div class="space-y-4 max-h-80 overflow-auto">
                        <template x-for="group in exceptionGroups" :key="group.date">
                            <div><p class="text-xs font-semibold text-gray-500 mb-2" x-text="dateLabel(group.date)"></p>
                                <template x-for="item in group.items" :key="item.worker_id">
                                    <div class="flex items-start justify-between gap-2 py-2 border-t border-gray-100">
                                        <button type="button" @click="openException(item)" class="text-left text-sm hover:text-blue-600"><span class="font-medium" x-text="names[item.worker_id]"></span><span class="block text-xs text-gray-500 mt-0.5" x-text="'→ ' + ruleLabel(item.rule)"></span></button>
                                        <button type="button" @click="removeException(item)" :aria-label="'Hapus pengecualian ' + names[item.worker_id] + ' ' + dateLabel(item.date)" class="text-gray-400 hover:text-red-500">&times;</button>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </section>
            </aside>
        </fieldset>

        <dialog x-ref="exceptionDialog" class="m-auto w-[calc(100%_-_2rem)] max-w-[760px] overflow-y-auto overflow-x-visible rounded-[2rem] border border-white/80 bg-white p-0 shadow-[0_30px_80px_rgba(15,23,42,0.23)]" style="max-height: calc(100vh - 3rem)" aria-labelledby="exception-title">
            <form @submit.prevent="saveException()" class="flex flex-col p-6 sm:p-8" style="min-height: min(620px, calc(100vh - 3rem))">
                <div class="flex justify-between items-center mb-5"><h2 id="exception-title" class="text-lg font-bold">Atur pengecualian</h2><button type="button" @click="$refs.exceptionDialog.close()" aria-label="Tutup pengecualian" class="text-gray-400 text-xl">&times;</button></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    @include('Absensi.partials.searchable-dropdown', [
                        'prefix' => 'exception-date', 'label' => 'Tanggal', 'model' => 'exceptionDate',
                        'options' => 'dates.map(day => ({value: day, label: dateLabel(day)}))',
                        'placeholder' => 'Pilih tanggal', 'searchPlaceholder' => 'Cari tanggal...',
                    ])
                    @include('Absensi.partials.searchable-dropdown', [
                        'prefix' => 'exception-worker', 'label' => 'Pekerja', 'model' => 'exceptionWorker',
                        'options' => 'selected.map(id => ({value: id, label: names[id]}))',
                        'placeholder' => 'Pilih pekerja', 'searchPlaceholder' => 'Cari nama pekerja...',
                    ])
                </div>
                @include('Absensi.partials.rule-fields', ['model' => 'exceptionDraft', 'prefix' => 'exception'])
                <p x-show="exceptionError" x-text="exceptionError" role="alert" class="text-sm text-red-600 mt-3"></p>
                <div class="flex justify-end gap-3 mt-auto pt-6"><button type="button" @click="$refs.exceptionDialog.close()" class="px-4 py-2 text-sm text-gray-500">Batal</button><button type="submit" class="px-4 py-2.5 rounded-xl bg-blue-600 text-white text-sm font-semibold">Terapkan pengecualian</button></div>
            </form>
        </dialog>

        <dialog x-ref="previewDialog" @cancel.prevent="if (!busy) $refs.previewDialog.close()" class="m-auto w-[calc(100%_-_2rem)] max-w-[500px] max-h-[90vh] overflow-auto rounded-[2rem] border border-white/80 bg-white/95 backdrop-blur-2xl p-6 shadow-[0_30px_80px_rgba(15,23,42,0.23)]" aria-labelledby="preview-title">
            <h2 id="preview-title" class="text-xl font-bold text-gray-900">Periksa sebelum menyimpan</h2>
            <p class="text-sm text-gray-500 mt-2" x-text="`${preview?.total ?? 0} absensi akan diproses`"></p>
            <div class="grid grid-cols-2 gap-3 my-5">
                <div class="p-4 rounded-xl bg-emerald-50"><p class="text-2xl font-bold text-emerald-700" x-text="preview?.new ?? 0"></p><p class="text-xs text-emerald-700 mt-1">Absensi baru</p></div>
                <div class="p-4 rounded-xl bg-amber-50"><p class="text-2xl font-bold text-amber-700" x-text="preview?.existing ?? 0"></p><p class="text-xs text-amber-700 mt-1">Sudah ada</p></div>
            </div>
            <fieldset x-show="preview?.existing > 0" :disabled="busy" class="space-y-3">
                <legend class="text-sm font-semibold text-gray-800 mb-3">Absensi yang sudah ada</legend>
                <label class="flex items-start gap-3 border rounded-xl p-3 cursor-pointer" :class="existingPolicy === 'skip' ? 'border-blue-300 bg-blue-50' : 'border-gray-200'"><input type="radio" name="existing_policy" value="skip" x-model="existingPolicy" class="mt-1 accent-blue-600"><span class="text-sm font-medium">Lewati yang sudah ada<span class="block text-xs text-gray-500 font-normal mt-1">Pertahankan data dan verifikasi sebelumnya, termasuk yang memiliki pengecualian.</span></span></label>
                <label class="flex items-start gap-3 border rounded-xl p-3 cursor-pointer" :class="existingPolicy === 'update' ? 'border-blue-300 bg-blue-50' : 'border-gray-200'"><input type="radio" name="existing_policy" value="update" x-model="existingPolicy" class="mt-1 accent-blue-600"><span class="text-sm font-medium">Perbarui yang sudah ada<span class="block text-xs text-gray-500 font-normal mt-1">Terapkan aturan dan pengecualian terbaru. Verifikasi diatur ulang.</span></span></label>
            </fieldset>
            <details x-show="preview?.existing > 0" class="mt-4 text-xs text-gray-500">
                <summary class="cursor-pointer">Lihat absensi yang sudah ada</summary>
                <ul class="max-h-40 overflow-auto mt-2 space-y-1"><template x-for="(item, index) in (preview?.existing_records ?? [])" :key="index"><li x-text="dateLabel(item.date) + ' · ' + item.name"></li></template></ul>
            </details>
            <p x-show="existingPolicy === 'update' && preview?.existing > 0" class="text-xs text-amber-700 mt-4">Dengan menyimpan, Anda mengonfirmasi penggantian absensi yang sudah ada untuk pilihan ini.</p>
            <p x-show="error" x-text="error" role="alert" class="mt-4 text-sm text-red-600 whitespace-pre-line"></p>
            <div class="flex justify-end gap-2 mt-6">
                <button type="button" @click="$refs.previewDialog.close()" :disabled="busy" class="px-4 py-2.5 text-sm text-gray-500 disabled:opacity-40">Batal</button>
                <button type="button" @click="save()" :disabled="busy" class="px-4 py-2.5 rounded-xl bg-blue-600 text-sm font-semibold text-white disabled:opacity-40" x-text="busy ? 'Menyimpan...' : (existingPolicy === 'skip' && preview?.new === 0 ? 'Selesai — lewati semua' : `Simpan ${existingPolicy === 'skip' ? (preview?.new ?? 0) : (preview?.total ?? 0)} Absensi`)"></button>
            </div>
        </dialog>
    </div>
@endsection

@section('scripts')
    <script src="/js/bulk-daily-attendance.js"></script>
@endsection
