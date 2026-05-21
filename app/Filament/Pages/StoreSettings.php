<?php

namespace App\Filament\Pages;

use App\Models\Location;
use App\Models\StoreSetting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class StoreSettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Pengaturan Toko';
    protected static ?string $title = 'Pengaturan Toko';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.pages.store-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $settings = StoreSetting::get();

        $this->form->fill([
            'store_name' => $settings->store_name,
            'store_province' => $settings->store_province,
            'store_city' => $settings->store_city,
            'store_latitude' => $settings->store_latitude,
            'store_longitude' => $settings->store_longitude,
            'shipping_rate_per_km' => $settings->shipping_rate_per_km,
            'min_shipping_cost' => $settings->min_shipping_cost,
            'max_shipping_cost' => $settings->max_shipping_cost,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Toko')
                    ->schema([
                        TextInput::make('store_name')
                            ->label('Nama Toko')
                            ->required()
                            ->maxLength(255),
                    ]),

                Section::make('Lokasi Toko')
                    ->description('Pilih lokasi toko untuk perhitungan ongkir')
                    ->schema([
                        Select::make('store_province')
                            ->label('Provinsi')
                            ->options(fn () => Location::select('province')->distinct()->orderBy('province')->pluck('province', 'province'))
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(fn (Set $set) => $set('store_city', null)),

                        Select::make('store_city')
                            ->label('Kota')
                            ->options(function (Get $get) {
                                $province = $get('store_province');
                                if (!$province) return [];
                                return Location::where('province', $province)
                                    ->orderBy('city')
                                    ->pluck('city', 'city');
                            })
                            ->searchable()
                            ->live()
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $city = $get('store_city');
                                $province = $get('store_province');
                                if ($city && $province) {
                                    $location = Location::where('province', $province)
                                        ->where('city', $city)
                                        ->first();
                                    if ($location) {
                                        $set('store_latitude', $location->latitude);
                                        $set('store_longitude', $location->longitude);
                                    }
                                }
                            }),

                        TextInput::make('store_latitude')
                            ->label('Latitude')
                            ->numeric()
                            ->step(0.0000001),

                        TextInput::make('store_longitude')
                            ->label('Longitude')
                            ->numeric()
                            ->step(0.0000001),
                    ])->columns(2),

                Section::make('Tarif Ongkir')
                    ->description('Konfigurasi biaya pengiriman')
                    ->schema([
                        TextInput::make('shipping_rate_per_km')
                            ->label('Tarif per KM')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        TextInput::make('min_shipping_cost')
                            ->label('Ongkir Minimum')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),

                        TextInput::make('max_shipping_cost')
                            ->label('Ongkir Maksimum')
                            ->numeric()
                            ->prefix('Rp')
                            ->required(),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $settings = StoreSetting::get();
        $settings->update($data);

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->success()
            ->send();
    }
}
