<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestWebhookDirect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:webhook-direct';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test direct webhook to N8N (bypass Laravel route)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $webhookUrl = config('services.n8n.webhook_url');
        
        if (!$webhookUrl) {
            $this->error('❌ N8N_WEBHOOK_URL tidak dikonfigurasi di .env');
            return 1;
        }

        $this->info('🔗 Testing direct connection to: ' . $webhookUrl);

        $testData = [
            'event_type' => 'direct_test',
            'message' => "🧪 *DIRECT TEST ke N8N*\n\n" .
                        "Testing koneksi langsung dari Laravel ke N8N\n" .
                        "Timestamp: " . now()->format('d/m/Y H:i:s'),
            'source' => 'laravel_direct_test'
        ];

        try {
            $this->info('🚀 Mengirim direct request ke N8N...');
            
            $response = Http::timeout(10)->post($webhookUrl, $testData);
            
            if ($response->successful()) {
                $this->info('✅ Direct test berhasil!');
                $this->line('📊 Status: ' . $response->status());
                $this->line('📝 Response: ' . $response->body());
            } else {
                $this->error('❌ Direct test gagal: ' . $response->status());
                $this->error('📝 Response: ' . $response->body());
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Connection error: ' . $e->getMessage());
            $this->line('');
            $this->warn('💡 Kemungkinan masalah:');
            $this->line('   1. N8N tidak running atau tidak dapat diakses');
            $this->line('   2. Webhook URL salah');
            $this->line('   3. Network firewall/proxy issue');
        }
        
        return 0;
    }
}
