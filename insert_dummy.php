<?php
include 'koneksi.php';

$dummy_products = [
    [
        'nama' => 'Gelang Manik Etnik',
        'deskripsi' => 'Gelang manik-manik handmade dengan motif etnik khas Nusantara. Cocok untuk aksesoris harian.',
        'harga' => 45000,
        'stok' => 20,
        'gambar' => 'https://images.unsplash.com/photo-1611591437281-460bfbe1220a?auto=format&fit=crop&q=80&w=500',
        'kat' => 1 // Aksesoris
    ],
    [
        'nama' => 'Tas Rajut Boho',
        'deskripsi' => 'Tas rajut tangan (crochet) dengan gaya bohemian. Kuat, stylish, dan ramah lingkungan.',
        'harga' => 125000,
        'stok' => 10,
        'gambar' => 'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?auto=format&fit=crop&q=80&w=500',
        'kat' => 3 // Pakaian/Aksesoris
    ],
    [
        'nama' => 'Lilin Aromaterapi Lavender',
        'deskripsi' => 'Lilin aromaterapi dari soy wax alami dengan essential oil lavender yang menenangkan.',
        'harga' => 65000,
        'stok' => 15,
        'gambar' => 'https://images.unsplash.com/photo-1603006905003-be475563bc59?auto=format&fit=crop&q=80&w=500',
        'kat' => 2 // Dekorasi
    ],
    [
        'nama' => 'Kalung Kayu Ukir',
        'deskripsi' => 'Kalung unik dari kayu jati yang diukir manual oleh pengrajin lokal Bali.',
        'harga' => 85000,
        'stok' => 8,
        'gambar' => 'https://images.unsplash.com/photo-1535632066927-ab7c9ab60908?auto=format&fit=crop&q=80&w=500',
        'kat' => 1 // Aksesoris
    ],
    [
        'nama' => 'Macrame Wall Hanging',
        'deskripsi' => 'Hiasan dinding macrame cantik untuk mempermanis ruang tamu atau kamar tidur Anda.',
        'harga' => 150000,
        'stok' => 5,
        'gambar' => 'https://images.unsplash.com/photo-1515516089376-88db1e26e9c0?auto=format&fit=crop&q=80&w=500',
        'kat' => 2 // Dekorasi
    ],
    [
        'nama' => 'Cangkir Keramik Lukis',
        'deskripsi' => 'Cangkir keramik yang dilukis tangan dengan motif floral. Microwave & dishwasher safe.',
        'harga' => 95000,
        'stok' => 12,
        'gambar' => 'https://images.unsplash.com/photo-1514228742587-6b1558fcca3d?auto=format&fit=crop&q=80&w=500',
        'kat' => 2 // Dekorasi
    ],
    [
        'nama' => 'Notebook Sampul Kain Batik',
        'deskripsi' => 'Buku catatan dengan sampul kain batik tulis asli. Kertas berkualitas acid-free.',
        'harga' => 55000,
        'stok' => 30,
        'gambar' => 'https://images.unsplash.com/photo-1531346878377-a5be20888e57?auto=format&fit=crop&q=80&w=500',
        'kat' => 4 // Lainnya
    ],
    [
        'nama' => 'Sandal Kulit Handmade',
        'deskripsi' => 'Sandal dari kulit sapi asli yang dijahit tangan. Sangat nyaman dan awet digunakan.',
        'harga' => 185000,
        'stok' => 7,
        'gambar' => 'https://images.unsplash.com/photo-1562273138-f46be4ebdf33?auto=format&fit=crop&q=80&w=500',
        'kat' => 3 // Pakaian
    ],
    [
        'nama' => 'Anting Clay Floral',
        'deskripsi' => 'Anting handmade dari polymer clay dengan detail bunga yang sangat halus dan ringan.',
        'harga' => 35000,
        'stok' => 25,
        'gambar' => 'https://images.unsplash.com/photo-1635767790414-061066468a5c?auto=format&fit=crop&q=80&w=500',
        'kat' => 1 // Aksesoris
    ],
    [
        'nama' => 'Talenan Kayu Mahoni',
        'deskripsi' => 'Talenan kayu mahoni solid dengan finishing food-grade oil. Cantik untuk dekorasi dapur.',
        'harga' => 75000,
        'stok' => 15,
        'gambar' => 'https://images.unsplash.com/photo-1616731948644-87d04e3f2cc6?auto=format&fit=crop&q=80&w=500',
        'kat' => 2 // Dekorasi
    ]
];

echo "<h1>Memasukkan Data Dummy...</h1>";

foreach ($dummy_products as $p) {
    $nama = $p['nama'];
    $desc = $p['deskripsi'];
    $harga = $p['harga'];
    $stok = $p['stok'];
    $img = $p['gambar'];
    $kat = $p['kat'];

    $query = "INSERT INTO produk (nama_produk, deskripsi, harga, stok, gambar, id_kategori) VALUES ('$nama', '$desc', $harga, $stok, '$img', $kat)";
    
    if (mysqli_query($conn, $query)) {
        echo "<p style='color: green;'>Berhasil: $nama</p>";
    } else {
        echo "<p style='color: red;'>Gagal: " . mysqli_error($conn) . "</p>";
    }
}

echo "<br><a href='index.php'>Kembali ke Beranda</a>";
?>
