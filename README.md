# Siklus

**Siklus** adalah aplikasi **Sistem Informasi Keluarga Sehat** yang dirancang untuk Posyandu.  
Tujuannya adalah memudahkan pencatatan data keluarga, pemantauan kesehatan, dan laporan secara real­time agar kader & petugas Posyandu bisa bekerja lebih efisien.

---

## 🧐 Fitur Utama

- Autentikasi & manajemen pengguna (admin, dokter, kader, dll)  
- Dashboard statistik:  
  + Sasaran (jumlah keluarga / anggota) yang sudah diregister  
  + Pemeriksaan medis & konsultasi  
  + Grafik berdasarkan status kesehatan (IMT, gula darah, asam urat, kolesterol, tensi)  
- Filter organisasi dan rentang tanggal untuk laporan dan dashboard  
- Aktivitas terbaru: mencatat perubahan & info terbaru (registrasi, pemeriksaan, konsultasi)  
- Perbandingan status kesehatan (belum diperiksa, hanya diperiksa, sudah konsultasi)

---

## 🛠 Teknologi yang Digunakan

- Backend: **Laravel (PHP)**  
- Frontend blade + Bootstrap / template Hope UI (atau tema serupa)  
- Database: MySQL / database yang kompatibel dengan Laravel  
- Grafik: menggunakan library seperti ApexCharts atau library chart JS lainnya

---

## 📁 Struktur Direktori Singkat

Beberapa direktori penting:
app/
├ Http/Controllers/ ← termasuk HomeController supaya logika dashboard
├ Models/
resources/
├ views/
├ dashboards/ ← view dashboard utama
├ components/partials ← view reusable (kartu, chart, dll)
routes/
├ web.php ← route untuk dashboard, auth, dll
database/
├ migrations/ ← migrasi tabel-tabel utama (users, sasaran, pemeriksaan, konsultasi, etc)

---

## 🚀 Cara Instalasi

Ikuti langkah-berikut untuk menjalankan aplikasi secara lokal:

1. Clone repository  
   ```bash
   git clone https://github.com/suluhkasihbangsa-droid/siklus.git
   cd siklus
   Install dependency backend dan frontend

   composer install
   npm install
   npm run dev


   Setup file .env

   cp .env.example .env
   php artisan key:generate


   Sesuaikan setting database di .env (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD).

   Jalankan migrasi & seeder (jika ada data dummy / awal)

   php artisan migrate --seed


   Jalankan server

   php artisan serve

   ⚙ Konfigurasi & Customize

   Filter organisasi/tanggal di dashboard bisa digunakan oleh admin untuk melihat data spesifik organisasi tertentu atau periode tertentu.

   Grafik / chart dinamika kesehatan sudah di-set berdasarkan data yang ada; kamu bisa menambah kolom data lain jika dibutuhkan.

   Aktivitas terbaru dibatasi jumlahnya (hari ini/default); kamu bisa ubah batas (limit) di kode controller agar menampilkan lebih banyak.

   🎯 Tujuan & Manfaat

   Mempermudah kerja kader Posyandu dengan data digital vs manual

   Memberi visibilitas lebih baik ke status kesehatan masyarakat secara cepat

   Mendukung pengambilan keputusan: tampilan grafik, laporan ringkas, aktivitas terakhir

   📄 Lisensi

   Proyek ini dilisensikan di bawah MIT License — bebas digunakan, dimodifikasi, dan didistribusi ulang selama menyertakan lisensi yang sesuai.

   📌 Cara Kontribusi

   Kalau kamu/kawan mau bantu:

   Fork repositori

   Buat branch fitur / perbaikan

   Komit perubahan

   Lakukan pull request

   Review & integrasi
