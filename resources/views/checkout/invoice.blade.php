@extends('layouts.user')
@section('title', 'Invoice #' . $order->id . ' - Ruang Baju')

@section('content')
    <div class="max-w-3xl mx-auto">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-green-800 dark:text-green-300">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-3 flex items-center gap-3">
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</p>
            </div>
        @endif

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
                @if($order->courier)
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Kurir</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->courier }}</span>
                </div>
                @endif
                <div class="flex justify-between items-center pt-3 border-t border-gray-100 dark:border-gray-700">
                    <span class="font-semibold text-gray-900 dark:text-white">Total</span>
                    <span class="text-xl font-bold text-gray-900 dark:text-white">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- QR Code (pending_payment only) --}}
            @if($order->status === 'pending_payment')
                <div class="mt-8 text-center">
                    @php
                        $qrData = 'QRIS:' . $order->virtual_account . '|Total:' . $order->total_price . '|Order:' . $order->id;
                        $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrData);
                    @endphp
                    <img src="{{ $qrUrl }}" alt="QR Pembayaran" class="mx-auto rounded-lg" width="200" height="200">
                    <p class="mt-3 text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $order->virtual_account }}</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Scan QR code untuk melakukan pembayaran</p>
                </div>

                {{-- Tombol Konfirmasi Bayar --}}
                <form action="{{ route('invoice.pay', $order) }}" method="POST" class="mt-6"
                      onsubmit="return confirm('Konfirmasi pembayaran? Pastikan Anda sudah melakukan transfer.')">
                    @csrf
                    <button type="submit" class="btn-primary w-full flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Saya Sudah Bayar
                    </button>
                </form>
            @endif

            {{-- Success Box (after payment) --}}
            @if(in_array($order->status, ['confirmed', 'processing', 'shipped', 'completed']))
                <div class="mt-8 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-medium text-green-800 dark:text-green-300">Pembayaran Berhasil!</p>
                        <p class="text-sm text-green-600 dark:text-green-400">Pesanan Anda sedang diproses oleh tim kami.</p>
                    </div>
                </div>
            @endif

            {{-- Tracking Progress (shipped only) --}}
            @if($order->status === 'shipped' && $order->tracking_status)
                <div class="mt-8 border-t border-gray-100 dark:border-gray-700 pt-6">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"/>
                        </svg>
                        <h3 class="font-semibold text-gray-900 dark:text-white">
                            Status Pengiriman: {{ $order->courier ?? 'Kurir' }} - {{ $order->tracking_number ?? '-' }}
                        </h3>
                    </div>

                    @php
                        $trackingSteps = [
                            'picked_up' => 'Dijemput Kurir',
                            'in_transit' => 'Dalam Perjalanan',
                            'arrived' => 'Tiba di Tujuan',
                            'delivered' => 'Diterima',
                        ];
                        $currentStepIndex = array_search($order->tracking_status, array_keys($trackingSteps));
                    @endphp

                    <div class="relative">
                        {{-- Progress bar --}}
                        <div class="absolute top-4 left-0 right-0 h-1 bg-gray-200 dark:bg-gray-700 rounded">
                            @php
                                $progress = ($currentStepIndex / (count($trackingSteps) - 1)) * 100;
                            @endphp
                            <div class="h-1 bg-purple-500 rounded transition-all" style="width: {{ $progress }}%"></div>
                        </div>

                        <div class="relative flex justify-between">
                            @foreach($trackingSteps as $key => $label)
                                @php
                                    $stepIndex = array_search($key, array_keys($trackingSteps));
                                    $isCompleted = $stepIndex <= $currentStepIndex;
                                    $isCurrent = $stepIndex === $currentStepIndex;
                                @endphp
                                <div class="flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center relative z-10 text-xs font-bold
                                        {{ $isCompleted ? 'bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-500' }}">
                                        @if($isCompleted)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @else
                                            {{ $stepIndex + 1 }}
                                        @endif
                                    </div>
                                    <p class="mt-2 text-xs font-medium text-center
                                        {{ $isCurrent ? 'text-purple-600 dark:text-purple-400' : ($isCompleted ? 'text-gray-600 dark:text-gray-400' : 'text-gray-400 dark:text-gray-500') }}">
                                        {{ $label }}
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-4">
                        @if($order->shipped_at)
                            Dikirim: {{ \Carbon\Carbon::parse($order->shipped_at)->format('d M Y, H:i') }}
                        @endif
                    </p>
                </div>
            @endif

            {{-- Shipping Address --}}
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
