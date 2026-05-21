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
}
