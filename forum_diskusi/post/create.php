<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/config/db.php';
if (!isset($_SESSION['user_id'])) { $_SESSION['msg']='Silakan masuk terlebih dahulu.';$_SESSION['msg_type']='error';redirect('auth/login.php'); }
$page_title = 'Buat Postingan';
$error = '';

$kats = mysqli_query($conn, "SELECT * FROM tb_kategori ORDER BY nama_kategori ASC");

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $judul  = trim($_POST['judul']??'');
    $isi    = trim($_POST['isi']??'');
    $kat_id = (int)($_POST['id_kategori']??0);
    if (!$judul||!$isi||!$kat_id) {
        $error = 'Semua kolom wajib diisi.';
    } elseif (strlen($judul)<5) {
        $error = 'Judul minimal 5 karakter.';
    } elseif (strlen($isi)<10) {
        $error = 'Isi pertanyaan minimal 10 karakter.';
    } else {
        $uid  = (int)$_SESSION['user_id'];
        $j    = mysqli_real_escape_string($conn,$judul);
        $i    = mysqli_real_escape_string($conn,$isi);
        mysqli_query($conn,"INSERT INTO tb_postingan (id_user,id_kategori,judul,isi_pertanyaan) VALUES ($uid,$kat_id,'$j','$i')");
        $new_id = mysqli_insert_id($conn);
        $_SESSION['msg']='Postingan berhasil dibuat!';
        $_SESSION['msg_type']='success';
        redirect('post/detail.php?id='.$new_id);
    }
}

require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/includes/header.php';
?>
<div class="page-header">
  <div>
    <h1 class="page-title">&#128221; Buat Postingan Baru</h1>
    <p class="page-subtitle">Ajukan pertanyaan atau mulai diskusi</p>
  </div>
  <a href="<?= BASE_URL ?>" class="btn btn-outline"><i class="fa fa-arrow-left"></i> Kembali</a>
</div>

<?php if ($error): ?>
<div class="alert alert-error"><i class="fa fa-circle-exclamation"></i> <?= $error ?></div>
<?php endif; ?>

<div class="form-card-wide">
  <form method="POST">
    <div class="form-group">
      <label class="form-label">Kategori <span style="color:#c62828">*</span></label>
      <select name="id_kategori" class="form-control" required>
        <option value="">-- Pilih Kategori --</option>
        <?php mysqli_data_seek($kats,0); while($k=mysqli_fetch_assoc($kats)): ?>
        <option value="<?= $k['id_kategori'] ?>" <?= (isset($_POST['id_kategori'])&&$_POST['id_kategori']==$k['id_kategori'])?'selected':'' ?>>
          <?= $k['icon'] ?> <?= clean($k['nama_kategori']) ?>
        </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">Judul Pertanyaan <span style="color:#c62828">*</span></label>
      <input type="text" name="judul" class="form-control" placeholder="Tulis judul yang jelas dan spesifik..." value="<?= clean($_POST['judul']??'') ?>" required>
    </div>
    <div class="form-group">
      <label class="form-label">Detail Pertanyaan <span style="color:#c62828">*</span></label>
      <textarea name="isi" class="form-control" rows="8" placeholder="Jelaskan pertanyaanmu secara detail agar mudah dipahami..."><?= clean($_POST['isi']??'') ?></textarea>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
      <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Kirim Postingan</button>
      <a href="<?= BASE_URL ?>" class="btn btn-outline">Batal</a>
    </div>
  </form>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/includes/footer.php'; ?>
