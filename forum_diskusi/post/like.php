<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/config/db.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode(['error'=>'Silakan masuk dulu.']); exit; }
$id  = (int)($_POST['id_post']??0);
$uid = (int)$_SESSION['user_id'];
if (!$id) { echo json_encode(['error'=>'ID tidak valid.']); exit; }
$cek = mysqli_fetch_assoc(mysqli_query($conn,"SELECT id_like FROM tb_like WHERE id_post=$id AND id_user=$uid"));
if ($cek) {
    mysqli_query($conn,"DELETE FROM tb_like WHERE id_post=$id AND id_user=$uid");
    $liked = false;
} else {
    mysqli_query($conn,"INSERT INTO tb_like (id_post,id_user) VALUES ($id,$uid)");
    $liked = true;
}
$count = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) c FROM tb_like WHERE id_post=$id"))['c'];
echo json_encode(['liked'=>$liked,'count'=>(int)$count]);
