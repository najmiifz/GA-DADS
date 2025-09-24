@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto py-6">
    <h1 class="text-3xl font-semibold mb-6 text-gray-800">SPJ Pending</h1>
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        @if($requests->isEmpty())
            <div class="p-6 text-gray-600">Tidak ada pengajuan SPJ pending.</div>
        @else
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pegawai</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($requests as $req)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $req->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $req->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $req->spj_date->format('d M Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <form action="{{ route('spj.approve', $req) }}" method="POST">@csrf
                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition">Approve</button>
                                    </form>
                                    <form action="{{ route('spj.reject', $req) }}" method="POST">@csrf
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition">Reject</button>
                                    </form>
                                    <a href="{{ route('spj.show', $req) }}" class="px-3 py-1 bg-gray-100 text-indigo-600 rounded hover:bg-gray-200 transition">Detail</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="md:hidden">
                @foreach($requests as $req)
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between">
                        <div>
                            <div class="text-sm font-medium text-gray-900">SPJ #{{ $req->id }}</div>
                            <div class="text-sm text-gray-500">{{ $req->user->name }}</div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <form action="{{ route('spj.approve', $req) }}" method="POST">@csrf
                                <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm">Approve</button>
                            </form>
                            <form action="{{ route('spj.reject', $req) }}" method="POST">@csrf
                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition text-sm">Reject</button>
                            </form>
                            <a href="{{ route('spj.show', $req) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Detail</a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="p-4">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
