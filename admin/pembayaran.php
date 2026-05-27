<?php
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id_pesanan = (int)$_GET['id'];
    $status = mysqli_real_escape_string($koneksi, $_GET['status']);
    
    $res_o = mysqli_query($koneksi, "SELECT p.*, u.nama FROM pesanan p JOIN pengguna u ON p.id_pengguna = u.id WHERE p.id = $id_pesanan");
    if ($row_o = mysqli_fetch_assoc($res_o)) {
        $nama_pembeli = mysqli_real_escape_string($koneksi, $row_o['nama']);
        if (mysqli_query($koneksi, "UPDATE pesanan SET status='$status' WHERE id=$id_pesanan")) {
            $nama_admin = mysqli_real_escape_string($koneksi, $_SESSION['nama']);
            $id_admin = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NULL';
            
            $status_keterangan = [
                'dibayar' => 'Mengonfirmasi pembayaran pesanan',
                'dikirim' => 'Mengirim produk untuk pesanan',
                'selesai' => 'Menyelesaikan pesanan',
                'dibatalkan' => 'Membatalkan pesanan'
            ];
            $tag = "#HM-" . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT);
            $ket_aksi = isset($status_keterangan[$status]) ? $status_keterangan[$status] : "Mengubah status pesanan menjadi '$status'";
            $ket_log = "$ket_aksi $tag dari '$nama_pembeli'";
            
            mysqli_query($koneksi, "INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan) VALUES ($id_admin, '$nama_admin', 'pesanan', '$status', '$ket_log')");
        }
    }
    
    header("Location: pembayaran.php");
    exit();
}

$kueri_pesanan = mysqli_query($koneksi, "SELECT p.*, u.nama FROM pesanan p JOIN pengguna u ON p.id_pengguna = u.id ORDER BY p.tanggal_pesanan DESC");
$pembayaran_tertunda = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan WHERE status = 'dibayar'"))['total'];
$testimoni_tertunda = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM testimonial WHERE status = 'pending'"))['total'];
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verifikasi Pembayaran - HandMadura Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
    <style type="text/tailwindcss">
        @import "tailwindcss";
        @custom-variant dark (&:where(.dark, .dark *));
    </style>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 flex selection:bg-lime-200 selection:text-lime-900 transition-colors duration-300 min-h-screen">
    
    <aside class="w-56 bg-white dark:bg-slate-900 h-screen border-r border-slate-200 dark:border-slate-800 flex flex-col sticky top-0 z-10 transition-colors duration-300 overflow-y-auto">
        <div class="p-5 pb-3">
            <a href="../index.php" class="text-xl font-extrabold text-slate-800 dark:text-slate-200 tracking-tight inline-block hover:scale-105 transition-transform">
                Hand<span class="text-lime-600">Madura.</span>
            </a>
            <p class="text-[9px] uppercase tracking-widest text-slate-400 dark:text-slate-500 font-bold mt-0.5">Admin Panel</p>
        </div>
        
        <nav class="flex-1 px-3 space-y-1">
            <a href="index.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-medium text-sm transition-colors group">
                <i class="fa-solid fa-chart-pie mr-2.5 w-4 text-center group-hover:scale-110 transition-transform"></i> Dasbor
            </a>
            <a href="produk.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-medium text-sm transition-colors group">
                <i class="fa-solid fa-box-open mr-2.5 w-4 text-center group-hover:scale-110 transition-transform"></i> Produk
            </a>
            <a href="pembayaran.php" class="flex items-center px-3.5 py-2.5 bg-lime-50 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400 rounded-xl font-bold text-sm transition-colors">
                <i class="fa-solid fa-credit-card mr-2.5 w-4 text-center"></i> Pembayaran
                <?php if ($pembayaran_tertunda > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full"><?= $pembayaran_tertunda; ?></span>
                <?php endif; ?>
            </a>
            <a href="testimoni.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-medium text-sm transition-colors group">
                <i class="fa-solid fa-comments mr-2.5 w-4 text-center group-hover:scale-110 transition-transform"></i> Testimonial
                <?php if ($testimoni_tertunda > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full"><?= $testimoni_tertunda; ?></span>
                <?php endif; ?>
            </a>
            <a href="pengguna.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-medium text-sm transition-colors group">
                <i class="fa-solid fa-users mr-2.5 w-4 text-center group-hover:scale-110 transition-transform"></i> Pengguna
            </a>
        </nav>
        
        <div class="p-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-1">
            <a href="../keluar.php" class="flex items-center px-3.5 py-2.5 text-slate-400 dark:text-slate-500 hover:text-red-650 hover:bg-red-50 dark:hover:bg-red-950/20 rounded-xl font-bold text-sm transition-colors group flex-grow">
                <i class="fa-solid fa-arrow-right-from-bracket mr-2.5 w-4 text-center group-hover:-translate-x-0.5 transition-transform"></i> Keluar
            </a>
            <button id="tombol-tema" class="text-slate-400 hover:text-lime-600 dark:text-slate-400 dark:hover:text-lime-400 p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer flex items-center justify-center" title="Ubah Tema">
                <i id="ikon-tombol-tema" class="fa-solid fa-moon text-base"></i>
            </button>
        </div>
    </aside>

    <main class="flex-grow p-5 sm:p-6 max-w-7xl">
        
        <div class="mb-6">
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">Verifikasi Pembayaran</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Tinjau bukti konfirmasi dari pelanggan dan perbarui status pesanan.</p>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm transition-colors duration-300">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-400 dark:text-slate-500 text-[10px] uppercase tracking-wider font-bold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 pl-6">ID Pesanan</th>
                        <th class="px-4 py-3.5">Pelanggan</th>
                        <th class="px-4 py-3.5">Total Tagihan</th>
                        <th class="px-4 py-3.5">Konfirmasi Akun/Rekening Pengirim</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-4 py-3.5 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/60">
                    <?php 
                    if(mysqli_num_rows($kueri_pesanan) > 0):
                        while($baris = mysqli_fetch_assoc($kueri_pesanan)): 
                            $warna_status = [
                                'menunggu'   => 'bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-900/30',
                                'dibayar'    => 'bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-900/30',
                                'dikirim'    => 'bg-indigo-50 dark:bg-indigo-950/20 text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-900/30',
                                'selesai'    => 'bg-lime-50 dark:bg-lime-950/20 text-lime-700 dark:text-lime-400 border-lime-200 dark:border-lime-900/30',
                                'dibatalkan' => 'bg-red-50 dark:bg-red-950/20 text-red-650 dark:text-red-400 border-red-200 dark:border-red-900/30'
                            ];
                    ?>
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 text-xs sm:text-sm transition-colors duration-200">
                        <td class="px-4 py-3 pl-6 font-mono text-[11px] font-bold text-slate-400 dark:text-slate-500">
                            #HM-<?= str_pad($baris['id'], 5, '0', STR_PAD_LEFT); ?>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-bold text-slate-800 dark:text-slate-200"><?= $baris['nama']; ?></span>
                        </td>
                        <td class="px-4 py-3 font-extrabold text-slate-800 dark:text-slate-200 whitespace-nowrap">
                            Rp <?= number_format($baris['total_harga'], 0, ',', '.'); ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php if ($baris['bukti_pembayaran']): ?>
                                <span class="inline-flex items-center text-lime-700 dark:text-lime-400 font-bold text-[10px] bg-lime-50 dark:bg-lime-950/40 px-2 py-1 rounded-xl border border-lime-100 dark:border-lime-900/40 whitespace-nowrap">
                                    <i class="fa-solid fa-file-invoice-dollar mr-1 text-lime-600 dark:text-lime-450"></i> <?= $baris['bukti_pembayaran']; ?>
                                </span>
                            <?php else: ?>
                                <span class="text-slate-350 dark:text-slate-600 italic text-[11px] whitespace-nowrap">Belum Konfirmasi</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-2 py-0.5 rounded-xl text-[9px] font-bold uppercase tracking-wider border whitespace-nowrap <?= $warna_status[$baris['status']] ?? 'bg-slate-50 dark:bg-slate-900 text-slate-400 dark:text-slate-500 border-slate-200 dark:border-slate-800'; ?>">
                                <?= $baris['status']; ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 pr-6 text-right">
                            <div class="relative inline-block text-left group/menu">
                                <button class="p-1.5 w-7 h-7 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none cursor-pointer">
                                    <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                </button>
                                
                                <div class="absolute right-0 w-44 mt-1 bg-white dark:bg-slate-900 rounded-xl shadow-lg dark:shadow-none border border-slate-100 dark:border-slate-850 opacity-0 invisible group-hover/menu:opacity-100 group-hover/menu:visible group-hover/menu:-translate-y-1 transition-all duration-200 z-50 overflow-hidden transform origin-top-right">
                                    <div class="py-1 text-left">
                                        <p class="px-4 py-2 text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest border-b border-slate-50 dark:border-slate-850">Ubah Status</p>
                                        <a href="pembayaran.php?id=<?= $baris['id']; ?>&status=dibayar" class="block px-4 py-2 text-xs font-bold text-blue-650 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/30 transition-colors">
                                            <i class="fa-solid fa-check w-4"></i> Konfirmasi Bayar
                                        </a>
                                        <a href="pembayaran.php?id=<?= $baris['id']; ?>&status=dikirim" class="block px-4 py-2 text-xs font-bold text-indigo-655 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 transition-colors">
                                            <i class="fa-solid fa-truck-fast w-4"></i> Kirim Produk
                                        </a>
                                        <a href="pembayaran.php?id=<?= $baris['id']; ?>&status=selesai" class="block px-4 py-2 text-xs font-bold text-lime-650 dark:text-lime-400 hover:bg-lime-50 dark:hover:bg-lime-950/30 transition-colors">
                                            <i class="fa-solid fa-flag-checkered w-4"></i> Selesaikan
                                        </a>
                                        <div class="h-px bg-slate-55 dark:bg-slate-800 my-1"></div>
                                        <a href="pembayaran.php?id=<?= $baris['id']; ?>&status=dibatalkan" class="block px-4 py-2 text-xs font-bold text-red-655 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
                                            <i class="fa-solid fa-xmark w-4"></i> Batalkan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-400 dark:text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-inbox text-2xl mb-2.5 text-slate-350 dark:text-slate-700"></i>
                                <p class="text-xs">Belum ada pesanan masuk.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const tombolTema = document.getElementById('tombol-tema');
        const ikonTema = document.getElementById('ikon-tombol-tema');

        function perbaruiIkon() {
            if (document.documentElement.classList.contains('dark')) {
                if (ikonTema) {
                    ikonTema.classList.replace('fa-moon', 'fa-sun');
                }
            } else {
                if (ikonTema) {
                    ikonTema.classList.replace('fa-sun', 'fa-moon');
                }
            }
        }

        perbaruiIkon();

        if (tombolTema) {
            tombolTema.addEventListener('click', () => {
                if (ikonTema) {
                    ikonTema.style.transform = 'rotate(360deg)';
                }
                
                setTimeout(() => {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    }
                    perbaruiIkon();
                    if (ikonTema) {
                        ikonTema.style.transform = '';
                    }
                }, 150);
            });
        }
    });
    </script>
</body>
</html>