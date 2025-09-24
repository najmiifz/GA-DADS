@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-rose-50">
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-red-600 to-rose-600 rounded-xl shadow-xl p-6 text-white">
                <div class="flex items-center">
                    <div class="h-12 w-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-user-cog text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold">Kelola Akun</h1>
                        <p class="text-red-100 mt-1">Kelola informasi akun pribadi Anda</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-3"></i>
                <span class="text-green-800">{{ session('success') }}</span>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Profile Information -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-user text-red-600 mr-3"></i>
                        Informasi Profil
                    </h2>
                    <p class="text-gray-600 mt-1">Informasi akun Anda (tidak dapat diubah)</p>
                </div>
                    <div class="p-6">
                        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            @include('profile.partials.avatar-upload')
                            <div class="mt-4">
                                <button type="submit"
                                        class="bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl">
                                    <i class="fas fa-upload mr-2"></i> Simpan Foto Profil
                                </button>
                            </div>
                        </form>
                        <div class="space-y-4 mt-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-gray-600">
                                {{ $user->name }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-gray-600">
                                {{ $user->email }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @if($user->role === 'admin') bg-red-100 text-red-800
                                    @else bg-blue-100 text-blue-800 @endif">
                                    <i class="fas @if($user->role === 'admin') fa-crown @else fa-user @endif mr-2"></i>
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-yellow-600 mr-3 mt-0.5"></i>
                            <div class="text-yellow-800">
                                <p class="font-medium">Informasi Penting</p>
                                <p class="text-sm mt-1">Nama dan email hanya dapat diubah oleh administrator sistem.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center">
                        <i class="fas fa-lock text-red-600 mr-3"></i>
                        Ubah Password
                    </h2>
                    <p class="text-gray-600 mt-1">Pastikan akun Anda menggunakan password yang kuat</p>
                </div>
                <div class="p-6">
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password Saat Ini
                                </label>
                                <div class="relative">
                                    <input type="password"
                                           id="current_password"
                                           name="current_password"
                                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 @error('current_password') border-red-500 @enderror"
                                           placeholder="Masukkan password saat ini">
                                    <i class="fas fa-eye-slash absolute right-3 top-3.5 text-gray-400 cursor-pointer toggle-password" data-target="current_password"></i>
                                </div>
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password Baru
                                </label>
                                <div class="relative">
                                    <input type="password"
                                           id="password"
                                           name="password"
                                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 @error('password') border-red-500 @enderror"
                                           placeholder="Masukkan password baru">
                                    <i class="fas fa-eye-slash absolute right-3 top-3.5 text-gray-400 cursor-pointer toggle-password" data-target="password"></i>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Konfirmasi Password Baru
                                </label>
                                <div class="relative">
                                    <input type="password"
                                           id="password_confirmation"
                                           name="password_confirmation"
                                           class="block w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500"
                                           placeholder="Konfirmasi password baru">
                                    <i class="fas fa-eye-slash absolute right-3 top-3.5 text-gray-400 cursor-pointer toggle-password" data-target="password_confirmation"></i>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="submit"
                                    class="w-full bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-300 shadow-lg hover:shadow-xl">
                                <i class="fas fa-save mr-2"></i>
                                Ubah Password
                            </button>
                        </div>
                    </form>

                    <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <div class="flex items-start">
                            <i class="fas fa-shield-alt text-red-600 mr-3 mt-0.5"></i>
                            <div class="text-red-800">
                                <p class="font-medium">Tips Keamanan</p>
                                <ul class="text-sm mt-1 space-y-1">
                                    <li>• Gunakan minimal 8 karakter</li>
                                    <li>• Kombinasikan huruf besar, kecil, dan angka</li>
                                    <li>• Jangan gunakan password yang mudah ditebak</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            const target = document.getElementById(this.dataset.target);
            const type = target.getAttribute('type') === 'password' ? 'text' : 'password';
            target.setAttribute('type', type);

            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    });
});
</script>
@endsection
