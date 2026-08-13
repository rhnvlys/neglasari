@extends('layouts.app')

@section('title', '- Login')

@section('content')
<div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Logo Resmi Desa Neglasari / Kabupaten -->
        <div class="flex justify-center flex-col items-center">
            <img src="{{ asset('images/logo-tasikmalaya.png') }}" alt="Logo Resmi Pemdes Neglasari" class="h-20 w-auto mb-3 object-contain filter drop-shadow-md">
            <h2 class="text-center text-3xl font-extrabold text-neglasari-dark">
                SIAP NEGLASARI
            </h2>
            <p class="mt-1 text-center text-sm font-medium text-neglasari-text-secondary">
                Sistem Informasi Absensi Perangkat Desa
            </p>
        </div>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-4 shadow-xl rounded-2xl sm:px-10 border border-neglasari-border">
            <form class="space-y-6" action="{{ route('login.post') }}" method="POST">
                @csrf
                <div>
                    <label for="login" class="block text-sm font-semibold text-neglasari-text">
                        Username atau Email
                    </label>
                    <div class="mt-1">
                        <input id="login" name="login" type="text" required value="{{ old('login') }}"
                            class="appearance-none block w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent sm:text-sm">
                    </div>
                    @error('login')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-neglasari-text">
                        Password
                    </label>
                    <div class="mt-1">
                        <input id="password" name="password" type="password" required
                            class="appearance-none block w-full px-3 py-2 border border-neglasari-border rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-neglasari-accent focus:border-neglasari-accent sm:text-sm">
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 text-neglasari-accent focus:ring-neglasari-accent border-neglasari-border rounded">
                        <label for="remember" class="ml-2 block text-sm text-neglasari-text-secondary">
                            Ingat saya
                        </label>
                    </div>
                </div>

                <div>
                    <button type="submit"
                        class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-xl shadow-sm text-sm font-semibold text-white bg-neglasari-main hover:bg-neglasari-accent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-neglasari-accent transition duration-150 ease-in-out">
                        Masuk Ke Sistem
                    </button>
                </div>
            </form>
            
            <div class="mt-6 border-t border-neglasari-border pt-4 text-center">
                <p class="text-xs text-neglasari-text-secondary">
                    Gunakan kredensial pengujian lokal (misal: username <code class="bg-gray-100 px-1 py-0.5 rounded text-neglasari-accent font-semibold">admin</code>, <code class="bg-gray-100 px-1 py-0.5 rounded text-neglasari-accent font-semibold">kades</code>, <code class="bg-gray-100 px-1 py-0.5 rounded text-neglasari-accent font-semibold">pegawai</code> dengan password <code class="bg-gray-100 px-1 py-0.5 rounded text-neglasari-accent font-semibold">password123</code>).
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
