<?php
// admin/laporan.php: Halaman kelola laporan kendala dan pengaduan pengguna untuk diverifikasi oleh admin.

include '../koneksi.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

// Proses pembaruan status laporan kendala jika ada request
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id_laporan = (int) $_GET['id'];
    $status_baru = $_GET['status'];

    $valid_status = ['pending', 'diproses', 'selesai'];
    if (in_array($status_baru, $valid_status)) {
        // Ambil data laporan sebelum update untuk keperluan pencatatan log
        $res_l = kueri("SELECT l.*, p.nama FROM laporan_kendala l JOIN pengguna p ON l.id_pengguna = p.id WHERE l.id = ?", [$id_laporan]);
        if ($row_l = mysqli_fetch_assoc($res_l)) {
            $nama_pengirim = $row_l['nama'];
            if (kueri("UPDATE laporan_kendala SET status = ? WHERE id = ?", [$status_baru, $id_laporan])) {
                $status_ket = [
                    'diproses' => 'Memproses laporan kendala',
                    'selesai' => 'Menyelesaikan laporan kendala'
                ];
                $ket_aksi = isset($status_ket[$status_baru]) ? $status_ket[$status_baru] : "Mengubah status laporan #LK-$id_laporan menjadi '$status_baru'";
                catat_log('laporan', $status_baru, "$ket_aksi dari pengguna '$nama_pengirim'");
            }
        }
    }
    header("Location: laporan.php");
    exit();
}

$pembayaran_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pesanan WHERE status = 'dibayar'"))['total'];
$testimoni_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM testimonial WHERE status = 'pending'"))['total'];
$laporan_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM laporan_kendala WHERE status = 'pending'"))['total'];

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
?>

<?php
$halaman_aktif = 'laporan';
$judul_halaman = 'Laporan Kendala';
include 'bagian/atas.php';
?>

    <main class="flex-grow p-4 sm:p-6 w-full max-w-6xl mx-auto overflow-x-hidden">
        
        <div class="mb-6">
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Kelola Laporan Kendala</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">Pantau, tindak lanjuti, dan verifikasi berkas bukti dari laporan kendala transaksi pengguna.</p>
        </div>

        <!-- Tab Penyaringan Status -->
        <div class="flex flex-wrap bg-slate-100/70 dark:bg-slate-900 p-1 rounded-xl mb-6 border border-slate-200/50 dark:border-slate-800 gap-1 sm:gap-0">
            <a href="?status=all" class="px-3.5 py-1.5 rounded-md text-xs font-bold transition-all duration-200 <?= $status_filter === 'all' ? 'bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 hover:bg-slate-200/50 dark:hover:bg-slate-800/40'; ?>">
                Semua Laporan
            </a>
            <a href="?status=pending" class="px-3.5 py-1.5 rounded-md text-xs font-bold transition-all duration-200 flex items-center <?= $status_filter === 'pending' ? 'bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 hover:bg-slate-200/50 dark:hover:bg-slate-800/40'; ?>">
                Pending
                <?php if ($laporan_tertunda > 0): ?>
                    <span class="ml-1.5 bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-400 text-[9px] px-1 py-0.5 rounded-md font-extrabold"><?= $laporan_tertunda; ?></span>
                <?php endif; ?>
            </a>
            <a href="?status=diproses" class="px-3.5 py-1.5 rounded-md text-xs font-bold transition-all duration-200 <?= $status_filter === 'diproses' ? 'bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 hover:bg-slate-200/50 dark:hover:bg-slate-800/40'; ?>">
                Diproses
            </a>
            <a href="?status=selesai" class="px-3.5 py-1.5 rounded-md text-xs font-bold transition-all duration-200 <?= $status_filter === 'selesai' ? 'bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 hover:bg-slate-200/50 dark:hover:bg-slate-800/40'; ?>">
                Selesai
            </a>
        </div>

        <?php
        if ($status_filter === 'all') {
            $kueri_laporan_admin = kueri("
                SELECT l.*, p.nama, p.email, p.no_telp 
                FROM laporan_kendala l 
                JOIN pengguna p ON l.id_pengguna = p.id 
                ORDER BY l.tanggal_dibuat DESC
            ");
        } else {
            $kueri_laporan_admin = kueri("
                SELECT l.*, p.nama, p.email, p.no_telp 
                FROM laporan_kendala l 
                JOIN pengguna p ON l.id_pengguna = p.id 
                WHERE l.status = ?
                ORDER BY l.tanggal_dibuat DESC
            ", [$status_filter]);
        }
        ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php if (mysqli_num_rows($kueri_laporan_admin) > 0): ?>
                <?php while ($lap = mysqli_fetch_assoc($kueri_laporan_admin)): 
                    $kelas_lencana = '';
                    $kelas_ikon = '';
                    if ($lap['status'] === 'selesai') {
                        $kelas_lencana = 'bg-lime-50 dark:bg-lime-900/20 text-lime-700 dark:text-lime-400 border-lime-200 dark:border-lime-900/30';
                        $kelas_ikon = 'fa-circle-check';
                    } elseif ($lap['status'] === 'diproses') {
                        $kelas_lencana = 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-900/30';
                        $kelas_ikon = 'fa-spinner fa-spin';
                    } else {
                        $kelas_lencana = 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-900/30';
                        $kelas_ikon = 'fa-clock';
                    }
                ?>
                    <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm transition-all duration-300 flex flex-col group justify-between">
                        
                        <div>
                            <!-- Header Laporan -->
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 line-clamp-1"><?= htmlspecialchars($lap['nama']); ?></h3>
                                    <p class="text-[9px] font-semibold text-slate-500 dark:text-slate-400 line-clamp-1"><?= htmlspecialchars($lap['email']); ?></p>
                                    <p class="text-[9px] font-semibold text-slate-500 dark:text-slate-400 line-clamp-1"><?= htmlspecialchars($lap['no_telp'] ?? '-'); ?></p>
                                </div>
                                <span class="px-2 py-0.5 rounded-xl text-[9px] font-bold uppercase tracking-wider border <?= $kelas_lencana; ?> whitespace-nowrap">
                                    <i class="fa-solid <?= $kelas_ikon; ?> mr-0.5"></i> <?= $lap['status']; ?>
                                </span>
                            </div>

                            <div class="h-px bg-slate-100 dark:bg-slate-800 my-2.5"></div>

                            <!-- Detail Deskripsi -->
                            <div class="space-y-1.5 mb-4">
                                <div class="flex items-center gap-1.5 text-[9px] font-extrabold uppercase tracking-wider text-slate-400">
                                    <span>Tipe: <?= htmlspecialchars($lap['tipe_laporan']); ?></span>
                                    <?php if ($lap['id_pesanan']): ?>
                                        <span>•</span>
                                        <span class="text-lime-600 dark:text-lime-400">Pesanan #KM-<?= str_pad($lap['id_pesanan'], 5, '0', STR_PAD_LEFT); ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-slate-600 dark:text-slate-300 text-xs sm:text-sm leading-relaxed italic">
                                    "<?= htmlspecialchars($lap['deskripsi']); ?>"
                                </p>
                            </div>
                        </div>

                        <!-- Footer & Aksi -->
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mt-auto">
                            <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500">
                                <i class="fa-regular fa-calendar-days mr-1 text-[10px]"></i> <?= date('d M Y, H:i', strtotime($lap['tanggal_dibuat'])); ?> WIB
                            </span>

                            <div class="flex items-center gap-2">
                                <!-- Tombol File Bukti -->
                                <a href="../<?= $lap['file_laporan']; ?>" target="_blank" class="w-8 h-8 flex items-center justify-center bg-slate-50 dark:bg-slate-950 text-slate-500 dark:text-slate-400 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-800 hover:text-slate-850 dark:hover:text-slate-250 border border-slate-200 dark:border-slate-800 transition-colors" title="Lihat Berkas Lampiran">
                                    <i class="fa-solid fa-paperclip text-xs"></i>
                                </a>

                                <?php if ($lap['status'] === 'pending'): ?>
                                    <a href="?id=<?= $lap['id']; ?>&status=diproses" class="w-8 h-8 flex items-center justify-center bg-indigo-50 dark:bg-indigo-950/20 text-indigo-600 dark:text-indigo-400 rounded-xl hover:bg-indigo-600 hover:text-white transition-colors border border-indigo-200 dark:border-indigo-900/30" title="Tandai Diproses">
                                        <i class="fa-solid fa-spinner text-xs"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if ($lap['status'] !== 'selesai'): ?>
                                    <a href="?id=<?= $lap['id']; ?>&status=selesai" class="w-8 h-8 flex items-center justify-center bg-lime-50 dark:bg-lime-950/20 text-lime-600 dark:text-lime-400 rounded-xl hover:bg-lime-600 hover:text-white transition-colors border border-lime-200 dark:border-lime-900/30" title="Tandai Selesai / Selesai Verifikasi">
                                        <i class="fa-solid fa-check text-xs"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-16 bg-white dark:bg-slate-900 rounded-xl border border-dashed border-slate-200 dark:border-slate-800">
                    <div class="w-12 h-12 bg-slate-50 dark:bg-slate-950 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 dark:text-slate-700">
                        <i class="fa-solid fa-circle-exclamation text-xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-0.5">Tidak ada laporan kendala</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Kategori ini belum memiliki data laporan kendala masuk dari pengguna.</p>
                </div>
            <?php endif; ?>
        </div>

    </main>

<?php include 'bagian/bawah.php'; ?>
