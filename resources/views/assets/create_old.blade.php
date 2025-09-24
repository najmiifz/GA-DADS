@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h1 class="text-2xl font-bold mb-6 text-gray-900">Tambah Asset Baru</h1>

                <form method="POST" action="{{ route('assets.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Tipe -->
                        <div>
                            <label for="tipe" class="block text-sm font-medium text-gray-700">Tipe Asset</label>
                            <select name="tipe" id="tipe" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                                <option value="">-- Pilih Tipe --</option>
                                <option value="Terpakai">Terpakai</option>
                                <option value="Tersedia">Tersedia</option>
                                <option value="Kendaraan">Kendaraan</option>
                                <option value="Splicer">Splicer</option>
                            </select>
                        </div>

                        <!-- Jenis Asset -->
                        <div>
                            <label for="jenis_aset" class="block text-sm font-medium text-gray-700">Jenis Asset</label>
                            <input type="text" name="jenis_aset" id="jenis_aset" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: Laptop, Mobil, Fusion Splicer" required>
                        </div>

                        <!-- Merk -->
                        <div>
                            <label for="merk" class="block text-sm font-medium text-gray-700">Merk</label>
                            <input type="text" name="merk" id="merk" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: Toyota, Dell, Fujikura">
                        </div>

                        <!-- PIC -->
                        <div>
                            <label for="pic" class="block text-sm font-medium text-gray-700">PIC (Person in Charge)</label>
                            <input type="text" name="pic" id="pic" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nama PIC">
                        </div>

                        <!-- Project -->
                        <div>
                            <label for="project" class="block text-sm font-medium text-gray-700">Project</label>
                            <input type="text" name="project" id="project" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Nama Project">
                        </div>

                        <!-- Lokasi -->
                        <div>
                            <label for="lokasi" class="block text-sm font-medium text-gray-700">Lokasi</label>
                            <input type="text" name="lokasi" id="lokasi" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="Contoh: Jakarta Pusat">
                        </div>

                        <!-- Tanggal Beli -->
                        <div>
                            <label for="tanggal_beli" class="block text-sm font-medium text-gray-700">Tanggal Beli</label>
                            <input type="date" name="tanggal_beli" id="tanggal_beli" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md" value="{{ old('tanggal_beli', '') }}">
                        </div>

                        <!-- Harga Beli -->
                        <div>
                            <label for="harga_beli" class="block text-sm font-medium text-gray-700">Harga Beli (Rp)</label>
                            <input type="number" name="harga_beli" id="harga_beli" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="0" min="0" step="1000">
                        </div>

                        <!-- Harga Sewa -->
                        <div>
                            <label for="harga_sewa" class="block text-sm font-medium text-gray-700">Harga Sewa (Rp)</label>
                            <input type="number" name="harga_sewa" id="harga_sewa" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="0" min="0" step="1000">
                        </div>

                        <!-- Status Pajak (untuk kendaraan) -->
                        <div>
                            <label for="status_pajak" class="block text-sm font-medium text-gray-700">Status Pajak (Kendaraan)</label>
                            <select name="status_pajak" id="status_pajak" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">-- Pilih Status --</option>
                                <option value="Lunas">Lunas</option>
                                <option value="Belum Lunas">Belum Lunas</option>
                                <option value="Tidak Lengkap">Tidak Lengkap</option>
                            </select>
                        </div>

                        <!-- Total Servis -->
                        <div>
                            <label for="total_servis" class="block text-sm font-medium text-gray-700">Total Servis</label>
                            <input type="number" name="total_servis" id="total_servis" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" placeholder="0" min="0" value="0">
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end space-x-4">
                        <a href="{{ route('assets.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Simpan Asset
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
