# 🖥️ Sistem Peminjaman Lab Komputer

Aplikasi Laravel fullstack untuk sistem peminjaman lab komputer dengan notifikasi Telegram otomatis.

## 🚀 Quick Start

```bash
composer install
cp .env.example .env
npm install
npm run build
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

## ✨ Fitur Utama

### 👨‍💼 Admin
- ✅ Kelola jadwal lab pakem (tetap)
- ✅ Approve/reject peminjaman dari guru
- ✅ Dashboard dengan statistik lengkap
- ✅ Notifikasi Telegram otomatis

### 👨‍🏫 Guru  
- ✅ Lihat jadwal lab real-time
- ✅ Ajukan peminjaman pada slot kosong
- ✅ Tracking status pengajuan
- ✅ Riwayat peminjaman lengkap

## 📊 Tech Stack

- **Backend:** Laravel 12 dengan Breeze Auth
- **Frontend:** Tailwind CSS (Responsive Design)
- **Database:** SQLite
- **Build:** Vite
- **Notifications:** N8N + Telegram Bot Integration

## 🔐 Default Login

```
Admin: admin@lab.com / password
Guru1: ahmad@lab.com / password  
Guru2: sari@lab.com / password
```

## 🤖 Telegram Integration

Sistem terintegrasi dengan N8N untuk mengirim notifikasi Telegram otomatis:

### 📝 Notifikasi yang Dikirim:
- **Pengajuan baru** - Ketika guru mengajukan peminjaman
- **Peminjaman disetujui** - Ketika admin approve
- **Peminjaman ditolak** - Ketika admin reject dengan alasan

### ⚡ Setup Integrasi:
1. [**Panduan Instalasi Lengkap**](INSTALLATION_GUIDE.md)
2. [**Setup N8N & Telegram**](N8N_TELEGRAM_SETUP.md) 
3. [**Testing Guide**](TESTING_GUIDE.md)

### 🧪 Quick Test:
```bash
php artisan test:booking-notification
```

## 🗄️ Database Schema

```sql
users (id, name, email, role, password)
lab_schedules (id, day, hour, subject, class, teacher, is_fixed)  
lab_bookings (id, user_id, day, hour, teacher_name, class, subject, status, notes)
```

## 📅 Sistem Jadwal

- **1 Lab Komputer** tersedia
- **9 Jam Pelajaran** per hari (08:00-16:00)
- **5 Hari Kerja** (Senin-Jumat)
- **Real-time availability** checking

## 🛠️ Environment Setup

Tambahkan di `.env` untuk integrasi Telegram:

```env
# N8N Telegram Integration
N8N_WEBHOOK_URL=https://your-n8n-instance.com/webhook/booking-notification
```

## 📱 Responsive Design

- ✅ **Mobile First** approach
- ✅ **Tablet** optimized
- ✅ **Desktop** enhanced
- ✅ **Touch-friendly** UI

## 🔧 Development Commands

```bash
# Development server
php artisan serve

# Database operations
php artisan migrate:fresh --seed
php artisan migrate:rollback

# Clear caches
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Test notifications
php artisan test:booking-notification
php artisan test:webhook-direct

# Cleanup tools
php artisan cleanup:rejected-bookings --dry-run
php artisan cleanup:rejected-bookings

# Monitor logs
tail -f storage/logs/laravel.log

# Build assets
npm run dev     # Development
npm run build   # Production
```

## 📁 Project Structure

```
app/
├── Http/Controllers/
│   ├── AdminController.php        # Admin dashboard & management
│   ├── GuruController.php         # Teacher booking system
│   └── WebhookController.php      # N8N webhook handler
├── Models/
│   ├── LabBooking.php             # Booking model
│   ├── LabSchedule.php            # Schedule model
│   └── User.php                   # User model with roles
└── Console/Commands/
    └── TestBookingNotification.php # Testing command

database/
├── migrations/                    # Database schema
└── seeders/                      # Sample data

resources/views/
├── admin/                        # Admin panels
├── guru/                         # Teacher interfaces
├── auth/                         # Authentication
└── layouts/                      # Shared layouts
```

## 🎯 API Endpoints

```bash
# Webhook for N8N integration
POST /webhook/n8n/booking-notification

# Admin routes (auth required)
GET  /admin/dashboard
GET  /admin/bookings  
POST /admin/bookings/{id}/approve
POST /admin/bookings/{id}/reject

# Teacher routes (auth required)
GET  /guru/dashboard
POST /guru/booking
GET  /guru/my-bookings
```

## 🔒 Security Features

- ✅ **Laravel Breeze** authentication
- ✅ **CSRF protection** on forms
- ✅ **Role-based** access control
- ✅ **Input validation** & sanitization
- ✅ **Database relationships** with constraints
- ✅ **Error handling** with logging

## 🚨 Known Issues & Solutions

### Issue: Notifikasi tidak terkirim
```bash
# Check webhook URL configuration
php artisan config:clear && php artisan config:cache

# Test webhook manually
php artisan test:booking-notification

# Check logs
tail -f storage/logs/laravel.log
```

### Issue: Schedule conflicts
Database menggunakan unique constraint untuk mencegah double booking pada slot yang sama.

### Issue: Permission denied
Pastikan storage folder writable:
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

This project is licensed under the MIT License - see the [Laravel License](https://laravel.com/docs/contributions#license) for details.

---

**Built with ❤️ using Laravel & modern web technologies**
