<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GA-DADS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white shadow-md rounded-lg p-8 w-full max-w-md">
        <!-- Logo and Brand -->
        <div class="flex flex-col items-center mb-6">
            <img src="{{ asset('images/logo-dads.png') }}" alt="GA-DADS Logo" class="w-20 h-20 mb-2">
            <span class="text-2xl font-bold text-red-600">GA-DADS</span>
        </div>
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Login</h2>

        <!-- Form Login -->
       <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-gray-700">Email</label>
                <input type="email" name="email" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500" required>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="password" class="w-full p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 pr-10" required>
                    <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-gray-700">
                        <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg id="eye-slash-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L12 12m-2.122-2.122L7.76 7.76m0 0L5.64 5.64m0 0L12 12m7.878-7.878L12 12m0 0l2.122 2.122M7.76 7.76l2.122 2.122"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Tombol Login -->
            <button type="submit" class="w-full bg-red-600 text-white p-2 rounded-lg hover:bg-red-700">
                Login
            </button>
        </form>

        <!-- Pesan Error -->
        @if(isset($errors) && $errors instanceof \Illuminate\Support\ViewErrorBag && $errors->any())
                <div class="mt-4 p-2 bg-red-100 text-red-600 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
        @endif
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeSlashIcon = document.getElementById('eye-slash-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeSlashIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeSlashIcon.classList.add('hidden');
            }
        }
    </script>

</body>
</html>
