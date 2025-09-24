@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('service-requests.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Buat Pengajuan Service</h1>
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
            <form action="{{ route('service-requests.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Asset Selection -->
                <div class="mb-6">
                    <label for="asset_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih Aset Mobil <span class="text-red-500">*</span>
                    </label>
                    <select name="asset_id" id="asset_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- pilih aset mobil --</option>
                        @foreach($assets as $asset)
                            <option value="{{ $asset->id }}" {{ old('asset_id') == $asset->id ? 'selected' : '' }}>
                                {{ $asset->merk }} {{ $asset->tipe }} - {{ $asset->serial_number }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-sm text-gray-500 mt-1">Pilih kendaraan yang akan di-service</p>
                </div>

                <!-- KM Saat Ini -->
                <div class="mb-6">
                    <label for="km_saat_ini" class="block text-sm font-medium text-gray-700 mb-2">
                        KM Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="km_saat_ini" id="km_saat_ini"
                           value="{{ old('km_saat_ini') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Contoh: 50000"
                           required>
                </div>

                <!-- Lokasi Project -->
                <div class="mb-6">
                    <label for="lokasi_project" class="block text-sm font-medium text-gray-700 mb-2">Lokasi Project <span class="text-red-500">*</span></label>
                    <input type="text" name="lokasi_project" id="lokasi_project"
                           value="{{ old('lokasi_project') }}"
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
                              required>{{ old('keluhan') }}</textarea>
                </div>

                <!-- Estimasi Harga -->
                <div x-data="{
                    display: '{{ old('estimasi_harga') }}',
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

                <!-- Foto Estimasi -->
                <div class="mb-6">
                    <label for="foto_estimasi" class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Struk Estimasi <span class="text-red-500">*</span>
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
                                    <p class="text-xs text-gray-500">PNG, JPG atau JPEG (MAX. 2MB per file)</p>
                                </div>
                            </label>
                        </div>

                        <!-- Preview Files -->
                        <div x-show="estimateFiles.length > 0" class="space-y-2">
                            <h4 class="text-sm font-medium text-gray-700">Foto estimasi yang dipilih:</h4>
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
                    <p class="mt-2 text-xs text-gray-500">
                        Upload minimal 1 foto struk estimasi dari bengkel. Maksimal 3 foto. PNG, JPG atau JPEG (MAX. 2MB per file)
                    </p>
                </div>

                <!-- Foto KM -->
                <div class="mb-6">
                    <label for="foto_km" class="block text-sm font-medium text-gray-700 mb-2">
                        Foto KM Saat Ini <span class="text-red-500">*</span>
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
                                   @change="handleFiles($event.target.files)"
                                   required>

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
                            <h4 class="text-sm font-medium text-gray-700">File yang dipilih:</h4>
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
                    <p class="mt-2 text-xs text-gray-500">
                        Upload foto odometer/speedometer yang menunjukkan KM saat ini. Minimal 1 foto, maksimal 5 foto.
                    </p>
                </div>

                <!-- Tanggal Service -->
                <div class="mb-6">
                    <label for="tanggal_servis" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Service <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="tanggal_servis" id="tanggal_servis"
                           value="{{ old('tanggal_servis') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           required>
                </div>

                <!-- Foto Bukti Service -->
                <div class="mb-6" x-data="{
                    proofFiles: [],
                    isDragOverProof: false,
                    handleProofFiles(fileList) {
                        this.proofFiles = Array.from(fileList).slice(0, 3);
                    },
                    removeProofFile(index) {
                        this.proofFiles.splice(index, 1);
                    }
                }">
                    <label for="foto_bukti_service" class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Bukti Service (Opsional)
                    </label>
                    <div class="relative">
                        <input type="file"
                               name="foto_bukti_service[]"
                               id="foto_bukti_service"
                               multiple
                               accept="image/*"
                               class="sr-only"
                               @change="handleProofFiles($event.target.files)">

                        <label for="foto_bukti_service"
                               class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition duration-200"
                               :class="{ 'border-purple-400 bg-purple-50': isDragOverProof }"
                               @dragover.prevent="isDragOverProof = true"
                               @dragleave.prevent="isDragOverProof = false"
                               @drop.prevent="isDragOverProof = false; handleProofFiles($event.dataTransfer.files)">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-camera text-3xl text-gray-400 mb-2"></i>
                                <p class="mb-2 text-sm text-gray-500">
                                    <span class="font-semibold">Klik untuk upload</span> atau drag and drop
                                </p>
                                <p class="text-xs text-gray-500">PNG, JPG atau JPEG (MAX. 2MB per file, max 3 files)</p>
                            </div>
                        </label>
                    </div>
                    <div x-show="proofFiles.length > 0" class="space-y-2 mt-2">
                        <h4 class="text-sm font-medium text-gray-700">Foto bukti service yang dipilih:</h4>
                        <template x-for="(file, index) in proofFiles" :key="index">
                            <div class="flex items-center justify-between p-2 bg-purple-50 rounded">
                                <span class="text-sm text-gray-600" x-text="file.name"></span>
                                <button type="button" @click="removeProofFile(index)" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">
                        Upload foto bukti service setelah selesai. Maksimal 3 foto.
                    </p>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('service-requests.index') }}"
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition duration-200">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200">
                        <i class="fas fa-save mr-2"></i>Kirim Pengajuan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
