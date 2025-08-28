# Manual Testing Guide

## Test URLs untuk Debug

### 1. Test Internal Webhook (Laravel)
```bash
curl -X POST http://localhost:8000/webhook/n8n/booking-notification \
-H "Content-Type: application/json" \
-d '{
  "event_type": "test_notification",
  "booking_id": 999,
  "user_name": "Test User",
  "user_email": "test@example.com", 
  "day": "Senin",
  "hour": 1,
  "teacher_name": "Test Teacher",
  "class": "Test Class", 
  "subject": "Test Subject",
  "status": "pending",
  "created_at": "2025-08-29 14:30:00",
  "message": "🧪 TEST - Pengajuan Peminjaman Lab\n\nIni adalah test message dari sistem."
}'
```

### 2. Test N8N Webhook Langsung
```bash
curl -X POST https://your-n8n-instance.com/webhook/booking-notification \
-H "Content-Type: application/json" \
-d '{
  "event_type": "test_notification",
  "message": "🧪 Direct test to N8N\n\nTesting webhook connection"
}'
```

### 3. Test Telegram Bot Langsung
```bash
curl -X POST https://api.telegram.org/bot<BOT_TOKEN>/sendMessage \
-H "Content-Type: application/json" \
-d '{
  "chat_id": "<CHAT_ID>",
  "text": "🧪 Direct test to Telegram Bot\n\nTesting bot connection",
  "parse_mode": "Markdown"
}'
```

## Laravel Artisan Commands

### Test Notification
```bash
php artisan test:booking-notification
```

### Test Direct ke N8N (bypass Laravel route)
```bash
php artisan test:webhook-direct
```

### Test N8N Switch Node (debug specific event_type)
```bash
# Test default booking_created
php artisan test:n8n-switch

# Test specific event types
php artisan test:n8n-switch booking_created
php artisan test:n8n-switch booking_status_updated  
php artisan test:n8n-switch test_notification
```

### Alternative Test (jika ada masalah CSRF)
```bash
# Test langsung ke webhook tanpa Laravel route
curl -X POST http://localhost:8000/webhook/n8n/booking-notification \
-H "Content-Type: application/json" \
-d '{
  "event_type": "test_notification",
  "message": "🧪 Test dari curl direct"
}'
```

### Check Routes
```bash
php artisan route:list | grep webhook
```

### Clear Config
```bash
php artisan config:clear
php artisan config:cache
```

### Monitor Logs
```bash
tail -f storage/logs/laravel.log
```

## Debug Checklist

### ✅ Laravel Side
- [ ] Route terdaftar: `/webhook/n8n/booking-notification`
- [ ] WebhookController ada dan berfungsi
- [ ] Config `services.n8n.webhook_url` terisi
- [ ] Environment variable `N8N_WEBHOOK_URL` ada di `.env`
- [ ] HTTP client bisa akses N8N webhook URL
- [ ] No error di Laravel logs

### ✅ N8N Side  
- [ ] Workflow aktif dan running
- [ ] Webhook node configured dengan path: `/booking-notification`
- [ ] Telegram credentials valid
- [ ] Chat ID benar di Set nodes
- [ ] Switch conditions sesuai dengan event_type
- [ ] No execution errors di N8N

### ✅ Telegram Side
- [ ] Bot token valid dan aktif
- [ ] Bot ditambahkan ke target chat/group/channel
- [ ] Bot punya permission untuk send messages
- [ ] Chat ID format benar (positive/negative number)
- [ ] Parse mode "Markdown" atau "HTML" sesuai format pesan

## Common Issues & Solutions

### Issue: Error 419 "Page Expired" (CSRF Token)
**Symptoms:** Test command mengembalikan error 419 dengan HTML response "Page Expired"

**Solution:**
```bash
# 1. Pastikan webhook dikecualikan dari CSRF verification
#    File sudah dikonfigurasi di bootstrap/app.php untuk mengecualikan:
#    'webhook/n8n/booking-notification'

# 2. Clear semua cache
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# 3. Restart Laravel server
php artisan serve

# 4. Test alternatif (direct ke N8N)
php artisan test:webhook-direct
```

**Root Cause:** Laravel secara default melindungi semua POST requests dengan CSRF token, termasuk webhook endpoints.

### Issue: Connection Timeout atau Network Error
**Symptoms:** cURL error, connection refused, timeout

**Solution:**
```bash
# 1. Test koneksi manual
ping your-n8n-domain.com

# 2. Test dengan wget/curl
wget -qO- https://your-n8n-instance.com/webhook/booking-notification

# 3. Check firewall rules
# 4. Verify N8N is running and accessible

# 5. Test dengan local N8N (jika applicable)
N8N_WEBHOOK_URL=http://localhost:5678/webhook/booking-notification
```

### Issue: Webhook 404 Not Found
**Solution:** 
```bash
php artisan route:clear
php artisan route:cache
php artisan serve
```

### Issue: N8N Webhook tidak respond
**Solution:**
1. Check N8N workflow status (Active/Inactive)
2. Verify webhook path sama dengan Laravel config
3. Check N8N logs untuk execution errors

### Issue: Telegram bot tidak kirim pesan
**Solution:**
1. Verify bot token dengan: `https://api.telegram.org/bot<TOKEN>/getMe`
2. Check bot permissions di group/channel
3. Verify chat ID dengan: `https://api.telegram.org/bot<TOKEN>/getUpdates`

### Issue: UniqueConstraintViolationException - Duplicate entry for day-hour
**Symptoms:** Error saat mengajukan peminjaman pada slot yang sebelumnya pernah ditolak

**Error Message:**
```
SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry 'selasa-1' for key 'lab_bookings_day_hour_unique'
```

**Root Cause:** 
Database memiliki unique constraint pada kombinasi `day` dan `hour` yang mencegah pengajuan ulang pada slot yang sama, meskipun peminjaman sebelumnya sudah ditolak.

**Solution:**
```bash
# 1. Run migration untuk menghapus unique constraint
php artisan migrate

# 2. Atau manual fix di database (MySQL/MariaDB)
# ALTER TABLE lab_bookings DROP INDEX lab_bookings_day_hour_unique;

# 3. Validasi sekarang hanya mengecek status 'pending' dan 'approved'
#    Peminjaman yang 'rejected' tidak akan memblokir pengajuan baru
```

**Prevention:** 
Sistem sekarang sudah diperbaiki untuk:
- Menghapus unique constraint pada level database
- Memperbaiki validasi aplikasi untuk hanya mengecek status `pending` dan `approved`
- Memungkinkan pengajuan ulang pada slot yang sebelumnya ditolak

### Issue: N8N Switch Event Type tidak melanjutkan ke node berikutnya
**Symptoms:** N8N menerima webhook dan mencapai Switch node, tapi execution berhenti di sana

**Root Cause:** 
Kondisi di Switch node tidak match dengan `event_type` yang dikirim dari Laravel.

**Debug Steps:**
1. **Check nilai event_type**: Pastikan Laravel mengirim `event_type` yang benar
2. **Verify Switch conditions**: Pastikan kondisi di Switch node sesuai

**Solution:**

#### 1. Fix Switch Node Conditions di N8N
Buka Switch node dan pastikan conditions seperti ini:

**Output 0 (New Booking):**
- Condition: `{{ $json.event_type }}` equals `booking_created`

**Output 1 (Status Update):**  
- Condition: `{{ $json.event_type }}` equals `booking_status_updated`

**Output 2 (Test):**
- Condition: `{{ $json.event_type }}` equals `test_notification`

#### 2. Alternative: Use Contains Instead of Equals
Jika masih tidak work, coba gunakan "contains" instead of "equals":
- `{{ $json.event_type }}` contains `booking_created`

#### 3. Debug dengan Expression di N8N
Tambahkan node "Set" sebelum Switch untuk debug:
```javascript
// Di Set node, tambahkan:
{
  "debug_event_type": "{{ $json.event_type }}",
  "debug_full_data": "{{ JSON.stringify($json) }}"
}
```

#### 4. Manual Test di N8N
1. Buka workflow di N8N
2. Klik **Execute Workflow** 
3. Pilih **Via Webhook**
4. Paste data ini untuk test:
```json
{
  "event_type": "booking_created",
  "message": "Test message",
  "booking_id": 123
}
```

### Issue: Format pesan rusak
**Solution:**
1. Test dengan plain text dulu (hapus parse_mode)
2. Check escape characters di Markdown
3. Verify message length (max 4096 characters)

## Sample Responses

### Laravel Webhook Success Response:
```json
{
  "success": true
}
```

### Laravel Webhook Error Response:
```json
{
  "error": "Webhook URL not configured"
}
```

### N8N Success Response:
```json
{
  "success": true,
  "message": "Notification sent successfully"  
}
```

### Telegram Success Response:
```json
{
  "ok": true,
  "result": {
    "message_id": 123,
    "date": 1693234567,
    "text": "Your message here"
  }
}
```
