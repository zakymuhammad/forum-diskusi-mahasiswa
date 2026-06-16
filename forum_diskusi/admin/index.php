<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/config/db.php';
$page_title = 'Dashboard';

$total_user  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_user"))['c'];
$total_post  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_postingan"))['c'];
$total_kom   = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_komentar"))['c'];
$total_like  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_like"))['c'];
$post_open   = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_postingan WHERE status='open'"))['c'];
$post_closed = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_postingan WHERE status='closed'"))['c'];
$user_baru   = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_user WHERE tgl_daftar >= DATE_SUB(NOW(), INTERVAL 7 DAY)"))['c'];
$admin_count = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_user WHERE role='admin'"))['c'];
$user_count  = $total_user - $admin_count;

$recent_posts = mysqli_query($conn,
  "SELECT p.*,u.username,k.nama_kategori,k.icon,
   (SELECT COUNT(*) FROM tb_komentar WHERE id_post=p.id_post) tc,
   (SELECT COUNT(*) FROM tb_like WHERE id_post=p.id_post) tl
   FROM tb_postingan p
   JOIN tb_user u ON u.id_user=p.id_user
   JOIN tb_kategori k ON k.id_kategori=p.id_kategori
   ORDER BY p.tgl_posting DESC LIMIT 5");

$recent_users = mysqli_query($conn,
  "SELECT u.*,(SELECT COUNT(*) FROM tb_postingan WHERE id_user=u.id_user) jp
   FROM tb_user u ORDER BY tgl_daftar DESC LIMIT 5");

require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/includes/admin_header.php';
?>

<!-- Page Header -->
<div class="admin-page-header">
  <div>
    <h1>&#128202; Dashboard</h1>
    <p>Selamat datang, <strong><?= clean($_SESSION['username']) ?></strong> &mdash; <?= date('l, d F Y') ?></p>
  </div>
  <a href="<?= BASE_URL ?>/post/create.php" class="btn btn-accent"><i class="fa fa-plus"></i> Buat Post Baru</a>
</div>

<!-- Stat Cards -->
<div class="admin-stat-row">
  <div class="astat blue">
    <div class="astat-accent"></div>
    <div class="astat-inner">
      <div class="astat-label">Total User</div>
      <div class="astat-row">
        <div class="astat-num"><?= $total_user ?></div>
        <div class="astat-icon"><i class="fa fa-users"></i></div>
      </div>
      <div class="astat-sub"><span class="hi g">+<?= $user_baru ?></span> user baru minggu ini</div>
    </div>
  </div>
  <div class="astat green">
    <div class="astat-accent"></div>
    <div class="astat-inner">
      <div class="astat-label">Total Post</div>
      <div class="astat-row">
        <div class="astat-num"><?= $total_post ?></div>
        <div class="astat-icon"><i class="fa fa-comments"></i></div>
      </div>
      <div class="astat-sub">
        <span class="hi g"><?= $post_open ?> aktif</span> &nbsp;
        <span class="hi r"><?= $post_closed ?> selesai</span>
      </div>
    </div>
  </div>
  <div class="astat orange">
    <div class="astat-accent"></div>
    <div class="astat-inner">
      <div class="astat-label">Total Komentar</div>
      <div class="astat-row">
        <div class="astat-num"><?= $total_kom ?></div>
        <div class="astat-icon"><i class="fa fa-comment-dots"></i></div>
      </div>
      <div class="astat-sub">Rata-rata <span class="hi o"><?= $total_post>0?number_format($total_kom/$total_post,1):'0' ?></span> per post</div>
    </div>
  </div>
  <div class="astat pink">
    <div class="astat-accent"></div>
    <div class="astat-inner">
      <div class="astat-label">Total Suka</div>
      <div class="astat-row">
        <div class="astat-num"><?= $total_like ?></div>
        <div class="astat-icon"><i class="fa fa-heart"></i></div>
      </div>
      <div class="astat-sub">Di <?= $total_post ?> postingan</div>
    </div>
  </div>
</div>

<!-- Content Grid -->
<div class="admin-grid-2">

  <!-- Post Terbaru -->
  <div class="admin-card">
    <div class="admin-card-header">
      <span class="admin-card-title"><i class="fa fa-clock-rotate-left"></i> Post Terbaru</span>
      <a href="<?= BASE_URL ?>/admin/posts.php">Lihat semua &rarr;</a>
    </div>
    <?php while($p=mysqli_fetch_assoc($recent_posts)): ?>
    <div class="post-row-admin" onclick="location.href='<?= BASE_URL ?>/post/detail.php?id=<?= $p['id_post'] ?>'">
      <div class="post-row-avt"><?= getInitial($p['username']) ?></div>
      <div style="flex:1;min-width:0;">
        <div style="font-size:.86rem;font-weight:600;color:#1a2260;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= clean($p['judul']) ?></div>
        <div style="font-size:.75rem;color:#8B92A8;margin-top:2px;display:flex;gap:8px;flex-wrap:wrap;">
          <span><i class="fa fa-user"></i> <?= clean($p['username']) ?></span>
          <span><?= $p['icon'] ?> <?= clean($p['nama_kategori']) ?></span>
          <span><i class="fa fa-comment"></i> <?= $p['tc'] ?></span>
          <span><i class="fa fa-heart"></i> <?= $p['tl'] ?></span>
        </div>
      </div>
      <div style="display:flex;flex-direction:column;align-items:flex-end;gap:3px;flex-shrink:0;">
        <span class="badge-status-<?= $p['status'] ?>"><?= $p['status']==='open'?'Aktif':'Selesai' ?></span>
        <span style="font-size:.72rem;color:#8B92A8;"><?= timeAgo($p['tgl_posting']) ?></span>
      </div>
    </div>
    <?php endwhile; ?>
  </div>

  <!-- Anggota Terbaru -->
  <div class="admin-card">
    <div class="admin-card-header">
      <span class="admin-card-title"><i class="fa fa-user-plus"></i> Anggota Terbaru</span>
      <a href="<?= BASE_URL ?>/admin/users.php">Lihat semua &rarr;</a>
    </div>
    <?php while($u=mysqli_fetch_assoc($recent_users)): ?>
    <div class="user-row-admin">
      <div class="user-row-avt"><?= getInitial($u['username']) ?></div>
      <div style="flex:1;min-width:0;">
        <div style="font-size:.86rem;font-weight:700;color:#1a2260;">
          <?= clean($u['username']) ?>
          <span class="badge-role-<?= $u['role'] ?>" style="margin-left:5px;"><?= ucfirst($u['role']) ?></span>
        </div>
        <div style="font-size:.75rem;color:#8B92A8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"><?= clean($u['email']) ?></div>
      </div>
      <div style="text-align:right;flex-shrink:0;">
        <div style="font-size:.82rem;font-weight:700;color:#223382;"><?= $u['jp'] ?> post</div>
        <div style="font-size:.72rem;color:#8B92A8;"><?= date('d M',strtotime($u['tgl_daftar'])) ?></div>
      </div>
    </div>
    <?php endwhile; ?>

    <div style="margin-top:14px;padding-top:12px;border-top:1px solid #F4F1EC;display:grid;grid-template-columns:1fr 1fr;gap:10px;">
      <div style="background:#FFF3E0;border-radius:10px;padding:12px;text-align:center;">
        <div style="font-size:1.6rem;font-weight:900;color:#e65100;"><?= $admin_count ?></div>
        <div style="font-size:.75rem;color:#8B92A8;margin-top:2px;">Admin</div>
      </div>
      <div style="background:#EEF1F8;border-radius:10px;padding:12px;text-align:center;">
        <div style="font-size:1.6rem;font-weight:900;color:#223382;"><?= $user_count ?></div>
        <div style="font-size:.75rem;color:#8B92A8;margin-top:2px;">User Biasa</div>
      </div>
    </div>
  </div>
</div>

<!-- Ringkasan Diskusi -->
<div class="admin-card">
  <div class="admin-card-header">
    <span class="admin-card-title"><i class="fa fa-chart-bar"></i> Ringkasan Diskusi</span>
  </div>
  <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
    <div style="background:#F0F2F8;border-radius:12px;padding:16px;">
      <div style="font-size:.75rem;font-weight:700;color:#8B92A8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">Status Post</div>
      <?php $pct = $total_post>0?round($post_open/$total_post*100):0; ?>
      <div style="display:flex;justify-content:space-between;font-size:.82rem;margin-bottom:6px;">
        <span style="color:#2e7d32;font-weight:700;">Aktif <?= $post_open ?></span>
        <span style="color:#c62828;font-weight:700;">Selesai <?= $post_closed ?></span>
      </div>
      <div class="progress-bar-wrap"><div class="progress-bar-fill" style="background:linear-gradient(90deg,#2e7d32,#43a047);width:<?= $pct ?>%;"></div></div>
      <div style="font-size:.75rem;color:#8B92A8;margin-top:5px;"><?= $pct ?>% masih aktif</div>
    </div>
    <div style="background:#F0F2F8;border-radius:12px;padding:16px;">
      <div style="font-size:.75rem;font-weight:700;color:#8B92A8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Rata-rata Jawaban</div>
      <div style="font-size:2.2rem;font-weight:900;color:#F98513;"><?= $total_post>0?number_format($total_kom/$total_post,1):'0' ?></div>
      <div style="font-size:.82rem;color:#8B92A8;margin-top:4px;">komentar per diskusi</div>
    </div>
    <div style="background:#F0F2F8;border-radius:12px;padding:16px;">
      <div style="font-size:.75rem;font-weight:700;color:#8B92A8;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px;">Rata-rata Suka</div>
      <div style="font-size:2.2rem;font-weight:900;color:#e91e63;"><?= $total_post>0?number_format($total_like/$total_post,1):'0' ?></div>
      <div style="font-size:.82rem;color:#8B92A8;margin-top:4px;">suka per diskusi</div>
    </div>
  </div>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/includes/admin_footer.php'; ?>
