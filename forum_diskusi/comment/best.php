<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/config/db.php';
if (!isset($_SESSION['user_id'])) redirect('auth/login.php');
$id      = isset($_GET['id'])?(int)$_GET['id']:0;
$post_id = isset($_GET['post'])?(int)$_GET['post']:0;
if (!$id||!$post_id) redirect('');
$post = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tb_postingan WHERE id_post=$post_id"));
if (!$post||$post['id_user']!=$_SESSION['user_id']) redirect('post/detail.php?id='.$post_id);
$kom = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM tb_komentar WHERE id_komentar=$id"));
if (!$kom||$kom['id_post']!=$post_id) redirect('post/detail.php?id='.$post_id);
if ($kom['is_best_answer']) {
    mysqli_query($conn,"UPDATE tb_komentar SET is_best_answer=0 WHERE id_komentar=$id");
    $_SESSION['msg']='Tanda jawaban terbaik dihapus.';
} else {
    mysqli_query($conn,"UPDATE tb_komentar SET is_best_answer=0 WHERE id_post=$post_id");
    mysqli_query($conn,"UPDATE tb_komentar SET is_best_answer=1 WHERE id_komentar=$id");
    $_SESSION['msg']='Jawaban terbaik ditandai!';
}
$_SESSION['msg_type']='success';
redirect('post/detail.php?id='.$post_id);
