<?php
$awalan = "../";
include '../koneksi.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: ../masuk.php");
    exit();
}

$id_pengguna = $_SESSION['user']['id'];
$kueri_pesanan = kueri("SELECT * FROM pesanan WHERE id_pengguna = ? ORDER BY tanggal_pesanan DESC", [$id_pengguna]);
?>

<?php include '../bagian/atas.php'; ?>

<div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-[80vh] transition-colors duration-300">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Riwayat Pesanan</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1.5 text-sm">Pantau status pengiriman HandMadura dan detail riwayat transaksi Anda di sini.</p>
        </div>

        <?php if (mysqli_num_rows($kueri_pesanan) > 0): ?>
            <div class="space-y-5">
                <?php while($baris = mysqli_fetch_assoc($kueri_pesanan)): 
                    $warna_status = [
                        'menunggu'   => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/30 dark:text-amber-400 dark:border-amber-900/40',
                        'dibayar'    => 'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/30 dark:text-sky-400 dark:border-sky-900/40',
                        'dikirim'    => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-950/30 dark:text-purple-400 dark:border-purple-900/40',
                        'selesai'    => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/30 dark:text-emerald-400 dark:border-emerald-900/40',
                        'dibatalkan' => 'bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-950/30 dark:text-rose-400 dark:border-rose-900/40'
                    ];
                    $warna_status_pilihan = $warna_status[$baris['status']] ?? 'bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700';
                ?>
                
                <div class="group bg-white dark:bg-slate-900 rounded-xl border border-slate-200/60 dark:border-slate-800 shadow-sm overflow-hidden transition-all duration-300">
                    <div class="p-5 sm:p-6 flex flex-col md:flex-row md:items-center justify-between gap-5">
                        
                        <div class="flex items-center space-x-4">
                            <div class="w-11 h-11 bg-slate-50 dark:bg-slate-950 rounded-2xl flex items-center justify-center text-slate-400 dark:text-slate-600 group-hover:bg-lime-50 dark:group-hover:bg-lime-950/30 group-hover:text-lime-600 dark:group-hover:text-lime-400 transition-colors duration-300 flex-shrink-0">
                                <i class="fa-solid fa-box text-base"></i>
                            </div>
                            <div>
                                <p class="text-[9px] text-slate-400 dark:text-slate-550 font-extrabold uppercase tracking-widest mb-0.5">ID Pesanan</p>
                                <p class="text-sm sm:text-base font-bold text-slate-800 dark:text-slate-200">#KM-<?= str_pad($baris['id'], 5, '0', STR_PAD_LEFT); ?></p>
                                <p class="text-[11px] text-slate-400 dark:text-slate-500 mt-0.5"><i class="fa-regular fa-clock mr-1"></i> <?= date('d M Y, H:i', strtotime($baris['tanggal_pesanan'])); ?> WIB</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center justify-between md:justify-end space-x-6 w-full md:w-auto pt-4 md:pt-0 border-t border-slate-100 dark:border-slate-800 md:border-0">
                            
                            <div class="text-left md:text-right">
                                <p class="text-[9px] text-slate-400 dark:text-slate-500 font-extrabold uppercase tracking-widest">Total Belanja</p>
                                <p class="text-base font-extrabold text-slate-800 dark:text-slate-200">Rp <?= number_format($baris['total_harga'], 0, ',', '.'); ?></p>
                            </div>
                            
                            <div class="flex items-center space-x-3.5">
                                <span class="px-3 py-1 rounded-xl text-xs font-bold border <?= $warna_status_pilihan; ?> capitalize whitespace-nowrap">
                                    <?= $baris['status']; ?>
                                </span>
                                
                                <?php if ($baris['status'] === 'menunggu'): ?>
                                    <a href="bayar.php?id=<?= $baris['id']; ?>" class="inline-flex items-center justify-center bg-lime-600 hover:bg-lime-700 text-white text-xs font-bold px-3.5 py-1.5 rounded-xl transition-all duration-200 border-none cursor-pointer">
                                        <i class="fa-solid fa-credit-card mr-1 text-[10px]"></i> Bayar Sekarang
                                    </a>
                                <?php elseif ($baris['status'] === 'selesai'): ?>
                                    <a href="tambah_ulasan.php" class="inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-3.5 py-1.5 rounded-xl transition-all duration-200 border-none cursor-pointer mr-2">
                                        <i class="fa-solid fa-pen-to-square mr-1 text-[10px]"></i> Beri Ulasan
                                    </a>
                                <?php endif; ?>
                                <?php if (in_array($baris['status'], ['dibayar', 'dikirim', 'selesai'])): ?>
                                    <a href="cetak_struk.php?id=<?= $baris['id']; ?>" target="_blank" class="inline-flex items-center justify-center bg-slate-600 hover:bg-slate-700 dark:bg-slate-800 dark:hover:bg-slate-700 text-white text-xs font-bold px-3.5 py-1.5 rounded-xl transition-all duration-200 border-none cursor-pointer mr-2">
                                        <i class="fa-solid fa-print mr-1 text-[10px]"></i> Cetak Struk
                                    </a>
                                <?php endif; ?>
                                <a href="lapor.php?id_pesanan=<?= $baris['id']; ?>" class="inline-flex items-center justify-center bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-950/30 dark:text-red-400 dark:hover:bg-red-900/30 text-xs font-bold px-3.5 py-1.5 rounded-xl transition-all duration-200 border border-red-200 dark:border-red-900/40 cursor-pointer">
                                    <i class="fa-solid fa-triangle-exclamation mr-1 text-[10px]"></i> Laporkan Kendala
                                </a>
                            </div>
                            
                        </div>
                    </div>
                    
                    <?php
                    $id_pesanan = $baris['id'];
                    $kueri_rincian = kueri("SELECT d.*, p.nama_produk FROM detail_pesanan d JOIN produk p ON d.id_produk = p.id WHERE d.id_pesanan = ?", [$id_pesanan]);
                    ?>
                    <div class="bg-slate-50/60 dark:bg-slate-950/40 px-5 py-3 border-t border-slate-200 dark:border-slate-800 flex flex-wrap gap-2 items-center">
                        <span class="text-[10px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-wider mr-1">Rincian Item:</span>
                        <?php while($data_rincian = mysqli_fetch_assoc($kueri_rincian)): ?>
                            <span class="text-[11px] sm:text-xs font-semibold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-900 px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-800 shadow-sm">
                                <?= $data_rincian['nama_produk']; ?> <span class="text-lime-600 dark:text-lime-400 font-bold ml-1">x<?= $data_rincian['jumlah']; ?></span>
                            </span>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
        <?php else: ?>
            
            <div class="py-20 sm:py-28 text-center bg-white dark:bg-slate-900 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="max-w-sm mx-auto px-4">
                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-950 rounded-full flex items-center justify-center mx-auto mb-5 text-slate-300 dark:text-slate-700 text-3xl">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 dark:bg-slate-900 dark:text-slate-200 mb-2">Belum Ada Transaksi</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6 text-xs sm:text-sm leading-relaxed">Anda belum pernah melakukan pesanan di HandMadura. Yuk, mulai koleksi pertamamu hari ini!</p>
                </div>
            </div>
            
        <?php endif; ?>
        
    </div>
</div>

<?php include '../bagian/bawah.php'; ?>
