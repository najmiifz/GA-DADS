@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Pengajuan SPJ Saya</h1>
            <a href="{{ route('spj.create') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-plus mr-2"></i>Pengajuan SPJ
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter Tabs -->
        <div class="mb-6 overflow-x-auto">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-4">
                    <a href="{{ route('spj-requests.index', ['status' => 'all']) }}"
                       class="py-2 px-1 border-b-2 font-medium text-sm {{ $status == 'all' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Semua
                        <span class="bg-gray-100 text-gray-900 ml-2 py-0.5 px-2.5 rounded-full text-xs">{{ $allRequests->count() }}</span>
                    </a>
                    <a href="{{ route('spj-requests.index', ['status' => 'pending']) }}"
                       class="py-2 px-1 border-b-2 font-medium text-sm {{ $status == 'pending' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Pending
                        <span class="bg-yellow-100 text-yellow-800 ml-2 py-0.5 px-2.5 rounded-full text-xs">{{ $allRequests->where('status', 'pending')->count() }}</span>
                    </a>
                          <a href="{{ route('spj-requests.index', ['status' => 'approved']) }}"
                              class="py-2 px-1 border-b-2 font-medium text-sm {{ $status == 'approved' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                Disetujui
                        <span class="bg-green-100 text-green-800 ml-2 py-0.5 px-2.5 rounded-full text-xs">{{ $allRequests->where('status', 'approved')->count() }}</span>
                    </a>
                    <a href="{{ route('spj-requests.index', ['status' => 'rejected']) }}"
                       class="py-2 px-1 border-b-2 font-medium text-sm {{ $status == 'rejected' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Ditolak
                        <span class="bg-red-100 text-red-800 ml-2 py-0.5 px-2.5 rounded-full text-xs">{{ $allRequests->where('status', 'rejected')->count() }}</span>
                    </a>
                </nav>
            </div>
        </div>

        @if($requests->count() > 0)
            <!-- Mobile: card list -->
            <div class="space-y-3 lg:hidden">
                @foreach($requests as $request)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-sm font-medium text-gray-900">SPJ #{{ $request->id }}</div>
                                <div class="text-xs text-gray-500">{{ $request->nama_pegawai }} · {{ $request->keperluan }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900">{{ $request->biaya_estimasi }}</div>
                                <div class="text-xs text-gray-500">{{ $request->spj_date->format('d M Y') }}</div>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <div>
                                @if($request->status == 'pending')
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($request->status == 'approved')
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Disetujui</span>
                                @elseif($request->status == 'rejected')
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('spj.view', $request) }}" class="bg-white border border-gray-200 px-3 py-2 rounded text-sm text-blue-600">Detail</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop: table (hidden on small screens) -->
            <div class="hidden lg:block overflow-x-auto">
                <div class="min-w-[900px]">
                    <table class="min-w-full w-full whitespace-nowrap bg-white border border-gray-200 rounded-lg">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. SPJ</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pegawai</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keperluan</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estimasi Biaya</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($requests as $request)
                                <tr>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">SPJ #{{ $request->id }}</div>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $request->nama_pegawai }}</div>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ Str::limit($request->keperluan, 30) }}</div>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $request->biaya_estimasi }}</div>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        @if($request->status == 'pending')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1"></i>
                                                Pending
                                            </span>
                                        @elseif($request->status == 'approved')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                <i class="fas fa-check mr-1"></i>
                                                Disetujui
                                            </span>
                                        @elseif($request->status == 'rejected')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                                <i class="fas fa-times mr-1"></i>
                                                Ditolak
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $request->spj_date->format('d/m/Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ $request->created_at->format('H:i') }}</div>
                                    </td>
                                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('spj.view', $request) }}" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $requests->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-500">
                    <i class="fas fa-file-alt text-4xl mb-4"></i>
                    <p class="text-lg">Belum ada pengajuan SPJ</p>
                    <p class="text-sm">Mulai buat pengajuan SPJ pertama Anda</p>
                    <div class="mt-4">
                        <a href="{{ route('spj.create') }}" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-plus mr-2"></i>Buat Pengajuan
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
