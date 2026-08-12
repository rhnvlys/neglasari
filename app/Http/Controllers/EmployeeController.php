<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\OfficeLocation;
use App\Models\Position;
use App\Models\Setting;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = Employee::with('position')->where('is_active', true);
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', '%' . $request->search . '%')
                  ->orWhere('employee_number', 'like', '%' . $request->search . '%')
                  ->orWhere('nik', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('position_id')) {
            $query->where('position_id', $request->position_id);
        }
        
        $employees = $query->orderBy('full_name')->paginate(15);
        $positions = Position::where('is_active', true)->orderBy('name')->get();
        
        return view('admin.employees.index', compact('employees', 'positions'));
    }

    public function create()
    {
        $positions = Position::where('is_active', true)->orderBy('name')->get();
        return view('admin.employees.create', compact('positions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_number' => 'required|string|unique:employees,employee_number',
            'nik' => 'nullable|string|unique:employees,nik',
            'full_name' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'position_id' => 'required|exists:positions,id',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:employees,email',
            'address' => 'nullable|string',
            'joined_at' => 'nullable|date',
            'employment_status' => 'required|string|in:permanent,contract,internship',
            'is_active' => 'boolean',
            'username' => 'nullable|string|max:50|unique:users,username',
            'password' => 'nullable|string|min:6',
            'role' => 'nullable|string|in:Admin,Anggota',
        ], [
            'employee_number.unique' => 'Nomor pegawai sudah digunakan.',
            'nik.unique' => 'NIK sudah digunakan.',
            'email.unique' => 'Email sudah digunakan.',
            'username.unique' => 'Username akun sudah digunakan.',
        ]);
        
        DB::beginTransaction();
        
        try {
            $employee = Employee::create($request->only([
                'employee_number', 'nik', 'full_name', 'gender', 'birth_place',
                'birth_date', 'position_id', 'phone', 'email', 'address', 'joined_at',
                'employment_status', 'is_active'
            ]));

            if ($request->filled('username')) {
                $role = $request->input('role', 'Anggota');
                $user = \App\Models\User::create([
                    'employee_id' => $employee->id,
                    'name' => $employee->full_name,
                    'username' => $request->username,
                    'email' => $request->email ?? ($request->username . '@neglasari.desa.id'),
                    'password' => \Illuminate\Support\Facades\Hash::make($request->password ?? 'password123'),
                    'is_active' => $request->boolean('is_active', true),
                ]);

                $rolesToAssign = [$role];
                if ($role === 'Admin') {
                    $rolesToAssign[] = 'Super Admin';
                    $rolesToAssign[] = 'Admin Desa';
                } else {
                    $rolesToAssign[] = 'Pegawai';
                }
                $user->syncRoles($rolesToAssign);
            }
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'create',
                'module' => 'employee',
                'description' => 'Menambahkan anggota baru: ' . $employee->full_name,
                'subject_type' => Employee::class,
                'subject_id' => $employee->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.employees.index')->with('success', 'Data perangkat / anggota berhasil ditambahkan.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data pegawai: ' . $e->getMessage());
        }
    }

    public function show(Employee $employee)
    {
        $employee->load(['position', 'attendances', 'leaveRequests', 'user']);
        return view('admin.employees.show', compact('employee'));
    }

    public function edit(Employee $employee)
    {
        $employee->load('user');
        $positions = Position::where('is_active', true)->orderBy('name')->get();
        return view('admin.employees.edit', compact('employee', 'positions'));
    }

    public function update(Request $request, Employee $employee)
    {
        $user = $employee->user;
        $request->validate([
            'employee_number' => ['required', 'string', Rule::unique('employees', 'employee_number')->ignore($employee->id)],
            'nik' => ['nullable', 'string', Rule::unique('employees', 'nik')->ignore($employee->id)],
            'full_name' => 'required|string|max:255',
            'gender' => 'required|string|in:male,female',
            'birth_place' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'position_id' => 'required|exists:positions,id',
            'phone' => 'nullable|string|max:20',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('employees', 'email')->ignore($employee->id)],
            'address' => 'nullable|string',
            'joined_at' => 'nullable|date',
            'employment_status' => 'required|string|in:permanent,contract,internship',
            'is_active' => 'boolean',
            'username' => ['nullable', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user?->id)],
            'password' => 'nullable|string|min:6',
            'role' => 'nullable|string|in:Admin,Anggota',
        ], [
            'employee_number.unique' => 'Nomor pegawai sudah digunakan.',
            'nik.unique' => 'NIK sudah digunakan.',
            'email.unique' => 'Email sudah digunakan.',
            'username.unique' => 'Username akun sudah digunakan.',
        ]);
        
        DB::beginTransaction();
        
        try {
            $oldValues = $employee->getOriginal();
            $employee->update($request->only([
                'employee_number', 'nik', 'full_name', 'gender', 'birth_place',
                'birth_date', 'position_id', 'phone', 'email', 'address', 'joined_at',
                'employment_status', 'is_active'
            ]));

            if ($request->filled('username')) {
                $role = $request->input('role', 'Anggota');
                $userData = [
                    'name' => $employee->full_name,
                    'username' => $request->username,
                    'email' => $request->email ?? ($request->username . '@neglasari.desa.id'),
                    'is_active' => $request->boolean('is_active', true),
                ];

                if ($request->filled('password')) {
                    $userData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
                }

                if ($user) {
                    $user->update($userData);
                } else {
                    $userData['employee_id'] = $employee->id;
                    $userData['password'] = \Illuminate\Support\Facades\Hash::make($request->password ?? 'password123');
                    $user = \App\Models\User::create($userData);
                }

                $rolesToAssign = [$role];
                if ($role === 'Admin') {
                    $rolesToAssign[] = 'Super Admin';
                    $rolesToAssign[] = 'Admin Desa';
                } else {
                    $rolesToAssign[] = 'Pegawai';
                }
                $user->syncRoles($rolesToAssign);
            }
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'update',
                'module' => 'employee',
                'description' => 'Mengubah data anggota: ' . $employee->full_name,
                'subject_type' => Employee::class,
                'subject_id' => $employee->id,
                'old_values' => $oldValues,
                'new_values' => $employee->getChanges(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.employees.index')->with('success', 'Data anggota berhasil diperbarui.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data anggota: ' . $e->getMessage());
        }
    }

    public function destroy(Employee $employee)
    {
        DB::beginTransaction();
        
        try {
            $employeeName = $employee->full_name;
            $employee->delete();
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'delete',
                'module' => 'employee',
                'description' => 'Menghapus pegawai: ' . $employeeName,
                'subject_type' => Employee::class,
                'subject_id' => $employee->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            
            DB::commit();
            
            return redirect()->route('admin.employees.index')->with('success', 'Pegawai berhasil dihapus.');
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menghapus pegawai.');
        }
    }
}
