@extends('layouts.user')
@section('title', 'Semua Produk - Ruang Baju')

@section('content')
    <div class="mb-8">
        <h1 class="font-serif text-3xl font-bold text-gray-900 dark:text-white">Semua Produk</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Koleksi lengkap pakaian Ruang Baju</p>
    </div>

    {{-- Product Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($products as $product)
            <div class="card group overflow-hidden">
                <div class="aspect-[4/5] overflow-hidden">
                    <img src="{{ asset('storage/' . $product->image) }}"
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
                    <div class="flex items-center justify-between">
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <a href="{{ route('product.detail', $product->id) }}"
                           class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">
                            Detail &rarr;
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
                <p class="text-sm text-gray-500 dark:text-gray-500">Produk akan segera tersedia.</p>
            </div>
        @endforelse
    </div>
@endsection
