@extends('layouts.app-main')

@section('content')
<div class="max-w-full mx-auto px-4 sm:px-6 lg:px-0">
    <div class="mb-4 md:mb-8">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900">Jadwal Lab Komputer</h2>
        <p class="text-sm md:text-base text-gray-600">Lihat jadwal pakem dan ajukan peminjaman lab pada slot yang tersedia</p>
    </div>

    <!-- Legend -->
    <div class="mb-4 md:mb-6 bg-white rounded-lg shadow p-3 md:p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-2 md:mb-3">Keterangan:</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 md:gap-4 text-xs">
            <div class="flex items-center">
                <div class="w-3 h-3 md:w-4 md:h-4 bg-red-100 border border-red-300 rounded mr-2 flex-shrink-0"></div>
                <span>Jadwal Pakem</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 md:w-4 md:h-4 bg-yellow-100 border border-yellow-300 rounded mr-2 flex-shrink-0"></div>
                <span>Sudah Dipinjam</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 md:w-4 md:h-4 bg-green-100 border border-green-300 rounded mr-2 flex-shrink-0"></div>
                <span>Tersedia</span>
            </div>
        </div>
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jam</th>
                        @foreach($days as $day)
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ ucfirst($day) }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($hours as $hour)
                        <tr>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                Jam {{ $hour }}
                            </td>
                            @foreach($days as $day)
                                @php
                                    $slot = $scheduleData[$day][$hour] ?? ['type' => 'available'];
                                @endphp
                                <td class="px-4 py-4 whitespace-nowrap text-sm">
                                    @if($slot['type'] === 'fixed')
                                        <div class="bg-red-100 border border-red-300 rounded-lg p-3">
                                            <div class="text-red-800 font-medium">{{ $slot['subject'] }}</div>
                                            <div class="text-red-600 text-xs">{{ $slot['class'] }}</div>
                                            <div class="text-red-600 text-xs">{{ $slot['teacher'] }}</div>
                                            <div class="text-red-500 text-xs mt-1">Jadwal Pakem</div>
                                        </div>
                                    @elseif($slot['type'] === 'approved')
                                        <div class="bg-yellow-100 border border-yellow-300 rounded-lg p-3">
                                            <div class="text-yellow-800 font-medium">{{ $slot['subject'] }}</div>
                                            <div class="text-yellow-600 text-xs">{{ $slot['class'] }}</div>
                                            <div class="text-yellow-600 text-xs">{{ $slot['teacher'] }}</div>
                                            <div class="text-yellow-500 text-xs mt-1">Sudah Dipinjam</div>
                                        </div>
                                    @elseif($slot['type'] === 'pending')
                                        <div class="bg-orange-100 border border-orange-300 rounded-lg p-3">
                                            <div class="text-orange-800 font-medium">{{ $slot['subject'] }}</div>
                                            <div class="text-orange-600 text-xs">{{ $slot['class'] }}</div>
                                            <div class="text-orange-600 text-xs">{{ $slot['teacher'] }}</div>
                                            <div class="text-orange-500 text-xs mt-1">Menunggu Persetujuan</div>
                                        </div>
                                    @else
                                        <div class="bg-green-100 border border-green-300 rounded-lg p-3">
                                            <div class="text-green-800 font-medium">Tersedia</div>
                                            <div class="text-green-600 text-xs mb-2">Slot kosong</div>
                                            <a href="{{ route('guru.booking.create', ['day' => $day, 'hour' => $hour]) }}" 
                                               class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition-colors">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Ajukan Peminjaman
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tablet View -->
    <div class="hidden md:block lg:hidden">
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Jam</th>
                            @foreach($days as $day)
                                <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                    {{ substr($day, 0, 3) }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($hours as $hour)
                            <tr>
                                <td class="px-3 py-3 text-sm font-medium text-gray-900">{{ $hour }}</td>
                                @foreach($days as $day)
                                    @php
                                        $slot = $scheduleData[$day][$hour] ?? ['type' => 'available'];
                                    @endphp
                                    <td class="px-2 py-3 text-xs">
                                        @if($slot['type'] === 'fixed')
                                            <div class="bg-red-100 border border-red-300 rounded p-2">
                                                <div class="text-red-800 font-medium text-xs">{{ substr($slot['subject'], 0, 12) }}{{ strlen($slot['subject']) > 12 ? '...' : '' }}</div>
                                                <div class="text-red-600 text-xs">{{ $slot['class'] }}</div>
                                                <div class="text-red-500 text-xs">Pakem</div>
                                            </div>
                                        @elseif($slot['type'] === 'approved')
                                            <div class="bg-yellow-100 border border-yellow-300 rounded p-2">
                                                <div class="text-yellow-800 font-medium text-xs">{{ substr($slot['subject'], 0, 12) }}{{ strlen($slot['subject']) > 12 ? '...' : '' }}</div>
                                                <div class="text-yellow-600 text-xs">{{ $slot['class'] }}</div>
                                                <div class="text-yellow-500 text-xs">Dipinjam</div>
                                            </div>
                                        @elseif($slot['type'] === 'pending')
                                            <div class="bg-orange-100 border border-orange-300 rounded p-2">
                                                <div class="text-orange-800 font-medium text-xs">{{ substr($slot['subject'], 0, 12) }}{{ strlen($slot['subject']) > 12 ? '...' : '' }}</div>
                                                <div class="text-orange-600 text-xs">{{ $slot['class'] }}</div>
                                                <div class="text-orange-500 text-xs">Pending</div>
                                            </div>
                                        @else
                                            <div class="bg-green-100 border border-green-300 rounded p-2">
                                                <div class="text-green-800 font-medium text-xs mb-1">Tersedia</div>
                                                <a href="{{ route('guru.booking.create', ['day' => $day, 'hour' => $hour]) }}" 
                                                   class="inline-block px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700">
                                                    Ajukan
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden">
        <div class="space-y-3">
            @foreach($days as $day)
                <div class="bg-white rounded-lg shadow border border-gray-200">
                    <div class="px-4 py-3 bg-gradient-to-r from-indigo-50 to-blue-50 border-b border-gray-200 rounded-t-lg">
                        <h3 class="text-base font-semibold text-gray-900 capitalize flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v16a2 2 0 002 2z"></path>
                            </svg>
                            {{ $day }}
                        </h3>
                    </div>
                    <div class="p-3">
                        <div class="grid grid-cols-3 gap-2">
                            @foreach($hours as $hour)
                                @php
                                    $slot = $scheduleData[$day][$hour] ?? ['type' => 'available'];
                                @endphp
                                <div class="border border-gray-200 rounded-lg p-2 min-h-[80px] flex flex-col">
                                    <div class="text-xs font-medium text-gray-700 mb-2 text-center bg-gray-50 rounded px-1 py-0.5">
                                        Jam {{ $hour }}
                                    </div>
                                    @if($slot['type'] === 'fixed')
                                        <div class="bg-red-50 border border-red-200 rounded p-2 flex-1 flex flex-col justify-between">
                                            <div>
                                                <div class="text-red-800 font-medium text-xs mb-1 leading-tight">
                                                    {{ strlen($slot['subject']) > 10 ? substr($slot['subject'], 0, 10) . '...' : $slot['subject'] }}
                                                </div>
                                                <div class="text-red-600 text-xs">{{ $slot['class'] }}</div>
                                            </div>
                                            <div class="text-red-500 text-xs mt-1 text-center bg-red-100 rounded px-1">Pakem</div>
                                        </div>
                                    @elseif($slot['type'] === 'approved')
                                        <div class="bg-yellow-50 border border-yellow-200 rounded p-2 flex-1 flex flex-col justify-between">
                                            <div>
                                                <div class="text-yellow-800 font-medium text-xs mb-1 leading-tight">
                                                    {{ strlen($slot['subject']) > 10 ? substr($slot['subject'], 0, 10) . '...' : $slot['subject'] }}
                                                </div>
                                                <div class="text-yellow-600 text-xs">{{ $slot['class'] }}</div>
                                            </div>
                                            <div class="text-yellow-500 text-xs mt-1 text-center bg-yellow-100 rounded px-1">Dipinjam</div>
                                        </div>
                                    @elseif($slot['type'] === 'pending')
                                        <div class="bg-orange-50 border border-orange-200 rounded p-2 flex-1 flex flex-col justify-between">
                                            <div>
                                                <div class="text-orange-800 font-medium text-xs mb-1 leading-tight">
                                                    {{ strlen($slot['subject']) > 10 ? substr($slot['subject'], 0, 10) . '...' : $slot['subject'] }}
                                                </div>
                                                <div class="text-orange-600 text-xs">{{ $slot['class'] }}</div>
                                            </div>
                                            <div class="text-orange-500 text-xs mt-1 text-center bg-orange-100 rounded px-1">Pending</div>
                                        </div>
                                    @else
                                        <div class="bg-green-50 border border-green-200 rounded p-2 flex-1 flex flex-col justify-between">
                                            <div class="text-green-800 font-medium text-xs mb-2 text-center">Tersedia</div>
                                            <a href="{{ route('guru.booking.create', ['day' => $day, 'hour' => $hour]) }}" 
                                               class="inline-block w-full text-center px-2 py-1.5 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition-colors duration-200 transform hover:scale-105">
                                                Ajukan
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Info Box for Mobile -->
    <div class="md:hidden mt-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-3">
        <div class="flex items-start">
            <svg class="w-4 h-4 text-blue-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-xs text-blue-800">
                <p class="font-medium mb-1">Tips Penggunaan:</p>
                <ul class="space-y-1">
                    <li>• Tap "Ajukan" untuk mengajukan peminjaman</li>
                    <li>• Scroll untuk melihat semua hari</li>
                    <li>• Cek status di "Riwayat Peminjaman"</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Quick Action Button for Mobile -->
    <div class="md:hidden mt-4">
        <a href="{{ route('guru.my-bookings') }}" 
           class="w-full inline-flex items-center justify-center px-4 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors duration-200 shadow-md">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Lihat Riwayat Peminjaman
        </a>
    </div>
</div>
@endsection