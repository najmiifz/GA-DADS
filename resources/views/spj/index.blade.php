@extends('layouts.app')
@section('content')
<div class="max-w-5xl mx-auto py-6">
    <h1 class="text-3xl font-semibold mb-6 text-gray-800">Daftar Pengajuan SPJ</h1>
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        @if($requests->isEmpty())
            <div class="p-6 text-gray-600">Tidak ada pengajuan SPJ.</div>
        @else
            {{-- Mobile cards --}}
            <div class="md:hidden">
                <div class="divide-y divide-gray-200">
                    @foreach($requests as $req)
                        <div class="p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">SPJ #{{ $req->id }}</div>
                                    <div class="text-sm text-gray-500">{{ $req->user->name }}</div>
                                </div>
                                <div class="text-sm text-gray-500 text-right">
                                    <div class="mb-1">{{ $req->spj_date->format('d M Y') }}</div>
                                    @php
                                        $colors = ['pending' => 'yellow', 'approved' => 'green', 'rejected' => 'red'];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $colors[$req->status] }}-100 text-{{ $colors[$req->status] }}-800">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <div class="text-sm text-gray-700">&nbsp;</div>
                                <div class="flex items-center space-x-2">
                                    @if($req->status === 'pending')
                                        <form action="{{ route('spj.approve', $req) }}" method="POST">@csrf
                                            <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition text-sm">Approve</button>
                                        </form>
                                        <form action="{{ route('spj.reject', $req) }}" method="POST">@csrf
                                            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition text-sm">Reject</button>
                                        </form>
                                        <a href="{{ route('spj.show', $req) }}" class="px-3 py-1 bg-gray-100 text-indigo-600 rounded hover:bg-gray-200 transition text-sm">Detail</a>
                                    @else
                                        <a href="{{ route('spj.show', $req) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">Lihat</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="p-4">{{ $requests->links() }}</div>
            </div>

            {{-- Desktop/table view --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Pegawai</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($requests as $req)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $req->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $req->user->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $req->spj_date->format('d M Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $colors = ['pending' => 'yellow', 'approved' => 'green', 'rejected' => 'red'];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $colors[$req->status] }}-100 text-{{ $colors[$req->status] }}-800">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($req->status === 'pending')
                                        <div class="flex items-center space-x-2">
                                            <form action="{{ route('spj.approve', $req) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700 transition">Approve</button>
                                            </form>
                                            <form action="{{ route('spj.reject', $req) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition">Reject</button>
                                            </form>
                                            <a href="{{ route('spj.show', $req) }}" class="px-3 py-1 bg-gray-100 text-indigo-600 rounded hover:bg-gray-200 transition">Detail</a>
                                        </div>
                                    @else
                                        <a href="{{ route('spj.show', $req) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
</div>
<!-- End SPJ list card -->
@endsection
