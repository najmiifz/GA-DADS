@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-lg mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('reimburse-requests.show', $reimburseRequest) }}" class="text-gray-600 hover:text-gray-800 mr-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Edit Pengajuan Service Motor</h1>
        </div>

        @if($reimburseRequest->status === 'rejected')
            <div class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                <p class="font-medium">Pengajuan ini sebelumnya ditolak.</p>
                <p class="text-sm">Setelah Anda edit dan simpan, status akan kembali menjadi "Pending" untuk ditinjau ulang oleh admin.</p>
                @if($reimburseRequest->catatan_admin)
                    <p class="text-sm mt-2"><strong>Catatan Admin:</strong> {{ $reimburseRequest->catatan_admin }}</p>
                @endif
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('reimburse-requests.update-user', $reimburseRequest) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Ketentuan NOTA -->
            <div class="mb-6 bg-yellow-50 border-l-4 border-yellow-500 p-4">
                <h2 class="font-semibold text-gray-800 mb-2">Ketentuan NOTA</h2>
                <ol class="list-decimal list-inside text-sm text-gray-700 space-y-1">
                    <li>Nota harus di scan secara keseluruhan (jangan terpisah-pisah), jelas terbaca dan dalam bentuk (.pdf)</li>
                    <li>Angka yang tertera di nota harus sama persis dengan angka yang dicantumkan di dalam form ini</li>
                </ol>
            </div>

            <div class="mb-4">
                <label for="asset_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Aset Motor <span class="text-red-500">*</span></label>
                <select name="asset_id" id="asset_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- pilih aset motor --</option>
                    @foreach($assets as $a)
                        <option value="{{ $a->id }}" {{ old('asset_id', $reimburseRequest->asset_id) == $a->id ? 'selected' : '' }}>
                            {{ $a->merk }} - {{ ucwords($a->jenis_aset) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="biaya" class="block text-sm font-medium text-gray-700 mb-1">Biaya Service <span class="text-red-500">*</span></label>
                <input type="number" name="biaya" id="biaya" value="{{ old('biaya', $reimburseRequest->biaya) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <div class="mb-4">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan <span class="text-red-500">*</span></label>
                <textarea name="keterangan" id="keterangan" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          required>{{ old('keterangan', $reimburseRequest->keterangan) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="tanggal_service" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Service <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_service" id="tanggal_service"
                       value="{{ old('tanggal_service', $reimburseRequest->tanggal_service ? $reimburseRequest->tanggal_service->format('Y-m-d') : '') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <!-- Current Files Display -->
            @if($reimburseRequest->bukti_struk)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Struk Saat Ini</label>
                    @php
                        $files = json_decode($reimburseRequest->bukti_struk, true) ?: [$reimburseRequest->bukti_struk];
                    @endphp
                    <div class="grid grid-cols-2 gap-2">
                        @foreach($files as $file)
                            @if(pathinfo($file, PATHINFO_EXTENSION) === 'pdf')
                                <div class="p-2 bg-gray-100 rounded flex items-center">
                                    <i class="fas fa-file-pdf text-red-500 mr-2"></i>
                                    <a href="{{ Storage::url($file) }}" target="_blank" class="text-sm text-blue-600 hover:underline">
                                        PDF File
                                    </a>
                                </div>
                            @else
                                <img src="{{ Storage::url($file) }}" alt="Bukti" class="w-full h-20 object-cover rounded">
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="mb-6">
                <label for="bukti_struk" class="block text-sm font-medium text-gray-700 mb-1">Bukti Struk Baru (opsional - akan mengganti yang lama)</label>
                <div x-data="{ files: [] }">
                    <input type="file" name="bukti_struk[]" id="bukti_struk" accept="image/*,application/pdf" multiple
                           class="sr-only" @change="files = Array.from($event.target.files).slice(0,3)">

                    <label for="bukti_struk" class="flex items-center px-3 py-2 border border-gray-300 rounded-md cursor-pointer">
                        <i class="fas fa-paperclip mr-2"></i>
                        <span class="text-sm text-gray-700">Pilih file bukti baru (maks 3)</span>
                    </label>

                    <div class="mt-2 space-y-1" x-show="files.length > 0">
                        <template x-for="(f, i) in files" :key="i">
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                <span class="text-sm text-gray-600" x-text="f.name"></span>
                                <button type="button" @click="files.splice(i,1); $refs.bukti_struk.value = null" class="text-red-500">Hapus</button>
                            </div>
                        </template>
                    </div>

                    <p class="mt-1 text-xs text-gray-500">Maksimal 3 file, masing-masing 2MB. JPG/PNG/PDF.</p>
                </div>
            </div>

            <!-- Current Service Photos Display -->
            @if($reimburseRequest->foto_bukti_service)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Bukti Service Saat Ini</label>
                    @php
                        $photos = json_decode($reimburseRequest->foto_bukti_service, true) ?: [$reimburseRequest->foto_bukti_service];
                    @endphp
                    <div class="grid grid-cols-3 gap-2">
                        @foreach($photos as $photo)
                            <img src="{{ Storage::url($photo) }}" alt="Bukti Service" class="w-full h-20 object-cover rounded">
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Foto Bukti Service (separate from struk) -->
            <div class="mb-6" x-data="{ proofFiles: [] }">
                <label for="foto_bukti_service" class="block text-sm font-medium text-gray-700 mb-1">Foto Bukti Service Baru (opsional - akan mengganti yang lama)</label>
                <input type="file" name="foto_bukti_service[]" id="foto_bukti_service" accept="image/*" multiple class="sr-only"
                       @change="proofFiles = Array.from($event.target.files).slice(0,3)">

                <label for="foto_bukti_service" class="flex items-center px-3 py-2 border border-gray-300 rounded-md cursor-pointer">
                    <i class="fas fa-camera mr-2"></i>
                    <span class="text-sm text-gray-700">Pilih foto bukti service baru</span>
                </label>

                <div class="mt-2 space-y-1" x-show="proofFiles.length > 0">
                    <template x-for="(f, i) in proofFiles" :key="i">
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                            <span class="text-sm text-gray-600" x-text="f.name"></span>
                            <button type="button" @click="proofFiles.splice(i,1); $refs.foto_bukti_service.value = null" class="text-red-500">Hapus</button>
                        </div>
                    </template>
                </div>
                <p class="mt-1 text-xs text-gray-500">JPG/PNG, maksimal 5MB per file (maks 3 file).</p>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('reimburse-requests.show', $reimburseRequest) }}"
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition duration-200">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>

                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-save mr-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
