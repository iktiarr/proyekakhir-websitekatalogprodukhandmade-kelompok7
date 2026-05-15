<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: masuk.php");
    exit();
}

$id_pengguna = $_SESSION['user_id'];
$query_cart = mysqli_query($conn, "SELECT k.*, p.harga, p.stok FROM keranjang k JOIN produk p ON k.id_produk = p.id WHERE k.id_pengguna = $id_pengguna");

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

<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-12">Checkout</h1>

        <form action="" method="POST" class="lg:flex lg:space-x-12">
            <div class="lg:w-2/3 space-y-8">
                <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-8 h-8 bg-amber-600 text-white rounded-full flex items-center justify-center text-sm mr-3">1</span>
                        Informasi Pengiriman
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penerima</label>
                            <input type="text" value="<?= $_SESSION['nama']; ?>" readonly class="w-full px-4 py-3 rounded-xl border border-gray-100 bg-gray-50 text-gray-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                            <textarea name="alamat" required rows="4" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition" placeholder="Masukkan alamat pengiriman lengkap..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-8 h-8 bg-amber-600 text-white rounded-full flex items-center justify-center text-sm mr-3">2</span>
                        Metode Pembayaran
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <label class="relative flex items-center p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition peer-checked:border-amber-600">
                            <input type="radio" name="metode_pembayaran" value="Transfer Bank" checked class="w-5 h-5 text-amber-600">
                            <span class="ml-3 font-bold text-gray-900">Transfer Bank</span>
                        </label>
                        <label class="relative flex items-center p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="metode_pembayaran" value="E-Wallet (Gopay/OVO)" class="w-5 h-5 text-amber-600">
                            <span class="ml-3 font-bold text-gray-900">E-Wallet (Gopay/OVO)</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="lg:w-1/3 mt-12 lg:mt-0">
                <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 sticky top-24">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">Ringkasan Pesanan</h3>
                    <div class="space-y-4 mb-8">
                        <?php 
                        mysqli_data_seek($query_cart, 0);
                        while($row = mysqli_fetch_assoc($query_cart)): 
                        ?>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500"><?= $row['jumlah']; ?>x Item</span>
                            <span class="font-bold text-gray-900">Rp <?= number_format($row['harga'] * $row['jumlah'], 0, ',', '.'); ?></span>
                        </div>
                        <?php endwhile; ?>
                        
                        <div class="h-[1px] bg-gray-50 w-full my-4"></div>
                        <div class="flex justify-between text-xl font-extrabold text-gray-900">
                            <span>Total</span>
                            <span>Rp <?= number_format($total_harga, 0, ',', '.'); ?></span>
                        </div>
                    </div>
                    <button type="submit" name="buat_pesanan" class="w-full bg-amber-600 text-white py-4 rounded-2xl font-bold text-center block hover:bg-amber-700 transition shadow-xl shadow-amber-200">
                        Buat Pesanan Sekarang
                    </button>
                    <p class="text-center text-xs text-gray-400 mt-4 italic">Dengan membuat pesanan, Anda menyetujui syarat dan ketentuan kami.</p>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
