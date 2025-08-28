# AJAX Implementation Documentation

## Overview
Sistem peminjaman lab telah diupgrade untuk menggunakan AJAX pada semua fitur, sehingga tidak ada reload halaman saat menggunakan tombol dan form.

## Changes Made

### 1. JavaScript Enhancement
- Modified `resources/js/app.js` dengan AJAX handlers
- Added fallback `public/js/ajax-handler.js` jika build process tidak tersedia
- Auto-loading fallback script di layout files

### 2. Controller Updates
Semua controller telah dimodifikasi untuk mendukung AJAX responses:

#### AdminController
- `storeSchedule()` - Tambah jadwal pakem
- `updateSchedule()` - Edit jadwal pakem  
- `destroySchedule()` - Hapus jadwal pakem
- `approveBooking()` - Setujui peminjaman
- `rejectBooking()` - Tolak peminjaman
- `cancelBooking()` - Batalkan peminjaman

#### GuruController
- `storeBooking()` - Submit pengajuan peminjaman

#### ProfileController
- `update()` - Update profile
- `destroy()` - Delete account

#### Auth\PasswordController
- `update()` - Update password

### 3. View Modifications
Semua form dan button telah ditambahkan attribute AJAX:

- `data-ajax="true"` - Untuk form dan button biasa
- `data-delete="true"` - Untuk tombol delete
- `data-modal="true"` - Untuk form di modal
- `data-confirm="message"` - Untuk konfirmasi sebelum aksi

### 4. Response Format
Controllers mengembalikan JSON response untuk AJAX request:

```json
{
    "success": true|false,
    "message": "Success/Error message",
    "redirect": "url",    // Optional: redirect URL
    "reload": true,       // Optional: reload page
    "errors": {}          // Optional: validation errors
}
```

### 5. Features Covered
- ✅ Tambah/Edit/Hapus Jadwal Pakem
- ✅ Approve/Reject/Cancel Peminjaman
- ✅ Submit Pengajuan Peminjaman
- ✅ Update Profile & Password
- ✅ Delete Account
- ✅ Form Validation with Error Display
- ✅ Success/Error Messages
- ✅ Confirmation Dialogs
- ✅ Loading States

## How to Build Assets

### Option 1: Using npm (Recommended)
```bash
# Install Node.js first, then:
npm install
npm run build

# For development with hot reload:
npm run dev
```

### Option 2: Manual Fallback
Jika Node.js tidak tersedia, sistem akan otomatis menggunakan fallback script `public/js/ajax-handler.js` yang tidak perlu build process.

## Usage Examples

### Form dengan AJAX
```html
<form action="/submit" method="POST" data-ajax="true">
    @csrf
    <!-- form fields -->
    <button type="submit">Submit</button>
</form>
```

### Delete Button
```html
<form action="/delete/1" method="POST" data-delete="true">
    @csrf
    @method('DELETE')
    <button type="submit" data-confirm="Are you sure?">Delete</button>
</form>
```

### Modal Form
```html
<form action="/submit" method="POST" data-modal="true">
    @csrf
    <!-- form fields -->
    <button type="submit">Submit</button>
</form>
```

## Browser Support
- Modern browsers dengan fetch() API support
- Auto fallback untuk error handling
- Progressive enhancement (still works without JS)

## User Experience Improvements
1. **No Page Reloads** - Semua aksi menggunakan AJAX
2. **Real-time Feedback** - Success/error messages appear instantly
3. **Loading States** - Buttons show "Processing..." during requests
4. **Error Validation** - Form errors displayed inline
5. **Smooth Transitions** - Messages fade in/out with animations

## Testing
Test semua fitur di browser untuk memastikan:
- Form submission tidak reload halaman
- Success/error messages muncul
- Validation errors ditampilkan
- Confirmation dialogs berfungsi
- Loading states terlihat
- Redirect/reload setelah sukses

## Troubleshooting
1. Jika AJAX tidak berfungsi, check browser console untuk error
2. Pastikan CSRF token tersedia di meta tag
3. Fallback script akan load otomatis jika Vite gagal
4. All forms masih berfungsi tanpa JavaScript (progressive enhancement)
