@extends('layouts.app')
@section('content')
<div class="max-w-3xl mx-auto py-6">
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-3xl font-semibold mb-6 text-gray-800">Detail Pengajuan SPJ #{{ $spjRequest->id }}</h1>
        <div class="grid grid-cols-1 gap-4">
            <!-- Nama Pegawai -->
            <div class="flex">
                <dt class="w-1/3 font-medium text-gray-600">Nama Pegawai</dt>
                <dd class="w-2/3 text-gray-800">{{ $spjRequest->nama_pegawai }}</dd>
            </div>
            <!-- Tanggal SPJ -->
            <div class="flex">
                <dt class="w-1/3 font-medium text-gray-600">Tanggal SPJ</dt>
                <dd class="w-2/3 text-gray-800">{{ \Carbon\Carbon::parse($spjRequest->spj_date)->format('d M Y') }}</dd>
            </div>
            <!-- Status -->
            <div class="flex">
                <dt class="w-1/3 font-medium text-gray-600">Status</dt>
                <dd class="w-2/3">
                    @php $map=['pending'=>'yellow','approved'=>'green','rejected'=>'red']; $c=$map[$spjRequest->status] ?? 'gray'; @endphp
                    <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-{{$c}}-100 text-{{$c}}-800">{{ ucfirst($spjRequest->status) }}</span>
                </dd>
            </div>
            <!-- Keperluan -->
            <div class="flex">
                <dt class="w-1/3 font-medium text-gray-600">Keperluan</dt>
                <dd class="w-2/3 text-gray-800">{{ $spjRequest->keperluan }}</dd>
            </div>
            <!-- Dokumen BAST Mutasi -->
            <div class="flex">
                <dt class="w-1/3 font-medium text-gray-600">BAST Mutasi</dt>
                <dd class="w-2/3 text-gray-800">{{ $spjRequest->bast_mutasi }}</dd>
            </div>
            @if($spjRequest->bast_mutasi_file)
            <div class="flex">
                <dt class="w-1/3 font-medium text-gray-600">File BAST Mutasi</dt>
                <dd class="w-2/3"><a href="{{ asset('storage/'.$spjRequest->bast_mutasi_file) }}" class="text-blue-600 hover:underline">Download</a></dd>
            </div>
            @endif
            <!-- Dokumen BAST Inventaris -->
            <div class="flex">
                <dt class="w-1/3 font-medium text-gray-600">BAST Inventaris</dt>
                <dd class="w-2/3 text-gray-800">{{ $spjRequest->bast_inventaris }}</dd>
            </div>
            @if($spjRequest->bast_inventaris_file)
            <div class="flex">
                <dt class="w-1/3 font-medium text-gray-600">File BAST Inventaris</dt>
                <dd class="w-2/3"><a href="{{ asset('storage/'.$spjRequest->bast_inventaris_file) }}" class="text-blue-600 hover:underline">Download</a></dd>
            </div>
            @endif
            <!-- Penugasan -->
            <div class="flex">
                <dt class="w-1/3 font-medium text-gray-600">Diperintahkan Oleh</dt>
                <dd class="w-2/3 text-gray-800">{{ $spjRequest->penugasan_by }}</dd>
            </div>
            @if($spjRequest->bukti_penugasan_file)
            <div class="flex">
                <dt class="w-1/3 font-medium text-gray-600">Bukti Penugasan</dt>
                <dd class="w-2/3"><a href="{{ asset('storage/'.$spjRequest->bukti_penugasan_file) }}" class="text-blue-600 hover:underline">Download</a></dd>
            </div>
            @endif
            <!-- Rute Perjalanan -->
            <div class="flex">
                <dt class="w-1/3 font-medium text-gray-600">Perjalanan Dari</dt>
                <dd class="w-2/3 text-gray-800">{{ $spjRequest->perjalanan_from }}</dd>
            </div>
            <div class="flex">
                <dt class="w-1/3 font-medium text-gray-600">Perjalanan Ke</dt>
                <dd class="w-2/3 text-gray-800">{{ $spjRequest->perjalanan_to }}</dd>
            </div>
            <!-- Transportasi -->
            <div class="flex">
                <dt class="w-1/3 font-medium text-gray-600">Transportasi</dt>
                <dd class="w-2/3 text-gray-800">{{ $spjRequest->transportasi }}</dd>
            </div>
            <!-- Biaya Estimasi -->
            <div class="flex">
                <dt class="w-1/3 font-medium text-gray-600">Biaya Estimasi</dt>
                <dd class="w-2/3 text-gray-800">{{ $spjRequest->biaya_estimasi }}</dd>
            </div>
            @if($spjRequest->nota_files)
                <div>
                    <dt class="font-medium text-gray-600">File Nota</dt>
                    <dd class="mt-2 space-y-1">
                        @foreach($spjRequest->nota_files as $file)
                            <a href="{{ asset('storage/' . $file) }}" class="text-indigo-600 hover:underline">{{ basename($file) }}</a><br>
                        @endforeach
                    </dd>
                </div>
            @endif
        </div>
        @if($spjRequest->status === 'pending')
            @canany(['isAdmin', 'kelola-akun'])
            <div class="mt-6 flex space-x-2">
                <form action="{{ route('spj.approve', $spjRequest) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Setujui</button>
                </form>
                <form action="{{ route('spj.reject', $spjRequest) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Tolak</button>
                </form>
            </div>
            @endcanany
        @endif
        <div class="mt-6 flex space-x-2">
            <a href="{{ route('spj.index') }}" class="inline-block px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">Kembali</a>
            <button onclick="printDetail()" class="inline-block px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                <i class="fas fa-print mr-2"></i>Cetak Detail
            </button>
            <a href="{{ route('spj.export-csv', $spjRequest) }}" class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                <i class="fas fa-file-csv mr-2"></i>Unduh CSV
            </a>
            </button>
        </div>
    </div>
</div>
<!-- End SPJ detail card -->

<script>
function printDetail() {
    // Debug: Check if function is called
    console.log('printDetail function called');

    try {
        const printWindow = window.open('', '_blank', 'width=800,height=600');

        // Check if window was blocked
        if (!printWindow) {
            alert('Pop-up blocker mungkin mencegah pencetakan. Silakan izinkan pop-up untuk situs ini dan coba lagi.');
            return;
        }

    const printContent = `<!DOCTYPE html>
<html>
<head>
    <title>Detail Pengajuan SPJ #{{ $spjRequest->id }}</title>
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
        <h1>Detail Pengajuan SPJ</h1>
        <h2>SPJ #{{ $spjRequest->id }}</h2>
    </div>

    <div class="section">
        <h3>Informasi Umum</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Nama Pegawai:</div>
                <div class="value">{{ $spjRequest->nama_pegawai }}</div>
            </div>
            <div class="info-item">
                <div class="label">Status:</div>
                <div class="value">{{ ucfirst($spjRequest->status) }}</div>
            </div>
            <div class="info-item">
                <div class="label">Tanggal SPJ:</div>
                <div class="value">{{ \Carbon\Carbon::parse($spjRequest->spj_date)->format('d M Y') }}</div>
            </div>
            <div class="info-item">
                <div class="label">Keperluan:</div>
                <div class="value">{{ $spjRequest->keperluan }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Dokumen BAST</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="label">BAST Mutasi:</div>
                <div class="value">{{ $spjRequest->bast_mutasi }}</div>
            </div>
            <div class="info-item">
                <div class="label">BAST Inventaris:</div>
                <div class="value">{{ $spjRequest->bast_inventaris }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Penugasan</h3>
        <div class="info-item">
            <div class="label">Diperintahkan Oleh:</div>
            <div class="value">{{ $spjRequest->penugasan_by }}</div>
        </div>
    </div>

    <div class="section">
        <h3>Rute Perjalanan</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Perjalanan Dari:</div>
                <div class="value">{{ $spjRequest->perjalanan_from }}</div>
            </div>
            <div class="info-item">
                <div class="label">Perjalanan Ke:</div>
                <div class="value">{{ $spjRequest->perjalanan_to }}</div>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Biaya dan Transportasi</h3>
        <div class="info-grid">
            <div class="info-item">
                <div class="label">Transportasi:</div>
                <div class="value">{{ $spjRequest->transportasi }}</div>
            </div>
            <div class="info-item">
                <div class="label">Biaya Estimasi:</div>
                <div class="value">{{ $spjRequest->biaya_estimasi }}</div>
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

        // Wait for content to load before printing
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
