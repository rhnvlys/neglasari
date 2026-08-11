@extends('layouts.admin')

@section('page-title', 'Tambah Lokasi Kantor')

@section('admin-content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-neglasari-border">
            <h2 class="text-xl font-bold text-neglasari-dark">Tambah Lokasi Kantor Baru</h2>
            <a href="{{ route('admin.locations.index') }}" class="text-sm font-semibold text-neglasari-text-secondary hover:text-neglasari-main flex items-center">
                &larr; Kembali
            </a>
        </div>

        <form action="{{ route('admin.locations.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-neglasari-text mb-1">Nama Lokasi / Kantor <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required placeholder="Contoh: Kantor Desa Neglasari" class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="address" class="block text-sm font-semibold text-neglasari-text mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                <textarea name="address" id="address" rows="2" required placeholder="Jl. Raya Desa Neglasari No..." class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">{{ old('address') }}</textarea>
                @error('address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="latitude" class="block text-sm font-semibold text-neglasari-text mb-1">Latitude (Lintang) <span class="text-red-500">*</span></label>
                    <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude', '-6.914744') }}" required class="w-full px-4 py-2 border border-neglasari-border rounded-xl font-mono focus:ring-neglasari-accent focus:border-neglasari-accent">
                    @error('latitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="longitude" class="block text-sm font-semibold text-neglasari-text mb-1">Longitude (Bujur) <span class="text-red-500">*</span></label>
                    <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude', '107.609810') }}" required class="w-full px-4 py-2 border border-neglasari-border rounded-xl font-mono focus:ring-neglasari-accent focus:border-neglasari-accent">
                    @error('longitude') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="radius_meters" class="block text-sm font-semibold text-neglasari-text mb-1">Radius Toleransi (Meter) <span class="text-red-500">*</span></label>
                    <input type="number" name="radius_meters" id="radius_meters" value="{{ old('radius_meters', 100) }}" min="1" required class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                    @error('radius_meters') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="maximum_accuracy_meters" class="block text-sm font-semibold text-neglasari-text mb-1">Max Akurasi GPS (Meter) <span class="text-red-500">*</span></label>
                    <input type="number" name="maximum_accuracy_meters" id="maximum_accuracy_meters" value="{{ old('maximum_accuracy_meters', 50) }}" min="1" required class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                    @error('maximum_accuracy_meters') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-3 pt-2">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="requires_photo" value="1" {{ old('requires_photo', true) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neglasari-main"></div>
                    <span class="ml-3 text-sm font-semibold text-neglasari-text">Wajibkan Swafoto (Selfie) saat Absen</span>
                </label>
                <br>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="allow_outside_radius" value="1" {{ old('allow_outside_radius', false) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neglasari-main"></div>
                    <span class="ml-3 text-sm font-semibold text-neglasari-text">Izinkan Absen di Luar Radius (Dinas Luar)</span>
                </label>
                <br>
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neglasari-main"></div>
                    <span class="ml-3 text-sm font-semibold text-neglasari-text">Status Lokasi Aktif</span>
                </label>
            </div>

            <div class="pt-4 flex justify-end space-x-3 border-t border-neglasari-border">
                <a href="{{ route('admin.locations.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-neglasari-main text-white font-semibold rounded-xl hover:bg-neglasari-accent transition shadow-md">Simpan Lokasi</button>
            </div>
        </form>
    </div>
</div>
@endsection
