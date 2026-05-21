@extends('layouts.user')
@section('title', $product->name . ' - Ruang Baju')

@section('content')
    {{-- Breadcrumb --}}
    <nav class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('home') }}" class="hover:text-brand-600">Home</a></li>
            <li><span>/</span></li>
            <li><a href="{{ route('home', ['category' => $product->category]) }}" class="hover:text-brand-600">{{ $product->category }}</a></li>
            <li><span>/</span></li>
            <li class="text-gray-900 dark:text-white font-medium">{{ $product->name }}</li>
        </ol>
    </nav>

    {{-- Product Detail --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        {{-- Image --}}
        <div class="rounded-2xl overflow-hidden bg-gray-100 dark:bg-surface-800">
            <img src="{{ asset('storage/' . $product->image) }}"
                 alt="{{ $product->name }}"
                 class="w-full h-[500px] object-cover"
                 onerror="this.src='https://via.placeholder.com/600x600?text=No+Image';">
        </div>

        {{-- Info --}}
        <div class="flex flex-col justify-center">
            <span class="inline-block text-xs font-medium text-brand-600 dark:text-brand-400 bg-brand-50 dark:bg-brand-900/30 px-3 py-1 rounded-md mb-4 w-fit">
                {{ $product->category }}
            </span>

            <h1 class="font-serif text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                {{ $product->name }}
            </h1>

            <p class="text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                {{ $product->description }}
            </p>

            <div class="text-3xl font-bold text-gray-900 dark:text-white mb-6">
                Rp {{ number_format($product->price, 0, ',', '.') }}
            </div>

            {{-- Stock Info --}}
            <div class="mb-6">
                @if($product->stock > 0)
                    <span class="inline-flex items-center gap-1.5 text-sm text-green-600 dark:text-green-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Stok tersedia ({{ $product->stock }})
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 text-sm text-red-600 dark:text-red-400">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        Stok habis
                    </span>
                @endif
            </div>

            {{-- Add to Cart Form --}}
            @if($product->stock > 0)
                <form action="{{ route('cart.add', $product->id) }}" method="POST" class="flex items-center gap-4">
                    @csrf
                    <div class="flex items-center border border-gray-200 dark:border-gray-700 rounded-lg">
                        <button type="button" onclick="decrementQty()" class="px-3 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-l-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                            </svg>
                        </button>
                        <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}"
                               class="w-16 text-center border-0 focus:ring-0 dark:bg-surface-900 dark:text-white text-sm">
                        <button type="button" onclick="incrementQty({{ $product->stock }})" class="px-3 py-2 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-r-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>
                    <button type="submit" class="btn-primary flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        Tambah ke Keranjang
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Prev/Next Navigation --}}
    <div class="flex items-center justify-between mt-12 pt-8 border-t border-gray-100 dark:border-gray-800">
        @if($previous)
            <a href="{{ route('product.detail', $previous->id) }}" class="flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-brand-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                {{ Str::limit($previous->name, 30) }}
            </a>
        @else
            <div></div>
        @endif

        @if($next)
            <a href="{{ route('product.detail', $next->id) }}" class="flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-brand-600">
                {{ Str::limit($next->name, 30) }}
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        @endif
    </div>

    {{-- Reviews Section Placeholder --}}
    <section class="mt-12" id="reviews">
        <h2 class="font-serif text-2xl font-bold text-gray-900 dark:text-white mb-6">Review</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada review untuk produk ini.</p>
    </section>

    {{-- Quantity JS --}}
    <script>
        function decrementQty() {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
        }
        function incrementQty(max) {
            const input = document.getElementById('quantity');
            if (parseInt(input.value) < max) input.value = parseInt(input.value) + 1;
        }
    </script>
@endsection
