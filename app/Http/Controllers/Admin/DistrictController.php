<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\District;

class DistrictController extends Controller
{
    public function index()
    {
        $districts = District::orderBy('order')->orderBy('name')->get();
        return view('admin.districts.index', compact('districts'));
    }

    public function create()
    {
        return view('admin.districts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'state' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'order' => 'integer|min:0'
        ]);

        District::create($request->all());

        return redirect()->route('admin.districts.index')
            ->with('success', 'District created successfully.');
    }

    public function edit(District $district)
    {
        return view('admin.districts.edit', compact('district'));
    }

    public function update(Request $request, District $district)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'state' => 'nullable|string|max:100',
            'postcode' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'order' => 'integer|min:0'
        ]);

        $district->update($request->all());

        return redirect()->route('admin.districts.index')
            ->with('success', 'District updated successfully.');
    }

    public function destroy(District $district)
    {
        $district->delete();

        // Return JSON response for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'District deleted successfully.'
            ]);
        }

        return redirect()->route('admin.districts.index')
            ->with('success', 'District deleted successfully.');
    }

    public function toggleStatus(District $district)
    {
        $district->is_active = !$district->is_active;
        $district->save();

        return response()->json([
            'success' => true,
            'is_active' => $district->is_active,
            'message' => 'District status updated successfully.'
        ]);
    }
}
