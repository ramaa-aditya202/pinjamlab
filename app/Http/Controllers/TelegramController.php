<?php

namespace App\Http\Controllers;

use App\Models\LabBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    /**
     * Handle booking action dari Telegram callback
     */
    public function handleBookingAction(Request $request)
    {
        try {
            // Validasi token untuk keamanan
            $expectedToken = config('services.telegram.webhook_token');
            $providedToken = $request->header('X-Telegram-Token');
            
            if (!$expectedToken || $providedToken !== $expectedToken) {
                Log::warning('Unauthorized Telegram callback attempt', [
                    'provided_token' => $providedToken,
                    'ip' => $request->ip()
                ]);
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $action = $request->input('action');
            $bookingId = $request->input('booking_id');
            $callbackQueryId = $request->input('callback_query_id');
            $chatId = $request->input('chat_id');
            $messageId = $request->input('message_id');

            // Validasi input
            if (!in_array($action, ['approve', 'reject'])) {
                return response()->json(['error' => 'Invalid action'], 400);
            }

            if (!$bookingId || !$callbackQueryId) {
                return response()->json(['error' => 'Missing required parameters'], 400);
            }

            // Cari booking
            $booking = LabBooking::with('user')->find($bookingId);
            if (!$booking) {
                return response()->json(['error' => 'Booking not found'], 404);
            }

            // Cek apakah booking masih pending
            if ($booking->status !== 'pending') {
                $this->answerCallbackQuery($callbackQueryId, "⚠️ Booking ini sudah diproses sebelumnya!");
                return response()->json(['message' => 'Booking already processed']);
            }

            // Proses action
            if ($action === 'approve') {
                $this->approveBooking($booking, $callbackQueryId, $chatId, $messageId);
            } else {
                $this->rejectBooking($booking, $callbackQueryId, $chatId, $messageId);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error handling Telegram booking action: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    /**
     * Approve booking via Telegram
     */
    private function approveBooking($booking, $callbackQueryId, $chatId, $messageId)
    {
        // Update status booking
        $booking->update(['status' => 'approved']);
        
        // Answer callback query
        $this->answerCallbackQuery($callbackQueryId, "✅ Peminjaman telah disetujui!");
        
        // Update message dengan status baru
        $this->updateTelegramMessage($chatId, $messageId, $booking, 'approved');
        
        // Kirim notifikasi status update ke n8n
        $this->sendStatusNotification($booking, 'approved');
        
        Log::info('Booking approved via Telegram', [
            'booking_id' => $booking->id,
            'user' => $booking->user->name
        ]);
    }

    /**
     * Reject booking via Telegram
     */
    private function rejectBooking($booking, $callbackQueryId, $chatId, $messageId)
    {
        // Update status booking
        $booking->update([
            'status' => 'rejected',
            'notes' => 'Ditolak melalui Telegram'
        ]);
        
        // Answer callback query
        $this->answerCallbackQuery($callbackQueryId, "❌ Peminjaman telah ditolak!");
        
        // Update message dengan status baru
        $this->updateTelegramMessage($chatId, $messageId, $booking, 'rejected');
        
        // Kirim notifikasi status update ke n8n
        $this->sendStatusNotification($booking, 'rejected', 'Ditolak melalui Telegram');
        
        Log::info('Booking rejected via Telegram', [
            'booking_id' => $booking->id,
            'user' => $booking->user->name
        ]);
    }

    /**
     * Answer callback query untuk feedback ke user
     */
    private function answerCallbackQuery($callbackQueryId, $text)
    {
        $telegramBotToken = config('services.telegram.bot_token');
        
        try {
            Http::timeout(5)->post("https://api.telegram.org/bot{$telegramBotToken}/answerCallbackQuery", [
                'callback_query_id' => $callbackQueryId,
                'text' => $text,
                'show_alert' => false
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to answer callback query: ' . $e->getMessage());
        }
    }

    /**
     * Update message di Telegram dengan status baru
     */
    private function updateTelegramMessage($chatId, $messageId, $booking, $status)
    {
        $telegramBotToken = config('services.telegram.bot_token');
        
        $dayNames = [
            'senin' => 'Senin',
            'selasa' => 'Selasa',
            'rabu' => 'Rabu', 
            'kamis' => 'Kamis',
            'jumat' => 'Jumat'
        ];

        $statusIcon = $status === 'approved' ? '✅' : '❌';
        $statusText = $status === 'approved' ? 'DISETUJUI' : 'DITOLAK';
        
        $updatedMessage = "📝 PENGAJUAN PEMINJAMAN LAB - {$statusIcon} {$statusText}\n\n" .
                         "👤 Pengaju: {$booking->user->name}\n" .
                         "📧 Email: {$booking->user->email}\n" .
                         "📅 Hari: " . ($dayNames[$booking->day] ?? $booking->day) . "\n" .
                         "🕐 Jam: Jam ke-{$booking->hour}\n" .
                         "👨‍🏫 Guru: {$booking->teacher_name}\n" .
                         "🏫 Kelas: {$booking->class}\n" .
                         "📚 Mata Pelajaran: {$booking->subject}\n" .
                         "⏰ Waktu Pengajuan: " . $booking->created_at->format('d/m/Y H:i') . "\n" .
                         "✅ Diproses pada: " . now()->format('d/m/Y H:i');

        try {
            Http::timeout(5)->post("https://api.telegram.org/bot{$telegramBotToken}/editMessageText", [
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'text' => $updatedMessage,
                'parse_mode' => 'HTML'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update Telegram message: ' . $e->getMessage());
        }
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
            
            $message = "{$statusIcon} PEMINJAMAN LAB {$statusText}\n\n" .
                      "👤 Pengaju: {$booking->user->name}\n" .
                      "📧 Email: {$booking->user->email}\n" .
                      "📅 Hari: " . ($dayNames[$booking->day] ?? $booking->day) . "\n" .
                      "🕐 Jam: Jam ke-{$booking->hour}\n" .
                      "👨‍🏫 Guru: {$booking->teacher_name}\n" .
                      "🏫 Kelas: {$booking->class}\n" .
                      "📚 Mata Pelajaran: {$booking->subject}\n";
            
            if ($notes && $status === 'rejected') {
                $message .= "📝 Alasan: {$notes}\n";
            }
            
            $message .= "⏰ Diproses pada: " . now()->format('d/m/Y H:i') . "\n" .
                       "🤖 Diproses via: Telegram Bot";

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
                'processed_via' => 'telegram',
                'message' => $message
            ];

            Http::timeout(5)->post(route('webhook.booking.notification'), $notificationData);

        } catch (\Exception $e) {
            Log::error('Failed to send status notification: ' . $e->getMessage());
        }
    }
}
