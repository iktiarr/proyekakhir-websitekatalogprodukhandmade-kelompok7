<?php
// admin/pengguna.php: Halaman kelola pengguna terdaftar, digunakan untuk melihat daftar pengguna dan menghapus akun pengguna (non-admin).

include '../koneksi.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    $cek_pengguna = kueri("SELECT nama, role FROM pengguna WHERE id = ?", [$id_hapus]);
    $data_pengguna = mysqli_fetch_assoc($cek_pengguna);
    
    if ($data_pengguna && $data_pengguna['role'] === 'user') {
        $nama_pengguna_hapus = $data_pengguna['nama'];
        if (kueri("DELETE FROM pengguna WHERE id = ?", [$id_hapus])) {
            catat_log('pengguna', 'hapus', "Menghapus pengguna '$nama_pengguna_hapus'");
        }
    }
    
    header("Location: pengguna.php");
    exit();
}

$kueri_pengguna = kueri("SELECT * FROM pengguna ORDER BY role ASC, nama ASC");
$pembayaran_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pesanan WHERE status = 'dibayar'"))['total'];
$testimoni_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM testimonial WHERE status = 'pending'"))['total'];
$laporan_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM laporan_kendala WHERE status = 'pending'"))['total'];
?>

<?php
$halaman_aktif = 'pengguna';
$judul_halaman = 'Kelola Pengguna';
include 'bagian/atas.php';
?>

    <main class="flex-grow p-4 sm:p-6 w-full max-w-6xl mx-auto overflow-x-hidden">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Kelola Pengguna</h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">Daftar pengguna dan admin yang terdaftar di situs HandMadura.</p>
            </div>
            <a href="cetak_laporan.php?tipe=pengguna" target="_blank" class="bg-lime-600 hover:bg-lime-700 text-white px-4 py-2.5 rounded-xl font-bold transition-all duration-300 flex items-center cursor-pointer text-xs sm:text-sm shadow-sm flex-shrink-0">
                <i class="fa-solid fa-file-pdf mr-1.5"></i> Cetak Laporan Pengguna
            </a>
        </div>
        


        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm transition-colors duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-400 dark:text-slate-500 text-[10px] uppercase tracking-wider font-bold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 pl-6">Pengguna</th>
                        <th class="px-4 py-3.5">Email</th>
                        <th class="px-4 py-3.5">No. Telepon</th>
                        <th class="px-4 py-3.5">Alamat</th>
                        <th class="px-4 py-3.5">Role Akses</th>
                        <th class="px-4 py-3.5">Bergabung Pada</th>
                        <th class="px-4 py-3.5 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/60">
                    <?php 
                    if(mysqli_num_rows($kueri_pengguna) > 0):
                        while($baris = mysqli_fetch_assoc($kueri_pengguna)): 
                    ?>
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 text-xs sm:text-sm transition-colors duration-200">
                        <td class="px-4 py-3 pl-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 <?= $baris['role'] === 'admin' ? 'bg-lime-100 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400' : 'bg-slate-100 dark:bg-slate-950 text-slate-500 dark:text-slate-400'; ?> rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    <?= strtoupper(substr($baris['nama'], 0, 1)); ?>
                                </div>
                                <span class="font-bold text-slate-800 dark:text-slate-200"><?= $baris['nama']; ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            <?= $baris['email']; ?>
                        </td>
                        <td class="px-4 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            <?= $baris['no_telp'] ?: '<span class="text-slate-300 dark:text-slate-600 italic">-</span>'; ?>
                        </td>
                        <td class="px-4 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 max-w-xs truncate" title="<?= htmlspecialchars($baris['alamat'] ?? ''); ?>">
                            <?= $baris['alamat'] ?: '<span class="text-slate-300 dark:text-slate-600 italic">-</span>'; ?>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-xl text-[9px] font-bold uppercase tracking-widest border whitespace-nowrap <?= $baris['role'] === 'admin' ? 'bg-lime-50 dark:bg-lime-950/20 text-lime-700 dark:text-lime-400 border-lime-200 dark:border-lime-900/30' : 'bg-slate-50 dark:bg-slate-950 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-800'; ?>">
                                <?php if($baris['role'] === 'admin'): ?>
                                    <i class="fa-solid fa-user-shield mr-1"></i>
                                <?php else: ?>
                                    <i class="fa-solid fa-user mr-1"></i>
                                <?php endif; ?>
                                <?= $baris['role']; ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs font-medium text-slate-400 dark:text-slate-500 whitespace-nowrap">
                            <i class="fa-regular fa-calendar-days mr-1.5 text-slate-400"></i><?= date('d M Y', strtotime($baris['tanggal_dibuat'])); ?>
                        </td>
                        <td class="px-4 py-3 pr-6 text-right whitespace-nowrap">
                            <?php if ($baris['role'] === 'user'): ?>
                                <a href="pengguna.php?hapus=<?= $baris['id']; ?>" onclick="return confirm('Hapus akun pengguna ini secara permanen?')" class="w-8 h-8 inline-flex items-center justify-center rounded-xl text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors" title="Hapus Pengguna">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-slate-300 dark:text-slate-600 text-xs italic">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-400 dark:text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-users-slash text-2xl mb-2.5 text-slate-300 dark:text-slate-700"></i>
                                <p class="text-xs">Belum ada pengguna terdaftar.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    </main>

<?php include 'bagian/bawah.php'; ?>