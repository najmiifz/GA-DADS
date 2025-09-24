@extends('layouts.app')

@section('title', 'Kelola PIC')

@section('content')
<div class="container mx-auto px-4 py-8">
        <div class="flex flex-col md:flex-row md:justify-between items-start md:items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-4 md:mb-0">Kelola PIC</h1>
            <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
                <form id="searchForm" method="GET" action="{{ route('users.index') }}" class="flex flex-wrap items-center gap-4">
                <div class="flex flex-col">
                    <label for="search" class="text-sm font-medium text-gray-700 mb-1">Cari PIC</label>
                    <input
                        type="text"
                        name="search"
                        id="search"
                        placeholder="Cari PIC..."
                        value="{{ request('search') }}"
                        class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-300"
                        onkeyup="this.form.submit()"
                    />
                </div>
                <div class="flex flex-col">
                    <label for="tipe" class="text-sm font-medium text-gray-700 mb-1">Filter Tipe Asset</label>
                    <select name="tipe" id="tipe" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-300" onchange="this.form.submit()">
                        <option value="">-- Semua Tipe --</option>
                        @foreach($tipes as $tipeOption)
                            <option value="{{ $tipeOption }}" {{ request('tipe') === $tipeOption ? 'selected' : '' }}>{{ $tipeOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="jenis_aset" class="text-sm font-medium text-gray-700 mb-1">Filter Jenis Asset</label>
                    <select name="jenis_aset" id="jenis_aset" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-300" onchange="this.form.submit()">
                        <option value="">-- Semua Jenis Asset --</option>
                        @foreach($jenisAsets as $ja)
                            <option value="{{ $ja }}" {{ request('jenis_aset') == $ja ? 'selected' : '' }}>{{ $ja }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="lokasi" class="text-sm font-medium text-gray-700 mb-1">Filter Lokasi</label>
                    <select name="lokasi" id="lokasi" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-300" onchange="this.form.submit()">
                        <option value="">-- Semua Lokasi --</option>
                        @foreach($lokasis as $lok)
                            <option value="{{ $lok }}" {{ request('lokasi') == $lok ? 'selected' : '' }}>{{ $lok }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="role" class="text-sm font-medium text-gray-700 mb-1">Filter Role</label>
                    <select name="role" id="role" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring focus:border-blue-300" onchange="this.form.submit()">
                        <option value="">-- Semua Role --</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>PIC</option>
                    </select>
                </div>
                </form>
                <a href="{{ route('users.create') }}"
                   class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-plus mr-2"></i>Tambah PIC
                </a>
            </div>
        </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            {{-- Mobile cards (visible on small screens) --}}
            <div class="md:hidden space-y-4">
                @foreach ($users as $user)
                    <div class="bg-white border border-gray-200 rounded-lg p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-3">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-12 h-12 rounded-full">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $user->email }}</div>
                                    <div class="text-xs text-gray-500">NIK: {{ $user->nik ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="inline-block px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded">{{ $user->role }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $user->lokasi ?? '-' }}</div>
                                <div class="text-xs text-gray-500 mt-1">Project: {{ $user->project ?? '-' }}</div>
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                <div>Jabatan: {{ $user->jabatan ?? '-' }}</div>
                                <div class="mt-1">
                                    @if($user->assets->count() > 0)
                                        <div x-data="{ open: false }">
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">{{ $user->assets->count() }} aset</span>
                                                <button @click="open = !open" class="text-indigo-600 hover:text-indigo-900 text-xs">
                                                    <i :class="open ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                                    <span x-text="open ? 'Sembunyikan' : 'Lihat'"></span>
                                                </button>
                                            </div>
                                            <div x-show="open" class="mt-2 max-w-full bg-gray-50 rounded-lg p-2 space-y-2 max-h-40 overflow-y-auto">
                                                @foreach($user->assets as $asset)
                                                    <div class="flex justify-between items-center text-xs bg-white rounded p-2">
                                                        <div>
                                                            <div class="font-medium text-gray-900">{{ $asset->jenis_aset }}</div>
                                                            <div class="text-gray-500">{{ $asset->tipe }}</div>
                                                        </div>
                                                        @if(!empty($asset->id))
                                                            <a href="{{ route('assets.show', $asset->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Lihat Detail">
                                                                <i class="fas fa-external-link-alt"></i>
                                                            </a>
                                                        @else
                                                            <span class="text-gray-400" title="ID aset kosong"><i class="fas fa-exclamation-circle"></i></span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Tidak ada aset</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center space-x-3">
                                <a href="{{ route('users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Edit</a>
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($users->hasPages())
                    <div class="mt-4 px-4">{{ $users->links() }}</div>
                @endif
            </div>

            {{-- Desktop table (hidden on small screens) --}}
            <div class="hidden md:block">
                <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">NIK</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nama
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Email
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Peran
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Lokasi
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Project
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jabatan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aset Dipegang
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($user->avatar_url)
                                        <img src="{{ $user->avatar_url }}" alt="Avatar" class="w-10 h-10 rounded-full">
                                    @else
                                        <div class="w-10 h-10 bg-gray-200 rounded-full"></div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->nik ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="name" value="{{ $user->name }}">
                                        <input type="hidden" name="email" value="{{ $user->email }}">
                                        <input type="hidden" name="nik" value="{{ $user->nik }}">
                                        <input type="hidden" name="lokasi" value="{{ $user->lokasi }}">
                                        <input type="hidden" name="jabatan" value="{{ $user->jabatan }}">
                                        <select name="role" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800" onchange="this.form.submit()">
                                            <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>PIC</option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->lokasi ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->project ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->jabatan ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->assets->count() > 0)
                                        <div x-data="{ open: false }" class="space-y-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ $user->assets->count() }} aset
                                                </span>
                                                <button @click="open = !open" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                                    <i :class="open ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                                    <span x-text="open ? 'Sembunyikan' : 'Lihat'"></span>
                                                </button>
                                            </div>
                                            <div x-show="open" class="mt-3 max-w-md bg-gray-50 rounded-lg p-3 space-y-2 max-h-40 overflow-y-auto">
                                                <h4 class="text-sm font-medium text-gray-900 mb-2">Aset yang Dipegang:</h4>
                                                @foreach($user->assets as $asset)
                                                    <div class="flex justify-between items-center text-xs bg-white rounded p-2">
                                                        <div>
                                                            <div class="font-medium text-gray-900">{{ $asset->jenis_aset }}</div>
                                                            <div class="text-gray-500">{{ $asset->tipe }}</div>
                                                        </div>
                                                        @if(!empty($asset->id))
                                                            <a href="{{ route('assets.show', $asset->id) }}" class="text-indigo-600 hover:text-indigo-900" title="Lihat Detail">
                                                                <i class="fas fa-external-link-alt"></i>
                                                            </a>
                                                        @else
                                                            <span class="text-gray-400" title="ID aset kosong"><i class="fas fa-exclamation-circle"></i></span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            Tidak ada aset
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('users.edit', $user->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($users->hasPages())
                    <div class="mt-4 px-6 py-4 border-t border-gray-200">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        {{-- End filter form and add button --}}
    </div>
        </div>
    </div>
</div>

<script>
// Toggle visibility of assets per user
function toggleAssets(userId) {
    const assetsDiv = document.getElementById(`assets-${userId}`);
    const button = document.querySelector(`#icon-${userId}`).closest('button');
    const icon = button.querySelector('i');
    if (assetsDiv.classList.contains('hidden')) {
        assetsDiv.classList.remove('hidden');
        icon.className = 'fas fa-eye-slash';
        button.textContent = '';
        button.append(icon);
        button.append(' Sembunyikan');
    } else {
        assetsDiv.classList.add('hidden');
        icon.className = 'fas fa-eye';
        button.textContent = '';
        button.append(icon);
        button.append(' Lihat');
    }
}

// Preserve search query in pagination links
document.querySelectorAll('.pagination a').forEach(a => {
    const params = new URLSearchParams(window.location.search);
    const search = params.get('search');
    if (search) {
        a.href = a.href.split('?')[0] + '?search=' + encodeURIComponent(search);
    }
});
</script>
@endsection
