<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestN8NSwitch extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:n8n-switch {event_type=booking_created}';

    /**
     * The console command description.
     */
    protected $description = 'Test specific event_type untuk debug N8N Switch node';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $eventType = $this->argument('event_type');
        $webhookUrl = config('services.n8n.webhook_url');
        
        if (!$webhookUrl) {
            $this->error('❌ N8N_WEBHOOK_URL tidak dikonfigurasi di .env');
            return 1;
        }

        $this->info("🔍 Testing N8N Switch dengan event_type: {$eventType}");
        $this->info("🔗 Target URL: {$webhookUrl}");

        $testData = [
            'event_type' => $eventType,
            'booking_id' => 999,
            'user_name' => 'Debug User',
            'user_email' => 'debug@test.com',
            'day' => 'Senin',
            'hour' => 1,
            'teacher_name' => 'Debug Teacher',
            'class' => 'Debug Class',
            'subject' => 'Debug Subject',
            'status' => 'pending',
            'created_at' => now()->format('Y-m-d H:i:s'),
            'message' => "🧪 *DEBUG TEST - {$eventType}*\n\n" .
                        "Testing N8N Switch node dengan event_type: {$eventType}\n" .
                        "Timestamp: " . now()->format('d/m/Y H:i:s') . "\n\n" .
                        "Jika pesan ini sampai di Telegram, berarti Switch node sudah bekerja!"
        ];

        try {
            $this->info('🚀 Mengirim debug request...');
            
            // Kirim langsung ke N8N (bypass Laravel webhook)
            $response = Http::timeout(10)->post($webhookUrl, $testData);
            
            if ($response->successful()) {
                $this->info('✅ Debug request berhasil dikirim!');
                $this->line('📊 Status: ' . $response->status());
                $this->line('📝 Response: ' . $response->body());
                $this->line('');
                $this->info('📱 Check Telegram untuk melihat apakah pesan diterima.');
                $this->line('   Jika tidak ada pesan, kemungkinan Switch node tidak match.');
            } else {
                $this->error('❌ Debug request gagal: ' . $response->status());
                $this->error('📝 Response: ' . $response->body());
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Connection error: ' . $e->getMessage());
        }
        
        $this->line('');
        $this->warn('💡 Available event_types untuk testing:');
        $this->line('   - booking_created (default)');
        $this->line('   - booking_status_updated');
        $this->line('   - test_notification');
        $this->line('');
        $this->line('Contoh: php artisan test:n8n-switch booking_status_updated');
        
        return 0;
    }
}
