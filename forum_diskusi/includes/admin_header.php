<?php
if (!isset($conn)) require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/config/db.php';
if (!isset($_SESSION['user_id'])||$_SESSION['role']!=='admin') {
    $_SESSION['msg']='Akses ditolak.';$_SESSION['msg_type']='error';
    header('Location: '.BASE_URL.'/'); exit;
}
$cur_admin_page = basename($_SERVER['PHP_SELF']);

// Hitung badge
$total_user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_user"))['c'];
$total_post = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_postingan"))['c'];
$total_kat  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_kategori"))['c'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title><?= isset($page_title)?clean($page_title).' &mdash; Admin ForumKu':'Admin ForumKu' ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
  <script>const BASE_URL = '<?= BASE_URL ?>';</script>
</head>
<body class="admin-body">

<!-- ADMIN TOPBAR -->
<header class="admin-topbar">
  <div class="admin-topbar-left">
    <button class="admin-sidebar-toggle" onclick="toggleAdminSidebar()" id="sidebarToggle">
      <i class="fa fa-bars"></i>
    </button>
    <a href="<?= BASE_URL ?>/admin/index.php" class="admin-logo">
      <span class="admin-logo-icon">&#128737;&#65039;</span>
      <span class="admin-logo-text">ForumKu <span>Admin</span></span>
    </a>
  </div>
  <div class="admin-topbar-right">
    <a href="<?= BASE_URL ?>/" class="admin-topbar-btn" target="_blank">
      <i class="fa fa-arrow-up-right-from-square"></i> Lihat Forum
    </a>
    <div class="admin-user-chip" onclick="toggleAdminDropdown()" id="adminUserChip">
      <div class="admin-user-avatar"><?= getInitial($_SESSION['username']) ?></div>
      <span><?= clean($_SESSION['username']) ?></span>
      <i class="fa fa-chevron-down" style="font-size:.7rem;"></i>
      <div class="admin-user-dropdown" id="adminUserDropdown">
        <a href="<?= BASE_URL ?>/profile.php"><i class="fa fa-user"></i> Profil Saya</a>
        <div class="divider"></div>
        <a href="<?= BASE_URL ?>/auth/logout.php" class="danger"><i class="fa fa-right-from-bracket"></i> Logout</a>
      </div>
    </div>
  </div>
</header>

<div class="admin-shell">
  <!-- ADMIN SIDEBAR -->
  <aside class="admin-sidebar" id="adminSidebar">

    <div class="admin-sidebar-section">
      <span class="admin-sidebar-label">Utama</span>
      <a href="<?= BASE_URL ?>/admin/index.php" class="admin-sidebar-item <?= $cur_admin_page==='index.php'?'active':'' ?>">
        <i class="fa fa-gauge"></i> Dashboard
      </a>
    </div>

    <div class="admin-sidebar-section">
      <span class="admin-sidebar-label">Kelola Konten</span>
      <a href="<?= BASE_URL ?>/admin/posts.php" class="admin-sidebar-item <?= $cur_admin_page==='posts.php'?'active':'' ?>">
        <i class="fa fa-comments"></i> Postingan
        <span class="admin-badge"><?= $total_post ?></span>
      </a>
      <a href="<?= BASE_URL ?>/admin/categories.php" class="admin-sidebar-item <?= $cur_admin_page==='categories.php'?'active':'' ?>">
        <i class="fa fa-tags"></i> Kategori
        <span class="admin-badge"><?= $total_kat ?></span>
      </a>
    </div>

    <div class="admin-sidebar-section">
      <span class="admin-sidebar-label">Pengguna</span>
      <a href="<?= BASE_URL ?>/admin/users.php" class="admin-sidebar-item <?= $cur_admin_page==='users.php'?'active':'' ?>">
        <i class="fa fa-users"></i> Semua User
        <span class="admin-badge"><?= $total_user ?></span>
      </a>
    </div>

    <div class="admin-sidebar-footer">
      <a href="<?= BASE_URL ?>/" class="admin-sidebar-item">
        <i class="fa fa-house"></i> Kembali ke Forum
      </a>
    </div>
  </aside>

  <!-- ADMIN CONTENT -->
  <main class="admin-content">
    <?php if (isset($_SESSION['msg'])): ?>
    <div class="alert alert-<?= $_SESSION['msg_type']??'info' ?>"><?= $_SESSION['msg'] ?></div>
    <?php unset($_SESSION['msg'],$_SESSION['msg_type']); endif; ?>
