-- ForumKu - Database SQL
-- Jalankan di phpMyAdmin Laragon

CREATE DATABASE IF NOT EXISTS forum_diskusi_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE forum_diskusi_db;

CREATE TABLE tb_user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    tgl_daftar DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE tb_kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(50) NOT NULL UNIQUE,
    deskripsi TEXT,
    icon VARCHAR(10) DEFAULT '📁'
);

CREATE TABLE tb_postingan (
    id_post INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_kategori INT NOT NULL,
    judul VARCHAR(200) NOT NULL,
    isi_pertanyaan TEXT NOT NULL,
    status ENUM('open','closed') DEFAULT 'open',
    tgl_posting DATETIME DEFAULT CURRENT_TIMESTAMP,
    tgl_edit DATETIME NULL,
    FOREIGN KEY (id_user) REFERENCES tb_user(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_kategori) REFERENCES tb_kategori(id_kategori) ON DELETE RESTRICT
);

CREATE TABLE tb_komentar (
    id_komentar INT AUTO_INCREMENT PRIMARY KEY,
    id_post INT NOT NULL,
    id_user INT NOT NULL,
    isi_komentar TEXT NOT NULL,
    is_best_answer TINYINT(1) DEFAULT 0,
    tgl_komentar DATETIME DEFAULT CURRENT_TIMESTAMP,
    tgl_edit DATETIME NULL,
    FOREIGN KEY (id_post) REFERENCES tb_postingan(id_post) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES tb_user(id_user) ON DELETE CASCADE
);

CREATE TABLE tb_like (
    id_like INT AUTO_INCREMENT PRIMARY KEY,
    id_post INT NOT NULL,
    id_user INT NOT NULL,
    UNIQUE KEY unique_like (id_post, id_user),
    FOREIGN KEY (id_post) REFERENCES tb_postingan(id_post) ON DELETE CASCADE,
    FOREIGN KEY (id_user) REFERENCES tb_user(id_user) ON DELETE CASCADE
);

-- Data awal
INSERT INTO tb_user (username, email, password, role) VALUES
('admin',      'admin@forum.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('budi_s',     'budi@mail.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('siti_r',     'siti@mail.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('andi_p',     'andi@mail.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user');

-- password semua: 'password'

INSERT INTO tb_kategori (nama_kategori, deskripsi, icon) VALUES
('Akademik',    'Diskusi seputar mata kuliah dan tugas.',     '📚'),
('Teknologi',   'Tren IT, coding, dan troubleshooting.',      '💻'),
('Info Magang', 'Lowongan, tips, dan pengalaman magang.',     '💼'),
('Organisasi',  'Kegiatan kemahasiswaan dan kampus.',         '🏛️'),
('Umum',        'Diskusi bebas seputar kehidupan kampus.',    '💬');

INSERT INTO tb_postingan (id_user, id_kategori, judul, isi_pertanyaan) VALUES
(2, 1, 'Apa perbedaan DDL dan DML di MySQL?', 'Halo teman-teman, saya masih bingung membedakan DDL dan DML di MySQL. Bisa tolong jelaskan beserta contohnya?'),
(3, 1, 'Cara menggunakan INNER JOIN 3 tabel?', 'Bagaimana sintaks INNER JOIN jika melibatkan 3 tabel? Saya sudah coba tapi hasilnya tidak sesuai.'),
(4, 2, 'Rekomendasi framework PHP untuk pemula?', 'Saya ingin belajar framework PHP. Mana yang cocok untuk pemula, Laravel atau CodeIgniter?'),
(2, 3, 'Info lowongan magang IT semester ganjil 2026', 'Ada yang tahu info magang IT di Malang atau sekitarnya? Share ya, terima kasih!');

INSERT INTO tb_komentar (id_post, id_user, isi_komentar, is_best_answer) VALUES
(1, 3, 'DDL (Data Definition Language) untuk mendefinisikan struktur: CREATE, ALTER, DROP. DML (Data Manipulation Language) untuk data: INSERT, UPDATE, DELETE, SELECT.', 1),
(1, 4, 'Singkatnya: DDL = struktur tabel, DML = isi data.', 0),
(2, 4, 'Gunakan: FROM tabelA INNER JOIN tabelB ON A.id=B.a_id INNER JOIN tabelC ON B.id=C.b_id', 1),
(3, 2, 'Untuk pemula saya sarankan CodeIgniter karena lebih ringan.', 0),
(3, 3, 'Kalau mau langsung kerja, Laravel lebih banyak dipakai industri.', 1);

INSERT INTO tb_like (id_post, id_user) VALUES (1,3),(1,4),(2,2),(3,2),(3,4);
