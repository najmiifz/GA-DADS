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
                <div class="flex gap-3">
                    <form id="export-form" action="{{ route('assets.export') }}" method="GET" class="m-0">
                        <input type="hidden" name="pic" id="hidden-pic">
                        <input type="hidden" name="tipe" id="hidden-tipe">
                        <input type="hidden" name="project" id="hidden-project">
                        <input type="hidden" name="lokasi" id="hidden-lokasi">
                        <input type="hidden" name="jenis_aset" id="hidden-jenis-aset">
                        <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-full shadow-md hover:bg-green-700 transition-colors" title="Ekspor CSV">
                            <i class="fas fa-file-excel mr-3"></i>
                            <span class="font-medium">Ekspor CSV</span>
                        </button>
                    </form>
                    @can('kelola-aset')
                    <a href="{{ route('assets.create') }}" class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-full shadow-md hover:bg-red-700 transition-colors" title="Tambah Asset">
                        <i class="fas fa-plus mr-3"></i>
                        <span class="font-medium">Tambah Asset</span>
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Enhanced Filters -->
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-6">
                <div>
                    <label for="search-pic" class="block text-sm font-medium text-gray-700 mb-1">PIC</label>
                    <input type="text" id="search-pic" placeholder="Cari PIC..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label for="filter-tipe" class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                    <select id="filter-tipe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tipe</option>
                        @if(isset($tipes))
                            @foreach($tipes as $tipe)
                                <option value="{{ $tipe }}">{{ $tipe }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <label for="filter-project" class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                    <select id="filter-project" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Project</option>
                        @if(isset($projects))
                            @foreach($projects as $project)
                                <option value="{{ $project }}">{{ $project }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <label for="filter-lokasi" class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <select id="filter-lokasi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Lokasi</option>
                        @if(isset($lokasis))
                            @foreach($lokasis as $lokasi)
                                <option value="{{ $lokasi }}">{{ $lokasi }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div>
                    <label for="filter-jenis-aset" class="block text-sm font-medium text-gray-700 mb-1">Jenis Asset</label>
                    <select id="filter-jenis-aset" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Jenis</option>
                        @if(isset($jenisAsets))
                            @foreach($jenisAsets as $jenis)
                                <option value="{{ $jenis }}">{{ $jenis }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="flex items-end">
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Beli</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Sewa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="asset-table-body" class="bg-white divide-y divide-gray-200">
                        @if(isset($assets) && count($assets) > 0)
                            @foreach($assets as $asset)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->tipe ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->jenis_aset ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($asset->pic === 'Available')
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                                    @elseif($asset->pic === 'Rusak')
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Rusak</span>
                                    @elseif($asset->pic === 'Hilang')
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Hilang</span>
                                    @else
                                        <div class="text-sm text-gray-900">{{ $asset->pic ?? '-' }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->merk ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->serial_number ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->project ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->lokasi ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $asset->tanggal_beli ? \Carbon\Carbon::parse($asset->tanggal_beli)->format('d M Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($asset->harga_beli ?? 0, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($asset->harga_sewa ?? 0, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('assets.show', $asset) }}"
                                           class="text-blue-600 hover:text-blue-900 transition-colors"
                                           title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('kelola-aset')
                                        <a href="{{ route('assets.edit', $asset) }}"
                                           class="text-yellow-600 hover:text-yellow-900 transition-colors"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteAsset({{ $asset->id }})"
                                                class="text-red-600 hover:text-red-900 transition-colors"
                                                title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="11" class="px-6 py-4 text-center text-gray-500">Tidak ada data asset</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(isset($assets) && method_exists($assets, 'links'))
                <div class="mt-6">
                    {{ $assets->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    initializeFilters();
});

function initializeCharts() {
    console.log('Initializing charts...');

    try {
        // Project distribution data
        const projectData = @json($projectSummary ?? []);
        console.log('Project data:', projectData);

        // Type distribution data
        const typeData = @json($jenisSummary ?? []);
        console.log('Type data:', typeData);

        // Project Pie Chart
        const projectCtx = document.getElementById('project-pie-chart');
        if (projectCtx && Object.keys(projectData).length > 0) {
            new Chart(projectCtx, {
                type: 'pie',
                data: {
                    labels: Object.keys(projectData),
                    datasets: [{
                        data: Object.values(projectData),
                        backgroundColor: [
                            '#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6',
                            '#06B6D4', '#F97316', '#84CC16', '#EC4899', '#6B7280'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }

        // Type Bar Chart
        const typeCtx = document.getElementById('type-bar-chart');
        if (typeCtx && Object.keys(typeData).length > 0) {
            new Chart(typeCtx, {
                type: 'bar',
                data: {
                    labels: Object.keys(typeData),
                    datasets: [{
                        label: 'Jumlah Asset',
                        data: Object.values(typeData),
                        backgroundColor: '#3B82F6'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
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
        const filterLokasi = document.getElementById('filter-lokasi');
        const filterJenisAset = document.getElementById('filter-jenis-aset');
        const resetBtn = document.getElementById('reset-filters');

        function applyFilters() {
            const searchTerm = searchInput?.value.toLowerCase() || '';
            const tipeFilter = filterTipe?.value || '';
            const projectFilter = filterProject?.value || '';
            const lokasiFilter = filterLokasi?.value || '';
            const jenisFilter = filterJenisAset?.value || '';

            console.log('Applying filters:', {
                search: searchTerm,
                tipe: tipeFilter,
                project: projectFilter,
                lokasi: lokasiFilter,
                jenis: jenisFilter
            });

            const rows = document.querySelectorAll('#asset-table-body tr');
            let visibleCount = 0;
            let totalNilai = 0;
            let availableCount = 0;
            let inUseCount = 0;

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length < 9) return;

                const tipe = cells[0]?.textContent.trim() || '';
                const jenisAset = cells[1]?.textContent.trim() || '';
                const picContent = cells[2]?.textContent.trim() || '';
                const pic = picContent.includes('Available') ? 'Available' : picContent;
                const merk = cells[3]?.textContent.trim() || '';
                const project = cells[5]?.textContent.trim() || '';
                const lokasi = cells[6]?.textContent.trim() || '';
                const hargaText = cells[8]?.textContent.trim().replace(/[^\d]/g, '') || '0';
                const harga = parseInt(hargaText, 10) || 0;

                const matchesSearch = !searchTerm ||
                    pic.toLowerCase().includes(searchTerm) ||
                    tipe.toLowerCase().includes(searchTerm) ||
                    merk.toLowerCase().includes(searchTerm);

                const matchesTipe = !tipeFilter || tipe === tipeFilter;
                const matchesProject = !projectFilter || project === projectFilter;
                const matchesLokasi = !lokasiFilter || lokasi === lokasiFilter;
                const matchesJenis = !jenisFilter || jenisAset === jenisFilter;

                const shouldShow = matchesSearch && matchesTipe && matchesProject &&
                                 matchesLokasi && matchesJenis;

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

            // Update hidden export form fields
            const hiddenPic = document.getElementById('hidden-pic');
            const hiddenTipe = document.getElementById('hidden-tipe');
            const hiddenProject = document.getElementById('hidden-project');
            const hiddenLokasi = document.getElementById('hidden-lokasi');
            const hiddenJenis = document.getElementById('hidden-jenis-aset');

            if (hiddenPic) hiddenPic.value = searchTerm;
            if (hiddenTipe) hiddenTipe.value = tipeFilter;
            if (hiddenProject) hiddenProject.value = projectFilter;
            if (hiddenLokasi) hiddenLokasi.value = lokasiFilter;
            if (hiddenJenis) hiddenJenis.value = jenisFilter;

            console.log('Filter applied. Visible rows:', visibleCount);
        }

        // Attach event listeners
        if (searchInput) searchInput.addEventListener('input', applyFilters);
        if (filterTipe) filterTipe.addEventListener('change', applyFilters);
        if (filterProject) filterProject.addEventListener('change', applyFilters);
        if (filterLokasi) filterLokasi.addEventListener('change', applyFilters);
        if (filterJenisAset) filterJenisAset.addEventListener('change', applyFilters);

        // Reset filters
        if (resetBtn) {
            resetBtn.addEventListener('click', function() {
                if (searchInput) searchInput.value = '';
                if (filterTipe) filterTipe.value = '';
                if (filterProject) filterProject.value = '';
                if (filterLokasi) filterLokasi.value = '';
                if (filterJenisAset) filterJenisAset.value = '';

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

                // Clear hidden export form fields
                const hiddenFields = ['hidden-pic', 'hidden-tipe', 'hidden-project', 'hidden-lokasi', 'hidden-jenis-aset'];
                hiddenFields.forEach(id => {
                    const field = document.getElementById(id);
                    if (field) field.value = '';
                });

                console.log('Filters reset');
            });
        }

        console.log('Filters initialized successfully');
    } catch (error) {
        console.error('Error initializing filters:', error);
    }
}

// Asset actions
window.deleteAsset = function(id) {
    if (!confirm('Anda yakin ingin menghapus asset ini?')) {
        return;
    }

    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    fetch(`/assets/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': token,
            'Content-Type': 'application/json',
        }
    })
    .then(response => {
        if (response.ok) {
            location.reload();
        } else {
            alert('Gagal menghapus asset');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus asset');
    });
};
</script>
@endsection
