@extends('layouts.app')

@section('content')
<div class="p-6">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white shadow p-4 rounded">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">
                    @if(auth()->user() && auth()->user()->role === 'admin')
                        Aktivitas Pengguna (Sedang Berlangsung)
                    @else
                        Aktivitas Anda (Sedang Berlangsung)
                    @endif
                </h2>
                <div class="flex items-center space-x-3">
                    <a href="{{ url()->current() }}" class="text-sm text-gray-600 hover:underline">Refresh</a>
                    <button id="history-toggle" type="button" class="text-sm px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Lihat Riwayat Aktivitas</button>
                </div>
            </div>

            @if($logs->isEmpty())
                <div class="text-gray-500 text-center">Tidak ada aktivitas yang sedang berlangsung.</div>
            @else
                <div class="space-y-3">
                    @foreach($logs as $log)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow bg-white">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-3">
                                <div class="w-12">
                                    <div class="rounded-full bg-red-100 text-red-700 w-12 h-12 flex items-center justify-center text-sm font-semibold">{{ strtoupper(substr($log->type,0,2)) }}</div>
                                </div>
                                @if(auth()->user() && auth()->user()->role === 'admin')
                                    <div class="flex flex-col justify-center text-sm">
                                        <div class="ml-2 font-medium">{{ optional($log->user)->name ? Str::limit(optional($log->user)->name, 18) : '—' }}</div>
                                        <div class="text-xs text-gray-400">{{ optional($log->user)->email ?? '' }}</div>
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ $log->link ?? '#' }}" class="font-medium text-gray-800 hover:text-red-600">{{ $log->nomor ?? ('#' . $log->type . '-' . $log->id) }}</a>
                                    <div class="text-sm text-gray-500 mt-1">Tipe: <span class="font-medium">{{ $log->type }}</span> • Status: <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">{{ $log->status ?? '-' }}</span></div>

                                    <div class="mt-2 text-sm text-gray-700">
                                        @if(!empty($log->asset))
                                            <div><strong>
                                                @if($log->type === 'APD')
                                                    Tim:
                                                @else
                                                    Asset:
                                                @endif
                                            </strong> {{ $log->asset }}</div>
                                        @endif

                                        @if(!empty($log->project))
                                            <div class="mt-1"><strong>Project:</strong> {{ $log->project }}</div>
                                        @endif

                                        @if(!empty($log->keterangan))
                                            <div class="mt-1"><strong>Keterangan:</strong> {{ \Illuminate\Support\Str::limit($log->keterangan, 140) }}</div>
                                        @endif

                                        @if(!empty($log->lokasi))
                                            <div class="mt-1 text-xs text-gray-500"><strong>Lokasi:</strong> {{ $log->lokasi }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                @if(!empty($log->amount))
                                    <div class="text-sm font-semibold mb-2">Rp {{ number_format($log->amount,0,',','.') }}</div>
                                @endif
                                <div class="text-sm text-gray-400">{{ $log->created_at ? $log->created_at->format('d M Y H:i') : '-' }}</div>
                                <div class="mt-3">
                                    <a href="{{ $log->link ?? '#' }}" class="text-sm text-blue-600 hover:underline">Lihat Detail</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
    <div id="activity-history-container" class="max-w-4xl mx-auto mt-6 hidden">
        <div class="bg-white shadow p-4 rounded">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Riwayat Aktivitas</h2>
                <div><a href="{{ url()->current() }}" class="text-sm text-gray-600 hover:underline">Refresh</a></div>
            </div>

            @if(empty($history) || $history->isEmpty())
                <div class="text-gray-500 text-center">Belum ada riwayat aktivitas.</div>
            @else
                <div class="space-y-3">
                    @foreach($history as $h)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-lg transition-shadow bg-white">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-3">
                                    <div class="w-12">
                                        <div class="rounded-full bg-gray-100 text-gray-700 w-12 h-12 flex items-center justify-center text-sm font-semibold">{{ strtoupper(substr($h->type,0,2)) }}</div>
                                    </div>
                                    <div>
                                        <a href="{{ $h->link ?? '#' }}" class="font-medium text-gray-800 hover:text-red-600">{{ $h->nomor ?? ('#' . $h->type . '-' . $h->id) }}</a>
                                        <div class="text-sm text-gray-500 mt-1">Tipe: <span class="font-medium">{{ $h->type }}</span> • Status: <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">{{ $h->status ?? '-' }}</span></div>
                                        @if(!empty($h->keterangan))
                                            <div class="mt-2 text-sm text-gray-700"><strong>Keterangan:</strong> {{ \Illuminate\Support\Str::limit($h->keterangan, 140) }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right text-sm text-gray-400">{{ $h->created_at ? $h->created_at->format('d M Y H:i') : '-' }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var btn = document.getElementById('history-toggle');
    var container = document.getElementById('activity-history-container');
    if (!btn || !container) return;
    btn.addEventListener('click', function () {
        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
            btn.textContent = 'Sembunyikan Riwayat Aktivitas';
            btn.classList.remove('bg-blue-600');
            btn.classList.add('bg-gray-200', 'text-gray-800');
        } else {
            container.classList.add('hidden');
            btn.textContent = 'Lihat Riwayat Aktivitas';
            btn.classList.remove('bg-gray-200', 'text-gray-800');
            btn.classList.add('bg-blue-600');
        }
    });
});
</script>
@endpush
