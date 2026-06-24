<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Pesanan';
    protected static ?string $modelLabel = 'Pesanan';
    protected static ?string $pluralModelLabel = 'Pesanan';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pesanan')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'username')
                            ->label('Pelanggan')
                            ->disabled(),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options(Order::STATUSES)
                            ->required(),
                        Forms\Components\TextInput::make('virtual_account')
                            ->label('Virtual Account')
                            ->disabled(),
                    ])->columns(3),

                Forms\Components\Section::make('Detail Harga')
                    ->schema([
                        Forms\Components\TextInput::make('subtotal')
                            ->label('Subtotal')
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('shipping_cost')
                            ->label('Ongkir')
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('total_price')
                            ->label('Total')
                            ->prefix('Rp')
                            ->numeric()
                            ->disabled(),
                    ])->columns(3),

                Forms\Components\Section::make('Pengiriman')
                    ->schema([
                        Forms\Components\TextInput::make('shipping_address')
                            ->label('Alamat Pengiriman')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Order #')
                    ->formatStateUsing(fn ($state) => '#' . str_pad($state, 5, '0', STR_PAD_LEFT))
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.username')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),

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

                Tables\Columns\TextColumn::make('virtual_account')
                    ->label('QRIS')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options(Order::STATUSES),
            ])
            ->actions([
                // Advance to next status
                Action::make('advance')
                    ->label(fn (Order $record) => match ($record->next_status) {
                        Order::STATUS_CONFIRMED => 'Konfirmasi Bayar',
                        Order::STATUS_PROCESSING => 'Proses Pesanan',
                        Order::STATUS_SHIPPED => 'Kirim',
                        Order::STATUS_COMPLETED => 'Selesai',
                        default => 'Lanjutkan',
                    })
                    ->icon(fn (Order $record) => match ($record->next_status) {
                        Order::STATUS_CONFIRMED => 'heroicon-o-check-circle',
                        Order::STATUS_PROCESSING => 'heroicon-o-cog-6-tooth',
                        Order::STATUS_SHIPPED => 'heroicon-o-truck',
                        Order::STATUS_COMPLETED => 'heroicon-o-flag',
                        default => 'heroicon-o-arrow-right',
                    })
                    ->color(fn (Order $record) => match ($record->next_status) {
                        Order::STATUS_CONFIRMED => 'info',
                        Order::STATUS_PROCESSING => 'primary',
                        Order::STATUS_SHIPPED => 'purple',
                        Order::STATUS_COMPLETED => 'success',
                        default => 'gray',
                    })
                    ->requiresConfirmation()
                    ->modalHeading(fn (Order $record) => 'Ubah status ke "' . Order::STATUSES[$record->next_status] . '"?')
                    ->modalDescription(fn (Order $record) => match ($record->next_status) {
                        Order::STATUS_SHIPPED => 'Kurir: ' . ($record->courier ?? 'Belum dipilih') . '. No resi akan digenerate otomatis.',
                        default => 'Status pesanan akan diperbarui.',
                    })
                    ->visible(fn (Order $record) => $record->canAdvance())
                    ->action(function (Order $record) {
                        // If advancing to shipped, auto-generate tracking
                        if ($record->next_status === Order::STATUS_SHIPPED) {
                            $courier = $record->courier ?: 'Lion Parcel';
                            $record->tracking_number = strtoupper(substr($courier, 0, 3))
                                . '-' . date('Ymd') . '-' . strtoupper(Str::random(4));
                            $record->shipped_at = now();
                            $record->tracking_status = Order::TRACKING_PICKED_UP;
                            if (!$record->courier) {
                                $record->courier = $courier;
                            }
                        }

                        $record->advance();

                        Notification::make()
                            ->title('Status diperbarui ke "' . $record->status_label . '"')
                            ->success()
                            ->send();
                    }),

                // Cancel order
                Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan pesanan ini?')
                    ->modalDescription('Pesanan yang dibatalkan tidak bisa dikembalikan.')
                    ->visible(fn (Order $record) => $record->canCancel())
                    ->action(function (Order $record) {
                        $record->cancel();
                        Notification::make()
                            ->title('Pesanan dibatalkan')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Orders are created by users via checkout
    }
}
