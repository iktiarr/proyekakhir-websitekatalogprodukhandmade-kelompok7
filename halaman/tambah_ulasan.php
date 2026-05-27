<?php
$awalan = "../";
include '../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../masuk.php");
    exit();
}

$galat = '';
$berhasil = '';

if (isset($_POST['submit_testimonial'])) {
    $id_pengguna = $_SESSION['user_id'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $pekerjaan = mysqli_real_escape_string($koneksi, $_POST['pekerjaan']);
    $isi_ulasan = mysqli_real_escape_string($koneksi, $_POST['isi_ulasan']);
    $rating = intval($_POST['rating']);

    if (empty($nama) || empty($isi_ulasan)) {
        $galat = "Nama dan ulasan wajib diisi!";
    } elseif (strlen($isi_ulasan) < 20) {
        $galat = "Ulasan minimal 20 karakter!";
    } elseif (strlen($isi_ulasan) > 500) {
        $galat = "Ulasan maksimal 500 karakter!";
    } else {
        $kueri_tambah = "INSERT INTO testimonial (id_pengguna, nama, pekerjaan, isi_ulasan, rating) 
                  VALUES ('$id_pengguna', '$nama', '$pekerjaan', '$isi_ulasan', '$rating')";
        
        if (mysqli_query($koneksi, $kueri_tambah)) {
            $berhasil = "Testimoni Anda berhasil dikirim! Menunggu verifikasi admin.";
            $nama_pengulas = mysqli_real_escape_string($koneksi, $nama);
            mysqli_query($koneksi, "INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan) VALUES ($id_pengguna, '$nama_pengulas', 'testimoni', 'tambah', 'Menulis ulasan baru (menunggu persetujuan)')");
            header("Refresh: 2; url=../index.php");
        } else {
            $galat = "Gagal mengirim testimoni. " . mysqli_error($koneksi);
        }
    }
}
?>

<?php include '../bagian/atas.php'; ?>

<div class="min-h-[80vh] bg-slate-55 dark:bg-slate-950 flex items-center justify-center py-10 px-4 sm:px-6 transition-colors duration-300">
    
    <div class="max-w-xl w-full bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-xl shadow-sm border border-slate-200/60 dark:border-slate-800">
        
        <div class="text-center mb-7">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-lime-50 dark:bg-lime-950/40 rounded-2xl mb-3 text-lime-600 dark:text-lime-400 shadow-sm border border-lime-100 dark:border-lime-900/30">
                <i class="fa-solid fa-pen-nib text-lg"></i>
            </div>
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Bagikan Ulasan Anda</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 text-xs sm:text-sm">Ulasan Anda sangat berarti bagi kami dan calon kolektor produk HandMadura lainnya.</p>
        </div>

        <?php if ($galat): ?>
            <div class="bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 p-3.5 rounded-xl mb-5 text-xs border border-red-150 dark:border-red-900/30 flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation mt-0.5 text-sm flex-shrink-0"></i> 
                <span><?= $galat; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($berhasil): ?>
            <div class="bg-lime-50 dark:bg-lime-950/20 text-lime-755 dark:text-lime-400 p-3.5 rounded-xl mb-5 text-xs border border-lime-200 dark:border-lime-900/30 flex flex-col sm:flex-row sm:items-center gap-3">
                <i class="fa-solid fa-circle-check text-lg sm:mt-0 mt-0.5 text-lime-600"></i> 
                <div>
                    <p class="font-bold"><?= $berhasil; ?></p>
                    <p class="text-[11px] text-lime-650 dark:text-lime-500 mt-0.5">Mengalihkan kembali ke beranda dalam 2 detik...</p>
                </div>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-5">
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-350 mb-1.5 uppercase tracking-wider">Nama Lengkap</label>
                    <input type="text" name="nama" value="<?= $_SESSION['nama'] ?? ''; ?>" required 
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-200 dark:border-slate-800 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800 dark:text-slate-200 placeholder-slate-400 text-sm" 
                        placeholder="Masukkan nama Anda">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-350 mb-1.5 uppercase tracking-wider">Pekerjaan / Status</label>
                    <input type="text" name="pekerjaan" 
                        class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-200 dark:border-slate-800 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800 dark:text-slate-200 placeholder-slate-400 text-sm" 
                        placeholder="Contoh: Kolektor Seni, Pegawai...">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-350 mb-2 uppercase tracking-wider">Rating Penilaian Anda</label>
                <div class="flex items-center gap-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 p-2.5 rounded-xl w-fit wadah-peringkat-bintang">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <input type="radio" id="star<?= $i; ?>" name="rating" value="<?= $i; ?>" class="peer hidden" <?= $i === 5 ? 'checked' : ''; ?>>
                        <label for="star<?= $i; ?>" class="cursor-pointer text-slate-300 dark:text-slate-650 peer-checked:text-lime-500 hover:text-lime-400 transition-colors duration-250 text-xl">
                            <i class="fa-solid fa-star"></i>
                        </label>
                    <?php endfor; ?>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-350 mb-2 uppercase tracking-wider">Ulasan Pengalaman</label>
                <textarea name="isi_ulasan" rows="4" required 
                    class="w-full px-3.5 py-2.5 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-200 dark:border-slate-800 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800 dark:text-slate-200 placeholder-slate-400 resize-none text-sm leading-relaxed" 
                    placeholder="Ceritakan kepuasan Anda terhadap detail produk HandMadura kami..."></textarea>
                <div class="flex justify-between items-center mt-1">
                    <p class="text-[10px] text-slate-400 dark:text-slate-550"><i class="fa-solid fa-circle-info mr-1"></i> Minimal 20 karakter, maksimal 500 karakter.</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-3 border-t border-slate-100 dark:border-slate-800 mt-6">
                <button type="submit" name="submit_testimonial" 
                    class="flex-grow bg-lime-600 text-white px-5 py-3 rounded-xl font-bold hover:bg-lime-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-lime-200/40 transition-all duration-300 flex items-center justify-center text-sm border-none cursor-pointer">
                    <i class="fa-solid fa-paper-plane mr-2"></i> Kirim Ulasan Autentik
                </button>
                <a href="../index.php" 
                    class="sm:w-1/4 bg-white dark:bg-slate-850 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-5 py-3 rounded-xl font-bold hover:bg-slate-100 dark:hover:bg-slate-750 hover:border-slate-300 hover:text-slate-950 transition-all duration-300 text-center flex items-center justify-center text-sm">
                    Batal
                </a>
            </div>
            
        </form>
    </div>
</div>

<style>
    .wadah-peringkat-bintang {
        display: inline-flex;
        flex-direction: row-reverse;
    }
    
    .wadah-peringkat-bintang label:hover,
    .wadah-peringkat-bintang label:hover ~ label {
        color: #a3e635;
    }

    .wadah-peringkat-bintang input:checked ~ label {
        color: #84cc16;
    }
</style>

<?php include '../bagian/bawah.php'; ?>
