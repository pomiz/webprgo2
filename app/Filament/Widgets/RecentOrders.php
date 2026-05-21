<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrders extends BaseWidget
{
    protected static ?string $heading = 'Pesanan Terbaru';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()->with('user')->latest()->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order #')
                    ->formatStateUsing(fn ($state) => '#' . str_pad($state, 5, '0', STR_PAD_LEFT)),

                Tables\Columns\TextColumn::make('user.username')
                    ->label('Pelanggan'),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR'),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Order::STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Order::STATUS_PENDING_PAYMENT => 'warning',
                        Order::STATUS_CONFIRMED => 'info',
                        Order::STATUS_PROCESSING => 'primary',
                        Order::STATUS_SHIPPED => 'purple',
                        Order::STATUS_COMPLETED => 'success',
                        Order::STATUS_CANCELLED => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
