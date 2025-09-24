@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Riwayat Pengajuan APD (Admin)</h1>
            <a href="{{ route('apd-requests.admin-index') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-arrow-left mr-2"></i>Kembali ke Pending
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-green-500 text-white p-4 rounded-lg">
                <div class="text-lg font-semibold">{{ $requests->where('status', 'received')->count() }}</div>
                <div class="text-sm">Diterima</div>
            </div>
            <div class="bg-red-500 text-white p-4 rounded-lg">
                <div class="text-lg font-semibold">{{ $requests->where('status', 'rejected')->count() }}</div>
                <div class="text-sm">Ditolak</div>
            </div>
            <div class="bg-blue-500 text-white p-4 rounded-lg">
                <div class="text-lg font-semibold">{{ $requests->where('status', 'approved')->sum('jumlah_apd') }}</div>
                <div class="text-sm">Total APD Diterima</div>
                </div>
            </div>

        <!-- Filter Section -->
        <div class="mb-6">
            <form method="GET" action="{{ route('apd-requests.admin-history') }}" class="flex flex-wrap items-center gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>Semua</option>
                        <option value="received" {{ $statusFilter == 'received' ? 'selected' : '' }}>Diterima</option>
                        <option value="rejected" {{ $statusFilter == 'rejected' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cluster</label>
                    <select name="cluster" class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="">Semua Cluster</option>
                        @foreach($requests->pluck('nama_cluster')->unique()->sort() as $cluster)
                            <option value="{{ $cluster }}" {{ request('cluster') == $cluster ? 'selected' : '' }}>{{ $cluster }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari PIC</label>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                           class="border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                           placeholder="Nama PIC...">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Bulk Delete Form -->
        <form id="deleteForm" action="{{ route('apd-requests.admin-history-delete') }}" method="POST">
            @csrf
            @method('DELETE')
            <div class="mb-4 flex gap-2">
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700" onclick="return confirm('Yakin ingin menghapus riwayat terpilih?');">
                    <i class="fas fa-trash mr-2"></i>Hapus Terpilih
                </button>
                <a href="{{ route('apd-requests.admin-history.export-csv', request()->query()) }}" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i>Export CSV
                </a>
            </div>
            @if($requests->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2">
                                    <input type="checkbox" id="selectAll" />
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pengajuan</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team Mandor</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cluster</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah APD</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($requests as $req)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <input type="checkbox" name="ids[]" value="{{ $req->id }}" class="rowCheckbox" />
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $req->nomor_pengajuan }}</div>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $req->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $req->user->email }}</div>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $req->team_mandor }}</div>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $req->nama_cluster }}</div>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $req->jumlah_apd }}</div>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        @if($req->status === 'received')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Diterima</span>
                                        @elseif($req->status === 'rejected')
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Ditolak</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-500">
                                        <div>Dibuat: {{ $req->created_at->format('d M Y H:i') }}</div>
                                        @if($req->approved_at)
                                            <div class="text-xs text-gray-400">Diproses: {{ $req->approved_at->format('d M Y H:i') }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm font-medium">
                                        <a href="{{ route('apd-requests.show', $req) }}" class="text-blue-600 hover:text-blue-800">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="text-gray-500">
                        <i class="fas fa-history text-4xl mb-4"></i>
                        <p class="text-lg">Belum ada riwayat pengajuan APD</p>
                        <p class="text-sm">Data akan muncul setelah ada pengajuan yang disetujui atau ditolak</p>
                    </div>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('selectAll');
        if (selectAll) {
            selectAll.addEventListener('change', function () {
                document.querySelectorAll('.rowCheckbox').forEach(function (checkbox) {
                    checkbox.checked = selectAll.checked;
                });
            });
        }
    });
</script>
@endpush
