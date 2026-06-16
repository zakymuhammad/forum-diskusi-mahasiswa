<?php
require_once 'config/db.php';
$page_title = 'Beranda';

$kat_id = isset($_GET['kat']) ? (int)$_GET['kat'] : 0;
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$sort   = isset($_GET['sort']) ? $_GET['sort'] : 'terbaru';
$page   = isset($_GET['p']) ? max(1,(int)$_GET['p']) : 1;
$limit  = 10;
$offset = ($page-1)*$limit;

$where = '1=1';
if ($kat_id) $where .= " AND p.id_kategori=$kat_id";
if ($search)  $where .= " AND (p.judul LIKE '%".mysqli_real_escape_string($conn,$search)."%' OR p.isi_pertanyaan LIKE '%".mysqli_real_escape_string($conn,$search)."%')";

$order = 'p.tgl_posting DESC';
if ($sort==='terlama')  $order='p.tgl_posting ASC';
if ($sort==='terpopuler') $order='total_like DESC';

$sql_count = "SELECT COUNT(*) c FROM tb_postingan p WHERE $where";
$total = mysqli_fetch_assoc(mysqli_query($conn,$sql_count))['c'];
$total_page = max(1,ceil($total/$limit));

$sql = "SELECT p.*,u.username,k.nama_kategori,k.icon,
        (SELECT COUNT(*) FROM tb_komentar WHERE id_post=p.id_post) total_komentar,
        (SELECT COUNT(*) FROM tb_like WHERE id_post=p.id_post) total_like
        FROM tb_postingan p
        JOIN tb_user u ON u.id_user=p.id_user
        JOIN tb_kategori k ON k.id_kategori=p.id_kategori
        WHERE $where ORDER BY $order LIMIT $limit OFFSET $offset";
$posts = mysqli_query($conn,$sql);

$kat_info = null;
if ($kat_id) $kat_info = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tb_kategori WHERE id_kategori=$kat_id"));

require_once 'includes/header.php';
?>

<?php if (isset($_SESSION['msg'])): ?>
<div class="alert alert-<?= $_SESSION['msg_type']??'info' ?>"><?= $_SESSION['msg'] ?></div>
<?php unset($_SESSION['msg'],$_SESSION['msg_type']); endif; ?>

<div class="page-header">
  <div>
    <h1 class="page-title">
      <?php if ($kat_info): ?>
        <?= $kat_info['icon'] ?> <?= clean($kat_info['nama_kategori']) ?>
      <?php elseif ($search): ?>
        &#128269; Hasil: "<?= clean($search) ?>"
      <?php else: ?>
        &#127968; Beranda Forum
      <?php endif; ?>
    </h1>
    <p class="page-subtitle"><?= $total ?> postingan ditemukan</p>
  </div>
  <?php if (isset($_SESSION['user_id'])): ?>
  <a href="<?= BASE_URL ?>/post/create.php" class="btn btn-primary"><i class="fa fa-plus"></i> Buat Postingan</a>
  <?php endif; ?>
</div>

<div class="filter-bar">
  <a href="?<?= $kat_id?'kat='.$kat_id.'&':'' ?>sort=terbaru<?= $search?'&q='.urlencode($search):'' ?>" class="filter-btn <?= $sort==='terbaru'?'active':'' ?>">&#128336; Terbaru</a>
  <a href="?<?= $kat_id?'kat='.$kat_id.'&':'' ?>sort=terpopuler<?= $search?'&q='.urlencode($search):'' ?>" class="filter-btn <?= $sort==='terpopuler'?'active':'' ?>">&#128293; Terpopuler</a>
  <a href="?<?= $kat_id?'kat='.$kat_id.'&':'' ?>sort=terlama<?= $search?'&q='.urlencode($search):'' ?>" class="filter-btn <?= $sort==='terlama'?'active':'' ?>">&#128337; Terlama</a>
</div>

<?php if (mysqli_num_rows($posts)===0): ?>
<div class="empty-state">
  <div class="empty-icon">&#128172;</div>
  <h3>Belum ada postingan</h3>
  <p>Jadilah yang pertama memulai diskusi!</p>
</div>
<?php else: ?>
<?php while($p=mysqli_fetch_assoc($posts)):
  $user_liked = false;
  if (isset($_SESSION['user_id'])) {
    $ul = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id_like FROM tb_like WHERE id_post={$p['id_post']} AND id_user={$_SESSION['user_id']}"));
    $user_liked = (bool)$ul;
  }
?>
<div class="post-card" onclick="location.href='<?= BASE_URL ?>/post/detail.php?id=<?= $p['id_post'] ?>'">
  <div class="post-header">
    <div class="post-avatar"><?= getInitial($p['username']) ?></div>
    <div class="post-meta">
      <div class="post-author-row">
        <span class="post-author"><?= clean($p['username']) ?></span>
        <span class="post-time"><i class="fa fa-clock"></i> <?= timeAgo($p['tgl_posting']) ?></span>
        <span class="badge-kategori"><?= $p['icon'] ?> <?= clean($p['nama_kategori']) ?></span>
        <span class="badge-status-<?= $p['status'] ?>"><?= $p['status']==='open'?'Aktif':'Selesai' ?></span>
      </div>
      <div class="post-title"><?= clean($p['judul']) ?></div>
      <div class="post-excerpt"><?= clean(mb_substr($p['isi_pertanyaan'],0,150)) ?>...</div>
      <div class="post-footer">
        <span class="post-stat"><i class="fa fa-comment"></i> <?= $p['total_komentar'] ?> jawaban</span>
        <span class="post-stat"><i class="fa fa-heart" style="color:<?= $user_liked?'#e91e63':'#8B92A8' ?>"></i> <?= $p['total_like'] ?> suka</span>
      </div>
    </div>
  </div>
</div>
<?php endwhile; ?>

<?php if ($total_page>1): ?>
<div class="pagination">
  <?php for($i=1;$i<=$total_page;$i++): ?>
  <a href="?<?= $kat_id?'kat='.$kat_id.'&':'' ?>sort=<?= $sort ?>&p=<?= $i ?><?= $search?'&q='.urlencode($search):'' ?>" class="page-link <?= $i===$page?'active':'' ?>"><?= $i ?></a>
  <?php endfor; ?>
</div>
<?php endif; ?>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
