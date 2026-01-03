<?php

namespace App\Filament\User\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Session;

class Cart extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?string $navigationLabel = 'Keranjang';
    
    protected static string $view = 'filament.user.pages.cart';
    
    protected static ?int $navigationSort = 2;

    public function mount(): void
    {
        // Load cart data when page is mounted
    }

    public function getCartItems(): array
    {
        return Session::get('cart', []);
    }

    public function getTotalPrice(): float
    {
        $cart = $this->getCartItems();
        $total = 0;
        
        foreach ($cart as $item) {
            $total += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
        }
        
        return $total;
    }

    public function removeFromCart($id): void
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);
        }
        
        $this->notify('success', 'Produk dihapus dari keranjang');
    }

    public function updateQuantity($id, $quantity): void
    {
        $cart = Session::get('cart', []);
        
        if (isset($cart[$id]) && $quantity > 0) {
            $cart[$id]['quantity'] = $quantity;
            Session::put('cart', $cart);
        }
    }

    public function clearCart(): void
    {
        Session::forget('cart');
        $this->notify('success', 'Keranjang dikosongkan');
    }
}