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

<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900">Riwayat Pesanan</h1>
            <p class="text-gray-500 mt-2">Pantau status pesanan dan transaksi Anda.</p>
        </div>

        <?php if (mysqli_num_rows($query) > 0): ?>
            <div class="space-y-6">
                <?php while($row = mysqli_fetch_assoc($query)): 
                    $status_colors = [
                        'menunggu' => 'bg-amber-50 text-amber-600 border-amber-100',
                        'dibayar' => 'bg-blue-50 text-blue-600 border-blue-100',
                        'dikirim' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                        'selesai' => 'bg-green-50 text-green-600 border-green-100',
                        'dibatalkan' => 'bg-red-50 text-red-600 border-red-100'
                    ];
                    $status_color = $status_colors[$row['status']] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                ?>
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden transition hover:shadow-lg">
                    <div class="p-6 md:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
                        <div class="flex items-center space-x-6">
                            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400">
                                <i class="fa-solid fa-box text-2xl"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">ID Pesanan</p>
                                <p class="text-lg font-bold text-gray-900">#HM-<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></p>
                                <p class="text-sm text-gray-500"><?= date('d M Y, H:i', strtotime($row['tanggal_pesanan'])); ?> WIB</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-8">
                            <div class="text-right hidden sm:block">
                                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Total</p>
                                <p class="text-xl font-extrabold text-gray-900">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></p>
                            </div>
                            <div>
                                <span class="px-4 py-2 rounded-full text-sm font-bold border <?= $status_color; ?> capitalize">
                                    <?= $row['status']; ?>
                                </span>
                            </div>
                            <a href="pembayaran.php?id=<?= $row['id']; ?>" class="p-3 bg-gray-50 text-gray-400 hover:text-amber-600 hover:bg-amber-50 rounded-xl transition">
                                <i class="fa-solid fa-chevron-right"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Detail Item (Optional Preview) -->
                    <?php
                    $id_pesanan = $row['id'];
                    $items_query = mysqli_query($conn, "SELECT d.*, p.nama_produk FROM detail_pesanan d JOIN produk p ON d.id_produk = p.id WHERE d.id_pesanan = $id_pesanan");
                    ?>
                    <div class="bg-gray-50/50 px-8 py-4 border-t border-gray-50 flex flex-wrap gap-4">
                        <?php while($item = mysqli_fetch_assoc($items_query)): ?>
                            <span class="text-xs font-medium text-gray-500 bg-white px-3 py-1 rounded-lg border border-gray-100">
                                <?= $item['nama_produk']; ?> (x<?= $item['jumlah']; ?>)
                            </span>
                        <?php endwhile; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="py-32 text-center bg-white rounded-[3rem] border border-dashed border-gray-200">
                <div class="max-w-xs mx-auto">
                    <i class="fa-solid fa-receipt text-6xl text-gray-200 mb-6"></i>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Pesanan</h3>
                    <p class="text-gray-500 mb-10">Anda belum pernah melakukan pemesanan apapun.</p>
                    <a href="katalog.php" class="inline-block bg-amber-600 text-white px-10 py-4 rounded-2xl font-bold hover:bg-amber-700 transition shadow-xl shadow-amber-200">
                        Mulai Belanja
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
