@extends('layouts.app')

@section('title', 'Kelola Asset')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8" x-data="assetManager()">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-xl p-6 text-white mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div class="mb-4 lg:mb-0">
                <h1 class="text-3xl font-bold mb-2">
                    <i class="fas fa-boxes mr-2"></i>Kelola Asset Perusahaan
                </h1>
                <p class="text-indigo-100 text-lg">Manajemen lengkap untuk semua asset perusahaan</p>
                <div class="flex items-center mt-2 space-x-4">
                    <span class="text-sm text-indigo-200">
                        <i class="fas fa-list mr-1"></i>Total: {{ $assets->total() ?? 0 }} Asset
                    </span>
                    <span class="text-sm text-indigo-200">
                        <i class="fas fa-eye mr-1"></i>Menampilkan {{ $assets->count() ?? 0 }} dari {{ $assets->total() ?? 0 }}
                    </span>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                @can('kelola-aset')
                <a href="{{ route('assets.create') }}"
                   class="px-6 py-3 bg-yellow-500 hover:bg-yellow-600 rounded-lg transition-all duration-200 font-semibold text-black flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i>Tambah Asset
                </a>
                @endcan
                <a href="{{ route('assets.export') }}"
                   class="px-6 py-3 bg-white/20 hover:bg-white/30 rounded-lg transition-all duration-200 backdrop-blur-sm flex items-center justify-center">
                    <i class="fas fa-download mr-2"></i>Export Excel
                </a>
            </div>
        </div>
</div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Assets -->
        <div class="bg-red-600 text-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">Total Assets</p>
                    <p class="text-2xl font-bold">{{ $totalAset ?? 0 }}</p>
                </div>
                <div class="p-3 bg-white rounded-lg">
                    <i class="fas fa-boxes text-red-600"></i>
                </div>
            </div>
        </div>
        <!-- Total Nilai -->
        <div class="bg-red-600 text-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">Total Nilai</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($totalNilai ?? 0, 0, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-white rounded-lg">
                    <i class="fas fa-dollar-sign text-red-600"></i>
                </div>
            </div>
        </div>
        <!-- Available -->
        <div class="bg-red-600 text-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">Available</p>
                    <p class="text-2xl font-bold">{{ $tersedia ?? 0 }}</p>
                </div>
                <div class="p-3 bg-white rounded-lg">
                    <i class="fas fa-check-circle text-red-600"></i>
                </div>
            </div>
        </div>
        <!-- In Use -->
        <div class="bg-red-600 text-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">In Use</p>
                    <p class="text-2xl font-bold">{{ $terpakai ?? 0 }}</p>
                </div>
                <div class="p-3 bg-white rounded-lg">
                    <i class="fas fa-exclamation-circle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Filter & Search Section -->
    <div class="bg-white rounded-xl shadow-lg p-6 mb-6 card-hover">
        <div class="flex flex-col lg:flex-row lg:items-center lg:space-x-6 space-y-4 lg:space-y-0">
            <!-- Search Bar -->
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text"
                           x-model="searchQuery"
                           @input="debounceSearch()"
                           placeholder="Cari nama asset, kode, atau lokasi..."
                           class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                    <div x-show="searchQuery"
                         @click="searchQuery = ''; performSearch()"
                         class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer">
                        <i class="fas fa-times text-gray-400 hover:text-gray-600"></i>
                    </div>
                </div>
            </div>

            <!-- Filter Dropdowns -->
            <div class="flex flex-wrap space-x-3">
                <!-- Jenis Filter -->
                <div class="relative" x-data="{ jenisOpen: false }">
                    <button @click="jenisOpen = !jenisOpen"
                            class="px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 flex items-center space-x-2 transition-colors">
                        <i class="fas fa-tags text-blue-500"></i>
                        <span x-text="selectedJenis || 'Semua Jenis'"></span>
                        <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': jenisOpen }"></i>
                    </button>
                    <div x-show="jenisOpen"
                         x-transition
                         @click.away="jenisOpen = false"
                         class="absolute z-10 mt-2 w-48 bg-white rounded-lg shadow-lg border">
                        <button @click="selectedJenis = ''; jenisOpen = false; performSearch()"
                                class="w-full text-left px-4 py-2 hover:bg-gray-50 rounded-t-lg">Semua Jenis</button>
                        <button @click="selectedJenis = 'Kendaraan'; jenisOpen = false; performSearch()"
                                class="w-full text-left px-4 py-2 hover:bg-gray-50">Kendaraan</button>
                        <button @click="selectedJenis = 'Splicer'; jenisOpen = false; performSearch()"
                                class="w-full text-left px-4 py-2 hover:bg-gray-50">Splicer</button>
                        <button @click="selectedJenis = 'IT Equipment'; jenisOpen = false; performSearch()"
                                class="w-full text-left px-4 py-2 hover:bg-gray-50 rounded-b-lg">IT Equipment</button>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="relative" x-data="{ statusOpen: false }">
                    <button @click="statusOpen = !statusOpen"
                            class="px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-lg border border-gray-200 flex items-center space-x-2 transition-colors">
                        <i class="fas fa-flag text-green-500"></i>
                        <span x-text="selectedStatus || 'Semua Status'"></span>
                        <i class="fas fa-chevron-down text-xs" :class="{ 'rotate-180': statusOpen }"></i>
                    </button>
                    <div x-show="statusOpen"
                         x-transition
                         @click.away="statusOpen = false"
                         class="absolute z-10 mt-2 w-48 bg-white rounded-lg shadow-lg border">
                        <button @click="selectedStatus = ''; statusOpen = false; performSearch()"
                                class="w-full text-left px-4 py-2 hover:bg-gray-50 rounded-t-lg">Semua Status</button>
                        <button @click="selectedStatus = 'Terpakai'; statusOpen = false; performSearch()"
                                class="w-full text-left px-4 py-2 hover:bg-gray-50">Terpakai</button>
                        <button @click="selectedStatus = 'Tersedia'; statusOpen = false; performSearch()"
                                class="w-full text-left px-4 py-2 hover:bg-gray-50 rounded-b-lg">Tersedia</button>
                    </div>
                </div>

                <!-- View Mode Toggle -->
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button @click="viewMode = 'grid'"
                            :class="viewMode === 'grid' ? 'bg-white shadow text-indigo-600' : 'text-gray-600 hover:text-gray-900'"
                            class="px-3 py-2 rounded-md transition-all">
                        <i class="fas fa-th"></i>
                    </button>
                    <button @click="viewMode = 'list'"
                            :class="viewMode === 'list' ? 'bg-white shadow text-indigo-600' : 'text-gray-600 hover:text-gray-900'"
                            class="px-3 py-2 rounded-md transition-all">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Active Filters Display -->
        <div x-show="hasActiveFilters()" class="flex flex-wrap gap-2 mt-4 pt-4 border-t border-gray-200">
            <span class="text-sm text-gray-600 mr-2">Filter aktif:</span>
            <template x-for="filter in getActiveFilters()">
                <span class="inline-flex items-center px-2 py-1 bg-indigo-100 text-indigo-800 text-xs font-medium rounded-full">
                    <span x-text="filter.label"></span>
                    <button @click="removeFilter(filter.type)" class="ml-1 text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </span>
            </template>
            <button @click="clearAllFilters()" class="text-xs text-red-600 hover:text-red-800 underline">
                Hapus semua filter
            </button>
        </div>
    </div>

    <!-- Assets Display -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover">
        <!-- Loading State -->
        <div x-show="loading" class="p-8 text-center">
            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-sm shadow rounded-md text-white bg-indigo-500 transition ease-in-out duration-150">
                <div class="animate-spin -ml-1 mr-3 h-5 w-5 text-white">
                    <i class="fas fa-circle-notch"></i>
                </div>
                Memuat data...
            </div>
        </div>

        <!-- Grid View -->
        <div x-show="viewMode === 'grid' && !loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 p-6">
            @forelse($assets as $asset)
                <div class="group bg-white border border-gray-200 rounded-xl p-6 hover:border-indigo-300 hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <!-- Asset Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-2">
                            <div class="w-10 h-10 {{ $asset->jenis === 'Kendaraan' ? 'bg-blue-100' : ($asset->jenis === 'Splicer' ? 'bg-green-100' : 'bg-purple-100') }} rounded-lg flex items-center justify-center">
                                <i class="fas {{ $asset->jenis === 'Kendaraan' ? 'fa-car' : ($asset->jenis === 'Splicer' ? 'fa-tools' : 'fa-laptop') }} {{ $asset->jenis === 'Kendaraan' ? 'text-blue-600' : ($asset->jenis === 'Splicer' ? 'text-green-600' : 'text-purple-600') }}"></i>
                            </div>
                            <span class="px-2 py-1 text-xs font-medium {{ $asset->status === 'Terpakai' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }} rounded-full">
                                {{ $asset->status ?? '-' }}
                            </span>
                        </div>
                        <div class="opacity-0 group-hover:opacity-100 transition-opacity">
                            <div class="relative" x-data="{ menuOpen: false }">
                                <button @click="menuOpen = !menuOpen" class="p-2 hover:bg-gray-100 rounded-lg">
                                    <i class="fas fa-ellipsis-v text-gray-400"></i>
                                </button>
                                <div x-show="menuOpen"
                                     x-transition
                                     @click.away="menuOpen = false"
                                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border z-20">
                                    @if(!empty($asset->id))
                                    <a href="{{ route('assets.show', $asset->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-eye mr-2"></i>Lihat Detail
                                    </a>
                                    @else
                                    <span class="block px-4 py-2 text-sm text-gray-400">
                                        <i class="fas fa-eye mr-2"></i>Lihat Detail
                                    </span>
                                    @endif
                                    @can('kelola-aset')
                                    <a href="{{ route('assets.edit', $asset->id) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i class="fas fa-edit mr-2"></i>Edit
                                    </a>
                                    <button onclick="confirmDelete('{{ route('assets.destroy', $asset->id) }}')"
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i class="fas fa-trash mr-2"></i>Hapus
                                    </button>
                                    @endcan
                                    @if(!empty($asset->id))
                                    <a href="{{ route('reimburse-requests.create', $asset) }}" class="block px-4 py-2 text-sm text-blue-600 hover:bg-gray-50">
                                        <i class="fas fa-wallet mr-2"></i>Ajukan Reimburse
                                    </a>
                                    @else
                                    <span class="block px-4 py-2 text-sm text-gray-400">
                                        <i class="fas fa-wallet mr-2"></i>Ajukan Reimburse
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Asset Info -->
                    <div class="space-y-3">
                        <div>
                            <h3 class="font-semibold text-gray-900 text-lg group-hover:text-indigo-600 transition-colors">
                                {{ $asset->nama }}
                            </h3>
                            <p class="text-sm text-gray-600 mt-1">{{ $asset->kode_asset }}</p>
                        </div>

                        <div class="space-y-2">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-tag w-4 mr-2"></i>
                                <span>{{ $asset->jenis }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-map-marker-alt w-4 mr-2"></i>
                                <span>{{ $asset->lokasi }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-project-diagram w-4 mr-2"></i>
                                <span>{{ $asset->project ?? 'Tidak ada project' }}</span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-100">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Nilai Asset:</span>
                                <span class="font-semibold text-gray-900">Rp {{ number_format($asset->nilai ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="flex space-x-2 pt-3">
                            @if(!empty($asset->id))
                            <a href="{{ route('assets.show', $asset->id) }}"
                               class="flex-1 px-3 py-2 bg-indigo-100 text-indigo-700 text-center text-sm font-medium rounded-lg hover:bg-indigo-200 transition-colors">
                                Detail
                            </a>
                            @else
                            <span class="flex-1 px-3 py-2 bg-gray-100 text-gray-400 text-center text-sm font-medium rounded-lg">
                                Detail
                            </span>
                            @endif
                            @can('kelola-aset')
                            <a href="{{ route('assets.edit', $asset->id) }}"
                               class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 text-center text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                                Edit
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-500">
                        <i class="fas fa-boxes text-6xl mb-4"></i>
                        <h3 class="text-xl font-medium mb-2">Belum ada asset</h3>
                        <p class="text-gray-400 mb-6">Mulai dengan menambahkan asset pertama Anda</p>
                        @can('kelola-aset')
                        <a href="{{ route('assets.create') }}"
                           class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Tambah Asset Pertama
                        </a>
                        @endcan
                    </div>
                </div>
            @endforelse
        </div>

        <!-- List View (desktop table for md+, mobile cards for small screens) -->
        <div x-show="viewMode === 'list' && !loading">
            {{-- Mobile list cards --}}
            <div class="md:hidden space-y-4 p-4">
                @forelse($assets as $asset)
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="font-medium text-gray-900">{{ $asset->nama }}</div>
                                <div class="text-sm text-gray-500">{{ $asset->kode_asset }}</div>
                            </div>
                                <div class="text-sm text-gray-500 text-right">
                                <div class="mb-1">{{ $asset->lokasi }}</div>
                                <div class="inline-block px-2 py-1 text-xs font-medium {{ $asset->status === 'Terpakai' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }} rounded">{{ $asset->status ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <div class="text-sm text-gray-700">Nilai: Rp {{ number_format($asset->nilai ?? 0, 0, ',', '.') }}</div>
                            <div class="flex items-center space-x-2">
                                @if(!empty($asset->id))
                                <a href="{{ route('assets.show', $asset->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Detail</a>
                                @else
                                <span class="text-gray-400 text-sm">Detail</span>
                                @endif
                                @can('kelola-aset')
                                <a href="{{ route('assets.edit', $asset->id) }}" class="text-yellow-600 hover:text-yellow-900 text-sm">Edit</a>
                                @endcan
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500">Belum ada asset</div>
                @endforelse
            </div>

            {{-- Desktop/table view --}}
            <div class="hidden md:block overflow-x-auto">
            @can('kelola-aset')
            <!-- Bulk Actions -->
            <div class="flex items-center justify-between mb-4 p-4 bg-gray-50 rounded-lg border" style="display: none;" id="bulkActionsAssets">
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600" id="selectedCountAssets">0 asset dipilih</span>
                </div>
                <div class="flex items-center space-x-2">
                    <button onclick="bulkDeleteAssets()" id="bulkDeleteBtnAssets" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors flex items-center space-x-2" style="display: none;">
                        <i class="fas fa-trash"></i>
                        <span>Hapus Asset Terpilih</span>
                    </button>
                </div>
            </div>
            @endcan

            <table class="min-w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        @can('kelola-aset')
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAllAssets" class="rounded border-gray-300 text-indigo-600 shadow-sm" title="Pilih Semua">
                        </th>
                        @endcan
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Asset
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Jenis & Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Lokasi
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Project
                        </th>
                        <!-- Status Asset Column -->
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-flag text-gray-400 mr-2"></i>
                                Status Asset
                            </div>
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Nilai
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($assets as $asset)
                        <tr class="hover:bg-gray-50 transition-colors">
                            @can('kelola-aset')
                            <td class="px-6 py-4">
                                <input type="checkbox" class="asset-checkbox-list rounded border-gray-300 text-indigo-600 shadow-sm" value="{{ $asset->id }}">
                            </td>
                            @endcan
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 {{ $asset->jenis === 'Kendaraan' ? 'bg-blue-100' : ($asset->jenis === 'Splicer' ? 'bg-green-100' : 'bg-purple-100') }} rounded-lg flex items-center justify-center">
                                        <i class="fas {{ $asset->jenis === 'Kendaraan' ? 'fa-car' : ($asset->jenis === 'Splicer' ? 'fa-tools' : 'fa-laptop') }} {{ $asset->jenis === 'Kendaraan' ? 'text-blue-600' : ($asset->jenis === 'Splicer' ? 'text-green-600' : 'text-purple-600') }} text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $asset->nama }}</div>
                                        <div class="text-sm text-gray-500">{{ $asset->kode_asset }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <span class="inline-block px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded">{{ $asset->jenis }}</span>
                                    <span class="block px-2 py-1 text-xs font-medium {{ $asset->status === 'Terpakai' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }} rounded">{{ $asset->status ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $asset->lokasi }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $asset->project ?? '-' }}</div>
                            </td>
                            <!-- Status Asset Value -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 asset-status">{{ $asset->status ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">Rp {{ number_format($asset->nilai ?? 0, 0, ',', '.') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2">
                                    @if(!empty($asset->id))
                                    <a href="{{ route('assets.show', $asset->id) }}"
                                       class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @else
                                    <span class="text-gray-400 text-sm"><i class="fas fa-eye"></i></span>
                                    @endif
                                    @can('kelola-aset')
                                    <a href="{{ route('assets.edit', $asset->id) }}"
                                       class="text-yellow-600 hover:text-yellow-900 text-sm font-medium">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="confirmDelete('{{ route('assets.destroy', $asset->id) }}')"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-boxes text-4xl mb-3"></i>
                                    <p class="text-lg font-medium">Belum ada asset</p>
                                    <p class="text-sm">Mulai dengan menambahkan asset pertama</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($assets->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                {{ $assets->links('pagination::tailwind') }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function assetManager() {
    return {
        searchQuery: '{{ request('search') }}',
        selectedJenis: '{{ request('jenis') }}',
        selectedStatus: '{{ request('status') }}',
        viewMode: 'grid',
        loading: false,
        debounceTimer: null,

        debounceSearch() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.performSearch();
            }, 500);
        },

        performSearch() {
            this.loading = true;
            const params = new URLSearchParams();

            if (this.searchQuery) params.set('search', this.searchQuery);
            if (this.selectedJenis) params.set('jenis', this.selectedJenis);
            if (this.selectedStatus) params.set('status', this.selectedStatus);

            const url = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
            window.location.href = url;
        },

        hasActiveFilters() {
            return this.searchQuery || this.selectedJenis || this.selectedStatus;
        },

        getActiveFilters() {
            const filters = [];
            if (this.searchQuery) filters.push({ type: 'search', label: `Search: ${this.searchQuery}` });
            if (this.selectedJenis) filters.push({ type: 'jenis', label: `Jenis: ${this.selectedJenis}` });
            if (this.selectedStatus) filters.push({ type: 'status', label: `Status: ${this.selectedStatus}` });
            return filters;
        },

        removeFilter(type) {
            if (type === 'search') this.searchQuery = '';
            if (type === 'jenis') this.selectedJenis = '';
            if (type === 'status') this.selectedStatus = '';
            this.performSearch();
        },

        clearAllFilters() {
            this.searchQuery = '';
            this.selectedJenis = '';
            this.selectedStatus = '';
            this.performSearch();
        }
    }
}

function confirmDelete(url) {
    if (confirm('Apakah Anda yakin ingin menghapus asset ini? Tindakan ini tidak dapat dibatalkan.')) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Bulk delete functionality for assets index
function toggleSelectAllAssets() {
    const selectAllCheckbox = document.getElementById('selectAllAssets');
    const assetCheckboxes = document.querySelectorAll('.asset-checkbox-list');

    assetCheckboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });

    updateBulkDeleteButtonAssets();
}

function updateBulkDeleteButtonAssets() {
    const checkedBoxes = document.querySelectorAll('.asset-checkbox-list:checked');
    const bulkActionsDiv = document.getElementById('bulkActionsAssets');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtnAssets');
    const selectedCount = document.getElementById('selectedCountAssets');

    if (checkedBoxes.length > 0) {
        bulkActionsDiv.style.display = 'flex';
        bulkDeleteBtn.style.display = 'inline-flex';
        selectedCount.textContent = `${checkedBoxes.length} asset dipilih`;
    } else {
        bulkActionsDiv.style.display = 'none';
        bulkDeleteBtn.style.display = 'none';
    }
}

function bulkDeleteAssets() {
    const checkedBoxes = document.querySelectorAll('.asset-checkbox-list:checked');
    const assetIds = Array.from(checkedBoxes).map(checkbox => checkbox.value);

    if (assetIds.length === 0) {
        alert('Pilih minimal satu asset untuk dihapus');
        return;
    }

    if (confirm(`Apakah Anda yakin ingin menghapus ${assetIds.length} asset? Tindakan ini tidak dapat dibatalkan.`)) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("assets.bulk-delete") }}';

        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        form.appendChild(csrfToken);

        // Add method field
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);

        // Add asset IDs
        const idsField = document.createElement('input');
        idsField.type = 'hidden';
        idsField.name = 'asset_ids';
        idsField.value = JSON.stringify(assetIds);
        form.appendChild(idsField);

        document.body.appendChild(form);
        form.submit();
    }
}

// Initialize event listeners for assets index
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox listener
    const selectAllCheckbox = document.getElementById('selectAllAssets');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', toggleSelectAllAssets);
    }

    // Individual checkbox listeners
    const assetCheckboxes = document.querySelectorAll('.asset-checkbox-list');
    assetCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Update select all checkbox state
            const selectAllCheckbox = document.getElementById('selectAllAssets');
            const totalCheckboxes = assetCheckboxes.length;
            const checkedCheckboxes = document.querySelectorAll('.asset-checkbox-list:checked').length;

            if (checkedCheckboxes === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedCheckboxes === totalCheckboxes) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }

            updateBulkDeleteButtonAssets();
        });
    });
});
</script>
@endpush
@endsection
