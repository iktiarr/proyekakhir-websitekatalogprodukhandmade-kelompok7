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

<div class="py-16 lg:py-24 bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="lg:flex lg:gap-12 xl:gap-16 items-start">
            <div class="lg:w-1/2 w-full mb-10 lg:mb-0">
                <div class="relative rounded-3xl overflow-hidden shadow-sm border border-slate-100 bg-slate-50 aspect-square group">
                    <img 
                        src="<?= $produk['gambar'] ?: 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=1000'; ?>" 
                        alt="<?= $produk['nama_produk']; ?>" 
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                    >
                    <div class="absolute top-4 left-4 sm:top-6 sm:left-6">
                        <span class="bg-white/90 backdrop-blur-sm px-4 py-2 rounded-xl text-xs sm:text-sm font-bold text-slate-700 shadow-sm border border-slate-100/50 uppercase tracking-widest">
                            <?= $produk['nama_kategori']; ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="lg:w-1/2 flex flex-col">
                
                <nav class="flex mb-4 sm:mb-6 text-xs sm:text-sm font-medium text-slate-400">
                    <a href="index.php" class="hover:text-lime-600 transition-colors">Beranda</a>
                    <span class="mx-2 text-slate-300">/</span>
                    <a href="katalog.php" class="hover:text-lime-600 transition-colors">Katalog</a>
                    <span class="mx-2 text-slate-300">/</span>
                    <span class="text-slate-800 truncate"><?= $produk['nama_produk']; ?></span>
                </nav>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-slate-800 leading-tight mb-4 sm:mb-6">
                    <?= $produk['nama_produk']; ?>
                </h1>
                
                <div class="flex items-center space-x-4 mb-8">
                    <span class="text-2xl sm:text-3xl font-extrabold text-lime-600">
                        Rp <?= number_format($produk['harga'], 0, ',', '.'); ?>
                    </span>
                    <div class="h-6 w-px bg-slate-200"></div>
                    <span class="text-sm sm:text-base text-slate-500 font-medium">
                        Stok: <span class="text-slate-800 font-bold"><?= $produk['stok']; ?></span> unit
                    </span>
                </div>

                <div class="bg-slate-50 rounded-2xl p-6 sm:p-8 mb-8 border border-slate-100">
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 mb-3">Deskripsi Produk</h3>
                    <p class="text-slate-600 leading-relaxed text-sm sm:text-base">
                        <?= nl2br($produk['deskripsi']); ?>
                    </p>
                </div>

                <form action="" method="POST" class="space-y-6 sm:space-y-8">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 mb-3 uppercase tracking-widest">Jumlah</label>
                        <div class="inline-flex items-center border border-slate-200 rounded-xl bg-white p-1">
                            <button type="button" onclick="changeQty(-1)" class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center hover:bg-slate-50 hover:text-lime-600 rounded-lg transition-colors text-slate-400">
                                <i class="fa-solid fa-minus text-sm"></i>
                            </button>
                            
                            <input type="number" name="jumlah" id="qtyInput" value="1" min="1" max="<?= $produk['stok']; ?>" class="w-14 sm:w-16 text-center font-bold text-slate-800 bg-transparent border-none focus:ring-0 outline-none p-0">
                            
                            <button type="button" onclick="changeQty(1)" class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center hover:bg-slate-50 hover:text-lime-600 rounded-lg transition-colors text-slate-400">
                                <i class="fa-solid fa-plus text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" name="tambah_keranjang" class="group w-full sm:w-auto inline-flex bg-lime-600 text-white py-3.5 sm:py-4 px-8 rounded-xl font-bold text-base hover:bg-lime-700 hover:-translate-y-1 hover:shadow-lg hover:shadow-lime-200/50 transition-all duration-300 items-center justify-center">
                        <i class="fa-solid fa-cart-plus mr-3 group-hover:scale-110 transition-transform duration-300"></i> Tambah ke Keranjang
                    </button>
                </form>                
            </div>
        </div>
        
    </div>
</div>

<script>
    function changeQty(amt) {
        const input = document.getElementById('qtyInput');
        let val = parseInt(input.value) + amt;
        
        // Batasan Minimal dan Maksimal
        if (val < 1) val = 1;
        if (val > <?= $produk['stok']; ?>) val = <?= $produk['stok']; ?>;
        
        input.value = val;
    }
</script>

<?php include 'includes/footer.php'; ?>