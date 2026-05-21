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

    // Mulai transaksi manual
    mysqli_begin_transaction($conn);

    try {
        // 1. Simpan ke tabel pesanan
        $query_order = "INSERT INTO pesanan (id_pengguna, total_harga, status, alamat, metode_pembayaran) VALUES ($id_pengguna, $total_harga, 'menunggu', '$alamat', '$metode')";
        mysqli_query($conn, $query_order);
        $id_pesanan = mysqli_insert_id($conn);

        // 2. Simpan ke detail_pesanan & Update Stok
        foreach ($items as $item) {
            $id_produk = $item['id_produk'];
            $jumlah = $item['jumlah'];
            $harga = $item['harga'];
            
            mysqli_query($conn, "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga) VALUES ($id_pesanan, $id_produk, $jumlah, $harga)");
            mysqli_query($conn, "UPDATE produk SET stok = stok - $jumlah WHERE id = $id_produk");
        }

        // 3. Kosongkan keranjang
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

<div class="py-16 lg:py-24 bg-slate-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-10 sm:mb-12">
            <span class="inline-block py-1.5 px-3.5 rounded-full bg-lime-100 text-lime-700 text-xs sm:text-sm font-bold tracking-wider mb-4 shadow-sm">
                TAHAP TERAKHIR
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-800">Checkout</h1>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-8 text-sm border border-red-100 flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i> 
                <span><?= $error; ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="lg:flex lg:gap-8 xl:gap-12 items-start">
            
            <div class="lg:w-2/3 space-y-6 sm:space-y-8 w-full mb-10 lg:mb-0">
                
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm">
                    <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-6 flex items-center">
                        <span class="w-8 h-8 sm:w-9 sm:h-9 bg-lime-100 text-lime-700 rounded-xl flex items-center justify-center text-sm font-extrabold mr-3">1</span>
                        Informasi Pengiriman
                    </h3>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Penerima</label>
                            <input type="text" value="<?= $_SESSION['nama']; ?>" readonly class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50 text-slate-500 outline-none cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Alamat Lengkap</label>
                            <textarea name="alamat" required rows="4" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800 placeholder-slate-400" placeholder="Contoh: Jl. Sudirman No. 123, RT 01/RW 02, Kel. Sukamaju, Kec. Maju, Kota Jakarta..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm">
                    <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-6 flex items-center">
                        <span class="w-8 h-8 sm:w-9 sm:h-9 bg-lime-100 text-lime-700 rounded-xl flex items-center justify-center text-sm font-extrabold mr-3">2</span>
                        Metode Pembayaran
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex items-center p-4 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-lime-500 has-[:checked]:bg-lime-50/30">
                            <input type="radio" name="metode_pembayaran" value="Transfer Bank" checked class="w-5 h-5 text-lime-600 border-slate-300 focus:ring-lime-500 focus:ring-offset-0">
                            <span class="ml-3 font-bold text-slate-700">Transfer Bank</span>
                        </label>
                        
                        <label class="relative flex items-center p-4 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors has-[:checked]:border-lime-500 has-[:checked]:bg-lime-50/30">
                            <input type="radio" name="metode_pembayaran" value="E-Wallet (Gopay/OVO)" class="w-5 h-5 text-lime-600 border-slate-300 focus:ring-lime-500 focus:ring-offset-0">
                            <span class="ml-3 font-bold text-slate-700">E-Wallet (Gopay/OVO)</span>
                        </label>
                    </div>
                </div>
                
            </div>

            <div class="lg:w-1/3 w-full">
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm sticky top-24">
                    <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-6">Ringkasan Pesanan</h3>
                    
                    <div class="space-y-3 mb-6 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                        <?php 
                        mysqli_data_seek($query_cart, 0);
                        while($row = mysqli_fetch_assoc($query_cart)): 
                        ?>
                        <div class="flex justify-between text-sm items-start gap-4 pb-3 border-b border-slate-50 last:border-0 last:pb-0">
                            <span class="text-slate-600 line-clamp-2 pr-2">
                                <span class="font-bold text-slate-400"><?= $row['jumlah']; ?>x</span> <?= $row['nama_produk']; ?>
                            </span>
                            <span class="font-bold text-slate-800 whitespace-nowrap">Rp <?= number_format($row['harga'] * $row['jumlah'], 0, ',', '.'); ?></span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <div class="h-px bg-slate-100 w-full mb-6"></div>

                    <div class="space-y-4 mb-8">
                        <div class="flex justify-between text-slate-500 text-sm">
                            <span>Estimasi Pengiriman</span>
                            <span class="text-lime-600 font-bold bg-lime-50 px-2 py-0.5 rounded text-xs">Gratis</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-base sm:text-lg font-bold text-slate-800">Total</span>
                            <span class="text-xl sm:text-2xl font-extrabold text-lime-600">Rp <?= number_format($total_harga, 0, ',', '.'); ?></span>
                        </div>
                    </div>
                    
                    <button type="submit" name="buat_pesanan" class="w-full bg-lime-600 text-white py-4 rounded-xl font-bold text-center flex items-center justify-center hover:bg-lime-700 hover:-translate-y-1 hover:shadow-lg hover:shadow-lime-200/50 transition-all duration-300">
                        Buat Pesanan Sekarang <i class="fa-solid fa-check ml-2"></i>
                    </button>
                    
                    <p class="text-center text-[11px] sm:text-xs text-slate-400 mt-4">
                        <i class="fa-solid fa-lock mr-1"></i> Data Anda dilindungi dan dienkripsi dengan aman.
                    </p>
                </div>
            </div>
            
        </form>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f8fafc;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<?php include 'includes/footer.php'; ?>