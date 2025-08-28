<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestBookingNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:booking-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test booking notification to n8n';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $testData = [
            'event_type' => 'test_notification',
            'booking_id' => 999,
            'user_name' => 'Test User',
            'user_email' => 'test@example.com',
            'day' => 'Senin',
            'hour' => 1,
            'teacher_name' => 'Test Teacher',
            'class' => 'Test Class',
            'subject' => 'Test Subject',
            'status' => 'pending',
            'created_at' => now()->format('Y-m-d H:i:s'),
            'message' => "🧪 *TEST - Pengajuan Peminjaman Lab*\n\n" .
                        "👤 *Pengaju:* Test User\n" .
                        "📧 *Email:* test@example.com\n" .
                        "📅 *Hari:* Senin\n" .
                        "🕐 *Jam:* Jam ke-1\n" .
                        "👨‍🏫 *Guru:* Test Teacher\n" .
                        "🏫 *Kelas:* Test Class\n" .
                        "📚 *Mata Pelajaran:* Test Subject\n" .
                        "⏰ *Waktu Pengajuan:* " . now()->format('d/m/Y H:i') . "\n\n" .
                        "Ini adalah pesan test dari sistem peminjaman lab."
        ];

        try {
            $this->info('🚀 Mengirim test notification ke n8n...');
            
            $response = Http::timeout(10)->post(route('webhook.booking.notification'), $testData);
            
            if ($response->successful()) {
                $this->info('✅ Test notification berhasil dikirim!');
                $this->info('📝 Data yang dikirim:');
                $this->line(json_encode($testData, JSON_PRETTY_PRINT));
            } else {
                $this->error('❌ Gagal mengirim test notification: ' . $response->status());
                $this->error('Response: ' . $response->body());
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}
