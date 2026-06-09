<?php
include_once '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

// Menghitung jumlah pembayaran/testimoni tertunda untuk sidebar badge
$pembayaran_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pesanan WHERE status = 'dibayar'"))['total'];
$testimoni_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM testimonial WHERE status = 'pending'"))['total'];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Laporan Admin - Dasbor Admin</title>
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
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 flex flex-col md:flex-row selection:bg-lime-200 selection:text-lime-900 transition-colors duration-300 min-h-screen">
    
    <!-- Mobile Header -->
    <header class="md:hidden bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 py-3 sticky top-0 z-40 flex items-center gap-3 w-full transition-colors duration-300">
        <button id="tombol-menu-mobile" class="p-2 -ml-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 transition-colors focus:outline-none flex items-center justify-center cursor-pointer">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
        <a href="../index.php" class="text-lg font-extrabold text-slate-800 dark:text-slate-200 tracking-tight">
            Hand<span class="text-lime-600">Madura.</span>
        </a>
    </header>

    <!-- Sidebar Navigation -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col transition-transform duration-300 transform -translate-x-full md:translate-x-0 md:sticky md:h-screen md:top-0 overflow-y-auto flex-shrink-0">
        <div class="p-5 pb-3 flex items-center justify-between">
            <div>
                <a href="../index.php" class="text-xl font-extrabold text-slate-800 dark:text-slate-200 tracking-tight inline-block">
                    Hand<span class="text-lime-600">Madura.</span>
                </a>
                <p class="text-[9px] uppercase tracking-widest text-slate-400 dark:text-slate-550 font-bold mt-0.5">Admin Panel</p>
            </div>
            <button id="tombol-tutup-sidebar" class="md:hidden p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer flex items-center justify-center">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        
        <nav class="flex-1 px-3 space-y-1">
            <a href="index.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-medium text-sm transition-colors group">
                <i class="fa-solid fa-chart-pie mr-2.5 w-4 text-center"></i> Dasbor
            </a>
            <a href="produk.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-medium text-sm transition-colors group">
                <i class="fa-solid fa-box-open mr-2.5 w-4 text-center"></i> Produk
            </a>
            <a href="pembayaran.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-medium text-sm transition-colors group">
                <i class="fa-solid fa-credit-card mr-2.5 w-4 text-center"></i> Pembayaran
                <?php if ($pembayaran_tertunda > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full"><?= $pembayaran_tertunda; ?></span>
                <?php endif; ?>
            </a>
            <a href="testimoni.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-medium text-sm transition-colors group">
                <i class="fa-solid fa-comments mr-2.5 w-4 text-center"></i> Testimonial
                <?php if ($testimoni_tertunda > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full"><?= $testimoni_tertunda; ?></span>
                <?php endif; ?>
            </a>
            <a href="pengguna.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-medium text-sm transition-colors group">
                <i class="fa-solid fa-users mr-2.5 w-4 text-center"></i> Pengguna
            </a>
            <a href="laporan.php" class="flex items-center px-3.5 py-2.5 bg-lime-50 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400 rounded-xl font-bold text-sm transition-colors">
                <i class="fa-solid fa-file-invoice mr-2.5 w-4 text-center"></i> Laporan
            </a>
        </nav>
        
        <div class="p-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-1">
            <a href="../keluar.php" class="flex items-center px-3.5 py-2.5 text-slate-400 dark:text-slate-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl font-bold text-sm transition-colors group flex-grow">
                <i class="fa-solid fa-arrow-right-from-bracket mr-2.5 w-4 text-center"></i> Keluar
            </a>
            <button id="tombol-tema" class="text-slate-400 hover:text-lime-600 dark:text-slate-400 dark:hover:text-lime-400 p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer flex items-center justify-center">
                <i id="ikon-tombol-tema" class="fa-solid fa-moon text-base"></i>
            </button>
        </div>
    </aside>

    <!-- Sidebar Backdrop -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0"></div>

    <!-- Main Content -->
    <main class="flex-grow p-4 sm:p-6 w-full max-w-7xl mx-auto overflow-x-hidden">
        
        <!-- Header Page -->
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Ekspor Laporan PDF</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1.5 text-sm">Ekspor daftar pengguna, ulasan testimonial, dan laporan keuangan toko secara dinamis ke berkas PDF list.</p>
        </div>

        <!-- Grid Opsi Laporan -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            
            <!-- Card 1: Laporan Daftar Pengguna -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow duration-300">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-lime-50 dark:bg-lime-950/40 text-lime-600 dark:text-lime-400 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-users text-lg"></i>
                        </div>
                        <h2 class="text-base font-extrabold text-slate-800 dark:text-slate-100">Daftar Pengguna</h2>
                    </div>
                    <p class="text-xs text-slate-550 dark:text-slate-400 leading-relaxed mb-6">
                        Menghasilkan laporan lengkap berisi data seluruh pengguna yang terdaftar di sistem HandMadura, termasuk informasi kontak, alamat, dan peran (role) mereka.
                    </p>
                </div>
                <div>
                    <div class="bg-slate-50 dark:bg-slate-950/60 p-3.5 rounded-xl border border-slate-100 dark:border-slate-900 text-[10px] text-slate-500 dark:text-slate-400 space-y-1.5 mb-6">
                        <span class="font-extrabold text-slate-600 dark:text-slate-350 block"><i class="fa-solid fa-circle-info mr-1"></i> Format Output:</span>
                        <p>Tabel Daftar PDF teratur yang siap cetak (kertas A4 / Simpan ke PDF).</p>
                    </div>
                    <a href="cetak_laporan.php?tipe=pengguna" target="_blank" class="w-full inline-flex items-center justify-center bg-lime-600 hover:bg-lime-700 dark:bg-lime-700 dark:hover:bg-lime-800 text-white py-3 rounded-xl text-xs font-bold transition-all duration-300 hover:translate-y-[-1px] cursor-pointer">
                        <i class="fa-solid fa-file-pdf mr-2"></i> Ekspor ke PDF
                    </a>
                </div>
            </div>

            <!-- Card 2: Laporan Daftar Ulasan -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow duration-300">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-comments text-lg"></i>
                        </div>
                        <h2 class="text-base font-extrabold text-slate-800 dark:text-slate-100">Daftar Ulasan</h2>
                    </div>
                    <p class="text-xs text-slate-550 dark:text-slate-400 leading-relaxed mb-6">
                        Menghasilkan laporan daftar testimoni dan ulasan dari pelanggan mengenai produk handmade kami, lengkap dengan rating dan status ulasan.
                    </p>
                </div>
                <div>
                    <div class="bg-slate-50 dark:bg-slate-950/60 p-3.5 rounded-xl border border-slate-100 dark:border-slate-900 text-[10px] text-slate-500 dark:text-slate-400 space-y-1.5 mb-6">
                        <span class="font-extrabold text-slate-600 dark:text-slate-350 block"><i class="fa-solid fa-circle-info mr-1"></i> Format Output:</span>
                        <p>Tabel Daftar PDF teratur yang siap cetak (kertas A4 / Simpan ke PDF).</p>
                    </div>
                    <a href="cetak_laporan.php?tipe=ulasan" target="_blank" class="w-full inline-flex items-center justify-center bg-amber-500 hover:bg-amber-600 dark:bg-amber-600 dark:hover:bg-amber-700 text-white py-3 rounded-xl text-xs font-bold transition-all duration-300 hover:translate-y-[-1px] cursor-pointer">
                        <i class="fa-solid fa-file-pdf mr-2"></i> Ekspor ke PDF
                    </a>
                </div>
            </div>

            <!-- Card 3: Laporan Keuangan (Total Keseluruhan) -->
            <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow duration-300">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 rounded-xl flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-wallet text-lg"></i>
                        </div>
                        <h2 class="text-base font-extrabold text-slate-800 dark:text-slate-100">Laporan Keuangan</h2>
                    </div>
                    <p class="text-xs text-slate-550 dark:text-slate-400 leading-relaxed mb-6">
                        Menghasilkan satu laporan komprehensif berisi daftar transaksi yang sukses dari awal berdirinya toko hingga saat ini, beserta total akumulasi pendapatan.
                    </p>
                </div>
                <div>
                    <div class="bg-slate-50 dark:bg-slate-950/60 p-3.5 rounded-xl border border-slate-100 dark:border-slate-900 text-[10px] text-slate-500 dark:text-slate-400 space-y-1.5 mb-6">
                        <span class="font-extrabold text-slate-600 dark:text-slate-350 block"><i class="fa-solid fa-circle-info mr-1"></i> Format Output:</span>
                        <p>Tabel Daftar Keuangan & Total Pendapatan yang siap cetak (A4 / Simpan PDF).</p>
                    </div>
                    <a href="cetak_laporan.php?tipe=keuangan" target="_blank" class="w-full inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 dark:bg-blue-700 dark:hover:bg-blue-800 text-white py-3 rounded-xl text-xs font-bold transition-all duration-300 hover:translate-y-[-1px] cursor-pointer">
                        <i class="fa-solid fa-file-pdf mr-2"></i> Ekspor ke PDF
                    </a>
                </div>
            </div>

        </div>

    </main>

    <!-- Theme and Mobile Sidebar Script -->
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

        // Mobile Sidebar Controls
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        const tombolMenuMobile = document.getElementById('tombol-menu-mobile');
        const tombolTutupSidebar = document.getElementById('tombol-tutup-sidebar');

        function bukaSidebar() {
            if (sidebar && backdrop) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.add('opacity-100');
                }, 10);
                document.body.style.overflow = 'hidden';
            }
        }

        function tutupSidebar() {
            if (sidebar && backdrop) {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.remove('opacity-100');
                setTimeout(() => {
                    backdrop.classList.add('hidden');
                }, 300);
                document.body.style.overflow = '';
            }
        }

        if (tombolMenuMobile) tombolMenuMobile.addEventListener('click', bukaSidebar);
        if (tombolTutupSidebar) tombolTutupSidebar.addEventListener('click', tutupSidebar);
        if (backdrop) backdrop.addEventListener('click', tutupSidebar);
    });
    </script>
</body>
</html>
