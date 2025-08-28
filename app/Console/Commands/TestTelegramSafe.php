<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestTelegramSafe extends Command
{
    protected $signature = 'test:telegram-safe';
    protected $description = 'Test Telegram dengan pesan tanpa karakter Markdown';

    public function handle()
    {
        $this->info('🧪 Testing Telegram Safe Message...');

        $testData = [
            'event_type' => 'test_safe_message',
            'message' => "📝 PENGAJUAN PEMINJAMAN LAB BARU\n\n" .
                        "👤 Pengaju: Test User\n" .
                        "📧 Email: test@example.com\n" .
                        "📅 Hari: Senin\n" .
                        "🕐 Jam: Jam ke-3\n" .
                        "👨‍🏫 Guru: Pak Ahmad\n" .
                        "🏫 Kelas: XII IPA 1\n" .
                        "📚 Mata Pelajaran: Matematika\n" .
                        "⏰ Waktu Pengajuan: 29/08/2025 10:45\n\n" .
                        "Silakan cek dashboard admin untuk menyetujui atau menolak pengajuan ini.",
            'booking_id' => 999,
            'user_name' => 'Test User',
            'user_email' => 'test@example.com'
        ];

        try {
            $this->info('📤 Mengirim test data...');
            
            $response = Http::timeout(30)->post(
                route('webhook.booking.notification'), 
                $testData
            );

            if ($response->successful()) {
                $this->info('✅ Webhook berhasil dikirim!');
                $this->info('📨 Response: ' . $response->body());
            } else {
                $this->error('❌ Webhook gagal!');
                $this->error('📨 Status: ' . $response->status());
                $this->error('📨 Response: ' . $response->body());
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}
