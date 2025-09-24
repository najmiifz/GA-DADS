@props([
    // You can override the src when using the component: <x-application-logo src="{{ asset('path/to/your.png') }}" />
    'src' => asset('images/logo.png'),
    'alt' => config('app.name', 'App') . ' logo',
])

<img src="{{ $src }}" alt="{{ $alt }}" {{ $attributes }} />
