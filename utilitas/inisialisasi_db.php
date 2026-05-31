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

// 1. SEEDING KATEGORI (MINIMAL 10)
mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 0");
$cek_kategori = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kategori");
$total_kategori = mysqli_fetch_assoc($cek_kategori)['total'];
if ($total_kategori < 10) {
    mysqli_query($koneksi, "TRUNCATE TABLE kategori");
    $kategori_dummy = ['Batik', 'Anyaman', 'Aksesoris', 'Dekorasi', 'Rajut', 'Keramik', 'Ukiran Kayu', 'Logam', 'Tekstil', 'Kulit'];
    foreach ($kategori_dummy as $kat) {
        mysqli_query($koneksi, "INSERT INTO kategori (nama_kategori) VALUES ('$kat')");
    }
    echo "<p style='color: blue;'>Kategori berhasil diseed (10 data).</p>";
}

// 2. SEEDING DAERAH (MINIMAL 10)
$cek_daerah = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM daerah");
$total_daerah = mysqli_fetch_assoc($cek_daerah)['total'];
if ($total_daerah < 10) {
    mysqli_query($koneksi, "TRUNCATE TABLE daerah");
    $daerah_dummy = ['Sumenep', 'Pamekasan', 'Sampang', 'Bangkalan', 'Kamal', 'Socah', 'Blega', 'Galis', 'Kalisat', 'Burneh'];
    foreach ($daerah_dummy as $dar) {
        mysqli_query($koneksi, "INSERT INTO daerah (nama_daerah) VALUES ('$dar')");
    }
    echo "<p style='color: blue;'>Daerah/Kabupaten berhasil diseed (10 data).</p>";
}

// 3. SEEDING PENGGUNA (MINIMAL 10)
$cek_pengguna = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengguna");
$total_pengguna = mysqli_fetch_assoc($cek_pengguna)['total'];
if ($total_pengguna < 10) {
    mysqli_query($koneksi, "TRUNCATE TABLE pengguna");
    
    // Admin default
    $sandi_admin = password_hash('admin123', PASSWORD_DEFAULT);
    mysqli_query($koneksi, "INSERT INTO pengguna (nama, email, password, role) VALUES ('Admin HandMadura', 'admin@handmadura.com', '$sandi_admin', 'admin')");
    
    // 9 Users dummy
    $nama_users = ['Budi Santoso', 'Siti Aminah', 'Joko Susilo', 'Rudi Hermawan', 'Ani Lestari', 'Eko Prasetyo', 'Dewi Sartika', 'Bambang Pamungkas', 'Megawati'];
    $emails = ['budi@example.com', 'siti@example.com', 'joko@example.com', 'rudi@example.com', 'ani@example.com', 'eko@example.com', 'dewi@example.com', 'bambang@example.com', 'mega@example.com'];
    $sandi_user = password_hash('user123', PASSWORD_DEFAULT);
    
    for ($i = 0; $i < 9; $i++) {
        $nama_u = $nama_users[$i];
        $email_u = $emails[$i];
        mysqli_query($koneksi, "INSERT INTO pengguna (nama, email, password, role, no_telp, alamat) VALUES ('$nama_u', '$email_u', '$sandi_user', 'user', '0812345678" . $i . "', 'Jl. Raya Madura No. " . ($i + 1) . "')");
    }
    echo "<p style='color: blue;'>Pengguna berhasil diseed (10 akun: 1 admin, 9 user).</p>";
}

// Ambil ID Kategori & Daerah hasil seeder untuk foreign keys produk
$daftar_kategori = [];
$res_kat = mysqli_query($koneksi, "SELECT id FROM kategori");
while ($row = mysqli_fetch_assoc($res_kat)) {
    $daftar_kategori[] = $row['id'];
}

$daftar_daerah = [];
$res_dar = mysqli_query($koneksi, "SELECT id FROM daerah");
while ($row = mysqli_fetch_assoc($res_dar)) {
    $daftar_daerah[] = $row['id'];
}

// 4. SEEDING PRODUK (MINIMAL 10)
$cek_produk = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM produk");
$total_produk = mysqli_fetch_assoc($cek_produk)['total'];
if ($total_produk < 10) {
    mysqli_query($koneksi, "TRUNCATE TABLE produk");
    
    $produk_dummy = [
        ['Batik Tulis Gentongan Madura', 'Batik tulis premium dengan pewarnaan alami gentongan tradisional yang tahan ratusan tahun.', 750000, 15, 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=600&q=80', $daftar_kategori[0], $daftar_daerah[3]],
        ['Gelas Anyaman Lontar', 'Gelas anyaman artistik berbahan dasar daun lontar kering pilihan dari pesisir Sumenep.', 35000, 50, 'https://images.unsplash.com/photo-1595475243560-3ec6d5a01a3f?auto=format&fit=crop&w=600&q=80', $daftar_kategori[1], $daftar_daerah[0]],
        ['Keris Pusaka Madura', 'Keris hias buatan empu besi Sumenep lengkap dengan warangka ukiran kayu jati mewah.', 2500000, 3, 'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?auto=format&fit=crop&w=600&q=80', $daftar_kategori[6], $daftar_daerah[0]],
        ['Miniatur Perahu Pasaran', 'Miniatur perahu layar hias tradisional Madura buatan pengrajin perahu Pamekasan.', 150000, 10, 'https://images.unsplash.com/photo-1545987796-200677ee1011?auto=format&fit=crop&w=600&q=80', $daftar_kategori[3], $daftar_daerah[1]],
        ['Kalung Manik Khas Sumenep', 'Kalung manik-manik etnik rajutan tangan dengan warna-warna cerah khas adat Keraton Sumenep.', 25000, 100, 'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?auto=format&fit=crop&w=600&q=80', $daftar_kategori[2], $daftar_daerah[0]],
        ['Tas Rajut Tali Kur', 'Tas rajut modern buatan tangan menggunakan benang tali kur tebal dan kokoh khas Sampang.', 120000, 25, 'https://images.unsplash.com/photo-1566150905478-db858f17f38a?auto=format&fit=crop&w=600&q=80', $daftar_kategori[4], $daftar_daerah[2]],
        ['Cobek Batu Sampang', 'Cobek dan ulekan batu gunung asli Sampang yang sangat kuat dan dihaluskan secara manual.', 85000, 30, 'https://images.unsplash.com/photo-1588854337236-6889d631faa8?auto=format&fit=crop&w=600&q=80', $daftar_kategori[3], $daftar_daerah[2]],
        ['Kipas Anyaman Bambu', 'Kipas angin tangan anyaman bambu halus bermotif etnik khas Pamekasan yang sejuk.', 15000, 80, 'https://images.unsplash.com/photo-1509319117193-57bab727e09d?auto=format&fit=crop&w=600&q=80', $daftar_kategori[1], $daftar_daerah[1]],
        ['Ukiran Kayu Jati Madura', 'Hiasan dinding ukiran relief kayu jati bermotif floral khas ukiran Bangkalan.', 1200000, 5, 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?auto=format&fit=crop&w=600&q=80', $daftar_kategori[6], $daftar_daerah[3]],
        ['Sabuk Kulit Karapan Sapi', 'Sabuk kulit sapi tebal dengan gesper kuningan bermotif ukiran karapan sapi Madura.', 200000, 12, 'https://images.unsplash.com/photo-1524498250077-3a9f0c578bd6?auto=format&fit=crop&w=600&q=80', $daftar_kategori[9], $daftar_daerah[3]]
    ];
    
    foreach ($produk_dummy as $prod) {
        $nama_p = mysqli_real_escape_string($koneksi, $prod[0]);
        $desk_p = mysqli_real_escape_string($koneksi, $prod[1]);
        $harga_p = $prod[2];
        $stok_p = $prod[3];
        $gbr_p = mysqli_real_escape_string($koneksi, $prod[4]);
        $kat_p = $prod[5];
        $dae_p = $prod[6];
        
        mysqli_query($koneksi, "INSERT INTO produk (nama_produk, deskripsi, harga, stok, gambar, id_kategori, id_daerah) VALUES ('$nama_p', '$desk_p', $harga_p, $stok_p, '$gbr_p', $kat_p, $dae_p)");
    }
    echo "<p style='color: blue;'>Produk berhasil diseed (10 data).</p>";
}

// Ambil ID Pengguna & Produk untuk foreign keys pesanan
$daftar_pengguna = [];
$res_peng = mysqli_query($koneksi, "SELECT id FROM pengguna WHERE role = 'user'");
while ($row = mysqli_fetch_assoc($res_peng)) {
    $daftar_pengguna[] = $row['id'];
}

$daftar_produk = [];
$res_prod = mysqli_query($koneksi, "SELECT id, harga FROM produk");
while ($row = mysqli_fetch_assoc($res_prod)) {
    $daftar_produk[] = $row;
}

// 5. SEEDING TESTIMONIAL (MINIMAL 10)
$cek_testimoni = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM testimonial");
$total_testimoni = mysqli_fetch_assoc($cek_testimoni)['total'];
if ($total_testimoni < 10) {
    mysqli_query($koneksi, "TRUNCATE TABLE testimonial");
    
    $isi_ulasan = [
        'Kualitas batik tulis gentongan-nya sangat luar biasa, warnanya solid dan pengerjaannya sangat rapi.',
        'Sangat senang bisa membeli kerajinan asli dari pengrajin Sumenep langsung. Gelas daun lontarnya cantik sekali!',
        'Keris hiasnya sangat detil dan kokoh. Cocok sekali untuk pajangan di ruang tamu rumah saya.',
        'Perahu pasaran Pamekasan sangat detil. Pengemasan sangat aman dan cepat sampai.',
        'Kalung manik-maniknya lucu sekali! Harganya sangat terjangkau untuk kerajinan tangan seindah ini.',
        'Tas rajutnya sangat kuat dan modis. Muat banyak barang bawaan sehari-hari.',
        'Cobek batu asli Sampang tidak berpasir sama sekali saat dipakai, sangat puas beli di sini.',
        'Kipas anyaman bambunya sejuk sekali dipakai santai di teras rumah. Pengiriman cepat.',
        'Hiasan dinding kayu jatinya sangat megah dan ukirannya sangat dalam khas Madura.',
        'Sabuk kulitnya sangat tebal dan wangi kulit asli. Gesper kuningan ukiran karapannya sangat gagah.'
    ];
    
    $pekerjaan_dummy = ['Pegawai Swasta', 'Mahasiswa', 'Kolektor Seni', 'Ibu Rumah Tangga', 'Guru', 'Wiraswasta', 'Koki', 'Arsitek', 'Dosen', 'Desainer'];
    
    for ($i = 0; $i < 10; $i++) {
        $user_id = $daftar_pengguna[$i % count($daftar_pengguna)];
        $res_u_info = mysqli_query($koneksi, "SELECT nama FROM pengguna WHERE id = $user_id");
        $u_info = mysqli_fetch_assoc($res_u_info);
        $nama_u = mysqli_real_escape_string($koneksi, $u_info['nama']);
        $pekerjaan_u = $pekerjaan_dummy[$i];
        $isi_u = mysqli_real_escape_string($koneksi, $isi_ulasan[$i]);
        $rating_u = rand(4, 5);
        
        mysqli_query($koneksi, "INSERT INTO testimonial (id_pengguna, nama, pekerjaan, isi_ulasan, rating, status) VALUES ($user_id, '$nama_u', '$pekerjaan_u', '$isi_u', $rating_u, 'approved')");
    }
    echo "<p style='color: blue;'>Testimonial berhasil diseed (10 data).</p>";
}

// 6. SEEDING PESANAN & DETAIL PESANAN (MINIMAL 10)
$cek_pesanan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan");
$total_pesanan = mysqli_fetch_assoc($cek_pesanan)['total'];
if ($total_pesanan < 10) {
    mysqli_query($koneksi, "TRUNCATE TABLE pesanan");
    mysqli_query($koneksi, "TRUNCATE TABLE detail_pesanan");
    
    $status_dummy = ['menunggu', 'dibayar', 'dikirim', 'selesai', 'dibatalkan', 'selesai', 'dikirim', 'dibayar', 'menunggu', 'selesai'];
    $metode_dummy = ['BCA Virtual Account', 'Mandiri Virtual Account', 'GoPay', 'Dana', 'Alfamart', 'Indomaret', 'BCA Virtual Account', 'GoPay', 'Dana', 'Indomaret'];
    
    for ($i = 0; $i < 10; $i++) {
        $user_id = $daftar_pengguna[$i % count($daftar_pengguna)];
        $prod_dummy_1 = $daftar_produk[$i % count($daftar_produk)];
        $prod_dummy_2 = $daftar_produk[($i + 1) % count($daftar_produk)];
        
        $jumlah1 = rand(1, 2);
        $jumlah2 = rand(1, 2);
        $total_h = ($prod_dummy_1['harga'] * $jumlah1) + ($prod_dummy_2['harga'] * $jumlah2);
        
        $stat = $status_dummy[$i];
        $metode = $metode_dummy[$i];
        $alamat = "Jl. Alamat Seeder No. " . ($i + 10) . ", Madura";
        $bukti = $stat !== 'menunggu' ? 'bukti_dummy_' . $i . '.jpg' : '';
        
        mysqli_query($koneksi, "INSERT INTO pesanan (id_pengguna, total_harga, status, alamat, metode_pembayaran, bukti_pembayaran) VALUES ($user_id, $total_h, '$stat', '$alamat', '$metode', '$bukti')");
        $id_pesanan_baru = mysqli_insert_id($koneksi);
        
        // Detail Pesanan 1
        $id_p1 = $prod_dummy_1['id'];
        $harga1 = $prod_dummy_1['harga'];
        mysqli_query($koneksi, "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga) VALUES ($id_pesanan_baru, $id_p1, $jumlah1, $harga1)");
        
        // Detail Pesanan 2
        $id_p2 = $prod_dummy_2['id'];
        $harga2 = $prod_dummy_2['harga'];
        mysqli_query($koneksi, "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga) VALUES ($id_pesanan_baru, $id_p2, $jumlah2, $harga2)");
    }
    echo "<p style='color: blue;'>Pesanan & Detail Pesanan berhasil diseed (masing-masing 10 data).</p>";
}

// 7. SEEDING KERANJANG (MINIMAL 10)
$cek_keranjang = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM keranjang");
$total_keranjang = mysqli_fetch_assoc($cek_keranjang)['total'];
if ($total_keranjang < 10) {
    mysqli_query($koneksi, "TRUNCATE TABLE keranjang");
    
    for ($i = 0; $i < 10; $i++) {
        $user_id = $daftar_pengguna[$i % count($daftar_pengguna)];
        $prod_id = $daftar_produk[($i + 3) % count($daftar_produk)]['id'];
        $jumlah = rand(1, 3);
        
        mysqli_query($koneksi, "INSERT INTO keranjang (id_pengguna, id_produk, jumlah) VALUES ($user_id, $prod_id, $jumlah)");
    }
    echo "<p style='color: blue;'>Keranjang belanja berhasil diseed (10 data).</p>";
}

mysqli_query($koneksi, "SET FOREIGN_KEY_CHECKS = 1");

echo "</div>";
?>
