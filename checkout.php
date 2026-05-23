<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: masuk.php");
    exit();
}

$id_pengguna = $_SESSION['user_id'];
$query_cart = mysqli_query($conn, "SELECT k.*, p.harga, p.stok, p.nama_produk FROM keranjang k JOIN produk p ON k.id_produk = p.id WHERE k.id_pengguna = $id_pengguna");

if (mysqli_num_rows($query_cart) == 0) {
    header("Location: katalog.php");
    exit();
}

$total_harga = 0;
$items = [];
while ($row = mysqli_fetch_assoc($query_cart)) {
    $total_harga += $row['harga'] * $row['jumlah'];
    $items[] = $row;
}

if (isset($_POST['buat_pesanan'])) {
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    $metode = mysqli_real_escape_string($conn, $_POST['metode_pembayaran']);

    
    mysqli_begin_transaction($conn);

    try {
        
        $query_order = "INSERT INTO pesanan (id_pengguna, total_harga, status, alamat, metode_pembayaran) VALUES ($id_pengguna, $total_harga, 'menunggu', '$alamat', '$metode')";
        mysqli_query($conn, $query_order);
        $id_pesanan = mysqli_insert_id($conn);

        
        foreach ($items as $item) {
            $id_produk = $item['id_produk'];
            $jumlah = $item['jumlah'];
            $harga = $item['harga'];
            
            mysqli_query($conn, "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga) VALUES ($id_pesanan, $id_produk, $jumlah, $harga)");
            mysqli_query($conn, "UPDATE produk SET stok = stok - $jumlah WHERE id = $id_produk");
        }

        
        mysqli_query($conn, "DELETE FROM keranjang WHERE id_pengguna = $id_pengguna");

        mysqli_commit($conn);
        header("Location: pembayaran.php?id=$id_pesanan");
        exit();
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $error = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="py-8 bg-slate-50 min-h-[75vh]">
    <div class="max-w-6xl mx-auto px-4">
        
        <div class="mb-6">
            <span class="inline-block py-1 px-2.5 rounded-full bg-lime-100 text-lime-700 text-[10px] font-bold tracking-wider mb-2 shadow-sm">
                TAHAP TERAKHIR
            </span>
            <h1 class="text-2xl font-extrabold text-slate-800">Checkout</h1>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm border border-red-100 flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i> 
                <span><?= $error; ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="lg:flex lg:gap-6 items-start">
            
            <div class="lg:w-2/3 space-y-4 w-full mb-6 lg:mb-0">
                
                <!-- 1. Informasi Pengiriman (Compact) -->
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
                        <span class="w-6 h-6 bg-lime-100 text-lime-700 rounded-lg flex items-center justify-center text-xs font-extrabold mr-2">1</span>
                        Informasi Pengiriman
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Nama Penerima</label>
                            <input type="text" value="<?= $_SESSION['nama']; ?>" readonly class="w-full px-3.5 py-2.5 rounded-xl border border-slate-100 bg-slate-50 text-slate-500 outline-none cursor-not-allowed text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 mb-1">Alamat Lengkap</label>
                            <textarea name="alamat" required rows="3" class="w-full px-3.5 py-2.5 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-sm text-slate-800 placeholder-slate-400" placeholder="Contoh: Jl. Sudirman No. 123, RT 01/RW 02, Kel. Sukamaju, Kec. Maju, Kota Jakarta..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- 2. Metode Pembayaran (Compact) -->
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                    <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center">
                        <span class="w-6 h-6 bg-lime-100 text-lime-700 rounded-lg flex items-center justify-center text-xs font-extrabold mr-2">2</span>
                        Metode Pembayaran
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                        <label class="relative flex items-center p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-lime-500 has-[:checked]:bg-lime-50/30">
                            <input type="radio" name="metode_pembayaran" value="BCA Virtual Account" checked class="w-4 h-4 text-lime-600 border-slate-300 focus:ring-lime-500 focus:ring-offset-0">
                            <span class="ml-2.5 text-xs font-bold text-slate-700">BCA Virtual Account</span>
                        </label>
                        
                        <label class="relative flex items-center p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-lime-500 has-[:checked]:bg-lime-50/30">
                            <input type="radio" name="metode_pembayaran" value="Mandiri Virtual Account" class="w-4 h-4 text-lime-600 border-slate-300 focus:ring-lime-500 focus:ring-offset-0">
                            <span class="ml-2.5 text-xs font-bold text-slate-700">Mandiri VA</span>
                        </label>

                        <label class="relative flex items-center p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-lime-500 has-[:checked]:bg-lime-50/30">
                            <input type="radio" name="metode_pembayaran" value="GoPay" class="w-4 h-4 text-lime-600 border-slate-300 focus:ring-lime-500 focus:ring-offset-0">
                            <span class="ml-2.5 text-xs font-bold text-slate-700">GoPay</span>
                        </label>

                        <label class="relative flex items-center p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-lime-500 has-[:checked]:bg-lime-50/30">
                            <input type="radio" name="metode_pembayaran" value="Dana" class="w-4 h-4 text-lime-600 border-slate-300 focus:ring-lime-500 focus:ring-offset-0">
                            <span class="ml-2.5 text-xs font-bold text-slate-700">Dana</span>
                        </label>

                        <label class="relative flex items-center p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-lime-500 has-[:checked]:bg-lime-50/30">
                            <input type="radio" name="metode_pembayaran" value="Alfamart" class="w-4 h-4 text-lime-600 border-slate-300 focus:ring-lime-500 focus:ring-offset-0">
                            <span class="ml-2.5 text-xs font-bold text-slate-700">Alfamart</span>
                        </label>

                        <label class="relative flex items-center p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-lime-500 bids-[:checked]:bg-lime-50/30">
                            <input type="radio" name="metode_pembayaran" value="Indomaret" class="w-4 h-4 text-lime-600 border-slate-300 focus:ring-lime-500 focus:ring-offset-0">
                            <span class="ml-2.5 text-xs font-bold text-slate-700">Indomaret</span>
                        </label>
                    </div>
                </div>
                
            </div>

            <!-- Ringkasan Pesanan (Compact) -->
            <div class="lg:w-1/3 w-full">
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm sticky top-24">
                    <h3 class="text-base font-bold text-slate-800 mb-4">Ringkasan Pesanan</h3>
                    
                    <div class="space-y-2 mb-4 max-h-40 overflow-y-auto pr-1 custom-scrollbar">
                        <?php 
                        mysqli_data_seek($query_cart, 0);
                        while($row = mysqli_fetch_assoc($query_cart)): 
                        ?>
                        <div class="flex justify-between text-xs items-start gap-4 pb-2 border-b border-slate-50 last:border-0 last:pb-0">
                            <span class="text-slate-500 line-clamp-1">
                                <span class="font-bold text-slate-400"><?= $row['jumlah']; ?>x</span> <?= $row['nama_produk']; ?>
                            </span>
                            <span class="font-bold text-slate-700 whitespace-nowrap">Rp <?= number_format($row['harga'] * $row['jumlah'], 0, ',', '.'); ?></span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <div class="h-px bg-slate-100 w-full mb-4"></div>

                    <div class="space-y-2.5 mb-6">
                        <div class="flex justify-between items-center pt-1.5">
                            <span class="text-sm font-bold text-slate-700">Total</span>
                            <span class="text-lg font-extrabold text-lime-600">Rp <?= number_format($total_harga, 0, ',', '.'); ?></span>
                        </div>
                    </div>
                    
                    <button type="submit" name="buat_pesanan" class="w-full bg-lime-600 text-white py-2.5 rounded-xl font-bold text-center flex items-center justify-center hover:bg-lime-700 hover:shadow-lg hover:shadow-lime-200/40 hover:-translate-y-0.5 transition-all duration-300 text-sm cursor-pointer">
                        Buat Pesanan Sekarang <i class="fa-solid fa-check ml-1.5"></i>
                    </button>
                </div>
            </div>
            
        </form>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f8fafc;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<?php include 'includes/footer.php'; ?>