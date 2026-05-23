<?php
$host = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$port = 4000;
$user = 'o1gMRaidvREj2xW.root';
$pass = 'aU9NXWLYnVHzPiNC';
$db   = 'uas_bersama';

$conn = mysqli_init();

mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

$koneksi = mysqli_real_connect($conn, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);

if (!$koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

date_default_timezone_set('Asia/Jakarta');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>