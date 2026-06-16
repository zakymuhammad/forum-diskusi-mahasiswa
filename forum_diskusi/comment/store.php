<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/config/db.php';
if (!isset($_SESSION['user_id'])) { $_SESSION['msg']='Silakan masuk dulu.';$_SESSION['msg_type']='error';redirect('auth/login.php'); }
if ($_SERVER['REQUEST_METHOD']!=='POST') redirect('');
$id_post = (int)($_POST['id_post']??0);
$isi     = trim($_POST['isi_komentar']??'');
if (!$id_post||!$isi) { $_SESSION['msg']='Komentar tidak boleh kosong.';$_SESSION['msg_type']='error';redirect('post/detail.php?id='.$id_post); }
$post = mysqli_fetch_assoc(mysqli_query($conn,"SELECT status FROM tb_postingan WHERE id_post=$id_post"));
if (!$post||$post['status']==='closed') { $_SESSION['msg']='Diskusi sudah ditutup.';$_SESSION['msg_type']='error';redirect('post/detail.php?id='.$id_post); }
$uid = (int)$_SESSION['user_id'];
$i   = mysqli_real_escape_string($conn,$isi);
mysqli_query($conn,"INSERT INTO tb_komentar (id_post,id_user,isi_komentar) VALUES ($id_post,$uid,'$i')");
$_SESSION['msg']='Jawaban berhasil dikirim!';
$_SESSION['msg_type']='success';
redirect('post/detail.php?id='.$id_post);
