// ForumKu - main.js

// Toggle user dropdown
function toggleDropdown() {
  const dd = document.getElementById('userDropdown');
  if (dd) dd.classList.toggle('show');
}
document.addEventListener('click', function(e) {
  const av = document.getElementById('userAvatar');
  const dd = document.getElementById('userDropdown');
  if (dd && av && !av.contains(e.target)) dd.classList.remove('show');
});

// Auto-hide alerts
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.alert').forEach(function(el) {
    setTimeout(function() {
      el.style.transition = 'opacity .5s';
      el.style.opacity = '0';
      setTimeout(function() { el.remove(); }, 500);
    }, 4000);
  });
});

// Confirm delete
function confirmDelete(msg) {
  return confirm(msg || 'Yakin ingin menghapus?');
}

// Like button AJAX
function toggleLike(postId) {
  const btn = document.getElementById('like-btn-' + postId);
  if (!btn) return;
  fetch(BASE_URL + '/post/like.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'id_post=' + postId + '&ajax=1'
  })
  .then(r => r.json())
  .then(data => {
    if (data.error) { alert(data.error); return; }
    btn.classList.toggle('liked', data.liked);
    const span = btn.querySelector('.like-count');
    if (span) span.textContent = data.count;
  })
  .catch(() => alert('Gagal menghubungi server.'));
}
