<?php
$awalan = "../";
include '../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../masuk.php");
    exit();
}

$id_pengguna = $_SESSION['user_id'];
$berhasil = '';
$galat = '';

if (isset($_GET['id'])) {
    $id_pesanan = (int)$_GET['id'];
    
    $kueri_pesanan = mysqli_query($koneksi, "SELECT * FROM pesanan WHERE id = $id_pesanan AND id_pengguna = $id_pengguna");
    $data_pesanan = mysqli_fetch_assoc($kueri_pesanan);
    
    if (!$data_pesanan) {
        header("Location: riwayat.php");
        exit();
    }
    
    if ($data_pesanan['status'] !== 'menunggu') {
        header("Location: riwayat.php");
        exit();
    }
    
    $metode_pembayaran = $data_pesanan['metode_pembayaran'];
    $total_pembayaran = $data_pesanan['total_harga'];
    
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
    
    if (isset($_POST['selesaikan_pembayaran'])) {
        $kueri_perbarui = "UPDATE pesanan SET bukti_pembayaran = '$kode_pembayaran', status = 'dibayar' WHERE id = $id_pesanan";
        if (mysqli_query($koneksi, $kueri_perbarui)) {
            $berhasil = "Pembayaran Berhasil! Pesanan Anda telah lunas dan siap dikirim.";
            $nama_pembeli = mysqli_real_escape_string($koneksi, $_SESSION['nama']);
            $tag = "#HM-" . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT);
            mysqli_query($koneksi, "INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan) VALUES ($id_pengguna, '$nama_pembeli', 'pesanan', 'dibayar', 'Melakukan konfirmasi pembayaran untuk $tag')");
            header("Refresh: 2; url=riwayat.php");
        } else {
            $galat = "Terjadi kesalahan saat memproses pembayaran.";
        }
    }
    
    include '../bagian/atas.php';
    ?>
    <div class="py-16 bg-slate-50 dark:bg-slate-950 min-h-[80vh] transition-colors duration-300">
        <div class="max-w-xl mx-auto px-4">
            
            <div class="text-center mb-8">
                <h1 class="text-3xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Selesaikan Pembayaran</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">Gunakan kode pembayaran unik di bawah untuk meresmikan pesanan kerajinan Anda.</p>
            </div>

            <?php if ($berhasil): ?>
                <div class="bg-lime-50 dark:bg-lime-950/20 text-lime-755 dark:text-lime-400 p-5 rounded-xl mb-6 border border-lime-200 dark:border-lime-900/30 flex items-center shadow-sm">
                    <i class="fa-solid fa-circle-check text-2xl mr-3 flex-shrink-0 text-lime-600"></i>
                    <div>
                        <p class="font-bold text-sm"><?= $berhasil; ?></p>
                        <p class="text-xs text-lime-650 dark:text-lime-500 mt-0.5">Mengarahkan Anda ke Riwayat Transaksi...</p>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($galat): ?>
                <div class="bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 p-4 rounded-xl mb-6 border border-red-200 dark:border-red-900/30 flex items-center shadow-sm">
                    <i class="fa-solid fa-circle-exclamation mr-3 flex-shrink-0"></i> <?= $galat; ?>
                </div>
            <?php endif; ?>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200/60 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 bg-slate-50/50 dark:bg-slate-950/40 border-b border-slate-200/60 dark:border-slate-800 flex justify-between items-center">
                    <div>
                        <p class="text-[10px] text-slate-400 dark:text-slate-550 uppercase tracking-widest font-extrabold">Total Pembayaran</p>
                        <p class="text-2xl font-extrabold text-lime-600 dark:text-lime-400">Rp <?= number_format($total_pembayaran, 0, ',', '.'); ?></p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block bg-slate-100 dark:bg-slate-800 text-slate-850 dark:text-slate-200 text-xs font-bold px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm">
                            <?= $metode_pembayaran; ?>
                        </span>
                    </div>
                </div>

                <div class="p-6 sm:p-8 text-center space-y-6">
                    <div>
                        <p class="text-xs text-slate-400 dark:text-slate-550 font-bold uppercase tracking-widest mb-3">Kode Pembayaran / Nomor Virtual Account</p>

                        <div class="bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 rounded-xl p-4 flex items-center justify-center max-w-sm mx-auto group">
                            <span class="font-mono text-xl sm:text-2xl font-extrabold text-slate-855 dark:text-slate-100 tracking-wider pl-2" id="kodePembayaran"><?= $kode_pembayaran; ?></span>
                        </div>
                    </div>
                    
                    <form action="" method="POST">
                        <button type="submit" name="selesaikan_pembayaran" class="w-full bg-lime-600 hover:bg-lime-700 text-white py-3.5 rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-lime-200/40 transition-all duration-300 cursor-pointer border-none">
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
