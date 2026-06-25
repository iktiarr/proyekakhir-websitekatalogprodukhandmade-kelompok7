<?php
/**
 * admin/bagian/atas.php
 * Komponen bersama untuk semua halaman admin:
 * HTML head, mobile header, sidebar navigasi, dan backdrop overlay.
 *
 * Variabel yang harus didefinisikan di pemanggil:
 *   $halaman_aktif   - string: 'dasbor' | 'produk' | 'pembayaran' | 'testimoni' | 'pengguna' | 'laporan'
 *   $judul_halaman   - string: judul tab browser (opsional, default: 'HandMadura Admin')
 *   $pembayaran_tertunda, $testimoni_tertunda, $laporan_tertunda - sudah di-query di pemanggil
 */

$judul_tab = isset($judul_halaman) ? $judul_halaman . ' - HandMadura Admin' : 'HandMadura Admin';
$halaman_aktif = $halaman_aktif ?? '';

// Helper untuk kelas nav item
function nav_kelas($nama, $aktif) {
    if ($nama === $aktif) {
        return 'flex items-center px-2.5 py-1.5 bg-lime-50 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400 rounded-xl font-bold text-xs';
    }
    return 'flex items-center px-2.5 py-1.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-bold text-xs transition-colors';
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $judul_tab ?></title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media (max-width: 767px) {
            #sidebar { transform: translateX(-100%) !important; transition: transform 0.3s ease-in-out !important; }
            #sidebar.active { transform: translateX(0) !important; }
        }
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
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 flex flex-col md:flex-row selection:bg-lime-200 selection:text-lime-900 transition-colors duration-300 min-h-screen">

    <!-- Header Seluler (Mobile Navbar) -->
    <header class="md:hidden bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-3 py-2.5 sticky top-0 z-40 flex items-center gap-3 w-full transition-colors duration-300">
        <button id="tombol-menu-mobile" class="p-1.5 -ml-1.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 focus:outline-none flex items-center justify-center cursor-pointer">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
        <a href="../index.php" class="text-lg font-extrabold text-slate-800 dark:text-slate-200 tracking-tight">
            Hand<span class="text-lime-600">Madura.</span>
        </a>
    </header>

    <!-- Sidebar Navigasi -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-56 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col transition-all duration-300 md:sticky md:h-screen md:top-0 overflow-y-auto flex-shrink-0 shadow-lg md:shadow-none">
        <div class="p-3 pb-2 flex items-center justify-between">
            <div>
                <a href="../index.php" class="text-lg font-extrabold text-slate-800 dark:text-slate-200 tracking-tight inline-block">
                    Hand<span class="text-lime-600">Madura.</span>
                </a>
                <p class="text-[8px] uppercase tracking-widest text-slate-400 dark:text-slate-500 font-bold mt-0.5">Admin Panel</p>
            </div>
            <button id="tombol-tutup-sidebar" class="md:hidden p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer flex items-center justify-center" title="Tutup Sidebar">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <nav class="flex-1 px-2 space-y-0.5">
            <a href="index.php" class="<?= nav_kelas('dasbor', $halaman_aktif) ?>">
                <i class="fa-solid fa-chart-pie mr-2 w-4 text-center"></i> Dasbor
            </a>
            <a href="produk.php" class="<?= nav_kelas('produk', $halaman_aktif) ?>">
                <i class="fa-solid fa-box-open mr-2 w-4 text-center"></i> Produk
            </a>
            <a href="pembayaran.php" class="<?= nav_kelas('pembayaran', $halaman_aktif) ?>">
                <i class="fa-solid fa-credit-card mr-2 w-4 text-center"></i> Pembayaran
                <?php if (($pembayaran_tertunda ?? 0) > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full"><?= $pembayaran_tertunda ?></span>
                <?php endif; ?>
            </a>
            <a href="testimoni.php" class="<?= nav_kelas('testimoni', $halaman_aktif) ?>">
                <i class="fa-solid fa-comments mr-2 w-4 text-center"></i> Testimonial
                <?php if (($testimoni_tertunda ?? 0) > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full"><?= $testimoni_tertunda ?></span>
                <?php endif; ?>
            </a>
            <a href="pengguna.php" class="<?= nav_kelas('pengguna', $halaman_aktif) ?>">
                <i class="fa-solid fa-users mr-2 w-4 text-center"></i> Pengguna
            </a>
            <a href="laporan.php" class="<?= nav_kelas('laporan', $halaman_aktif) ?>">
                <i class="fa-solid fa-circle-exclamation mr-2 w-4 text-center"></i> Laporan Kendala
                <?php if (($laporan_tertunda ?? 0) > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full"><?= $laporan_tertunda ?></span>
                <?php endif; ?>
            </a>
        </nav>

        <div class="p-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-1">
            <a href="../keluar.php?dari=admin" class="flex items-center px-2.5 py-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl font-bold text-xs transition-colors flex-grow">
                <i class="fa-solid fa-arrow-right-from-bracket mr-2 w-4 text-center"></i> Keluar
            </a>
            <button id="tombol-tema" class="text-slate-400 hover:text-lime-600 dark:hover:text-lime-400 p-1.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer flex items-center justify-center" title="Ubah Tema">
                <i id="ikon-tombol-tema" class="fa-solid fa-moon text-base"></i>
            </button>
        </div>
    </aside>

    <!-- Backdrop Overlay Seluler -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 opacity-0 pointer-events-none transition-opacity duration-300"></div>
