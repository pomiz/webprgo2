@extends('layouts.user')
@section('title', 'Keranjang - Ruang Baju')

@section('content')
    <h1 class="font-serif text-3xl font-bold text-gray-900 dark:text-white mb-8">Keranjang Belanja</h1>

    @if($cartItems->isEmpty())
        <div class="text-center py-16">
            <svg class="w-20 h-20 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-600 dark:text-gray-400 mb-2">Keranjang kosong</h3>
            <p class="text-sm text-gray-500 dark:text-gray-500 mb-6">Belum ada produk di keranjang Anda.</p>
            <a href="{{ route('home') }}" class="btn-primary inline-block">Mulai Belanja</a>
        </div>
    @else
        {{-- Checkout form (hidden, only contains csrf and submit logic) --}}
        <form action="{{ route('checkout.prepare') }}" method="POST" id="checkout-form">
            @csrf
        </form>

        {{-- Delete forms (outside checkout form to avoid nesting) --}}
        @foreach($cartItems as $item)
            <form id="delete-form-{{ $item->product_id }}" action="{{ route('cart.remove', $item->product_id) }}" method="POST" style="display:none;">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Cart Items --}}
            <div class="lg:col-span-2 space-y-4">
                @foreach($cartItems as $item)
                    <div class="card p-4 flex items-center gap-4">
                        {{-- Checkbox (linked to checkout form) --}}
                        <input type="checkbox" name="selected_products[]" value="{{ $item->product_id }}"
                               form="checkout-form"
                               class="w-5 h-5 rounded border-gray-300 text-brand-600 focus:ring-brand-500"
                               checked>

                        {{-- Image --}}
                        <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0 bg-gray-100 dark:bg-surface-800">
                            <img src="{{ (str_starts_with($item->product->image, 'http') ? $item->product->image : asset('storage/' . $item->product->image)) }}"
                                 alt="{{ $item->product->name }}"
                                 class="w-full h-full object-cover"
                                 onerror="this.src='https://via.placeholder.com/80x80?text=No+Image';">
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-gray-900 dark:text-white truncate">{{ $item->product->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Rp {{ number_format($item->product->price, 0, ',', '.') }} x {{ $item->quantity }}
                            </p>
                        </div>

                        {{-- Subtotal --}}
                        <div class="text-right">
                            <p class="font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}
                            </p>
                        </div>

                        {{-- Remove (submits separate delete form) --}}
                        <button type="submit" form="delete-form-{{ $item->product_id }}" class="flex-shrink-0 p-2 text-gray-400 hover:text-red-500 transition-colors" title="Hapus">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>

            {{-- Summary Sidebar --}}
            <div class="lg:col-span-1">
                <div class="card p-6 sticky top-24">
                    <h3 class="font-serif text-lg font-semibold text-gray-900 dark:text-white mb-4">Ringkasan</h3>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Total Item</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $cartItems->sum('quantity') }} produk</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Subtotal</span>
                            <span class="font-medium text-gray-900 dark:text-white">
                                Rp {{ number_format($cartItems->sum(fn($item) => $item->product->price * $item->quantity), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-700 pt-4 mb-6">
                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                            <span class="font-bold text-lg text-gray-900 dark:text-white">
                                Rp {{ number_format($cartItems->sum(fn($item) => $item->product->price * $item->quantity), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <button type="submit" form="checkout-form" class="btn-primary w-full text-center">
                        Checkout
                    </button>
                </div>
            </div>
        </div>
    @endif
@endsection
