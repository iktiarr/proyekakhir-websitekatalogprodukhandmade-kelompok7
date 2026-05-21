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

<div class="min-h-[85vh] bg-slate-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-2xl w-full bg-white p-8 sm:p-10 rounded-2xl shadow-sm border border-slate-100">
        
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-lime-50 rounded-2xl mb-4 text-lime-600 shadow-sm border border-lime-100">
                <i class="fa-solid fa-pen-nib text-xl"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800">Bagikan Pengalaman Anda</h1>
            <p class="text-slate-500 mt-2 text-sm sm:text-base">Ulasan Anda sangat berarti bagi kami dan calon pelanggan lainnya.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm border border-red-100 flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i> 
                <span><?= $error; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-lime-50 text-lime-700 p-4 rounded-xl mb-6 text-sm border border-lime-100 flex flex-col sm:flex-row sm:items-center gap-3">
                <i class="fa-solid fa-circle-check text-lg sm:mt-0 mt-0.5"></i> 
                <div>
                    <p class="font-bold"><?= $success; ?></p>
                    <p class="text-xs text-lime-600 mt-1">Mengalihkan ke beranda dalam 2 detik...</p>
                </div>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="nama" value="<?= $_SESSION['nama'] ?? ''; ?>" required 
                        class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800 placeholder-slate-400" 
                        placeholder="Masukkan nama Anda">
                </div>
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Pekerjaan/Status <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <input type="text" name="pekerjaan" 
                        class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800 placeholder-slate-400" 
                        placeholder="Contoh: Pegawai Swasta, dll">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Penilaian Anda</label>
                <div class="flex items-center gap-1 bg-slate-50 border border-slate-200 p-3 rounded-xl w-fit star-rating-container">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" id="star<?= $i; ?>" name="rating" value="<?= $i; ?>" class="peer hidden" <?= $i === 5 ? 'checked' : ''; ?>>
                        <label for="star<?= $i; ?>" class="cursor-pointer text-slate-300 peer-checked:text-lime-500 hover:text-lime-400 transition-colors duration-200 text-2xl">
                            <i class="fa-solid fa-star"></i>
                        </label>
                    <?php endfor; ?>
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">Tulis Ulasan Anda</label>
                <textarea name="isi_ulasan" rows="5" required 
                    class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800 placeholder-slate-400 resize-none" 
                    placeholder="Ceritakan pengalaman Anda berbelanja dan kualitas produk kami..."></textarea>
                <div class="flex justify-between items-center mt-1.5">
                    <p class="text-xs text-slate-500"><i class="fa-solid fa-circle-info mr-1"></i> Minimal 20 karakter, maksimal 500 karakter</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-slate-100 mt-8">
                <button type="submit" name="submit_testimonial" 
                    class="flex-1 bg-lime-600 text-white px-6 py-3.5 rounded-xl font-bold hover:bg-lime-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-lime-200/50 transition-all duration-300 flex items-center justify-center">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Kirim Ulasan
                </button>
                <a href="index.php" 
                    class="sm:w-1/3 bg-white text-slate-700 border border-slate-200 px-6 py-3.5 rounded-xl font-bold hover:bg-slate-50 hover:border-slate-300 hover:text-slate-900 transition-all duration-300 text-center flex items-center justify-center">
                    Batal
                </a>
            </div>
            
        </form>

        <div class="mt-8 pt-6">
            <p class="text-[11px] sm:text-xs text-slate-400 text-center bg-slate-50 p-3 rounded-lg border border-slate-100">
                <i class="fa-solid fa-shield-check text-lime-600 mr-1.5"></i>
                Ulasan Anda akan dimoderasi (diperiksa) terlebih dahulu oleh tim kami sebelum ditampilkan di halaman utama demi kenyamanan bersama.
            </p>
        </div>
        
    </div>
</div>

<style>
    .star-rating-container {
        display: inline-flex;
        flex-direction: row-reverse; /* Balik urutan agar peer selector css berfungsi maju */
    }
    
    /* Ketika sebuah bintang di-hover, warnai bintang itu dan semua bintang di sebelah "kiri" nya (yang dalam html ada di sebelah kanan karena di reverse) */
    .star-rating-container label:hover,
    .star-rating-container label:hover ~ label {
        color: #a3e635; /* lime-400 */
    }

    /* Ketika sebuah bintang dipilih (checked), warnai bintang itu dan semua bintang sebelumnya */
    .star-rating-container input:checked ~ label {
        color: #84cc16; /* lime-500 */
    }
</style>

<?php include 'includes/footer.php'; ?>