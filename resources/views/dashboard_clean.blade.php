@extends('layouts.app')

@section('title', 'Dashboard Assets')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-6">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Asset Management</h1>
            <p class="text-gray-600 mt-2">Kelola asset perusahaan dengan mudah</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Assets</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalAssets }}</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <i class="fas fa-boxes text-blue-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Nilai</p>
                        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalNilai, 0, ',', '.') }}</p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Available</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $availableAssets }}</p>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded-lg">
                        <i class="fas fa-check-circle text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">In Use</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $inUseAssets }}</p>
                    </div>
                    <div class="p-3 bg-red-50 rounded-lg">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Project Distribution Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Project</h3>
                <div style="height: 250px; position: relative;">
                    <canvas id="project-pie-chart"></canvas>
                </div>
            </div>

            <!-- Type Distribution Chart -->
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Tipe Asset</h3>
                <div style="height: 250px; position: relative;">
                    <canvas id="type-bar-chart"></canvas>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4 sm:mb-0">Daftar Asset</h2>
                <button id="add-asset-btn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Tambah Asset
                </button>
            </div>

            <!-- Filters -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div>
                    <input type="text" id="search-pic" placeholder="Cari PIC..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <select id="filter-tipe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tipe</option>
                        <option value="Elektronik">Elektronik</option>
                        <option value="Kendaraan">Kendaraan</option>
                        <option value="Alat">Alat</option>
                    </select>
                </div>
                <div>
                    <select id="filter-project" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Project</option>
                        <option value="Head Office">Head Office</option>
                        <option value="Moratel">Moratel</option>
                        <option value="EMR">EMR</option>
                    </select>
                </div>
                <div>
                    <select id="filter-lokasi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Lokasi</option>
                        <option value="Jakarta">Jakarta</option>
                        <option value="Surabaya">Surabaya</option>
                        <option value="Bandung">Bandung</option>
                    </select>
                </div>
                <div>
                    <button id="reset-filters" class="w-full px-3 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">
                        Reset Filter
                    </button>
                </div>
            </div>

            <!-- Asset Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Asset</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Serial Number</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tahun</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="asset-table-body" class="bg-white divide-y divide-gray-200">
                        @foreach($assets as $asset)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->tipe }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->jenis_aset }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $asset->pic }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->merk }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->serial_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->project }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->lokasi }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->tanggal_beli ? \Carbon\Carbon::parse($asset->tanggal_beli)->format('d M Y') : 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($asset->harga_beli, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $asset->status == 'Available' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $asset->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <button onclick="viewAsset({{ $asset->id }})"
                                            class="text-blue-600 hover:text-blue-900 transition-colors"
                                            title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editAsset({{ $asset->id }})"
                                            class="text-yellow-600 hover:text-yellow-900 transition-colors"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deleteAsset({{ $asset->id }})"
                                            class="text-red-600 hover:text-red-900 transition-colors"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-4">
                <div class="flex-1 flex justify-between sm:hidden">
                    <button class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Previous</button>
                    <button class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">Next</button>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing <span class="font-medium">1</span> to <span class="font-medium">{{ count($assets) }}</span> of
                            <span class="font-medium" id="total-items">{{ count($assets) }}</span> results
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Asset -->
<div id="asset-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Tambah Asset Baru</h2>
                    <button data-close-modal class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="asset-form" method="POST" action="{{ route('assets.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Tipe -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                            <select name="tipe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Pilih Tipe</option>
                                <option value="Elektronik">Elektronik</option>
                                <option value="Kendaraan">Kendaraan</option>
                                <option value="Alat">Alat</option>
                            </select>
                        </div>

                        <!-- Jenis Asset -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Asset</label>
                            <select name="jenis_aset" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Pilih Jenis Asset</option>
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

                        <!-- Serial Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
                            <input type="text" name="serial_number" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Project -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                            <select name="project" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Pilih Project</option>
                                <option value="Head Office">Head Office</option>
                                <option value="Moratel">Moratel</option>
                                <option value="EMR">EMR</option>
                            </select>
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                            <select name="lokasi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="">Pilih Lokasi</option>
                                <option value="Jakarta">Jakarta</option>
                                <option value="Surabaya">Surabaya</option>
                                <option value="Bandung">Bandung</option>
                            </select>
                        </div>

                        <!-- Tahun Beli -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Beli</label>
                            <input type="date" name="tanggal_beli" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="{{ old('tanggal_beli', '') }}">
                        </div>

                        <!-- Harga Beli -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli</label>
                            <input type="number" name="harga_beli" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" min="0">
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <option value="Available">Available</option>
                                <option value="In Use">In Use</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </div>

                        <!-- Data Pajak (for Kendaraan) -->
                        <div class="hidden" id="modal-pajak">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pajak</label>
                            <input type="date" name="tanggal_pajak" class="w-full px-3 py-2 border rounded-md">
                            <label class="block text-sm font-medium text-gray-700 mb-1 mt-3">Jumlah Pajak</label>
                            <input type="number" name="jumlah_pajak" class="w-full px-3 py-2 border rounded-md" placeholder="0">
                            <label class="block text-sm font-medium text-gray-700 mb-1 mt-3">Status Pajak</label>
                            <select name="status_pajak" class="w-full px-3 py-2 border rounded-md">
                                <option value="">-- Pilih Status --</option>
                                <option value="Lunas">Lunas</option>
                                <option value="Belum Lunas">Belum Lunas</option>
                            </select>
                        </div>

                        <!-- Riwayat Servis (for Kendaraan) -->
                        <div class="hidden" id="modal-servis">
                            <h3 class="text-sm font-medium text-gray-800 mt-4 mb-2">Riwayat Servis</h3>
                            <div id="service-rows-modal" class="space-y-3">
                                <div class="flex gap-2 items-center service-row">
                                    <input type="hidden" name="service_id[]" value="">
                                    <input type="date" name="service_date[]" class="px-3 py-2 border rounded w-40">
                                    <input type="text" name="service_desc[]" placeholder="Keterangan servis" class="px-3 py-2 border rounded flex-1">
                                    <input type="number" name="service_cost[]" placeholder="Biaya" class="px-3 py-2 border rounded w-32">
                                    <input type="text" name="service_vendor[]" placeholder="Vendor" class="px-3 py-2 border rounded w-48">
                                    <div class="flex items-center gap-2">
                                        <input type="file" name="service_file[]" class="px-2">
                                        <button type="button" class="text-red-600 remove-service">✕</button>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button id="add-service-modal" type="button" class="px-3 py-1 bg-green-500 text-white rounded">Tambah Riwayat</button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 mt-6 pt-6 border-t">
                        <button type="button" data-close-modal class="px-6 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
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

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard initializing...');

    // Elements
    const addAssetBtn = document.getElementById('add-asset-btn');
    const modal = document.getElementById('asset-modal');
    const assetForm = document.getElementById('asset-form');
    const closeModalBtns = document.querySelectorAll('[data-close-modal]');
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');

    // Check elements
    if (!addAssetBtn || !modal) {
        console.error('Critical elements missing!');
        return;
    }

    console.log('All elements found successfully');

    // --- MODAL FUNCTIONS ---
    function showModal() {
        console.log('Showing modal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideModal() {
        console.log('Hiding modal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        assetForm.reset();
    }

    function showToast(message, isSuccess = true) {
        console.log('Showing toast:', message);
        toastMessage.textContent = message;
        toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg transform transition-transform duration-300 z-50 ${
            isSuccess ? 'bg-green-500' : 'bg-red-500'
        } text-white`;

        // Show toast
        toast.style.transform = 'translateY(0)';

        // Hide after 3 seconds
        setTimeout(() => {
            toast.style.transform = 'translateY(100%)';
        }, 3000);
    }

    // --- EVENT LISTENERS ---
    addAssetBtn.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Add Asset button clicked');
        showModal();
    });

    // Close modal handlers
    closeModalBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            hideModal();
        });
    });

    // Close on backdrop click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal();
        }
    });

    // Form submission
    assetForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        console.log('Form submitted');

        const formData = new FormData(assetForm);
        const submitBtn = assetForm.querySelector('button[type="submit"]');

        // Show loading
        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';

        try {
            const response = await fetch(assetForm.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            });

            // Try to parse JSON response
            let json = null;
            try {
                json = await response.json();
            } catch (e) {
                // not JSON
            }

            if (response.ok && json && json.success) {
                showToast(json.message || 'Sukses', true);
                hideModal();
                // optional: update UI with json.asset
            } else if (response.ok && json && !json.success) {
                showToast(json.message || 'Gagal menyimpan data', false);
            } else if (response.ok && !json) {
                // non-JSON successful response (fallback)
                hideModal();
                setTimeout(() => window.location.reload(), 800);
            } else {
                const errMsg = (json && json.message) ? json.message : 'Gagal menyimpan data';
                showToast(errMsg, false);
            }
        } catch (error) {
            console.error('Error:', error);
            showToast('Terjadi kesalahan: ' + error.message, false);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Simpan';
        }
    });

    // show/hide pajak & servis when tipe is Kendaraan
    const tipeSelect = assetForm.querySelector('select[name="tipe"]');
    const pajakSection = document.getElementById('modal-pajak');
    const servisSection = document.getElementById('modal-servis');

    function updateModalVehicleSections() {
        if (!tipeSelect) return;
        const isVehicle = tipeSelect.value === 'Kendaraan';
        pajakSection?.classList.toggle('hidden', !isVehicle);
        servisSection?.classList.toggle('hidden', !isVehicle);
    }
    tipeSelect?.addEventListener('change', updateModalVehicleSections);

    // service rows add/remove for modal
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'add-service-modal') {
            const container = document.getElementById('service-rows-modal');
            const div = document.createElement('div');
            div.className = 'flex gap-2 items-center service-row';
            div.innerHTML =
                '<input type="hidden" name="service_id[]" value="">' +
                '<input type="date" name="service_date[]" class="px-3 py-2 border rounded w-40">' +
                '<input type="text" name="service_desc[]" placeholder="Keterangan servis" class="px-3 py-2 border rounded flex-1">' +
                '<input type="number" name="service_cost[]" placeholder="Biaya" class="px-3 py-2 border rounded w-32">' +
                '<input type="text" name="service_vendor[]" placeholder="Vendor" class="px-3 py-2 border rounded w-48">' +
                '<div class="flex items-center gap-2">' +
                    '<input type="file" name="service_file[]" class="px-2">' +
                    '<button type="button" class="text-red-600 remove-service">✕</button>' +
                '</div>';
            container.appendChild(div);
        }

        if (e.target && e.target.classList && e.target.classList.contains('remove-service')) {
            const row = e.target.closest('.service-row');
            row?.remove();
        }
    });

    // --- CHARTS ---
    function createCharts() {
        // Project Pie Chart
        const projectCtx = document.getElementById('project-pie-chart');
        if (projectCtx) {
            new Chart(projectCtx, {
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
                                font: { size: 11 }
                            }
                        }
                    }
                }
            });
        }

        // Type Bar Chart
        const typeCtx = document.getElementById('type-bar-chart');
        if (typeCtx) {
            new Chart(typeCtx, {
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
                            max: 4
                        }
                    }
                }
            });
        }
    }

    // --- FILTERS ---
    function initFilters() {
        const searchInput = document.getElementById('search-pic');
        const filterTipe = document.getElementById('filter-tipe');
        const filterProject = document.getElementById('filter-project');
        const filterLokasi = document.getElementById('filter-lokasi');
        const resetBtn = document.getElementById('reset-filters');

        function applyFilters() {
            const searchTerm = searchInput?.value.toLowerCase() || '';
            const tipeFilter = filterTipe?.value || '';
            const projectFilter = filterProject?.value || '';
            const lokasiFilter = filterLokasi?.value || '';

            const rows = document.querySelectorAll('#asset-table-body tr');
            let visibleCount = 0;

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length === 0) return;

                const tipe = cells[0]?.textContent.trim() || '';
                const pic = cells[2]?.textContent.trim() || '';
                const project = cells[5]?.textContent.trim() || '';
                const lokasi = cells[6]?.textContent.trim() || '';

                const matchesSearch = !searchTerm || pic.toLowerCase().includes(searchTerm);
                const matchesTipe = !tipeFilter || tipe === tipeFilter;
                const matchesProject = !projectFilter || project === projectFilter;
                const matchesLokasi = !lokasiFilter || lokasi === lokasiFilter;

                const shouldShow = matchesSearch && matchesTipe && matchesProject && matchesLokasi;

                row.style.display = shouldShow ? '' : 'none';
                if (shouldShow) visibleCount++;
            });

            // Update count
            const totalItems = document.getElementById('total-items');
            if (totalItems) totalItems.textContent = visibleCount;
        }

        // Attach listeners
        searchInput?.addEventListener('input', applyFilters);
        filterTipe?.addEventListener('change', applyFilters);
        filterProject?.addEventListener('change', applyFilters);
        filterLokasi?.addEventListener('change', applyFilters);

        // Reset
        resetBtn?.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            if (filterTipe) filterTipe.value = '';
            if (filterProject) filterProject.value = '';
            if (filterLokasi) filterLokasi.value = '';
            applyFilters();
        });
    }

    // --- TABLE ACTIONS ---
    window.viewAsset = function(id) {
        console.log('View asset:', id);
        showToast('Fitur detail akan segera tersedia', false);
    };

    window.editAsset = async function(id) {
        console.log('Edit asset:', id);
        try {
            const res = await fetch(`/assets/${id}/json`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw new Error('Gagal mengambil data asset');
            const payload = await res.json();
            const asset = payload.asset;

            // populate simple form inputs by name
            Object.keys(asset).forEach(key => {
                const el = assetForm.querySelector(`[name="${key}"]`);
                if (!el) return;
                if (el.tagName === 'SELECT' || el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                    el.value = asset[key] === null ? '' : asset[key];
                }
            });

            // populate pajak fields specifically
            if (asset.tanggal_pajak) assetForm.querySelector('[name="tanggal_pajak"]').value = asset.tanggal_pajak.substring(0,10);
            if (asset.jumlah_pajak) assetForm.querySelector('[name="jumlah_pajak"]').value = asset.jumlah_pajak;
            if (asset.status_pajak) assetForm.querySelector('[name="status_pajak"]').value = asset.status_pajak;

            // populate service rows
            const svcContainer = document.getElementById('service-rows-modal');
            if (Array.isArray(asset.services) && svcContainer) {
                svcContainer.innerHTML = '';
                asset.services.forEach(s => {
                    const div = document.createElement('div');
                    div.className = 'flex gap-2 items-center service-row';
                    const fileLink = s.file_path ?
                        '<a href="' + s.file_path + '" class="text-sm text-gray-600" target="_blank">Lihat</a>' :
                        '<span class="text-sm text-gray-500">No file</span>';
                    div.innerHTML =
                        '<input type="hidden" name="service_id[]" value="' + s.id + '">' +
                        '<input type="date" name="service_date[]" class="px-3 py-2 border rounded w-40" value="' + (s.service_date ? s.service_date.substring(0,10) : '') + '">' +
                        '<input type="text" name="service_desc[]" placeholder="Keterangan servis" class="px-3 py-2 border rounded flex-1" value="' + (s.description||'').replace(/"/g,'&quot;') + '">' +
                        '<input type="number" name="service_cost[]" placeholder="Biaya" class="px-3 py-2 border rounded w-32" value="' + (s.cost||'') + '">' +
                        '<input type="text" name="service_vendor[]" placeholder="Vendor" class="px-3 py-2 border rounded w-48" value="' + (s.vendor||'').replace(/"/g,'&quot;') + '">' +
                        '<div class="flex items-center gap-2">' +
                            '<input type="file" name="service_file[]" class="px-2">' +
                            fileLink +
                            '<button type="button" class="text-red-600 remove-service">✕</button>' +
                        '</div>';
                    svcContainer.appendChild(div);
                });
            }

            // set form action to update route
            assetForm.action = `/assets/${asset.id}`;

            // ensure hidden _method input exists and is PUT
            let methodInput = assetForm.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                assetForm.appendChild(methodInput);
            }
            methodInput.value = 'PUT';

            // open modal
            showModal();
        } catch (err) {
            console.error(err);
            showToast('Gagal memuat data untuk edit: ' + err.message, false);
        }
    };

    window.deleteAsset = function(id) {
        if (confirm('Apakah Anda yakin ingin menghapus asset ini?')) {
            console.log('Delete asset:', id);
            showToast('Fitur hapus akan segera tersedia', false);
        }
    };

    // Initialize
    createCharts();
    initFilters();

    console.log('Dashboard initialized successfully!');
});
</script>
@endpush
