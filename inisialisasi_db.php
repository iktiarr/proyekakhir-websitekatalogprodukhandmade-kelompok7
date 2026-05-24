<?php
include 'koneksi.php';

$queries = [
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

foreach ($queries as $query) {
    if (mysqli_query($conn, $query)) {
        echo "<p style='color: green;'>Sukses: " . substr($query, 0, 50) . "...</p>";
    } else {
        echo "<p style='color: red;'>Gagal: " . mysqli_error($conn) . "</p>";
    }
}

$checkAdmin = mysqli_query($conn, "SELECT * FROM pengguna WHERE role = 'admin'");
if (mysqli_num_rows($checkAdmin) == 0) {
    $namaAdmin = 'Admin Handmade';
    $emailAdmin = 'admin@handmade.com';
    $passAdmin = password_hash('admin123', PASSWORD_DEFAULT);
    $queryAdmin = "INSERT INTO pengguna (nama, email, password, role) VALUES ('$namaAdmin', '$emailAdmin', '$passAdmin', 'admin')";
    if (mysqli_query($conn, $queryAdmin)) {
        echo "<p style='color: blue;'>Admin default dibuat: admin@handmade.com / admin123</p>";
    }
}

$checkBatik = mysqli_query($conn, "SELECT * FROM kategori WHERE nama_kategori = 'Batik'");
if (mysqli_num_rows($checkBatik) == 0) {
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");
    mysqli_query($conn, "TRUNCATE TABLE kategori");
    mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('Batik'), ('Anyaman'), ('Aksesoris'), ('Dekorasi'), ('Rajut')");
    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");
    echo "<p style='color: blue;'>Kategori didefinisikan ulang: Batik, Anyaman, Aksesoris, Dekorasi, Rajut.</p>";
}

$checkDaerahTable = mysqli_query($conn, "SHOW TABLES LIKE 'daerah'");
if ($checkDaerahTable && mysqli_num_rows($checkDaerahTable) > 0) {
    $checkDaerah = mysqli_query($conn, "SELECT * FROM daerah");
    if (!$checkDaerah || mysqli_num_rows($checkDaerah) == 0) {
        mysqli_query($conn, "INSERT INTO daerah (nama_daerah) VALUES ('Sumenep'), ('Pamekasan'), ('Sampang'), ('Bangkalan')");
        echo "<p style='color: blue;'>Seeder daerah berhasil dimasukkan: Sumenep, Pamekasan, Sampang, Bangkalan.</p>";
    }
}

$checkOldColumn = mysqli_query($conn, "SHOW COLUMNS FROM produk LIKE 'daerah'");
if ($checkOldColumn && mysqli_num_rows($checkOldColumn) > 0) {
    mysqli_query($conn, "ALTER TABLE produk DROP COLUMN daerah");
    echo "<p style='color: blue;'>Kolom daerah (lama) berhasil dihapus dari tabel produk.</p>";
}

$checkNewColumn = mysqli_query($conn, "SHOW COLUMNS FROM produk LIKE 'id_daerah'");
if ($checkNewColumn && mysqli_num_rows($checkNewColumn) == 0) {
    mysqli_query($conn, "ALTER TABLE produk ADD COLUMN id_daerah INT NULL");
    mysqli_query($conn, "ALTER TABLE produk ADD CONSTRAINT fk_produk_daerah FOREIGN KEY (id_daerah) REFERENCES daerah(id) ON DELETE SET NULL");
    echo "<p style='color: blue;'>Kolom id_daerah (baru) dan relasi berhasil ditambahkan ke tabel produk.</p>";
}

echo "</div>";
?>
