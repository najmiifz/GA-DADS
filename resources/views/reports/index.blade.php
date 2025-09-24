@extends('layouts.app')

@section('title', 'Reports & Analytics')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8" x-data="reportsAnalytics()">
        <!-- Header -->
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Reports & Analytics</h1>
                <p class="text-gray-600 mt-2">Laporan komprehensif dan analisis data asset</p>
            </div>
            <div class="space-x-3">
                <button @click="generateReport()"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Generate Report
                </button>
                <button @click="exportAllReports()"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Export All
                </button>
            </div>
        </div>

        <!-- Report Categories -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Asset Summary</h3>
                        <p class="text-sm text-gray-600">Overview laporan</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-chart-pie text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <button @click="showReport('summary')"
                            class="w-full text-left px-4 py-2 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                        View Summary Report
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Financial Report</h3>
                        <p class="text-sm text-gray-600">Laporan keuangan</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <button @click="showReport('financial')"
                            class="w-full text-left px-4 py-2 text-green-600 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
                        View Financial Report
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Condition Report</h3>
                        <p class="text-sm text-gray-600">Status kondisi asset</p>
                    </div>
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <button @click="showReport('condition')"
                            class="w-full text-left px-4 py-2 text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
                        View Condition Report
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Location Report</h3>
                        <p class="text-sm text-gray-600">Sebaran per lokasi</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-map-marker-alt text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <button @click="showReport('location')"
                            class="w-full text-left px-4 py-2 text-purple-600 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                        View Location Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Report Display Area -->
        <div x-show="currentReport !== null" class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                        <span x-text="reportTitles[currentReport]"></span>
                    </h3>
                    <div class="space-x-2">
                        <button @click="exportReport()"
                                class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                            <i class="fas fa-download mr-2"></i>
                            Export
                        </button>
                        <button @click="currentReport = null"
                                class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700">
                            <i class="fas fa-times mr-2"></i>
                            Close
                        </button>
                    </div>
                </div>
            </div>

            <!-- Summary Report -->
            <div x-show="currentReport === 'summary'" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-blue-600">124</div>
                        <div class="text-sm text-gray-600">Total Assets</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-green-600">89%</div>
                        <div class="text-sm text-gray-600">Condition Good</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-purple-600">8</div>
                        <div class="text-sm text-gray-600">Locations</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-orange-600">Rp 2.1B</div>
                        <div class="text-sm text-gray-600">Total Value</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <h4 class="text-lg font-semibold mb-4">Asset Distribution by Type</h4>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Laptops</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: 45%"></div>
                                    </div>
                                    <span class="text-sm font-medium">45%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">PC Desktop</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: 30%"></div>
                                    </div>
                                    <span class="text-sm font-medium">30%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Printers</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-yellow-600 h-2 rounded-full" style="width: 15%"></div>
                                    </div>
                                    <span class="text-sm font-medium">15%</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-600">Others</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-32 bg-gray-200 rounded-full h-2">
                                        <div class="bg-purple-600 h-2 rounded-full" style="width: 10%"></div>
                                    </div>
                                    <span class="text-sm font-medium">10%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold mb-4">Recent Trends</h4>
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg">
                                <i class="fas fa-arrow-up text-green-600"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Asset Acquisition</div>
                                    <div class="text-sm text-gray-600">+12% this quarter</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                                <i class="fas fa-arrow-right text-blue-600"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Maintenance Rate</div>
                                    <div class="text-sm text-gray-600">Stable at 3%</div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3 p-3 bg-yellow-50 rounded-lg">
                                <i class="fas fa-arrow-down text-yellow-600"></i>
                                <div>
                                    <div class="font-medium text-gray-900">Replacement Needed</div>
                                    <div class="text-sm text-gray-600">-5% from last month</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial Report -->
            <div x-show="currentReport === 'financial'" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="text-center p-6 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">Rp 2.1B</div>
                        <div class="text-sm text-gray-600">Total Asset Value</div>
                    </div>
                    <div class="text-center p-6 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">Rp 180M</div>
                        <div class="text-sm text-gray-600">This Year Purchases</div>
                    </div>
                    <div class="text-center p-6 bg-orange-50 rounded-lg">
                        <div class="text-2xl font-bold text-orange-600">Rp 45M</div>
                        <div class="text-sm text-gray-600">Maintenance Costs</div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Count</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Average Price</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">Laptops</td>
                                <td class="px-6 py-4 whitespace-nowrap">56</td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp 945M</td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp 16.9M</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">Desktop PCs</td>
                                <td class="px-6 py-4 whitespace-nowrap">37</td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp 629M</td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp 17.0M</td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">Printers</td>
                                <td class="px-6 py-4 whitespace-nowrap">19</td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp 266M</td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp 14.0M</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Condition Report -->
            <div x-show="currentReport === 'condition'" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-6 bg-green-50 rounded-lg">
                        <div class="text-3xl font-bold text-green-600">110</div>
                        <div class="text-sm text-gray-600">Good Condition</div>
                        <div class="text-xs text-gray-500">89% of total</div>
                    </div>
                    <div class="text-center p-6 bg-yellow-50 rounded-lg">
                        <div class="text-3xl font-bold text-yellow-600">10</div>
                        <div class="text-sm text-gray-600">Need Repair</div>
                        <div class="text-xs text-gray-500">8% of total</div>
                    </div>
                    <div class="text-center p-6 bg-red-50 rounded-lg">
                        <div class="text-3xl font-bold text-red-600">4</div>
                        <div class="text-sm text-gray-600">Damaged</div>
                        <div class="text-xs text-gray-500">3% of total</div>
                    </div>
                </div>
            </div>

            <!-- Location Report -->
            <div x-show="currentReport === 'location'" class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asset Count</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Condition Distribution</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">Jakarta</td>
                                <td class="px-6 py-4 whitespace-nowrap">45</td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp 756M</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-1">
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">40 Good</span>
                                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">4 Repair</span>
                                        <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">1 Damaged</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-medium">Bandung</td>
                                <td class="px-6 py-4 whitespace-nowrap">38</td>
                                <td class="px-6 py-4 whitespace-nowrap">Rp 642M</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex space-x-1">
                                        <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">35 Good</span>
                                        <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">2 Repair</span>
                                        <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">1 Damaged</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Report Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <button @click="scheduleReport()"
                        class="flex items-center justify-center px-4 py-3 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200">
                    <i class="fas fa-clock mr-2"></i>
                    Schedule Reports
                </button>
                <button @click="customReport()"
                        class="flex items-center justify-center px-4 py-3 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200">
                    <i class="fas fa-cog mr-2"></i>
                    Custom Report
                </button>
                <button @click="emailReport()"
                        class="flex items-center justify-center px-4 py-3 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200">
                    <i class="fas fa-envelope mr-2"></i>
                    Email Report
                </button>
                <button @click="archiveReports()"
                        class="flex items-center justify-center px-4 py-3 bg-white text-gray-700 rounded-lg hover:bg-gray-50 transition-colors border border-gray-200">
                    <i class="fas fa-archive mr-2"></i>
                    Report Archive
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function reportsAnalytics() {
    return {
        currentReport: null,
        reportTitles: {
            'summary': 'Asset Summary Report',
            'financial': 'Financial Analysis Report',
            'condition': 'Asset Condition Report',
            'location': 'Location Distribution Report'
        },

        showReport(type) {
            this.currentReport = type;
        },

        generateReport() {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: { message: 'Generating comprehensive report...', type: 'info' }
            }));
        },

        exportReport() {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: { message: 'Report exported successfully!', type: 'success' }
            }));
        },

        exportAllReports() {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: { message: 'All reports export started!', type: 'info' }
            }));
        },

        scheduleReport() {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: { message: 'Report scheduling feature coming soon!', type: 'info' }
            }));
        },

        customReport() {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: { message: 'Custom report builder coming soon!', type: 'info' }
            }));
        },

        emailReport() {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: { message: 'Email report feature coming soon!', type: 'info' }
            }));
        },

        archiveReports() {
            window.dispatchEvent(new CustomEvent('show-notification', {
                detail: { message: 'Report archive feature coming soon!', type: 'info' }
            }));
        }
    }
}
</script>
@endpush
@endsection
