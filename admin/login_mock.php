<?php
// admin/login_mock.php: File pembantu untuk mensimulasikan login admin secara instan untuk kebutuhan pengujian.

include_once '../koneksi.php';

$_SESSION['admin'] = [
    'id' => 1,
    'nama' => 'Admin Test',
    'role' => 'admin'
];

header("Location: index.php");
exit();
?>
