<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MJA</title>

    {{-- Tailwind via CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- Font Poppins --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="flex items-center justify-center min-h-screen px-4 relative">

        {{-- Success Notification Floating Center --}}
        <x-notification />

        {{-- Card --}}
        <div class="bg-white w-full max-w-md rounded-xl shadow p-8">

            {{-- Logo --}}
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/mja-logo.png') }}" class="w-60" alt="MJA Logo">
            </div>

            {{-- Title --}}
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Sign in</h2>

            {{-- Form --}}
            <form method="POST" action="{{ route('login.process') }}">
                @csrf

                {{-- Email --}}
                <div class="mb-4">
                    <label class="text-sm text-gray-600">Email</label>
                    <input type="email" name="email"
                        class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring focus:ring-blue-200 focus:outline-none"
                        placeholder="Masukkan email">
                </div>

                {{-- Password --}}
                <div class="mb-4">
                    <label class="text-sm text-gray-600">Password</label>
                    <div class="relative mt-1">
                        <input type="password" name="password" id="passwordInput"
                            class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring focus:ring-black focus:outline-none"
                            placeholder="Masukkan password">

                        <!-- Eye Icon -->
                        <button type="button" onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-black hover:text-gray-700">
                            <!-- eye open -->
                            <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>

                            <!-- eye closed -->
                            <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-11-8-11-8a21.77 21.77 0 014.22-5.94" />
                                <path d="M1 1l22 22" />
                                <path d="M9.53 9.53a3.5 3.5 0 014.95 4.95" />
                            </svg>
                        </button>
                    </div>
                </div>


                {{-- Login Button --}}
                <button class="w-full py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition">
                    Sign in
                </button>

                {{-- OR --}}
                {{-- <div class="my-6 flex items-center">
                    <div class="flex-grow border-t border-gray-300"></div>
                    <span class="px-3 text-sm text-gray-400">atau</span>
                    <div class="flex-grow border-t border-gray-300"></div>
                </div> --}}

                {{-- Other options --}}
                {{-- <div class="space-y-3">

                    <button type="button"
                        class="w-full py-2 border border-gray-300 rounded-lg flex items-center justify-center gap-2 hover:bg-gray-50 transition">
                        <img src="https://www.svgrepo.com/show/475656/google-color.svg" class="w-5" alt="">
                        Sign in dengan Google
                    </button>

                    <button type="button"
                        class="w-full py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Sign in dengan ID Karyawan
                    </button>

                    <button type="button"
                        class="w-full py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                        Sign in dengan nomor telepon
                    </button>

                </div> --}}

            </form>

            {{-- Bottom Links --}}
            <div class="text-center mt-6">
                <a href="{{ route('password.request') }}" class="text-blue-600 text-sm hover:underline">Lupa password</a>
                {{-- <span class="mx-2 text-gray-300">•</span>
                <a href="#" class="text-blue-600 text-sm hover:underline">Buat akun demo</a> --}}
            </div>

        </div>
    </div>

    {{-- Footer --}}
    <div class="text-center text-xs text-gray-400 mt-4 pb-4">
        Kebijakan privasi • Ketentuan penggunaan • Tentang MJA Account
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>

    <script>
        function togglePassword() {
            const input = document.getElementById('passwordInput');
            const eyeOpen = document.getElementById('eyeOpen');
            const eyeClosed = document.getElementById('eyeClosed');

            if (input.type === "password") {
                input.type = "text";
                eyeOpen.classList.add("hidden");
                eyeClosed.classList.remove("hidden");
            } else {
                input.type = "password";
                eyeClosed.classList.add("hidden");
                eyeOpen.classList.remove("hidden");
            }
        }
    </script>
</body>

</html>
