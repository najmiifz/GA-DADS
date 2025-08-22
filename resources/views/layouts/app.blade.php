<div class="flex">
  <aside class="w-64 bg-gray-900 text-white h-screen p-4">
    <h2 class="font-bold text-lg mb-4">Menu</h2>
    <ul>
      <li><a href="{{route('assets.index')}}" class="block py-2">Dasbor & Tabel Aset</a></li>
      <li><a href="{{route('assets.vehicles')}}" class="block py-2">Dasbor Kendaraan</a></li>
      <li><a href="{{route('assets.splicers')}}" class="block py-2">Dasbor Splicer</a></li>
      <li><a href="{{route('assets.export')}}" class="block py-2">Export Excel</a></li>
      <li>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="block py-2 text-red-500">Logout</button>
        </form>
      </li>
    </ul>
  </aside>
  <main class="flex-1 p-6">@yield('content')</main>
</div>
