# Integrasi N8N dengan Telegram Bot untuk Notifikasi Peminjaman Lab

## Overview
Sistem ini terintegrasi dengan n8n untuk mengirim notifikasi ke Telegram Bot ketika:
1. Ada pengajuan peminjaman lab baru
2. Admin menyetujui peminjaman
3. Admin menolak peminjaman

## Setup Telegram Bot

### 1. Buat Bot di Telegram
1. Chat dengan [@BotFather](https://t.me/BotFather) di Telegram
2. Ketik `/newbot`
3. Berikan nama bot (contoh: "Lab Booking Bot")
4. Berikan username bot (contoh: "lab_booking_bot")
5. Simpan token yang diberikan

### 2. Setup Chat/Channel Target
- **Untuk grup**: Tambahkan bot ke grup dan berikan admin permission
- **Untuk channel**: Tambahkan bot sebagai admin di channel
- **Untuk personal chat**: Kirim pesan `/start` ke bot

### 3. Dapatkan Chat ID
Jalankan URL ini di browser (ganti `YOUR_BOT_TOKEN` dengan token bot):
```
https://api.telegram.org/botYOUR_BOT_TOKEN/getUpdates
```

Atau kirim pesan ke bot dan cek response untuk mendapatkan `chat.id`.

## Setup N8N Workflow

### 1. Buat Workflow Baru di N8N
Buat workflow dengan node-node berikut:

### 2. Webhook Node (Trigger)
```json
{
  "httpMethod": "POST",
  "path": "/booking-notification",
  "responseMode": "responseNode",
  "options": {}
}
```

### 3. Switch Node (Conditional Logic)
Tambahkan kondisi berdasarkan `event_type`:
```json
{
  "rules": {
    "rules": [
      {
        "conditions": [
          {
            "leftValue": "{{ $json.event_type }}",
            "rightValue": "booking_created",
            "operator": {
              "type": "string",
              "operation": "equals"
            }
          }
        ],
        "output": 0
      },
      {
        "conditions": [
          {
            "leftValue": "{{ $json.event_type }}",
            "rightValue": "booking_status_updated",
            "operator": {
              "type": "string",
              "operation": "equals"
            }
          }
        ],
        "output": 1
      }
    ]
  }
}
```

### 4. Set Node (Data Formatting)
Untuk setiap branch dari Switch node, tambahkan Set node:

**Untuk booking_created:**
```json
{
  "values": {
    "telegram_message": "{{ $json.message }}",
    "chat_id": "YOUR_CHAT_ID_HERE",
    "parse_mode": "Markdown"
  }
}
```

**Untuk booking_status_updated:**
```json
{
  "values": {
    "telegram_message": "{{ $json.message }}",
    "chat_id": "YOUR_CHAT_ID_HERE", 
    "parse_mode": "Markdown"
  }
}
```

### 5. Telegram Bot Node
```json
{
  "chatId": "{{ $json.chat_id }}",
  "text": "{{ $json.telegram_message }}",
  "parseMode": "Markdown",
  "credentials": "telegramApi"
}
```

### 6. Response Node
```json
{
  "statusCode": 200,
  "body": {
    "success": true,
    "message": "Notification sent successfully"
  }
}
```

## Konfigurasi Laravel

### 1. Environment Variables
Tambahkan di file `.env`:
```env
N8N_WEBHOOK_URL=https://your-n8n-instance.com/webhook/booking-notification
```

### 2. Testing
Jalankan command untuk test notifikasi:
```bash
php artisan test:booking-notification
```

## Format Pesan Notifikasi

### Pengajuan Baru
```
📝 *Pengajuan Peminjaman Lab Baru*

👤 *Pengaju:* Nama User
📧 *Email:* user@example.com
📅 *Hari:* Senin
🕐 *Jam:* Jam ke-1
👨‍🏫 *Guru:* Nama Guru
🏫 *Kelas:* X IPA 1
📚 *Mata Pelajaran:* Matematika
⏰ *Waktu Pengajuan:* 29/08/2025 14:30

Silakan cek dashboard admin untuk menyetujui atau menolak pengajuan ini.
```

### Peminjaman Disetujui
```
✅ *Peminjaman Lab DISETUJUI*

👤 *Pengaju:* Nama User
📧 *Email:* user@example.com
📅 *Hari:* Senin
🕐 *Jam:* Jam ke-1
👨‍🏫 *Guru:* Nama Guru
🏫 *Kelas:* X IPA 1
📚 *Mata Pelajaran:* Matematika
⏰ *Diproses pada:* 29/08/2025 15:00
```

### Peminjaman Ditolak
```
❌ *Peminjaman Lab DITOLAK*

👤 *Pengaju:* Nama User
📧 *Email:* user@example.com
📅 *Hari:* Senin
🕐 *Jam:* Jam ke-1
👨‍🏫 *Guru:* Nama Guru
🏫 *Kelas:* X IPA 1
📚 *Mata Pelajaran:* Matematika
📝 *Alasan:* Bentrok dengan kegiatan lain
⏰ *Diproses pada:* 29/08/2025 15:00
```

## Data Structure

Sistem akan mengirim data dengan struktur JSON berikut:

### Untuk Pengajuan Baru (`booking_created`)
```json
{
  "event_type": "booking_created",
  "booking_id": 123,
  "user_name": "John Doe",
  "user_email": "john@example.com",
  "day": "Senin",
  "hour": 1,
  "teacher_name": "Guru Example",
  "class": "X IPA 1",
  "subject": "Matematika",
  "status": "pending",
  "created_at": "2025-08-29 14:30:00",
  "message": "📝 *Pengajuan Peminjaman Lab Baru*\n\n..."
}
```

### Untuk Update Status (`booking_status_updated`)
```json
{
  "event_type": "booking_status_updated",
  "booking_id": 123,
  "status": "approved",
  "user_name": "John Doe",
  "user_email": "john@example.com",
  "day": "Senin",
  "hour": 1,
  "teacher_name": "Guru Example",
  "class": "X IPA 1",
  "subject": "Matematika",
  "notes": "Optional rejection reason",
  "processed_at": "2025-08-29 15:00:00",
  "message": "✅ *Peminjaman Lab DISETUJUI*\n\n..."
}
```

## Troubleshooting

### 1. Webhook tidak berfungsi
- Pastikan N8N_WEBHOOK_URL sudah benar di `.env`
- Cek log Laravel: `tail -f storage/logs/laravel.log`
- Test dengan: `php artisan test:booking-notification`

### 2. Bot tidak mengirim pesan
- Pastikan bot token benar di n8n credentials
- Pastikan chat_id benar
- Pastikan bot memiliki permission untuk mengirim pesan

### 3. Format pesan tidak sesuai
- Periksa parse_mode di Telegram node (gunakan "Markdown")
- Pastikan format Markdown valid dalam pesan

## Security Notes

1. **Webhook endpoint** tidak memerlukan autentikasi untuk kemudahan integrasi dengan n8n
2. **Rate limiting** dihandle oleh timeout 5 detik
3. **Error handling** tidak akan mengganggu proses utama aplikasi
4. **Logging** semua notifikasi tercatat di Laravel log

## Monitoring

Monitor notifikasi melalui:
1. Laravel logs: `storage/logs/laravel.log`
2. N8N execution logs
3. Telegram Bot API logs
4. Database: Cek status peminjaman di tabel `lab_bookings`
