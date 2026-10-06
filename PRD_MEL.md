# Product Requirements Document (PRD): Sistem Monitoring, Evaluasi, dan Pembelajaran (MEL)

## 1. Pendahuluan

### 1.1 Tujuan Dokumen
Dokumen Product Requirements Document (PRD) ini bertujuan untuk mendefinisikan persyaratan fungsional, non-fungsional, dan arsitektur dasar untuk pengembangan Sistem Monitoring, Evaluasi, dan Pembelajaran (Sistem MEL). Dokumen ini berfungsi sebagai panduan utama bagi tim pengembang (terutama backend developer), desainer, dan pemangku kepentingan lainnya untuk memahami ruang lingkup, fitur, struktur data, dan alur kerja aplikasi.

### 1.2 Latar Belakang & Visi Produk
Berdasarkan dokumen "BAHANMEL_2.docx" dan diagram yang dilampirkan, Sistem MEL dirancang untuk memfasilitasi proses pemantauan, evaluasi, dan pelaporan hasil pekerjaan/proyek di dalam sebuah organisasi (seperti Yayasan atau Lembaga YNKI).

Visi produk ini adalah untuk menciptakan platform digital terpusat yang merampingkan alur kerja MEL, mulai dari input data lapangan, verifikasi berjenjang, hingga pengesahan akhir, serta menyediakan transparansi informasi kepada publik. Sistem ini bertujuan untuk meningkatkan akuntabilitas, efisiensi pelaporan, dan memastikan bahwa setiap proyek berjalan sesuai dengan indikator kinerja dan tujuan organisasi.

### 1.3 Ruang Lingkup
Sistem MEL akan mencakup fungsionalitas berikut:
*   Manajemen proyek dan kegiatan (Project & Activity Management).
*   Manajemen pengguna dan hak akses (Role-Based Access Control).
*   Manajemen dan penjadwalan kegiatan (Timeline).
*   Sistem notifikasi (Reminder) otomatis.
*   Pelaporan hasil pekerjaan dari lapangan (Field Update).
*   Proses evaluasi data (membandingkan hasil vs indikator) secara berjenjang.
*   Mekanisme permintaan perbaikan/klarifikasi data (Correction Request).
*   Proses persetujuan dan pengesahan (Approval) oleh pimpinan.
*   Dashboard dan publikasi informasi umum untuk masyarakat luas (Non-User).

## 2. Pendekatan Teknis: OOP PHP Native
Aplikasi ini akan dibangun menggunakan pendekatan Object-Oriented Programming (OOP) dengan bahasa pemrograman PHP (Native/tanpa framework eksternal besar seperti Laravel/CodeIgniter, sesuai permintaan). Hal ini bertujuan untuk memastikan struktur kode yang modular, mudah dipelihara (maintainable), dan dapat digunakan kembali (reusable).

Arsitektur aplikasi akan mengikuti pola MVC (Model-View-Controller) secara logis:
*   **Model:** Merepresentasikan struktur data, logika bisnis, dan interaksi dengan database (mengimplementasikan kelas-kelas pada Class Diagram).
*   **View:** Antarmuka pengguna (HTML/CSS/JS) yang menerima data dari Controller.
*   **Controller:** Mengatur alur permintaan dari pengguna (routing), memanggil metode pada Model yang sesuai, dan mengirimkan hasilnya ke View.

## 3. Pengguna (User Personas) dan Peran (Role)
Sistem ini melayani beberapa jenis pengguna dengan peran (Role) yang berbeda:
1.  **Staf Lapangan (Field Staff):** Melaksanakan kegiatan, mengunggah laporan hasil (`FieldUpdate`), merespons permintaan perbaikan (`CorrectionRequest`).
2.  **Staf MEL:** Mengelola timeline kegiatan (`ActivityTimeline`), memverifikasi laporan awal, meneruskan data ke PL, mengelola hasil akhir MEL (`MELResult`).
3.  **Project Leader (PL):** Mengevaluasi (`Evaluation`) hasil pekerjaan terhadap indikator kinerja (`Indicator`), meminta perbaikan (`CorrectionRequest`), meneruskan hasil ke Direktur.
4.  **Direktur / Pimpinan:** Memberikan persetujuan akhir (`Approval`) terhadap hasil MEL yang telah dievaluasi, memastikan kesesuaian dengan tujuan YNKI.
5.  **Non-User (Publik):** Mengakses dashboard umum untuk melihat status agregat proyek yang dipublikasikan (`Project.publicStatus`).

## 4. Persyaratan Fungsional dan Pemetaan Kelas (OOP)
Berdasarkan analisis diagram (Use Case, Activity, Sequence, dan Class Diagram), berikut adalah fitur utama dan hubungannya dengan struktur kelas OOP:

### 4.1 Modul Autentikasi dan Pengguna
*   **Fitur:** Login, logout, manajemen profil.
*   **Kelas Terkait:**
    *   `User`: Menangani autentikasi (`login()`, `logout()`), menyimpan data pengguna (`email`, `password` ter-hash), relasi ke `Role`.
    *   `Role`: Mendefinisikan hak akses (`roleName`).

### 4.2 Modul Manajemen Proyek dan Kegiatan
*   **Fitur:** Membuat proyek baru, menambahkan anggota proyek, mendefinisikan kegiatan dan indikator.
*   **Kelas Terkait:**
    *   `Project`: Entitas utama proyek (`createProject()`, `updateStatus()`).
    *   `ProjectMember`: Mengelola relasi antara `User` dan `Project` beserta perannya dalam proyek tersebut.
    *   `Activity`: Rincian kegiatan di dalam proyek (`createActivity()`).
    *   `Indicator`: Parameter keberhasilan suatu kegiatan (`targetValue`, `unit`).

### 4.3 Modul Timeline dan Reminder
*   **Fitur:** Mengatur jadwal kegiatan, mengirim notifikasi otomatis.
*   **Kelas Terkait:**
    *   `ActivityTimeline`: Menentukan rentang waktu dan kapan reminder harus dikirim (`calculateReminder()`).
    *   `Notification`: Entitas untuk menyimpan pesan notifikasi ke pengguna (`sendNotification()`, status `isRead`).

### 4.4 Modul Pelaporan Lapangan
*   **Fitur:** Input data hasil pekerjaan, unggah bukti.
*   **Kelas Terkait:**
    *   `FieldUpdate`: Menampung data laporan dari Staf Lapangan (`submitUpdate()`, `realizationValue`, `evidenceFile`). Kelas ini berelasi dengan `Activity` dan `User`.

### 4.5 Modul Evaluasi dan Klarifikasi (Loop Perbaikan)
*   **Fitur:** Membandingkan laporan dengan indikator, memberikan catatan perbaikan.
*   **Kelas Terkait:**
    *   `Evaluation`: Dilakukan oleh PL. Membandingkan `FieldUpdate` dengan `Indicator` (`evaluateResult()`, `compareIndicator()`).
    *   `CorrectionRequest`: Jika evaluasi tidak memenuhi syarat, PL membuat request perbaikan ke Staf Lapangan (`createCorrection()`, `submitResponse()`). Proses ini dapat berulang.

### 4.6 Modul Pengesahan dan Hasil MEL
*   **Fitur:** Pengesahan akhir oleh Direktur, penetapan hasil MEL final.
*   **Kelas Terkait:**
    *   `Approval`: Keputusan akhir Direktur atas proses yang sudah berjalan (`approveResult()`, `rejectResult()`).
    *   `MELResult`: Data final agregasi atau rangkuman evaluasi dari suatu kegiatan yang siap dipublikasikan atau diarsipkan (`createMELResult()`).

### 4.7 Modul Akses Publik
*   **Fitur:** Dashboard ringkasan tanpa login.
*   **Kelas Terkait:** Mengambil data agregat (Read-only) dari `Project` (dengan filter `publicStatus`), `Activity`, dan mungkin `MELResult` (yang disetujui publik). Tidak ada metode manipulasi data di sini.

## 5. Alur Kerja Utama (Merujuk Sequence Diagram)
1.  **Akses Publik:** Akses URL Publik -> Controller memanggil metode *read-only* dari model `Project` (mencari `publicStatus` = true) -> Menampilkan View Dashboard. Detail memerlukan login.
2.  **Manajemen Timeline & Reminder (Cron Job):** Staf MEL membuat `ActivityTimeline`. Sistem backend (misal via Cron Job di server) menjalankan script berkala yang memanggil `ActivityTimeline->calculateReminder()`. Jika waktunya tepat, script akan memicu pembuatan objek `Notification` dan fungsi `sendNotification()`.
3.  **Pelaporan Data:** Staf Lapangan login -> Akses form `Activity` mereka -> Submit form -> Sistem membuat objek `FieldUpdate` baru (`submitUpdate()`) dan menyimpannya ke database.
4.  **Perbaikan Data (Loop):** PL membuat objek `Evaluation` terkait suatu `FieldUpdate`. Jika `Evaluation->result` adalah 'Tidak Sesuai', PL membuat objek `CorrectionRequest`. Notifikasi dikirim ke Staf Lapangan. Staf Lapangan memperbarui `FieldUpdate` dan mengisi `CorrectionRequest->submitResponse()`. PL melakukan `Evaluation` ulang.
5.  **Pengesahan Akhir:** Direktur melihat `MELResult` (yang dibuat Staf MEL berdasarkan `Evaluation` PL). Direktur membuat entitas `Approval` (`approveResult()` atau `rejectResult()`). Status keseluruhan diperbarui.

## 6. Struktur Basis Data (Diturunkan dari Class Diagram)
Sistem akan menggunakan database relasional (misal: MySQL/MariaDB). Pemetaan kelas ke tabel (ORM manual) secara umum adalah 1-ke-1 (Satu Kelas = Satu Tabel). Kunci Tamu (Foreign Keys) digunakan untuk mengelola relasi:
*   `roles` (id, role_name, ...)
*   `users` (id, role_id (FK), name, email, password, ...)
*   `projects` (id, project_code, name, created_by (FK), ...)
*   `project_members` (id, project_id (FK), user_id (FK), member_role)
*   `activities` (id, project_id (FK), name, ...)
*   `indicators` (id, activity_id (FK), target_value, ...)
*   `activity_timelines` (id, activity_id (FK), start_date, reminder_date, ...)
*   `notifications` (id, user_id (FK), activity_id (FK), message, is_read, ...)
*   `field_updates` (id, activity_id (FK), user_id (FK), realization_value, evidence_file, ...)
*   `evaluations` (id, update_id (FK), indicator_id (FK), evaluator_id (FK), result, ...)
*   `correction_requests` (id, evaluation_id (FK), requested_by (FK), assigned_to (FK), ...)
*   `mel_results` (id, activity_id (FK), evaluation_id (FK), submitted_by (FK), ...)
*   `approvals` (id, mel_result_id (FK), approved_by (FK), decision, notes, ...)

## 7. Persyaratan Non-Fungsional
*   **Keamanan:** Menggunakan `password_hash()` PHP, prepared statements (PDO) untuk mencegah SQL Injection, validasi sesi (Session) dan otorisasi ketat di setiap Controller.
*   **Arsitektur Kode:** Menerapkan autoloader PHP (`spl_autoload_register`) untuk memuat kelas secara otomatis. Struktur folder yang jelas (misal: `/models`, `/views`, `/controllers`, `/config`).
*   **Kinerja:** Penggunaan indeks pada kolom Foreign Key di database untuk mempercepat query relasional.
*   **Desain Antarmuka Pengguna (UI/UX) & Panduan Warna:**
    *   Aplikasi harus memiliki tampilan yang responsif untuk kemudahan akses di berbagai perangkat (desktop, tablet, maupun mobile).
    *   **Skema Warna Wajib (Color Palette):**
        *   **`#B5CE88`** (Hijau Pastel): Digunakan sebagai warna utama untuk elemen interaktif seperti **Tombol Utama (Primary Button)**, **Aksen navigasi**, status "Berhasil/Sesuai", dan elemen penting lainnya agar memberikan kesan sejuk, segar, dan positif.
        *   **`#FBFFFF`** (Putih Kebiruan Terang): Digunakan sebagai **Warna Latar Belakang (Background Color)** utama aplikasi atau warna dasar pada area konten/kartu (card). Tujuannya adalah untuk memberikan kontras yang baik, kebersihan visual (clean look), dan kenyamanan membaca.

## 8. Referensi
*   Dokumen "BAHANMEL_2.docx"
*   Class Diagram - Sistem Monitoring, Evaluation, and Learning (MEL)
*   Use Case, Activity, dan Sequence Diagrams yang dilampirkan sebelumnya.