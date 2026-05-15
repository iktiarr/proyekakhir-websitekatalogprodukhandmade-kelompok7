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

<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-4xl font-extrabold text-gray-900 mb-12">Keranjang Belanja</h1>

        <?php if (mysqli_num_rows($query) > 0): ?>
            <div class="lg:flex lg:space-x-12">
                <div class="lg:w-2/3">
                    <form action="" method="POST">
                        <div class="space-y-6">
                            <?php 
                            $total = 0;
                            while($row = mysqli_fetch_assoc($query)): 
                                $subtotal = $row['harga'] * $row['jumlah'];
                                $total += $subtotal;
                            ?>
                            <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center">
                                <div class="w-24 h-24 rounded-2xl overflow-hidden bg-gray-50 flex-shrink-0">
                                    <img src="<?= $row['gambar'] ?: 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=200'; ?>" alt="<?= $row['nama_produk']; ?>" class="w-full h-full object-cover">
                                </div>
                                <div class="ml-6 flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-bold text-gray-900 text-lg"><?= $row['nama_produk']; ?></h3>
                                            <p class="text-amber-600 font-bold">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
                                        </div>
                                        <a href="keranjang.php?hapus=<?= $row['id']; ?>" class="text-gray-400 hover:text-red-500 transition p-2">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="flex items-center border border-gray-100 rounded-xl bg-gray-50 p-1">
                                            <input type="number" name="jumlah[<?= $row['id']; ?>]" value="<?= $row['jumlah']; ?>" min="1" max="<?= $row['stok']; ?>" class="w-12 text-center font-bold bg-transparent border-none focus:ring-0">
                                        </div>
                                        <p class="text-gray-900 font-bold">Rp <?= number_format($subtotal, 0, ',', '.'); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        </div>
                        <div class="mt-8 flex justify-between">
                            <a href="katalog.php" class="text-gray-500 font-bold hover:text-amber-600 transition flex items-center">
                                <i class="fa-solid fa-arrow-left mr-2"></i> Lanjut Belanja
                            </a>
                            <button type="submit" name="update_cart" class="text-amber-600 font-bold hover:underline">Update Keranjang</button>
                        </div>
                    </form>
                </div>

                <div class="lg:w-1/3 mt-12 lg:mt-0">
                    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 sticky top-24">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Ringkasan Pesanan</h3>
                        <div class="space-y-4 mb-8">
                            <div class="flex justify-between text-gray-500">
                                <span>Subtotal</span>
                                <span>Rp <?= number_format($total, 0, ',', '.'); ?></span>
                            </div>
                            <div class="flex justify-between text-gray-500">
                                <span>Pengiriman</span>
                                <span class="text-green-600 font-medium">Gratis</span>
                            </div>
                            <div class="h-[1px] bg-gray-50 w-full my-4"></div>
                            <div class="flex justify-between text-xl font-extrabold text-gray-900">
                                <span>Total</span>
                                <span>Rp <?= number_format($total, 0, ',', '.'); ?></span>
                            </div>
                        </div>
                        <a href="checkout.php" class="w-full bg-amber-600 text-white py-4 rounded-2xl font-bold text-center block hover:bg-amber-700 transition shadow-xl shadow-amber-200">
                            Lanjut ke Checkout
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="py-32 text-center bg-white rounded-[3rem] border border-dashed border-gray-200">
                <div class="max-w-xs mx-auto">
                    <div class="w-24 h-24 bg-amber-50 rounded-full flex items-center justify-center mx-auto mb-6 text-amber-600">
                        <i class="fa-solid fa-cart-shopping text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Keranjang Kosong</h3>
                    <p class="text-gray-500 mb-10">Sepertinya Anda belum memilih produk apapun untuk dibeli.</p>
                    <a href="katalog.php" class="inline-block bg-amber-600 text-white px-10 py-4 rounded-2xl font-bold hover:bg-amber-700 transition shadow-xl shadow-amber-200">
                        Mulai Belanja
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
