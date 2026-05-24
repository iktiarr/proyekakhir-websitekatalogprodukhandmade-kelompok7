<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: masuk.php");
    exit();
}

$id_pengguna = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT * FROM pesanan WHERE id_pengguna = $id_pengguna ORDER BY tanggal_pesanan DESC");
?>

<?php include 'includes/header.php'; ?>

<div class="py-8 bg-slate-50 dark:bg-slate-950 min-h-[75vh] transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4">
        
        <div class="mb-6">
            <span class="inline-block py-1 px-2.5 rounded-full bg-lime-100 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400 text-[10px] font-bold tracking-wider mb-2 shadow-sm border border-transparent dark:border-lime-900/30">
                TRANSAKSI ANDA
            </span>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">Riwayat Pesanan</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">Pantau status pesanan dan detail transaksi Anda di sini.</p>
        </div>

        <?php if (mysqli_num_rows($query) > 0): ?>
            <div class="space-y-4">
                <?php while($row = mysqli_fetch_assoc($query)): 
                    $status_colors = [
                        'menunggu'   => 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-950/20 dark:text-amber-400 dark:border-amber-900/35',
                        'dibayar'    => 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900/35',
                        'dikirim'    => 'bg-indigo-50 text-indigo-600 border-indigo-200 dark:bg-indigo-950/20 dark:text-indigo-400 dark:border-indigo-900/35',
                        'selesai'    => 'bg-lime-50 text-lime-700 border-lime-200 dark:bg-lime-950/20 dark:text-lime-400 dark:border-lime-900/35',
                        'dibatalkan' => 'bg-red-50 text-red-600 border-red-200 dark:bg-red-950/20 dark:text-red-400 dark:border-red-900/35'
                    ];
                    $status_color = $status_colors[$row['status']] ?? 'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700';
                ?>
                
                <div class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-100 dark:border-slate-800 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md hover:-translate-y-0.5">
                    <div class="p-4 sm:p-5 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        
                        <div class="flex items-center space-x-4">
                            <div class="w-10 h-10 bg-slate-50 dark:bg-slate-950 rounded-xl flex items-center justify-center text-slate-400 dark:text-slate-600 group-hover:bg-lime-50 dark:group-hover:bg-lime-950/30 group-hover:text-lime-600 dark:group-hover:text-lime-400 transition-colors duration-300">
                                <i class="fa-solid fa-box text-base"></i>
                            </div>
                            <div>
                                <p class="text-[9px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-widest">ID Pesanan</p>
                                <p class="text-sm sm:text-base font-bold text-slate-800 dark:text-slate-200">#HM-<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></p>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5"><i class="fa-regular fa-clock mr-1"></i> <?= date('d M Y, H:i', strtotime($row['tanggal_pesanan'])); ?> WIB</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between md:justify-end space-x-6 w-full md:w-auto pt-3 md:pt-0 border-t border-slate-100 dark:border-slate-800 md:border-0">
                            
                            <div class="text-left md:text-right">
                                <p class="text-[9px] text-slate-400 dark:text-slate-500 font-bold uppercase tracking-widest">Total Belanja</p>
                                <p class="text-sm sm:text-base font-extrabold text-slate-800 dark:text-slate-200">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></p>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold border <?= $status_color; ?> capitalize whitespace-nowrap animate-pulse-subtle">
                                    <?= $row['status']; ?>
                                </span>
                            </div>
                            
                        </div>
                    </div>
                    
                    <?php
                    $id_pesanan = $row['id'];
                    $items_query = mysqli_query($conn, "SELECT d.*, p.nama_produk FROM detail_pesanan d JOIN produk p ON d.id_produk = p.id WHERE d.id_pesanan = $id_pesanan");
                    ?>
                    <div class="bg-slate-50/60 dark:bg-slate-950/40 px-4 sm:px-5 py-2.5 border-t border-slate-100 dark:border-slate-800 flex flex-wrap gap-1.5 items-center">
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 mr-1 flex items-center">Item:</span>
                        <?php while($item = mysqli_fetch_assoc($items_query)): ?>
                            <span class="text-[10px] sm:text-xs font-medium text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900 px-2 py-0.5 rounded-md border border-slate-200 dark:border-slate-800 shadow-sm">
                                <?= $item['nama_produk']; ?> <span class="text-slate-400 dark:text-slate-500 ml-0.5">x<?= $item['jumlah']; ?></span>
                            </span>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
        <?php else: ?>
            
            <div class="py-20 sm:py-32 text-center bg-white dark:bg-slate-900 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                <div class="max-w-sm mx-auto px-4">
                    <div class="w-20 h-20 bg-slate-50 dark:bg-slate-950 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300 dark:text-slate-700 text-4xl">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-200 mb-2">Belum Ada Pesanan</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-8">Anda belum pernah melakukan transaksi. Ayo temukan produk handmade favorit Anda!</p>
                    <a href="katalog.php" class="inline-flex items-center justify-center bg-lime-600 text-white px-8 py-3.5 rounded-xl font-bold hover:bg-lime-700 hover:-translate-y-1 hover:shadow-lg hover:shadow-lime-200/50 transition-all duration-300">
                        <i class="fa-solid fa-bag-shopping mr-2"></i> Mulai Belanja
                    </a>
                </div>
            </div>
            
        <?php endif; ?>
        
    </div>
</div>

<?php include 'includes/footer.php'; ?>