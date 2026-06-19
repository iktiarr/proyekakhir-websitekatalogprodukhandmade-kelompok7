<?php
$awalan = "../";
include '../koneksi.php';

if (!isset($_SESSION['user']['id']) && !isset($_SESSION['admin']['id'])) {
    header("Location: ../masuk.php");
    exit();
}

$id_pengguna = $_SESSION['user']['id'] ?? null;
$adalah_admin = isset($_SESSION['admin']['role']) && $_SESSION['admin']['role'] === 'admin';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID Pesanan tidak valid.");
}

$id_pesanan = intval($_GET['id']);

// Ambil data pesanan dan pengguna
$query_pesanan = kueri(
    "SELECT p.*, u.nama, u.email, u.no_telp FROM pesanan p 
     JOIN pengguna u ON p.id_pengguna = u.id 
     WHERE p.id = ?", 
    [$id_pesanan]
);

if (mysqli_num_rows($query_pesanan) === 0) {
    die("Pesanan tidak ditemukan.");
}

$pesanan = mysqli_fetch_assoc($query_pesanan);

// Validasi akses keamanan: Hanya pembeli yang bersangkutan atau admin yang boleh mencetak
if ($pesanan['id_pengguna'] != $id_pengguna && !$adalah_admin) {
    die("Anda tidak memiliki izin untuk melihat/mencetak struk ini.");
}

// Ambil rincian produk pesanan
$query_detail = kueri(
    "SELECT d.*, pr.nama_produk FROM detail_pesanan d 
     JOIN produk pr ON d.id_produk = pr.id 
     WHERE d.id_pesanan = ?", 
    [$id_pesanan]
);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Struk Pembelian #KM-<?= str_pad($pesanan['id'], 5, '0', STR_PAD_LEFT); ?></title>
    <!-- Google Fonts & Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: white !important;
                color: black !important;
            }
            .print-border-none {
                border: none !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen pb-12">

    <!-- Action Bar / Kontrol Tombol (Disembunyikan saat cetak) -->
    <div class="no-print bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-3xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="riwayat.php" class="inline-flex items-center text-sm font-semibold text-slate-600 hover:text-lime-600 transition-colors duration-200">
                <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Riwayat
            </a>
            <div class="flex items-center space-x-2">
                <button onclick="window.print()" class="inline-flex items-center bg-lime-600 hover:bg-lime-700 text-white text-sm font-bold px-4 py-2 rounded-xl shadow-sm transition-all duration-200 border-none cursor-pointer">
                    <i class="fa-solid fa-print mr-2"></i> Cetak / Simpan PDF
                </button>
            </div>
        </div>
    </div>

    <!-- Main Container Struk -->
    <div class="max-w-3xl mx-auto px-4 mt-6">
        <div class="print-border-none bg-white border border-slate-200/60 rounded-2xl shadow-sm p-6 sm:p-10 relative overflow-hidden">
            
            <!-- Stempel Lunas Virtual -->
            <?php if (in_array($pesanan['status'], ['dibayar', 'dikirim', 'selesai'])): ?>
                <div class="absolute right-6 top-28 sm:right-12 sm:top-10 rotate-12 opacity-80 pointer-events-none select-none">
                    <div class="border-4 border-emerald-600 text-emerald-650 font-black text-xl tracking-widest px-4 py-1.5 rounded-lg uppercase">
                        LUNAS
                    </div>
                </div>
            <?php endif; ?>

            <!-- Header Struk -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-100 pb-6 mb-8 gap-4">
                <div>
                    <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">
                        Hand<span class="text-lime-600">Madura.</span>
                    </h1>
                    <p class="text-xs text-slate-400 mt-1">Katalog Kerajinan Autentik Madura</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">Sumenep - Pamekasan - Sampang - Bangkalan</p>
                </div>
                <div class="text-left sm:text-right">
                    <h2 class="text-lg font-bold text-slate-800 tracking-wide uppercase">Struk Transaksi</h2>
                    <p class="text-xs font-semibold text-slate-400 mt-1">No: #KM-<?= str_pad($pesanan['id'], 5, '0', STR_PAD_LEFT); ?></p>
                    <p class="text-[11px] text-slate-500 mt-0.5"><?= date('d M Y, H:i', strtotime($pesanan['tanggal_pesanan'])); ?> WIB</p>
                </div>
            </div>

            <!-- Detail Transaksi & Pembeli -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Info Pembeli -->
                <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-100">
                    <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-2.5">Detail Pelanggan</h3>
                    <div class="space-y-1.5 text-xs">
                        <p class="text-slate-800 font-bold"><?= htmlspecialchars($pesanan['nama']); ?></p>
                        <p class="text-slate-500"><i class="fa-regular fa-envelope mr-1.5 text-[10px]"></i><?= htmlspecialchars($pesanan['email']); ?></p>
                        <p class="text-slate-500"><i class="fa-solid fa-phone mr-1.5 text-[10px]"></i><?= htmlspecialchars($pesanan['no_telp'] ?? '-'); ?></p>
                    </div>
                </div>

                <!-- Info Pembayaran & Pengiriman -->
                <div class="bg-slate-50/50 p-4 rounded-xl border border-slate-100">
                    <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider mb-2.5">Metode & Pengiriman</h3>
                    <div class="space-y-1.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-500">Metode Bayar:</span>
                            <span class="font-semibold text-slate-800"><?= htmlspecialchars($pesanan['metode_pembayaran'] ?? 'Transfer Bank'); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Status Transaksi:</span>
                            <span class="font-bold text-emerald-650 capitalize"><?= htmlspecialchars($pesanan['status']); ?></span>
                        </div>
                        <div class="flex flex-col mt-2 pt-2 border-t border-slate-100">
                            <span class="text-slate-500 mb-0.5">Alamat Kirim:</span>
                            <span class="text-slate-800 leading-relaxed font-medium"><?= nl2br(htmlspecialchars($pesanan['alamat'] ?? '-')); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Daftar Produk -->
            <div class="border border-slate-150 rounded-xl overflow-hidden mb-8">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-150 text-slate-600 font-bold">
                            <th class="p-3.5 pl-4">Item Produk</th>
                            <th class="p-3.5 text-right">Harga Satuan</th>
                            <th class="p-3.5 text-center">Jumlah</th>
                            <th class="p-3.5 text-right pr-4">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php 
                        $subtotal_pesanan = 0;
                        while($detail = mysqli_fetch_assoc($query_detail)): 
                            $subtotal_item = $detail['harga'] * $detail['jumlah'];
                            $subtotal_pesanan += $subtotal_item;
                        ?>
                            <tr class="hover:bg-slate-50/40 transition-colors duration-150">
                                <td class="p-3.5 pl-4 font-semibold text-slate-800"><?= htmlspecialchars($detail['nama_produk']); ?></td>
                                <td class="p-3.5 text-right text-slate-600">Rp <?= number_format($detail['harga'], 0, ',', '.'); ?></td>
                                <td class="p-3.5 text-center text-slate-700 font-bold"><?= $detail['jumlah']; ?></td>
                                <td class="p-3.5 text-right font-bold text-slate-800 pr-4">Rp <?= number_format($subtotal_item, 0, ',', '.'); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Total Akhir Belanja -->
            <div class="flex justify-end mb-10">
                <div class="w-full sm:w-80 space-y-2 text-xs">
                    <div class="flex justify-between items-center text-slate-500">
                        <span>Subtotal Belanja</span>
                        <span>Rp <?= number_format($subtotal_pesanan, 0, ',', '.'); ?></span>
                    </div>
                    <div class="flex justify-between items-center text-slate-500">
                        <span>Ongkos Kirim</span>
                        <span class="text-emerald-600 font-semibold">Gratis</span>
                    </div>
                    <div class="h-px bg-slate-100 my-1"></div>
                    <div class="flex justify-between items-center text-sm font-black pt-1">
                        <span class="text-slate-800">Total Harga</span>
                        <span class="text-lime-600 text-base">Rp <?= number_format($pesanan['total_harga'], 0, ',', '.'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Footer Struk -->
            <div class="border-t border-slate-100 pt-6 text-center">
                <p class="text-xs font-semibold text-slate-500">Terima kasih telah berbelanja kerajinan autentik di HandMadura.</p>
                <p class="text-[10px] text-slate-400 mt-1">Struk ini dicetak secara otomatis dan merupakan bukti pembayaran yang sah.</p>
            </div>

        </div>
    </div>

    <!-- Script Autocetak -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 300);
        };
    </script>
</body>
</html>
