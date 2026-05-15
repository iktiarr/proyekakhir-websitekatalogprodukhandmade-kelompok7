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

<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-gray-900">Pembayaran</h1>
            <p class="text-gray-500 mt-2">Selesaikan pembayaran untuk memproses pesanan Anda.</p>
        </div>

        <?php if ($success): ?>
            <div class="bg-green-50 text-green-600 p-6 rounded-3xl mb-8 border border-green-100 flex items-center shadow-sm">
                <i class="fa-solid fa-circle-check text-2xl mr-4"></i>
                <div>
                    <p class="font-bold"><?= $success; ?></p>
                    <p class="text-sm opacity-80">Mengalihkan Anda ke riwayat pesanan dalam 3 detik...</p>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 gap-8">
            <!-- Instruksi Pembayaran -->
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <div class="flex justify-between items-center mb-8 pb-6 border-b border-gray-50">
                    <div>
                        <p class="text-sm text-gray-400 uppercase tracking-widest font-bold">Total Tagihan</p>
                        <p class="text-3xl font-extrabold text-amber-600">Rp <?= number_format($pesanan['total_harga'], 0, ',', '.'); ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-400 font-bold">ID Pesanan</p>
                        <p class="font-mono text-gray-900 font-bold">#HM-<?= str_pad($pesanan['id'], 5, '0', STR_PAD_LEFT); ?></p>
                    </div>
                </div>

                <h3 class="text-xl font-bold text-gray-900 mb-6">Instruksi Transfer (<?= $pesanan['metode_pembayaran']; ?>)</h3>
                
                <?php if ($pesanan['metode_pembayaran'] === 'Transfer Bank'): ?>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/5/5c/Bank_Central_Asia.svg" alt="BCA" class="h-6 mr-4">
                            <div>
                                <p class="text-xs text-gray-400 font-bold">BCA</p>
                                <p class="font-bold text-gray-900">1234567890</p>
                                <p class="text-xs text-gray-500">a/n Handmade Katalog</p>
                            </div>
                        </div>
                        <div class="flex items-center p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <img src="https://upload.wikimedia.org/wikipedia/id/f/fa/Bank_Mandiri_logo.svg" alt="Mandiri" class="h-6 mr-4">
                            <div>
                                <p class="text-xs text-gray-400 font-bold">MANDIRI</p>
                                <p class="font-bold text-gray-900">0987654321</p>
                                <p class="text-xs text-gray-500">a/n Handmade Katalog</p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-gray-50 rounded-2xl border border-gray-100">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/8/86/Gopay_logo.svg" alt="Gopay" class="h-6 mr-4">
                            <div>
                                <p class="text-xs text-gray-400 font-bold">GOPAY / OVO</p>
                                <p class="font-bold text-gray-900">0812-3456-7890</p>
                                <p class="text-xs text-gray-500">a/n Handmade Katalog</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="mt-8 bg-amber-50 p-6 rounded-2xl border border-amber-100">
                    <p class="text-sm text-amber-800 leading-relaxed italic">
                        <i class="fa-solid fa-circle-info mr-2"></i> Pastikan nominal transfer sesuai hingga 3 digit terakhir jika ada, untuk mempercepat proses verifikasi.
                    </p>
                </div>
            </div>

            <!-- Upload Bukti -->
            <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Konfirmasi Pembayaran</h3>
                <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div class="border-2 border-dashed border-gray-100 rounded-[2rem] p-12 text-center hover:border-amber-200 transition bg-gray-50/50 group">
                        <input type="file" name="bukti" id="buktiInput" required class="hidden" accept="image/*" onchange="previewFile()">
                        <label for="buktiInput" class="cursor-pointer">
                            <div id="uploadPlaceholder">
                                <i class="fa-solid fa-cloud-arrow-up text-5xl text-gray-200 group-hover:text-amber-300 transition mb-4"></i>
                                <p class="text-gray-500 font-medium">Klik untuk pilih foto bukti transfer</p>
                                <p class="text-xs text-gray-400 mt-2">Format: JPG, PNG, WEBP (Maks. 2MB)</p>
                            </div>
                            <div id="previewContainer" class="hidden">
                                <img id="previewImg" src="#" class="max-h-48 mx-auto rounded-xl shadow-lg mb-4">
                                <p class="text-amber-600 font-bold text-sm">File terpilih!</p>
                            </div>
                        </label>
                    </div>
                    <button type="submit" name="upload_bukti" class="w-full bg-amber-600 text-white py-4 rounded-2xl font-bold text-lg hover:bg-amber-700 transition shadow-xl shadow-amber-200">
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
