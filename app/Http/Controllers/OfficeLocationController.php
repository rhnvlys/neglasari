<?php

namespace App\Http\Controllers;

use App\Models\OfficeLocation;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class OfficeLocationController extends Controller
{
    public function index(Request $request)
    {
        $query = OfficeLocation::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $locations = $query->orderBy('name')->paginate(10);

        return view('admin.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:1',
            'maximum_accuracy_meters' => 'required|integer|min:1',
            'requires_photo' => 'boolean',
            'allow_outside_radius' => 'boolean',
            'requires_outside_verification' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['latitude'] = $request->filled('latitude') ? $request->latitude : -7.164300;
        $validated['longitude'] = $request->filled('longitude') ? $request->longitude : 108.083200;
        $validated['requires_photo'] = $request->has('requires_photo');
        $validated['allow_outside_radius'] = $request->has('allow_outside_radius');
        $validated['requires_outside_verification'] = $request->has('requires_outside_verification');
        $validated['is_active'] = $request->has('is_active');

        $location = OfficeLocation::create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE',
            'module' => 'OFFICE_LOCATIONS',
            'description' => "Menambahkan lokasi kantor baru: {$location->name}",
            'subject_type' => OfficeLocation::class,
            'subject_id' => $location->id,
            'new_values' => $location->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.locations.index')->with('success', 'Lokasi kantor berhasil ditambahkan.');
    }

    public function edit(OfficeLocation $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(Request $request, OfficeLocation $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'address' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:1',
            'maximum_accuracy_meters' => 'required|integer|min:1',
            'requires_photo' => 'boolean',
            'allow_outside_radius' => 'boolean',
            'requires_outside_verification' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $oldValues = $location->toArray();
        $validated['latitude'] = $request->filled('latitude') ? $request->latitude : ($location->latitude ?? -7.164300);
        $validated['longitude'] = $request->filled('longitude') ? $request->longitude : ($location->longitude ?? 108.083200);
        $validated['requires_photo'] = $request->has('requires_photo');
        $validated['allow_outside_radius'] = $request->has('allow_outside_radius');
        $validated['requires_outside_verification'] = $request->has('requires_outside_verification');
        $validated['is_active'] = $request->has('is_active');

        $location->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE',
            'module' => 'OFFICE_LOCATIONS',
            'description' => "Mengubah lokasi kantor: {$location->name}",
            'subject_type' => OfficeLocation::class,
            'subject_id' => $location->id,
            'old_values' => $oldValues,
            'new_values' => $location->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.locations.index')->with('success', 'Lokasi kantor berhasil diperbarui.');
    }

    public function destroy(OfficeLocation $location, Request $request)
    {
        $oldValues = $location->toArray();
        $location->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE',
            'module' => 'OFFICE_LOCATIONS',
            'description' => "Menghapus lokasi kantor: {$oldValues['name']}",
            'subject_type' => OfficeLocation::class,
            'subject_id' => $location->id,
            'old_values' => $oldValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.locations.index')->with('success', 'Lokasi kantor berhasil dihapus.');
    }
}
