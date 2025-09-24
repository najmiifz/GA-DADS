@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('apd-requests.index') }}" class="text-gray-600 hover:text-gray-800 mr-3">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-800">Pengajuan APD</h1>
        </div>
        <!-- APD Request Form -->
        <form action="{{ route('apd-requests.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="team_mandor" class="block text-sm font-medium text-gray-700">Nama Tim Mandor <span class="text-red-500">*</span></label>
                <input type="text" name="team_mandor" id="team_mandor" value="{{ old('team_mandor') }}" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="helm" class="block text-sm font-medium text-gray-700">Helm</label>
                    <input type="number" name="helm" id="helm" value="{{ old('helm',0) }}" min="0" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div>
                    <label for="rompi" class="block text-sm font-medium text-gray-700">Rompi</label>
                    <input type="number" name="rompi" id="rompi" value="{{ old('rompi',0) }}" min="0" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div>
                    <label for="apboots" class="block text-sm font-medium text-gray-700">AP Boots</label>
                    <input type="number" name="apboots" id="apboots" value="{{ old('apboots',0) }}" min="0" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div>
                    <label for="body_harness" class="block text-sm font-medium text-gray-700">Body Harness</label>
                    <input type="number" name="body_harness" id="body_harness" value="{{ old('body_harness',0) }}" min="0" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
                <div class="col-span-2">
                    <label for="sarung_tangan" class="block text-sm font-medium text-gray-700">Sarung Tangan</label>
                    <input type="number" name="sarung_tangan" id="sarung_tangan" value="{{ old('sarung_tangan',0) }}" min="0" class="mt-1 block w-full border border-gray-300 rounded-md p-2">
                </div>
            </div>
            <div>
                <label for="nama_cluster" class="block text-sm font-medium text-gray-700">Nama Cluster <span class="text-red-500">*</span></label>
                <input type="text" name="nama_cluster" id="nama_cluster" value="{{ old('nama_cluster') }}" required class="mt-1 block w-full border border-gray-300 rounded-md p-2">
            </div>
            <div>
                <label for="lokasi_project" class="block text-sm font-medium text-gray-700">Lokasi Project</label>
                <input type="text" name="lokasi_project" id="lokasi_project" value="{{ old('lokasi_project') }}" class="mt-1 block w-full border border-gray-300 rounded-md p-2" placeholder="Contoh: Site A">
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md">Ajukan APD</button>
            </div>
        </form>
        @if(session('success'))
            <div class="mt-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">{{ session('success') }}</div>
        @endif
    </div>
</div>
@endsection
