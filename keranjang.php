<?php
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: masuk.php");
    exit();
}

$id_pengguna = $_SESSION['user_id'];


if (isset($_GET['hapus'])) {
    $id_cart = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM keranjang WHERE id = $id_cart AND id_pengguna = $id_pengguna");
    header("Location: keranjang.php");
    exit();
}


if (isset($_POST['update_cart'])) {
    foreach ($_POST['jumlah'] as $id_cart => $jumlah) {
        $jumlah = (int)$jumlah;
        if ($jumlah < 1) {
            $jumlah = 1;
        }
        mysqli_query($conn, "UPDATE keranjang SET jumlah = $jumlah WHERE id = $id_cart AND id_pengguna = $id_pengguna");
    }
    header("Location: keranjang.php");
    exit();
}

$query = mysqli_query($conn, "SELECT k.*, p.nama_produk, p.harga, p.gambar, p.stok FROM keranjang k JOIN produk p ON k.id_produk = p.id WHERE k.id_pengguna = $id_pengguna");

$countQuery = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(jumlah) as total_qty FROM keranjang WHERE id_pengguna = $id_pengguna"));
$total_qty = (int)$countQuery['total_qty'];
?>

<?php include 'includes/header.php'; ?>

<div class="py-8 sm:py-10 bg-slate-50 dark:bg-slate-950 min-h-screen transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-6 sm:mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 dark:text-slate-100">Keranjang Belanja <span class="text-slate-400 dark:text-slate-500 font-normal text-lg sm:text-xl">(<?= $total_qty; ?> Item)</span></h1>
        </div>

        <?php if (mysqli_num_rows($query) > 0): ?>
            <form action="checkout.php" method="POST" id="cartForm">
                <div class="lg:flex lg:gap-6 items-start">
                    
                    <div class="lg:w-2/3 w-full mb-6 lg:mb-0">
                        <!-- Pilih Semua Header -->
                        <div class="bg-white dark:bg-slate-900 px-4 py-2.5 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm mb-3 flex items-center">
                            <input type="checkbox" id="selectAll" checked class="w-4 h-4 text-lime-600 border-slate-300 dark:border-slate-700 rounded focus:ring-lime-500 cursor-pointer mr-2">
                            <label for="selectAll" class="text-xs font-bold text-slate-700 dark:text-slate-350 cursor-pointer select-none">Pilih Semua</label>
                        </div>
                        
                        <div class="space-y-3">
                            <?php 
                            $total = 0;
                            while($row = mysqli_fetch_assoc($query)): 
                                $subtotal = $row['harga'] * $row['jumlah'];
                                $total += $subtotal;
                            ?>
                            <div class="group bg-white dark:bg-slate-900 p-3 sm:p-4 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 hover:shadow-md hover:border-lime-200 dark:hover:border-lime-700 transition-all duration-300 relative">
                                
                                <!-- Checkbox Item -->
                                <div class="flex items-center flex-shrink-0">
                                    <input type="checkbox" name="cart_ids[]" value="<?= $row['id']; ?>" checked class="cart-checkbox w-4 h-4 text-lime-600 border-slate-300 dark:border-slate-700 rounded focus:ring-lime-500 cursor-pointer" data-price="<?= $row['harga']; ?>" data-id="<?= $row['id']; ?>">
                                </div>
                                
                                <a href="keranjang.php?hapus=<?= $row['id']; ?>" class="absolute top-3 right-3 sm:static sm:order-last text-slate-300 dark:text-slate-600 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 p-2 rounded-lg transition-colors duration-300 flex-shrink-0" title="Hapus Item">
                                    <i class="fa-solid fa-trash-can text-base"></i>
                                </a>

                                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-lg overflow-hidden bg-slate-50 dark:bg-slate-950 flex-shrink-0 border border-slate-100 dark:border-slate-800">
                                    <img src="<?= $row['gambar'] ?: 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=200'; ?>" alt="<?= $row['nama_produk']; ?>" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=200';">
                                </div>
                                
                                <div class="flex-1 flex flex-col justify-center">
                                    <h3 class="font-bold text-slate-800 dark:text-slate-200 text-sm sm:text-base pr-8 sm:pr-0 mb-0.5 line-clamp-1"><?= $row['nama_produk']; ?></h3>
                                    <p class="text-lime-600 dark:text-lime-400 font-extrabold text-xs sm:text-sm mb-2 sm:mb-0">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>
                                    
                                    <div class="mt-auto flex items-center justify-between sm:hidden pt-2 border-t border-slate-100 dark:border-slate-800">
                                        <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded bg-white dark:bg-slate-800">
                                            <input type="number" name="jumlah[<?= $row['id']; ?>]" value="<?= $row['jumlah']; ?>" min="1" max="<?= $row['stok']; ?>" class="w-12 text-center font-bold text-slate-800 dark:text-slate-200 bg-transparent border-none focus:ring-0 text-xs py-1 p-0 quantity-input" data-id="<?= $row['id']; ?>">
                                        </div>
                                        <p class="text-slate-800 dark:text-slate-200 font-bold text-xs subtotal-val">Rp <?= number_format($subtotal, 0, ',', '.'); ?></p>
                                    </div>
                                </div>

                                <div class="hidden sm:flex flex-col items-end gap-1.5 flex-shrink-0">
                                    <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded bg-white dark:bg-slate-800 overflow-hidden focus-within:border-lime-500 focus-within:ring-2 focus-within:ring-lime-500/20 transition-all">
                                        <input type="number" name="jumlah[<?= $row['id']; ?>]" value="<?= $row['jumlah']; ?>" min="1" max="<?= $row['stok']; ?>" class="w-12 text-center font-bold text-slate-800 dark:text-slate-200 bg-transparent border-none focus:ring-0 text-xs py-1 px-1 quantity-input" data-id="<?= $row['id']; ?>">
                                    </div>
                                    <p class="text-slate-800 dark:text-slate-200 font-extrabold text-xs sm:text-sm subtotal-val">Rp <?= number_format($subtotal, 0, ',', '.'); ?></p>
                                </div>
                                
                            </div>
                            <?php endwhile; ?>
                        </div>
                        
                        <div class="mt-5 flex flex-col-reverse sm:flex-row justify-between items-center gap-3">
                            <a href="katalog.php" class="group flex items-center text-slate-500 dark:text-slate-450 font-medium hover:text-lime-600 dark:hover:text-lime-400 transition-colors duration-300 text-xs sm:text-sm">
                                <i class="fa-solid fa-arrow-left mr-1.5 transition-transform duration-300 group-hover:-translate-x-1"></i> Lanjut Belanja
                            </a>
                            <button type="submit" name="update_cart" formaction="keranjang.php" class="w-full sm:w-auto bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 px-4 py-2 rounded-lg font-bold hover:bg-lime-55 dark:hover:bg-lime-950/30 hover:text-lime-700 dark:hover:text-lime-450 hover:border-lime-200 border border-transparent transition-all duration-300 flex items-center justify-center text-xs sm:text-sm cursor-pointer">
                                <i class="fa-solid fa-rotate mr-1.5"></i> Update Keranjang
                            </button>
                        </div>
                    </div>

                    <div class="lg:w-1/3 w-full">
                        <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm sticky top-20">
                            <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">Ringkasan Pesanan</h3>
                            
                            <div class="space-y-3 mb-6">
                                <div class="flex justify-between text-slate-500 dark:text-slate-450 text-xs sm:text-sm">
                                    <span>Subtotal</span>
                                    <span class="font-medium text-slate-700 dark:text-slate-300 total-val">Rp <?= number_format($total, 0, ',', '.'); ?></span>
                                </div>
                                <div class="flex justify-between text-slate-500 dark:text-slate-450 text-xs sm:text-sm">
                                    <span>Estimasi Pengiriman</span>
                                    <span class="text-lime-600 dark:text-lime-400 font-bold bg-lime-50 dark:bg-lime-950/40 px-2 py-0.5 rounded text-[10px] sm:text-xs border border-transparent dark:border-lime-900/30">Gratis</span>
                                </div>
                                
                                <div class="h-px bg-slate-100 dark:bg-slate-800 w-full my-3"></div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-sm sm:text-base font-bold text-slate-800 dark:text-slate-100">Total</span>
                                    <span class="text-lg sm:text-xl font-extrabold text-lime-600 dark:text-lime-400 total-val">Rp <?= number_format($total, 0, ',', '.'); ?></span>
                                </div>
                            </div>
                            
                            <button type="submit" name="checkout" class="group w-full inline-flex items-center justify-center bg-lime-600 text-white py-2.5 rounded-lg font-bold text-sm hover:bg-lime-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-lime-200/50 transition-all duration-300 cursor-pointer border-none outline-none">
                                Checkout Sekarang <i class="fa-solid fa-arrow-right ml-1.5 transition-transform duration-300 group-hover:translate-x-1"></i>
                            </button>
                        </div>
                    </div>
                    
                </div>
            </form>
                
        <?php else: ?>
            
            <div class="py-12 sm:py-20 text-center bg-white dark:bg-slate-900 rounded-xl border border-dashed border-slate-200 dark:border-slate-800">
                <div class="max-w-sm mx-auto px-4">
                    <div class="w-14 h-14 bg-slate-50 dark:bg-slate-950 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300 dark:text-slate-700 text-2xl">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold text-slate-800 dark:text-slate-200 mb-1.5">Keranjang Kosong</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6 text-xs sm:text-sm">Sepertinya Anda belum menambahkan produk apapun ke dalam keranjang. Yuk, lihat koleksi kami!</p>
                    <a href="katalog.php" class="inline-flex items-center justify-center bg-lime-600 text-white px-6 py-2 rounded-lg font-bold text-xs sm:text-sm hover:bg-lime-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-lime-200/50 transition-all duration-300">
                        <i class="fa-solid fa-bag-shopping mr-1.5"></i> Mulai Belanja
                    </a>
                </div>
            </div>
            
        <?php endif; ?>
        
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.cart-checkbox');
    const qtyInputs = document.querySelectorAll('.quantity-input');

    function calculateTotal() {
        let total = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) {
                const id = cb.getAttribute('data-id');
                const price = parseFloat(cb.getAttribute('data-price'));
                
                // Find quantity input
                const qtyInput = document.querySelector(`.quantity-input[data-id="${id}"]`);
                const qty = qtyInput ? parseInt(qtyInput.value) || 0 : 0;
                const subtotal = price * qty;
                
                // Update subtotal elements
                const group = cb.closest('.group');
                if (group) {
                    const subtotalElements = group.querySelectorAll('.subtotal-val');
                    subtotalElements.forEach(el => {
                        el.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
                    });
                }
                
                total += subtotal;
            } else {
                // If not checked, set subtotal to 0 or update it based on quantity anyway
                const id = cb.getAttribute('data-id');
                const price = parseFloat(cb.getAttribute('data-price'));
                const qtyInput = document.querySelector(`.quantity-input[data-id="${id}"]`);
                const qty = qtyInput ? parseInt(qtyInput.value) || 0 : 0;
                const subtotal = price * qty;
                const group = cb.closest('.group');
                if (group) {
                    const subtotalElements = group.querySelectorAll('.subtotal-val');
                    subtotalElements.forEach(el => {
                        el.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
                    });
                }
            }
        });
        
        // Update all total-val elements
        const totalElements = document.querySelectorAll('.total-val');
        totalElements.forEach(el => {
            el.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        });
    }

    // Sync quantity inputs and recalculate
    qtyInputs.forEach(input => {
        input.addEventListener('input', function() {
            const id = this.getAttribute('data-id');
            const val = this.value;
            const matches = document.querySelectorAll(`.quantity-input[data-id="${id}"]`);
            matches.forEach(m => {
                if (m !== this) m.value = val;
            });
            calculateTotal();
        });
        input.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const val = this.value;
            const matches = document.querySelectorAll(`.quantity-input[data-id="${id}"]`);
            matches.forEach(m => {
                if (m !== this) m.value = val;
            });
            calculateTotal();
        });
    });

    // Checkboxes change listeners
    checkboxes.forEach(cb => {
        cb.addEventListener('change', calculateTotal);
    });

    // Select All listener
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            calculateTotal();
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>