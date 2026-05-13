<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ServiceAvailabilityService;
use Illuminate\Http\Request;

class ServiceAvailabilityController extends Controller
{
    protected ServiceAvailabilityService $service;

    public function __construct(ServiceAvailabilityService $service)
    {
        $this->service = $service;
    }

    public function check(Request $request)
    {
        $data = $request->validate([
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'postcode' => 'nullable|string|max:20',
            'suburb' => 'nullable|string|max:150',
        ]);

        $result = $this->service->check(
            isset($data['latitude']) ? (float) $data['latitude'] : null,
            isset($data['longitude']) ? (float) $data['longitude'] : null,
            $data['postcode'] ?? null,
            $data['suburb'] ?? null
        );

        return response()->json([
            'allowed' => $result['allowed'],
            'message' => $result['allowed']
                ? 'Great! We service your area.'
                : ($result['reason'] ?? 'This area is not currently serviced.'),
        ]);
    }
}
