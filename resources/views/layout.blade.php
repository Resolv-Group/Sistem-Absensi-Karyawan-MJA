<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mitra Jua Abadi</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Alpine Js --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    {{-- Sweet Alert 2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    <x-navbar />
    <x-notification />

    <div class="px-16">
        @yield('header')
        @yield('content')
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @yield('scripts')

    <!-- Loading Screen -->
    <div id="loading-screen" class="fixed inset-0 bg-gray-100 flex items-center justify-center z-50 hidden">
        <div class="text-center">
            <div class="loader ease-linear rounded-full border-4 border-t-4 border-gray-200 h-12 w-12 mb-4 animate-spin"></div>
            <p class="text-sm text-gray-500">Loading...</p>
        </div>
    </div>

    <style>
        .loader {
            border-top-color: #3498db;
        }
    </style>

    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>



    <script>
        const loadingScreen = document.getElementById('loading-screen');

        // Handle first load (not from manual navigation)
        if (!sessionStorage.getItem('manualNavigation')) {
            window.addEventListener('DOMContentLoaded', () => {
                loadingScreen.classList.remove('hidden');
                setTimeout(() => {
                    loadingScreen.classList.add('hidden');
                }, 600);
            });
        } else {
            sessionStorage.removeItem('manualNavigation');
        }

        //  Handle manual navigation (link clicks)
        document.querySelectorAll('a[href]').forEach(link => {
            link.addEventListener('click', function (e) {
                const target = e.currentTarget.getAttribute('target');
                const href = e.currentTarget.getAttribute('href');

                if (
                    href.startsWith('#') ||
                    href.startsWith('javascript:') ||
                    target === '_blank'
                ) return;

                e.preventDefault();
                loadingScreen.classList.remove('hidden');

                // Mark that we're navigating manually
                sessionStorage.setItem('manualNavigation', 'true');

                setTimeout(() => {
                    window.location.href = href;
                }, 800);
            });
        });

        // Hide loader when coming back via browser back/forward button
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                // Page loaded from bfcache (back/forward)
                loadingScreen.classList.add('hidden');
            }
        });
    </script>

</body>
</html>
