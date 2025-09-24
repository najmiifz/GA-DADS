@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-lg mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('assets.show', $asset) }}" class="text-gray-600 hover:text-gray-800 mr-3">
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

        <form action="{{ route('service-requests.store-motor', $asset) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label for="biaya" class="block text-sm font-medium text-gray-700 mb-1">Biaya Service <span class="text-red-500">*</span></label>
                <input type="text" name="biaya" id="biaya" value="{{ old('biaya') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 rupiah"
                       placeholder="Rp 0" required>
            </div>
            <div class="mb-4">
                <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan Service <span class="text-red-500">*</span></label>
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
                <label for="bukti_file" class="block text-sm font-medium text-gray-700 mb-1">Bukti Service (jpg,jpeg,png,pdf)</label>
                <input type="file" name="bukti_file" id="bukti_file" accept="image/*,application/pdf"
                       class="w-full text-sm text-gray-500">
                <p class="mt-1 text-xs text-gray-500">Maksimal 2MB</p>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    Ajukan Service
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
