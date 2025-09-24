@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Buat Halaman Aset Baru</h2>

        <form action="{{ route('asset-pages.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <!-- Nama Halaman -->
                <div>
                    <label for="page_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Halaman</label>
                    <input type="text" name="name" id="page_name" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: Aset Elektronik" required>
                    <p class="text-xs text-gray-500 mt-1">Ini adalah judul yang akan muncul di menu navigasi.</p>
                </div>

                <!-- Tipe Aset -->
                <div>
                    <label for="asset_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe Aset untuk Ditampilkan</label>
                    <select name="asset_type" id="asset_type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <option value="">-- Pilih Tipe Aset --</option>
                        @foreach($assetTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Halaman baru akan menampilkan semua aset dengan tipe yang dipilih.</p>
                </div>

                <!-- Icon -->
                <div>
                    <label for="icon" class="block text-sm font-medium text-gray-700 mb-1">Ikon Menu (Font Awesome)</label>
                    <input type="text" name="icon" id="icon" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Contoh: fas fa-laptop">
                    <p class="text-xs text-gray-500 mt-1">Gunakan kelas dari Font Awesome. Contoh: `fas fa-car`, `fas fa-tools`, `fas fa-laptop`.</p>
                </div>

                <!-- Chart Configuration -->
                <div x-data="chartConfigurator()" class="border-t border-gray-200 pt-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Konfigurasi Grafik</h3>
                            <p class="text-sm text-gray-500">Tambahkan dan atur grafik yang akan ditampilkan di halaman.</p>
                        </div>
                        <button @click="addChart" type="button" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg text-sm">
                            <i class="fas fa-plus mr-2"></i>Tambah Grafik
                        </button>
                    </div>

                    <div class="space-y-4">
                        <template x-for="(chart, index) in charts" :key="index">
                            <div class="p-4 border border-gray-200 rounded-lg bg-gray-50 relative">
                                <h4 class="font-bold text-gray-700 mb-3" x-text="'Grafik #' + (index + 1)"></h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Judul Grafik</label>
                                        <input type="text" :name="'charts[' + index + '][title]'" x-model="chart.title" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm" placeholder="Contoh: Aset per Lokasi" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Grafik</label>
                                        <select :name="'charts[' + index + '][type]'" x-model="chart.type" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm" required>
                                            <option value="doughnut">Doughnut</option>
                                            <option value="pie">Pie</option>
                                            <option value="bar">Bar</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Data Berdasarkan</label>
                                        <select :name="'charts[' + index + '][group_by]'" x-model="chart.group_by" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm" required>
                                            <option value="pic">PIC</option>
                                            <option value="project">Project</option>
                                            <option value="lokasi">Lokasi</option>
                                            <option value="merk">Merk</option>
                                            <option value="status">Status</option>
                                        </select>
                                    </div>
                                </div>
                                <button @click="removeChart(index)" type="button" class="absolute top-2 right-2 text-gray-400 hover:text-red-500">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                     <input type="hidden" name="chart_config" :value="JSON.stringify(charts)">
                </div>

            </div>

            <div class="mt-8 flex justify-end">
                <a href="{{ route('asset-pages.index') }}" class="px-6 py-3 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 transition-colors font-semibold mr-4">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors font-semibold">
                    Simpan dan Buat Halaman
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function chartConfigurator() {
    return {
        charts: [],
        addChart() {
            this.charts.push({
                title: '',
                type: 'doughnut',
                group_by: 'pic'
            });
        },
        removeChart(index) {
            this.charts.splice(index, 1);
        }
    }
}
</script>
@endsection
