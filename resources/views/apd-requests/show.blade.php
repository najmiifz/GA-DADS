@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-4">
        <a href="{{ route('apd-requests.index') }}" class="text-gray-600 hover:text-gray-800 mr-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Detail Pengajuan APD</h1>
        <div class="ml-auto flex space-x-2">
            <button onclick="printDetail()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-print mr-2"></i>Print Detail
            </button>
            <a href="{{ route('apd-requests.export-csv', $apdRequest) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                <i class="fas fa-file-csv mr-2"></i>Export CSV
            </a>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="space-y-2">
            <p><strong>Nomor Pengajuan:</strong> {{ $apdRequest->nomor_pengajuan }}</p>
            <p><strong>Nama Tim Mandor:</strong> {{ $apdRequest->team_mandor }}</p>
            <p><strong>Jumlah APD:</strong> {{ $apdRequest->jumlah_apd }}</p>
            <p><strong>Helm:</strong> {{ $apdRequest->helm }}</p>
            <p><strong>Rompi:</strong> {{ $apdRequest->rompi }}</p>
            <p><strong>AP Boots:</strong> {{ $apdRequest->apboots }}</p>
            <p><strong>Body Harness:</strong> {{ $apdRequest->body_harness }}</p>
            <p><strong>Sarung Tangan:</strong> {{ $apdRequest->sarung_tangan }}</p>
            <p><strong>Nama Cluster:</strong> {{ $apdRequest->nama_cluster }}</p>
            <p><strong>Status:</strong>
                @if($apdRequest->status === 'approved')
                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Disetujui</span>
                @elseif($apdRequest->status === 'rejected')
                    <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Ditolak</span>
                @else
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">Pending</span>
                @endif
            </p>
            <p><strong>Tanggal Disetujui:</strong> {{ $apdRequest->approved_at ? $apdRequest->approved_at->format('d M Y') : '-' }}</p>
        </div>
        @can('kelola-akun')
            @if($apdRequest->status === 'pending')
                <form action="{{ route('apd-requests.approve', $apdRequest) }}" method="POST" class="mt-6">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Setujui</button>
                </form>
            @endif
        @endcan
        @if($apdRequest->status === 'delivery' && auth()->id() === $apdRequest->user_id)
            <form action="{{ route('apd-requests.receive', $apdRequest) }}" method="POST" class="mt-6">
                @csrf
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Terima APD</button>
            </form>
        @endif
        @if($apdRequest->status === 'rejected' && $apdRequest->user_id === auth()->id())
            <div class="mt-6">
                <a href="{{ route('apd-requests.edit', $apdRequest) }}" class="px-4 py-2 bg-orange-600 text-white rounded hover:bg-orange-700">
                    <i class="fas fa-edit mr-2"></i>Edit Pengajuan
                </a>
                <p class="text-sm text-gray-600 mt-2">Pengajuan ditolak. Anda dapat mengedit dan mengajukan kembali.</p>
            </div>
        @endif
        @can('kelola-akun')
            @if($apdRequest->status === 'approved')
                <div class="mt-4 bg-gray-50 p-4 rounded">
                    <h2 class="font-semibold text-gray-700 mb-2">Stock Saat Ini</h2>
                    <ul class="list-disc list-inside mb-4">
                        @foreach(['helm','rompi','apboots','body_harness','sarung_tangan'] as $item)
                            @php $stk = App\Models\StockItem::where('name',$item)->first(); @endphp
                            <li class="capitalize">{{ str_replace('_',' ', $item) }}: <strong>{{ $stk?->stock ?? 0 }}</strong></li>
                        @endforeach
                    </ul>
                    <form action="{{ route('apd-requests.restock', $apdRequest) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Restock Lagi</button>
                    </form>
                </div>
            @endif
        @endcan
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
    <title>Detail Pengajuan APD - {{ $apdRequest->nomor_pengajuan }}</title>
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
        <h1>Detail Pengajuan APD</h1>
        <h2>{{ $apdRequest->nomor_pengajuan }}</h2>
    </div>

    <div class="section">
        <h3>Informasi Umum</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Nomor Pengajuan:</div>
                <div class="value">{{ $apdRequest->nomor_pengajuan }}</div>
            </div>
            <div class="info-item">
                <div class="label">Status:</div>
                <div class="value">{{ ucfirst($apdRequest->status) }}</div>
            </div>
            <div class="info-item">
                <div class="label">Nama Tim Mandor:</div>
                <div class="value">{{ $apdRequest->team_mandor }}</div>
            </div>
            <div class="info-item">
                <div class="label">Nama Cluster:</div>
                <div class="value">{{ $apdRequest->nama_cluster }}</div>
            </div>
            <div class="info-item">
                <div class="label">Tanggal Pengajuan:</div>
                <div class="value">{{ $apdRequest->created_at->format('d/m/Y H:i') }}</div>
            </div>
            <div class="info-item">
                <div class="label">Tanggal Disetujui:</div>
                <div class="value">{{ $apdRequest->approved_at ? $apdRequest->approved_at->format('d/m/Y H:i') : '-' }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Detail APD</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Helm:</div>
                <div class="value">{{ $apdRequest->helm }} pcs</div>
            </div>
            <div class="info-item">
                <div class="label">Rompi:</div>
                <div class="value">{{ $apdRequest->rompi }} pcs</div>
            </div>
            <div class="info-item">
                <div class="label">AP Boots:</div>
                <div class="value">{{ $apdRequest->apboots }} pcs</div>
            </div>
            <div class="info-item">
                <div class="label">Body Harness:</div>
                <div class="value">{{ $apdRequest->body_harness }} pcs</div>
            </div>
            <div class="info-item">
                <div class="label">Sarung Tangan:</div>
                <div class="value">{{ $apdRequest->sarung_tangan }} pcs</div>
            </div>
            <div class="info-item">
                <div class="label">Total APD:</div>
                <div class="value">{{ $apdRequest->jumlah_apd }} pcs</div>
            </div>
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
