<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar User</title>
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
    <h1>Daftar User</h1>
    <div class="meta">
        Dicetak: {{ $generatedAt->format('d M Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 44px;">No</th>
                <th>Nama</th>
                <th>Email</th>
                <th style="width: 90px;">Role</th>
                <th style="width: 140px;" class="nowrap">Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $index => $user)
                <tr>
                    <td class="right">{{ $index + 1 }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ ucfirst($user->role) }}</td>
                    <td class="nowrap">{{ optional($user->created_at)->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada user.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
