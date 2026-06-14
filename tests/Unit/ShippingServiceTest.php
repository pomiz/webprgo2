<?php

use App\Services\ShippingService;

test('calculateDistance returns correct distance between two points', function () {
    $service = new ShippingService();

    // Jakarta to Bandung (~150km)
    $distance = $service->calculateDistance(-6.2088, 106.8456, -6.9175, 107.6191);

    expect($distance)->toBeGreaterThan(100);
    expect($distance)->toBeLessThan(200);
});

test('calculateDistance returns zero for same coordinates', function () {
    $service = new ShippingService();

    $distance = $service->calculateDistance(-6.2088, 106.8456, -6.2088, 106.8456);

    expect($distance)->toBe(0.0);
});

test('calculateDistance handles cross-hemisphere coordinates', function () {
    $service = new ShippingService();

    // Jakarta (south) to Medan (north) ~1400km
    $distance = $service->calculateDistance(-6.2088, 106.8456, 3.5952, 98.6722);

    expect($distance)->toBeGreaterThan(1200);
    expect($distance)->toBeLessThan(1600);
});
