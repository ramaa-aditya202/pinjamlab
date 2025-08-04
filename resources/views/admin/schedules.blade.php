@extends('layouts.app-main')

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Kelola Jadwal Pakem</h2>
            <p class="text-gray-600">Kelola jadwal tetap lab komputer yang tidak dapat dipinjam</p>
        </div>
        <a href="{{ route('admin.schedules.create') }}" 
           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m-6 0h6m6 0H6"></path>
            </svg>
            Tambah Jadwal Pakem
        </a>
    </div>

    @if($schedules->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Guru</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($schedules as $schedule)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 capitalize">
                                    {{ $schedule->day_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Jam {{ $schedule->hour }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $schedule->subject }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $schedule->class }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $schedule->teacher }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.schedules.edit', $schedule) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 text-xs px-2 py-1 bg-indigo-100 rounded hover:bg-indigo-200">
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 text-xs px-2 py-1 bg-red-100 rounded hover:bg-red-200"
                                                    onclick="return confirm('Hapus jadwal pakem ini?')">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile View -->
        <div class="md:hidden mt-6">
            <div class="space-y-4">
                @foreach($schedules as $schedule)
                    <div class="bg-white rounded-lg shadow p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $schedule->day_name }}, Jam {{ $schedule->hour }}
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                Pakem
                            </span>
                        </div>
                        
                        <div class="space-y-2 text-sm text-gray-600">
                            <div><strong>Mata Pelajaran:</strong> {{ $schedule->subject }}</div>
                            <div><strong>Kelas:</strong> {{ $schedule->class }}</div>
                            <div><strong>Guru:</strong> {{ $schedule->teacher }}</div>
                        </div>
                        
                        <div class="mt-4 flex space-x-2">
                            <a href="{{ route('admin.schedules.edit', $schedule) }}" 
                               class="px-3 py-1 bg-indigo-600 text-white text-xs rounded hover:bg-indigo-700">
                                Edit
                            </a>
                            <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-3 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700"
                                        onclick="return confirm('Hapus jadwal pakem ini?')">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-8 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v16a2 2 0 002 2z"></path>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Belum Ada Jadwal Pakem</h3>
            <p class="mt-2 text-sm text-gray-600">
                Belum ada jadwal pakem yang dibuat. Klik tombol di atas untuk menambah jadwal pakem.
            </p>
            <div class="mt-6">
                <a href="{{ route('admin.schedules.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    Tambah Jadwal Pakem
                </a>
            </div>
        </div>
    @endif
</div>
@endsection