@extends('layouts.app')

@section('content')
<div class="p-6 space-y-6">
    <h1 class="text-3xl font-bold text-center text-gray-800">Edit Halaman Aset</h1>

    <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-xl p-8">
        <form action="{{ route('asset-pages.update', $page->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama Halaman</label>
                <input type="text" id="name" name="name" value="{{ old('name', $page->name) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="asset_type" class="block mb-2 text-sm font-medium text-gray-900">Tipe Aset</label>
                <select id="asset_type" name="asset_type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                    @foreach($assetTypes as $type)
                        <option value="{{ $type }}" @if(old('asset_type', $page->asset_type) == $type) selected @endif>{{ $type }}</option>
                    @endforeach
                </select>
                 @error('asset_type')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="icon" class="block mb-2 text-sm font-medium text-gray-900">Ikon (Contoh: fas fa-cogs)</label>
                <input type="text" id="icon" name="icon" value="{{ old('icon', $page->icon) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <p class="mt-2 text-sm text-gray-500">Opsional. Lihat <a href="https://fontawesome.com/v5/search" target="_blank" class="text-blue-600 hover:underline">Font Awesome</a> untuk daftar ikon.</p>
                @error('icon')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Chart Configuration -->
            <script>
                // Initial chart configuration for Alpine
                window.initialCharts = @json(old('chart_config', $page->chart_config) ?? []);
            </script>
            <div x-data="{
                charts: window.initialCharts,
                addChart() {
                    this.charts.push({ title: '', type: 'doughnut', group_by: 'pic' });
                },
                removeChart(index) {
                    this.charts.splice(index, 1);
                }
            }" class="border-t border-gray-200 pt-6 mt-6">
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

            <div class="flex justify-end mt-8">
                <a href="{{ route('asset-pages.index') }}" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mr-2 mb-2">Batal</a>
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@endsection
