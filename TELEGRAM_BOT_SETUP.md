# Setup Telegram Bot dengan Tombol Setujui/Tolak

## Langkah-langkah Setup

### 1. Buat Telegram Bot

1. **Buka @BotFather** di Telegram
2. Kirim perintah `/newbot`
3. Berikan nama untuk bot (contoh: "Lab Booking Bot")
4. Berikan username untuk bot (contoh: "lab_booking_bot")
5. **Simpan token** yang diberikan BotFather

### 2. Setup Grup Admin Telegram

1. **Buat grup Telegram** untuk admin
2. **Tambahkan bot** ke dalam grup
3. **Berikan admin privileges** ke bot
4. **Dapatkan Chat ID grup**:
   ```bash
   # Kirim pesan di grup, lalu akses URL ini:
   https://api.telegram.org/bot<BOT_TOKEN>/getUpdates
   
   # Cari "chat":{"id":-1234567890} di response JSON
   # Chat ID grup selalu negatif
   ```

### 3. Konfigurasi Environment Variables

Tambahkan ke file `.env`:
```bash
# Token bot dari BotFather
TELEGRAM_BOT_TOKEN=123456789:ABCDefGHIjklMNOpqrsTUVwxyz

# Generate random string untuk keamanan webhook
TELEGRAM_WEBHOOK_TOKEN=your_secure_random_token_here

# URL aplikasi Laravel untuk callback n8n
APP_URL=https://your-laravel-app.com

# URL webhook n8n
N8N_WEBHOOK_URL=https://your-n8n-instance.com/webhook/bookinglab
```

### 4. Setup n8n Workflow

#### a. Import Workflow
1. Buka n8n
2. Import file `Notif Pinjam Lab.json` yang sudah dimodifikasi
3. Set environment variables di n8n:
   - `LARAVEL_APP_URL`
   - `TELEGRAM_WEBHOOK_TOKEN`

#### b. Konfigurasi Node Telegram
1. **Node "Send Booking Request"**:
   - Set Chat ID grup admin
   - Pastikan credential Telegram Bot tersedia
   
2. **Node "Send Status Update"**:
   - Set Chat ID yang sama
   - Gunakan credential yang sama

#### c. Setup Webhook URLs
1. **Webhook "Telegram Callback"** akan mendapat URL:
   ```
   https://your-n8n-instance.com/webhook/telegram-callback
   ```
   
2. **Set URL ini** di konfigurasi Telegram bot webhook:
   ```bash
   curl -X POST "https://api.telegram.org/bot<BOT_TOKEN>/setWebhook" \
        -H "Content-Type: application/json" \
        -d '{"url": "https://your-n8n-instance.com/webhook/telegram-callback"}'
   ```

### 5. Testing Setup

#### a. Test Basic Configuration
```bash
# Test konfigurasi
php artisan test:telegram-integration --booking-id=1

# Atau buat booking dummy untuk test
php artisan test:telegram-integration
```

#### b. Test Manual Flow
1. **Login sebagai guru** dan ajukan peminjaman
2. **Cek grup Telegram admin** - harusnya ada pesan dengan 2 tombol
3. **Klik tombol** Setujui atau Tolak
4. **Pesan harusnya update** dengan status baru
5. **Cek dashboard admin** - status harusnya berubah

### 6. Troubleshooting

#### Tombol Tidak Muncul
```bash
# Cek log Laravel
tail -f storage/logs/laravel.log

# Cek apakah event_type terkirim dengan benar
# Harusnya ada: "event_type": "booking_created"
```

#### Callback Tidak Bekerja
```bash
# Test webhook Telegram bot
curl -X POST "https://api.telegram.org/bot<BOT_TOKEN>/getWebhookInfo"

# Pastikan webhook URL terset ke n8n
```

#### API Endpoint Error
```bash
# Test API endpoint langsung
curl -X POST "https://your-laravel-app.com/api/telegram/booking-action" \
     -H "Content-Type: application/json" \
     -H "X-Telegram-Token: your_webhook_token" \
     -d '{
       "action": "approve",
       "booking_id": 1,
       "callback_query_id": "test123",
       "user_id": "123456789",
       "chat_id": "-1234567890",
       "message_id": "100"
     }'
```

### 7. Keamanan

#### Token Security
- **Jangan commit** token ke git
- **Gunakan HTTPS** untuk semua webhook
- **Generate strong** webhook token untuk validasi

#### Webhook Validation
- Laravel memvalidasi `X-Telegram-Token` header
- Hanya request dari n8n dengan token valid yang diproses

#### Bot Permissions
- Bot hanya perlu permission **send messages** dan **edit messages**
- Tidak perlu admin penuh di grup

### 8. Environment Variables Reference

```bash
# Wajib ada
TELEGRAM_BOT_TOKEN=         # Dari @BotFather
TELEGRAM_WEBHOOK_TOKEN=     # Random string untuk keamanan
N8N_WEBHOOK_URL=           # URL webhook n8n
APP_URL=                   # URL Laravel app

# Optional (sudah ada default)
DB_CONNECTION=sqlite
LOG_CHANNEL=stack
```

### 9. Monitoring dan Maintenance

#### Log Monitoring
```bash
# Monitor log real-time
tail -f storage/logs/laravel.log | grep -i telegram

# Cek error khusus Telegram
grep "Telegram" storage/logs/laravel.log
```

#### Health Check
```bash
# Cek status bot
curl "https://api.telegram.org/bot<BOT_TOKEN>/getMe"

# Cek webhook info
curl "https://api.telegram.org/bot<BOT_TOKEN>/getWebhookInfo"
```

## Diagram Alur Lengkap

```
1. Guru submit booking
   ↓
2. Laravel kirim ke n8n (event_type: booking_created)  
   ↓
3. n8n cek event_type → kirim ke Telegram dengan tombol
   ↓  
4. Admin di Telegram klik tombol
   ↓
5. Telegram kirim callback ke n8n
   ↓
6. n8n parse callback → kirim ke Laravel API
   ↓
7. Laravel update database + update pesan Telegram
   ↓
8. Laravel kirim status notification ke n8n
   ↓ 
9. n8n kirim pesan status update ke Telegram
```

## Selamat! 🎉

Telegram bot dengan tombol interaktif telah siap digunakan. Admin sekarang bisa menyetujui/menolak pengajuan peminjaman lab langsung dari Telegram tanpa perlu membuka dashboard web.
