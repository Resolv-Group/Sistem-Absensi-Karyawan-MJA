@extends('layout')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER SECTION --}}
        <div class="mb-8">
            <nav class="flex text-sm font-medium text-gray-500 mb-2">
                <a href="/mitra-kerja" class="hover:text-gray-700 transition">Mitra Kerja</a>
                <span class="mx-2 text-gray-400">/</span>
                <span class="text-blue-600">Tambah</span>
            </nav>

            <div class="flex items-center gap-4">
                <a href="/mitra-kerja"
                    class="group p-2 rounded-full border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 transform group-hover:-translate-x-0.5 transition" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Tambah Mitra Kerja</h1>
                    <p class="text-sm text-gray-500 mt-1">Isi formulir di bawah untuk mendaftarkan informasi mitra kerja baru.</p>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 p-4 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM CARD --}}
        <form id="formTambahMitraKerja" action="#" method="POST" enctype="multipart/form-data"
      class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden max-w-5xl mx-auto">
    @csrf

    {{-- HEADER --}}
    <div class="px-8 py-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-blue-100 text-blue-600 rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-900">Tambah Mitra Kerja</h2>
                <p class="text-sm text-gray-500">Lengkapi data identitas perusahaan dan status kontrak.</p>
            </div>
        </div>
    </div>

    <div class="p-8">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-8">

            {{-- LEFT COLUMN: Logo Upload --}}
            <div class="md:col-span-4 lg:col-span-3 space-y-4">
                <label class="block text-sm font-bold text-gray-700">Logo Perusahaan</label>

                <div class="relative w-full aspect-square rounded-2xl border-2 border-dashed border-gray-300 hover:border-blue-500 hover:bg-blue-50/50 transition overflow-hidden flex flex-col items-center justify-center bg-gray-50 cursor-pointer group"
                    onclick="document.getElementById('fotoInput').click()">

                    <input type="file" id="fotoInput" name="foto" accept="image/*" class="hidden" onchange="previewPhoto(event)">

                    {{-- Placeholder --}}
                    <div id="placeholder" class="text-center pointer-events-none p-4">
                        <div class="bg-white p-3 rounded-full shadow-sm inline-block mb-3 group-hover:scale-110 transition-transform duration-200">
                            <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-gray-900">Upload Logo</p>
                        <p class="text-xs text-gray-500 mt-1">PNG, JPG (Max 2MB)</p>
                    </div>

                    {{-- Preview --}}
                    <img id="previewImage" class="absolute inset-0 w-full h-full object-cover hidden" alt="Preview Foto">

                    {{-- Remove Button --}}
                    <button type="button" id="removeBtn" onclick="removePhoto(event)"
                        class="absolute top-2 right-2 bg-white text-red-600 p-1.5 rounded-full shadow-md hover:bg-red-50 transition hidden z-10">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
            </div>

            {{-- RIGHT COLUMN: Form Inputs --}}
            <div class="md:col-span-8 lg:col-span-9 space-y-8">

                {{-- SECTION 1: Identitas Perusahaan --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">

                    {{-- Nama Mitra --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Perusahaan / Mitra <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_mitra" placeholder="PT. Contoh Sejahtera Abadi"
                            class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-blue-200 transition py-2.5 px-4 text-sm font-medium"
                            value="{{ old('nama_mitra') }}">
                    </div>

                    {{-- Bidang Usaha (Alpine JS Dropdown Restored) --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Bidang Usaha</label>
                        <div x-data="{ open: false, selected: '{{ old('bidang_usaha') }}' || '', list: [{ val: '1', label: 'IT & Software' }, { val: '2', label: 'Manufaktur' }, { val: '3', label: 'Retail' }] }"
                             class="relative">

                            {{-- Input Hidden --}}
                            <input type="hidden" name="bidang_usaha" x-model="selected">

                            {{-- Trigger Button --}}
                            <button type="button" @click="open=!open"
                                class="w-full bg-gray-50 rounded-lg py-2.5 px-3 cursor-pointer flex justify-between items-center select-none shadow-sm hover:border-gray-200 hover:ring-1 hover:ring-gray-200 transition">
                                <span x-text="list.find(x=>x.val==selected)?.label || 'Pilih Bidang...'" :class="selected ? 'text-gray-900' : 'text-gray-400'"></span>
                                <svg class="w-4 h-4 text-gray-500 transform transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            {{-- Dropdown Body --}}
                            <ul x-show="open" @click.outside="open=false" x-transition.opacity
                                class="absolute w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-48 overflow-y-auto z-50">
                                <template x-for="item in list" :key="item.val">
                                    <li @click="selected=item.val; open=false"
                                        class="px-4 py-2.5 text-sm hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition flex items-center justify-between group">
                                        <span x-text="item.label"></span>
                                        <svg x-show="selected == item.val" class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    {{-- Pimpinan --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Nama Pimpinan</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </span>
                            <input type="text" name="pimpinan" placeholder="Nama Direktur"
                                class="w-full pl-10 rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-blue-200 transition py-2.5 px-4 text-sm font-medium"
                                value="{{ old('pimpinan') }}">
                        </div>
                    </div>

                    {{-- Telepon --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">No. Telp Perusahaan</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </span>
                            <input type="text" name="telp_perusahaan" placeholder="021-xxxx / 081-xxxx"
                                class="w-full pl-10 rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-blue-200 transition py-2.5 px-4 text-sm font-medium"
                                value="{{ old('telp_perusahaan') }}">
                        </div>
                    </div>

                    <div x-data="{ open: false, selected: '{{ old('status_pajak') }}' || '', list: ['PKP (Pengusaha Kena Pajak)','NON-PKP',] }" class="relative">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Status Pajak</label>

                        <input type="hidden" name="status_pajak" x-model="selected">

                        <div @click="open=!open"
                            class=" bg-gray-50 rounded-lg py-2.5 px-3 cursor-pointer flex justify-between items-center select-none shadow-sm hover:border-gray-200 hover:ring-1 hover:ring-gray-200 transition">
                            <span x-text="selected || 'Pilih Status Pajak'"
                                :class="selected ? 'text-gray-900' : 'text-gray-400'"></span>
                            <svg class="w-4 h-4 text-gray-500" :class="{ 'rotate-180': open }" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>

                        <ul x-show="open" @click.outside="open=false" x-transition.opacity.duration.200ms
                            class="absolute w-full mt-1 border border-gray-200 bg-white rounded-lg shadow-xl overflow-y-auto max-h-60 z-50">
                            <template x-for="item in list" :key="item">
                                <li @click="selected=item; open=false"
                                    class="px-3 py-2.5 text-sm hover:bg-blue-50 hover:text-blue-700 cursor-pointer transition border-b border-gray-50 last:border-0"
                                    x-text="item">
                                </li>
                            </template>
                        </ul>
                        @error('jabatan')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Alamat (New from ERD) --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-1">Alamat Lengkap</label>
                        <textarea name="alamat" rows="3" placeholder="Jl. Raya Utama No. 123, Kecamatan..."
                            class="w-full rounded-lg border-gray-300 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-blue-200 transition py-2.5 px-4 text-sm font-medium resize-none">{{ old('alamat') }}</textarea>
                    </div>

                </div>
                {{-- VISUAL DIVIDER --}}
<div class="col-span-1 md:col-span-12 mt-4 mb-2">
    <div class="flex items-center gap-4">
        <div class="h-px flex-1 bg-gray-200"></div>
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider bg-white px-2">
            Detail Kontrak & Legalitas
        </span>
        <div class="h-px flex-1 bg-gray-200"></div>
    </div>
</div>

{{-- SECTION 2: INPUTS --}}
<div class="col-span-1 md:col-span-12 grid grid-cols-1 sm:grid-cols-3 gap-6">

    {{-- Mulai Kerjasama --}}
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">Mulai Kerjasama</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <input type="date" name="tgl_mulai_kerjasama"
                class="w-full pl-10 rounded-lg border-gray-300 bg-gray-50 text-gray-900
                focus:bg-white focus:border-blue-500 focus:ring-blue-200 transition py-2.5 px-3 text-sm font-medium"
                value="{{ old('tgl_mulai_kerjasama') }}">
        </div>
    </div>

    {{-- Berakhir MoU --}}
    <div>
        <label class="block text-sm font-bold text-gray-700 mb-1">Berakhir MoU</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <input type="date" name="tgl_akhir_mou"
                class="w-full pl-10 rounded-lg border-gray-300 bg-gray-50 text-gray-900
                focus:bg-white focus:border-blue-500 focus:ring-blue-200 transition py-2.5 px-3 text-sm font-medium"
                value="{{ old('tgl_akhir_mou') }}">
        </div>
    </div>

    {{-- Status MoU (Consistent Design) --}}
    <div x-data="{ open: false, selected: '{{ old('status_mou') }}' || 'Aktif', list: ['Aktif','Perpanjangan','Tidak Aktif'] }" class="relative">
    <label class="block text-sm font-bold text-gray-700 mb-1">Status MoU</label>
    <input type="hidden" name="status_mou" x-model="selected">

    <button type="button" @click="open=!open"
        class="w-full bg-gray-50 border border-gray-300 rounded-lg py-2.5 px-4 flex justify-between items-center text-sm font-medium hover:bg-white focus:ring-2 focus:ring-blue-200 focus:border-blue-500 transition">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full"
                    :class="{ 'bg-green-500': selected == 'Aktif', 'bg-yellow-500': selected == 'Perpanjangan', 'bg-red-500': selected.includes('Tidak') }">
            </span>
            <span x-text="selected" class="text-gray-900"></span>
        </div>
        <svg class="w-4 h-4 text-gray-500 transform transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
    </button>

    {{-- UPDATED: Added 'bottom-full mb-1 origin-bottom' to make it open upwards --}}
    <ul x-show="open" @click.outside="open=false" x-transition.opacity
        class="absolute w-full bottom-full mb-1 bg-white border border-gray-200 rounded-lg shadow-xl z-50 py-1 origin-bottom">
        <template x-for="item in list" :key="item">
            <li @click="selected=item; open=false"
                class="px-4 py-2.5 text-sm hover:bg-blue-50 hover:text-blue-600 cursor-pointer transition flex items-center gap-2">
                <span class="w-2 h-2 rounded-full"
                        :class="{ 'bg-green-500': item == 'Aktif', 'bg-yellow-500': item == 'Perpanjangan', 'bg-red-500': item.includes('Tidak') }">
                </span>
                <span x-text="item"></span>
            </li>
        </template>
    </ul>
    @error('status_mou')
        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

</div>

            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="bg-gray-50 px-8 py-5 flex items-center justify-end gap-3 border-t border-gray-200">
        <a href="{{ route('view.pekerja') }}"
            class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-2 focus:ring-gray-200 transition shadow-sm">
            Batalkan
        </a>
        <button type="submit" id="save-btn"
            class="flex items-center gap-2 px-6 py-2.5 text-sm font-bold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition shadow-md hover:shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Simpan Data
        </button>
    </div>
</form>
    </div>
@endsection

@section('scripts')
    <script src="/js/tambah-pekerja.js"></script>

    @if (session('success'))
        <script>
            Swal.fire({
                title: "{{ session('success') }}",
                text: "Apakah Anda mau menambah data lagi?",
                icon: 'success',
                showDenyButton: true,
                confirmButtonText: "Tambah lagi",
                denyButtonText: "Ke daftar pekerja",
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('view.tambah.pekerja') }}";
                } else {
                    window.location.href = "{{ route('view.pekerja') }}";
                }
            });
        </script>
    @endif
@endsection
