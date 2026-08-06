@extends('layouts.employee')

@section('title', '- Buat Pengajuan')

@section('employee-content')
<div class="space-y-6">
    <div class="bg-white rounded-2xl shadow-lg p-6 border border-neglasari-border">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-neglasari-dark">Buat Pengajuan Baru</h2>
            <a href="{{ route('pegawai.leave-requests.index') }}" class="text-sm text-neglasari-main font-semibold hover:underline">Kembali</a>
        </div>

        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg text-sm text-blue-800">
            <strong>Info:</strong> Pengajuan yang dibuat akan memengaruhi absensi Anda jika sudah disetujui oleh Kepala Desa atau Admin.
        </div>
        
        <form action="{{ route('pegawai.leave-requests.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label for="type" class="block text-sm font-semibold text-neglasari-text mb-1">Jenis Pengajuan <span class="text-red-500">*</span></label>
                <select name="type" id="type" class="w-full rounded-xl border-neglasari-border focus:border-neglasari-main focus:ring focus:ring-neglasari-main focus:ring-opacity-50 @error('type') border-red-500 @enderror" required>
                    <option value="">Pilih Jenis</option>
                    @foreach($types as $type)
                        <option value="{{ $type->value }}" {{ old('type') === $type->value ? 'selected' : '' }}>{{ $type->label() }}</option>
                    @endforeach
                </select>
                @error('type') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="start_date" class="block text-sm font-semibold text-neglasari-text mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}" class="w-full rounded-xl border-neglasari-border focus:border-neglasari-main focus:ring focus:ring-neglasari-main focus:ring-opacity-50 @error('start_date') border-red-500 @enderror" required>
                    @error('start_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-semibold text-neglasari-text mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}" class="w-full rounded-xl border-neglasari-border focus:border-neglasari-main focus:ring focus:ring-neglasari-main focus:ring-opacity-50 @error('end_date') border-red-500 @enderror" required>
                    @error('end_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="reason" class="block text-sm font-semibold text-neglasari-text mb-1">Alasan Lengkap <span class="text-red-500">*</span></label>
                <textarea name="reason" id="reason" rows="4" class="w-full rounded-xl border-neglasari-border focus:border-neglasari-main focus:ring focus:ring-neglasari-main focus:ring-opacity-50 @error('reason') border-red-500 @enderror" required placeholder="Jelaskan alasan pengajuan Anda secara detail...">{{ old('reason') }}</textarea>
                @error('reason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="attachment" class="block text-sm font-semibold text-neglasari-text mb-1">Lampiran Pendukung <span class="text-gray-400 font-normal">(Opsional)</span></label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl relative hover:bg-gray-50 transition">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label for="attachment" class="relative cursor-pointer bg-white rounded-md font-medium text-neglasari-main hover:text-neglasari-accent focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-neglasari-main">
                                <span>Upload sebuah file</span>
                                <input id="attachment" name="attachment" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png">
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">PDF, PNG, JPG maksimal 5MB</p>
                    </div>
                </div>
                @error('attachment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <p id="file-name-display" class="text-xs text-neglasari-main mt-2 font-medium"></p>
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3 bg-neglasari-main text-white font-bold rounded-xl shadow-md hover:bg-neglasari-accent transition duration-150">
                    Kirim Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('attachment').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name;
        if (fileName) {
            document.getElementById('file-name-display').textContent = 'File terpilih: ' + fileName;
        } else {
            document.getElementById('file-name-display').textContent = '';
        }
    });
</script>
@endpush
@endsection
