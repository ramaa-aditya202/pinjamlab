@extends('layouts.app-main')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header Section - Responsive -->
    <div class="mb-6 sm:mb-8">
        <div class="flex flex-col space-y-4 sm:flex-row sm:items-center sm:justify-between sm:space-y-0">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Jadwal Lab Komputer</h2>
                <p class="text-sm sm:text-base text-gray-600 mt-1">Lihat dan kelola jadwal lab secara keseluruhan</p>
            </div>
            <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                <a href="{{ route('admin.schedules.create') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    <span class="hidden sm:inline">Tambah Jadwal Pakem</span>
                    <span class="sm:hidden">Tambah Jadwal</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Legend - Responsive -->
    <div class="mb-4 sm:mb-6 bg-white rounded-lg shadow p-3 sm:p-4">
        <h3 class="text-sm font-semibold text-gray-700 mb-2 sm:mb-3">Keterangan:</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-4 text-xs">
            <div class="flex items-center">
                <div class="w-3 h-3 sm:w-4 sm:h-4 bg-red-100 border border-red-300 rounded mr-2"></div>
                <span>Jadwal Pakem (Tetap)</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 sm:w-4 sm:h-4 bg-yellow-100 border border-yellow-300 rounded mr-2"></div>
                <span>Sudah Dipinjam (Disetujui)</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 sm:w-4 sm:h-4 bg-green-100 border border-green-300 rounded mr-2"></div>
                <span>Tersedia</span>
            </div>
        </div>
    </div>

    <!-- Schedule Table - Responsive -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Mobile view -->
        <div class="block sm:hidden">
            @foreach($days as $day)
                <div class="border-b border-gray-200 last:border-b-0">
                    <div class="bg-gray-50 px-4 py-3">
                        <h3 class="text-sm font-semibold text-gray-900">{{ ucfirst($day) }}</h3>
                    </div>
                    <div class="p-4 space-y-3">
                        @foreach($hours as $hour)
                            @php
                                $slot = $scheduleData[$day][$hour] ?? ['type' => 'available'];
                            @endphp
                            <div class="flex items-start space-x-3">
                                <div class="text-xs font-medium text-gray-500 w-12 flex-shrink-0 mt-1">
                                    Jam {{ $hour }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    @if($slot['type'] === 'fixed')
                                        <div class="bg-red-100 border border-red-300 rounded-lg p-3 relative">
                                            <div class="text-red-800 font-medium text-sm">{{ $slot['subject'] }}</div>
                                            <div class="text-red-600 text-xs">{{ $slot['class'] }}</div>
                                            <div class="text-red-600 text-xs">{{ $slot['teacher'] }}</div>
                                            <div class="text-red-500 text-xs mt-1">Jadwal Pakem</div>
                                            
                                            <!-- Mobile action buttons -->
                                            <div class="flex space-x-2 mt-2">
                                                <a href="{{ route('admin.schedules.edit', $slot['id']) }}?from_lab_schedule=1" 
                                                   class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-xs">
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.schedules.destroy', $slot['id']) }}?from_lab_schedule=1" method="POST" data-delete="true" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            data-confirm="Hapus jadwal pakem ini?"
                                                            class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif($slot['type'] === 'approved')
                                        <div class="bg-yellow-100 border border-yellow-300 rounded-lg p-3 relative">
                                            <div class="text-yellow-800 font-medium text-sm">{{ $slot['subject'] }}</div>
                                            <div class="text-yellow-600 text-xs">{{ $slot['class'] }}</div>
                                            <div class="text-yellow-600 text-xs">{{ $slot['teacher'] }}</div>
                                            <div class="text-yellow-500 text-xs mt-1">Peminjaman Disetujui</div>
                                            
                                            <!-- Mobile cancel button -->
                                            <div class="mt-2">
                                                <form action="{{ route('admin.bookings.cancel', $slot['id']) }}" method="POST" data-delete="true" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            data-confirm="Batalkan peminjaman ini?"
                                                            class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">
                                                        Batalkan
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif($slot['type'] === 'pending')
                                        <div class="bg-orange-100 border border-orange-300 rounded-lg p-3 relative">
                                            <div class="text-orange-800 font-medium text-sm">{{ $slot['subject'] }}</div>
                                            <div class="text-orange-600 text-xs">{{ $slot['class'] }}</div>
                                            <div class="text-orange-600 text-xs">{{ $slot['teacher'] }}</div>
                                            <div class="text-orange-500 text-xs mt-1">Menunggu Persetujuan</div>
                                            
                                            <!-- Mobile action buttons -->
                                            <div class="mt-2 flex space-x-1">
                                                <form action="{{ route('admin.bookings.approve', $slot['id']) }}" method="POST" data-ajax="true" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            data-confirm="Setujui peminjaman ini?"
                                                            class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs">
                                                        Setujui
                                                    </button>
                                                </form>
                                                <button onclick="showRejectModal({{ $slot['id'] }})"
                                                        class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">
                                                    Tolak
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-green-100 border border-green-300 rounded-lg p-3 text-center">
                                            <div class="text-green-600 text-sm">Tersedia</div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Desktop view -->
        <div class="hidden sm:block overflow-x-auto">
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
                                        <div class="bg-red-100 border border-red-300 rounded-lg p-3 relative group">
                                            <div class="text-red-800 font-medium">{{ $slot['subject'] }}</div>
                                            <div class="text-red-600 text-xs">{{ $slot['class'] }}</div>
                                            <div class="text-red-600 text-xs">{{ $slot['teacher'] }}</div>
                                            <div class="text-red-500 text-xs mt-1">Jadwal Pakem</div>
                                            
                                            <!-- Desktop action buttons -->
                                            <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity flex space-x-1">
                                                <a href="{{ route('admin.schedules.edit', $slot['id']) }}?from_lab_schedule=1" 
                                                   class="bg-blue-500 hover:bg-blue-600 text-white p-1 rounded text-xs"
                                                   title="Edit Jadwal">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </a>
                                                <form action="{{ route('admin.schedules.destroy', $slot['id']) }}?from_lab_schedule=1" method="POST" data-delete="true" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            data-confirm="Hapus jadwal pakem ini?"
                                                            class="bg-red-500 hover:bg-red-600 text-white p-1 rounded text-xs"
                                                            title="Hapus Jadwal">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif($slot['type'] === 'approved')
                                        <div class="bg-yellow-100 border border-yellow-300 rounded-lg p-3 relative group">
                                            <div class="text-yellow-800 font-medium">{{ $slot['subject'] }}</div>
                                            <div class="text-yellow-600 text-xs">{{ $slot['class'] }}</div>
                                            <div class="text-yellow-600 text-xs">{{ $slot['teacher'] }}</div>
                                            <div class="text-yellow-500 text-xs mt-1">Peminjaman Disetujui</div>
                                            
                                            <!-- Desktop action button for canceling booking -->
                                            <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <form action="{{ route('admin.bookings.cancel', $slot['id']) }}" method="POST" data-delete="true" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            data-confirm="Batalkan peminjaman ini?"
                                                            class="bg-red-500 hover:bg-red-600 text-white p-1 rounded text-xs"
                                                            title="Batalkan Peminjaman">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif($slot['type'] === 'pending')
                                        <div class="bg-orange-100 border border-orange-300 rounded-lg p-3 relative group">
                                            <div class="text-orange-800 font-medium">{{ $slot['subject'] }}</div>
                                            <div class="text-orange-600 text-xs">{{ $slot['class'] }}</div>
                                            <div class="text-orange-600 text-xs">{{ $slot['teacher'] }}</div>
                                            <div class="text-orange-500 text-xs mt-1">Menunggu Persetujuan</div>
                                            
                                            <!-- Desktop action buttons for approve/reject -->
                                            <div class="absolute top-1 right-1 opacity-0 group-hover:opacity-100 transition-opacity flex space-x-1">
                                                <form action="{{ route('admin.bookings.approve', $slot['id']) }}" method="POST" data-ajax="true" class="inline">
                                                    @csrf
                                                    <button type="submit" 
                                                            data-confirm="Setujui peminjaman ini?"
                                                            class="bg-green-500 hover:bg-green-600 text-white p-1 rounded text-xs"
                                                            title="Setujui Peminjaman">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                                <button onclick="showRejectModal({{ $slot['id'] }})"
                                                        class="bg-red-500 hover:bg-red-600 text-white p-1 rounded text-xs"
                                                        title="Tolak Peminjaman">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-green-100 border border-green-300 rounded-lg p-3 text-center">
                                            <div class="text-green-600 text-sm">Tersedia</div>
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

<!-- Reject Modal - Responsive -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50 p-4">
    <div class="relative min-h-screen flex items-center justify-center">
        <div class="relative bg-white rounded-lg shadow-lg w-full max-w-md mx-auto">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tolak Peminjaman</h3>
                <form id="rejectForm" method="POST" data-modal="true">
                    @csrf
                    <div class="mb-4">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Alasan Penolakan
                        </label>
                        <textarea name="notes" 
                                  id="notes" 
                                  rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                  placeholder="Masukkan alasan penolakan (opsional)"></textarea>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                        <button type="button" 
                                onclick="closeRejectModal()"
                                class="w-full sm:w-auto px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 text-sm font-medium">
                            Batal
                        </button>
                        <button type="submit" 
                                class="w-full sm:w-auto px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">
                            Tolak Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showRejectModal(bookingId) {
    document.getElementById('rejectForm').action = `/admin/bookings/${bookingId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('notes').value = '';
}
</script>
@endsection