@extends('layouts.app')

@section('title', 'Advanced Search')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8" x-data="advancedSearch()">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Advanced Search</h1>
            <p class="text-gray-600 mt-2">Pencarian lanjutan dengan filter lengkap</p>
        </div>

        <!-- Search Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-search text-blue-600 mr-2"></i>
                    Search Filters
                </h3>
            </div>
            <div class="p-6">
                <form @submit.prevent="performSearch">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Text Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keyword</label>
                            <input type="text" x-model="filters.keyword"
                                   placeholder="Search by name, serial, etc..."
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Asset Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Asset</label>
                            <select x-model="filters.jenis_aset" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Jenis</option>
                                <option value="Laptop">Laptop</option>
                                <option value="PC">PC</option>
                                <option value="Monitor">Monitor</option>
                                <option value="Printer">Printer</option>
                                <option value="Network Equipment">Network Equipment</option>
                            </select>
                        </div>

                        <!-- Condition -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Kondisi</label>
                            <select x-model="filters.kondisi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Kondisi</option>
                                <option value="Baik">Baik</option>
                                <option value="Rusak">Rusak</option>
                                <option value="Perlu Perbaikan">Perlu Perbaikan</option>
                            </select>
                        </div>

                        <!-- Location -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                            <select x-model="filters.lokasi" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Semua Lokasi</option>
                                <option value="Jakarta">Jakarta</option>
                                <option value="Bandung">Bandung</option>
                                <option value="Surabaya">Surabaya</option>
                                <option value="Medan">Medan</option>
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Minimal</label>
                            <input type="number" x-model="filters.harga_min" placeholder="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Maksimal</label>
                            <input type="number" x-model="filters.harga_max" placeholder="999999999"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Date Range -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Beli Dari</label>
                            <input type="date" x-model="filters.tanggal_dari"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Beli Sampai</label>
                            <input type="date" x-model="filters.tanggal_sampai"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- PIC -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">PIC</label>
                            <input type="text" x-model="filters.pic" placeholder="Person in charge"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Search Actions -->
                    <div class="mt-8 flex flex-wrap gap-4">
                        <button type="submit"
                                class="inline-flex items-center px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            Search Assets
                        </button>
                        <button type="button" @click="clearFilters()"
                                class="inline-flex items-center px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                            <i class="fas fa-times mr-2"></i>
                            Clear Filters
                        </button>
                        <button type="button" @click="saveSearch()"
                                class="inline-flex items-center px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-bookmark mr-2"></i>
                            Save Search
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Saved Searches -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-bookmark text-green-600 mr-2"></i>
                    Saved Searches
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-medium text-gray-900">Laptop Jakarta</h4>
                                <p class="text-sm text-gray-500">Semua laptop di Jakarta</p>
                            </div>
                            <button class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-medium text-gray-900">Assets Rusak</h4>
                                <p class="text-sm text-gray-500">Semua asset dengan kondisi rusak</p>
                            </div>
                            <button class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-medium text-gray-900">High Value Assets</h4>
                                <p class="text-sm text-gray-500">Assets dengan harga > 10 juta</p>
                            </div>
                            <button class="text-red-600 hover:text-red-900">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search Results -->
        <div x-show="showResults" class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-list text-purple-600 mr-2"></i>
                    Search Results
                    <span x-text="`(${searchResults.length} found)`" class="ml-2 text-sm text-gray-500"></span>
                </h3>
            </div>
            <div class="p-6">
                <div x-show="searchResults.length === 0" class="text-center py-8">
                    <i class="fas fa-search text-gray-300 text-4xl mb-4"></i>
                    <p class="text-gray-500">No assets found matching your criteria</p>
                </div>

                <div x-show="searchResults.length > 0" class="space-y-4">
                    <template x-for="asset in searchResults" :key="asset.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900" x-text="asset.merk"></h4>
                                    <p class="text-sm text-gray-600" x-text="asset.jenis_aset"></p>
                                    <div class="flex items-center space-x-4 mt-2 text-sm text-gray-500">
                                        <span x-text="`Location: ${asset.lokasi}`"></span>
                                        <span x-text="`Condition: ${asset.kondisi}`"></span>
                                        <span x-text="`Price: Rp ${asset.harga_beli?.toLocaleString()}`"></span>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function advancedSearch() {
    return {
        showResults: false,
        filters: {
            keyword: '',
            jenis_aset: '',
            kondisi: '',
            lokasi: '',
            harga_min: '',
            harga_max: '',
            tanggal_dari: '',
            tanggal_sampai: '',
            pic: ''
        },
        searchResults: [],

        performSearch() {
            // This would make an AJAX call to search endpoint
            this.showResults = true;

            // Mock data for demonstration
            this.searchResults = [
                {
                    id: 1,
                    merk: 'Dell Latitude 5520',
                    jenis_aset: 'Laptop',
                    lokasi: 'Jakarta',
                    kondisi: 'Baik',
                    harga_beli: 15000000
                },
                {
                    id: 2,
                    merk: 'HP LaserJet Pro',
                    jenis_aset: 'Printer',
                    lokasi: 'Bandung',
                    kondisi: 'Baik',
                    harga_beli: 3500000
                }
            ];

            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: {
                    message: `Found ${this.searchResults.length} assets matching your criteria`,
                    type: 'success'
                }
            }));
        },

        clearFilters() {
            this.filters = {
                keyword: '',
                jenis_aset: '',
                kondisi: '',
                lokasi: '',
                harga_min: '',
                harga_max: '',
                tanggal_dari: '',
                tanggal_sampai: '',
                pic: ''
            };
            this.showResults = false;
            this.searchResults = [];
        },

        saveSearch() {
            // This would save the current search criteria
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: {
                    message: 'Search criteria saved successfully!',
                    type: 'success'
                }
            }));
        }
    }
}
</script>
@endpush
@endsection
