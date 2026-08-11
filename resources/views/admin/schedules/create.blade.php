@extends('layouts.admin')

@section('page-title', 'Tambah Jadwal Kerja')

@section('admin-content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-neglasari-border">
            <h2 class="text-xl font-bold text-neglasari-dark">Tambah Jadwal Kerja Baru</h2>
            <a href="{{ route('admin.schedules.index') }}" class="text-sm font-semibold text-neglasari-text-secondary hover:text-neglasari-main flex items-center">
                &larr; Kembali
            </a>
        </div>

        <form action="{{ route('admin.schedules.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-semibold text-neglasari-text mb-1">Nama Jadwal <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Jam Kerja Senin - Kamis" class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="day_of_week" class="block text-sm font-semibold text-neglasari-text mb-1">Hari Kerja <span class="text-red-500">*</span></label>
                    <select name="day_of_week" id="day_of_week" required class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                        <option value="1" {{ old('day_of_week') == 1 ? 'selected' : '' }}>Senin</option>
                        <option value="2" {{ old('day_of_week') == 2 ? 'selected' : '' }}>Selasa</option>
                        <option value="3" {{ old('day_of_week') == 3 ? 'selected' : '' }}>Rabu</option>
                        <option value="4" {{ old('day_of_week') == 4 ? 'selected' : '' }}>Kamis</option>
                        <option value="5" {{ old('day_of_week') == 5 ? 'selected' : '' }}>Jumat</option>
                        <option value="6" {{ old('day_of_week') == 6 ? 'selected' : '' }}>Sabtu</option>
                        <option value="7" {{ old('day_of_week') == 7 ? 'selected' : '' }}>Minggu</option>
                    </select>
                    @error('day_of_week') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Jam Masuk -->
            <div class="p-4 bg-neglasari-bg rounded-xl border border-neglasari-border">
                <h3 class="text-sm font-bold text-neglasari-dark mb-3">Pengaturan Jam Masuk</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="check_in_start" class="block text-xs font-semibold text-neglasari-text mb-1">Awal Jam Masuk</label>
                        <input type="time" name="check_in_start" id="check_in_start" value="{{ old('check_in_start', '07:00') }}" required class="w-full px-3 py-2 border border-neglasari-border rounded-xl">
                    </div>
                    <div>
                        <label for="check_in_time" class="block text-xs font-semibold text-neglasari-text mb-1">Jam Masuk Utama <span class="text-red-500">*</span></label>
                        <input type="time" name="check_in_time" id="check_in_time" value="{{ old('check_in_time', '08:00') }}" required class="w-full px-3 py-2 border border-neglasari-border rounded-xl font-bold text-green-700">
                    </div>
                    <div>
                        <label for="check_in_end" class="block text-xs font-semibold text-neglasari-text mb-1">Batas Akhir Masuk</label>
                        <input type="time" name="check_in_end" id="check_in_end" value="{{ old('check_in_end', '09:00') }}" required class="w-full px-3 py-2 border border-neglasari-border rounded-xl">
                    </div>
                </div>
            </div>

            <!-- Jam Pulang -->
            <div class="p-4 bg-neglasari-bg rounded-xl border border-neglasari-border">
                <h3 class="text-sm font-bold text-neglasari-dark mb-3">Pengaturan Jam Pulang</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="check_out_start" class="block text-xs font-semibold text-neglasari-text mb-1">Awal Jam Pulang</label>
                        <input type="time" name="check_out_start" id="check_out_start" value="{{ old('check_out_start', '15:30') }}" required class="w-full px-3 py-2 border border-neglasari-border rounded-xl">
                    </div>
                    <div>
                        <label for="check_out_time" class="block text-xs font-semibold text-neglasari-text mb-1">Jam Pulang Utama <span class="text-red-500">*</span></label>
                        <input type="time" name="check_out_time" id="check_out_time" value="{{ old('check_out_time', '16:00') }}" required class="w-full px-3 py-2 border border-neglasari-border rounded-xl font-bold text-blue-700">
                    </div>
                    <div>
                        <label for="check_out_end" class="block text-xs font-semibold text-neglasari-text mb-1">Batas Akhir Pulang</label>
                        <input type="time" name="check_out_end" id="check_out_end" value="{{ old('check_out_end', '18:00') }}" required class="w-full px-3 py-2 border border-neglasari-border rounded-xl">
                    </div>
                </div>
            </div>

            <!-- Toleransi Keterlambatan -->
            <div>
                <label for="late_tolerance_minutes" class="block text-sm font-semibold text-neglasari-text mb-1">Toleransi Keterlambatan (Menit) <span class="text-red-500">*</span></label>
                <input type="number" name="late_tolerance_minutes" id="late_tolerance_minutes" value="{{ old('late_tolerance_minutes', 15) }}" min="0" required class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                @error('late_tolerance_minutes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex flex-wrap items-center gap-6 pt-2">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_workday" value="1" {{ old('is_workday', true) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neglasari-main"></div>
                    <span class="ml-3 text-sm font-semibold text-neglasari-text">Hari Kerja</span>
                </label>

                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_default" value="1" {{ old('is_default', false) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neglasari-main"></div>
                    <span class="ml-3 text-sm font-semibold text-neglasari-text">Jadwal Default</span>
                </label>

                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neglasari-main"></div>
                    <span class="ml-3 text-sm font-semibold text-neglasari-text">Status Aktif</span>
                </label>
            </div>

            <div class="pt-4 flex justify-end space-x-3 border-t border-neglasari-border">
                <a href="{{ route('admin.schedules.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-neglasari-main text-white font-semibold rounded-xl hover:bg-neglasari-accent transition shadow-md">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>
@endsection
