<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>GA-DADS Asset Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.12.0/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- jQuery (required for Select2) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Choices.js CSS (searchable select) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <!-- Choices.js JS -->
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden !important;
            width: 100vw;
            max-width: 100vw;
        }
        html {
            overflow-x: hidden !important;
            width: 100vw;
            max-width: 100vw;
        }
        .container, .max-w-7xl, .w-full {
            max-width: 100%;
            overflow-x: hidden;
        }
        .nav-link {
            transition: all 0.2s ease-in-out;
        }
        .nav-link.active {
            background-color: #dc2626; /* red-600 */
            color: white;
        }
        .nav-link:not(.active):hover {
            background-color: #fecaca; /* red-200 */
            color: #991b1b;
        }
        .btn-action {
            transition: all 0.2s ease-in-out;
        }
        .btn-action:active {
            transform: scale(0.95);
        }
        .sortable {
            cursor: pointer;
            user-select: none;
        }
        .sortable:hover {
            background-color: #f3f4f6;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #fca5a5;
        }

        /* Mobile touch improvements */
        @media (max-width: 768px) {
            .btn, button, .nav-link {
                min-height: 44px; /* iOS touch target size */
                min-width: 44px;
            }

            /* Larger tap targets for mobile */
            .mobile-tap-target {
                padding: 12px 16px;
                font-size: 16px; /* Prevent zoom on iOS */
            }

            /* Better mobile form inputs */
            input, select, textarea {
                font-size: 16px; /* Prevent zoom on iOS */
                padding: 12px;
            }

            /* Mobile card spacing */
            .mobile-card {
                margin: 8px 0;
                border-radius: 12px;
            }

            /* Improved mobile table */
            .mobile-table-card {
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                border-radius: 8px;
                margin-bottom: 12px;
                padding: 16px;
            }
        }

        /* Smooth scrolling for mobile */
        html {
            scroll-behavior: smooth;
        }

        /* Hide horizontal scrollbar on mobile */
        @media (max-width: 768px) {
            body {
                overflow-x: hidden;
            }

            /* Improve mobile button interactions */
            .btn:active, button:active, .nav-link:active {
                transform: scale(0.98);
                transition: transform 0.1s;
            }

            /* Better mobile form focus states */
            input:focus, select:focus, textarea:focus {
                border-color: #ef4444;
                box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
                outline: none;
            }

            /* Mobile-friendly card hover states */
            .mobile-card:active {
                transform: scale(0.98);
                transition: transform 0.1s;
            }

            /* Ensure dropdown menus work well on mobile */
            .dropdown-menu {
                max-height: 70vh;
                overflow-y: auto;
            }

            /* Improve sidebar visibility on mobile */
            #sidebar {
                background: white !important;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
                border-right: 1px solid #e5e7eb;
            }

            #sidebar nav {
                background: white !important;
            }

            /* Prevent body scroll when sidebar is open */
            body.overflow-hidden {
                overflow: hidden;
            }
        }

        /* Ensure form dropdowns have proper z-index */
        select {
            position: relative;
            z-index: 10;
        }

        /* Fix dropdown z-index conflicts and style */
        /* Style Choices.js dropdown list for single-selects */
        .choices__list--dropdown.is-active {
            z-index: 1000 !important;
            width: 100% !important; /* match select width */
            max-height: 300px !important; /* Increase height limit */
            overflow-y: auto !important;
        }
        /* Hide the search input inside the dropdown for single-select */
        .choices__list--dropdown .choices__input {
            display: none !important;
        }

        /* Ensure form elements are above mobile overlay when needed */
        form select,
        form .choices {
            position: relative;
            z-index: 50;
        }
    </style>
</head>

<body class="bg-gray-100">

    <!-- Main Application Container -->
    <div class="relative min-h-screen md:flex">
    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-white shadow-xl flex flex-col h-screen flex-shrink-0 fixed md:fixed inset-y-0 left-0 z-40 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out border-r border-gray-200">
        <!-- Mobile Close Button and Logo (hidden for user 'baccan') -->
        @php
            $currentUser = auth()->user();
            $isBaccan = false;
            if ($currentUser) {
                $username = $currentUser->username ?? '';
                $name = $currentUser->name ?? '';
                $email = $currentUser->email ?? '';
                if (
                    (is_string($username) && stripos($username, 'baccan') !== false) ||
                    (is_string($name) && stripos($name, 'baccan') !== false) ||
                    (is_string($email) && stripos($email, 'baccan') !== false)
                ) {
                    $isBaccan = true;
                }
            }
        @endphp

        @unless($isBaccan)
            <div class="md:hidden flex items-center justify-between p-4 border-b border-gray-200 bg-red-600">
                <span class="text-white font-semibold">Menu</span>
                <button id="mobile-close-button" class="text-white hover:text-gray-200 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <div class="p-4 md:p-6 bg-white border-b border-gray-100">
                <div class="inline-block bg-white rounded-lg mx-auto flex flex-col items-center justify-center shadow-sm py-4 px-2">
                    <img src="{{ asset('images/logo-dads.png') }}" alt="GA-DADS Logo" class="w-16 md:w-24 mb-2">
                    <span class="mt-2 text-xl md:text-2xl font-bold text-red-600 text-center whitespace-nowrap">GA-DADS</span>
                </div>
            </div>
        @endunless

        <nav class="mt-4 md:mt-6 px-3 md:px-4 flex-1 overflow-y-auto custom-scrollbar bg-white">
            <a href="{{ route('dashboard') }}"
               class="nav-link flex items-center px-3 md:px-4 py-3 mt-1 md:mt-2 text-gray-600 font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt w-5 text-center mr-3"></i>
                <span class="text-sm md:text-base">Dashboard</span>
            </a>

            {{-- Aktivitas Anda (all users) --}}
            <a href="{{ route('activity.logs') }}"
               class="nav-link flex items-center px-3 md:px-4 py-3 mt-1 md:mt-2 text-gray-600 font-medium rounded-lg {{ request()->routeIs('activity-logs') || request()->routeIs('activity.logs') ? 'active' : '' }}">
                <i class="fas fa-list w-5 text-center mr-3"></i>
                <span class="text-sm md:text-base">Daftar Pengajuan</span>
            </a>

            {{-- Notifikasi --}}
            <a href="{{ route('notifications.index') }}"
               class="nav-link flex items-center px-3 md:px-4 py-3 mt-1 md:mt-2 text-gray-600 font-medium rounded-lg {{ request()->routeIs('notifications.index') ? 'active' : '' }}">
                <i class="fas fa-bell w-5 text-center mr-3"></i>
                <span class="text-sm md:text-base flex items-center">
                    Notifikasi
                    @php $unread = auth()->user()->unreadNotifications->count(); @endphp
                    @if($unread)
                        <span class="ml-2 inline-block bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $unread }}</span>
                    @endif
                </span>
            </a>

                @if(auth()->user()->role === 'admin')
                <a href="{{ route('assets.vehicles') }}"
                   class="nav-link mobile-tap-target flex items-center px-3 md:px-4 py-3 mt-1 md:mt-2 text-gray-600 font-medium rounded-lg {{ request()->routeIs('assets.vehicles') ? 'active' : '' }}">
                    <i class="fas fa-car w-5 text-center mr-3"></i>
                    <span class="text-sm md:text-base">Kendaraan</span>
                </a>
                {{-- Splicers link removed --}}
                @endif

                {{-- Custom Asset Pages (hanya untuk admin) --}}
                @if(auth()->user()->role === 'admin')
                @if(isset($customPages))
                    @foreach($customPages as $page)
                        <a href="{{ route('asset-pages.show', $page->slug) }}"
                           class="nav-link flex items-center px-4 py-2 mt-2 text-gray-600 font-medium rounded-lg {{ request()->is('halaman/' . $page->slug) ? 'active' : '' }}">
                            <i class="{{ $page->icon ?? 'fas fa-file-alt' }} w-5 text-center mr-3"></i>
                            {{ $page->name }}
                        </a>
                    @endforeach
                @endif
                @endif

                {{-- Divider --}}
                <hr class="my-4 border-gray-200">

                @if(auth()->user()->role === 'admin')
                    {{-- Pengajuan PIC Dropdown untuk Admin --}}
                    @php
                        $picActive = request()->routeIs([
                            'service-requests.*',
                            'apd-requests.*',
                            'reimburse-requests.*',
                            'spj.index',
                            'spj.history'
                        ]);
                    @endphp
                    <div x-data="{ openPic: {{ $picActive ? 'true' : 'false' }} }">
                        <button @click="openPic = !openPic"
                                class="nav-link mobile-tap-target flex items-center px-3 md:px-4 py-3 mt-1 md:mt-2 w-full text-gray-600 font-medium rounded-lg {{ $picActive ? 'active' : '' }}">
                            <i class="fas fa-user-cog w-5 text-center mr-3"></i>
                            <span class="text-sm md:text-base">Pengajuan PIC</span>
                            <i :class="openPic ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"
                               class="ml-auto text-sm"></i>
                        </button>
                        <div x-show="openPic" x-cloak class="mt-1 space-y-1 pl-12">
                            {{-- Service Requests untuk Admin --}}
                            @php
                                $serviceActive = request()->routeIs('service-requests.*');
                                $servicePendingCount = \App\Models\ServiceRequest::where('status', 'pending')->count();
                            @endphp
                            <div x-data="{ open: {{ $serviceActive ? 'true' : 'false' }} }">
                                <button @click="open = !open"
                                        class="nav-link mobile-tap-target flex items-center px-3 md:px-4 py-3 mt-1 md:mt-2 w-full text-gray-600 font-medium rounded-lg {{ $serviceActive ? 'active' : '' }}">
                                    <i class="fas fa-tools w-5 text-center mr-3"></i>
                                    <span class="text-sm md:text-base">Kelola Service</span>
                                    <i :class="open ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"
                                       class="ml-auto text-sm"></i>
                                </button>
                                <div x-show="open" x-cloak class="mt-1 space-y-1 pl-12">
                                    <a href="{{ route('service-requests.index') }}"
                                       class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs(['service-requests.index','service-requests.create','service-requests.show','service-requests.edit']) ? 'bg-red-200 text-white' : '' }}">
                                        <i class="fas fa-tools w-5 text-center mr-3"></i>
                                        <span>Pengajuan Service</span>
                                    </a>
                                    <a href="{{ route('service-requests.service-pending') }}"
                                       class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('service-requests.service-pending') ? 'bg-red-200 text-white' : '' }}">
                                        <i class="fas fa-clock w-5 text-center mr-3"></i>
                                        <span>Service Pending @if($servicePendingCount)<span class="ml-2 inline-block bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $servicePendingCount }}</span>@endif</span>
                                    </a>
                                    <a href="{{ route('service-requests.service-history') }}"
                                       class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('service-requests.service-history') ? 'bg-red-200 text-white' : '' }}">
                                        <i class="fas fa-history w-5 text-center mr-3"></i>
                                        <span>Riwayat Service</span>
                                    </a>
                                    <a href="{{ route('service-requests.all-activities') }}"
                                       class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('service-requests.all-activities') ? 'bg-red-200 text-white' : '' }}">
                                        <i class="fas fa-list w-5 text-center mr-3"></i>
                                        <span>Semua Aktivitas</span>
                                    </a>
                                </div>
                            </div>

                            {{-- Kelola APD untuk Admin --}}
                            @php
                                $apdActive = request()->routeIs('apd-requests.*');
                                $apdNotifications = auth()->user()->unreadNotifications()
                                    ->where('type', App\Notifications\NewApdRequestNotification::class)
                                    ->count();
                                $apdDeliveryCount = \App\Models\ApdRequest::where('status','delivery')->count();
                            @endphp
                            <div x-data="{ openApd: {{ $apdActive ? 'true' : 'false' }} }">
                                <button @click="openApd = !openApd"
                                        class="nav-link flex items-center px-4 py-2 mt-2 w-full text-gray-600 font-medium rounded-lg {{ $apdActive ? 'active' : '' }}">
                                    <i class="fas fa-hard-hat w-5 text-center mr-3"></i>
                                    <span>Kelola APD @if($apdNotifications)<span class="ml-2 inline-block bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $apdNotifications }}</span>@endif</span>
                                    <i :class="openApd ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"
                                       class="ml-auto text-sm"></i>
                                </button>
                                <div x-show="openApd" x-cloak class="mt-1 space-y-1 pl-12">
                                    <a href="{{ route('apd-requests.admin-index', ['status' => 'all']) }}"
                                       class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ (request()->routeIs('apd-requests.admin-index') && request('status')=='all') ? 'bg-red-200 text-white' : '' }}">
                                        <i class="fas fa-list w-5 text-center mr-3"></i>
                                        <span>All Activities</span>
                                    </a>
                                    <a href="{{ route('apd-requests.admin-index', ['status' => 'pending']) }}"
                                       class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('apd-requests.admin-index') && request('status')=='pending' ? 'bg-red-200 text-white' : '' }}">
                                        <i class="fas fa-clipboard-check w-5 text-center mr-3"></i>
                                        <span>Pengajuan APD</span>
                                    </a>
                                    <a href="{{ route('apd-requests.admin-index', ['status' => 'delivery']) }}"
                                       class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('apd-requests.admin-index') && request('status')=='delivery' ? 'bg-red-200 text-white' : '' }}">
                                        <i class="fas fa-truck w-5 text-center mr-3"></i>
                                        <span>APD Dikirim @if($apdDeliveryCount)<span class="ml-2 inline-block bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $apdDeliveryCount }}</span>@endif</span>
                                    </a>
                                    <a href="{{ route('apd-requests.admin-history') }}"
                                       class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('apd-requests.admin-history') ? 'bg-red-200 text-white' : '' }}">
                                        <i class="fas fa-history w-5 text-center mr-3"></i>
                                        <span>Riwayat APD</span>
                                    </a>
                                </div>
                            </div>

                            {{-- Kelola Reimburse untuk Admin --}}
                            @php
                                $reimburseActive = request()->routeIs('reimburse-requests.*');
                                $reimburseCount = \App\Models\ReimburseRequest::where('status', 'pending')->count();
                            @endphp
                            <div x-data="{ openReimburse: {{ $reimburseActive ? 'true' : 'false' }} }">
                                <button @click="openReimburse = !openReimburse"
                                        class="nav-link flex items-center px-4 py-2 mt-2 w-full text-gray-600 font-medium rounded-lg {{ $reimburseActive ? 'active' : '' }}">
                                    <i class="fas fa-receipt w-5 text-center mr-3"></i>
                                    <span>Kelola Reimburse @if($reimburseCount)<span class="ml-2 inline-block bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $reimburseCount }}</span>@endif</span>
                                    <i :class="openReimburse ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"
                                       class="ml-auto text-sm"></i>
                                </button>
                                <div x-show="openReimburse" x-cloak class="mt-1 space-y-1 pl-12">
                                    <a href="{{ route('reimburse-requests.admin-index') }}"
                                       class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('reimburse-requests.admin-index') ? 'bg-red-200 text-white' : '' }}">
                                        <i class="fas fa-list w-5 text-center mr-3"></i>
                                        <span>All Activities</span>
                                    </a>
                                    <a href="{{ route('reimburse-requests.admin-index', ['status' => 'pending']) }}"
                                       class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('reimburse-requests.admin-index') && request('status')=='pending' ? 'bg-red-200 text-white' : '' }}">
                                        <i class="fas fa-clipboard-check w-5 text-center mr-3"></i>
                                        <span>Pengajuan Reimburse</span>
                                    </a>
                                    <a href="{{ route('reimburse-requests.history') }}"
                                       class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('reimburse-requests.history') ? 'bg-red-200 text-white' : '' }}">
                                        <i class="fas fa-history w-5 text-center mr-3"></i>
                                        <span>Riwayat Reimburse</span>
                                    </a>
                                </div>
                            </div>

                            {{-- SPJ Requests untuk Admin --}}
                            @php
                                $spjActive = request()->routeIs(['spj-requests.*','spj.create','spj.index','spj.history']);
                                $spjPendingCount = \App\Models\SpjRequest::where('status', 'pending')->count();
                            @endphp
                            <div x-data="{ openSpj: {{ $spjActive ? 'true' : 'false' }} }">
                                <button @click="openSpj = !openSpj"
                                        class="nav-link flex items-center px-4 py-2 mt-2 w-full text-gray-600 font-medium rounded-lg {{ $spjActive ? 'active' : '' }}">
                                    <i class="fas fa-file-alt w-5 text-center mr-3"></i>
                                    <span>Pengajuan SPJ @if($spjPendingCount)<span class="ml-2 inline-block bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $spjPendingCount }}</span>@endif</span>
                                    <i :class="openSpj ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"
                                       class="ml-auto text-sm"></i>
                                </button>
                                <div x-show="openSpj" x-cloak class="mt-1 space-y-1 pl-12">
                                    <a href="{{ route('spj.index') }}"
                                       class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('spj.index') && !request('status') ? 'bg-red-200 text-white' : '' }}">
                                        <i class="fas fa-list w-5 text-center mr-3"></i>
                                        <span>All Activities</span>
                                    </a>
                                    <a href="{{ route('spj.index', ['status' => 'pending']) }}"
                                       class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('spj.index') && request('status')=='pending' ? 'bg-red-200 text-white' : '' }}">
                                        <i class="fas fa-clipboard-check w-5 text-center mr-3"></i>
                                        <span>Pengajuan SPJ @if($spjPendingCount)<span class="ml-2 inline-block bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $spjPendingCount }}</span>@endif</span>
                                    </a>
                                    <a href="{{ route('spj.history') }}"
                                       class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('spj.history') ? 'bg-red-200 text-white' : '' }}">
                                        <i class="fas fa-history w-5 text-center mr-3"></i>
                                        <span>Riwayat SPJ</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Menu utama untuk User non-admin --}}
                    @php
                        $serviceActive = request()->routeIs('service-requests.*');
                        $servicePendingCount = auth()->user()->serviceRequests()->where('status', 'pending')->count();
                    @endphp
                    <div x-data="{ open: {{ $serviceActive ? 'true' : 'false' }} }">
                        <button @click="open = !open"
                                class="nav-link mobile-tap-target flex items-center px-3 md:px-4 py-3 mt-1 md:mt-2 w-full text-gray-600 font-medium rounded-lg {{ $serviceActive ? 'active' : '' }}">
                            <i class="fas fa-tools w-5 text-center mr-3"></i>
                            <span class="text-sm md:text-base">Kelola Service</span>
                            <i :class="open ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"
                               class="ml-auto text-sm"></i>
                        </button>
                        <div x-show="open" x-cloak class="mt-1 space-y-1 pl-12">
                            <a href="{{ route('service-requests.index') }}"
                               class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs(['service-requests.index','service-requests.create','service-requests.show','service-requests.edit']) ? 'bg-red-200 text-white' : '' }}">
                                <i class="fas fa-tools w-5 text-center mr-3"></i>
                                <span>Pengajuan Service</span>
                            </a>
                            <a href="{{ route('service-requests.service-pending') }}"
                               class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('service-requests.service-pending') ? 'bg-red-200 text-white' : '' }}">
                                <i class="fas fa-clock w-5 text-center mr-3"></i>
                                <span>Service Pending @if($servicePendingCount)<span class="ml-2 inline-block bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $servicePendingCount }}</span>@endif</span>
                            </a>
                            <a href="{{ route('service-requests.service-history') }}"
                               class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('service-requests.service-history') ? 'bg-red-200 text-white' : '' }}">
                                <i class="fas fa-history w-5 text-center mr-3"></i>
                                <span>Riwayat Service</span>
                            </a>
                        </div>
                    </div>

                    {{-- Kelola APD untuk User non-admin --}}
                    @php
                        $apdActive = request()->routeIs('apd-requests.*');
                        $apdNotifications = auth()->user()->unreadNotifications()
                            ->where('type', App\Notifications\NewApdRequestNotification::class)
                            ->count();
                        $apdDeliveryCount = auth()->user()->apdRequests()->where('status','delivery')->count();
                    @endphp
                    <div x-data="{ openApd: {{ $apdActive ? 'true' : 'false' }} }">
                        <button @click="openApd = !openApd"
                                class="nav-link mobile-tap-target flex items-center px-3 md:px-4 py-3 mt-1 md:mt-2 w-full text-gray-600 font-medium rounded-lg {{ $apdActive ? 'active' : '' }}">
                            <i class="fas fa-hard-hat w-5 text-center mr-3"></i>
                            <span class="text-sm md:text-base">Kelola APD @if($apdNotifications)<span class="ml-2 inline-block bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $apdNotifications }}</span>@endif</span>
                            <i :class="openApd ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"
                               class="ml-auto text-sm"></i>
                        </button>
                        <div x-show="openApd" x-cloak class="mt-1 space-y-1 pl-12">
                            <a href="{{ route('apd-requests.index') }}"
                               class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('apd-requests.index') && !request('status') ? 'bg-red-200 text-white' : '' }}">
                                <i class="fas fa-list w-5 text-center mr-3"></i>
                                <span>Pengajuan APD</span>
                            </a>
                            <a href="{{ route('apd-requests.index', ['status' => 'delivery']) }}"
                               class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('apd-requests.index') && request('status')=='delivery' ? 'bg-red-200 text-white' : '' }}">
                                <i class="fas fa-truck w-5 text-center mr-3"></i>
                                <span>APD Dikirim @if($apdDeliveryCount)<span class="ml-2 inline-block bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $apdDeliveryCount }}</span>@endif</span>
                            </a>
                            <a href="{{ route('apd-requests.history') }}"
                               class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('apd-requests.history') ? 'bg-red-200 text-white' : '' }}">
                                <i class="fas fa-history w-5 text-center mr-3"></i>
                                <span>Riwayat APD</span>
                            </a>
                        </div>
                    </div>

                    {{-- Kelola Reimburse untuk User non-admin --}}
                    @php
                        $reimburseActive = request()->routeIs('reimburse-requests.*');
                        $reimburseCount = auth()->user()->reimburseRequests()->where('status', 'pending')->count();
                    @endphp
                    <div x-data="{ openReimburse: {{ $reimburseActive ? 'true' : 'false' }} }">
                        <button @click="openReimburse = !openReimburse"
                                class="nav-link mobile-tap-target flex items-center px-3 md:px-4 py-3 mt-1 md:mt-2 w-full text-gray-600 font-medium rounded-lg {{ $reimburseActive ? 'active' : '' }}">
                            <i class="fas fa-receipt w-5 text-center mr-3"></i>
                            <span class="text-sm md:text-base">Kelola Reimburse @if($reimburseCount)<span class="ml-2 inline-block bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $reimburseCount }}</span>@endif</span>
                            <i :class="openReimburse ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"
                               class="ml-auto text-sm"></i>
                        </button>
                        <div x-show="openReimburse" x-cloak class="mt-1 space-y-1 pl-12">
                            <a href="{{ route('reimburse-requests.create') }}"
                               class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('reimburse-requests.create') ? 'bg-red-200 text-white' : '' }}">
                                <i class="fas fa-plus w-5 text-center mr-3"></i>
                                <span>Pengajuan Reimburse</span>
                            </a>
                            <a href="{{ route('reimburse-requests.history') }}"
                               class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('reimburse-requests.history') ? 'bg-red-200 text-white' : '' }}">
                                <i class="fas fa-history w-5 text-center mr-3"></i>
                                <span>Riwayat Reimburse</span>
                            </a>
                        </div>
                    </div>

                    {{-- Pengajuan SPJ untuk User non-admin --}}
                    @php
                        $spjActive = request()->routeIs(['spj-requests.*','spj.create','spj.index','spj.history']);
                        $spjPendingCount = auth()->user()->spjRequests()->where('status', 'pending')->count();
                    @endphp
                    <div x-data="{ openSpj: {{ $spjActive ? 'true' : 'false' }} }">
                        <button @click="openSpj = !openSpj"
                                class="nav-link mobile-tap-target flex items-center px-3 md:px-4 py-3 mt-1 md:mt-2 w-full text-gray-600 font-medium rounded-lg {{ $spjActive ? 'active' : '' }}">
                            <i class="fas fa-file-alt w-5 text-center mr-3"></i>
                            <span class="text-sm md:text-base">Pengajuan SPJ @if($spjPendingCount)<span class="ml-2 inline-block bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $spjPendingCount }}</span>@endif</span>
                            <i :class="openSpj ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"
                               class="ml-auto text-sm"></i>
                        </button>
                        <div x-show="openSpj" x-cloak class="mt-1 space-y-1 pl-12">
                            <a href="{{ route('spj-requests.index') }}"
                               class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('spj-requests.index') ? 'bg-red-200 text-white' : '' }}">
                                <i class="fas fa-list w-5 text-center mr-3"></i>
                                <span>Pengajuan SPJ @if($spjPendingCount)<span class="ml-2 inline-block bg-red-500 text-white text-xs rounded-full px-2 py-0.5">{{ $spjPendingCount }}</span>@endif</span>
                            </a>
                            <a href="{{ route('spj.history') }}"
                               class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('spj.history') ? 'bg-red-200 text-white' : '' }}">
                                <i class="fas fa-history w-5 text-center mr-3"></i>
                                <span>Riwayat SPJ</span>
                            </a>
                        </div>
                    </div>
                @endif

                {{-- Divider --}}
                <hr class="my-4 border-gray-200">

                @if(auth()->user()->role === 'admin')
                    @php
                        $adminActive = request()->routeIs(['users.*','asset-pages.*','activity.logs']);
                    @endphp
                    <div x-data="{ openAdmin: {{ $adminActive ? 'true' : 'false' }} }">
                        <button @click="openAdmin = !openAdmin"
                                class="nav-link flex items-center px-4 py-2 mt-2 w-full text-gray-600 font-medium rounded-lg {{ $adminActive ? 'active' : '' }}">
                            <i class="fas fa-user-shield w-5 text-center mr-3"></i>
                            Kelola Admin
                            <i :class="openAdmin ? 'fas fa-chevron-down' : 'fas fa-chevron-right'" class="ml-auto text-sm"></i>
                        </button>
                        <div x-show="openAdmin" x-cloak class="mt-1 space-y-1 pl-12">
                            <a href="{{ route('users.index') }}"
                               class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('users.*') ? 'bg-red-200 text-white' : '' }}">
                                <i class="fas fa-users-cog w-5 text-center mr-3"></i><span>Kelola PIC</span>
                            </a>
                            <a href="{{ route('asset-pages.index') }}"
                               class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('asset-pages.*') ? 'bg-red-200 text-white' : '' }}">
                                <i class="fas fa-cogs w-5 text-center mr-3"></i><span>Kelola Halaman</span>
                            </a>
                            <a href="{{ route('activity.logs') }}"
                               class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('activity.logs') ? 'bg-red-200 text-white' : '' }}">
                                <i class="fas fa-clock-rotate-left w-5 text-center mr-3"></i><span>Activity Logs</span>
                            </a>
                        </div>
                    </div>
                @endif

                {{-- Kelola Akun Pribadi (untuk semua user) --}}
                <a href="{{ route('profile.edit') }}"
                   class="nav-link flex items-center px-4 py-2 mt-2 text-gray-600 font-medium rounded-lg {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <i class="fas fa-user-cog w-5 text-center mr-3"></i>
                    Kelola Akun
                </a>

            </nav>
            <div class="mt-auto w-full p-4 border-t">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full overflow-hidden">
                        @if(Auth::user()->avatar_url)
                            <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center">
                                <span class="text-white text-xs font-bold">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-gray-600 text-sm font-medium">{{ Auth::user()->name }}</p>
                        <p class="text-gray-500 text-xs">{{ ucfirst(Auth::user()->role) }}</p>
                    </div>
                    <form method="GET" action="{{ route('logout') }}" class="ml-2">
                        @csrf
                        <button type="submit" class="p-2 text-gray-500 hover:text-red-600 transition-colors">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 md:pl-64 min-w-0">
            <div class="w-full overflow-x-hidden">
                <!-- Mobile menu button (hidden for user 'baccan') -->
                @php
                    // Re-evaluate current user in this scope
                    $currentUser = auth()->user();
                    $isBaccan = false;
                    if ($currentUser) {
                        $username = $currentUser->username ?? '';
                        $name = $currentUser->name ?? '';
                        $email = $currentUser->email ?? '';
                        if (
                            (is_string($username) && stripos($username, 'baccan') !== false) ||
                            (is_string($name) && stripos($name, 'baccan') !== false) ||
                            (is_string($email) && stripos($email, 'baccan') !== false)
                        ) {
                            $isBaccan = true;
                        }
                    }
                @endphp

                @unless($isBaccan)
                    <div class="md:hidden p-4 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <button id="mobile-menu-button" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg shadow-lg hover:bg-red-700 transition-colors">
                                <i class="fas fa-bars mr-2"></i>
                                <span class="font-medium">Menu</span>
                            </button>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-lg mr-2">
                                    <img src="{{ asset('images/logo-dads.png') }}" alt="GA-DADS Logo" class="w-8 h-8">
                                </div>
                                <h1 class="text-lg font-semibold text-red-600">GA-DADS</h1>
                            </div>
                        </div>
                    </div>
                @endunless

                <main class="w-full px-4 md:px-6">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <!-- Mobile Overlay: use opacity + pointer-events so transitions work reliably -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 opacity-0 pointer-events-none md:hidden transition-opacity duration-300"></div>

    <script>
    // Mobile menu toggle
        const sidebar = document.getElementById('sidebar');
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileOverlay = document.getElementById('mobile-overlay');

        // Defensive: ensure elements exist before operating on them
        function showOverlay() {
            if (!mobileOverlay) return;
            mobileOverlay.classList.remove('opacity-0', 'pointer-events-none');
            mobileOverlay.classList.add('opacity-100');
        }

        function hideOverlay() {
            if (!mobileOverlay) return;
            mobileOverlay.classList.remove('opacity-100');
            mobileOverlay.classList.add('opacity-0', 'pointer-events-none');
        }

        function closeSidebar() {
            if (!sidebar) return;
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0');
            hideOverlay();
            document.body.classList.remove('overflow-hidden');
        }

        function openSidebar() {
            if (!sidebar) return;
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0');
            showOverlay();
            document.body.classList.add('overflow-hidden');
        }

        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', function() {
                if (!sidebar) return;
                const isHidden = sidebar.classList.contains('-translate-x-full') || !sidebar.classList.contains('translate-x-0');
                if (isHidden) {
                    openSidebar();
                } else {
                    closeSidebar();
                }
            });
        }

        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', closeSidebar);
        }

        // Close sidebar when clicking any nav link on mobile
        document.addEventListener('DOMContentLoaded', function() {
            if (sidebar) {
                const navLinks = sidebar.querySelectorAll('a');
                navLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        if (window.innerWidth < 768) {
                            closeSidebar();
                        }
                    });
                });
            }

            // Mobile close button in sidebar
            const mobileCloseBtn = document.getElementById('mobile-close-button');
            if (mobileCloseBtn) {
                mobileCloseBtn.addEventListener('click', closeSidebar);
            }
        });

        // Auto adjust sidebar on screen size change
        function checkScreenSize() {
            if (!sidebar) return;
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                hideOverlay();
                document.body.classList.remove('overflow-hidden');
            } else {
                // ensure sidebar is hidden by default on small screens
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('translate-x-0');
                hideOverlay();
                document.body.classList.remove('overflow-hidden');
            }
        }

        window.addEventListener('resize', checkScreenSize);
        checkScreenSize(); // Initial check

        // Close sidebar when clicking on form elements to prevent conflicts
        document.addEventListener('click', function(e) {
            // Check if clicked element is a form element or within a form
            if (e.target.closest('form') || e.target.matches('select, input, textarea')) {
                if (window.innerWidth < 768) {
                    closeSidebar();
                }
            }
        });

        // Prevent form element focus issues with sidebar overlay
        document.addEventListener('focusin', function(e) {
            if (e.target.matches('select, input, textarea') && window.innerWidth < 768) {
                closeSidebar();
            }
        });
        // Initialize Choices.js on any select.select2 (searchable selects)
        if (typeof Choices !== 'undefined') {
            const choicesMap = new Map();
            document.querySelectorAll('select.select2').forEach(sel => {
                try {
                    const c = new Choices(sel, {
                        // Disable built-in search for single-selects to simplify UI
                        searchEnabled: false,
                        shouldSort: false,
                        itemSelectText: '',
                        noResultsText: 'Tidak ditemukan',
                        loadingText: 'Memuat...'
                    });
                    const key = sel.id || sel.name || Symbol();
                    choicesMap.set(key, c);

                    // When this select is interacted with, hide other Choices dropdowns to avoid overlapping menus
                    const hideOthers = () => {
                        choicesMap.forEach((otherC) => {
                            if (otherC !== c && typeof otherC.hideDropdown === 'function') {
                                try { otherC.hideDropdown(); } catch (e) { /* ignore */ }
                            }
                        });
                    };

                    sel.addEventListener('click', hideOthers);
                    sel.addEventListener('focus', hideOthers);
                    // Also hide others when this instance opens (API may expose showDropdown)
                    if (typeof c.passedElement === 'object') {
                        // best-effort: some versions expose show/hide methods
                        if (typeof c.showDropdown === 'function') {
                            const origShow = c.showDropdown.bind(c);
                            c.showDropdown = function() { hideOthers(); return origShow(); };
                        }
                    }

                } catch (e) {
                    console.warn('Choices init failed for', sel, e);
                }
            });

            // Ensure any form submits copy the real select value into hidden inputs named appropriately.
            // Prefer using a data-hidden-id attribute on the select to copy into an existing hidden input (safer).
            document.querySelectorAll('form').forEach(f => {
                f.addEventListener('submit', function (ev) {
                    // for each select.select2 in this form
                    this.querySelectorAll('select.select2').forEach(s => {
                        // If a select provides data-hidden-id, copy value directly there
                        const hiddenId = s.getAttribute('data-hidden-id');
                        if (hiddenId) {
                            const hiddenEl = document.getElementById(hiddenId) || this.querySelector('#' + hiddenId);
                            if (hiddenEl) {
                                hiddenEl.value = s.value;
                                return;
                            }
                        }

                        // fallback: copy into a hidden input named after the select's name (create if missing)
                        const name = s.getAttribute('name') || s.id;
                        if (!name) return;
                        let hidden = this.querySelector('input[type="hidden"][name="' + name + '"]');
                        if (!hidden) {
                            hidden = document.createElement('input');
                            hidden.type = 'hidden';
                            hidden.name = name;
                            this.appendChild(hidden);
                        }
                        hidden.value = s.value;
                    });
                }, {capture: true});
            });

            // Hide any open Choices dropdowns when clicking outside or on resize to prevent dangling menus
            document.addEventListener('click', function(e) {
                // If click is not inside any choices container, hide all
                const insideChoices = e.target.closest('.choices') !== null;
                if (!insideChoices) {
                    choicesMap.forEach((c) => {
                        if (typeof c.hideDropdown === 'function') {
                            try { c.hideDropdown(); } catch (e) { /* ignore */ }
                        }
                    });
                }
            });

            window.addEventListener('resize', function() {
                choicesMap.forEach((c) => {
                    if (typeof c.hideDropdown === 'function') {
                        try { c.hideDropdown(); } catch (e) { /* ignore */ }
                    }
                });
            });
        }
    </script>

    {{-- Stack tambahan scripts dari view --}}
    @stack('scripts')

</body>
</html>
