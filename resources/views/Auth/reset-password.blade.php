<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Mitra Jua Abadi</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-lg rounded-lg p-8 w-full max-w-md">

        <h2 class="text-2xl font-semibold text-center mb-6">Reset Password</h2>

        {{-- ERROR MESSAGE --}}
        @if ($errors->any())
            <div class="mb-4 bg-red-100 text-red-700 px-4 py-3 rounded">
                <p class="font-semibold mb-1">Terjadi kesalahan:</p>
                <ul class="list-disc ml-5 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ str_replace('The', '', str_replace('.', '', $error)) }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            {{-- Reset token --}}
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email --}}
            <label class="block mb-2 font-medium">Email</label>
            <input type="email" name="email" value="{{ request()->email }}"
                readonly
                class="w-full border p-2 rounded mb-4 bg-gray-100 cursor-not-allowed">

            {{-- Password baru --}}
            <label class="block mb-2 font-medium">Password Baru</label>
            <div class="relative mb-4">
                <input type="password" id="password" name="password" class="w-full border p-2 rounded pr-10" required>
                <button type="button"
                        onclick="togglePassword('password', 'toggleIcon1')"
                        class="absolute right-3 top-3 text-gray-500">
                    <svg id="toggleIcon1" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 9.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                    </svg>
                </button>
            </div>

            {{-- Konfirmasi password --}}
            <label class="block mb-2 font-medium">Konfirmasi Password</label>
            <div class="relative mb-6">
                <input type="password" id="password_confirmation" name="password_confirmation"
                       class="w-full border p-2 rounded pr-10" required>
                <button type="button"
                        onclick="togglePassword('password_confirmation', 'toggleIcon2')"
                        class="absolute right-3 top-3 text-gray-500">
                    <svg id="toggleIcon2" xmlns="http://www.w3.org/2000/svg" fill="none"
                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 9.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M2.25 12s3.75-7.5 9.75-7.5 9.75 7.5 9.75 7.5-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                    </svg>
                </button>
            </div>

            {{-- Tombol --}}
            <button class="w-full bg-blue-600 hover:bg-blue-700 text-white p-3 rounded font-medium">
                Reset Password
            </button>
        </form>
    </div>

    {{-- SHOW / HIDE PASSWORD SCRIPT --}}
    <script>
        function togglePassword(fieldId, iconId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(iconId);

            if (field.type === "password") {
                field.type = "text";
                icon.classList.add("text-blue-600");
            } else {
                field.type = "password";
                icon.classList.remove("text-blue-600");
            }
        }
    </script>

</body>
</html>
