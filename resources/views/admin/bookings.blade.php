@extends('layouts.app-main')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Header Section - Responsive -->
    <div class="mb-6 sm:mb-8">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Kelola Peminjaman</h2>
        <p class="text-sm sm:text-base text-gray-600 mt-1">Setujui atau tolak pengajuan peminjaman lab dari guru</p>
    </div>

    @if($bookings->count() > 0)
        <!-- Desktop Table View -->
        <div class="hidden lg:block bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengaju</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hari & Jam</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detail</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($bookings as $booking)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->teacher_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 capitalize">{{ $booking->day_name }}</div>
                                    <div class="text-sm text-gray-500">Jam {{ $booking->hour }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $booking->subject }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->class }}</div>
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
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $booking->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($booking->status === 'pending')
                                        <div class="flex space-x-2">
                                            <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST" data-ajax="true" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        data-confirm="Setujui peminjaman ini?"
                                                        class="text-green-600 hover:text-green-900 text-xs px-2 py-1 bg-green-100 rounded hover:bg-green-200">
                                                    Setujui
                                                </button>
                                            </form>
                                            
                                            <button onclick="openRejectModal({{ $booking->id }})" 
                                                    class="text-red-600 hover:text-red-900 text-xs px-2 py-1 bg-red-100 rounded hover:bg-red-200">
                                                Tolak
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">Sudah diproses</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile/Tablet Card View -->
        <div class="lg:hidden space-y-3">
            @foreach($bookings as $booking)
                <div class="bg-white rounded-lg shadow border border-gray-200 p-4">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-gray-900">
                                {{ $booking->teacher_name }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">{{ $booking->user->email }}</div>
                        </div>
                        <div class="flex-shrink-0 ml-3">
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
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                        <div>
                            <span class="text-gray-500 text-xs">Hari & Jam:</span>
                            <div class="font-medium text-gray-900">{{ $booking->day_name }}, Jam {{ $booking->hour }}</div>
                        </div>
                        <div>
                            <span class="text-gray-500 text-xs">Tanggal:</span>
                            <div class="text-gray-900">{{ $booking->created_at->format('d/m/Y') }}</div>
                        </div>
                    </div>
                    
                    <div class="space-y-2 text-sm mb-4">
                        <div class="flex">
                            <span class="text-gray-500 w-20 flex-shrink-0 text-xs">Mapel:</span>
                            <span class="text-gray-900 font-medium">{{ $booking->subject }}</span>
                        </div>
                        <div class="flex">
                            <span class="text-gray-500 w-20 flex-shrink-0 text-xs">Kelas:</span>
                            <span class="text-gray-900">{{ $booking->class }}</span>
                        </div>
                    </div>
                    
                    @if($booking->status === 'pending')
                        <div class="pt-3 border-t border-gray-200 flex space-x-2">
                            <form action="{{ route('admin.bookings.approve', $booking) }}" method="POST" data-ajax="true" class="flex-1">
                                @csrf
                                <button type="submit" 
                                        data-confirm="Setujui peminjaman ini?"
                                        class="w-full px-3 py-2 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition-colors">
                                    Setujui
                                </button>
                            </form>
                            
                            <button onclick="openRejectModal({{ $booking->id }})" 
                                    class="flex-1 px-3 py-2 bg-red-600 text-white text-xs font-medium rounded hover:bg-red-700 transition-colors">
                                Tolak
                            </button>
                        </div>
                    @else
                        <div class="pt-3 border-t border-gray-200 text-center">
                            <span class="text-gray-400 text-xs">Peminjaman sudah diproses</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-6 sm:p-8 text-center">
            <svg class="mx-auto h-10 w-10 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="mt-4 text-base sm:text-lg font-medium text-gray-900">Belum Ada Peminjaman</h3>
            <p class="mt-2 text-sm sm:text-base text-gray-600">
                Belum ada pengajuan peminjaman dari guru yang perlu diproses.
            </p>
        </div>
    @endif
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
function openRejectModal(bookingId) {
    document.getElementById('rejectForm').action = `/admin/bookings/${bookingId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('notes').value = '';
}
</script>

@endsection