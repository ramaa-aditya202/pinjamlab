# Panduan Sederhana N8N - Tanpa Switch Node

Karena Switch node sering mengalami masalah saat import JSON, ini adalah workflow sederhana yang mengirim semua notifikasi ke Telegram tanpa pemisahan berdasarkan event_type.

## 1. Import Workflow Sederhana

1. Buka N8N
2. Klik "Import from File" 
3. Upload file `n8n-simple-workflow.json`
4. Workflow akan terdiri dari:
   - **Webhook** → **Set** → **Telegram** → **Respond**

## 2. Konfigurasi Manual

### Webhook Node
- Sudah dikonfigurasi untuk menerima POST di path `booking-notification`
- Tidak perlu diubah

### Set Node
- Mengatur variabel:
  - `telegram_message`: Menggunakan pesan dari Laravel
  - `chat_id`: **GANTI dengan Chat ID Telegram Anda**
  - `event_debug`: Untuk debugging, menampilkan event_type

**⚠️ PENTING: Ganti `YOUR_CHAT_ID_HERE` dengan Chat ID Telegram Anda**

### Telegram Node
- **Credentials**: Pilih/buat credentials Telegram Bot Anda
- Chat ID sudah diambil dari Set node
- Parse Mode: Markdown (untuk format pesan)
- Pesan akan menampilkan notifikasi + debug info

### Respond Node
- Mengembalikan response JSON ke Laravel
- Status 200 dengan konfirmasi event_type

## 3. Testing

1. **Aktifkan workflow** di N8N
2. **Copy Production URL** dari Webhook node
3. **Update Laravel config** di `app/Http/Controllers/WebhookController.php`:
   ```php
   private function sendToN8N($data)
   {
       $response = Http::timeout(30)->post('URL_N8N_WEBHOOK_ANDA', $data);
       // ...
   }
   ```
4. **Test dengan artisan command**:
   ```
   php artisan test:n8n-webhook
   ```

## 4. Verifikasi Hasil

✅ **Laravel**: Lihat log untuk konfirmasi webhook terkirim  
✅ **N8N**: Periksa execution history untuk debugging  
✅ **Telegram**: Harus menerima pesan notifikasi  

## 5. Upgrade ke Switch Node (Opsional)

Setelah workflow sederhana berfungsi, Anda bisa menambahkan Switch node secara manual:

1. Tambah Switch node antara Webhook dan Set
2. Konfigurasi conditions:
   - **booking_submitted**: `{{ $json.event_type === "booking_submitted" }}`
   - **booking_approved**: `{{ $json.event_type === "booking_approved" }}`  
   - **booking_rejected**: `{{ $json.event_type === "booking_rejected" }}`
3. Tambah Set node terpisah untuk setiap condition
4. Hubungkan ke Telegram nodes yang berbeda

Tapi untuk sekarang, workflow sederhana ini sudah cukup untuk testing integrasi dasar.
