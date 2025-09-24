@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <h1 class="text-3xl font-bold text-center text-gray-800">{{ $page->name }}</h1>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-red-600 text-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">Total Assets</p>
                    <p class="text-2xl font-bold">{{ $totalAssets }}</p>
                </div>
                <div class="p-3 bg-white rounded-lg">
                    <i class="fas fa-boxes text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-red-600 text-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">Total Nilai</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($totalNilai, 0, ',', '.') }}</p>
                </div>
                <div class="p-3 bg-white rounded-lg">
                    <i class="fas fa-dollar-sign text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-red-600 text-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">Available</p>
                    <p class="text-2xl font-bold">{{ $availableAssets }}</p>
                </div>
                <div class="p-3 bg-white rounded-lg">
                    <i class="fas fa-check-circle text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-red-600 text-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">In Use</p>
                    <p class="text-2xl font-bold">{{ $inUseAssets }}</p>
                </div>
                <div class="p-3 bg-white rounded-lg">
                    <i class="fas fa-exclamation-circle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>
    {{-- Dynamic Charts --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @if(!empty($chartData))
            @foreach($chartData as $index => $chart)
            <div class="bg-white shadow-lg rounded-xl p-6">
                <h2 class="text-xl font-bold text-gray-700 mb-4">{{ $chart['title'] }}</h2>
                <div class="relative h-72">
                    <canvas id="chart-{{ $index }}"></canvas>
                </div>
            </div>
            @endforeach
        @endif
    </div>

    {{-- Table --}}
    <div class="bg-white shadow-lg rounded-xl p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Daftar Aset</h2>
            <div>
                <a href="{{ route('asset-pages.export-csv', array_merge(['slug' => $page->slug], $filters)) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
                    <i class="fas fa-file-excel mr-2"></i>Ekspor CSV
                </a>
                <a href="{{ route('assets.create', ['tipe' => $page->asset_type]) }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg text-sm ml-2">
                    <i class="fas fa-plus mr-2"></i>Tambah Asset
                </a>
            </div>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('asset-pages.show', $page->slug) }}" method="GET" class="mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 items-center">
                <input type="text" name="pic" placeholder="Cari PIC..." value="{{ $filters['pic'] ?? '' }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

                <select name="project" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Project</option>
                    @foreach($distinctProjects as $project)
                        <option value="{{ $project }}" {{ ($filters['project'] ?? '') == $project ? 'selected' : '' }}>{{ $project }}</option>
                    @endforeach
                </select>

                <select name="lokasi" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Lokasi</option>
                    @foreach($distinctLokasi as $lokasi)
                        <option value="{{ $lokasi }}" {{ ($filters['lokasi'] ?? '') == $lokasi ? 'selected' : '' }}>{{ $lokasi }}</option>
                    @endforeach
                </select>

                <div class="flex space-x-2">
                    <button type="submit" class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-md hover:bg-blue-700 transition-colors">Filter</button>
                    <a href="{{ route('asset-pages.show', $page->slug) }}" class="w-full bg-gray-200 text-gray-800 font-semibold py-2 px-4 rounded-md hover:bg-gray-300 transition-colors text-center">Reset</a>
                </div>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Aset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        @if (in_array(strtolower($page->asset_type), ['kendaraan', 'splicer']))
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pajak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Servis</th>
                        @endif
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Sewa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Aset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($assets as $asset)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->tipe }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->jenis_aset }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->merk }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->project }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->lokasi }}</td>
                                        @if (in_array(strtolower($page->asset_type), ['kendaraan', 'splicer']))
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @php $sp = trim(strtolower($asset->status_pajak ?? '')); @endphp
                                            @if($sp === 'lunas')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                                            @elseif($sp === 'belum lunas')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Belum Lunas</span>
                                            @elseif($sp === 'tidak lengkap')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">Tidak Lengkap</span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->total_servis ? 'Rp ' . number_format($asset->total_servis, 0, ',', '.') : '-' }}</td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $asset->harga_sewa ? 'Rp ' . number_format($asset->harga_sewa, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            @php $picVal = $asset->pic ?? '-'; @endphp
                                            @if($picVal === 'Available')
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                                            @else
                                                <span class="text-gray-900">{{ $picVal }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $asset->serial_number ?? '-' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            @if(!empty($asset->id))
                                            <a href="{{ route('assets.show', $asset->id) }}"
                                               class="text-blue-600 hover:text-blue-900 transition-colors"
                                               title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('assets.edit', $asset->id) }}"
                                               class="text-yellow-600 hover:text-yellow-900 transition-colors"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('assets.destroy', $asset->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus aset ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @else
                                            <span class="text-gray-400"><i class="fas fa-eye mr-2"></i>Detail</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ strcasecmp($page->asset_type, 'Kendaraan') === 0 ? 10 : 8 }}" class="px-6 py-4 text-center text-gray-500">No assets found</td>
                                    </tr>
                                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-6">
            @if($assets instanceof \Illuminate\Pagination\LengthAwarePaginator || $assets instanceof \Illuminate\Pagination\Paginator)
                {{ $assets->links() }}
            @endif
        </div>
    </div>
</div>

<!-- Modal for Viewing Asset Details -->
<div id="view-asset-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full m-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 id="modal-title" class="text-2xl font-bold text-gray-800">Detail Aset</h2>
                <button id="close-view-asset-modal" class="text-gray-500 hover:text-gray-800">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div id="asset-details-content" class="p-6 max-h-[70vh] overflow-y-auto">
            <!-- Dynamic content will be loaded here -->
        </div>
        <div class="p-6 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <button type="button" id="export-servis-btn" class="hidden px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors shadow-sm">
                <i class="fas fa-file-csv mr-2"></i>Ekspor Servis (CSV)
            </button>
            <button type="button" id="close-view-asset" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors shadow-sm">Tutup</button>
        </div>
    </div>
</div>

<div id="image-modal" class="fixed inset-0 bg-black bg-opacity-75 hidden z-50 flex items-center justify-center p-4">
    <div class="relative">
        <button onclick="closeImageModal()" class="absolute -top-10 right-0 text-white text-3xl font-bold">&times;</button>
        <img id="modal-image" src="" alt="Gambar Aset" class="max-w-full max-h-[90vh] rounded-lg">
        <p id="modal-caption" class="text-white text-center mt-2"></p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartColors = [
        'rgba(255, 99, 132, 0.7)', 'rgba(54, 162, 235, 0.7)', 'rgba(255, 206, 86, 0.7)',
        'rgba(75, 192, 192, 0.7)', 'rgba(153, 102, 255, 0.7)', 'rgba(255, 159, 64, 0.7)',
        'rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)',
        'rgba(75, 192, 192, 0.5)', 'rgba(153, 102, 255, 0.5)', 'rgba(255, 159, 64, 0.5)'
    ];

    function renderChart(ctx, type, label, data) {
        new Chart(ctx, {
            type: type,
            data: {
                labels: Object.keys(data),
                datasets: [{
                    label: label,
                    data: Object.values(data),
                    backgroundColor: chartColors,
                    borderColor: chartColors.map(color => color.replace('0.7', '1')),
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                }
            }
        });
    }

    const chartData = @json($chartData ?? []);

    if (chartData.length > 0) {
        chartData.forEach((chart, index) => {
            const ctx = document.getElementById('chart-' + index);
            if (ctx) {
                renderChart(ctx, chart.type, chart.title, chart.data);
            }
        });
    }

    function closeModal() {
        document.getElementById('view-asset-modal').classList.add('hidden');
    }

    document.getElementById('close-view-asset-modal').addEventListener('click', closeModal);
    document.getElementById('close-view-asset').addEventListener('click', closeModal);

    window.viewAsset = function(assetId) {
        fetch(`/assets/${assetId}/json`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) return;
                const a = data.asset || {};
                const modalContent = document.getElementById('asset-details-content');
                const exportBtn = document.getElementById('export-servis-btn');

                function fmtRupiah(n) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(Number(n || 0));
                }
                function fmtDate(iso) {
                    if (!iso) return 'N/A';
                    const d = new Date(iso);
                    if (isNaN(d.getTime())) return 'N/A';
                    const opts = { day: '2-digit', month: 'short', year: 'numeric' };
                    return d.toLocaleDateString('id-ID', opts);
                }

                const services = Array.isArray(a.services) ? a.services.slice().reverse() : [];
                const servicesList = services.length ? services.map(s => {
                    const fileHref = s.file_url || s.file_path || '';
                    const dateStr = s.service_date ? fmtDate(s.service_date) : '-';
                    const costStr = fmtRupiah(s.cost || 0);
                    const vendor = s.vendor || '-';
                    const desc = s.description || '-';
                    const fileHtml = fileHref ? `<a href="${fileHref}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">Bukti Terlampir</a>` : '<span class="text-gray-400">Bukti tidak ada</span>';
                    return `
                        <div class="border rounded-lg p-3 bg-white">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-sm text-gray-500">${dateStr} • <span class="text-gray-600">${vendor}</span></div>
                                    <div class="font-medium text-gray-800">${desc}</div>
                                    <div class="text-xs text-gray-500 mt-1">${fileHtml}</div>
                                </div>
                                <div class="text-right font-semibold text-gray-800">${costStr}</div>
                            </div>
                        </div>`;
                }).join('\n') : '<p class="text-gray-500">Belum ada riwayat servis.</p>';

                // Header summary cards
                const hargaBeliCard = `
                    <div class="flex items-center gap-3 p-4 rounded-lg bg-green-50 border">
                        <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center"><i class="fas fa-money-bill"></i></div>
                        <div>
                            <div class="text-xs text-gray-500">Harga Beli</div>
                            <div class="font-semibold text-gray-800">${fmtRupiah(a.harga_beli || 0)}</div>
                        </div>
                    </div>`;
                const tanggalBeliCard = `
                    <div class="flex items-center gap-3 p-4 rounded-lg bg-indigo-50 border">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center"><i class="fas fa-calendar"></i></div>
                        <div>
                            <div class="text-xs text-gray-500">Tanggal Beli</div>
                            <div class="font-semibold text-gray-800">${fmtDate(a.tanggal_beli)}</div>
                        </div>
                    </div>`;
                const picCard = `
                    <div class="flex items-center gap-3 p-4 rounded-lg bg-blue-50 border">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center"><i class="fas fa-user"></i></div>
                        <div>
                            <div class="text-xs text-gray-500">PIC</div>
                            <div class="font-semibold text-gray-800">${a.pic || '-'}</div>
                        </div>
                    </div>`;
                const totalServisCard = `
                    <div class="flex items-center gap-3 p-4 rounded-lg bg-rose-50 border">
                        <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center"><i class="fas fa-wrench"></i></div>
                        <div>
                            <div class="text-xs text-gray-500">Total Biaya Servis</div>
                            <div class="font-semibold text-gray-800">${fmtRupiah(a.total_servis || 0)}</div>
                        </div>
                    </div>`;

                // For Kendaraan type, add Pajak card
                const isKendaraan = (a.tipe || '').toLowerCase() === 'kendaraan';
                const statusPajakCard = isKendaraan ? `
                    <div class="flex items-center gap-3 p-4 rounded-lg bg-yellow-50 border">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 text-yellow-600 flex items-center justify-center"><i class="fas fa-file-invoice-dollar"></i></div>
                        <div>
                            <div class="text-xs text-gray-500">Status Pajak</div>
                            <div class="font-semibold text-gray-800">${a.status_pajak || '-'}</div>
                        </div>
                    </div>` : '';

                // Determine grid columns based on asset type
                const gridCols = isKendaraan ? 'grid-cols-1 md:grid-cols-5' : 'grid-cols-1 md:grid-cols-4';

                // Compose detail HTML
                modalContent.innerHTML = `
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Detail Asset</h1>
                            <p class="text-sm text-gray-500">Informasi lengkap aset ${a.merk || '-'}</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" id="hdr-edit-asset" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"><i class="fas fa-edit mr-2"></i>Edit Asset</button>
                            <button type="button" id="hdr-back" class="inline-flex items-center px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-800"><i class="fas fa-arrow-left mr-2"></i>Kembali</button>
                        </div>
                    </div>

                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">${a.merk || '-'} <span class="text-gray-500">${a.serial_number ? '(' + a.serial_number + ')' : ''}</span></h2>
                            <p class="text-sm text-gray-500 mt-1">Tipe: ${a.tipe || '-'} • Jenis: ${a.jenis_aset || '-'}</p>
                        </div>
                        <div class="text-sm text-gray-500">Serial Number<br><span class="text-gray-900 font-semibold">${a.serial_number || '-'}</span></div>
                    </div>

                    <div class="${gridCols} gap-4 mt-4">
                        ${hargaBeliCard}
                        ${tanggalBeliCard}
                        ${picCard}
                        ${totalServisCard}
                        ${statusPajakCard}
                    </div>

                    <!-- Foto Aset -->
                    ${a.foto_aset_url ? `
                    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-4"><h3 class="font-semibold">Foto Aset</h3></div>
                        <div class="p-4"><img src="${a.foto_aset_url}" alt="Foto Aset" class="w-full max-w-md h-auto object-cover border rounded-lg cursor-pointer" onclick="openImageModal('${a.foto_aset_url}','Foto Aset')"></div>
                    </div>` : ''}

                    <!-- Vehicle Photos Section for Kendaraan -->
                    ${isKendaraan && (a.foto_stnk_url || a.foto_kendaraan_url) ? `
                    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-4"><h3 class="font-semibold">Foto Kendaraan</h3></div>
                        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            ${a.foto_stnk_url ? `
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Foto STNK</h4>
                                <img src="${a.foto_stnk_url}" alt="Foto STNK" class="w-full h-auto object-cover border rounded-lg cursor-pointer" onclick="openImageModal('${a.foto_stnk_url}','Foto STNK')">
                            </div>` : ''}
                            ${a.foto_kendaraan_url ? `
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Foto Kendaraan</h4>
                                <img src="${a.foto_kendaraan_url}" alt="Foto Kendaraan" class="w-full h-auto object-cover border rounded-lg cursor-pointer" onclick="openImageModal('${a.foto_kendaraan_url}','Foto Kendaraan')">
                            </div>` : ''}
                        </div>
                    </div>` : ''}

                    <div class="mt-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-base font-semibold text-gray-800 mb-3">Ringkasan Asset</h3>
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div class="text-gray-500">Jenis Asset</div><div class="text-gray-900 font-medium">${a.jenis_aset || '-'}</div>
                                    <div class="text-gray-500">Tipe</div><div class="text-gray-900 font-medium">${a.tipe || '-'}</div>
                                    <div class="text-gray-500">Merk</div><div class="text-gray-900 font-medium">${a.merk || '-'}</div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-800 mb-3">Riwayat Servis Lengkap</h3>
                                <div class="space-y-3 max-h-64 overflow-y-auto">${servicesList}</div>
                            </div>
                        </div>

                    <!-- Pajak Information for Kendaraan -->
                    ${isKendaraan ? `
                    <div class="mt-6 bg-white rounded-lg border border-gray-200 p-4">
                        <h3 class="text-base font-semibold text-gray-800 mb-3">Informasi Pajak Kendaraan</h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                            <div class="text-gray-500">Tanggal Pajak</div><div class="text-gray-900 font-medium">${a.tanggal_pajak ? fmtDate(a.tanggal_pajak) : '-'}</div>
                            <div class="text-gray-500">Jumlah Pajak</div><div class="text-gray-900 font-medium">${a.jumlah_pajak ? fmtRupiah(a.jumlah_pajak) : '-'}</div>
                            <div class="text-gray-500">Status Pajak</div><div class="text-gray-900 font-medium">
                                ${a.status_pajak ? `<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ${
                                    a.status_pajak.toLowerCase() === 'lunas' ? 'bg-green-100 text-green-800' :
                                    a.status_pajak.toLowerCase() === 'belum lunas' ? 'bg-yellow-100 text-yellow-800' :
                                    a.status_pajak.toLowerCase() === 'tidak lengkap' ? 'bg-red-100 text-red-800' :
                                    'bg-gray-100 text-gray-800'
                                }">${a.status_pajak}</span>` : '-'}
                            </div>
                        </div>
                    </div>` : ''}

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div class="bg-white p-4 rounded-lg border">
                            <h3 class="text-base font-semibold text-gray-800 mb-3">Informasi Asset</h3>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="text-gray-500">Jenis Aset</div><div class="text-gray-900 font-medium">${a.jenis_aset || '-'}</div>
                                <div class="text-gray-500">Tipe</div><div class="text-gray-900 font-medium">${a.tipe || '-'}</div>
                                <div class="text-gray-500">Merk</div><div class="text-gray-900 font-medium">${a.merk || '-'}</div>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-lg border">
                            <h3 class="text-base font-semibold text-gray-800 mb-3">Lokasi & Project</h3>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="text-gray-500">Lokasi</div><div class="text-gray-900 font-medium">${a.lokasi || '-'}</div>
                                <div class="text-gray-500">Project</div><div class="text-gray-900 font-medium">${a.project || '-'}</div>
                                <div class="text-gray-500">PIC</div><div class="text-gray-900 font-medium">${a.pic || '-'}</div>
                            </div>
                        </div>
                    </div>
                    <!-- Keterangan Aset -->
                    ${a.keterangan ? `
                    <div class="mt-6 bg-white rounded-lg border border-gray-200 p-4">
                        <h3 class="text-base font-semibold text-gray-800 mb-3">Keterangan Aset</h3>
                        <p class="text-gray-700 leading-relaxed">${a.keterangan}</p>
                    </div>
                    ` : ''}

                    <div class="mt-6 bg-white p-4 rounded-lg border">
                        <h3 class="text-base font-semibold text-gray-800 mb-3">Timeline Asset</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center"><i class="fas fa-plus"></i></div>
                                <div>
                                    <div class="text-sm font-medium text-gray-800">Asset Created</div>
                                    <div class="text-xs text-gray-500">${fmtDate(a.created_at)}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center"><i class="fas fa-sync"></i></div>
                                <div>
                                    <div class="text-sm font-medium text-gray-800">Last Updated</div>
                                    <div class="text-xs text-gray-500">${fmtDate(a.updated_at)}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <button type="button" id="btn-print-detail" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200"><i class="fas fa-print mr-2"></i>Print Detail</button>
                        <button type="button" id="btn-edit-asset" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"><i class="fas fa-edit mr-2"></i>Edit Asset</button>
                        <button type="button" id="btn-copy-info" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200"><i class="fas fa-copy mr-2"></i>Copy Info</button>
                        ${isKendaraan && (a.tanggal_pajak || a.jumlah_pajak || a.status_pajak) ? `
                        <button type="button" id="btn-export-pajak" class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700"><i class="fas fa-file-invoice mr-2"></i>Ekspor Pajak</button>` : ''}
                    </div>
                `;

                // Footer export button
                if (services.length) {
                    exportBtn.classList.remove('hidden');
                    exportBtn.onclick = () => window.location.href = `/assets/${a.id}/export-servis`;
                } else {
                    exportBtn.classList.add('hidden');
                }

                // Bind header/quick actions
                const btnPrint = document.getElementById('btn-print-detail');
                const btnEdit = document.getElementById('btn-edit-asset');
                const btnCopy = document.getElementById('btn-copy-info');
                const btnExportPajak = document.getElementById('btn-export-pajak');
                const hdrEdit = document.getElementById('hdr-edit-asset');
                const hdrBack = document.getElementById('hdr-back');
                btnPrint?.addEventListener('click', () => window.print());
                btnEdit?.addEventListener('click', () => window.editAsset(a.id));
                btnCopy?.addEventListener('click', () => {
                    const text = `Asset: ${a.merk || '-'} ${a.serial_number ? '(' + a.serial_number + ')' : ''}\n` +
                                 `Tipe/Jenis: ${a.tipe || '-'} / ${a.jenis_aset || '-'}\n` +
                                 `PIC: ${a.pic || '-'}\n` +
                                 `Lokasi/Project: ${a.lokasi || '-'} / ${a.project || '-'}\n` +
                                 `Total Servis: ${fmtRupiah(a.total_servis || 0)}` +
                                 (isKendaraan ? `\nStatus Pajak: ${a.status_pajak || '-'}` : '');
                    navigator.clipboard?.writeText(text).then(()=>{}).catch(()=>{});
                });
                btnExportPajak?.addEventListener('click', () => window.location.href = `/assets/${a.id}/export-pajak`);
                hdrEdit?.addEventListener('click', () => window.editAsset(a.id));
                hdrBack?.addEventListener('click', () => document.getElementById('view-asset-modal').classList.add('hidden'));

                document.getElementById('view-asset-modal').classList.remove('hidden');
            });
    }

    window.editAsset = function(id) {
        window.location.href = `/assets/${id}/edit`;
    }

    window.deleteAsset = function(id) {
        if (!confirm('Anda yakin ingin menghapus asset ini? Tindakan ini tidak dapat dibatalkan.')) {
            return;
        }

        const url = `/assets/${id}`;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(url, {
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
                alert(data.message || 'Asset berhasil dihapus.');
                location.reload();
            } else {
                alert(data.message || 'Gagal menghapus asset.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus asset.');
        });
    };

    window.openImageModal = function(src, caption) {
        document.getElementById('modal-image').src = src;
        document.getElementById('modal-caption').innerText = caption;
        document.getElementById('image-modal').classList.remove('hidden');
    }

    window.closeImageModal = function() {
        document.getElementById('image-modal').classList.add('hidden');
    }
});
</script>

@endsection
