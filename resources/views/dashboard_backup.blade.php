@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Header Section -->
<div class="bg-white shadow-sm p-4 mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600">PT. DADS Duta Anugrah Damai Sejahtera - Asset Management System</p>
        </div>
        @if(Auth::user()->role === 'admin')
        <div class="flex gap-2">
            <button id="add-asset-btn"
                    class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 btn-action">
                <i class="fas fa-plus mr-2"></i>Tambah Aset
            </button>
            <button id="export-csv-btn"
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 btn-action">
                <i class="fas fa-download mr-2"></i>Export CSV
            </button>
        </div>
        @endif
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 px-6 mb-6">
    <!-- Total Assets Card -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
        <div class="flex items-center">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600">Total Aset</p>
                <p id="total-assets" class="text-3xl font-bold text-gray-900">{{ $totalAset ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-boxes text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Value Card -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
        <div class="flex items-center">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600">Total Nilai</p>
                <p id="total-value" class="text-3xl font-bold text-gray-900">Rp {{ number_format($totalNilai ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Available Assets Card -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
        <div class="flex items-center">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600">Tersedia</p>
                <p id="available-assets" class="text-3xl font-bold text-gray-900">{{ $tersedia ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Used Assets Card -->
    <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
        <div class="flex items-center">
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-600">Terpakai</p>
                <p id="used-assets" class="text-3xl font-bold text-gray-900">{{ $terpakai ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Section - Repositioned -->
<div class="px-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>Quick Actions
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="#" onclick="document.getElementById('add-asset-btn').click(); return false;"
               class="group flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-blue-500 hover:bg-blue-50 transition-all">
                <div class="w-12 h-12 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-plus text-blue-600"></i>
                </div>
                <h4 class="font-medium text-gray-900 text-center">Tambah Aset</h4>
                <p class="text-xs text-gray-500 mt-1 text-center">Daftarkan aset baru</p>
            </a>

            <a href="{{ route('assets.index') }}"
               class="group flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-purple-500 hover:bg-purple-50 transition-all">
                <div class="w-12 h-12 bg-purple-100 group-hover:bg-purple-200 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-list text-purple-600"></i>
                </div>
                <h4 class="font-medium text-gray-900 text-center">Lihat Semua Aset</h4>
                <p class="text-xs text-gray-500 mt-1 text-center">Browse dan kelola aset</p>
            </a>

            <a href="{{ route('assets.vehicles') }}"
               class="group flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-green-500 hover:bg-green-50 transition-all">
                <div class="w-12 h-12 bg-green-100 group-hover:bg-green-200 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-car text-green-600"></i>
                </div>
                <h4 class="font-medium text-gray-900 text-center">Kelola Kendaraan</h4>
                <p class="text-xs text-gray-500 mt-1 text-center">Management kendaraan</p>
            </a>

            <a href="{{ route('assets.export') }}"
               class="group flex flex-col items-center p-4 border border-gray-200 rounded-lg hover:border-yellow-500 hover:bg-yellow-50 transition-all">
                <div class="w-12 h-12 bg-yellow-100 group-hover:bg-yellow-200 rounded-lg flex items-center justify-center mb-3">
                    <i class="fas fa-download text-yellow-600"></i>
                </div>
                <h4 class="font-medium text-gray-900 text-center">Export Data</h4>
                <p class="text-xs text-gray-500 mt-1 text-center">Download laporan Excel</p>
            </a>
        </div>
    </div>
</div>

<!-- Overview Stats Section -->
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 px-6 mb-6">
    <!-- Ringkasan Global -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-4">Ringkasan Global</h3>
        <div class="space-y-4">
            <div>
                <p class="text-sm text-gray-600">Total Nilai Aset</p>
                <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalNilai ?? 1000656000, 0, ',', '.') }}</p>
            </div>
            <div class="grid grid-cols-3 gap-4 text-center">
                <div>
                    <p class="text-sm text-gray-600">Total Aset</p>
                    <p class="text-xl font-bold text-gray-900">{{ $totalAset ?? 7 }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Terpakai</p>
                    <p class="text-xl font-bold text-red-600">{{ $terpakai ?? 5 }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Tersedia</p>
                    <p class="text-xl font-bold text-green-600">{{ $tersedia ?? 2 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Aset per Project Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-4">Aset per Project</h3>
        <div class="relative" style="height: 200px;">
            <canvas id="project-pie-chart"></canvas>
        </div>
    </div>

    <!-- Aset per Jenis Chart -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-4">Aset per Jenis</h3>
        <div class="relative" style="height: 200px;">
            <canvas id="type-bar-chart"></canvas>
        </div>
    </div>

    <!-- Rincian per Lokasi -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-500 mb-4">Rincian per Lokasi</h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Bandung</span>
                <div class="text-right">
                    <span class="text-sm font-medium">3 Aset</span>
                    <span class="text-xs text-gray-500 block">Rp 637.858.000</span>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Indramayu</span>
                <div class="text-right">
                    <span class="text-sm font-medium">2 Aset</span>
                    <span class="text-xs text-gray-500 block">Rp 295.798.000</span>
                </div>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Lampung</span>
                <div class="text-right">
                    <span class="text-sm font-medium">2 Aset</span>
                    <span class="text-xs text-gray-500 block">Rp 67.000.000</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters and Search Section -->
<div class="px-6 mb-4">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
            <!-- Search PIC -->
            <div>
                <input type="text" id="search-pic" placeholder="Cari PIC"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Filter Tipe -->
            <div>
                <select id="filter-tipe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Tipe</option>
                    <option value="Mobil">Mobil</option>
                    <option value="Laptop">Laptop</option>
                    <option value="Core Alignment">Core Alignment</option>
                    <option value="Cladding Alignment">Cladding Alignment</option>
                </select>
            </div>

            <!-- Filter Project -->
            <div>
                <select id="filter-project" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Project</option>
                    <option value="Head Office">Head Office</option>
                    <option value="Moratel">Moratel</option>
                    <option value="EMR">EMR</option>
                </select>
            </div>

            <!-- Filter Lokasi -->
            <div>
                <select id="filter-lokasi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Lokasi</option>
                    <option value="Bandung">Bandung</option>
                    <option value="Indramayu">Indramayu</option>
                    <option value="Lampung">Lampung</option>
                </select>
            </div>

            <!-- Filter Jenis -->
            <div>
                <select id="filter-jenis" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Jenis</option>
                    <option value="Elektronik">Elektronik</option>
                    <option value="Kendaraan">Kendaraan</option>
                    <option value="Alat">Alat</option>
                </select>
            </div>
        </div>

        <div class="flex justify-end">
            <button id="reset-filters" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">
                Reset
            </button>
        </div>
    </div>
</div>

<!-- Main Assets Table -->
<div class="px-6 mb-6">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="assets-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TIPE</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JENIS ASET</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MERK</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NOMOR SN</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PROJECT</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">LOKASI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TAHUN BELI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">UMUR</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">HARGA BELI</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AKSI</th>
                    </tr>
                </thead>
                <tbody id="asset-table-body" class="bg-white divide-y divide-gray-200">
                    @if(isset($assets) && $assets->count() > 0)
                        @foreach($assets as $asset)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->tipe ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->jenis_aset ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->pic ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->merk ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->nomor_sn ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->project ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->lokasi ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->tanggal_beli ? \Carbon\Carbon::parse($asset->tanggal_beli)->format('d M Y') : '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($asset->tahun_beli)
                                    {{ date('Y') - $asset->tahun_beli }} tahun
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $asset->harga_beli ? 'Rp ' . number_format($asset->harga_beli, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <button onclick="viewAsset({{ $asset->id }})"
                                            class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if(Auth::user()->role === 'admin')
                                    <button onclick="editAsset({{ $asset->id }})"
                                            class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteAsset({{ $asset->id }})"
                                            class="text-red-600 hover:text-red-900" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="11" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-inbox text-4xl text-gray-300 mb-2"></i>
                                    <p class="text-lg font-medium">Tidak ada data aset</p>
                                    <p class="text-sm">Tambahkan aset untuk melihat data di sini</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div id="pagination-controls" class="px-6 py-4 flex items-center justify-between border-t border-gray-200 bg-gray-50">
            <div class="flex-1 flex justify-between sm:hidden">
                <button id="prev-btn-mobile" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Previous
                </button>
                <button id="next-btn-mobile" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Next
                </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700">
                        Showing <span id="start-item">1</span> to <span id="end-item">10</span> of <span id="total-items">{{ $assets->count() ?? 0 }}</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <button id="prev-btn" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <div id="page-numbers" class="flex">
                            <!-- Page numbers will be inserted here -->
                        </div>
                        <button id="next-btn" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Daftar Aset</h3>

        <!-- Search and Filter Controls -->
        <div class="mb-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <div class="md:col-span-2">
                    <input type="text" id="search-pic" placeholder="Cari berdasarkan PIC..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500">
                </div>
                <select id="filter-tipe" class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="semua">Semua Tipe</option>
                </select>
                <select id="filter-project" class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="semua">Semua Project</option>
                </select>
                <select id="filter-lokasi" class="px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500">
                    <option value="semua">Semua Lokasi</option>
                </select>
                <button id="reset-filters-btn" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600">
                    Reset
                </button>
            </div>
        </div>

        <!-- Assets Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead id="table-header" class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-column="jenisAset">
                            Jenis Aset
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-column="pic">
                            PIC
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-column="merk">
                            Merk
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-column="nomorSn">
                            Nomor SN
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-column="project">
                            Project
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-column="lokasi">
                            Lokasi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-column="tahunBeli">
                            Tahun
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sortable" data-column="hargaBeli">
                            Harga Beli
                        </th>
                        <th id="pajak-header" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="display: none;">
                            Pajak
                        </th>
                        <th id="servis-header" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider" style="display: none;">
                            Total Servis
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody id="asset-table-body" class="bg-white divide-y divide-gray-200">
                    <!-- Table content will be populated by JavaScript -->
                </tbody>
            </table>
            <div id="no-data-message" class="text-center py-8 text-gray-500" style="display: none;">
                <i class="fas fa-inbox text-4xl mb-3"></i>
                <p>Tidak ada data yang ditemukan.</p>
            </div>
        </div>

        <!-- Pagination -->
        <div id="pagination-controls" class="mt-6 flex justify-between items-center">
            <!-- Pagination will be populated by JavaScript -->
        </div>
    </div>
</div><!-- Asset Form Modal -->
<div id="asset-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-10 mx-auto p-5 border w-full max-w-4xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg text-center leading-6 font-medium text-gray-900 mb-4" id="modal-title">Tambah Aset Baru</h3>
            <form id="asset-form" class="space-y-6">
                <input type="hidden" id="asset-id">

                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                        <select id="modal-tipe" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500 modal-select" data-name="Tipe">
                            <option value="">Pilih Tipe</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Aset</label>
                        <select id="modal-jenisAset" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500 modal-select" data-name="Jenis Aset">
                            <option value="">Pilih Jenis Aset</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">PIC</label>
                        <select id="modal-pic" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500 modal-select" data-name="PIC">
                            <option value="">Pilih PIC</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Merk</label>
                        <input type="text" id="modal-merk" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Masukkan merk">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor SN</label>
                        <input type="text" id="modal-nomorSn" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="Masukkan nomor SN">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                        <select id="modal-project" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500 modal-select" data-name="Project">
                            <option value="">Pilih Project</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                        <select id="modal-lokasi" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500 modal-select" data-name="Lokasi">
                            <option value="">Pilih Lokasi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Beli</label>
                        <input type="number" id="modal-tahunBeli" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="2024" min="1980" max="2030">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Beli</label>
                        <input type="number" id="modal-hargaBeli" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="0" min="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Harga Sewa</label>
                        <input type="number" id="modal-hargaSewa" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="0" min="0">
                    </div>
                </div>

                <!-- Extended fields for Kendaraan and Splicer -->
                <div id="extended-fields" class="hidden pt-4 border-t">
                    <!-- Pajak Fields (for Kendaraan only) -->
                    <div id="pajak-fields" class="hidden">
                        <h4 class="text-md font-semibold text-gray-700 mb-4">Informasi Pajak</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pajak</label>
                                <input type="date" id="modal-pajak-tanggal" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Pajak</label>
                                <input type="number" id="modal-pajak-jumlah" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500" placeholder="0" min="0">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status Pajak</label>
                                <select id="modal-pajak-status" class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                    <option value="Belum Lunas">Belum Lunas</option>
                                    <option value="Lunas">Lunas</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Service History Fields (for Kendaraan and Splicer) -->
                    <div id="servis-fields" class="hidden mt-6">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="text-md font-semibold text-gray-700">Riwayat Servis</h4>
                            <button type="button" id="add-service-history-btn" class="px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors">
                                Tambah Riwayat
                            </button>
                        </div>
                        <div id="service-history-container" class="space-y-3">
                            <!-- Service history items will be added here -->
                        </div>
                    </div>
                </div>
            </form>
            <div class="flex justify-center gap-3 pt-6 border-t mt-6">
                <button id="save-asset-btn" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors btn-action">
                    Simpan
                </button>
                <button id="cancel-btn" class="px-6 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors btn-action">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="detail-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3 border-b">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="detail-modal-title">Detail Aset</h3>
            <button id="close-detail-modal-btn" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mt-4 space-y-6">
            <div id="detail-pajak-section" class="hidden">
                <h4 class="text-md font-semibold text-gray-700 mb-2">Informasi Pajak</h4>
                <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                    <p class="text-gray-500">Tanggal Pajak:</p>
                    <p id="detail-pajak-tanggal" class="font-medium text-gray-800"></p>
                    <p class="text-gray-500">Jumlah Pajak:</p>
                    <p id="detail-pajak-jumlah" class="font-medium text-gray-800"></p>
                    <p class="text-gray-500">Status Pajak:</p>
                    <div id="detail-pajak-status"></div>
                </div>
            </div>
            <div id="detail-servis-section" class="hidden">
                <h4 class="text-md font-semibold text-gray-700 mb-2">Riwayat Servis Lengkap</h4>
                <div id="detail-servis-list" class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar border-t pt-2 mt-1 pr-2">
                </div>
            </div>
        </div>
        <div class="border-t pt-4 mt-6 flex justify-end gap-3">
            <button id="export-pajak-btn" class="hidden px-4 py-2 bg-blue-500 text-white text-sm rounded-md hover:bg-blue-600 btn-action">
                Ekspor Pajak (CSV)
            </button>
            <button id="export-servis-btn" class="hidden px-4 py-2 bg-green-500 text-white text-sm rounded-md hover:bg-green-600 btn-action">
                Ekspor Servis (CSV)
            </button>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirm-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-40 mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4" id="confirm-title">Hapus Aset</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Apakah Anda yakin ingin menghapus aset ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="flex justify-center gap-3 mt-4">
                <button id="confirm-delete-btn" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors btn-action">
                    Ya, Hapus
                </button>
                <button id="cancel-delete-btn" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors btn-action">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loader -->
<div id="loader" style="display: none; position: fixed; top: 20px; right: 20px; border: 4px solid #e5e7eb; border-top: 4px solid #ef4444; border-radius: 50%; width: 30px; height: 30px; animation: spin 1s linear infinite; z-index: 100;">
</div>

<!-- Toast Notification -->
<div id="toast" style="position: fixed; bottom: -100px; left: 50%; transform: translateX(-50%); padding: 12px 24px; border-radius: 9999px; font-weight: 500; transition: all 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55); z-index: 100;">
</div>
    </div>

    <!-- Location Summary Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>Ringkasan per Lokasi
                </h3>
                <div class="flex space-x-2">
                    <button onclick="exportLocationData()"
                            class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors text-sm">
                        <i class="fas fa-download mr-1"></i>Export
                    </button>
                    <button onclick="refreshLocationData()"
                            class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                        <i class="fas fa-sync-alt mr-1"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-map-pin mr-1"></i>Lokasi
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-boxes mr-1"></i>Jumlah Asset
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-dollar-sign mr-1"></i>Total Nilai
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <i class="fas fa-chart-bar mr-1"></i>Persentase
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @if(isset($lokasiSummary) && count($lokasiSummary) > 0)
                        @foreach($lokasiSummary as $lokasi)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-building text-indigo-600"></i>
                                        </div>
                                        <span class="font-medium text-gray-900">{{ $lokasi->lokasi }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $lokasi->jumlah }} items
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <div class="font-semibold text-gray-900">Rp {{ number_format($lokasi->total, 0, ',', '.') }}</div>
                                        <div class="text-gray-500">Rata-rata: Rp {{ $lokasi->jumlah > 0 ? number_format($lokasi->total / $lokasi->jumlah, 0, ',', '.') : 0 }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full transition-all duration-1000"
                                                 style="width: {{ $totalNilai > 0 ? ($lokasi->total / $totalNilai) * 100 : 0 }}%"></div>
                                        </div>
                                        <span class="text-xs text-gray-600 min-w-max">{{ $totalNilai > 0 ? round(($lokasi->total / $totalNilai) * 100, 1) : 0 }}%</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('assets.index') }}?lokasi={{ urlencode($lokasi->lokasi) }}"
                                       class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        <i class="fas fa-eye mr-1"></i>Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-map-marker-alt text-4xl mb-3"></i>
                                    <p class="text-lg font-medium">Belum ada data lokasi</p>
                                    <p class="text-sm">Tambahkan asset untuk melihat distribusi lokasi</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Loading Spinner -->
<div id="loader" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-6 rounded-lg text-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
            <p class="text-gray-600">Loading...</p>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-full transition-transform duration-300 z-50">
    <div class="flex items-center">
        <i class="fas fa-check-circle mr-2"></i>
        <span id="toast-message">Pesan</span>
    </div>
</div>

<!-- Modal Tambah Aset -->
<div id="asset-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-screen overflow-y-auto">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 text-center">Tambah Aset Baru</h2>

                <form id="asset-form" method="POST" action="{{ route('assets.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Tipe -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                            <select name="tipe" id="modal-tipe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Pilih Tipe</option>
                                <option value="Elektronik">Elektronik</option>
                                <option value="Kendaraan">Kendaraan</option>
                                <option value="Alat">Alat</option>
                            </select>
                        </div>

                        <!-- Jenis Aset -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Aset</label>
                            <select name="jenis_aset" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Pilih Jenis Aset</option>
                                <option value="Cladding Alignment">Cladding Alignment</option>
                                <option value="Core Alignment">Core Alignment</option>
                                <option value="Laptop">Laptop</option>
                                <option value="Mobil">Mobil</option>
                            </select>
                        </div>

                        <!-- PIC -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PIC</label>
                            <select name="pic" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Pilih PIC</option>
                                <option value="Available">Available</option>
                                <option value="John Doe">John Doe</option>
                                <option value="Jane Smith">Jane Smith</option>
                            </select>
                        </div>

                        <!-- Merk -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Merk</label>
                            <input type="text" name="merk" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <!-- Nomor SN -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor SN</label>
                            <input type="text" name="serial_number" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Project -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                            <select name="project" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Pilih Project</option>
                                <option value="EMR">EMR</option>
                                <option value="Head Office">Head Office</option>
                                <option value="Moratel">Moratel</option>
                            </select>
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                            <select name="lokasi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Pilih Lokasi</option>
                                <option value="Bandung">Bandung</option>
                                <option value="Indramayu">Indramayu</option>
                                <option value="Lampung">Lampung</option>
                            </select>
                        </div>

                        <!-- Tahun Beli -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Beli</label>
                            <input type="date" name="tanggal_beli" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Harga Beli -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli</label>
                            <input type="number" name="harga_beli" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Keterangan -->
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan</label>
                            <textarea name="keterangan" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan keterangan tambahan (opsional)"></textarea>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-center gap-4 mt-6">
                        <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            Simpan
                        </button>
                        <button type="button" id="cancel-btn" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded'); // Debug log

    // --- DOM ELEMENTS ---
    const modal = document.getElementById('asset-modal');
    const addAssetBtn = document.getElementById('add-asset-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const assetForm = document.getElementById('asset-form');

    console.log('Elements found:', { modal: !!modal, addAssetBtn: !!addAssetBtn, cancelBtn: !!cancelBtn, assetForm: !!assetForm }); // Debug log

    // --- MODAL FUNCTIONS ---
    const showModal = () => {
        console.log('showModal called');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            console.log('Modal should be visible now');
        } else {
            console.log('Modal element not found!');
        }
    };

    const hideModal = () => {
        console.log('hideModal called');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            // Reset form
            if (assetForm) assetForm.reset();
        }
    };

    // --- EVENT LISTENERS ---
    if (addAssetBtn) {
        addAssetBtn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Add asset button clicked');
            showModal();
        });
        console.log('Add asset button listener attached');
    } else {
        console.log('Add asset button not found!');
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function(e) {
            e.preventDefault();
            hideModal();
        });
    }

    // Close modal when clicking outside
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                hideModal();
            }
        });
    }

    // Handle form submission
    assetForm?.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(assetForm);
        const submitBtn = assetForm.querySelector('button[type="submit"]');

        // Show loading state
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';
        }

        try {
            const response = await fetch(assetForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            if (response.ok) {
                showToast('Aset berhasil ditambahkan!', true);
                hideModal();
                // Reload page to show new data
                setTimeout(() => window.location.reload(), 1000);
            } else {
                throw new Error('Gagal menyimpan data');
            }
        } catch (error) {
            showToast('Terjadi kesalahan: ' + error.message, false);
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Simpan';
            }
        }
    });

    // --- UTILITY & UI FUNCTIONS ---
    const showToast = (message, isSuccess = true) => {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toast-message');
        const icon = toast.querySelector('i');

        if (toast && toastMessage) {
            toastMessage.textContent = message;
            toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg transform transition-transform duration-300 z-50 ${
                isSuccess ? 'bg-green-500' : 'bg-red-500'
            } text-white`;
            icon.className = isSuccess ? 'fas fa-check-circle mr-2' : 'fas fa-exclamation-circle mr-2';

            // Show toast
            toast.style.transform = 'translateY(0)';

            // Hide toast after 3 seconds
            setTimeout(() => {
                toast.style.transform = 'translateY(100%)';
            }, 3000);
        }
    };

    const showLoader = () => {
        if (loader) loader.style.display = 'block';
    };

    const hideLoader = () => {
        if (loader) loader.style.display = 'none';
    };

    const formatCurrency = (number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    };

    // --- MODAL FUNCTIONS ---
    const showDetailModal = (id) => {
        currentDetailAssetId = parseInt(id);
        const asset = assets.find(a => a.id === currentDetailAssetId);
        if (!asset) return;

        document.getElementById('detail-modal-title').textContent = `Detail Aset: ${asset.merk || 'N/A'}`;

        const detailPajakSection = document.getElementById('detail-pajak-section');
        const detailServisSection = document.getElementById('detail-servis-section');

        detailPajakSection.classList.add('hidden');
        detailServisSection.classList.add('hidden');
        exportPajakBtn.classList.add('hidden');
        exportServisBtn.classList.add('hidden');

        if (asset.tipe === 'Kendaraan' && asset.pajak) {
            detailPajakSection.classList.remove('hidden');
            exportPajakBtn.classList.remove('hidden');
            document.getElementById('detail-pajak-tanggal').textContent = asset.pajak.tanggal ?
                new Date(asset.pajak.tanggal).toLocaleDateString('id-ID', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                }) : 'N/A';
            document.getElementById('detail-pajak-jumlah').textContent = asset.pajak.jumlah ?
                formatCurrency(asset.pajak.jumlah) : 'N/A';
            const statusEl = document.getElementById('detail-pajak-status');
            const statusClass = asset.pajak.status === 'Lunas' ?
                'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
            statusEl.innerHTML = `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">${asset.pajak.status}</span>`;
        }

        if ((asset.tipe === 'Kendaraan' || asset.tipe === 'Splicer') && asset.servis) {
            detailServisSection.classList.remove('hidden');
            if(asset.servis.length > 0) exportServisBtn.classList.remove('hidden');

            const servisListEl = document.getElementById('detail-servis-list');
            servisListEl.innerHTML = '';
            if (asset.servis.length > 0) {
                asset.servis.forEach((servis, index) => {
                    const servisItem = document.createElement('div');
                    servisItem.className = 'bg-gray-50 p-3 rounded-lg';
                    servisItem.innerHTML = `
                        <div class="flex justify-between items-start mb-2">
                            <p class="font-medium text-gray-900">${servis.keterangan || 'N/A'}</p>
                            <span class="text-xs text-gray-500">${servis.tanggal ? new Date(servis.tanggal).toLocaleDateString('id-ID') : 'N/A'}</span>
                        </div>
                        <div class="text-sm text-gray-600">
                            <p>Biaya: ${servis.biaya ? formatCurrency(servis.biaya) : 'N/A'}</p>
                            <p>Bengkel: ${servis.bengkel || 'N/A'}</p>
                            ${servis.bukti ? `<p>Bukti: <span class="text-blue-600">${servis.bukti}</span></p>` : ''}
                        </div>
                    `;
                    servisListEl.appendChild(servisItem);
                });
            } else {
                servisListEl.innerHTML = '<p class="text-sm text-gray-500 italic">Belum ada riwayat servis.</p>';
            }
        }

        detailModal.classList.remove('hidden');
    };

    const hideDetailModal = () => {
        currentDetailAssetId = null;
        detailModal.classList.add('hidden');
    };

    const showModal = (asset = null) => {
        assetForm.reset();
        document.getElementById('asset-id').value = asset ? asset.id : '';
        modalTitle.textContent = asset ? 'Ubah Aset' : 'Tambah Aset Baru';

        populateModalSelects(asset);

        if (asset) {
            Object.keys(asset).forEach(key => {
                const el = document.getElementById(`modal-${key}`);
                if (el) el.value = asset[key];
            });
        }

        updateModalFieldsVisibility(asset ? asset.tipe : modalTipeSelect.value, asset);
        modal.classList.remove('hidden');
    };

    const hideModal = () => modal.classList.add('hidden');

    // --- DATA RENDERING ---
    const addTableActionListeners = (tbody) => {
        tbody.querySelectorAll('.edit-btn').forEach(b =>
            b.addEventListener('click', (e) =>
                showModal(assets.find(a => a.id == e.currentTarget.dataset.id))
            )
        );
        tbody.querySelectorAll('.delete-btn').forEach(b =>
            b.addEventListener('click', (e) => {
                assetIdToDelete = e.currentTarget.dataset.id;
                confirmModal.classList.remove('hidden');
            })
        );
        tbody.querySelectorAll('.detail-btn').forEach(b =>
            b.addEventListener('click', (e) => showDetailModal(e.currentTarget.dataset.id))
        );
    };

    const createActionButtonsHTML = (asset) => {
        let html = '';
        if (asset.tipe === 'Kendaraan' || asset.tipe === 'Splicer') {
            html += `<button class="text-blue-600 hover:text-blue-900 detail-btn p-1 rounded-md hover:bg-blue-100 btn-action" title="Lihat Detail" data-id="${asset.id}">
                <i class="fas fa-eye"></i>
            </button>`;
        }
        if (currentUserRole === 'admin') {
            const adminMargin = html ? 'ml-2' : '';
            html += `<button class="text-indigo-600 hover:text-indigo-900 ${adminMargin} edit-btn p-1 rounded-md hover:bg-indigo-100 btn-action" title="Ubah Aset" data-id="${asset.id}">
                <i class="fas fa-edit"></i>
            </button>`;
            html += `<button class="text-red-600 hover:text-red-900 ml-2 delete-btn p-1 rounded-md hover:bg-red-100 btn-action" title="Hapus Aset" data-id="${asset.id}">
                <i class="fas fa-trash"></i>
            </button>`;
        }
        return `<td class="px-4 py-3 whitespace-nowrap text-sm font-medium"><div class="flex items-center gap-1">${html}</div></td>`;
    };

    const renderTablePage = () => {
        const showPajakColumn = filterTipe.value === 'Kendaraan';
        const showServisColumn = filterTipe.value === 'Kendaraan' || filterTipe.value === 'Splicer';

        document.getElementById('pajak-header').style.display = showPajakColumn ? '' : 'none';
        document.getElementById('servis-header').style.display = showServisColumn ? '' : 'none';

        tableBody.innerHTML = '';
        const start = (currentPage - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        const paginatedAssets = filteredAssets.slice(start, end);

        noDataMessage.style.display = paginatedAssets.length === 0 ? 'block' : 'none';
        tableBody.style.display = paginatedAssets.length === 0 ? 'none' : '';

        paginatedAssets.forEach(asset => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';

            const actionCell = createActionButtonsHTML(asset);
            const picCell = asset.pic && asset.pic.toLowerCase() === 'available' ?
                `<td class="px-4 py-3"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Available</span></td>` :
                `<td class="px-4 py-3">${asset.pic || 'N/A'}</td>`;

            let extendedColumnsHTML = '';
            if (showPajakColumn && asset.pajak) {
                const statusClass = asset.pajak.status === 'Lunas' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                extendedColumnsHTML += `<td class="px-4 py-3"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">${asset.pajak.status || 'N/A'}</span></td>`;
            } else if (showPajakColumn) {
                extendedColumnsHTML += `<td class="px-4 py-3">N/A</td>`;
            }

            if (showServisColumn) {
                const totalServis = (asset.servis || []).reduce((sum, s) => sum + (s.biaya || 0), 0);
                extendedColumnsHTML += `<td class="px-4 py-3">${totalServis > 0 ? formatCurrency(totalServis) : '-'}</td>`;
            }

            row.innerHTML = `
                <td class="px-4 py-3 font-medium">${asset.jenisAset || 'N/A'}</td>
                ${picCell}
                <td class="px-4 py-3">${asset.merk || 'N/A'}</td>
                <td class="px-4 py-3">${asset.nomorSn || 'N/A'}</td>
                <td class="px-4 py-3">${asset.project || 'N/A'}</td>
                <td class="px-4 py-3">${asset.lokasi || 'N/A'}</td>
                <td class="px-4 py-3">${asset.tahunBeli || 'N/A'}</td>
                <td class="px-4 py-3">${asset.hargaBeli ? formatCurrency(asset.hargaBeli) : 'N/A'}</td>
                ${extendedColumnsHTML}
                ${actionCell}
            `;
            tableBody.appendChild(row);
        });
        addTableActionListeners(tableBody);
        renderPagination();
    };

    const renderPagination = () => {
        paginationControls.innerHTML = '';
        const pageCount = Math.ceil(filteredAssets.length / rowsPerPage);
        if (pageCount <= 1) return;

        paginationControls.innerHTML = `
            <p class="text-sm text-gray-700">
                Menampilkan <span>${(currentPage - 1) * rowsPerPage + 1}</span>-<span>${Math.min(currentPage * rowsPerPage, filteredAssets.length)}</span> dari <span>${filteredAssets.length}</span>
            </p>
            <div>
                <button id="prev-page" class="px-4 py-2 border rounded-md btn-action" ${currentPage === 1 ? 'disabled' : ''}>
                    Prev
                </button>
                <button id="next-page" class="ml-3 px-4 py-2 border rounded-md btn-action" ${currentPage === pageCount ? 'disabled' : ''}>
                    Next
                </button>
            </div>
        `;

        document.getElementById('prev-page')?.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderTablePage();
            }
        });

        document.getElementById('next-page')?.addEventListener('click', () => {
            if (currentPage < pageCount) {
                currentPage++;
                renderTablePage();
            }
        });
    };

    // --- CHART & DASHBOARD FUNCTIONS ---
    const updateDashboard = (data) => {
        const totalValue = data.reduce((sum, asset) => sum + (asset.hargaBeli || 0), 0);
        const availableData = data.filter(asset => asset.pic && asset.pic.toLowerCase() === 'available');
        const usedData = data.filter(asset => !asset.pic || asset.pic.toLowerCase() !== 'available');

        document.getElementById('total-assets').textContent = data.length;
        document.getElementById('total-value').textContent = formatCurrency(totalValue);
        document.getElementById('available-assets').textContent = availableData.length;
        document.getElementById('used-assets').textContent = usedData.length;

        const projectCounts = data.reduce((acc, asset) => {
            if (asset.project) {
                acc[asset.project] = (acc[asset.project] || 0) + 1;
            }
            return acc;
        }, {});
        updateChart(projectChart, Object.keys(projectCounts), Object.values(projectCounts));

        const typeCounts = data.reduce((acc, asset) => {
            if (asset.jenisAset) {
                acc[asset.jenisAset] = (acc[asset.jenisAset] || 0) + 1;
            }
            return acc;
        }, {});
        updateChart(typeChart, Object.keys(typeCounts), Object.values(typeCounts));

        const locationData = data.reduce((acc, asset) => {
            if (asset.lokasi) {
                if (!acc[asset.lokasi]) acc[asset.lokasi] = { count: 0, value: 0 };
                acc[asset.lokasi].count++;
                acc[asset.lokasi].value += asset.hargaBeli || 0;
            }
            return acc;
        }, {});

        const locationBreakdownEl = document.getElementById('location-breakdown');
        if (locationBreakdownEl) {
            locationBreakdownEl.innerHTML = Object.keys(locationData).length > 0 ?
                Object.entries(locationData).map(([loc, val]) => `
                    <div class="flex justify-between items-center text-sm">
                        <p class="font-medium">${loc}</p>
                        <p>${val.count} Aset | ${formatCurrency(val.value)}</p>
                    </div>
                `).join('') :
                `<p class="text-sm text-gray-500">Tidak ada data.</p>`;
        }
    };

    const createCharts = () => {
        if (projectChart) projectChart.destroy();
        if (typeChart) typeChart.destroy();

        // Project Pie Chart
        const projectPieCtx = document.getElementById('project-pie-chart');
        if (projectPieCtx) {
            projectChart = new Chart(projectPieCtx.getContext('2d'), {
                type: 'pie',
                data: {
                    labels: ['Head Office', 'Moratel', 'EMR'],
                    datasets: [{
                        data: [40, 35, 25],
                        backgroundColor: ['#3b82f6', '#f59e0b', '#10b981'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                font: { size: 11 },
                                padding: 10
                            }
                        }
                    }
                }
            });
        }

        // Type Bar Chart
        const typeBarCtx = document.getElementById('type-bar-chart');
        if (typeBarCtx) {
            typeChart = new Chart(typeBarCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: ['Mobil', 'Core Alignment', 'Cladding Alignment', 'Laptop'],
                    datasets: [{
                        data: [3, 1, 1, 2],
                        backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y',
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: 3,
                            ticks: { stepSize: 1 }
                        },
                        y: {
                            ticks: {
                                font: { size: 10 }
                            }
                        }
                    }
                }
            });
        }
    };

    const updateChart = (chart, labels, data) => {
        if (chart) {
            chart.data.labels = labels;
            chart.data.datasets[0].data = data;
            chart.update();
        }
    };

    // --- FILTER AND SEARCH FUNCTIONALITY ---
    const initFilters = () => {
        const searchInput = document.getElementById('search-pic');
        const filterTipe = document.getElementById('filter-tipe');
        const filterProject = document.getElementById('filter-project');
        const filterLokasi = document.getElementById('filter-lokasi');
        const filterJenis = document.getElementById('filter-jenis');
        const resetBtn = document.getElementById('reset-filters');

        // Filter function
        const applyFilters = () => {
            const searchTerm = searchInput?.value.toLowerCase() || '';
            const tipeFilter = filterTipe?.value || '';
            const projectFilter = filterProject?.value || '';
            const lokasiFilter = filterLokasi?.value || '';
            const jenisFilter = filterJenis?.value || '';

            const rows = document.querySelectorAll('#asset-table-body tr:not(.no-data)');
            let visibleCount = 0;

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length < 10) return;

                const tipe = cells[0].textContent.trim();
                const jenisAset = cells[1].textContent.trim();
                const pic = cells[2].textContent.trim();
                const project = cells[5].textContent.trim();
                const lokasi = cells[6].textContent.trim();

                const matchesSearch = !searchTerm || pic.toLowerCase().includes(searchTerm);
                const matchesTipe = !tipeFilter || tipe === tipeFilter;
                const matchesProject = !projectFilter || project === projectFilter;
                const matchesLokasi = !lokasiFilter || lokasi === lokasiFilter;
                const matchesJenis = !jenisFilter || jenisAset === jenisFilter;

                const shouldShow = matchesSearch && matchesTipe && matchesProject && matchesLokasi && matchesJenis;

                row.style.display = shouldShow ? '' : 'none';
                if (shouldShow) visibleCount++;
            });

            // Update pagination info
            const totalItems = document.getElementById('total-items');
            if (totalItems) totalItems.textContent = visibleCount;
        };

        // Attach event listeners
        searchInput?.addEventListener('input', applyFilters);
        filterTipe?.addEventListener('change', applyFilters);
        filterProject?.addEventListener('change', applyFilters);
        filterLokasi?.addEventListener('change', applyFilters);
        filterJenis?.addEventListener('change', applyFilters);

        // Reset filters
        resetBtn?.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            if (filterTipe) filterTipe.value = '';
            if (filterProject) filterProject.value = '';
            if (filterLokasi) filterLokasi.value = '';
            if (filterJenis) filterJenis.value = '';
            applyFilters();
        });
    };

    // --- TABLE ACTIONS ---
    window.viewAsset = (id) => {
        // Implementation for view asset detail
        console.log('View asset:', id);
        showToast('Fitur detail akan segera tersedia', false);
    };

    window.editAsset = (id) => {
        // Implementation for edit asset
        console.log('Edit asset:', id);
        showToast('Fitur edit akan segera tersedia', false);
    };

    window.deleteAsset = (id) => {
        if (confirm('Apakah Anda yakin ingin menghapus aset ini?')) {
            console.log('Delete asset:', id);
            showToast('Fitur hapus akan segera tersedia', false);
        }
    };

    const processData = () => {
        showLoader();
        setTimeout(() => {
            const sQ = searchPicInput ? searchPicInput.value.toLowerCase() : '';
            const tF = filterTipe ? filterTipe.value : 'semua';
            const pF = filterProject ? filterProject.value : 'semua';
            const lF = filterLokasi ? filterLokasi.value : 'semua';
            const jF = filterJenis ? filterJenis.value : 'semua';

            filteredAssets = assets.filter(a =>
                (a.pic || '').toLowerCase().includes(sQ) &&
                (tF === 'semua' || a.tipe === tF) &&
                (pF === 'semua' || a.project === pF) &&
                (lF === 'semua' || a.lokasi === lF) &&
                (jF === 'semua' || a.jenisAset === jF)
            );

            if (currentSort.column) {
                filteredAssets.sort((a, b) => {
                    const aVal = a[currentSort.column] || '';
                    const bVal = b[currentSort.column] || '';
                    const result = aVal > bVal ? 1 : aVal < bVal ? -1 : 0;
                    return currentSort.direction === 'asc' ? result : -result;
                });
            }

            currentPage = 1;
            updateDashboard(filteredAssets);
            renderTablePage();
            hideLoader();
        }, 300);
    };

    // --- CRUD OPERATIONS ---
    const saveAsset = (e) => {
        e.preventDefault();
        const id = document.getElementById('asset-id').value;
        const data = {
            tipe: document.getElementById('modal-tipe').value,
            jenisAset: document.getElementById('modal-jenisAset').value,
            pic: document.getElementById('modal-pic').value,
            merk: document.getElementById('modal-merk').value,
            nomorSn: document.getElementById('modal-nomorSn').value,
            project: document.getElementById('modal-project').value,
            lokasi: document.getElementById('modal-lokasi').value,
            tahunBeli: parseInt(document.getElementById('modal-tahunBeli').value, 10) || 0,
            hargaBeli: parseFloat(document.getElementById('modal-hargaBeli').value) || 0,
            hargaSewa: parseFloat(document.getElementById('modal-hargaSewa').value) || 0
        };

        if (data.tipe === 'Kendaraan') {
            data.pajak = {
                tanggal: document.getElementById('modal-pajak-tanggal').value,
                jumlah: parseFloat(document.getElementById('modal-pajak-jumlah').value) || 0,
                status: document.getElementById('modal-pajak-status').value
            };
        }

        if (data.tipe === 'Kendaraan' || data.tipe === 'Splicer') {
            data.servis = [];
            document.querySelectorAll('.service-history-item').forEach(item => {
                const servis = {
                    tanggal: item.querySelector('.service-date').value,
                    keterangan: item.querySelector('.service-desc').value,
                    biaya: parseFloat(item.querySelector('.service-cost').value) || 0,
                    bengkel: item.querySelector('.service-shop').value,
                    bukti: item.querySelector('.service-proof').textContent || ''
                };
                if (servis.tanggal || servis.keterangan) {
                    data.servis.push(servis);
                }
            });
        }

        if (id) {
            const index = assets.findIndex(a => a.id === parseInt(id));
            if (index !== -1) {
                assets[index] = { ...assets[index], ...data };
                showToast('Data aset berhasil diperbarui!');
            }
        } else {
            data.id = assets.length > 0 ? Math.max(...assets.map(a => a.id)) + 1 : 1;
            if ((data.tipe === 'Kendaraan' || data.tipe === 'Splicer') && !data.servis) {
                data.servis = [];
            }
            assets.push(data);
            showToast('Aset baru berhasil ditambahkan!');
        }

        populateFilters();
        processData();
        hideModal();
    };

    const deleteAsset = () => {
        assets = assets.filter(a => a.id !== parseInt(assetIdToDelete));
        confirmModal.classList.add('hidden');
        populateFilters();
        processData();
        showToast('Aset telah dihapus.', false);
    };

    // --- EXPORT FUNCTIONS ---
    const exportToCSV = () => {
        const headers = ['ID', 'Tipe', 'Jenis Aset', 'PIC', 'Merk', 'Nomor SN', 'Project', 'Lokasi', 'Tahun Beli', 'Harga Beli', 'Harga Sewa'];
        const rows = filteredAssets.map(asset => [
            asset.id, asset.tipe, asset.jenisAset, asset.pic, asset.merk,
            asset.nomorSn, asset.project, asset.lokasi, asset.tahunBeli,
            asset.hargaBeli, asset.hargaSewa
        ]);

        let csvContent = "data:text/csv;charset=utf-8," +
            [headers.join(','), ...rows.map(e => e.join(','))].join('\\n');
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "laporan_aset.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        showToast("Ekspor CSV berhasil!");
    };

    // --- FORM HELPER FUNCTIONS ---
    const populateModalSelects = (selectedAsset = {}) => {
        const populate = (selectEl, key) => {
            if (!selectEl) return;
            const options = [...new Set(assets.map(a => a[key]).filter(val => val))].sort();
            selectEl.innerHTML = '<option value="">Pilih ' + key + '</option>';
            options.forEach(opt => selectEl.add(new Option(opt, opt)));
            if (currentUserRole === 'admin') {
                selectEl.add(new Option('+ Tambah Baru', '__add_new__'));
            }
        };

        ['tipe', 'jenisAset', 'pic', 'project', 'lokasi'].forEach(key => {
            const el = document.getElementById(`modal-${key}`);
            populate(el, key);
        });
    };

    const addServiceHistoryItem = (item = {}) => {
        const div = document.createElement('div');
        div.className = 'service-history-item grid grid-cols-1 md:grid-cols-12 gap-2 items-center p-2 bg-gray-50 rounded';
        div.innerHTML = `
            <div class="md:col-span-2">
                <input type="date" value="${item.tanggal || ''}" class="service-date w-full p-1 border rounded text-sm">
            </div>
            <div class="md:col-span-3">
                <input type="text" value="${item.keterangan || ''}" placeholder="Keterangan" class="service-desc w-full p-1 border rounded text-sm">
            </div>
            <div class="md:col-span-2">
                <input type="number" value="${item.biaya || ''}" placeholder="Biaya" class="service-cost w-full p-1 border rounded text-sm">
            </div>
            <div class="md:col-span-2">
                <input type="text" value="${item.bengkel || ''}" placeholder="Bengkel" class="service-shop w-full p-1 border rounded text-sm">
            </div>
            <div class="md:col-span-2 flex items-center gap-1">
                <span class="service-proof text-xs text-gray-600">${item.bukti || 'No file'}</span>
                <button type="button" class="upload-proof-btn text-blue-500 hover:text-blue-700 text-xs">Upload</button>
            </div>
            <div class="md:col-span-1 text-right">
                <button type="button" class="remove-service-btn text-red-500 hover:text-red-700 text-lg">&times;</button>
            </div>
        `;
        serviceHistoryContainer.appendChild(div);

        div.querySelector('.remove-service-btn').addEventListener('click', () => div.remove());
        div.querySelector('.upload-proof-btn').addEventListener('click', () => {
            const newFilename = prompt("Simulasi upload file. Masukkan nama file:", "bukti.pdf");
            if (newFilename) {
                div.querySelector('.service-proof').textContent = newFilename;
            }
        });
    };

    const updateModalFieldsVisibility = (assetType, asset = null) => {
        extendedFields.classList.toggle('hidden', assetType !== 'Kendaraan' && assetType !== 'Splicer');
        pajakFields.classList.toggle('hidden', assetType !== 'Kendaraan');
        servisFields.classList.toggle('hidden', assetType !== 'Kendaraan' && assetType !== 'Splicer');

        if (assetType === 'Kendaraan') {
            const pajak = asset ? asset.pajak : {};
            document.getElementById('modal-pajak-tanggal').value = pajak?.tanggal || '';
            document.getElementById('modal-pajak-jumlah').value = pajak?.jumlah || '';
            document.getElementById('modal-pajak-status').value = pajak?.status || 'Belum Lunas';
        }

        if (assetType === 'Kendaraan' || assetType === 'Splicer') {
            serviceHistoryContainer.innerHTML = '';
            if (asset && asset.servis) {
                asset.servis.forEach(servis => addServiceHistoryItem(servis));
            }
        }
    };

    // --- EVENT LISTENERS SETUP ---
    if (tableHeader) {
        tableHeader.addEventListener('click', (e) => {
            if (e.target.classList.contains('sortable')) {
                const column = e.target.dataset.column;
                if (currentSort.column === column) {
                    currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.column = column;
                    currentSort.direction = 'asc';
                }
                processData();
            }
        });
    }

    if (currentUserRole === 'admin') {
        addAssetBtn?.addEventListener('click', () => showModal());
        exportCsvBtn?.addEventListener('click', exportToCSV);
        saveAssetBtn?.addEventListener('click', saveAsset);
        addServiceHistoryBtn?.addEventListener('click', () => addServiceHistoryItem());
    }

    modalTipeSelect?.addEventListener('change', (e) => updateModalFieldsVisibility(e.target.value));
    resetFiltersBtn?.addEventListener('click', () => {
        if (searchPicInput) searchPicInput.value = '';
        if (filterTipe) filterTipe.value = 'semua';
        if (filterProject) filterProject.value = 'semua';
        if (filterLokasi) filterLokasi.value = 'semua';
        if (filterJenis) filterJenis.value = 'semua';
        processData();
    });

    cancelBtn?.addEventListener('click', hideModal);
    closeDetailModalBtn?.addEventListener('click', hideDetailModal);
    exportPajakBtn?.addEventListener('click', () => showToast('Export Pajak functionality'));
    exportServisBtn?.addEventListener('click', () => showToast('Export Servis functionality'));
    confirmDeleteBtn?.addEventListener('click', deleteAsset);
    cancelDeleteBtn?.addEventListener('click', () => confirmModal.classList.add('hidden'));

    [searchPicInput, filterTipe, filterProject, filterLokasi, filterJenis].forEach(el => {
        if (el) {
            el.addEventListener('input', processData);
            el.addEventListener('change', processData);
        }
    });

    // --- INITIALIZATION ---
    createCharts();
    initFilters();
    hideLoader();
});

// Add some basic CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .sortable { cursor: pointer; user-select: none; }
    .sortable:hover { background-color: #f3f4f6; }
    .btn-action { transition: all 0.2s ease-in-out; }
    .btn-action:active { transform: scale(0.95); }
`;
document.head.appendChild(style);
</script>
@endpush
@endsection
