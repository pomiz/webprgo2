<?php

namespace App\Services;

use App\Models\StoreSetting;

class ShippingService
{
    /**
     * Calculate distance between two coordinates using Haversine formula.
     *
     * @param float $lat1 Latitude of point 1
     * @param float $lon1 Longitude of point 1
     * @param float $lat2 Latitude of point 2
     * @param float $lon2 Longitude of point 2
     * @return float Distance in kilometers
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Calculate shipping cost based on user coordinates.
     *
     * @param float $userLat User latitude
     * @param float $userLon User longitude
     * @return array{distance_km: float, cost: float, error: string|null}
     */
    public function calculateShippingCost(float $userLat, float $userLon): array
    {
        $settings = StoreSetting::get();

        if (!$settings->store_latitude || !$settings->store_longitude) {
            return [
                'distance_km' => 0,
                'cost' => 0,
                'error' => 'Lokasi toko belum diatur.',
            ];
        }

        $distance = $this->calculateDistance(
            (float) $settings->store_latitude,
            (float) $settings->store_longitude,
            $userLat,
            $userLon
        );

        $cost = $distance * (float) $settings->shipping_rate_per_km;

        // Apply min/max bounds
        $cost = max((float) $settings->min_shipping_cost, $cost);
        $cost = min((float) $settings->max_shipping_cost, $cost);

        return [
            'distance_km' => round($distance, 2),
            'cost' => round($cost, 0),
            'error' => null,
        ];
    }

    /**
     * Get available courier options with pricing based on distance.
     * Each courier has a base multiplier and delivery speed.
     *
     * @param float $baseCost Base shipping cost from Haversine
     * @return array<int, array{code: string, name: string, cost: float, estimate: string}>
     */
    public function getCourierOptions(float $baseCost): array
    {
        return [
            [
                'code' => 'jne',
                'name' => 'JNE Reguler',
                'multiplier' => 1.0,
                'estimate' => '3-5 hari',
                'cost' => max(10000, round($baseCost * 1.0)),
            ],
            [
                'code' => 'jnt',
                'name' => 'J&T Express',
                'multiplier' => 1.3,
                'estimate' => '1-2 hari',
                'cost' => max(10000, round($baseCost * 1.3)),
            ],
            [
                'code' => 'sicepat',
                'name' => 'SiCepat',
                'multiplier' => 1.15,
                'estimate' => '2-3 hari',
                'cost' => max(10000, round($baseCost * 1.15)),
            ],
            [
                'code' => 'anteraja',
                'name' => 'AnterAja',
                'multiplier' => 1.1,
                'estimate' => '2-4 hari',
                'cost' => max(10000, round($baseCost * 1.1)),
            ],
            [
                'code' => 'lion',
                'name' => 'Lion Parcel',
                'multiplier' => 0.9,
                'estimate' => '3-6 hari',
                'cost' => max(10000, round($baseCost * 0.9)),
            ],
        ];
    }
}
