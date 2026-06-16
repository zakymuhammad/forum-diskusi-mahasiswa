<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/config/db.php';
if (!isset($_SESSION['user_id'])) redirect('auth/login.php');
$id      = isset($_GET['id'])?(int)$_GET['id']:0;
$post_id = isset($_GET['post'])?(int)$_GET['post']:0;
if (!$id) redirect('');
$kom = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tb_komentar WHERE id_komentar=$id"));
if (!$kom||($kom['id_user']!=$_SESSION['user_id']&&$_SESSION['role']!=='admin')) redirect('post/detail.php?id='.$post_id);
mysqli_query($conn,"DELETE FROM tb_komentar WHERE id_komentar=$id");
$_SESSION['msg']='Komentar berhasil dihapus.';
$_SESSION['msg_type']='success';
redirect('post/detail.php?id='.($post_id?:$kom['id_post']));
