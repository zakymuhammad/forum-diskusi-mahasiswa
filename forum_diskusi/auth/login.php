<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/config/db.php';
if (isset($_SESSION['user_id'])) redirect('');
$page_title = 'Masuk';
$error = '';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $email = trim($_POST['email']??'');
    $pass  = $_POST['password']??'';
    if (!$email||!$pass) {
        $error = 'Email dan password wajib diisi.';
    } else {
        $e = mysqli_real_escape_string($conn,$email);
        $row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tb_user WHERE email='$e' LIMIT 1"));
        if ($row && password_verify($pass,$row['password'])) {
            $_SESSION['user_id'] = $row['id_user'];
            $_SESSION['username']= $row['username'];
            $_SESSION['email']   = $row['email'];
            $_SESSION['role']    = $row['role'];
            $_SESSION['msg']     = 'Selamat datang, '.$row['username'].'!';
            $_SESSION['msg_type']= 'success';
            redirect('');
        } else {
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Masuk &mdash; ForumKu</title>
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
    <h2 class="auth-title">Masuk ke Akun</h2>
    <?php if ($error): ?>
    <div class="alert alert-error"><i class="fa fa-circle-exclamation"></i> <?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="email@contoh.com" value="<?= clean($_POST['email']??'') ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Password kamu" required>
      </div>
      <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
        <i class="fa fa-right-to-bracket"></i> Masuk
      </button>
    </form>
    <div class="demo-box">
      <strong>&#128274; Akun Demo:</strong>
      <span>Admin &mdash; admin@forum.com / password</span>
      <span>User &nbsp;&mdash; budi@mail.com / password</span>
    </div>
    <p class="auth-footer">Belum punya akun? <a href="<?= BASE_URL ?>/auth/register.php">Daftar sekarang</a></p>
  </div>
</div>
</body>
</html>
