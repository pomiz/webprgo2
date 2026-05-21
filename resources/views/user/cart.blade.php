<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Ruang Baju</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; margin: 0; color: #111; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .cart-container { background: white; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); padding: 30px; }
        .cart-item { display: grid; grid-template-columns: auto auto 1fr auto auto; align-items: center; gap: 20px; border-bottom: 1px solid #eee; padding: 20px 0; }
        .cart-item:first-child { padding-top: 0; }
        .cart-item:last-child { border-bottom: none; }
        .cart-item-img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
        .cart-item-name { font-weight: 600; font-size: 18px; }
        .cart-item-price { font-size: 16px; color: #555; }
        .cart-item-quantity { font-weight: 500; }
        .btn-remove { background: none; border: none; color: #dc3545; cursor: pointer; font-size: 14px; font-weight: 600; text-decoration: none; }
        .cart-header { display: flex; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 15px; margin-bottom: 10px; font-weight: 600; color: #555; }
        .cart-summary { border-top: 2px solid #eee; padding-top: 20px; margin-top: 30px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 18px; }
        .summary-row.total { font-size: 22px; font-weight: 700; }
        .btn-checkout { width: 100%; background: black; color: white; border: none; padding: 16px; border-radius: 10px; font-weight: 600; cursor: pointer; transition: .25s; margin-top: 10px; font-size: 16px; }
        .btn-checkout:hover { opacity: .85; }
        .btn-checkout:disabled { background: #ccc; cursor: not-allowed; }
        .back-link { display: inline-block; margin-top: 20px; font-size: 14px; color: #555; text-decoration: none; }
    </style>
</head>

<body>
{{-- 
<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">🛍️ Ruang Baju</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link text-white">Halo, {{ auth()->user()->username }}</span>
                </li>
            </ul>
        </div>
    </div>
</nav> --}}

<div class="container">
    <h1 class="fw-bold mb-4">Keranjang Belanja</h1>

    @if(Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    @if(Session::has('error'))
        <div class="alert alert-error">{{ Session::get('error') }}</div>
    @endif

    <div class="cart-container">
        @if($cartItems->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-cart-x display-1 text-muted"></i>
                <p class="mt-3">Keranjang Anda kosong.</p>
                <a href="{{ route('home') }}" class="btn btn-primary rounded-pill">Mulai Belanja</a>
            </div>
        @else
            <form id="checkout-form" action="{{ route('checkout') }}" method="POST">
                @csrf
                <div class="cart-header">
                    <input type="checkbox" id="select-all" class="form-check-input me-2">
                    <label for="select-all">Pilih Semua</label>
                </div>

                @foreach($cartItems as $item)
                    <div class="cart-item" data-id="{{ $item->product_id }}" data-price="{{ $item->product->price }}" data-quantity="{{ $item->quantity }}">
                        <input type="checkbox" name="selected_products[]" value="{{ $item->product_id }}" class="product-checkbox form-check-input">
                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="cart-item-img" onerror="this.src='https://via.placeholder.com/80x80?text=No+Image';">
                        <div>
                            <div class="cart-item-name">{{ $item->product->name }}</div>
                            <div class="cart-item-price">Rp {{ number_format($item->product->price, 0, ',', '.') }}</div>
                        </div>
                        <div class="cart-item-quantity">x {{ $item->quantity }}</div>
                        
                        <form action="{{ route('cart.remove', $item->product_id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-remove" title="Hapus item" onclick="return confirm('Anda yakin ingin menghapus item ini dari keranjang?')">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                @endforeach
            </form>

            <div class="cart-summary">
                <div class="summary-row">
                    <span>Total Produk Terpilih</span>
                    <span id="total-items">0</span>
                </div>
                <div class="summary-row total">
                    <span>Total Harga</span>
                    <span>Rp <span id="total-price">0</span></span>
                </div>
                <button type="submit" form="checkout-form" id="checkout-btn" class="btn-checkout" disabled>Lanjut ke Checkout</button>
            </div>
        @endif
    </div>

    <a href="{{ route('home') }}" class="back-link">← Kembali Belanja</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('select-all');
        const productCheckboxes = document.querySelectorAll('.product-checkbox');
        const checkoutBtn = document.getElementById('checkout-btn');

        function updateCartSummary() {
            let totalItems = 0;
            let totalPrice = 0;
            let somethingSelected = false;

            productCheckboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    somethingSelected = true;
                    const itemElement = checkbox.closest('.cart-item');
                    const quantity = parseInt(itemElement.dataset.quantity);
                    const price = parseFloat(itemElement.dataset.price);
                    
                    totalItems += quantity;
                    totalPrice += quantity * price;
                }
            });

            document.getElementById('total-items').innerText = totalItems;
            document.getElementById('total-price').innerText = totalPrice.toLocaleString('id-ID');
            
            checkoutBtn.disabled = !somethingSelected;

            if (productCheckboxes.length > 0) {
                selectAllCheckbox.checked = Array.from(productCheckboxes).every(cb => cb.checked);
            }
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function (e) {
                productCheckboxes.forEach(checkbox => {
                    checkbox.checked = e.target.checked;
                });
                updateCartSummary();
            });
        }

        productCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateCartSummary);
        });

        updateCartSummary();
    });
</script>

</body>
</html>