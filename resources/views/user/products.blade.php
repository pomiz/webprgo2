@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Daftar Produk</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach ($products as $product)
            <div class="border rounded-lg p-4 shadow">
                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded">
                <h2 class="text-lg font-semibold mt-3">{{ $product->name }}</h2>
                <p class="text-gray-600 text-sm mt-1">{{ Str::limit($product->description, 100) }}</p>
                <p class="font-bold text-blue-600 mt-2">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
            </div>
        @endforeach
    </div>
</div>
@endsection