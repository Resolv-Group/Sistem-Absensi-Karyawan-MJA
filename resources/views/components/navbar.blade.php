<nav class="w-full bg-white shadow-sm border-b px-8 py-3 flex items-center justify-between">

    {{-- Left: Logo --}}
    <div class="flex items-center gap-3">
        <img
            src="{{ asset('images/mja-logo.png') }}"
            alt="MJA Logo"
            class="w-8 h-8 object-contain"
        >
    </div>

    {{-- Middle: Menu --}}
    <ul class="flex items-center gap-8 text-sm font-medium">
        <li class="relative pb-2 cursor-pointer">
            <a href="#" class="text-gray-700 hover:text-black">Dashboard</a>
        </li>

        <li class="relative pb-2 cursor-pointer">
            <a href="#" class="text-gray-700 hover:text-black border-b-2 border-red-500 pb-2">Pekerja</a>
        </li>

        <li class="relative pb-2 cursor-pointer">
            <a href="#" class="text-gray-700 hover:text-black">Staff</a>
        </li>

        <li class="relative pb-2 cursor-pointer">
            <a href="#" class="text-gray-700 hover:text-black">Unit</a>
        </li>

        <li class="relative pb-2 cursor-pointer flex items-center gap-1">
            <span>Time Management</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </li>

        <li class="relative pb-2 cursor-pointer">
            <a href="#" class="text-gray-700 hover:text-black">Payroll</a>
        </li>
    </ul>

    {{-- Right: Profile Icon --}}
    <div>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-gray-700 cursor-pointer" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5.121 17.804A4 4 0 0112 15a4 4 0 016.879 2.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
    </div>
</nav>
