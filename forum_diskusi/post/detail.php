<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/config/db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) redirect('');

$post = mysqli_fetch_assoc(mysqli_query($conn,
  "SELECT p.*,u.username,k.nama_kategori,k.icon,
   (SELECT COUNT(*) FROM tb_like WHERE id_post=p.id_post) total_like,
   (SELECT COUNT(*) FROM tb_komentar WHERE id_post=p.id_post) total_komentar
   FROM tb_postingan p
   JOIN tb_user u ON u.id_user=p.id_user
   JOIN tb_kategori k ON k.id_kategori=p.id_kategori
   WHERE p.id_post=$id"));
if (!$post) { $_SESSION['msg']='Postingan tidak ditemukan.';$_SESSION['msg_type']='error';redirect(''); }

$user_liked = false;
if (isset($_SESSION['user_id'])) {
  $ul = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id_like FROM tb_like WHERE id_post=$id AND id_user={$_SESSION['user_id']}"));
  $user_liked = (bool)$ul;
}

$koms = mysqli_query($conn,
  "SELECT k.*,u.username FROM tb_komentar k
   JOIN tb_user u ON u.id_user=k.id_user
   WHERE k.id_post=$id ORDER BY k.is_best_answer DESC, k.tgl_komentar ASC");

$page_title = $post['judul'];
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/includes/header.php';
?>

<?php if (isset($_SESSION['msg'])): ?>
<div class="alert alert-<?= $_SESSION['msg_type']??'info' ?>"><?= $_SESSION['msg'] ?></div>
<?php unset($_SESSION['msg'],$_SESSION['msg_type']); endif; ?>

<!-- Detail Post -->
<div class="detail-card">
  <div class="post-author-row" style="margin-bottom:10px;">
    <div class="post-avatar" style="width:36px;height:36px;font-size:.8rem;"><?= getInitial($post['username']) ?></div>
    <span class="post-author"><?= clean($post['username']) ?></span>
    <span class="post-time"><i class="fa fa-clock"></i> <?= timeAgo($post['tgl_posting']) ?></span>
    <span class="badge-kategori"><?= $post['icon'] ?> <?= clean($post['nama_kategori']) ?></span>
    <span class="badge-status-<?= $post['status'] ?>"><?= $post['status']==='open'?'Aktif':'Selesai' ?></span>
  </div>
  <h1 class="detail-title"><?= clean($post['judul']) ?></h1>
  <div class="detail-body"><?= clean($post['isi_pertanyaan']) ?></div>
  <div class="post-footer" style="margin-top:16px;">
    <button id="like-btn-<?= $id ?>" class="like-btn <?= $user_liked?'liked':'' ?>" onclick="toggleLike(<?= $id ?>)">
      <i class="fa fa-heart"></i> <span class="like-count"><?= $post['total_like'] ?></span> Suka
    </button>
    <span class="post-stat"><i class="fa fa-comment"></i> <?= $post['total_komentar'] ?> Jawaban</span>
    <?php if (isset($_SESSION['user_id']) && ($_SESSION['user_id']==$post['id_user']||$_SESSION['role']==='admin')): ?>
    <a href="<?= BASE_URL ?>/post/edit.php?id=<?= $id ?>" class="btn-sm"><i class="fa fa-pen"></i> Edit</a>
    <?php if ($_SESSION['role']==='admin'||$_SESSION['user_id']==$post['id_user']): ?>
    <a href="<?= BASE_URL ?>/post/close.php?id=<?= $id ?>" class="btn-sm" onclick="return confirm('<?= $post['status']==='open'?'Tutup':'Buka kembali' ?> diskusi ini?')">
      <i class="fa fa-<?= $post['status']==='open'?'lock':'lock-open' ?>"></i> <?= $post['status']==='open'?'Tutup':'Buka' ?>
    </a>
    <a href="<?= BASE_URL ?>/post/delete.php?id=<?= $id ?>" class="btn-sm danger" onclick="return confirmDelete('Hapus postingan ini?')"><i class="fa fa-trash"></i> Hapus</a>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</div>

<!-- Komentar -->
<div class="comments-section">
  <h2 style="font-size:.95rem;font-weight:700;color:#1F2430;margin-bottom:12px;"><i class="fa fa-comments"></i> <?= $post['total_komentar'] ?> Jawaban</h2>
  <?php if (mysqli_num_rows($koms)===0): ?>
  <div class="empty-state" style="padding:28px 0;">
    <div class="empty-icon">&#128172;</div>
    <h3>Belum ada jawaban</h3>
    <p>Jadilah yang pertama menjawab!</p>
  </div>
  <?php else: while($k=mysqli_fetch_assoc($koms)): ?>
  <div class="comment-card <?= $k['is_best_answer']?'best-answer':'' ?>">
    <?php if ($k['is_best_answer']): ?>
    <div class="best-answer-badge"><i class="fa fa-star"></i> Jawaban Terbaik</div>
    <?php endif; ?>
    <div class="comment-header">
      <div class="comment-user">
        <div class="comment-avatar"><?= getInitial($k['username']) ?></div>
        <div>
          <div class="comment-username"><?= clean($k['username']) ?></div>
          <div class="comment-time"><i class="fa fa-clock"></i> <?= timeAgo($k['tgl_komentar']) ?><?= $k['tgl_edit']?' (diedit)':'' ?></div>
        </div>
      </div>
    </div>
    <div class="comment-body"><?= clean($k['isi_komentar']) ?></div>
    <?php if (isset($_SESSION['user_id'])): ?>
    <div class="comment-actions">
      <?php if ($_SESSION['user_id']==$post['id_user'] && $post['status']==='open'): ?>
      <a href="<?= BASE_URL ?>/comment/best.php?id=<?= $k['id_komentar'] ?>&post=<?= $id ?>" class="btn-sm best" onclick="return confirm('Tandai sebagai jawaban terbaik?')">
        <i class="fa fa-star"></i> <?= $k['is_best_answer']?'Hapus Terbaik':'Terbaik' ?>
      </a>
      <?php endif; ?>
      <?php if ($_SESSION['user_id']==$k['id_user']||$_SESSION['role']==='admin'): ?>
      <a href="<?= BASE_URL ?>/comment/edit.php?id=<?= $k['id_komentar'] ?>&post=<?= $id ?>" class="btn-sm"><i class="fa fa-pen"></i> Edit</a>
      <a href="<?= BASE_URL ?>/comment/delete.php?id=<?= $k['id_komentar'] ?>&post=<?= $id ?>" class="btn-sm danger" onclick="return confirmDelete('Hapus komentar ini?')"><i class="fa fa-trash"></i> Hapus</a>
      <?php endif; ?>
    </div>
    <?php endif; ?>
  </div>
  <?php endwhile; endif; ?>
</div>

<!-- Form Komentar -->
<?php if (isset($_SESSION['user_id'])): ?>
<?php if ($post['status']==='open'): ?>
<div class="comment-form-card">
  <h3><i class="fa fa-reply"></i> Tulis Jawaban</h3>
  <form method="POST" action="<?= BASE_URL ?>/comment/store.php">
    <input type="hidden" name="id_post" value="<?= $id ?>">
    <div class="form-group">
      <textarea name="isi_komentar" class="form-control" rows="4" placeholder="Tulis jawaban atau komentarmu..." required></textarea>
    </div>
    <button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> Kirim Jawaban</button>
  </form>
</div>
<?php else: ?>
<div class="alert alert-info"><i class="fa fa-lock"></i> Diskusi ini sudah ditutup. Tidak bisa menambah komentar baru.</div>
<?php endif; ?>
<?php else: ?>
<div class="alert alert-info"><i class="fa fa-info-circle"></i> <a href="<?= BASE_URL ?>/auth/login.php" style="color:#223382;font-weight:700;">Masuk</a> untuk memberikan jawaban.</div>
<?php endif; ?>

<?php require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/includes/footer.php'; ?>
