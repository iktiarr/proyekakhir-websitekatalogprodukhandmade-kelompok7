<?php
$host = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$port = 4000;
$user = 'o1gMRaidvREj2xW.root';
$pass = 'aU9NXWLYnVHzPiNC';
$db   = 'uas_bersama';

$koneksi = mysqli_init();

mysqli_ssl_set($koneksi, NULL, NULL, NULL, NULL, NULL);

$berhasil_koneksi = mysqli_real_connect($koneksi, $host, $user, $pass, $db, $port, NULL, MYSQLI_CLIENT_SSL);

if (!$berhasil_koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

// Kolom alamat dan no_telp sudah sukses dibuat di database, nonaktifkan DDL Alter pada setiap koneksi agar loading halaman sangat cepat.
// mysqli_query($koneksi, "ALTER TABLE pengguna ADD COLUMN IF NOT EXISTS alamat TEXT NULL");
// mysqli_query($koneksi, "ALTER TABLE pengguna ADD COLUMN IF NOT EXISTS no_telp VARCHAR(20) NULL");

date_default_timezone_set('Asia/Jakarta');

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>