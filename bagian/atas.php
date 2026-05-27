<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$sudah_masuk = isset($_SESSION['user_id']);
$adalah_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

if ($adalah_admin && basename($_SERVER['PHP_SELF']) !== 'index.php' && !str_contains($_SERVER['PHP_SELF'], 'admin/')) {
    header("Location: " . ($awalan ?? '') . "admin/index.php");
    exit();
}

$nama_depan_pengguna = '';
if ($sudah_masuk && isset($_SESSION['nama'])) {
    $bagian_nama = explode(' ', trim($_SESSION['nama']));
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
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 selection:bg-lime-200 selection:text-lime-900 transition-colors duration-300">
    
    <nav class="bg-white/90 dark:bg-slate-900/90 backdrop-blur-lg sticky top-0 z-50 border-b border-slate-150 dark:border-slate-800 shadow-sm transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                
                <div class="flex items-center flex-shrink-0">
                    <a href="<?= $adalah_admin ? $awalan . 'admin/index.php' : $awalan . 'index.php'; ?>" class="text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight transition-transform hover:scale-105">
                        Hand<span class="text-lime-600">Madura.</span>
                    </a>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <?php if ($adalah_admin): ?>
                        <a href="<?= $awalan; ?>admin/index.php" class="bg-lime-600 text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-lime-700 hover:shadow-lg hover:shadow-lime-200/50 transition-all duration-300 flex items-center gap-2">
                            <i class="fa-solid fa-user-shield text-xs"></i> Dasbor Admin
                        </a>
                    <?php else: ?>
                        <a href="<?= $awalan; ?>index.php" class="text-slate-500 dark:text-slate-400 hover:text-lime-600 dark:hover:text-lime-400 transition-colors font-semibold text-sm">Beranda</a>
                        <a href="<?= $awalan; ?>halaman/katalog.php" class="text-slate-500 dark:text-slate-400 hover:text-lime-600 dark:hover:text-lime-400 transition-colors font-semibold text-sm">Katalog</a>
                        <?php if ($sudah_masuk): ?>
                            <a href="<?= $awalan; ?>halaman/riwayat.php" class="text-slate-500 dark:text-slate-400 hover:text-lime-600 dark:hover:text-lime-400 transition-colors font-semibold text-sm">Riwayat Transaksi</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <div class="flex items-center space-x-3 sm:space-x-4">
                    <button id="tombol-tema" class="text-slate-400 hover:text-lime-600 dark:text-slate-400 dark:hover:text-lime-400 p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 transition-all duration-300 cursor-pointer flex items-center justify-center" title="Ubah Tema">
                        <i id="ikon-tombol-tema" class="fa-solid fa-moon text-lg transition-transform duration-300"></i>
                    </button>

                    <?php if ($sudah_masuk): ?>
                        
                        <div class="hidden sm:flex items-center text-sm text-slate-550 dark:text-slate-400">
                            Halo, <span class="font-bold text-slate-800 dark:text-slate-200 ml-1"><?= $nama_depan_pengguna; ?></span>!
                        </div>

                        <?php if (!$adalah_admin): ?>
                        <a href="<?= $awalan; ?>halaman/keranjang.php" class="relative text-slate-400 hover:text-lime-600 dark:text-slate-400 dark:hover:text-lime-400 transition-colors p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 group">
                            <i class="fa-solid fa-cart-shopping text-lg group-hover:scale-110 transition-transform"></i>
                        </a>
                        <?php endif; ?>
                        
                        <div class="h-6 w-px bg-slate-200 dark:bg-slate-800 hidden sm:block mx-1"></div>
                        
                        <a href="<?= $awalan; ?>keluar.php" class="flex items-center text-slate-400 hover:text-red-500 dark:text-slate-400 dark:hover:text-red-400 transition-colors p-2 rounded-full hover:bg-red-50 dark:hover:bg-red-950/30 group" title="Keluar">
                            <i class="fa-solid fa-arrow-right-from-bracket text-lg group-hover:translate-x-0.5 transition-transform"></i>
                            <span class="hidden sm:inline-block ml-2 text-sm font-bold group-hover:text-red-650 dark:group-hover:text-red-400">Keluar</span>
                        </a>
                        
                    <?php else: ?>
                        
                        <a href="<?= $awalan; ?>masuk.php" class="text-slate-650 dark:text-slate-300 hover:text-lime-600 dark:hover:text-lime-400 transition-colors font-bold text-sm">Masuk</a>
                        <a href="<?= $awalan; ?>daftar.php" class="bg-lime-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-lime-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-lime-200/50 transition-all duration-300">Daftar</a>
                        
                    <?php endif; ?>
                </div>
                
            </div>
        </div>
    </nav>
    
    <main class="min-h-screen">
