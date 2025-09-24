@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">
        Notifikasi
        @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
        @if($unreadCount > 0)
            <span class="inline-block ml-2 bg-red-500 text-white text-sm rounded-full px-2 py-0.5">
                {{ $unreadCount }}
            </span>
        @endif
    </h1>

    @if($notifications->count() > 0)
        <div class="flex items-center justify-between mb-4">
            <div></div>
            <form method="POST" action="{{ route('notifications.markAllRead') }}">
                @csrf
                <button type="submit" class="px-3 py-2 bg-green-500 text-white rounded-md text-sm hover:bg-green-600">Tandai sudah dibaca semua</button>
            </form>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            @foreach($notifications as $notification)
                <div class="border-b border-gray-200 last:border-b-0">
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-sm text-gray-600 mb-1">
                                    {{ $notification->created_at->format('d M Y, H:i') }}
                                </p>
                                <p class="text-gray-900">
                                    {{ $notification->data['message'] }}
                                </p>
                                @if(isset($notification->data['url']))
                                    <a href="{{ $notification->data['url'] }}" class="text-blue-600 hover:text-blue-800 text-sm mt-2 inline-block">
                                        Lihat Detail
                                    </a>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('notifications.markAsRead', $notification->id) }}" class="ml-4">
                                @csrf
                                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                                    Tandai sudah dibaca
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white shadow-md rounded-lg p-8 text-center">
            <div class="text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5-5-5h5v-12h5v12z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak ada notifikasi</h3>
                <p class="text-gray-500">Anda tidak memiliki notifikasi yang belum dibaca.</p>
            </div>
        </div>
    @endif
</div>
@endsection
