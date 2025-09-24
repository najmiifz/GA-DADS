@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-4">
        <a href="{{ auth()->user()->role === 'admin' ? route('reimburse-requests.admin-index') : route('reimburse-requests.index') }}"
           class="text-gray-600 hover:text-gray-800 mr-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Detail Pengajuan Reimburse</h1>
        <div class="ml-auto flex space-x-2">
            <button onclick="printDetail()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-print mr-2"></i>Print Detail
            </button>
            <a href="{{ route('reimburse-requests.export-csv', $reimburseRequest) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-file-csv mr-2"></i>Export CSV
            </a>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="space-y-2">
            <p><strong>Nomor Pengajuan:</strong> {{ $reimburseRequest->nomor_pengajuan }}</p>
            <p><strong>Asset:</strong> {{ $reimburseRequest->asset->merk }} ({{ $reimburseRequest->asset->tipe }})</p>
            <p><strong>Biaya:</strong> Rp {{ number_format($reimburseRequest->biaya,0,',','.') }}</p>
            <p><strong>Keterangan:</strong> {{ $reimburseRequest->keterangan }}</p>
            <p><strong>Tanggal Service:</strong> {{ $reimburseRequest->tanggal_service->format('d M Y') }}</p>
            <p><strong>Bukti Struk:</strong>
                @php
                    $bukti = $reimburseRequest->bukti_struk;
                    $files = [];
                    if($bukti) {
                        $decoded = json_decode($bukti, true);
                        if(is_array($decoded)) {
                            $files = $decoded;
                        } else {
                            $files = [$bukti];
                        }
                    }

                    // separate image files from non-image files so Bukti Struk only shows non-image links (pdfs)
                    $nonImageFiles = [];
                    $imageFilesInBukti = [];
                    foreach($files as $f) {
                        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                        if(in_array($ext, ['jpg','jpeg','png'])) {
                            $imageFilesInBukti[] = $f;
                        } else {
                            $nonImageFiles[] = $f;
                        }
                    }
                @endphp

                @if(!empty($nonImageFiles))
                    <div class="flex items-center space-x-2">
                        @foreach($nonImageFiles as $f)
                            <a href="{{ asset('storage/' . $f) }}" target="_blank" class="text-blue-600 underline">Lihat</a>
                        @endforeach
                    </div>
                @elseif(!empty($imageFilesInBukti))
                    {{-- If only images exist inside bukti_struk (legacy), show small links but prefer rendering real images from foto_bukti_service below --}}
                    <div class="text-sm text-gray-500">(File image ditemukan pada bukti_struk; akan ditampilkan di bagian Bukti Service jika tersedia)</div>
                @else
                    -
                @endif
            </p>

            <p><strong>Foto Bukti Service:</strong>
                @php
                    $foto = $reimburseRequest->foto_bukti_service;
                    $fotoFiles = [];
                    if($foto) {
                        $decodedFoto = json_decode($foto, true);
                        if(is_array($decodedFoto)) {
                            $fotoFiles = $decodedFoto;
                        } else {
                            $fotoFiles = [$foto];
                        }
                    }

                    // also collect image files that may still be stored in bukti_struk (legacy)
                    $legacyImages = [];
                    $bukti = $reimburseRequest->bukti_struk;
                    if($bukti) {
                        $decoded = json_decode($bukti, true);
                        if(is_array($decoded)) {
                            $files = $decoded;
                        } else {
                            $files = [$bukti];
                        }
                        foreach($files as $f) {
                            $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
                            if(in_array($ext, ['jpg','jpeg','png'])) {
                                $legacyImages[] = $f;
                            }
                        }
                    }

                    // merge fotoFiles with legacyImages but avoid duplicates
                    $allImages = array_values(array_unique(array_merge($fotoFiles, $legacyImages)));
                @endphp

                @if(!empty($allImages))
                    <div class="flex items-center space-x-2">
                        @foreach($allImages as $f)
                            <a href="{{ asset('storage/' . $f) }}" target="_blank">
                                <img src="{{ asset('storage/' . $f) }}" alt="Foto Bukti" class="h-12 w-12 object-cover rounded">
                            </a>
                        @endforeach
                    </div>
                @else
                    -
                @endif
            </p>
            @if($reimburseRequest->foto_bukti_service)
                @php
                    $evidence = json_decode($reimburseRequest->foto_bukti_service, true) ?: [];
                @endphp
                @if(!empty($evidence))
                    <p><strong>Bukti Service:</strong></p>
                    <div class="flex items-center space-x-2 mb-2">
                        @foreach($evidence as $f)
                            <a href="{{ asset('storage/' . $f) }}" target="_blank">
                                <img src="{{ asset('storage/' . $f) }}" alt="Bukti Service" class="h-10 w-10 object-cover rounded">
                            </a>
                        @endforeach
                    </div>
                @endif
            @endif
            <p><strong>Status:</strong>
                @if($reimburseRequest->status === 'approved')
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Disetujui</span>
                @elseif($reimburseRequest->status === 'rejected')
                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Ditolak</span>
                @else
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                @endif
            </p>
            <p><strong>Tanggal Disetujui:</strong> {{ $reimburseRequest->approved_at ? $reimburseRequest->approved_at->format('d M Y') : '-' }}</p>
        </div>
        @can('kelola-akun')
            @if($reimburseRequest->status === 'pending')
                <form action="{{ route('reimburse-requests.approve', $reimburseRequest) }}" method="POST" class="mt-6 inline-block">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 mr-2">Setujui</button>
                </form>
                <form action="{{ route('reimburse-requests.reject', $reimburseRequest) }}" method="POST" class="mt-6 inline-block">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Tolak</button>
                </form>
            @endif
        @endcan
        @if($reimburseRequest->status === 'rejected' && $reimburseRequest->user_id === auth()->id())
            <div class="mt-6">
                <a href="{{ route('reimburse-requests.edit', $reimburseRequest) }}" class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">
                    <i class="fas fa-edit mr-2"></i>Edit Pengajuan
                </a>
                <p class="text-sm text-gray-600 mt-2">Pengajuan ditolak. Anda dapat mengedit dan mengajukan kembali.</p>
                @if($reimburseRequest->catatan_admin)
                    <p class="text-sm text-gray-600 mt-1"><strong>Catatan Admin:</strong> {{ $reimburseRequest->catatan_admin }}</p>
                @endif
            </div>
        @endif
    </div>
</div>

<script>
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
    <title>Detail Pengajuan Reimburse - {{ $reimburseRequest->nomor_pengajuan }}</title>
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
        <h1>Detail Pengajuan Reimburse</h1>
        <h2>{{ $reimburseRequest->nomor_pengajuan }}</h2>
    </div>

    <div class="section">
        <h3>Informasi Umum</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Nomor Pengajuan:</div>
                <div class="value">{{ $reimburseRequest->nomor_pengajuan }}</div>
            </div>
            <div class="info-item">
                <div class="label">Status:</div>
                <div class="value">{{ ucfirst($reimburseRequest->status) }}</div>
            </div>
            <div class="info-item">
                <div class="label">Tanggal Pengajuan:</div>
                <div class="value">{{ $reimburseRequest->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-item">
                <div class="label">PIC:</div>
                <div class="value">{{ $reimburseRequest->user->name }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Informasi Asset</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Asset:</div>
                <div class="value">{{ $reimburseRequest->asset->merk }} ({{ $reimburseRequest->asset->tipe }})</div>
            </div>
            <div class="info-item">
                <div class="label">No. Seri:</div>
                <div class="value">{{ $reimburseRequest->asset->no_seri ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Detail Reimburse</h3>
        <div class="info-item">
            <div class="label">Jenis Reimburse:</div>
            <div class="value">{{ $reimburseRequest->jenis_reimburse ?? '-' }}</div>
        </div>
        <div class="info-item">
            <div class="label">Biaya:</div>
            <div class="value">Rp {{ number_format($reimburseRequest->biaya, 0, ',', '.') }}</div>
        </div>
        <div class="info-item">
            <div class="label">Tanggal Service:</div>
            <div class="value">{{ $reimburseRequest->tanggal_service->format('d/m/Y') }}</div>
        </div>
        <div class="info-item">
            <div class="label">Keterangan:</div>
            <div class="value">{{ $reimburseRequest->keterangan }}</div>
        </div>
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
