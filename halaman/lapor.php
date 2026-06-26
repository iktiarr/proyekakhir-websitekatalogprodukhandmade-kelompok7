<?php
/**
 * Halaman Laporkan Kendala
 * Berfungsi untuk mengirim laporan masalah (seperti kegagalan sistem setelah pembayaran lunas)
 * dan mengunggah berkas bukti berformat gambar atau PDF.
 */
// Aktifkan pelaporan error agar bisa dideteksi jika terjadi kesalahan pada hosting
error_reporting(E_ALL);
ini_set('display_errors', 1);

$awalan = "../";
include '../koneksi.php';

// Proteksi halaman: pastikan pengguna sudah masuk
if (!isset($_SESSION['user']['id'])) {
    header("Location: ../masuk.php");
    exit();
}

$id_pengguna = $_SESSION['user']['id'];
$galat = '';
$sukses = '';

// Ambil daftar pesanan milik pengguna untuk opsi dropdown
$res_pesanan = kueri("SELECT id, total_harga, status, tanggal_pesanan FROM pesanan WHERE id_pengguna = ? ORDER BY tanggal_pesanan DESC", [$id_pengguna]);
$daftar_pesanan = [];
if ($res_pesanan) {
    while ($row = mysqli_fetch_assoc($res_pesanan)) {
        $daftar_pesanan[] = $row;
    }
} else {
    $galat = "Gagal memuat data pesanan: " . mysqli_error($koneksi);
}

// Tangkap id_pesanan dari query parameter jika ada
$id_pesanan_default = isset($_GET['id_pesanan']) ? (int)$_GET['id_pesanan'] : 0;

// Proses form jika dikirim
if (isset($_POST['kirim_laporan'])) {
    $id_pesanan = isset($_POST['id_pesanan']) && $_POST['id_pesanan'] !== '' ? (int)$_POST['id_pesanan'] : null;
    $tipe_laporan = trim($_POST['tipe_laporan']);
    $deskripsi = trim($_POST['deskripsi']);
    
    // Validasi input
    if (empty($deskripsi) || empty($tipe_laporan)) {
        $galat = "Tipe kendala dan deskripsi tidak boleh kosong!";
    } elseif (!isset($_FILES['file_laporan']) || $_FILES['file_laporan']['error'] === UPLOAD_ERR_NO_FILE) {
        $galat = "Anda wajib melampirkan berkas bukti (PDF atau Foto)!";
    } else {
        $file = $_FILES['file_laporan'];
        $nama_file = $file['name'];
        $tmp_file = $file['tmp_name'];
        $ukuran_file = $file['size'];
        $error_file = $file['error'];
        
        // Cek upload error dari PHP
        if ($error_file !== UPLOAD_ERR_OK) {
            if ($error_file === UPLOAD_ERR_INI_SIZE || $error_file === UPLOAD_ERR_FORM_SIZE) {
                $galat = "Ukuran berkas terlalu besar! Melebihi batas maksimal yang diizinkan oleh server.";
            } elseif ($error_file === UPLOAD_ERR_PARTIAL) {
                $galat = "Berkas hanya terunggah sebagian. Silakan coba lagi.";
            } elseif ($error_file === UPLOAD_ERR_NO_TMP_DIR) {
                $galat = "Server hosting tidak memiliki folder penyimpanan sementara. Hubungi admin hosting.";
            } elseif ($error_file === UPLOAD_ERR_CANT_WRITE) {
                $galat = "Gagal menulis berkas ke penyimpanan server (masalah izin/kuota penuh).";
            } else {
                $galat = "Terjadi kesalahan saat mengunggah berkas (Kode Error: $error_file).";
            }
        } else {
            // Cek ekstensi file
            $ekstensi_diperbolehkan = ['pdf', 'png', 'jpg', 'jpeg'];
            $ekstensi_file = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
            
            if (!in_array($ekstensi_file, $ekstensi_diperbolehkan)) {
                $galat = "Format berkas tidak valid! Hanya diperbolehkan PDF, PNG, JPG, atau JPEG.";
            } elseif ($ukuran_file > 5 * 1024 * 1024) { // 5MB limit
                $galat = "Ukuran berkas terlalu besar! Maksimal ukuran adalah 5MB.";
            } else {
                // Gunakan path absolut yang aman untuk InfinityFree
                $direktori_tujuan = dirname(__DIR__) . '/uploads/laporan/';
                
                // Pastikan direktori ada dan dapat ditulisi
                if (!is_dir($direktori_tujuan)) {
                    if (!@mkdir($direktori_tujuan, 0777, true)) {
                        $galat = "Gagal membuat folder penyimpanan di server. Silakan buat folder 'uploads/laporan' secara manual melalui FTP FileZilla/cPanel.";
                    }
                }
                
                if (empty($galat)) {
                    if (!is_writable($direktori_tujuan)) {
                        $galat = "Folder penyimpanan di server tidak dapat ditulisi (not writable). Hubungi admin hosting atau atur chmod folder 'uploads/laporan' ke 777.";
                    } else {
                        // Generate nama file unik
                        $nama_file_baru = 'laporan_' . $id_pengguna . '_' . time() . '_' . rand(100, 999) . '.' . $ekstensi_file;
                        $jalur_simpan = $direktori_tujuan . $nama_file_baru;
                        $jalur_db = 'uploads/laporan/' . $nama_file_baru;
                        
                        if (move_uploaded_file($tmp_file, $jalur_simpan)) {
                            // Simpan ke database
                            $kueri_tambah = kueri("
                                INSERT INTO laporan_kendala (id_pengguna, id_pesanan, tipe_laporan, deskripsi, file_laporan, status) 
                                VALUES (?, ?, ?, ?, ?, 'pending')
                            ", [$id_pengguna, $id_pesanan, $tipe_laporan, $deskripsi, $jalur_db]);
                            
                            if ($kueri_tambah) {
                                $sukses = "Laporan kendala Anda berhasil dikirim! Tim admin akan segera meninjau berkas Anda.";
                                // Catat aktivitas
                                $nama_pengirim = $_SESSION['user']['nama'];
                                $keterangan_log = "Mengirim laporan kendala tipe '$tipe_laporan'" . ($id_pesanan ? " untuk pesanan #KM-" . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT) : "");
                                kueri("INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan) VALUES (?, ?, 'laporan', 'tambah', ?)", [$id_pengguna, $nama_pengirim, $keterangan_log]);
                            } else {
                                $galat = "Gagal menyimpan data laporan ke database: " . mysqli_error($koneksi);
                                // Hapus berkas yang terlanjur diunggah jika db gagal
                                @unlink($jalur_simpan);
                            }
                        } else {
                            $galat = "Gagal memindahkan berkas ke folder penyimpanan. Pastikan folder 'uploads/laporan' memiliki izin menulis (writable).";
                        }
                    }
                }
            }
        }
    }
}

// Ambil riwayat laporan kendala pengguna
$kueri_laporan = kueri("
    SELECT l.*, p.status as status_pesanan, p.total_harga 
    FROM laporan_kendala l 
    LEFT JOIN pesanan p ON l.id_pesanan = p.id 
    WHERE l.id_pengguna = ? 
    ORDER BY l.tanggal_dibuat DESC
", [$id_pengguna]);

if ($kueri_laporan === false) {
    $galat = "Gagal memuat riwayat laporan: " . mysqli_error($koneksi);
}

include '../bagian/atas.php';
?>

<div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-[85vh] transition-colors duration-300">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Lapor Kendala</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1.5 text-sm">Punya masalah dengan pembayaran atau transaksi Anda? Unggah laporan dan berkas bukti di sini agar tim kami dapat segera menangani.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Kolom Kiri: Form Laporan -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm sticky top-20 transition-colors duration-300">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-5 pb-2 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-file-circle-exclamation text-lime-600"></i> Buat Laporan Baru
                    </h2>

                    <!-- Notifikasi Sukses -->
                    <?php if ($sukses): ?>
                        <div class="bg-lime-50 dark:bg-lime-950/20 text-lime-800 dark:text-lime-400 p-4 rounded-xl mb-5 border border-lime-200 dark:border-lime-900/30 flex items-start">
                            <i class="fa-solid fa-circle-check text-base mr-2 flex-shrink-0 text-lime-600 mt-0.5"></i>
                            <span class="text-xs font-semibold"><?= $sukses; ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Notifikasi Gagal -->
                    <?php if ($galat): ?>
                        <div class="bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 p-4 rounded-xl mb-5 border border-red-200 dark:border-red-900/30 flex items-start">
                            <i class="fa-solid fa-circle-exclamation text-base mr-2 flex-shrink-0 text-red-500 mt-0.5"></i>
                            <span class="text-xs font-semibold"><?= $galat; ?></span>
                        </div>
                    <?php endif; ?>

                    <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
                        
                        <!-- Pilihan Pesanan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Terkait Pesanan</label>
                            <select name="id_pesanan" class="w-full px-3 py-2 bg-white dark:bg-slate-950 dark:text-slate-100 rounded-xl border border-slate-200 dark:border-slate-800 outline-none text-xs">
                                <option value="">-- Tidak Terkait Pesanan (Kendala Umum) --</option>
                                <?php foreach ($daftar_pesanan as $pes): 
                                    $selected = ($id_pesanan_default === (int)$pes['id']) ? 'selected' : '';
                                    $tag = '#KM-' . str_pad($pes['id'], 5, '0', STR_PAD_LEFT);
                                    $tgl = date('d M Y', strtotime($pes['tanggal_pesanan']));
                                    $nominal = 'Rp ' . number_format($pes['total_harga'], 0, ',', '.');
                                ?>
                                    <option value="<?= $pes['id']; ?>" <?= $selected; ?>>
                                        <?= $tag; ?> (<?= $nominal; ?>) - <?= $pes['status']; ?> [<?= $tgl; ?>]
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tipe Laporan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Tipe Kendala</label>
                            <select name="tipe_laporan" required class="w-full px-3 py-2 bg-white dark:bg-slate-950 dark:text-slate-100 rounded-xl border border-slate-200 dark:border-slate-800 outline-none text-xs">
                                <option value="Pembayaran Gagal (Sudah Transfer)">Pembayaran Gagal (Sudah Transfer)</option>
                                <option value="Status Transaksi Tidak Berubah">Status Transaksi Tidak Berubah</option>
                                <option value="Kendala Pengiriman Paket">Kendala Pengiriman Paket</option>
                                <option value="Masalah Kualitas Kerajinan/Produk">Masalah Kualitas Kerajinan/Produk</option>
                                <option value="Lainnya">Lainnya / Masalah Akun</option>
                            </select>
                        </div>

                        <!-- Deskripsi Laporan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Deskripsi Masalah</label>
                            <textarea name="deskripsi" rows="4" required class="w-full px-3 py-2 bg-white dark:bg-slate-950 dark:text-slate-100 rounded-xl border border-slate-200 dark:border-slate-800 outline-none text-xs resize-none" placeholder="Jelaskan secara lengkap kendala yang Anda alami..."></textarea>
                        </div>

                        <!-- Upload Lampiran -->
                        <div>
                            <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Unggah Bukti (PDF / Foto)</label>
                            <input type="file" name="file_laporan" accept=".pdf,.png,.jpg,.jpeg" required class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-lime-50 file:text-lime-700 hover:file:bg-lime-100 dark:file:bg-slate-850 dark:file:text-slate-350 cursor-pointer">
                            <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-1">Ekstensi berkas: PDF, PNG, JPG, JPEG. Maksimal ukuran: 5MB.</p>
                        </div>

                        <!-- Tombol Submit -->
                        <button type="submit" name="kirim_laporan" class="w-full bg-lime-600 hover:bg-lime-700 text-white font-bold py-2.5 rounded-xl transition-all duration-300 text-xs flex items-center justify-center gap-1.5 cursor-pointer shadow-sm">
                            <i class="fa-solid fa-paper-plane text-[10px]"></i> Kirim Laporan
                        </button>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Riwayat Laporan -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 shadow-sm transition-colors duration-300">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100 mb-5 pb-2 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-slate-500"></i> Riwayat Laporan Kendala Anda
                    </h2>

                    <?php if ($kueri_laporan && mysqli_num_rows($kueri_laporan) > 0): ?>
                        <div class="space-y-4">
                            <?php while ($lap = mysqli_fetch_assoc($kueri_laporan)): 
                                $warna_status = [
                                    'pending'  => 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-900/40',
                                    'diproses' => 'bg-indigo-50 text-indigo-650 border-indigo-200 dark:bg-indigo-900/20 dark:text-indigo-400 dark:border-indigo-900/40',
                                    'selesai'  => 'bg-lime-50 text-lime-700 border-lime-200 dark:bg-lime-900/20 dark:text-lime-400 dark:border-lime-900/40'
                                ];
                                $status_pilihan = $warna_status[$lap['status']] ?? 'bg-slate-50 text-slate-655 border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700';
                            ?>
                                <div class="p-4 rounded-xl border border-slate-200/60 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20 flex flex-col sm:flex-row justify-between items-start gap-4">
                                    <div class="space-y-2 max-w-md">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="inline-block px-2.5 py-0.5 rounded-xl text-[9px] font-bold uppercase tracking-wider border <?= $status_pilihan; ?>">
                                                <?= $lap['status']; ?>
                                            </span>
                                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-550">
                                                Tipe: <?= htmlspecialchars($lap['tipe_laporan']); ?>
                                            </span>
                                        </div>

                                        <p class="text-xs text-slate-600 dark:text-slate-350 leading-relaxed italic">
                                            "<?= htmlspecialchars($lap['deskripsi']); ?>"
                                        </p>

                                        <div class="flex items-center gap-3 text-[10px] text-slate-400 dark:text-slate-500 font-medium">
                                            <span><i class="fa-regular fa-calendar-days mr-1 text-[9px]"></i> <?= date('d M Y, H:i', strtotime($lap['tanggal_dibuat'])); ?> WIB</span>
                                            <?php if ($lap['id_pesanan']): ?>
                                                <span><i class="fa-solid fa-receipt mr-1 text-[9px]"></i> Pesanan: #KM-<?= str_pad($lap['id_pesanan'], 5, '0', STR_PAD_LEFT); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="flex-shrink-0">
                                        <a href="<?= $awalan . $lap['file_laporan']; ?>" target="_blank" class="inline-flex items-center justify-center bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:text-lime-600 dark:hover:text-lime-400 px-3 py-1.5 rounded-lg text-[10px] font-bold transition-all duration-200 shadow-sm whitespace-nowrap cursor-pointer">
                                            <i class="fa-solid fa-paperclip mr-1 text-[9px]"></i> Lihat Bukti
                                        </a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <div class="py-12 text-center bg-slate-50/50 dark:bg-slate-950/20 rounded-xl border border-dashed border-slate-200 dark:border-slate-850">
                            <div class="w-10 h-10 bg-white dark:bg-slate-900 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 dark:text-slate-700 shadow-sm">
                                <i class="fa-solid fa-clipboard-list text-base"></i>
                            </div>
                            <h3 class="text-xs font-bold text-slate-700 dark:text-slate-300 mb-0.5">Belum ada riwayat laporan</h3>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500">Semua laporan kendala yang Anda ajukan akan muncul di bagian ini.</p>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

        </div>

    </div>
</div>

<?php include '../bagian/bawah.php'; ?>
