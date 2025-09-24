@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Service Pending</h1>
        @if(in_array(auth()->user()->role, ['admin', 'super-admin']))
            <div class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                Kelola service yang sedang berlangsung dan perlu verifikasi
            </div>
        @else
            <div class="text-sm text-gray-600">
                <i class="fas fa-info-circle mr-1"></i>
                Service yang perlu Anda selesaikan
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
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nomor Pengajuan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Asset
                            </th>
                            @if(in_array(auth()->user()->role, ['admin', 'super-admin']))
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    User
                                </th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            @if(in_array(auth()->user()->role, ['admin', 'super-admin']))
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Service
                                </th>
                            @endif
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
                                </td>
                                @if(in_array(auth()->user()->role, ['admin', 'super-admin']))
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $request->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $request->user->email }}</div>
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {!! $request->status_badge !!}
                                </td>
                                @if(in_array(auth()->user()->role, ['admin', 'super-admin']))
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($request->biaya_servis)
                                                Rp {{ number_format($request->biaya_servis, 0, ',', '.') }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $request->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($request->status === 'service_pending' && auth()->user()->role !== 'admin')
                                        <button onclick="openCompleteServiceModal({{ $request->id }})"
                                                class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs mr-2">
                                            Selesaikan Service
                                        </button>
                                    @endif

                                    @if($request->status === 'service_completed' && in_array(auth()->user()->role, ['admin', 'super-admin']))
                                        <button onclick="openVerifyModal({{ $request->id }})"
                                                class="bg-purple-500 hover:bg-purple-600 text-white px-3 py-1 rounded text-xs mr-2">
                                            Verifikasi
                                        </button>
                                    @endif

                                    <a href="{{ route('service-requests.show', $request) }}" class="text-blue-600 hover:text-blue-900">
                                        Lihat Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 bg-gray-50">
                {{ $serviceRequests->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <div class="text-gray-500 text-lg mb-4">
                    <i class="fas fa-clock text-4xl mb-4"></i>
                    @if(in_array(auth()->user()->role, ['admin', 'super-admin']))
                        <p>Tidak ada service yang perlu dikelola</p>
                    @else
                        <p>Tidak ada service pending untuk Anda</p>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Complete Service Modal (for Users) -->
@if(auth()->user()->role !== 'admin')
    <div id="completeServiceModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full p-6 max-h-full overflow-y-auto">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Selesaikan Service</h3>
            <form id="completeServiceForm" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="tanggal_selesai_service" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai Service <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_selesai_service" id="tanggal_selesai_service"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                               required>
                    </div>

                    <div>
                        <label for="total_service" class="block text-sm font-medium text-gray-700 mb-2">Total Biaya Service <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                            <input type="number" name="total_service" id="total_service"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                   placeholder="0"
                                   min="0"
                                   step="1000"
                                   required>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Masukkan total biaya service yang sudah dibayarkan.</p>
                    </div>

                    <div>
                        <label for="catatan_user" class="block text-sm font-medium text-gray-700 mb-2">Catatan Service <span class="text-red-500">*</span></label>
                        <textarea name="catatan_user" id="catatan_user" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                  placeholder="Jelaskan apa yang sudah dikerjakan saat service..."
                                  required></textarea>
                    </div>

                    <div>
                        <label for="foto_struk_service" class="block text-sm font-medium text-gray-700 mb-2">Foto Struk Service <span class="text-red-500">*</span></label>
                        <input type="file" name="foto_struk_service[]" id="foto_struk_service"
                               multiple
                               accept="image/*,.pdf"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                               required>
                        <p class="mt-1 text-xs text-gray-500">Upload foto atau file PDF struk service. Maksimal 5MB per file.</p>
                    </div>

                    <!-- Add new field for service evidence photos -->
                    <div class="mt-4">
                        <label for="foto_bukti_service" class="block text-sm font-medium text-gray-700 mb-2">Foto Bukti Service (Max 3)</label>
                        <input type="file" name="foto_bukti_service[]" id="foto_bukti_service"
                               multiple
                               accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"

                               >
                        <p class="mt-1 text-xs text-gray-500">Upload hingga 3 foto bukti service. JPG/PNG, maksimal 5MB per file.</p>
                    </div>
                </div>

                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" onclick="closeCompleteServiceModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Selesaikan Service
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<!-- Verify Modal (for Admin) -->
@if(in_array(auth()->user()->role, ['admin', 'super-admin']))
    <div id="verifyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-lg w-full p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Verifikasi Service</h3>
            <form id="verifyForm" method="POST">
                @csrf

                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-sm text-gray-600 mb-2">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            User telah menyelesaikan service dan mengupload struk pembayaran.
                        </p>
                        <p class="text-sm font-medium text-gray-800">
                            Pastikan Anda telah memeriksa detail service dan struk pembayaran sebelum melakukan verifikasi.
                        </p>
                    </div>

                    <div>
                        <label for="catatan_verifikasi" class="block text-sm font-medium text-gray-700 mb-2">Catatan Verifikasi (Opsional)</label>
                        <textarea name="catatan_verifikasi" id="catatan_verifikasi" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500"
                                  placeholder="Tambahkan catatan verifikasi jika diperlukan..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 mt-6">
                    <button type="button" onclick="closeVerifyModal()"
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">
                        <i class="fas fa-check mr-2"></i>Verifikasi Service
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif

<script>
let currentServiceRequestId = null;

@if(auth()->user()->role !== 'admin')
function openCompleteServiceModal(serviceRequestId) {
    currentServiceRequestId = serviceRequestId;
    document.getElementById('completeServiceForm').action = `/service-requests/${serviceRequestId}/complete-service`;
    document.getElementById('completeServiceModal').classList.remove('hidden');
}

function closeCompleteServiceModal() {
    document.getElementById('completeServiceModal').classList.add('hidden');
    currentServiceRequestId = null;
}
@endif

@if(in_array(auth()->user()->role, ['admin', 'super-admin']))
function openVerifyModal(serviceRequestId) {
    currentServiceRequestId = serviceRequestId;
    document.getElementById('verifyForm').action = `/service-requests/${serviceRequestId}/verify`;
    document.getElementById('verifyModal').classList.remove('hidden');
}

function closeVerifyModal() {
    document.getElementById('verifyModal').classList.add('hidden');
    currentServiceRequestId = null;
}
@endif

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.id === 'completeServiceModal') closeCompleteServiceModal();
    if (e.target.id === 'verifyModal') closeVerifyModal();
});
</script>
@endsection
