@extends('layout')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER SECTION --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">

            {{-- Left Side: Breadcrumb & Title --}}
            <div>
                <nav class="flex text-sm font-medium text-gray-500 mb-2">
                    <a href="/mitra-kerja" class="hover:text-gray-700 transition">Mitra Kerja</a>
                    <span class="mx-2 text-gray-400">/</span>
                    <span class="text-blue-600">Detail</span>
                </nav>

                <div class="flex items-center gap-4">
                    <a href="/mitra-kerja"
                        class="group p-2 rounded-full border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 transform group-hover:-translate-x-0.5 transition" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Detail Mitra Kerja</h1>
                        <p class="text-sm text-gray-500 mt-1">Informasi lengkap profil perusahaan dan legalitas kontrak.</p>
                    </div>
                </div>
            </div>

            {{-- Right Side: Action Buttons --}}
            <div class="flex items-center gap-3">
                <button
                    onclick="confirmToggleStatus({{ $mitraKerja->id }}, {{ $mitraKerja->status_aktif }})"
                    class="px-4 py-2 text-sm font-medium    
                        {{ $mitraKerja->status_aktif ? 'text-red-600 bg-red-50 border-red-100 hover:bg-red-100'
                                                : 'text-emerald-600 bg-emerald-50 border-emerald-100 hover:bg-emerald-100' }}
                        border rounded-lg transition shadow-sm">
                    {{ $mitraKerja->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
                <a href="{{ route('view.ubah.mitra-kerja', $mitraKerja->id) }}"
                    class="px-4 py-2 text-sm font-medium text-white bg-black border border-black rounded-lg hover:bg-gray-800 transition shadow-sm flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Ubah Data
                </a>
            </div>
        </div>

        {{-- Success Notification Floating Center --}}
        {{-- <x-notification /> --}}

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- LEFT COLUMN: Company Profile Card --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden sticky top-8">
                    {{-- Banner --}}
                    <div class="h-32 bg-gradient-to-r from-blue-600 to-indigo-700 relative">
                        <div class="absolute inset-0 bg-white/10 pattern-dots"></div>
                    </div>

                    <div class="px-6 pb-6 relative text-center">
                        {{-- Company Logo --}}
                        <div class="relative -mt-16 inline-block">
                            <div
                                class="h-32 w-32 rounded-xl border-4 border-white shadow-lg bg-white overflow-hidden flex items-center justify-center">
                                @if ($mitraKerja->image_base64)
                                    <img class="w-full h-full object-cover" src="{{ $mitraKerja->image_base64 }}"
                                        alt="{{ $mitraKerja->nama_mitra }}">
                                @else
                                    <img class="w-full h-full object-cover"
                                        src="https://ui-avatars.com/api/?name={{ urlencode($mitraKerja->nama_mitra) }}&background=random&color=fff&size=128"
                                        alt="">
                                @endif
                            </div>
                        </div>

                        {{-- Company Name & Status --}}
                        <h2 class="mt-4 text-xl font-bold text-gray-900 leading-tight">
                            {{ Str::limit(ucwords(collect(explode(' ', strtolower($mitraKerja->nama_mitra)))->take(2)->implode(' ')),15) }}
                        </h2>
                        
                        <div class="mt-2 flex justify-center gap-2">
                            @if($mitraKerja->status_mou === "Aktif Disnaker")
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    <span class="w-1.5 h-1.5 bg-green-600 rounded-full mr-1.5"></span>
                                    Aktif Disnaker
                                </span>
                            @elseif($mitraKerja->status_mou === "Perpanjangan")
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                    <span class="w-1.5 h-1.5 bg-yellow-600 rounded-full mr-1.5"></span>
                                    Segera Habis
                                </span>
                            @elseif($mitraKerja->status_mou === "Tidak Aktif")
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-red-800 border border-red-200">
                                    <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-1.5"></span>
                                    Tidak Aktif
                                </span>
                            @endif
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $mitraKerja->status_pajak }}
                            </span>
                        </div>

                        {{-- Quick Info List --}}
                        <div class="mt-8 text-left space-y-4 border-t border-gray-100 pt-6">
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-500 font-medium">Bidang Usaha</span>
                                <span class="text-sm font-bold text-gray-900 text-right">{{ $mitraKerja->bidangUsaha->nama }}</span>
                            </div>
                            <div class="flex justify-between items-start">
                                <span class="text-sm text-gray-500 font-medium">Pimpinan</span>
                                <div class="text-right">
                                    <span class="block text-sm font-bold text-gray-900">{{ $mitraKerja->pimpinan }}</span>
                                    <span class="text-xs text-gray-400">Penanggung Jawab</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 font-medium">Kerjasama Sejak</span>
                                <span class="text-sm font-bold text-gray-900">{{ formatTanggal($mitraKerja->tgl_mulai_kerjasama) }}</span>
                            </div>
                        </div>

                        {{-- Contact Actions --}}
                        @if ($mitraKerja->telp_perusahaan)
                            <div class="mt-6 grid grid-cols-1 gap-3">
                                <a href="tel:{{ $mitraKerja->telp_perusahaan }}"
                                    class="flex items-center justify-center gap-2 w-full py-2 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 border border-gray-200 transition">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                        </path>
                                    </svg>
                                    Telepon
                                </a>
                                {{-- <a href="mailto:info@contoh.com"
                                class="flex items-center justify-center gap-2 w-full py-2 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg hover:bg-gray-100 border border-gray-200 transition">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                            Email
                            </a> --}}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Detail Info Tabs --}}
            <div class="lg:col-span-2">
                <div x-data="{ tab: 'identity' }"
                    class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden min-h-full">
                    <div class="border-b border-gray-200 bg-gray-50/50 px-6">

                        {{-- Tabs --}}
                        <nav class="-mb-px flex space-x-8">
                            <button @click="tab='identity'"
                                :class="tab == 'identity' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500'"
                                class="whitespace-nowrap py-4 px-1 font-medium text-sm">
                                Identitas Perusahaan
                            </button>
                            {{-- <button @click="tab='contract'"
                                :class="tab == 'contract' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500'"
                                class="whitespace-nowrap py-4 px-1 font-medium text-sm">
                                Kontrak & MoU
                            </button> --}}
                            {{-- <button @click="tab='workers'"
                                :class="tab == 'workers' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500'"
                                class="whitespace-nowrap py-4 px-1 font-medium text-sm">
                                Tenaga Kerja
                            </button> --}}
                        </nav>
                    </div>

                    <div class="p-8">
                        <div x-show="tab=='identity'">
                            @include('Mitra Kerja.Detail.identitas-perusahaan')
                        </div>
                        {{-- <div x-show="tab=='contract'">
                            @include('Mitra Kerja.Detail.contract')
                        </div> --}}
                        {{-- <div x-show="tab=='workers'">
                            @include('Mitra Kerja.Detail.workers')
                        </div> --}}

                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection

<script>
function confirmToggleStatus(id, statusAktif) {
    const isAktif = statusAktif == 1;

    Swal.fire({
        title: isAktif ? 'Nonaktifkan Mitra Kerja?' : 'Aktifkan Mitra Kerja?',
        text: isAktif
            ? 'Mitra Kerja ini akan dinonaktifkan.'
            : 'Mitra Kerja ini akan diaktifkan kembali.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: isAktif ? '#dc2626' : '#059669',
        cancelButtonColor: '#6b7280',
        confirmButtonText: isAktif ? 'Ya, nonaktifkan' : 'Ya, aktifkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/mitra-kerja/toggle-status/${id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error();
                return res.json();
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => location.reload());
            })
            .catch(() => {
                Swal.fire('Error', 'Terjadi kesalahan', 'error');
            });
        }
    });
}
</script>
