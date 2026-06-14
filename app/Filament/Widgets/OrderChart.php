<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrderChart extends ChartWidget
{
    protected static ?string $heading = 'Pesanan 7 Hari Terakhir';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $days = collect(range(6, 0))->map(function ($daysAgo) {
            return Carbon::now()->subDays($daysAgo)->format('Y-m-d');
        });

        $orders = Order::whereDate('created_at', '>=', Carbon::now()->subDays(6))
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_price) as revenue')
            ->groupBy('date')
            ->pluck('count', 'date');

        $revenue = Order::whereDate('created_at', '>=', Carbon::now()->subDays(6))
            ->whereNotIn('status', [Order::STATUS_CANCELLED])
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as revenue')
            ->groupBy('date')
            ->pluck('revenue', 'date');

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Pesanan',
                    'data' => $days->map(fn ($date) => $orders->get($date, 0))->toArray(),
                    'borderColor' => '#c06a22',
                    'backgroundColor' => 'rgba(192, 106, 34, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Pendapatan (Rp ribu)',
                    'data' => $days->map(fn ($date) => round(($revenue->get($date, 0)) / 1000))->toArray(),
                    'borderColor' => '#34d399',
                    'backgroundColor' => 'rgba(52, 211, 153, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $days->map(fn ($date) => Carbon::parse($date)->format('d M'))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
