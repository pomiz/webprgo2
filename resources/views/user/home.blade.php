@extends('layouts.user')
@section('title', 'Ruang Baju - Home')

@section('content')
    {{-- Hero Section --}}
    <section class="relative rounded-2xl overflow-hidden mb-12">
        <img src="https://images.unsplash.com/photo-1540221652346-e5dd6b50f3e7?w=1200&auto=format&fit=crop&q=60"
             alt="Hero" class="w-full h-80 object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-black/70 to-black/30 flex items-center">
            <div class="px-8 md:px-12">
                <h1 class="text-3xl md:text-5xl font-serif font-bold text-white mb-3">Casual & Minimalist Fashion</h1>
                <p class="text-gray-200 text-lg max-w-lg">Pakaian unisex dari balita hingga remaja — nyaman, stylish, dan modern.</p>
            </div>
        </div>
    </section>

    {{-- Search --}}
    <form method="GET" action="{{ route('home') }}" class="mb-8">
        <div class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   class="flex-1 rounded-lg border-gray-200 dark:border-gray-700 dark:bg-surface-800 dark:text-white px-4 py-3 text-sm focus:ring-brand-500 focus:border-brand-500"
                   placeholder="Cari produk... contoh: hoodie, kaos, sweater">
            <button type="submit" class="btn-primary">Cari</button>
        </div>
    </form>

    {{-- Categories --}}
    <div class="flex flex-wrap gap-2 mb-8">
        <a href="{{ route('home') }}"
           class="{{ request('category') ? 'btn-outline' : 'btn-primary' }} text-sm !py-2 !px-4">
            Semua
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('home', ['category' => $cat]) }}"
               class="{{ request('category') == $cat ? 'btn-primary' : 'btn-outline' }} text-sm !py-2 !px-4">
                {{ $cat }}
            </a>
        @endforeach
    </div>

    {{-- Search/Filter Info --}}
    @if($search)
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg px-4 py-3 mb-6 flex items-center justify-between">
            <span class="text-sm text-blue-700 dark:text-blue-300">
                Hasil pencarian untuk "<strong>{{ $search }}</strong>": {{ $products->count() }} produk
            </span>
            <a href="{{ route('home') }}" class="text-sm text-blue-600 hover:underline">Clear</a>
        </div>
    @endif

    @if($category && !$search)
        <div class="bg-gray-50 dark:bg-surface-800 border border-gray-200 dark:border-gray-700 rounded-lg px-4 py-3 mb-6 flex items-center justify-between">
            <span class="text-sm text-gray-600 dark:text-gray-300">
                Kategori: <strong>{{ $category }}</strong> ({{ $products->count() }} produk)
            </span>
            <a href="{{ route('home') }}" class="text-sm text-brand-600 hover:underline">Lihat Semua</a>
        </div>
    @endif

    {{-- Product Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
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
                    @if($product->reviewCount() > 0)
                        <div class="flex items-center gap-1 mb-1">
                            <x-star-rating :rating="$product->averageRating()" size="w-3.5 h-3.5" />
                            <span class="text-xs text-gray-500 dark:text-gray-400">({{ $product->reviewCount() }})</span>
                        </div>
                    @endif
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-600 dark:text-gray-400 mb-2">
                    @if($search)
                        Produk "{{ $search }}" tidak ditemukan
                    @else
                        Tidak ada produk di kategori "{{ $category }}"
                    @endif
                </h3>
                <a href="{{ route('home') }}" class="btn-primary inline-block mt-4">Lihat Semua Produk</a>
            </div>
        @endforelse
    </div>
@endsection
