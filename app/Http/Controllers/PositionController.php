<?php

namespace App\Http\Controllers;

use App\Models\Position;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $query = Position::withCount('employees');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $positions = $query->orderBy('sort_order')->orderBy('name')->paginate(10);

        return view('admin.positions.index', compact('positions'));
    }

    public function create()
    {
        return view('admin.positions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:positions,code',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $position = Position::create($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'CREATE',
            'module' => 'POSITIONS',
            'description' => "Menambahkan jabatan baru: {$position->name}",
            'subject_type' => Position::class,
            'subject_id' => $position->id,
            'new_values' => $position->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.positions.index')->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function edit(Position $position)
    {
        return view('admin.positions.edit', compact('position'));
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:positions,code,' . $position->id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'sort_order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $oldValues = $position->toArray();
        $validated['is_active'] = $request->has('is_active');

        $position->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE',
            'module' => 'POSITIONS',
            'description' => "Mengubah jabatan: {$position->name}",
            'subject_type' => Position::class,
            'subject_id' => $position->id,
            'old_values' => $oldValues,
            'new_values' => $position->fresh()->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.positions.index')->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position, Request $request)
    {
        if ($position->employees()->count() > 0) {
            return redirect()->back()->with('error', 'Jabatan tidak dapat dihapus karena masih digunakan oleh pegawai.');
        }

        $oldValues = $position->toArray();
        $position->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'DELETE',
            'module' => 'POSITIONS',
            'description' => "Menghapus jabatan: {$oldValues['name']}",
            'subject_type' => Position::class,
            'subject_id' => $position->id,
            'old_values' => $oldValues,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('admin.positions.index')->with('success', 'Jabatan berhasil dihapus.');
    }
}
