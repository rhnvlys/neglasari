@extends('layouts.admin')

@section('page-title', 'Edit Pegawai')

@section('admin-content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-neglasari-dark">Edit Pegawai</h2>
            <a href="{{ route('admin.employees.index') }}" class="px-4 py-2 bg-gray-200 text-neglasari-text font-semibold rounded-xl shadow-md hover:bg-gray-300 transition duration-150 text-sm">
                Kembali
            </a>
        </div>
        
        <form method="POST" action="{{ route('admin.employees.update', $employee) }}" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="employee_number" class="block text-sm font-semibold text-neglasari-text mb-1">Nomor Pegawai (NIP / NIPD) *</label>
                    <input type="text" name="employee_number" id="employee_number" value="{{ old('employee_number', $employee->employee_number) }}" required class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                    @error('employee_number')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="nik" class="block text-sm font-semibold text-neglasari-text mb-1">NIK</label>
                    <input type="text" name="nik" id="nik" value="{{ old('nik', $employee->nik) }}" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                    @error('nik')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="full_name" class="block text-sm font-semibold text-neglasari-text mb-1">Nama Lengkap *</label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name', $employee->full_name) }}" required class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                    @error('full_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="gender" class="block text-sm font-semibold text-neglasari-text mb-1">Jenis Kelamin *</label>
                    <select name="gender" id="gender" required class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="male" {{ old('gender', $employee->gender?->value) === 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender', $employee->gender?->value) === 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="birth_place" class="block text-sm font-semibold text-neglasari-text mb-1">Tempat Lahir</label>
                    <input type="text" name="birth_place" id="birth_place" value="{{ old('birth_place', $employee->birth_place) }}" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                </div>
                
                <div>
                    <label for="birth_date" class="block text-sm font-semibold text-neglasari-text mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date', optional($employee->birth_date)->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                </div>
                
                <div>
                    <label for="position_id" class="block text-sm font-semibold text-neglasari-text mb-1">Jabatan *</label>
                    <select name="position_id" id="position_id" required class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                        <option value="">Pilih Jabatan</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ old('position_id', $employee->position_id) == $position->id ? 'selected' : '' }}>
                                {{ $position->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('position_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="employment_status" class="block text-sm font-semibold text-neglasari-text mb-1">Status Kepegawaian *</label>
                    <select name="employment_status" id="employment_status" required class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                        <option value="">Pilih Status</option>
                        <option value="permanent" {{ old('employment_status', $employee->employment_status?->value) === 'permanent' ? 'selected' : '' }}>Tetap</option>
                        <option value="contract" {{ old('employment_status', $employee->employment_status?->value) === 'contract' ? 'selected' : '' }}>Kontrak</option>
                        <option value="internship" {{ old('employment_status', $employee->employment_status?->value) === 'internship' ? 'selected' : '' }}>Magang</option>
                    </select>
                    @error('employment_status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-semibold text-neglasari-text mb-1">Nomor Telepon</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone', $employee->phone) }}" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-semibold text-neglasari-text mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $employee->email) }}" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="joined_at" class="block text-sm font-semibold text-neglasari-text mb-1">Tanggal Bergabung</label>
                    <input type="date" name="joined_at" id="joined_at" value="{{ old('joined_at', optional($employee->joined_at)->format('Y-m-d')) }}" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                </div>
                
                <div class="flex items-center space-x-2 pt-6">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $employee->is_active) ? 'checked' : '' }} class="h-4 w-4 text-neglasari-accent focus:ring-neglasari-accent border-neglasari-border rounded">
                    <label for="is_active" class="text-sm font-semibold text-neglasari-text">Status Aktif</label>
                </div>
            </div>
            
            <div class="col-span-2">
                <label for="address" class="block text-sm font-semibold text-neglasari-text mb-1">Alamat</label>
                <textarea name="address" id="address" rows="3" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">{{ old('address', $employee->address) }}</textarea>
            </div>

            <!-- Informasi Akun Login -->
            <div class="border-t border-neglasari-border pt-6 mt-6">
                <h3 class="text-lg font-bold text-neglasari-dark mb-4">Informasi Akun Akses Sistem (Opsi)</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="username" class="block text-sm font-semibold text-neglasari-text mb-1">Username Login</label>
                        <input type="text" name="username" id="username" value="{{ old('username', $employee->user?->username) }}" placeholder="Username" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                        @error('username')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-neglasari-text mb-1">Password Baru (Kosongkan jika tak diubah)</label>
                        <input type="password" name="password" id="password" placeholder="Isi untuk ubah password" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                    </div>

                    <div>
                        <label for="role" class="block text-sm font-semibold text-neglasari-text mb-1">Peran Akses (Role)</label>
                        @php
                            $currentRole = $employee->user?->hasRole('Admin') ? 'Admin' : 'Anggota';
                        @endphp
                        <select name="role" id="role" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                            <option value="Anggota" {{ old('role', $currentRole) === 'Anggota' ? 'selected' : '' }}>Anggota (Perangkat & Staf)</option>
                            <option value="Admin" {{ old('role', $currentRole) === 'Admin' ? 'selected' : '' }}>Admin (Staff IT Desa)</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.employees.index') }}" class="px-6 py-2 bg-gray-200 text-neglasari-text font-semibold rounded-xl shadow-md hover:bg-gray-300 transition duration-150">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-neglasari-main text-white font-semibold rounded-xl shadow-md hover:bg-neglasari-accent transition duration-150">
                    Perbarui
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
