<?php
// admin/testimoni.php: Halaman kelola testimonial pelanggan, memungkinkan admin menyetujui (approve) atau menghapus (delete) ulasan.

include '../koneksi.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

$testimoni_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM testimonial WHERE status = 'pending'"))['total'];
$pembayaran_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pesanan WHERE status = 'dibayar'"))['total'];
$laporan_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM laporan_kendala WHERE status = 'pending'"))['total'];

$aksi = isset($_GET['action']) ? $_GET['action'] : 'list';
$id_testimoni = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (isset($_GET['approve']) && $id_testimoni > 0) {
    $res_t = kueri("SELECT t.*, p.nama FROM testimonial t JOIN pengguna p ON t.id_pengguna = p.id WHERE t.id = ?", [$id_testimoni]);
    if ($row_t = mysqli_fetch_assoc($res_t)) {
        $nama_pengulas = $row_t['nama'];
        if (kueri("UPDATE testimonial SET status = 'approved' WHERE id = ?", [$id_testimoni])) {
            catat_log('testimoni', 'setujui', "Menyetujui testimoni dari '$nama_pengulas'");
        }
    }
    header("Location: testimoni.php");
    exit();
}

if (isset($_GET['reject']) && $id_testimoni > 0) {
    $res_t = kueri("SELECT t.*, p.nama FROM testimonial t JOIN pengguna p ON t.id_pengguna = p.id WHERE t.id = ?", [$id_testimoni]);
    if ($row_t = mysqli_fetch_assoc($res_t)) {
        $nama_pengulas = $row_t['nama'];
        if (kueri("UPDATE testimonial SET status = 'rejected' WHERE id = ?", [$id_testimoni])) {
            catat_log('testimoni', 'tolak', "Menolak testimoni dari '$nama_pengulas'");
        }
    }
    header("Location: testimoni.php");
    exit();
}

if (isset($_GET['delete']) && $id_testimoni > 0) {
    $res_t = kueri("SELECT t.*, p.nama FROM testimonial t JOIN pengguna p ON t.id_pengguna = p.id WHERE t.id = ?", [$id_testimoni]);
    if ($row_t = mysqli_fetch_assoc($res_t)) {
        $nama_pengulas = $row_t['nama'];
        if (kueri("DELETE FROM testimonial WHERE id = ?", [$id_testimoni])) {
            catat_log('testimoni', 'hapus', "Menghapus testimoni dari '$nama_pengulas'");
        }
    }
    header("Location: testimoni.php");
    exit();
}

$halaman_aktif = 'testimoni';
$judul_halaman = 'Kelola Testimoni';
include 'bagian/atas.php';
?>

    <main class="flex-grow p-4 sm:p-6 w-full max-w-6xl mx-auto overflow-x-hidden">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Kelola Testimonial</h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">Review dan setujui testimonial serta ulasan dari pembeli produk kerajinan.</p>
            </div>
            <a href="cetak_laporan.php?tipe=ulasan" target="_blank" class="bg-amber-500 hover:bg-amber-600 text-white px-4 py-2.5 rounded-xl font-bold transition-all duration-300 flex items-center cursor-pointer text-xs sm:text-sm shadow-sm flex-shrink-0">
                <i class="fa-solid fa-file-pdf mr-1.5"></i> Cetak Laporan Ulasan
            </a>
        </div>
        


        <?php
        $status = isset($_GET['status']) ? $_GET['status'] : 'all';
        ?>

        <div class="flex flex-wrap bg-slate-100/70 dark:bg-slate-900 p-1 rounded-xl mb-6 border border-slate-200/50 dark:border-slate-800 gap-1 sm:gap-0">
            <a href="?status=all" class="px-3.5 py-1.5 rounded-md text-xs font-bold transition-all duration-200 <?= $status === 'all' ? 'bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 hover:bg-slate-200/50 dark:hover:bg-slate-800/40'; ?>">
                Semua Ulasan
            </a>
            <a href="?status=pending" class="px-3.5 py-1.5 rounded-md text-xs font-bold transition-all duration-200 flex items-center <?= $status === 'pending' ? 'bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 hover:bg-slate-200/50 dark:hover:bg-slate-800/40'; ?>">
                Pending
                <?php if ($testimoni_tertunda > 0): ?>
                    <span class="ml-1.5 bg-amber-100 dark:bg-amber-900 text-amber-700 dark:text-amber-400 text-[9px] px-1 py-0.5 rounded-md"><?= $testimoni_tertunda; ?></span>
                <?php endif; ?>
            </a>
            <a href="?status=approved" class="px-3.5 py-1.5 rounded-md text-xs font-bold transition-all duration-200 <?= $status === 'approved' ? 'bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 hover:bg-slate-200/50 dark:hover:bg-slate-800/40'; ?>">
                Disetujui
            </a>
            <a href="?status=rejected" class="px-3.5 py-1.5 rounded-md text-xs font-bold transition-all duration-200 <?= $status === 'rejected' ? 'bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 hover:bg-slate-200/50 dark:hover:bg-slate-800/40'; ?>">
                Ditolak
            </a>
        </div>

        <?php
        if ($status === 'all') {
            $kueri_testimoni = kueri("
                SELECT t.*, p.nama as pengguna_nama, p.email 
                FROM testimonial t 
                JOIN pengguna p ON t.id_pengguna = p.id 
                ORDER BY t.tanggal_dibuat DESC
            ");
        } else {
            $kueri_testimoni = kueri("
                SELECT t.*, p.nama as pengguna_nama, p.email 
                FROM testimonial t 
                JOIN pengguna p ON t.id_pengguna = p.id 
                WHERE t.status = ?
                ORDER BY t.tanggal_dibuat DESC
            ", [$status]);
        }
        ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php if (mysqli_num_rows($kueri_testimoni) > 0): ?>
                <?php while ($testimoni = mysqli_fetch_assoc($kueri_testimoni)): 
                    
                    $kelas_lencana = '';
                    $kelas_ikon = '';
                    if ($testimoni['status'] === 'approved') {
                        $kelas_lencana = 'bg-lime-50 dark:bg-lime-900/20 text-lime-700 dark:text-lime-400 border-lime-200 dark:border-lime-900/30';
                        $kelas_ikon = 'fa-check';
                    } elseif ($testimoni['status'] === 'rejected') {
                        $kelas_lencana = 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-200 dark:border-red-900/30';
                        $kelas_ikon = 'fa-xmark';
                    } else {
                        $kelas_lencana = 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-900/30';
                        $kelas_ikon = 'fa-clock';
                    }
                ?>
                    <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm transition-all duration-300 flex flex-col group">
                        
                        <div class="flex justify-between items-start mb-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 flex items-center justify-center font-bold text-slate-500 dark:text-slate-400 shadow-sm text-xs">
                                    <?= strtoupper(substr($testimoni['nama'], 0, 1)); ?>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 line-clamp-1"><?= $testimoni['nama']; ?></h3>
                                    <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 line-clamp-1"><?= $testimoni['pekerjaan'] ?: 'Pelanggan'; ?></p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-xl text-[9px] font-bold uppercase tracking-wider border <?= $kelas_lencana; ?>">
                                <i class="fa-solid <?= $kelas_ikon; ?> mr-0.5"></i> <?= $testimoni['status']; ?>
                            </span>
                        </div>

                        <div class="flex gap-0.5 mb-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fa-solid fa-star text-xs <?= $i <= $testimoni['rating'] ? 'text-lime-500' : 'text-slate-200 dark:text-slate-800'; ?>"></i>
                            <?php endfor; ?>
                        </div>

                        <p class="text-slate-600 dark:text-slate-300 text-xs sm:text-sm mb-4 flex-grow italic leading-relaxed">
                            "<?= $testimoni['isi_ulasan']; ?>"
                        </p>

                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mt-auto">
                            <span class="text-[10px] font-medium text-slate-400 dark:text-slate-500">
                                <i class="fa-regular fa-calendar mr-1"></i> <?= date('d M Y, H:i', strtotime($testimoni['tanggal_dibuat'])); ?>
                            </span>
                            
                            <div class="flex flex-wrap gap-1.5">
                                <?php if ($testimoni['status'] === 'pending'): ?>
                                    <a href="?approve&id=<?= $testimoni['id']; ?>" class="w-7 h-7 flex items-center justify-center bg-lime-50 dark:bg-lime-900/40 text-lime-600 dark:text-lime-400 rounded-xl hover:bg-lime-600 hover:text-white transition-colors border border-transparent hover:border-lime-700" title="Setujui">
                                        <i class="fa-solid fa-check text-xs"></i>
                                    </a>
                                    <a href="?reject&id=<?= $testimoni['id']; ?>" class="w-7 h-7 flex items-center justify-center bg-red-50 dark:bg-red-900/40 text-red-500 dark:text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-colors border border-transparent hover:border-red-600" title="Tolak">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <a href="?delete&id=<?= $testimoni['id']; ?>" onclick="return confirm('Yakin ingin menghapus testimonial ini permanen?')" class="w-7 h-7 flex items-center justify-center bg-slate-50 dark:bg-slate-950 text-slate-400 dark:text-slate-500 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-300 transition-colors border border-transparent" title="Hapus Permanen">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </a>
                            </div>
                        </div>
                        
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-16 bg-white dark:bg-slate-900 rounded-xl border border-dashed border-slate-200 dark:border-slate-800">
                    <div class="w-12 h-12 bg-slate-50 dark:bg-slate-950 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 dark:text-slate-700">
                        <i class="fa-solid fa-comments text-xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-0.5">Tidak ada ulasan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Kategori ini belum memiliki data testimonial untuk ditampilkan.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>


<?php include 'bagian/bawah.php'; ?>
