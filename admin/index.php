<?php
include '../koneksi.php';

// Cek Role Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

// Statistik
$countUser = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengguna WHERE role = 'user'"))['total'];
$countAdmin = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pengguna WHERE role = 'admin'"))['total'];
$countProduct = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM produk"))['total'];
$totalSales = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_harga) as total FROM pesanan WHERE status != 'menunggu'"))['total'];
$pendingPayments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pesanan WHERE status = 'dibayar'"))['total'];
$pendingTestimonial = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM testimonial WHERE status = 'pending'"))['total'];

// Ambil nama depan admin untuk sapaan
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
</head>
<body class="bg-slate-50 text-slate-800 flex selection:bg-lime-200 selection:text-lime-900">
    
    <aside class="w-64 bg-white min-h-screen border-r border-slate-200 flex flex-col sticky top-0 shadow-sm z-10">
        <div class="p-8 pb-6">
            <a href="../index.php" class="text-2xl font-extrabold text-slate-800 tracking-tight transition-transform hover:scale-105 inline-block">
                Hand<span class="text-lime-600">made.</span>
            </a>
            <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mt-1">Admin Panel</p>
        </div>
        
        <nav class="flex-1 px-4 space-y-1.5">
            <a href="index.php" class="flex items-center px-4 py-3 bg-lime-50 text-lime-700 rounded-xl font-bold transition-colors">
                <i class="fa-solid fa-chart-pie mr-3 w-5 text-center"></i> Dasbor
            </a>
            <a href="produk.php" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-xl font-medium transition-colors group">
                <i class="fa-solid fa-box-open mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i> Produk
            </a>
            <a href="pembayaran.php" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-xl font-medium transition-colors group">
                <i class="fa-solid fa-credit-card mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i> Pembayaran
                <?php if ($pendingPayments > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm"><?= $pendingPayments; ?></span>
                <?php endif; ?>
            </a>
            <a href="testimonial.php" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-xl font-medium transition-colors group">
                <i class="fa-solid fa-comments mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i> Testimonial
                <?php if ($pendingTestimonial > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm"><?= $pendingTestimonial; ?></span>
                <?php endif; ?>
            </a>
            <a href="pengguna.php" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-xl font-medium transition-colors group">
                <i class="fa-solid fa-users mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i> Pengguna
            </a>
        </nav>
        
        <div class="p-4 border-t border-slate-100">
            <a href="../logout.php" class="flex items-center px-4 py-3 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl font-bold transition-colors group">
                <i class="fa-solid fa-arrow-right-from-bracket mr-3 w-5 text-center group-hover:-translate-x-1 transition-transform"></i> Keluar
            </a>
        </div>
    </aside>

    <main class="flex-1 p-8 lg:p-10 lg:pl-12 max-w-7xl">
        
        <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800">Dasbor Overview</h1>
                <p class="text-slate-500 mt-1">Selamat datang kembali, <span class="font-bold text-slate-700"><?= $admin_first_name; ?></span>.</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-white px-4 py-2.5 rounded-xl border border-slate-200 flex items-center space-x-3 shadow-sm">
                    <div class="w-8 h-8 bg-lime-100 rounded-lg flex items-center justify-center text-lime-600">
                        <i class="fa-solid fa-user-shield text-sm"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-700">Administrator</span>
                </div>
            </div>
        </header>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-lime-200 hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 mb-4 group-hover:bg-lime-50 group-hover:text-lime-600 transition-colors">
                    <i class="fa-solid fa-users text-xl"></i>
                </div>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mb-1">Total Pengguna</p>
                <h2 class="text-3xl font-extrabold text-slate-800"><?= $countUser; ?></h2>
            </div>
            
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-lime-200 hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 mb-4 group-hover:bg-lime-50 group-hover:text-lime-600 transition-colors">
                    <i class="fa-solid fa-box-open text-xl"></i>
                </div>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mb-1">Total Produk</p>
                <h2 class="text-3xl font-extrabold text-slate-800"><?= $countProduct; ?></h2>
            </div>
            
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-lime-200 hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 mb-4 group-hover:bg-lime-50 group-hover:text-lime-600 transition-colors">
                    <i class="fa-solid fa-wallet text-xl"></i>
                </div>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mb-1">Pendapatan Bersih</p>
                <h2 class="text-3xl font-extrabold text-slate-800">Rp <?= number_format($totalSales ?: 0, 0, ',', '.'); ?></h2>
            </div>
            
            <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:border-lime-200 hover:-translate-y-1 transition-all duration-300 group">
                <div class="w-12 h-12 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 mb-4 group-hover:bg-lime-50 group-hover:text-lime-600 transition-colors">
                    <i class="fa-solid fa-user-tie text-xl"></i>
                </div>
                <p class="text-slate-400 text-[11px] font-bold uppercase tracking-widest mb-1">Total Admin</p>
                <h2 class="text-3xl font-extrabold text-slate-800"><?= $countAdmin; ?></h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden flex flex-col">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800">Pesanan Terbaru</h3>
                    <a href="pembayaran.php" class="text-sm font-bold text-lime-600 hover:text-lime-700 hover:underline">Lihat Semua</a>
                </div>
                <div class="p-6 flex-grow">
                    <div class="space-y-4">
                        <?php
                        $latest = mysqli_query($conn, "SELECT p.*, u.nama FROM pesanan p JOIN pengguna u ON p.id_pengguna = u.id ORDER BY p.tanggal_pesanan DESC LIMIT 5");
                        if(mysqli_num_rows($latest) > 0):
                            while($row = mysqli_fetch_assoc($latest)):
                        ?>
                        <div class="flex items-center justify-between group p-3 hover:bg-slate-50 rounded-xl transition-colors border border-transparent hover:border-slate-100 cursor-default">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-slate-50 rounded-xl flex items-center justify-center text-slate-400 border border-slate-100 group-hover:bg-white group-hover:text-lime-600 group-hover:border-lime-100 transition-all">
                                    <i class="fa-solid fa-bag-shopping text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-800"><?= $row['nama']; ?></p>
                                    <p class="text-xs text-slate-400 mt-0.5"><i class="fa-regular fa-clock mr-1"></i><?= date('H:i, d M Y', strtotime($row['tanggal_pesanan'])); ?></p>
                                </div>
                            </div>
                            <span class="text-sm font-extrabold text-slate-800 group-hover:text-lime-600 transition-colors">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></span>
                        </div>
                        <?php 
                            endwhile; 
                        else: 
                        ?>
                        <div class="text-center py-8">
                            <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300">
                                <i class="fa-solid fa-receipt text-lg"></i>
                            </div>
                            <p class="text-slate-400 text-sm">Belum ada pesanan terbaru masuk.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>