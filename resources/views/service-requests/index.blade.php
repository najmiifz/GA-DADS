@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">
            @if(in_array(auth()->user()->role, ['admin', 'super-admin']))
                Kelola Pengajuan Service
            @else
                Pengajuan Service
            @endif
        </h1>
        @if(!in_array(auth()->user()->role, ['admin', 'super-admin']))
            <a href="{{ route('service-requests.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-plus mr-2"></i>Buat Pengajuan Baru
            </a>
        @else
            <div class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                Kelola dan setujui pengajuan service dari user
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        @if($serviceRequests->count() > 0)
            {{-- Mobile card list (visible on small screens) --}}
            <div class="md:hidden">
                <div class="divide-y divide-gray-200">
                    @foreach($serviceRequests as $request)
                        <div class="p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $request->nomor_pengajuan }}</div>
                                    <div class="text-sm text-gray-500">{{ $request->asset->merk ?? 'N/A' }} {{ $request->asset->tipe ?? '' }}</div>
                                    <div class="text-sm text-gray-500">{{ $request->asset->serial_number ?? '' }}</div>
                                </div>
                                <div class="text-sm text-gray-500 text-right">
                                    <div class="mb-1">{{ $request->created_at->format('d/m/Y') }}</div>
                                    <div>{!! $request->status_badge !!}</div>
                                </div>
                            </div>

                            <div class="mt-3 flex items-center justify-between">
                                <div class="text-sm text-gray-700">
                                    <div>{{ $request->user->name }}</div>
                                    <div class="text-xs text-gray-500">KM: {{ $request->km_saat_ini }}</div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('service-requests.show', $request) }}" class="text-blue-600 hover:text-blue-900 text-sm">Lihat</a>

                                    @if(in_array(auth()->user()->role, ['admin', 'super-admin']))
                                        @if($request->status === 'pending')
                                            <button onclick="approveRequest({{ $request->id }})" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">Setujui</button>
                                            <button onclick="rejectRequest({{ $request->id }})" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">Tolak</button>
                                        @elseif($request->status === 'approved')
                                            <span class="text-xs text-green-600 font-medium">Sudah Disetujui</span>
                                        @endif
                                    @else
                                        @if(($request->isPending() || $request->isRejected()) && $request->user_id === auth()->id())
                                            <a href="{{ route('service-requests.edit', $request) }}" class="text-yellow-600 hover:text-yellow-900 text-sm">Edit</a>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="px-4 py-3 bg-gray-50">
                    {{ $serviceRequests->links() }}
                </div>
            </div>

            {{-- Desktop/table view (hidden on small screens) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nomor Pengajuan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Asset
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                KM
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estimasi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($serviceRequests as $request)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $request->nomor_pengajuan }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $request->asset->merk ?? 'N/A' }} {{ $request->asset->tipe ?? '' }}</div>
                                        <div class="text-sm text-gray-500">{{ $request->asset->serial_number ?? 'N/A' }}</div>
                                        @if($request->lokasi_project)
                                            <div class="text-sm text-gray-500">Lokasi: {{ $request->lokasi_project }}</div>
                                        @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $request->user->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $request->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $request->km_saat_ini }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($request->estimasi_harga)
                                        <span class="text-green-600 font-medium">
                                            Rp {{ number_format($request->estimasi_harga, 0, ',', '.') }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {!! $request->status_badge !!}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $request->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('service-requests.show', $request) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                        Lihat
                                    </a>

                                    @if(in_array(auth()->user()->role, ['admin', 'super-admin']))
                                        {{-- Aksi untuk Admin --}}
                                        @if($request->status === 'pending')
                                            <button onclick="approveRequest({{ $request->id }})"
                                                    class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs mr-2">
                                                Setujui
                                            </button>
                                            <button onclick="rejectRequest({{ $request->id }})"
                                                    class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs mr-2">
                                                Tolak
                                            </button>
                                        @elseif($request->status === 'approved')
                                            <span class="text-xs text-green-600 font-medium">Sudah Disetujui</span>
                                        @endif
                                    @else
                                        {{-- Aksi untuk User --}}
                                        @if(($request->isPending() || $request->isRejected()) && $request->user_id === auth()->id())
                                            <a href="{{ route('service-requests.edit', $request) }}" class="text-yellow-600 hover:text-yellow-900 mr-3">
                                                Edit
                                            </a>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-500 text-lg mb-4">
                    <i class="fas fa-tools text-4xl mb-4"></i>
                    @if(in_array(auth()->user()->role, ['admin', 'super-admin']))
                        <p>Belum ada pengajuan service yang perlu dikelola</p>
                        <p class="text-sm mt-2">Pengajuan dari user akan muncul di sini</p>
                    @else
                        <p>Belum ada pengajuan service</p>
                        <a href="{{ route('service-requests.create') }}" class="inline-block mt-4 bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition duration-200">
                            Buat Pengajuan Pertama
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@if(in_array(auth()->user()->role, ['admin', 'super-admin']))
<script>
function approveRequest(id) {
    console.log('Approving request ID:', id);
    if (confirm('Apakah Anda yakin ingin menyetujui pengajuan service ini?')) {
        console.log('User confirmed approval');
        fetch(`/service-requests/${id}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',             // Tambahkan header ini
                'X-Requested-With': 'XMLHttpRequest',      // dan ini
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                location.reload();
            } else {
                alert('Terjadi kesalahan: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses permintaan');
        });
    }
}

function rejectRequest(id) {
    console.log('Rejecting request ID:', id);
    const reason = prompt('Masukkan alasan penolakan:');
    if (reason && reason.trim()) {
        console.log('User provided reason:', reason);
        fetch(`/service-requests/${id}/reject`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',             // Tambahkan header ini
                'X-Requested-With': 'XMLHttpRequest',      // dan ini
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ reason: reason.trim() })
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                location.reload();
            } else {
                alert('Terjadi kesalahan: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat memproses permintaan');
        });
    }
}
</script>
@endif

@endsection
