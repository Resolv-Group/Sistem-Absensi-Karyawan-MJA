<nav class="w-full bg-white shadow-sm border-b px-8 py-3 flex items-center justify-between sticky top-0 z-50">

    {{-- Left: Logo --}}
    <a href="{{route('view.dashboard')}}" class="flex items-center gap-3">
        <img
            src="{{ asset('images/mja-logo.png') }}"
            alt="MJA Logo"
            class="w-8 h-8 object-contain"
        >
    </a>

    {{-- Middle: Menu --}}
    <ul class="flex items-center gap-8 text-sm font-medium">
    <li class="relative pb-2 cursor-pointer">
        <a href="{{route('view.dashboard')}}"
            class="text-gray-700 hover:text-black {{ Request::is('dashboard') ? 'border-b-2 border-red-500 pb-2 text-black' : '' }}">
            Dashboard
        </a>
    </li>

    @if(in_array(Auth::user()->role, ['admin','hrd','pic']))
    <li class="relative pb-2 cursor-pointer">
        <a href="{{ route('view.pekerja') }}"
        class="text-gray-700 hover:text-black {{ Request::is('daftar-pekerja') || Request::is('pekerja*') ? 'border-b-2 border-red-500 pb-2 text-black' : '' }}">
            Pekerja
        </a>
    </li>
    @endif


    @if(in_array(Auth::user()->role, ['admin','hrd','akuntan']))
    <li class="relative pb-2 cursor-pointer">
        <a href="{{ route('view.staff') }}"
        class="text-gray-700 hover:text-black {{ Request::is('daftar-staff') || Request::is('staff*') ? 'border-b-2 border-red-500 pb-2 text-black' : '' }}">
            Staff
        </a>
    </li>
    @endif



    <li class="relative pb-2 cursor-pointer">
        <a href="/mitra-kerja"
            class="text-gray-700 hover:text-black {{ (Request::is('mitra-kerja') || Request::is('mitra-kerja') || Request::is('mitra-kerja/*')) ? 'border-b-2 border-red-500 pb-2 text-black' : '' }}">
            Mitra Kerja
        </a>
    </li>

    <li class="relative pb-2 cursor-pointer">
        <a href="/unit"
            class="text-gray-700 hover:text-black {{ (Request::is('unit') || Request::is('unit') || Request::is('unit/*'))  ? 'border-b-2 border-red-500 pb-2 text-black' : '' }}">
            Unit
        </a>
    </li>

    <li class="relative pb-2 cursor-pointer">
        <a href="/absensi"
            class="text-gray-700 hover:text-black {{ (Request::is('absensi') || Request::is('absensi') || Request::is('absensi/*')) ? 'border-b-2 border-red-500 pb-2 text-black' : '' }}">
            Absensi
        </a>
    </li>
</ul>


    {{-- <li class="relative pb-2 cursor-pointer flex items-center gap-1">
            <span>Time Management</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </li> --}}

    {{-- Right: Profile Icon --}}
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-700 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5.121 17.804A4 4 0 0112 15a4 4 0 016.879 2.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    </div>
</nav>
