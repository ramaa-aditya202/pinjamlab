<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\LabBooking;
use App\Models\User;

class TestTelegramIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:telegram-integration {--booking-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Telegram integration dengan tombol setujui/tolak';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🤖 Testing Telegram Integration...');
        $this->newLine();

        // Cek konfigurasi
        if (!$this->checkConfiguration()) {
            return 1;
        }

        // Test dengan booking yang ada atau buat dummy
        $bookingId = $this->option('booking-id');
        
        if ($bookingId) {
            $booking = LabBooking::with('user')->find($bookingId);
            if (!$booking) {
                $this->error("❌ Booking dengan ID {$bookingId} tidak ditemukan");
                return 1;
            }
        } else {
            $this->info('📝 Membuat booking dummy untuk testing...');
            $booking = $this->createDummyBooking();
        }

        $this->info("📋 Testing dengan Booking ID: {$booking->id}");
        $this->info("👤 User: {$booking->user->name}");
        $this->info("📅 Jadwal: " . ucfirst($booking->day) . ", Jam ke-{$booking->hour}");
        $this->newLine();

        // Test kirim notifikasi dengan tombol
        if ($this->testBookingNotification($booking)) {
            $this->info('✅ Notifikasi berhasil dikirim ke Telegram dengan tombol');
            
            // Simulasi callback API
            if ($this->testCallbackAPI($booking)) {
                $this->info('✅ Test callback API berhasil');
                
                // Cleanup dummy booking jika dibuat
                if (!$this->option('booking-id')) {
                    $booking->delete();
                    $this->info('🗑️  Dummy booking telah dihapus');
                }
                
                $this->newLine();
                $this->info('🎉 Semua test berhasil! Implementasi Telegram siap digunakan.');
                return 0;
            }
        }

        return 1;
    }

    /**
     * Check required configuration
     */
    private function checkConfiguration(): bool
    {
        $this->info('⚙️  Mengecek konfigurasi...');

        $requiredConfigs = [
            'N8N_WEBHOOK_URL' => config('services.n8n.webhook_url'),
            'TELEGRAM_BOT_TOKEN' => config('services.telegram.bot_token'),
            'TELEGRAM_WEBHOOK_TOKEN' => config('services.telegram.webhook_token'),
        ];

        foreach ($requiredConfigs as $key => $value) {
            if (empty($value)) {
                $this->error("❌ {$key} tidak dikonfigurasi");
                $this->line("   Silakan tambahkan ke file .env");
                return false;
            } else {
                $this->info("✅ {$key}: " . substr($value, 0, 20) . '...');
            }
        }

        return true;
    }

    /**
     * Create dummy booking for testing
     */
    private function createDummyBooking(): LabBooking
    {
        $guru = User::where('role', 'guru')->first();
        if (!$guru) {
            $this->error('❌ Tidak ada user dengan role guru');
            exit(1);
        }

        return LabBooking::create([
            'user_id' => $guru->id,
            'day' => 'senin',
            'hour' => 1,
            'teacher_name' => 'Guru Test',
            'class' => 'XII IPA 1',
            'subject' => 'Testing Telegram',
            'status' => 'pending'
        ]);
    }

    /**
     * Test sending booking notification with buttons
     */
    private function testBookingNotification($booking): bool
    {
        try {
            $this->info('📤 Mengirim notifikasi booking ke n8n...');

            $user = $booking->user;
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
                'message' => "📝 PENGAJUAN PEMINJAMAN LAB BARU (TEST)\n\n" .
                            "👤 Pengaju: {$user->name}\n" .
                            "📧 Email: {$user->email}\n" .
                            "📅 Hari: " . ($dayNames[$booking->day] ?? $booking->day) . "\n" .
                            "🕐 Jam: Jam ke-{$booking->hour}\n" .
                            "👨‍🏫 Guru: {$booking->teacher_name}\n" .
                            "🏫 Kelas: {$booking->class}\n" .
                            "📚 Mata Pelajaran: {$booking->subject}\n" .
                            "⏰ Waktu Pengajuan: " . $booking->created_at->format('d/m/Y H:i') . "\n\n" .
                            "🧪 Ini adalah TEST MESSAGE - Tombol setujui/tolak harusnya muncul!"
            ];

            $response = Http::timeout(10)->post(route('webhook.booking.notification'), $notificationData);

            if ($response->successful()) {
                $this->info('✅ Notifikasi berhasil dikirim');
                $this->line('   Cek grup Telegram, harusnya ada pesan dengan tombol ✅ Setujui dan ❌ Tolak');
                return true;
            } else {
                $this->error('❌ Gagal mengirim notifikasi: ' . $response->body());
                return false;
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Test callback API endpoint
     */
    private function testCallbackAPI($booking): bool
    {
        try {
            $this->info('🔧 Testing callback API endpoint...');

            // Test approve action
            $approveData = [
                'action' => 'approve',
                'booking_id' => $booking->id,
                'callback_query_id' => 'test_query_' . time(),
                'user_id' => '123456789',
                'chat_id' => '-4846058783',
                'message_id' => '999'
            ];

            $response = Http::withHeaders([
                'X-Telegram-Token' => config('services.telegram.webhook_token'),
                'Content-Type' => 'application/json'
            ])->post(route('api.telegram.booking-action'), $approveData);

            if ($response->successful()) {
                $this->info('✅ Test approve API berhasil');
                
                // Reload booking untuk cek status
                $booking->refresh();
                if ($booking->status === 'approved') {
                    $this->info('✅ Status booking berhasil diupdate ke approved');
                } else {
                    $this->warn('⚠️  Status booking tidak berubah: ' . $booking->status);
                }
                
                return true;
            } else {
                $this->error('❌ Test approve API gagal: ' . $response->body());
                return false;
            }

        } catch (\Exception $e) {
            $this->error('❌ Error testing API: ' . $e->getMessage());
            return false;
        }
    }
}
