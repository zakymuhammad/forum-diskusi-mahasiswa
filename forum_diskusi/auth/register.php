<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/config/db.php';
if (isset($_SESSION['user_id'])) redirect('');
$page_title = 'Daftar';
$error = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $username = trim($_POST['username']??'');
    $email    = trim($_POST['email']??'');
    $pass     = $_POST['password']??'';
    $pass2    = $_POST['password2']??'';
    if (!$username||!$email||!$pass||!$pass2) {
        $error = 'Semua kolom wajib diisi.';
    } elseif (strlen($username)<3) {
        $error = 'Username minimal 3 karakter.';
    } elseif (!filter_var($email,FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($pass)<6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($pass!==$pass2) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $u = mysqli_real_escape_string($conn,$username);
        $e = mysqli_real_escape_string($conn,$email);
        $cek = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id_user FROM tb_user WHERE username='$u' OR email='$e' LIMIT 1"));
        if ($cek) {
            $error = 'Username atau email sudah digunakan.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            mysqli_query($conn,"INSERT INTO tb_user (username,email,password) VALUES ('$u','$e','$hash')");
            $_SESSION['msg']     = 'Akun berhasil dibuat! Silakan masuk.';
            $_SESSION['msg_type']= 'success';
            redirect('auth/login.php');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Daftar &mdash; ForumKu</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
</head>
<body>
<div class="auth-wrapper">
  <div class="auth-card">
    <div class="auth-logo">
      <span class="logo-icon">&#128172;</span>
      <h1>ForumKu</h1>
      <p>Forum Diskusi Mahasiswa</p>
    </div>
    <h2 class="auth-title">Buat Akun Baru</h2>
    <?php if ($error): ?>
    <div class="alert alert-error"><i class="fa fa-circle-exclamation"></i> <?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" placeholder="Nama pengguna" value="<?= clean($_POST['username']??'') ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="email@contoh.com" value="<?= clean($_POST['email']??'') ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required>
      </div>
      <div class="form-group">
        <label class="form-label">Konfirmasi Password</label>
        <input type="password" name="password2" class="form-control" placeholder="Ulangi password" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
        <i class="fa fa-user-plus"></i> Daftar Sekarang
      </button>
    </form>
    <p class="auth-footer">Sudah punya akun? <a href="<?= BASE_URL ?>/auth/login.php">Masuk di sini</a></p>
  </div>
</div>
</body>
</html>
