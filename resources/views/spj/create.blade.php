@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6">
    <h1 class="text-3xl font-semibold mb-6 text-gray-800">Pengajuan SPJ</h1>
      <form action="{{ route('spj.store') }}" method="POST" enctype="multipart/form-data" novalidate class="bg-white shadow-lg rounded-lg p-6 space-y-6"
          x-data="{ bastMutasiFiles: [], bastInventarisFiles: [], penugasanFiles: [], notaFiles: [] }">
        @csrf

        <!-- Section: Dokumen BAST -->
        <div class="border-b border-gray-200 pb-4">
            <h2 class="text-xl font-semibold text-gray-700">1. Dokumen BAST</h2>
        </div>
        <div class="space-y-4">
            <!-- BAST Mutasi -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Isi BAST jika mutasi/perpindahan site</label>
                <textarea name="bast_mutasi" rows="2" class="mt-1 block w-full form-textarea border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masukkan rinciannya...">{{ old('bast_mutasi') }}</textarea>
                <label class="mt-2 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm text-gray-700 cursor-pointer hover:bg-gray-50">
                    Pilih File
                    <input type="file" x-ref="bastMutasi" name="bast_mutasi_file" class="hidden" @change="bastMutasiFiles = Array.from($refs.bastMutasi.files).slice(0,1)"/>
                </label>
                <p class="text-xs text-gray-500 mt-1">Format file: PDF, JPG, PNG (max 5MB).</p>
                <div class="mt-2 space-y-1 text-sm text-gray-600" x-show="bastMutasiFiles.length">
                    <template x-for="(file,index) in bastMutasiFiles" :key="index">
                        <div x-text="file.name"></div>
                    </template>
                </div>
            </div>

            <!-- BAST Inventaris -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Isi BAST Aset dan Inventaris</label>
                <textarea name="bast_inventaris" rows="2" class="mt-1 block w-full form-textarea border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="Masukkan rinciannya...">{{ old('bast_inventaris') }}</textarea>
                <label class="mt-2 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm text-gray-700 cursor-pointer hover:bg-gray-50">
                    Pilih File
                    <input type="file" x-ref="bastInventaris" name="bast_inventaris_file" class="hidden" @change="bastInventarisFiles = Array.from($refs.bastInventaris.files).slice(0,1)"/>
                </label>
                <p class="text-xs text-gray-500 mt-1">Format file: PDF, JPG, PNG (max 5MB).</p>
                <div class="mt-2 space-y-1 text-sm text-gray-600" x-show="bastInventarisFiles.length">
                    <template x-for="(file,index) in bastInventarisFiles" :key="index">
                        <div x-text="file.name"></div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Section: Detail Perjalanan -->
        <div class="border-t border-gray-200 pt-6">
            <h2 class="text-xl font-semibold text-gray-700">2. Detail Perjalanan</h2>
        </div>
        <div class="space-y-4">
            <!-- Nama Pegawai -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Pegawai <span class="text-red-500">*</span></label>
                <input type="text" name="nama_pegawai" value="{{ old('nama_pegawai') }}" class="mt-1 block w-full form-input border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required placeholder="Masukkan nama pegawai" />
            </div>

            <!-- Keperluan -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Keperluan request SPJ <span class="text-red-500">*</span></label>
                <textarea name="keperluan" rows="2" class="mt-1 block w-full form-textarea border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required placeholder="Jelaskan keperluan perjalanan...">{{ old('keperluan') }}</textarea>
            </div>

            <!-- Lokasi Project -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Lokasi Project</label>
                <input type="text" name="lokasi_project" value="{{ old('lokasi_project') }}" class="mt-1 block w-full form-input border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: Project Alpha, Site B" />
            </div>

            <!-- Penugasan -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Siapa yang menugaskan? <span class="text-red-500">*</span></label>
                <input type="text" name="penugasan_by" value="{{ old('penugasan_by') }}" class="mt-1 block w-full form-input border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required placeholder="Nama penugas" />
                <label class="mt-2 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm text-gray-700 cursor-pointer hover:bg-gray-50">
                    Unggah Bukti Penugasan
                    <input type="file" x-ref="penugasanFile" name="bukti_penugasan_file" class="hidden" @change="penugasanFiles = Array.from($refs.penugasanFile.files).slice(0,1)"/>
                </label>
                <p class="text-xs text-gray-500 mt-1">Format file: PDF, JPG, PNG (max 5MB).</p>
                <div class="mt-2 text-sm text-gray-600" x-show="penugasanFiles.length">
                    <template x-for="(file,index) in penugasanFiles" :key="index">
                        <div x-text="file.name"></div>
                    </template>
                </div>
            </div>
            <!-- End Penugasan -->

            <!-- Section: Rute Perjalanan -->
            <div class="border-t border-gray-200 pt-6">
                <h2 class="text-xl font-semibold text-gray-700">2. Rute Perjalanan</h2>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <!-- Perjalanan Dari -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Dari <span class="text-red-500">*</span></label>
                    <input type="text" name="perjalanan_from" value="{{ old('perjalanan_from') }}" class="mt-1 block w-full form-input border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required placeholder="Contoh: Jakarta" />
                </div>

                <!-- Perjalanan Ke -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">Ke <span class="text-red-500">*</span></label>
                    <input type="text" name="perjalanan_to" value="{{ old('perjalanan_to') }}" class="mt-1 block w-full form-input border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required placeholder="Contoh: Bandung" />
                </div>
            </div>

            <!-- SPJ Tanggal -->
            <div>
                <label class="block text-sm font-medium text-gray-700">SPJ untuk kapan? <span class="text-red-500">*</span></label>
                <input type="date" name="spj_date" value="{{ old('spj_date') ? \Carbon\Carbon::parse(old('spj_date'))->format('Y-m-d') : '' }}" class="mt-1 block w-full form-input border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required />
            </div>

            <!-- Transportasi -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Menggunakan transportasi apa <span class="text-red-500">*</span></label>
                <input type="text" name="transportasi" value="{{ old('transportasi') }}" class="mt-1 block w-full form-input border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required placeholder="Contoh: Kereta, Mobil" />
            </div>
        </div>

        <!-- Section: Biaya & Nota -->
        <div class="border-t border-gray-200 pt-6">
            <h2 class="text-xl font-semibold text-gray-700">3. Biaya & Nota</h2>
        </div>
        <div class="space-y-4">
            <!-- Biaya Estimasi -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Estimasi biaya perjalanan <span class="text-red-500">*</span></label>
                <textarea name="biaya_estimasi" rows="3" class="mt-1 block w-full form-textarea border border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500" required placeholder="Rincian biaya, misal: tiket, transport">{{ old('biaya_estimasi') }}</textarea>
            </div>

            <!-- Nota -->
            <div x-data="{ files: [] }">
                <label class="block text-sm font-medium text-gray-700">Silakan dilampirkan nota (JIKA REIMBURSE)</label>
                <label class="mt-2 flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm text-gray-700 cursor-pointer hover:bg-gray-50">
                    Pilih Nota (max 10 file)
                    <input type="file" x-ref="nota" name="nota_files[]" multiple class="hidden"
                           @change="files = Array.from($refs.nota.files).slice(0,10)" />
                </label>
                <p class="text-xs text-gray-500 mt-1">Format file: PDF, JPG, PNG (max 5MB per file).</p>
                <div class="mt-2 space-y-1" x-show="files.length > 0">
                    <template x-for="(file, index) in files" :key="index">
                        <div class="text-sm text-gray-600" x-text="file.name"></div>
                    </template>
                </div>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full px-6 py-3 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700 transition">Kirim Pengajuan SPJ</button>
        </div>
    </form>
</div>
<!-- End form card -->
@endsection
