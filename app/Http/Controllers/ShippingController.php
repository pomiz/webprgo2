<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\UserAddress;
use App\Services\ShippingService;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    public function getProvinces()
    {
        $provinces = Location::select('province')
            ->distinct()
            ->orderBy('province')
            ->pluck('province');

        return response()->json($provinces);
    }

    public function getCities(Request $request)
    {
        $request->validate(['province' => 'required|string']);

        $cities = Location::where('province', $request->province)
            ->select('city', 'latitude', 'longitude')
            ->distinct()
            ->orderBy('city')
            ->get();

        return response()->json($cities);
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $service = new ShippingService();
        $result = $service->calculateShippingCost(
            (float) $request->latitude,
            (float) $request->longitude
        );

        return response()->json($result);
    }

    public function saveAddress(Request $request)
    {
        $request->validate([
            'province' => 'nullable|string',
            'city' => 'nullable|string',
            'full_address' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        // Set all existing addresses to non-default
        UserAddress::where('user_id', auth()->id())
            ->update(['is_default' => false]);

        $address = UserAddress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'province' => $request->province,
                'city' => $request->city,
            ],
            [
                'full_address' => $request->full_address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'is_default' => true,
            ]
        );

        return response()->json(['success' => true, 'address' => $address]);
    }

    /**
     * Get courier options with pricing based on base shipping cost.
     */
    public function getCouriers(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $service = new ShippingService();
        $shipping = $service->calculateShippingCost(
            (float) $request->latitude,
            (float) $request->longitude
        );

        if ($shipping['error']) {
            return response()->json(['error' => $shipping['error']]);
        }

        $couriers = $service->getCourierOptions($shipping['cost']);

        return response()->json([
            'distance_km' => $shipping['distance_km'],
            'couriers' => $couriers,
        ]);
    }
}
