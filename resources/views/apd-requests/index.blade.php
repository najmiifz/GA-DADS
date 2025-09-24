@extends('layouts.app')

@section('content')
<div class="px-6 py-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Pengajuan APD Saya</h1>
            <a href="{{ route('apd-requests.create') }}" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                <i class="fas fa-plus mr-2"></i>Pengajuan Baru
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                {{ session('success') }}
            </div>
        @endif

    <!-- Filter Tabs -->
    @php $all = auth()->user()->apdRequests; @endphp
    <div class="mb-6 overflow-x-auto">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-4">
                    <a href="{{ route('apd-requests.index', ['status' => 'all']) }}"
                       class="py-2 px-1 border-b-2 font-medium text-sm {{ request('status')=='all' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Semua
                        <span class="bg-gray-100 text-gray-900 ml-2 py-0.5 px-2.5 rounded-full text-xs">{{ $all->count() }}</span>
                    </a>
                    <a href="{{ route('apd-requests.index', ['status' => 'pending']) }}"
                       class="py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'pending' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Pending
                        <span class="bg-yellow-100 text-yellow-800 ml-2 py-0.5 px-2.5 rounded-full text-xs">{{ $all->where('status', 'pending')->count() }}</span>
                    </a>
                    <a href="{{ route('apd-requests.index', ['status' => 'delivery']) }}"
                       class="py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'delivery' ? 'border-blue-600 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Dikirim
                        <span class="bg-blue-100 text-blue-800 ml-2 py-0.5 px-2.5 rounded-full text-xs">{{ $all->where('status', 'delivery')->count() }}</span>
                    </a>
                    <a href="{{ route('apd-requests.index', ['status' => 'received']) }}"
                       class="py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'received' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Diterima
                        <span class="bg-green-100 text-green-800 ml-2 py-0.5 px-2.5 rounded-full text-xs">{{ $all->where('status', 'received')->count() }}</span>
                    </a>
                    <a href="{{ route('apd-requests.index', ['status' => 'rejected']) }}"
                       class="py-2 px-1 border-b-2 font-medium text-sm {{ request('status') == 'rejected' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Ditolak
                        <span class="bg-red-100 text-red-800 ml-2 py-0.5 px-2.5 rounded-full text-xs">{{ $requests->where('status', 'rejected')->count() }}</span>
                    </a>
                </nav>
            </div>
        </div>

        @if($requests->count() > 0)
            <!-- Mobile: card list (visible on small screens) -->
            <div class="space-y-3 md:hidden">
                @foreach($requests as $request)
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $request->nomor_pengajuan }}</div>
                                <div class="text-xs text-gray-500">{{ $request->team_mandor }} · {{ $request->nama_cluster }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm font-semibold text-gray-900">{{ $request->jumlah_apd }}</div>
                                <div class="text-xs text-gray-500">{{ $request->created_at->format('d M Y') }}</div>
                            </div>
                        </div>
                        <div class="mt-3 flex items-center justify-between">
                            <div>
                                @if($request->status == 'pending')
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                @elseif($request->status == 'delivery')
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">Dikirim</span>
                                @elseif($request->status == 'approved')
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Disetujui</span>
                                @elseif($request->status == 'received')
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Diterima</span>
                                @elseif($request->status == 'rejected')
                                    <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Ditolak</span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                @if(!empty($request->id) && is_numeric($request->id))
                                    <a href="{{ route('apd-requests.show', $request->id) }}" class="bg-white border border-gray-200 px-3 py-2 rounded text-sm text-blue-600">Detail</a>
                                @else
                                    <span class="bg-white border border-gray-200 px-3 py-2 rounded text-sm text-gray-400">Detail</span>
                                @endif
                                @if($request->status == 'rejected')
                                    @if(!empty($request->id) && is_numeric($request->id))
                                        <a href="{{ route('apd-requests.edit', $request->id) }}" class="bg-orange-600 text-white px-3 py-2 rounded text-sm">Edit</a>
                                    @else
                                        <span class="px-3 py-2 rounded text-sm text-gray-400 bg-gray-100">Edit</span>
                                    @endif
                                @endif
                                @if($request->status == 'delivery')
                                    @if(!empty($request->id) && is_numeric($request->id))
                                        <form action="{{ route('apd-requests.receive', $request->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-green-600 text-white px-3 py-2 rounded text-sm">Terima</button>
                                        </form>
                                    @else
                                        <button disabled class="bg-gray-200 text-gray-500 px-3 py-2 rounded text-sm">Terima</button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Table for md+ screens -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full w-full whitespace-nowrap bg-white border border-gray-200 rounded-lg">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Pengajuan</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Team Mandor</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cluster</th>
                            <th class="hidden sm:table-cell px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah APD</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($requests as $request)
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $request->nomor_pengajuan }}</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $request->team_mandor }}</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $request->nama_cluster }}</div>
                                </td>
                                <td class="hidden sm:table-cell px-3 py-2 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $request->jumlah_apd }}</div>
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    @if($request->status == 'pending')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            <i class="fas fa-clock mr-1"></i>
                                            Pending
                                        </span>
                                    @elseif($request->status == 'delivery')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                            <i class="fas fa-shipping-fast mr-1"></i>
                                            Dikirim
                                        </span>
                                    @elseif($request->status == 'approved')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>
                                            Disetujui
                                        </span>
                                    @elseif($request->status == 'received')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            <i class="fas fa-check mr-1"></i>
                                            Diterima
                                        </span>
                                    @elseif($request->status == 'rejected')
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            <i class="fas fa-times mr-1"></i>
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $request->created_at->format('d/m/Y H:i') }}</div>
                                    @if($request->approved_at)
                                        <div class="text-xs text-gray-500">Diproses: {{ $request->approved_at->format('d/m/Y H:i') }}</div>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-sm space-x-2">
                                    @php $state = strtolower($request->status ?? ''); @endphp
                                    @switch($state)
                                        @case('pending')
                                            @if(!empty($request->id) && is_numeric($request->id))
                                                <a href="{{ route('apd-requests.edit', $request->id) }}" class="px-2 py-1 bg-gray-200 rounded-md text-xs">Edit</a>
                                                <a href="{{ route('apd-requests.show', $request->id) }}" class="px-2 py-1 bg-blue-200 rounded-md text-xs">Detail</a>
                                            @endif
                                            @break
                                        @case('approved')
                                        @case('received')
                                        @case('rejected')
                                            @if(!empty($request->id) && is_numeric($request->id))
                                                <a href="{{ route('apd-requests.show', $request->id) }}" class="px-2 py-1 bg-blue-200 rounded-md text-xs">Detail</a>
                                            @endif
                                            @break
                                        @case('delivery')
                                            @if(!empty($request->id) && is_numeric($request->id))
                                                @if(auth()->id() === $request->user_id)
                                                    <form action="{{ route('apd-requests.receive', $request->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="px-2 py-1 bg-green-200 rounded-md text-xs">Terima</button>
                                                    </form>
                                                    <a href="{{ route('apd-requests.show', $request->id) }}" class="px-2 py-1 bg-blue-200 rounded-md text-xs">Detail</a>
                                                @elseif(auth()->user()->can('kelola-akun'))
                                                    <form action="{{ route('apd-requests.restock', $request->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="px-2 py-1 bg-purple-200 rounded-md text-xs">Restock</button>
                                                    </form>
                                                    <a href="{{ route('apd-requests.show', $request->id) }}" class="px-2 py-1 bg-blue-200 rounded-md text-xs">Detail</a>
                                                @else
                                                    <a href="{{ route('apd-requests.show', $request->id) }}" class="px-2 py-1 bg-blue-200 rounded-md text-xs">Detail</a>
                                                @endif
                                            @endif
                                            @break
                                        @default
                                            @if(!empty($request->id) && is_numeric($request->id))
                                                <a href="{{ route('apd-requests.show', $request->id) }}" class="px-2 py-1 bg-blue-200 rounded-md text-xs">Detail</a>
                                            @endif
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-500">
                    <i class="fas fa-hard-hat text-4xl mb-4"></i>
                    <p class="text-lg">Belum ada pengajuan APD</p>
                    <p class="text-sm">Mulai buat pengajuan APD pertama Anda</p>
                    <div class="mt-4">
                        <a href="{{ route('apd-requests.create') }}" class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-plus mr-2"></i>Buat Pengajuan
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
