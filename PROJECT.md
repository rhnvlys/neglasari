# SIAP Neglasari - Project Definition

## Project Scope
Sistem Informasi Absensi Perangkat Desa Neglasari (SIAP Neglasari) adalah aplikasi absensi yang mendukung verifikasi geolokasi dan bukti foto, lengkap dengan modul pengajuan cuti, manajemen jam kerja, dan laporan.

### Out of Scope (Currently)
- Face recognition
- Aplikasi Native Android/iOS (hanya versi web responsif)

## Tech Stack (Locked)
- Backend: Laravel 11 (PHP 8.3+)
- Frontend: Blade, Tailwind CSS, Alpine.js
- Database: SQLite (local/testing), MySQL/MariaDB (production)
- Auth & Roles: Laravel Sanctum, Spatie Permission
- File processing: Intervention Image (v3/v2)
- Laporan: Laravel Excel, DomPDF
- UI Chart: Chart.js
- Peta: Leaflet (OpenStreetMap)

## Database Schema (Entities)
1. `users`: Sistem autentikasi (extends Spatie interfaces)
2. `employees`: Profil pegawai, relasi ke user dan jabatan
3. `positions`: Jabatan perangkat desa
4. `work_schedules`: Master jam kerja dan toleransi keterlambatan
5. `employee_schedules`: Pivot penugasan jadwal ke pegawai (periode)
6. `office_locations`: Lokasi absensi yang valid beserta radiusnya
7. `attendances`: Log absensi harian (check-in, check-out, geolokasi, status)
8. `attendance_corrections`: Riwayat koreksi data absensi oleh admin
9. `leave_requests`: Pengajuan cuti, sakit, dinas luar
10. `holidays`: Hari libur nasional atau desa
11. `settings`: Pengaturan aplikasi (key-value store)
12. `activity_logs`: Audit log

## API Contract
- Menggunakan standar REST API.
- Prefix: `/api/v1`
- Auth: Bearer token (Sanctum) - untuk keperluan mobile/PWA mendatang (opsional). Web menggunakan session biasa.

## Design Tokens
- Primary: `#1F5D42`
- Dark: `#173E2E`
- Accent: `#2F7D5A`
- Background: `#F6F8F6`
- Text Main: `#17201B`
- Spacing: 8pt grid (`tailwinid` default)
- Font: Inter (atau default sans-serif sistem)

## Deployment Target
- Shared Hosting cPanel
- Public directory mapping `public_html`
- Environment Variables wajib di-set untuk SMTP, App_URL, DB.

## Definition of Done (DoD)
- Kode lolos static analysis.
- UI responsive di mobile dan desktop.
- Fungsi utama (CRUD dan Absen) memiliki test coverage memadai dan hijau (PASS).
- Tidak ada dead code.
