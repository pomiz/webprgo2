<x-app-layout>
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">Daftar Produk</h1>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($products as $product)
                <div class="border rounded-lg p-4 shadow bg-white dark:bg-gray-800">
                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover rounded">
                    <h2 class="text-lg font-semibold mt-3 dark:text-white">{{ $product->name }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">{{ Str::limit($product->description, 100) }}</p>
                    <p class="font-bold text-blue-600 mt-2">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                    
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('product.detail', $product->id) }}" class="flex-1 bg-gray-200 dark:bg-gray-700 text-center py-2 rounded font-semibold text-sm">Detail</a>
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full bg-black text-white py-2 rounded font-semibold text-sm">Add to Cart</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>