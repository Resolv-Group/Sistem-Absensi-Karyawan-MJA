@if (session('success'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2500)" class="fixed top-6 left-1/2 -translate-x-1/2 z-50">
        <div x-show="show" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="bg-green-600 text-white px-5 py-3 rounded-lg shadow-lg text-sm font-semibold">
            ✔ {{ session('success') }}
        </div>
    </div>
@elseif (session('error'))
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" class="fixed top-6 left-1/2 -translate-x-1/2 z-50">

        <div x-show="show" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="bg-red-600 text-white px-5 py-3 rounded-lg shadow-lg text-sm font-semibold max-w-md text-center">

            ❌ {{ session('error') }}

        </div>
    </div>
@elseif ($errors->any())
    <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" class="fixed top-6 left-1/2 -translate-x-1/2 z-50">

        <div x-show="show" x-transition:enter="transition ease-out duration-500"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-500" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="bg-red-600 text-white px-5 py-3 rounded-lg shadow-lg text-sm font-semibold max-w-md text-center">

            ❌ {{ $errors->first() }}

        </div>
    </div>
@endif
