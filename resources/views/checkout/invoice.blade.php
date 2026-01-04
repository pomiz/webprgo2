<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Pesanan #{{ $order->id }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 30px;
            color: #333;
        }
        .invoice-container {
            max-width: 800px;
            margin: auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            padding: 40px;
        }
        .invoice-header {
            text-align: center;
            margin-bottom: 40px;
        }
        .invoice-header h1 {
            font-size: 28px;
            font-weight: 700;
            color: #155724;
            margin: 0;
        }
        .invoice-header p {
            font-size: 16px;
            color: #555;
        }
        .va-box {
            background: #e9f5ff;
            border: 2px dashed #0D6EFD;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        .va-box .va-title {
            font-size: 16px;
            font-weight: 600;
            color: #0a58ca;
            margin-bottom: 8px;
        }
        .va-box .va-number {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 1px;
            color: #000;
        }
        .order-details, .order-summary {
            margin-bottom: 30px;
        }
        .order-details h2, .order-summary h2 {
            font-size: 20px;
            font-weight: 600;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .details-grid div {
            font-size: 15px;
        }
        .details-grid span {
            font-weight: 600;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        .items-table th, .items-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .items-table th {
            background: #f8f9fa;
            font-weight: 600;
        }
        .items-table .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: 700;
            font-size: 18px;
        }
        
        /* Buttons */
        .footer {
            text-align: center;
            margin-top: 40px;
            display: flex;
            justify-content: center;
            gap: 15px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            font-size: 15px;
            border-radius: 10px;
            font-weight: 600;
            text-decoration: none;
            transition: .25s;
            cursor: pointer;
            border: none;
        }
        .btn-home {
            background: black;
            color: white;
        }
        .btn-home:hover {
            opacity: .85;
            color: white;
        }
        .btn-print {
            background: white;
            color: black;
            border: 2px solid black;
        }
        .btn-print:hover {
            background: #f1f1f1;
        }

        /* PRINT STYLES */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .invoice-container {
                box-shadow: none;
                max-width: 100%;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="invoice-container">
    <div class="invoice-header">
        <h1>Pembayaran Berhasil</h1>
        <p>Pesanan Anda telah kami terima. Terima kasih telah berbelanja!</p>
    </div>

    <div class="va-box">
        <div class="va-title">NOMOR VIRTUAL ACCOUNT</div>
        <div class="va-number">{{ $order->virtual_account }}</div>
    </div>

    <div class="order-details">
        <h2>Detail Pesanan</h2>
        <div class="details-grid">
            <div><span>Nomor Pesanan:</span> #{{ $order->id }}</div>
            <div><span>Tanggal Pesanan:</span> {{ $order->created_at->format('d F Y') }}</div>
            <div><span>Nama Pelanggan:</span> {{ $order->user->name }}</div>
            <div><span>Status:</span> <span style="color: #155724; font-weight:700;">{{ ucfirst($order->status) }}</span></div>
        </div>
    </div>

    <div class="order-summary">
        <h2>Rincian Barang</h2>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th class="text-right">Kuantitas</th>
                    <th class="text-right">Harga Satuan</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product->name }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="3" class="text-right">Total Keseluruhan</td>
                    <td class="text-right">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Tombol tidak akan muncul saat diprint karena class no-print -->
    <div class="footer no-print">
        <button onclick="window.print()" class="btn btn-print">
            <i class="bi bi-printer me-2" style="margin-right:8px;"></i> Cetak / Simpan PDF
        </button>
        <a href="{{ route('home') }}" class="btn btn-home">
            Kembali ke Beranda
        </a>
    </div>
</div>

</body>
</html>