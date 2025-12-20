<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Data Product</title>

    <style>
        :root {
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-800: #1f2937;
        }

        body {
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI",
                         Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            font-size: 12px;
            color: var(--gray-800);
            background: white;
            margin: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 24px;
        }

        .header h1 {
            font-size: 18px;
            font-weight: 600;
            margin: 0;
        }

        .header p {
            margin-top: 4px;
            font-size: 11px;
            color: var(--gray-600);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        thead {
            background: var(--gray-100);
        }

        th {
            text-align: left;
            font-weight: 600;
            padding: 10px 12px;
            border-bottom: 1px solid var(--gray-300);
            font-size: 11px;
            color: var(--gray-600);
            text-transform: uppercase;
        }

        td {
            padding: 10px 12px;
            border-bottom: 1px solid var(--gray-200);
            font-size: 12px;
        }

        tbody tr:nth-child(even) {
            background: var(--gray-50);
        }

        .footer {
            margin-top: 24px;
            font-size: 10px;
            color: var(--gray-600);
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 16px;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <div class="header">
        <h1>Laporan Data Produk</h1>
        <p>Ruang Baju</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nama Product</th>
                <th>Kategori</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Tanggal Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($produk as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category }}</td>
                    <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                    <td>{{ $item->stock }}</td>
                    <td>{{ $item->created_at->format('d M Y, H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada {{ now()->format('d M Y, H:i') }}
    </div>

</body>
</html>
