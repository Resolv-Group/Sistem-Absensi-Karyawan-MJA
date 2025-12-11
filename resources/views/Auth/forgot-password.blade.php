<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Mitra Jua Abadi</title>

    <!-- Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="bg-white shadow-lg rounded-xl p-10 w-full max-w-md">

        <h2 class="text-2xl font-bold text-center text-gray-700 mb-6">Lupa Password</h2>
        <p class="text-sm text-gray-500 text-center mb-6">
            Masukkan email Anda. Kami akan mengirimkan link untuk reset password.
        </p>

        {{-- Success Message --}}
        @if (session('status'))
            <div class="bg-green-100 text-green-700 px-4 py-3 rounded-lg mb-4">
                {{ session('status') }}
            </div>
        @endif

        {{-- Error Message --}}
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded-lg mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-gray-700 mb-1 font-medium text-sm">Email</label>
                <input 
                    type="email" 
                    name="email"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-blue-400 focus:border-blue-400"
                    placeholder="nama@email.com"
                    required
                >
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition">
                Kirim Link Reset Password
            </button>

        </form>

        <div class="text-center mt-6">
            <a href="{{ route('login') }}" class="text-blue-600 text-sm hover:underline">
                Kembali ke Login
            </a>
        </div>

    </div>

</body>

</html>
