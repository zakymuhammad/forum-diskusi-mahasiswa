<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/config/db.php';
if (!isset($_SESSION['user_id'])) redirect('auth/login.php');
$id = isset($_GET['id'])?(int)$_GET['id']:0;
if(!$id) redirect('');

$post = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tb_postingan WHERE id_post=$id"));
if (!$post) redirect('');
if ($post['id_user']!=$_SESSION['user_id'] && $_SESSION['role']!=='admin') redirect('post/detail.php?id='.$id);

$kats  = mysqli_query($conn,"SELECT * FROM tb_kategori ORDER BY nama_kategori ASC");
$error = '';
$page_title = 'Edit Postingan';

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $judul = trim($_POST['judul']??'');
    $isi   = trim($_POST['isi']??'');
    $kid   = (int)($_POST['id_kategori']??0);
    if (!$judul||!$isi||!$kid) { $error='Semua kolom wajib diisi.'; }
    else {
        $j=mysqli_real_escape_string($conn,$judul);
        $i=mysqli_real_escape_string($conn,$isi);
        mysqli_query($conn,"UPDATE tb_postingan SET judul='$j',isi_pertanyaan='$i',id_kategori=$kid,tgl_edit=NOW() WHERE id_post=$id");
        $_SESSION['msg']='Postingan berhasil diperbarui.';
        $_SESSION['msg_type']='success';
        redirect('post/detail.php?id='.$id);
    }
}

require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/includes/header.php';
?>
<div class="page-header">
  <h1 class="page-title">&#9999;&#65039; Edit Postingan</h1>
  <a href="<?= BASE_URL ?>/post/detail.php?id=<?= $id ?>" class="btn btn-outline"><i class="fa fa-arrow-left"></i> Kembali</a>
</div>
<?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
<div class="form-card-wide">
  <form method="POST">
    <div class="form-group">
      <label class="form-label">Kategori</label>
      <select name="id_kategori" class="form-control" required>
        <?php while($k=mysqli_fetch_assoc($kats)): ?>
        <option value="<?= $k['id_kategori'] ?>" <?= $k['id_kategori']==$post['id_kategori']?'selected':'' ?>><?= $k['icon'] ?> <?= clean($k['nama_kategori']) ?></option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="form-group">
      <label class="form-label">Judul</label>
      <input type="text" name="judul" class="form-control" value="<?= clean(isset($_POST['judul'])?$_POST['judul']:$post['judul']) ?>" required>
    </div>
    <div class="form-group">
      <label class="form-label">Detail Pertanyaan</label>
      <textarea name="isi" class="form-control" rows="8" required><?= clean(isset($_POST['isi'])?$_POST['isi']:$post['isi_pertanyaan']) ?></textarea>
    </div>
    <div style="display:flex;gap:10px;">
      <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
      <a href="<?= BASE_URL ?>/post/detail.php?id=<?= $id ?>" class="btn btn-outline">Batal</a>
    </div>
  </form>
</div>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/includes/footer.php'; ?>
