  </main>
</div><!-- .admin-shell -->

<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<script>
function toggleAdminSidebar() {
  document.getElementById('adminSidebar').classList.toggle('collapsed');
  document.querySelector('.admin-content').classList.toggle('expanded');
}
function toggleAdminDropdown() {
  document.getElementById('adminUserDropdown').classList.toggle('show');
}
document.addEventListener('click', function(e) {
  var chip = document.getElementById('adminUserChip');
  var dd   = document.getElementById('adminUserDropdown');
  if (chip && dd && !chip.contains(e.target)) dd.classList.remove('show');
});
</script>
</body>
</html>
