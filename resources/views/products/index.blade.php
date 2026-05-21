@extends('layouts.user')
@section('title', 'Produk - Ruang Baju')

@section('content')
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="font-serif text-3xl font-bold text-gray-900 dark:text-white">Semua Produk</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Temukan pakaian casual dan minimalist favorit Anda</p>
        </div>
        <span class="inline-flex items-center bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-sm font-medium px-3 py-1 rounded-full">
            {{ $products->count() }} Produk
        </span>
    </div>

    {{-- Product Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($products as $product)
            <div class="card group overflow-hidden">
                <div class="aspect-[4/5] overflow-hidden">
                    <img src="{{ $product->image }}"
                         alt="{{ $product->name }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                         onerror="this.src='https://via.placeholder.com/400x500?text=No+Image';">
                </div>
                <div class="p-5">
                    <span class="inline-block text-xs font-medium text-brand-600 dark:text-brand-400 bg-brand-50 dark:bg-brand-900/30 px-2.5 py-1 rounded-md mb-2">
                        {{ $product->category }}
                    </span>
                    <h3 class="font-serif font-semibold text-lg text-gray-900 dark:text-white mb-1">{{ $product->name }}</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">{{ Str::limit($product->description, 65) }}</p>
                    <p class="text-lg font-bold text-gray-900 dark:text-white mb-4">Rp {{ number_format($product->price, 0, ',', '.') }}</p>

                    <div class="flex gap-2">
                        <a href="{{ route('product.detail', $product->id) }}"
                           class="flex-1 text-center text-sm font-semibold border border-brand-600 text-brand-600 dark:border-brand-400 dark:text-brand-400 hover:bg-brand-50 dark:hover:bg-brand-900/20 px-4 py-2 rounded-lg transition-colors">
                            Detail
                        </a>
                        <a href="{{ route('cart.add', $product->id) }}"
                           class="flex-1 text-center text-sm font-semibold bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-lg transition-colors">
                            + Keranjang
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16">
                <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-600 dark:text-gray-400 mb-2">Belum ada produk</h3>
                <p class="text-sm text-gray-500 dark:text-gray-500">Admin belum menambahkan produk apa pun.</p>
            </div>
        @endforelse
    </div>
@endsection
