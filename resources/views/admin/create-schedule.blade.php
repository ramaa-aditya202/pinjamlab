@extends('layouts.app-main')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header Section - Responsive -->
    <div class="mb-6 sm:mb-8">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Tambah Jadwal Pakem</h2>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Buat jadwal tetap lab komputer yang tidak dapat dipinjam</p>
    </div>

    <div class="bg-white rounded-lg shadow p-4 sm:p-6">
        <form action="{{ route('admin.schedules.store') }}" method="POST" class="space-y-4 sm:space-y-6">
            @csrf

            <!-- Hidden field to track source -->
            @if(request('day') || request('hour'))
                <input type="hidden" name="from_lab_schedule" value="1">
            @endif

            <div>
                <label for="day" class="block text-sm font-medium text-gray-700 mb-2">
                    Hari <span class="text-red-500">*</span>
                </label>
                <select name="day" 
                        id="day" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('day') border-red-500 @enderror"
                        required>
                    <option value="">Pilih Hari</option>
                    <option value="senin" {{ old('day', $prefilledDay ?? '') === 'senin' ? 'selected' : '' }}>Senin</option>
                    <option value="selasa" {{ old('day', $prefilledDay ?? '') === 'selasa' ? 'selected' : '' }}>Selasa</option>
                    <option value="rabu" {{ old('day', $prefilledDay ?? '') === 'rabu' ? 'selected' : '' }}>Rabu</option>
                    <option value="kamis" {{ old('day', $prefilledDay ?? '') === 'kamis' ? 'selected' : '' }}>Kamis</option>
                    <option value="jumat" {{ old('day', $prefilledDay ?? '') === 'jumat' ? 'selected' : '' }}>Jumat</option>
                </select>
                @error('day')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="hour" class="block text-sm font-medium text-gray-700 mb-2">
                    Jam Pelajaran <span class="text-red-500">*</span>
                </label>
                <select name="hour" 
                        id="hour" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('hour') border-red-500 @enderror"
                        required>
                    <option value="">Pilih Jam</option>
                    @for($i = 1; $i <= 9; $i++)
                        <option value="{{ $i }}" {{ old('hour', $prefilledHour ?? '') == $i ? 'selected' : '' }}>Jam {{ $i }}</option>
                    @endfor
                </select>
                @error('hour')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                    Mata Pelajaran <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="subject" 
                       name="subject" 
                       value="{{ old('subject') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('subject') border-red-500 @enderror"
                       placeholder="Contoh: Pemrograman Web, Basis Data, TIK"
                       required>
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="class" class="block text-sm font-medium text-gray-700 mb-2">
                    Kelas <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="class" 
                       name="class" 
                       value="{{ old('class') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('class') border-red-500 @enderror"
                       placeholder="Contoh: X IPA 1, XI TKJ, XII RPL"
                       required>
                @error('class')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="teacher" class="block text-sm font-medium text-gray-700 mb-2">
                    Guru Pengampu <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="teacher" 
                       name="teacher" 
                       value="{{ old('teacher') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('teacher') border-red-500 @enderror"
                       placeholder="Nama guru pengampu"
                       required>
                @error('teacher')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions - Responsive -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pt-4 sm:pt-6 border-t border-gray-200 space-y-3 sm:space-y-0">
                <a href="{{ route('admin.schedules') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
                
                <button type="submit" 
                        class="inline-flex items-center justify-center px-4 sm:px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span class="hidden sm:inline">Simpan Jadwal Pakem</span>
                    <span class="sm:hidden">Simpan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Information - Responsive -->
    <div class="mt-4 sm:mt-6 bg-gray-50 rounded-lg p-3 sm:p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-2">Informasi:</h3>
        <ul class="text-xs text-gray-600 space-y-1">
            <li>• Jadwal pakem adalah jadwal tetap yang tidak dapat dipinjam oleh guru</li>
            <li>• Pastikan tidak ada jadwal pakem lain pada hari dan jam yang sama</li>
            <li>• Jadwal pakem akan ditampilkan di dashboard guru dengan warna merah</li>
            <li>• Jadwal yang sudah dibuat dapat diedit atau dihapus jika diperlukan</li>
        </ul>
    </div>
</div>
@endsection