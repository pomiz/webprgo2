<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Keranjang Belanja</h2>
            
            @if(count($this->getCartItems()) > 0)
                <div class="space-y-4">
                    @foreach($this->getCartItems() as $id => $item)
                        <div class="border rounded-lg p-4 flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="font-semibold text-lg">{{ $item['name'] ?? 'Produk' }}</h3>
                                <p class="text-gray-600">Rp. {{ number_format($item['price'] ?? 0, 0, ',', '.') }}</p>
                            </div>
                            
                            <div class="flex items-center space-x-4">
                                <div class="flex items-center space-x-2">
                                    <button 
                                        wire:click="updateQuantity('{{ $id }}', {{ ($item['quantity'] ?? 1) - 1 }})"
                                        class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center"
                                    >
                                        -
                                    </button>
                                    <span class="w-12 text-center">{{ $item['quantity'] ?? 1 }}</span>
                                    <button 
                                        wire:click="updateQuantity('{{ $id }}', {{ ($item['quantity'] ?? 1) + 1 }})"
                                        class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center"
                                    >
                                        +
                                    </button>
                                </div>
                                
                                <div class="text-right">
                                    <p class="font-semibold">Rp. {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 0, ',', '.') }}</p>
                                </div>
                                
                                <button 
                                    wire:click="removeFromCart('{{ $id }}')"
                                    class="text-red-500 hover:text-red-700"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-6 border-t pt-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-semibold">Total:</h3>
                        <p class="text-2xl font-bold text-primary-600">Rp. {{ number_format($this->getTotalPrice(), 0, ',', '.') }}</p>
                    </div>
                    
                    <div class="flex space-x-4">
                        <button 
                            wire:click="clearCart"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                        >
                            Kosongkan Keranjang
                        </button>
                        
                        <a href="{{ route('checkout') }}" class="px-6 py-2 bg-primary-600 text-white rounded-md hover:bg-primary-700">
                            Checkout
                        </a>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Keranjang kosong</h3>
                    <p class="mt-1 text-sm text-gray-500">Belum ada produk di keranjang belanja Anda.</p>
                    <div class="mt-6">
                        <a href="{{ route('products') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                            Belanja Sekarang
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>