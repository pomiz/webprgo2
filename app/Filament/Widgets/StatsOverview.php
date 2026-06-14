<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalRevenue = Order::whereNotIn('status', [Order::STATUS_CANCELLED])->sum('total_price');
        $totalOrders = Order::count();
        $pendingOrders = Order::where('status', Order::STATUS_PENDING_PAYMENT)->count();
        $totalProducts = Product::count();
        $totalUsers = User::where('role', 'user')->count();
        $completedOrders = Order::where('status', Order::STATUS_COMPLETED)->count();

        return [
            Stat::make('Total Pendapatan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Dari semua pesanan (excl. batal)')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Total Pesanan', $totalOrders)
                ->description($pendingOrders . ' menunggu pembayaran')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('primary'),

            Stat::make('Pesanan Selesai', $completedOrders)
                ->description(($totalOrders > 0 ? round(($completedOrders / $totalOrders) * 100) : 0) . '% completion rate')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Total Produk', $totalProducts)
                ->description(Product::where('stock', '>', 0)->count() . ' tersedia')
                ->descriptionIcon('heroicon-m-cube')
                ->color('info'),

            Stat::make('Total Pelanggan', $totalUsers)
                ->description('User terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
        ];
    }
}
