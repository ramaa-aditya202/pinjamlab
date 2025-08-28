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
