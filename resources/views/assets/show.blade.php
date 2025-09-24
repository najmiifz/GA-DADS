@extends('layouts.app')

@section('title', 'Detail Asset')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-red-600">Detail Asset</h1>
                <p class="text-red-600 mt-2">Informasi lengkap asset {{ $asset->merk ?? 'N/A' }}</p>
            </div>
            <div class="space-x-3">
                <a href="{{ route('assets.edit', $asset) }}"
                   class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Asset
                </a>
                <a href="{{ route('assets.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-red-200 text-red-800 rounded-lg hover:bg-red-300 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Asset Overview Card -->
        <div class="bg-white rounded-xl shadow-sm border border-red-200 mb-8">
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <div>
                            @if($asset->foto_aset)
                                <img src="{{ Storage::url($asset->foto_aset) }}" alt="Foto Asset" class="w-64 h-64 rounded-lg object-cover border">
                            @endif
                            <div class="mt-4 space-y-3">
                                @if($asset->foto_stnk)
                                    <div>
                                        <div class="text-sm text-gray-500">Foto STNK</div>
                                        <a href="{{ Storage::url($asset->foto_stnk) }}" target="_blank">
                                            <img src="{{ Storage::url($asset->foto_stnk) }}" alt="Foto STNK" class="w-40 h-28 rounded-md object-cover border">
                                        </a>
                                    </div>
                                @endif
                                @if($asset->foto_kendaraan)
                                    <div>
                                        <div class="text-sm text-gray-500">Foto Kendaraan</div>
                                        <a href="{{ Storage::url($asset->foto_kendaraan) }}" target="_blank">
                                            <img src="{{ Storage::url($asset->foto_kendaraan) }}" alt="Foto Kendaraan" class="w-40 h-28 rounded-md object-cover border">
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if(strtolower($asset->tipe) === 'kendaraan')
                        <div>
                            <div class="text-sm text-gray-500">Nomor Plat Kendaraan</div>
                            <div class="text-lg font-mono text-gray-900">{{ $asset->plate_number ?? 'N/A' }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-green-600 text-sm font-medium">Harga Beli</div>
                        <div class="text-green-900 text-xl font-bold">
                Rp {{ number_format($asset->harga_beli ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-blue-600 text-sm font-medium">Tanggal Beli</div>
                        <div class="text-blue-900 text-xl font-bold">
                            {{ $asset->tanggal_beli ? \Carbon\Carbon::parse($asset->tanggal_beli)->format('d M Y') : 'N/A' }}
                        </div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <div class="text-purple-600 text-sm font-medium">PIC</div>
                        <div class="flex items-center space-x-3">
                            @if($asset->user && $asset->user->avatar_url)
                                <img src="{{ $asset->user->avatar_url }}" alt="Foto PIC" class="w-10 h-10 rounded-full object-cover border">
                            @endif
                            <div class="text-purple-900 text-xl font-bold">
                                @if($asset->user)
                                    <a href="{{ route('users.edit', $asset->user->id) }}" class="hover:underline">
                                        {{ $asset->user->name }}
                                    </a>
                                @else
                                    {{ optional($asset->user)->name ?? $asset->pic ?? 'N/A' }}
                                @endif
                            </div>
                        </div>
                    </div>
                    @if(in_array(strtolower($asset->tipe), ['kendaraan', 'splicer']))
                    <div class="bg-orange-50 p-4 rounded-lg">
                        <div class="text-orange-600 text-sm font-medium">Total Biaya Servis</div>
                        <div class="text-orange-900 text-xl font-bold">Rp {{ number_format($asset->total_servis ?? 0,0,',','.') }}</div>
                    </div>
                    @endif
                    {{-- Vehicle Tax Data --}}
                    @if(strtolower($asset->tipe) === 'kendaraan')
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="text-yellow-600 text-sm font-medium">Tanggal Pajak</div>
                        <div class="text-yellow-900 text-xl font-bold">
                            {{ $asset->tanggal_pajak ? \Carbon\Carbon::parse($asset->tanggal_pajak)->format('d M Y') : 'N/A' }}
                        </div>
                        <div class="text-yellow-600 text-sm font-medium mt-2">Jumlah Pajak</div>
                        <div class="text-yellow-900 text-xl font-bold">
                            Rp {{ number_format($asset->jumlah_pajak ?? 0, 0, ',', '.') }}
                        </div>
                        <div class="text-yellow-600 text-sm font-medium mt-2">Status Pajak</div>
                        <div class="text-yellow-900 text-xl font-bold capitalize">
                            {{ $asset->status_pajak ?? 'N/A' }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- Vehicle Tax Info --}}
        @if($asset->tanggal_pajak)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="text-sm font-medium text-gray-500">Tanggal Pajak</label>
                    <p class="text-gray-900 font-medium">{{ \Carbon\Carbon::parse($asset->tanggal_pajak)->format('d M Y') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Jumlah Pajak</label>
                    <p class="text-gray-900 font-medium">Rp {{ number_format($asset->jumlah_pajak ?? 0,0,',','.') }}</p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Status Pajak</label>
                    <p class="text-gray-900 font-medium capitalize">{{ $asset->status_pajak ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Service History Section -->
        @if($asset->services->count())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-history text-indigo-600 mr-2"></i>
                    Riwayat Service Aset
                </h3>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keluhan/Masalah</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Service</th>
                             <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                             <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bengkel</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Service</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($asset->services as $history)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $history->description ?? '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($history->service_date)->format('d M Y') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $asset->user->name ?? '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $history->vendor ?? '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">Rp {{ number_format($history->cost ?? 0,0,',','.') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">
                                @if($history->file_path)
                                    <a href="{{ Storage::url($history->file_path) }}" target="_blank" class="text-blue-600 hover:underline">Lihat Dokumen</a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Service Request History for Admin --}}
    @if(!empty($serviceRequests) && count($serviceRequests) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-tools text-red-600 mr-2"></i>
                    Riwayat Pengajuan Service
                </h3>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Pengajuan</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keluhan</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estimasi</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($serviceRequests as $req)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $req->nomor_pengajuan }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $req->keluhan }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $req->user->name ?? '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-green-600 font-medium">
                                @if($req->estimasi_harga)
                                    Rp {{ number_format($req->estimasi_harga,0,',','.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">
                                @if($req->biaya_servis)
                                    Rp {{ number_format($req->biaya_servis,0,',','.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap">{!! $req->status_badge !!}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                {{ $req->approved_at ? $req->approved_at->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('service-requests.show', $req) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- Reimburse Request History for Admin --}}
    @if(!empty($reimburseRequests) && count($reimburseRequests) > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-wallet text-green-600 mr-2"></i>
                    Riwayat Pengajuan Reimburse
                </h3>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Pengajuan</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengaju</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($reimburseRequests as $r)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $r->nomor_pengajuan ?? $r->id }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $r->user->name ?? ($r->pengaju_name ?? '-') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($r->biaya ?? $r->amount ?? 0,0,',','.') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $r->keterangan ?? '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{!! $r->status_badge ?? e($r->status) !!}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $r->created_at ? \Carbon\Carbon::parse($r->created_at)->format('d M Y') : '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center">
                                    <a href="{{ route('reimburse-requests.show', $r) }}" class="text-blue-600 hover:text-blue-900" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

        <!-- Previous Asset Holder History for Admin -->
        @if(auth()->user()->role === 'admin' && $asset->holderHistories->count())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-user-friends text-blue-600 mr-2"></i>
                    Riwayat Pemegang Aset Sebelumnya
                </h3>
            </div>
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemegang</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Mulai</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Selesai</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($asset->holderHistories as $holder)
                        <tr>
                            @php
                                // Determine holder display name
                                $holderName = $holder->holder_name;
                                if(\Illuminate\Support\Str::startsWith($holderName, 'user:')) {
                                    $uid = (int) \Illuminate\Support\Str::after($holderName, 'user:');
                                    $user = \App\Models\User::find($uid);
                                    $holderName = $user ? $user->name : $holderName;
                                }
                                // Process note to replace any user references
                                $rawNote = $holder->note ?? '-';
                                $processedNote = preg_replace_callback('/user:(\d+)/', function($matches) {
                                    $user = \App\Models\User::find((int)$matches[1]);
                                    return $user ? $user->name : $matches[0];
                                }, $rawNote);
                            @endphp
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $holderName }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ \Carbon\Carbon::parse($holder->start_date)->format('d M Y') }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $holder->end_date ? \Carbon\Carbon::parse($holder->end_date)->format('d M Y') : '-' }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900">{{ $processedNote }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <!-- Location & Project -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-map-marker-alt text-green-600 mr-2"></i>
                    Lokasi & Project
                </h3>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Lokasi</label>
                    <p class="text-gray-900 font-medium flex items-center">
                        <i class="fas fa-building text-gray-400 mr-2"></i>
                        {{ $asset->lokasi ?? 'N/A' }}
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Project</label>
                    <p class="text-gray-900 font-medium flex items-center">
                        <i class="fas fa-project-diagram text-gray-400 mr-2"></i>
                        {{ $asset->project ?? 'N/A' }}
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">PIC (Person in Charge)</label>
                    <p class="text-gray-900 font-medium flex items-center">
                        <i class="fas fa-user text-gray-400 mr-2"></i>
                        @if($asset->user)
                            <a href="{{ route('users.edit', $asset->user->id) }}" class="hover:underline">
                                {{ $asset->user->name }}
                            </a>
                        @else
                            {{ $asset->pic ?? 'N/A' }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        @if($asset->keterangan)
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-sticky-note text-yellow-600 mr-2"></i>
                    Keterangan
                </h3>
            </div>
            <div class="p-6">
                <p class="text-gray-700 leading-relaxed">{{ $asset->keterangan }}</p>
            </div>
        </div>
        @endif

        <!-- Asset Timeline/History -->
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-history text-indigo-600 mr-2"></i>
                    Timeline Asset
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-plus text-green-600 text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Asset Created</div>
                            <div class="text-sm text-gray-500">{{ $asset->created_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                    @if($asset->updated_at != $asset->created_at)
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-edit text-blue-600 text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">Last Updated</div>
                            <div class="text-sm text-gray-500">{{ $asset->updated_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <button onclick="window.print()"
                        class="flex items-center justify-center px-4 py-3 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200">
                    <i class="fas fa-print mr-2"></i>
                    Print Detail
                </button>
                <a href="{{ route('assets.edit', $asset) }}"
                   class="flex items-center justify-center px-4 py-3 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200">
                    <i class="fas fa-edit mr-2"></i>
                    Edit Asset
                </a>
                    <a href="{{ route('assets.export.csv', $asset) }}" target="_blank"
                       class="flex items-center justify-center px-4 py-3 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200">
                        <i class="fas fa-file-csv mr-2"></i>
                        Export CSV
                    </a>
                <button onclick="copyAssetInfo()"
                        class="flex items-center justify-center px-4 py-3 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200">
                    <i class="fas fa-copy mr-2"></i>
                    Copy Info
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let isTableView = false;
let currentPage = 1;
const itemsPerPage = 10;

function copyAssetInfo() {
    const assetInfo = `Asset Information:
- Merk: {{ $asset->merk ?? 'N/A' }}
- Jenis: {{ $asset->jenis_aset ?? 'N/A' }}
- Serial Number: {{ $asset->serial_number ?? 'N/A' }}
- Lokasi: {{ $asset->lokasi ?? 'N/A' }}
- Project: {{ $asset->project ?? 'N/A' }}
- Kondisi: {{ $asset->kondisi ?? 'N/A' }}`;

    navigator.clipboard.writeText(assetInfo).then(function() {
        window.dispatchEvent(new CustomEvent('show-notification', {
            detail: { message: 'Asset information copied to clipboard!', type: 'success' }
        }));
    });
}

function openImageModal(imageSrc, title) {
    // Create modal HTML
    const modalHTML = `
        <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50">
            <div class="relative max-w-4xl max-h-full p-4">
                <div class="bg-white rounded-lg">
                    <div class="flex justify-between items-center p-4 border-b">
                        <h3 class="text-lg font-semibold">${title}</h3>
                        <button onclick="closeImageModal()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <div class="p-4">
                        <img src="${imageSrc}" alt="${title}" class="max-w-full max-h-96 object-contain">
                    </div>
                </div>
            </div>
        </div>
    `;

    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);

    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    const modal = document.getElementById('imageModal');
    if (modal) {
        modal.remove();
        document.body.style.overflow = 'auto';
    }
}

// Toggle between card and table view
function toggleServiceView() {
    const cardView = document.getElementById('cardView');
    const tableView = document.getElementById('tableView');
    const toggleBtn = document.getElementById('toggleBtn');

    isTableView = !isTableView;

    if (isTableView) {
        cardView.classList.add('hidden');
        tableView.classList.remove('hidden');
        toggleBtn.innerHTML = '<i class="fas fa-th-large mr-1"></i> Tampilan Card';
    } else {
        cardView.classList.remove('hidden');
        tableView.classList.add('hidden');
        toggleBtn.innerHTML = '<i class="fas fa-list mr-1"></i> Tampilan Kompak';
    }

    // Re-apply filters after view change
    filterServices();
}

// Filter and search functionality
function filterServices() {
    const searchTerm = document.getElementById('serviceSearch').value.toLowerCase();
    const yearFilter = document.getElementById('yearFilter').value;
    const sortOrder = document.getElementById('sortOrder').value;

    // Get all service items
    const items = isTableView ?
        document.querySelectorAll('.service-row') :
        document.querySelectorAll('.service-item');

    let visibleItems = [];

    items.forEach(item => {
        const description = item.dataset.description || '';
        const vendor = item.dataset.vendor || '';
        const year = item.dataset.year || '';

        // Apply filters
        const matchesSearch = description.includes(searchTerm) || vendor.includes(searchTerm);
        const matchesYear = !yearFilter || year === yearFilter;

        if (matchesSearch && matchesYear) {
            item.style.display = '';
            visibleItems.push(item);
        } else {
            item.style.display = 'none';
        }
    });

    // Sort visible items
    sortServices(visibleItems, sortOrder);

    // Update pagination if needed
    updatePagination(visibleItems.length);
}

function sortServices(items, sortOrder) {
    const container = isTableView ?
        document.querySelector('#tableView tbody') :
        document.getElementById('cardView');

    // Convert to array and sort
    const sortedItems = Array.from(items).sort((a, b) => {
        switch (sortOrder) {
            case 'newest':
                return new Date(b.dataset.date || 0) - new Date(a.dataset.date || 0);
            case 'oldest':
                return new Date(a.dataset.date || 0) - new Date(b.dataset.date || 0);
            case 'cost_high':
                return parseInt(b.dataset.cost || 0) - parseInt(a.dataset.cost || 0);
            case 'cost_low':
                return parseInt(a.dataset.cost || 0) - parseInt(b.dataset.cost || 0);
            default:
                return 0;
        }
    });

    // Re-append sorted items
    sortedItems.forEach(item => {
        container.appendChild(item);
    });
}

function updatePagination(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const container = document.getElementById('paginationContainer');

    if (!container || totalPages <= 1) {
        if (container) container.innerHTML = '';
        return;
    }

    let paginationHTML = '';

    for (let i = 1; i <= totalPages; i++) {
        const activeClass = i === currentPage ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-50';
        paginationHTML += `
            <button onclick="goToPage(${i})" class="px-3 py-2 text-sm border rounded-lg ${activeClass}">
                ${i}
            </button>
        `;
    }

    container.innerHTML = paginationHTML;
}

function goToPage(page) {
    currentPage = page;
    const items = isTableView ?
        document.querySelectorAll('.service-row') :
        document.querySelectorAll('.service-item');

    items.forEach((item, index) => {
        if (item.style.display !== 'none') {
            const itemPage = Math.ceil((index + 1) / itemsPerPage);
            item.style.display = itemPage === page ? '' : 'none';
        }
    });

    updatePagination(items.length);
}

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Search input
    document.getElementById('serviceSearch').addEventListener('input', filterServices);

    // Filter selects
    document.getElementById('yearFilter').addEventListener('change', filterServices);
    document.getElementById('sortOrder').addEventListener('change', filterServices);

    // Initial pagination setup
    const totalServices = {{ $asset->services->count() }};
    if (totalServices > itemsPerPage) {
        updatePagination(totalServices);
        goToPage(1);
    }
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeImageModal();
    }
});
</script>
@endpush
@endsection
