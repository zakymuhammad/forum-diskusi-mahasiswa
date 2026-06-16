  </main>
  <!-- RIGHT PANEL -->
  <aside class="right-panel">
    <?php
    $r_posts  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_postingan"))['c'];
    $r_users  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_user"))['c'];
    $r_koms   = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_komentar"))['c'];
    $r_open   = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_postingan WHERE status='open'"))['c'];
    ?>
    <div class="right-panel-card">
      <span class="right-panel-title">&#128202; Statistik Forum</span>
      <div class="stat-row"><span class="stat-row-label">Total Postingan</span><span class="stat-row-value"><?= $r_posts ?></span></div>
      <div class="stat-row"><span class="stat-row-label">Total Anggota</span><span class="stat-row-value"><?= $r_users ?></span></div>
      <div class="stat-row"><span class="stat-row-label">Total Komentar</span><span class="stat-row-value"><?= $r_koms ?></span></div>
      <div class="stat-row"><span class="stat-row-label">Diskusi Aktif</span><span class="stat-row-value"><?= $r_open ?></span></div>
    </div>
    <?php
    $top_kat = mysqli_query($conn,"SELECT k.id_kategori,k.nama_kategori,k.icon,COUNT(p.id_post) cnt FROM tb_kategori k LEFT JOIN tb_postingan p ON p.id_kategori=k.id_kategori GROUP BY k.id_kategori ORDER BY cnt DESC LIMIT 5");
    ?>
    <div class="right-panel-card">
      <span class="right-panel-title">&#128293; Kategori Populer</span>
      <?php while($tk=mysqli_fetch_assoc($top_kat)): ?>
      <a href="<?= BASE_URL ?>/?kat=<?= $tk['id_kategori'] ?>" class="top-category">
        <span><?= $tk['icon'] ?></span>
        <span class="top-category-name"><?= clean($tk['nama_kategori']) ?></span>
        <span class="top-category-count"><?= $tk['cnt'] ?> post</span>
      </a>
      <?php endwhile; ?>
    </div>
  </aside>
</div><!-- .layout -->

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
