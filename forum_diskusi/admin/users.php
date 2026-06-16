<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/config/db.php';
$page_title = 'Kelola User';

if (isset($_GET['del'])) {
    $del = (int)$_GET['del'];
    if ($del!==$_SESSION['user_id']) {
        mysqli_query($conn,"DELETE FROM tb_user WHERE id_user=$del");
        $_SESSION['msg']='User berhasil dihapus.';$_SESSION['msg_type']='success';
    } else {
        $_SESSION['msg']='Tidak bisa menghapus akun sendiri.';$_SESSION['msg_type']='error';
    }
    redirect('admin/users.php');
}
if (isset($_GET['role'])) {
    $rid = (int)$_GET['role'];
    if ($rid!==$_SESSION['user_id']) {
        $cur = mysqli_fetch_assoc(mysqli_query($conn,"SELECT role FROM tb_user WHERE id_user=$rid"));
        $new = $cur['role']==='admin'?'user':'admin';
        mysqli_query($conn,"UPDATE tb_user SET role='$new' WHERE id_user=$rid");
        $_SESSION['msg']='Role diubah ke '.$new.'.';$_SESSION['msg_type']='success';
    }
    redirect('admin/users.php');
}

$search = isset($_GET['q'])?trim($_GET['q']):''; 
$filter = isset($_GET['role_filter'])?$_GET['role_filter']:'';
$where  = '1=1';
if ($search) $where .= " AND (u.username LIKE '%".mysqli_real_escape_string($conn,$search)."%' OR u.email LIKE '%".mysqli_real_escape_string($conn,$search)."%')";
if ($filter==='admin'||$filter==='user') $where .= " AND u.role='$filter'";

$users = mysqli_query($conn,
  "SELECT u.*,(SELECT COUNT(*) FROM tb_postingan WHERE id_user=u.id_user) jp,
   (SELECT COUNT(*) FROM tb_komentar WHERE id_user=u.id_user) jk
   FROM tb_user u WHERE $where ORDER BY u.tgl_daftar DESC");
$total_rows = mysqli_num_rows($users);

require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/includes/admin_header.php';
?>

<div class="admin-page-header">
  <div>
    <h1><i class="fa fa-users"></i> Kelola User</h1>
    <p><?= $total_rows ?> user ditemukan</p>
  </div>
</div>
<div class="admin-breadcrumb">
  <a href="<?= BASE_URL ?>/admin/index.php">Dashboard</a>
  <i class="fa fa-chevron-right" style="font-size:.6rem;"></i>
  <span>Kelola User</span>
</div>

<!-- Filter -->
<div class="admin-card" style="padding:14px 18px;">
  <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <input type="text" name="q" class="form-control" style="max-width:240px;" placeholder="&#128269; Cari username atau email..." value="<?= clean($search) ?>">
    <select name="role_filter" class="form-control" style="max-width:160px;">
      <option value="">Semua Role</option>
      <option value="admin" <?= $filter==='admin'?'selected':'' ?>>Admin</option>
      <option value="user" <?= $filter==='user'?'selected':'' ?>>User</option>
    </select>
    <button type="submit" class="btn btn-primary" style="padding:9px 16px;"><i class="fa fa-magnifying-glass"></i> Cari</button>
    <?php if ($search||$filter): ?>
    <a href="<?= BASE_URL ?>/admin/users.php" class="btn btn-outline" style="padding:9px 16px;">Reset</a>
    <?php endif; ?>
  </form>
</div>

<!-- Tabel -->
<div class="admin-card" style="padding:0;">
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th style="padding-left:18px;">#</th>
          <th>User</th>
          <th>Email</th>
          <th>Role</th>
          <th>Post</th>
          <th>Komentar</th>
          <th>Bergabung</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php $no=1; while($u=mysqli_fetch_assoc($users)): ?>
      <tr>
        <td style="padding-left:18px;color:#8B92A8;font-size:.82rem;"><?= $no++ ?></td>
        <td>
          <div style="display:flex;align-items:center;gap:9px;">
            <div style="width:34px;height:34px;min-width:34px;background:linear-gradient(135deg,#223382,#3f51b5);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.82rem;flex-shrink:0;"><?= getInitial($u['username']) ?></div>
            <div>
              <div style="font-weight:700;font-size:.88rem;"><?= clean($u['username']) ?></div>
              <?php if ($u['id_user']===$_SESSION['user_id']): ?>
              <span style="font-size:.72rem;color:#F98513;font-weight:600;">&#9733; Akun ini</span>
              <?php endif; ?>
            </div>
          </div>
        </td>
        <td style="font-size:.83rem;color:#8B92A8;"><?= clean($u['email']) ?></td>
        <td><span class="badge-role-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
        <td style="font-weight:700;color:#223382;"><?= $u['jp'] ?></td>
        <td style="font-weight:700;color:#223382;"><?= $u['jk'] ?></td>
        <td style="font-size:.82rem;color:#8B92A8;"><?= date('d M Y',strtotime($u['tgl_daftar'])) ?></td>
        <td>
          <?php if ($u['id_user']!==$_SESSION['user_id']): ?>
          <div style="display:flex;gap:5px;">
            <a href="?role=<?= $u['id_user'] ?>" class="btn-sm" onclick="return confirm('Ubah role user ini?')" title="<?= $u['role']==='admin'?'Jadikan User':'Jadikan Admin' ?>">
              <i class="fa fa-<?= $u['role']==='admin'?'user':'shield-halved' ?>"></i>
              <?= $u['role']==='admin'?'&rarr; User':'&rarr; Admin' ?>
            </a>
            <a href="?del=<?= $u['id_user'] ?>" class="btn-sm danger" onclick="return confirmDelete('Hapus user <?= clean($u['username']) ?>? Semua postingannya akan ikut terhapus.')">
              <i class="fa fa-trash"></i>
            </a>
          </div>
          <?php else: ?>
          <span style="font-size:.78rem;color:#8B92A8;">&#8212;</span>
          <?php endif; ?>
        </td>
      </tr>
      <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/includes/admin_footer.php'; ?>
