@extends('layouts.admin')

@section('page-title', 'Tambah Pegawai Baru')

@section('admin-content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-neglasari-dark">Tambah Pegawai Baru</h2>
            <a href="{{ route('admin.employees.index') }}" class="px-4 py-2 bg-gray-200 text-neglasari-text font-semibold rounded-xl shadow-md hover:bg-gray-300 transition duration-150 text-sm">
                Kembali
            </a>
        </div>
        
        <form method="POST" action="{{ route('admin.employees.store') }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="employee_number" class="block text-sm font-semibold text-neglasari-text mb-1">Nomor Pegawai (NIP / NIPD) *</label>
                    <input type="text" name="employee_number" id="employee_number" value="{{ old('employee_number') }}" required class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                    @error('employee_number')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="nik" class="block text-sm font-semibold text-neglasari-text mb-1">NIK</label>
                    <input type="text" name="nik" id="nik" value="{{ old('nik') }}" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                    @error('nik')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="full_name" class="block text-sm font-semibold text-neglasari-text mb-1">Nama Lengkap *</label>
                    <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" required class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                    @error('full_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="gender" class="block text-sm font-semibold text-neglasari-text mb-1">Jenis Kelamin *</label>
                    <select name="gender" id="gender" required class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="birth_place" class="block text-sm font-semibold text-neglasari-text mb-1">Tempat Lahir</label>
                    <input type="text" name="birth_place" id="birth_place" value="{{ old('birth_place') }}" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                </div>
                
                <div>
                    <label for="birth_date" class="block text-sm font-semibold text-neglasari-text mb-1">Tanggal Lahir</label>
                    <input type="date" name="birth_date" id="birth_date" value="{{ old('birth_date') }}" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                </div>
                
                <div>
                    <label for="position_id" class="block text-sm font-semibold text-neglasari-text mb-1">Jabatan *</label>
                    <select name="position_id" id="position_id" required class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                        <option value="">Pilih Jabatan</option>
                        @foreach($positions as $position)
                            <option value="{{ $position->id }}" {{ old('position_id') == $position->id ? 'selected' : '' }}>
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
                        <option value="permanent" {{ old('employment_status') === 'permanent' ? 'selected' : '' }}>Tetap</option>
                        <option value="contract" {{ old('employment_status') === 'contract' ? 'selected' : '' }}>Kontrak</option>
                        <option value="internship" {{ old('employment_status') === 'internship' ? 'selected' : '' }}>Magang</option>
                    </select>
                    @error('employment_status')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="phone" class="block text-sm font-semibold text-neglasari-text mb-1">Nomor Telepon</label>
                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                </div>
                
                <div>
                    <label for="email" class="block text-sm font-semibold text-neglasari-text mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="joined_at" class="block text-sm font-semibold text-neglasari-text mb-1">Tanggal Bergabung</label>
                    <input type="date" name="joined_at" id="joined_at" value="{{ old('joined_at') }}" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">
                </div>
                
                <div class="flex items-center space-x-2 pt-6">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="h-4 w-4 text-neglasari-accent focus:ring-neglasari-accent border-neglasari-border rounded">
                    <label for="is_active" class="text-sm font-semibold text-neglasari-text">Status Aktif</label>
                </div>
            </div>
            
            <div class="col-span-2">
                <label for="address" class="block text-sm font-semibold text-neglasari-text mb-1">Alamat</label>
                <textarea name="address" id="address" rows="3" class="w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent">{{ old('address') }}</textarea>
            </div>
            
            <div class="flex justify-end space-x-2">
                <a href="{{ route('admin.employees.index') }}" class="px-6 py-2 bg-gray-200 text-neglasari-text font-semibold rounded-xl shadow-md hover:bg-gray-300 transition duration-150">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-neglasari-main text-white font-semibold rounded-xl shadow-md hover:bg-neglasari-accent transition duration-150">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
