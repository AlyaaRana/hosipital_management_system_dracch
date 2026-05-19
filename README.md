# Hospital Management System API 🏥
> Hospital Management System (HMS) RESTful API built with Laravel. Final Project for BNCC LnT Back-End 2026.

## 👥 Our Team

| Name | Role | Core Responsibilities |
| :--- | :--- | :--- |
| **Alyaa Rana Raya** | Backend Lead & Architect | DB Schema (3NF), Sanctum Auth, Core Logic |
| **Joshua Genio Wiratama** | API & Data Specialist | REST Standards, Seeding/Factory, Pagination |
| **Nathan Grabiel Pramellah** | Storage & Comms Engineer | File Storage, Laravel Scheduler, Mailing |

## ✅ Implementasi Saat Ini
- Error handling API terpusat untuk `404`, `422`, dan `500` di `bootstrap/app.php`
- Soft delete file ditambahkan pada `app/Models/File.php` dan migration `database/migrations/2026_04_11_035139_create_files_table.php`
- `FileController` sekarang mendukung upload, download, dan soft delete
- `Schedule` model dan migration diperbaiki agar `available_slots` tersedia untuk appointment
- `MedicalRecordController` sekarang menyimpan dan menampilkan rekam medis secara nyata
- 10 test case sudah ditambahkan untuk menutup alur utama dan validasi

## 🧪 Test Plan
Semua test berada di folder `tests/Feature`.

### 10 test case yang disiapkan
1. `GET /api/v1/invalid-endpoint` -> 404 JSON
2. `POST /api/v1/auth/register` validasi gagal -> 422
3. `POST /api/v1/auth/register` berhasil -> 201
4. `POST /api/v1/auth/login` berhasil -> 200
5. `POST /api/v1/appointments` berhasil membuat appointment
6. `POST /api/v1/appointments` gagal saat jadwal penuh -> 422
7. `GET /api/v1/reports/export` berhasil untuk admin
8. `GET /api/v1/reports/export` ditolak untuk non-admin -> 403
9. `POST /api/v1/files/upload` dan `GET /api/v1/files/{id}` berhasil
10. `DELETE /api/v1/files/{id}` melakukan soft delete dan `GET` setelahnya -> 404

## ▶️ Menjalankan Test dan Coverage
Jalankan perintah berikut di direktori proyek:

```bash
composer test
```

Atau untuk coverage:

```bash
php artisan test --coverage
```

Simpan screenshot hasil coverage untuk dokumentasi README.

## 📌 Catatan Penting
- Endpoint admin export laporan: `GET /api/v1/reports/export`
- Endpoint upload file: `POST /api/v1/files/upload`
- Endpoint soft delete file: `DELETE /api/v1/files/{id}`
- Scheduler `files:purge` sudah dipasang di `app/Console/Kernel.php` untuk menjalankan penghapusan fisik file yang sudah soft deleted.

*Note: Commit messages in this repository use the initials `[ARR]`, `[NGP]`, and `[JGW]` to identify the author's contributions.*
