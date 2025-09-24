@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Create User</h2>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('users.store') }}" class="max-w-xl mx-auto p-6 bg-white border border-gray-200 rounded-lg shadow-sm space-y-6" enctype="multipart/form-data">
                        @csrf
                        @if ($errors->any())
                            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-800 rounded">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Name -->
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700">Name</label>
                            <input id="name" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" type="text" name="name" value="{{ old('name') }}" required autofocus />
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                            <input id="email" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" type="email" name="email" value="{{ old('email') }}" required />
                        </div>

                        <!-- Password -->
                        <div class="relative">
                            <label for="password" class="block font-medium text-sm text-gray-700">Password</label>
                            <input id="password" class="mt-1 w-full pr-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" type="password" name="password" required autocomplete="new-password" />
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400" onclick="togglePassword('password')">
                                <i id="password-eye" class="fas fa-eye"></i>
                            </button>
                        </div>

                        <!-- Confirm Password -->
                        <div class="relative">
                            <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Confirm Password</label>
                            <input id="password_confirmation" class="mt-1 w-full pr-10 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" type="password" name="password_confirmation" required />
                            <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400" onclick="togglePassword('password_confirmation')">
                                <i id="password_confirmation-eye" class="fas fa-eye"></i>
                            </button>
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role" class="block font-medium text-sm text-gray-700">Role</label>
                            <select name="role" id="role" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="user" selected>User/PIC</option>
                                <option value="admin">Admin</option>
                            </select>
                            <p class="mt-1 text-sm text-gray-500">Pilih role untuk user baru. User/PIC hanya dapat mengelola asset yang di-assign</p>
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label for="lokasi" class="block font-medium text-sm text-gray-700">Lokasi</label>
                            <input id="lokasi" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" type="text" name="lokasi" value="{{ old('lokasi') }}" placeholder="Contoh: Jakarta, Bandung, Surabaya" />
                        </div>

                        <!-- Project -->
                        <div>
                            <label for="project" class="block font-medium text-sm text-gray-700">Project</label>
                            <input id="project" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" type="text" name="project" value="{{ old('project') }}" placeholder="Project yang ditugaskan (opsional)" />
                        </div>

                        <!-- Jabatan -->
                        <div>
                            <label for="jabatan" class="block font-medium text-sm text-gray-700">Jabatan</label>
                            <input id="jabatan" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" type="text" name="jabatan" value="{{ old('jabatan') }}" placeholder="Contoh: Manager, Supervisor, Staff" />
                        </div>

                        <!-- NIK -->
                        <div>
                            <label for="nik" class="block font-medium text-sm text-gray-700">NIK</label>
                            <input id="nik" type="text" name="nik" value="{{ old('nik') }}" class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm" />
                        </div>
                        <!-- Avatar -->
                        <div>
                            <label for="avatar" class="block font-medium text-sm text-gray-700">Foto PIC</label>
                            <input id="avatar" type="file" name="avatar" accept="image/*" class="mt-1 w-full" />
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="inline-flex items-center px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 font-medium transition-colors">
                                Create
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
    </div>
@endsection
