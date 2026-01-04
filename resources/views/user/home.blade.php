<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Baju</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            transition: background .3s, color .3s;
        }

        .dark-mode {
            background-color: #0f0f0f !important;
            color: #e9e9e9 !important;
        }

        /* HERO */
        .hero {
            position: relative;
            background-image: url('https://images.unsplash.com/photo-1540221652346-e5dd6b50f3e7?w=1200&auto=format&fit=crop&q=60');
            background-size: cover;
            background-position: center;
            border-radius: 14px;
            padding: 80px 20px;
            text-align: center;
            overflow: hidden;
            color: white;
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.45);
        }

        .hero h2,
        .hero p {
            position: relative;
            z-index: 2;
        }

        /* PRODUCTS */
        .product-card {
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            border: 1px solid #e9e9e9;
            transition: .25s ease;
        }

        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }

        .product-img {
            height: 240px;
            object-fit: cover;
            background: #f1f1f1;
            width: 100%;
        }

        .category-badge {
            background: #f1f5f9;
            color: #333;
            font-size: 11px;
            border-radius: 6px;
            padding: 3px 10px;
            font-weight: 500;
        }

        .btn-custom {
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            padding: 8px;
        }

        /* DARK MODE APPLY */
        .dark-mode .product-card {
            background: #1a1a1a;
            border: 1px solid #2b2b2b;
        }

        .dark-mode .category-badge {
            background: #2b2b2b;
            color: #fff;
        }

        .dark-mode .hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.55);
        }

        /* Floating Cart Button */
        .floating-cart-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: #000;
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            text-decoration: none;
            transition: .25s;
            z-index: 1000;
        }
        .floating-cart-btn:hover {
            transform: scale(1.1);
            color: white;
        }
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            font-size: 12px;
            font-weight: 600;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>

<div class="container py-4">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold m-0">🛍️ Ruang Baju</h3>
            <div class="d-flex align-items-center gap-3">
                <span class="fw-semibold">
                    Halo, {{ auth()->user()->username }}
                </span>
    
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    <!-- Hero -->
    <div class="hero mb-5">
        <div class="hero-overlay"></div>
        <h2>Casual & Minimalist Fashion </h2>
        <p>Pakaian unisex dari balita hingga remaja — nyaman, stylish, dan modern.</p>
    </div>

    <!-- Search -->
    <form method="GET" action="{{ route('home') }}" class="input-group mb-4">
        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-lg"
               placeholder="Cari produk... contoh: hoodie, kaos, sweater">
        <button class="btn btn-primary btn-lg">Cari</button>
    </form>

    <!-- Categories -->
    <div class="text-center mb-4">
        <a href="{{ route('home') }}" class="btn btn-dark rounded-pill px-3 fw-semibold {{ request('category') ? '' : 'active' }}">
            Semua
        </a>

        @foreach($categories as $cat)
            <a href="{{ route('home', ['category' => $cat]) }}"
               class="btn btn-outline-dark rounded-pill px-3 fw-semibold {{ request('category') == $cat ? 'active' : '' }}">
                {{ $cat }}
            </a>
        @endforeach
    </div>

    <!-- Search Results Info -->
    @if($search)
        <div class="alert alert-info d-flex justify-content-between align-items-center" role="alert">
            <div>
                <i class="bi bi-search me-2"></i>
                <strong>Hasil pencarian untuk "{{ $search }}":</strong>
                {{ $products->count() }} produk ditemukan
            </div>
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i>Clear
            </a>
        </div>
    @endif

    <!-- Category Filter Info -->
    @if($category && !$search)
        <div class="alert alert-secondary" role="alert">
            <i class="bi bi-funnel me-2"></i>
            <strong>Kategori:</strong> {{ $category }}
            <span class="text-muted">({{ $products->count() }} produk)</span>
            <a href="{{ route('home') }}" class="btn btn-sm btn-outline-secondary ms-2">
                <i class="bi bi-x-circle me-1"></i>All
            </a>
        </div>
    @endif

    <!-- Product List -->
    <div class="row">
        @forelse ($products as $product)
            <div class="col-md-4 mb-4">
                <div class="product-card">
                    <img src="{{ asset('storage/' . $product->image) }}"
                         class="product-img"
                         alt="{{ $product->name }}"
                         onerror="this.src='https://via.placeholder.com/400x400?text=No+Image';">

                    <div class="p-3 d-flex flex-column">
                        <span class="category-badge mb-2">{{ $product->category }}</span>
                        <h5 class="fw-bold">{{ $product->name }}</h5>
                        <p class="text-muted small mb-1">{{ Str::limit($product->description, 65) }}</p>
                        <h5 class="text-primary fw-bold mb-3">Rp {{ number_format($product->price, 0, ',', '.') }}</h5>

                        <a href="{{ route('product.detail', $product->id) }}"
                           class="btn btn-outline-primary btn-custom mt-auto">
                            Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center mt-5">
                    <i class="bi bi-search display-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">
                        @if($search)
                            Produk dengan keyword "{{ $search }}" tidak ditemukan.
                        @else
                            Produk tidak ditemukan di kategori "{{ $category }}".
                        @endif
                    </h4>
                    <p class="text-muted">Coba dengan keyword lain atau lihat semua produk.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary">
                        <i class="bi bi-house me-2"></i>Lihat Semua Produk
                    </a>
                </div>
            </div>
        @endforelse
    </div>

</div>

<!-- Floating Cart Button -->
<a href="{{ route('cart.index') }}" class="floating-cart-btn">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-cart-fill" viewBox="0 0 16 16">
        <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
    </svg>
    @if(count(session('cart', [])) > 0)
        <span class="cart-count">{{ count(session('cart', [])) }}</span>
    @endif
</a>

</body>
</html>
