@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow-md p-6" x-data="{
                        tipe: '{{ $asset->tipe }}',
                        jenis_aset: '{{ $asset->jenis_aset }}',
                        user_id: '{{ $asset->user_id }}',
                        project: '{{ $asset->project }}',
                        lokasi: '{{ $asset->lokasi }}',
                        // Service toggle
                        hasService: {{ $asset->services->count() > 0 || strtolower($asset->tipe) === 'kendaraan' || strtolower($asset->tipe) === 'splicer' ? 'true' : 'false' }},
                        isTipeKendaraan() { return this.tipe.toLowerCase() === 'kendaraan'; },
                        isTipeSplicer() { return this.tipe.toLowerCase() === 'splicer'; },
                        shouldShowService() { return this.hasService || this.isTipeKendaraan() || this.isTipeSplicer(); }
                    }"
                    x-init="$watch('tipe', value => {
                        if (value.toLowerCase() === 'kendaraan' || value.toLowerCase() === 'splicer') {
                            this.hasService = true;
                        }
                    })">
        @if(auth()->user()->role === 'admin')
            <h1 class="text-3xl font-bold mb-6">Edit Aset (Admin)</h1>
        @else
            <h1 class="text-3xl font-bold mb-6">Edit Aset - {{ $asset->merk }} {{ $asset->jenis_aset }}</h1>
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-blue-700">
                    <i class="fas fa-info-circle mr-2"></i>
                    Sebagai PIC, Anda hanya dapat mengedit <strong>Keterangan Asset</strong> dan <strong>Data Pajak</strong>
                </p>
            </div>
        @endif

        {{-- Display flash messages --}}
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                    <span class="text-red-800">{{ session('error') }}</span>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    <span class="text-green-800">{{ session('success') }}</span>
                </div>
            </div>
        @endif

            <form
                method="POST"
                action="{{ route('assets.update', $asset->id) }}"
                enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    @if(auth()->user()->role === 'admin')
                    <!-- Admin: Semua Field -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Tipe Asset -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label for="tipe" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-layer-group text-blue-500 mr-2"></i>
                                Tipe Asset
                            </label>
                            <select id="tipe" name="tipe" x-model="tipe" x-ref="tipeSelect" @change="if($event.target.value==='__add__'){ /* show modal */ }" class="form-input w-full">
                                <option value="">-- Pilih Tipe --</option>
                                <option value="__add__">+ Tambah Baru</option>
                                @foreach($tipes as $tipeOption)
                                    @if($tipeOption !== '__add__')
                                        <option value="{{ $tipeOption }}" {{ $asset->tipe == $tipeOption ? 'selected' : '' }}>{{ $tipeOption }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <template x-if="tipe==='__add__'">
                                <input type="text" name="tipe_custom" placeholder="Masukkan tipe baru..." class="mt-2 block w-full px-3 py-2 border rounded-md">
                            </template>
                        </div>
                       <!-- PIC / Status Asset -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label for="pic" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-users text-green-500 mr-2"></i>
                                PIC / Status Asset
                            </label>
                            <select name="pic" id="pic" class="form-input w-full">
                                <option value="Available" style="color:#16a34a;" {{ $asset->pic === 'Available' ? 'selected' : '' }}>🟢 Available</option>
                                <!-- Rusak and Hilang removed from PIC dropdown to avoid selecting status from PIC field -->
                                <optgroup label="👥 PIC Users">
                                @foreach($usersForPic as $user)
                                    <option value="user:{{ $user->id }}" {{ $asset->user_id == $user->id ? 'selected' : '' }}>👤 {{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                                </optgroup>
                            </select>
                            @error('pic')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <!-- Jenis Asset -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label for="jenis_aset" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tags text-green-500 mr-2"></i>
                                Jenis Asset
                            </label>
                            <select id="jenis_aset" name="jenis_aset" x-model="jenis_aset" x-ref="jenisSelect" @change="if($event.target.value==='__add__'){ /* show modal */ }" class="form-input w-full">
                                <option value="">-- Pilih Jenis --</option>
                                <option value="__add__">+ Tambah Baru</option>
                                <!-- Fixed asset types -->
                                <option value="Laptop">Laptop</option>
                                <option value="Handphone">Handphone</option>
                                <option value="Splicer">Splicer</option>
                                <option value="Otdr">Otdr</option>
                                <option value="Ols">Ols</option>
                                <option value="Opm">Opm</option>
                                <option value="Furniture">Furniture</option>
                                @foreach($jenisAsets as $jenisOption)
                                    @if($jenisOption !== '__add__')
                                        <option value="{{ $jenisOption }}" {{ $asset->jenis_aset == $jenisOption ? 'selected' : '' }}>{{ $jenisOption }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <template x-if="jenis_aset==='__add__'">
                                <input type="text" name="jenis_aset_custom" placeholder="Masukkan jenis baru..." class="mt-2 block w-full px-3 py-2 border rounded-md">
                            </template>
                        </div>

                        <!-- Status Aset -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <label for="status" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                                Status Aset
                            </label>
                            <select name="status" id="status" class="form-input w-full">
                                <option value="">Pilih Status</option>
                                <option value="Available" {{ $asset->status=='Available' ? 'selected' : '' }}>Available</option>
                                <option value="Rusak" {{ $asset->status=='Rusak' ? 'selected' : '' }}>Rusak</option>
                                <option value="Hilang" {{ $asset->status=='Hilang' ? 'selected' : '' }}>Hilang</option>
                            </select>
                            @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <!-- Merk -->
                        <div>
                            <label for="merk" class="block text-sm font-medium text-gray-700">Merk</label>
                            <input type="text" name="merk" id="merk" value="{{ $asset->merk }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Innova Zenix">
                        </div>

                        <!-- Nomor SN -->
                        <div>
                            <label for="serial_number" class="block text-sm font-medium text-gray-700">Nomor Aset</label>
                            <input type="text" name="serial_number" id="serial_number" value="{{ $asset->serial_number }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="D 1338 ALF">
                        </div>
                        {{-- Plate Number for Kendaraan --}}
                        @if(strtolower($asset->tipe ?? '') === 'kendaraan')
                        <div class="mt-4">
                            <label for="plate_number" class="block text-sm font-medium text-gray-700">Nomor Plat Kendaraan</label>
                            <input type="text" name="plate_number" id="plate_number" value="{{ $asset->plate_number }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="B 1234 CD">
                            @error('plate_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        @endif

                        <!-- Project -->
                        <div>
                            <label for="project" class="block text-sm font-medium text-gray-700">Project</label>
                            <select name="project" id="project" x-model="project" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="">-- Pilih Project --</option>
                                <option value="__add__">+ Tambah Baru</option>
                                @foreach($projects as $proj)
                                    @if($proj !== '__add__')
                                        <option value="{{ $proj }}" {{ $asset->project == $proj ? 'selected' : '' }}>{{ $proj }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <template x-if="project==='__add__'">
                                <input type="text" name="project_custom" placeholder="Masukkan project baru..." class="mt-2 block w-full px-3 py-2 border rounded-md">
                            </template>
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label for="lokasi" class="block text-sm font-medium text-gray-700">Lokasi</label>
                            <select name="lokasi" id="lokasi" x-model="lokasi" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="">-- Pilih Lokasi --</option>
                                <option value="__add__">+ Tambah Baru</option>
                                @foreach($lokasis as $lok)
                                    @if($lok !== '__add__')
                                        <option value="{{ $lok }}" {{ $asset->lokasi == $lok ? 'selected' : '' }}>{{ $lok }}</option>
                                    @endif
                                @endforeach
                            </select>
                            <template x-if="lokasi==='__add__'">
                                <input type="text" name="lokasi_custom" placeholder="Masukkan lokasi baru..." class="mt-2 block w-full px-3 py-2 border rounded-md">
                            </template>
                        </div>





                        <!-- Tanggal Beli -->
                        <div>
                            <label for="tanggal_beli" class="block text-sm font-medium text-gray-700">Tanggal Beli</label>
                            <input type="date" name="tanggal_beli" id="tanggal_beli" value="{{ old('tanggal_beli', $asset->tanggal_beli ? date('Y-m-d', strtotime($asset->tanggal_beli)) : '') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>

                        <!-- Harga Beli -->
                        <div>
                            <label for="harga_beli" class="block text-sm font-medium text-gray-700">Harga Beli (Rp)</label>
                            <input type="number" name="harga_beli" id="harga_beli" value="{{ $asset->harga_beli }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="0" min="0" step="1000">
                        </div>

                        <!-- Harga Sewa -->
                        <div>
                            <label for="harga_sewa" class="block text-sm font-medium text-gray-700">Harga Sewa (Rp)</label>
                            <input type="number" name="harga_sewa" id="harga_sewa" value="{{ $asset->harga_sewa }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="0" min="0" step="1000">
                        </div>

                        <!-- Foto Aset (opsional) -->
                        <div class="bg-white border-2 border-gray-200 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4 text-gray-800">
                                <i class="fas fa-camera mr-2"></i>Foto Aset (Opsional)
                            </h3>
                            <div>
                                <label for="foto_aset" class="block text-sm font-medium text-gray-700">Pilih Foto Aset</label>
                                <div class="mt-2">
                                    <input type="file" name="foto_aset" id="foto_aset" accept="image/*" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" onchange="document.getElementById('foto-aset-name').textContent = this.files[0]?.name || '{{ basename($asset->foto_aset ?? '') ?: 'No file chosen' }}'">
                                    <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, maksimal 2MB</p>
                                </div>
                                <span id="foto-aset-name" class="text-sm text-gray-500 block mt-2">{{ $asset->foto_aset ? basename($asset->foto_aset) : 'No file chosen' }}</span>
                                @if(isset($asset->foto_aset) && $asset->foto_aset)
                                    <div class="mt-4">
                                        <p class="text-sm font-medium text-gray-700 mb-2">Preview Foto Saat Ini:</p>
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($asset->foto_aset) }}" alt="Foto Aset" class="w-48 h-36 object-cover rounded-lg border">
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Keterangan Aset (opsional) -->
                        <div>
                            <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan Aset (opsional)</label>
                            <input type="text" name="keterangan" id="keterangan" value="{{ old('keterangan', $asset->keterangan ?? $asset->sifat ?? '') }}" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Masukkan keterangan aset...">
                        </div>

                        </div>
                    @else
                    <!-- User/PIC: Field Terbatas -->
                    <div class="space-y-6">
                        <!-- Informasi Asset (Read-only) -->
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4 text-gray-800">Informasi Asset</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Tipe</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-white p-2 border rounded">{{ $asset->tipe }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Jenis Asset</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-white p-2 border rounded">{{ $asset->jenis_aset }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Merk</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-white p-2 border rounded">{{ $asset->merk }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Serial Number</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-white p-2 border rounded">{{ $asset->serial_number ?? 'Tidak ada' }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Project</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-white p-2 border rounded">{{ $asset->project }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Lokasi</label>
                                    <p class="mt-1 text-sm text-gray-900 bg-white p-2 border rounded">{{ $asset->lokasi }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan Asset (Editable) -->
                        <div class="bg-white border-2 border-blue-200 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4 text-blue-800">
                                <i class="fas fa-edit mr-2"></i>Keterangan Asset
                            </h3>
                            <div>
                                <label for="keterangan" class="block text-sm font-medium text-gray-700">Keterangan Asset</label>
                                <textarea name="keterangan" id="keterangan" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Masukkan keterangan asset...">{{ old('keterangan', $asset->keterangan ?? '') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">Anda dapat menambahkan catatan, kondisi, atau informasi penting tentang asset ini</p>
                            </div>
                        </div>

                        <!-- Foto Asset (Editable) -->
                        <div class="bg-white border-2 border-green-200 p-6 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4 text-green-800">
                                <i class="fas fa-camera mr-2"></i>Foto Asset
                            </h3>
                            <div>
                                <label for="foto_aset" class="block text-sm font-medium text-gray-700">Upload Foto Asset</label>
                                <div class="mt-2">
                                    <input type="file" name="foto_aset" id="foto_aset" accept="image/*" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500" onchange="previewImage(this)">
                                    <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, maksimal 2MB</p>
                                </div>

                                <!-- Current Photo Preview -->
                                @if($asset->foto_aset)
                                <div class="mt-4">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Foto Saat Ini:</p>
                                    <img id="current-photo" src="{{ \Illuminate\Support\Facades\Storage::url($asset->foto_aset) }}" alt="Foto Asset" class="w-48 h-36 object-cover rounded-lg border">
                                </div>
                                @endif

                                <!-- New Photo Preview -->
                                <div id="new-photo-preview" class="mt-4 hidden">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Preview Foto Baru:</p>
                                    <img id="preview-image" src="" alt="Preview" class="w-48 h-36 object-cover rounded-lg border">
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

        <!-- Tax Data Section (Vehicle Only) -->
    <div class="vehicle-only mt-6" style="{{ strcasecmp($asset->tipe ?? '', 'Kendaraan') === 0 ? '' : 'display:none;' }}">
            <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-5 py-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-white">Data Pajak Kendaraan</h3>
                                <p class="text-yellow-100 text-xs mt-1">Informasi pajak dan status compliance kendaraan</p>
                            </div>
                        </div>
                        <div class="text-right">
                            @php
                                $taxStatus = 'unknown';
                                $statusIcon = 'fas fa-question-circle';
                                $statusColor = 'text-yellow-100';

                                if($asset->tanggal_pajak) {
                                    $taxDate = \Carbon\Carbon::parse($asset->tanggal_pajak);
                                    $today = \Carbon\Carbon::now();
                                    $daysUntilExpiry = $today->diffInDays($taxDate, false);

                                    if($daysUntilExpiry > 30) {
                                        $taxStatus = 'active';
                                        $statusIcon = 'fas fa-check-circle';
                                        $statusColor = 'text-green-200';
                                    } elseif($daysUntilExpiry > 0) {
                                        $taxStatus = 'warning';
                                        $statusIcon = 'fas fa-exclamation-triangle';
                                        $statusColor = 'text-orange-200';
                                    } else {
                                        $taxStatus = 'expired';
                                        $statusIcon = 'fas fa-times-circle';
                                        $statusColor = 'text-red-200';
                                    }
                                }
                            @endphp
                            <i class="{{ $statusIcon }} {{ $statusColor }} text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <!-- Tax Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tanggal Pajak
                            </label>
                            <div class="relative">
                                <input type="date"
                                       name="tanggal_pajak"
                                       value="{{ $asset->tanggal_pajak ? date('Y-m-d', strtotime($asset->tanggal_pajak)) : '' }}"
                                       class="w-full pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors text-sm">
                            </div>
                        </div>

                        <!-- Tax Amount -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Jumlah Pajak (Rp)
                            </label>
                            <div class="relative">
                                <input type="number"
                                       name="jumlah_pajak"
                                       value="{{ $asset->jumlah_pajak ?? '' }}"
                                       placeholder="2500000"
                                       class="w-full pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-colors text-sm">
                            </div>
                        </div>

                        <!-- Tax Status Input -->
                        <div>
                            <label for="status_pajak" class="block text-sm font-medium text-gray-700 mb-1">Status Pajak</label>
                            <select id="status_pajak" name="status_pajak" class="mt-1 form-input w-full">
                                <option value="">-- Pilih Status --</option>
                                <option value="Lunas" {{ strtolower(trim($asset->status_pajak ?? '')) === 'lunas' ? 'selected' : '' }}>Lunas</option>
                                <option value="Belum Lunas" {{ strtolower(trim($asset->status_pajak ?? '')) !== 'lunas' ? 'selected' : '' }}>Belum Lunas</option>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Status pembayaran pajak kendaraan</p>
                        </div>
                    </div>

                    <!-- Tax Information Cards -->
                    @if($asset->tanggal_pajak)
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Payment History -->
                            <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-history text-blue-500 mr-2"></i>
                                    <h4 class="font-medium text-blue-800 text-sm">Riwayat Pembayaran</h4>
                                </div>
                                <p class="text-blue-700 text-xs">
                                    Terakhir: {{ \Carbon\Carbon::parse($asset->tanggal_pajak)->subYear()->format('d M Y') }}
                                </p>
                                @if($asset->jumlah_pajak)
                                    <p class="text-blue-600 text-xs">
                                        Jumlah: Rp {{ number_format((float)$asset->jumlah_pajak, 0, ',', '.') }}
                                    </p>
                                @endif
                            </div>

                            <!-- Next Payment -->
                            <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                                <div class="flex items-center mb-1">
                                    <i class="fas fa-calendar-check text-green-500 mr-2"></i>
                                    <h4 class="font-medium text-green-800 text-sm">Jatuh Tempo Berikutnya</h4>
                                </div>
                                @php
                                    $nextPayment = \Carbon\Carbon::parse($asset->tanggal_pajak);
                                @endphp
                                <p class="text-green-700 text-xs">
                                    Tanggal: {{ $nextPayment->format('d M Y') }}
                                </p>
                                <p class="text-green-600 text-xs">
                                    {{ $nextPayment->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>


    @if(auth()->user()->role === 'admin')
    <!-- Service History Section -->
    @include('assets._service_section')
    @endif




        <!-- Action Buttons -->
        <div class="mt-6 flex items-center justify-end space-x-4">
            <a href="{{ route('assets.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                Batal
            </a>
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                Update Asset
            </button>
        </div>
    <script>
    function updateFileName(input) {
        const fileName = input.files && input.files.length > 0 ? input.files[0].name : 'Tidak ada file';
        const container = input.closest('.service-row');
        const fileNameEl = container.querySelector('.file-name');
        if (fileNameEl) {
            fileNameEl.innerHTML = fileName !== 'Tidak ada file'
                ? '<i class="fas fa-file-alt mr-1"></i>' + fileName
                : '<i class="fas fa-file-plus mr-1"></i>Tidak ada file';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Elemen-elemen yang dibutuhkan
        const serviceSection = document.querySelector('.service-enabled');
        const addServiceBtn = document.getElementById('add-service');
        const serviceRowsContainer = document.getElementById('service-rows');
        const vehicleOnlyEls = document.querySelectorAll('.vehicle-only');
        const tipeSelect = document.querySelector('select[name="tipe"]');

        // Debug: log semua elemen
        console.log('Elements found:', {
            serviceSection,
            addServiceBtn,
            serviceRowsContainer
        });

        // Cek jumlah riwayat servis saat halaman dimuat
        const updateServiceVisibility = function() {
            const hasRows = serviceRowsContainer && serviceRowsContainer.querySelectorAll('.service-row').length > 0;

            if (hasRows) {
                if (serviceSection) serviceSection.style.display = 'block';
            } else {
                if (serviceSection) serviceSection.style.display = 'none';
            }
        };

        // Fungsi untuk membuat row baru
        function addNewServiceRow() {
            if (!serviceRowsContainer) return;

            const currentCount = serviceRowsContainer.querySelectorAll('.service-row').length;
            const serviceNumber = currentCount + 1;

            const div = document.createElement('div');
            div.className = 'service-row bg-white border border-gray-200 rounded-lg p-4 mb-4';
            div.innerHTML = `
                <input type="hidden" name="service_id[]" value="">
                <input type="hidden" name="existing_service_file[]" value="">

                <!-- Header dengan nomor -->
                <div class="flex items-center justify-between mb-3">
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">
                        Service #${serviceNumber}
                    </span>
                    <div class="flex items-center space-x-2">
                        <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs">Baru</span>
                        <button type="button" class="remove-service text-red-500 hover:text-red-700 p-1" title="Hapus">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                </div>

                <!-- Form dalam 1 baris horizontal -->
                <div class="grid grid-cols-4 gap-4 items-end">
                    <!-- Tanggal Masuk -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                        <input type="date" name="service_date[]"
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:border-blue-500"
                               required placeholder="dd/mm/yyyy">
                    </div>

                    <!-- Tanggal Selesai -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                        <input type="date" name="service_end_date[]"
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:border-blue-500"
                               placeholder="dd/mm/yyyy">
                    </div>

                    <!-- Biaya Service -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Service</label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500 text-sm">Rp</span>
                            <input type="number" name="service_cost[]"
                                   class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded focus:border-blue-500"
                                   placeholder="0">
                        </div>
                    </div>

                    <!-- Keterangan Service -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fas fa-edit text-green-500 mr-1"></i>Keterangan Service
                        </label>
                        <input type="text" name="service_desc[]"
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:border-blue-500"
                               required placeholder="Contoh: Service berkala, Ganti oli">
                    </div>
                </div>

                <!-- Baris kedua: Upload dokumen dan actions -->
                <div class="mt-4 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 rounded-lg border border-blue-200 cursor-pointer hover:bg-blue-100 transition-colors">
                            <i class="fas fa-upload mr-2"></i>
                            Upload Dokumen
                            <input type="file" name="service_file[]" class="hidden"
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                                   onchange="updateFileName(this)">
                        </label>

                        <span class="text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Tidak ada file dipilih
                        </span>
                    </div>

                    <button type="button" class="remove-service bg-red-50 text-red-700 px-3 py-1 rounded text-xs hover:bg-red-100 transition-colors">
                        <i class="fas fa-times-circle mr-1"></i>
                        Hapus
                    </button>
                </div>
            `;
            serviceRowsContainer.appendChild(div);

            // Auto-focus pada tanggal untuk kemudahan
            const dateInput = div.querySelector('input[name="service_date[]"]');
            if (dateInput) {
                dateInput.focus();
            }

            return div;
        }

        // Tombol tambah riwayat servis
        if (addServiceBtn) {
            addServiceBtn.addEventListener('click', function() {
                addNewServiceRow();
                updateServiceVisibility();
            });
        }

        // Update tampilan berdasarkan tipe
        if (tipeSelect) {
            tipeSelect.addEventListener('change', function() {
                const selectedType = this.value.toLowerCase();
                const isVehicle = selectedType === 'kendaraan';
                const isServiceAutoEnabled = isVehicle || selectedType === 'splicer';

                // Tampilkan/sembunyikan bagian pajak
                if (vehicleOnlyEls) {
                    vehicleOnlyEls.forEach(el => {
                        el.style.display = isVehicle ? '' : 'none';
                    });
                }

                // Untuk tipe kendaraan dan splicer, otomatis tampilkan bagian riwayat servis
                if (isServiceAutoEnabled) {
                    // Tampilkan bagian servis
                    if (serviceSection) {
                        serviceSection.style.display = 'block';

                        // Tambahkan row baru jika belum ada
                        if (serviceRowsContainer && serviceRowsContainer.querySelectorAll('.service-row').length === 0) {
                            addNewServiceRow();
                        }
                    }

                    // Enable hidden field
                    const autoServiceFlag = document.getElementById('auto-service-flag');
                    if (autoServiceFlag) autoServiceFlag.disabled = false;
                } else {
                    // Untuk tipe lain, biarkan pengguna memilih
                    if (serviceRowsContainer && serviceRowsContainer.querySelectorAll('.service-row').length === 0) {
                        if (serviceSection) serviceSection.style.display = 'none';
                    }

                    // Disable hidden field
                    const autoServiceFlag = document.getElementById('auto-service-flag');
                    if (autoServiceFlag) autoServiceFlag.disabled = true;
                }
            });

            // Inisialisasi tampilan awal berdasarkan tipe yang dipilih
            const selectedType = tipeSelect.value.toLowerCase();
            if (selectedType === 'kendaraan' || selectedType === 'splicer') {
                // Tampilkan bagian servis
                if (serviceSection) serviceSection.style.display = 'block';
            }
        }

        // Tombol hapus row servis - menggunakan event delegation
        document.addEventListener('click', function(e) {
            if (e.target && (e.target.classList.contains('remove-service') || e.target.closest('.remove-service'))) {
                e.preventDefault();

                const button = e.target.classList.contains('remove-service') ? e.target : e.target.closest('.remove-service');
                const row = button.closest('.service-row');

                if (row) {
                    // Confirmation dialog
                    if (confirm('Apakah Anda yakin ingin menghapus service ini?')) {
                        const serviceIdInput = row.querySelector('input[name="service_id[]"]');

                        if (serviceIdInput && serviceIdInput.value) {
                            // Existing service - mark for deletion
                            const deleteInput = document.createElement('input');
                            deleteInput.type = 'hidden';
                            deleteInput.name = 'delete_service_ids[]';
                            deleteInput.value = serviceIdInput.value;
                            row.appendChild(deleteInput);

                            // Hide the row instead of removing it completely
                            row.style.display = 'none';

                            // Show notification
                            showNotification('Service ditandai untuk dihapus dan akan dihapus saat formulir disimpan', 'warning');
                        } else {
                            // New service - safe to remove completely
                            row.remove();
                            showNotification('Service dihapus', 'success');
                        }

                        // Update service numbers and visibility
                        updateServiceNumbers();
                        updateServiceVisibility();
                    }
                }
            }
        });

        // Function to update service numbers
        function updateServiceNumbers() {
            const visibleServiceRows = serviceRowsContainer.querySelectorAll('.service-row:not([style*="display: none"])');
            visibleServiceRows.forEach((row, index) => {
                const numberSpan = row.querySelector('span.bg-blue-100');
                if (numberSpan) {
                    numberSpan.textContent = `Service #${index + 1}`;
                }
            });
        }

        // Function to show notifications
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transition-all transform translate-x-full opacity-0`;

            if (type === 'success') {
                notification.className += ' bg-green-500 text-white';
            } else if (type === 'warning') {
                notification.className += ' bg-yellow-500 text-white';
            } else if (type === 'error') {
                notification.className += ' bg-red-500 text-white';
            }

            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'times-circle'} mr-2"></i>
                    ${message}
                </div>
            `;

            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full', 'opacity-0');
            }, 100);

            // Animate out after 3 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }

        // Function untuk preview image
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-image').src = e.target.result;
                    document.getElementById('new-photo-preview').classList.remove('hidden');
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Function untuk update filename display
        function updateFileName(input) {
            const serviceRow = input.closest('.service-row');
            if (!serviceRow) return;

            // Look for any file display elements in the same row
            let fileDisplay = serviceRow.querySelector('.file-name, .current-file, [data-file-display]');

            if (input.files && input.files.length > 0) {
                const fileName = input.files[0].name;
                const fileSize = (input.files[0].size / 1024 / 1024).toFixed(2); // Size in MB

                // Create or update file display
                if (!fileDisplay) {
                    fileDisplay = document.createElement('div');
                    fileDisplay.className = 'mt-2 p-2 bg-green-50 border border-green-200 rounded-lg text-sm';
                    fileDisplay.setAttribute('data-file-display', 'true');
                    input.parentNode.appendChild(fileDisplay);
                }

                fileDisplay.innerHTML = `
                    <div class="flex items-center text-green-700">
                        <i class="fas fa-file mr-2"></i>
                        <span class="font-medium">${fileName}</span>
                        <span class="ml-2 text-xs text-green-600">(${fileSize} MB)</span>
                    </div>
                `;
                fileDisplay.className = 'mt-2 p-2 bg-green-50 border border-green-200 rounded-lg text-sm';
            } else {
                // No file selected, remove display if it exists
                if (fileDisplay && fileDisplay.hasAttribute('data-file-display')) {
                    fileDisplay.remove();
                }
            }
        }

        // Initialize visibility saat pertama kali load
        updateServiceVisibility();
    });

    // Auto-calculate harga_sewa ketika harga_beli diubah
    const hargaBeliInput = document.getElementById('harga_beli');
    const hargaSewaInput = document.getElementById('harga_sewa');
    if (hargaBeliInput && hargaSewaInput) {
        hargaBeliInput.addEventListener('input', function() {
            const hargaBeli = parseFloat(this.value) || 0;
            const hargaSewa = Math.round((hargaBeli * 0.2) / 12);
            hargaSewaInput.value = hargaSewa;
        });
    }
    </script>

@endsection

