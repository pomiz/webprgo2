<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Toko Baju Lil</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; margin: 0; padding: 30px; color: #111; }
        .container { max-width: 960px; margin: auto; }
        h1 { font-size: 28px; font-weight: 700; margin-bottom: 30px; }
        .cart-container { background: white; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.08); padding: 30px; }
        .cart-item { display: grid; grid-template-columns: auto 1fr auto auto; align-items: center; gap: 20px; border-bottom: 1px solid #eee; padding: 20px 0; }
        .cart-item:first-child { padding-top: 0; }
        .cart-item:last-child { border-bottom: none; }
        .cart-item-img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
        .cart-item-name { font-weight: 600; font-size: 18px; }
        .cart-item-price { font-size: 16px; color: #555; }
        .cart-item-quantity { font-weight: 500; }
        .btn-remove { background: none; border: none; color: #dc3545; cursor: pointer; font-size: 14px; font-weight: 600; }
        .cart-header { display: flex; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 15px; margin-bottom: 10px; font-weight: 600; color: #555; }
        .cart-summary { border-top: 2px solid #eee; padding-top: 20px; margin-top: 30px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 18px; }
        .summary-row.total { font-size: 22px; font-weight: 700; }
        .btn-checkout { width: 100%; background: black; color: white; border: none; padding: 16px; border-radius: 10px; font-weight: 600; cursor: pointer; transition: .25s; margin-top: 10px; font-size: 16px; }
        .btn-checkout:hover { opacity: .85; }
        .btn-checkout:disabled { background: #ccc; cursor: not-allowed; }
        .back-link { display: inline-block; margin-top: 20px; font-size: 14px; color: #555; text-decoration: none; }
        .alert { padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .alert-success { background-color: #d4edda; color: #155724; }
        .alert-error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<div class="container">
    <h1>Keranjang Belanja Anda</h1>

    @if(Session::has('success'))
        <div class="alert alert-success">{{ Session::get('success') }}</div>
    @endif
    @if(Session::has('error'))
        <div class="alert alert-error">{{ Session::get('error') }}</div>
    @endif

    <div class="cart-container">
        @if(empty($cart))
            <p>Keranjang Anda kosong. <a href="{{ route('home') }}" class="back-link">Lanjutkan belanja</a></p>
        @else
            <form id="checkout-form" action="{{ route('checkout') }}" method="POST">
                @csrf
                <div class="cart-header">
                    <input type="checkbox" id="select-all" class="mr-4">
                    <label for="select-all">Pilih Semua</label>
                </div>

                @foreach($cart as $id => $details)
                    <div class="cart-item" data-id="{{ $id }}" data-price="{{ $details['price'] }}" data-quantity="{{ $details['quantity'] }}">
                        <input type="checkbox" name="selected_products[]" value="{{ $id }}" class="product-checkbox">
                        <img src="{{ asset('storage/' . $details['image']) }}" alt="{{ $details['name'] }}" class="cart-item-img">
                        <div>
                            <div class="cart-item-name">{{ $details['name'] }}</div>
                            <div class="cart-item-price">Rp {{ number_format($details['price'], 0, ',', '.') }}</div>
                        </div>
                        <div class="cart-item-quantity">x {{ $details['quantity'] }}</div>
                        
                        <a href="{{ route('cart.remove', $id) }}" class="btn-remove" title="Hapus item" onclick="return confirm('Anda yakin ingin menghapus item ini dari keranjang?')">Hapus</a>
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

    <a href="{{ route('home') }}" class="back-link">← Lanjutkan Belanja</a>

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
            
            // Enable/disable checkout button
            checkoutBtn.disabled = !somethingSelected;

            // Update select-all checkbox state
            const allProductCheckboxes = Array.from(productCheckboxes);
            if (allProductCheckboxes.length > 0) {
                selectAllCheckbox.checked = allProductCheckboxes.every(cb => cb.checked);
            }
        }

        selectAllCheckbox.addEventListener('change', function (e) {
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = e.target.checked;
            });
            updateCartSummary();
        });

        productCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateCartSummary);
        });

        // Initial calculation on page load
        updateCartSummary();
    });
</script>

</body>
</html>
