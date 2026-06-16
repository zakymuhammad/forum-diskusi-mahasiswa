<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/config/db.php';
if (!isset($_SESSION['user_id'])) redirect('auth/login.php');
$id = isset($_GET['id'])?(int)$_GET['id']:0;
if(!$id) redirect('');
$post = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tb_postingan WHERE id_post=$id"));
if (!$post||($post['id_user']!=$_SESSION['user_id']&&$_SESSION['role']!=='admin')) redirect('');
mysqli_query($conn,"DELETE FROM tb_postingan WHERE id_post=$id");
$_SESSION['msg']='Postingan berhasil dihapus.';
$_SESSION['msg_type']='success';
redirect('');
