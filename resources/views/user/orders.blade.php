@extends('layouts.user')
@section('title', 'Pesanan Saya - Ruang Baju')

@section('content')
    <h1 class="font-serif text-3xl font-bold text-gray-900 dark:text-white mb-8">Pesanan Saya</h1>

    @if($orders->isEmpty())
        <div class="text-center py-16">
            <svg class="w-20 h-20 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-600 dark:text-gray-400 mb-2">Belum ada pesanan</h3>
            <p class="text-sm text-gray-500 dark:text-gray-500 mb-6">Mulai belanja dan pesanan Anda akan muncul di sini.</p>
            <a href="{{ route('home') }}" class="btn-primary inline-block">Mulai Belanja</a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($orders as $order)
                <div class="card p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        {{-- Order Info --}}
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="font-medium text-gray-900 dark:text-white">
                                    Order #{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                </h3>
                                @php
                                    $colorMap = [
                                        'pending_payment' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-300',
                                        'confirmed' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300',
                                        'processing' => 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-300',
                                        'shipped' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300',
                                        'completed' => 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300',
                                        'cancelled' => 'bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300',
                                    ];
                                    $badgeClass = $colorMap[$order->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach($order->items->take(3) as $item)
                                    <span class="text-xs bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300 px-2 py-1 rounded-lg">
                                        {{ $item->product->name ?? 'Produk dihapus' }} (x{{ $item->quantity }})
                                    </span>
                                @endforeach
                                @if($order->items->count() > 3)
                                    <span class="text-xs text-gray-500 dark:text-gray-400 px-2 py-1">
                                        +{{ $order->items->count() - 3 }} lainnya
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Price & Action --}}
                        <div class="text-right flex-shrink-0">
                            <p class="text-lg font-bold text-gray-900 dark:text-white">
                                Rp {{ number_format($order->total_price, 0, ',', '.') }}
                            </p>
                            <a href="{{ route('invoice.show', $order) }}" class="inline-block mt-2 text-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400">
                                Lihat Invoice &rarr;
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
