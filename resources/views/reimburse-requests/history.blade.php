@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Riwayat Pengajuan Reimburse</h1>
            @if(auth()->user()->role !== 'admin')
                <a href="{{ route('reimburse-requests.create') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    <i class="fas fa-plus mr-2"></i>Pengajuan Baru
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-green-500 text-white p-4 rounded-lg">
                <div class="text-lg font-semibold">{{ $requests->where('status', 'approved')->count() }}</div>
                <div class="text-sm">Disetujui</div>
            </div>
            <div class="bg-red-500 text-white p-4 rounded-lg">
                <div class="text-lg font-semibold">{{ $requests->where('status', 'rejected')->count() }}</div>
                <div class="text-sm">Ditolak</div>
            </div>
            <div class="bg-blue-500 text-white p-4 rounded-lg">
                <div class="text-lg font-semibold">Rp {{ number_format($requests->where('status', 'approved')->sum('biaya'), 0, ',', '.') }}</div>
                <div class="text-sm">Total Diterima</div>
            </div>
        </div>

        @if($requests->count() > 0)
            <!-- Mobile: stacked cards -->
            <div class="space-y-3 md:hidden">
                @foreach($requests as $request)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $request->nomor_pengajuan }}</div>
                                <div class="text-xs text-gray-500">{{ $request->user->name ?? '-' }} &middot; {{ $request->asset->merk ?? 'N/A' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900">Rp {{ number_format($request->biaya, 0, ',', '.') }}</div>
                                <div class="text-xs text-gray-500">{{ $request->tanggal_service ? \Carbon\Carbon::parse($request->tanggal_service)->format('d/m/Y') : 'N/A' }}</div>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-gray-700">
                            <span class="font-semibold">Keterangan:</span> {{ $request->keterangan ?? '-' }}
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <div>
                                @if($request->status == 'approved')
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Disetujui</span>
                                @elseif($request->status == 'rejected')
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('reimburse-requests.show', $request) }}" class="bg-white border border-gray-200 px-3 py-2 rounded text-sm text-blue-600">Detail</a>
                                @if($request->status === 'rejected' && $request->user_id === auth()->id())
                                    <a href="{{ route('reimburse-requests.edit', $request) }}" class="bg-orange-600 text-white px-3 py-2 rounded text-sm">Edit</a>
                                @endif
                                @if($request->bukti_struk)
                                    <a href="{{ asset('storage/' . $request->bukti_struk) }}" target="_blank" class="bg-green-100 text-green-800 px-3 py-2 rounded text-sm">File</a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Table for md+ screens -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NO</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NOMOR PENGAJUAN</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PENGAJU</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">JUMLAH</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">KETERANGAN</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">STATUS</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">TANGGAL</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($requests as $request)
                            <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('reimburse-requests.show', $request) }}'">
                                <td class="px-3 py-2 whitespace-nowrap">{{ $loop->iteration }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $request->nomor_pengajuan }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $request->user->name ?? '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">Rp {{ number_format($request->biaya, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ !empty($request->keterangan) ? $request->keterangan : '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $request->status ?? '-' }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $request->created_at->format('d M Y') }}</td>
                                <td class="px-3 py-2 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('reimburse-requests.show', $request) }}" class="text-blue-600 hover:text-blue-900" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($request->status === 'rejected' && $request->user_id === auth()->id())
                                            <a href="{{ route('reimburse-requests.edit', $request) }}" class="text-orange-600 hover:text-orange-800" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($request->bukti_struk)
                                            <a href="{{ asset('storage/' . $request->bukti_struk) }}" target="_blank" class="text-green-600 hover:text-green-900" title="File">
                                                <i class="fas fa-file"></i>
                                            </a>
                                        @endif
                                    </div>
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
                    <p class="text-lg">Belum ada riwayat pengajuan reimburse</p>
                    <p class="text-sm">Riwayat pengajuan yang disetujui atau ditolak akan muncul di sini</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
