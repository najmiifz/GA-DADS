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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
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
    </style>
</head>

<body class="bg-gray-100">

    <!-- Main Application Container -->
    <div class="relative min-h-screen md:flex">
    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-red-50 shadow-md flex flex-col h-screen flex-shrink-0 fixed md:fixed inset-y-0 left-0 z-30 transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out">
            <div class="p-6 flex flex-col items-center justify-center">
                <div class="w-24 h-24 bg-white rounded-lg flex items-center justify-center">
                    <img src="{{ asset('images/logo-dads.svg') }}" alt="GA-DADS Logo" class="w-20 h-20">
                </div>
                <span class="mt-2 text-2xl font-bold text-red-600">GA-DADS</span>
            </div>
            <nav class="mt-6 px-4 flex-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}"
                   class="nav-link flex items-center px-4 py-2 mt-2 text-gray-600 font-medium rounded-lg {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt w-5 text-center mr-3"></i>
                    Dashboard
                </a>

                @if(auth()->user()->role === 'admin')
                <a href="{{ route('assets.vehicles') }}"
                   class="nav-link flex items-center px-4 py-2 mt-2 text-gray-600 font-medium rounded-lg {{ request()->routeIs('assets.vehicles') ? 'active' : '' }}">
                    <i class="fas fa-car w-5 text-center mr-3"></i>
                    Kendaraan
                </a>
                <a href="{{ route('assets.splicers') }}"
                   class="nav-link flex items-center px-4 py-2 mt-2 text-gray-600 font-medium rounded-lg {{ request()->routeIs('assets.splicers') ? 'active' : '' }}">
                    <i class="fas fa-tools w-5 text-center mr-3"></i>
                    Splicers
                </a>
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

                {{-- Kelola Service Dropdown --}}
                @php
                    $serviceActive = request()->routeIs('service-requests.*');
                @endphp
                <div x-data="{ open: {{ $serviceActive ? 'true' : 'false' }} }">
                    <button @click="open = !open"
                            class="nav-link flex items-center px-4 py-2 mt-2 w-full text-gray-600 font-medium rounded-lg {{ $serviceActive ? 'active' : '' }}">
                        <i class="fas fa-tools w-5 text-center mr-3"></i>
                        Kelola Service
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
                            <span>Service Pending</span>
                        </a>
                        <a href="{{ route('service-requests.service-history') }}"
                           class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('service-requests.service-history') ? 'bg-red-200 text-white' : '' }}">
                            <i class="fas fa-history w-5 text-center mr-3"></i>
                            <span>Riwayat Service</span>
                        </a>
                    </div>
                </div>

                {{-- Kelola APD Dropdown --}}
                @php
                    $apdActive = request()->routeIs('apd-requests.*');
                @endphp
                <div x-data="{ openApd: {{ $apdActive ? 'true' : 'false' }} }">
                    <button @click="openApd = !openApd"
                            class="nav-link flex items-center px-4 py-2 mt-2 w-full text-gray-600 font-medium rounded-lg {{ $apdActive ? 'active' : '' }}">
                        <i class="fas fa-hard-hat w-5 text-center mr-3"></i>
                        Kelola APD
                        <i :class="openApd ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"
                           class="ml-auto text-sm"></i>
                    </button>
                    <div x-show="openApd" x-cloak class="mt-1 space-y-1 pl-12">
                        <a href="{{ route('apd-requests.index') }}"
                           class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('apd-requests.index') ? 'bg-red-200 text-white' : '' }}">
                            <i class="fas fa-list w-5 text-center mr-3"></i>
                            <span>All Activities</span>
                        </a>
                        <a href="{{ route('apd-requests.create') }}"
                           class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('apd-requests.create') ? 'bg-red-200 text-white' : '' }}">
                            <i class="fas fa-plus w-5 text-center mr-3"></i>
                            <span>Pengajuan APD</span>
                        </a>
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('apd-requests.admin-index') }}"
                               class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('apd-requests.admin-index') ? 'bg-red-200 text-white' : '' }}">
                                <i class="fas fa-user-shield w-5 text-center mr-3"></i>
                                <span>Admin APD</span>
                            </a>
                        @endif
                        <a href="{{ route('apd-requests.history') }}"
                           class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('apd-requests.history') ? 'bg-red-200 text-white' : '' }}">
                            <i class="fas fa-history w-5 text-center mr-3"></i>
                            <span>Riwayat APD</span>
                        </a>
                    </div>
                </div>

                {{-- Kelola Reimburse Dropdown --}}
                @php
                    $reimburseActive = request()->routeIs('reimburse-requests.*');
                @endphp
                <div x-data="{ openReimburse: {{ $reimburseActive ? 'true' : 'false' }} }">
                    <button @click="openReimburse = !openReimburse"
                            class="nav-link flex items-center px-4 py-2 mt-2 w-full text-gray-600 font-medium rounded-lg {{ $reimburseActive ? 'active' : '' }}">
                        <i class="fas fa-receipt w-5 text-center mr-3"></i>
                        Kelola Reimburse
                        <i :class="openReimburse ? 'fas fa-chevron-down' : 'fas fa-chevron-right'"
                           class="ml-auto text-sm"></i>
                    </button>
                    <div x-show="openReimburse" x-cloak class="mt-1 space-y-1 pl-12">
                        <a href="{{ route('reimburse-requests.index') }}"
                           class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('reimburse-requests.index') ? 'bg-red-200 text-white' : '' }}">
                            <i class="fas fa-list w-5 text-center mr-3"></i>
                            <span>All Activities</span>
                        </a>
                        <a href="{{ route('reimburse-requests.create') }}"
                           class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('reimburse-requests.create') ? 'bg-red-200 text-white' : '' }}">
                            <i class="fas fa-plus w-5 text-center mr-3"></i>
                            <span>Pengajuan Reimburse</span>
                        </a>
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('reimburse-requests.admin-index') }}"
                               class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('reimburse-requests.admin-index') ? 'bg-red-200 text-white' : '' }}">
                                <i class="fas fa-user-shield w-5 text-center mr-3"></i>
                                <span>Admin Reimburse</span>
                            </a>
                        @endif
                        <a href="{{ route('reimburse-requests.history') }}"
                           class="flex items-center px-4 py-2 text-gray-600 rounded-lg hover:bg-red-200 {{ request()->routeIs('reimburse-requests.history') ? 'bg-red-200 text-white' : '' }}">
                            <i class="fas fa-history w-5 text-center mr-3"></i>
                            <span>Riwayat Reimburse</span>
                        </a>
                    </div>
                </div>

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

                {{-- Notifikasi --}}
                <a href="{{ route('notifications.index') }}"
                   class="nav-link flex items-center px-4 py-2 mt-2 text-gray-600 font-medium rounded-lg {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                    <i class="fas fa-bell w-5 text-center mr-3"></i>
                    Notifikasi
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-1 min-w-[20px] text-center">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </a>
            </nav>
            <div class="mt-auto w-full p-4 border-t">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full overflow-hidden">
                        @if(Auth::user()->avatar)
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
        <div class="flex-1 md:ml-64 p-6">
            <!-- Mobile menu button -->
            <button id="mobile-menu-button" class="md:hidden mb-4 p-2 rounded-md bg-red-600 text-white">
                <i class="fas fa-bars"></i>
            </button>

            <main>
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Overlay -->
    <div id="mobile-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden md:hidden"></div>

    <script>
        // Mobile menu toggle
        const sidebar = document.getElementById('sidebar');
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileOverlay = document.getElementById('mobile-overlay');

        mobileMenuButton.addEventListener('click', function() {
            sidebar.classList.toggle('-translate-x-full');
            mobileOverlay.classList.toggle('hidden');
        });

        mobileOverlay.addEventListener('click', function() {
            sidebar.classList.add('-translate-x-full');
            mobileOverlay.classList.add('hidden');
        });

        // Auto hide sidebar on larger screens
        function checkScreenSize() {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('-translate-x-full');
                mobileOverlay.classList.add('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        }

        window.addEventListener('resize', checkScreenSize);
        checkScreenSize(); // Initial check
    </script>

</body>
</html>
