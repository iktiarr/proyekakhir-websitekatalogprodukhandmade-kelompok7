<?php
include '../koneksi.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}


if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int)$_GET['id'];
    $status = mysqli_real_escape_string($conn, $_GET['status']);
    mysqli_query($conn, "UPDATE pesanan SET status='$status' WHERE id=$id");
    header("Location: pembayaran.php");
    exit();
}


$query = mysqli_query($conn, "SELECT p.*, u.nama FROM pesanan p JOIN pengguna u ON p.id_pengguna = u.id ORDER BY p.tanggal_pesanan DESC");
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verifikasi Pembayaran - Handmade Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 flex text-slate-800 selection:bg-lime-200 selection:text-lime-900">
    
    <!-- Sidebar Admin -->
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
            <a href="pembayaran.php" class="flex items-center px-3.5 py-2.5 bg-lime-50 text-lime-700 rounded-lg font-bold text-sm">
                <i class="fa-solid fa-credit-card mr-2.5 w-4 text-center"></i> Pembayaran
            </a>
            <a href="testimonial.php" class="flex items-center px-3.5 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-lg font-medium text-sm group">
                <i class="fa-solid fa-comments mr-2.5 w-4 text-center"></i> Testimonial
            </a>
            <a href="pengguna.php" class="flex items-center px-3.5 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-lg font-medium text-sm group">
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
            <h1 class="text-2xl font-extrabold text-slate-800">Verifikasi Pembayaran</h1>
            <p class="text-slate-500 text-xs mt-0.5">Tinjau bukti konfirmasi dari pelanggan dan perbarui status pesanan.</p>
        </div>

        <!-- Tabel Pembayaran -->
        <div class="bg-white rounded-xl border border-slate-200">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3.5 pl-6">ID Pesanan</th>
                        <th class="px-4 py-3.5">Pelanggan</th>
                        <th class="px-4 py-3.5">Total Tagihan</th>
                        <th class="px-4 py-3.5">Konfirmasi Akun/Rekening Pengirim</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-4 py-3.5 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php 
                    if(mysqli_num_rows($query) > 0):
                        while($row = mysqli_fetch_assoc($query)): 
                            $status_colors = [
                                'menunggu'   => 'bg-amber-50 text-amber-600 border-amber-200',
                                'dibayar'    => 'bg-blue-50 text-blue-600 border-blue-200',
                                'dikirim'    => 'bg-indigo-50 text-indigo-600 border-indigo-200',
                                'selesai'    => 'bg-lime-50 text-lime-700 border-lime-200',
                                'dibatalkan' => 'bg-red-50 text-red-600 border-red-200'
                            ];
                    ?>
                    <tr class="hover:bg-slate-50/80 text-xs sm:text-sm">
                        <td class="px-4 py-3 pl-6 font-mono text-[11px] font-bold text-slate-400">
                            #HM-<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-bold text-slate-800"><?= $row['nama']; ?></span>
                        </td>
                        <td class="px-4 py-3 font-extrabold text-slate-800 whitespace-nowrap">
                            Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php if ($row['bukti_pembayaran']): ?>
                                <span class="inline-flex items-center text-lime-700 font-bold text-[10px] bg-lime-50 px-2 py-1 rounded border border-lime-100">
                                    <i class="fa-solid fa-file-invoice-dollar mr-1 text-lime-600"></i> <?= $row['bukti_pembayaran']; ?>
                                </span>
                            <?php else: ?>
                                <span class="text-slate-300 italic text-[11px] whitespace-nowrap">Belum Konfirmasi</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider border whitespace-nowrap <?= $status_colors[$row['status']] ?? 'bg-slate-50 text-slate-400 border-slate-200'; ?>">
                                <?= $row['status']; ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 pr-6 text-right">
                            <div class="relative inline-block text-left group/menu">
                                <button class="p-1.5 w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors focus:outline-none cursor-pointer">
                                    <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                </button>
                                
                                <div class="absolute right-0 w-44 mt-1 bg-white rounded-xl shadow-lg border border-slate-100 opacity-0 invisible group-hover/menu:opacity-100 group-hover/menu:visible group-hover/menu:-translate-y-1 transition-all duration-200 z-50 overflow-hidden transform origin-top-right">
                                    <div class="py-1 text-left">
                                        <p class="px-4 py-2 text-[9px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-50">Ubah Status</p>
                                        <a href="pembayaran.php?id=<?= $row['id']; ?>&status=dibayar" class="block px-4 py-2 text-xs font-bold text-blue-600 hover:bg-blue-50 transition-colors">
                                            <i class="fa-solid fa-check w-4"></i> Konfirmasi Bayar
                                        </a>
                                        <a href="pembayaran.php?id=<?= $row['id']; ?>&status=dikirim" class="block px-4 py-2 text-xs font-bold text-indigo-600 hover:bg-indigo-50 transition-colors">
                                            <i class="fa-solid fa-truck-fast w-4"></i> Kirim Produk
                                        </a>
                                        <a href="pembayaran.php?id=<?= $row['id']; ?>&status=selesai" class="block px-4 py-2 text-xs font-bold text-lime-600 hover:bg-lime-50 transition-colors">
                                            <i class="fa-solid fa-flag-checkered w-4"></i> Selesaikan
                                        </a>
                                        <div class="h-px bg-slate-50 my-1"></div>
                                        <a href="pembayaran.php?id=<?= $row['id']; ?>&status=dibatalkan" class="block px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50 transition-colors">
                                            <i class="fa-solid fa-xmark w-4"></i> Batalkan
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-inbox text-2xl mb-2.5 text-slate-300"></i>
                                <p class="text-xs">Belum ada pesanan masuk.</p>
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