@extends('layout')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER SECTION --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">

            {{-- Left Side: Breadcrumb & Title --}}
            <div>
                <nav class="flex text-sm font-medium text-gray-500 mb-2">
                    <a href="{{ route('view.dashboard') }}" class="hover:text-gray-700 transition">Dashboard</a>
                    <span class="mx-2 text-gray-400">/</span>
                    <a href="{{ route('view.staff') }}" class="hover:text-gray-700 transition">Staff</a>
                    <span class="mx-2 text-gray-400">/</span>
                    <span class="text-blue-600">Detail</span>
                </nav>

                <div class="flex items-center gap-4">
                    <a href="{{ route('view.staff') }}"
                        class="group p-2 rounded-full border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 transform group-hover:-translate-x-0.5 transition" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Detail Staff</h1>
                        <p class="text-sm text-gray-500 mt-1">Informasi lengkap data diri staff.</p>
                    </div>
                </div>
            </div>

            {{-- Right Side: Action Buttons --}}
            <div class="flex items-center gap-3">
                <button onclick="confirmToggleStatus({{ $staff->id }}, {{ $staff->status_aktif }})"
                    class="px-4 py-2 text-sm font-medium
                        {{ $staff->status_aktif
                            ? 'text-red-600 bg-red-50 border-red-100 hover:bg-red-100'
                            : 'text-emerald-600 bg-emerald-50 border-emerald-100 hover:bg-emerald-100' }}
                        border rounded-lg transition shadow-sm">
                    {{ $staff->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
                {{-- <button
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition shadow-sm">
                    Cetak Data
                </button> --}}
                <a href="{{ route('view.ubah.staff', $staff->id) }}"
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
        <x-notification />

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- LEFT COLUMN: Profile Card --}}
            {{-- LEFT COLUMN: Profile Card --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden sticky top-8">

                    {{-- Banner & Avatar --}}
                    <div class="relative">
                        <div class="h-32 bg-gradient-to-br from-green-500 to-emerald-700"></div>
                        <div class="absolute -bottom-16 left-0 right-0 flex justify-center">
                            <div
                                class="h-32 w-32 rounded-full border-[5px] border-white shadow-lg bg-gray-100 overflow-hidden">
                                @if ($staff->image_base64)
                                    <img src="{{ $staff->image_base64 }}" alt="Foto {{ $staff->nama }}"
                                        class="w-full h-full object-cover" />
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($staff->nama) }}&background=random&size=128"
                                        alt="Profile Placeholder" class="w-full h-full object-cover">
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Identity Section --}}
                    <div class="pt-20 pb-6 px-6 text-center">
                        <h2 class="text-xl font-bold text-gray-900 leading-tight">{{ $staff->nama }}</h2>
                        <p class="text-sm font-medium text-gray-500 mt-1">{{ $staff->jabatan }}</p>

                        {{-- Status Badges (Centered) --}}
                        <div class="mt-4 flex flex-wrap justify-center gap-2">
                            {{-- Status Keaktifan --}}
                            @if ($staff->status_aktif == 1)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                    Aktif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                    Non Aktif
                                </span>
                            @endif

                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">
                                {{ $staff->perusahaan }}
                            </span>
                        </div>
                    </div>

                    {{-- Quick Stats Grid (Cleaner than a list) --}}
                    <div class="px-6 pb-6">
                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                            <div class="grid grid-cols-2 gap-y-4 gap-x-2">

                                {{-- ID Staff --}}
                                <div class="col-span-1">
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">ID Staff</p>
                                    <p class="text-sm font-bold text-gray-900 mt-0.5 truncate">{{ $staff->id_staff }}</p>
                                </div>

                                {{-- Unit Kerja --}}
                                <div class="col-span-1">
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Unit Kerja</p>
                                    <p class="text-sm font-bold text-gray-900 mt-0.5 truncate"
                                        title="{{ $staff->unit_kerja }}">{{ $staff->unit_kerja }}</p>
                                </div>

                                {{-- Bergabung --}}
                                <div class="col-span-1">
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Bergabung</p>
                                    <p class="text-sm font-medium text-gray-700 mt-0.5">
                                        {{ formatTanggal($staff->tgl_bergabung) }}</p>
                                </div>

                                {{-- Status PKWT --}}
                                <div class="col-span-1">
                                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Tipe PKWT</p>
                                    <p class="text-sm font-medium text-gray-700 mt-0.5">
                                        {{ $staff->status_perjanjian_kerja }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Actions (Distinct Footer) --}}
                    <div class="border-t border-gray-100 grid grid-cols-2 divide-x divide-gray-100">
                        <a href="tel:{{ $staff->telp }}"
                            class="flex items-center justify-center gap-2 py-4 text-sm font-medium text-gray-600 hover:text-green-600 hover:bg-gray-50 transition group">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-green-500 transition" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <span>Call</span>
                        </a>
                        <a href="mailto:{{ $staff->email }}"
                            class="flex items-center justify-center gap-2 py-4 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-gray-50 transition group">
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-blue-500 transition" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span>Email</span>
                        </a>
                    </div>
                </div>

                {{-- Optional: If you really need the extra dates, put them in a small collapsible text below the card or leave them for the tabs --}}
                <div class="mt-4 text-center">
                    <p class="text-xs text-gray-400">Data dibuat pada {{ formatTanggal($staff->created_at) }}</p>
                </div>
            </div>

            {{-- RIGHT COLUMN: Detail Info --}}
            <div class="lg:col-span-2">
                <div x-data="{ tab: 'personal' }"
                    class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden min-h-full">
                    <div class="border-b border-gray-200 bg-gray-50/50 px-6">

                        {{-- Tabs --}}
                        <nav class="-mb-px flex space-x-8">
                            <button @click="tab='personal'"
                                :class="tab == 'personal' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500'"
                                class="whitespace-nowrap py-4 px-1 font-medium text-sm">
                                Personal Info
                            </button>
                            <button @click="tab='emergency'"
                                :class="tab == 'emergency' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500'"
                                class="whitespace-nowrap py-4 px-1 font-medium text-sm">
                                Emergency Contact
                            </button>
                            <button @click="tab='history'"
                                :class="tab == 'history' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500'"
                                class="whitespace-nowrap py-4 px-1 font-medium text-sm">
                                Riwayat
                            </button>
                        </nav>
                    </div>

                    <div class="p-8">
                        <div x-show="tab=='personal'">
                            @include('Staff.Detail.personal')
                        </div>
                        <div x-show="tab=='emergency'">
                            @include('Staff.Detail.emergency')
                        </div>
                        <div x-show="tab=='history'">
                            @include('Staff.Detail.histori')
                        </div>

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
            title: isAktif ? 'Nonaktifkan staff?' : 'Aktifkan staff?',
            text: isAktif ?
                'staff ini akan dinonaktifkan.' :
                'staff ini akan diaktifkan kembali.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: isAktif ? '#dc2626' : '#059669',
            cancelButtonColor: '#6b7280',
            confirmButtonText: isAktif ? 'Ya, nonaktifkan' : 'Ya, aktifkan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/staff/toggle-status/${id}`, {
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
