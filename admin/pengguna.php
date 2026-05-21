<?php
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

$query = mysqli_query($conn, "SELECT * FROM pengguna ORDER BY role ASC, nama ASC");
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
</head>
<body class="bg-slate-50 flex text-slate-800 selection:bg-lime-200 selection:text-lime-900">
    
    <aside class="w-64 bg-white min-h-screen border-r border-slate-200 flex flex-col sticky top-0 shadow-sm z-10">
        <div class="p-8 pb-6">
            <a href="../index.php" class="text-2xl font-extrabold text-slate-800 tracking-tight transition-transform hover:scale-105 inline-block">
                Hand<span class="text-lime-600">made.</span>
            </a>
            <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mt-1">Admin Panel</p>
        </div>
        
        <nav class="flex-1 px-4 space-y-1.5">
            <a href="index.php" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-xl font-medium transition-colors group">
                <i class="fa-solid fa-chart-pie mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i> Dasbor
            </a>
            <a href="produk.php" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-xl font-medium transition-colors group">
                <i class="fa-solid fa-box-open mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i> Produk
            </a>
            <a href="pembayaran.php" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-xl font-medium transition-colors group">
                <i class="fa-solid fa-credit-card mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i> Pembayaran
            </a>
            <a href="testimonial.php" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-xl font-medium transition-colors group">
                <i class="fa-solid fa-comments mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i> Testimonial
            </a>
            <a href="pengguna.php" class="flex items-center px-4 py-3 bg-lime-50 text-lime-700 rounded-xl font-bold transition-colors">
                <i class="fa-solid fa-users mr-3 w-5 text-center"></i> Pengguna
            </a>
        </nav>
        
        <div class="p-4 border-t border-slate-100">
            <a href="../logout.php" class="flex items-center px-4 py-3 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl font-bold transition-colors group">
                <i class="fa-solid fa-arrow-right-from-bracket mr-3 w-5 text-center group-hover:-translate-x-1 transition-transform"></i> Keluar
            </a>
        </div>
    </aside>

    <main class="flex-1 p-8 lg:p-10 max-w-7xl">
        
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold text-slate-800">Kelola Pengguna</h1>
            <p class="text-slate-500 mt-1">Daftar semua pengguna terdaftar dan administrator sistem.</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-400 text-[11px] uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-5 pl-8">Pengguna</th>
                        <th class="px-6 py-5">Email</th>
                        <th class="px-6 py-5">Role Akses</th>
                        <th class="px-6 py-5 pr-8">Bergabung Pada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php 
                    if(mysqli_num_rows($query) > 0):
                        while($row = mysqli_fetch_assoc($query)): 
                    ?>
                    <tr class="hover:bg-slate-50/80 transition-colors duration-200">
                        <td class="px-6 py-4 pl-8">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 <?= $row['role'] === 'admin' ? 'bg-lime-100 text-lime-700' : 'bg-slate-100 text-slate-500'; ?> rounded-full flex items-center justify-center font-bold text-sm shadow-sm">
                                    <?= strtoupper(substr($row['nama'], 0, 1)); ?>
                                </div>
                                <span class="font-bold text-slate-800"><?= $row['nama']; ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-500 whitespace-nowrap">
                            <?= $row['email']; ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 rounded-md text-[10px] font-bold uppercase tracking-widest border whitespace-nowrap <?= $row['role'] === 'admin' ? 'bg-lime-50 text-lime-700 border-lime-200' : 'bg-slate-50 text-slate-500 border-slate-200'; ?>">
                                <?php if($row['role'] === 'admin'): ?>
                                    <i class="fa-solid fa-user-shield mr-1.5"></i>
                                <?php else: ?>
                                    <i class="fa-solid fa-user mr-1.5"></i>
                                <?php endif; ?>
                                <?= $row['role']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 pr-8 text-sm font-medium text-slate-400 whitespace-nowrap">
                            <i class="fa-regular fa-calendar-days mr-2"></i><?= date('d M Y', strtotime($row['tanggal_dibuat'])); ?>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-users-slash text-3xl mb-3 text-slate-300"></i>
                                <p class="text-sm">Belum ada pengguna terdaftar.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>