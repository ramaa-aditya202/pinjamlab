# 🚀 Panduan Instalasi Integrasi N8N dengan Telegram Bot

## 📋 Prerequisites
- Laravel aplikasi sudah berjalan
- N8N instance (bisa self-hosted atau cloud)
- Telegram Bot token
- Chat/Group/Channel ID untuk menerima notifikasi

## 🔧 Step 1: Setup Environment

### 1.1 Update .env file
Tambahkan konfigurasi berikut di file `.env`:

```env
# N8N Configuration for Telegram Notifications
N8N_WEBHOOK_URL=https://your-n8n-instance.com/webhook/booking-notification
```

**Contoh:**
```env
N8N_WEBHOOK_URL=https://n8n.yourdomain.com/webhook/booking-notification
```

### 1.2 Clear config cache
```bash
php artisan config:clear
php artisan config:cache
```

## 🤖 Step 2: Setup Telegram Bot

### 2.1 Buat Bot Baru
1. Buka Telegram dan cari `@BotFather`
2. Kirim perintah `/newbot`
3. Berikan nama bot: `Lab Booking Notifier`
4. Berikan username: `lab_booking_notifier_bot` (harus unik)
5. **Simpan token** yang diberikan (contoh: `123456789:AABBCCddEEff...`)

### 2.2 Setup Target Chat
Pilih salah satu opsi:

**Opsi A: Personal Chat**
1. Mulai chat dengan bot Anda
2. Kirim `/start`

**Opsi B: Group Chat**
1. Buat group baru atau gunakan yang sudah ada
2. Tambahkan bot ke group
3. Beri bot admin permission

**Opsi C: Channel**
1. Buat channel atau gunakan yang sudah ada
2. Tambahkan bot sebagai admin
3. Berikan permission untuk post messages

### 2.3 Dapatkan Chat ID
1. Kirim pesan ke bot/group/channel
2. Buka URL ini di browser (ganti `YOUR_BOT_TOKEN`):
   ```
   https://api.telegram.org/botYOUR_BOT_TOKEN/getUpdates
   ```
3. Cari `chat.id` di response JSON
4. **Simpan Chat ID** (bisa berupa angka positif/negatif)

**Contoh response:**
```json
{
  "result": [
    {
      "message": {
        "chat": {
          "id": -1001234567890,  // <-- Ini Chat ID nya
          "title": "Lab Notifications"
        }
      }
    }
  ]
}
```

## 🔌 Step 3: Setup N8N Workflow

### 3.1 Import Workflow
1. Login ke N8N instance Anda
2. Buat workflow baru
3. Klik **Import** dan upload file `n8n-lab-booking-workflow.json`

### 3.2 Konfigurasi Credentials
1. Klik node **Telegram** manapun
2. Buat credentials baru:
   - **Name**: `Lab Booking Bot`
   - **Access Token**: Token bot dari BotFather
3. Test connection untuk memastikan berhasil

### 3.3 Update Chat ID
1. Klik setiap node **Set** (Set New Booking Data, Set Status Update Data, Set Test Notification Data)
2. Ubah value `YOUR_TELEGRAM_CHAT_ID` dengan Chat ID yang sudah didapat
3. **Save** workflow

### 3.4 Activate Webhook
1. Klik node **Webhook Trigger**
2. Copy **Production URL** yang muncul
3. Gunakan URL ini untuk konfigurasi Laravel

**Contoh webhook URL:**
```
https://n8n.yourdomain.com/webhook/booking-notification
```

## 🔄 Step 4: Update Laravel Configuration

### 4.1 Update .env dengan webhook URL yang benar
```env
N8N_WEBHOOK_URL=https://n8n.yourdomain.com/webhook/booking-notification
```

### 4.2 Clear cache
```bash
php artisan config:clear
php artisan config:cache
```

## 🧪 Step 5: Testing

### 5.1 Test dari Laravel
```bash
php artisan test:booking-notification
```

Expected output:
```
🚀 Mengirim test notification ke n8n...
✅ Test notification berhasil dikirim!
```

### 5.2 Test Webhook di N8N
1. Buka workflow di N8N
2. Klik **Execute Workflow** 
3. Pilih **Via Webhook**
4. Kirim test payload

### 5.3 Verifikasi di Telegram
Periksa chat/group/channel target, seharusnya menerima pesan test.

## 🎯 Step 6: Production Testing

### 6.1 Test dengan Pengajuan Real
1. Login sebagai guru di aplikasi
2. Ajukan peminjaman lab baru
3. Cek apakah notifikasi masuk ke Telegram

### 6.2 Test Approval/Rejection
1. Login sebagai admin
2. Approve/reject pengajuan
3. Cek notifikasi status update

## 🔍 Troubleshooting

### Problem 1: Webhook tidak terpanggil
**Symptoms:** Test command sukses tapi tidak ada pesan di Telegram

**Solutions:**
1. Periksa N8N webhook URL di `.env`
2. Pastikan workflow di N8N aktif
3. Cek N8N execution logs
4. Verify webhook path: `/webhook/booking-notification`

### Problem 2: Bot tidak mengirim pesan
**Symptoms:** N8N menerima webhook tapi bot tidak kirim pesan

**Solutions:**
1. Periksa bot token di N8N credentials
2. Verifikasi Chat ID benar
3. Pastikan bot punya permission di group/channel
4. Test manual di N8N node

### Problem 3: Format pesan rusak
**Symptoms:** Pesan terkirim tapi format tidak sesuai

**Solutions:**
1. Pastikan Parse Mode = `Markdown`
2. Periksa escape characters di pesan
3. Test dengan plain text dulu

### Problem 4: Laravel error
**Symptoms:** Error 500 saat pengajuan peminjaman

**Solutions:**
1. Cek Laravel logs: `tail -f storage/logs/laravel.log`
2. Pastikan route webhook terdaftar: `php artisan route:list | grep webhook`
3. Verify HTTP client timeout setting

## 📊 Monitoring

### Laravel Logs
```bash
tail -f storage/logs/laravel.log | grep "booking notification"
```

### N8N Execution History
1. Buka N8N dashboard
2. Klik **Executions** 
3. Monitor success/failed executions

### Telegram Bot Logs
Monitor via Telegram API:
```
https://api.telegram.org/botYOUR_BOT_TOKEN/getUpdates
```

## 🔐 Security Best Practices

### 1. Environment Protection
```env
# Jangan commit token ke Git
TELEGRAM_BOT_TOKEN=123456789:AABBCCddEEff...
N8N_WEBHOOK_URL=https://...
```

### 2. Webhook Security
- Gunakan HTTPS untuk webhook URL
- Consider adding webhook signature validation
- Rate limiting pada webhook endpoint

### 3. Bot Permissions
- Minimal permissions untuk bot
- Restrict bot access ke channel/group tertentu

## 📈 Advanced Configuration

### Custom Message Templates
Edit di `GuruController::sendBookingNotification()` dan `AdminController::sendStatusNotification()`

### Multiple Notification Channels
Duplicate workflow untuk kirim ke multiple groups/channels

### Conditional Notifications
Tambah logic di N8N untuk filter notifications berdasarkan criteria tertentu

## 🎉 Success Indicators

✅ **Test command berhasil**
✅ **Webhook terpanggil di N8N** 
✅ **Pesan masuk ke Telegram**
✅ **Real pengajuan trigger notifikasi**
✅ **Approval/rejection trigger notifikasi**

---

## 📞 Support

Jika ada masalah, periksa:
1. Laravel logs: `storage/logs/laravel.log`
2. N8N execution logs
3. Webhook URL accessibility
4. Bot credentials dan permissions

**Selamat! Sistem notifikasi Telegram sudah terintegrasi dengan aplikasi peminjaman lab.** 🎊
