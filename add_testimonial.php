<?php
include 'koneksi.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: masuk.php?redirect=add_testimonial.php");
    exit();
}

$error = '';
$success = '';

if (isset($_POST['submit_testimonial'])) {
    $id_pengguna = $_SESSION['user_id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $pekerjaan = mysqli_real_escape_string($conn, $_POST['pekerjaan']);
    $isi_ulasan = mysqli_real_escape_string($conn, $_POST['isi_ulasan']);
    $rating = intval($_POST['rating']);

    // Validasi
    if (empty($nama) || empty($isi_ulasan)) {
        $error = "Nama dan ulasan harus diisi!";
    } elseif (strlen($isi_ulasan) < 20) {
        $error = "Ulasan minimal 20 karakter!";
    } elseif (strlen($isi_ulasan) > 500) {
        $error = "Ulasan maksimal 500 karakter!";
    } else {
        $query = "INSERT INTO testimonial (id_pengguna, nama, pekerjaan, isi_ulasan, rating) 
                  VALUES ('$id_pengguna', '$nama', '$pekerjaan', '$isi_ulasan', '$rating')";
        
        if (mysqli_query($conn, $query)) {
            $success = "Testimonial Anda berhasil dikirim! Menunggu persetujuan admin.";
            // Redirect ke home setelah 3 detik
            header("Refresh: 2; url=index.php");
        } else {
            $error = "Gagal mengirim testimonial. " . mysqli_error($conn);
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="max-w-2xl mx-auto py-12 sm:py-16 lg:py-20 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-xl shadow-xl border border-lime-200 p-6 sm:p-8">
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Bagikan Pengalaman Anda</h1>
            <p class="text-gray-600 text-sm">Ulasan Anda membantu kami dan calon pelanggan lainnya untuk tahu kualitas produk kami.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-xl mb-5 border border-red-200 flex items-start gap-3 text-sm">
                <i class="fa-solid fa-circle-exclamation mt-1 flex-shrink-0"></i>
                <span><?= $error; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-lime-50 text-lime-700 p-3 rounded-xl mb-5 border border-lime-200 flex items-start gap-3 text-sm">
                <i class="fa-solid fa-check-circle mt-1 flex-shrink-0"></i>
                <span><?= $success; ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Nama Lengkap</label>
                <input type="text" name="nama" value="<?= $_SESSION['nama'] ?? ''; ?>" required 
                    class="w-full px-4 py-3 rounded-xl border border-lime-200 focus:ring-2 focus:ring-lime-500 focus:border-lime-500 outline-none transition" 
                    placeholder="Masukkan nama Anda">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Pekerjaan/Status</label>
                <input type="text" name="pekerjaan" 
                    class="w-full px-4 py-3 rounded-xl border border-lime-200 focus:ring-2 focus:ring-lime-500 focus:border-lime-500 outline-none transition" 
                    placeholder="Contoh: Pelanggan Setia, Pengusaha Muda, dll">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Rating</label>
                <div class="flex gap-2">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <label class="flex items-center gap-2 cursor-pointer text-sm">
                            <input type="radio" name="rating" value="<?= $i; ?>" <?= $i === 5 ? 'checked' : ''; ?> class="w-4 h-4 text-lime-600">
                            <span class="text-base">
                                <?php for ($j = 1; $j <= $i; $j++): ?>
                                    <i class="fa-solid fa-star text-lime-500"></i>
                                <?php endfor; ?>
                            </span>
                        </label>
                    <?php endfor; ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Ulasan Anda</label>
                <textarea name="isi_ulasan" rows="6" required 
                    class="w-full px-4 py-3 rounded-xl border border-lime-200 focus:ring-2 focus:ring-lime-500 focus:border-lime-500 outline-none transition resize-none" 
                    placeholder="Ceritakan pengalaman Anda menggunakan produk kami... (minimal 20 karakter)"></textarea>
                <p class="text-xs text-gray-500 mt-1">Minimal 20 karakter, maksimal 500 karakter</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="submit" name="submit_testimonial" 
                    class="flex-1 bg-gradient-to-r from-lime-600 to-lime-500 text-white px-6 py-3 rounded-xl font-bold hover:from-lime-700 hover:to-lime-600 transition shadow-lg">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Kirim Ulasan
                </button>
                <a href="index.php" 
                    class="flex-1 bg-gray-100 text-gray-900 px-6 py-3 rounded-xl font-bold hover:bg-gray-200 transition text-center">
                    Batal
                </a>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-200">
            <p class="text-sm text-gray-600 text-center">
                <i class="fa-solid fa-info-circle text-lime-600 mr-2"></i>
                Ulasan Anda akan ditampilkan setelah disetujui oleh admin kami.
            </p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
