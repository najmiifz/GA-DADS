// ...existing code above...
<td class="px-4 py-3 text-xs">
    @php $state = strtolower($item->status ?? ''); @endphp
    @switch($state)
        @case('pending')
            @if(!empty($item->id) && is_numeric($item->id))
                <a href="{{ route('apd-requests.edit', $item->id) }}" class="block px-2 py-1 bg-gray-200 rounded-md">Edit</a>
                <a href="{{ route('apd-requests.show', $item->id) }}" class="block px-2 py-1 bg-blue-200 rounded-md mt-1">Detail</a>
            @endif
            @break
        @case('approved')
        @case('received')
        @case('rejected')
            @if(!empty($item->id) && is_numeric($item->id))
                <a href="{{ route('apd-requests.show', $item->id) }}" class="block px-2 py-1 bg-blue-200 rounded-md">Detail</a>
            @endif
            @break
        @case('delivery')
            @if(!empty($item->id) && is_numeric($item->id))
                <a href="{{ route('apd-requests.restock', $item->id) }}" class="block px-2 py-1 bg-purple-200 rounded-md">Restock</a>
                <a href="{{ route('apd-requests.show', $item->id) }}" class="block px-2 py-1 bg-blue-200 rounded-md mt-1">Detail</a>
            @endif
            @break
        @default
            @if(!empty($item->id) && is_numeric($item->id))
                <a href="{{ route('apd-requests.show', $item->id) }}" class="block px-2 py-1 bg-blue-200 rounded-md">Detail</a>
            @endif
    @endswitch
</td>
// ...existing code below...