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
                        <p id="stat-total-assets" class="text-2xl font-bold text-gray-900">{{ $totalAssets ?? 0 }}</p>
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
                        <p id="stat-total-nilai" class="text-2xl font-bold text-gray-900">Rp {{ number_format($totalNilai ?? 0, 0, ',', '.') }}</p>
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
                        <p id="stat-available" class="text-2xl font-bold text-gray-900">{{ $availableAssets ?? 0 }}</p>
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
                        <p id="stat-in-use" class="text-2xl font-bold text-gray-900">{{ $inUseAssets ?? 0 }}</p>
                    </div>
                    <div class="p-3 bg-red-50 rounded-lg">
                        <i class="fas fa-clock text-red-600"></i>
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

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex flex-col sm:flex-row gap-4">
                <input type="text" id="search-pic" placeholder="Cari PIC..."
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">

                <select id="filter-tipe" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Tipe</option>
                    @if(isset($tipes))
                        @foreach($tipes as $tipe)
                            <option value="{{ $tipe }}">{{ $tipe }}</option>
                        @endforeach
                    @endif
                </select>

                <select id="filter-project" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Project</option>
                    @if(isset($projects))
                        @foreach($projects as $project)
                            <option value="{{ $project }}">{{ $project }}</option>
                        @endforeach
                    @endif
                </select>

                <button id="reset-filters" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                    Reset
                </button>

                @can('kelola-aset')
                <a href="{{ route('assets.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Tambah Asset
                </a>
                @endcan
            </div>
        </div>

        <!-- Asset Table -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Daftar Asset</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merk</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="asset-table-body" class="bg-white divide-y divide-gray-200">
                        @if(isset($assets) && $assets->count() > 0)
                            @foreach($assets as $asset)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->tipe ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->merk ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->pic ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->project ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->lokasi ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($asset->harga_beli ?? 0, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <button onclick="viewAsset({{ $asset->id }})" class="text-blue-600 hover:text-blue-900" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @can('kelola-aset')
                                        <a href="{{ route('assets.edit', $asset->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteAsset({{ $asset->id }})" class="text-red-600 hover:text-red-900" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    Belum ada data asset
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
console.log('Dashboard script loading...');

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard initializing...');
    console.log('Chart.js available:', typeof Chart !== 'undefined');

    // Initialize charts and filters
    initializeCharts();
    initializeFilters();
});

// Chart variables
let projectChart = null;
let typeChart = null;

function initializeCharts() {
    console.log('Initializing charts...');

    try {
        // Project data from server
        const projectLabels = {!! json_encode(isset($projectSummary) ? array_keys($projectSummary->toArray()) : []) !!};
        const projectData = {!! json_encode(isset($projectSummary) ? array_values($projectSummary->toArray()) : []) !!};

        // Type data from server
        const typeLabels = {!! json_encode(isset($jenisSummary) ? array_keys($jenisSummary->toArray()) : []) !!};
        const typeData = {!! json_encode(isset($jenisSummary) ? array_values($jenisSummary->toArray()) : []) !!};

        console.log('Project data:', { labels: projectLabels, data: projectData });
        console.log('Type data:', { labels: typeLabels, data: typeData });

        // Create project pie chart
        const projectCtx = document.getElementById('project-pie-chart');
        if (projectCtx && typeof Chart !== 'undefined') {
            if (projectLabels.length > 0) {
                projectChart = new Chart(projectCtx, {
                    type: 'pie',
                    data: {
                        labels: projectLabels,
                        datasets: [{
                            data: projectData,
                            backgroundColor: [
                                '#3b82f6', '#f59e0b', '#10b981', '#8b5cf6', '#ef4444', '#06b6d4',
                                '#f97316', '#84cc16', '#ec4899', '#6366f1'
                            ],
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
                console.log('Project chart created successfully');
            } else {
                projectCtx.getContext('2d').fillText('No data available', 10, 50);
            }
        }

        // Create type bar chart
        const typeCtx = document.getElementById('type-bar-chart');
        if (typeCtx && typeof Chart !== 'undefined') {
            if (typeLabels.length > 0) {
                typeChart = new Chart(typeCtx, {
                    type: 'bar',
                    data: {
                        labels: typeLabels,
                        datasets: [{
                            data: typeData,
                            backgroundColor: [
                                '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#ef4444'
                            ]
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
                                beginAtZero: true
                            }
                        }
                    }
                });
                console.log('Type chart created successfully');
            } else {
                typeCtx.getContext('2d').fillText('No data available', 10, 50);
            }
        }
    } catch (error) {
        console.error('Error initializing charts:', error);
    }
}

function initializeFilters() {
    console.log('Initializing filters...');

    try {
        // Get filter elements
        const searchInput = document.getElementById('search-pic');
        const filterTipe = document.getElementById('filter-tipe');
        const filterProject = document.getElementById('filter-project');
        const resetBtn = document.getElementById('reset-filters');

        function applyFilters() {
            const searchTerm = searchInput?.value.toLowerCase() || '';
            const tipeFilter = filterTipe?.value || '';
            const projectFilter = filterProject?.value || '';

            console.log('Applying filters:', { searchTerm, tipeFilter, projectFilter });

            const rows = document.querySelectorAll('#asset-table-body tr');
            let visibleCount = 0;
            let totalNilai = 0;
            let availableCount = 0;
            let inUseCount = 0;

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length < 6) return;

                const tipe = cells[0]?.textContent.trim() || '';
                const pic = cells[2]?.textContent.trim() || '';
                const project = cells[3]?.textContent.trim() || '';
                const hargaText = cells[5]?.textContent.trim().replace(/[^\d]/g, '') || '0';
                const harga = parseInt(hargaText, 10) || 0;

                const matchesSearch = !searchTerm || pic.toLowerCase().includes(searchTerm);
                const matchesTipe = !tipeFilter || tipe === tipeFilter;
                const matchesProject = !projectFilter || project === projectFilter;

                const shouldShow = matchesSearch && matchesTipe && matchesProject;

                row.style.display = shouldShow ? '' : 'none';

                if (shouldShow) {
                    visibleCount++;
                    totalNilai += harga;
                    if (pic === 'Available') {
                        availableCount++;
                    } else {
                        inUseCount++;
                    }
                }
            });

            // Update stats
            const totalEl = document.getElementById('stat-total-assets');
            const nilaiEl = document.getElementById('stat-total-nilai');
            const availableEl = document.getElementById('stat-available');
            const inUseEl = document.getElementById('stat-in-use');

            if (totalEl) totalEl.textContent = visibleCount;
            if (nilaiEl) nilaiEl.textContent = 'Rp ' + totalNilai.toLocaleString('id-ID');
            if (availableEl) availableEl.textContent = availableCount;
            if (inUseEl) inUseEl.textContent = inUseCount;

            console.log('Filter applied. Visible rows:', visibleCount);
        }

        // Attach event listeners
        if (searchInput) searchInput.addEventListener('input', applyFilters);
        if (filterTipe) filterTipe.addEventListener('change', applyFilters);
        if (filterProject) filterProject.addEventListener('change', applyFilters);

        // Reset filters
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                if (searchInput) searchInput.value = '';
                if (filterTipe) filterTipe.value = '';
                if (filterProject) filterProject.value = '';

                // Show all rows
                const rows = document.querySelectorAll('#asset-table-body tr');
                rows.forEach(row => row.style.display = '');

                // Reset stats to original values
                const totalEl = document.getElementById('stat-total-assets');
                const nilaiEl = document.getElementById('stat-total-nilai');
                const availableEl = document.getElementById('stat-available');
                const inUseEl = document.getElementById('stat-in-use');

                if (totalEl) totalEl.textContent = '{{ $totalAssets ?? 0 }}';
                if (nilaiEl) nilaiEl.textContent = 'Rp {{ number_format($totalNilai ?? 0, 0, ",", ".") }}';
                if (availableEl) availableEl.textContent = '{{ $availableAssets ?? 0 }}';
                if (inUseEl) inUseEl.textContent = '{{ $inUseAssets ?? 0 }}';

                console.log('Filters reset');
            });
        }

        console.log('Filters initialized successfully');
    } catch (error) {
        console.error('Error initializing filters:', error);
    }
}

// Asset actions
window.viewAsset = function(id) {
    console.log('View asset:', id);
    alert('View asset functionality - to be implemented');
};

window.deleteAsset = function(id) {
    if (!confirm('Anda yakin ingin menghapus asset ini?')) {
        return;
    }

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!token) {
        alert('CSRF token not found');
        return;
    }

    fetch(`/assets/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove row from table
            const row = document.querySelector(`button[onclick="deleteAsset(${id})"]`)?.closest('tr');
            if (row) {
                row.remove();
            }
            alert('Asset berhasil dihapus');
        } else {
            alert('Gagal menghapus asset: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus asset');
    });
};

console.log('Dashboard script loaded');
</script>
@endpush
