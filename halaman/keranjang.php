<?php
$awalan = "../";
include '../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../masuk.php");
    exit();
}

$id_pengguna = $_SESSION['user_id'];

if (isset($_GET['hapus'])) {
    $id_keranjang = (int)$_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM keranjang WHERE id = $id_keranjang AND id_pengguna = $id_pengguna");
    header("Location: keranjang.php");
    exit();
}

if (isset($_POST['update_cart'])) {
    foreach ($_POST['jumlah'] as $id_keranjang => $jumlah) {
        $jumlah = (int)$jumlah;
        if ($jumlah < 1) {
            $jumlah = 1;
        }
        mysqli_query($koneksi, "UPDATE keranjang SET jumlah = $jumlah WHERE id = $id_keranjang AND id_pengguna = $id_pengguna");
    }
    header("Location: keranjang.php");
    exit();
}

$kueri = mysqli_query($koneksi, "SELECT k.*, p.nama_produk, p.harga, p.gambar, p.stok FROM keranjang k JOIN produk p ON k.id_produk = p.id WHERE k.id_pengguna = $id_pengguna");

$kueri_jumlah = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(jumlah) as total_qty FROM keranjang WHERE id_pengguna = $id_pengguna"));
$total_jumlah = (int)$kueri_jumlah['total_qty'];
?>

<?php include '../bagian/atas.php'; ?>

<<!-- Bagian Keranjang Belanja Utama -->
<div class="py-12 sm:py-16 bg-slate-50 dark:bg-slate-950 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Halaman -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Keranjang Belanja</h1>
        </div>

        <?php if (mysqli_num_rows($kueri) > 0): ?>
            <!-- Form Checkout / Update Keranjang -->
            <form action="checkout.php" method="POST" id="formulirKeranjang">
                <div class="lg:flex lg:gap-8 items-start">
                    
                    <!-- Sektor Daftar Item Keranjang -->
                    <div class="lg:w-2/3 w-full mb-8 lg:mb-0">
                        <div class="bg-white dark:bg-slate-900 px-5 py-3.5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm mb-4 flex items-center">
                            <input type="checkbox" id="pilihSemua" checked class="w-4 h-4 text-lime-600 border-slate-350 dark:border-slate-700 rounded focus:ring-lime-500 cursor-pointer mr-3">
                            <label for="pilihSemua" class="text-xs font-bold text-slate-700 dark:text-slate-300 cursor-pointer select-none">Pilih Semua Kerajinan</label>
                        </div>
                        
                        <!-- List Item Keranjang -->
                        <div class="space-y-4">
                            <?php 
                            $total_akhir = 0;
                            while($baris = mysqli_fetch_assoc($kueri)): 
                                $total_sementara = $baris['harga'] * $baris['jumlah'];
                                $total_akhir += $total_sementara;
                            ?>
                            <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-5 relative">
                                
                                <!-- Checkbox Item -->
                                <div class="flex items-center flex-shrink-0">
                                    <input type="checkbox" name="cart_ids[]" value="<?= $baris['id']; ?>" checked class="kotak-centang-keranjang w-4 h-4 text-lime-600 border-slate-350 dark:border-slate-700 rounded focus:ring-lime-500 cursor-pointer" data-price="<?= $baris['harga']; ?>" data-id="<?= $baris['id']; ?>">
                                </div>
                                
                                <!-- Tombol Hapus Item -->
                                <a href="keranjang.php?hapus=<?= $baris['id']; ?>" class="absolute top-4 right-4 sm:static sm:order-last text-slate-300 dark:text-slate-600 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 p-2.5 rounded-xl flex-shrink-0" title="Hapus Item">
                                    <i class="fa-solid fa-trash-can text-base"></i>
                                </a>

                                <!-- Foto Thumbnail Produk -->
                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden bg-slate-50 dark:bg-slate-950 flex-shrink-0 border border-slate-200 dark:border-slate-800">
                                    <img src="<?= $baris['gambar']; ?>" alt="<?= $baris['nama_produk']; ?>" class="w-full h-full object-cover">
                                </div>
                                
                                <!-- Informasi Kerajinan -->
                                <div class="flex-1 flex flex-col justify-center">
                                    <h3 class="font-bold text-slate-800 dark:text-slate-200 text-sm sm:text-base pr-8 sm:pr-0 mb-1 line-clamp-1"><?= $baris['nama_produk']; ?></h3>
                                    <p class="text-lime-600 dark:text-lime-400 font-bold text-xs sm:text-sm mb-3 sm:mb-0">Rp <?= number_format($baris['harga'], 0, ',', '.'); ?></p>
                                    
                                    <!-- Kontrol Jumlah Seluler -->
                                    <div class="mt-auto flex items-center justify-between sm:hidden pt-2 border-t border-slate-100 dark:border-slate-800">
                                        <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded bg-white dark:bg-slate-800">
                                            <input type="number" name="jumlah[<?= $baris['id']; ?>]" value="<?= $baris['jumlah']; ?>" min="1" max="<?= $baris['stok']; ?>" class="w-12 text-center font-bold text-slate-800 dark:text-slate-200 bg-transparent border-none focus:ring-0 text-xs py-1.5 p-0 masukan-jumlah" data-id="<?= $baris['id']; ?>">
                                        </div>
                                        <p class="text-slate-800 dark:text-slate-200 font-bold text-xs nilai-total-sementara">Rp <?= number_format($total_sementara, 0, ',', '.'); ?></p>
                                    </div>
                                </div>

                                <!-- Kontrol Jumlah Desktop -->
                                <div class="hidden sm:flex flex-col items-end gap-2 flex-shrink-0">
                                    <div class="flex items-center border border-slate-200 dark:border-slate-700 rounded bg-white dark:bg-slate-800 overflow-hidden focus-within:border-lime-500">
                                        <input type="number" name="jumlah[<?= $baris['id']; ?>]" value="<?= $baris['jumlah']; ?>" min="1" max="<?= $baris['stok']; ?>" class="w-12 text-center font-bold text-slate-850 dark:text-slate-200 bg-transparent border-none focus:ring-0 text-xs py-1 px-1.5 masukan-jumlah" data-id="<?= $baris['id']; ?>">
                                    </div>
                                    <p class="text-slate-850 dark:text-slate-200 font-bold text-xs sm:text-sm nilai-total-sementara">Rp <?= number_format($total_sementara, 0, ',', '.'); ?></p>
                                </div>
                                
                             </div>
                            <?php endwhile; ?>
                        </div>
                        
                        <!-- Navigasi Aksi Bawah Keranjang -->
                        <div class="mt-6 flex flex-col-reverse sm:flex-row justify-between items-center gap-4">
                            <a href="katalog.php" class="flex items-center text-slate-500 dark:text-slate-400 font-semibold hover:text-lime-600 dark:hover:text-lime-400 text-xs sm:text-sm">
                                <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Katalog
                            </a>
                            <button type="submit" name="update_cart" formaction="keranjang.php" class="w-full sm:w-auto bg-slate-100 dark:bg-slate-800 text-slate-650 dark:text-slate-300 px-5 py-3 rounded-xl font-bold hover:bg-lime-50 dark:hover:bg-lime-950/30 hover:text-lime-750 dark:hover:text-lime-400 border border-transparent flex items-center justify-center text-xs sm:text-sm cursor-pointer shadow-sm">
                                <i class="fa-solid fa-rotate mr-2"></i> Perbarui Keranjang
                            </button>
                        </div>
                    </div>

                    <!-- Ringkasan Order & Checkout -->
                    <div class="lg:w-1/3 w-full">
                        <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm sticky top-24">
                            <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-5">Ringkasan Pemesanan</h3>
                            
                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                                    <span>Total Kerajinan Pilihan</span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200 nilai-total">Rp <?= number_format($total_akhir, 0, ',', '.'); ?></span>
                                </div>
                                <div class="flex justify-between text-slate-500 dark:text-slate-400 text-xs sm:text-sm">
                                    <span>Biaya Pengiriman</span>
                                    <span class="text-lime-700 dark:text-lime-400 font-bold bg-lime-50 dark:bg-lime-950/40 px-2.5 py-0.5 rounded-lg text-[10px] sm:text-xs">Gratis Ongkir</span>
                                </div>
                                
                                <div class="h-px bg-slate-150 dark:bg-slate-800 w-full my-4"></div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-sm sm:text-base font-bold text-slate-800 dark:text-slate-100">Total Harga</span>
                                    <span class="text-xl font-bold text-lime-600 dark:text-lime-400 nilai-total">Rp <?= number_format($total_akhir, 0, ',', '.'); ?></span>
                                </div>
                            </div>
                            
                            <button type="submit" name="checkout" class="w-full inline-flex items-center justify-center bg-lime-600 text-white py-3.5 rounded-xl font-bold text-sm hover:bg-lime-700 cursor-pointer border-none outline-none">
                                Lanjutkan Checkout <i class="fa-solid fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>
                    
                </div>
            </form>
                
        <?php else: ?>
            
            <!-- Keadaan jika keranjang kosong -->
            <div class="py-16 sm:py-24 text-center bg-white dark:bg-slate-900 rounded-xl border border-dashed border-slate-200 dark:border-slate-800">
                <div class="max-w-md mx-auto px-4">
                    <div class="w-16 h-16 bg-slate-50 dark:bg-slate-950 rounded-full flex items-center justify-center mx-auto mb-4.5 text-slate-350 dark:text-slate-700 text-3xl shadow-inner">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 dark:text-slate-200 mb-2">Keranjang Belanja Kosong</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6 text-xs sm:text-sm leading-relaxed">Anda belum menambahkan produk HandMadura ke dalam keranjang. Silakan jelajahi mahakarya pengrajin kami!</p>
                    <a href="katalog.php" class="inline-flex items-center justify-center bg-lime-600 text-white px-7 py-3.5 rounded-xl font-bold text-xs sm:text-sm hover:bg-lime-700">
                        <i class="fa-solid fa-bag-shopping mr-2"></i> Mulai Berbelanja
                    </a>
                </div>
            </div>
            
        <?php endif; ?>
        
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const centangSemua = document.getElementById('pilihSemua');
    const daftarKotakCentang = document.querySelectorAll('.kotak-centang-keranjang');
    const daftarMasukanJumlah = document.querySelectorAll('.masukan-jumlah');

    function hitungTotal() {
        let total = 0;
        daftarKotakCentang.forEach(kotakCentang => {
            if (kotakCentang.checked) {
                const id = kotakCentang.getAttribute('data-id');
                const harga = parseFloat(kotakCentang.getAttribute('data-price'));
                
                const masukanJumlah = document.querySelector(`.masukan-jumlah[data-id="${id}"]`);
                const jumlah = masukanJumlah ? parseInt(masukanJumlah.value) || 0 : 0;
                const totalSementara = harga * jumlah;
                
                const group = kotakCentang.closest('.group');
                if (group) {
                    const elemenTotalSementara = group.querySelectorAll('.nilai-total-sementara');
                    elemenTotalSementara.forEach(el => {
                        el.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalSementara);
                    });
                }
                
                total += totalSementara;
            } else {
                const id = kotakCentang.getAttribute('data-id');
                const harga = parseFloat(kotakCentang.getAttribute('data-price'));
                const masukanJumlah = document.querySelector(`.masukan-jumlah[data-id="${id}"]`);
                const jumlah = masukanJumlah ? parseInt(masukanJumlah.value) || 0 : 0;
                const totalSementara = harga * jumlah;
                const group = kotakCentang.closest('.group');
                if (group) {
                    const elemenTotalSementara = group.querySelectorAll('.nilai-total-sementara');
                    elemenTotalSementara.forEach(el => {
                        el.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalSementara);
                    });
                }
            }
        });
        
        const elemenTotal = document.querySelectorAll('.nilai-total');
        elemenTotal.forEach(el => {
            el.innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        });
    }

    daftarMasukanJumlah.forEach(input => {
        input.addEventListener('input', function() {
            const id = this.getAttribute('data-id');
            const nilai = this.value;
            const kecocokan = document.querySelectorAll(`.masukan-jumlah[data-id="${id}"]`);
            kecocokan.forEach(m => {
                if (m !== this) m.value = nilai;
            });
            hitungTotal();
        });
        input.addEventListener('change', function() {
            const id = this.getAttribute('data-id');
            const nilai = this.value;
            const kecocokan = document.querySelectorAll(`.masukan-jumlah[data-id="${id}"]`);
            kecocokan.forEach(m => {
                if (m !== this) m.value = nilai;
            });
            hitungTotal();
        });
    });

    daftarKotakCentang.forEach(kotakCentang => {
        kotakCentang.addEventListener('change', hitungTotal);
    });

    if (centangSemua) {
        centangSemua.addEventListener('change', function() {
            daftarKotakCentang.forEach(kotakCentang => {
                kotakCentang.checked = this.checked;
            });
            hitungTotal();
        });
    }
});
</script>

<?php include '../bagian/bawah.php'; ?>
