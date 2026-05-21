<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: masuk.php");
    exit();
}

$id_pengguna = $_SESSION['user_id'];

// Hapus item
if (isset($_GET['hapus'])) {
    $id_cart = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM keranjang WHERE id = $id_cart AND id_pengguna = $id_pengguna");
    header("Location: keranjang.php");
    exit();
}

// Update jumlah
if (isset($_POST['update_cart'])) {
    foreach ($_POST['jumlah'] as $id_cart => $jumlah) {
        $jumlah = (int)$jumlah;
        mysqli_query($conn, "UPDATE keranjang SET jumlah = $jumlah WHERE id = $id_cart AND id_pengguna = $id_pengguna");
    }
    header("Location: keranjang.php");
    exit();
}

$query = mysqli_query($conn, "SELECT k.*, p.nama_produk, p.harga, p.gambar, p.stok FROM keranjang k JOIN produk p ON k.id_produk = p.id WHERE k.id_pengguna = $id_pengguna");
?>

<?php include 'includes/header.php'; ?>

<div class="py-16 lg:py-24 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-10 sm:mb-12">
            <span class="inline-block py-1.5 px-3.5 rounded-full bg-lime-100 text-lime-700 text-xs sm:text-sm font-bold tracking-wider mb-4 shadow-sm">
                SIAP CHECKOUT?
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-800">Keranjang Belanja</h1>
        </div>

        <?php if (mysqli_num_rows($query) > 0): ?>
            <div class="lg:flex lg:gap-8 xl:gap-12 items-start">
                
                <div class="lg:w-2/3 w-full mb-10 lg:mb-0">
                    <form action="" method="POST">
                        <div class="space-y-4 sm:space-y-6">
                            <?php 
                            $total = 0;
                            while($row = mysqli_fetch_assoc($query)): 
                                $subtotal = $row['harga'] * $row['jumlah'];
                                $total += $subtotal;
                            ?>
                            <div class="group bg-white p-4 sm:p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6 hover:shadow-md hover:border-lime-200 transition-all duration-300 relative">
                                
                                <a href="keranjang.php?hapus=<?= $row['id']; ?>" class="absolute top-4 right-4 sm:static sm:order-last text-slate-300 hover:text-red-500 hover:bg-red-50 p-2.5 rounded-xl transition-colors duration-300 flex-shrink-0" title="Hapus Item">
                                    <i class="fa-solid fa-trash-can text-lg"></i>
                                </a>

                                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden bg-slate-50 flex-shrink-0 border border-slate-100">
                                    <img src="<?= $row['gambar'] ?: 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=200'; ?>" alt="<?= $row['nama_produk']; ?>" class="w-full h-full object-cover">
                                </div>
                                
                                <div class="flex-1 flex flex-col justify-center">
                                    <h3 class="font-bold text-slate-800 text-base sm:text-lg pr-8 sm:pr-0 mb-1 line-clamp-2"><?= $row['nama_produk']; ?></h3>
                                    <p class="text-lime-600 font-extrabold mb-4 sm:mb-0">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
                                    
                                    <div class="mt-auto flex items-center justify-between sm:hidden pt-4 border-t border-slate-100">
                                        <div class="flex items-center border border-slate-200 rounded-lg bg-white">
                                            <input type="number" name="jumlah[<?= $row['id']; ?>]" value="<?= $row['jumlah']; ?>" min="1" max="<?= $row['stok']; ?>" class="w-14 text-center font-bold text-slate-800 bg-transparent border-none focus:ring-0 text-sm py-1.5 p-0">
                                        </div>
                                        <p class="text-slate-800 font-bold text-sm">Rp <?= number_format($subtotal, 0, ',', '.'); ?></p>
                                    </div>
                                </div>

                                <div class="hidden sm:flex flex-col items-end gap-3 flex-shrink-0">
                                    <div class="flex items-center border border-slate-200 rounded-lg bg-white overflow-hidden focus-within:border-lime-500 focus-within:ring-2 focus-within:ring-lime-500/20 transition-all">
                                        <input type="number" name="jumlah[<?= $row['id']; ?>]" value="<?= $row['jumlah']; ?>" min="1" max="<?= $row['stok']; ?>" class="w-16 text-center font-bold text-slate-800 bg-transparent border-none focus:ring-0 text-sm py-2 px-1">
                                    </div>
                                    <p class="text-slate-800 font-extrabold">Rp <?= number_format($subtotal, 0, ',', '.'); ?></p>
                                </div>
                                
                            </div>
                            <?php endwhile; ?>
                        </div>
                        
                        <div class="mt-8 flex flex-col-reverse sm:flex-row justify-between items-center gap-4">
                            <a href="katalog.php" class="group flex items-center text-slate-500 font-medium hover:text-lime-600 transition-colors duration-300">
                                <i class="fa-solid fa-arrow-left mr-2 transition-transform duration-300 group-hover:-translate-x-1"></i> Lanjut Belanja
                            </a>
                            <button type="submit" name="update_cart" class="w-full sm:w-auto bg-slate-100 text-slate-600 px-6 py-2.5 rounded-xl font-bold hover:bg-lime-50 hover:text-lime-700 hover:border-lime-200 border border-transparent transition-all duration-300 flex items-center justify-center">
                                <i class="fa-solid fa-rotate mr-2"></i> Update Keranjang
                            </button>
                        </div>
                    </form>
                </div>

                <div class="lg:w-1/3 w-full">
                    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm sticky top-24">
                        <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-6">Ringkasan Pesanan</h3>
                        
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between text-slate-500 text-sm sm:text-base">
                                <span>Subtotal</span>
                                <span class="font-medium text-slate-700">Rp <?= number_format($total, 0, ',', '.'); ?></span>
                            </div>
                            <div class="flex justify-between text-slate-500 text-sm sm:text-base">
                                <span>Estimasi Pengiriman</span>
                                <span class="text-lime-600 font-bold bg-lime-50 px-2 py-0.5 rounded text-xs sm:text-sm">Gratis</span>
                            </div>
                            
                            <div class="h-px bg-slate-100 w-full my-4"></div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-base sm:text-lg font-bold text-slate-800">Total</span>
                                <span class="text-xl sm:text-2xl font-extrabold text-lime-600">Rp <?= number_format($total, 0, ',', '.'); ?></span>
                            </div>
                        </div>
                        
                        <a href="checkout.php" class="group w-full inline-flex items-center justify-center bg-lime-600 text-white py-4 rounded-xl font-bold text-base hover:bg-lime-700 hover:-translate-y-1 hover:shadow-lg hover:shadow-lime-200/50 transition-all duration-300">
                            Checkout Sekarang <i class="fa-solid fa-arrow-right ml-2 transition-transform duration-300 group-hover:translate-x-1"></i>
                        </a>
                        
                        <div class="mt-6 flex items-center justify-center gap-4 text-xs text-slate-400">
                            <span class="flex items-center"><i class="fa-solid fa-shield-check mr-1.5"></i> Aman</span>
                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                            <span class="flex items-center"><i class="fa-solid fa-arrow-rotate-left mr-1.5"></i> Garansi Retur</span>
                        </div>
                    </div>
                </div>
                
            </div>
            
        <?php else: ?>
            
            <div class="py-20 sm:py-32 text-center bg-white rounded-2xl border border-dashed border-slate-200">
                <div class="max-w-sm mx-auto px-4">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300 text-4xl">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-800 mb-2">Keranjang Kosong</h3>
                    <p class="text-slate-500 mb-8 text-sm sm:text-base">Sepertinya Anda belum menambahkan produk apapun ke dalam keranjang. Yuk, lihat koleksi kami!</p>
                    <a href="katalog.php" class="inline-flex items-center justify-center bg-lime-600 text-white px-8 py-3.5 rounded-xl font-bold text-sm sm:text-base hover:bg-lime-700 hover:-translate-y-1 hover:shadow-lg hover:shadow-lime-200/50 transition-all duration-300">
                        <i class="fa-solid fa-bag-shopping mr-2"></i> Mulai Belanja
                    </a>
                </div>
            </div>
            
        <?php endif; ?>
        
    </div>
</div>

<?php include 'includes/footer.php'; ?>