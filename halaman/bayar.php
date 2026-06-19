<?php
/**
 * Halaman Bayar (Konfirmasi Pembayaran)
 * Berfungsi untuk menerbitkan nomor Virtual Account / Kode Pembayaran unik
 * dan memproses status pelunasan transaksi pembelian kerajinan Madura.
 */
$awalan = "../";
include '../koneksi.php';

if (!isset($_SESSION['user']['id'])) {
    header("Location: ../masuk.php");
    exit();
}

$id_pengguna = $_SESSION['user']['id'];
$berhasil = '';
$galat = '';

if (isset($_GET['id'])) {
    $id_pesanan = (int)$_GET['id'];
    
    // Tarik data pesanan yang bersangkutan dari database
    $kueri_pesanan = kueri("SELECT * FROM pesanan WHERE id = ? AND id_pengguna = ?", [$id_pesanan, $id_pengguna]);
    $data_pesanan = mysqli_fetch_assoc($kueri_pesanan);
    
    if (!$data_pesanan) {
        header("Location: riwayat.php");
        exit();
    }
    
    // Cegah pembayaran ulang jika status pesanan tidak menunggu pembayaran
    if ($data_pesanan['status'] !== 'menunggu') {
        header("Location: riwayat.php");
        exit();
    }
    
    $metode_pembayaran = $data_pesanan['metode_pembayaran'];
    $total_pembayaran = $data_pesanan['total_harga'];
    
    // Tentukan kode pembayaran / nomor virtual account secara acak/terstruktur
    $kode_pembayaran = '';
    if ($metode_pembayaran === 'BCA Virtual Account') {
        $kode_pembayaran = '80012' . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT) . '932';
    } elseif ($metode_pembayaran === 'Mandiri Virtual Account') {
        $kode_pembayaran = '89022' . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT) . '854';
    } elseif ($metode_pembayaran === 'GoPay') {
        $kode_pembayaran = 'GP-9482' . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT);
    } elseif ($metode_pembayaran === 'Dana') {
        $kode_pembayaran = 'DN-1192' . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT);
    } elseif ($metode_pembayaran === 'Alfamart') {
        $kode_pembayaran = 'ALFA' . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT) . '83';
    } elseif ($metode_pembayaran === 'Indomaret') {
        $kode_pembayaran = 'INDO' . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT) . '59';
    } else {
        $kode_pembayaran = 'PAY' . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT);
    }
    
    // Tangani aksi ketika tombol selesaikan pembayaran diklik oleh pengguna
    if (isset($_POST['selesaikan_pembayaran'])) {
        $kueri_perbarui = kueri("UPDATE pesanan SET bukti_pembayaran = ?, status = 'dibayar' WHERE id = ?", [$kode_pembayaran, $id_pesanan]);
        if ($kueri_perbarui) {
            $berhasil = "Pembayaran Berhasil! Pesanan Anda telah lunas dan siap dikirim.";
            $nama_pembeli = $_SESSION['user']['nama'];
            $tag = "#HM-" . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT);
            kueri("INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan) VALUES (?, ?, 'pesanan', 'dibayar', ?)", [$id_pengguna, $nama_pembeli, "Melakukan konfirmasi pembayaran untuk $tag"]);
            header("Refresh: 2; url=riwayat.php");
        } else {
            $galat = "Terjadi kesalahan saat memproses pembayaran.";
        }
    }
    
    include '../bagian/atas.php';
    ?>
    <div class="py-16 bg-slate-50 dark:bg-slate-950 min-h-[80vh]">
        <div class="max-w-xl mx-auto px-4">
            
            <!-- Judul & Deskripsi Halaman -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-slate-800 dark:text-slate-100">Selesaikan Pembayaran</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Gunakan kode pembayaran unik di bawah untuk meresmikan pesanan kerajinan Anda.</p>
            </div>

            <!-- Notifikasi Berhasil -->
            <?php if ($berhasil): ?>
                <div class="bg-lime-50 dark:bg-lime-950/20 text-lime-800 dark:text-lime-400 p-5 rounded-xl mb-6 border border-lime-200 dark:border-lime-900/30 flex items-center">
                    <i class="fa-solid fa-circle-check text-2xl mr-3 flex-shrink-0 text-lime-600"></i>
                    <div>
                        <p class="font-bold text-sm"><?= $berhasil; ?></p>
                        <p class="text-xs text-lime-600 dark:text-lime-500 mt-0.5 font-medium">Mengarahkan Anda ke Riwayat Transaksi...</p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Notifikasi Gagal -->
            <?php if ($galat): ?>
                <div class="bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 p-4 rounded-xl mb-6 border border-red-200 dark:border-red-900/30 flex items-center">
                    <i class="fa-solid fa-circle-exclamation mr-3 flex-shrink-0"></i> <?= $galat; ?>
                </div>
            <?php endif; ?>

            <!-- Rincian Jumlah & Kode Pembayaran -->
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="p-6 bg-slate-50/50 dark:bg-slate-950/40 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center">
                    <div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 uppercase tracking-widest font-bold">Total Pembayaran</p>
                        <p class="text-2xl font-bold text-lime-600 dark:text-lime-400">Rp <?= number_format($total_pembayaran, 0, ',', '.'); ?></p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-bold px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700">
                            <?= $metode_pembayaran; ?>
                        </span>
                    </div>
                </div>

                <div class="p-6 sm:p-8 text-center space-y-6">
                    <div>
                        <p class="text-xs text-slate-400 dark:text-slate-500 font-bold uppercase tracking-widest mb-3">Kode Pembayaran / Nomor Virtual Account</p>

                        <div class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl p-4 flex items-center justify-center max-w-sm mx-auto">
                            <span class="font-mono text-xl sm:text-2xl font-bold text-slate-800 dark:text-slate-100 tracking-wider pl-2" id="kodePembayaran"><?= $kode_pembayaran; ?></span>
                        </div>
                    </div>
                    
                    <!-- Form Konfirmasi Selesai Bayar -->
                    <form action="" method="POST">
                        <button type="submit" name="selesaikan_pembayaran" class="w-full bg-lime-600 hover:bg-lime-700 text-white py-3.5 rounded-xl font-bold text-sm cursor-pointer border-none">
                            <i class="fa-solid fa-circle-check mr-2"></i> Konfirmasi Pembayaran Selesai
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
    include '../bagian/bawah.php';
    exit();
}

header("Location: riwayat.php");
exit();
?>
