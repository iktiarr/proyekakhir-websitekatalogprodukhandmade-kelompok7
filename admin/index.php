<?php
include '../koneksi.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}


$countUser = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengguna WHERE role = 'user'"))['total'];
$countAdmin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengguna WHERE role = 'admin'"))['total'];
$countProduct = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM produk"))['total'];
$totalSales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as total FROM pesanan WHERE status != 'menunggu'"))['total'];
$pendingPayments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan WHERE status = 'dibayar'"))['total'];
$pendingTestimonial = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM testimonial WHERE status = 'pending'"))['total'];


$admin_first_name = explode(' ', trim($_SESSION['nama']))[0];
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dasbor Admin - Handmade</title>
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
    
    <aside class="w-56 bg-white dark:bg-slate-900 min-h-screen border-r border-slate-200 dark:border-slate-800 flex flex-col sticky top-0 z-10 transition-colors duration-300">
        <div class="p-5 pb-3">
            <a href="../index.php" class="text-xl font-extrabold text-slate-800 dark:text-slate-200 tracking-tight inline-block hover:scale-105 transition-transform">
                Hand<span class="text-lime-600">made.</span>
            </a>
            <p class="text-[9px] uppercase tracking-widest text-slate-400 dark:text-slate-500 font-bold mt-0.5">Admin Panel</p>
        </div>
        
        <nav class="flex-1 px-3 space-y-1">
            <a href="index.php" class="flex items-center px-3.5 py-2.5 bg-lime-50 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400 rounded-lg font-bold text-sm transition-colors">
                <i class="fa-solid fa-chart-pie mr-2.5 w-4 text-center"></i> Dasbor
            </a>
            <a href="produk.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-lg font-medium text-sm transition-colors group">
                <i class="fa-solid fa-box-open mr-2.5 w-4 text-center group-hover:scale-110 transition-transform"></i> Produk
            </a>
            <a href="pembayaran.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-lg font-medium text-sm transition-colors group">
                <i class="fa-solid fa-credit-card mr-2.5 w-4 text-center group-hover:scale-110 transition-transform"></i> Pembayaran
                <?php if ($pendingPayments > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full"><?= $pendingPayments; ?></span>
                <?php endif; ?>
            </a>
            <a href="testimonial.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-lg font-medium text-sm transition-colors group">
                <i class="fa-solid fa-comments mr-2.5 w-4 text-center group-hover:scale-110 transition-transform"></i> Testimonial
                <?php if ($pendingTestimonial > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full"><?= $pendingTestimonial; ?></span>
                <?php endif; ?>
            </a>
            <a href="pengguna.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-lg font-medium text-sm transition-colors group">
                <i class="fa-solid fa-users mr-2.5 w-4 text-center group-hover:scale-110 transition-transform"></i> Pengguna
            </a>
        </nav>
        
        <div class="p-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-1">
            <a href="../logout.php" class="flex items-center px-3.5 py-2.5 text-slate-400 dark:text-slate-500 hover:text-red-650 hover:bg-red-50 dark:hover:bg-red-950/20 rounded-lg font-bold text-sm transition-colors group flex-grow">
                <i class="fa-solid fa-arrow-right-from-bracket mr-2.5 w-4 text-center group-hover:-translate-x-0.5 transition-transform"></i> Keluar
            </a>
            <button id="theme-toggle" class="text-slate-400 hover:text-lime-600 dark:text-slate-400 dark:hover:text-lime-400 p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer flex items-center justify-center" title="Ubah Tema">
                <i id="theme-toggle-icon" class="fa-solid fa-moon text-base"></i>
            </button>
        </div>
    </aside>

    <main class="flex-grow p-5 sm:p-6 max-w-7xl">
        
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">Dasbor Overview</h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Selamat datang kembali, <span class="font-bold text-slate-700 dark:text-slate-350"><?= $admin_first_name; ?></span>.</p>
            </div>
            <div class="flex items-center space-x-3">
                <div class="bg-white dark:bg-slate-900 px-3 py-2 rounded-lg border border-slate-200 dark:border-slate-800 flex items-center space-x-2.5">
                    <div class="w-7 h-7 bg-lime-100 dark:bg-lime-950/40 rounded-md flex items-center justify-center text-lime-600 dark:text-lime-400">
                        <i class="fa-solid fa-user-shield text-xs"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300">Administrator</span>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-xl border border-slate-200 dark:border-slate-800">
                <div class="w-10 h-10 bg-slate-100 dark:bg-slate-950 rounded-lg flex items-center justify-center text-slate-500 dark:text-slate-400 mb-3">
                    <i class="fa-solid fa-users text-lg"></i>
                </div>
                <p class="text-slate-400 dark:text-slate-500 text-[9px] font-bold uppercase tracking-widest mb-0.5">Total Pengguna</p>
                <h2 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100"><?= $countUser; ?></h2>
            </div>
            
            <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-xl border border-slate-200 dark:border-slate-800">
                <div class="w-10 h-10 bg-slate-100 dark:bg-slate-950 rounded-lg flex items-center justify-center text-slate-500 dark:text-slate-400 mb-3">
                    <i class="fa-solid fa-box-open text-lg"></i>
                </div>
                <p class="text-slate-400 dark:text-slate-500 text-[9px] font-bold uppercase tracking-widest mb-0.5">Total Produk</p>
                <h2 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100"><?= $countProduct; ?></h2>
            </div>
            
            <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-xl border border-slate-200 dark:border-slate-800">
                <div class="w-10 h-10 bg-slate-100 dark:bg-slate-950 rounded-lg flex items-center justify-center text-slate-500 dark:text-slate-400 mb-3">
                    <i class="fa-solid fa-wallet text-lg"></i>
                </div>
                <p class="text-slate-400 dark:text-slate-500 text-[9px] font-bold uppercase tracking-widest mb-0.5">Pendapatan Bersih</p>
                <h2 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">Rp <?= number_format($totalSales ?: 0, 0, ',', '.'); ?></h2>
            </div>
            
            <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-xl border border-slate-200 dark:border-slate-800">
                <div class="w-10 h-10 bg-slate-100 dark:bg-slate-950 rounded-lg flex items-center justify-center text-slate-500 dark:text-slate-400 mb-3">
                    <i class="fa-solid fa-user-tie text-lg"></i>
                </div>
                <p class="text-slate-400 dark:text-slate-500 text-[9px] font-bold uppercase tracking-widest mb-0.5">Total Admin</p>
                <h2 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100"><?= $countAdmin; ?></h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
                <div class="p-4 sm:p-5 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center">
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-100">Pesanan Terbaru</h3>
                    <a href="pembayaran.php" class="text-xs font-bold text-lime-600 dark:text-lime-400 hover:text-lime-700 dark:hover:text-lime-300 hover:underline">Lihat Semua</a>
                </div>
                <div class="p-4 sm:p-5 flex-grow">
                    <div class="space-y-3">
                        <?php
                        $latest = mysqli_query($conn, "SELECT p.*, u.nama FROM pesanan p JOIN pengguna u ON p.id_pengguna = u.id ORDER BY p.tanggal_pesanan DESC LIMIT 5");
                        if(mysqli_num_rows($latest) > 0):
                            while($row = mysqli_fetch_assoc($latest)):
                        ?>
                        <div class="flex items-center justify-between p-2.5 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800/80 rounded-lg cursor-default">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white dark:bg-slate-900 rounded-lg flex items-center justify-center text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-800 flex-shrink-0">
                                    <i class="fa-solid fa-bag-shopping text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200"><?= $row['nama']; ?></p>
                                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-0.5"><i class="fa-regular fa-clock mr-1"></i><?= date('H:i, d M Y', strtotime($row['tanggal_pesanan'])); ?></p>
                                </div>
                            </div>
                            <span class="text-xs font-extrabold text-slate-800 dark:text-slate-200">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></span>
                        </div>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                        <div class="text-center py-6">
                            <div class="w-10 h-10 bg-slate-50 dark:bg-slate-950 rounded-full flex items-center justify-center mx-auto mb-2.5 text-slate-300 dark:text-slate-700">
                                <i class="fa-solid fa-receipt text-base"></i>
                            </div>
                            <p class="text-slate-400 dark:text-slate-500 text-xs">Belum ada pesanan terbaru masuk.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleIcon = document.getElementById('theme-toggle-icon');

        function updateIcon() {
            if (document.documentElement.classList.contains('dark')) {
                if (themeToggleIcon) {
                    themeToggleIcon.classList.replace('fa-moon', 'fa-sun');
                }
            } else {
                if (themeToggleIcon) {
                    themeToggleIcon.classList.replace('fa-sun', 'fa-moon');
                }
            }
        }

        updateIcon();

        if (themeToggleBtn) {
            themeToggleBtn.addEventListener('click', () => {
                if (themeToggleIcon) {
                    themeToggleIcon.style.transform = 'rotate(360deg)';
                }
                
                setTimeout(() => {
                    if (document.documentElement.classList.contains('dark')) {
                        document.documentElement.classList.remove('dark');
                        localStorage.setItem('theme', 'light');
                    } else {
                        document.documentElement.classList.add('dark');
                        localStorage.setItem('theme', 'dark');
                    }
                    updateIcon();
                    if (themeToggleIcon) {
                        themeToggleIcon.style.transform = '';
                    }
                }, 150);
            });
        }
    });
    </script>
</body>
</html>