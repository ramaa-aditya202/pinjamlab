<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\LabSchedule;
use App\Models\LabBooking;

class AdminController extends Controller
{
    // Dashboard admin
    public function dashboard()
    {
        $pendingBookings = LabBooking::where('status', 'pending')->count();
        $totalSchedules = LabSchedule::count();
        $approvedBookings = LabBooking::where('status', 'approved')->count();
        $rejectedBookings = LabBooking::where('status', 'rejected')->count();
        $totalFixedSchedules = LabSchedule::count(); // Same as $totalSchedules for fixed schedules
        $recentBookings = LabBooking::with('user')->latest()->take(5)->get();
        
        return view('admin.dashboard', compact(
            'pendingBookings', 
            'totalSchedules', 
            'approvedBookings', 
            'rejectedBookings',
            'totalFixedSchedules',
            'recentBookings'
        ));
    }

    // Kelola jadwal pakem
    public function schedules()
    {
        $schedules = LabSchedule::orderBy('day')->orderBy('hour')->get();
        return view('admin.schedules', compact('schedules'));
    }

    // Form tambah jadwal pakem
    public function createSchedule(Request $request)
    {
        $prefilledDay = $request->query('day');
        $prefilledHour = $request->query('hour');
        
        return view('admin.create-schedule', compact('prefilledDay', 'prefilledHour'));
    }

    // Simpan jadwal pakem
    public function storeSchedule(Request $request)
    {
        $request->validate([
            'day' => 'required|in:senin,selasa,rabu,kamis,jumat',
            'hour' => 'required|integer|min:1|max:9',
            'subject' => 'required|string|max:255',
            'class' => 'required|string|max:255',
            'teacher' => 'required|string|max:255',
        ]);

        // Cek apakah sudah ada jadwal pada hari dan jam yang sama
        $existingSchedule = LabSchedule::where('day', $request->day)
                                      ->where('hour', $request->hour)
                                      ->first();
        
        if ($existingSchedule) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Sudah ada jadwal pakem pada hari dan jam tersebut');
        }

        // Cek apakah sudah ada peminjaman yang disetujui pada hari dan jam yang sama
        $existingBooking = LabBooking::where('day', $request->day)
                                    ->where('hour', $request->hour)
                                    ->where('status', 'approved')
                                    ->first();
        
        if ($existingBooking) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Sudah ada peminjaman yang disetujui pada hari dan jam tersebut');
        }

        LabSchedule::create($request->all());

        // Redirect ke jadwal lab jika datang dari sana, atau ke schedules jika dari menu biasa
        if ($request->filled('from_lab_schedule')) {
            return redirect()->route('admin.lab-schedule')->with('success', 'Jadwal pakem berhasil ditambahkan');
        }

        return redirect()->route('admin.schedules')->with('success', 'Jadwal pakem berhasil ditambahkan');
    }

    // Edit jadwal pakem
    public function editSchedule(LabSchedule $schedule)
    {
        return view('admin.edit-schedule', compact('schedule'));
    }

    // Update jadwal pakem
    public function updateSchedule(Request $request, LabSchedule $schedule)
    {
        $request->validate([
            'day' => 'required|in:senin,selasa,rabu,kamis,jumat',
            'hour' => 'required|integer|min:1|max:9',
            'subject' => 'required|string|max:255',
            'class' => 'required|string|max:255',
            'teacher' => 'required|string|max:255',
        ]);

        // Cek apakah sudah ada jadwal lain pada hari dan jam yang sama (selain yang sedang diedit)
        $existingSchedule = LabSchedule::where('day', $request->day)
                                      ->where('hour', $request->hour)
                                      ->where('id', '!=', $schedule->id)
                                      ->first();
        
        if ($existingSchedule) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Sudah ada jadwal pakem lain pada hari dan jam tersebut');
        }

        // Cek apakah sudah ada peminjaman yang disetujui pada hari dan jam yang sama
        $existingBooking = LabBooking::where('day', $request->day)
                                    ->where('hour', $request->hour)
                                    ->where('status', 'approved')
                                    ->first();
        
        if ($existingBooking) {
            return redirect()->back()
                           ->withInput()
                           ->with('error', 'Sudah ada peminjaman yang disetujui pada hari dan jam tersebut');
        }

        $schedule->update($request->all());

        // Redirect ke jadwal lab jika datang dari sana, atau ke schedules jika dari menu biasa
        if ($request->filled('from_lab_schedule')) {
            return redirect()->route('admin.lab-schedule')->with('success', 'Jadwal pakem berhasil diperbarui');
        }

        return redirect()->route('admin.schedules')->with('success', 'Jadwal pakem berhasil diperbarui');
    }

    // Hapus jadwal pakem
    public function destroySchedule(LabSchedule $schedule)
    {
        $schedule->delete();
        
        // Redirect ke jadwal lab jika ada parameter from_lab_schedule
        if (request()->has('from_lab_schedule')) {
            return redirect()->route('admin.lab-schedule')->with('success', 'Jadwal pakem berhasil dihapus');
        }
        
        return redirect()->route('admin.schedules')->with('success', 'Jadwal pakem berhasil dihapus');
    }

    // Kelola peminjaman
    public function bookings()
    {
        $bookings = LabBooking::with('user')->orderBy('created_at', 'desc')->get();
        return view('admin.bookings', compact('bookings'));
    }

    // Approve peminjaman
    public function approveBooking(LabBooking $booking)
    {
        $booking->update(['status' => 'approved']);
        
        // Kirim notifikasi approval
        $this->sendStatusNotification($booking, 'approved');
        
        return redirect()->route('admin.bookings')->with('success', 'Peminjaman berhasil disetujui');
    }

    // Reject peminjaman
    public function rejectBooking(Request $request, LabBooking $booking)
    {
        $booking->update([
            'status' => 'rejected',
            'notes' => $request->notes
        ]);
        
        // Kirim notifikasi rejection
        $this->sendStatusNotification($booking, 'rejected', $request->notes);
        
        return redirect()->route('admin.bookings')->with('success', 'Peminjaman berhasil ditolak');
    }

    // Lihat jadwal lab keseluruhan (seperti di dashboard guru)
    public function labSchedule()
    {
        $days = ['senin', 'selasa', 'rabu', 'kamis', 'jumat'];
        $hours = range(1, 9);
        
        // Ambil jadwal pakem
        $fixedSchedules = LabSchedule::where('is_fixed', true)->get();
        
        // Ambil peminjaman yang sudah disetujui
        $approvedBookings = LabBooking::where('status', 'approved')->get();
        
        // Gabungkan data untuk ditampilkan dalam tabel
        $scheduleData = [];
        foreach ($days as $day) {
            $scheduleData[$day] = [];
            foreach ($hours as $hour) {
                // Cek apakah ada jadwal pakem
                $fixedSchedule = $fixedSchedules->where('day', $day)->where('hour', $hour)->first();
                
                // Cek apakah ada peminjaman yang disetujui
                $approvedBooking = $approvedBookings->where('day', $day)->where('hour', $hour)->first();
                
                if ($fixedSchedule) {
                    $scheduleData[$day][$hour] = [
                        'type' => 'fixed',
                        'subject' => $fixedSchedule->subject,
                        'class' => $fixedSchedule->class,
                        'teacher' => $fixedSchedule->teacher,
                        'id' => $fixedSchedule->id,
                        'model' => 'schedule'
                    ];
                } elseif ($approvedBooking) {
                    $scheduleData[$day][$hour] = [
                        'type' => 'booked',
                        'subject' => $approvedBooking->subject,
                        'class' => $approvedBooking->class,
                        'teacher' => $approvedBooking->teacher_name,
                        'id' => $approvedBooking->id,
                        'model' => 'booking'
                    ];
                } else {
                    $scheduleData[$day][$hour] = [
                        'type' => 'available',
                    ];
                }
            }
        }
        
        return view('admin.lab-schedule', compact('scheduleData', 'days', 'hours'));
    }

    // Hapus peminjaman yang disetujui
    public function cancelBooking(LabBooking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.lab-schedule')->with('success', 'Peminjaman berhasil dibatalkan');
    }

    /**
     * Kirim notifikasi status update ke n8n
     */
    private function sendStatusNotification($booking, $status, $notes = null)
    {
        try {
            $dayNames = [
                'senin' => 'Senin',
                'selasa' => 'Selasa', 
                'rabu' => 'Rabu',
                'kamis' => 'Kamis',
                'jumat' => 'Jumat'
            ];

            $statusIcon = $status === 'approved' ? '✅' : '❌';
            $statusText = $status === 'approved' ? 'DISETUJUI' : 'DITOLAK';
            
            $message = "{$statusIcon} *Peminjaman Lab {$statusText}*\n\n" .
                      "👤 *Pengaju:* {$booking->user->name}\n" .
                      "📧 *Email:* {$booking->user->email}\n" .
                      "📅 *Hari:* " . ($dayNames[$booking->day] ?? $booking->day) . "\n" .
                      "🕐 *Jam:* Jam ke-{$booking->hour}\n" .
                      "👨‍🏫 *Guru:* {$booking->teacher_name}\n" .
                      "🏫 *Kelas:* {$booking->class}\n" .
                      "📚 *Mata Pelajaran:* {$booking->subject}\n";
            
            if ($notes && $status === 'rejected') {
                $message .= "📝 *Alasan:* {$notes}\n";
            }
            
            $message .= "⏰ *Diproses pada:* " . now()->format('d/m/Y H:i');

            $notificationData = [
                'event_type' => 'booking_status_updated',
                'booking_id' => $booking->id,
                'status' => $status,
                'user_name' => $booking->user->name,
                'user_email' => $booking->user->email,
                'day' => $dayNames[$booking->day] ?? $booking->day,
                'hour' => $booking->hour,
                'teacher_name' => $booking->teacher_name,
                'class' => $booking->class,
                'subject' => $booking->subject,
                'notes' => $notes,
                'processed_at' => now()->format('Y-m-d H:i:s'),
                'message' => $message
            ];

            Http::timeout(5)->post(route('webhook.booking.notification'), $notificationData);

        } catch (\Exception $e) {
            Log::error('Failed to send status notification: ' . $e->getMessage());
        }
    }
}
