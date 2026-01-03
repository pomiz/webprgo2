<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} - Detail Produk</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 30px;
            color: #111;
        }

        .container {
            max-width: 1080px;
            margin: auto;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            background: #f1f1f1;
        }

        .details {
            padding: 40px;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .desc {
            font-size: 14px;
            line-height: 1.6;
            color: #555;
            margin-bottom: 20px;
        }

        .price {
            font-size: 24px;
            font-weight: 700;
            color: #0D6EFD;
            margin-bottom: 32px;
        }

        .btn-primary {
            display: inline-block;
            padding: 14px 22px;
            font-size: 15px;
            border-radius: 10px;
            font-weight: 600;
            background: black;
            color: white;
            text-decoration: none;
            letter-spacing: .3px;
            transition: .25s;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            opacity: .85;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            font-size: 14px;
            color: #555;
            text-decoration: none;
            transition: .25s;
        }

        .back-link:hover {
            color: #000;
        }

        /* Responsive */
        @media (max-width: 900px) {
            .container {
                grid-template-columns: 1fr;
            }
            .product-img {
                height: 380px;
            }
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

        /* Product Navigation */
        .nav-arrow {
            position: fixed;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.8);
            color: #333;
            padding: 15px;
            text-decoration: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: .25s;
            z-index: 1001;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .nav-arrow:hover {
            background: white;
            transform: translateY(-50%) scale(1.1);
        }
        .nav-prev {
            left: 40px;
        }
        .nav-next {
            right: 40px;
        }
    </style>
</head>
<body>

<div class="container">
    
    <!-- Image -->
    <img src="{{ asset('storage/' . $product->image) }}" 
         alt="{{ $product->name }}" class="product-img">

    <!-- Details -->
    <div class="details">
        <h1>{{ $product->name }}</h1>
        <p class="desc">{{ $product->description }}</p>
        <p class="price">Rp {{ number_format($product->price, 0, ',', '.') }}</p>

        <form action="{{ route('cart.add', $product) }}" method="POST">
            @csrf
            <div class="flex items-center mb-4">
                <label for="quantity" class="mr-2">Quantity:</label>
                <input type="number" name="quantity" id="quantity" value="1" min="1" max="{{ $product->stock }}" class="border rounded px-2 py-1 w-20">
                <span class="ml-2 text-sm text-gray-600">Stock: {{ $product->stock }}</span>
            </div>
            <button type="submit" class="btn-primary" style="margin-top: 1rem;">Add to Cart</button>
        </form>

        <a href="{{ route('home') }}" class="back-link">← Kembali ke beranda</a>
    </div>

</div>

<!-- Page Navigation -->
@if($previous)
    <a href="{{ route('product.detail', $previous->id) }}" class="nav-arrow nav-prev" title="Produk Sebelumnya">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
        </svg>
    </a>
@endif

@if($next)
    <a href="{{ route('product.detail', $next->id) }}" class="nav-arrow nav-next" title="Produk Selanjutnya">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
          <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
        </svg>
    </a>
@endif

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
