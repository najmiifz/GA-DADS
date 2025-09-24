@extends('layouts.app')
@section('title', 'Edit PIC')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-900">Edit PIC</h1>
        <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('users.update', $user->id) }}" class="max-w-md mx-auto p-4 bg-white border border-gray-100 rounded-lg shadow space-y-4" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">Name</label>
                            <input id="name" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus />
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                            <input id="email" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" type="email" name="email" value="{{ old('email', $user->email) }}" required />
                        </div>

                        <!-- Password -->
                        <div class="relative">
                            <label for="password" class="block font-medium text-sm text-gray-700">Password (leave blank to keep current)</label>
                            <input id="password" class="mt-1 w-full pr-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" type="password" name="password" autocomplete="new-password" />
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400" onclick="togglePassword('password')">
                                <i id="password-eye" class="fas fa-eye"></i>
                            </button>
                        </div>

                        <!-- Confirm Password -->
                        <div class="relative">
                            <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirm Password</label>
                            <input id="password_confirmation" class="mt-1 w-full pr-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" type="password" name="password_confirmation" />
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400" onclick="togglePassword('password_confirmation')">
                                <i id="password_confirmation-eye" class="fas fa-eye"></i>
                            </button>
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role" class="block font-medium text-sm text-gray-700">Role</label>
                            <select name="role" id="role" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>User/PIC</option>
                                <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Admin dapat mengakses semua fitur, User/PIC hanya dapat mengelola asset yang di-assign</p>
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label for="lokasi" class="block font-medium text-sm text-gray-700">Lokasi</label>
                            <input id="lokasi" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" type="text" name="lokasi" value="{{ old('lokasi', $user->lokasi) }}" placeholder="Contoh: Jakarta, Bandung, Surabaya" />
                        </div>

                        <!-- Project -->
                        <div>
                            <label for="project" class="block font-medium text-sm text-gray-700">Project</label>
                            <input id="project" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" type="text" name="project" value="{{ old('project', $user->project) }}" placeholder="Project yang ditugaskan (opsional)" />
                        </div>

                        <!-- Jabatan -->
                        <div>
                            <label for="jabatan" class="block font-medium text-sm text-gray-700">Jabatan</label>
                            <input id="jabatan" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" type="text" name="jabatan" value="{{ old('jabatan', $user->jabatan) }}" placeholder="Contoh: Manager, Supervisor, Staff" />
                        </div>

                        <!-- NIK -->
                        <div>
                            <label for="nik" class="block font-medium text-sm text-gray-700">NIK</label>
                            <input id="nik" type="text" name="nik" value="{{ old('nik', $user->nik) }}" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" />
                        </div>
                        <!-- Avatar -->
                        <div>
                            <label for="avatar" class="block font-medium text-sm text-gray-700">Foto PIC</label>
                            <input id="avatar" type="file" name="avatar" accept="image/*" class="mt-1 w-full" />
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="Avatar" class="mt-2 w-20 h-20 rounded-full">
                            @endif
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium transition-colors">
                                Update
                            </button>
                        </div>
                        <script>
                        function togglePassword(fieldId) {
                            const field = document.getElementById(fieldId);
                            const eye = document.getElementById(fieldId + '-eye');
                            if (field.type === 'password') {
                                field.type = 'text';
                                eye.classList.remove('fa-eye');
                                eye.classList.add('fa-eye-slash');
                            } else {
                                field.type = 'password';
                                eye.classList.add('fa-eye');
                                eye.classList.remove('fa-eye-slash');
                            }
                        }
                        </script>
                        </form>
                </div>
            </div>
        </div>
</div>
@endsection
