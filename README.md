# 💬 ForumKu — Forum Diskusi Mahasiswa

> Platform tanya-jawab berbasis web untuk mahasiswa. Ajukan pertanyaan, beri jawaban, sukai postingan, dan tandai jawaban terbaik — seperti Stack Overflow versi kampus.

![PHP](https://img.shields.io/badge/PHP-Native-777BB4?style=flat&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/Database-MySQL-4479A1?style=flat&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green)
![Status](https://img.shields.io/badge/Status-Active-success)

---

## 📖 Tentang Project

**ForumKu** adalah aplikasi web forum diskusi yang ditujukan untuk mahasiswa. Pengguna dapat membuat pertanyaan sesuai kategori, mendapatkan jawaban dari pengguna lain, memberi *like*, serta menandai **jawaban terbaik** (best answer). Tersedia juga **panel admin** untuk memantau statistik dan mengelola seluruh data forum.

Project ini dibangun menggunakan **PHP Native** (tanpa framework) dan **MySQL**, cocok dijalankan di lingkungan **Laragon / XAMPP**.

---

## ✨ Fitur Utama

### 👤 Autentikasi & Akun
- Registrasi dengan validasi (username, email, password)
- Login & Logout berbasis session
- Halaman profil: ubah data diri, ganti password, & statistik pribadi

### 📝 Postingan / Diskusi
- Buat, edit, dan hapus postingan (sesuai hak akses)
- Buka & tutup diskusi (status *open* / *closed*)
- Pencarian berdasarkan judul/isi
- Filter per kategori & sortir (Terbaru, Terlama, Terpopuler)
- Pagination (10 postingan per halaman)

### 💬 Komentar / Jawaban
- Tambah, edit, dan hapus jawaban
- Tandai **Jawaban Terbaik** (best answer)

### ❤️ Interaksi
- Tombol Like real-time menggunakan **AJAX** (tanpa reload halaman)
- 5 kategori diskusi: Akademik, Teknologi, Info Magang, Organisasi, Umum

### 🛠️ Panel Admin
- Dashboard statistik (user, post, komentar, like, dll.)
- Kelola User, Postingan, dan Kategori

---

## 🧰 Teknologi

| Lapisan        | Teknologi                                   |
| -------------- | ------------------------------------------- |
| Backend        | PHP Native                                  |
| Database       | MySQL                                       |
| Frontend       | HTML, CSS, JavaScript                       |
| Ikon           | Font Awesome 6                              |
| Interaktivitas | AJAX (fetch)                                |
| Keamanan       | `password_hash`, sanitasi input, session    |

---

## 📂 Struktur Folder

```
forum_diskusi/
├── admin/            # Panel admin (dashboard, user, post, kategori)
├── assets/           # CSS & JavaScript
│   ├── css/
│   └── js/
├── auth/             # Login, register, logout
├── comment/          # CRUD komentar & best answer
├── config/           # Konfigurasi database & helper
├── includes/         # Header & footer (user + admin)
├── post/             # CRUD postingan, like, buka/tutup
├── database.sql      # Struktur & data awal database
├── index.php         # Halaman beranda forum
└── profile.php       # Halaman profil pengguna
```

---

## 🗄️ Struktur Database

Menggunakan 5 tabel relasional:

| Tabel          | Fungsi                                                        |
| -------------- | ------------------------------------------------------------- |
| `tb_user`      | Data akun pengguna & role (user / admin)                      |
| `tb_kategori`  | Daftar kategori diskusi                                       |
| `tb_postingan` | Pertanyaan/diskusi (status open/closed)                       |
| `tb_komentar`  | Jawaban/komentar (penanda best answer)                        |
| `tb_like`      | Catatan like (relasi unik user & postingan)                   |

---

## 🚀 Cara Instalasi

1. **Clone / download** repository ini ke folder web server:
   ```
   C:/laragon/www/forum_diskusi   (untuk Laragon)
   atau  htdocs/forum_diskusi      (untuk XAMPP)
   ```
2. **Jalankan** Apache & MySQL melalui Laragon/XAMPP.
3. **Buat database** — buka phpMyAdmin, lalu impor file `database.sql`.
4. **Sesuaikan konfigurasi** pada `config/db.php` bila perlu:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'forum_diskusi_db');
   define('BASE_URL', 'http://localhost/forum_diskusi');
   ```
5. **Akses** melalui browser:
   ```
   http://localhost/forum_diskusi
   ```

---

## 🔑 Akun Demo

| Peran | Email             | Password   |
| ----- | ----------------- | ---------- |
| Admin | admin@forum.com   | `password` |
| User  | budi@mail.com     | `password` |

> Semua akun bawaan menggunakan password: **`password`**

---

## 📸 Cuplikan Layar

> _Tambahkan screenshot tampilan aplikasi di sini (beranda, detail diskusi, dashboard admin)._

---

## 📌 Rencana Pengembangan

- [ ] Upload foto profil pengguna
- [ ] Notifikasi saat postingan dijawab
- [ ] Sistem reputasi / poin pengguna
- [ ] Editor teks kaya (rich text) untuk postingan

---

## 👨‍💻 Pembuat

**Arzaki Muhamad Fadil**

---

## 📄 Lisensi

Project ini dilisensikan di bawah **MIT License** — bebas digunakan untuk keperluan belajar.
