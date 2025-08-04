@extends('layouts.app-main')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900">Edit Jadwal Pakem</h2>
        <p class="text-gray-600">Edit jadwal tetap lab komputer</p>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.schedules.update', $schedule) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Hidden field to track source -->
            @if(request('from_lab_schedule'))
                <input type="hidden" name="from_lab_schedule" value="1">
            @endif

            <div>
                <label for="day" class="block text-sm font-medium text-gray-700 mb-2">
                    Hari <span class="text-red-500">*</span>
                </label>
                <select name="day" 
                        id="day" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('day') border-red-500 @enderror"
                        required>
                    <option value="">Pilih Hari</option>
                    <option value="senin" {{ old('day', $schedule->day) === 'senin' ? 'selected' : '' }}>Senin</option>
                    <option value="selasa" {{ old('day', $schedule->day) === 'selasa' ? 'selected' : '' }}>Selasa</option>
                    <option value="rabu" {{ old('day', $schedule->day) === 'rabu' ? 'selected' : '' }}>Rabu</option>
                    <option value="kamis" {{ old('day', $schedule->day) === 'kamis' ? 'selected' : '' }}>Kamis</option>
                    <option value="jumat" {{ old('day', $schedule->day) === 'jumat' ? 'selected' : '' }}>Jumat</option>
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
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('hour') border-red-500 @enderror"
                        required>
                    <option value="">Pilih Jam</option>
                    @for($i = 1; $i <= 9; $i++)
                        <option value="{{ $i }}" {{ old('hour', $schedule->hour) == $i ? 'selected' : '' }}>Jam {{ $i }}</option>
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
                       value="{{ old('subject', $schedule->subject) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('subject') border-red-500 @enderror"
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
                       value="{{ old('class', $schedule->class) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('class') border-red-500 @enderror"
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
                       value="{{ old('teacher', $schedule->teacher) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('teacher') border-red-500 @enderror"
                       placeholder="Nama guru pengampu"
                       required>
                @error('teacher')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                <a href="{{ route('admin.schedules') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
                
                <button type="submit" 
                        class="inline-flex items-center px-6 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Update Jadwal Pakem
                </button>
            </div>
        </form>
    </div>
</div>
@endsection