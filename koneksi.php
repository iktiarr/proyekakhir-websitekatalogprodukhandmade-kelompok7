<?php
$host = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$port = 4000;
$user = 'o1gMRaidvREj2xW.root';
$pass = 'S4eBrzT8gyBXQETx';
$db   = 'uas_bersama';

$koneksi = mysqli_init();

mysqli_ssl_set($koneksi, NULL, NULL, NULL, NULL, NULL);

$berhasil_koneksi = mysqli_real_connect($koneksi, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);

if (!$berhasil_koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

date_default_timezone_set('Asia/Jakarta');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>