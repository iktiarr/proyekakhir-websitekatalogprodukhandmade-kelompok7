<?php
include 'koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: katalog.php");
    exit();
}

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id WHERE p.id = $id");
$produk = mysqli_fetch_assoc($query);

if (!$produk) {
    header("Location: katalog.php");
    exit();
}

// Tambah ke keranjang
if (isset($_POST['tambah_keranjang'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: masuk.php");
        exit();
    }

    $id_pengguna = $_SESSION['user_id'];
    $jumlah = (int)$_POST['jumlah'];
    
    // Cek jika produk sudah ada di keranjang
    $check = mysqli_query($conn, "SELECT * FROM keranjang WHERE id_pengguna = $id_pengguna AND id_produk = $id");
    if (mysqli_num_rows($check) > 0) {
        mysqli_query($conn, "UPDATE keranjang SET jumlah = jumlah + $jumlah WHERE id_pengguna = $id_pengguna AND id_produk = $id");
    } else {
        mysqli_query($conn, "INSERT INTO keranjang (id_pengguna, id_produk, jumlah) VALUES ($id_pengguna, $id, $jumlah)");
    }
    header("Location: keranjang.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="lg:flex lg:space-x-12">
            <!-- Galeri Gambar -->
            <div class="lg:w-1/2">
                <div class="relative rounded-[2.5rem] overflow-hidden shadow-2xl bg-gray-100 aspect-square">
                    <img src="<?= $produk['gambar'] ?: 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=1000'; ?>" alt="<?= $produk['nama_produk']; ?>" class="w-full h-full object-cover transform hover:scale-110 transition duration-700">
                    <div class="absolute top-6 left-6">
                        <span class="bg-white/90 backdrop-blur px-4 py-2 rounded-full text-sm font-bold text-amber-600 shadow-lg uppercase tracking-widest">
                            <?= $produk['nama_kategori']; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Detail Produk -->
            <div class="lg:w-1/2 mt-12 lg:mt-0">
                <nav class="flex mb-4 text-sm font-medium text-gray-400">
                    <a href="index.php" class="hover:text-amber-600">Beranda</a>
                    <span class="mx-2">/</span>
                    <a href="katalog.php" class="hover:text-amber-600">Katalog</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-900"><?= $produk['nama_produk']; ?></span>
                </nav>

                <h1 class="text-4xl font-extrabold text-gray-900 leading-tight mb-4"><?= $produk['nama_produk']; ?></h1>
                
                <div class="flex items-center space-x-4 mb-8">
                    <span class="text-3xl font-extrabold text-amber-600">Rp <?= number_format($produk['harga'], 0, ',', '.'); ?></span>
                    <div class="h-6 w-[1px] bg-gray-200"></div>
                    <span class="text-gray-500 font-medium">Stok: <?= $produk['stok']; ?> unit</span>
                </div>

                <div class="bg-gray-50 rounded-3xl p-8 mb-8 border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Deskripsi Produk</h3>
                    <p class="text-gray-600 leading-relaxed">
                        <?= nl2br($produk['deskripsi']); ?>
                    </p>
                </div>

                <form action="" method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-900 mb-3 uppercase tracking-wider">Jumlah</label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center border-2 border-gray-100 rounded-2xl bg-white p-1">
                                <button type="button" onclick="changeQty(-1)" class="w-12 h-12 flex items-center justify-center hover:bg-gray-50 rounded-xl transition text-gray-500">
                                    <i class="fa-solid fa-minus"></i>
                                </button>
                                <input type="number" name="jumlah" id="qtyInput" value="1" min="1" max="<?= $produk['stok']; ?>" class="w-16 text-center font-bold bg-transparent border-none focus:ring-0 outline-none">
                                <button type="button" onclick="changeQty(1)" class="w-12 h-12 flex items-center justify-center hover:bg-gray-50 rounded-xl transition text-gray-500">
                                    <i class="fa-solid fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="submit" name="tambah_keranjang" class="flex-1 bg-amber-600 text-white py-4 rounded-2xl font-bold text-lg hover:bg-amber-700 transition shadow-xl shadow-amber-200 flex items-center justify-center">
                            <i class="fa-solid fa-cart-plus mr-3"></i> Tambah ke Keranjang
                        </button>
                    </div>
                </form>

                <div class="mt-12 pt-8 border-t border-gray-50">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-50 rounded-full flex items-center justify-center text-green-600">
                                <i class="fa-solid fa-truck-fast"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-500">Pengiriman Cepat</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-full flex items-center justify-center text-blue-600">
                                <i class="fa-solid fa-shield-check"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-500">Kualitas Terjamin</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function changeQty(amt) {
        const input = document.getElementById('qtyInput');
        let val = parseInt(input.value) + amt;
        if (val < 1) val = 1;
        if (val > <?= $produk['stok']; ?>) val = <?= $produk['stok']; ?>;
        input.value = val;
    }
</script>

<?php include 'includes/footer.php'; ?>
