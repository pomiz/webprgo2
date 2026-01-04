<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - Ruang Baju</title>
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

        /* NAVIGATION */
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

        /* CART BADGE */
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
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

        .dark-mode .navbar {
            background: linear-gradient(135deg, #4a5fc1 0%, #5a3a7a 100%);
        }
    </style>
</head>

<body>

<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('home') }}">
            🛍️ Ruang Baju
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('products.index') }}">
                        <i class="bi bi-grid me-1"></i>Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('cart.index') }}">
                        <i class="bi bi-cart3 me-1"></i>Keranjang
                    </a>
                </li>
            </ul>
            
            <ul class="navbar-nav">
                <li class="nav-item">
                    <button id="darkModeToggle" class="btn btn-sm btn-outline-light rounded-pill px-3">
                        ☀️ / 🌙
                    </button>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('profile.edit') }}">Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item">Logout</button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold m-0">🛍️ Semua Produk</h3>
            <p class="text-muted mb-0">Temukan pakaian casual dan minimalist favorit Anda</p>
        </div>
        <div>
            <span class="badge bg-success fs-6">
                {{ $products->count() }} Produk
            </span>
        </div>
    </div>

    <!-- Product List -->
    <div class="row">
        @forelse ($products as $product)
            <div class="col-md-4 mb-4">
                <div class="product-card">
                    <img src="{{ $product->image }}"
                         class="product-img"
                         alt="{{ $product->name }}"
                         onerror="this.src='https://via.placeholder.com/400x400?text=No+Image';">

                    <div class="p-3 d-flex flex-column">
                        <span class="category-badge mb-2">{{ $product->category }}</span>
                        <h5 class="fw-bold">{{ $product->name }}</h5>
                        <p class="text-muted small mb-1">{{ Str::limit($product->description, 65) }}</p>
                        <h5 class="text-primary fw-bold mb-3">Rp {{ number_format($product->price, 0, ',', '.') }}</h5>

                        <div class="d-flex gap-2">
                            <a href="{{ route('product.detail', $product->id) }}"
                               class="btn btn-outline-primary btn-custom flex-fill">
                                <i class="bi bi-eye me-1"></i>Detail
                            </a>
                            <a href="{{ route('cart.add', $product->id) }}"
                               class="btn btn-primary btn-custom flex-fill">
                                <i class="bi bi-cart-plus me-1"></i>+ Keranjang
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center mt-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <h4 class="mt-3 text-muted">Belum ada produk</h4>
                    <p class="text-muted">Admin belum menambahkan produk apa pun.</p>
                </div>
            </div>
        @endforelse
    </div>

</div>

<script>
    const body = document.body;
    const toggle = document.getElementById('darkModeToggle');
    const savedTheme = localStorage.getItem('theme');

    if (savedTheme === 'dark') body.classList.add('dark-mode');

    toggle.addEventListener('click', () => {
        body.classList.toggle('dark-mode');
        localStorage.setItem('theme',
            body.classList.contains('dark-mode') ? 'dark' : 'light'
        );
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>