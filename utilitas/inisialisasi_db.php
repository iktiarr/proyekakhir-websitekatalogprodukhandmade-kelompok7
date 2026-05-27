<?php
include '../koneksi.php';

$daftar_kueri = [
    "CREATE TABLE IF NOT EXISTS pengguna (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        tanggal_dibuat TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS kategori (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_kategori VARCHAR(50) NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS daerah (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_daerah VARCHAR(50) NOT NULL UNIQUE
    )",
    "CREATE TABLE IF NOT EXISTS produk (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_produk VARCHAR(100) NOT NULL,
        deskripsi TEXT,
        harga DECIMAL(10, 2) NOT NULL,
        stok INT NOT NULL,
        gambar VARCHAR(255),
        id_kategori INT,
        id_daerah INT,
        FOREIGN KEY (id_kategori) REFERENCES kategori(id) ON DELETE SET NULL,
        FOREIGN KEY (id_daerah) REFERENCES daerah(id) ON DELETE SET NULL
    )",
    "CREATE TABLE IF NOT EXISTS pesanan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_pengguna INT,
        tanggal_pesanan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        total_harga DECIMAL(10, 2) NOT NULL,
        status ENUM('menunggu', 'dibayar', 'dikirim', 'selesai', 'dibatalkan') DEFAULT 'menunggu',
        alamat TEXT,
        metode_pembayaran VARCHAR(50),
        bukti_pembayaran VARCHAR(255),
        FOREIGN KEY (id_pengguna) REFERENCES pengguna(id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS detail_pesanan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_pesanan INT,
        id_produk INT,
        jumlah INT NOT NULL,
        harga DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (id_pesanan) REFERENCES pesanan(id) ON DELETE CASCADE,
        FOREIGN KEY (id_produk) REFERENCES produk(id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS keranjang (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_pengguna INT,
        id_produk INT,
        jumlah INT NOT NULL,
        FOREIGN KEY (id_pengguna) REFERENCES pengguna(id) ON DELETE CASCADE,
        FOREIGN KEY (id_produk) REFERENCES produk(id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS testimonial (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_pengguna INT NOT NULL,
        nama VARCHAR(100) NOT NULL,
        pekerjaan VARCHAR(100),
        isi_ulasan TEXT NOT NULL,
        rating INT DEFAULT 5,
        tanggal_dibuat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        FOREIGN KEY (id_pengguna) REFERENCES pengguna(id) ON DELETE CASCADE
    )",
    "ALTER TABLE produk MODIFY COLUMN harga DECIMAL(10, 2) NOT NULL",
    "ALTER TABLE pesanan MODIFY COLUMN total_harga DECIMAL(10, 2) NOT NULL",
    "ALTER TABLE detail_pesanan MODIFY COLUMN harga DECIMAL(10, 2) NOT NULL"
];

echo "<div style='font-family: sans-serif; padding: 20px;'>";
echo "<h1>Inisialisasi Database</h1>";

foreach ($daftar_kueri as $kueri) {
    if (mysqli_query($koneksi, $kueri)) {
        echo "<p style='color: green;'>Sukses: " . substr($kueri, 0, 50) . "...</p>";
    } else {
        echo "<p style='color: red;'>Gagal: " . mysqli_error($koneksi) . "</p>";
    }
}

$cek_admin = mysqli_query($koneksi, "SELECT * FROM pengguna WHERE role = 'admin'");
if (mysqli_num_rows($cek_admin) == 0) {
    $nama_admin = 'Admin HandMadura';
    $email_admin = 'admin@handmadura.com';
    $sandi_admin = password_hash('admin123', PASSWORD_DEFAULT);
    $kueri_admin = "INSERT INTO pengguna (nama, email, password, role) VALUES ('$nama_admin', '$email_admin', '$sandi_admin', 'admin')";
    if (mysqli_query($koneksi, $kueri_admin)) {
        echo "<p style='color: blue;'>Admin default dibuat: admin@handmadura.com / admin123</p>";
    }
}

$cek_batik = mysqli_query($koneksi, "SELECT * FROM kategori WHERE nama_kategori = 'Batik'");
if (mysqli_num_rows($cek_batik) == 0) {
    mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0");
    mysqli_query($koneksi, "TRUNCATE TABLE kategori");
    mysqli_query($koneksi, "INSERT INTO kategori (nama_kategori) VALUES ('Batik'), ('Anyaman'), ('Aksesoris'), ('Dekorasi'), ('Rajut')");
    mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1");
    echo "<p style='color: blue;'>Kategori didefinisikan ulang: Batik, Anyaman, Aksesoris, Dekorasi, Rajut.</p>";
}

$cek_tabel_daerah = mysqli_query($koneksi, "SHOW TABLES LIKE 'daerah'");
if ($cek_tabel_daerah && mysqli_num_rows($cek_tabel_daerah) > 0) {
    $cek_daerah = mysqli_query($koneksi, "SELECT * FROM daerah");
    if (!$cek_daerah || mysqli_num_rows($cek_daerah) == 0) {
        mysqli_query($koneksi, "INSERT INTO daerah (nama_daerah) VALUES ('Sumenep'), ('Pamekasan'), ('Sampang'), ('Bangkalan')");
        echo "<p style='color: blue;'>Seeder daerah berhasil dimasukkan: Sumenep, Pamekasan, Sampang, Bangkalan.</p>";
    }
}

$cek_kolom_lama = mysqli_query($koneksi, "SHOW COLUMNS FROM produk LIKE 'daerah'");
if ($cek_kolom_lama && mysqli_num_rows($cek_kolom_lama) > 0) {
    mysqli_query($koneksi, "ALTER TABLE produk DROP COLUMN daerah");
    echo "<p style='color: blue;'>Kolom daerah (lama) berhasil dihapus dari tabel produk.</p>";
}

$cek_kolom_baru = mysqli_query($koneksi, "SHOW COLUMNS FROM produk LIKE 'id_daerah'");
if ($cek_kolom_baru && mysqli_num_rows($cek_kolom_baru) == 0) {
    mysqli_query($koneksi, "ALTER TABLE produk ADD COLUMN id_daerah INT NULL");
    mysqli_query($koneksi, "ALTER TABLE produk ADD CONSTRAINT fk_produk_daerah FOREIGN KEY (id_daerah) REFERENCES daerah(id) ON DELETE SET NULL");
    echo "<p style='color: blue;'>Kolom id_daerah (baru) dan relasi berhasil ditambahkan ke tabel produk.</p>";
}

echo "</div>";
?>
