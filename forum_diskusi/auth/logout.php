<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/forum_diskusi/config/db.php';
session_destroy();
header('Location: '.BASE_URL.'/auth/login.php');
exit;
