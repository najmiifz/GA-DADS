@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Pengajuan Reimburse</h1>
        @if(auth()->user()->role !== 'admin')
            <a href="{{ route('reimburse-requests.create') }}"
               class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Ajukan Reimburse
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($requests->isEmpty())
        <p class="text-gray-600">Belum ada pengajuan reimburse pending.</p>
    @else
    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th>No</th>
                        <th>Nomor Pengajuan</th>
                        <th>PIC</th>
                        <th>Asset</th>
                        <th>Biaya</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($requests as $req)
                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('reimburse-requests.show', $req) }}'">
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $req->nomor_pengajuan }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $req->user->name ?? '-' }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">{{ $req->asset->merk }}</td>
                        <td class="px-4 py-2 text-sm text-gray-900">Rp {{ number_format($req->biaya,0,',','.') }}</td>
                        <td class="px-4 py-2 text-sm">
                            @if($req->status === 'approved')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check mr-1"></i>Disetujui
                                    </span>
                            @elseif($req->status === 'rejected')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <i class="fas fa-times mr-1"></i>Ditolak
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>Pending
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-2 text-sm text-gray-500">{{ $req->tanggal_service->format('d M Y') }}</td>
                        <td class="px-4 py-2 text-sm font-medium">
                            <a href="{{ route('reimburse-requests.show', $req) }}" class="text-blue-600 hover:text-blue-800">Detail</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    <!-- Mobile Cards -->
    <div class="md:hidden space-y-4">
            @foreach($requests as $req)
                <div class="bg-white p-4 rounded-lg shadow cursor-pointer" onclick="window.location='{{ route('reimburse-requests.show', $req) }}'">
                    <div class="flex justify-between items-center mb-2">
                        <div class="font-semibold text-gray-900">{{ $req->asset->merk ?? 'N/A' }} {{ $req->asset->tipe ?? '' }}</div>
                        <div class="text-xs text-gray-500">{{ $req->nomor_pengajuan }}</div>
                    </div>
                    <div class="text-sm text-gray-700 mb-1">PIC: <span class="font-medium text-gray-900">{{ $req->user->name ?? '-' }}</span></div>
                    <div class="text-sm text-gray-700 mb-1">Biaya: Rp {{ number_format($req->biaya,0,',','.') }}</div>
                    <div class="text-sm text-gray-700 mb-1">Status: <span class="font-medium text-gray-900">{{ ucfirst($req->status) }}</span></div>
                    <div class="text-sm text-gray-500 mb-2">Tanggal: {{ $req->tanggal_service->format('d M Y') }}</div>
                    <a href="{{ route('reimburse-requests.show', $req) }}" class="text-blue-600 hover:underline text-sm">Detail</a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
