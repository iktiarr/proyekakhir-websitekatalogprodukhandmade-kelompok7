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

<div class="py-8 bg-slate-50 min-h-[75vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-5 text-center sm:text-left">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800">Katalog Produk</h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1 max-w-2xl">Jelajahi karya seni terbaik dari pengrajin kami. Temukan produk yang sesuai dengan selera Anda.</p>
        </div>

        <!-- Filter Kategori (Compact) -->
        <div class="flex flex-wrap items-center gap-2 mb-6">
            <a href="katalog.php" class="<?= $kat_id == 0 ? 'bg-lime-600 text-white shadow-sm border-lime-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-lime-600 hover:border-lime-200' ?> px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all duration-300 border">
                Semua Produk
            </a>
            
            <?php 
            mysqli_data_seek($res_kat, 0);
            while($k = mysqli_fetch_assoc($res_kat)): ?>
                <a href="katalog.php?kategori=<?= $k['id']; ?>" class="<?= $kat_id == $k['id'] ? 'bg-lime-600 text-white shadow-sm border-lime-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:text-lime-600 hover:border-lime-200' ?> px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all duration-300 border">
                    <?= $k['nama_kategori']; ?>
                </a>
            <?php endwhile; ?>
        </div>

        <!-- Grid Produk (Compact) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php if (mysqli_num_rows($res_produk) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($res_produk)): ?>
                
                <div class="group bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-lime-900/5 hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    
                    <div class="relative h-44 sm:h-48 overflow-hidden bg-slate-100">
                        <img 
                            src="<?= $row['gambar'] ?: 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=500'; ?>" 
                            alt="<?= $row['nama_produk']; ?>" 
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=500';"
                        >
                        <div class="absolute top-2.5 left-2.5">
                            <span class="bg-white/90 backdrop-blur-sm px-2 py-1 rounded text-[9px] font-bold text-slate-700 uppercase tracking-widest shadow-sm">
                                <?= $row['nama_kategori']; ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-4 sm:p-4.5 flex flex-col flex-grow">
                        <h3 class="text-sm sm:text-base font-bold text-slate-800 group-hover:text-lime-600 transition-colors duration-300 line-clamp-1">
                            <?= $row['nama_produk']; ?>
                        </h3>
                        
                        <p class="text-slate-500 text-xs mt-1.5 line-clamp-2 flex-grow">
                            <?= $row['deskripsi']; ?>
                        </p>
                        
                        <div class="mt-3 flex justify-between items-center pt-3 border-t border-slate-50">
                            <span class="text-base font-extrabold text-lime-600">
                                Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                            </span>
                            
                            <a href="produk_detail.php?id=<?= $row['id']; ?>" class="w-8 h-8 flex items-center justify-center bg-slate-50 text-slate-400 rounded-full group-hover:bg-lime-600 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-arrow-right text-xs -rotate-45 group-hover:rotate-0 transition-transform duration-300"></i>
                            </a>
                        </div>
                    </div>
                    
                </div>
                <?php endwhile; ?>
                
            <?php else: ?>
                
                <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-dashed border-slate-200">
                    <div class="max-w-md mx-auto px-4">
                        <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 text-xl">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-800 mb-1">Produk Tidak Ditemukan</h3>
                        <p class="text-slate-500 text-xs mb-4">Maaf, kategori yang Anda pilih belum memiliki produk saat ini. Silakan coba kategori lainnya.</p>
                        <a href="katalog.php" class="inline-flex items-center justify-center bg-lime-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-lime-700 hover:shadow-lg hover:shadow-lime-200/40 hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">
                            Lihat Semua Produk
                        </a>
                    </div>
                </div>
                
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>