<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/config/db.php';
$page_title = 'Kelola Kategori';
$error = '';

if (isset($_GET['del'])) {
    $del = (int)$_GET['del'];
    $cek = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_postingan WHERE id_kategori=$del"));
    if ($cek['c']>0) {
        $_SESSION['msg']='Tidak bisa hapus, kategori masih digunakan oleh '.$cek['c'].' post.';$_SESSION['msg_type']='error';
    } else {
        mysqli_query($conn,"DELETE FROM tb_kategori WHERE id_kategori=$del");
        $_SESSION['msg']='Kategori dihapus.';$_SESSION['msg_type']='success';
    }
    redirect('admin/categories.php');
}

$edit_kat = null;
if (isset($_GET['edit'])) {
    $edit_kat = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tb_kategori WHERE id_kategori=".(int)$_GET['edit']));
}

if ($_SERVER['REQUEST_METHOD']==='POST') {
    $nama    = trim($_POST['nama']??'');
    $desk    = trim($_POST['deskripsi']??'');
    $icon    = trim($_POST['icon']??'📁');
    $edit_id = (int)($_POST['edit_id']??0);
    if (!$nama) { $error='Nama kategori wajib diisi.'; }
    else {
        $n=mysqli_real_escape_string($conn,$nama);
        $d=mysqli_real_escape_string($conn,$desk);
        $ic=mysqli_real_escape_string($conn,$icon);
        if ($edit_id) {
            mysqli_query($conn,"UPDATE tb_kategori SET nama_kategori='$n',deskripsi='$d',icon='$ic' WHERE id_kategori=$edit_id");
            $_SESSION['msg']='Kategori diperbarui.';$_SESSION['msg_type']='success';
        } else {
            $cek=mysqli_fetch_assoc(mysqli_query($conn,"SELECT id_kategori FROM tb_kategori WHERE nama_kategori='$n' LIMIT 1"));
            if ($cek) { $error='Nama kategori sudah ada.'; goto show; }
            mysqli_query($conn,"INSERT INTO tb_kategori (nama_kategori,deskripsi,icon) VALUES ('$n','$d','$ic')");
            $_SESSION['msg']='Kategori ditambahkan.';$_SESSION['msg_type']='success';
        }
        redirect('admin/categories.php');
    }
}
show:
$kats = mysqli_query($conn,
  "SELECT k.*,(SELECT COUNT(*) FROM tb_postingan WHERE id_kategori=k.id_kategori) jml
   FROM tb_kategori k ORDER BY k.nama_kategori");

require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/includes/admin_header.php';
?>

<div class="admin-page-header">
  <div>
    <h1><i class="fa fa-tags"></i> Kelola Kategori</h1>
    <p>Tambah, edit, atau hapus kategori forum</p>
  </div>
</div>
<div class="admin-breadcrumb">
  <a href="<?= BASE_URL ?>/admin/index.php">Dashboard</a>
  <i class="fa fa-chevron-right" style="font-size:.6rem;"></i>
  <span>Kelola Kategori</span>
</div>

<div class="admin-grid-auto">

  <!-- Form Tambah/Edit -->
  <div class="admin-form-card">
    <div class="admin-form-title">
      <?php if ($edit_kat): ?>
      <i class="fa fa-pen" style="color:#F98513;"></i> Edit Kategori
      <?php else: ?>
      <i class="fa fa-plus-circle" style="color:#2e7d32;"></i> Tambah Kategori
      <?php endif; ?>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error"><i class="fa fa-circle-exclamation"></i> <?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <?php if ($edit_kat): ?>
      <input type="hidden" name="edit_id" value="<?= $edit_kat['id_kategori'] ?>">
      <?php endif; ?>

      <div class="form-group">
        <label class="form-label">Icon (emoji) <span style="color:#8B92A8;font-weight:400;">&#8212; contoh: 📚 💻 🏛️</span></label>
        <input type="text" name="icon" class="form-control"
          value="<?= clean($edit_kat?$edit_kat['icon']:(isset($_POST['icon'])?$_POST['icon']:'📁')) ?>"
          placeholder="📁" maxlength="5"
          style="font-size:1.5rem;padding:8px 12px;width:80px;text-align:center;">
      </div>

      <div class="form-group">
        <label class="form-label">Nama Kategori <span style="color:#c62828;">*</span></label>
        <input type="text" name="nama" class="form-control"
          value="<?= clean($edit_kat?$edit_kat['nama_kategori']:(isset($_POST['nama'])?$_POST['nama']:'')) ?>"
          placeholder="cth. Akademik, Teknologi..." required>
      </div>

      <div class="form-group">
        <label class="form-label">Deskripsi <span style="color:#8B92A8;font-weight:400;">(opsional)</span></label>
        <textarea name="deskripsi" class="form-control" rows="3"
          placeholder="Deskripsi singkat kategori ini..."><?= clean($edit_kat?$edit_kat['deskripsi']:(isset($_POST['deskripsi'])?$_POST['deskripsi']:'')) ?></textarea>
      </div>

      <div style="display:flex;gap:8px;">
        <button type="submit" class="btn <?= $edit_kat?'btn-accent':'btn-primary' ?>">
          <i class="fa fa-<?= $edit_kat?'save':'plus' ?>"></i>
          <?= $edit_kat?'Simpan Perubahan':'Tambah Kategori' ?>
        </button>
        <?php if ($edit_kat): ?>
        <a href="<?= BASE_URL ?>/admin/categories.php" class="btn btn-outline">Batal</a>
        <?php endif; ?>
      </div>
    </form>
  </div>

  <!-- Daftar Kategori -->
  <div>
    <div class="admin-card" style="padding:0;">
      <div style="padding:16px 18px;border-bottom:1px solid #F4F1EC;">
        <span class="admin-card-title"><i class="fa fa-list"></i> Daftar Kategori</span>
      </div>
      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th style="padding-left:18px;">#</th>
              <th>Kategori</th>
              <th>Deskripsi</th>
              <th>Post</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
          <?php $no=1; while($k=mysqli_fetch_assoc($kats)): ?>
          <tr>
            <td style="padding-left:18px;color:#8B92A8;font-size:.82rem;"><?= $no++ ?></td>
            <td>
              <div style="display:flex;align-items:center;gap:8px;">
                <span style="font-size:1.3rem;"><?= $k['icon'] ?></span>
                <span style="font-weight:700;font-size:.88rem;"><?= clean($k['nama_kategori']) ?></span>
              </div>
            </td>
            <td style="font-size:.82rem;color:#8B92A8;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
              <?= clean($k['deskripsi'])?:'-' ?>
            </td>
            <td>
              <span style="background:#EEF1F8;color:#223382;font-weight:700;font-size:.8rem;padding:3px 9px;border-radius:10px;"><?= $k['jml'] ?></span>
            </td>
            <td>
              <div style="display:flex;gap:5px;">
                <a href="?edit=<?= $k['id_kategori'] ?>" class="btn-sm"><i class="fa fa-pen"></i> Edit</a>
                <a href="?del=<?= $k['id_kategori'] ?>" class="btn-sm danger"
                  onclick="return confirmDelete('Hapus kategori <?= clean($k['nama_kategori']) ?>?<?= $k['jml']>0?' Tidak bisa dihapus, masih ada '.$k['jml'].' post.':'' ?>')">
                  <i class="fa fa-trash"></i>
                </a>
              </div>
            </td>
          </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/includes/admin_footer.php'; ?>
