# Hospital Management System API 🏥
> Hospital Management System (HMS) RESTful API built with Laravel. Final Project for BNCC LnT Back-End 2026.

## 👥 Our Team

| Name | Role | Core Responsibilities |
| :--- | :--- | :--- |
| **Alyaa Rana Raya** | Backend Lead & Architect | DB Schema (3NF), Sanctum Auth, Core Logic |
| **Joshua Genio Wiratama** | API & Data Specialist | REST Standards, Seeding/Factory, Pagination |
| **Nathan Grabiel Pramellah** | Storage & Comms Engineer | File Storage, Laravel Scheduler, Mailing |

*Note: Commit messages in this repository use the initials `[ARR]`, `[NGP]`, and `[JGW]` to identify the author's contributions.*

## ✨ Fitur Utama
* **Multi-Role Authentication:** Akses terstruktur menggunakan Laravel Sanctum untuk Admin, Dokter, dan Pasien.
* **Appointment Management:** Sistem booking janji temu yang terintegrasi dengan jadwal dokter.
* **Medical Records:** Pencatatan dan pembacaan riwayat medis (diagnosis & resep) secara aman.
* **Secure File Storage:** Upload dokumen rekam medis dan pasfoto dengan validasi dan otorisasi.
* **Automated Mailing & Task:** Notifikasi email (konfirmasi & *reminder*) menggunakan Laravel Scheduler.

## 🗄️ Entity Relationship Diagram (ERD)
![ERD Hospital Management System](assets/image/erd.jpeg)

## ⚙️ Instalasi & Setup Environment
Berikut adalah langkah-langkah untuk menjalankan aplikasi secara lokal:

1. Clone repository ini:
   ```bash
   git clone [https://github.com/AlyaaRana/hosipital_management_system_dracch.git]
   cd hosipital_management_system_dracch
2. Install seluruh dependencies (PHP & vendor):
   composer install
3. Setup file environment
   cp .env.example .env
   php artisan key:generate
4. Konfigurasi kredensial Database di file .env, lalu jalankan migrasi beserta dummy data (Seeder):
   php artisan migrate --seed
5. Hubungkan direktori storage untuk akses file:
   php artisan storage:link
6. Jalankan server lokal:
   php artisan serve

## 🧪 Test Plan & Code Coverage
Proyek ini mengimplementasikan Feature Test dan Unit Test sesuai standar (minimal 10 Test Cases).
Unit Tests (tests/Unit/)
1. AppointmentStatusTest - State machine logic pergantian status janji temu.
2. FileValidationTest - Validasi ukuran maksimal (5MB) dan ekstensi file (jpeg/png/pdf).
3. DoctorScheduleTest - Validasi jadwal dokter.

Feature Tests (tests/Feature/)
1. AuthenticationTest - Uji coba Register, Login (200), Login Gagal (401), dan Logout.
2. AppointmentTest - Membuat appointment (201), Gagal saat jadwal penuh (422), Update, dan Pembatalan.
3. FileUploadTest - Upload file sukses, Stream/Read file, dan Soft delete file.
4. ReportTest - Uji export laporan PDF/CSV untuk Admin (200) dan ditolak untuk non-Admin (403).

Menjalankan Test:
composer test
# atau
php artisan test --coverage


