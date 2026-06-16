<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/config/db.php';
if (!isset($_SESSION['user_id'])) redirect('auth/login.php');
$id   = isset($_GET['id'])?(int)$_GET['id']:0;
$post_id = isset($_GET['post'])?(int)$_GET['post']:0;
if (!$id||!$post_id) redirect('');
$kom = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tb_komentar WHERE id_komentar=$id"));
if (!$kom||($kom['id_user']!=$_SESSION['user_id']&&$_SESSION['role']!=='admin')) redirect('post/detail.php?id='.$post_id);
$error='';
$page_title='Edit Komentar';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $isi = trim($_POST['isi_komentar']??'');
    if (!$isi) { $error='Komentar tidak boleh kosong.'; }
    else {
        $i=mysqli_real_escape_string($conn,$isi);
        mysqli_query($conn,"UPDATE tb_komentar SET isi_komentar='$i',tgl_edit=NOW() WHERE id_komentar=$id");
        $_SESSION['msg']='Komentar berhasil diperbarui.';
        $_SESSION['msg_type']='success';
        redirect('post/detail.php?id='.$post_id);
    }
}
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/includes/header.php';
?>
<div class="page-header">
  <h1 class="page-title">&#9999;&#65039; Edit Komentar</h1>
  <a href="<?= BASE_URL ?>/post/detail.php?id=<?= $post_id ?>" class="btn btn-outline"><i class="fa fa-arrow-left"></i> Kembali</a>
</div>
<?php if ($error): ?><div class="alert alert-error"><?= $error ?></div><?php endif; ?>
<div class="form-card-wide">
  <form method="POST">
    <div class="form-group">
      <label class="form-label">Isi Komentar</label>
      <textarea name="isi_komentar" class="form-control" rows="6" required><?= clean(isset($_POST['isi_komentar'])?$_POST['isi_komentar']:$kom['isi_komentar']) ?></textarea>
    </div>
    <div style="display:flex;gap:10px;">
      <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Simpan</button>
      <a href="<?= BASE_URL ?>/post/detail.php?id=<?= $post_id ?>" class="btn btn-outline">Batal</a>
    </div>
  </form>
</div>
<?php require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/includes/footer.php'; ?>
