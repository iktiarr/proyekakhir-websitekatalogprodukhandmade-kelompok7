<?php
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

// Update Status Pesanan
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
            <a href="pembayaran.php" class="flex items-center px-4 py-3 bg-lime-50 text-lime-700 rounded-xl font-bold transition-colors">
                <i class="fa-solid fa-credit-card mr-3 w-5 text-center"></i> Pembayaran
            </a>
            <a href="testimonial.php" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-xl font-medium transition-colors group">
                <i class="fa-solid fa-comments mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i> Testimonial
            </a>
            <a href="pengguna.php" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-xl font-medium transition-colors group">
                <i class="fa-solid fa-users mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i> Pengguna
            </a>
        </nav>
    </aside>

    <main class="flex-1 p-8 lg:p-10 max-w-7xl">
        
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold text-slate-800">Verifikasi Pembayaran</h1>
            <p class="text-slate-500 mt-1">Tinjau bukti transfer dari pelanggan dan perbarui status pesanan.</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-400 text-[11px] uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-5 pl-8">ID Pesanan</th>
                        <th class="px-6 py-5">Pelanggan</th>
                        <th class="px-6 py-5">Total Tagihan</th>
                        <th class="px-6 py-5 text-center">Bukti</th>
                        <th class="px-6 py-5 text-center">Status</th>
                        <th class="px-6 py-5 pr-8 text-right">Aksi</th>
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
                    <tr class="hover:bg-slate-50/80 transition-colors duration-200">
                        <td class="px-6 py-5 pl-8 font-mono text-xs font-bold text-slate-400">
                            #HM-<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?>
                        </td>
                        <td class="px-6 py-5">
                            <span class="font-bold text-slate-800"><?= $row['nama']; ?></span>
                        </td>
                        <td class="px-6 py-5 font-extrabold text-slate-800 whitespace-nowrap">
                            Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <?php if ($row['bukti_pembayaran']): ?>
                                <button onclick="showBukti('../<?= $row['bukti_pembayaran']; ?>')" class="inline-flex items-center text-lime-600 font-bold text-xs bg-lime-50 hover:bg-lime-100 px-3 py-1.5 rounded-lg transition-colors border border-transparent hover:border-lime-200">
                                    <i class="fa-solid fa-image mr-1.5"></i> Lihat
                                </button>
                            <?php else: ?>
                                <span class="text-slate-300 italic text-xs whitespace-nowrap">Belum ada</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-5 text-center">
                            <span class="inline-block px-3 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border whitespace-nowrap <?= $status_colors[$row['status']] ?? 'bg-slate-50 text-slate-400 border-slate-200'; ?>">
                                <?= $row['status']; ?>
                            </span>
                        </td>
                        <td class="px-6 py-5 pr-8 text-right">
                            <div class="relative inline-block text-left group/menu">
                                <button class="p-2 w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors focus:outline-none">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                
                                <div class="absolute right-0 w-44 mt-1 bg-white rounded-xl shadow-lg border border-slate-100 opacity-0 invisible group-hover/menu:opacity-100 group-hover/menu:visible group-hover/menu:-translate-y-1 transition-all duration-200 z-50 overflow-hidden transform origin-top-right">
                                    <div class="py-1 text-left">
                                        <p class="px-4 py-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-50">Ubah Status</p>
                                        <a href="pembayaran.php?id=<?= $row['id']; ?>&status=dibayar" class="block px-4 py-2.5 text-xs font-bold text-blue-600 hover:bg-blue-50 transition-colors">
                                            <i class="fa-solid fa-check w-4"></i> Konfirmasi Bayar
                                        </a>
                                        <a href="pembayaran.php?id=<?= $row['id']; ?>&status=dikirim" class="block px-4 py-2.5 text-xs font-bold text-indigo-600 hover:bg-indigo-50 transition-colors">
                                            <i class="fa-solid fa-truck-fast w-4"></i> Kirim Produk
                                        </a>
                                        <a href="pembayaran.php?id=<?= $row['id']; ?>&status=selesai" class="block px-4 py-2.5 text-xs font-bold text-lime-600 hover:bg-lime-50 transition-colors">
                                            <i class="fa-solid fa-flag-checkered w-4"></i> Selesaikan
                                        </a>
                                        <div class="h-px bg-slate-50 my-1"></div>
                                        <a href="pembayaran.php?id=<?= $row['id']; ?>&status=dibatalkan" class="block px-4 py-2.5 text-xs font-bold text-red-600 hover:bg-red-50 transition-colors">
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
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-inbox text-3xl mb-3 text-slate-300"></i>
                                <p class="text-sm">Belum ada pesanan masuk.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="modalBukti" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[60] flex items-center justify-center opacity-0 invisible transition-all duration-300" onclick="closeModal()">
        <div class="relative max-w-2xl w-full p-4 transform scale-95 transition-transform duration-300" id="modalContent" onclick="event.stopPropagation()">
            <button onclick="closeModal()" class="absolute -top-12 right-4 sm:-right-4 w-10 h-10 bg-white/10 hover:bg-white/20 text-white rounded-full flex items-center justify-center transition-colors backdrop-blur-md">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
            <img id="imgBukti" src="" class="w-auto mx-auto rounded-2xl shadow-2xl max-h-[85vh] border-4 border-white object-contain bg-white">
        </div>
    </div>

    <script>
        function showBukti(src) {
            const modal = document.getElementById('modalBukti');
            const content = document.getElementById('modalContent');
            const img = document.getElementById('imgBukti');
            
            img.src = src;
            
            modal.classList.remove('opacity-0', 'invisible');
            content.classList.remove('scale-95');
        }

        function closeModal() {
            const modal = document.getElementById('modalBukti');
            const content = document.getElementById('modalContent');
            
            content.classList.add('scale-95');
            modal.classList.add('opacity-0', 'invisible');
            
            setTimeout(() => {
                document.getElementById('imgBukti').src = '';
            }, 300);
        }
    </script>
</body>
</html>