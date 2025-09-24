@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <h1 class="text-3xl font-bold text-center text-gray-800">Dasbor Splicer</h1>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-red-600 text-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">Total Splicers</p>
                    <p class="text-2xl font-bold">{{ $splicers->total() ?? $splicers->count() }}</p>
                </div>
                <div class="p-3 bg-white rounded-lg">
                    <i class="fas fa-tools text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-red-600 text-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">Total Nilai</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($splicers->sum('harga_sewa'),0,',','.') }}</p>
                </div>
                <div class="p-3 bg-white rounded-lg">
                    <i class="fas fa-dollar-sign text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-red-600 text-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">Available</p>
                    <p class="text-2xl font-bold">{{ $splicers->where('pic','Available')->count() }}</p>
                </div>
                <div class="p-3 bg-white rounded-lg">
                    <i class="fas fa-check-circle text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-red-600 text-white rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">In Use</p>
                    <p class="text-2xl font-bold">{{ $splicers->where('pic','<>','Available')->count() }}</p>
                </div>
                <div class="p-3 bg-white rounded-lg">
                    <i class="fas fa-exclamation-circle text-red-600"></i>
                </div>
            </div>
        </div>
    </div>
    {{-- Chart --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
            <h2 id="chart-title" class="text-xl font-bold text-gray-700">Total Biaya Servis per Splicer</h2>
            <div class="flex items-center gap-2">
                <label for="splicer-select" class="text-sm font-medium text-gray-600">Pilih Splicer:</label>
                <select id="splicer-select" class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Semua Splicer</option>
                    @foreach($splicerList as $splicer)
                        <option value="{{ $splicer->id }}" {{ request('splicer_id') == $splicer->id ? 'selected' : '' }}>
                            {{ $splicer->merk }} ({{ $splicer->serial_number }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="relative h-72">
            <canvas id="splicerChart"></canvas>
        </div>

    <div class="bg-white shadow-lg rounded-xl p-6">
        <!-- Filter bar -->
        <form method="GET" action="{{ route('assets.splicers') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end mb-6">
            <div>
                <label for="pic-filter" class="text-sm font-medium text-gray-600">Filter PIC</label>
                <select id="pic-filter" name="pic" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                    <option value="">Semua PIC</option>
                    @if(collect($pics)->contains('Available'))
                        <option value="Available" {{ request('pic') == 'Available' ? 'selected' : '' }} class="bg-green-100 text-green-800 font-semibold">Available</option>
                    @endif
                    @foreach($pics as $pic)
                        @if($pic !== 'Available')
                            <option value="{{ $pic }}" {{ request('pic') == $pic ? 'selected' : '' }}>{{ $pic }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label for="project-filter" class="text-sm font-medium text-gray-600">Filter Project</label>
                <select id="project-filter" name="project" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                    <option value="">Semua Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project }}" {{ request('project') == $project ? 'selected' : '' }}>{{ $project }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="lokasi-filter" class="text-sm font-medium text-gray-600">Filter Lokasi</label>
                <select id="lokasi-filter" name="lokasi" class="w-full mt-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="this.form.submit()">
                    <option value="">Semua Lokasi</option>
                    @foreach($lokasis as $lokasi)
                        <option value="{{ $lokasi }}" {{ request('lokasi') == $lokasi ? 'selected' : '' }}>{{ $lokasi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-3 justify-end">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors shadow-sm">Filter</button>
                <a href="{{ route('assets.splicers') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors shadow-sm">Reset</a>
            </div>
        </form>

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4">
            <h2 class="text-xl font-bold text-gray-700">Data Splicer</h2>
            <div class="flex gap-3 mt-3 sm:mt-0">
                <form id="export-splicers-form" action="{{ route('assets.export.splicers') }}" method="GET" class="m-0">
                    <input type="hidden" name="pic" value="{{ request('pic') }}">
                    <input type="hidden" name="project" value="{{ request('project') }}">
                    <input type="hidden" name="lokasi" value="{{ request('lokasi') }}">
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                    <input type="hidden" name="dir" value="{{ request('dir') }}">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-full shadow-md hover:bg-green-700 transition-colors" title="Ekspor CSV">
                        <i class="fas fa-file-excel mr-3"></i>
                        <span class="font-medium">Ekspor CSV</span>
                    </button>
                </form>
                @can('kelola-aset')
                <button id="add-asset-splicers-btn" onclick="showModalSplicers(false)" class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-full shadow-md hover:bg-red-700 transition-colors" title="Tambah Asset">
                    <i class="fas fa-plus mr-3"></i>
                    <span class="font-medium">Tambah Asset</span>
                </button>
                @endcan
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        @php
                            $sortableCols = ['jenis_aset' => 'Jenis Aset', 'pic' => 'PIC', 'merk' => 'Merk', 'project' => 'Project', 'lokasi' => 'Lokasi', 'harga_sewa' => 'Harga Sewa', 'total_servis' => 'Total Servis'];
                        @endphp
                        @foreach($sortableCols as $col => $title)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('assets.splicers', array_merge(request()->query(), ['sort' => $col, 'dir' => request('dir') == 'asc' ? 'desc' : 'asc'])) }}" class="flex items-center gap-2">
                                    {{ $title }}
                                    @if(request('sort') == $col)
                                        <i class="fas fa-sort-{{ request('dir') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </a>
                            </th>
                        @endforeach
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Aset</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($splicers as $splicer)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $splicer->jenis_aset }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($splicer->pic === 'Available')
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                            @elseif($splicer->pic === 'Rusak')
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Rusak</span>
                            @elseif($splicer->pic === 'Hilang')
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Hilang</span>
                            @else
                                <div class="text-sm text-gray-900">{{ $splicer->user->name ?? $splicer->pic }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $splicer->merk }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $splicer->project }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $splicer->lokasi }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $splicer->harga_sewa > 0 ? 'Rp ' . number_format($splicer->harga_sewa, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $splicer->total_servis > 0 ? 'Rp ' . number_format($splicer->total_servis, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $splicer->serial_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-3">
                                <button onclick="viewAsset({{ $splicer->id }})" class="text-blue-600 hover:text-blue-900" title="Detail"><i class="fas fa-eye"></i></button>
                                @can('kelola-aset')
                                <button onclick="editAsset({{ $splicer->id }})" class="text-yellow-600 hover:text-yellow-900" title="Edit"><i class="fas fa-edit"></i></button>
                                <button onclick="deleteAsset({{ $splicer->id }})" class="text-red-600 hover:text-red-900" title="Hapus"><i class="fas fa-trash"></i></button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-10 text-gray-500">Tidak ada data.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="flex justify-between items-center mt-6">
            <p class="text-sm text-gray-600">
                Menampilkan {{ $splicers->firstItem() ?? 0 }}-{{ $splicers->lastItem() ?? 0 }} dari {{ $splicers->total() ?? 0 }}
            </p>
            <div class="flex space-x-4">
                <a href="{{ $splicers->previousPageUrl() }}" class="{{ !$splicers->onFirstPage() ? 'px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50' : 'px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-400 cursor-not-allowed' }}">
                    Prev
                </a>
                <a href="{{ $splicers->nextPageUrl() }}" class="{{ $splicers->hasMorePages() ? 'px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50' : 'px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-400 cursor-not-allowed' }}">
                    Next
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Viewing Asset Details -->
<div id="view-asset-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-6xl w-full m-4">
        <div class="p-6 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h2 id="modal-title" class="text-2xl font-bold text-gray-800">Detail Aset</h2>
                <button id="close-view-asset-modal" class="text-gray-500 hover:text-gray-800">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>
        <div id="asset-details-content" class="p-6 max-h-[70vh] overflow-y-auto">
            <!-- Dynamic content will be loaded here -->
        </div>
        <div class="p-6 bg-gray-50 border-t border-gray-200 flex justify-end gap-3">
            <button type="button" id="export-servis-btn" class="hidden px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors shadow-sm">
                <i class="fas fa-file-csv mr-2"></i>Ekspor Servis (CSV)
            </button>
            <button type="button" id="close-view-asset" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors shadow-sm">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal for create/edit asset -->
<div id="asset-modal-splicers" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg max-w-7xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-900">Tambah Asset Baru</h2>
                    <button data-close-modal-splicers class="text-gray-500 hover:text-gray-800 focus:outline-none">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="asset-form-splicers" method="POST" action="{{ route('assets.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tipe Asset -->
                        <div>
                            <label for="tipe" class="block text-sm font-medium text-gray-700 mb-1">Tipe Asset *</label>
                            <select name="tipe" id="tipe" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm" required onchange="handleCustomField(this, 'tipe_custom')">
                                <option value="">Pilih Tipe</option>
                                <option value="__add__">+ Tambah Baru</option>
                                @foreach($tipes as $t)
                                    <option value="{{ $t }}" {{ (request('tipe') == $t || old('tipe') == $t || $t == 'Splicer') ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="tipe_custom" id="tipe_custom" placeholder="Masukkan tipe baru..." class="hidden mt-2 w-full px-3 py-2 border rounded-md">
                        </div>

                        <!-- Jenis Asset -->
                        <div>
                            <label for="jenis_aset" class="block text-sm font-medium text-gray-700 mb-1">Jenis Asset *</label>
                            <select name="jenis_aset" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm" required>
                                <option value="">Pilih Jenis</option>
                                <option value="__add__">+ Tambah Baru</option>
                                @foreach($jenisAsets as $ja)
                                    <option value="{{ $ja }}" {{ (request('jenis_aset') == $ja || old('jenis_aset') == $ja) ? 'selected' : '' }}>{{ $ja }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="jenis_aset_custom" placeholder="Masukkan jenis baru..." class="hidden mt-2 w-full px-3 py-2 border rounded-md">
                        </div>

                        <!-- Merk -->
                        <div>
                            <label for="merk" class="block text-sm font-medium text-gray-700 mb-1">Merk *</label>
                            <select name="merk" id="merk" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm" required onchange="handleCustomField(this, 'merk_custom')">
                                <option value="">Pilih Merk</option>
                                <option value="__add__">+ Tambah Baru</option>
                                @foreach($merks as $m)
                                    <option value="{{ $m }}" {{ (request('merk') == $m || old('merk') == $m) ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="merk_custom" id="merk_custom" placeholder="Masukkan merk baru..." class="hidden mt-2 w-full px-3 py-2 border rounded-md">
                        </div>

                        <!-- Nomor Aset -->
                        <div>
                            <label for="serial_number" class="block text-sm font-medium text-gray-700 mb-1">Nomor Aset</label>
                            <input type="text" name="serial_number" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                        </div>

                        <!-- PIC -->
                        <div>
                            <label for="pic" class="block text-sm font-medium text-gray-700 mb-1">PIC *</label>
                            <select name="pic" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm" required>
                                <option value="">Pilih PIC</option>
                                <option value="__add__">+ Tambah Baru</option>
                                <option value="Rusak" style="color: #D97706;">Rusak</option>
                                <option value="Hilang" style="color: #DC2626;">Hilang</option>
                                <option value="Available">Available</option>
                            </select>
                            <input type="text" name="pic_custom" placeholder="Masukkan PIC baru..." class="hidden mt-2 w-full px-3 py-2 border rounded-md">
                        </div>

                        <!-- Project -->
                        <div>
                            <label for="project" class="block text-sm font-medium text-gray-700 mb-1">Project *</label>
                            <select name="project" id="project" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm" required onchange="handleCustomField(this, 'project_custom')">
                                <option value="">Pilih Project</option>
                                <option value="__add__">+ Tambah Baru</option>
                                @foreach($projects as $project)
                                    <option value="{{ $project }}">{{ $project }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="project_custom" id="project_custom" placeholder="Masukkan project baru..." class="hidden mt-2 w-full px-3 py-2 border rounded-md">
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-1">Lokasi *</label>
                            <select name="lokasi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm" required>
                                <option value="">Pilih Lokasi</option>
                                <option value="__add__">+ Tambah Baru</option>
                                @foreach($lokasis as $lokasi)
                                    <option value="{{ $lokasi }}">{{ $lokasi }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="lokasi_custom" placeholder="Masukkan lokasi baru..." class="hidden mt-2 w-full px-3 py-2 border rounded-md">
                        </div>

                        <!-- Tanggal Beli -->
                        <div>
                            <label for="tanggal_beli" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Beli</label>
                            <input type="date" name="tanggal_beli" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                        </div>

                        <!-- Harga Beli -->
                        <div>
                            <label for="harga_beli" class="block text-sm font-medium text-gray-700 mb-1">Harga Beli</label>
                            <input type="number" name="harga_beli" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                        </div>

                        <!-- Harga Sewa -->
                        <div>
                            <label for="harga_sewa" class="block text-sm font-medium text-gray-700 mb-1">Harga Sewa</label>
                            <input type="number" name="harga_sewa" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 shadow-sm">
                        </div>
                        <!-- Foto Aset (opsional) -->
                        <div class="mt-6">
                            <label for="foto_aset_splicers" class="block text-sm font-medium text-gray-700 mb-1">Foto Aset (opsional)</label>
                            <div class="flex items-center gap-4">
                                <div id="foto-aset-preview-splicers" class="hidden">
                                    <img src="" alt="Foto Aset" class="w-32 h-24 object-cover border rounded" />
                                    <p class="text-sm text-gray-500 mt-1">Foto saat ini</p>
                                </div>
                                <div class="flex flex-col">
                                    <input type="file" name="foto_aset" id="foto_aset_splicers" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md" onchange="updateFileNameSplicers(this)">
                                    <span id="foto-aset-name-splicers" class="text-sm text-gray-500 mt-1">No file</span>
                                </div>
                            </div>
                        </div>
                        <!-- Keterangan Aset (opsional) -->
                        <div>
                            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan Aset (opsional)</label>
                            <input type="text" name="keterangan" id="keterangan" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Masukkan keterangan aset...">
                        </div>
                    </div> <!-- end form grid -->

                   {{-- Riwayat Servis --}}
                   <div class="border-t pt-6">
                       <div class="flex items-center justify-between mb-4">
                           <h3 class="text-lg font-semibold">Riwayat Servis</h3>
                           <button type="button" id="add-service-btn" class="px-4 py-2 bg-green-500 text-white rounded">Tambah Riwayat</button>
                       </div>
                       <div id="service-rows-splicers" class="space-y-3">
                           <div class="flex gap-3 items-center service-row bg-white border rounded-lg p-3">
                               <input type="hidden" name="service_id[]" value="">
                               <div class="flex-1">
                                   <input type="text" name="service_date[]" placeholder="dd/mm/yyyy" onfocus="this.type='date'" onblur="if(!this.value) this.type='text'" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                               </div>
                               <div class="flex-1 min-w-[200px]">
                                   <input type="text" name="service_desc[]" placeholder="Keterangan servis" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                               </div>
                               <div style="width:140px;">
                                   <input type="number" name="service_cost[]" placeholder="Biaya" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                               </div>
                               <div style="width:160px;">
                                   <input type="text" name="service_vendor[]" placeholder="Vendor" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                               </div>
                               <div class="flex items-center gap-2">
                                   <label class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md border border-gray-300 cursor-pointer">
                                       Upload
                                       <input type="file" name="service_file[]" class="hidden" onchange="updateFileName(this)">
                                   </label>
                                   <span class="text-sm text-gray-500 file-name">No file</span>
                                   <button type="button" class="text-red-600 hover:text-red-800 remove-service" onclick="removeServiceRow(this)">✕</button>
                               </div>
                           </div>
                       </div>
                   </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button data-close-modal-splicers type="button" class="px-6 py-3 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors">Batal</button>
                        <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartCanvas = document.getElementById('splicerChart');
    if (!chartCanvas) return;

    let splicerChart;

    const initialChartData = @json($servicePerSplicer ?? []);
    const historyChartData = @json($splicerHistoryData ?? null);

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
    }

    function renderChart(data, type = 'bar', title = 'Total Biaya Servis per Splicer') {
        if (splicerChart) {
            splicerChart.destroy();
        }
        document.getElementById('chart-title').innerText = title;
        splicerChart = new Chart(chartCanvas, {
            type: type,
            data: {
                labels: Object.keys(data),
                datasets: [{
                    label: 'Biaya Servis',
                    data: Object.values(data),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return 'Rp ' + (value / 1000) + 'k';
                            }
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += formatRupiah(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }

    if (historyChartData) {
        const selectedSplicerOption = document.querySelector('#splicer-select option:checked');
        const chartTitle = `Riwayat Biaya Servis: ${selectedSplicerOption.text}`;
        renderChart(historyChartData, 'line', chartTitle);
    } else {
        renderChart(initialChartData);
    }

    document.getElementById('splicer-select').addEventListener('change', function() {
        const splicerId = this.value;
        const url = new URL(window.location.href);
        if (splicerId) {
            url.searchParams.set('splicer_id', splicerId);
        } else {
            url.searchParams.delete('splicer_id');
        }
        window.location.href = url.toString();
    });


    function closeModal() {
        document.getElementById('view-asset-modal').classList.add('hidden');
    }

    document.getElementById('close-view-asset-modal').addEventListener('click', closeModal);
    document.getElementById('close-view-asset').addEventListener('click', closeModal);

    window.viewAsset = function(assetId) {
        fetch(`/assets/${assetId}/json`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) return;
                const a = data.asset || {};
                const modalContent = document.getElementById('asset-details-content');
                const exportBtn = document.getElementById('export-servis-btn');

                function fmtRupiah(n) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(Number(n || 0));
                }
                function fmtDate(iso) {
                    if (!iso) return 'N/A';
                    const d = new Date(iso);
                    if (isNaN(d.getTime())) return 'N/A';
                    const opts = { day: '2-digit', month: 'short', year: 'numeric' };
                    return d.toLocaleDateString('id-ID', opts);
                }

                const services = Array.isArray(a.services) ? a.services.slice().reverse() : [];
                const servicesList = services.length ? services.map(s => {
                    const fileHref = s.file_url || s.file_path || '';
                    const dateStr = s.service_date ? fmtDate(s.service_date) : '-';
                    const costStr = fmtRupiah(s.cost || 0);
                    const vendor = s.vendor || '-';
                    const desc = s.description || '-';
                    const fileHtml = fileHref ? `<a href="${fileHref}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">Bukti Terlampir</a>` : '<span class="text-gray-400">Bukti tidak ada</span>';
                    return `
                        <div class="border rounded-lg p-3 bg-white">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-sm text-gray-500">${dateStr} • <span class="text-gray-600">${vendor}</span></div>
                                    <div class="font-medium text-gray-800">${desc}</div>
                                    <div class="text-xs text-gray-500 mt-1">${fileHtml}</div>
                                </div>
                                <div class="text-right font-semibold text-gray-800">${costStr}</div>
                            </div>
                        </div>`;
                }).join('\n') : '<p class="text-gray-500">Belum ada riwayat servis.</p>';

                // Header summary cards (tanpa pajak untuk Splicer)
                const hargaBeliCard = `
                    <div class="flex items-center gap-3 p-4 rounded-lg bg-green-50 border">
                        <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center"><i class="fas fa-money-bill"></i></div>
                        <div>
                            <div class="text-xs text-gray-500">Harga Beli</div>
                            <div class="font-semibold text-gray-800">${fmtRupiah(a.harga_beli || 0)}</div>
                        </div>
                    </div>`;
                const tanggalBeliCard = `
                    <div class="flex items-center gap-3 p-4 rounded-lg bg-indigo-50 border">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center"><i class="fas fa-calendar"></i></div>
                        <div>
                            <div class="text-xs text-gray-500">Tanggal Beli</div>
                            <div class="font-semibold text-gray-800">${fmtDate(a.tanggal_beli)}</div>
                        </div>
                    </div>`;
                const picCard = `
                    <div class="flex items-center gap-3 p-4 rounded-lg bg-blue-50 border">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center"><i class="fas fa-user"></i></div>
                        <div>
                            <div class="text-xs text-gray-500">PIC</div>
                            <div class="font-semibold text-gray-800">${a.pic || '-'}</div>
                        </div>
                    </div>`;
                const totalServisCard = `
                    <div class="flex items-center gap-3 p-4 rounded-lg bg-rose-50 border">
                        <div class="w-10 h-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center"><i class="fas fa-wrench"></i></div>
                        <div>
                            <div class="text-xs text-gray-500">Total Biaya Servis</div>
                            <div class="font-semibold text-gray-800">${fmtRupiah(a.total_servis || 0)}</div>
                        </div>
                    </div>`;

                // Compose detail HTML
                modalContent.innerHTML = `
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Detail Asset</h1>
                            <p class="text-sm text-gray-500">Informasi lengkap aset ${a.merk || '-'}</p>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" id="hdr-edit-asset" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"><i class="fas fa-edit mr-2"></i>Edit Asset</button>
                            <button type="button" id="hdr-back" class="inline-flex items-center px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-800"><i class="fas fa-arrow-left mr-2"></i>Kembali</button>
                        </div>
                    </div>

                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">${a.merk || '-'} <span class="text-gray-500">${a.serial_number ? '(' + a.serial_number + ')' : ''}</span></h2>
                            <p class="text-sm text-gray-500 mt-1">Tipe: ${a.tipe || '-'} • Jenis: ${a.jenis_aset || '-'}</p>
                        </div>
                        <div class="text-sm text-gray-500">Serial Number<br><span class="text-gray-900 font-semibold">${a.serial_number || '-'}</span></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                        ${hargaBeliCard}
                            ${tanggalBeliCard}
                        ${picCard}
                        ${totalServisCard}
                    </div>

                    <!-- Foto Aset -->
                    ${a.foto_aset_url ? `
                    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="p-4"><h3 class="font-semibold">Foto Aset</h3></div>
                        <div class="p-4"><img src="${a.foto_aset_url}" alt="Foto Aset" class="w-full max-w-md h-auto object-cover border rounded-lg cursor-pointer" onclick="openImageModal('${a.foto_aset_url}','Foto Aset')"></div>
                    </div>` : ''}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-base font-semibold text-gray-800 mb-3">Ringkasan Asset</h3>
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div class="text-gray-500">Jenis Asset</div><div class="text-gray-900 font-medium">${a.jenis_aset || '-'}</div>
                                    <div class="text-gray-500">Tipe</div><div class="text-gray-900 font-medium">${a.tipe || '-'}</div>
                                    <div class="text-gray-500">Merk</div><div class="text-gray-900 font-medium">${a.merk || '-'}</div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-base font-semibold text-gray-800 mb-3">Riwayat Servis Lengkap</h3>
                                <div class="space-y-3 max-h-64 overflow-y-auto">${servicesList}</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                        <div class="bg-white p-4 rounded-lg border">
                            <h3 class="text-base font-semibold text-gray-800 mb-3">Informasi Asset</h3>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="text-gray-500">Jenis Aset</div><div class="text-gray-900 font-medium">${a.jenis_aset || '-'}</div>
                                <div class="text-gray-500">Tipe</div><div class="text-gray-900 font-medium">${a.tipe || '-'}</div>
                                <div class="text-gray-500">Merk</div><div class="text-gray-900 font-medium">${a.merk || '-'}</div>
                            </div>
                        </div>
                        <div class="bg-white p-4 rounded-lg border">
                            <h3 class="text-base font-semibold text-gray-800 mb-3">Lokasi & Project</h3>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="text-gray-500">Lokasi</div><div class="text-gray-900 font-medium">${a.lokasi || '-'}</div>
                                <div class="text-gray-500">Project</div><div class="text-gray-900 font-medium">${a.project || '-'}</div>
                                <div class="text-gray-500">PIC</div><div class="text-gray-900 font-medium">${a.pic || '-'}</div>
                            </div>
                        </div>
                    </div>
                    <!-- Keterangan Aset -->
                    ${a.keterangan ? `
                    <div class="mt-6 bg-white rounded-lg border border-gray-200 p-4">
                        <h3 class="text-base font-semibold text-gray-800 mb-3">Keterangan Aset</h3>
                        <p class="text-gray-700 leading-relaxed">${a.keterangan}</p>
                    </div>
                    ` : ''}

                    <div class="mt-6 bg-white p-4 rounded-lg border">
                        <h3 class="text-base font-semibold text-gray-800 mb-3">Timeline Asset</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center"><i class="fas fa-plus"></i></div>
                                <div>
                                    <div class="text-sm font-medium text-gray-800">Asset Created</div>
                                    <div class="text-xs text-gray-500">${fmtDate(a.created_at)}</div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center"><i class="fas fa-sync"></i></div>
                                <div>
                                    <div class="text-sm font-medium text-gray-800">Last Updated</div>
                                    <div class="text-xs text-gray-500">${fmtDate(a.updated_at)}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <button type="button" id="btn-print-detail" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200"><i class="fas fa-print mr-2"></i>Print Detail</button>
                        <button type="button" id="btn-edit-asset" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700"><i class="fas fa-edit mr-2"></i>Edit Asset</button>
                        <button type="button" id="btn-copy-info" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200"><i class="fas fa-copy mr-2"></i>Copy Info</button>
                    </div>
                `;

                // Footer export button (only servis for splicer)
                if (services.length) {
                    exportBtn.classList.remove('hidden');
                    exportBtn.onclick = () => window.location.href = `/assets/${a.id}/export-servis`;
                } else {
                    exportBtn.classList.add('hidden');
                }

                // Bind header/quick actions
                const btnPrint = document.getElementById('btn-print-detail');
                const btnEdit = document.getElementById('btn-edit-asset');
                const btnCopy = document.getElementById('btn-copy-info');
                const hdrEdit = document.getElementById('hdr-edit-asset');
                const hdrBack = document.getElementById('hdr-back');
                btnPrint?.addEventListener('click', () => window.print());
                btnEdit?.addEventListener('click', () => window.editAsset(a.id));
                btnCopy?.addEventListener('click', () => {
                    const text = `Asset: ${a.merk || '-'} ${a.serial_number ? '(' + a.serial_number + ')' : ''}\n` +
                                 `Tipe/Jenis: ${a.tipe || '-'} / ${a.jenis_aset || '-'}\n` +
                                 `PIC: ${a.pic || '-'}\n` +
                                 `Lokasi/Project: ${a.lokasi || '-'} / ${a.project || '-'}\n` +
                                 `Total Servis: ${fmtRupiah(a.total_servis || 0)}`;
                    navigator.clipboard?.writeText(text).then(()=>{}).catch(()=>{});
                });
                hdrEdit?.addEventListener('click', () => window.editAsset(a.id));
                hdrBack?.addEventListener('click', () => document.getElementById('view-asset-modal').classList.add('hidden'));

                document.getElementById('view-asset-modal').classList.remove('hidden');
            });
    }

    // Edit asset (restore proper function)
    window.editAsset = function(id) {
        fetch(`/assets/${id}/json`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) { alert('Gagal mengambil data asset'); return; }
                const asset = data.asset || {};
                const form = document.getElementById('asset-form-splicers');
                if (!form) return;
                form.action = `/assets/${id}`;
                let methodField = document.querySelector('input[name="_method"]');
                if (!methodField) {
                    methodField = document.createElement('input');
                    methodField.type = 'hidden';
                    methodField.name = '_method';
                    form.appendChild(methodField);
                }
                methodField.value = 'PUT';

                // Ensure merk select includes the asset's existing merk
                const merkSelect = form.querySelector('[name="merk"]');
                if (asset.merk) {
                    const exists = Array.from(merkSelect.options).some(o => o.value === asset.merk);
                    if (!exists) {
                        const opt = document.createElement('option');
                        opt.value = asset.merk;
                        opt.text = asset.merk;
                        merkSelect.appendChild(opt);
                    }
                }

                // Populate fields
                form.querySelector('[name="tipe"]').value = asset.tipe || 'Splicer';
                form.querySelector('[name="jenis_aset"]').value = asset.jenis_aset || '';
                form.querySelector('[name="merk"]').value = asset.merk || '';
                form.querySelector('[name="serial_number"]').value = asset.serial_number || '';
                form.querySelector('[name="pic"]').value = asset.pic || '';
                form.querySelector('[name="project"]').value = asset.project || '';
                form.querySelector('[name="lokasi"]').value = asset.lokasi || '';
                form.querySelector('[name="tanggal_beli"]').value = asset.tanggal_beli || '';
                form.querySelector('[name="harga_beli"]').value = asset.harga_beli || '';
                form.querySelector('[name="harga_sewa"]').value = asset.harga_sewa || '';
                // Reset foto_aset input and show existing filename and preview
                const fotoInput = form.querySelector('[name="foto_aset"]');
                if (fotoInput) {
                    fotoInput.value = '';
                }
                const nameSpan = document.getElementById('foto-aset-name-splicers');
                const preview = document.getElementById('foto-aset-preview-splicers');
                if (asset.foto_aset_url) {
                    if (nameSpan) nameSpan.textContent = asset.foto_aset_url.split('/').pop();
                    if (preview) {
                        preview.classList.remove('hidden');
                        preview.querySelector('img').src = asset.foto_aset_url;
                    }
                } else {
                    if (nameSpan) nameSpan.textContent = 'No file';
                    if (preview) preview.classList.add('hidden');
                }
                form.querySelector('[name="keterangan"]').value = asset.keterangan || '';

                // Populate existing service history rows
                const serviceContainer = document.getElementById('service-rows-splicers');
                if (serviceContainer) {
                    serviceContainer.innerHTML = '';
                    (asset.services || []).forEach(s => {
                        const div = document.createElement('div');
                        div.className = 'flex gap-3 items-center service-row bg-white border rounded-lg p-3';
                        div.innerHTML =
                            `<input type="hidden" name="service_id[]" value="${s.id}">` +
                            `<div class="flex-1">` +
                                `<input type="text" name="service_date[]" placeholder="dd/mm/yyyy" onfocus="this.type='date'" onblur="if(!this.value) this.type='text'" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="${s.service_date || ''}">` +
                            `</div>` +
                            `<div class="flex-1 min-w-[200px]">` +
                                `<input type="text" name="service_desc[]" placeholder="Keterangan servis" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="${s.description || ''}">` +
                            `</div>` +
                            `<div style="width:140px;">` +
                                `<input type="number" name="service_cost[]" placeholder="Biaya" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="${s.cost || ''}">` +
                            `</div>` +
                            `<div style="width:160px;">` +
                                `<input type="text" name="service_vendor[]" placeholder="Vendor" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" value="${s.vendor || ''}">` +
                            `</div>` +
                            `<div class="flex items-center gap-2">` +
                                (s.file_url ? `<a href="${s.file_url}" target="_blank" class="text-blue-600 hover:underline text-sm">Lihat</a>` : '') +
                                `<label class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-md border border-gray-300 cursor-pointer text-sm">` +
                                    `Ubah File` +
                                    `<input type="file" name="service_file[]" class="hidden" onchange="updateFileName(this)">` +
                                `</label>` +
                                `<span class="text-sm text-gray-500 file-name">${s.file_path ? s.file_path.split('/').pop() : 'No file'}</span>` +
                                `<input type="hidden" name="existing_service_file[]" value="${s.file_path || ''}">` +
                                `<button type="button" class="text-red-600 hover:text-red-800 remove-service" onclick="removeServiceRow(this)">✕</button>` +
                            `</div>`;
                        serviceContainer.appendChild(div);
                    });
                }

                // Update titles/buttons
                document.querySelector('#asset-modal-splicers h2').textContent = 'Edit Asset';
                document.querySelector('#asset-form-splicers button[type="submit"]').textContent = 'Update';

                // Show modal
                showModalSplicers(true);
            })
            .catch(err => {
                console.error('Error:', err);
                alert('Terjadi kesalahan saat mengambil data asset');
            });
    }

    window.deleteAsset = function(id) {
        if (!confirm('Anda yakin ingin menghapus asset ini? Tindakan ini tidak dapat dibatalkan.')) {
            return;
        }

        const url = `/assets/${id}`;
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Asset berhasil dihapus.'); // Simple alert for now
                // Find and remove the row from the table
                const row = document.querySelector(`button[onclick="deleteAsset(${id})"]`).closest('tr');
                if (row) {
                    row.remove();
                }
            } else {
                alert(data.message || 'Gagal menghapus asset.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus asset.');
        });
    };

    // Modal functionality for add asset
    const modal = document.getElementById('asset-modal-splicers');
    const assetForm = document.getElementById('asset-form-splicers');
    const closeBtns = document.querySelectorAll('[data-close-modal-splicers]');

    function showModal(isEdit = false) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        if (!isEdit) {
            // Reset form for new asset
            assetForm.reset();

            // Reset form action to create route
            assetForm.action = "{{ route('assets.store') }}";

            // Remove method-spoofing field if exists
            const methodField = document.querySelector('input[name="_method"]');
            if (methodField) methodField.remove();

            // Clear service rows except first
            const serviceContainer = document.getElementById('service-rows-splicers');
            const serviceRows = serviceContainer.querySelectorAll('.service-row');
            for (let i = 1; i < serviceRows.length; i++) {
                serviceRows[i].remove();
            }

            // Reset first service row
            const firstRow = serviceContainer.querySelector('.service-row');
            if (firstRow) {
                firstRow.querySelector('[name="service_id[]"]').value = '';
                firstRow.querySelector('[name="service_date[]"]').value = '';
                firstRow.querySelector('[name="service_desc[]"]').value = '';
                firstRow.querySelector('[name="service_cost[]"]').value = '';
                firstRow.querySelector('[name="service_vendor[]"]').value = '';
                firstRow.querySelector('.file-name').textContent = 'No file';
            }

            // Reset modal title and button text
            document.querySelector('#asset-modal-splicers h2').textContent = 'Tambah Asset Baru';
            document.querySelector('#asset-form-splicers button[type="submit"]').textContent = 'Simpan';
        }
    }

    function hideModal() {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        assetForm.reset();
    }

    // Expose for external buttons and trigger custom-input toggles
    window.showModalSplicers = function(isEdit = false) {
        showModal(isEdit);
        // trigger custom toggle for all custom-select fields
        ['tipe','jenis_aset','merk','pic','project','lokasi'].forEach(name => {
            const sel = assetForm.querySelector(`[name="${name}"]`);
            if (sel) sel.dispatchEvent(new Event('change'));
        });
    };
    window.hideModalSplicers = hideModal;

    closeBtns.forEach(btn => btn.addEventListener('click', e => {
        e.preventDefault();
        hideModal();
    }));

    // Click outside to close
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal();
        }
    });

    // Submit form via AJAX
    assetForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(assetForm);
        const submitBtn = assetForm.querySelector('button[type="submit"]');

        submitBtn.disabled = true;
        submitBtn.textContent = 'Menyimpan...';

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch(assetForm.action, {
                method: 'POST',
                credentials: 'same-origin',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': token
                }
            });

            const json = await res.json().catch(() => null);

            if (res.ok && json && json.success) {
                hideModal();
                alert(json.message || 'Asset berhasil ditambahkan');
                location.reload();
            } else if (res.ok) {
                hideModal();
                location.reload();
            } else {
                alert((json && json.message) ? json.message : 'Gagal menambahkan asset');
            }
        } catch (err) {
            console.error(err);
            alert('Error: ' + err.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Simpan';
        }
    });

    // Toggle custom inputs within the splicers form when selecting "+ Tambah Baru"
    const splicersForm = document.getElementById('asset-form-splicers');

    // Global function for handling custom fields
    window.handleCustomField = function(select, customFieldId) {
        const customInput = document.getElementById(customFieldId);
        if (!customInput) return;

        if (select.value === '__add__') {
            customInput.classList.remove('hidden');
            customInput.required = true;
            customInput.focus();
        } else {
            customInput.classList.add('hidden');
            customInput.required = false;
            customInput.value = '';
        }
    };

    // Function to update file name display
    window.updateFileName = function(input) {
        const fileNameSpan = input.parentNode.nextElementSibling;
        if (fileNameSpan) {
            fileNameSpan.textContent = input.files.length ? input.files[0].name : 'No file';
        }
    };
    // Helper for splicers foto_aset file input
    window.updateFileNameSplicers = function(input) {
        const span = document.getElementById('foto-aset-name-splicers');
        if (span) {
            span.textContent = input.files.length ? input.files[0].name : 'No file';
        }
    };

    // Function to remove service row
    window.removeServiceRow = function(button) {
        const row = button.closest('.service-row');
        const container = document.getElementById('service-rows-splicers');
        const rows = container.querySelectorAll('.service-row');

        if (rows.length > 1) {
            row.remove();
        } else {
            // Reset the last remaining row
            row.querySelector('[name="service_id[]"]').value = '';
            row.querySelector('[name="service_date[]"]').value = '';
            row.querySelector('[name="service_desc[]"]').value = '';
            row.querySelector('[name="service_cost[]"]').value = '';
            row.querySelector('[name="service_vendor[]"]').value = '';
            row.querySelector('.file-name').textContent = 'No file';
        }
    };

    // Function to add service row
    window.addServiceRow = function(serviceData = null) {
        const container = document.getElementById('service-rows-splicers');
        const firstRow = container.querySelector('.service-row');
        if (!firstRow) return;

        const newRow = firstRow.cloneNode(true);

        // Clear values in new row
        newRow.querySelector('[name="service_id[]"]').value = serviceData?.id || '';
        newRow.querySelector('[name="service_date[]"]').value = serviceData?.service_date || '';
        newRow.querySelector('[name="service_desc[]"]').value = serviceData?.description || '';
        newRow.querySelector('[name="service_cost[]"]').value = serviceData?.cost || '';
        newRow.querySelector('[name="service_vendor[]"]').value = serviceData?.vendor || '';
        newRow.querySelector('.file-name').textContent = 'No file';

        container.appendChild(newRow);
    };

    // Setup event listeners
    function setupCustomToggleSplicers(name) {
        if (!splicersForm) return;
        const select = splicersForm.querySelector(`[name="${name}"]`);
        const custom = splicersForm.querySelector(`[name="${name}_custom"]`);
        if (!select || !custom) return;

        function sync() {
            const show = select.value === '__add__';
            custom.classList.toggle('hidden', !show);
            custom.required = show;
            if (!show) custom.value = '';
        }
        select.addEventListener('change', sync);
        sync();
    }

    // Add service button event listener
    document.getElementById('add-service-btn').addEventListener('click', function() {
        addServiceRow();
    });

    ['tipe','jenis_aset','merk','pic','project','lokasi'].forEach(setupCustomToggleSplicers);
});
</script>
@endsection
