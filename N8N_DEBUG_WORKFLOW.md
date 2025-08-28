# Debug N8N Import Error "Could not find property option"

## 🔍 **Root Cause Analysis**

Error "Could not find property option" biasanya disebabkan oleh:

1. **Invalid typeVersion**: Switch node dengan `typeVersion: 3` tidak kompatibel dengan versi N8N lama
2. **Complex nested structures**: `rules.rules` array structure
3. **Missing properties**: Node parameters yang tidak sesuai dengan versi N8N
4. **Expression syntax**: `={{ }}` vs `={{}}` (spasi bisa bermasalah)

## 🚀 **Solusi Bertahap**

### **Step 1: Gunakan Workflow Minimal**
File: `n8n-minimal-workflow.json`
- Hanya Webhook → Telegram (2 nodes)
- Tanpa Switch/Set nodes yang kompleks
- Expression syntax sederhana

### **Step 2: Jika Minimal Berhasil, Upgrade ke Compatible**  
File: `n8n-compatible-workflow.json`
- Webhook → Set → Telegram (3 nodes)
- `typeVersion: 1` untuk semua nodes
- Expression tanpa spasi: `={{$json.field}}`

### **Step 3: Manual Configuration (Fallback)**
Jika JSON import tetap gagal:

1. **Buat workflow baru di N8N**
2. **Drag & drop nodes secara manual**:
   - Webhook node → set path `booking-notification`
   - Set node → tambah fields: message, event_type, chat_id
   - Telegram node → set chat_id dan message
3. **Connect nodes** secara visual
4. **Test dengan webhook URL**

## 📋 **Prioritas Testing**

1. ✅ **Import `n8n-minimal-workflow.json`** dulu
2. ✅ **Ganti Chat ID dan Bot Credentials**
3. ✅ **Test dengan Laravel**: 
   ```powershell
   php artisan test:n8n-webhook
   ```
4. ⚠️ **Jika error persists**: Coba manual setup

## 🔧 **Expression Syntax Yang Benar**

| ❌ Salah | ✅ Benar |
|----------|----------|
| `={{ $json.field }}` | `={{$json.field}}` |
| `typeVersion: 3` | `typeVersion: 1` |
| `rules: { rules: [...] }` | Hindari Switch node kompleks |
| `options: {}` | Hapus property kosong |

## 💡 **Debugging Tips**

- **Check N8N version**: Workflow dibuat untuk N8N v0.235+, mungkin Anda pakai versi lama
- **Browser console**: Buka DevTools untuk error details saat import
- **Start simple**: Workflow minimal dulu, tambah complexity bertahap

**Coba import `n8n-minimal-workflow.json` dan lapor hasilnya!**
