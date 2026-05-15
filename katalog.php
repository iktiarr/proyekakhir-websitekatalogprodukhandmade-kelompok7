<?php
include 'koneksi.php';

$kat_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$query_kat = "SELECT * FROM kategori";
$res_kat = mysqli_query($conn, $query_kat);

$where = "";
if ($kat_id > 0) {
    $where = " WHERE id_kategori = $kat_id";
}

$query_produk = "SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id $where";
$res_produk = mysqli_query($conn, $query_produk);
?>

<?php include 'includes/header.php'; ?>

<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900">Katalog Produk</h1>
            <p class="text-gray-500 mt-2">Jelajahi karya seni terbaik dari pengrajin kami.</p>
        </div>

        <!-- Filter Kategori -->
        <div class="flex flex-wrap gap-3 mb-12">
            <a href="katalog.php" class="<?= $kat_id == 0 ? 'bg-amber-600 text-white shadow-lg shadow-amber-200' : 'bg-white text-gray-600 hover:bg-gray-100' ?> px-6 py-2.5 rounded-xl font-bold transition border border-transparent">
                Semua
            </a>
            <?php while($k = mysqli_fetch_assoc($res_kat)): ?>
                <a href="katalog.php?kategori=<?= $k['id']; ?>" class="<?= $kat_id == $k['id'] ? 'bg-amber-600 text-white shadow-lg shadow-amber-200' : 'bg-white text-gray-600 hover:bg-gray-100' ?> px-6 py-2.5 rounded-xl font-bold transition border border-transparent">
                    <?= $k['nama_kategori']; ?>
                </a>
            <?php endwhile; ?>
        </div>

        <!-- Grid Produk -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php if (mysqli_num_rows($res_produk) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($res_produk)): ?>
                <div class="group bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 border border-gray-100">
                    <div class="relative h-64 overflow-hidden">
                        <img src="<?= $row['gambar'] ?: 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=500'; ?>" alt="<?= $row['nama_produk']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        <div class="absolute top-4 left-4">
                            <span class="bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-amber-600 uppercase tracking-wider">
                                <?= $row['nama_kategori']; ?>
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-amber-600 transition"><?= $row['nama_produk']; ?></h3>
                        <p class="text-gray-500 text-sm mt-2 line-clamp-2"><?= $row['deskripsi']; ?></p>
                        <div class="mt-6 flex justify-between items-center">
                            <div class="flex flex-col">
                                <span class="text-xs text-gray-400">Harga</span>
                                <span class="text-xl font-extrabold text-gray-900">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></span>
                            </div>
                            <a href="produk_detail.php?id=<?= $row['id']; ?>" class="p-3 bg-gray-50 text-amber-600 rounded-xl group-hover:bg-amber-600 group-hover:text-white transition shadow-sm">
                                <i class="fa-solid fa-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full py-40 text-center bg-white rounded-[3rem] border border-dashed border-gray-200">
                    <div class="max-w-xs mx-auto">
                        <i class="fa-solid fa-box-open text-6xl text-gray-200 mb-6"></i>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Produk Tidak Ditemukan</h3>
                        <p class="text-gray-500 mb-8">Maaf, kategori ini belum memiliki produk yang tersedia saat ini.</p>
                        <a href="katalog.php" class="inline-block bg-amber-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-amber-700 transition shadow-lg shadow-amber-200">Reset Filter</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
