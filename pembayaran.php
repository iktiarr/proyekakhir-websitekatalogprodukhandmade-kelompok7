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
if (isset($_POST['upload_bukti'])) {
    $target_dir = "uploads/bukti/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_extension = pathinfo($_FILES["bukti"]["name"], PATHINFO_EXTENSION);
    $filename = "bukti_" . $id_pesanan . "_" . time() . "." . $file_extension;
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($_FILES["bukti"]["tmp_name"], $target_file)) {
        mysqli_query($conn, "UPDATE pesanan SET bukti_pembayaran = '$target_file', status = 'dibayar' WHERE id = $id_pesanan");
        $success = "Bukti pembayaran berhasil diunggah! Pesanan Anda sedang diproses.";
        header("Refresh: 3; url=riwayat.php");
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="py-16 lg:py-24 bg-slate-50 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-10 sm:mb-12">
            <span class="inline-block py-1.5 px-3.5 rounded-full bg-lime-100 text-lime-700 text-xs sm:text-sm font-bold tracking-wider mb-4 shadow-sm">
                SELESAIKAN TRANSAKSI
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-800">Pembayaran</h1>
            <p class="text-slate-500 mt-2 text-sm sm:text-base">Selesaikan pembayaran untuk memproses pesanan Anda.</p>
        </div>

        <?php if ($success): ?>
            <div class="bg-lime-50 text-lime-700 p-6 rounded-2xl mb-8 border border-lime-100 flex items-center shadow-sm transition-all">
                <i class="fa-solid fa-circle-check text-2xl mr-4"></i>
                <div>
                    <p class="font-bold text-sm sm:text-base"><?= $success; ?></p>
                    <p class="text-xs sm:text-sm text-lime-600 mt-1">Mengalihkan Anda ke riwayat pesanan dalam 3 detik...</p>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 gap-6 sm:gap-8">
            
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm">
                
                <div class="flex justify-between items-center mb-8 pb-6 border-b border-slate-100">
                    <div>
                        <p class="text-[10px] sm:text-xs text-slate-400 uppercase tracking-widest font-bold mb-1">Total Tagihan</p>
                        <p class="text-2xl sm:text-3xl font-extrabold text-lime-600">Rp <?= number_format($pesanan['total_harga'], 0, ',', '.'); ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] sm:text-xs text-slate-400 font-bold mb-1 uppercase tracking-widest">ID Pesanan</p>
                        <p class="font-mono text-slate-800 font-bold bg-slate-50 px-2 py-1 rounded">#HM-<?= str_pad($pesanan['id'], 5, '0', STR_PAD_LEFT); ?></p>
                    </div>
                </div>

                <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-6">Instruksi Transfer (<?= $pesanan['metode_pembayaran']; ?>)</h3>
                
                <?php if ($pesanan['metode_pembayaran'] === 'Transfer Bank'): ?>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" alt="BCA" class="h-6 w-16 object-contain mr-4">
                            <div>
                                <p class="text-xs text-slate-500 font-bold">BCA</p>
                                <p class="font-bold text-slate-800 text-lg tracking-wide">1234567890</p>
                                <p class="text-xs text-slate-500">a/n Handmade Katalog</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <img src="https://upload.wikimedia.org/wikipedia/id/f/fa/Bank_Mandiri_logo.svg" alt="Mandiri" class="h-6 w-16 object-contain mr-4">
                            <div>
                                <p class="text-xs text-slate-500 font-bold">MANDIRI</p>
                                <p class="font-bold text-slate-800 text-lg tracking-wide">0987654321</p>
                                <p class="text-xs text-slate-500">a/n Handmade Katalog</p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/8/86/Gopay_logo.svg" alt="Gopay" class="h-6 w-16 object-contain mr-4">
                            <div>
                                <p class="text-xs text-slate-500 font-bold">GOPAY / OVO</p>
                                <p class="font-bold text-slate-800 text-lg tracking-wide">0812-3456-7890</p>
                                <p class="text-xs text-slate-500">a/n Handmade Katalog</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-8 bg-lime-50 p-5 sm:p-6 rounded-xl border border-lime-100 flex items-start">
                    <i class="fa-solid fa-circle-info text-lime-600 mt-0.5 mr-3"></i>
                    <p class="text-xs sm:text-sm text-lime-800 leading-relaxed">
                        Pastikan nominal transfer sesuai hingga 3 digit terakhir jika ada, untuk mempercepat proses verifikasi pembayaran Anda.
                    </p>
                </div>
            </div>

            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm">
                <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-6">Konfirmasi Pembayaran</h3>
                
                <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div class="border-2 border-dashed border-slate-200 rounded-2xl p-8 sm:p-12 text-center hover:border-lime-400 hover:bg-slate-50 transition-colors bg-white group cursor-pointer relative overflow-hidden">
                        <input type="file" name="bukti" id="buktiInput" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept="image/*" onchange="previewFile()">
                        
                        <div id="uploadPlaceholder" class="pointer-events-none">
                            <i class="fa-solid fa-cloud-arrow-up text-4xl sm:text-5xl text-slate-300 group-hover:text-lime-500 transition-colors mb-4"></i>
                            <p class="text-slate-600 font-medium text-sm sm:text-base">Klik atau seret foto bukti transfer ke sini</p>
                            <p class="text-xs text-slate-400 mt-2">Format yang didukung: JPG, PNG, WEBP (Maks. 2MB)</p>
                        </div>
                        
                        <div id="previewContainer" class="hidden pointer-events-none">
                            <img id="previewImg" src="#" class="max-h-48 mx-auto rounded-xl shadow-sm mb-4 border border-slate-100">
                            <p class="text-lime-600 font-bold text-sm bg-lime-50 inline-block px-3 py-1 rounded-full"><i class="fa-solid fa-check mr-1"></i> File siap diunggah</p>
                            <p class="text-xs text-slate-400 mt-2">Klik lagi jika ingin mengganti foto</p>
                        </div>
                    </div>
                    
                    <button type="submit" name="upload_bukti" class="w-full bg-lime-600 text-white py-3.5 sm:py-4 rounded-xl font-bold text-base hover:bg-lime-700 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-lime-200/50">
                        Kirim Bukti Pembayaran
                    </button>
                </form>
            </div>
            
        </div>
    </div>
</div>

<script>
    function previewFile() {
        const preview = document.getElementById('previewImg');
        const file = document.getElementById('buktiInput').files[0];
        const reader = new FileReader();
        const placeholder = document.getElementById('uploadPlaceholder');
        const container = document.getElementById('previewContainer');

        reader.onloadend = function () {
            preview.src = reader.result;
            placeholder.classList.add('hidden');
            container.classList.remove('hidden');
        }

        if (file) {
            reader.readAsDataURL(file);
        } else {
            preview.src = "";
            placeholder.classList.remove('hidden');
            container.classList.add('hidden');
        }
    }
</script>

<?php include 'includes/footer.php'; ?>