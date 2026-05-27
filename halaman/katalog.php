<?php
$awalan = "../";
include '../koneksi.php';

$id_kategori = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$id_daerah = isset($_GET['daerah']) ? (int)$_GET['daerah'] : 0;

$kueri_kategori = "SELECT * FROM kategori";
$hasil_kategori = mysqli_query($koneksi, $kueri_kategori);

$kueri_daerah = "SELECT * FROM daerah";
$hasil_daerah = mysqli_query($koneksi, $kueri_daerah);

$kondisi = [];
if ($id_kategori > 0) {
    $kondisi[] = "p.id_kategori = $id_kategori";
}
if ($id_daerah > 0) {
    $kondisi[] = "p.id_daerah = $id_daerah";
}

$klausa_kondisi = "";
if (count($kondisi) > 0) {
    $klausa_kondisi = " WHERE " . implode(" AND ", $kondisi);
}

$kueri_produk = "SELECT p.*, k.nama_kategori, d.nama_daerah FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id LEFT JOIN daerah d ON p.id_daerah = d.id $klausa_kondisi";
$hasil_produk = mysqli_query($koneksi, $kueri_produk);
?>

<?php include '../bagian/atas.php'; ?>

<div class="py-12 sm:py-16 bg-slate-50 dark:bg-slate-950 min-h-[80vh] transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 text-center sm:text-left">
            <h1 class="text-3xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Katalog HandMadura</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5 max-w-2xl leading-relaxed">Jelajahi kerajinan tradisional terbaik karya maestro lokal. Dukung langsung pelestarian warisan budaya melalui HandMadura dengan setiap pembelian.</p>
        </div>

        <div class="mb-8 space-y-5 bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200/60 dark:border-slate-800 shadow-sm">
            <div>
                <p class="text-[10px] font-extrabold text-slate-450 dark:text-slate-500 uppercase tracking-widest mb-2.5 flex items-center gap-1"><i class="fa-solid fa-tags text-lime-600"></i> Kategori Kerajinan</p>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="katalog.php<?= $id_daerah > 0 ? '?daerah=' . $id_daerah : ''; ?>" class="<?= $id_kategori == 0 ? 'bg-lime-600 text-white shadow-sm border-lime-600' : 'bg-slate-50 dark:bg-slate-950 text-slate-650 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:bg-lime-50 dark:hover:bg-lime-950/30 hover:text-lime-700 dark:hover:text-lime-400 hover:border-lime-200 dark:hover:border-lime-700' ?> px-3.5 py-1.5 rounded-xl font-bold text-xs border transition-all duration-200">
                        Semua Kategori
                    </a>
                    
                    <?php 
                    mysqli_data_seek($hasil_kategori, 0);
                    while($data_kategori = mysqli_fetch_assoc($hasil_kategori)): ?>
                        <a href="katalog.php?kategori=<?= $data_kategori['id']; ?><?= $id_daerah > 0 ? '&daerah=' . $id_daerah : ''; ?>" class="<?= $id_kategori == $data_kategori['id'] ? 'bg-lime-600 text-white shadow-sm border-lime-600' : 'bg-slate-50 dark:bg-slate-950 text-slate-655 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:bg-lime-50 dark:hover:bg-lime-950/30 hover:text-lime-700 dark:hover:text-lime-400 hover:border-lime-200 dark:hover:border-lime-700' ?> px-3.5 py-1.5 rounded-xl font-bold text-xs border transition-all duration-200">
                            <?= $data_kategori['nama_kategori']; ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="h-px bg-slate-100 dark:bg-slate-800/80 w-full"></div>

            <div>
                <p class="text-[10px] font-extrabold text-slate-455 dark:text-slate-500 uppercase tracking-widest mb-2.5 flex items-center gap-1"><i class="fa-solid fa-location-dot text-lime-600"></i> Asal Kabupaten</p>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="katalog.php<?= $id_kategori > 0 ? '?kategori=' . $id_kategori : ''; ?>" class="<?= $id_daerah == 0 ? 'bg-lime-600 text-white shadow-sm border-lime-600' : 'bg-slate-50 dark:bg-slate-950 text-slate-655 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:bg-lime-50 dark:hover:bg-lime-950/30 hover:text-lime-700 dark:hover:text-lime-400 hover:border-lime-200 dark:hover:border-lime-700' ?> px-3.5 py-1.5 rounded-xl font-bold text-xs border transition-all duration-200">
                        Semua Kabupaten
                    </a>
                    
                    <?php 
                    while($data_daerah = mysqli_fetch_assoc($hasil_daerah)): ?>
                        <a href="katalog.php?daerah=<?= $data_daerah['id']; ?><?= $id_kategori > 0 ? '&kategori=' . $id_kategori : ''; ?>" class="<?= $id_daerah == $data_daerah['id'] ? 'bg-lime-600 text-white shadow-sm border-lime-600' : 'bg-slate-50 dark:bg-slate-950 text-slate-655 dark:text-slate-300 border-slate-200 dark:border-slate-800 hover:bg-lime-50 dark:hover:bg-lime-950/30 hover:text-lime-700 dark:hover:text-lime-400 hover:border-lime-200 dark:hover:border-lime-700' ?> px-3.5 py-1.5 rounded-xl font-bold text-xs border transition-all duration-200">
                            <?= $data_daerah['nama_daerah']; ?>
                        </a>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php if (mysqli_num_rows($hasil_produk) > 0): ?>
                <?php while($baris = mysqli_fetch_assoc($hasil_produk)): ?>
                
                <div class="group bg-white dark:bg-slate-900 rounded-xl overflow-hidden border border-slate-200/60 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-lime-900/5 hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    
                    <div class="relative h-48 sm:h-52 overflow-hidden bg-slate-100 dark:bg-slate-800">
                        <img 
                            src="<?= $baris['gambar']; ?>" 
                            alt="<?= $baris['nama_produk']; ?>" 
                            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                        >
                        <div class="absolute top-2.5 left-2.5 flex gap-1.5">
                            <span class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm px-2.5 py-1 rounded text-[9px] font-bold text-slate-700 dark:text-slate-200 uppercase tracking-widest shadow-sm border border-slate-100 dark:border-slate-850">
                                <?= $baris['nama_kategori']; ?>
                            </span>
                            <?php if(!empty($baris['nama_daerah'])): ?>
                                <span class="bg-lime-600/95 dark:bg-lime-650/80 backdrop-blur-sm px-2.5 py-1 rounded text-[9px] font-bold text-white uppercase tracking-widest shadow-sm flex items-center gap-0.5">
                                    <?= $baris['nama_daerah']; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="text-sm sm:text-base font-bold text-slate-800 dark:text-slate-200 group-hover:text-lime-600 dark:group-hover:text-lime-400 transition-colors duration-300 line-clamp-1">
                            <?= $baris['nama_produk']; ?>
                        </h3>
                        
                        <p class="text-slate-500 dark:text-slate-400 text-xs mt-1.5 line-clamp-2 flex-grow leading-relaxed">
                            <?= $baris['deskripsi']; ?>
                        </p>
                        
                        <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-slate-800/80 flex justify-between items-center">
                            <span class="text-base font-extrabold text-lime-600 dark:text-lime-400">
                                Rp <?= number_format($baris['harga'], 0, ',', '.'); ?>
                            </span>
                            
                            <a href="detail_produk.php?id=<?= $baris['id']; ?>" class="w-9 h-9 flex items-center justify-center bg-slate-50 dark:bg-slate-800 text-slate-400 dark:text-slate-500 rounded-full group-hover:bg-lime-600 group-hover:text-white transition-all duration-300">
                                <i class="fa-solid fa-arrow-right text-xs -rotate-45 group-hover:rotate-0 transition-transform duration-300"></i>
                            </a>
                        </div>
                    </div>
                    
                </div>
                <?php endwhile; ?>
                
            <?php else: ?>
                
                <div class="col-span-full py-16 text-center bg-white dark:bg-slate-900 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="max-w-md mx-auto px-4">
                        <div class="w-12 h-12 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-350 dark:text-slate-600 text-xl">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-1">Produk Tidak Ditemukan</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-xs mb-4">Maaf, kombinasi kategori dan asal daerah yang Anda pilih belum tersedia kerajinan saat ini. Silakan pilih opsi lainnya.</p>
                        <a href="katalog.php" class="inline-flex items-center justify-center bg-lime-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-lime-700 hover:shadow-lg hover:shadow-lime-200/40 hover:-translate-y-0.5 transition-all duration-300 cursor-pointer">
                            Reset Filter Produk
                        </a>
                    </div>
                </div>
                
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../bagian/bawah.php'; ?>
