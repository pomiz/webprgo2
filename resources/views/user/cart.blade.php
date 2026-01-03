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
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .cart-item {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }

        .cart-total {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
    </style>
</head>

<body>

<div class="container py-5">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">🛒 Keranjang Belanja</h2>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Lanjut Belanja
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(count($cart) > 0)
        <div class="row">
            <div class="col-md-8">
                @foreach($cart as $id => $details)
                    <div class="cart-item">
                        <img src="{{ $details['image'] }}" 
                             alt="{{ $details['name'] }}"
                             onerror="this.src='https://via.placeholder.com/100x100?text=No+Image';">
                        
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1">{{ $details['name'] }}</h5>
                            <p class="text-muted mb-2">
                                Quantity: <span class="fw-semibold">{{ $details['quantity'] }}</span>
                            </p>
                            <p class="text-primary fw-bold mb-0">
                                Rp {{ number_format($details['price'], 0, ',', '.') }}
                            </p>
                        </div>
                        
                        <div>
                            <p class="fw-bold text-end mb-2">
                                Rp {{ number_format($details['price'] * $details['quantity'], 0, ',', '.') }}
                            </p>
                            <a href="{{ route('cart.remove', $id) }}" 
                               class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash me-1"></i>Hapus
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="col-md-4">
                <div class="cart-total">
                    <h4 class="fw-bold mb-3">Ringkasan Belanja</h4>
                    
                    @php
                        $total = 0;
                        foreach($cart as $details) {
                            $total += $details['price'] * $details['quantity'];
                        }
                    @endphp
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span class="fw-semibold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Total:</span>
                        <span class="fw-bold text-primary fs-5">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    
                    @php
                        $whatsappMessage = "Halo, saya ingin memesan:\n\n";
                        foreach($cart as $details) {
                            $whatsappMessage .= "- {$details['name']} ({$details['quantity']}x) = Rp " . number_format($details['price'] * $details['quantity'], 0, ',', '.') . "\n";
                        }
                        $whatsappMessage .= "\nTotal: Rp " . number_format($total, 0, ',', '.');
                    @endphp
                    
                    <a href="https://wa.me/62895359586490?text={{ urlencode($whatsappMessage) }}" 
                       target="_blank"
                       class="btn btn-success w-100 py-3 fw-bold">
                        <i class="bi bi-whatsapp me-2"></i>Checkout via WhatsApp
                    </a>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h4 class="mt-4 text-muted">Keranjang belanja Anda kosong</h4>
            <p class="text-muted">Mulai belanja dan tambahkan produk ke keranjang!</p>
            <a href="{{ route('home') }}" class="btn btn-primary mt-3">
                <i class="bi bi-shop me-2"></i>Mulai Belanja
            </a>
        </div>
    @endif

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
