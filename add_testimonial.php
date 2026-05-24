<?php
include 'koneksi.php';


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
            
            header("Refresh: 2; url=index.php");
        } else {
            $error = "Gagal mengirim testimonial. " . mysqli_error($conn);
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="min-h-[70vh] bg-slate-50 dark:bg-slate-950 flex items-center justify-center py-6 px-4 sm:px-6 transition-colors duration-300">
    
    <div class="max-w-xl w-full bg-white dark:bg-slate-900 p-5 sm:p-6 rounded-xl shadow-sm border border-slate-100 dark:border-slate-800">
        
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-10 h-10 bg-lime-50 dark:bg-lime-950/40 rounded-xl mb-2 text-lime-600 dark:text-lime-400 shadow-sm border border-lime-100 dark:border-lime-900/30">
                <i class="fa-solid fa-pen-nib text-lg"></i>
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100">Bagikan Pengalaman Anda</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 text-xs sm:text-sm">Ulasan Anda sangat berarti bagi kami dan calon pelanggan lainnya.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 p-3 rounded-lg mb-4 text-xs border border-red-100 dark:border-red-900/30 flex items-start gap-2.5">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i> 
                <span><?= $error; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-lime-50 dark:bg-lime-950/20 text-lime-700 dark:text-lime-400 p-3 rounded-lg mb-4 text-xs border border-lime-100 dark:border-lime-900/30 flex flex-col sm:flex-row sm:items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-base sm:mt-0 mt-0.5"></i> 
                <div>
                    <p class="font-bold"><?= $success; ?></p>
                    <p class="text-[11px] text-lime-650 dark:text-lime-450 mt-0.5">Mengalihkan ke beranda dalam 2 detik...</p>
                </div>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama" value="<?= $_SESSION['nama'] ?? ''; ?>" required 
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-800 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800 dark:text-slate-200 placeholder-slate-400 text-sm" 
                        placeholder="Masukkan nama Anda">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Pekerjaan/Status <span class="text-slate-400 dark:text-slate-500 font-normal">(Opsional)</span></label>
                    <input type="text" name="pekerjaan" 
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-800 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800 dark:text-slate-200 placeholder-slate-400 text-sm" 
                        placeholder="Contoh: Pegawai Swasta, dll">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Penilaian Anda</label>
                <div class="flex items-center gap-1 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 p-2 rounded-lg w-fit star-rating-container">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" id="star<?= $i; ?>" name="rating" value="<?= $i; ?>" class="peer hidden" <?= $i === 5 ? 'checked' : ''; ?>>
                        <label for="star<?= $i; ?>" class="cursor-pointer text-slate-300 dark:text-slate-650 peer-checked:text-lime-500 hover:text-lime-400 transition-colors duration-200 text-xl">
                            <i class="fa-solid fa-star"></i>
                        </label>
                    <?php endfor; ?>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Tulis Ulasan Anda</label>
                <textarea name="isi_ulasan" rows="3" required 
                    class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 rounded-lg border border-slate-200 dark:border-slate-800 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800 dark:text-slate-200 placeholder-slate-400 resize-none text-sm" 
                    placeholder="Ceritakan pengalaman Anda berbelanja dan kualitas produk kami..."></textarea>
                <div class="flex justify-between items-center mt-1">
                    <p class="text-[11px] text-slate-500 dark:text-slate-400"><i class="fa-solid fa-circle-info mr-1"></i> Minimal 20 karakter, maksimal 500 karakter</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-3 border-t border-slate-100 dark:border-slate-800 mt-6">
                <button type="submit" name="submit_testimonial" 
                    class="flex-1 bg-lime-600 text-white px-5 py-2.5 rounded-lg font-bold hover:bg-lime-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-lime-200/50 transition-all duration-300 flex items-center justify-center text-sm border-none cursor-pointer">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Kirim Ulasan
                </button>
                <a href="index.php" 
                    class="sm:w-1/4 bg-white dark:bg-slate-850 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-5 py-2.5 rounded-lg font-bold hover:bg-slate-50 dark:hover:bg-slate-750 hover:border-slate-300 dark:hover:border-slate-650 hover:text-slate-900 dark:hover:text-slate-100 transition-all duration-300 text-center flex items-center justify-center text-sm">
                    Batal
                </a>
            </div>
            
        </form>
    </div>
</div>

<style>
    .star-rating-container {
        display: inline-flex;
        flex-direction: row-reverse;
    }
    
    .star-rating-container label:hover,
    .star-rating-container label:hover ~ label {
        color: #a3e635;
    }

    .star-rating-container input:checked ~ label {
        color: #84cc16;
    }
</style>

<?php include 'includes/footer.php'; ?>