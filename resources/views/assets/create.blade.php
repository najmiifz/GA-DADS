@extends('layouts.app')

@section('title', 'Tambah Asset Baru')

@section('content')
<div class="container mx-auto px-4 py-6">
    <style>
        /* Immediate page-level override for form controls to diagnose visibility issues */
        .form-input, input.form-input, select.form-input, textarea.form-input,
        input, select, textarea { color: #111827 !important; background: #ffffff !important; }
        .form-input::placeholder, input::placeholder, textarea::placeholder { color: #9ca3af !important; }
        select.form-input option, select option { color: #111827 !important; background: #ffffff !important; }
        /* Select2 rendered elements */
        .select2-container .select2-selection,
        .select2-container--default .select2-selection--single .select2-selection__rendered,
        .select2-container .select2-search__field,
        .select2-container .select2-selection__rendered {
            color: #111827 !important;
            background: #ffffff !important;
        }
        /* Choices.js / custom select wrappers */
        .choices__inner, .choices__input, .choices__list, .choices__item {
            color: #111827 !important;
            background: #ffffff !important;
        }
        /* Force dropdown option text color when browser uses native UI */
        .select2-container .select2-results__option, .choices__list .choices__item {
            color: #111827 !important;
        }
    </style>
    <h1 class="text-3xl font-bold mb-6">Tambah Aset Baru</h1>
    <div class="bg-white rounded-lg shadow-md p-6">
        @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                {{ session('error') }}
            </div>
        @endif
    <div x-data="{
                  tipe: '{{ old('tipe') }}',
                  jenis_aset: '{{ old('jenis_aset') }}',
                  pic: '{{ old('pic') }}',
                  statusValue: '{{ old('status') }}',
                  project: '{{ old('project') }}',
                  lokasi: '{{ old('lokasi') }}',
                  serial: '{{ old('serial_number') }}',
                  showTipeModal: false,
                  newTipe: '',
                  showJenisModal: false,
                  newJenis: '',
                  showPicModal: false,
                  newPic: '',
                  showProjectModal: false,
                  newProject: '',
                  showLokasiModal: false,
                  newLokasi: '',
                  isTipeKendaraan() {
                      return this.tipe.toLowerCase() === 'kendaraan';
                  },
                  isTipeSplicer() { return this.tipe.toLowerCase() === 'splicer'; },
                  shouldShowService() { return this.isTipeKendaraan() || this.isTipeSplicer(); },
                  async fetchSerial(v) {
                      if (!v) return this.serial = '';
                      try {
                          const res = await fetch(`/api/next-serial/${encodeURIComponent(v)}`);
                          const data = await res.json();
                          this.serial = data.next;
                      } catch (e) {
                          console.error(e);
                      }
                  },
                   // Auto-sync lokasi when PIC changes
                   init() {
                       this.$watch('pic', id => {
                           const opt = this.$refs.picSelect.querySelector(`option[value='${id}']`);
                           if (opt && opt.dataset.lokasi) {
                               this.lokasi = opt.dataset.lokasi;
                           }
                       });
                      // If a PIC is already selected on page load, set lokasi immediately
                      if (this.pic) {
                          const optInit = this.$refs.picSelect.querySelector(`option[value='${this.pic}']`);
                          if (optInit && optInit.dataset && optInit.dataset.lokasi) {
                              this.lokasi = optInit.dataset.lokasi;
                              // ensure the lokasi option exists in the select (same behavior as change handler)
                              if (!this.$refs.lokasiSelect.querySelector(`option[value='${this.lokasi}']`)) {
                                  this.$refs.lokasiSelect.insertAdjacentHTML('beforeend', `<option value='${this.lokasi}'>${this.lokasi}</option>`);
                              }
                          }
                      }
                      // Watch jenis_aset to fetch next serial
                      this.$watch('jenis_aset', v => this.fetchSerial(v));
                      // On init if sudah ada old value
                      if (this.jenis_aset) this.fetchSerial(this.jenis_aset);
                   }
                 }" x-init="init()">
    <form method="POST" action="{{ route('assets.store') }}" enctype="multipart/form-data" class="space-y-8" onsubmit="document.getElementById('pic_hidden').value = document.getElementById('pic_select').value">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Tipe -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label for="tipe" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-layer-group text-blue-500 mr-2"></i>
                        Tipe Asset
                    </label>
                    <select id="tipe" name="tipe" x-model="tipe" x-ref="tipeSelect" @change="if($event.target.value==='__add__'){ showTipeModal=true }" class="form-input w-full">
                        <option value="">Pilih Tipe</option>
                        <option value="__add__">--- Tambah Baru ---</option>
                        @php
                            // Always include these fixed types, then append other types from DB, avoiding duplicates
                            $fixedTipes = ['Kendaraan', 'Splicer'];
                            $merged = collect($fixedTipes)->merge($tipes)->unique();
                        @endphp
                        @foreach($merged as $opt)
                            <option value="{{ $opt }}">{{ $opt }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Pilih kategori utama asset seperti Kendaraan, Splicer, dll.</p>
                    @error('tipe')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Jenis Asset -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label for="jenis_aset" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tags text-green-500 mr-2"></i>
                        Jenis Asset
                    </label>
                    <select id="jenis_aset" name="jenis_aset" x-model="jenis_aset" x-ref="jenisSelect" @change="if($event.target.value==='__add__'){ showJenisModal=true }" class="form-input w-full">
                        <option value="">Pilih Jenis Asset</option>
                        <option value="__add__">+ Tambah Baru</option>
                        @php
                            $fixedJenis = ['Laptop','Handphone','Splicer','Otdr','Ols','Opm','Furniture','Mobil','Motor'];
                            $mergedJenis = collect($fixedJenis)->merge($jenisAsets)->unique();
                        @endphp
                        @foreach($mergedJenis as $j)
                            <option value="{{ $j }}">{{ $j }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Spesifikasi lebih detail dari asset (contoh: Mobil, Motor, dll.)</p>
                    @error('jenis_aset')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Status Aset -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label for="status" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        Status Aset
                    </label>
                    <select name="status" id="status" class="form-input w-full" x-model="statusValue">
                        <option value="">Normal (Pilih PIC di bawah)</option>
                        <option value="Available">Available</option>
                        <option value="Rusak">Rusak</option>
                        <option value="Hilang">Hilang</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Jika Available/Rusak/Hilang, PIC akan diabaikan</p>
                    @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- PIC -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label for="pic" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user text-purple-500 mr-2"></i>
                        Person In Charge (PIC)
                    </label>
                    <div x-show="['Available','Rusak','Hilang'].includes(statusValue)" x-cloak>
                        <input type="text" :value="statusValue" readonly class="form-input w-full bg-gray-100" placeholder="Otomatis diset sesuai status">
                    </div>
                    <div x-show="!['Available','Rusak','Hilang'].includes(statusValue)">
                        <!-- Hidden input ensures the PIC value is always sent to the server -->
                        <input type="hidden" id="pic_hidden" name="pic" x-model="pic" />
                        <select id="pic_select" name="pic_select" data-hidden-id="pic_hidden" class="select2 form-input w-full" x-ref="picSelect"
                            @change="
                                // Ensure Alpine's `pic` is a plain string (Choices.js may expose complex objects otherwise)
                                if($event.target.value==='__add__'){ showPicModal=true } else {
                                    // set Alpine model to the selected value (string)
                                    pic = $event.target.value.toString();
                                    const loc = $event.target.selectedOptions[0]?.dataset?.lokasi;
                                    if(loc) {
                                        // inject option if not exists
                                        if(!$refs.lokasiSelect.querySelector(`option[value='${loc}']`)) {
                                            $refs.lokasiSelect.insertAdjacentHTML('beforeend', `<option value='${loc}'>${loc}</option>`);
                                        }
                                        lokasi = loc;
                                    }
                                }
                            "
                            >
                            <option value="">Pilih PIC</option>
                            <option value="__add__">+ Tambah Baru</option>
                            @foreach($usersForPic as $user)
                                <option value="{{ $user->id }}" data-lokasi="{{ $user->lokasi }}">👤 {{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Siapa yang bertanggung jawab atas asset ini</p>
                        @error('pic')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Merk -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label for="merk" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-trademark text-orange-500 mr-2"></i>
                        Merk/Brand
                    </label>
                    <input type="text" name="merk" id="merk" class="form-input w-full" placeholder="Contoh: Toyota, Honda, Fujikura">
                    <p class="text-xs text-gray-500 mt-1">Merek atau brand dari asset</p>
                    @error('merk')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Nomor Asset/Serial Number (Generated) -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label for="serial_number" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-barcode text-indigo-500 mr-2"></i>
                        Nomor Asset/Serial Number
                    </label>
                    <input type="text" name="serial_number" id="serial_number" x-model="serial" readonly class="mt-1 block w-full px-3 py-2 bg-gray-100 border border-gray-300 rounded-md" placeholder="Otomatis terisi" />
                </div>
                <!-- Plate Number (for Kendaraan) -->
                <div x-show="isTipeKendaraan()" class="bg-gray-50 p-4 rounded-lg">
                    <label for="plate_number" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-id-card text-green-500 mr-2"></i>
                        Nomor Plat Kendaraan
                    </label>
                    <input type="text" name="plate_number" id="plate_number" class="form-input w-full" placeholder="Contoh: B 1234 CD">
                    <p class="text-xs text-gray-500 mt-1">Nomor plat kendaraan jika tipe asset adalah kendaraan</p>
                    @error('plate_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Project -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label for="project" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-project-diagram text-teal-500 mr-2"></i>
                        Project
                    </label>
                    <select id="project" name="project" x-model="project" x-ref="projectSelect" @change="if($event.target.value==='__add__'){ showProjectModal=true }" class="form-input w-full">
                        <option value="" {{ old('project') == '' ? 'selected' : '' }}>Pilih Project</option>
                        <option value="__add__">+ Tambah Baru</option>
                        @foreach($projects as $proj)
                            <option value="{{ $proj }}" {{ old('project') == $proj ? 'selected' : '' }}>{{ $proj }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Project atau kegiatan dimana asset ini digunakan</p>
                    @error('project')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Lokasi -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label for="lokasi" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-map-marker-alt text-red-500 mr-2"></i>
                        Lokasi
                    </label>
                    <select id="lokasi" name="lokasi" x-model="lokasi" x-ref="lokasiSelect" @change="if($event.target.value==='__add__'){ showLokasiModal=true; $event.target.value='' }" class="form-input w-full">
                        <option value="">Pilih Lokasi</option>
                        <option value="__add__">+ Tambah Baru</option>
                        @foreach($lokasis as $lok)
                            <option value="{{ $lok }}" :selected="lokasi==='{{ $lok }}'">{{ $lok }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Tempat dimana asset ini berada atau beroperasi</p>
                    @error('lokasi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Tanggal Beli -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label for="tanggal_beli" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-calendar-alt text-blue-500 mr-2"></i>
                        Tanggal Pembelian
                    </label>
                    <input type="date" name="tanggal_beli" id="tanggal_beli" class="form-input w-full">
                    <p class="text-xs text-gray-500 mt-1">Kapan asset ini dibeli atau diperoleh</p>
                </div>

                <!-- Harga Beli -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label for="harga_beli" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-money-bill-wave text-green-500 mr-2"></i>
                        Harga Pembelian
                    </label>
                    <input type="text" name="harga_beli" id="harga_beli" value="{{ old('harga_beli') }}" class="form-input w-full rupiah" placeholder="Rp 0">
                    <p class="text-xs text-gray-500 mt-1">Harga beli asset dalam Rupiah</p>
                </div>

                <!-- Harga Sewa -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label for="harga_sewa" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-hand-holding-usd text-yellow-500 mr-2"></i>
                        Harga Sewa (Opsional)
                    </label>
                    <input type="text" name="harga_sewa" id="harga_sewa" value="{{ old('harga_sewa') }}" class="form-input w-full rupiah" placeholder="Rp 0">
                    <p class="text-xs text-gray-500 mt-1">Tarif sewa asset per periode (jika asset disewakan)</p>
                </div>

                <!-- Foto Aset (opsional) -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label for="foto_aset" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-camera text-pink-500 mr-2"></i>
                        Foto Asset (Opsional)
                    </label>
                    <input type="file" name="foto_aset" id="foto_aset" accept="image/*" class="form-input w-full">
                    <p class="text-xs text-gray-500 mt-1">Upload foto asset. Format: JPG, PNG, maksimal 2MB</p>
                </div>

                <!-- Keterangan Aset (opsional) -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <label for="keterangan" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-sticky-note text-amber-500 mr-2"></i>
                        Keterangan Asset (Opsional)
                    </label>
                    <input type="text" name="keterangan" id="keterangan" value="{{ old('keterangan') }}" class="form-input w-full" placeholder="Catatan tambahan tentang asset...">
                    <p class="text-xs text-gray-500 mt-1">Informasi tambahan atau catatan khusus tentang asset</p>
                </div>
            </div>
            <!-- Vehicle-only sections -->
            <template x-if="isTipeKendaraan()">
                <div class="space-y-8">
                    <!-- Data Pajak -->
                    <div class="bg-amber-600 text-white rounded-lg overflow-hidden">
                        <div class="px-6 py-4 flex items-center justify-between">
                                <div class="flex items-center">
                                    <div>
                                        <h3 class="text-lg font-semibold">Data Pajak Kendaraan</h3>
                                        <p class="text-sm text-amber-100">Informasi pajak dan status compliance kendaraan</p>
                                    </div>
                                </div>
                                <div class="text-white/90"> </div>
                            </div>
                        <div class="px-6 py-6 bg-white rounded-b-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-center">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Pajak</label>
                                    <div class="relative">
                                        <input type="date" name="tanggal_pajak" class="w-full pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors text-sm" placeholder="dd/mm/yyyy">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Pajak (Rp)</label>
                                    <div class="relative">
                                        <input type="number" name="jumlah_pajak" class="w-full pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-colors text-sm" placeholder="2500000">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Pajak</label>
                                    <div class="flex items-center justify-between">
                                        <select id="status_pajak" name="status_pajak" class="form-input w-36">
                                            <option value="">-- Pilih Status --</option>
                                            <option value="Lunas">Lunas</option>
                                            <option value="Belum Lunas">Belum Lunas</option>
                                        </select>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Status pembayaran pajak kendaraan</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Foto Kendaraan -->
                    <div class="border-t-4 border-purple-500 bg-purple-50 p-6 rounded-lg">
                        <h3 class="flex items-center text-lg font-semibold mb-4 text-purple-700">
                            <i class="fas fa-images text-purple-500 mr-3"></i>
                            Dokumentasi Kendaraan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white p-4 rounded-lg border border-purple-200">
                                <label class="flex items-center text-sm font-medium text-gray-700 mb-3">
                                    <i class="fas fa-id-card text-blue-500 mr-2"></i>
                                    Foto STNK
                                </label>
                                <input type="file" name="foto_stnk" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-purple-500">
                                <div class="mt-2 p-2 bg-blue-50 rounded border border-blue-200">
                                    <p class="text-xs text-blue-700">📄 Upload foto STNK kendaraan</p>
                                    <p class="text-xs text-gray-500">Format: JPG, PNG, maksimal 2MB</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Riwayat Servis -->
                </div>
            </template>
            </template>

            <!-- Service history section, auto-enabled for Kendaraan & Splicer -->
            <div x-show="shouldShowService()" class="border-t-4 border-orange-500 bg-orange-50 p-6 rounded-lg mt-6" id="service-section">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="flex items-center text-lg font-semibold text-orange-700">
                        <i class="fas fa-tools text-orange-500 mr-3"></i>
                        Riwayat Service & Maintenance
                    </h3>
                    <button type="button" id="add-service-btn" class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-medium rounded-md transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Tambah Riwayat
                    </button>
                </div>
                <div id="service-rows" class="space-y-4">
                    <!-- Baris service default -->
                    <div class="service-row bg-white border-l-4 border-orange-400 rounded-lg p-4 shadow-sm">
                        <input type="hidden" name="service_id[]" value="">
                        <input type="hidden" name="existing_service_file[]" value="">

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label for="service_tanggal_masuk" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                                <input type="date" name="service_tanggal_masuk[]" class="form-input w-full">
                            </div>
                            <div>
                                <label for="service_tanggal_selesai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                                <input type="date" name="service_tanggal_selesai[]" class="form-input w-full">
                            </div>
                            <div>
                                <label for="service_biaya" class="block text-sm font-medium text-gray-700 mb-1">Biaya Service</label>
                                <input type="text" name="service_biaya[]" class="form-input w-full rupiah" placeholder="Rp 0">
                            </div>
                            <div>
                                <label for="service_keterangan" class="flex items-center text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-wrench text-green-500 mr-2"></i>
                                    Keterangan Service
                                </label>
                                <input type="text" name="service_keterangan[]" class="form-input w-full" placeholder="Contoh: Service berkala, Ganti oli">
                            </div>
                        </div>

                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                            <div class="flex items-center gap-3">
                                <label class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-md border cursor-pointer transition-colors">
                                    <i class="fas fa-file-upload mr-2"></i>
                                    Upload Dokumen
                                    <input type="file" name="service_file[]" class="hidden" onchange="updateFileName(this)">
                                </label>
                                <span class="text-sm text-gray-600 file-name">
                                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                    Tidak ada file dipilih
                                </span>
                            </div>
                            <button type="button" class="remove-service inline-flex items-center bg-red-500 hover:bg-red-600 text-white rounded-md px-4 py-2 font-medium transition-colors" title="Hapus Riwayat Service">
                                <i class="fas fa-trash mr-2"></i>
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
                <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                        <strong>Tips:</strong> Anda dapat menambahkan beberapa riwayat service sekaligus. Upload nota atau invoice service untuk dokumentasi yang lebih lengkap.
                    </p>
                </div>
            </div>


            <!-- Submit Button -->
            <div class="flex justify-center pt-8 border-t">
                <button type="submit" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold text-lg rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                    <i class="fas fa-save text-xl mr-3"></i>
                    Simpan Asset
                    <i class="fas fa-arrow-right ml-3"></i>
                </button>
            </div>
        </form>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const picSelect = document.getElementById('pic_select');
                const picHidden = document.getElementById('pic_hidden');
                const form = picSelect ? picSelect.closest('form') : null;
                function syncPic() {
                    if (!picSelect || !picHidden) return;
                    picHidden.value = picSelect.value || '';
                }
                if (picSelect) {
                    picSelect.addEventListener('change', syncPic);
                }
                if (form) {
                    form.addEventListener('submit', function() {
                        syncPic();
                    });
                }
                // initial sync
                syncPic();
            });
        </script>
    <!-- Modal Tambah Tipe Baru -->
    <div x-show="showTipeModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" x-cloak>
            <div class="bg-white rounded-xl p-6 w-96 shadow-2xl border-t-4 border-blue-500">
                <h2 class="flex items-center text-xl font-semibold mb-6 text-blue-700">
                    <i class="fas fa-plus-circle text-blue-500 mr-3"></i>
                    Tambah Tipe Asset Baru
                </h2>
                <div class="mb-6">
                    <label class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-layer-group text-blue-500 mr-2"></i>
                        Nama Tipe Asset
                    </label>
                    <input type="text" x-model="newTipe" placeholder="Contoh: Laptop, Smartphone, Furniture..." class="form-input w-full bg-gray-50 border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    <p class="text-xs text-gray-500 mt-1">Masukkan nama tipe asset yang baru</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showTipeModal=false; tipe=''; newTipe=''" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </button>
                    <button type="button" @click="if(newTipe){ $refs.tipeSelect.insertAdjacentHTML('beforeend', `<option value='${newTipe}' selected>${newTipe}</option>`); showTipeModal=false; tipe=newTipe; newTipe='' }" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
        <!-- Modal Tambah Jenis Asset Baru -->
        <div x-show="showJenisModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" x-cloak>
            <div class="bg-white rounded-xl p-6 w-96 shadow-2xl border-t-4 border-green-500">
                <h2 class="flex items-center text-xl font-semibold mb-6 text-green-700">
                    <i class="fas fa-tags text-green-500 mr-3"></i>
                    Tambah Jenis Asset Baru
                </h2>
                <div class="mb-6">
                    <label class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-tag text-green-500 mr-2"></i>
                        Nama Jenis Asset
                    </label>
                    <input type="text" x-model="newJenis" placeholder="Contoh: Gaming, Office, Tools..." class="form-input w-full bg-gray-50 border-gray-300 focus:border-green-500 focus:ring-green-500">
                    <p class="text-xs text-gray-500 mt-1">Masukkan kategori jenis asset yang baru</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showJenisModal=false; jenis_aset=''; newJenis=''" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </button>
                    <button type="button" @click="if(newJenis){ $refs.jenisSelect.insertAdjacentHTML('beforeend', `<option value='${newJenis}' selected>${newJenis}</option>`); showJenisModal=false; jenis_aset=newJenis; newJenis='' }" class="inline-flex items-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
        <!-- Modal Tambah PIC Baru -->
        <div x-show="showPicModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" x-cloak>
            <div class="bg-white rounded-xl p-6 w-96 shadow-2xl border-t-4 border-purple-500">
                <h2 class="flex items-center text-xl font-semibold mb-6 text-purple-700">
                    <i class="fas fa-user text-purple-500 mr-3"></i>
                    Tambah PIC Baru
                </h2>
                <div class="mb-6">
                    <label class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-user-tie text-purple-500 mr-2"></i>
                        Nama Person In Charge
                    </label>
                    <input type="text" x-model="newPic" placeholder="Contoh: John Doe, IT Manager..." class="form-input w-full bg-gray-50 border-gray-300 focus:border-purple-500 focus:ring-purple-500">
                    <p class="text-xs text-gray-500 mt-1">Masukkan nama penanggung jawab asset</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showPicModal=false; pic=''; newPic=''" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </button>
                    <button type="button" @click="if(newPic){ $refs.picSelect.insertAdjacentHTML('beforeend', `<option value='${newPic}' selected>${newPic}</option>`); showPicModal=false; pic=newPic; newPic='' }" class="inline-flex items-center px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
        <!-- Modal Tambah Project Baru -->
        <div x-show="showProjectModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" x-cloak>
            <div class="bg-white rounded-xl p-6 w-96 shadow-2xl border-t-4 border-indigo-500">
                <h2 class="flex items-center text-xl font-semibold mb-6 text-indigo-700">
                    <i class="fas fa-project-diagram text-indigo-500 mr-3"></i>
                    Tambah Project Baru
                </h2>
                <div class="mb-6">
                    <label class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-folder-open text-indigo-500 mr-2"></i>
                        Nama Project
                    </label>
                    <input type="text" x-model="newProject" placeholder="Contoh: Website Development, Office Upgrade..." class="form-input w-full bg-gray-50 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Masukkan nama project atau kegiatan</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showProjectModal=false; project=''; newProject=''" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </button>
                    <button type="button" @click="if(newProject){ $refs.projectSelect.insertAdjacentHTML('beforeend', `<option value='${newProject}' selected>${newProject}</option>`); showProjectModal=false; project=newProject; newProject='' }" class="inline-flex items-center px-4 py-2 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Simpan
                    </button>
                </div>
            </div>
        </div>
        <!-- Modal Tambah Lokasi Baru -->
        <div x-show="showLokasiModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50" x-cloak>
            <div class="bg-white rounded-xl p-6 w-96 shadow-2xl border-t-4 border-orange-500">
                <h2 class="flex items-center text-xl font-semibold mb-6 text-orange-700">
                    <i class="fas fa-map-marker-alt text-orange-500 mr-3"></i>
                    Tambah Lokasi Baru
                </h2>
                <div class="mb-6">
                    <label class="flex items-center text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-building text-orange-500 mr-2"></i>
                        Nama Lokasi
                    </label>
                    <input type="text" x-model="newLokasi" placeholder="Contoh: Lantai 2 Ruang IT, Gudang A..." class="form-input w-full bg-gray-50 border-gray-300 focus:border-orange-500 focus:ring-orange-500">
                    <p class="text-xs text-gray-500 mt-1">Masukkan lokasi penempatan asset</p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="showLokasiModal=false; newLokasi=''; $refs.lokasiSelect.value=''" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </button>
                    <button type="button"
                            @click="if (newLokasi.trim()) {
                                // Tambah opsi baru ke select
                                const option = document.createElement('option');
                                option.value = newLokasi.trim();
                                option.text = newLokasi.trim();
                                option.selected = true;
                                $refs.lokasiSelect.appendChild(option);

                                // Set model dan reset modal
                                lokasi = newLokasi.trim();
                                showLokasiModal = false;
                                newLokasi = '';
                            }"
                            class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            Simpan
                        </button>
                    </div>
                </div>
            </div>
        </div>
        </div>
     </div>
 </div>

@endsection

@push('scripts')
<script>
    function updateFileName(input) {
        const fileName = input.files && input.files.length > 0 ? input.files[0].name : 'Tidak ada file';
        const container = input.closest('label').parentElement;
        const fileNameEl = container.querySelector('.file-name');
        if (fileNameEl) {
            fileNameEl.textContent = fileName;
        }
    }

document.addEventListener('DOMContentLoaded', function() {
    // Debug untuk memantau perubahan tipe
    const tipeSelect = document.getElementById('tipe');
    const serviceSection = document.getElementById('service-section');

    // Fungsi untuk membuat row baru servis
    function addNewServiceRow() {
        const serviceRowsContainer = document.getElementById('service-rows');
        if (!serviceRowsContainer) return;

        const div = document.createElement('div');
        div.className = 'service-row bg-white border rounded-lg p-4 mb-3';
        div.innerHTML = `
            <input type="hidden" name="service_id[]" value="">
            <input type="hidden" name="existing_service_file[]" value="">

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Masuk</label>
                    <input type="date" name="service_tanggal_masuk[]" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai</label>
                    <input type="date" name="service_tanggal_selesai[]" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Service</label>
                    <input type="text" name="service_biaya[]" class="form-input w-full rupiah" placeholder="Rp 0">
                </div>
                <div>
                    <label class="flex items-center text-sm font-medium text-gray-700 mb-1">
                        <i class="fas fa-wrench text-green-500 mr-2"></i>
                        Keterangan Service
                    </label>
                    <input type="text" name="service_keterangan[]" class="form-input w-full" placeholder="Contoh: Service berkala, Ganti oli">
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <label class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md border border-blue-300 cursor-pointer hover:bg-blue-200">
                        Upload File
                        <input type="file" name="service_file[]" class="hidden" onchange="updateFileName(this)">
                    </label>
                    <span class="text-sm text-gray-600 file-name">Tidak ada file</span>
                </div>
                <button type="button" class="remove-service bg-red-500 hover:bg-red-600 text-white rounded-md px-4 py-2 font-medium transition-colors" title="Hapus Riwayat Servis">
                    <i class="fas fa-trash mr-2"></i>Hapus
                </button>
            </div>
        `;
        serviceRowsContainer.appendChild(div);
        return div;
    }

    // Tambah event listener untuk tombol remove-service
    document.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-service')) {
            const row = e.target.closest('.service-row');
            if (row) row.remove();
        }
    });

    // Format input Rupiah
    function formatRupiah(angka, prefix = 'Rp ') {
        var number_string = angka.replace(/[^\d]/g, '').toString();
        var sisa = number_string.length % 3;
        var rupiah = number_string.substr(0, sisa);
        var ribuan = number_string.substr(sisa).match(/\d{3}/g);
        if (ribuan) {
            var separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return prefix + rupiah;
    }
    document.querySelectorAll('.rupiah').forEach(function(el) {
        el.addEventListener('input', function(e) {
            var cursorPos = this.selectionStart;
            var originalLength = this.value.length;
            this.value = formatRupiah(this.value);
            var newLength = this.value.length;
            this.setSelectionRange(cursorPos + (newLength - originalLength), cursorPos + (newLength - originalLength));
        });
        // inisialisasi format awal jika ada nilai
        if (el.value) el.value = formatRupiah(el.value);
    });
    // Auto-calculate harga_sewa: 20% of harga_beli / 12
    const hargaBeliInput = document.getElementById('harga_beli');
    const hargaSewaInput = document.getElementById('harga_sewa');
    if (hargaBeliInput && hargaSewaInput) {
        hargaBeliInput.addEventListener('input', function() {
            // extract numeric value
            const numeric = Number(this.value.replace(/[^\d]/g, ''));
            // calculate 20% per year divided by 12 months
            const sewa = Math.round((numeric * 0.2) / 12);
            hargaSewaInput.value = formatRupiah(sewa.toString());
        });
    }

    // Tambah event listener untuk tombol "Tambah Riwayat"
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'add-service-btn') {
            addNewServiceRow();
        }
    });
});

// Auto-fill Lokasi based on selected PIC
document.addEventListener('DOMContentLoaded', function() {
    // Map of user_id to lokasi
    const userLocations = @json($users->pluck('lokasi','id'));
    // prefer the pic_select id (used above), but also support legacy ids
    const picSelect = document.getElementById('pic_select') || document.getElementById('user_id') || document.getElementById('pic');
    const lokasiSelect = document.getElementById('lokasi');

    function fillLokasiFromSelect(sel) {
        if (!sel || !lokasiSelect) return;
        const val = sel.value;
        // if numeric and mapped, use the mapping
        if ((/^\d+$/).test(val) && userLocations[val]) {
            const loc = userLocations[val];
            // ensure option exists
            if (!lokasiSelect.querySelector(`option[value='${loc}']`)) {
                lokasiSelect.insertAdjacentHTML('beforeend', `<option value='${loc}'>${loc}</option>`);
            }
            lokasiSelect.value = loc;
            return;
        }
        // fallback: check selected option's data-lokasi attribute (covers modal-added options or non-numeric values)
        const opt = sel.selectedOptions && sel.selectedOptions[0];
        if (opt && opt.dataset && opt.dataset.lokasi) {
            const loc = opt.dataset.lokasi;
            if (!lokasiSelect.querySelector(`option[value='${loc}']`)) {
                lokasiSelect.insertAdjacentHTML('beforeend', `<option value='${loc}'>${loc}</option>`);
            }
            lokasiSelect.value = loc;
        }
    }

    if (picSelect && lokasiSelect) {
        // initialize on load (covers pre-selected PIC)
        fillLokasiFromSelect(picSelect);
        // update on change
        picSelect.addEventListener('change', function(e) {
            fillLokasiFromSelect(e.target);
        });
    }
});
</script>
@endpush
