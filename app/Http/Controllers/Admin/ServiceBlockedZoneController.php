<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingLayoutConfig;
use App\Models\ServiceBlockedZone;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceBlockedZoneController extends Controller
{
    public function index()
    {
        $polygons = ServiceBlockedZone::polygons()->orderBy('name')->get();
        $postcodes = ServiceBlockedZone::postcodes()->orderBy('postcode')->get();
        $suburbs = ServiceBlockedZone::suburbs()->orderBy('suburb')->get();
        $layoutConfig = LandingLayoutConfig::first();

        return view('admin.blocked-zones.index', compact('polygons', 'postcodes', 'suburbs', 'layoutConfig'));
    }

    public function create(Request $request)
    {
        $type = $request->query('type', 'polygon');
        if (!in_array($type, ['polygon', 'postcode', 'suburb'], true)) {
            $type = 'polygon';
        }
        $layoutConfig = LandingLayoutConfig::first();

        return view('admin.blocked-zones.create', compact('type', 'layoutConfig'));
    }

    public function store(Request $request)
    {
        $data = $this->validateZone($request);

        ServiceBlockedZone::create($data);

        return redirect()->route('admin.blocked-zones.index')
            ->with('success', 'Blocked zone created successfully.');
    }

    public function edit(ServiceBlockedZone $blocked_zone)
    {
        $layoutConfig = LandingLayoutConfig::first();
        return view('admin.blocked-zones.edit', ['zone' => $blocked_zone, 'layoutConfig' => $layoutConfig]);
    }

    public function update(Request $request, ServiceBlockedZone $blocked_zone)
    {
        $data = $this->validateZone($request, $blocked_zone);

        $blocked_zone->update($data);

        return redirect()->route('admin.blocked-zones.index')
            ->with('success', 'Blocked zone updated successfully.');
    }

    public function destroy(ServiceBlockedZone $blocked_zone)
    {
        $blocked_zone->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Blocked zone deleted successfully.',
            ]);
        }

        return redirect()->route('admin.blocked-zones.index')
            ->with('success', 'Blocked zone deleted successfully.');
    }

    public function toggleStatus(ServiceBlockedZone $blocked_zone)
    {
        $blocked_zone->is_active = !$blocked_zone->is_active;
        $blocked_zone->save();

        return response()->json([
            'success' => true,
            'is_active' => $blocked_zone->is_active,
            'message' => 'Blocked zone status updated.',
        ]);
    }

    /**
     * Valida los campos según el tipo de zona.
     */
    private function validateZone(Request $request, ?ServiceBlockedZone $existing = null): array
    {
        $type = $request->input('type');

        $rules = [
            'type' => ['required', Rule::in(['polygon', 'postcode', 'suburb'])],
            'name' => ['required', 'string', 'max:150'],
            'state' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ];

        if ($type === 'polygon') {
            $rules['polygon_coordinates'] = ['required', 'string'];
        } elseif ($type === 'postcode') {
            $rules['postcode'] = ['required', 'string', 'max:20'];
        } elseif ($type === 'suburb') {
            $rules['suburb'] = ['required', 'string', 'max:150'];
        }

        $validated = $request->validate($rules);

        // Decodificar polygon_coordinates JSON
        if ($type === 'polygon') {
            $coords = json_decode($validated['polygon_coordinates'], true);
            if (!is_array($coords) || count($coords) < 3) {
                abort(422, 'A polygon must have at least 3 coordinates.');
            }
            $validated['polygon_coordinates'] = $coords;
            $validated['postcode'] = null;
            $validated['suburb'] = null;
        } elseif ($type === 'postcode') {
            $validated['polygon_coordinates'] = null;
            $validated['suburb'] = null;
        } elseif ($type === 'suburb') {
            $validated['polygon_coordinates'] = null;
            $validated['postcode'] = null;
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        return $validated;
    }
}
