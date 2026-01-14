<nav
    class="w-full bg-white/80 backdrop-blur-md border-b border-gray-100 px-8 py-2.5 flex items-center justify-between sticky top-0 z-50">

    {{-- Left: Logo & Branding --}}
    <a href="{{ route('view.dashboard') }}" class="flex items-center gap-3.5 group">
        <div class="relative">
            <div
                class="absolute inset-0 bg-red-200 blur-lg opacity-0 group-hover:opacity-40 transition-opacity duration-500">
            </div>
            <div
                class="relative p-2 bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-sm shadow-red-200 group-hover:scale-105 group-hover:rotate-3 transition-all duration-500">
                <img src="{{ asset('images/mja-logo.png') }}" alt="MJA Logo" class="w-8 h-8 object-contain rounded-md">
            </div>
        </div>
        <div class="flex flex-col leading-none">
            <span class="font-black text-gray-900 tracking-tighter text-sm lg:text-base uppercase">Mitra Jua
                Abadi</span>
            <span class="text-[10px] font-bold text-red-500 tracking-[0.2em] uppercase mt-0.5">Sistem Terpadu</span>
        </div>
    </a>

    {{-- Middle: Menu --}}
    <ul class="hidden md:flex items-center gap-1.5 bg-gray-50/50 p-1 rounded-2xl border border-gray-100/50">
        {{-- DASHBOARD --}}
        <li>
            <a href="{{ route('view.dashboard') }}"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all duration-300
                {{ request()->routeIs('view.dashboard') ? 'bg-white text-red-600 shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-900 hover:bg-white/50' }}">
                <svg class="w-4 h-4 {{ request()->routeIs('view.dashboard') ? 'text-red-500' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span>Dashboard</span>
            </a>
        </li>

    {{-- Role Based Menus --}}
    @foreach ([
        ['role' => ['admin', 'hrd'], 'route' => 'view.pekerja', 'label' => 'Pekerja', 'pattern' => 'view.pekerja*', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
        ['role' => ['admin', 'hrd', 'akuntan'], 'url' => '/payroll', 'label' => 'Payroll', 'pattern' => 'payroll*', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.407 2.646 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.407-2.646-1M12 16a6.002 6.002 0 006-4m-12 0a6.002 6.002 0 006 4m6-4V7a1 1 0 00-1-1H7a1 1 0 00-1 1v10a1 1 0 001 1h10a1 1 0 001-1v-4'],
        ['role' => ['admin', 'hrd', 'akuntan'], 'route' => 'view.staff', 'label' => 'Staff', 'pattern' => 'view.staff*', 'icon' => 'M21 13.255A2.396 2.396 0 0019.5 13H17c-1.105 0-2 .895-2 2s.895 2 2 2h2.5c.39 0 .753-.105 1.055-.255A5.002 5.002 0 1121 13.255zM11 13.255A2.396 2.396 0 009.5 13H7c-1.105 0-2 .895-2 2s.895 2 2 2h2.5c.39 0 .753-.105 1.055-.255A5.002 5.002 0 1111 13.255z'],
        ['role' => ['admin', 'hrd'], 'url' => '/mitra-kerja', 'label' => 'Mitra', 'pattern' => 'mitra-kerja*', 'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4'],
    ] as $item)
        @php
            // Logic to check active state: works for both Route names and URL patterns
            $isActive = isset($item['route']) ? request()->routeIs($item['pattern']) : Request::is($item['pattern']);
        @endphp

        @if (in_array(Auth::user()->role, $item['role']))
            <li>
                <a href="{{ isset($item['route']) ? route($item['route']) : $item['url'] }}"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all duration-300
                    {{ $isActive ? 'bg-white text-red-600 shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-900 hover:bg-white/50' }}">
                    <svg class="w-4 h-4 {{ $isActive ? 'text-red-500' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
                    </svg>
                    <span>{{ $item['label'] }}</span>
                </a>
            </li>
        @endif
    @endforeach

        {{-- Unit Logic --}}
        @if (in_array(Auth::user()->role, ['admin', 'hrd']))
            <li>
                <a href="/unit"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all duration-300
                    {{ Request::is('unit*') || request()->routeIs('view.detail.unit*') ? 'bg-white text-red-600 shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-900 hover:bg-white/50' }}">
                    <svg class="w-4 h-4 {{ Request::is('unit*') ? 'text-red-500' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Unit</span>
                </a>
            </li>
        @elseif(Auth::user()->role === 'pic' && Auth::user()->units->count())
            <li class="relative group">
                <button
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all duration-300
        {{ Request::is('unit*') ? 'bg-white text-red-600 shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-900 hover:bg-white/50' }}">
                    <svg class="w-4 h-4 {{ Request::is('unit*') ? 'text-red-500' : '' }}" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Unit ({{ Auth::user()->units->count() }})</span>
                    <svg class="w-3 h-3 opacity-40 transition-transform group-hover:rotate-180" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M19 9l-7 7-7-7" stroke-width="3" />
                    </svg>
                </button>

                {{-- Dropdown Menu --}}
                <div
                    class="absolute top-full left-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 p-1.5 transform origin-top translate-y-2 group-hover:translate-y-0">
                    @foreach (Auth::user()->units as $unit)
                        <a href="{{ route('view.detail.unit', $unit->id) }}"
                            class="group/item flex items-center gap-3 px-4 py-2.5 text-[11px] font-bold text-gray-600 hover:bg-red-50 hover:text-red-600 rounded-xl transition-all">

                            {{-- Small List Dot --}}
                            <div
                                class="w-1.5 h-1.5 rounded-full bg-gray-300 group-hover/item:bg-red-500 group-hover/item:scale-125 transition-all duration-300">
                            </div>

                            {{-- Unit Name --}}
                            <span class="truncate">{{ $unit->nama_unit }}</span>
                        </a>
                    @endforeach
                </div>
            </li>
        @endif

        {{-- ABSENSI --}}
        @if (in_array(Auth::user()->role, ['admin', 'pic']))
            <li>
                <a href="/absensi"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold transition-all duration-300
                    {{ Request::is('absensi*') ? 'bg-white text-red-600 shadow-sm ring-1 ring-black/5' : 'text-gray-500 hover:text-gray-900 hover:bg-white/50' }}">
                    <svg class="w-4 h-4 {{ Request::is('absensi*') ? 'text-red-500' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    <span>Absensi</span>
                </a>
            </li>
        @endif
    </ul>

    {{-- Right: Info & Profile --}}
    <div class="flex items-center gap-4">

        {{-- Smart Capsule Widget --}}
        <div x-data="smartCapsule()"
            class="hidden xl:flex items-center gap-3 pl-2 pr-4 py-1.5 bg-gray-50 border border-gray-100 rounded-2xl transition-all duration-500 hover:shadow-sm hover:bg-white group/capsule">
            <div
                class="w-8 h-8 rounded-xl bg-white shadow-sm flex items-center justify-center text-base border border-gray-100 group-hover/capsule:scale-110 transition-transform">
                <span x-text="greetingIcon"></span>
            </div>
            <div class="flex flex-col">
                <div class="flex items-center gap-1.5">
                    <span class="text-[13px] font-black text-gray-900 uppercase tracking-tight"
                        x-text="greetingText"></span>
                    <span class="w-1 h-1 bg-red-400 rounded-full animate-pulse"></span>
                </div>
                <div class="h-3 overflow-hidden">
                    <p class="text-[11px] font-bold text-gray-400 italic" x-text="currentQuote"></p>
                </div>
            </div>
        </div>

        <div class="h-6 w-px bg-gray-200"></div>

        {{-- Profile Block --}}
        <div class="relative group">
            <button class="flex items-center gap-3 py-1 focus:outline-none group/btn">
                <div class="hidden sm:block text-right">
                    <p
                        class="text-xs font-black text-gray-900 leading-none group-hover/btn:text-red-600 transition-colors">
                        {{ Auth::user()->name }}</p>
                    <span
                        class="inline-flex px-1.5 py-0.5 rounded-md bg-gray-100 text-[8px] font-black uppercase tracking-widest text-gray-500 border border-gray-200 group-hover/btn:bg-red-50 group-hover/btn:text-red-600 group-hover/btn:border-red-100 transition-all">
                        {{ Auth::user()->role }}
                    </span>
                </div>

                <div class="relative">
                    <div
                        class="h-10 w-10 rounded-2xl border-2 border-white ring-1 ring-gray-100 shadow-sm overflow-hidden group-hover/btn:ring-red-200 transition-all duration-300">
                        <img src="{{ Auth::user()->foto ? asset('storage/' . Auth::user()->foto) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=FEE2E2&color=EF4444&bold=true' }}"
                            class="w-full h-full object-cover">
                    </div>
                    <div
                        class="absolute -bottom-1 -right-1.5 w-4 h-4 bg-white rounded-full border border-gray-100 shadow-sm flex items-center justify-center group-hover/btn:rotate-180 transition-transform duration-500">
                        <svg class="w-2.5 h-2.5 text-gray-400 group-hover/btn:text-red-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </button>

            {{-- Dropdown --}}
            <div
                class="absolute right-0 top-full mt-3 w-56 bg-white rounded-2xl shadow-2xl border border-gray-100 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform origin-top-right translate-y-2 group-hover:translate-y-0 z-50">
                <div class="px-4 py-3 border-b border-gray-50 mb-1">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1.5">Akun
                        Saya</p>
                    <p class="text-xs font-black text-gray-900 truncate">{{ Auth::user()->email }}</p>
                </div>
                <a href="{{ route('view.profil', Auth::user()->id) }}"
                    class="flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-gray-600 hover:bg-red-50 hover:text-red-600 transition-colors">
                    <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profil Saya
                </a>
                <div class="border-t border-gray-50 my-1"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-4 py-2.5 text-xs font-black text-red-600 hover:bg-red-50 flex items-center gap-3">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3 3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    function smartCapsule() {
        return {
            greetingText: '',
            greetingIcon: '',
            currentQuote: '',
            quotes: [
                "Semangat kerjanya hari ini!",
                "Pastikan data sudah akurat.",
                "Detail adalah kunci sukses.",
                "Satu langkah kecil berarti besar.",
                "Cek kembali data sebelum simpan.",
                "You're doing great!"
            ],
            init() {
                this.updateGreeting();
                this.currentQuote = this.quotes[Math.floor(Math.random() * this.quotes.length)];
                setInterval(() => {
                    this.currentQuote = this.quotes[Math.floor(Math.random() * this.quotes.length)];
                }, 100000);
            },
            updateGreeting() {
                const hour = new Date().getHours();
                if (hour < 11) {
                    this.greetingText = 'Selamat Pagi';
                    this.greetingIcon = '☀️';
                } else if (hour < 15) {
                    this.greetingText = 'Selamat Siang';
                    this.greetingIcon = '🌤️';
                } else if (hour < 19) {
                    this.greetingText = 'Selamat Sore';
                    this.greetingIcon = '🌅';
                } else {
                    this.greetingText = 'Selamat Malam';
                    this.greetingIcon = '🌙';
                }
            }
        }
    }
</script>
