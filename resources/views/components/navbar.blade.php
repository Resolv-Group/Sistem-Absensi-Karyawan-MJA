<nav class="w-full bg-white shadow-sm border-b px-8 py-3 flex items-center justify-between sticky top-0 z-50">

    {{-- Left: Logo --}}
    <a href="{{ route('view.dashboard') }}" class="flex items-center gap-3">
        <img src="{{ asset('images/mja-logo.png') }}" alt="MJA Logo" class="w-8 h-8 object-contain">
    </a>

    {{-- Middle: Menu --}}
    <ul class="flex items-center gap-8 text-sm font-medium">
        <li class="relative pb-2 cursor-pointer">
            <a href="{{ route('view.dashboard') }}"
                class="text-gray-700 hover:text-black {{ Request::is('dashboard') ? 'border-b-2 border-red-500 pb-2 text-black' : '' }}">
                Dashboard
            </a>
        </li>

        @if (in_array(Auth::user()->role, ['admin', 'hrd', 'pic']))
            <li class="relative pb-2 cursor-pointer">
                <a href="{{ route('view.pekerja') }}"
                    class="text-gray-700 hover:text-black {{ Request::is('daftar-pekerja') || Request::is('pekerja*') ? 'border-b-2 border-red-500 pb-2 text-black' : '' }}">
                    Pekerja
                </a>
            </li>
        @endif


        @if (in_array(Auth::user()->role, ['admin', 'hrd', 'akuntan']))
            <li class="relative pb-2 cursor-pointer">
                <a href="{{ route('view.staff') }}"
                    class="text-gray-700 hover:text-black {{ Request::is('daftar-staff') || Request::is('staff*') ? 'border-b-2 border-red-500 pb-2 text-black' : '' }}">
                    Staff
                </a>
            </li>
        @endif



        <li class="relative pb-2 cursor-pointer">
            <a href="/mitra-kerja"
                class="text-gray-700 hover:text-black {{ Request::is('mitra-kerja') || Request::is('mitra-kerja') || Request::is('mitra-kerja/*') ? 'border-b-2 border-red-500 pb-2 text-black' : '' }}">
                Mitra Kerja
            </a>
        </li>

        <li class="relative pb-2 cursor-pointer">
            <a href="/unit"
                class="text-gray-700 hover:text-black {{ Request::is('unit') || Request::is('unit') || Request::is('unit/*') ? 'border-b-2 border-red-500 pb-2 text-black' : '' }}">
                Unit
            </a>
        </li>

        {{-- <li class="relative pb-2 cursor-pointer">
            <a href="/absensi"
                class="text-gray-700 hover:text-black {{ Request::is('absensi') || Request::is('absensi') || Request::is('absensi/*') ? 'border-b-2 border-red-500 pb-2 text-black' : '' }}">
                Absensi
            </a>
        </li> --}}

        @if(Auth::user()->role === 'pic' && Auth::user()->units->count())
        <li class="relative group">

            <button
                class="flex items-center gap-1 text-gray-700 hover:text-black pb-2
                {{ Request::is('absensi*') ? 'border-b-2 border-red-500 text-black' : '' }}">
                Absensi
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div class="absolute top-full left-0 mt-0 pt-2
                    w-48 bg-white rounded-xl shadow-lg border
                    opacity-0 invisible group-hover:opacity-100 group-hover:visible">


                @foreach(Auth::user()->units as $unit)
                    <a href="{{ route('view.detail.unit', $unit->id) }}"
                    class="block px-4 py-2 text-sm hover:bg-gray-50">
                        {{ $unit->nama_unit }}
                    </a>
                @endforeach

            </div>
        </li>
        @endif

    </ul>


    {{-- <li class="relative pb-2 cursor-pointer flex items-center gap-1">
            <span>Time Management</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </li> --}}

    {{-- Right: Profile Icon --}}
    {{-- Right: User Profile Dropdown --}}
    <div class="relative group">

        {{-- 1. THE TRIGGER (Name + Avatar) --}}
        <button class="flex items-center gap-3 focus:outline-none">
            {{-- Name (Hidden on small mobile, visible on desktop) --}}
            <div class="hidden md:block text-right">
                <div class="text-sm font-bold text-gray-800 leading-tight">
                    {{ Auth::user()->name ?? 'User' }}
                </div>
                <div class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">
                    {{ Auth::user()->role ?? 'Staff' }}
                </div>
            </div>

            {{-- Avatar --}}
            <div class="h-9 w-9 rounded-full border border-gray-200 overflow-hidden shadow-sm">
                {{-- Use Real Avatar if available, otherwise UI Avatars --}}
                @if(Auth::user()->foto)
                     {{-- Adjust path based on your storage --}}
                    <img src="{{ asset('storage/' . Auth::user()->foto) }}"
                         alt="Profile" class="w-full h-full object-cover">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=0D8ABC&color=fff&size=128"
                         alt="Profile" class="w-full h-full object-cover">
                @endif
            </div>

            {{-- Chevron Icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        {{-- 2. THE DROPDOWN MENU (Appears on Hover) --}}
        {{-- 'invisible group-hover:visible' handles the toggle without JavaScript --}}
        <div class="absolute right-0 top-full mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform z-50">

            {{-- Header (Mobile only) --}}
            <div class="md:hidden px-4 py-2 border-b border-gray-100 mb-1">
                <p class="text-sm font-bold text-gray-900">{{ Auth::user()->name }}</p>
                <p class="text-xs text-gray-500">{{ Auth::user()->role }}</p>
            </div>

            {{-- Menu Items --}}
            <a href="{{route('view.profil', Auth::user()->id)}}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                Profil Saya
            </a>
            {{-- <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-600">
                Pengaturan
            </a> --}}

            <div class="border-t border-gray-100 my-1"></div>

            {{-- Logout Button --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Keluar / Logout
                </button>
            </form>
        </div>
    </div>
</nav>
