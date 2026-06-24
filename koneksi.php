<?php
// koneksi.php: Mengatur koneksi database MySQL/TiDB Cloud menggunakan SSL, inisialisasi sesi, dan menyediakan fungsi utilitas global.

$host = 'gateway01.ap-southeast-1.prod.aws.tidbcloud.com';
$port = 4000;
$user = 'o1gMRaidvREj2xW.root';
$pass = 'DTSeLqd5xztVM0r1';
$db = 'uas_bersama';

// Inisialisasi koneksi MySQLi
$koneksi = mysqli_init();

// Mengatur opsi SSL untuk keamanan koneksi TiDB Cloud
mysqli_ssl_set($koneksi, null, null, null, null, null);
mysqli_options($koneksi, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);

// Menghubungkan ke database dengan SSL
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

// Menyetel zona waktu default ke Waktu Indonesia Barat (WIB)
date_default_timezone_set('Asia/Jakarta');

// Memulai sesi PHP jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Menjalankan query SQL menggunakan prepared statement untuk mencegah SQL injection.
 * @param string $sql Query SQL dengan placeholder '?'.
 * @param array $params Parameter untuk di-bind ke query.
 * @return mysqli_result|mysqli_stmt|bool Hasil query berupa objek result, statement, atau false.
 */
function kueri($sql, $params = [])
{
    global $koneksi;
    if (empty($params)) {
        return mysqli_query($koneksi, $sql);
    }
    $stmt = mysqli_prepare($koneksi, $sql);
    if (!$stmt)
        return false;

    $types = '';
    foreach ($params as $p) {
        if (is_int($p))
            $types .= 'i';
        elseif (is_double($p))
            $types .= 'd';
        else
            $types .= 's';
    }

    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return $res ?: $stmt;
}

/**
 * Mencatat log aktivitas admin atau pengguna ke dalam database.
 * @param string $tipe Kategori log (misal: 'produk', 'pembayaran').
 * @param string $aksi Jenis tindakan (misal: 'tambah', 'ubah', 'hapus').
 * @param string $keterangan Deskripsi detail aktivitas.
 */
function catat_log($tipe, $aksi, $keterangan)
{
    $id_admin = null;
    $nama_admin = 'System';
    if (isset($_SESSION['admin'])) {
        $id_admin = (int) $_SESSION['admin']['id'];
        $nama_admin = $_SESSION['admin']['nama'];
    } elseif (isset($_SESSION['user'])) {
        $id_admin = (int) $_SESSION['user']['id'];
        $nama_admin = $_SESSION['user']['nama'];
    }
    kueri(
        "INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan) VALUES (?, ?, ?, ?, ?)",
        [$id_admin, $nama_admin, $tipe, $aksi, $keterangan]
    );
}

/**
 * Mendapatkan link/jalur foto produk yang benar, mendukung link online (HTTP) dan file lokal (uploads/).
 * @param string $gambar Jalur file atau URL gambar.
 * @return string Jalur gambar relatif yang siap dipasang di tag HTML img.
 */
function dapatkan_jalur_gambar($gambar)
{
    if (empty($gambar)) {
        return '';
    }
    // Jika merupakan link online eksternal
    if (strpos($gambar, 'http://') === 0 || strpos($gambar, 'https://') === 0) {
        return $gambar;
    }
    // Jika file lokal, sesuaikan awalan folder berdasarkan letak pemanggil
    global $awalan;
    if (isset($awalan)) {
        return $awalan . $gambar;
    }
    // Deteksi jika diakses dari folder admin
    $dir_sekarang = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    if (preg_match('/\/admin(\/|$)/', $dir_sekarang)) {
        return '../' . $gambar;
    }
    return $gambar;
}

// Buat tabel laporan_kendala jika belum ada
mysqli_query($koneksi, "
    CREATE TABLE IF NOT EXISTS `laporan_kendala` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `id_pengguna` INT NOT NULL,
      `id_pesanan` INT DEFAULT NULL,
      `tipe_laporan` VARCHAR(50) DEFAULT 'Pembayaran',
      `deskripsi` TEXT NOT NULL,
      `file_laporan` VARCHAR(255) NOT NULL,
      `status` ENUM('pending', 'diproses', 'selesai') DEFAULT 'pending',
      `tanggal_dibuat` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      CONSTRAINT `fk_laporan_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE,
      CONSTRAINT `fk_laporan_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin
");
?>