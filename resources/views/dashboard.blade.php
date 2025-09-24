@extends('layouts.app')

@section('title', 'Dashboard Assets')

@section('content')
<div class="bg-gradient-to-br from-red-50 to-rose-100 min-h-screen overflow-x-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Welcome Header -->
    <div class="mb-8 rounded-xl shadow-lg p-6 bg-gradient-to-br from-red-500 to-red-600 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Dashboard Asset Management</h1>
                    <p class="text-red-100">PT. Duta Anugrah Damai Sejahtera</p>
                    <p class="text-sm text-red-200 mt-1">📅 {{ \Carbon\Carbon::now()->format('l, d F Y') }}</p>
                </div>
                <div class="hidden md:block">
                    <div class="text-right">
                        <p class="text-sm text-red-200">Selamat datang,</p>
                        <p class="text-lg font-semibold text-white">{{ auth()->user()->name }}</p>
                        <span class="inline-block px-3 py-1 text-xs font-medium bg-white bg-opacity-20 text-white rounded-full">
                            {{ ucfirst(auth()->user()->role) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Summary Cards dengan warna merah -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total Assets -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl shadow-lg p-6 transform hover:scale-105 transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">📦 Total Assets</p>
                        <p id="stat-total-assets" class="text-3xl font-bold">{{ $totalAssets ?? 0 }}</p>
                        <p class="text-red-100 text-xs mt-1">Unit terdaftar</p>
                    </div>
                    <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                        <i class="fas fa-boxes text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Nilai -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl shadow-lg p-6 transform hover:scale-105 transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">💰 Total Nilai</p>
                        <p id="stat-total-nilai" class="text-2xl font-bold">Rp {{ number_format($totalNilai ?? 0, 0, ',', '.') }}</p>
                        <p class="text-red-100 text-xs mt-1">Nilai investasi</p>
                    </div>
                    <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                        <i class="fas fa-dollar-sign text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Available -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl shadow-lg p-6 transform hover:scale-105 transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">✅ Available</p>
                        <p id="stat-available" class="text-3xl font-bold">{{ $availableAssets ?? 0 }}</p>
                        <p class="text-red-100 text-xs mt-1">Siap digunakan</p>
                    </div>
                    <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- In Use -->
            <div class="bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl shadow-lg p-6 transform hover:scale-105 transition-all duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium">🔄 In Use</p>
                        <p id="stat-in-use" class="text-3xl font-bold">{{ $inUseAssets ?? 0 }}</p>
                        <p class="text-red-100 text-xs mt-1">Sedang digunakan</p>
                    </div>
                    <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                        <i class="fas fa-exclamation-circle text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section dengan styling lebih menarik -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Project Distribution Chart -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 min-w-0">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-red-100 rounded-lg mr-3">
                        <i class="fas fa-chart-pie text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">📊 Distribusi Project</h3>
                        <p class="text-sm text-gray-500">Pembagian asset berdasarkan project</p>
                    </div>
                </div>
                <div style="height: 300px; position: relative;">
                    <canvas id="project-pie-chart"></canvas>
                </div>
            </div>

            <!-- Type Distribution Chart -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 min-w-0">
                <div class="flex items-center mb-4">
                    <div class="p-2 bg-red-100 rounded-lg mr-3">
                        <i class="fas fa-chart-bar text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">📈 Distribusi Tipe Asset</h3>
                        <p class="text-sm text-gray-500">Jumlah asset berdasarkan kategori</p>
                    </div>
                </div>
                <div style="height: 300px; position: relative;">
                    <canvas id="type-bar-chart"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Actions Section -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 mb-8">
            <div class="flex items-center mb-6">
                <div class="p-2 bg-red-100 rounded-lg mr-3">
                    <i class="fas fa-bolt text-red-600"></i>
                </div>
                <div>
                    <h3 class="text-xl font-semibold text-gray-900">⚡ Quick Actions</h3>
                    <p class="text-sm text-gray-500">Aksi cepat untuk mengelola sistem</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <a href="{{ route('assets.create') }}" class="group bg-gradient-to-r from-red-500 to-red-600 text-white p-4 rounded-xl shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-3 bg-white bg-opacity-20 rounded-lg mr-3">
                            <i class="fas fa-plus text-xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold">Tambah Asset</p>
                            <p class="text-xs text-red-100">Registrasi asset baru</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('activity.logs') }}" class="group bg-gradient-to-r from-red-500 to-red-600 text-white p-4 rounded-xl shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-3 bg-white bg-opacity-20 rounded-lg mr-3">
                            <i class="fas fa-history text-xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold">Activity Logs</p>
                            <p class="text-xs text-rose-100">Riwayat aktivitas</p>
                        </div>
                    </div>
                </a>

                <a href="{{ route('assets.export') }}" class="group bg-gradient-to-r from-red-500 to-red-600 text-white p-4 rounded-xl shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                    <div class="flex items-center">
                        <div class="p-3 bg-white bg-opacity-20 rounded-lg mr-3">
                            <i class="fas fa-download text-xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold">Export Data</p>
                            <p class="text-xs text-pink-100">Download laporan</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>


        <!-- Asset Management Section -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center mb-6">
                <div class="p-2 bg-red-100 rounded-lg mr-3">
                    <i class="fas fa-table text-red-600"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-semibold text-gray-900">Manajemen Asset</h2>
                    <p class="text-sm text-gray-500">Kelola dan monitor semua asset perusahaan</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Export Actions dengan styling lebih baik -->
                    <div x-data="{ open: false }" class="relative inline-block text-left">
                        <div>
                            <button @click="open = !open" type="button" class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 bg-red-600 text-white rounded-lg shadow-md hover:bg-red-700 transition-all duration-200" id="menu-button" aria-expanded="true" aria-haspopup="true">
                                <i class="fas fa-download mr-2"></i>
                                <span class="font-medium">Export</span>
                                <svg class="w-4 h-4 ml-2 -mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </div>

                        <div x-show="open" @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="origin-top-right absolute right-0 mt-2 w-64 rounded-xl shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10"
                             role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                            <div class="py-1" role="none">
                                <a href="{{ route('assets.export') }}?type=basic" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors" role="menuitem" tabindex="-1">
                                    <i class="fas fa-file-csv text-red-500 mr-3"></i>
                                    <div>
                                        <p class="font-medium">Data Utama</p>
                                        <p class="text-xs text-gray-500">Export basic info</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    @can('kelola-aset')
                    <a href="{{ route('assets.create') }}" class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 bg-red-600 text-white rounded-lg shadow-md hover:bg-red-700 transition-all duration-200 hover:scale-105" title="Tambah Asset">
                        <i class="fas fa-plus mr-2"></i>
                        <span class="font-medium">Tambah Asset</span>
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Smart Filters dengan tabs -->
            <div class="bg-red-50 rounded-xl p-4 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-filter text-red-600 mr-2"></i>
                    <h3 class="text-lg font-medium text-gray-900">Smart Filters</h3>
                    <span class="ml-2 px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">Real-time</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-7 gap-4">
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label for="search-pic" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search text-gray-400 mr-1"></i>
                            Cari PIC/Merk
                        </label>
                        <input type="text" id="search-pic" placeholder="Ketik untuk mencari..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                    </div>
                    <div>
                        <label for="filter-tipe" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-tag text-gray-400 mr-1"></i>
                            Tipe Asset
                        </label>
                        <select id="filter-tipe" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                            <option value="">Semua Tipe</option>
                            @if(isset($tipes))
                                @foreach($tipes as $tipe)
                                    <option value="{{ $tipe }}">{{ $tipe }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div>
                        <label for="filter-project" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-project-diagram text-gray-400 mr-1"></i>
                            Project
                        </label>
                        <select id="filter-project" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                            <option value=""> Semua Project</option>
                            @if(isset($projects))
                                @foreach($projects as $project)
                                    <option value="{{ $project }}">{{ $project }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div>
                        <label for="filter-lokasi" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i>
                            Lokasi
                        </label>
                        <select id="filter-lokasi" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                            <option value="">Semua Lokasi</option>
                            @if(isset($lokasis))
                                @foreach($lokasis as $lokasi)
                                    @if($lokasi !== '__add__')
                                        <option value="{{ $lokasi }}">{{ $lokasi }}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div>
                        <label for="filter-jenis-aset" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-cube text-gray-400 mr-1"></i>
                            Jenis Asset
                        </label>
                        <select id="filter-jenis-aset" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                            <option value="">Semua Jenis</option>
                            @if(isset($jenisAsets))
                                @foreach($jenisAsets as $jenis)
                                    <option value="{{ $jenis }}">{{ $jenis }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <!-- Status Asset Filter -->
                    <div>
                        <label for="filter-status" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-info-circle text-gray-400 mr-1"></i>
                            Status Asset
                        </label>
                        <select id="filter-status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all">
                            <option value="">Pilih Status</option>
                            <option value="Available">Available</option>
                            <option value="Rusak">Rusak</option>
                            <option value="Hilang">Hilang</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button id="reset-filters" class="w-full px-3 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-undo mr-2"></i>
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Asset Table dengan styling modern -->
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <!-- Bulk Actions dengan styling yang lebih baik -->
                <div id="bulkActions" class="flex items-center justify-between p-4 bg-blue-50 border-b border-blue-200">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-blue-600 mr-2"></i>
                            <span class="text-sm font-medium text-blue-900" id="selectedCount">0 asset dipilih</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button onclick="bulkDelete()" id="bulkDeleteBtn" class="px-4 py-2 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-all duration-200 flex items-center space-x-2 shadow-md hover:shadow-lg">
                            <i class="fas fa-trash"></i>
                            <span>Hapus Asset Terpilih</span>
                        </button>
                    </div>
                </div>

                <!-- Loading indicator -->
                <div id="table-loading" class="hidden p-8 text-center">
                    <div class="inline-flex items-center px-4 py-2 text-blue-600">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Memuat data...</span>
                    </div>
                </div>

                <!-- Mobile Card View -->
                <div class="lg:hidden" id="mobile-asset-cards">
                    <div class="p-4 space-y-4">
                        <!-- Mobile cards will be generated by JavaScript -->
                    </div>
                </div>

                <!-- Desktop Table View -->
                <div class="overflow-x-auto lg:block">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="selectAll" class="form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" title="Pilih Semua">
                                        <label for="selectAll" class="ml-2 text-xs text-gray-500">Pilih</label>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-tag text-gray-400 mr-2"></i>
                                        Tipe
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-cube text-gray-400 mr-2"></i>
                                        Jenis Asset
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-dollar-sign text-gray-400 mr-2"></i>
                                        Harga Beli
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-user text-gray-400 mr-2"></i>
                                        PIC
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-id-card text-gray-400 mr-2"></i>
                                        NIK
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-briefcase text-gray-400 mr-2"></i>
                                        Jabatan
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-industry text-gray-400 mr-2"></i>
                                        Merk
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-barcode text-gray-400 mr-2"></i>
                                        Serial
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-project-diagram text-gray-400 mr-2"></i>
                                        Project
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt text-gray-400 mr-2"></i>
                                        Lokasi
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar text-gray-400 mr-2"></i>
                                        Tanggal Beli
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-money-bill text-gray-400 mr-2"></i>
                                        Harga Sewa
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    <div class="flex items-center">
                                        <i class="fas fa-cog text-gray-400 mr-2"></i>
                                        Aksi
                                    </div>
                                </th>
                            </tr>
                        </thead>
                            <tbody id="asset-table-body">
                                @forelse($assets as $asset)
                                    <tr class="table-row-hover">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @php
                                                $checkboxValue = $asset->getKey() ?: $asset->id ?: 'no-id';
                                            @endphp
                                            <input type="checkbox" class="asset-checkbox form-checkbox h-4 w-4 text-blue-600 rounded border-gray-300 focus:ring-blue-500" value="{{ $checkboxValue }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 asset-tipe">{{ $asset->tipe ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 asset-jenis">{{ $asset->jenis_aset ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 asset-harga" data-value="{{ floatval($asset->getRawOriginal('harga_beli') ?? 0) }}">Rp {{ number_format(floatval($asset->getRawOriginal('harga_beli') ?? 0), 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 asset-pic" data-user-id="{{ $asset->user_id ?? '' }}">{{ optional($asset->user)->name ?? $asset->pic ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 asset-nik">{{ optional($asset->user)->nik ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ optional($asset->user)->jabatan ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 asset-merk">{{ $asset->merk ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->serial_number ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 asset-project">{{ $asset->project ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 asset-lokasi">{{ $asset->lokasi ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->tanggal_beli ? \Carbon\Carbon::parse($asset->tanggal_beli)->format('d M Y') : 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format(floatval($asset->getRawOriginal('harga_sewa') ?? 0), 0, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center gap-2">

                                                @php
                                                    $assetId = $asset->getKey() ?: $asset->id ?: ($asset->exists ? 'unknown' : 'new');
                                                @endphp

                                                @if($assetId && $assetId !== 'unknown' && $assetId !== 'new')
                                                    <a href="{{ route('assets.show', $assetId) }}" class="text-blue-600 hover:text-blue-900 transition-colors" title="Lihat Detail"><i class="fas fa-eye"></i></a>
                                                    <a href="{{ route('assets.edit', $assetId) }}" class="text-yellow-600 hover:text-yellow-900 transition-colors" title="Edit"><i class="fas fa-edit"></i></a>
                                                    <button onclick="deleteAsset({{ $assetId }})" class="text-red-600 hover:text-red-900 transition-colors" title="Hapus"><i class="fas fa-trash"></i></button>
                                                @else
                                                    <span class="text-red-400 text-xs">NO VALID ID ({{ $assetId }})</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="no-data"><td colspan="14" class="px-6 py-4 text-center text-gray-500">Tidak ada data asset</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- End Desktop Table View -->
            </div>

            <!-- Pagination -->
            @if(isset($assets) && is_object($assets) && method_exists($assets, 'links'))
                <div class="mt-6">
                    {{ $assets->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
/* Custom animations dan transitions */
.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.loading-pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.filter-active {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
    color: white !important;
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
}

.table-row-hover:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    transform: scale(1.01);
    transition: all 0.2s ease;
}

/* Toast notification styles */
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1000;
    max-width: 300px;
}

.stats-counter {
    transition: all 0.3s ease;
}

.stats-counter:hover {
    transform: scale(1.05);
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();
    initializeCharts();
    initializeBulkDelete();
    addToastNotifications();
    addFilterAnimations();
  });

  // Toast notification system
  function addToastNotifications() {
    window.showToast = function(message, type = 'success') {
      const toast = document.createElement('div');
      toast.className = `toast fade-in px-6 py-4 rounded-lg shadow-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white`;
      toast.innerHTML = `
        <div class="flex items-center">
          <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} mr-2"></i>
          <span>${message}</span>
          <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
            <i class="fas fa-times"></i>
          </button>
        </div>
      `;
      document.body.appendChild(toast);

      setTimeout(() => {
        if (toast.parentElement) {
          toast.remove();
        }
      }, 5000);
    };
  }

  // Add filter animations
  function addFilterAnimations() {
    const filterElements = ['#filter-tipe', '#filter-project', '#filter-lokasi', '#filter-jenis-aset'];
    filterElements.forEach(selector => {
      const element = document.querySelector(selector);
      if (element) {
        element.addEventListener('change', function() {
          if (this.value !== '') {
            this.classList.add('filter-active');
          } else {
            this.classList.remove('filter-active');
          }
        });
      }
    });
  }

  function initializeCharts() {
    const projectData = @json($projectSummary ?? []);
    const typeData    = @json($jenisSummary ?? []);

    // Enhanced pie chart
    const pieCtx = document.getElementById('project-pie-chart');
    if (pieCtx) {
      new Chart(pieCtx, {
        type:'pie',
        data:{
          labels: Object.keys(projectData),
          datasets:[{
            data: Object.values(projectData),
            backgroundColor:['#3B82F6','#EF4444','#10B981','#F59E0B','#8B5CF6','#06B6D4','#F97316','#84CC16','#EC4899','#6B7280'],
            borderWidth: 3,
            borderColor: '#ffffff',
            hoverBorderWidth: 5,
            hoverBorderColor: '#ffffff'
          }]
        },
        options:{
          responsive:true,
          maintainAspectRatio:false,
          plugins:{
            legend:{ position:'bottom', labels: { padding: 20, usePointStyle: true } },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleColor: '#ffffff',
              bodyColor: '#ffffff',
              borderColor: '#ef4444',
              borderWidth: 1,
              cornerRadius: 8
            }
          },
          animation: { animateRotate: true, animateScale: true }
        }
      });
    }

    // Enhanced bar chart
    const barCtx = document.getElementById('type-bar-chart');
    if (barCtx) {
      new Chart(barCtx, {
        type:'bar',
        data:{
          labels: Object.keys(typeData),
          datasets:[{
            label:'Jumlah Asset',
            data: Object.values(typeData),
            backgroundColor: 'rgba(239, 68, 68, 0.8)',
            borderColor: '#ef4444',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
          }]
        },
        options:{
          responsive:true,
          maintainAspectRatio:false,
          scales:{
            y:{
              beginAtZero:true,
              grid: { color: 'rgba(0, 0, 0, 0.05)' },
              ticks: { color: '#6B7280' }
            },
            x: {
              grid: { display: false },
              ticks: { color: '#6B7280' }
            }
          },
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              titleColor: '#ffffff',
              bodyColor: '#ffffff',
              borderColor: '#ef4444',
              borderWidth: 1,
              cornerRadius: 8
            }
          },
          animation: { duration: 1000, easing: 'easeOutQuart' }
        }
      });
    }
  }

  function initializeFilters() {
    try {
      const search = document.getElementById('search-pic');
      const tipe   = document.getElementById('filter-tipe');
      const proj   = document.getElementById('filter-project');
      const loca   = document.getElementById('filter-lokasi');
      const jenis  = document.getElementById('filter-jenis-aset');
      const status  = document.getElementById('filter-status');
      const reset  = document.getElementById('reset-filters');

      function applyFilters() {
        // Show loading
        const tableLoading = document.getElementById('table-loading');
        if (tableLoading) {
          tableLoading.classList.remove('hidden');
        }

        setTimeout(() => {
          const searchTerm = search ? search.value.toLowerCase() : '';
          const tipeFilter = tipe ? tipe.value : '';
          const projectFilter = proj ? proj.value : '';
          const lokasiFilter = loca ? loca.value : '';
          const jenisFilter = jenis ? jenis.value : '';
          const statusFilter = status ? status.value : '';

          const rows = document.querySelectorAll('#asset-table-body tr');
          let visibleCount = 0;
          let totalNilai = 0;
          let availableCount = 0;
          let inUseCount = 0;

          rows.forEach(row => {
            // Skip if this is the "no data" row
            if (row.querySelector('td[colspan]')) {
              return;
            }

            const picElement = row.querySelector('.asset-pic');
            const tipeElement = row.querySelector('.asset-tipe');
            const projectElement = row.querySelector('.asset-project');
            const lokasiElement = row.querySelector('.asset-lokasi');
            const jenisElement = row.querySelector('.asset-jenis');
            const merkElement = row.querySelector('.asset-merk');
            const hargaElement = row.querySelector('.asset-harga');

            const pic = picElement ? picElement.textContent.toLowerCase() : '';
            const tipeText = tipeElement ? tipeElement.textContent : '';
            const project = projectElement ? projectElement.textContent : '';
            const lokasi = lokasiElement ? lokasiElement.textContent : '';
            const jenis = jenisElement ? jenisElement.textContent : '';
            const merk = merkElement ? merkElement.textContent.toLowerCase() : '';
            const harga = hargaElement ? parseInt(hargaElement.dataset.value || '0') : 0;

            const matchesSearch = searchTerm === '' || pic.includes(searchTerm) || merk.includes(searchTerm);
            const matchesTipe = tipeFilter === '' || tipeText === tipeFilter;
            const matchesProject = projectFilter === '' || project === projectFilter;
            const matchesLokasi = lokasiFilter === '' || lokasi === lokasiFilter;
            const matchesJenis = jenisFilter === '' || jenis === jenisFilter;
            const matchesStatus = statusFilter === '' || pic.includes(statusFilter.toLowerCase());

            if (matchesSearch && matchesTipe && matchesProject && matchesLokasi && matchesJenis && matchesStatus) {
              row.style.display = '';
              row.classList.add('fade-in');
              visibleCount++;
              totalNilai += harga;
              if (pic.includes('available')) {
                availableCount++;
              } else if (!pic.includes('rusak') && !pic.includes('hilang') && pic.trim() !== '' && pic !== '-') {
                inUseCount++;
              }
            } else {
              row.style.display = 'none';
              row.classList.remove('fade-in');
            }
          });

          // Update stats with animation
          updateStatsWithAnimation('stat-total-assets', visibleCount);
          updateStatsWithAnimation('stat-total-nilai', 'Rp ' + totalNilai.toLocaleString('id-ID'));
          updateStatsWithAnimation('stat-available', availableCount);
          updateStatsWithAnimation('stat-in-use', inUseCount);

          // Hide loading
          if (tableLoading) {
            tableLoading.classList.add('hidden');
          }

          // Show toast if filtered
          if (searchTerm || tipeFilter || projectFilter || lokasiFilter || jenisFilter || statusFilter) {
          }
        }, 300); // Small delay for better UX
      }

      function updateStatsWithAnimation(elementId, newValue) {
        const element = document.getElementById(elementId);
        if (element) {
          element.style.transform = 'scale(1.1)';
          element.style.transition = 'transform 0.2s ease';
          setTimeout(() => {
            element.textContent = newValue;
            element.style.transform = 'scale(1)';
          }, 100);
        }
      }

      [search, tipe, proj, loca, jenis, status].forEach(el => el && el.addEventListener('change', applyFilters));
      if (search) search.addEventListener('input', applyFilters);

      if (reset) reset.addEventListener('click', function() {
        if (search) search.value = '';
        if (tipe) { tipe.value = ''; tipe.classList.remove('filter-active'); }
        if (proj) { proj.value = ''; proj.classList.remove('filter-active'); }
        if (loca) { loca.value = ''; loca.classList.remove('filter-active'); }
        if (jenis) { jenis.value = ''; jenis.classList.remove('filter-active'); }
        if (status) { status.value = ''; status.classList.remove('filter-active'); }

        // Show all rows
        const rows = document.querySelectorAll('#asset-table-body tr');
        rows.forEach(row => {
          row.style.display = '';
          row.classList.add('fade-in');
        });

        // Reset stats to original values
        updateStatsWithAnimation('stat-total-assets', '{{ $totalAssets ?? 0 }}');
        updateStatsWithAnimation('stat-total-nilai', 'Rp {{ number_format($totalNilai ?? 0, 0, ",", ".") }}');
        updateStatsWithAnimation('stat-available', '{{ $availableAssets ?? 0 }}');
        updateStatsWithAnimation('stat-in-use', '{{ $inUseAssets ?? 0 }}');

      });

      applyFilters();
    } catch(e) {
      console.error('Filter init error:', e);
    }
  }  function initializeBulkDelete() {
    window.toggleSelectAll = function() {
      const selectAllCheckbox = document.getElementById('selectAll');
      const assetCheckboxes = document.querySelectorAll('.asset-checkbox');

      assetCheckboxes.forEach(checkbox => {
          checkbox.checked = selectAllCheckbox.checked;
      });

      updateBulkDeleteButton();
    }

    window.updateBulkDeleteButton = function() {
      const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
      const bulkActionsDiv = document.getElementById('bulkActions');
      const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
      const selectedCount = document.getElementById('selectedCount');

      if (checkedBoxes.length > 0) {
          bulkActionsDiv.style.display = 'flex';
          bulkDeleteBtn.style.display = 'inline-flex';
          selectedCount.textContent = `${checkedBoxes.length} asset dipilih`;
      } else {
          bulkActionsDiv.style.display = 'none';
          bulkDeleteBtn.style.display = 'none';
      }
    }

    window.bulkDelete = function() {
      const checkedBoxes = document.querySelectorAll('.asset-checkbox:checked');
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
          csrfToken.value = '{{ csrf_token() }}';
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

    // Add event listeners for checkboxes
    const selectAllCheckbox = document.getElementById('selectAll');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', toggleSelectAll);
    }

    // Individual checkbox listeners
    const assetCheckboxes = document.querySelectorAll('.asset-checkbox');
    assetCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Update select all checkbox state
            const selectAllCheckbox = document.getElementById('selectAll');
            const totalCheckboxes = assetCheckboxes.length;
            const checkedCheckboxes = document.querySelectorAll('.asset-checkbox:checked').length;

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

            updateBulkDeleteButton();
        });
    });
}

// Asset actions
window.deleteAsset = function(id) {
    if (!confirm('Anda yakin ingin menghapus asset ini?')) return;

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    fetch(`/assets/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(async response => {
        if (response.ok) {
            location.reload();
            return;
        }

        // Try to parse JSON body for a helpful message
        let body = null;
        try {
            body = await response.json();
        } catch (err) {
            // non-json body
            console.warn('Failed to parse JSON error body', err);
        }

        console.error('Delete failed:', response, body);

        // Specific handling for common HTTP errors
        if (response.status === 419) {
            alert('Session kadaluarsa atau token CSRF tidak valid. Silakan muat ulang halaman dan coba lagi.');
            return;
        }
        if (response.status === 403) {
            alert(body?.message || 'Anda tidak memiliki izin untuk menghapus asset ini.');
            return;
        }

        // Show server-provided message when available
        if (body && (body.message || body.error)) {
            alert(body.message || body.error);
            return;
        }

        alert('Gagal menghapus asset');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus asset');
    });
};

// Generate mobile cards from table data
function generateMobileCards() {
    const tableBody = document.getElementById('asset-table-body');
    const mobileContainer = document.querySelector('#mobile-asset-cards .space-y-4');

    if (!tableBody || !mobileContainer) return;

    mobileContainer.innerHTML = '';

    const rows = tableBody.querySelectorAll('tr:not(.no-data)');

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length === 0) return;

        const cardHTML = `
            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex items-start space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-400 to-red-600 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">${cells[7]?.textContent?.trim() || '-'}</h3>
                            <p class="text-sm text-gray-500">${cells[2]?.textContent?.trim() || '-'}</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        ${cells[cells.length-1]?.innerHTML || ''}
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <span class="text-gray-500">Tipe:</span>
                        <span class="font-medium ml-1">${cells[1]?.textContent?.trim() || '-'}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Status:</span>
                        <span class="font-medium ml-1">${cells[3]?.textContent?.trim() || '-'}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">PIC:</span>
                        <span class="font-medium ml-1">${cells[4]?.textContent?.trim() || '-'}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">NIK:</span>
                        <span class="font-medium ml-1">${cells[5]?.textContent?.trim() || '-'}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Project:</span>
                        <span class="font-medium ml-1">${cells[9]?.textContent?.trim() || '-'}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Lokasi:</span>
                        <span class="font-medium ml-1">${cells[10]?.textContent?.trim() || '-'}</span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-gray-500">Serial:</span>
                        <span class="font-medium ml-1">${cells[8]?.textContent?.trim() || '-'}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Harga Beli:</span>
                        <span class="font-medium ml-1 text-green-600">${cells[12]?.textContent?.trim() || '-'}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Harga Sewa:</span>
                        <span class="font-medium ml-1 text-blue-600">${cells[13]?.textContent?.trim() || '-'}</span>
                    </div>
                </div>
            </div>
        `;

        mobileContainer.insertAdjacentHTML('beforeend', cardHTML);
    });
}

// Initialize mobile cards on page load and after filters
document.addEventListener('DOMContentLoaded', function() {
    generateMobileCards();

    // Regenerate cards when filters are applied
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' && mutation.target.id === 'asset-table-body') {
                generateMobileCards();
            }
        });
    });

    const tableBody = document.getElementById('asset-table-body');
    if (tableBody) {
        observer.observe(tableBody, { childList: true, subtree: true });
    }
    // Debug helper: log asset ids present in the DOM (useful when switching DB)
    function debugAssetIds() {
        try {
            const ids = Array.from(document.querySelectorAll('.asset-checkbox')).map(cb => cb.value || null);
            console.log('DEBUG: asset checkbox values:', ids);
            const rows = Array.from(document.querySelectorAll('#asset-table-body tr'));
            rows.forEach((r, i) => {
                const cb = r.querySelector('.asset-checkbox');
                if (!cb) {
                    console.log(`DEBUG: row ${i} has no checkbox`);
                } else if (!cb.value) {
                    console.log(`DEBUG: row ${i} checkbox empty value — row text:`, r.textContent.trim().slice(0,120));
                }
            });
        } catch (e) {
            console.warn('DEBUG: failed to enumerate asset ids', e);
        }
    }

    // Call debug helper on initial load
    debugAssetIds();
});
</script>
@endpush
