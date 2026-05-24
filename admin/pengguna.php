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
    
    <aside class="w-56 bg-white min-h-screen border-r border-slate-200 flex flex-col sticky top-0 z-10">
        <div class="p-5 pb-3">
            <a href="../index.php" class="text-xl font-extrabold text-slate-800 tracking-tight inline-block">
                Hand<span class="text-lime-600">made.</span>
            </a>
            <p class="text-[9px] uppercase tracking-widest text-slate-400 font-bold mt-0.5">Admin Panel</p>
        </div>
        
        <nav class="flex-1 px-3 space-y-1">
            <a href="index.php" class="flex items-center px-3.5 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-lg font-medium text-sm group">
                <i class="fa-solid fa-chart-pie mr-2.5 w-4 text-center"></i> Dasbor
            </a>
            <a href="produk.php" class="flex items-center px-3.5 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-lg font-medium text-sm group">
                <i class="fa-solid fa-box-open mr-2.5 w-4 text-center"></i> Produk
            </a>
            <a href="pembayaran.php" class="flex items-center px-3.5 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-lg font-medium text-sm group">
                <i class="fa-solid fa-credit-card mr-2.5 w-4 text-center"></i> Pembayaran
            </a>
            <a href="testimonial.php" class="flex items-center px-3.5 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-lg font-medium text-sm group">
                <i class="fa-solid fa-comments mr-2.5 w-4 text-center"></i> Testimonial
            </a>
            <a href="pengguna.php" class="flex items-center px-3.5 py-2.5 bg-lime-50 text-lime-700 rounded-lg font-bold text-sm">
                <i class="fa-solid fa-users mr-2.5 w-4 text-center"></i> Pengguna
            </a>
        </nav>
        
        <div class="p-3 border-t border-slate-100">
            <a href="../logout.php" class="flex items-center px-3.5 py-2.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg font-bold text-sm group">
                <i class="fa-solid fa-arrow-right-from-bracket mr-2.5 w-4 text-center"></i> Keluar
            </a>
        </div>
    </aside>

    <main class="flex-1 p-5 sm:p-6 max-w-7xl">
        
        <div class="mb-6">
            <h1 class="text-2xl font-extrabold text-slate-800">Kelola Pengguna</h1>
            <p class="text-slate-500 text-xs mt-0.5">Daftar semua pengguna terdaftar dan administrator sistem.</p>
        </div>

        <div class="bg-white rounded-xl border border-slate-200">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3.5 pl-6">Pengguna</th>
                        <th class="px-4 py-3.5">Email</th>
                        <th class="px-4 py-3.5">Role Akses</th>
                        <th class="px-4 py-3.5">Bergabung Pada</th>
                        <th class="px-4 py-3.5 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php 
                    if(mysqli_num_rows($query) > 0):
                        while($row = mysqli_fetch_assoc($query)): 
                    ?>
                    <tr class="hover:bg-slate-50/80 text-xs sm:text-sm">
                        <td class="px-4 py-3 pl-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 <?= $row['role'] === 'admin' ? 'bg-lime-100 text-lime-700' : 'bg-slate-100 text-slate-500'; ?> rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    <?= strtoupper(substr($row['nama'], 0, 1)); ?>
                                </div>
                                <span class="font-bold text-slate-800"><?= $row['nama']; ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs font-medium text-slate-500 whitespace-nowrap">
                            <?= $row['email']; ?>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-widest border whitespace-nowrap <?= $row['role'] === 'admin' ? 'bg-lime-50 text-lime-700 border-lime-200' : 'bg-slate-50 text-slate-500 border-slate-200'; ?>">
                                <?php if($row['role'] === 'admin'): ?>
                                    <i class="fa-solid fa-user-shield mr-1"></i>
                                <?php else: ?>
                                    <i class="fa-solid fa-user mr-1"></i>
                                <?php endif; ?>
                                <?= $row['role']; ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs font-medium text-slate-400 whitespace-nowrap">
                            <i class="fa-regular fa-calendar-days mr-1.5"></i><?= date('d M Y', strtotime($row['tanggal_dibuat'])); ?>
                        </td>
                        <td class="px-4 py-3 pr-6 text-right whitespace-nowrap">
                            <?php if ($row['role'] === 'user'): ?>
                                <a href="pengguna.php?hapus=<?= $row['id']; ?>" onclick="return confirm('Hapus akun pengguna ini secara permanen?')" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-red-500 hover:text-red-700 hover:bg-red-50 transition-colors" title="Hapus Pengguna">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-slate-300 text-xs italic">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    <tr>
                        <td colspan="5" class="px-4 py-10 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-users-slash text-2xl mb-2.5 text-slate-300"></i>
                                <p class="text-xs">Belum ada pengguna terdaftar.</p>
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