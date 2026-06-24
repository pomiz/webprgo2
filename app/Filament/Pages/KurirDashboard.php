<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class KurirDashboard extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Dashboard Kurir';
    protected static ?string $title = 'Dashboard Kurir';
    protected static ?string $slug = 'kurir';
    protected static ?int $navigationSort = 10;

    protected static string $view = 'filament.pages.kurir-dashboard';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::query()->where('status', Order::STATUS_SHIPPED)->whereNotNull('tracking_status'))
            ->defaultSort('shipped_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('Order #')
                    ->formatStateUsing(fn ($state) => '#' . str_pad($state, 5, '0', STR_PAD_LEFT))
                    ->sortable(),

                TextColumn::make('user.username')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('courier')
                    ->label('Kurir')
                    ->sortable(),

                TextColumn::make('tracking_number')
                    ->label('No Resi')
                    ->searchable(),

                TextColumn::make('tracking_status')
                    ->label('Tracking')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => Order::TRACKING_STATUSES[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        Order::TRACKING_PICKED_UP => 'warning',
                        Order::TRACKING_IN_TRANSIT => 'info',
                        Order::TRACKING_ARRIVED => 'primary',
                        Order::TRACKING_DELIVERED => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('shipped_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->actions([
                Action::make('advance_tracking')
                    ->label(fn (Order $record) => match ($record->tracking_status) {
                        Order::TRACKING_PICKED_UP => 'Dalam Perjalanan',
                        Order::TRACKING_IN_TRANSIT => 'Tiba di Tujuan',
                        Order::TRACKING_ARRIVED => 'Diterima',
                        default => 'Lanjutkan',
                    })
                    ->icon(fn (Order $record) => match ($record->tracking_status) {
                        Order::TRACKING_PICKED_UP => 'heroicon-o-arrow-right',
                        Order::TRACKING_IN_TRANSIT => 'heroicon-o-map-pin',
                        Order::TRACKING_ARRIVED => 'heroicon-o-check-circle',
                        default => 'heroicon-o-arrow-right',
                    })
                    ->color(fn (Order $record) => match ($record->tracking_status) {
                        Order::TRACKING_ARRIVED => 'success',
                        default => 'primary',
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn (Order $record) => match ($record->tracking_status) {
                        Order::TRACKING_ARRIVED => 'Konfirmasi pesanan diterima?',
                        default => 'Lanjutkan tracking ke "' . (Order::TRACKING_STATUSES[$record->nextTrackingStep] ?? '') . '"?',
                    })
                    ->modalDescription(fn (Order $record) => match ($record->tracking_status) {
                        Order::TRACKING_ARRIVED => 'Ini akan menyelesaikan pesanan secara otomatis.',
                        default => 'Status pengiriman akan diperbarui.',
                    })
                    ->visible(fn (Order $record) => $record->canAdvanceTracking())
                    ->action(function (Order $record) {
                        $record->advanceTracking();

                        $message = $record->status === Order::STATUS_COMPLETED
                            ? 'Pesanan diterima dan telah selesai!'
                            : 'Tracking diperbarui ke "' . $record->tracking_label . '"';

                        Notification::make()
                            ->title($message)
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
