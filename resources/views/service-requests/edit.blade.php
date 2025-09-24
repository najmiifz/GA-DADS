@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('service-requests.show', $serviceRequest) }}" class="text-blue-600 hover:text-blue-800 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Edit Pengajuan Service</h1>
                <p class="text-gray-600">{{ $serviceRequest->nomor_pengajuan }}</p>
            </div>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if(isset($errors) && $errors instanceof \Illuminate\Support\ViewErrorBag && $errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6">
            <form action="{{ route('service-requests.update', $serviceRequest) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Asset Selection -->
                <div class="mb-6">
                    <label for="asset_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Kendaraan <span class="text-red-500">*</span>
                    </label>
                    <select name="asset_id" id="asset_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}"
                                    {{ (old('asset_id', $serviceRequest->asset_id) == $asset->id) ? 'selected' : '' }}>
                                {{ $asset->nama_barang }} - {{ $asset->nomor_polisi }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- KM Saat Ini -->
                <div class="mb-6">
                    <label for="km_saat_ini" class="block text-sm font-medium text-gray-700 mb-2">
                        KM Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="km_saat_ini" id="km_saat_ini"
                           value="{{ old('km_saat_ini', $serviceRequest->km_saat_ini) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Contoh: 50000"
                           required>
                </div>

                <!-- Lokasi Project -->
                <div class="mb-6">
                    <label for="lokasi_project" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Project <span class="text-red-500">*</span></label>
                    <input type="text" name="lokasi_project" id="lokasi_project"
                           value="{{ old('lokasi_project', $serviceRequest->lokasi_project) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Contoh: Proyek ABC - Site 1" required>
                </div>

                <!-- Keluhan -->
                <div class="mb-6">
                    <label for="keluhan" class="block text-sm font-medium text-gray-700 mb-2">
                        Keluhan / Masalah <span class="text-red-500">*</span>
                    </label>
                    <textarea name="keluhan" id="keluhan" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Jelaskan masalah atau keluhan yang dialami kendaraan..."
                              required>{{ old('keluhan', $serviceRequest->keluhan) }}</textarea>
                </div>

                <!-- Estimasi Harga -->
                <div x-data="{
                    display: '{{ old('estimasi_harga', $serviceRequest->estimasi_harga) }}',
                    init() {
                        this.display = this.format(this.display);
                    },
                    format(value) {
                        const num = value.toString().replace(/\D/g, '');
                        return num ? new Intl.NumberFormat('id-ID').format(num) : '';
                    },
                    parse(value) {
                        return value.replace(/\D/g, '');
                    }
                }" x-init="init()" class="mb-6">
                    <label for="estimasi_display" class="block text-sm font-medium text-gray-700 mb-2">
                        Estimasi Harga Service <span class="text-red-500">*</span>
                    </label>
                    <input type="text" x-model="display" id="estimasi_display" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Contoh: Rp 500.000"
                           @input="display = format(display)">
                    <input type="hidden" name="estimasi_harga" :value="parse(display)" id="estimasi_harga">
                    <p class="mt-1 text-xs text-gray-500">
                        Masukkan estimasi biaya service jika sudah ada perkiraan dari bengkel.
                    </p>
                </div>

                <!-- Existing Foto Estimasi -->
                @if($serviceRequest->foto_estimasi)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto Estimasi Saat Ini (Existing)</label>
                        <div class="grid grid-cols-2 gap-4 mb-4" x-data="{ existingEstimatePhotos: {{ json_encode($serviceRequest->foto_estimasi) }} }">
                            <template x-for="(filename, index) in existingEstimatePhotos" :key="index">
                                <div class="relative">
                                    <img :src="`/storage/service-requests/estimates/${filename}`" alt="Foto Estimasi" class="w-full h-32 object-cover rounded-lg">
                                    <button type="button"
                                            @click="deleteExistingEstimatePhoto(filename, index)"
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                @endif

                <!-- Existing Foto KM -->
                @if($serviceRequest->foto_km)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Foto KM Saat Ini (Existing)</label>
                        <div class="grid grid-cols-2 gap-4 mb-4" x-data="{ existingPhotos: {{ json_encode($serviceRequest->foto_km) }} }">
                            <template x-for="(filename, index) in existingPhotos" :key="index">
                                <div class="relative">
                                    <img :src="`/storage/service-requests/km/${filename}`" alt="Foto KM" class="w-full h-32 object-cover rounded-lg">
                                    <button type="button"
                                            @click="deleteExistingPhoto(filename, index)"
                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                @endif

                <!-- Add New Foto Estimasi -->
                <div class="mb-6">
                    <label for="foto_estimasi" class="block text-sm font-medium text-gray-700 mb-2">
                        Tambah Foto Estimasi Baru (Opsional)
                    </label>
                    <div x-data="{
                        estimateFiles: [],
                        isDragOverEstimate: false,
                        handleEstimateFiles(fileList) {
                            this.estimateFiles = Array.from(fileList);
                        },
                        removeEstimateFile(index) {
                            this.estimateFiles.splice(index, 1);
                        }
                    }" class="space-y-4">

                        <!-- File Input -->
                        <div class="relative">
                            <input type="file"
                                   name="foto_estimasi[]"
                                   id="foto_estimasi"
                                   multiple
                                   accept="image/*"
                                   class="sr-only"
                                   @change="handleEstimateFiles($event.target.files)">

                            <label for="foto_estimasi"
                                   class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition duration-200"
                                   :class="{ 'border-green-400 bg-green-50': isDragOverEstimate }"
                                   @dragover.prevent="isDragOverEstimate = true"
                                   @dragleave.prevent="isDragOverEstimate = false"
                                   @drop.prevent="isDragOverEstimate = false; handleEstimateFiles($event.dataTransfer.files)">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <i class="fas fa-receipt text-3xl text-gray-400 mb-2"></i>
                                            <p class="mb-2 text-sm text-gray-500">
                                                <span class="font-semibold">Klik untuk upload</span> struk estimasi
                                            </p>
                                            <p class="text-xs text-gray-500">PNG, JPG atau JPEG (MAX. 2MB per file). Saat edit, foto estimasi baru bersifat opsional.</p>
                                        </div>
                            </label>
                        </div>

                        <!-- Preview Files -->
                        <div x-show="estimateFiles.length > 0" class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-700">File estimasi baru yang dipilih:</h4>
                            <template x-for="(file, index) in estimateFiles" :key="index">
                                <div class="flex items-center justify-between p-2 bg-green-50 rounded">
                                    <span class="text-sm text-gray-600" x-text="file.name"></span>
                                    <button type="button" @click="removeEstimateFile(index)" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Add New Foto KM -->
                <div class="mb-6">
                    <label for="foto_km" class="block text-sm font-medium text-gray-700 mb-2">
                        Tambah Foto KM Baru (Opsional)
                    </label>
                    <div x-data="{
                        files: [],
                        isDragOver: false,
                        handleFiles(fileList) {
                            this.files = Array.from(fileList);
                        },
                        removeFile(index) {
                            this.files.splice(index, 1);
                        }
                    }" class="space-y-4">

                        <!-- File Input -->
                        <div class="relative">
                            <input type="file"
                                   name="foto_km[]"
                                   id="foto_km"
                                   multiple
                                   accept="image/*"
                                   class="sr-only"
                                   @change="handleFiles($event.target.files)">

                            <label for="foto_km"
                                   class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition duration-200"
                                   :class="{ 'border-blue-400 bg-blue-50': isDragOver }"
                                   @dragover.prevent="isDragOver = true"
                                   @dragleave.prevent="isDragOver = false"
                                   @drop.prevent="isDragOver = false; handleFiles($event.dataTransfer.files)">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                    <p class="mb-2 text-sm text-gray-500">
                                        <span class="font-semibold">Klik untuk upload</span> atau drag and drop
                                    </p>
                                    <p class="text-xs text-gray-500">PNG, JPG atau JPEG (MAX. 2MB per file)</p>
                                </div>
                            </label>
                        </div>

                        <!-- Preview Files -->
                        <div x-show="files.length > 0" class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-700">File baru yang dipilih:</h4>
                            <template x-for="(file, index) in files" :key="index">
                                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                                    <span class="text-sm text-gray-600" x-text="file.name"></span>
                                    <button type="button" @click="removeFile(index)" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('service-requests.show', $serviceRequest) }}"
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-200">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-save mr-2"></i>Update Pengajuan
                    </button>
                </div>
            </form>

            <!-- Delete Button -->
            @if($serviceRequest->isPending())
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <form action="{{ route('service-requests.destroy', $serviceRequest) }}" method="POST"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan service ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-trash mr-2"></i>Hapus Pengajuan
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function deleteExistingPhoto(filename, index) {
    if (confirm('Apakah Anda yakin ingin menghapus foto ini?')) {
        fetch(`{{ route('service-requests.delete-foto-km', $serviceRequest) }}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ filename: filename })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove from Alpine data
                this.existingPhotos.splice(index, 1);
            } else {
                alert('Gagal menghapus foto: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus foto');
        });
    }
}

function deleteExistingEstimatePhoto(filename, index) {
    if (confirm('Apakah Anda yakin ingin menghapus foto estimasi ini?')) {
        fetch(`{{ route('service-requests.delete-foto-estimasi', $serviceRequest) }}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ filename: filename })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove from Alpine data
                this.existingEstimatePhotos.splice(index, 1);
            } else {
                alert('Gagal menghapus foto estimasi: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus foto estimasi');
        });
    }
}
</script>
@endsection
