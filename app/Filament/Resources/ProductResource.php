<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Produk';
    protected static ?string $navigationGroup = 'Manajemen Toko';
    protected static ?string $pluralModelLabel = 'Produk';
    protected static ?int $navigationSort = 1;
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Produk')
                    ->required()
                    ->maxLength(255),

         
                Forms\Components\Select::make('category')
                    ->label('Kategori')
                    ->options([
                        'Kaos' => 'Kaos',
                        'Kemeja' => 'Kemeja',
                        'Celana' => 'Celana',
                        'Jaket' => 'Jaket',
                        'Aksesoris' => 'Aksesoris',
                    ])
                    ->required()
                    ->searchable(),

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi Produk')
                    ->rows(4)
                    ->maxLength(65535),

                Forms\Components\TextInput::make('price')
                    ->label('Harga')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),

                Forms\Components\TextInput::make('stock')
                    ->label('Stok')
                    ->numeric()
                    ->default(0)
                    ->required(),

                FileUpload::make('image')
                    ->label('Gambar Produk')
                    ->image()
                    ->disk('public')
                    ->directory('/') // Ubah ke folder 'products'
                    
                    ->required(),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('image')
                    ->label('Gambar')
                    ->formatStateUsing(fn (?string $state) => $state
                        ? '<img src="' . e($state) . '" onerror="this.src=\'https://via.placeholder.com/40\'" style="width:40px;height:40px;object-fit:cover;border-radius:50%">'
                        : '<div style="width:40px;height:40px;background:#eee;border-radius:50%"></div>')
                    ->html()
                    ->searchable(false)
                    ->sortable(false),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    // ->money('IDR', true)
                    ->prefix('Rp ')
                   
                    ->sortable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-o-printer')
                    ->url(fn (): string => route('print.products'), shouldOpenInNewTab: true),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
    