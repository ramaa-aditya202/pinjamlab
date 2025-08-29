<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class DemoTelegramBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:telegram-bot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Demo Telegram bot dengan mengirim pesan test langsung';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🤖 Demo Telegram Bot - Mengirim Pesan Test...');
        $this->newLine();

        $botToken = config('services.telegram.bot_token');
        if (!$botToken) {
            $this->error('❌ TELEGRAM_BOT_TOKEN belum dikonfigurasi');
            $this->line('   Tambahkan ke .env: TELEGRAM_BOT_TOKEN=your_bot_token');
            return 1;
        }

        // Chat ID yang akan dituju (bisa diganti sesuai kebutuhan)
        $chatId = $this->ask('Masukkan Chat ID tujuan (contoh: -4846058783 untuk grup)', '-4846058783');

        $this->info("📤 Mengirim pesan test ke Chat ID: {$chatId}");

        // Test 1: Pesan biasa
        $this->testBasicMessage($botToken, $chatId);

        // Test 2: Pesan dengan tombol
        $this->testMessageWithButtons($botToken, $chatId);

        return 0;
    }

    /**
     * Test pesan biasa
     */
    private function testBasicMessage($botToken, $chatId)
    {
        try {
            $message = "🧪 **TEST MESSAGE**\n\n" .
                      "Ini adalah pesan test dari Laravel untuk memastikan bot Telegram berfungsi.\n\n" .
                      "✅ Bot token: Valid\n" .
                      "✅ Chat ID: {$chatId}\n" .
                      "✅ Connection: OK\n\n" .
                      "⏰ Waktu: " . now()->format('Y-m-d H:i:s');

            $response = Http::timeout(10)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown'
            ]);

            if ($response->successful()) {
                $this->info('✅ Pesan test berhasil dikirim');
            } else {
                $this->error('❌ Gagal mengirim pesan test: ' . $response->body());
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }

    /**
     * Test pesan dengan tombol inline keyboard
     */
    private function testMessageWithButtons($botToken, $chatId)
    {
        try {
            $this->info('📤 Mengirim pesan dengan tombol...');

            $message = "🧪 **TEST MESSAGE DENGAN TOMBOL**\n\n" .
                      "📝 SIMULASI PENGAJUAN PEMINJAMAN LAB\n\n" .
                      "👤 Pengaju: Guru Test\n" .
                      "📧 Email: test@example.com\n" .
                      "📅 Hari: Senin\n" .
                      "🕐 Jam: Jam ke-1\n" .
                      "👨‍🏫 Guru: Ahmad Test\n" .
                      "🏫 Kelas: XII IPA 1\n" .
                      "📚 Mata Pelajaran: Demo Testing\n" .
                      "⏰ Waktu Pengajuan: " . now()->format('d/m/Y H:i') . "\n\n" .
                      "Silakan pilih tindakan:";

            $inlineKeyboard = [
                [
                    [
                        'text' => '✅ Setujui',
                        'callback_data' => 'approve_demo_123'
                    ],
                    [
                        'text' => '❌ Tolak', 
                        'callback_data' => 'reject_demo_123'
                    ]
                ]
            ];

            $response = Http::timeout(10)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
                'reply_markup' => json_encode([
                    'inline_keyboard' => $inlineKeyboard
                ])
            ]);

            if ($response->successful()) {
                $this->info('✅ Pesan dengan tombol berhasil dikirim');
                $this->newLine();
                $this->line('💡 Tips:');
                $this->line('   - Tombol akan muncul di bawah pesan');
                $this->line('   - Klik tombol untuk test callback');
                $this->line('   - Pastikan webhook n8n sudah dikonfigurasi untuk menangani callback');
                $this->newLine();
                $this->warn('⚠️  Callback tidak akan diproses karena ini hanya demo');
                $this->line('   Gunakan `php artisan test:telegram-integration` untuk test lengkap');
            } else {
                $this->error('❌ Gagal mengirim pesan dengan tombol: ' . $response->body());
            }

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
        }
    }
}
