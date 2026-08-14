@extends('layouts.admin')

@section('page-title', 'Pengaturan Sistem')

@section('admin-content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-neglasari-border">
            <div>
                <h2 class="text-xl font-bold text-neglasari-dark">Pengaturan Sistem SIAP Neglasari</h2>
                <p class="text-xs text-neglasari-text-secondary mt-1">Konfigurasi parameter instansi, absensi, dan keamanan</p>
            </div>
        </div>

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Informasi Instansi & Logo -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-neglasari-main uppercase tracking-wider border-b border-neglasari-border pb-2">Informasi Instansi & Logo Desa</h3>
                
                <!-- Card Upload Logo -->
                <div class="bg-neglasari-bg/50 p-4 rounded-2xl border border-neglasari-border flex flex-col sm:flex-row items-center gap-4">
                    <div class="w-24 h-24 bg-white rounded-xl p-2 border border-neglasari-border flex items-center justify-center shadow-sm flex-shrink-0">
                        <img src="{{ asset('images/logo-tasikmalaya.png') }}?v={{ time() }}" alt="Logo Instansi Current" class="max-h-full max-w-full object-contain">
                    </div>
                    <div class="space-y-2 text-center sm:text-left flex-1">
                        <label for="logo" class="block text-sm font-semibold text-neglasari-text">Upload Logo Instansi / Desa Baru</label>
                        <input type="file" name="logo" id="logo" accept="image/png,image/jpeg,image/svg+xml" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-neglasari-main file:text-white hover:file:bg-neglasari-accent cursor-pointer">
                        <p class="text-[11px] text-neglasari-text-secondary">Format: PNG, JPG, SVG. Maksimal 2 MB. Logo otomatis diperbarui di seluruh sistem.</p>
                        @error('logo') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-neglasari-text mb-1">Nama Instansi / Desa</label>
                        <input type="text" name="settings[app_name]" value="Pemerintah Desa Neglasari" class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-neglasari-text mb-1">Kabupaten / Kota</label>
                        <input type="text" name="settings[regency]" value="Kabupaten Tasikmalaya" class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-neglasari-text mb-1">Alamat Kantor Desa</label>
                        <textarea name="settings[address]" rows="2" class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">Jl. Raya Neglasari No. 01, Desa Neglasari, Kecamatan Jatinunggal</textarea>
                    </div>
                </div>
            </div>

            <!-- Parameter Kehadiran -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-neglasari-main uppercase tracking-wider border-b border-neglasari-border pb-2">Aturan & Validasi Absensi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-neglasari-text mb-1">Toleransi Keterlambatan Default (Menit)</label>
                        <input type="number" name="settings[default_late_tolerance]" value="15" class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-neglasari-text mb-1">Maksimal Jarak GPS (Meter)</label>
                        <input type="number" name="settings[default_radius]" value="100" class="w-full px-4 py-2 border border-neglasari-border rounded-xl focus:ring-neglasari-accent focus:border-neglasari-accent">
                    </div>
                </div>
            </div>

            <!-- Notifikasi & Sistem -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold text-neglasari-main uppercase tracking-wider border-b border-neglasari-border pb-2">Fitur Keamanan</h3>
                <div class="space-y-3">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="settings[enable_face_recognition]" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neglasari-main"></div>
                        <span class="ml-3 text-sm font-semibold text-neglasari-text">Wajib Swafoto Kamera Depan saat Absen</span>
                    </label>
                    <br>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="settings[enable_strict_geofence]" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-neglasari-main"></div>
                        <span class="ml-3 text-sm font-semibold text-neglasari-text">Tolak Absensi di Luar Radius GPS</span>
                    </label>
                </div>
            </div>

            <div class="pt-4 flex justify-end space-x-3 border-t border-neglasari-border">
                <button type="submit" class="px-6 py-2.5 bg-neglasari-main text-white font-semibold rounded-xl hover:bg-neglasari-accent transition shadow-md">Simpan Pengaturan</button>
            </div>
        </form>
    </div>
</div>
@endsection
