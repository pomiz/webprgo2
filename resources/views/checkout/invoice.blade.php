@extends('layouts.user')
@section('title', 'Invoice #' . $order->id . ' - Ruang Baju')

@section('content')
    <div class="max-w-3xl mx-auto">
        {{-- Invoice Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="font-serif text-3xl font-bold text-gray-900 dark:text-white">Invoice</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
            <button onclick="window.print()" class="btn-outline text-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Cetak
            </button>
        </div>

        {{-- Invoice Card --}}
        <div class="card p-8">
            {{-- Store & Order Info --}}
            <div class="flex justify-between items-start mb-8 pb-6 border-b border-gray-100 dark:border-gray-700">
                <div>
                    <h2 class="font-serif text-xl font-bold text-gray-900 dark:text-white">Ruang Baju</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Fashion Store</p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-600 dark:text-gray-300">
                        <span class="text-gray-500 dark:text-gray-400">Tanggal:</span>
                        {{ $order->created_at->format('d M Y, H:i') }}
                    </p>
                    <div class="mt-2">
                        @if(in_array($order->status, ['confirmed', 'processing', 'shipped', 'completed']))
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300">
                                {{ $order->status_label }}
                            </span>
                        @elseif($order->status === 'cancelled')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300">
                                {{ $order->status_label }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300">
                                {{ $order->status_label }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="mb-8">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-700">
                            <th class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider pb-3">Produk</th>
                            <th class="text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider pb-3">Qty</th>
                            <th class="text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider pb-3">Harga</th>
                            <th class="text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider pb-3">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @foreach($order->items as $item)
                            <tr>
                                <td class="py-3 text-sm text-gray-900 dark:text-white">{{ $item->product->name ?? 'Produk dihapus' }}</td>
                                <td class="py-3 text-sm text-gray-600 dark:text-gray-300 text-center">{{ $item->quantity }}</td>
                                <td class="py-3 text-sm text-gray-600 dark:text-gray-300 text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                <td class="py-3 text-sm font-medium text-gray-900 dark:text-white text-right">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Subtotal</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Ongkir</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-700">
                    <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Virtual Account --}}
            <div class="mt-8 bg-brand-50 dark:bg-brand-900/20 border border-brand-200 dark:border-brand-800 rounded-lg p-4">
                <p class="text-sm font-medium text-brand-800 dark:text-brand-300 mb-1">Virtual Account</p>
                <p class="text-lg font-bold text-brand-900 dark:text-brand-200 font-mono tracking-wider">{{ $order->virtual_account }}</p>
                <p class="text-xs text-brand-600 dark:text-brand-400 mt-1">Gunakan nomor ini untuk melakukan pembayaran</p>
            </div>

            @if($order->shipping_address)
            <div class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                <span class="font-medium">Alamat Pengiriman:</span> {{ $order->shipping_address }}
            </div>
            @endif
        </div>

        {{-- Back Button --}}
        <div class="mt-6 text-center">
            <a href="{{ route('home') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">
                &larr; Kembali ke Beranda
            </a>
        </div>
    </div>
@endsection
