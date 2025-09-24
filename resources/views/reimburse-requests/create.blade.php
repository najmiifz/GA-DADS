@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-lg mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('reimburse-requests.index') }}" class="text-gray-600 hover:text-gray-800 mr-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Pengajuan Service Motor</h1>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                {{ session('success') }}
            </div>
        @endif
        @if(isset($errors) && $errors instanceof \Illuminate\Support\ViewErrorBag && $errors->any())
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('reimburse-requests.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
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
                        <option value="{{ $a->id }}">{{ $a->merk }} - {{ ucwords($a->jenis_aset) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-4">
                <label for="lokasi_project" class="block text-sm font-medium text-gray-700 mb-1">Lokasi Project <span class="text-red-500">*</span></label>
                <input type="text" name="lokasi_project" id="lokasi_project" value="{{ old('lokasi_project') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>
            <div class="mb-4">
                <label for="biaya" class="block text-sm font-medium text-gray-700 mb-1">Biaya Service <span class="text-red-500">*</span></label>
                <input type="text" name="biaya" id="biaya" value="{{ old('biaya') }}"
                       placeholder="Rp 0"
                       class="rupiah w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>
            <div class="mb-4">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan <span class="text-red-500">*</span></label>
                <textarea name="keterangan" id="keterangan" rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          required>{{ old('keterangan') }}</textarea>
            </div>
            <div class="mb-4">
                <label for="tanggal_service" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Service <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_service" id="tanggal_service" value="{{ old('tanggal_service') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>
            <div class="mb-6">
                <label for="bukti_struk" class="block text-sm font-medium text-gray-700 mb-1">Bukti Struk (jpg,jpeg,png,pdf) <span class="text-red-500">*</span></label>
                <div x-data="{ files: [] }">
                    <input type="file" name="bukti_struk[]" id="bukti_struk" accept="image/*,application/pdf" multiple
                           class="sr-only" required @change="files = Array.from($event.target.files).slice(0,3)">

                    <label for="bukti_struk" class="flex items-center px-3 py-2 border border-gray-300 rounded-md cursor-pointer">
                        <i class="fas fa-paperclip mr-2"></i>
                        <span class="text-sm text-gray-700">Pilih file bukti (maks 3)</span>
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

            <!-- Foto Bukti Service (separate from struk) -->
            <div class="mb-6" x-data="{ proofFiles: [] }">
                <label for="foto_bukti_service" class="block text-sm font-medium text-gray-700 mb-1">Foto Bukti Service (opsional, max 3)</label>
                <input type="file" name="foto_bukti_service[]" id="foto_bukti_service" accept="image/*" multiple class="sr-only"
                       @change="proofFiles = Array.from($event.target.files).slice(0,3)">

                <label for="foto_bukti_service" class="flex items-center px-3 py-2 border border-gray-300 rounded-md cursor-pointer">
                    <i class="fas fa-camera mr-2"></i>
                    <span class="text-sm text-gray-700">Pilih foto bukti service</span>
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


            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    Ajukan Service
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
