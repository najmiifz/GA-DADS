@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50">
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6 flex justify-between items-center">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Kembali ke Dashboard
            </a>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition-colors duration-200">
                    <i class="fas fa-sign-out-alt mr-2"></i>
                    Logout
                </button>
            </form>
        </div>

        <!-- Asset Photos -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="relative">
                @if($asset->foto_aset)
                    <img src="{{ asset('storage/' . $asset->foto_aset) }}"
                         alt="Foto {{ $asset->merk }}"
                         class="w-full h-64 sm:h-80 object-cover cursor-pointer"
                         onclick="openImageModal('{{ asset('storage/' . $asset->foto_aset) }}', 'Foto {{ $asset->merk }}')">
                @else
                    <div class="w-full h-64 sm:h-80 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                        <div class="text-center">
                            <i class="fas fa-image text-gray-400 text-6xl mb-4"></i>
                            <p class="text-gray-500 font-medium">Foto belum tersedia</p>
                            @can('kelola-aset')
                                <p class="text-gray-400 text-sm mt-2">Klik Edit Asset untuk upload foto</p>
                            @endcan
                        </div>
                    </div>
                @endif

                <!-- Image indicator -->
                <div class="absolute top-4 right-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                    <i class="fas fa-camera mr-1"></i>
                    1/1
                </div>

            </div>
        </div>


        <!-- Asset Title -->
        <div class="mb-6 flex items-start justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $asset->merk }} ({{ $asset->tahun_beli }})</h1>
                <p class="text-lg text-gray-600">{{ $asset->jenis_aset }}</p>
            </div>
            <div class="mt-1">
                @php
                    $status = $asset->pic === 'Available' || ($asset->status ?? '') === 'Available' ? 'Available' : ($asset->status ?? $asset->pic ?? 'N/A');
                @endphp
                @if(strtolower($status) === 'available')
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">Tersedia</span>
                @elseif(strtolower($status) === 'rusak')
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">Rusak</span>
                @elseif(strtolower($status) === 'hilang')
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-800">Hilang</span>
                @else
                    <span class="inline-flex px-3 py-1 rounded-full text-sm font-semibold bg-yellow-100 text-yellow-800">{{ $status }}</span>
                @endif
            </div>
        </div>


        <!-- Asset Details -->
        <section class="bg-white rounded-2xl shadow-lg p-6 mb-6 space-y-4">
            <h2 class="text-xl font-semibold text-gray-900 border-b pb-2">Detail Asset</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Type -->
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-tag text-gray-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tipe</p>
                        <p class="font-semibold text-gray-900">{{ $asset->tipe ?? 'Tidak ada data' }}</p>
                    </div>
                </div>

                <!-- Project -->
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-project-diagram text-red-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Project</p>
                        <p class="font-semibold text-gray-900">{{ $asset->project }}</p>
                    </div>
                </div>

                <!-- Location -->
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-map-marker-alt text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Lokasi</p>
                        <p class="font-semibold text-gray-900">{{ $asset->lokasi }}</p>
                    </div>
                </div>

                <!-- Status -->
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-info-circle text-indigo-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Status Aset</p>
                        @php
                            $statusLabel = $asset->status ?? $asset->pic ?? 'N/A';
                        @endphp
                        <p class="font-semibold text-gray-900">{{ $statusLabel }}</p>
                    </div>
                </div>

                <!-- Serial Number -->
                @if($asset->serial_number)
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-barcode text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Serial Number</p>
                        <p class="font-semibold text-gray-900">{{ $asset->serial_number }}</p>
                    </div>
                </div>
                @endif

                <!-- Purchase Date -->
                @if($asset->tanggal_beli)
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-calendar text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tanggal Beli</p>
                        <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($asset->tanggal_beli)->format('d M Y') }}</p>
                    </div>
                </div>
                @endif

                <!-- Asset Type -->
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-cogs text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Jenis Aset</p>
                        <p class="font-semibold text-gray-900">{{ $asset->jenis_aset ?? 'Tidak ada data' }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Owner Info -->
        <section class="bg-white rounded-2xl shadow-lg p-6 mb-6 space-y-4">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-gradient-to-br from-red-400 to-red-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-user text-white text-xl"></i>
                </div>
                <div class="flex-1">
                    <p class="text-sm text-gray-500">Pemegang Asset</p>
                    <p class="text-xl font-bold text-gray-900">{{ $asset->user->name ?? 'Tidak ada data' }}</p>
                    @if($asset->user && $asset->user->lokasi)
                        <p class="text-gray-600">{{ $asset->user->lokasi }}</p>
                    @endif
                </div>
                @if($asset->user && $asset->user->phone)
                <div class="flex space-x-2">
                    <button class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-colors">
                        <i class="fas fa-phone text-gray-600"></i>
                    </button>
                    <button class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center hover:bg-gray-200 transition-colors">
                        <i class="fas fa-comment text-gray-600"></i>
                    </button>
                </div>
                @endif
            </div>
        </section>


        {{-- Vehicle Tax Info (for Kendaraan) --}}
    @if(strtolower($asset->tipe) === 'kendaraan' || strtolower($asset->jenis_aset) === 'motor')
        <section class="bg-white rounded-2xl shadow-lg p-6 mb-6 space-y-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pajak Kendaraan</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Tanggal Pajak</p>
                    <p class="font-medium text-gray-900">{{ $asset->tanggal_pajak ? \Carbon\Carbon::parse($asset->tanggal_pajak)->format('d M Y') : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Jumlah Pajak</p>
                    <p class="font-medium text-gray-900">Rp {{ number_format($asset->jumlah_pajak ?? 0,0,',','.') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Status Pajak</p>
                    <p class="font-medium text-gray-900 capitalize">{{ $asset->status_pajak ?? 'N/A' }}</p>
                </div>
            </div>
        </section>
        {{-- STNK Photo (for Kendaraan) --}}
        @if($asset->foto_stnk)
        <section class="bg-white rounded-2xl shadow-lg p-6 mb-6 space-y-4">
            <h2 class="text-lg font-semibold text-gray-900 border-b pb-2">Foto STNK</h2>
            <div class="flex justify-center">
                <img src="{{ asset('storage/' . $asset->foto_stnk) }}" alt="Foto STNK" class="w-full max-w-xs object-contain rounded-lg cursor-pointer"
                     onclick="openImageModal('{{ asset('storage/' . $asset->foto_stnk) }}', 'Foto STNK')">
            </div>
        </section>
        @endif
    @endif

        <!-- Additional Info -->
        @if($asset->keterangan)
        <section class="bg-white rounded-2xl shadow-lg p-6 mb-6 space-y-4">
            <h2 class="text-lg font-semibold text-gray-900 border-b pb-2">Keterangan</h2>
            <p class="text-gray-700 leading-relaxed">{{ $asset->keterangan }}</p>
        </section>
        @endif

        <!-- Service History -->
    @if($serviceHistories->count())
        <section class="bg-white rounded-2xl shadow-lg p-6 mb-6 space-y-4">
            <div class="flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-900 border-b pb-2">Riwayat Service</h2>
                <div class="text-sm text-gray-600">Total: {{ $serviceHistories->count() }} service</div>
            </div>
            <div class="space-y-3">
            @foreach($serviceHistories as $history)
                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-wrench text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ $history->description ?? 'Service' }}</p>
                        <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($history->service_date)->format('d M Y') }}</p>
                        <p class="text-xs text-gray-500">Vendor: {{ $history->vendor ?? '-' }}</p>
                        <p class="text-xs text-gray-500">PIC: {{ $asset->user->name ?? '-' }}</p>
                    </div>
                    @if($history->cost)
                        <p class="text-sm font-medium text-gray-900">Rp {{ number_format($history->cost, 0, ',', '.') }}</p>
                    @endif
                </div>
            @endforeach
            </div>
        </section>
        @endif

        <!-- Daftar Riwayat Pengajuan Service -->
    @if(!empty($serviceRequests) && count($serviceRequests) > 0)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-tools text-red-600 mr-2"></i>
                    Riwayat Pengajuan Service
                </h3>
                <div class="p-6 space-y-4">
                    @foreach($serviceRequests as $req)
                    <div class="bg-gray-50 p-4 rounded-lg flex justify-between items-center">
                        <div class="flex items-start space-x-4">
                            <div class="p-3 bg-red-100 rounded-lg">
                                <i class="fas fa-tools text-red-600"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $req->keluhan }}</p>
                                <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($req->created_at)->format('d M Y') }}</p>
                                <p class="text-xs text-gray-500">Nomor: {{ $req->nomor_pengajuan }}</p>
                                <p class="text-xs text-gray-500">PIC: {{ $req->user->name }}</p>
                            </div>
                        </div>

                            @push('scripts')
                            <script>
                            document.addEventListener('DOMContentLoaded', function(){
                                // Toggle reimburse menus
                                document.querySelectorAll('[data-reimburse-toggle]').forEach(function(btn){
                                    btn.addEventListener('click', function(e){
                                        e.stopPropagation();
                                        var id = btn.getAttribute('data-menu-id');
                                        var menu = document.getElementById(id);
                                        if(!menu) return;
                                        // close other menus
                                        document.querySelectorAll('.reimburse-menu').forEach(function(m){ if(m !== menu) m.style.display = 'none'; });
                                        menu.style.display = (menu.style.display === 'none' || menu.style.display === '') ? 'block' : 'none';
                                        btn.setAttribute('aria-expanded', menu.style.display === 'block' ? 'true' : 'false');
                                    });
                                });

                                // Close on outside click
                                document.addEventListener('click', function(){
                                    document.querySelectorAll('.reimburse-menu').forEach(function(m){ m.style.display = 'none'; });
                                    document.querySelectorAll('[data-reimburse-toggle]').forEach(function(b){ b.setAttribute('aria-expanded','false'); });
                                });

                                // Close on ESC
                                document.addEventListener('keydown', function(e){
                                    if(e.key === 'Escape'){
                                        document.querySelectorAll('.reimburse-menu').forEach(function(m){ m.style.display = 'none'; });
                                    }
                                });
                            });
                            </script>
                            @endpush
                        <div class="text-sm font-medium text-gray-900">
                            Rp {{ number_format($req->biaya_servis ?? $req->estimasi_harga ?? 0, 0, ',', '.') }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
    @endif

            <!-- Reimburse History (compact) -->
        @if(!empty($reimburseRequests) && count($reimburseRequests) > 0)
            <section class="bg-white rounded-2xl shadow-lg p-6 mb-6 space-y-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Riwayat Pengajuan Reimburse</h2>
                    <div class="text-sm text-gray-600">Total: {{ $reimburseRequests->count() }} pengajuan</div>
                </div>
                <div class="space-y-2">
                    @foreach($reimburseRequests as $r)
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-wallet text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900">{{ $r->nomor_pengajuan ?? 'Pengajuan #' . $r->id }}</p>
                                <p class="text-sm text-gray-500">{{ $r->keterangan ?? '-' }}</p>
                                <p class="text-sm text-gray-500">Pengaju: {{ $r->user->name ?? ($r->pengaju_name ?? '-') }} • {{ $r->created_at ? \Carbon\Carbon::parse($r->created_at)->format('d M Y') : '-' }}</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="text-sm font-medium text-gray-900">Rp {{ number_format($r->biaya ?? $r->amount ?? 0,0,',','.') }}</div>
                                <div class="relative">
                                <button data-reimburse-toggle data-menu-id="reimburse-menu-{{ $r->id }}" type="button" class="p-2 rounded hover:bg-gray-100" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v text-gray-400"></i>
                                </button>
                                <div id="reimburse-menu-{{ $r->id }}" class="reimburse-menu absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg z-50" style="display:none;">
                                        <a href="{{ route('reimburse-requests.show', $r) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Detail</a>
                                        <a href="{{ route('reimburse-requests.export-csv', $r) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Export CSV</a>
                                        @if(auth()->user()->role === 'admin')
                                            <form action="{{ route('reimburse-requests.approve', $r) }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-green-600 hover:bg-gray-100">Approve</button>
                                            </form>
                                            <form action="{{ route('reimburse-requests.reject', $r) }}" method="POST" class="m-0">
                                                @csrf
                                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">Reject</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- Action Buttons -->
        <section class="bg-white rounded-2xl shadow-lg p-4 mb-6 flex flex-wrap justify-center space-x-4">
                @if(strtolower($asset->jenis_aset) === 'motor')
                    <a href="{{ route('reimburse-requests.create') }}"
                       class="inline-flex items-center px-5 py-2 bg-yellow-500 hover:bg-yellow-600 text-white font-medium rounded-lg shadow transition-all hover:shadow-md">
                        <i class="fas fa-wallet mr-2"></i>
                        Ajukan Reimburse
                    </a>
                 @endif
                 @if(strtolower($asset->jenis_aset) !== 'motor')
                    <a href="{{ route('service-requests.create', ['asset_id' => $asset->id]) }}"
                       class="inline-flex items-center px-5 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg shadow transition-all hover:shadow-md">
                        <i class="fas fa-tools mr-2"></i>
                        Ajukan Service
                    </a>
                 @endif
                 @can('kelola-aset')
                <a href="{{ route('assets.edit', $asset->id) }}"
                   class="inline-flex items-center px-5 py-2 bg-gray-700 hover:bg-gray-800 text-white font-medium rounded-lg shadow transition-all hover:shadow-md">
                     <i class="fas fa-edit mr-2"></i>
                     Edit Asset
                 </a>
                @endcan
        </section>
        {{-- Riwayat Pemegang Sebelumnya --}}
        @if($asset->holderHistories->count())
        <div x-data="{ open: false }" class="bg-white rounded-2xl shadow-lg mb-6">
            <button @click="open = !open" class="w-full flex items-center justify-between p-6">
                <h2 class="text-lg font-semibold text-gray-900">Riwayat Pemegang Sebelumnya</h2>
                <i :class="open ? 'fas fa-chevron-up text-gray-600' : 'fas fa-chevron-down text-gray-600'"></i>
            </button>
            <div x-show="open" x-cloak class="p-6 pt-0 space-y-2 border-t border-gray-200">
                @foreach($asset->holderHistories as $history)
                    <div class="flex items-center p-2 bg-gray-50 rounded-lg">
                        <div class="flex-1">
                            <p class="text-sm text-gray-500">Dipegang oleh <span class="font-medium text-gray-900">{{ $history->holder_name }}</span></p>
                            <p class="text-xs text-gray-500">Periode: {{ $history->start_date ? \Carbon\Carbon::parse($history->start_date)->format('d M Y') : '-' }} sampai {{ $history->end_date ? \Carbon\Carbon::parse($history->end_date)->format('d M Y') : 'Sekarang' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-2xl hover:text-gray-300">
            <i class="fas fa-times"></i>
        </button>
        <img id="modalImage" src="" alt="" class="max-w-full max-h-full object-contain">
        <p id="modalCaption" class="text-white text-center mt-4"></p>
    </div>
</div>

<script>
function openImageModal(src, caption) {
    document.getElementById('imageModal').classList.remove('hidden');
    document.getElementById('imageModal').classList.add('flex');
    document.getElementById('modalImage').src = src;
    document.getElementById('modalCaption').textContent = caption;
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    document.getElementById('imageModal').classList.remove('flex');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside
document.getElementById('imageModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImageModal();
    }
});
</script>
@endsection
