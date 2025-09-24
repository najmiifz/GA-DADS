@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-red-600 text-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium">Total Kendaraan</p>
                        <p class="text-2xl font-bold">{{ $vehicles->count() }}</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg">
                        <i class="fas fa-car text-red-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-red-600 text-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium">Total Nilai</p>
                        <p class="text-2xl font-bold">Rp {{ number_format($vehicles->sum('harga_beli'), 0, ',', '.') }}</p>
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
                        <p class="text-2xl font-bold">{{ $vehicles->where('pic', 'Available')->count() }}</p>
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
                        <p class="text-2xl font-bold">{{ $vehicles->where('pic', '<>', 'Available')->count() }}</p>
                    </div>
                    <div class="p-3 bg-white rounded-lg">
                        <i class="fas fa-exclamation-circle text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>
        <!-- Charts (moved to top) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-4 rounded shadow">
                <h3 class="text-sm font-medium mb-2">Status Pajak Kendaraan</h3>
                <div style="height:200px; position:relative;"><canvas id="taxChart"></canvas></div>
            </div>
            <div class="bg-white p-4 rounded shadow">
                <h3 class="text-sm font-medium mb-2">Biaya Servis per Kendaraan</h3>
                <div style="height:200px; position:relative;"><canvas id="serviceChart"></canvas></div>
            </div>
        </div>

                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4 sm:mb-0">Data Tabel Kendaraan</h2>
            <div class="flex gap-3">
                <form id="export-vehicles-form" action="{{ route('assets.export.vehicles') }}" method="GET" class="m-0">
                    <input type="hidden" name="pic" value="{{ request('pic') }}">
                    <input type="hidden" name="project" value="{{ request('project') }}">
                    <input type="hidden" name="lokasi" value="{{ request('lokasi') }}">
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-full shadow-md hover:bg-green-700 transition-colors" title="Ekspor CSV">
                        <i class="fas fa-file-excel mr-3"></i>
                        <span class="font-medium">Ekspor CSV</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Filter bar -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('assets.vehicles') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
            <div>
                <label class="text-sm font-medium text-gray-600">Filter PIC</label>
                    <select name="pic" class="w-full mt-1 px-3 py-2 border rounded-md" onchange="this.form.submit()">
                    <option value="">Semua PIC</option>
                    @if($vehicles->pluck('pic')->contains('Available'))
                        <option value="Available" {{ request('pic') == 'Available' ? 'selected' : '' }} class="bg-green-100 text-green-800 font-semibold">Available</option>
                    @endif
                    @php
                        $excluded = ['available', 'rusak', 'hilang'];
                        $userNames = $usersForPic->map(function($u){ return $u->name; })->toArray();
                    @endphp
                    @foreach($vehicles->pluck('pic')->unique()->filter()->values() as $pic)
                        @if(strtolower(trim($pic)) === 'available' || strtolower(trim($pic)) === 'rusak' || strtolower(trim($pic)) === 'hilang')
                            @continue
                        @endif
                        @if(!in_array($pic, $userNames))
                            {{-- If pic is not a known user (could be legacy string), still display it unless it's 'super-admin' --}}
                            @if(strtolower(trim($pic)) === 'super-admin')
                                @continue
                            @endif
                        @endif
                        <option value="{{ $pic }}" {{ request('pic') == $pic ? 'selected' : '' }}>{{ $pic }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-sm font-medium text-gray-600">Filter Jenis Asset</label>
                <select name="jenis_aset" class="w-full mt-1 px-3 py-2 border rounded-md" onchange="this.form.submit()">
                    <option value="">Semua Jenis</option>
                    @foreach(
                        collect($vehicles->pluck('jenis_aset')->unique())->filter()->values() as $ja)
                        <option value="{{ $ja }}" {{ request('jenis_aset') == $ja ? 'selected' : '' }}>{{ $ja }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Filter Project</label>
                <select name="project" class="w-full mt-1 px-3 py-2 border rounded-md" onchange="this.form.submit()">
                    <option value="">Semua Project</option>
                    @foreach($vehicles->pluck('project')->unique()->filter()->values() as $p)
                        <option value="{{ $p }}" {{ request('project') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Filter Lokasi</label>
                <select name="lokasi" class="w-full mt-1 px-3 py-2 border rounded-md" onchange="this.form.submit()">
                    <option value="">Semua Lokasi</option>
                    @foreach($vehicles->pluck('lokasi')->unique()->filter()->values() as $l)
                        <option value="{{ $l }}" {{ request('lokasi') == $l ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-600">Urutkan</label>
                <select name="sort" class="w-full mt-1 px-3 py-2 border rounded-md" onchange="this.form.submit()">
                    <option value="">Terbaru</option>
                    <option value="pajak_terdekat" {{ request('sort') == 'pajak_terdekat' ? 'selected' : '' }}>Jatuh Tempo Pajak Terdekat</option>
                </select>
            </div>

            <div class="flex gap-3 justify-end md:col-span-4">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md">Filter</button>
                <a href="{{ route('assets.vehicles') }}" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-semibold mb-4">Data Tabel Kendaraan</h2>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis Aset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PIC</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Beli</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Beli</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nomor Aset</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga Sewa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pajak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pajak</th>
                        <!-- Total Servis column removed per request -->
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Reimburse</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Pengajuan Service</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($vehicles as $v)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-900">{{ $v->jenis_aset }}</td>
                        <td class="px-6 py-6 whitespace-nowrap">
                            @if($v->pic === 'Available')
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                            @elseif($v->pic === 'Rusak')
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Rusak</span>
                            @elseif($v->pic === 'Hilang')
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Hilang</span>
                            @else
                                <div class="text-sm text-gray-900">{{ $v->user->name ?? $v->pic }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-900">{{ $v->merk }}</td>
                        <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-900">{{ $v->tanggal_beli ? \Carbon\Carbon::parse($v->tanggal_beli)->format('d M Y') : 'N/A' }}</td>
                        <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format(floatval($v->harga_beli), 0, ',', '.') }}</td>
                        <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-900">{{ $v->serial_number }}</td>
                        <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-900">{{ $v->project }}</td>
                        <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-900">{{ $v->lokasi }}</td>
                        <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format(floatval($v->harga_sewa), 0, ',', '.') }}</td>
                        <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-900">{{ $v->tanggal_pajak ? \Carbon\Carbon::parse($v->tanggal_pajak)->format('d M Y') : 'N/A' }}</td>
                        <td class="px-6 py-6 whitespace-nowrap text-sm">
                            @if(strtolower(trim($v->status_pajak ?? '')) === 'lunas')
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Lunas</span>
                            @else
                                <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Belum Lunas</span>
                            @endif
                        </td>
                        <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format(floatval($v->reimburse_cost_sum ?? $v->reimburseRequests()->sum('biaya') ?? 0),0,',','.') }}
                        </td>
                        <td class="px-6 py-6 whitespace-nowrap text-sm text-gray-900">
                            Rp {{ number_format(floatval($v->service_requests_total_cost ?? 0),0,',','.') }}
                        </td>
                        <td class="px-6 py-6 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex items-center justify-end gap-3">
                                <button onclick="viewAsset({{ $v->id }})" class="text-blue-600 hover:text-blue-900"><i class="fas fa-eye"></i></button>
                                @can('kelola-aset')
                                <a href="{{ route('assets.edit', $v->id) }}" class="text-yellow-600 hover:text-yellow-900"><i class="fas fa-edit"></i></a>
                                <button onclick="deleteAsset({{ $v->id }})" class="text-red-600 hover:text-red-900"><i class="fas fa-trash"></i></button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex justify-between items-center mt-6">
                <p class="text-sm text-gray-600">
                    Menampilkan {{ $vehicles->firstItem() ?? 0 }}-{{ $vehicles->lastItem() ?? 0 }} dari {{ $vehicles->total() ?? 0 }}
                </p>
                <div class="flex space-x-4">
                    <a href="{{ $vehicles->previousPageUrl() }}" class="{{ !$vehicles->onFirstPage() ? 'px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50' : 'px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-400 cursor-not-allowed' }}">
                        Prev
                    </a>
                    <a href="{{ $vehicles->nextPageUrl() }}" class="{{ $vehicles->hasMorePages() ? 'px-4 py-2 bg-white border border-gray-300 rounded-md hover:bg-gray-50' : 'px-4 py-2 bg-gray-100 border border-gray-300 rounded-md text-gray-400 cursor-not-allowed' }}">
                        Next
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for edit/create on vehicles page -->
<div id="asset-modal-vehicles" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-lg max-w-7xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h2 id="modal-title" class="text-xl font-bold text-gray-900">Tambah Asset</h2>
                    <button data-close-modal-vehicles class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form id="asset-form-vehicles" method="POST" action="{{ route('assets.store') }}" enctype="multipart/form-data">
                    @csrf
                    <!-- hidden year field synced with date -->
                    <input type="hidden" name="tahun_beli" id="form-tahun-beli-vehicles" value="">
                    <input type="hidden" name="_method" value="POST">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                            <select name="tipe" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                <option value="">Pilih Tipe</option>
                                <option value="__add__">+ Tambah Baru</option>
                                <option value="Kendaraan">Kendaraan</option>
                                <option value="Elektronik">Elektronik</option>
                            </select>
                            <input type="text" name="tipe_custom" placeholder="Masukkan tipe baru..." class="hidden mt-2 w-full px-3 py-2 border rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Asset</label>
                            <select name="jenis_aset" class="w-full px-3 py-2 border rounded-md" required>
                                <option value="">-- Pilih Jenis --</option>
                                <option value="__add__">+ Tambah Baru</option>
                                @foreach($vehicles->pluck('jenis_aset')->unique()->filter()->values() as $jenis)
                                    <option value="{{ $jenis }}">{{ $jenis }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="jenis_aset_custom" placeholder="Masukkan jenis baru..." class="hidden mt-2 w-full px-3 py-2 border rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">PIC</label>
                            <select name="pic" class="w-full px-3 py-2 border rounded-md">
                                <option value="">-- Pilih PIC --</option>
                                <option value="__add__">+ Tambah Baru</option>
                                <option value="Available" class="bg-green-100 text-green-800 font-semibold">Available</option>
                                @foreach($vehicles->pluck('pic')->unique()->filter()->values() as $pic)
                                    @if(strtolower(trim($pic)) === 'available' || strtolower(trim($pic)) === 'super-admin') @continue @endif
                                    <option value="{{ $pic }}">{{ $pic }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="pic_custom" placeholder="Masukkan PIC baru..." class="hidden mt-2 w-full px-3 py-2 border rounded-md">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Merk</label>
                            <input name="merk" class="w-full px-3 py-2 border rounded-md" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Aset</label>
                            <input name="serial_number" class="w-full px-3 py-2 border rounded-md" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Project</label>
                            <select name="project" class="w-full px-3 py-2 border rounded-md" required
                                onchange="if(this.value==='__add__'){ this.nextElementSibling.classList.remove('hidden'); } else { this.nextElementSibling.classList.add('hidden'); }">
                                <option value="">-- Pilih Project --</option>
                                <option value="__add__">+ Tambah Baru</option>
                                @foreach($vehicles->pluck('project')->unique()->filter()->values() as $p)
                                    <option value="{{ $p }}">{{ $p }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="project_custom" placeholder="Masukkan project baru..." class="hidden mt-2 w-full px-3 py-2 border rounded-md">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                            <select name="lokasi" class="w-full px-3 py-2 border rounded-md" required
                                onchange="if(this.value==='__add__'){ this.nextElementSibling.classList.remove('hidden'); } else { this.nextElementSibling.classList.add('hidden'); }">
                                <option value="">-- Pilih Lokasi --</option>
                                <option value="__add__">+ Tambah Baru</option>
                                @foreach($vehicles->pluck('lokasi')->unique()->values() as $lokasi)
                                    <option value="{{ $lokasi }}">{{ $lokasi }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="lokasi_custom" placeholder="Masukkan lokasi baru..." class="hidden mt-2 w-full px-3 py-2 border rounded-md">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Beli</label>
                            <input name="tanggal_beli" type="date" class="w-full px-3 py-2 border rounded-md" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Beli</label>
                            <input name="harga_beli" type="number" class="w-full px-3 py-2 border rounded-md" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Harga Sewa</label>
                            <input name="harga_sewa" type="number" class="w-full px-3 py-2 border rounded-md" />
                        </div>

                        <!-- Foto Aset (opsional) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Aset (opsional)</label>
                            <input type="file" name="foto_aset" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>

                        <!-- Keterangan Aset (opsional) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Aset (opsional)</label>
                            <input type="text" name="keterangan" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Masukkan keterangan aset...">
                        </div>

                        <!-- Vehicle-specific extra: span full width of grid -->
                        <div id="modal-pajak-vehicles" class="hidden sm:col-span-3 w-full">
                            <h3 class="text-sm font-medium text-gray-800 mb-3">Data Pajak</h3>
                            <input type="hidden" name="pajak_id" value="">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">Tanggal Pajak</label>
                                    <input type="date" name="tanggal_pajak" class="w-full px-4 py-3 border border-gray-300 rounded-lg" />
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">Jumlah Pajak</label>
                                    <input type="number" name="jumlah_pajak" class="w-full px-4 py-3 border border-gray-300 rounded-lg" placeholder="0" />
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 mb-1">Status Pajak</label>
                                    <select name="status_pajak" class="w-full px-4 py-3 border border-gray-300 rounded-lg">
                                        <option value="">-- Pilih Status --</option>
                                        <option value="Lunas">Lunas</option>
                                        <option value="Belum Lunas">Belum Lunas</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Foto Kendaraan (Kendaraan saja) -->
                            <div class="mt-6">
                                <h3 class="text-sm font-medium text-gray-800 mb-3">Foto Kendaraan</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-2">Foto STNK</label>
                                        <div id="foto-stnk-preview" class="mb-2 hidden">
                                            <img class="w-32 h-24 object-cover border rounded">
                                            <p class="text-sm text-gray-500 mt-1">Foto saat ini</p>
                                        </div>
                                        <input type="file" name="foto_stnk" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG, maksimal 2MB. Kosongkan jika tidak ingin mengubah.</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm text-gray-700 mb-2">Foto Kendaraan</label>
                                        <div id="foto-kendaraan-preview" class="mb-2 hidden">
                                            <img class="w-32 h-24 object-cover border rounded">
                                            <p class="text-sm text-gray-500 mt-1">Foto saat ini</p>
                                        </div>
                                        <input type="file" name="foto_kendaraan" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                        <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG, maksimal 2MB. Kosongkan jika tidak ingin mengubah.</p>
                                    </div>
                                </div>
                                <!-- Foto Aset (opsional) -->
                                <div class="mt-6">
                                    <h3 class="text-lg font-semibold mb-4">Foto Aset (opsional)</h3>
                                    <div class="flex items-center gap-4">
                                        <div id="foto-aset-preview-vehicles" class="hidden">
                                            <img src="" alt="Foto Aset" class="w-32 h-24 object-cover border rounded" />
                                            <p class="text-sm text-gray-500 mt-1">Foto saat ini</p>
                                        </div>
                                        <input type="file" name="foto_aset" id="foto_aset" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md" />
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-sm font-medium text-gray-800">Riwayat Servis</h3>
                                    <button id="add-service-modal-vehicles" type="button" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg font-medium">Tambah Riwayat</button>
                                </div>

                                <div id="modal-servis-vehicles" class="">
                                    <div id="service-rows-vehicles" class="space-y-3">
                                        <!-- service rows injected here -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center gap-4 mt-6 pt-6 border-t">
                        <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-lg text-base font-semibold">Simpan</button>
                        <button type="button" data-close-modal-vehicles class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg text-base font-medium">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const taxData = @json($taxData ?? []);
    const serviceTotals = @json($serviceTotals ?? []);
    const reimburseTotals = @json($reimburseTotals ?? []);
  const taxCtx = document.getElementById('taxChart');
  if (taxCtx) new Chart(taxCtx, { type: 'doughnut', data: { labels: Object.keys(taxData), datasets: [{ data: Object.values(taxData), backgroundColor: ['#10B981','#EF4444','#F59E0B'] }] }, options: { responsive: true, maintainAspectRatio: false } });
  const svcCtx = document.getElementById('serviceChart');
  if (svcCtx) new Chart(svcCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(serviceTotals),
            datasets: [
                { label: 'Biaya Servis', data: Object.values(serviceTotals), backgroundColor: '#3B82F6' },
                { label: 'Biaya Reimburse', data: Object.values(reimburseTotals), backgroundColor: '#10B981' }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
    });
});
</script>
@endpush
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('asset-modal-vehicles');
    const assetForm = document.getElementById('asset-form-vehicles');
    const closeBtns = document.querySelectorAll('[data-close-modal-vehicles]');

    function showModal() { modal.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    function hideModal() { modal.classList.add('hidden'); document.body.style.overflow = 'auto'; assetForm.reset(); }
    // expose for external buttons
    // Open modal in edit mode: do not reset form or change title here
    window.showModalVehicles = function() {
        document.getElementById('modal-title').textContent = 'Edit Asset';
        updateVehicleSections();
        showModal();
        // trigger custom toggle for project and lokasi in edit mode
        ['project','lokasi'].forEach(n => {
            const sel = assetForm.querySelector(`[name="${n}"]`);
            if (sel) sel.dispatchEvent(new Event('change'));
        });
    };
    // Open modal in add mode: reset form and set title
    window.openAddModalVehicles = function() {
        assetForm.reset();
        assetForm.action = "{{ route('assets.store') }}";
        assetForm.querySelector('[name="_method"]').value = 'POST';
        // Automatically set tipe to Kendaraan since this is vehicles page
        assetForm.querySelector('[name="tipe"]').value = 'Kendaraan';
        document.getElementById('modal-title').textContent = 'Tambah Asset';
        // Hide foto previews for new asset
        document.getElementById('foto-stnk-preview')?.classList.add('hidden');
        document.getElementById('foto-kendaraan-preview')?.classList.add('hidden');
        updateVehicleSections();
        showModal();
        // trigger custom toggle for project and lokasi
        ['project','lokasi'].forEach(n => {
            const sel = assetForm.querySelector(`[name="${n}"]`);
            if (sel) {
                console.log(`Trigger toggle for ${n}`);
                sel.dispatchEvent(new Event('change'));
            }
        });
    };
    window.hideModalVehicles = hideModal;

    closeBtns.forEach(b => b.addEventListener('click', e => { e.preventDefault(); hideModal(); }));

    // sync hidden tahun_beli from tanggal_beli
    const tahunField = document.getElementById('form-tahun-beli-vehicles');
    const tanggalInput = assetForm.querySelector('[name="tanggal_beli"]');
    function syncTahun() {
        if (tanggalInput && tahunField) {
            const val = tanggalInput.value;
            tahunField.value = val ? new Date(val).getFullYear() : '';
        }
    }
    tanggalInput?.addEventListener('change', syncTahun);
    // initial sync in case of prefilled value
    syncTahun();
    // toggle pajak & servis for Kendaraan
    const tipeSelect = assetForm.querySelector('[name="tipe"]');
    const pajakSection = document.getElementById('modal-pajak-vehicles');
    const servisSection = document.getElementById('modal-servis-vehicles');
    function updateVehicleSections() {
        const isVehicle = tipeSelect && tipeSelect.value === 'Kendaraan';
        pajakSection?.classList.toggle('hidden', !isVehicle);
        servisSection?.classList.toggle('hidden', !isVehicle);
    }
    tipeSelect?.addEventListener('change', updateVehicleSections);
    // Expose updateVehicleSections globally for editAsset
    window.updateVehicleSections = updateVehicleSections;

    // Toggle custom inputs when "__add__" selected
    function setupCustomToggle(selectName) {
        const select = assetForm.querySelector(`[name="${selectName}"]`);
        const custom = assetForm.querySelector(`[name="${selectName}_custom"]`);
        if (!select || !custom) return;
        function sync() {
            const show = select.value === '__add__';
            custom.classList.toggle('hidden', !show);
            if (!show) custom.value = '';
        }
        select.addEventListener('change', sync);
        // initial
        sync();
    }
    ['tipe','jenis_aset','pic','project','lokasi'].forEach(setupCustomToggle);

    // service rows handlers
    document.addEventListener('click', function(e) {
    if (e.target && e.target.id === 'add-service-modal-vehicles') {
            const container = document.getElementById('service-rows-vehicles');
            const div = document.createElement('div');
            div.className = 'flex gap-3 items-center service-row bg-gray-50 p-3 rounded-lg';
            div.innerHTML =
        '<input type="hidden" name="service_id[]" value="">' +
        '<input type="hidden" name="existing_service_file[]" value="">' +
                '<input type="date" name="service_date[]" class="px-4 py-2 border border-gray-300 rounded-lg w-40">' +
                '<input type="text" name="service_desc[]" placeholder="Keterangan servis" class="px-4 py-2 border border-gray-300 rounded-lg flex-1">' +
                '<input type="number" name="service_cost[]" placeholder="Biaya" class="px-4 py-2 border border-gray-300 rounded-lg w-32">' +
                '<input type="text" name="service_vendor[]" placeholder="Vendor" class="px-4 py-2 border border-gray-300 rounded-lg w-48">' +
                '<div class="flex items-center gap-2">' +
                    '<input type="file" accept=".pdf,.jpg,.jpeg,.png,.xls,.xlsx,.doc,.docx" name="service_file[]" class="px-2 py-1">' +
                    '<button type="button" class="text-red-600 remove-service font-medium">✕</button>' +
                '</div>';
            container.appendChild(div);
        }

        if (e.target && e.target.classList && e.target.classList.contains('remove-service')) {
            const row = e.target.closest('.service-row'); row?.remove();
        }
    });

    // editAsset for vehicles (fetch json and populate modal)
    window.editAsset = async function(id) {
        console.log('editAsset called for id:', id);
        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch(`/assets/${id}/json`, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token } });
            if (!res.ok) throw new Error('Gagal mengambil data');
            console.log('Fetch response ok, parsing JSON');
            const payload = await res.json();
            console.log('Asset payload received:', payload);
            const a = payload.asset;

            // Set the form action to the update route
            assetForm.action = `/assets/${id}`;
            assetForm.querySelector('[name="_method"]').value = 'PUT';

            // match only fields that exist in the new form
            ['tipe','jenis_aset','pic','lokasi','project','tanggal_beli'].forEach(k => {
                const el = assetForm.querySelector('[name="'+k+'"]'); if (!el) return; el.value = a[k] ?? '';
            });
            // ensure custom inputs are hidden when not __add__
            ['tipe','jenis_aset','pic','project','lokasi'].forEach(k => {
                const sel = assetForm.querySelector(`[name="${k}"]`);
                const custom = assetForm.querySelector(`[name="${k}_custom"]`);
                if (sel && custom && sel.value !== '__add__') custom.classList.add('hidden');
            });
            // Populate other fields (excluding file inputs)
            ['merk','serial_number','harga_beli','harga_sewa','keterangan'].forEach(k => {
                const el = assetForm.querySelector('[name="'+k+'"]'); if (!el) return; el.value = a[k] ?? '';
            });
            // File inputs cannot be pre-filled; clear and show existing file name
            const fotoInput = assetForm.querySelector('[name="foto_aset"]');
            if (fotoInput) {
                fotoInput.value = ''; // only empty string allowed
                // display existing file name
                let existingName = a.foto_aset_url ? a.foto_aset_url.split('/').pop() : 'No file';
                let nameSpan = fotoInput.parentNode.querySelector('.file-name');
                if (!nameSpan) {
                    nameSpan = document.createElement('span');
                    nameSpan.className = 'text-sm text-gray-500 file-name';
                    fotoInput.parentNode.appendChild(nameSpan);
                }
                nameSpan.textContent = existingName;
            }
            console.log('Form fields populated');
            // Set modal title to Edit Asset
            document.getElementById('modal-title').textContent = 'Edit Asset';

            // Populate existing service history rows
            const serviceContainer = document.getElementById('service-rows-vehicles');
            if (serviceContainer) {
                serviceContainer.innerHTML = '';
                    (a.services || []).forEach(s => {
                    const div = document.createElement('div');
                    div.className = 'flex gap-3 items-center service-row bg-gray-50 p-3 rounded-lg';
                    div.innerHTML =
                        `<input type="hidden" name="service_id[]" value="${s.id}">` +
                        `<input type="hidden" name="existing_service_file[]" value="${s.file_path || ''}">` +
                        `<input type="date" name="service_date[]" class="px-4 py-2 border border-gray-300 rounded-lg w-40" value="${s.service_date}">` +
                        `<input type="text" name="service_desc[]" placeholder="Keterangan servis" class="px-4 py-2 border border-gray-300 rounded-lg flex-1" value="${s.description}">` +
                        `<input type="number" name="service_cost[]" placeholder="Biaya" class="px-4 py-2 border border-gray-300 rounded-lg w-32" value="${s.cost}">` +
                        `<input type="text" name="service_vendor[]" placeholder="Vendor" class="px-4 py-2 border border-gray-300 rounded-lg w-48" value="${s.vendor}">` +
                        `<div class="flex items-center gap-2">` +
                            `${s.file_path ? `<span class="text-sm text-gray-600">${s.file_path.split('/').pop()}</span>` : '<span class="text-sm text-gray-500">No file</span>'}` +
                            `<input type="file" accept=".pdf,.jpg,.jpeg,.png,.xls,.xlsx,.doc,.docx" name="service_file[]" class="px-2 py-1">` +
                            `<button type="button" class="text-red-600 remove-service font-medium">✕</button>` +
                        `</div>`;
                    serviceContainer.appendChild(div);
                });
            }

            // Populate foto aset preview
            if (a.foto_aset_url) {
                const asetPreview = document.getElementById('foto-aset-preview-vehicles');
                if (asetPreview) {
                    asetPreview.classList.remove('hidden');
                    asetPreview.querySelector('img').src = a.foto_aset_url;
                }
            }
            // Populate pajak fields
            const pajak = a.pajak_history && a.pajak_history.length > 0 ? a.pajak_history[0] : {};
            assetForm.querySelector('[name="pajak_id"]').value = pajak.id || '';
            assetForm.querySelector('[name="tanggal_pajak"]').value = pajak.tanggal_pajak || a.tanggal_pajak || '';
            assetForm.querySelector('[name="jumlah_pajak"]').value = pajak.jumlah_pajak || a.jumlah_pajak || '';
            assetForm.querySelector('[name="status_pajak"]').value = pajak.status_pajak || a.status_pajak || '';

            // Populate foto kendaraan previews if they exist
            if (a.foto_stnk) {
                const stnkPreview = document.getElementById('foto-stnk-preview');
                if (stnkPreview) {
                    stnkPreview.classList.remove('hidden');
                    stnkPreview.querySelector('img').src = a.foto_stnk_url || `/storage/${a.foto_stnk}`;
                }
            }
            if (a.foto_kendaraan) {
                const kendaraanPreview = document.getElementById('foto-kendaraan-preview');
                if (kendaraanPreview) {
                    kendaraanPreview.classList.remove('hidden');
                    kendaraanPreview.querySelector('img').src = a.foto_kendaraan_url || `/storage/${a.foto_kendaraan}`;
                }
            }

            // Update pajak & servis sections visibility after populating tipe
            if (typeof updateVehicleSections === 'function') {
                updateVehicleSections();
            }
            // trigger custom toggles after edit
            ['project','lokasi'].forEach(n => {
                const sel = assetForm.querySelector(`[name="${n}"]`);
                if (sel) sel.dispatchEvent(new Event('change'));
            });
            // Open the modal for editing
            if (typeof showModalVehicles === 'function') {
                showModalVehicles();
            }
        } catch (err) {
            console.error('Error in editAsset:', err);
            alert(err.message);
        }
    }

    // submit via AJAX
    assetForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const fd = new FormData(assetForm);
        const btn = assetForm.querySelector('button[type="submit"]'); btn.disabled = true; btn.textContent = 'Menyimpan...';
        try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const res = await fetch(assetForm.action, { method: 'POST', credentials: 'same-origin', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': token } });
            const json = await res.json().catch(()=>null);
            if (res.ok && json && json.success) { hideModal(); alert(json.message||'Sukses'); location.reload(); }
            else if (res.ok) { hideModal(); location.reload(); }
            else alert((json && json.message) ? json.message : 'Gagal');
        } catch (err) { console.error(err); alert('Error: '+err.message); }
        finally { btn.disabled = false; btn.textContent = 'Simpan'; }
    });

    // viewAsset/deleteAsset fallback
    window.viewAsset = function(id){ window.location.href = '/assets/'+id; };
    window.deleteAsset = function(id){ if(confirm('Hapus asset ini?')) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            fetch('/assets/'+id, { method:'DELETE', credentials: 'same-origin', headers:{ 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest' } }).then(()=>location.reload());
        } };
});
</script>
@endsection

    <aside id="sidebar" class="w-64 bg-red-600 shadow-md flex-shrink-0 fixed md:fixed inset-y-0 left-0 z-30 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="p-6 flex items-center justify-center">
            <div class="w-16 h-16 bg-white rounded-lg flex items-center justify-center">
                <img src="{{ asset('images/logo-dads.png') }}" alt="GA-DADS Logo" class="w-14 h-14">
            </div>
            <span class="ml-3 text-3xl font-bold text-white">GA-DADS</span>
        </div>
    <nav class="mt-6 px-4">
        <a href="http://127.0.0.1:8000/dashboard" class="nav-link flex items-center px-4 py-2 mt-2 text-white font-medium rounded-lg">
            <i class="fas fa-tachometer-alt w-5 text-center mr-3"></i>
            Dashboard
        </a>
        <a href="http://127.0.0.1:8000/vehicles" class="nav-link flex items-center px-4 py-2 mt-2 text-white font-medium rounded-lg active">
            <i class="fas fa-car w-5 text-center mr-3"></i>
            Kendaraan
        </a>
            {{-- Splicers link removed --}}
    </nav>
    <div class="absolute bottom-0 left-0 w-full p-4 border-t">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                <span class="text-red-600 text-xs font-bold">A</span>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm font-medium text-white">Admin</p>
                <p class="text-xs text-white capitalize">user</p>
            </div>
            <a href="{{ url('/logout') }}" class="text-white hover:text-gray-300 transition-colors" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </div>
</aside>
