<?php
$host = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$port = 4000;
$user = 'o1gMRaidvREj2xW.root';
$pass = 'aU9NXWLYnVHzPiNC';
$db   = 'uas_bersama';

// Inisialisasi MySQLi
$conn = mysqli_init();

// Pengaturan SSL (Penting untuk TiDB Cloud)
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// Melakukan koneksi
$koneksi = mysqli_real_connect($conn, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);

if (!$koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>