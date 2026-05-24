<?php
include 'koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: katalog.php");
    exit();
}

$id = (int)$_GET['id'];
$query = mysqli_query($conn, "SELECT p.*, k.nama_kategori, d.nama_daerah FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id LEFT JOIN daerah d ON p.id_daerah = d.id WHERE p.id = $id");
$produk = mysqli_fetch_assoc($query);

if (!$produk) {
    header("Location: katalog.php");
    exit();
}


if (isset($_POST['tambah_keranjang'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: masuk.php");
        exit();
    }

    $id_pengguna = $_SESSION['user_id'];
    $jumlah = (int)$_POST['jumlah'];
    
    
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

<div class="py-8 bg-white dark:bg-slate-900 min-h-[70vh] transition-colors duration-300">
    <div class="max-w-5xl mx-auto px-4">
        
        <div class="lg:flex lg:gap-8 items-start">
            <!-- Kolom Kiri: Gambar Produk (Compact) -->
            <div class="lg:w-5/12 w-full mb-6 lg:mb-0 max-w-sm mx-auto lg:max-w-none">
                <div class="relative rounded-2xl overflow-hidden shadow-sm border border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 aspect-square group">
                    <img 
                        src="<?= $produk['gambar'] ?: 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=1000'; ?>" 
                        alt="<?= $produk['nama_produk']; ?>" 
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=1000';"
                    >
                    <div class="absolute top-3 left-3 flex gap-2">
                        <span class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-slate-700 dark:text-slate-200 shadow-sm border border-slate-100 dark:border-slate-800 uppercase tracking-widest">
                            <?= $produk['nama_kategori']; ?>
                        </span>
                        <?php if(!empty($produk['nama_daerah'])): ?>
                            <span class="bg-lime-600/95 backdrop-blur-sm px-2.5 py-1.5 rounded-lg text-[10px] font-bold text-white shadow-sm border border-lime-600 uppercase tracking-widest flex items-center gap-1">
                                <i class="fa-solid fa-location-dot"></i> <?= $produk['nama_daerah']; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Detail & Form (Compact) -->
            <div class="lg:w-7/12 flex flex-col pl-0 lg:pl-4">
                
                <nav class="flex mb-3 text-xs font-semibold text-slate-400 dark:text-slate-550">
                    <a href="index.php" class="hover:text-lime-600 dark:hover:text-lime-400 transition-colors">Beranda</a>
                    <span class="mx-1.5 text-slate-300 dark:text-slate-700">/</span>
                    <a href="katalog.php" class="hover:text-lime-600 dark:hover:text-lime-400 transition-colors">Katalog</a>
                    <span class="mx-1.5 text-slate-300 dark:text-slate-700">/</span>
                    <span class="text-slate-700 dark:text-slate-300 truncate max-w-[200px]"><?= $produk['nama_produk']; ?></span>
                </nav>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 dark:text-slate-100 leading-tight mb-3">
                    <?= $produk['nama_produk']; ?>
                </h1>
                
                <div class="flex items-center space-x-3 mb-5 border-b border-slate-50 dark:border-slate-800 pb-4">
                    <span class="text-xl sm:text-2xl font-extrabold text-lime-600 dark:text-lime-400">
                        Rp <?= number_format($produk['harga'], 0, ',', '.'); ?>
                    </span>
                    <div class="h-4 w-px bg-slate-200 dark:bg-slate-800"></div>
                    <span class="text-xs sm:text-sm text-slate-500 dark:text-slate-450 font-medium">
                        Stok: <span class="text-slate-800 dark:text-slate-200 font-bold"><?= $produk['stok']; ?></span> unit
                    </span>
                </div>

                <div class="bg-slate-50/50 dark:bg-slate-950/50 rounded-xl p-4 sm:p-5 mb-5 border border-slate-100/80 dark:border-slate-800">
                    <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider mb-2">Deskripsi Produk</h3>
                    <p class="text-slate-600 dark:text-slate-350 leading-relaxed text-xs sm:text-sm">
                        <?= nl2br($produk['deskripsi']); ?>
                    </p>
                </div>

                <form action="" method="POST" class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 mb-1.5 uppercase tracking-widest">Jumlah</label>
                            <div class="inline-flex items-center border border-slate-200 dark:border-slate-800 rounded-xl bg-white dark:bg-slate-800 p-0.5 shadow-sm">
                                <button type="button" onclick="changeQty(-1)" class="w-8 h-8 flex items-center justify-center hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-lime-600 dark:hover:text-lime-400 rounded-lg transition-colors text-slate-400 dark:text-slate-500">
                                    <i class="fa-solid fa-minus text-xs"></i>
                                </button>
                                
                                <input type="number" name="jumlah" id="qtyInput" value="1" min="1" max="<?= $produk['stok']; ?>" class="w-10 text-center font-bold text-slate-800 dark:text-slate-200 bg-transparent border-none focus:ring-0 outline-none p-0 text-sm">
                                
                                <button type="button" onclick="changeQty(1)" class="w-8 h-8 flex items-center justify-center hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-lime-600 dark:hover:text-lime-400 rounded-lg transition-colors text-slate-400 dark:text-slate-500">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" name="tambah_keranjang" class="group w-full sm:w-auto inline-flex bg-lime-600 text-white py-2.5 px-6 rounded-xl font-bold text-sm hover:bg-lime-700 hover:shadow-lg hover:shadow-lime-200/40 hover:-translate-y-0.5 transition-all duration-300 items-center justify-center cursor-pointer border-none">
                        <i class="fa-solid fa-cart-plus mr-2 group-hover:scale-110 transition-transform"></i> Tambah ke Keranjang
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
        
        if (val < 1) val = 1;
        if (val > <?= $produk['stok']; ?>) val = <?= $produk['stok']; ?>;
        
        input.value = val;
    }
</script>

<?php include 'includes/footer.php'; ?>