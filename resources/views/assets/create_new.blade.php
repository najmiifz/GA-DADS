@extends('layouts.app')

@section('title', 'Tambah Asset Baru')

@section('content')
<div class="p-6" x-data="assetCreateForm()">
    <!-- Header dengan Breadcrumb -->
    <div class="mb-6">
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors">
                        <i class="fas fa-home mr-2"></i>Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <a href="{{ route('assets.index') }}" class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors">
                            Asset Management
                        </a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                        <span class="text-sm font-medium text-gray-500">Tambah Asset</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">
                        <i class="fas fa-plus-circle mr-2"></i>Tambah Asset Baru
                    </h1>
                    <p class="text-blue-100 text-lg">Daftarkan asset baru ke dalam sistem management</p>
                </div>
                <div class="hidden lg:block">
                    <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-plus text-3xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Indicator -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-600">Progress Pengisian</span>
            <span class="text-sm font-medium text-indigo-600" x-text="Math.round(formProgress) + '%'"></span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-2 rounded-full transition-all duration-500 ease-out"
                 :style="'width: ' + formProgress + '%'"></div>
        </div>
    </div>

    <!-- Main Form -->
    <form method="POST" action="{{ route('assets.store') }}" class="space-y-8" @submit="validateForm">
        @csrf

        <!-- Step 1: Basic Information -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-info-circle text-blue-600"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Informasi Dasar Asset</h2>
                        <p class="text-sm text-gray-600">Masukkan informasi dasar mengenai asset</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama Asset -->
                    <div class="md:col-span-2">
                        <label for="nama" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-tag text-indigo-500 mr-2"></i>Nama Asset <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="nama"
                               id="nama"
                               x-model="formData.nama"
                               @input="calculateProgress"
                               class="form-input w-full"
                               placeholder="Masukkan nama lengkap asset..."
                               required>
                        <p class="mt-1 text-xs text-gray-500">Contoh: "Laptop Dell Latitude 7420" atau "Toyota Avanza G 2020"</p>
                        @error('nama')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kode Asset -->
                    <div>
                        <label for="kode_asset" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-barcode text-purple-500 mr-2"></i>Kode Asset <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               name="kode_asset"
                               id="kode_asset"
                               x-model="formData.kode_asset"
                               @input="calculateProgress"
                               class="form-input"
                               placeholder="AST-001"
                               required>
                        <p class="mt-1 text-xs text-gray-500">Format: AST-XXX (akan dibuat otomatis jika kosong)</p>
                        @error('kode_asset')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Asset -->
                    <div>
                        <label for="jenis" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-layer-group text-green-500 mr-2"></i>Jenis Asset <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis"
                                id="jenis"
                                x-model="formData.jenis"
                                @change="calculateProgress; updateSubCategories()"
                                class="form-input"
                                required>
                            <option value="">-- Pilih Jenis Asset --</option>
                            <option value="Kendaraan">🚗 Kendaraan</option>
                            <option value="Splicer">🔧 Splicer & Tools</option>
                            <option value="IT Equipment">💻 IT Equipment</option>
                            <option value="Furniture">🪑 Furniture & Office</option>
                            <option value="Electronic">⚡ Electronic</option>
                        </select>
                        @error('jenis')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Sub Kategori (Dynamic) -->
                    <div x-show="showSubCategory" x-transition>
                        <label for="sub_kategori" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-list text-orange-500 mr-2"></i>Sub Kategori
                        </label>
                        <select name="sub_kategori"
                                id="sub_kategori"
                                x-model="formData.sub_kategori"
                                @change="calculateProgress"
                                class="form-input">
                            <option value="">-- Pilih Sub Kategori --</option>
                            <template x-for="category in subCategories" :key="category">
                                <option :value="category" x-text="category"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Merk -->
                    <div>
                        <label for="merk" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-award text-yellow-500 mr-2"></i>Merk/Brand
                        </label>
                        <input type="text"
                               name="merk"
                               id="merk"
                               x-model="formData.merk"
                               @input="calculateProgress"
                               class="form-input"
                               placeholder="Toyota, Dell, Fujikura, dll">
                        @error('merk')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2: Technical Details -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-cogs text-green-600"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Detail Teknis</h2>
                        <p class="text-sm text-gray-600">Spesifikasi dan detail teknis asset</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Model/Tipe -->
                    <div>
                        <label for="model" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-cube text-indigo-500 mr-2"></i>Model/Tipe
                        </label>
                        <input type="text"
                               name="model"
                               id="model"
                               x-model="formData.model"
                               @input="calculateProgress"
                               class="form-input"
                               placeholder="Contoh: Avanza G, Latitude 7420, FSM-70S">
                        @error('model')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Serial Number -->
                    <div>
                        <label for="serial_number" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-hashtag text-purple-500 mr-2"></i>Serial Number
                        </label>
                        <input type="text"
                               name="serial_number"
                               id="serial_number"
                               x-model="formData.serial_number"
                               @input="calculateProgress"
                               class="form-input"
                               placeholder="S/N atau nomor seri">
                        @error('serial_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tahun Pembuatan -->
                    <div>
                        <label for="tahun" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar text-blue-500 mr-2"></i>Tahun Pembuatan
                        </label>
                        <select name="tahun"
                                id="tahun"
                                x-model="formData.tahun"
                                @change="calculateProgress"
                                class="form-input">
                            <option value="">-- Pilih Tahun --</option>
                            <template x-for="year in years" :key="year">
                                <option :value="year" x-text="year"></option>
                            </template>
                        </select>
                        @error('tahun')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nilai Asset -->
                    <div>
                        <label for="nilai" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-dollar-sign text-green-500 mr-2"></i>Nilai Asset (Rp)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="number"
                                   name="nilai"
                                   id="nilai"
                                   x-model="formData.nilai"
                                   @input="calculateProgress; formatCurrency()"
                                   class="form-input pl-12"
                                   placeholder="0"
                                   step="1000">
                        </div>
                        <p class="mt-1 text-xs text-gray-500" x-text="'Nilai dalam format: ' + formatNumber(formData.nilai || 0)"></p>
                        @error('nilai')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Location & Assignment -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden card-hover">
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-map-marker-alt text-orange-600"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Lokasi & Penugasan</h2>
                        <p class="text-sm text-gray-600">Informasi lokasi dan penugasan asset</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Lokasi -->
                    <div>
                        <label for="lokasi" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-building text-red-500 mr-2"></i>Lokasi <span class="text-red-500">*</span>
                        </label>
                        <select name="lokasi"
                                id="lokasi"
                                x-model="formData.lokasi"
                                @change="calculateProgress"
                                class="form-input"
                                required>
                            <option value="">-- Pilih Lokasi --</option>
                            <option value="Kantor Pusat Jakarta">🏢 Kantor Pusat Jakarta</option>
                            <option value="Kantor Cabang Surabaya">🏢 Kantor Cabang Surabaya</option>
                            <option value="Kantor Cabang Medan">🏢 Kantor Cabang Medan</option>
                            <option value="Gudang Utama">📦 Gudang Utama</option>
                            <option value="Workshop">🔧 Workshop</option>
                            <option value="Site Project">🏗️ Site Project</option>
                        </select>
                        @error('lokasi')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-flag text-blue-500 mr-2"></i>Status <span class="text-red-500">*</span>
                        </label>
                        <select name="status"
                                id="status"
                                x-model="formData.status"
                                @change="calculateProgress"
                                class="form-input"
                                required>
                            <option value="">-- Pilih Status --</option>
                            <option value="Tersedia">✅ Tersedia</option>
                            <option value="Terpakai">🔄 Terpakai</option>
                            <option value="Maintenance">🔧 Maintenance</option>
                            <option value="Rusak">❌ Rusak</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- PIC -->
                    <div>
                        <label for="pic" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-green-500 mr-2"></i>PIC (Person in Charge)
                        </label>
                        <input type="text"
                               name="pic"
                               id="pic"
                               x-model="formData.pic"
                               @input="calculateProgress"
                               class="form-input"
                               placeholder="Nama lengkap PIC">
                        @error('pic')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Project -->
                    <div>
                        <label for="project" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-project-diagram text-purple-500 mr-2"></i>Project
                        </label>
                        <input type="text"
                               name="project"
                               id="project"
                               x-model="formData.project"
                               @input="calculateProgress"
                               class="form-input"
                               placeholder="Nama project yang menggunakan asset">
                        @error('project')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Keterangan -->
                    <div class="md:col-span-2">
                        <label for="keterangan" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-sticky-note text-yellow-500 mr-2"></i>Keterangan/Catatan
                        </label>
                        <textarea name="keterangan"
                                  id="keterangan"
                                  x-model="formData.keterangan"
                                  @input="calculateProgress"
                                  rows="3"
                                  class="form-input"
                                  placeholder="Catatan tambahan, kondisi, atau informasi penting lainnya..."></textarea>
                        @error('keterangan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-xl shadow-lg p-6 card-hover">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                    <button type="submit"
                            class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 border border-transparent rounded-lg font-semibold text-white transition-all duration-200 transform hover:scale-105 active:scale-95">
                        <i class="fas fa-save mr-2"></i>
                        <span x-text="formProgress < 100 ? 'Simpan Draft' : 'Simpan Asset'"></span>
                    </button>

                    <button type="button"
                            @click="saveAsDraft()"
                            class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 border border-gray-300 rounded-lg font-semibold text-gray-700 transition-all duration-200">
                        <i class="fas fa-file-alt mr-2"></i>Simpan sebagai Draft
                    </button>

                    <a href="{{ route('assets.index') }}"
                       class="inline-flex items-center justify-center px-6 py-3 bg-white hover:bg-gray-50 border border-gray-300 rounded-lg font-semibold text-gray-700 transition-all duration-200">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                </div>

                <!-- Form Validation Status -->
                <div class="flex items-center space-x-3">
                    <div class="flex items-center" x-show="formProgress >= 100">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse mr-2"></div>
                        <span class="text-sm font-medium text-green-600">Form siap disimpan</span>
                    </div>
                    <div class="flex items-center" x-show="formProgress < 100 && formProgress > 0">
                        <div class="w-3 h-3 bg-yellow-500 rounded-full animate-pulse mr-2"></div>
                        <span class="text-sm font-medium text-yellow-600">Lengkapi form</span>
                    </div>
                    <div class="flex items-center" x-show="formProgress === 0">
                        <div class="w-3 h-3 bg-gray-400 rounded-full mr-2"></div>
                        <span class="text-sm font-medium text-gray-500">Mulai mengisi form</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function assetCreateForm() {
    return {
        formData: {
            nama: '',
            kode_asset: '',
            jenis: '',
            sub_kategori: '',
            merk: '',
            model: '',
            serial_number: '',
            tahun: '',
            nilai: '',
            lokasi: '',
            status: '',
            pic: '',
            project: '',
            keterangan: ''
        },
        formProgress: 0,
        showSubCategory: false,
        subCategories: [],
        years: [],

        init() {
            // Generate years (current year to 20 years ago)
            const currentYear = new Date().getFullYear();
            for (let i = currentYear; i >= currentYear - 20; i--) {
                this.years.push(i);
            }
            this.calculateProgress();
        },

        calculateProgress() {
            const requiredFields = ['nama', 'jenis', 'lokasi', 'status'];
            const optionalFields = ['kode_asset', 'merk', 'model', 'serial_number', 'tahun', 'nilai', 'pic', 'project', 'keterangan', 'sub_kategori'];

            let filledRequired = 0;
            let filledOptional = 0;

            // Check required fields
            requiredFields.forEach(field => {
                if (this.formData[field] && this.formData[field].trim() !== '') {
                    filledRequired++;
                }
            });

            // Check optional fields
            optionalFields.forEach(field => {
                if (this.formData[field] && this.formData[field].trim() !== '') {
                    filledOptional++;
                }
            });

            // Calculate progress (required fields = 70%, optional = 30%)
            const requiredProgress = (filledRequired / requiredFields.length) * 70;
            const optionalProgress = (filledOptional / optionalFields.length) * 30;

            this.formProgress = requiredProgress + optionalProgress;
        },

        updateSubCategories() {
            const categories = {
                'Kendaraan': ['Mobil', 'Motor', 'Truk', 'Bus', 'Alat Berat'],
                'Splicer': ['Fusion Splicer', 'Mechanical Splicer', 'OTDR', 'Power Meter', 'Light Source'],
                'IT Equipment': ['Laptop', 'Desktop', 'Server', 'Network Equipment', 'Printer', 'Scanner'],
                'Furniture': ['Meja', 'Kursi', 'Lemari', 'Rak', 'Sofa'],
                'Electronic': ['AC', 'TV', 'Proyektor', 'Sound System', 'Camera']
            };

            this.subCategories = categories[this.formData.jenis] || [];
            this.showSubCategory = this.subCategories.length > 0;
            this.formData.sub_kategori = '';
            this.calculateProgress();
        },

        formatNumber(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        },

        formatCurrency() {
            // Auto format currency while typing
            if (this.formData.nilai) {
                const number = parseInt(this.formData.nilai.toString().replace(/[^0-9]/g, ''));
                if (!isNaN(number)) {
                    this.formData.nilai = number;
                }
            }
        },

        validateForm(event) {
            const requiredFields = ['nama', 'jenis', 'lokasi', 'status'];
            const missingFields = [];

            requiredFields.forEach(field => {
                if (!this.formData[field] || this.formData[field].trim() === '') {
                    missingFields.push(field);
                }
            });

            if (missingFields.length > 0) {
                event.preventDefault();
                window.dispatchEvent(new CustomEvent('show-notification', {
                    detail: {
                        message: `Mohon lengkapi field yang wajib diisi: ${missingFields.join(', ')}`,
                        type: 'error'
                    }
                }));
                return false;
            }

            // Show success notification
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: {
                    message: 'Asset berhasil disimpan!',
                    type: 'success'
                }
            }));

            return true;
        },

        saveAsDraft() {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: {
                    message: 'Draft berhasil disimpan!',
                    type: 'info'
                }
            }));
        }
    }
}
</script>
@endpush
@endsection
