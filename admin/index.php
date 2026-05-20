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
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Handmade</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex">
    <!-- Sidebar -->
    <aside class="w-64 bg-white min-h-screen border-r border-gray-100 flex flex-col sticky top-0">
        <div class="p-8">
            <a href="../index.php" class="text-2xl font-bold bg-gradient-to-r from-lime-600 to-lime-500 bg-clip-text text-transparent">Handmade Admin.</a>
        </div>
        <nav class="flex-1 px-4 space-y-2">
            <a href="index.php" class="flex items-center px-4 py-3 bg-lime-50 text-lime-700 rounded-xl font-bold transition">
                <i class="fa-solid fa-chart-line mr-3"></i> Dashboard
            </a>
            <a href="produk.php" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-lime-700 rounded-xl font-medium transition">
                <i class="fa-solid fa-box mr-3"></i> Produk
            </a>
            <a href="pembayaran.php" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-lime-700 rounded-xl font-medium transition">
                <i class="fa-solid fa-credit-card mr-3"></i> Pembayaran
                <?php if ($pendingPayments > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full"><?= $pendingPayments; ?></span>
                <?php endif; ?>
            </a>
            <a href="testimonial.php" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-lime-700 rounded-xl font-medium transition">
                <i class="fa-solid fa-comments mr-3"></i> Testimonial
                <?php if ($pendingTestimonial > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full"><?= $pendingTestimonial; ?></span>
                <?php endif; ?>
            </a>
            <a href="pengguna.php" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-lime-700 rounded-xl font-medium transition">
                <i class="fa-solid fa-users mr-3"></i> Pengguna
            </a>
        </nav>
        <div class="p-4 border-t border-gray-50">
            <a href="../logout.php" class="flex items-center px-4 py-3 text-red-500 hover:bg-red-50 rounded-xl font-medium transition">
                <i class="fa-solid fa-right-from-bracket mr-3"></i> Keluar
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 lg:p-12">
        <header class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">Dashboard</h1>
                <p class="text-gray-500">Selamat datang kembali, <?= $_SESSION['nama']; ?>.</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-white p-2 rounded-xl border border-gray-100 flex items-center space-x-3 pr-4 shadow-sm">
                    <div class="w-10 h-10 bg-lime-50 rounded-lg flex items-center justify-center text-lime-600">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <span class="text-sm font-bold text-gray-900">Administrator</span>
                </div>
            </div>
        </header>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-500 group">
                <div class="w-14 h-14 bg-lime-50 rounded-xl flex items-center justify-center text-lime-600 mb-5 group-hover:bg-lime-600 group-hover:text-white transition">
                    <i class="fa-solid fa-users text-2xl"></i>
                </div>
                <p class="text-gray-400 text-sm font-bold uppercase tracking-widest">Total Pengguna</p>
                <h2 class="text-3xl font-extrabold text-gray-900 mt-2"><?= $countUser; ?></h2>
            </div>
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-500 group">
                <div class="w-14 h-14 bg-lime-50 rounded-xl flex items-center justify-center text-lime-600 mb-5 group-hover:bg-lime-600 group-hover:text-white transition">
                    <i class="fa-solid fa-box text-2xl"></i>
                </div>
                <p class="text-gray-400 text-sm font-bold uppercase tracking-widest">Total Produk</p>
                <h2 class="text-3xl font-extrabold text-gray-900 mt-2"><?= $countProduct; ?></h2>
            </div>
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-500 group">
                <div class="w-14 h-14 bg-lime-50 rounded-xl flex items-center justify-center text-lime-600 mb-5 group-hover:bg-lime-600 group-hover:text-white transition">
                    <i class="fa-solid fa-money-bill-trend-up text-2xl"></i>
                </div>
                <p class="text-gray-400 text-sm font-bold uppercase tracking-widest">Pendapatan</p>
                <h2 class="text-3xl font-extrabold text-gray-900 mt-2">Rp <?= number_format($totalSales ?: 0, 0, ',', '.'); ?></h2>
            </div>
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-500 group">
                <div class="w-14 h-14 bg-lime-50 rounded-xl flex items-center justify-center text-lime-600 mb-5 group-hover:bg-lime-600 group-hover:text-white transition">
                    <i class="fa-solid fa-user-tie text-2xl"></i>
                </div>
                <p class="text-gray-400 text-sm font-bold uppercase tracking-widest">Total Admin</p>
                <h2 class="text-3xl font-extrabold text-gray-900 mt-2"><?= $countAdmin; ?></h2>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Aktivitas Terbaru</h3>
                <div class="space-y-4">
                    <?php
                    $latest = mysqli_query($conn, "SELECT p.*, u.nama FROM pesanan p JOIN pengguna u ON p.id_pengguna = u.id ORDER BY p.tanggal_pesanan DESC LIMIT 5");
                    while($row = mysqli_fetch_assoc($latest)):
                    ?>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-gray-400 border border-gray-100">
                                <i class="fa-solid fa-shopping-bag text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900"><?= $row['nama']; ?></p>
                                <p class="text-xs text-gray-400"><?= date('H:i, d M Y', strtotime($row['tanggal_pesanan'])); ?></p>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-gray-900">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></span>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <div class="bg-lime-600 p-8 rounded-xl shadow-2xl shadow-lime-200/50 relative overflow-hidden flex flex-col justify-center">
                <div class="absolute top-0 right-0 w-56 h-56 bg-lime-500 rounded-full -mr-28 -mt-28 filter blur-3xl opacity-50"></div>
                <div class="relative z-10">
                    <h3 class="text-3xl font-extrabold text-white mb-4">Butuh Bantuan?</h3>
                    <p class="text-lime-100 mb-6 leading-relaxed">Kelola produk, pantau pembayaran, dan berikan pelayanan terbaik untuk pelanggan Anda.</p>
                    <a href="produk.php" class="inline-block bg-white text-lime-600 px-6 py-3 rounded-xl font-bold hover:bg-gray-50 transition shadow-xl">Kelola Produk</a>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
