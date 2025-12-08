@extends('layout')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER SECTION --}}
        <div class="mb-8 flex flex-col sm:flex-row sm:items-end justify-between gap-4">

            {{-- Left Side: Breadcrumb & Title --}}
            <div>
                <nav class="flex text-sm font-medium text-gray-500 mb-2">
                    <a href="/daftar-pekerja" class="hover:text-gray-700 transition">Pekerja</a>
                    <span class="mx-2 text-gray-400">/</span>
                    <span class="text-blue-600">Detail</span>
                </nav>

                <div class="flex items-center gap-4">
                    <a href="/daftar-pekerja"
                        class="group p-2 rounded-full border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5 transform group-hover:-translate-x-0.5 transition" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Detail Pekerja</h1>
                        <p class="text-sm text-gray-500 mt-1">Informasi lengkap data diri dan kepegawaian.</p>
                    </div>
                </div>
            </div>

            {{-- Right Side: Action Buttons --}}
            <div class="flex items-center gap-3">
                <button
                    class="px-4 py-2 text-sm font-medium text-red-600 bg-red-50 border border-red-100 rounded-lg hover:bg-red-100 transition shadow-sm">
                    Nonaktifkan
                </button>
                <button
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition shadow-sm">
                    Cetak Data
                </button>
                <a href="{{ route('view.ubah.pekerja', $pekerja->id) }}"
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- LEFT COLUMN: Profile Card --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    {{-- Banner --}}
                    <div class="h-32 bg-gradient-to-r from-blue-500 to-blue-600"></div>

                    <div class="px-6 pb-6 relative text-center">
                        {{-- Avatar --}}
                        <div class="relative -mt-16 inline-block">
                            <div class="h-32 w-32 rounded-full border-4 border-white shadow-md bg-gray-200 overflow-hidden">

                                {{-- Check if our custom accessor returns data --}}
                                @if ($pekerja->image_base64)
                                    {{-- Display the Base64 image --}}
                                    <img src="{{ $pekerja->image_base64 }}" alt="Foto {{ $pekerja->nama }}"
                                        class="w-full h-full object-cover" />
                                @else
                                    {{-- Fallback image --}}
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($pekerja->nama) }}&background=random&size=128"
                                        alt="Profile Placeholder" class="w-full h-full object-cover">
                                @endif

                            </div>
                        </div>

                        {{-- Name & Unit --}}
                        <h2 class="mt-4 text-xl font-bold text-gray-900">
                            {{ Str::limit(ucwords(collect(explode(' ', strtolower($pekerja->nama)))->take(2)->implode(' ')),15) }}
                        </h2>


                        <span
                            class="inline-block mt-2 px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-50 rounded-full border border-blue-100">
                            PT. DOOR PAEYA
                        </span>

                        {{-- Info List --}}
                        <div class="mt-8 text-left space-y-4 border-t border-gray-100 pt-6">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 font-medium">ID Pekerja</span>
                                <span class="text-sm font-bold text-gray-900">{{ $pekerja->id }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 font-medium">Tanggal Bergabung</span>
                                <span
                                    class="text-sm font-bold text-gray-900">{{ formatTanggal($pekerja->tgl_bergabung) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 font-medium">Tanggal Resign</span>
                                <span
                                    class="text-sm font-bold text-gray-900">{{ formatTanggal($pekerja->tgl_resign) ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 font-medium">Status</span>
                                @if ($pekerja->status_aktif == 1)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    bg-green-100 text-green-800 border border-green-200">
                                        <span class="w-1.5 h-1.5 bg-green-600 rounded-full mr-1.5"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    bg-red-100 text-red-800 border border-red-200">
                                        <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-1.5"></span>
                                        Non Aktif
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
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
                            <button @click="tab='salary'"
                                :class="tab == 'salary' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500'"
                                class="whitespace-nowrap py-4 px-1 font-medium text-sm">
                                PKWT
                            </button>
                            <button @click="tab='history'"
                                :class="tab == 'history' ? 'border-b-2 border-blue-500 text-blue-600' : 'text-gray-500'"
                                class="whitespace-nowrap py-4 px-1 font-medium text-sm">
                                Histori
                            </button>
                        </nav>
                    </div>

                    <div class="p-8">
                        <div x-show="tab=='personal'">
                            @include('Pekerja.Detail.personal')
                        </div>

                        <div x-show="tab=='emergency'">
                            @include('Pekerja.Detail.emergency')
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection
