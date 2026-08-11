<?php

namespace App\Http\Controllers;

use App\Models\WorkSchedule;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class WorkScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = WorkSchedule::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $schedules = $query->orderBy('day_of_week')->orderBy('name')->paginate(10);

        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        return view('admin.schedules.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'day_of_week' => 'required|integer|between:1,7',
            'check_in_start' => 'required|date_format:H:i',
            'check_in_time' => 'required|date_format:H:i',
            'check_in_end' => 'required|date_format:H:i',
            'late_tolerance_minutes' => 'required|integer|min:0',
            'check_out_start' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i',
            'check_out_end' => 'required|date_format:H:i',
            'is_workday' => 'boolean',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['is_workday'] = $request->has('is_workday');
        $validated['is_default'] = $request->has('is_default');
        $validated['is_active'] = $request->has('is_active');

        $schedule = WorkSchedule::create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE',
            'module' => 'WORK_SCHEDULES',
            'description' => "Menambahkan jadwal kerja baru: {$schedule->name}",
            'subject_type' => WorkSchedule::class,
            'subject_id' => $schedule->id,
            'new_values' => $schedule->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal kerja berhasil ditambahkan.');
    }

    public function edit(WorkSchedule $schedule)
    {
        return view('admin.schedules.edit', compact('schedule'));
    }

    public function update(Request $request, WorkSchedule $schedule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'day_of_week' => 'required|integer|between:1,7',
            'check_in_start' => 'required|date_format:H:i',
            'check_in_time' => 'required|date_format:H:i',
            'check_in_end' => 'required|date_format:H:i',
            'late_tolerance_minutes' => 'required|integer|min:0',
            'check_out_start' => 'required|date_format:H:i',
            'check_out_time' => 'required|date_format:H:i',
            'check_out_end' => 'required|date_format:H:i',
            'is_workday' => 'boolean',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $oldValues = $schedule->toArray();
        $validated['is_workday'] = $request->has('is_workday');
        $validated['is_default'] = $request->has('is_default');
        $validated['is_active'] = $request->has('is_active');

        $schedule->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE',
            'module' => 'WORK_SCHEDULES',
            'description' => "Mengubah jadwal kerja: {$schedule->name}",
            'subject_type' => WorkSchedule::class,
            'subject_id' => $schedule->id,
            'old_values' => $oldValues,
            'new_values' => $schedule->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal kerja berhasil diperbarui.');
    }

    public function destroy(WorkSchedule $schedule, Request $request)
    {
        $oldValues = $schedule->toArray();
        $schedule->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE',
            'module' => 'WORK_SCHEDULES',
            'description' => "Menghapus jadwal kerja: {$oldValues['name']}",
            'subject_type' => WorkSchedule::class,
            'subject_id' => $schedule->id,
            'old_values' => $oldValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal kerja berhasil dihapus.');
    }
}
