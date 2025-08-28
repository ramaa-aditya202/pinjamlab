# 🔧 Troubleshooting Guide - N8N Telegram Integration

## ❌ Error 419: Page Expired (CSRF Token Mismatch)

### Gejala:
```
❌ Gagal mengirim test notification: 419
Response: <!DOCTYPE html>...Page Expired...
```

### Penyebab:
Laravel melindungi semua POST requests dengan CSRF token validation, termasuk webhook endpoints.

### Solusi:

#### 1. Verifikasi Konfigurasi CSRF Exception
Pastikan file `bootstrap/app.php` berisi:
```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->validateCsrfTokens(except: [
        'webhook/n8n/booking-notification',
    ]);
})
```

#### 2. Clear Cache
```bash
php artisan config:clear
php artisan route:clear  
php artisan cache:clear
```

#### 3. Restart Server
```bash
# Stop current server (Ctrl+C)
php artisan serve
```

#### 4. Test Alternatif
```bash
# Test langsung ke N8N (bypass Laravel)
php artisan test:webhook-direct

# Test dengan curl manual
curl -X POST http://localhost:8000/webhook/n8n/booking-notification \
-H "Content-Type: application/json" \
-d '{"event_type":"test","message":"test"}'
```

---

## 🔌 Connection Refused / Timeout

### Gejala:
```
❌ Connection error: cURL error 7: Failed to connect
❌ Connection error: Operation timed out
```

### Penyebab:
N8N instance tidak dapat diakses dari Laravel server.

### Solusi:

#### 1. Verify N8N URL
```bash
# Test manual access
curl -X POST https://your-n8n-instance.com/webhook/booking-notification \
-H "Content-Type: application/json" \
-d '{"test": "data"}'
```

#### 2. Check Network Connectivity
```bash
# Test ping
ping your-n8n-domain.com

# Test port
telnet your-n8n-domain.com 443

# Test DNS resolution  
nslookup your-n8n-domain.com
```

#### 3. Firewall/Proxy Issues
- Check server firewall rules
- Verify proxy settings if behind corporate network
- Check SSL certificates for HTTPS endpoints

#### 4. Local N8N Testing
```bash
# If running N8N locally
N8N_WEBHOOK_URL=http://localhost:5678/webhook/booking-notification
```

---

## 🤖 N8N Workflow Issues

### Gejala:
Laravel request sukses, tapi tidak ada pesan Telegram.

### Debug Steps:

#### 1. Check N8N Execution Log
1. Login ke N8N dashboard
2. Go to **Executions** menu
3. Look for recent executions
4. Check for errors in workflow steps

#### 2. Test Workflow Manual
1. Open workflow in N8N
2. Click **Execute Workflow**
3. Choose **Via Webhook**  
4. Send test payload
5. Verify each node execution

#### 3. Common N8N Issues:
- **Webhook not activated**: Ensure workflow is **Active**
- **Wrong webhook path**: Should be `/booking-notification`
- **Telegram credentials invalid**: Test bot token
- **Chat ID wrong**: Verify with `/getUpdates` API

---

## 📱 Telegram Bot Issues

### Gejala:
N8N receives webhook, tapi bot tidak kirim pesan.

### Debug Steps:

#### 1. Verify Bot Token
```bash
curl "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getMe"
```
Expected response:
```json
{"ok":true,"result":{"id":123456789,"is_bot":true,"first_name":"YourBot"}}
```

#### 2. Verify Chat ID
```bash  
curl "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/getUpdates"
```
Look for `chat.id` in recent messages.

#### 3. Test Direct Send
```bash
curl -X POST "https://api.telegram.org/bot<YOUR_BOT_TOKEN>/sendMessage" \
-H "Content-Type: application/json" \
-d '{
  "chat_id": "<CHAT_ID>",
  "text": "Direct test message",
  "parse_mode": "Markdown"
}'
```

#### 4. Bot Permissions
- **Private chat**: User must have started chat with bot (`/start`)
- **Group chat**: Bot must be added as member with message permissions
- **Channel**: Bot must be added as admin with post permissions

---

## 🔍 General Debugging

### Laravel Logs
```bash
tail -f storage/logs/laravel.log | grep -i "booking\|webhook\|notification"
```

### N8N Logs
Check N8N server logs or execution history in dashboard.

### Test Commands
```bash
# Test full flow
php artisan test:booking-notification

# Test direct N8N connection  
php artisan test:webhook-direct

# Check Laravel routes
php artisan route:list | grep webhook

# Check Laravel config
php artisan tinker
>>> config('services.n8n.webhook_url')
```

### Environment Check
```bash
# Verify .env file
cat .env | grep N8N

# Test environment loading
php artisan tinker
>>> env('N8N_WEBHOOK_URL')
```

---

## ✅ Success Indicators

### Laravel Side:
- ✅ Route accessible: `POST /webhook/n8n/booking-notification`
- ✅ No CSRF errors (419)
- ✅ Config loaded: `config('services.n8n.webhook_url')`
- ✅ HTTP client works: Response 200 from N8N

### N8N Side:
- ✅ Workflow is **Active**
- ✅ Webhook receives data
- ✅ Switch conditions match `event_type`
- ✅ Set nodes format data correctly
- ✅ Telegram node executes without errors

### Telegram Side:
- ✅ Bot responds to `/start`
- ✅ Bot has correct permissions
- ✅ Messages appear in target chat
- ✅ Markdown formatting works

---

## 🆘 Emergency Fallback

Jika semua tidak berhasil, setup notification alternatif:

### Option 1: Email Notifications
```php
// Tambahkan di GuruController
Mail::to('admin@lab.com')->send(new BookingNotification($booking));
```

### Option 2: Database Notifications
```php
// Laravel built-in notifications
$admin = User::where('role', 'admin')->first();
$admin->notify(new BookingCreated($booking));
```

### Option 3: Log-based Monitoring  
```php
// Enhanced logging
Log::channel('telegram')->info('Booking Created', $booking->toArray());
```

Dengan setup monitoring external pada log files.

---

**💡 Tip**: Selalu test secara bertahap - Laravel → N8N → Telegram - untuk isolasi masalah dengan cepat.

---

## 🗄️ Database Constraint Issues

### Gejala:
```
UniqueConstraintViolationException
SQLSTATE[23000]: Integrity constraint violation: 1062 
Duplicate entry 'selasa-1' for key 'lab_bookings_day_hour_unique'
```

### Penyebab:
Database memiliki unique constraint pada kombinasi `day` dan `hour` yang mencegah pengajuan ulang pada slot yang sebelumnya pernah ditolak.

### Solusi:

#### 1. Run Migration (Recommended)
```bash
# Migration akan menghapus unique constraint
php artisan migrate

# Verifikasi migration berhasil
php artisan migrate:status
```

#### 2. Manual Database Fix (Emergency)
```sql
-- Untuk MySQL/MariaDB
ALTER TABLE lab_bookings DROP INDEX lab_bookings_day_hour_unique;

-- Untuk SQLite (drop dan recreate table tanpa constraint)
-- Lebih kompleks, gunakan migration saja
```

#### 3. Cleanup Data Lama (Optional)
```bash
# Lihat data rejected yang mungkin menyebabkan masalah
php artisan cleanup:rejected-bookings --dry-run

# Hapus data rejected (opsional)
php artisan cleanup:rejected-bookings
```

#### 4. Verifikasi Fix
```bash
# Test pengajuan ulang pada slot yang sebelumnya ditolak
# Seharusnya sekarang berhasil tanpa error constraint
```

### Prevention:
- ✅ Unique constraint dihapus dari database
- ✅ Validasi aplikasi diperbaiki hanya cek status `pending` dan `approved`
- ✅ Peminjaman yang `rejected` tidak memblokir pengajuan baru
- ✅ Data integrity tetap terjaga melalui aplikasi logic
