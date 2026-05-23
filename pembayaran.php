<?php
include 'koneksi.php';


if (!isset($_GET['id']) || !isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$id_pesanan = (int)$_GET['id'];
$id_pengguna = $_SESSION['user_id'];


$query = mysqli_query($conn, "SELECT * FROM pesanan WHERE id = $id_pesanan AND id_pengguna = $id_pengguna");
$pesanan = mysqli_fetch_assoc($query);

if (!$pesanan) {
    header("Location: index.php");
    exit();
}

$success = '';
$error = '';
$metode = $pesanan['metode_pembayaran'];


$kode_pembayaran = '';
if ($metode === 'BCA Virtual Account') {
    $kode_pembayaran = '80012' . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT) . '932';
} elseif ($metode === 'Mandiri Virtual Account') {
    $kode_pembayaran = '89022' . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT) . '854';
} elseif ($metode === 'GoPay') {
    $kode_pembayaran = 'GP-9482' . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT);
} elseif ($metode === 'Dana') {
    $kode_pembayaran = 'DN-1192' . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT);
} elseif ($metode === 'Alfamart') {
    $kode_pembayaran = 'ALFA' . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT) . '83';
} elseif ($metode === 'Indomaret') {
    $kode_pembayaran = 'INDO' . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT) . '59';
} else {
    $kode_pembayaran = 'PAY' . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT);
}


if (isset($_POST['selesaikan_pembayaran'])) {
    
    $query_update = "UPDATE pesanan SET bukti_pembayaran = '$kode_pembayaran', status = 'dibayar' WHERE id = $id_pesanan";
    if (mysqli_query($conn, $query_update)) {
        $success = "Pembayaran Berhasil! Pesanan Anda telah lunas dan siap dikirim.";
        header("Refresh: 2; url=riwayat.php");
    } else {
        $error = "Terjadi kesalahan saat memproses pembayaran.";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="py-16 lg:py-24 bg-slate-50 min-h-screen">
    <div class="max-w-xl mx-auto px-4">
        
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-slate-800">Selesaikan Pembayaran</h1>
            <p class="text-slate-500 mt-2 text-sm">Gunakan kode pembayaran unik di bawah untuk menyelesaikan pesanan Anda.</p>
        </div>

        <?php if ($success): ?>
            <div class="bg-lime-50 text-lime-700 p-5 rounded-2xl mb-6 border border-lime-100 flex items-center shadow-sm">
                <i class="fa-solid fa-circle-check text-2xl mr-3 flex-shrink-0"></i>
                <div>
                    <p class="font-bold text-sm"><?= $success; ?></p>
                    <p class="text-xs text-lime-600 mt-1">Mengalihkan ke riwayat pesanan dalam 2 detik...</p>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 border border-red-100 flex items-center shadow-sm">
                <i class="fa-solid fa-circle-exclamation mr-3 flex-shrink-0"></i> <?= $error; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <!-- Header Kartu Pembayaran -->
            <div class="p-6 bg-slate-50/50 border-b border-slate-100 flex justify-between items-center">
                <div>
                    <p class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Total Pembayaran</p>
                    <p class="text-2xl font-extrabold text-lime-600">Rp <?= number_format($pesanan['total_harga'], 0, ',', '.'); ?></p>
                </div>
                <div class="text-right">
                    <span class="inline-block bg-slate-100 text-slate-800 text-xs font-bold px-2.5 py-1 rounded-md border border-slate-200 mt-1">
                        <?= $metode; ?>
                    </span>
                </div>
            </div>

            <!-- Bagian Kode Pembayaran Unik -->
            <div class="p-6 sm:p-8 text-center space-y-6">
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mb-2">Kode Pembayaran / Nomor Virtual Account</p>
                    
                    <!-- Box Kode Unik -->
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex items-center justify-center max-w-sm mx-auto group">
                        <span class="font-mono text-xl sm:text-2xl font-extrabold text-slate-800 tracking-wider pl-2" id="payCode"><?= $kode_pembayaran; ?></span>
                    </div>
                </div>

                <!-- Form Konfirmasi Selesai Instan -->
                <form action="" method="POST">
                    <button type="submit" name="selesaikan_pembayaran" class="w-full bg-lime-600 hover:bg-lime-700 text-white py-3.5 rounded-xl font-bold text-sm hover:shadow-lg hover:shadow-lime-200/40 transition-all duration-300 hover:-translate-y-0.5 cursor-pointer">
                        <i class="fa-solid fa-circle-check mr-1.5"></i> Selesaikan Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>