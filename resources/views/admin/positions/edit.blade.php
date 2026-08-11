@extends('layouts.admin')

@section('page-title', 'Edit Jabatan')

@section('admin-content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-neglasari-border">
            <h2 class="text-xl font-bold text-neglasari-dark">Edit Jabatan: {{ $position->name }}</h2>
            <a href="{{ route('admin.positions.index') }}" class="text-sm font-semibold text-neglasari-text-secondary hover:text-neglasari-main flex items-center">
                &larr; Kembali
            </a>
        </div>

        <form action="{{ route('admin.positions.update', $position) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="code" class="block text-sm font-semibold text-neglasari-text mb-1">Kode Jabatan <span class="text-red-500">*</span></label>
                <input type="text" name="code" id="code" value="{{ old('code', $position->code) }}" required class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                @error('code') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="name" class="block text-sm font-semibold text-neglasari-text mb-1">Nama Jabatan <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $position->name) }}" required class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-neglasari-text mb-1">Deskripsi / Tugas Pokok</label>
                <textarea name="description" id="description" rows="3" class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">{{ old('description', $position->description) }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="sort_order" class="block text-sm font-semibold text-neglasari-text mb-1">Urutan Hierarki <span class="text-red-500">*</span></label>
                    <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $position->sort_order) }}" min="0" required class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                    @error('sort_order') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center mt-6">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $position->is_active) ? 'checked' : '' }} class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neglasari-main"></div>
                        <span class="ml-3 text-sm font-semibold text-neglasari-text">Status Aktif</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 flex justify-end space-x-3 border-t border-neglasari-border">
                <a href="{{ route('admin.positions.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-neglasari-main text-white font-semibold rounded-xl hover:bg-neglasari-accent transition shadow-md">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
