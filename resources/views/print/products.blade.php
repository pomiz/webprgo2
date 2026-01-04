<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Produk</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111827; }
        h1 { font-size: 18px; margin: 0 0 6px 0; }
        .meta { font-size: 11px; color: #6b7280; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; vertical-align: top; }
        th { background: #f9fafb; text-align: left; }
        .right { text-align: right; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
    <h1>Daftar Produk</h1>
    <div class="meta">
        Dicetak: {{ $generatedAt->format('d M Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 44px;">No</th>
                <th>Nama</th>
                <th style="width: 120px;">Kategori</th>
                <th style="width: 110px;" class="right">Harga</th>
                <th style="width: 70px;" class="right">Stok</th>
                <th style="width: 140px;" class="nowrap">Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $index => $product)
                <tr>
                    <td class="right">{{ $index + 1 }}</td>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->category }}</td>
                    <td class="right">Rp{{ number_format((float) $product->price, 0, ',', '.') }}</td>
                    <td class="right">{{ $product->stock }}</td>
                    <td class="nowrap">{{ optional($product->created_at)->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Belum ada produk.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
