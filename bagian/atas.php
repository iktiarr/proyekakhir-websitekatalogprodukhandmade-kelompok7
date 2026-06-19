<?php
/**
 * Bagian Header Atas
 * Berfungsi untuk menginisialisasi sesi, mendeteksi role pengguna, 
 * dan memuat markup HTML/CSS serta navigasi bar.
 */
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$sudah_masuk = isset($_SESSION['user']['id']);
$adalah_admin = isset($_SESSION['admin']['role']) && $_SESSION['admin']['role'] === 'admin';

$nama_depan_pengguna = '';
if ($sudah_masuk && isset($_SESSION['user']['nama'])) {
    $bagian_nama = explode(' ', trim($_SESSION['user']['nama']));
    $nama_depan_pengguna = $bagian_nama[0];
}

if (!isset($awalan)) {
    $awalan = '';
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>HandMadura - Katalog Kerajinan Autentik</title>
    <!-- Google Fonts & Tailwind CSS CDN -->
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
        // Deteksi preferensi warna gelap dari localStorage atau sistem
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 selection:bg-lime-200 selection:text-lime-900">
    
    <!-- Navigation Bar Utama -->
    <nav class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md sticky top-0 z-50 border-b border-slate-200 dark:border-slate-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                
                <!-- Logo Aplikasi -->
                <div class="flex items-center flex-shrink-0">
                    <a href="<?= $awalan; ?>index.php" class="text-2xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">
                        Hand<span class="text-lime-600">Madura.</span>
                    </a>
                </div>
                
                <!-- Menu Navigasi Desktop -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="<?= $awalan; ?>index.php" class="text-slate-500 dark:text-slate-400 hover:text-lime-600 dark:hover:text-lime-400 font-semibold text-sm">Beranda</a>
                    <a href="<?= $awalan; ?>halaman/katalog.php" class="text-slate-500 dark:text-slate-400 hover:text-lime-600 dark:hover:text-lime-400 font-semibold text-sm">Katalog</a>
                    <?php if ($sudah_masuk): ?>
                        <a href="<?= $awalan; ?>halaman/riwayat.php" class="text-slate-500 dark:text-slate-400 hover:text-lime-600 dark:hover:text-lime-400 font-semibold text-sm">Riwayat</a>
                        <a href="<?= $awalan; ?>halaman/profil.php" class="text-slate-500 dark:text-slate-400 hover:text-lime-600 dark:hover:text-lime-400 font-semibold text-sm">Profil</a>
                    <?php endif; ?>
                </div>

                <!-- Kontrol Akun & Mode Malam -->
                <div class="flex items-center space-x-3 sm:space-x-4">
                    <!-- Tombol Ubah Tema -->
                    <button id="tombol-tema" class="text-slate-400 hover:text-lime-600 dark:text-slate-400 dark:hover:text-lime-400 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer flex items-center justify-center" title="Ubah Tema">
                        <i id="ikon-tombol-tema" class="fa-solid fa-moon text-lg"></i>
                    </button>

                    <?php if ($sudah_masuk): ?>
                        
                        <div class="hidden sm:flex items-center text-sm text-slate-600 dark:text-slate-400">
                            Halo, <a href="<?= $awalan; ?>halaman/profil.php" class="font-bold text-slate-800 dark:text-slate-200 hover:text-lime-600 dark:hover:text-lime-400 ml-1"><?= $nama_depan_pengguna; ?></a>!
                        </div>

                        <?php if ($sudah_masuk): ?>
                        <!-- Icon Keranjang Belanja -->
                        <a href="<?= $awalan; ?>halaman/keranjang.php" class="relative text-slate-400 hover:text-lime-600 dark:text-slate-400 dark:hover:text-lime-400 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800">
                            <i class="fa-solid fa-cart-shopping text-lg"></i>
                        </a>
                        <?php endif; ?>
                        
                        <div class="h-6 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block mx-1"></div>
                        
                        <!-- Tombol Keluar Akun -->
                        <a href="<?= $awalan; ?>keluar.php" class="flex items-center text-slate-400 hover:text-red-500 dark:text-slate-400 dark:hover:text-red-400 p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-950/30" title="Keluar">
                            <i class="fa-solid fa-arrow-right-from-bracket text-lg"></i>
                            <span class="hidden sm:inline-block ml-2 text-sm font-bold">Keluar</span>
                        </a>
                        
                    <?php else: ?>
                        
                        <!-- Menu Pengunjung Non-Login -->
                        <a href="<?= $awalan; ?>masuk.php" class="hidden sm:inline-block text-slate-600 dark:text-slate-350 hover:text-lime-600 dark:hover:text-lime-400 font-bold text-sm">Masuk</a>
                        <a href="<?= $awalan; ?>daftar.php" class="hidden sm:inline-block bg-lime-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-lime-700">Daftar</a>
                        
                    <?php endif; ?>

                    <!-- Tombol Menu Seluler (Burger) -->
                    <button id="tombol-menu-seluler" class="md:hidden text-slate-500 hover:text-lime-600 dark:text-slate-400 dark:hover:text-lime-400 p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer flex items-center justify-center" aria-expanded="false" aria-label="Menu Utama">
                        <i id="ikon-menu-seluler" class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>
                
            </div>
        </div>

        <!-- Menu Seluler Dropdown -->
        <div id="menu-seluler" class="hidden md:hidden border-t border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900">
            <div class="px-4 pt-3 pb-4 space-y-2">
                <a href="<?= $awalan; ?>index.php" class="block px-3 py-2.5 rounded-xl text-base font-semibold text-slate-600 dark:text-slate-350 hover:bg-slate-100 dark:hover:bg-slate-800/50 hover:text-lime-600 dark:hover:text-lime-400">Beranda</a>
                <a href="<?= $awalan; ?>halaman/katalog.php" class="block px-3 py-2.5 rounded-xl text-base font-semibold text-slate-600 dark:text-slate-350 hover:bg-slate-100 dark:hover:bg-slate-800/50 hover:text-lime-600 dark:hover:text-lime-400">Katalog</a>
                <?php if ($sudah_masuk): ?>
                    <a href="<?= $awalan; ?>halaman/riwayat.php" class="block px-3 py-2.5 rounded-xl text-base font-semibold text-slate-600 dark:text-slate-350 hover:bg-slate-100 dark:hover:bg-slate-800/50 hover:text-lime-600 dark:hover:text-lime-400">Riwayat Transaksi</a>
                    <a href="<?= $awalan; ?>halaman/profil.php" class="block px-3 py-2.5 rounded-xl text-base font-semibold text-slate-600 dark:text-slate-350 hover:bg-slate-100 dark:hover:bg-slate-800/50 hover:text-lime-600 dark:hover:text-lime-400">Profil Saya</a>
                <?php endif; ?>
                
                <?php if ($sudah_masuk): ?>
                    <!-- Profil Pengguna Seluler -->
                    <div class="pt-4 mt-2 border-t border-slate-200 dark:border-slate-800 sm:hidden">
                        <a href="<?= $awalan; ?>halaman/profil.php" class="flex items-center px-3 py-2 hover:bg-slate-100 dark:hover:bg-slate-800/50 rounded-xl">
                            <div class="flex-shrink-0 bg-lime-100 dark:bg-lime-950 text-lime-600 dark:text-lime-400 w-10 h-10 rounded-full flex items-center justify-center font-bold">
                                <?= strtoupper(substr($nama_depan_pengguna, 0, 1)); ?>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-bold text-slate-800 dark:text-slate-200">Halo, <?= $nama_depan_pengguna; ?>!</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">Ubah Profil</div>
                            </div>
                        </a>
                    </div>
                <?php else: ?>
                    <!-- Tombol Masuk/Daftar Seluler -->
                    <div class="pt-4 mt-2 border-t border-slate-200 dark:border-slate-800 flex flex-col space-y-2 sm:hidden">
                        <a href="<?= $awalan; ?>masuk.php" class="w-full text-center py-2.5 rounded-xl text-base font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800/50">Masuk</a>
                        <a href="<?= $awalan; ?>daftar.php" class="w-full text-center bg-lime-600 hover:bg-lime-700 text-white py-2.5 rounded-xl text-base font-bold">Daftar</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <main class="min-h-screen">
