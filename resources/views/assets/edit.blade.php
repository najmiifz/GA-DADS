@extends('layouts.app')

@section('content')
<div class="p-6 max-w-xl mx-auto bg-white rounded shadow">
    <h1 class="text-xl font-bold mb-4">Edit Aset</h1>
    <form method="POST" action="{{ route('assets.update',$asset->id) }}">
        @csrf @method('PUT')
        <input type="text" name="jenis_aset" value="{{ $asset->jenis_aset }}" class="w-full mb-3 p-2 border rounded">
        <input type="text" name="pic" value="{{ $asset->pic }}" class="w-full mb-3 p-2 border rounded">
        <input type="text" name="project" value="{{ $asset->project }}" class="w-full mb-3 p-2 border rounded">
        <input type="text" name="lokasi" value="{{ $asset->lokasi }}" class="w-full mb-3 p-2 border rounded">
        <input type="number" name="harga_beli" value="{{ $asset->harga_beli }}" class="w-full mb-3 p-2 border rounded">
        <button class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
    </form>
</div>
@endsection

