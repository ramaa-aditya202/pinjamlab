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
        $this->info('🔧 Checking configuration...');
        
        $webhookUrl = config('services.n8n.webhook_url');
        if (!$webhookUrl) {
            $this->error('❌ N8N_WEBHOOK_URL tidak dikonfigurasi di .env');
            $this->line('   Tambahkan: N8N_WEBHOOK_URL=https://your-n8n-instance.com/webhook/booking-notification');
            return 1;
        }
        
        $this->info("🔗 Webhook URL: {$webhookUrl}");

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
                $this->line('� Response Status: ' . $response->status());
                $this->line('📝 Response Body: ' . $response->body());
            } else {
                $this->error('❌ Gagal mengirim test notification: ' . $response->status());
                $this->error('📝 Response: ' . $response->body());
                
                if ($response->status() === 419) {
                    $this->line('');
                    $this->warn('💡 Tips untuk Error 419:');
                    $this->line('   1. Pastikan webhook dikecualikan dari CSRF');
                    $this->line('   2. Jalankan: php artisan config:clear');
                    $this->line('   3. Restart server: php artisan serve');
                }
            }
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->line('');
            $this->warn('💡 Possible issues:');
            $this->line('   1. N8N webhook URL tidak dapat diakses');
            $this->line('   2. Network connectivity problem');
            $this->line('   3. Laravel route tidak terdaftar');
        }
        
        return 0;
    }
}
