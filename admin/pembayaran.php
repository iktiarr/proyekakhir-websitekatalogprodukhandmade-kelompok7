<?php
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id_pesanan = (int)$_GET['id'];
    $status = $_GET['status'];
    
    $res_o = kueri("SELECT p.*, u.nama FROM pesanan p JOIN pengguna u ON p.id_pengguna = u.id WHERE p.id = ?", [$id_pesanan]);
    if ($row_o = mysqli_fetch_assoc($res_o)) {
        $nama_pembeli = $row_o['nama'];
        if (kueri("UPDATE pesanan SET status=? WHERE id=?", [$status, $id_pesanan])) {
            $status_keterangan = [
                'dibayar' => 'Mengonfirmasi pembayaran pesanan',
                'dikirim' => 'Mengirim produk untuk pesanan',
                'selesai' => 'Menyelesaikan pesanan',
                'dibatalkan' => 'Membatalkan pesanan'
            ];
            $tag = "#HM-" . str_pad($id_pesanan, 5, '0', STR_PAD_LEFT);
            $ket_aksi = isset($status_keterangan[$status]) ? $status_keterangan[$status] : "Mengubah status pesanan menjadi '$status'";
            catat_log('pesanan', $status, "$ket_aksi $tag dari '$nama_pembeli'");
        }
    }
    
    header("Location: pembayaran.php");
    exit();
}

$kueri_pesanan = kueri("SELECT p.*, u.nama, u.email FROM pesanan p JOIN pengguna u ON p.id_pengguna = u.id ORDER BY p.tanggal_pesanan DESC");
$pembayaran_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pesanan WHERE status = 'dibayar'"))['total'];
$testimoni_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM testimonial WHERE status = 'pending'"))['total'];
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Verifikasi Pembayaran - HandMadura Admin</title>
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
    
    <!-- Header Seluler (Mobile Navbar) -->
    <header class="md:hidden bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 py-3 sticky top-0 z-40 flex items-center gap-3 w-full transition-colors duration-300">
        <button id="tombol-menu-mobile" class="p-2 -ml-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 transition-colors focus:outline-none flex items-center justify-center cursor-pointer">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
        <a href="../index.php" class="text-lg font-extrabold text-slate-800 dark:text-slate-200 tracking-tight">
            Hand<span class="text-lime-600">Madura.</span>
        </a>
    </header>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col transition-transform duration-300 transform -translate-x-full md:translate-x-0 md:sticky md:h-screen md:top-0 overflow-y-auto flex-shrink-0">
        <div class="p-5 pb-3 flex items-center justify-between">
            <div>
                <a href="../index.php" class="text-xl font-extrabold text-slate-800 dark:text-slate-200 tracking-tight inline-block transition-transform">
                    Hand<span class="text-lime-600">Madura.</span>
                </a>
                <p class="text-[9px] uppercase tracking-widest text-slate-400 dark:text-slate-500 font-bold mt-0.5">Admin Panel</p>
            </div>
            <button id="tombol-tutup-sidebar" class="md:hidden p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer flex items-center justify-center" title="Tutup Sidebar">
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
            <a href="pembayaran.php" class="flex items-center px-3.5 py-2.5 bg-lime-50 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400 rounded-xl font-bold text-sm transition-colors">
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
            <a href="laporan.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-medium text-sm transition-colors group">
                <i class="fa-solid fa-file-invoice mr-2.5 w-4 text-center"></i> Laporan
            </a>
        </nav>
        
        <div class="p-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-1">
            <a href="../keluar.php" class="flex items-center px-3.5 py-2.5 text-slate-400 dark:text-slate-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-950/20 rounded-xl font-bold text-sm transition-colors group flex-grow">
                <i class="fa-solid fa-arrow-right-from-bracket mr-2.5 w-4 text-center transition-transform"></i> Keluar
            </a>
            <button id="tombol-tema" class="text-slate-400 hover:text-lime-600 dark:text-slate-400 dark:hover:text-lime-400 p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer flex items-center justify-center" title="Ubah Tema">
                <i id="ikon-tombol-tema" class="fa-solid fa-moon text-base"></i>
            </button>
        </div>
    </aside>

    <!-- Latar Buram Seluler (Backdrop Overlay) -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0"></div>

    <main class="flex-grow p-4 sm:p-6 w-full max-w-7xl mx-auto overflow-x-hidden">
        


        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm transition-colors duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-400 dark:text-slate-500 text-[10px] uppercase tracking-wider font-bold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 pl-6">ID Pesanan</th>
                        <th class="px-4 py-3.5">Pelanggan</th>
                        <th class="px-4 py-3.5">Total Tagihan</th>
                        <th class="px-4 py-3.5">Bukti Pembayaran</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-4 py-3.5 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/60">
                    <?php 
                    if(mysqli_num_rows($kueri_pesanan) > 0):
                        while($baris = mysqli_fetch_assoc($kueri_pesanan)): 
                            $warna_status = [
                                'menunggu'   => 'bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-900/30',
                                'dibayar'    => 'bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-900/30',
                                'dikirim'    => 'bg-indigo-50 dark:bg-indigo-950/20 text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-900/30',
                                'selesai'    => 'bg-lime-50 dark:bg-lime-950/20 text-lime-700 dark:text-lime-400 border-lime-200 dark:border-lime-900/30',
                                'dibatalkan' => 'bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 border-red-200 dark:border-red-900/30'
                            ];
                            
                            // Ambil rincian produk pesanan
                            $id_pesanan_aktif = (int)$baris['id'];
                            $kueri_detail_item = kueri("
                                SELECT dp.*, prod.nama_produk, prod.gambar 
                                FROM detail_pesanan dp 
                                JOIN produk prod ON dp.id_produk = prod.id 
                                WHERE dp.id_pesanan = ?
                            ", [$id_pesanan_aktif]);
                            $item_pesanan = [];
                            while ($item = mysqli_fetch_assoc($kueri_detail_item)) {
                                $item_pesanan[] = [
                                    'nama_produk' => $item['nama_produk'],
                                    'gambar' => $item['gambar'],
                                    'jumlah' => (int)$item['jumlah'],
                                    'harga' => (float)$item['harga']
                                ];
                            }
                            $item_pesanan_json = json_encode($item_pesanan, JSON_HEX_APOS | JSON_HEX_QUOT);
                    ?>
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 text-xs sm:text-sm transition-colors duration-200">
                        <td class="px-4 py-3 pl-6 font-mono text-[11px] font-bold text-slate-400 dark:text-slate-500">
                            #HM-<?= str_pad($baris['id'], 5, '0', STR_PAD_LEFT); ?>
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-bold text-slate-800 dark:text-slate-200"><?= $baris['nama']; ?></span>
                        </td>
                        <td class="px-4 py-3 font-extrabold text-slate-800 dark:text-slate-200 whitespace-nowrap">
                            Rp <?= number_format($baris['total_harga'], 0, ',', '.'); ?>
                        </td>
                        <td class="px-4 py-3">
                            <?php if ($baris['bukti_pembayaran']): ?>
                                <span class="inline-flex items-center text-lime-700 dark:text-lime-400 font-bold text-[10px] bg-lime-50 dark:bg-lime-950/40 px-2 py-1 rounded-xl border border-lime-100 dark:border-lime-900/40 whitespace-nowrap">
                                    <i class="fa-solid fa-file-invoice-dollar mr-1 text-lime-600 dark:text-lime-400"></i> <?= $baris['bukti_pembayaran']; ?>
                                </span>
                            <?php else: ?>
                                <span class="text-slate-300 dark:text-slate-600 italic text-[11px] whitespace-nowrap">Belum Konfirmasi</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-block px-2 py-0.5 rounded-xl text-[9px] font-bold uppercase tracking-wider border whitespace-nowrap <?= $warna_status[$baris['status']] ?? 'bg-slate-50 dark:bg-slate-900 text-slate-400 dark:text-slate-500 border-slate-200 dark:border-slate-800'; ?>">
                                <?= $baris['status']; ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 pr-6 text-right">
                            <div class="relative inline-block text-left group/menu">
                                <button class="p-1.5 w-7 h-7 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors focus:outline-none cursor-pointer">
                                    <i class="fa-solid fa-ellipsis-vertical text-sm"></i>
                                </button>
                                
                                <div class="absolute right-0 w-44 mt-1 bg-white dark:bg-slate-900 rounded-xl shadow-lg dark:shadow-none border border-slate-100 dark:border-slate-800 hidden group-hover/menu:block z-50 overflow-hidden transform origin-top-right">
                                    <div class="py-1 text-left">
                                        <p class="px-4 py-2 text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest border-b border-slate-50 dark:border-slate-800">Aksi Pesanan</p>
                                        <button type="button" 
                                            class="w-full text-left block px-4 py-2 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer border-none bg-transparent tombol-lihat-detail"
                                            data-id="<?= $baris['id']; ?>"
                                            data-tag="#HM-<?= str_pad($baris['id'], 5, '0', STR_PAD_LEFT); ?>"
                                            data-tanggal="<?= date('d M Y, H:i', strtotime($baris['tanggal_pesanan'])); ?> WIB"
                                            data-nama="<?= htmlspecialchars($baris['nama']); ?>"
                                            data-email="<?= htmlspecialchars($baris['email']); ?>"
                                            data-alamat="<?= htmlspecialchars($baris['alamat'] ?? '-'); ?>"
                                            data-metode="<?= htmlspecialchars($baris['metode_pembayaran'] ?? '-'); ?>"
                                            data-bukti="<?= htmlspecialchars($baris['bukti_pembayaran'] ?? '-'); ?>"
                                            data-status="<?= htmlspecialchars($baris['status']); ?>"
                                            data-total="Rp <?= number_format($baris['total_harga'], 0, ',', '.'); ?>"
                                            data-rincian='<?= $item_pesanan_json; ?>'>
                                            <i class="fa-solid fa-eye w-4 text-slate-400"></i> Lihat Detail
                                        </button>
                                        <div class="h-px bg-slate-100 dark:bg-slate-800 my-1"></div>
                                        <a href="pembayaran.php?id=<?= $baris['id']; ?>&status=dibayar" class="block px-4 py-2 text-xs font-bold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/30 transition-colors">
                                            <i class="fa-solid fa-check w-4"></i> Konfirmasi Bayar
                                        </a>
                                        <a href="pembayaran.php?id=<?= $baris['id']; ?>&status=dikirim" class="block px-4 py-2 text-xs font-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-950/30 transition-colors">
                                            <i class="fa-solid fa-truck-fast w-4"></i> Kirim Produk
                                        </a>
                                        <a href="pembayaran.php?id=<?= $baris['id']; ?>&status=selesai" class="block px-4 py-2 text-xs font-bold text-lime-600 dark:text-lime-400 hover:bg-lime-50 dark:hover:bg-lime-950/30 transition-colors">
                                            <i class="fa-solid fa-flag-checkered w-4"></i> Selesaikan
                                        </a>
                                        <div class="h-px bg-slate-100 dark:bg-slate-800 my-1"></div>
                                        <a href="pembayaran.php?id=<?= $baris['id']; ?>&status=dibatalkan" class="block px-4 py-2 text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors">
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
                        <td colspan="6" class="px-4 py-10 text-center text-slate-400 dark:text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-inbox text-2xl mb-2.5 text-slate-300 dark:text-slate-700"></i>
                                <p class="text-xs">Belum ada pesanan masuk.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL DETAIL PESANAN INTERAKTIF -->
    <div id="modal-detail-pesanan" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop Overlay -->
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div id="backdrop-modal" class="fixed inset-0 bg-slate-900/60 dark:bg-slate-950/80 backdrop-blur-sm transition-opacity duration-300 ease-out opacity-0"></div>

            <!-- Trick the browser into centering the modal contents. -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Container -->
            <div id="kontainer-modal" class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full border border-slate-200 dark:border-slate-800 scale-95 opacity-0 duration-300 ease-out">
                
                <!-- Header Modal -->
                <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-slate-50/50 dark:bg-slate-900/50">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-lime-600 dark:text-lime-400"></i>
                            Rincian Transaksi <span id="md-tag" class="font-mono text-xs text-slate-400 dark:text-slate-500 font-bold"></span>
                        </h3>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium mt-0.5" id="md-tanggal"></p>
                    </div>
                    <button type="button" id="tombol-tutup-modal" class="p-2 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer flex items-center justify-center">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <!-- Body Modal -->
                <div class="p-6 space-y-6 max-h-[65vh] overflow-y-auto custom-scrollbar">
                    
                    <!-- Status & Pelanggan Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Pelanggan -->
                        <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 flex items-start gap-3">
                            <div class="w-9 h-9 bg-lime-100 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-[9px] text-slate-400 dark:text-slate-500 font-extrabold uppercase tracking-widest mb-0.5">Informasi Pelanggan</p>
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-100 truncate" id="md-nama"></p>
                                <p class="text-[10px] text-slate-500 dark:text-slate-400 truncate" id="md-email"></p>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 flex items-start gap-3">
                            <div id="md-status-icon-container" class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0">
                                <i id="md-status-icon" class="fa-solid"></i>
                            </div>
                            <div>
                                <p class="text-[9px] text-slate-400 dark:text-slate-500 font-extrabold uppercase tracking-widest mb-0.5">Status Pemesanan</p>
                                <span id="md-status-badge" class="inline-block px-2.5 py-0.5 rounded-xl text-[9px] font-bold uppercase tracking-wider border mt-1"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Rincian Item Belanja -->
                    <div>
                        <h4 class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-boxes-stacked text-lime-600 dark:text-lime-400"></i> Daftar Produk Yang Dipesan
                        </h4>
                        
                        <div id="md-item-list" class="space-y-3.5">
                            <!-- Items inserted dynamically -->
                        </div>
                    </div>

                    <!-- Alamat Pengiriman -->
                    <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl border border-slate-100 dark:border-slate-800">
                        <h4 class="text-[9px] font-extrabold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                            <i class="fa-solid fa-map-location-dot"></i> Alamat Pengiriman
                        </h4>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed font-medium" id="md-alamat"></p>
                    </div>

                    <!-- Metode & Kode Pembayaran -->
                    <div class="bg-slate-50 dark:bg-slate-950 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-[9px] text-slate-400 dark:text-slate-500 font-extrabold uppercase tracking-widest mb-1"><i class="fa-solid fa-credit-card mr-1 text-[10px]"></i> Metode Pembayaran</p>
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-200" id="md-metode"></p>
                        </div>
                        <div>
                            <p class="text-[9px] text-slate-400 dark:text-slate-500 font-extrabold uppercase tracking-widest mb-1"><i class="fa-solid fa-file-invoice-dollar mr-1 text-[10px]"></i> Bukti / Kode Bayar</p>
                            <p class="text-xs font-mono font-bold text-lime-600 dark:text-lime-400" id="md-bukti"></p>
                        </div>
                    </div>

                </div>

                <!-- Footer Modal (Total Belanja & Pintasan Tindakan) -->
                <div class="px-6 py-4.5 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-center sm:text-left">
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-extrabold uppercase tracking-widest mb-0.5">Total Harga Transaksi</p>
                        <p class="text-xl font-black text-slate-800 dark:text-white" id="md-total"></p>
                    </div>
                    <div class="flex items-center gap-2.5 w-full sm:w-auto" id="md-action-footer">
                        <!-- Action buttons inserted dynamically -->
                    </div>
                </div>

            </div>
        </div>
    </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // --- LOGIKA MODAL DETAIL PESANAN ---
        const modal = document.getElementById('modal-detail-pesanan');
        const backdropModal = document.getElementById('backdrop-modal');
        const kontainerModal = document.getElementById('kontainer-modal');
        const tombolTutupModal = document.getElementById('tombol-tutup-modal');
        
        // Element bindings
        const mdTag = document.getElementById('md-tag');
        const mdTanggal = document.getElementById('md-tanggal');
        const mdNama = document.getElementById('md-nama');
        const mdEmail = document.getElementById('md-email');
        const mdAlamat = document.getElementById('md-alamat');
        const mdMetode = document.getElementById('md-metode');
        const mdBukti = document.getElementById('md-bukti');
        const mdTotal = document.getElementById('md-total');
        const mdStatusBadge = document.getElementById('md-status-badge');
        const mdStatusIcon = document.getElementById('md-status-icon');
        const mdStatusIconContainer = document.getElementById('md-status-icon-container');
        const mdItemList = document.getElementById('md-item-list');
        const mdActionFooter = document.getElementById('md-action-footer');

        function bukaModalPesanan(btn) {
            // Read data attributes
            const id = btn.getAttribute('data-id');
            const tag = btn.getAttribute('data-tag');
            const tanggal = btn.getAttribute('data-tanggal');
            const nama = btn.getAttribute('data-nama');
            const email = btn.getAttribute('data-email');
            const alamat = btn.getAttribute('data-alamat');
            const metode = btn.getAttribute('data-metode');
            const bukti = btn.getAttribute('data-bukti');
            const status = btn.getAttribute('data-status');
            const total = btn.getAttribute('data-total');
            const rincian = JSON.parse(btn.getAttribute('data-rincian'));

            // Populate metadata text
            mdTag.textContent = tag;
            mdTanggal.innerHTML = `<i class="fa-regular fa-calendar-days mr-1 text-[9px]"></i> Pemesanan pada: ${tanggal}`;
            mdNama.textContent = nama;
            mdEmail.textContent = email;
            mdAlamat.textContent = alamat;
            mdMetode.textContent = metode;
            mdTotal.textContent = total;

            // Handle Bukti Pembayaran
            if (bukti && bukti !== '-') {
                mdBukti.innerHTML = `<span class="bg-lime-50 dark:bg-lime-950/40 border border-lime-100 dark:border-lime-900/40 px-2.5 py-1 rounded-xl text-lime-700 dark:text-lime-400 font-mono text-[10px] inline-flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-[9px]"></i> ${bukti}</span>`;
            } else {
                mdBukti.innerHTML = `<span class="text-slate-400 dark:text-slate-600 italic font-medium text-[11px]">Belum melakukan pembayaran</span>`;
            }

            // Handle Status & Colors
            mdStatusBadge.textContent = status;
            mdStatusBadge.className = 'inline-block px-2.5 py-0.5 rounded-xl text-[9px] font-bold uppercase tracking-wider border ';
            mdStatusIconContainer.className = 'w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0 ';
            mdStatusIcon.className = 'fa-solid ';

            let colorClasses = '';
            let iconClass = '';
            if (status === 'menunggu') {
                colorClasses = 'bg-amber-50 dark:bg-amber-950/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-900/30';
                iconClass = 'fa-clock';
            } else if (status === 'dibayar') {
                colorClasses = 'bg-blue-50 dark:bg-blue-950/20 text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-900/30';
                iconClass = 'fa-check';
            } else if (status === 'dikirim') {
                colorClasses = 'bg-indigo-50 dark:bg-indigo-950/20 text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-900/30';
                iconClass = 'fa-truck-fast';
            } else if (status === 'selesai') {
                colorClasses = 'bg-lime-50 dark:bg-lime-950/20 text-lime-700 dark:text-lime-400 border-lime-200 dark:border-lime-900/30';
                iconClass = 'fa-flag-checkered';
            } else {
                colorClasses = 'bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 border-red-200 dark:border-red-900/30';
                iconClass = 'fa-xmark';
            }
            
            mdStatusBadge.classList.add(...colorClasses.split(' '));
            mdStatusIconContainer.classList.add(...colorClasses.split(' '));
            mdStatusIcon.classList.add(iconClass);

            // Populate ordered items list
            mdItemList.innerHTML = '';
            rincian.forEach(item => {
                // Determine image path
                const imgSrc = item.gambar.startsWith('http') ? item.gambar : `../uploads/${item.gambar}`;
                const subtotal = item.harga * item.jumlah;
                const formattedHarga = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(item.harga);
                const formattedSubtotal = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 0 }).format(subtotal);

                const itemHtml = `
                    <div class="flex items-center justify-between p-3.5 bg-slate-50 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800 rounded-2xl gap-3">
                        <div class="flex items-center gap-3.5 min-w-0">
                            <img src="${imgSrc}" alt="${item.nama_produk}" class="w-12 h-12 rounded-xl object-cover border border-slate-200/60 dark:border-slate-800 flex-shrink-0 shadow-sm" onerror="this.src='https://images.unsplash.com/photo-1584917865442-de89df76afd3?auto=format&fit=crop&w=150&q=80'">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate leading-snug">${item.nama_produk}</p>
                                <p class="text-[10px] text-slate-400 dark:text-slate-550 font-semibold mt-1 flex items-center gap-1">${formattedHarga} <span class="text-lime-600 dark:text-lime-400 font-black">x${item.jumlah}</span></p>
                            </div>
                        </div>
                        <p class="text-xs font-extrabold text-slate-800 dark:text-slate-100 whitespace-nowrap">${formattedSubtotal}</p>
                    </div>
                `;
                mdItemList.insertAdjacentHTML('beforeend', itemHtml);
            });

            // Populate quick action buttons based on status
            mdActionFooter.innerHTML = '';
            let actionHtml = '';
            if (status === 'menunggu') {
                actionHtml = `
                    <a href="pembayaran.php?id=${id}&status=dibayar" class="flex-1 sm:flex-initial bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl text-center shadow-md transition-colors"><i class="fa-solid fa-check mr-1 text-[10px]"></i> Konfirmasi Bayar</a>
                    <a href="pembayaran.php?id=${id}&status=dibatalkan" class="flex-1 sm:flex-initial bg-red-50 hover:bg-red-100 dark:bg-red-950/30 text-red-600 dark:text-red-400 text-xs font-bold px-4 py-2.5 rounded-xl text-center transition-colors"><i class="fa-solid fa-xmark mr-1 text-[10px]"></i> Batalkan</a>
                `;
            } else if (status === 'dibayar') {
                actionHtml = `
                    <a href="pembayaran.php?id=${id}&status=dikirim" class="flex-1 sm:flex-initial bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl text-center shadow-md transition-colors"><i class="fa-solid fa-truck-fast mr-1 text-[10px]"></i> Kirim Produk</a>
                    <a href="pembayaran.php?id=${id}&status=dibatalkan" class="flex-1 sm:flex-initial bg-red-50 hover:bg-red-100 dark:bg-red-950/30 text-red-600 dark:text-red-400 text-xs font-bold px-4 py-2.5 rounded-xl text-center transition-colors"><i class="fa-solid fa-xmark mr-1 text-[10px]"></i> Batalkan</a>
                `;
            } else if (status === 'dikirim') {
                actionHtml = `
                    <a href="pembayaran.php?id=${id}&status=selesai" class="flex-1 sm:flex-initial bg-lime-600 hover:bg-lime-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl text-center shadow-md transition-colors"><i class="fa-solid fa-flag-checkered mr-1 text-[10px]"></i> Selesaikan</a>
                `;
            }
            
            mdActionFooter.insertAdjacentHTML('beforeend', actionHtml + `<button type="button" onclick="tutupModalPesanan()" class="flex-1 sm:flex-initial bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-xs font-bold px-4 py-2.5 rounded-xl text-center transition-colors hover:bg-slate-50 dark:hover:bg-slate-700">Tutup</button>`);

            // Show Modal with Animation
            modal.classList.remove('hidden');
            setTimeout(() => {
                backdropModal.classList.replace('opacity-0', 'opacity-100');
                kontainerModal.classList.replace('scale-95', 'scale-100');
                kontainerModal.classList.replace('opacity-0', 'opacity-100');
            }, 10);
            document.body.style.overflow = 'hidden';
        }

        function tutupModalPesanan() {
            backdropModal.classList.replace('opacity-100', 'opacity-0');
            kontainerModal.classList.replace('scale-100', 'scale-95');
            kontainerModal.classList.replace('opacity-100', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 250);
            document.body.style.overflow = '';
        }

        // Attach event listeners to detail buttons
        document.querySelectorAll('.tombol-lihat-detail').forEach(btn => {
            btn.addEventListener('click', () => bukaModalPesanan(btn));
        });

        if (tombolTutupModal) tombolTutupModal.addEventListener('click', tutupModalPesanan);
        if (backdropModal) backdropModal.addEventListener('click', tutupModalPesanan);
        
        // Expose function globally so inline button calls work
        window.tutupModalPesanan = tutupModalPesanan;
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

        // Pengontrol Sidebar Seluler
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