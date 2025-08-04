@extends('layouts.app-main')

@section('content')
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-0">
    <div class="mb-4 md:mb-8">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900">Riwayat Peminjaman</h2>
        <p class="text-sm md:text-base text-gray-600">Lihat status dan riwayat pengajuan peminjaman lab Anda</p>
    </div>

    @if($bookings->count() > 0)
        <!-- Status Summary for Mobile -->
        <div class="md:hidden mb-4 grid grid-cols-3 gap-2 text-xs">
            @php
                $pending = $bookings->where('status', 'pending')->count();
                $approved = $bookings->where('status', 'approved')->count();
                $rejected = $bookings->where('status', 'rejected')->count();
            @endphp
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-2 text-center">
                <div class="font-semibold text-yellow-800">{{ $pending }}</div>
                <div class="text-yellow-600">Pending</div>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-lg p-2 text-center">
                <div class="font-semibold text-green-800">{{ $approved }}</div>
                <div class="text-green-600">Disetujui</div>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-2 text-center">
                <div class="font-semibold text-red-800">{{ $rejected }}</div>
                <div class="text-red-600">Ditolak</div>
            </div>
        </div>

        <!-- Desktop Table View -->
        <div class="hidden lg:block bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mata Pelajaran</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($bookings as $booking)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $booking->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">
                                    {{ $booking->day_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Jam {{ $booking->hour }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $booking->subject }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $booking->class }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($booking->status === 'pending')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            {{ $booking->status_name }}
                                        </span>
                                    @elseif($booking->status === 'approved')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $booking->status_name }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            {{ $booking->status_name }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $booking->notes ?: '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tablet View -->
        <div class="hidden md:block lg:hidden bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jadwal</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mata Pelajaran</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($bookings as $booking)
                            <tr>
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ $booking->created_at->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    {{ ucfirst(substr($booking->day_name, 0, 3)) }}, Jam {{ $booking->hour }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-900">
                                    <div>{{ $booking->subject }}</div>
                                    <div class="text-xs text-gray-500">{{ $booking->class }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    @if($booking->status === 'pending')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            {{ $booking->status_name }}
                                        </span>
                                    @elseif($booking->status === 'approved')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            {{ $booking->status_name }}
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            {{ $booking->status_name }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden">
            <div class="space-y-3">
                @foreach($bookings as $booking)
                    <div class="bg-white rounded-lg shadow border-l-4 
                        @if($booking->status === 'pending') border-yellow-400
                        @elseif($booking->status === 'approved') border-green-400
                        @else border-red-400
                        @endif">
                        
                        <!-- Card Header -->
                        <div class="px-4 py-3 bg-gradient-to-r 
                            @if($booking->status === 'pending') from-yellow-50 to-orange-50
                            @elseif($booking->status === 'approved') from-green-50 to-emerald-50  
                            @else from-red-50 to-pink-50
                            @endif border-b border-gray-100">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 
                                        @if($booking->status === 'pending') text-yellow-600
                                        @elseif($booking->status === 'approved') text-green-600
                                        @else text-red-600
                                        @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v16a2 2 0 002 2z"></path>
                                    </svg>
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ $booking->day_name }}, Jam {{ $booking->hour }}
                                    </div>
                                </div>
                                @if($booking->status === 'pending')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        {{ $booking->status_name }}
                                    </span>
                                @elseif($booking->status === 'approved')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 border border-green-200">
                                        {{ $booking->status_name }}
                                    </span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800 border border-red-200">
                                        {{ $booking->status_name }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="p-4">
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="space-y-2">
                                    <div class="flex items-start">
                                        <svg class="w-3 h-3 mt-1 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <div>
                                            <span class="text-gray-500 text-xs">Mata Pelajaran</span>
                                            <div class="font-medium text-gray-900">{{ $booking->subject }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <svg class="w-3 h-3 mt-1 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0a2 2 0 01-2 2H7a2 2 0 01-2-2"></path>
                                        </svg>
                                        <div>
                                            <span class="text-gray-500 text-xs">Kelas</span>
                                            <div class="font-medium text-gray-900">{{ $booking->class }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <div class="flex items-start">
                                        <svg class="w-3 h-3 mt-1 mr-2 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <div>
                                            <span class="text-gray-500 text-xs">Tanggal Ajuan</span>
                                            <div class="font-medium text-gray-900">{{ $booking->created_at->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $booking->created_at->format('H:i') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($booking->notes)
                                <div class="mt-3 pt-3 border-t border-gray-100">
                                    <div class="flex items-start">
                                        <svg class="w-3 h-3 mt-1 mr-2 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                        </svg>
                                        <div>
                                            <span class="text-gray-500 text-xs">Catatan Admin</span>
                                            <div class="text-sm text-red-600 bg-red-50 rounded p-2 mt-1">{{ $booking->notes }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6 md:p-8 text-center">
            <svg class="mx-auto h-12 w-12 md:h-16 md:w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="mt-4 text-lg md:text-xl font-medium text-gray-900">Belum Ada Peminjaman</h3>
            <p class="mt-2 text-sm md:text-base text-gray-600">
                Anda belum pernah mengajukan peminjaman lab. Silakan lihat jadwal dan ajukan peminjaman pada slot yang tersedia.
            </p>
            <div class="mt-6">
                <a href="{{ route('guru.dashboard') }}" 
                   class="inline-flex items-center px-4 py-2 md:px-6 md:py-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v16a2 2 0 002 2z"></path>
                    </svg>
                    Lihat Jadwal Lab
                </a>
            </div>
        </div>
    @endif

    <!-- Quick Actions for Mobile -->
    <div class="md:hidden mt-4 space-y-3">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-blue-600 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium text-blue-800">Perlu mengajukan peminjaman lagi?</span>
                </div>
                <a href="{{ route('guru.dashboard') }}" 
                   class="px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded hover:bg-blue-700 transition-colors duration-200">
                    Lihat Jadwal
                </a>
            </div>
        </div>
        
        @if($bookings->count() > 0)
            <div class="text-center">
                <button onclick="window.scrollTo(0, 0)" 
                        class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                    </svg>
                    Kembali ke Atas
                </button>
            </div>
        @endif
    </div>
</div>
@endsection