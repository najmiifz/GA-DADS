@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-2xl font-semibold mb-6 text-gray-800">Edit Pengajuan APD</h1>

        @if($apdRequest->status === 'rejected')
            <div class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                <p class="font-medium">Pengajuan ini sebelumnya ditolak.</p>
                <p class="text-sm">Setelah Anda edit dan simpan, status akan kembali menjadi "Pending" untuk ditinjau ulang oleh admin.</p>
            </div>
        @endif

        <form action="{{ route('apd-requests.update-user', $apdRequest) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Team Mandor -->
                <div>
                    <label for="team_mandor" class="block text-sm font-medium text-gray-700 mb-2">Nama Tim Mandor <span class="text-red-500">*</span></label>
                    <input type="text" name="team_mandor" id="team_mandor"
                           value="{{ old('team_mandor', $apdRequest->team_mandor) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                    @error('team_mandor')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Cluster -->
                <div>
                    <label for="nama_cluster" class="block text-sm font-medium text-gray-700 mb-2">Nama Cluster <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_cluster" id="nama_cluster"
                           value="{{ old('nama_cluster', $apdRequest->nama_cluster) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                    @error('nama_cluster')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <h3 class="text-lg font-medium text-gray-800 mb-4">Jumlah APD yang Dibutuhkan</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Helm -->
                    <div>
                        <label for="helm" class="block text-sm font-medium text-gray-700 mb-2">Helm (pcs)</label>
                        <input type="number" name="helm" id="helm" min="0" max="100"
                               value="{{ old('helm', $apdRequest->helm) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('helm')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Rompi -->
                    <div>
                        <label for="rompi" class="block text-sm font-medium text-gray-700 mb-2">Rompi (pcs)</label>
                        <input type="number" name="rompi" id="rompi" min="0" max="100"
                               value="{{ old('rompi', $apdRequest->rompi) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('rompi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- AP Boots -->
                    <div>
                        <label for="apboots" class="block text-sm font-medium text-gray-700 mb-2">AP Boots (pcs)</label>
                        <input type="number" name="apboots" id="apboots" min="0" max="100"
                               value="{{ old('apboots', $apdRequest->apboots) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('apboots')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Body Harness -->
                    <div>
                        <label for="body_harness" class="block text-sm font-medium text-gray-700 mb-2">Body Harness (pcs)</label>
                        <input type="number" name="body_harness" id="body_harness" min="0" max="100"
                               value="{{ old('body_harness', $apdRequest->body_harness) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('body_harness')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sarung Tangan -->
                    <div>
                        <label for="sarung_tangan" class="block text-sm font-medium text-gray-700 mb-2">Sarung Tangan (pcs)</label>
                        <input type="number" name="sarung_tangan" id="sarung_tangan" min="0" max="100"
                               value="{{ old('sarung_tangan', $apdRequest->sarung_tangan) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               required>
                        @error('sarung_tangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-between">
                <a href="{{ route('apd-requests.show', $apdRequest) }}"
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>

                <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
