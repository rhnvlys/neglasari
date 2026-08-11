@extends('layouts.admin')

@section('page-title', 'Edit Hari Libur')

@section('admin-content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-neglasari-border">
            <h2 class="text-xl font-bold text-neglasari-dark">Edit Hari Libur: {{ $holiday->name }}</h2>
            <a href="{{ route('admin.holidays.index') }}" class="text-sm font-semibold text-neglasari-text-secondary hover:text-neglasari-main flex items-center">
                &larr; Kembali
            </a>
        </div>

        <form action="{{ route('admin.holidays.update', $holiday) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-semibold text-neglasari-text mb-1">Nama Hari Libur <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $holiday->name) }}" required class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-semibold text-neglasari-text mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $holiday->start_date?->format('Y-m-d')) }}" required class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                    @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="end_date" class="block text-sm font-semibold text-neglasari-text mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $holiday->end_date?->format('Y-m-d')) }}" required class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                    @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="type" class="block text-sm font-semibold text-neglasari-text mb-1">Kategori Libur <span class="text-red-500">*</span></label>
                <select name="type" id="type" required class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                    @foreach($types as $type)
                        <option value="{{ $type->value }}" {{ old('type', is_object($holiday->type) ? $holiday->type->value : $holiday->type) == $type->value ? 'selected' : '' }}>
                            {{ $type->value }}
                        </option>
                    @endforeach
                </select>
                @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-neglasari-text mb-1">Deskripsi / Catatan</label>
                <textarea name="description" id="description" rows="3" class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">{{ old('description', $holiday->description) }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-3 pt-2">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="applies_to_all" value="1" {{ old('applies_to_all', $holiday->applies_to_all) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neglasari-main"></div>
                    <span class="ml-3 text-sm font-semibold text-neglasari-text">Berlaku untuk Semua Pegawai</span>
                </label>
                <br>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $holiday->is_active) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neglasari-main"></div>
                    <span class="ml-3 text-sm font-semibold text-neglasari-text">Status Aktif</span>
                </label>
            </div>

            <div class="pt-4 flex justify-end space-x-3 border-t border-neglasari-border">
                <a href="{{ route('admin.holidays.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-neglasari-main text-white font-semibold rounded-xl hover:bg-neglasari-accent transition shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
