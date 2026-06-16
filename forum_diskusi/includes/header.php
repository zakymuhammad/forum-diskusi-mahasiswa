<?php
if (!isset($conn)) require_once $_SERVER['DOCUMENT_ROOT'] . '/forum_diskusi/config/db.php';
$cur_page = basename($_SERVER['PHP_SELF']);
$cur_dir  = basename(dirname($_SERVER['PHP_SELF']));
$kat_q = mysqli_query($conn, "SELECT * FROM tb_kategori ORDER BY nama_kategori ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($page_title) ? clean($page_title).' &mdash; ForumKu' : 'ForumKu' ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous">
  <script>const BASE_URL = '<?= BASE_URL ?>';</script>
</head>
<body>

<nav class="navbar">
  <!-- Kolom 1: Brand -->
  <a href="<?= BASE_URL ?>" class="navbar-brand">
    <span class="brand-icon">&#128172;</span>
    <span>ForumKu</span>
  </a>
  <!-- Kolom 2: Search -->
  <form class="navbar-search" action="<?= BASE_URL ?>/index.php" method="GET">
    <span class="search-icon"><i class="fa fa-magnifying-glass"></i></span>
    <input type="text" name="q" placeholder="Cari topik atau pertanyaan..."
           value="<?= isset($_GET['q']) ? clean($_GET['q']) : '' ?>">
  </form>
  <!-- Kolom 3: Actions (selalu 230px, konten berubah tapi lebar tetap) -->
  <div class="navbar-actions">
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="<?= BASE_URL ?>/post/create.php" class="btn-nav btn-nav-primary">
        <i class="fa fa-plus"></i> Buat Post
      </a>
      <div class="user-avatar" onclick="toggleDropdown()" id="userAvatar">
        <?= getInitial($_SESSION['username']) ?>
        <div class="user-dropdown" id="userDropdown">
          <a href="<?= BASE_URL ?>/profile.php"><i class="fa fa-user"></i> <?= clean($_SESSION['username']) ?></a>
          <?php if ($_SESSION['role'] === 'admin'): ?>
          <a href="<?= BASE_URL ?>/admin/index.php"><i class="fa fa-shield-halved"></i> Admin Panel</a>
          <?php endif; ?>
          <div class="divider"></div>
          <a href="<?= BASE_URL ?>/auth/logout.php" class="danger"><i class="fa fa-right-from-bracket"></i> Logout</a>
        </div>
      </div>
    <?php else: ?>
      <a href="<?= BASE_URL ?>/auth/login.php" class="btn-nav btn-nav-outline">Masuk</a>
      <a href="<?= BASE_URL ?>/auth/register.php" class="btn-nav btn-nav-primary">Daftar</a>
    <?php endif; ?>
  </div>
</nav>

<div class="layout">
  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="sidebar-section">
      <span class="sidebar-title">Menu</span>
      <nav class="sidebar-nav">
        <a href="<?= BASE_URL ?>/" class="<?= ($cur_page==='index.php' && $cur_dir!=='admin') ? 'active':'' ?>">
          <i class="fa fa-house"></i>&nbsp; Beranda
        </a>
        <?php if (isset($_SESSION['user_id'])): ?>
        <a href="<?= BASE_URL ?>/post/create.php" class="<?= $cur_page==='create.php'?'active':'' ?>">
          <i class="fa fa-pen-to-square"></i>&nbsp; Buat Postingan
        </a>
        <a href="<?= BASE_URL ?>/profile.php" class="<?= $cur_page==='profile.php'?'active':'' ?>">
          <i class="fa fa-user"></i>&nbsp; Profil Saya
        </a>
        <?php if ($_SESSION['role']==='admin'): ?>
        <a href="<?= BASE_URL ?>/admin/index.php" class="<?= $cur_dir==='admin'?'active':'' ?>">
          <i class="fa fa-shield-halved"></i>&nbsp; Admin Panel
        </a>
        <?php endif; ?>
        <?php endif; ?>
      </nav>
    </div>
    <div class="sidebar-section">
      <span class="sidebar-title">Kategori</span>
      <nav>
        <a href="<?= BASE_URL ?>/" class="kategori-item <?= ($cur_page==='index.php'&&$cur_dir!=='admin'&&!isset($_GET['kat']))?'active':'' ?>">
          <span class="kat-icon">&#128196;</span>
          <span class="kat-name">Semua</span>
        </a>
        <?php while($kat=mysqli_fetch_assoc($kat_q)): ?>
        <a href="<?= BASE_URL ?>/?kat=<?= $kat['id_kategori'] ?>" class="kategori-item <?= (isset($_GET['kat'])&&(int)$_GET['kat']===(int)$kat['id_kategori'])?'active':'' ?>">
          <span class="kat-icon"><?= $kat['icon'] ?></span>
          <span class="kat-name"><?= clean($kat['nama_kategori']) ?></span>
        </a>
        <?php endwhile; ?>
      </nav>
    </div>
  </aside>
  <!-- MAIN CONTENT -->
  <main class="main-content">
