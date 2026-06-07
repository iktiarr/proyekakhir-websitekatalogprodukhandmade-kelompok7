<?php
$awalan = "../";
include '../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../masuk.php");
    exit();
}

$id_pengguna = $_SESSION['user_id'];
$galat = '';

$kueri_pengguna_info = kueri("SELECT alamat, no_telp FROM pengguna WHERE id = ?", [$id_pengguna]);
$data_pengguna_info = mysqli_fetch_assoc($kueri_pengguna_info);
$autofill_alamat = $data_pengguna_info['alamat'] ?? '';
$autofill_no_telp = $data_pengguna_info['no_telp'] ?? '';

$daftar_id_keranjang = [];
if (isset($_POST['cart_ids'])) {
    $daftar_id_keranjang = $_POST['cart_ids'];
} elseif (isset($_POST['checked_cart_ids'])) {
    $daftar_id_keranjang = $_POST['checked_cart_ids'];
}

if (!empty($daftar_id_keranjang)) {
    $placeholders = implode(',', array_fill(0, count($daftar_id_keranjang), '?'));
    $params = array_merge([$id_pengguna], array_map('intval', $daftar_id_keranjang));
    $kueri_keranjang = kueri("SELECT k.*, p.harga, p.stok, p.nama_produk FROM keranjang k JOIN produk p ON k.id_produk = p.id WHERE k.id_pengguna = ? AND k.id IN ($placeholders)", $params);
} else {
    $kueri_keranjang = kueri("SELECT k.*, p.harga, p.stok, p.nama_produk FROM keranjang k JOIN produk p ON k.id_produk = p.id WHERE k.id_pengguna = ?", [$id_pengguna]);
}

if (!$kueri_keranjang || mysqli_num_rows($kueri_keranjang) == 0) {
    header("Location: katalog.php");
    exit();
}

$total_harga = 0;
$daftar_kerajinan = [];
while ($baris = mysqli_fetch_assoc($kueri_keranjang)) {
    $total_harga += $baris['harga'] * $baris['jumlah'];
    $daftar_kerajinan[] = $baris;
}

if (isset($_POST['buat_pesanan'])) {
    $alamat_lengkap = trim($_POST['alamat']);
    $no_telepon = trim($_POST['no_telp']);
    $metode_pembayaran = $_POST['metode_pembayaran'];

    if (empty($alamat_lengkap) || empty($no_telepon)) {
        $galat = "Alamat dan Nomor Telepon wajib diisi!";
    } elseif (strlen($no_telepon) < 12) {
        $galat = "Nomor telepon minimal harus terdiri dari 12 digit!";
    } elseif (!preg_match("/^[0-9]+$/", $no_telepon)) {
        $galat = "Nomor telepon hanya boleh berisi angka!";
    } else {
        $alamat = $alamat_lengkap . " | Telp: " . $no_telepon;
        mysqli_begin_transaction($koneksi);

        try {
            $berhasil_pesan = kueri("INSERT INTO pesanan (id_pengguna, total_harga, status, alamat, metode_pembayaran) VALUES (?, ?, 'menunggu', ?, ?)", [$id_pengguna, $total_harga, $alamat, $metode_pembayaran]);
        $id_pesanan = mysqli_insert_id($koneksi);
        
        $nama_pembeli = $_SESSION['nama'];
        $tag = "#HM-" . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT);
        kueri("INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan) VALUES (?, ?, 'pesanan', 'tambah', ?)", [$id_pengguna, $nama_pembeli, "Membuat pesanan baru $tag"]);

        foreach ($daftar_kerajinan as $kerajinan) {
            $id_produk = $kerajinan['id_produk'];
            $jumlah = $kerajinan['jumlah'];
            $harga = $kerajinan['harga'];
            
            $hasil_produk = mysqli_fetch_assoc(kueri("SELECT stok, nama_produk FROM produk WHERE id = ?", [$id_produk]));
            if ($hasil_produk['stok'] < $jumlah) {
                throw new Exception("Stok untuk '" . $hasil_produk['nama_produk'] . "' tidak mencukupi! Tersisa: " . $hasil_produk['stok']);
            }
            
            kueri("INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga) VALUES (?, ?, ?, ?)", [$id_pesanan, $id_produk, $jumlah, $harga]);
            kueri("UPDATE produk SET stok = stok - ? WHERE id = ?", [$jumlah, $id_produk]);
        }

        $daftar_id_keranjang_dibayar = [];
        foreach ($daftar_kerajinan as $kerajinan) {
            $daftar_id_keranjang_dibayar[] = $kerajinan['id'];
        }
        if (!empty($daftar_id_keranjang_dibayar)) {
            $placeholders_dibayar = implode(',', array_fill(0, count($daftar_id_keranjang_dibayar), '?'));
            $params_dibayar = array_merge([$id_pengguna], array_map('intval', $daftar_id_keranjang_dibayar));
            kueri("DELETE FROM keranjang WHERE id_pengguna = ? AND id IN ($placeholders_dibayar)", $params_dibayar);
        }

            mysqli_commit($koneksi);
            
            header("Location: bayar.php?id=$id_pesanan");
            exit();
        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            $galat = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>

<?php include '../bagian/atas.php'; ?>

<div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-[80vh] transition-colors duration-300">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Checkout Pesanan</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-1.5">Lengkapi detail pengiriman dan selesaikan pesanan kerajinan pilihan Anda.</p>
        </div>

        <?php if ($galat): ?>
            <div class="bg-red-50 dark:bg-red-950/30 text-red-600 dark:text-red-400 p-4 rounded-xl mb-6 text-sm border border-red-200 dark:border-red-900/50 flex items-start gap-3 shadow-sm">
                <i class="fa-solid fa-circle-exclamation mt-0.5 text-base flex-shrink-0"></i> 
                <span><?= $galat; ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="lg:flex lg:gap-8 items-start">
            <?php foreach ($daftar_kerajinan as $kerajinan): ?>
                <input type="hidden" name="checked_cart_ids[]" value="<?= $kerajinan['id']; ?>">
            <?php endforeach; ?>
            
            <div class="lg:w-2/3 space-y-6 w-full mb-6 lg:mb-0">
                
                <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-xl border border-slate-200/60 dark:border-slate-800 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-150 mb-5 flex items-center">
                        <span class="w-7 h-7 bg-lime-100 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400 rounded-lg flex items-center justify-center text-xs font-extrabold mr-3">1</span>
                        Informasi Pengiriman
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Nama Penerima</label>
                            <input type="text" value="<?= $_SESSION['nama']; ?>" readonly class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-100 text-slate-500 outline-none cursor-not-allowed text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Nomor Telepon</label>
                            <input type="tel" name="no_telp" value="<?= htmlspecialchars($autofill_no_telp); ?>" required minlength="12" pattern="[0-9]{12,}" title="Nomor telepon minimal harus terdiri dari 12 digit angka" class="w-full px-4 py-3 bg-white rounded-xl border border-slate-200 outline-none text-sm text-slate-800 placeholder-slate-400" placeholder="Contoh: 081234567890" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Alamat Lengkap Tujuan</label>
                            <textarea name="alamat" required rows="3" class="w-full px-4 py-3 bg-white rounded-xl border border-slate-200 outline-none text-sm text-slate-800 placeholder-slate-400 resize-none"><?= $autofill_alamat; ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-xl border border-slate-200/60 dark:border-slate-800 shadow-sm">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-150 mb-5 flex items-center">
                        <span class="w-7 h-7 bg-lime-100 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400 rounded-lg flex items-center justify-center text-xs font-extrabold mr-3">2</span>
                        Metode Pembayaran
                    </h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                        <label class="relative flex items-center p-3.5 border border-slate-200 dark:border-slate-800 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-950/50 transition-colors has-[:checked]:border-lime-500 has-[:checked]:bg-lime-50/20 dark:has-[:checked]:bg-lime-950/20">
                            <input type="radio" name="metode_pembayaran" value="BCA Virtual Account" checked class="w-4 h-4 text-lime-600 border-slate-300 cursor-pointer">
                            <span class="ml-2.5 text-xs font-bold text-slate-700 dark:text-slate-300">BCA Virtual Account</span>
                        </label>
                        
                        <label class="relative flex items-center p-3.5 border border-slate-200 dark:border-slate-800 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-950/50 transition-colors has-[:checked]:border-lime-500 has-[:checked]:bg-lime-50/20 dark:has-[:checked]:bg-lime-950/20">
                            <input type="radio" name="metode_pembayaran" value="Mandiri Virtual Account" class="w-4 h-4 text-lime-600 border-slate-300 cursor-pointer">
                            <span class="ml-2.5 text-xs font-bold text-slate-700 dark:text-slate-300">Mandiri VA</span>
                        </label>
 
                        <label class="relative flex items-center p-3.5 border border-slate-200 dark:border-slate-800 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-950/50 transition-colors has-[:checked]:border-lime-500 has-[:checked]:bg-lime-50/20 dark:has-[:checked]:bg-lime-950/20">
                            <input type="radio" name="metode_pembayaran" value="GoPay" class="w-4 h-4 text-lime-600 border-slate-300 cursor-pointer">
                            <span class="ml-2.5 text-xs font-bold text-slate-700 dark:text-slate-300">GoPay</span>
                        </label>
 
                        <label class="relative flex items-center p-3.5 border border-slate-200 dark:border-slate-800 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-950/50 transition-colors has-[:checked]:border-lime-500 has-[:checked]:bg-lime-50/20 dark:has-[:checked]:bg-lime-950/20">
                            <input type="radio" name="metode_pembayaran" value="Dana" class="w-4 h-4 text-lime-600 border-slate-300 cursor-pointer">
                            <span class="ml-2.5 text-xs font-bold text-slate-700 dark:text-slate-300">Dana</span>
                        </label>
 
                        <label class="relative flex items-center p-3.5 border border-slate-200 dark:border-slate-800 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-950/50 transition-colors has-[:checked]:border-lime-500 has-[:checked]:bg-lime-50/20 dark:has-[:checked]:bg-lime-950/20">
                            <input type="radio" name="metode_pembayaran" value="Alfamart" class="w-4 h-4 text-lime-600 border-slate-300 cursor-pointer">
                            <span class="ml-2.5 text-xs font-bold text-slate-700 dark:text-slate-300">Alfamart</span>
                        </label>
 
                        <label class="relative flex items-center p-3.5 border border-slate-200 dark:border-slate-800 rounded-xl cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-950/50 transition-colors has-[:checked]:border-lime-500 has-[:checked]:bg-lime-50/20 dark:has-[:checked]:bg-lime-950/20">
                            <input type="radio" name="metode_pembayaran" value="Indomaret" class="w-4 h-4 text-lime-600 border-slate-300 cursor-pointer">
                            <span class="ml-2.5 text-xs font-bold text-slate-700 dark:text-slate-300">Indomaret</span>
                        </label>
                    </div>
                </div>
                
            </div>

            <div class="lg:w-1/3 w-full">
                <div class="bg-white dark:bg-slate-900 p-6 sm:p-7 rounded-xl border border-slate-200/60 dark:border-slate-800 shadow-sm sticky top-24">
                    <h3 class="text-base sm:text-lg font-bold text-slate-800 dark:text-slate-100 mb-4">Ringkasan Pesanan</h3>
                    
                    <div class="space-y-2.5 mb-5 max-h-48 overflow-y-auto pr-1.5 custom-scrollbar">
                        <?php 
                        mysqli_data_seek($kueri_keranjang, 0);
                        while($baris = mysqli_fetch_assoc($kueri_keranjang)): 
                        ?>
                        <div class="flex justify-between text-xs items-start gap-4 pb-2.5 border-b border-slate-100 dark:border-slate-800 last:border-0 last:pb-0">
                            <span class="text-slate-500 dark:text-slate-400 line-clamp-1 leading-relaxed">
                                <span class="font-extrabold text-slate-400 dark:text-slate-600"><?= $baris['jumlah']; ?>x</span> <?= $baris['nama_produk']; ?>
                            </span>
                            <span class="font-bold text-slate-800 dark:text-slate-200 whitespace-nowrap">Rp <?= number_format($baris['harga'] * $baris['jumlah'], 0, ',', '.'); ?></span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <div class="h-px bg-slate-200 dark:bg-slate-800 w-full mb-4"></div>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center text-slate-500 dark:text-slate-400 text-xs">
                            <span>Total Ongkos Kirim</span>
                            <span class="text-lime-700 dark:text-lime-400 font-extrabold">Gratis Ongkir</span>
                        </div>
                        <div class="flex justify-between items-center pt-2">
                            <span class="text-sm font-bold text-slate-800 dark:text-slate-200">Total Belanja</span>
                            <span class="text-xl font-extrabold text-lime-600 dark:text-lime-400">Rp <?= number_format($total_harga, 0, ',', '.'); ?></span>
                        </div>
                    </div>
                    
                    <button type="submit" name="buat_pesanan" class="w-full bg-lime-600 text-white py-3 rounded-xl font-bold text-center flex items-center justify-center hover:bg-lime-700 transition-all duration-300 text-sm cursor-pointer border-none shadow-sm">
                        Buat Pesanan & Bayar <i class="fa-solid fa-arrow-right ml-2"></i>
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
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<?php include '../bagian/bawah.php'; ?>
