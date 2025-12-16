@extends('layout')

@section('content')
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Success Notification --}}
        <x-notification />

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Pengaturan Akun</h1>
            <p class="mt-2 text-sm text-gray-500">
                Kelola informasi profil, kontak, dan keamanan akun Anda dalam satu tempat.
            </p>
        </div>

        {{-- MAIN FORM WRAPPER --}}
        <form action="{{ route('profil.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT') {{-- Assuming you use PUT for updates --}}

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                {{-- LEFT COLUMN: PROFILE PHOTO (Width: 4/12) --}}
                <div class="lg:col-span-4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden sticky top-8">

                        {{-- Decorative Banner --}}
                        <div class="h-32 bg-gradient-to-br from-blue-600 to-blue-800 relative"></div>

                        <div class="px-6 pb-8 text-center relative">
                            {{-- Avatar Container --}}
                            <div class="relative -mt-16 mb-5 inline-block group">
                                <div
                                    class="h-32 w-32 rounded-full border-4 border-white shadow-md bg-white overflow-hidden mx-auto relative">
                                    {{-- Image Preview Target --}}
                                    <img id="avatar-preview"
                                        src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=0D8ABC&color=fff&size=256' }}"
                                        class="w-full h-full object-cover transition-opacity hover:opacity-90">
                                </div>

                                {{-- Edit Photo Button (Overlay) --}}
                                <label for="foto-input"
                                    class="absolute bottom-1 right-1 bg-white text-gray-700 p-2 rounded-full border border-gray-200 shadow-sm cursor-pointer hover:bg-blue-50 hover:text-blue-600 transition-colors z-10"
                                    title="Ganti Foto">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </label>

                                {{-- Hidden File Input --}}
                                <input type="file" id="foto-input" name="foto" class="hidden" accept="image/*"
                                    onchange="previewImage(this)">
                            </div>

                            {{-- Static Name Display --}}
                            <h2 class="text-xl font-bold text-gray-900">{{ Auth::user()->name }}</h2>
                            <p class="text-sm text-gray-500 mb-4">{{ Auth::user()->email }}</p>


                            {{-- Badges --}}
                            <div class="flex flex-wrap justify-center gap-2 mb-6">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                    {{ strtoupper(Auth::user()->role ?? 'Staff') }}
                                </span>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-100">
                                    Aktif
                                </span>
                            </div>

                            {{-- Data Points --}}
                            <div class="grid grid-cols-2 gap-4 border-t border-gray-100 pt-6">
                                <div class="text-center col-span-2">
                                    <span class="block text-xs text-gray-400 uppercase tracking-wider font-semibold">Member
                                        Sejak</span>
                                    <span class="block text-sm font-semibold text-gray-700 mt-1">
                                        {{ Auth::user()->created_at ? Auth::user()->created_at->format('d M Y') : '-' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: EDITABLE FORMS (Width: 8/12) --}}
                <div class="lg:col-span-8 space-y-6">

                    {{-- SECTION 1: PERSONAL INFO --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-lg font-bold text-gray-900">Informasi Pribadi</h3>
                            <p class="text-xs text-gray-500">Perbarui kontak dan data diri Anda.</p>
                        </div>

                        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Name (Editable) --}}
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Nama
                                    Lengkap</label>
                                <input type="text" name="name" value="{{ old('name', Auth::user()->name) }}" readonly
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm
           focus:outline-none focus:ring-0 focus:border-gray-300 cursor-default">
                                @error('name')
                                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Email</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                                        class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors">
                                </div>
                                @error('email')
                                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Phone --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Nomor
                                    Telepon</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="telp"
                                        value="{{ old('telp', Auth::user()->staff->telp ?? '') }}" placeholder="08xxxxxxxx"
                                        class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors">
                                </div>
                                @error('telp')
                                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- SECTION 2: SECURITY --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="text-lg font-bold text-gray-900">Keamanan</h3>
                            <p class="text-xs text-gray-500">Ganti password (kosongkan jika tidak ingin mengubah).</p>
                        </div>

                        <div class="p-6 space-y-6">
                            {{-- Current Password --}}
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Password Saat Ini</label>
                                <div class="relative">
                                    <input type="password" id="current_password" name="current_password"
                                        class="w-full pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors"
                                        placeholder="Diperlukan untuk menyimpan perubahan sensitif">
                                    <button type="button" onclick="togglePassword('current_password')"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                        <svg id="current_password-show" class="h-5 w-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <svg id="current_password-hide" class="h-5 w-5 hidden" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                        </svg>
                                    </button>
                                </div>
                                @error('current_password')
                                    <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- New Password --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Password Baru
                                        (Opsional)</label>
                                    <div class="relative">
                                        <input type="password" id="new_password" name="new_password"
                                            class="w-full pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors"
                                            placeholder="Min. 8 karakter">
                                        <button type="button" onclick="togglePassword('new_password')"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                            <svg id="new_password-show" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg id="new_password-hide" class="h-5 w-5 hidden" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                    @error('new_password')
                                        <span class="text-xs text-red-500 mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Confirm Password --}}
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Ulangi Password
                                        Baru</label>
                                    <div class="relative">
                                        <input type="password" id="new_password_confirmation"
                                            name="new_password_confirmation"
                                            class="w-full pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-500 transition-colors"
                                            placeholder="Konfirmasi password">
                                        <button type="button" onclick="togglePassword('new_password_confirmation')"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                                            <svg id="new_password_confirmation-show" class="h-5 w-5" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg id="new_password_confirmation-hide" class="h-5 w-5 hidden"
                                                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Footer / Submit --}}
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end gap-3">
                            <button type="reset"
                                class="text-sm font-medium text-gray-600 hover:text-gray-900 px-4 py-2 transition-colors">
                                Reset
                            </button>
                            <button type="submit"
                                class="inline-flex items-center justify-center px-6 py-2.5 border border-transparent text-sm font-bold rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-all hover:shadow-md">
                                Simpan Perubahan
                            </button>
                        </div>
                    </div>
                </div>

            </div> {{-- End Grid --}}
        </form>
    </div>
@endsection


@section('scripts')
    {{-- JAVASCRIPT: TOGGLE VISIBILITY --}}
    <script>
        // Toggle Password Visibility
        function togglePassword(fieldId) {
            const input = document.getElementById(fieldId);
            const iconShow = document.getElementById(fieldId + '-show');
            const iconHide = document.getElementById(fieldId + '-hide');

            if (input.type === 'password') {
                input.type = 'text';
                iconShow.classList.add('hidden');
                iconHide.classList.remove('hidden');
            } else {
                input.type = 'password';
                iconShow.classList.remove('hidden');
                iconHide.classList.add('hidden');
            }
        }

        // Preview Image when File Selected
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
