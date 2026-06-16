<?php
require_once 'config/db.php';
if (!isset($_SESSION['user_id'])) { $_SESSION['msg']='Silakan masuk dulu.';$_SESSION['msg_type']='error';redirect('auth/login.php'); }
$uid = (int)$_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tb_user WHERE id_user=$uid"));
$page_title = 'Profil - '.$user['username'];

$total_post = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_postingan WHERE id_user=$uid"))['c'];
$total_kom  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_komentar WHERE id_user=$uid"))['c'];
$total_like = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_like l JOIN tb_postingan p ON p.id_post=l.id_post WHERE p.id_user=$uid"))['c'];

$posts = mysqli_query($conn,
  "SELECT p.*,k.nama_kategori,k.icon,
   (SELECT COUNT(*) FROM tb_komentar WHERE id_post=p.id_post) tc
   FROM tb_postingan p JOIN tb_kategori k ON k.id_kategori=p.id_kategori
   WHERE p.id_user=$uid ORDER BY p.tgl_posting DESC LIMIT 5");

$error=$success='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $username = trim($_POST['username']??'');
    $email    = trim($_POST['email']??'');
    $pass_old = $_POST['pass_old']??'';
    $pass_new = $_POST['pass_new']??'';
    if (!$username||!$email) { $error='Username dan email wajib diisi.'; }
    elseif (!filter_var($email,FILTER_VALIDATE_EMAIL)) { $error='Format email tidak valid.'; }
    else {
        $u=mysqli_real_escape_string($conn,$username);
        $e=mysqli_real_escape_string($conn,$email);
        $cek=mysqli_fetch_assoc(mysqli_query($conn,"SELECT id_user FROM tb_user WHERE (username='$u' OR email='$e') AND id_user!=$uid LIMIT 1"));
        if ($cek) { $error='Username atau email sudah digunakan akun lain.'; }
        else {
            if ($pass_old&&$pass_new) {
                if (!password_verify($pass_old,$user['password'])) { $error='Password lama salah.'; goto end; }
                if (strlen($pass_new)<6) { $error='Password baru minimal 6 karakter.'; goto end; }
                $hash=password_hash($pass_new,PASSWORD_DEFAULT);
                mysqli_query($conn,"UPDATE tb_user SET username='$u',email='$e',password='$hash' WHERE id_user=$uid");
            } else {
                mysqli_query($conn,"UPDATE tb_user SET username='$u',email='$e' WHERE id_user=$uid");
            }
            $_SESSION['username']=$username;
            $_SESSION['email']=$email;
            $success='Profil berhasil diperbarui!';
            $user=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tb_user WHERE id_user=$uid"));
        }
    }
    end:
}

require_once 'includes/header.php';
?>
<?php if ($error): ?><div class="alert alert-error"><i class="fa fa-circle-exclamation"></i> <?= $error ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><i class="fa fa-check-circle"></i> <?= $success ?></div><?php endif; ?>

<div class="profile-card">
  <div class="profile-header">
    <div class="profile-avatar"><?= getInitial($user['username']) ?></div>
    <div class="profile-info">
      <h2><?= clean($user['username']) ?></h2>
      <p><i class="fa fa-envelope"></i> <?= clean($user['email']) ?></p>
      <p style="margin-top:4px;"><span class="badge-role-<?= $user['role'] ?>"><?= ucfirst($user['role']) ?></span> &bull; Bergabung <?= date('d M Y',strtotime($user['tgl_daftar'])) ?></p>
    </div>
  </div>
  <div class="profile-stats">
    <div class="stat-item"><span class="stat-number"><?= $total_post ?></span><span class="stat-label">Postingan</span></div>
    <div class="stat-item"><span class="stat-number"><?= $total_kom ?></span><span class="stat-label">Jawaban</span></div>
    <div class="stat-item"><span class="stat-number"><?= $total_like ?></span><span class="stat-label">Disukai</span></div>
  </div>
</div>

<div class="section-card">
  <div class="section-title"><i class="fa fa-clock-rotate-left"></i> Postingan Terakhir</div>
  <?php if (mysqli_num_rows($posts)===0): ?>
  <p style="color:#8B92A8;font-size:.87rem;">Belum ada postingan.</p>
  <?php else: while($p=mysqli_fetch_assoc($posts)): ?>
  <div class="mini-post" onclick="location.href='<?= BASE_URL ?>/post/detail.php?id=<?= $p['id_post'] ?>'">
    <div>
      <div class="mini-post-title"><?= clean($p['judul']) ?></div>
      <div style="font-size:.75rem;color:#8B92A8;margin-top:2px;"><?= $p['icon'] ?> <?= clean($p['nama_kategori']) ?> &bull; <?= timeAgo($p['tgl_posting']) ?></div>
    </div>
    <span class="badge-status-<?= $p['status'] ?>"><?= $p['status']==='open'?'Aktif':'Selesai' ?></span>
  </div>
  <?php endwhile; endif; ?>
</div>

<div class="section-card">
  <div class="section-title"><i class="fa fa-pen"></i> Edit Profil</div>
  <form method="POST">
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
      <div class="form-group">
        <label class="form-label">Username</label>
        <input type="text" name="username" class="form-control" value="<?= clean($user['username']) ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="<?= clean($user['email']) ?>" required>
      </div>
    </div>
    <p style="font-size:.82rem;color:#8B92A8;margin-bottom:12px;">Isi kolom berikut hanya jika ingin mengganti password:</p>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
      <div class="form-group">
        <label class="form-label">Password Lama</label>
        <input type="password" name="pass_old" class="form-control" placeholder="Password saat ini">
      </div>
      <div class="form-group">
        <label class="form-label">Password Baru</label>
        <input type="password" name="pass_new" class="form-control" placeholder="Min. 6 karakter">
      </div>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan Perubahan</button>
  </form>
</div>

<?php require_once 'includes/footer.php'; ?>
