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
    <title>Konfirmasi Pembayaran - Handmade Admin</title>
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
            <a href="../index.php" class="text-2xl font-bold bg-gradient-to-r from-amber-600 to-orange-500 bg-clip-text text-transparent">Handmade Admin.</a>
        </div>
        <nav class="flex-1 px-4 space-y-2">
            <a href="index.php" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-amber-600 rounded-xl font-medium transition">
                <i class="fa-solid fa-chart-line mr-3"></i> Dashboard
            </a>
            <a href="produk.php" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-amber-600 rounded-xl font-medium transition">
                <i class="fa-solid fa-box mr-3"></i> Produk
            </a>
            <a href="pembayaran.php" class="flex items-center px-4 py-3 bg-amber-50 text-amber-600 rounded-xl font-bold transition">
                <i class="fa-solid fa-credit-card mr-3"></i> Pembayaran
            </a>
            <a href="pengguna.php" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-amber-600 rounded-xl font-medium transition">
                <i class="fa-solid fa-users mr-3"></i> Pengguna
            </a>
        </nav>
    </aside>

    <main class="flex-1 p-8 lg:p-12">
        <div class="mb-12">
            <h1 class="text-3xl font-extrabold text-gray-900">Verifikasi Pembayaran</h1>
            <p class="text-gray-500">Tinjau bukti transfer dan update status pengiriman.</p>
        </div>

        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-400 text-xs uppercase tracking-widest font-bold">
                    <tr>
                        <th class="px-8 py-6">ID Pesanan</th>
                        <th class="px-8 py-6">Pelanggan</th>
                        <th class="px-8 py-6">Total Tagihan</th>
                        <th class="px-8 py-6">Bukti</th>
                        <th class="px-8 py-6 text-center">Status</th>
                        <th class="px-8 py-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php while($row = mysqli_fetch_assoc($query)): 
                        $status_colors = [
                            'menunggu' => 'bg-amber-50 text-amber-600',
                            'dibayar' => 'bg-blue-50 text-blue-600',
                            'dikirim' => 'bg-indigo-50 text-indigo-600',
                            'selesai' => 'bg-green-50 text-green-600',
                            'dibatalkan' => 'bg-red-50 text-red-600'
                        ];
                    ?>
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-8 py-6 font-mono text-xs font-bold text-gray-400">#HM-<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></td>
                        <td class="px-8 py-6">
                            <span class="font-bold text-gray-900"><?= $row['nama']; ?></span>
                        </td>
                        <td class="px-8 py-6 font-bold text-gray-900">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                        <td class="px-8 py-6">
                            <?php if ($row['bukti_pembayaran']): ?>
                                <button onclick="showBukti('../<?= $row['bukti_pembayaran']; ?>')" class="text-amber-600 font-bold text-xs hover:underline flex items-center">
                                    <i class="fa-solid fa-image mr-2"></i> Lihat Bukti
                                </button>
                            <?php else: ?>
                                <span class="text-gray-300 italic text-xs">Belum upload</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-8 py-6 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-tighter border <?= $status_colors[$row['status']] ?? 'bg-gray-50 text-gray-400'; ?>">
                                <?= $row['status']; ?>
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="relative inline-block text-left group">
                                <button class="p-2 text-gray-400 hover:text-gray-600 transition"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                                <div class="absolute right-0 w-40 mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 hidden group-hover:block z-20 overflow-hidden">
                                    <a href="pembayaran.php?id=<?= $row['id']; ?>&status=dibayar" class="block px-4 py-2 text-xs font-bold text-blue-600 hover:bg-blue-50">Konfirmasi Bayar</a>
                                    <a href="pembayaran.php?id=<?= $row['id']; ?>&status=dikirim" class="block px-4 py-2 text-xs font-bold text-indigo-600 hover:bg-indigo-50">Kirim Produk</a>
                                    <a href="pembayaran.php?id=<?= $row['id']; ?>&status=selesai" class="block px-4 py-2 text-xs font-bold text-green-600 hover:bg-green-50">Selesaikan</a>
                                    <a href="pembayaran.php?id=<?= $row['id']; ?>&status=dibatalkan" class="block px-4 py-2 text-xs font-bold text-red-600 hover:bg-red-50">Batalkan</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal Bukti -->
    <div id="modalBukti" class="fixed inset-0 bg-black/80 backdrop-blur-md z-50 flex items-center justify-center hidden" onclick="this.classList.add('hidden')">
        <div class="max-w-2xl p-4">
            <img id="imgBukti" src="" class="rounded-3xl shadow-2xl max-h-[80vh]">
        </div>
    </div>

    <script>
        function showBukti(src) {
            document.getElementById('imgBukti').src = src;
            document.getElementById('modalBukti').classList.remove('hidden');
        }
    </script>
</body>
</html>
