# 📋 Manual Setup N8N Switch Node

Karena import JSON kadang tidak sempurna, berikut setup manual untuk Switch node:

## 🔧 Switch Node Configuration

### 1. Basic Settings
- **Mode**: `Fixed` (default)
- **Data Type**: `String`

### 2. Value 1 (Input)
```
{{ $json.event_type }}
```
**⚠️ PENTING**: Pastikan ini diketik di **Expression editor** (klik icon fx)

### 3. Routing Rules

#### Rule 1 - New Booking
- **Operation**: `Equal`
- **Value 2**: `booking_created` (text biasa, bukan expression)
- **Output**: `0`

#### Rule 2 - Status Update  
- **Operation**: `Equal`
- **Value 2**: `booking_status_updated` (text biasa, bukan expression)
- **Output**: `1`

#### Rule 3 - Test Notification
- **Operation**: `Equal`
- **Value 2**: `test_notification` (text biasa, bukan expression)
- **Output**: `2`

## 🎯 Step-by-Step Setup

### 1. Buat Switch Node Baru
1. Tambah node **Switch** ke workflow
2. Connect dari **Webhook Trigger**

### 2. Configure Value 1
1. Klik field **Value 1**
2. Klik icon **fx** (Expression)
3. Ketik: `{{ $json.event_type }}`
4. Klik **Accept**

### 3. Configure Rules
1. **Add Rule** untuk setiap kondisi:
   
   **Rule 1:**
   - Operation: `Equal`
   - Value 2: `booking_created`
   - Output: `0`
   
   **Rule 2:**
   - Operation: `Equal`  
   - Value 2: `booking_status_updated`
   - Output: `1`
   
   **Rule 3:**
   - Operation: `Equal`
   - Value 2: `test_notification`
   - Output: `2`

### 4. Connect Outputs
- **Output 0** → Set New Booking Data
- **Output 1** → Set Status Update Data  
- **Output 2** → Set Test Notification Data

## ✅ Verification

### Test dengan Expression Editor
Di Value 1, pastikan muncul:
```
{{ $json.event_type }}
```

### Test Manual
1. **Execute Workflow** → **Via Webhook**
2. Paste test data:
```json
{
  "event_type": "booking_created",
  "message": "Test message"
}
```
3. Verify execution berlanjut ke output yang benar

## 🚨 Common Mistakes

❌ **Value 1 berisi**: `0` atau kosong
✅ **Value 1 seharusnya**: `{{ $json.event_type }}`

❌ **Value 2 berisi**: Expression `{{ ... }}`  
✅ **Value 2 seharusnya**: Text biasa seperti `booking_created`

❌ **Mode**: `Rules` 
✅ **Mode**: `Fixed`

## 🔍 Debug Tips

Jika masih tidak work:

1. **Tambahkan Set node** sebelum Switch:
```javascript
{
  "debug_event_type": "{{ $json.event_type }}",
  "debug_full_data": "{{ JSON.stringify($json) }}"
}
```

2. **Check execution log** untuk melihat data yang masuk

3. **Manual test** dengan data sederhana dulu

---

**💡 Tip**: Import JSON kadang bermasalah dengan expressions. Setup manual lebih reliable!
