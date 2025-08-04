@extends('layouts.app-main')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-0">
    <div class="mb-6 md:mb-8">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900">Ajukan Peminjaman Lab</h2>
        <p class="text-sm md:text-base text-gray-600">Isi form berikut untuk mengajukan peminjaman lab komputer</p>
    </div>

    <!-- Slot Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 md:p-4 mb-4 md:mb-6">
        <div class="flex items-center">
            <svg class="w-4 h-4 md:w-5 md:h-5 text-blue-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="text-sm font-semibold text-blue-800">Slot yang Dipilih</h3>
                <p class="text-sm text-blue-600">{{ ucfirst($day) }}, Jam {{ $hour }}</p>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow p-4 md:p-6">
        <form action="{{ route('guru.booking.store') }}" method="POST" class="space-y-4 md:space-y-6">
            @csrf
            
            <input type="hidden" name="day" value="{{ $day }}">
            <input type="hidden" name="hour" value="{{ $hour }}">

            <div>
                <label for="teacher_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Guru <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="teacher_name" 
                       name="teacher_name" 
                       value="{{ old('teacher_name', auth()->user()->name) }}"
                       class="w-full px-3 py-3 md:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('teacher_name') border-red-500 @enderror"
                       placeholder="Masukkan nama guru"
                       required>
                @error('teacher_name')
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
                       class="w-full px-3 py-3 md:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('class') border-red-500 @enderror"
                       placeholder="Contoh: X IPA 1, XI TKJ, XII RPL"
                       required>
                @error('class')
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
                       class="w-full px-3 py-3 md:py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm @error('subject') border-red-500 @enderror"
                       placeholder="Contoh: Pemrograman Web, Basis Data, TIK"
                       required>
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-4 md:pt-6 border-t border-gray-200">
                <a href="{{ route('guru.dashboard') }}" 
                   class="inline-flex items-center justify-center px-4 py-3 md:py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 order-2 sm:order-1">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
                
                <button type="submit" 
                        class="inline-flex items-center justify-center px-6 py-3 md:py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 order-1 sm:order-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Ajukan Peminjaman
                </button>
            </div>
        </form>
    </div>

    <!-- Information -->
    <div class="mt-4 md:mt-6 bg-gray-50 rounded-lg p-3 md:p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-2">Informasi Penting:</h3>
        <ul class="text-xs text-gray-600 space-y-1">
            <li>• Pengajuan akan dikirim ke admin untuk persetujuan</li>
            <li>• Pastikan data yang diisi sudah benar sebelum submit</li>
            <li>• Status pengajuan dapat dilihat di menu "Riwayat Peminjaman"</li>
            <li>• Pengajuan yang disetujui tidak dapat dibatalkan</li>
        </ul>
    </div>
</div>
@endsection