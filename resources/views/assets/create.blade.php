@extends('layouts.app')

@section('content')
<div class="p-6 max-w-xl mx-auto bg-white rounded shadow">
    <h1 class="text-xl font-bold mb-4">Tambah Aset</h1>
    <form method="POST" action="{{ route('assets.store') }}">
        @csrf
        <input type="text" name="jenis_aset" placeholder="Jenis Aset" class="w-full mb-3 p-2 border rounded">
        <input type="text" name="pic" placeholder="PIC" class="w-full mb-3 p-2 border rounded">
        <input type="text" name="project" placeholder="Project" class="w-full mb-3 p-2 border rounded">
        <input type="text" name="lokasi" placeholder="Lokasi" class="w-full mb-3 p-2 border rounded">
        <input type="number" name="harga_beli" placeholder="Harga Beli" class="w-full mb-3 p-2 border rounded">
        <button class="bg-green-500 text-white px-4 py-2 rounded">Simpan</button>
    </form>
</div>
@endsection
