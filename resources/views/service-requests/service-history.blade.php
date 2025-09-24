@extends('layouts.app')

@section('title', 'Riwayat Service')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Riwayat Service</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">Total: {{ $serviceRequests->count() }} service</span>
                    <a href="{{ route('service-requests.service-history.export-csv') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                        <i class="fas fa-file-csv mr-1"></i>Export CSV
                    </a>
                </div>
        </div>

        @if($serviceRequests->isEmpty())
            <div class="text-center py-12">
                <i class="fas fa-history text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Belum Ada Riwayat Service</h3>
                <p class="text-gray-500">Service yang sudah selesai akan muncul di sini.</p>
            </div>
        @else
            <form method="POST" action="{{ route('service-requests.bulk-delete') }}" onsubmit="return confirm('Yakin menghapus service terpilih?')">
                @csrf
                @method('DELETE')
                @if(auth()->user()->role === 'admin')
                    <div class="flex justify-end mb-4">
                        <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">Hapus Terpilih</button>
                    </div>
                @endif
                <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            @if(auth()->user()->role === 'admin')
                                <th class="px-4 py-3"><input type="checkbox" id="select-all" class="form-checkbox h-4 w-4 text-blue-600"></th>
                            @endif
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asset</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama PIC</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi PIC</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Service</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estimasi</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Biaya</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($serviceRequests as $index => $request)
                            <tr class="hover:bg-gray-50">
                                        @if(auth()->user()->role === 'admin')
                                            <td class="px-4 py-4 whitespace-nowrap">
                                                <input type="checkbox" name="ids[]" value="{{ $request->id }}" class="form-checkbox h-4 w-4 text-blue-600">
                                            </td>
                                        @endif
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ optional($request->asset)->merk ?? 'N/A' }} {{ optional($request->asset)->tipe ?? '' }}</div>
                                    <div class="text-sm text-gray-500">{{ optional($request->asset)->serial_number ?? 'N/A' }}</div>
                                </td>
                               <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->user->name ?? 'N/A' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">{{ $request->user->lokasi ?? 'N/A' }}</td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $request->keluhan }}</div>
                                    <div class="text-sm text-gray-500">KM: {{ number_format($request->km_saat_ini) }}</div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if($request->estimasi_harga)
                                        <div class="text-sm font-medium text-green-600">
                                            Rp {{ number_format($request->estimasi_harga, 0, ',', '.') }}
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                       @if($request->biaya_servis)
                                           <div class="text-sm font-medium text-blue-600">
                                               Rp {{ number_format($request->biaya_servis, 0, ',', '.') }}
                                           </div>
                                       @elseif($request->estimasi_harga)
                                           <div class="text-sm font-medium text-green-600">
                                               Rp {{ number_format($request->estimasi_harga, 0, ',', '.') }}
                                           </div>
                                       @else
                                           <span class="text-sm text-gray-400">-</span>
                                       @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if($request->status === 'completed')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Selesai
                                        </span>
                                    @elseif($request->status === 'verified')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-check-double mr-1"></i>
                                            Terverifikasi
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if($request->tanggal_selesai_service)
                                            {{ $request->tanggal_selesai_service->format('d/m/Y') }}
                                        @elseif($request->verified_at)
                                            {{ $request->verified_at->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="javascript:void(0)"
                                       onclick="showServiceDetail('{{ route('service-requests.showJson', $request->id) }}');"
                                       class="text-blue-600 hover:text-blue-900 mr-3 inline-block">
                                        <i class="fas fa-eye"></i>
                                        Detail
                                    </a>
                                    <a href="{{ route('service-requests.export-csv', $request) }}" class="inline-block px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700" title="Export CSV">
                                        <i class="fas fa-file-csv mr-1"></i>Export CSV
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Card list for mobile -->
            <div class="md:hidden space-y-4">
                @foreach($serviceRequests as $request)
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-semibold text-gray-800">{{ optional($request->asset)->merk ?? 'N/A' }} {{ optional($request->asset)->tipe ?? '' }}</span>
                            <span class="text-xs font-medium text-gray-500">{{ 'No: '. $request->nomor_pengajuan }}</span>
                        </div>
                        <div class="text-sm text-gray-600 mb-1"><span>Nama PIC: {{ $request->user->name ?? '-' }}</span></div>
                        <div class="text-sm text-gray-600 mb-1"><span>Lokasi PIC: {{ $request->user->lokasi ?? '-' }}</span></div>
                        <div class="text-sm text-gray-600 mb-1">
                            <span>Jenis: {{ $request->keluhan }}</span>
                        </div>
                        <div class="text-sm text-gray-600 mb-1">
                            <span>Tanggal Service: {{ optional($request->tanggal_selesai_service)->format('d/m/Y') ?? '-' }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600 mb-2">
                            <span class="mr-2">Bukti:</span>
                            @if(!empty($request->foto_bukti_service_urls))
                                @foreach($request->foto_bukti_service_urls as $url)
                                    <img src="{{ $url }}" alt="Bukti" class="h-6 w-6 object-cover rounded mr-1">
                                @endforeach
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </div>
                        <div class="flex justify-between text-sm text-gray-700 mb-2">
                            @if($request->biaya_servis)
                                <span>Total: Rp {{ number_format($request->biaya_servis,0,',','.') }}</span>
                            @elseif($request->estimasi_harga)
                                <span>Total: Rp {{ number_format($request->estimasi_harga,0,',','.') }}</span>
                            @else
                                <span>Total: -</span>
                            @endif
                            <span class="inline-flex items-center px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-xs">
                                @if($request->status === 'completed')
                                    Selesai
                                @elseif($request->status === 'verified')
                                    Terverifikasi
                                @endif
                            </span>
                        </div>
                        <a href="{{ route('service-requests.show', $request) }}" class="text-blue-600 hover:underline text-sm">Detail</a>
                        <a href="{{ route('service-requests.export-csv', $request) }}" class="ml-4 inline-block px-3 py-1 bg-blue-600 text-white rounded text-sm hover:bg-blue-700">Export CSV</a>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                @if(method_exists($serviceRequests, 'hasPages') && $serviceRequests->hasPages())
                    {!! $serviceRequests->render() !!}
                @endif
            </div>
            </form>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const selectAll = document.getElementById('select-all');
                    if (selectAll) {
                        selectAll.addEventListener('change', function() {
                            document.querySelectorAll('input[name="ids[]"]').forEach(cb => cb.checked = this.checked);
                        });
                    }
                });
            </script>
        @endif
    </div>
</div>

{{-- Modal Detail Service --}}
<div id="serviceDetailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Detail Riwayat Service
                    </h3>
                    <button onclick="closeServiceDetail()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <div id="serviceDetailContent">
                    {{-- Content will be loaded via AJAX --}}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Show service detail modal and fetch data from provided API URL
function showServiceDetail(apiUrl) {
    const modal = document.getElementById('serviceDetailModal');
    const content = document.getElementById('serviceDetailContent');

    // Show modal
    modal.classList.remove('hidden');

    // Show loading indicator
    content.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-gray-400"></i> Loading...</div>';

    console.log('Fetching service detail from URL:', apiUrl);
    fetch(apiUrl, { credentials: 'same-origin' })
        .then(response => {
            console.log('Response status:', response.status, 'Content-Type:', response.headers.get('content-type'));
            return response.json();
        })
        .then(data => {
            content.innerHTML = generateServiceDetailHTML(data);
        })
        .catch(error => {
            content.innerHTML = '<div class="text-center text-red-500 py-4">Error loading data</div>';
        });
}

function closeServiceDetail() {
    document.getElementById('serviceDetailModal').classList.add('hidden');
}

function generateServiceDetailHTML(data) {
    const request = data.serviceRequest;
    // helper to pick first defined key
    function pick(obj, ...keys) {
        for (let k of keys) {
            if (obj && obj[k] !== undefined && obj[k] !== null) return obj[k];
        }
        return null;
    }

    function fmtCurrency(v) {
        if (v === null || v === undefined || v === '') return '-';
        const n = parseInt(v);
        if (isNaN(n)) return '-';
        return 'Rp ' + n.toLocaleString('id-ID');
    }

    function fmtDate(v) {
        if (!v) return null;
        const d = new Date(v);
        if (isNaN(d.getTime())) return null;
        return d.toLocaleDateString('id-ID');
    }

    const nomor = pick(request, 'nomor_pengajuan', 'nomor', 'no_pengajuan') || '-';
    const pengaju = pick(request, 'user', 'pengaju');
    const pengajuName = pengaju && (pengaju.name || pengaju.full_name || pengaju.nama) ? (pengaju.name || pengaju.full_name || pengaju.nama) : (pick(request, 'user_name') || '-');
    const pengajuEmail = pengaju && (pengaju.email) ? pengaju.email : '';
    const pengajuLokasi = pengaju && (pengaju.lokasi || pengaju.city || pengaju.kota) ? (pengaju.lokasi || pengaju.city || pengaju.kota) : (pick(request, 'lokasi') || '-');

    const assetMerk = pick(request, 'asset') ? (request.asset.merk || request.asset.nama || '-') : '-';
    const assetTipe = pick(request, 'asset') ? (request.asset.tipe || '') : '';
    const serial = pick(request, 'asset') ? (request.asset.serial_number || request.asset.serial || 'N/A') : 'N/A';

    const keluhan = pick(request, 'keluhan', 'jenis_service') || '-';
    const kmNow = pick(request, 'km_saat_ini', 'km_sekarang', 'current_km') ? parseInt(pick(request, 'km_saat_ini', 'km_sekarang', 'current_km')) : null;
    const deskripsi = pick(request, 'deskripsi', 'keterangan_servis', 'keterangan', 'description') || '-';

    const estimasi = pick(request, 'estimasi_harga', 'estimasi') || null;
    const totalBiaya = pick(request, 'biaya_servis', 'biaya', 'total') || null;

    const approvedAt = pick(request, 'tanggal_approval', 'approved_at', 'approvedAt', 'tanggal_disetujui');
    const selesaiAt = pick(request, 'tanggal_selesai', 'tanggal_selesai_service', 'tanggal_selesai_service', 'tanggal_selesai');
    const verifiedAt = pick(request, 'tanggal_verifikasi', 'verified_at', 'verifiedAt');

    let html = `
        <div class="space-y-6">
            <!-- Informasi Umum -->
            <div>
                <h4 class="font-semibold text-gray-900 border-b pb-2">Informasi Service</h4>
                <div class="mt-3 space-y-2 text-sm text-gray-700">
                    <div><strong>Nomor Pengajuan:</strong> ${nomor}</div>
                    <div><strong>Asset:</strong> ${assetMerk} ${assetTipe}</div>
                    <div><strong>Serial Number:</strong> ${serial}</div>
                    <div><strong>Pengaju:</strong> ${pengajuName}${pengajuEmail ? ' — ' + pengajuEmail : ''}</div>
                    <div><strong>Lokasi PIC:</strong> ${pengajuLokasi}</div>
                    <div><strong>Keluhan:</strong> ${keluhan}</div>
                    <div><strong>KM Sekarang:</strong> ${kmNow !== null ? kmNow.toLocaleString() : '-'}</div>
                    <div><strong>Deskripsi:</strong> ${deskripsi}</div>
                </div>
            </div>

            <!-- Biaya -->
            <div>
                <h4 class="font-semibold text-gray-900 border-b pb-2">Biaya</h4>
                <div class="mt-3 space-y-2 text-sm text-gray-700">
                    <div><strong>Estimasi Harga:</strong> <span class="text-green-600 font-medium">${fmtCurrency(estimasi)}</span></div>
                    <div><strong>Total Biaya:</strong> <span class="text-blue-600 font-medium">${totalBiaya ? fmtCurrency(totalBiaya) : (estimasi ? fmtCurrency(estimasi) : '-')}</span></div>
                    <div><strong>Catatan Admin:</strong> ${pick(request, 'catatan_admin', 'admin_note') || '-'}</div>
                    <div><strong>Catatan User:</strong> ${pick(request, 'catatan_user', 'user_note') || '-'}</div>
                </div>
            </div>

            <!-- Tanggal Service Diajukan -->
            <div>
                <h4 class="font-semibold text-gray-900 border-b pb-2">Tanggal Service Diajukan</h4>
                <div class="mt-3 text-sm text-gray-700">
                    ${fmtDate(pick(request, 'tanggal_servis', 'tanggal_service')) || '-'}
                </div>
            </div>

            <!-- Timeline -->
            <div>
                <h4 class="font-semibold text-gray-900 border-b pb-2">Timeline</h4>
                <div class="mt-3 space-y-3 text-sm text-gray-700">
                    <div>
                        <div class="font-medium">Service Diajukan</div>
                        <div class="text-xs text-gray-500">${fmtDate(pick(request,'created_at','createdAt')) || '-'}</div>
                    </div>

                    ${approvedAt ? `
                    <div>
                        <div class="font-medium">Disetujui Admin</div>
                        <div class="text-xs text-gray-500">${fmtDate(approvedAt)}</div>
                    </div>
                    ` : ''}

                    ${selesaiAt ? `
                    <div>
                        <div class="font-medium">Service Selesai</div>
                        <div class="text-xs text-gray-500">${fmtDate(selesaiAt)}</div>
                    </div>
                    ` : ''}

                    ${verifiedAt ? `
                    <div>
                        <div class="font-medium">Diverifikasi Admin</div>
                        <div class="text-xs text-gray-500">${fmtDate(verifiedAt)}</div>
                    </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;

    // Add photos section if any photos exist (including foto_bukti_service)
    const photoGroups = [];
    // push photo groups with their storage paths so we can build correct URLs
    if (request.foto_km && request.foto_km.length > 0) photoGroups.push({title: 'Foto KM', files: request.foto_km, path: 'service-requests/km'});
    if (request.foto_estimasi && request.foto_estimasi.length > 0) photoGroups.push({title: 'Foto Estimasi', files: request.foto_estimasi, path: 'service-requests/estimates'});
    if (request.foto_struk_service && request.foto_struk_service.length > 0) photoGroups.push({title: 'Foto Struk Service', files: request.foto_struk_service, path: 'service-requests/service-receipts'});
    if (request.foto_invoice && request.foto_invoice.length > 0) photoGroups.push({title: 'Foto Invoice', files: request.foto_invoice, path: 'service-requests/invoices'});
    if (request.foto_bukti_service && request.foto_bukti_service.length > 0) photoGroups.push({title: 'Foto Bukti Service', files: request.foto_bukti_service, path: 'service-requests/service-evidence'});

    if (photoGroups.length > 0) {
        html += `
            <div class="mt-6">
                <h4 class="text-lg font-semibold text-gray-900 border-b pb-2 mb-4">Dokumentasi</h4>
                <div class="space-y-6">
        `;

        photoGroups.forEach(group => {
            html += `
                <div>
                    <h5 class="text-md font-semibold text-gray-800 mb-3">${group.title}</h5>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            `;

            group.files.forEach(file => {
                const imageUrl = `/storage/${(group.path ? group.path + '/' : '')}${file}`;
                // click: use existing modal opener if available, otherwise open in new tab
                html += `
                    <img src="${imageUrl}" alt="${group.title}"
                         loading="lazy"
                         class="w-full h-28 object-cover rounded border cursor-pointer hover:opacity-90"
                         onclick="(function(){ if (typeof window.openImageModal === 'function') { window.openImageModal('${imageUrl}'); } else { window.open('${imageUrl}', '_blank'); } })()">
                `;
            });

            html += `
                    </div>
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;
    }

    return html;
}

// Close modal when clicking outside
document.getElementById('serviceDetailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeServiceDetail();
    }
});
</script>
@endsection
