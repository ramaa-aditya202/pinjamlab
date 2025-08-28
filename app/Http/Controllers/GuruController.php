<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\LabSchedule;
use App\Models\LabBooking;

class GuruController extends Controller
{
    // Dashboard guru - menampilkan jadwal lab dalam bentuk tabel
    public function dashboard()
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
                    ];
                } elseif ($approvedBooking) {
                    $scheduleData[$day][$hour] = [
                        'type' => 'booked',
                        'subject' => $approvedBooking->subject,
                        'class' => $approvedBooking->class,
                        'teacher' => $approvedBooking->teacher_name,
                    ];
                } else {
                    $scheduleData[$day][$hour] = [
                        'type' => 'available',
                    ];
                }
            }
        }
        
        return view('guru.dashboard', compact('scheduleData', 'days', 'hours'));
    }

    // Form pengajuan peminjaman
    public function createBooking(Request $request)
    {
        $day = $request->day;
        $hour = $request->hour;
        
        // Validasi apakah slot tersedia
        $isFixed = LabSchedule::where('day', $day)->where('hour', $hour)->where('is_fixed', true)->exists();
        $isBooked = LabBooking::where('day', $day)->where('hour', $hour)->whereIn('status', ['pending', 'approved'])->exists();
        
        if ($isFixed || $isBooked) {
            return redirect()->route('guru.dashboard')->with('error', 'Slot jadwal tidak tersedia');
        }
        
        return view('guru.create-booking', compact('day', 'hour'));
    }

    // Simpan pengajuan peminjaman
    public function storeBooking(Request $request)
    {
        $request->validate([
            'day' => 'required|in:senin,selasa,rabu,kamis,jumat',
            'hour' => 'required|integer|min:1|max:9',
            'teacher_name' => 'required|string|max:255',
            'class' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
        ]);

        // Cek lagi apakah slot masih tersedia
        $isFixed = LabSchedule::where('day', $request->day)->where('hour', $request->hour)->where('is_fixed', true)->exists();
        $isBooked = LabBooking::where('day', $request->day)->where('hour', $request->hour)->whereIn('status', ['pending', 'approved'])->exists();
        
        if ($isFixed || $isBooked) {
            return redirect()->route('guru.dashboard')->with('error', 'Slot jadwal tidak tersedia');
        }

        $booking = LabBooking::create([
            'user_id' => auth()->id(),
            'day' => $request->day,
            'hour' => $request->hour,
            'teacher_name' => $request->teacher_name,
            'class' => $request->class,
            'subject' => $request->subject,
            'status' => 'pending'
        ]);

        // Kirim notifikasi ke n8n
        $this->sendBookingNotification($booking);

        return redirect()->route('guru.dashboard')->with('success', 'Pengajuan peminjaman berhasil disubmit');
    }

    // Lihat riwayat peminjaman guru
    public function myBookings()
    {
        $bookings = LabBooking::where('user_id', auth()->id())->orderBy('created_at', 'desc')->get();
        return view('guru.my-bookings', compact('bookings'));
    }

    /**
     * Kirim notifikasi peminjaman ke n8n
     */
    private function sendBookingNotification($booking)
    {
        try {
            $user = auth()->user();
            $dayNames = [
                'senin' => 'Senin',
                'selasa' => 'Selasa', 
                'rabu' => 'Rabu',
                'kamis' => 'Kamis',
                'jumat' => 'Jumat'
            ];

            $notificationData = [
                'event_type' => 'booking_created',
                'booking_id' => $booking->id,
                'user_name' => $user->name,
                'user_email' => $user->email,
                'day' => $dayNames[$booking->day] ?? $booking->day,
                'hour' => $booking->hour,
                'teacher_name' => $booking->teacher_name,
                'class' => $booking->class,
                'subject' => $booking->subject,
                'status' => $booking->status,
                'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
                'message' => "📝 *Pengajuan Peminjaman Lab Baru*\n\n" .
                            "👤 *Pengaju:* {$user->name}\n" .
                            "📧 *Email:* {$user->email}\n" .
                            "📅 *Hari:* " . ($dayNames[$booking->day] ?? $booking->day) . "\n" .
                            "🕐 *Jam:* Jam ke-{$booking->hour}\n" .
                            "👨‍🏫 *Guru:* {$booking->teacher_name}\n" .
                            "🏫 *Kelas:* {$booking->class}\n" .
                            "📚 *Mata Pelajaran:* {$booking->subject}\n" .
                            "⏰ *Waktu Pengajuan:* " . $booking->created_at->format('d/m/Y H:i') . "\n\n" .
                            "Silakan cek dashboard admin untuk menyetujui atau menolak pengajuan ini."
            ];

            // Kirim ke internal webhook
            Http::timeout(5)->post(route('webhook.booking.notification'), $notificationData);

        } catch (\Exception $e) {
            Log::error('Failed to send booking notification: ' . $e->getMessage());
            // Jangan gagalkan proses utama jika notifikasi gagal
        }
    }
}
