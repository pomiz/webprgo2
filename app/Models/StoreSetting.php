<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    protected $fillable = [
        'store_name',
        'store_city',
        'store_province',
        'store_latitude',
        'store_longitude',
        'shipping_rate_per_km',
        'min_shipping_cost',
        'max_shipping_cost',
    ];

    protected $casts = [
        'store_latitude' => 'decimal:7',
        'store_longitude' => 'decimal:7',
        'shipping_rate_per_km' => 'decimal:2',
        'min_shipping_cost' => 'decimal:2',
        'max_shipping_cost' => 'decimal:2',
    ];

    public static function get(): self
    {
        return self::firstOrCreate([], [
            'store_name' => 'Ruang Baju',
            'shipping_rate_per_km' => 2000,
            'min_shipping_cost' => 10000,
            'max_shipping_cost' => 100000,
        ]);
    }
}
