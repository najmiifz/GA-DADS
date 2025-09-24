@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-rose-50">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-red-600 to-rose-600 rounded-xl shadow-xl p-6 text-white relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 bg-white opacity-10">
                    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="white" fill-opacity="0.1"%3E%3Cpath d="m0 40l40-40h-40z"/%3E%3C/g%3E%3C/svg%3E');"></div>
                </div>
                <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="mb-4 sm:mb-0">
                        <div class="flex items-center mb-2">
                            <div class="h-16 w-16 bg-white rounded-full overflow-hidden flex items-center justify-center mr-4 border-2 border-white">
                                @if(auth()->user()->avatar_url)
                                    <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="h-full w-full object-cover">
                                @else
                                    <i class="fas fa-user-tie text-3xl"></i>
                                @endif
                            </div>
                            <div>
                                <h1 class="text-2xl sm:text-3xl font-bold">Dashboard PIC</h1>
                                <p class="text-red-100 mt-1">Selamat datang, {{ auth()->user()->name }}</p>
                                <p class="text-white text-sm mt-1">Lokasi: {{ auth()->user()->lokasi ?? '-' }}</p>
                                <p class="text-white text-sm">Jabatan: {{ auth()->user()->jabatan ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-left sm:text-right">
                        <div class="bg-white bg-opacity-20 rounded-lg p-4 backdrop-blur-sm">
                            <div class="text-3xl sm:text-4xl font-bold">{{ $totalAssets }}</div>
                            <div class="text-red-100 text-sm">Asset yang Anda kelola</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Asset List -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-5 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center mb-2 sm:mb-0">
                        <div class="h-10 w-10 bg-gradient-to-r from-red-500 to-rose-500 rounded-lg flex items-center justify-center mr-3">
                            <i class="fas fa-clipboard-list text-white"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-800">Daftar Asset Anda</h2>
                    </div>
                    <div class="flex items-center">
                        <div class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm font-medium">
                            {{ $assets->count() }} dari {{ $assets->total() }} asset
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile-First Card Layout -->
            <div class="p-6">
                @forelse($assets as $asset)
                <div class="group bg-gradient-to-r from-white to-gray-50 hover:from-red-50 hover:to-rose-50 rounded-xl p-5 mb-4 border border-gray-200 hover:border-red-300 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between">
                        <!-- Asset Info -->
                        <div class="flex items-start flex-1 mb-4 sm:mb-0">
                            <div class="flex-shrink-0 h-14 w-14 relative">
                                <div class="h-14 w-14 rounded-xl bg-gradient-to-br shadow-lg flex items-center justify-center group-hover:scale-110 transition-transform duration-300
                                    @if($asset->tipe === 'Kendaraan') from-red-400 to-red-600
                                    @elseif($asset->tipe === 'Elektronik') from-orange-400 to-orange-600
                                    @elseif($asset->tipe === 'Peralatan') from-pink-400 to-pink-600
                                    @else from-rose-400 to-rose-600 @endif">
                                    @if($asset->tipe === 'Kendaraan')
                                        <i class="fas fa-car text-white text-xl"></i>
                                    @elseif($asset->tipe === 'Elektronik')
                                        <i class="fas fa-laptop text-white text-xl"></i>
                                    @elseif($asset->tipe === 'Peralatan')
                                        <i class="fas fa-tools text-white text-xl"></i>
                                    @else
                                        <i class="fas fa-box text-white text-xl"></i>
                                    @endif
                                </div>
                                <!-- Status Dot -->
                                <div class="absolute -top-1 -right-1 h-4 w-4 bg-green-400 border-2 border-white rounded-full animate-pulse"></div>
                            </div>
                            <div class="ml-4 flex-1 min-w-0">
                                <div class="flex items-center mb-1">
                                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-red-600 transition-colors duration-300">{{ $asset->merk }}</h3>
                                    <span class="ml-2 px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">{{ $asset->tipe }}</span>
                                </div>
                                <p class="text-sm text-gray-600 mb-3 font-medium">{{ $asset->jenis_aset }}</p>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div class="flex items-center text-sm text-gray-600 bg-gray-50 rounded-lg p-2">
                                        <div class="h-8 w-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-project-diagram text-red-600 text-xs"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-xs text-gray-500 font-medium">Project</div>
                                            <div class="font-semibold text-gray-700">{{ $asset->project }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600 bg-gray-50 rounded-lg p-2">
                                        <div class="h-8 w-8 bg-rose-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-map-marker-alt text-rose-600 text-xs"></i>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-xs text-gray-500 font-medium">Lokasi</div>
                                            <div class="font-semibold text-gray-700">{{ $asset->lokasi }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="flex-shrink-0 w-full sm:w-auto sm:ml-6">
                            @if(!empty($asset) && isset($asset->id))
                                <a href="{{ route('assets.show', $asset) }}"
                                   class="group-hover:scale-105 transition-all duration-300 w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white text-sm font-semibold rounded-xl shadow-lg hover:shadow-xl">
                                    <i class="fas fa-eye mr-2"></i>
                                    Lihat Detail
                                </a>
                            @else
                                <button type="button" disabled class="opacity-60 cursor-not-allowed w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-500 text-sm font-semibold rounded-xl border">
                                    <i class="fas fa-eye mr-2"></i>
                                    Lihat Detail
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-16">
                    <div class="relative">
                        <!-- Animated Background -->
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="h-32 w-32 bg-gradient-to-r from-red-100 to-rose-100 rounded-full animate-pulse"></div>
                        </div>
                        <div class="relative text-gray-500">
                                        @if(!empty($asset->id))
                                            <a href="{{ route('assets.show', $asset) }}"
                                               class="group-hover:scale-105 transition-all duration-300 w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white text-sm font-semibold rounded-xl shadow-lg hover:shadow-xl">
                                                <i class="fas fa-eye mr-2"></i>
                                                Lihat Detail
                                            </a>
                                        @else
                                            <span class="text-gray-400">Lihat Detail</span>
                                        @endif
                    </div>
                </div>
                @endforelse
            </div>

            @if($assets->hasPages())
            <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200 rounded-b-xl">
                <div class="flex justify-center">
                    {{ $assets->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
