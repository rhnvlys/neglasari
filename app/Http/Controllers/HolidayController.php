<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Models\ActivityLog;
use App\Enums\HolidayType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $query = Holiday::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $holidays = $query->orderBy('start_date', 'desc')->paginate(10);

        return view('admin.holidays.index', compact('holidays'));
    }

    public function create()
    {
        $types = HolidayType::cases();
        return view('admin.holidays.create', compact('types'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => ['required', new Enum(HolidayType::class)],
            'description' => 'nullable|string|max:255',
            'applies_to_all' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['applies_to_all'] = $request->has('applies_to_all');
        $validated['is_active'] = $request->has('is_active');

        $holiday = Holiday::create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE',
            'module' => 'HOLIDAYS',
            'description' => "Menambahkan hari libur: {$holiday->name}",
            'subject_type' => Holiday::class,
            'subject_id' => $holiday->id,
            'new_values' => $holiday->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.holidays.index')->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function edit(Holiday $holiday)
    {
        $types = HolidayType::cases();
        return view('admin.holidays.edit', compact('holiday', 'types'));
    }

    public function update(Request $request, Holiday $holiday)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => ['required', new Enum(HolidayType::class)],
            'description' => 'nullable|string|max:255',
            'applies_to_all' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $oldValues = $holiday->toArray();
        $validated['applies_to_all'] = $request->has('applies_to_all');
        $validated['is_active'] = $request->has('is_active');

        $holiday->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE',
            'module' => 'HOLIDAYS',
            'description' => "Mengubah hari libur: {$holiday->name}",
            'subject_type' => Holiday::class,
            'subject_id' => $holiday->id,
            'old_values' => $oldValues,
            'new_values' => $holiday->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.holidays.index')->with('success', 'Hari libur berhasil diperbarui.');
    }

    public function destroy(Holiday $holiday, Request $request)
    {
        $oldValues = $holiday->toArray();
        $holiday->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE',
            'module' => 'HOLIDAYS',
            'description' => "Menghapus hari libur: {$oldValues['name']}",
            'subject_type' => Holiday::class,
            'subject_id' => $holiday->id,
            'old_values' => $oldValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.holidays.index')->with('success', 'Hari libur berhasil dihapus.');
    }
}
