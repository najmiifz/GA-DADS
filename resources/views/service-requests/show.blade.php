@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
        <div class="max-w-4xl mx-auto">
        <!-- Header Card -->
        <div class="bg-white rounded-xl shadow p-6 mb-6">
            <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('service-requests.index') }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Detail Pengajuan Service</h1>
                    <p class="text-gray-600">{{ $serviceRequest->nomor_pengajuan }}</p>
                </div>
            </div>

            <div class="flex space-x-2">
                <button onclick="printDetail()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-print mr-2"></i>Print Detail
                </button>

                <a href="{{ route('service-requests.export-csv', $serviceRequest) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-file-csv mr-2"></i>Export CSV
                </a>

                @if(($serviceRequest->isPending() || $serviceRequest->isRejected()) && ($serviceRequest->user_id === auth()->id() || auth()->user()->can('kelola-akun')))
                    <a href="{{ route('service-requests.edit', $serviceRequest) }}"
                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                @endif

                @can('kelola-akun')
                    @if($serviceRequest->canBeApproved())
                        <button onclick="openApprovalModal()"
                                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-check mr-2"></i>Setujui
                        </button>
                        <button onclick="openRejectionModal()"
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-times mr-2"></i>Tolak
                        </button>
                    @endif

                    @if($serviceRequest->canBeVerified())
                        <button onclick="openVerifyModal()"
                                class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-check-double mr-2"></i>Verifikasi
                        </button>
                    @endif

                    @if($serviceRequest->canBeCompleted())
                        <button onclick="openCompletionModal()"
                                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-clipboard-check mr-2"></i>Selesaikan
                        </button>
                    @endif
                @endcan

                @if($serviceRequest->canCompleteService() && auth()->id() === $serviceRequest->user_id)
                    <button onclick="openCompleteServiceModal()"
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition duration-200">
                        <i class="fas fa-tools mr-2"></i>Selesaikan Service
                    </button>
                @endif
            </div>
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

        <!-- Progress Tracker -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Progress Service</h2>
            @php
                $steps = ['Pengajuan','Disetujui','Diverifikasi','Selesai'];
            @endphp
            <div class="flex items-center">
                @foreach($steps as $index => $label)
                    @php
                        $statusCompleted = match($index) {
                            0 => true,
                            1 => $serviceRequest->approver !== null,
                            2 => $serviceRequest->verifier !== null,
                            3 => $serviceRequest->isCompleted(),
                            default => false,
                        };
                    @endphp
                    <div class="flex items-center flex-1">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-white {{ $statusCompleted ? 'bg-red-600' : 'bg-gray-200 text-gray-500' }}">
                            @if($statusCompleted)
                                <i class="fas fa-check"></i>
                            @else
                                {{ $index + 1 }}
                            @endif
                        </div>
                        <span class="ml-2 text-sm text-gray-700">{{ $label }}</span>
                        @if(!$loop->last)
                            <div class="flex-1 h-1 mx-2 {{ $statusCompleted ? 'bg-red-600' : 'bg-gray-200' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

    <!-- Single-column stacked layout -->
    <div class="space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Pengajuan</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Status</label>
                            <div class="mt-1">
                                {!! $serviceRequest->status_badge !!}
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Pengaju</label>
                            <p class="mt-1 text-gray-900">{{ $serviceRequest->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $serviceRequest->user->email }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Tanggal Pengajuan</label>
                            <p class="mt-1 text-gray-900">{{ $serviceRequest->created_at->format('d/m/Y H:i') }}</p>
                        </div>

                        @if($serviceRequest->lokasi_project)
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Lokasi Project</label>
                                <p class="mt-1 text-gray-900">{{ $serviceRequest->lokasi_project }}</p>
                            </div>
                        @endif

                        @if($serviceRequest->approver)
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Disetujui/Ditolak Oleh</label>
                                <p class="mt-1 text-gray-900">{{ $serviceRequest->approver->name }}</p>
                                <p class="text-sm text-gray-500">{{ $serviceRequest->approved_at?->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif

                        @if($serviceRequest->verifier)
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Diverifikasi Oleh</label>
                                <p class="mt-1 text-gray-900">{{ $serviceRequest->verifier->name }}</p>
                                <p class="text-sm text-gray-500">{{ $serviceRequest->verified_at?->format('d/m/Y H:i') }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Vehicle Information -->
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kendaraan</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600">Nama Kendaraan</label>
                            <p class="mt-1 text-gray-900">{{ $serviceRequest->asset->merk ?? 'N/A' }} {{ $serviceRequest->asset->tipe ?? '' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">Serial Number</label>
                            <p class="mt-1 text-gray-900">{{ $serviceRequest->asset->serial_number ?? 'N/A' }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-600">KM Saat Ini</label>
                            <p class="mt-1 text-gray-900">{{ $serviceRequest->km_saat_ini }}</p>
                        </div>
                    </div>
                </div>
            </div>

    <!-- All sections stacked -->
                <!-- Keluhan -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Keluhan / Masalah</h2>
                    <p class="text-gray-900 whitespace-pre-line">{{ $serviceRequest->keluhan }}</p>
                </div>

                <!-- Estimasi Harga -->
                @if($serviceRequest->estimasi_harga)
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Estimasi Harga Service</h2>
                        <p class="text-2xl font-bold text-green-600">{{ $serviceRequest->estimasi_harga ? 'Rp ' . number_format((float) $serviceRequest->estimasi_harga, 0, ',', '.') : '-' }}</p>
                    </div>
                @endif

                <!-- Foto KM -->
                @if($serviceRequest->foto_km)
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Foto KM Saat Ini</h2>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($serviceRequest->foto_km_urls as $url)
                                <div class="relative">
                                    <img src="{{ $url }}" alt="Foto KM" class="w-full h-32 object-cover rounded-lg cursor-pointer" onclick="openImageModal('{{ $url }}')">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Foto Estimasi -->
                @if($serviceRequest->foto_estimasi)
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Foto Struk Estimasi</h2>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($serviceRequest->foto_estimasi_urls as $url)
                                <div class="relative">
                                    <img src="{{ $url }}" alt="Foto Estimasi" class="w-full h-32 object-cover rounded-lg cursor-pointer" onclick="openImageModal('{{ $url }}')">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Admin Notes -->
                @if($serviceRequest->catatan_admin)
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Catatan Admin</h2>
                        <p class="text-gray-900 whitespace-pre-line">{{ $serviceRequest->catatan_admin }}</p>
                    </div>
                @endif

                <!-- Service Information (if completed) -->
                @if($serviceRequest->isCompleted())
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Service</h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Tanggal Service</label>
                                <p class="mt-1 text-gray-900">{{ $serviceRequest->tanggal_servis?->format('d/m/Y') }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600">Biaya Service</label>
                                <p class="mt-1 text-2xl font-bold text-green-600">{{ $serviceRequest->biaya_servis ? 'Rp ' . number_format((float) $serviceRequest->biaya_servis, 0, ',', '.') : '-' }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-600">Keterangan Service</label>
                                <p class="mt-1 text-gray-900 whitespace-pre-line">{{ $serviceRequest->keterangan_servis }}</p>
                            </div>
                        </div>

                        @if($serviceRequest->foto_invoice)
                            <div class="mt-6">
                                <h3 class="text-md font-semibold text-gray-800 mb-3">Foto Invoice</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    @foreach($serviceRequest->foto_invoice_urls as $url)
                                        <div class="relative">
                                            <img src="{{ $url }}" alt="Foto Invoice" class="w-full h-32 object-cover rounded-lg cursor-pointer" onclick="openImageModal('{{ $url }}')">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        {{-- Debug links removed to avoid duplicate output; gallery is rendered once below. --}}
                    </div>
                @endif

                <!-- User Service Completion Information -->
                @if($serviceRequest->isServiceCompleted() || $serviceRequest->isCompleted())
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Penyelesaian Service oleh User</h2>

                        <div class="space-y-4">
                            @if($serviceRequest->tanggal_selesai_service)
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Tanggal Selesai Service</label>
                                    <p class="mt-1 text-gray-900">{{ $serviceRequest->tanggal_selesai_service?->format('d/m/Y') }}</p>
                                </div>
                            @endif

                            @if($serviceRequest->catatan_user)
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Catatan User</label>
                                    <p class="mt-1 text-gray-900 whitespace-pre-line">{{ $serviceRequest->catatan_user }}</p>
                                </div>
                            @endif
                            @if($serviceRequest->biaya_servis)
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Total Biaya Service</label>
                                    <p class="mt-1 text-gray-900">{{ $serviceRequest->biaya_servis ? 'Rp ' . number_format((float) $serviceRequest->biaya_servis, 0, ',', '.') : '-' }}</p>
                                </div>
                            @endif
                        </div>

                        @if($serviceRequest->foto_struk_service)
                            <div class="mt-6">
                                <h3 class="text-md font-semibold text-gray-800 mb-3">Foto Struk Service</h3>
                                <div class="grid grid-cols-2 gap-4">
                                    @foreach($serviceRequest->foto_struk_service_urls as $url)
                                        <div class="relative">
                                            <img src="{{ $url }}" alt="Foto Struk Service" class="w-full h-32 object-cover rounded-lg cursor-pointer" onclick="openImageModal('{{ $url }}')">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($serviceRequest->foto_bukti_service)
                            <div class="mt-6">
                                <h3 class="text-md font-semibold text-gray-800 mb-3">Foto Bukti Service</h3>
                                <div class="grid grid-cols-3 gap-4">
                                    @foreach($serviceRequest->foto_bukti_service_urls as $url)
                                        <div class="relative">
                                            <img src="{{ $url }}" alt="Foto Bukti Service" class="w-full h-32 object-cover rounded-lg cursor-pointer" onclick="openImageModal('{{ $url }}')">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full flex items-center justify-center">
        <!-- Prev button -->
        <button id="modalPrev" onclick="modalPrev()" class="absolute left-4 text-white bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-75">
            <i class="fas fa-chevron-left"></i>
        </button>

        <img id="modalImage" src="" alt="Full size image" class="max-w-full max-h-full object-contain rounded">

        <!-- Next button -->
        <button id="modalNext" onclick="modalNext()" class="absolute right-4 text-white bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-75">
            <i class="fas fa-chevron-right"></i>
        </button>

        <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white text-2xl bg-black bg-opacity-50 rounded-full w-10 h-10 flex items-center justify-center hover:bg-opacity-75">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

@can('kelola-akun')
    <!-- Approval Modal -->
    <div id="approvalModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Setujui Pengajuan Service</h3>
            <form action="{{ route('service-requests.approve', $serviceRequest) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="catatan_admin_approve" class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea name="catatan_admin" id="catatan_admin_approve" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeApprovalModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                        Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Rejection Modal -->
    <div id="rejectionModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Tolak Pengajuan Service</h3>
            <form action="{{ route('service-requests.reject', $serviceRequest) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="catatan_admin_reject" class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                    <textarea name="catatan_admin" id="catatan_admin_reject" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                              placeholder="Jelaskan alasan penolakan..."
                              required></textarea>
                </div>
                <div class="flex justify-end space-x-4">
                    <button type="button" onclick="closeRejectionModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Completion Modal -->
    @if($serviceRequest->canBeCompleted())
        <div id="completionModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-2xl w-full p-6 max-h-full overflow-y-auto">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Selesaikan Service</h3>
                <form action="{{ route('service-requests.complete', $serviceRequest) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-4">
                        <div>
                            <label for="tanggal_servis" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Service <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_servis" id="tanggal_servis"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   required>
                        </div>

                        <div>
                            <label for="biaya_servis" class="block text-sm font-medium text-gray-700 mb-2">Biaya Service <span class="text-red-500">*</span></label>
                            <input type="number" name="biaya_servis" id="biaya_servis"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="0"
                                   min="0"
                                   required>
                        </div>

                        <div>
                            <label for="keterangan_servis" class="block text-sm font-medium text-gray-700 mb-2">Keterangan Service <span class="text-red-500">*</span></label>
                            <textarea name="keterangan_servis" id="keterangan_servis" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                      placeholder="Jelaskan detail service yang dilakukan..."
                                      required></textarea>
                        </div>

                        <div>
                            <label for="foto_invoice" class="block text-sm font-medium text-gray-700 mb-2">Foto Invoice <span class="text-red-500">*</span></label>
                            <input type="file" name="foto_invoice[]" id="foto_invoice"
                                   multiple
                                   accept="image/*,.pdf"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   required>
                            <p class="mt-1 text-xs text-gray-500">Upload foto atau file PDF invoice. Maksimal 5MB per file.</p>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4 mt-6">
                        <button type="button" onclick="closeCompletionModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Selesaikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endcan

<!-- Complete Service Modal (for Users) -->
@if($serviceRequest->canCompleteService() && auth()->id() === $serviceRequest->user_id)
    <div id="completeServiceModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full p-6 max-h-full overflow-y-auto">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Selesaikan Service</h3>
            <form action="{{ route('service-requests.complete-service', $serviceRequest) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="space-y-4">
                    <div>
                        <label for="tanggal_selesai_service" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai Service <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_selesai_service" id="tanggal_selesai_service"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                               required>
                    </div>
                    <div>
                        <label for="total_service" class="block text-sm font-medium text-gray-700 mb-2">Total Biaya Service (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="total_service" id="total_service"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                               placeholder="Masukkan total biaya service"
                               min="0" step="1000" required>
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

                    <div>
                        <label for="foto_bukti_service" class="block text-sm font-medium text-gray-700 mb-2">Foto Bukti Service (opsional, max 3)</label>
                        <input type="file" name="foto_bukti_service[]" id="foto_bukti_service"
                               multiple
                               accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500">
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
                        {{-- foto_bukti_service gallery removed here to avoid duplicate rendering. The gallery is rendered below inside the Service Completion card so it appears once and inside the white content area. --}}

<!-- Verify Modal (for Admin) -->
@can('kelola-akun')
    @if($serviceRequest->canBeVerified())
        <div id="verifyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
            <div class="bg-white rounded-lg max-w-lg w-full p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Verifikasi Service</h3>
                <form action="{{ route('service-requests.verify', $serviceRequest) }}" method="POST">
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
@endcan

<script>
// Image modal gallery state
let _modalGallery = [];
let _modalIndex = 0;

function openImageModal(src) {
    // Build gallery from images present inside the service detail modal if available
    try {
        const containerImgs = document.querySelectorAll('#serviceDetailModal img');
        _modalGallery = Array.from(containerImgs).map(i => i.src).filter(s => !!s);
    } catch (e) {
        _modalGallery = [];
    }

    // Fallback: if no gallery found, include the single src
    if (!_modalGallery || _modalGallery.length === 0) {
        _modalGallery = [src];
    }

    _modalIndex = _modalGallery.indexOf(src);
    if (_modalIndex === -1) _modalIndex = 0;

    document.getElementById('modalImage').src = _modalGallery[_modalIndex];
    document.getElementById('imageModal').classList.remove('hidden');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.add('hidden');
    // clear src to avoid previous image sticking
    document.getElementById('modalImage').src = '';
    _modalGallery = [];
    _modalIndex = 0;
}

function modalPrev() {
    if (!_modalGallery || _modalGallery.length === 0) return;
    _modalIndex = (_modalIndex - 1 + _modalGallery.length) % _modalGallery.length;
    document.getElementById('modalImage').src = _modalGallery[_modalIndex];
}

function modalNext() {
    if (!_modalGallery || _modalGallery.length === 0) return;
    _modalIndex = (_modalIndex + 1) % _modalGallery.length;
    document.getElementById('modalImage').src = _modalGallery[_modalIndex];
}

function openApprovalModal() {
    document.getElementById('approvalModal').classList.remove('hidden');
}

function closeApprovalModal() {
    document.getElementById('approvalModal').classList.add('hidden');
}

function openRejectionModal() {
    document.getElementById('rejectionModal').classList.remove('hidden');
}

function closeRejectionModal() {
    document.getElementById('rejectionModal').classList.add('hidden');
}

function openCompletionModal() {
    document.getElementById('completionModal').classList.remove('hidden');
}

function closeCompletionModal() {
    document.getElementById('completionModal').classList.add('hidden');
}

function openCompleteServiceModal() {
    document.getElementById('completeServiceModal').classList.remove('hidden');
}

function closeCompleteServiceModal() {
    document.getElementById('completeServiceModal').classList.add('hidden');
}

function openVerifyModal() {
    document.getElementById('verifyModal').classList.remove('hidden');
}

function closeVerifyModal() {
    document.getElementById('verifyModal').classList.add('hidden');
}

// Close modals when clicking outside
document.addEventListener('click', function(e) {
    if (e.target.id === 'imageModal') closeImageModal();
    if (e.target.id === 'approvalModal') closeApprovalModal();
    if (e.target.id === 'rejectionModal') closeRejectionModal();
    if (e.target.id === 'completionModal') closeCompletionModal();
    if (e.target.id === 'completeServiceModal') closeCompleteServiceModal();
    if (e.target.id === 'verifyModal') closeVerifyModal();
});

// Keyboard navigation for image modal
document.addEventListener('keydown', function(e) {
    const modal = document.getElementById('imageModal');
    if (!modal || modal.classList.contains('hidden')) return;

    if (e.key === 'Escape') {
        closeImageModal();
    } else if (e.key === 'ArrowLeft') {
        modalPrev();
    } else if (e.key === 'ArrowRight') {
        modalNext();
    }
});

// Print Detail function
function printDetail() {
    console.log('printDetail function called');

    try {
        const printWindow = window.open('', '_blank', 'width=800,height=600');

        if (!printWindow) {
            alert('Pop-up blocker mungkin mencegah pencetakan. Silakan izinkan pop-up untuk situs ini dan coba lagi.');
            return;
        }

        const printContent = `<!DOCTYPE html>
<html>
<head>
    <title>Detail Pengajuan Service - {{ $serviceRequest->nomor_pengajuan }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .info-item { margin-bottom: 10px; }
        .label { font-weight: bold; color: #333; }
        .value { color: #666; }
        .section { margin-bottom: 30px; }
        .section h3 { border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        @media print { body { margin: 0; } }
    </style>
</head>
<body>
    <div class="header">
        <h1>Detail Pengajuan Service</h1>
        <h2>{{ $serviceRequest->nomor_pengajuan }}</h2>
    </div>

    <div class="section">
        <h3>Informasi Umum</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Nomor Pengajuan:</div>
                <div class="value">{{ $serviceRequest->nomor_pengajuan }}</div>
            </div>
            <div class="info-item">
                <div class="label">Status:</div>
                <div class="value">{{ ucfirst($serviceRequest->status) }}</div>
            </div>
            <div class="info-item">
                <div class="label">Tanggal Pengajuan:</div>
                <div class="value">{{ $serviceRequest->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-item">
                <div class="label">PIC:</div>
                <div class="value">{{ $serviceRequest->user->name }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Informasi Asset</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Asset:</div>
                <div class="value">{{ $serviceRequest->asset->merk }} {{ $serviceRequest->asset->tipe }}</div>
            </div>
            <div class="info-item">
                <div class="label">No. Seri:</div>
                <div class="value">{{ $serviceRequest->asset->no_seri }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Detail Service</h3>
        <div class="info-item">
            <div class="label">Jenis Service:</div>
            <div class="value">{{ $serviceRequest->jenis_service }}</div>
        </div>
        <div class="info-item">
            <div class="label">Keluhan:</div>
            <div class="value">{{ $serviceRequest->keluhan }}</div>
        </div>
        @if($serviceRequest->estimasi_harga)
        <div class="info-item">
            <div class="label">Estimasi Harga:</div>
            <div class="value">Rp {{ number_format($serviceRequest->estimasi_harga ?? 0, 0, ',', '.') }}</div>
        </div>
        @endif
        @if($serviceRequest->biaya_servis)
        <div class="info-item">
            <div class="label">Biaya Service:</div>
            <div class="value">Rp {{ number_format($serviceRequest->biaya_servis ?? 0, 0, ',', '.') }}</div>
        </div>
        @endif
    </div>

    <div class="section">
        <p style="text-align: center; margin-top: 50px; font-size: 12px; color: #666;">
            Dicetak pada: {{ date('d/m/Y H:i:s') }}
        </p>
    </div>
</body>
</html>`;

        printWindow.document.write(printContent);
        printWindow.document.close();

        setTimeout(() => {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        }, 500);

    } catch (error) {
        console.error('Error in printDetail:', error);
        alert('Terjadi kesalahan saat mencetak. Silakan coba lagi atau hubungi administrator.');
    }
}
</script>
@endsection
