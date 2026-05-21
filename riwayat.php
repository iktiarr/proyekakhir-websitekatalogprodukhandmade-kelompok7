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

<div class="py-16 lg:py-24 bg-slate-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-10 sm:mb-12">
            <span class="inline-block py-1.5 px-3.5 rounded-full bg-lime-100 text-lime-700 text-xs sm:text-sm font-bold tracking-wider mb-4 shadow-sm">
                TRANSAKSI ANDA
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-800">Riwayat Pesanan</h1>
            <p class="text-slate-500 mt-2 text-base sm:text-lg">Pantau status pesanan dan detail transaksi Anda di sini.</p>
        </div>

        <?php if (mysqli_num_rows($query) > 0): ?>
            <div class="space-y-6">
                <?php while($row = mysqli_fetch_assoc($query)): 
                    // Penyesuaian warna status agar lebih modern dan estetik
                    $status_colors = [
                        'menunggu'   => 'bg-amber-50 text-amber-600 border-amber-200',
                        'dibayar'    => 'bg-blue-50 text-blue-600 border-blue-200',
                        'dikirim'    => 'bg-indigo-50 text-indigo-600 border-indigo-200',
                        'selesai'    => 'bg-lime-50 text-lime-700 border-lime-200', // Diselaraskan dengan tema
                        'dibatalkan' => 'bg-red-50 text-red-600 border-red-200'
                    ];
                    $status_color = $status_colors[$row['status']] ?? 'bg-slate-50 text-slate-600 border-slate-200';
                ?>
                
                <div class="group bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden transition-all duration-300 hover:shadow-xl hover:shadow-lime-900/5 hover:-translate-y-1">
                    <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        
                        <div class="flex items-center space-x-5 sm:space-x-6">
                            <div class="w-14 h-14 sm:w-16 sm:h-16 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 group-hover:bg-lime-50 group-hover:text-lime-600 transition-colors duration-300">
                                <i class="fa-solid fa-box text-xl sm:text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">ID Pesanan</p>
                                <p class="text-lg sm:text-xl font-bold text-slate-800">#HM-<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></p>
                                <p class="text-xs sm:text-sm text-slate-500 mt-0.5"><i class="fa-regular fa-clock mr-1"></i> <?= date('d M Y, H:i', strtotime($row['tanggal_pesanan'])); ?> WIB</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between md:justify-end space-x-4 sm:space-x-8 w-full md:w-auto pt-4 md:pt-0 border-t border-slate-100 md:border-0">
                            
                            <div class="text-left md:text-right">
                                <p class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">Total Belanja</p>
                                <p class="text-lg sm:text-xl font-extrabold text-slate-800">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></p>
                            </div>
                            
                            <div class="flex items-center space-x-3 sm:space-x-4">
                                <span class="px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-bold border <?= $status_color; ?> capitalize whitespace-nowrap">
                                    <?= $row['status']; ?>
                                </span>
                                
                                <a href="pembayaran.php?id=<?= $row['id']; ?>" class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 hover:text-white hover:bg-lime-600 rounded-xl transition-all duration-300 flex-shrink-0">
                                    <i class="fa-solid fa-chevron-right text-sm"></i>
                                </a>
                            </div>
                            
                        </div>
                    </div>
                    
                    <?php
                    $id_pesanan = $row['id'];
                    $items_query = mysqli_query($conn, "SELECT d.*, p.nama_produk FROM detail_pesanan d JOIN produk p ON d.id_produk = p.id WHERE d.id_pesanan = $id_pesanan");
                    ?>
                    <div class="bg-slate-50/80 px-6 md:px-8 py-4 border-t border-slate-100 flex flex-wrap gap-2 sm:gap-3">
                        <span class="text-xs font-bold text-slate-400 mr-2 flex items-center">Item:</span>
                        <?php while($item = mysqli_fetch_assoc($items_query)): ?>
                            <span class="text-[11px] sm:text-xs font-medium text-slate-600 bg-white px-2.5 py-1 rounded-md border border-slate-200 shadow-sm">
                                <?= $item['nama_produk']; ?> <span class="text-slate-400 ml-1">x<?= $item['jumlah']; ?></span>
                            </span>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
        <?php else: ?>
            
            <div class="py-20 sm:py-32 text-center bg-white rounded-2xl border border-dashed border-slate-200">
                <div class="max-w-sm mx-auto px-4">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300 text-4xl">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-800 mb-2">Belum Ada Pesanan</h3>
                    <p class="text-slate-500 mb-8">Anda belum pernah melakukan transaksi. Ayo temukan produk handmade favorit Anda!</p>
                    <a href="katalog.php" class="inline-flex items-center justify-center bg-lime-600 text-white px-8 py-3.5 rounded-xl font-bold hover:bg-lime-700 hover:-translate-y-1 hover:shadow-lg hover:shadow-lime-200/50 transition-all duration-300">
                        <i class="fa-solid fa-bag-shopping mr-2"></i> Mulai Belanja
                    </a>
                </div>
            </div>
            
        <?php endif; ?>
        
    </div>
</div>

<?php include 'includes/footer.php'; ?>