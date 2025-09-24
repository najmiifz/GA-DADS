@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-6">
    <h1 class="text-3xl font-semibold mb-6 text-gray-800">Riwayat Pengajuan SPJ</h1>
    <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        @if($requests->isEmpty())
            <div class="p-6 text-gray-600">Belum ada riwayat pengajuan SPJ.</div>
        @else
            <div class="overflow-x-auto">
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
                                        $colors = ['approved' => 'green', 'rejected' => 'red'];
                                    @endphp
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $colors[$req->status] }}-100 text-{{ $colors[$req->status] }}-800">
                                        {{ ucfirst($req->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('spj.view', $req) }}" class="text-indigo-600 hover:text-indigo-900">Lihat</a>
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
@endsection
