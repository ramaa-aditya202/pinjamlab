<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

# Sistem Peminjaman Lab Komputer

Ini adalah aplikasi Laravel fullstack untuk sistem peminjaman lab komputer dengan 2 role: admin dan guru.

## Fitur Utama:
- **Admin**: Dapat menginput jadwal lab pakem (tetap) dan menyetujui/menolak peminjaman dari guru
- **Guru**: Dapat melihat jadwal pakem dan mengajukan peminjaman pada slot yang kosong

## Struktur Database:
- `users`: Tabel user dengan kolom role (admin/guru)
- `lab_schedules`: Jadwal pakem yang dibuat admin
- `lab_bookings`: Pengajuan peminjaman dari guru

## Tech Stack:
- Laravel 12 dengan Breeze authentication
- Tailwind CSS untuk styling responsif
- Vite untuk build tools
- SQLite database

## Login Credentials:
- **Admin**: admin@lab.com / password
- **Guru**: ahmad@lab.com / password atau sari@lab.com / password

## Jadwal Lab:
- 1 Lab komputer tersedia
- 9 jam pelajaran per hari (Senin-Jumat)
- Jadwal ditampilkan dalam tabel responsif
- Tombol "Ajukan Peminjaman" muncul pada slot kosong