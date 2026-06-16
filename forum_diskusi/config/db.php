<?php
// ============================================
// KONFIGURASI DATABASE - ForumKu
// ============================================
date_default_timezone_set('Asia/Jakarta');

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'forum_diskusi_db');
define('BASE_URL', 'http://localhost/forum_diskusi');

$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if (!$conn) {
    die('<div style="font-family:sans-serif;padding:40px;text-align:center;"><h2>&#9888; Koneksi Database Gagal</h2><p>' . mysqli_connect_error() . '</p><p>Pastikan Laragon berjalan dan database <b>forum_diskusi_db</b> sudah dibuat.</p></div>');
}
mysqli_set_charset($conn, 'utf8mb4');

function redirect($url)
{
    header('Location: ' . BASE_URL . '/' . $url);
    exit;
}
function clean($d)
{
    return htmlspecialchars(strip_tags(trim($d)));
}
function timeAgo($dt)
{
    $diff = (new DateTime())->diff(new DateTime($dt));
    if ($diff->y > 0) return $diff->y . ' tahun lalu';
    if ($diff->m > 0) return $diff->m . ' bulan lalu';
    if ($diff->d > 0) return $diff->d . ' hari lalu';
    if ($diff->h > 0) return $diff->h . ' jam lalu';
    if ($diff->i > 0) return $diff->i . ' menit lalu';
    return 'Baru saja';
}
function getInitial($u)
{
    return strtoupper(substr($u, 0, 1));
}

session_start();
