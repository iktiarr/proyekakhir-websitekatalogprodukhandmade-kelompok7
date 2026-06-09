<?php
include_once '../koneksi.php';

$_SESSION['role'] = 'admin';
$_SESSION['user_id'] = 1;
$_SESSION['nama'] = 'Admin Test';

header("Location: laporan.php");
exit();
?>
