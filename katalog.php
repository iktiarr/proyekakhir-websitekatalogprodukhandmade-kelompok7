<?php
include 'koneksi.php';

$kat_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$daerah_id = isset($_GET['daerah']) ? (int)$_GET['daerah'] : 0;

$query_kat = "SELECT * FROM kategori";
$res_kat = mysqli_query($conn, $query_kat);

$query_daerah = "SELECT * FROM daerah";
$res_daerah = mysqli_query($conn, $query_daerah);

$where = [];
if ($kat_id > 0) {
    $where[] = "p.id_kategori = $kat_id";
}
if ($daerah_id > 0) {
    $where[] = "p.id_daerah = $daerah_id";
}

$where_clause = "";
if (count($where) > 0) {
    $where_clause = " WHERE " . implode(" AND ", $where);
}

$query_produk = "SELECT p.*, k.nama_kategori, d.nama_daerah FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id LEFT JOIN daerah d ON p.id_daerah = d.id $where_clause";
$res_produk = mysqli_query($conn, $query_produk);
?>

<?php include 'includes/header.php'; ?>

<div class="py-8 bg-slate-50 dark:bg-slate-950 min-h-[75vh] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-5 text-center sm:text-left">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 dark:text-slate-100">Katalog Produk</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs sm:text-sm mt-1 max-w-2xl">Jelajahi karya seni terbaik dari pengrajin kami. Temukan produk yang sesuai dengan selera Anda.</p>
        </div>

        <!-- Filter Kategori & Daerah -->
        <div class="mb-5 space-y-4">
            <div>
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5"><i class="fa-solid fa-tags mr-1"></i>Kategori</p>
                <div class="flex flex-wrap items-center gap-1.5">
                    <a href="katalog.php<?= $daerah_id > 0 ? '?daerah=' . $daerah_id : ''; ?>" class="<?= $kat_id == 0 ? 'bg-lime-600 text-white shadow-sm border-lime-600' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 hover:border-lime-200 dark:hover:border-lime-700' ?> px-3 py-1 rounded-xl font-bold text-xs border transition-all duration-200">
                        Semua Kategori
                    </a>
                    
                    <?php 
                    mysqli_data_seek($res_kat, 0);
                    while($k = mysqli_fetch_assoc($res_kat)): ?>
                        <a href="katalog.php?kategori=<?= $k['id']; ?><?= $daerah_id > 0 ? '&daerah=' . $daerah_id : ''; ?>" class="<?= $kat_id == $k['id'] ? 'bg-lime-600 text-white shadow-sm border-lime-600' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 hover:border-lime-200 dark:hover:border-lime-700' ?> px-3 py-1 rounded-xl font-bold text-xs border transition-all duration-200">
                            <?= $k['nama_kategori']; ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>

            <div>
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1.5"><i class="fa-solid fa-location-dot mr-1"></i>Asal Daerah</p>
                <div class="flex flex-wrap items-center gap-1.5">
                    <a href="katalog.php<?= $kat_id > 0 ? '?kategori=' . $kat_id : ''; ?>" class="<?= $daerah_id == 0 ? 'bg-lime-600 text-white shadow-sm border-lime-600' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 hover:border-lime-200 dark:hover:border-lime-700' ?> px-3 py-1 rounded-xl font-bold text-xs border transition-all duration-200">
                        Semua Daerah
                    </a>
                    
                    <?php 
                    while($d = mysqli_fetch_assoc($res_daerah)): ?>
                        <a href="katalog.php?daerah=<?= $d['id']; ?><?= $kat_id > 0 ? '&kategori=' . $kat_id : ''; ?>" class="<?= $daerah_id == $d['id'] ? 'bg-lime-600 text-white shadow-sm border-lime-600' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 hover:border-lime-200 dark:hover:border-lime-700' ?> px-3 py-1 rounded-xl font-bold text-xs border transition-all duration-200">
                            <?= $d['nama_daerah']; ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- Grid Produk (Compact) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php if (mysqli_num_rows($res_produk) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($res_produk)): ?>
                
                <div class="group bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-lime-900/5 hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    
                    <div class="relative h-44 sm:h-48 overflow-hidden bg-slate-100 dark:bg-slate-800">
                        <img 
                            src="<?= $row['gambar'] ?: 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=500'; ?>" 
                            alt="<?= $row['nama_produk']; ?>" 
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=500';"
                        >
                        <div class="absolute top-2.5 left-2.5 flex gap-1.5">
                            <span class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-sm px-2 py-1 rounded text-[9px] font-bold text-slate-700 dark:text-slate-200 uppercase tracking-widest shadow-sm">
                                <?= $row['nama_kategori']; ?>
                            </span>
                            <?php if(!empty($row['nama_daerah'])): ?>
                                <span class="bg-lime-600/90 dark:bg-lime-600/80 backdrop-blur-sm px-2 py-1 rounded text-[9px] font-bold text-white uppercase tracking-widest shadow-sm flex items-center gap-0.5">
                                    <i class="fa-solid fa-location-dot"></i> <?= $row['nama_daerah']; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="p-4 sm:p-4.5 flex flex-col flex-grow">
                        <h3 class="text-sm sm:text-base font-bold text-slate-800 dark:text-slate-200 group-hover:text-lime-600 dark:group-hover:text-lime-400 transition-colors duration-300 line-clamp-1">
                            <?= $row['nama_produk']; ?>
                        </h3>
                        
                        <p class="text-slate-500 dark:text-slate-400 text-xs mt-1.5 line-clamp-2 flex-grow">
                            <?= $row['deskripsi']; ?>
                        </p>
                        
                        <div class="mt-3 flex justify-between items-center pt-3 border-t border-slate-50 dark:border-slate-800/80">
                            <span class="text-base font-extrabold text-lime-600 dark:text-lime-400">
                                Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
                            </span>
                            
                            <a href="produk_detail.php?id=<?= $row['id']; ?>" class="w-8 h-8 flex items-center justify-center bg-slate-50 dark:bg-slate-800 text-slate-400 dark:text-slate-500 rounded-full group-hover:bg-lime-600 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-arrow-right text-xs -rotate-45 group-hover:rotate-0 transition-transform duration-300"></i>
                            </a>
                        </div>
                    </div>
                    
                </div>
                <?php endwhile; ?>
                
            <?php else: ?>
                
                <div class="col-span-full py-16 text-center bg-white dark:bg-slate-900 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                    <div class="max-w-md mx-auto px-4">
                        <div class="w-12 h-12 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 dark:text-slate-600 text-xl">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-1">Produk Tidak Ditemukan</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-xs mb-4">Maaf, kategori yang Anda pilih belum memiliki produk saat ini. Silakan coba kategori lainnya.</p>
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