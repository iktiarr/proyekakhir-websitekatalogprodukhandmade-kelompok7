<?php
// admin/index.php: Halaman dasbor panel admin, menampilkan statistik toko, ringkasan transaksi, serta log aktivitas sistem terbaru.

include '../koneksi.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

kueri("
    CREATE TABLE IF NOT EXISTS log_aktivitas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        id_pengguna INT NULL,
        nama_pengguna VARCHAR(100) NOT NULL,
        tipe_aktivitas VARCHAR(50) NOT NULL,
        aksi VARCHAR(50) NOT NULL,
        keterangan TEXT NOT NULL,
        tanggal_dibuat TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
");

$cek_log = kueri("SELECT COUNT(*) as total FROM log_aktivitas");
$total_log = $cek_log ? mysqli_fetch_assoc($cek_log)['total'] : 0;
if ($total_log == 0) {
    $res_u = kueri("SELECT * FROM pengguna ORDER BY tanggal_dibuat ASC LIMIT 10");
    while ($row = mysqli_fetch_assoc($res_u)) {
        $id = $row['id'];
        $nama = $row['nama'];
        $role = $row['role'];
        $tgl = $row['tanggal_dibuat'];
        $msg = $role === 'admin' ? 'Menginisialisasi sebagai akun Administrator' : 'Mendaftar sebagai pengguna baru';
        kueri("INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan, tanggal_dibuat) VALUES (?, ?, 'pengguna', 'daftar', ?, ?)", [$id, $nama, $msg, $tgl]);
    }

    $res_p = kueri("SELECT * FROM produk LIMIT 5");
    $res_admin = kueri("SELECT id, nama FROM pengguna WHERE role='admin' LIMIT 1");
    if ($admin_row = mysqli_fetch_assoc($res_admin)) {
        $admin_id = $admin_row['id'];
        $admin_nama = $admin_row['nama'];
        while ($row = mysqli_fetch_assoc($res_p)) {
            $nama_p = $row['nama_produk'];
            kueri("INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan) VALUES (?, ?, 'produk', 'tambah', ?)", [$admin_id, $admin_nama, "Menambahkan produk baru '$nama_p'"]);
        }
    }

    $res_t = kueri("SELECT t.*, p.nama FROM testimonial t JOIN pengguna p ON t.id_pengguna = p.id LIMIT 5");
    while ($row = mysqli_fetch_assoc($res_t)) {
        $nama_pengulas = $row['nama'];
        $status_t = $row['status'];
        $tgl = $row['tanggal_dibuat'];
        kueri("INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan, tanggal_dibuat) VALUES (?, ?, 'testimoni', 'tambah', 'Menulis ulasan baru', ?)", [$row['id_pengguna'], $nama_pengulas, $tgl]);
        if ($status_t === 'approved' && isset($admin_id)) {
            kueri("INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan, tanggal_dibuat) VALUES (?, ?, 'testimoni', 'setujui', ?, ?)", [$admin_id, $admin_nama, "Menyetujui testimoni dari '$nama_pengulas'", $tgl]);
        }
    }

    $res_o = kueri("SELECT p.*, u.nama FROM pesanan p JOIN pengguna u ON p.id_pengguna = u.id LIMIT 5");
    while ($row = mysqli_fetch_assoc($res_o)) {
        $nama_pembeli = $row['nama'];
        $id_o = $row['id'];
        $tgl = $row['tanggal_pesanan'];
        $status_o = $row['status'];
        $tag = "#HM-" . str_pad($id_o, 5, '0', STR_PAD_LEFT);
        kueri("INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan, tanggal_dibuat) VALUES (?, ?, 'pesanan', 'tambah', ?, ?)", [$row['id_pengguna'], $nama_pembeli, "Membuat pesanan baru $tag", $tgl]);
        if ($status_o !== 'menunggu' && isset($admin_id)) {
            $status_keterangan = [
                'dibayar' => 'Mengonfirmasi pembayaran pesanan',
                'dikirim' => 'Mengirim produk untuk pesanan',
                'selesai' => 'Menyelesaikan pesanan',
                'dibatalkan' => 'Membatalkan pesanan'
            ];
            $ket_aksi = isset($status_keterangan[$status_o]) ? $status_keterangan[$status_o] : "Mengubah status pesanan menjadi '$status_o'";
            kueri("INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan, tanggal_dibuat) VALUES (?, ?, 'pesanan', ?, ?, ?)", [$admin_id, $admin_nama, $status_o, "$ket_aksi $tag dari '$nama_pembeli'", $tgl]);
        }
    }
}

$jumlah_pengguna = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pengguna WHERE role = 'user'"))['total'];
$jumlah_admin = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pengguna WHERE role = 'admin'"))['total'];
$jumlah_produk = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM produk"))['total'];
$pembayaran_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pesanan WHERE status = 'dibayar'"))['total'];
$testimoni_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM testimonial WHERE status = 'pending'"))['total'];
$laporan_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM laporan_kendala WHERE status = 'pending'"))['total'];

$pendapatan_hari_ini = mysqli_fetch_assoc(kueri("SELECT SUM(total_harga) AS total FROM pesanan WHERE status IN ('dibayar', 'dikirim', 'selesai') AND DATE(tanggal_pesanan) = CURDATE()"))['total'] ?? 0;
$pendapatan_seminggu = mysqli_fetch_assoc(kueri("SELECT SUM(total_harga) AS total FROM pesanan WHERE status IN ('dibayar', 'dikirim', 'selesai') AND tanggal_pesanan >= DATE_SUB(NOW(), INTERVAL 7 DAY)"))['total'] ?? 0;
$pendapatan_sebulan = mysqli_fetch_assoc(kueri("SELECT SUM(total_harga) AS total FROM pesanan WHERE status IN ('dibayar', 'dikirim', 'selesai') AND tanggal_pesanan >= DATE_SUB(NOW(), INTERVAL 30 DAY)"))['total'] ?? 0;
$pendapatan_total = mysqli_fetch_assoc(kueri("SELECT SUM(total_harga) AS total FROM pesanan WHERE status IN ('dibayar', 'dikirim', 'selesai')"))['total'] ?? 0;



$nama_depan_admin = explode(' ', trim($_SESSION['admin']['nama']))[0];
?>

<?php
$halaman_aktif = 'dasbor';
$judul_halaman = 'Dasbor';
include 'bagian/atas.php';
?>

    <main class="flex-grow p-4 sm:p-6 w-full max-w-6xl mx-auto overflow-x-hidden">
        


        <!-- Baris Metrik Umum -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <!-- Total Pengguna -->
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3 shadow-sm transition-colors duration-300">
                <div>
                    <p class="text-slate-400 dark:text-slate-550 text-[9px] font-bold uppercase tracking-widest mb-0.5">Total Pengguna</p>
                    <h2 class="text-xl font-extrabold text-slate-800 dark:text-slate-100"><?= $jumlah_pengguna; ?></h2>
                </div>
                <div class="w-8 h-8 bg-slate-50 dark:bg-slate-950 rounded-xl flex items-center justify-center text-slate-500 dark:text-slate-400 flex-shrink-0">
                    <i class="fa-solid fa-users text-sm"></i>
                </div>
            </div>
            
            <!-- Total Produk -->
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3 shadow-sm transition-colors duration-300">
                <div>
                    <p class="text-slate-400 dark:text-slate-550 text-[9px] font-bold uppercase tracking-widest mb-0.5">Total Produk</p>
                    <h2 class="text-xl font-extrabold text-slate-800 dark:text-slate-100"><?= $jumlah_produk; ?></h2>
                </div>
                <div class="w-8 h-8 bg-slate-50 dark:bg-slate-950 rounded-xl flex items-center justify-center text-slate-500 dark:text-slate-400 flex-shrink-0">
                    <i class="fa-solid fa-box-open text-sm"></i>
                </div>
            </div>

            <!-- Total Admin -->
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3 shadow-sm transition-colors duration-300">
                <div>
                    <p class="text-slate-400 dark:text-slate-550 text-[9px] font-bold uppercase tracking-widest mb-0.5">Total Admin</p>
                    <h2 class="text-xl font-extrabold text-slate-800 dark:text-slate-100"><?= $jumlah_admin; ?></h2>
                </div>
                <div class="w-8 h-8 bg-slate-50 dark:bg-slate-950 rounded-xl flex items-center justify-center text-slate-500 dark:text-slate-400 flex-shrink-0">
                    <i class="fa-solid fa-user-tie text-sm"></i>
                </div>
            </div>

            <!-- Ulasan Pending -->
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex items-center justify-between gap-3 shadow-sm transition-colors duration-300">
                <div>
                    <p class="text-slate-400 dark:text-slate-550 text-[9px] font-bold uppercase tracking-widest mb-0.5">Ulasan Pending</p>
                    <h2 class="text-xl font-extrabold text-slate-800 dark:text-slate-100"><?= $testimoni_tertunda; ?></h2>
                </div>
                <div class="w-8 h-8 <?= $testimoni_tertunda > 0 ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400' : 'bg-slate-50 dark:bg-slate-900 text-slate-500 dark:text-slate-400'; ?> rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-comments text-sm"></i>
                </div>
            </div>
        </div>

        <!-- Section Analisis Pendapatan -->
        <div class="mb-6">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Hari Ini -->
                <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col justify-between shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-slate-400 dark:text-slate-550 text-[9px] font-bold uppercase tracking-widest">Hari Ini</p>
                        <span class="w-7 h-7 bg-lime-50 dark:bg-lime-900/40 rounded-lg flex items-center justify-center text-lime-600 dark:text-lime-400 text-[11px] font-bold">1H</span>
                    </div>
                    <h2 class="text-base sm:text-lg font-black text-slate-800 dark:text-slate-100 mt-2.5">
                        Rp <?= number_format($pendapatan_hari_ini, 0, ',', '.'); ?>
                    </h2>
                </div>
 
                <!-- Seminggu -->
                <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col justify-between shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-slate-400 dark:text-slate-555 text-[9px] font-bold uppercase tracking-widest">Seminggu</p>
                        <span class="w-7 h-7 bg-lime-50 dark:bg-lime-900/40 rounded-lg flex items-center justify-center text-lime-600 dark:text-lime-400 text-[11px] font-bold">7H</span>
                    </div>
                    <h2 class="text-base sm:text-lg font-black text-slate-800 dark:text-slate-100 mt-2.5">
                        Rp <?= number_format($pendapatan_seminggu, 0, ',', '.'); ?>
                    </h2>
                </div>
 
                <!-- Sebulan -->
                <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col justify-between shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-slate-400 dark:text-slate-550 text-[9px] font-bold uppercase tracking-widest">Sebulan</p>
                        <span class="w-7 h-7 bg-lime-50 dark:bg-lime-900/40 rounded-lg flex items-center justify-center text-lime-600 dark:text-lime-400 text-[11px] font-bold">30H</span>
                    </div>
                    <h2 class="text-base sm:text-lg font-black text-slate-800 dark:text-slate-100 mt-2.5">
                        Rp <?= number_format($pendapatan_sebulan, 0, ',', '.'); ?>
                    </h2>
                </div>
 
                <!-- Total Semua -->
                <div class="bg-white dark:bg-slate-900 p-4 rounded-xl border border-slate-200 dark:border-slate-800 flex flex-col justify-between shadow-sm">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-slate-400 dark:text-slate-550 text-[9px] font-bold uppercase tracking-widest">Total Pendapatan</p>
                        <span class="w-7 h-7 bg-lime-50 dark:bg-lime-900/40 rounded-lg flex items-center justify-center text-lime-600 dark:text-lime-400 text-[11px] font-bold">ALL</span>
                    </div>
                    <h2 class="text-base sm:text-lg font-black text-slate-800 dark:text-slate-100 mt-2.5">
                        Rp <?= number_format($pendapatan_total, 0, ',', '.'); ?>
                    </h2>
                </div>
            </div>
        </div>


        


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
                <div class="p-3.5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Pesanan Terbaru</h3>
                    <a href="pembayaran.php" class="text-xs font-bold text-lime-600 dark:text-lime-400 hover:text-lime-700 dark:hover:text-lime-300 hover:underline">Lihat Semua</a>
                </div>
                <div class="p-3.5 flex-grow">
                    <div class="space-y-3">
                        <?php
                        $kueri_pesanan_terbaru = kueri("SELECT p.*, u.nama FROM pesanan p JOIN pengguna u ON p.id_pengguna = u.id ORDER BY p.tanggal_pesanan DESC LIMIT 5");
                        if(mysqli_num_rows($kueri_pesanan_terbaru) > 0):
                            while($baris = mysqli_fetch_assoc($kueri_pesanan_terbaru)):
                        ?>
                        <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 rounded-xl cursor-default">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white dark:bg-slate-900 rounded-xl flex items-center justify-center text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-800 flex-shrink-0">
                                    <i class="fa-solid fa-bag-shopping text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200"><?= $baris['nama']; ?></p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-550 mt-0.5"><i class="fa-regular fa-clock mr-1"></i><?= date('H:i, d M Y', strtotime($baris['tanggal_pesanan'])); ?></p>
                                </div>
                            </div>
                            <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200">Rp <?= number_format($baris['total_harga'], 0, ',', '.'); ?></span>
                        </div>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                        <div class="text-center py-6">
                            <div class="w-10 h-10 bg-slate-50 dark:bg-slate-950 rounded-full flex items-center justify-center mx-auto mb-2.5 text-slate-300 dark:text-slate-700">
                                <i class="fa-solid fa-receipt text-base"></i>
                            </div>
                            <p class="text-slate-400 dark:text-slate-550 text-xs">Belum ada pesanan terbaru masuk.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col shadow-sm transition-colors duration-300">
                <div class="p-3.5 border-b border-slate-100 dark:border-slate-800">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Aktivitas Terbaru</h3>
                </div>
                <div class="p-3.5 flex-grow overflow-y-auto max-h-[340px] custom-scrollbar">
                    <div class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        <?php
                        $kueri_aktivitas = kueri("SELECT * FROM log_aktivitas ORDER BY tanggal_dibuat DESC, id DESC LIMIT 8");
                        if(mysqli_num_rows($kueri_aktivitas) > 0):
                            while($aktivitas = mysqli_fetch_assoc($kueri_aktivitas)):
                                $tipe = $aktivitas['tipe_aktivitas'];
                                $ikon_aktivitas = 'fa-circle-dot';
                                if ($tipe === 'produk') $ikon_aktivitas = 'fa-box-open';
                                elseif ($tipe === 'pengguna') $ikon_aktivitas = 'fa-user';
                                elseif ($tipe === 'testimoni') $ikon_aktivitas = 'fa-comment';
                                elseif ($tipe === 'pesanan') $ikon_aktivitas = 'fa-receipt';
                        ?>
                        <div class="py-3 flex items-start gap-3 first:pt-0 last:pb-0 text-xs">
                            <div class="w-7 h-7 bg-slate-50 dark:bg-slate-950 rounded-lg border border-slate-100 dark:border-slate-800 flex items-center justify-center text-slate-400 dark:text-slate-500 flex-shrink-0">
                                <i class="fa-solid <?= $ikon_aktivitas; ?> text-[10px]"></i>
                            </div>
                            <div class="flex-grow min-w-0">
                                <p class="text-slate-600 dark:text-slate-400 leading-normal">
                                    <span class="font-bold text-slate-800 dark:text-slate-200"><?= $aktivitas['nama_pengguna']; ?></span> 
                                    <?= $aktivitas['keterangan']; ?>
                                </p>
                                <span class="text-[9px] text-slate-400 dark:text-slate-550 block mt-0.5"><i class="fa-regular fa-clock mr-1"></i><?= date('H:i, d M', strtotime($aktivitas['tanggal_dibuat'])); ?></span>
                            </div>
                        </div>
                        <?php
                            endwhile;
                        else:
                        ?>
                        <div class="text-center py-6">
                            <div class="w-10 h-10 bg-slate-50 dark:bg-slate-950 rounded-full flex items-center justify-center mx-auto mb-2.5 text-slate-300 dark:text-slate-700">
                                <i class="fa-solid fa-list-check text-base"></i>
                            </div>
                            <p class="text-slate-400 dark:text-slate-500 text-xs">Belum ada riwayat aktivitas terbaru.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>

<?php include 'bagian/bawah.php'; ?>