<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/config/db.php';
$page_title = 'Kelola Post';

if (isset($_GET['del'])) {
    mysqli_query($conn,"DELETE FROM tb_postingan WHERE id_post=".(int)$_GET['del']);
    $_SESSION['msg']='Post dihapus.';$_SESSION['msg_type']='success';
    redirect('admin/posts.php');
}
if (isset($_GET['toggle'])) {
    $tid = (int)$_GET['toggle'];
    $s = mysqli_fetch_assoc(mysqli_query($conn,"SELECT status FROM tb_postingan WHERE id_post=$tid"));
    $ns = $s['status']==='open'?'closed':'open';
    mysqli_query($conn,"UPDATE tb_postingan SET status='$ns' WHERE id_post=$tid");
    $_SESSION['msg']='Status diubah ke '.$ns.'.';$_SESSION['msg_type']='success';
    redirect('admin/posts.php');
}

$search = isset($_GET['q'])?trim($_GET['q']):''; 
$kat_id = isset($_GET['kat'])?(int)$_GET['kat']:0;
$status = isset($_GET['status'])?$_GET['status']:'';
$where  = '1=1';
if ($search) $where .= " AND p.judul LIKE '%".mysqli_real_escape_string($conn,$search)."%'";
if ($kat_id) $where .= " AND p.id_kategori=$kat_id";
if ($status==='open'||$status==='closed') $where .= " AND p.status='$status'";

$posts = mysqli_query($conn,
  "SELECT p.*,u.username,k.nama_kategori,k.icon,
   (SELECT COUNT(*) FROM tb_komentar WHERE id_post=p.id_post) tc,
   (SELECT COUNT(*) FROM tb_like WHERE id_post=p.id_post) tl
   FROM tb_postingan p
   JOIN tb_user u ON u.id_user=p.id_user
   JOIN tb_kategori k ON k.id_kategori=p.id_kategori
   WHERE $where ORDER BY p.tgl_posting DESC");
$total_rows = mysqli_num_rows($posts);
$kats = mysqli_query($conn,"SELECT * FROM tb_kategori ORDER BY nama_kategori");

require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/includes/admin_header.php';
?>

<div class="admin-page-header">
  <div>
    <h1><i class="fa fa-comments"></i> Kelola Post</h1>
    <p><?= $total_rows ?> post ditemukan</p>
  </div>
</div>
<div class="admin-breadcrumb">
  <a href="<?= BASE_URL ?>/admin/index.php">Dashboard</a>
  <i class="fa fa-chevron-right" style="font-size:.6rem;"></i>
  <span>Kelola Post</span>
</div>

<!-- Filter -->
<div class="admin-card" style="padding:14px 18px;">
  <form method="GET" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;">
    <input type="text" name="q" class="form-control" style="max-width:220px;" placeholder="&#128269; Cari judul..." value="<?= clean($search) ?>">
    <select name="kat" class="form-control" style="max-width:160px;">
      <option value="">Semua Kategori</option>
      <?php while($k=mysqli_fetch_assoc($kats)): ?>
      <option value="<?= $k['id_kategori'] ?>" <?= $kat_id===$k['id_kategori']?'selected':'' ?>><?= $k['icon'] ?> <?= clean($k['nama_kategori']) ?></option>
      <?php endwhile; ?>
    </select>
    <select name="status" class="form-control" style="max-width:140px;">
      <option value="">Semua Status</option>
      <option value="open" <?= $status==='open'?'selected':'' ?>>Aktif</option>
      <option value="closed" <?= $status==='closed'?'selected':'' ?>>Selesai</option>
    </select>
    <button type="submit" class="btn btn-primary" style="padding:9px 16px;"><i class="fa fa-magnifying-glass"></i> Cari</button>
    <?php if ($search||$kat_id||$status): ?>
    <a href="<?= BASE_URL ?>/admin/posts.php" class="btn btn-outline" style="padding:9px 16px;">Reset</a>
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
          <th>Judul</th>
          <th>Penulis</th>
          <th>Kategori</th>
          <th>Jawaban</th>
          <th>Suka</th>
          <th>Status</th>
          <th>Tanggal</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
      <?php $no=1; while($p=mysqli_fetch_assoc($posts)): ?>
      <tr>
        <td style="padding-left:18px;color:#8B92A8;font-size:.82rem;"><?= $no++ ?></td>
        <td style="max-width:220px;">
          <a href="<?= BASE_URL ?>/post/detail.php?id=<?= $p['id_post'] ?>" style="font-weight:600;color:#1a2260;font-size:.86rem;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" target="_blank">
            <?= clean($p['judul']) ?>
          </a>
        </td>
        <td>
          <div style="display:flex;align-items:center;gap:7px;">
            <div style="width:28px;height:28px;min-width:28px;background:linear-gradient(135deg,#223382,#3f51b5);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:.72rem;"><?= getInitial($p['username']) ?></div>
            <span style="font-size:.84rem;"><?= clean($p['username']) ?></span>
          </div>
        </td>
        <td><span class="badge-kategori"><?= $p['icon'] ?> <?= clean($p['nama_kategori']) ?></span></td>
        <td style="font-weight:700;color:#223382;text-align:center;"><?= $p['tc'] ?></td>
        <td style="font-weight:700;color:#e91e63;text-align:center;"><?= $p['tl'] ?></td>
        <td><span class="badge-status-<?= $p['status'] ?>"><?= $p['status']==='open'?'Aktif':'Selesai' ?></span></td>
        <td style="font-size:.8rem;color:#8B92A8;white-space:nowrap;"><?= date('d M Y',strtotime($p['tgl_posting'])) ?></td>
        <td>
          <div style="display:flex;gap:5px;">
            <a href="<?= BASE_URL ?>/post/edit.php?id=<?= $p['id_post'] ?>" class="btn-sm" title="Edit"><i class="fa fa-pen"></i></a>
            <a href="?toggle=<?= $p['id_post'] ?>" class="btn-sm" title="<?= $p['status']==='open'?'Tutup':'Buka' ?>" onclick="return confirm('Ubah status post?')">
              <i class="fa fa-<?= $p['status']==='open'?'lock':'lock-open' ?>"></i>
            </a>
            <a href="?del=<?= $p['id_post'] ?>" class="btn-sm danger" title="Hapus" onclick="return confirmDelete('Hapus post ini? Semua komentar juga akan terhapus.')"><i class="fa fa-trash"></i></a>
          </div>
        </td>
      </tr>
      <?php endwhile; ?>
      <?php if ($total_rows===0): ?>
      <tr><td colspan="9" style="text-align:center;padding:32px;color:#8B92A8;">Tidak ada post ditemukan.</td></tr>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/includes/admin_footer.php'; ?>
