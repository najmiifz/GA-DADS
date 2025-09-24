@extends('layouts.app')

@section('content')
<div class="p-6">
    <!-- Page Header -->
    <div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard GA-DADS</h1>
    <p class="text-gray-600">Selamat datang di sistem manajemen aset GA-DADS</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Assets -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-boxes text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Aset</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalAssets ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Vehicles -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-car text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Kendaraan</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalVehicles ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Splicers -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-tools text-yellow-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Splicers</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalSplicers ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Other Assets -->
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-cube text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Lainnya</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalOthers ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <!-- Asset Distribution Chart -->
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribusi Aset</h3>
            <div style="height: 250px;">
                <canvas id="assetChart"></canvas>
            </div>
        </div>

        <!-- Asset Condition Chart -->
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Kondisi Aset</h3>
            <div style="height: 250px;">
                <canvas id="conditionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Assets Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-4 py-3 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Aset Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nama</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Kondisi</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @if(isset($recentAssets) && $recentAssets->count() > 0)
                        @foreach($recentAssets as $asset)
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3">
                                <div class="text-sm font-medium text-gray-900">{{ $asset->name }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-800">
                                    {{ $asset->category }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 text-xs rounded
                                    @if($asset->condition == 'baik') bg-green-100 text-green-800
                                    @elseif($asset->condition == 'rusak') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($asset->condition) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">
                                {{ $asset->created_at->format('d M Y') }}
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                Tidak ada data aset
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Asset Distribution Chart
    const assetCtx = document.getElementById('assetChart').getContext('2d');
    new Chart(assetCtx, {
        type: 'doughnut',
        data: {
            labels: ['Kendaraan', 'Splicers', 'Lainnya'],
            datasets: [{
                data: [
                    {{ $totalVehicles ?? 0 }},
                    {{ $totalSplicers ?? 0 }},
                    {{ $totalOthers ?? 0 }}
                ],
                backgroundColor: [
                    '#10B981',
                    '#F59E0B',
                    '#8B5CF6'
                ],
                borderColor: [
                    '#059669',
                    '#D97706',
                    '#7C3AED'
                ],
                borderWidth: 2
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

    // Asset Condition Chart
    const conditionCtx = document.getElementById('conditionChart').getContext('2d');
    new Chart(conditionCtx, {
        type: 'bar',
        data: {
            labels: ['Baik', 'Rusak', 'Perbaikan'],
            datasets: [{
                label: 'Jumlah Aset',
                data: [
                    {{ $conditionCounts['baik'] ?? 0 }},
                    {{ $conditionCounts['rusak'] ?? 0 }},
                    {{ $conditionCounts['perbaikan'] ?? 0 }}
                ],
                backgroundColor: [
                    '#10B981',
                    '#EF4444',
                    '#F59E0B'
                ],
                borderColor: [
                    '#059669',
                    '#DC2626',
                    '#D97706'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endsection
