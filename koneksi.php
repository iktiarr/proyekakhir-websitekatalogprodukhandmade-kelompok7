<?php

$host = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$port = 4000;
$user = 'o1gMRaidvREj2xW.root';
$pass = 'DTSeLqd5xztVM0r1';
$db   = 'uas_bersama';

$koneksi = mysqli_init();

mysqli_ssl_set($koneksi, null, null, null, null, null);
mysqli_options($koneksi, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);

$berhasil_koneksi = mysqli_real_connect(
    $koneksi,
    $host,
    $user,
    $pass,
    $db,
    $port,
    null,
    MYSQLI_CLIENT_SSL
);

if (!$berhasil_koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

date_default_timezone_set('Asia/Jakarta');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function kueri($sql, $params = []) {
    global $koneksi;
    if (empty($params)) {
        return mysqli_query($koneksi, $sql);
    }
    $stmt = mysqli_prepare($koneksi, $sql);
    if (!$stmt) return false;
    
    $types = '';
    foreach ($params as $p) {
        if (is_int($p)) $types .= 'i';
        elseif (is_double($p)) $types .= 'd';
        else $types .= 's';
    }
    
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return $res ?: $stmt;
}

function catat_log($tipe, $aksi, $keterangan) {
    $id_admin = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
    $nama_admin = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'System';
    kueri(
        "INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan) VALUES (?, ?, ?, ?, ?)",
        [$id_admin, $nama_admin, $tipe, $aksi, $keterangan]
    );
}
?>