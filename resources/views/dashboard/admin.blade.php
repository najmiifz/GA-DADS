@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-8">
        <!-- Welcome Banner -->
        <div class="mb-6">
            <div class="bg-gradient-to-r from-red-600 to-red-800 rounded-xl shadow-xl p-6 text-white overflow-hidden">
                <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="mb-4 sm:mb-0">
                        <h1 class="text-3xl font-bold">Selamat datang, {{ auth()->user()->name }}</h1>
                        <p class="text-indigo-200 mt-1">Role: {{ ucfirst(auth()->user()->role) }}</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <p class="text-xl">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Total Asset -->
            <div class="group relative overflow-hidden rounded-xl shadow-lg transform transition hover:scale-105" style="background: linear-gradient(135deg, #F87171, #DC2626);">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-white bg-opacity-30">
                        <i class="fas fa-box text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm font-medium text-white">Total Asset</h2>
                        <p class="text-2xl font-semibold text-white">{{ $totalAssets }}</p>
                    </div>
                </div>
                <div class="absolute bottom-4 right-4 opacity-20 text-white text-6xl">
                    <i class="fas fa-box"></i>
                </div>
            </div>
            <!-- Total PIC -->
            <div class="group relative overflow-hidden rounded-xl shadow-lg transform transition hover:scale-105" style="background: linear-gradient(135deg, #F87171, #DC2626);">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-white bg-opacity-30">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm font-medium text-white">Total PIC</h2>
                        <p class="text-2xl font-semibold text-white">{{ $totalUsers }}</p>
                    </div>
                </div>
                <div class="absolute bottom-4 right-4 opacity-20 text-white text-6xl">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <!-- Total Nilai -->
            <div class="group relative overflow-hidden rounded-xl shadow-lg transform transition hover:scale-105" style="background: linear-gradient(135deg, #F87171, #DC2626);">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-white bg-opacity-30">
                        <i class="fas fa-dollar-sign text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm font-medium text-white">Total Nilai</h2>
                        <p class="text-2xl font-semibold text-white">Rp {{ number_format($totalNilai, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="absolute bottom-4 right-4 opacity-20 text-white text-6xl">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <!-- Projects -->
            <div class="group relative overflow-hidden rounded-xl shadow-lg transform transition hover:scale-105" style="background: linear-gradient(135deg, #F87171, #DC2626);">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-white bg-opacity-30">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm font-medium text-white">Projects</h2>
                        <p class="text-2xl font-semibold text-white">{{ count($projectData) }}</p>
                    </div>
                </div>
                <div class="absolute bottom-4 right-4 opacity-20 text-white text-6xl">
                    <i class="fas fa-project-diagram"></i>
                </div>
            </div>
            <!-- Total Pengajuan Service -->
            <div class="group relative overflow-hidden rounded-xl shadow-lg transform transition hover:scale-105" style="background: linear-gradient(135deg, #F87171, #DC2626);">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-white bg-opacity-30">
                        <i class="fas fa-file-alt text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm font-medium text-white">Pengajuan Service Pending</h2>
                        <p class="text-2xl font-semibold text-white">{{ $pendingServiceRequests }}</p>
                    </div>
                </div>
                <div class="absolute bottom-4 right-4 opacity-20 text-white text-6xl">
                    <i class="fas fa-cogs"></i>
                </div>
            </div>
            <!-- Total Pengajuan APD -->
            <div class="group relative overflow-hidden rounded-xl shadow-lg transform transition hover:scale-105" style="background: linear-gradient(135deg, #F87171, #DC2626);">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-white bg-opacity-30">
                        <i class="fas fa-hard-hat text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm font-medium text-white">Pengajuan APD Pending</h2>
                        <p class="text-2xl font-semibold text-white">{{ $totalApdRequests }}</p>
                    </div>
                </div>
                <div class="absolute bottom-4 right-4 opacity-20 text-white text-6xl">
                    <i class="fas fa-hard-hat"></i>
                </div>
            </div>
            <!-- Total Pengajuan Reimburse -->
            <div class="group relative overflow-hidden rounded-xl shadow-lg transform transition hover:scale-105" style="background: linear-gradient(135deg, #F87171, #DC2626);">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-white bg-opacity-30">
                        <i class="fas fa-receipt text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm font-medium text-white">Pengajuan Reimburse Pending</h2>
                        <p class="text-2xl font-semibold text-white">{{ $totalReimburseRequests }}</p>
                    </div>
                </div>
                <div class="absolute bottom-4 right-4 opacity-20 text-white text-6xl">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>

            <!-- Pengajuan SPJ Pending -->
            <div class="group relative overflow-hidden rounded-xl shadow-lg transform transition hover:scale-105" style="background: linear-gradient(135deg, #F87171, #DC2626);">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-white bg-opacity-30">
                        <i class="fas fa-file-alt text-white text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-sm font-medium text-white">Pengajuan SPJ Pending</h2>
                        <p class="text-2xl font-semibold text-white">{{ $totalSpjRequests }}</p>
                    </div>
                </div>
                <div class="absolute bottom-4 right-4 opacity-20 text-white text-6xl">
                    <i class="fas fa-file-alt"></i>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Tambah Asset Baru -->
                    <a href="{{ route('assets.create') }}"
                        class="group relative overflow-hidden rounded-xl shadow-lg transform transition hover:scale-105"
                        style="background: linear-gradient(135deg, #F87171, #DC2626);">
                    <div class="p-6 flex items-center">
                        <i class="fas fa-plus text-white text-3xl mr-4"></i>
                        <span class="text-white font-semibold text-lg">Tambah Asset Baru</span>
                    </div>
                    <div class="absolute bottom-4 right-4 opacity-20 text-white text-6xl">
                        <i class="fas fa-box"></i>
                    </div>
                </a>

                    <!-- Kelola Asset -->
                    <a href="{{ route('assets.index') }}"
                        class="group relative overflow-hidden rounded-xl shadow-lg transform transition hover:scale-105"
                        style="background: linear-gradient(135deg, #F87171, #DC2626);">
                    <div class="p-6 flex items-center">
                        <i class="fas fa-list text-white text-3xl mr-4"></i>
                        <span class="text-white font-semibold text-lg">Kelola Asset</span>
                    </div>
                    <div class="absolute bottom-4 right-4 opacity-20 text-white text-6xl">
                        <i class="fas fa-box-open"></i>
                    </div>
                </a>

                    <!-- Kelola PIC -->
                    <a href="{{ route('users.index') }}"
                        class="group relative overflow-hidden rounded-xl shadow-lg transform transition hover:scale-105"
                        style="background: linear-gradient(135deg, #F87171, #DC2626);">
                    <div class="p-6 flex items-center">
                        <i class="fas fa-user-cog text-white text-3xl mr-4"></i>
                        <span class="text-white font-semibold text-lg">Kelola PIC</span>
                    </div>
                    <div class="absolute bottom-4 right-4 opacity-20 text-white text-6xl">
                        <i class="fas fa-users"></i>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>


@endsection
