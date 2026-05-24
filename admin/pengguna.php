<?php
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

// Aksi hapus akun khusus untuk role = 'user'
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    // Proteksi: Cek role akun sebelum menghapus
    $check = mysqli_query($conn, "SELECT role FROM pengguna WHERE id = $id_hapus");
    $user_data = mysqli_fetch_assoc($check);
    
    if ($user_data && $user_data['role'] === 'user') {
        mysqli_query($conn, "DELETE FROM pengguna WHERE id = $id_hapus");
    }
    
    header("Location: pengguna.php");
    exit();
}

$query = mysqli_query($conn, "SELECT * FROM pengguna ORDER BY role ASC, nama ASC");
$pendingPayments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan WHERE status = 'dibayar'"))['total'];
$pendingTestimonial = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM testimonial WHERE status = 'pending'"))['total'];
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Pengguna - Handmade Admin</title>
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
            <a href="index.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-lg font-medium text-sm transition-colors group">
                <i class="fa-solid fa-chart-pie mr-2.5 w-4 text-center group-hover:scale-110 transition-transform"></i> Dasbor
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
            <a href="pengguna.php" class="flex items-center px-3.5 py-2.5 bg-lime-50 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400 rounded-lg font-bold text-sm transition-colors">
                <i class="fa-solid fa-users mr-2.5 w-4 text-center"></i> Pengguna
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
        
        <div class="mb-6">
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">Kelola Pengguna</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Daftar semua pengguna terdaftar dan administrator sistem.</p>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm transition-colors duration-300">
            <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-400 dark:text-slate-500 text-[10px] uppercase tracking-wider font-bold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 pl-6">Pengguna</th>
                        <th class="px-4 py-3.5">Email</th>
                        <th class="px-4 py-3.5">Role Akses</th>
                        <th class="px-4 py-3.5">Bergabung Pada</th>
                        <th class="px-4 py-3.5 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/60">
                    <?php 
                    if(mysqli_num_rows($query) > 0):
                        while($row = mysqli_fetch_assoc($query)): 
                    ?>
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 text-xs sm:text-sm transition-colors duration-200">
                        <td class="px-4 py-3 pl-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 <?= $row['role'] === 'admin' ? 'bg-lime-100 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400' : 'bg-slate-100 dark:bg-slate-950 text-slate-500 dark:text-slate-400'; ?> rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    <?= strtoupper(substr($row['nama'], 0, 1)); ?>
                                </div>
                                <span class="font-bold text-slate-800 dark:text-slate-200"><?= $row['nama']; ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            <?= $row['email']; ?>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-widest border whitespace-nowrap <?= $row['role'] === 'admin' ? 'bg-lime-50 dark:bg-lime-950/20 text-lime-700 dark:text-lime-400 border-lime-200 dark:border-lime-900/30' : 'bg-slate-50 dark:bg-slate-950 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-850'; ?>">
                                <?php if($row['role'] === 'admin'): ?>
                                    <i class="fa-solid fa-user-shield mr-1"></i>
                                <?php else: ?>
                                    <i class="fa-solid fa-user mr-1"></i>
                                <?php endif; ?>
                                <?= $row['role']; ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs font-medium text-slate-400 dark:text-slate-500 whitespace-nowrap">
                            <i class="fa-regular fa-calendar-days mr-1.5 text-slate-400"></i><?= date('d M Y', strtotime($row['tanggal_dibuat'])); ?>
                        </td>
                        <td class="px-4 py-3 pr-6 text-right whitespace-nowrap">
                            <?php if ($row['role'] === 'user'): ?>
                                <a href="pengguna.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Hapus akun pengguna ini secara permanen?')" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors" title="Hapus Pengguna">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-slate-350 dark:text-slate-600 text-xs italic">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-400 dark:text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-users-slash text-2xl mb-2.5 text-slate-350 dark:text-slate-700"></i>
                                <p class="text-xs">Belum ada pengguna terdaftar.</p>
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