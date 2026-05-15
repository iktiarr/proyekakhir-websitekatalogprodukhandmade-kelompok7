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
    "CREATE TABLE IF NOT EXISTS produk (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_produk VARCHAR(100) NOT NULL,
        deskripsi TEXT,
        harga DECIMAL(10, 2) NOT NULL,
        stok INT NOT NULL,
        gambar VARCHAR(255),
        id_kategori INT,
        FOREIGN KEY (id_kategori) REFERENCES kategori(id) ON DELETE SET NULL
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
    )"
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

// Cek jika sudah ada admin, jika belum buat satu
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

// Tambah kategori default jika kosong
$checkKategori = mysqli_query($conn, "SELECT * FROM kategori");
if (mysqli_num_rows($checkKategori) == 0) {
    mysqli_query($conn, "INSERT INTO kategori (nama_kategori) VALUES ('Aksesoris'), ('Dekorasi'), ('Pakaian'), ('Lainnya')");
    echo "<p style='color: blue;'>Kategori default dibuat.</p>";
}

echo "</div>";
?>
