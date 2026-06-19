<?php
// admin/cetak_laporan.php: Halaman khusus cetak laporan (print view) untuk mencetak data keuangan, ulasan, atau pengguna dalam format cetak ramah printer.

include_once '../koneksi.php';

// Otorisasi Admin
if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

$tipe = isset($_GET['tipe']) ? $_GET['tipe'] : '';
$tipe_valid = ['pengguna', 'ulasan', 'keuangan'];

if (!in_array($tipe, $tipe_valid)) {
    die("Tipe laporan tidak valid.");
}

$judul_laporan = "";
$deskripsi_laporan = "";
$data_result = null;

if ($tipe === 'pengguna') {
    $judul_laporan = "Laporan Daftar Pengguna";
    $deskripsi_laporan = "Daftar lengkap seluruh pengguna terdaftar di platform HandMadura.";
    $data_result = kueri("SELECT id, nama, email, role, no_telp, alamat, tanggal_dibuat FROM pengguna ORDER BY role ASC, nama ASC");
    catat_log('pengguna', 'ekspor', "Mencetak laporan PDF daftar pengguna");
} elseif ($tipe === 'ulasan') {
    $judul_laporan = "Laporan Daftar Ulasan";
    $deskripsi_laporan = "Daftar lengkap testimonial dan ulasan pelanggan untuk produk HandMadura.";
    $data_result = kueri("SELECT t.*, p.email FROM testimonial t JOIN pengguna p ON t.id_pengguna = p.id ORDER BY t.tanggal_dibuat DESC");
    catat_log('testimoni', 'ekspor', "Mencetak laporan PDF daftar ulasan");
} else {
    $judul_laporan = "Laporan Keuangan & Transaksi";
    $deskripsi_laporan = "Laporan akumulasi seluruh pesanan sukses (dibayar, dikirim, selesai) beserta total pendapatan.";
    $data_result = kueri("
        SELECT p.*, u.nama, u.email 
        FROM pesanan p 
        JOIN pengguna u ON p.id_pengguna = u.id 
        WHERE p.status IN ('dibayar', 'dikirim', 'selesai') 
        ORDER BY p.tanggal_pesanan DESC
    ");
    catat_log('keuangan', 'ekspor', "Mencetak laporan PDF keuangan dan transaksi");
}

$total_pendapatan = 0;
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $judul_laporan; ?> - HandMadura</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background-color: white !important;
                color: black !important;
            }
            .print-area {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            tr {
                page-break-inside: avoid;
            }
            th {
                background-color: #f1f5f9 !important;
                color: #0f172a !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
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
<body class="bg-slate-100 dark:bg-slate-950 text-slate-800 dark:text-slate-100 min-h-screen py-8 px-4 sm:px-6 transition-colors duration-300">

    <!-- Action Bar (Floating, Hidden on Print) -->
    <div class="max-w-6xl mx-auto mb-6 no-print">
        <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border border-slate-200 dark:border-slate-800 p-4 rounded-2xl shadow-sm flex items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="javascript:window.close()" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-850 hover:bg-slate-200 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-350 transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="text-sm font-bold">Pratinjau Cetak</h2>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400">Siap diekspor ke berkas PDF</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="inline-flex items-center justify-center bg-lime-600 hover:bg-lime-700 text-white font-bold text-xs py-2 px-4 rounded-xl transition-all duration-300 hover:scale-[1.02] cursor-pointer">
                    <i class="fa-solid fa-print mr-2"></i> Cetak Laporan
                </button>
                <button id="tombol-tema" class="text-slate-400 dark:text-slate-500 hover:text-lime-600 dark:hover:text-lime-400 p-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-850 transition-colors cursor-pointer flex items-center justify-center">
                    <i id="ikon-tombol-tema" class="fa-solid fa-moon text-base"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Printable Paper Area -->
    <div class="print-area max-w-6xl mx-auto bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-850 shadow-md p-6 sm:p-10 rounded-3xl transition-colors duration-300">
        
        <!-- Document Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b border-slate-200 dark:border-slate-800 pb-6 mb-6 gap-4">
            <div>
                <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                    Hand<span class="text-lime-600">Madura.</span>
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Situs Katalog & Penjualan Produk Handmade Madura</p>
            </div>
            <div class="text-left sm:text-right">
                <div class="inline-block bg-lime-50 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400 font-extrabold text-[10px] px-2.5 py-1 rounded-lg uppercase tracking-wider mb-2">
                    Laporan Resmi
                </div>
                <p class="text-xs text-slate-400 dark:text-slate-500">Tanggal Cetak: <span class="font-bold text-slate-700 dark:text-slate-350"><?= date('d F Y, H:i'); ?> WIB</span></p>
            </div>
        </div>

        <!-- Document Metadata Info -->
        <div class="mb-6">
            <h2 class="text-lg font-extrabold text-slate-850 dark:text-slate-100 tracking-tight"><?= $judul_laporan; ?></h2>
            <p class="text-xs text-slate-500 dark:text-slate-450 mt-1"><?= $deskripsi_laporan; ?></p>
        </div>

        <!-- Table Container -->
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse text-[11px]">
                
                <?php if ($tipe === 'pengguna'): ?>
                    <thead>
                        <tr class="bg-slate-100 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-bold uppercase tracking-wider">
                            <th class="p-3 pl-4 rounded-l-lg">ID</th>
                            <th class="p-3">Nama Lengkap</th>
                            <th class="p-3">Email</th>
                            <th class="p-3">Role</th>
                            <th class="p-3">No. Telepon</th>
                            <th class="p-3">Alamat</th>
                            <th class="p-3 pr-4 rounded-r-lg">Tanggal Dibuat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        <?php if (mysqli_num_rows($data_result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($data_result)): ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-950/20 transition-colors">
                                    <td class="p-3 pl-4 font-bold text-slate-400">#U-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                    <td class="p-3 font-bold text-slate-800 dark:text-slate-200"><?= htmlspecialchars($row['nama']); ?></td>
                                    <td class="p-3 text-slate-600 dark:text-slate-405 font-medium"><?= htmlspecialchars($row['email']); ?></td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase <?= $row['role'] === 'admin' ? 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400'; ?>">
                                            <?= htmlspecialchars($row['role']); ?>
                                        </span>
                                    </td>
                                    <td class="p-3 text-slate-600 dark:text-slate-400"><?= htmlspecialchars($row['no_telp'] ?? '-'); ?></td>
                                    <td class="p-3 text-slate-600 dark:text-slate-400 max-w-[150px] truncate" title="<?= htmlspecialchars($row['alamat'] ?? ''); ?>"><?= htmlspecialchars($row['alamat'] ?? '-'); ?></td>
                                    <td class="p-3 pr-4 text-slate-400 font-medium"><?= date('d M Y, H:i', strtotime($row['tanggal_dibuat'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-400">Tidak ada data pengguna terdaftar.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                <?php elseif ($tipe === 'ulasan'): ?>
                    <thead>
                        <tr class="bg-slate-100 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-bold uppercase tracking-wider">
                            <th class="p-3 pl-4 rounded-l-lg">ID</th>
                            <th class="p-3">Pengulas</th>
                            <th class="p-3">Pekerjaan</th>
                            <th class="p-3">Isi Ulasan</th>
                            <th class="p-3 text-center">Rating</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 pr-4 rounded-r-lg">Tanggal Ulasan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        <?php if (mysqli_num_rows($data_result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($data_result)): ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-950/20 transition-colors">
                                    <td class="p-3 pl-4 font-bold text-slate-400">#T-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                    <td class="p-3">
                                        <p class="font-bold text-slate-800 dark:text-slate-200"><?= htmlspecialchars($row['nama']); ?></p>
                                        <p class="text-[9px] text-slate-450 dark:text-slate-500 font-medium"><?= htmlspecialchars($row['email']); ?></p>
                                    </td>
                                    <td class="p-3 text-slate-600 dark:text-slate-400 font-medium"><?= htmlspecialchars($row['pekerjaan'] ?? '-'); ?></td>
                                    <td class="p-3 text-slate-600 dark:text-slate-400 max-w-[200px] whitespace-normal leading-relaxed"><?= htmlspecialchars($row['isi_ulasan']); ?></td>
                                    <td class="p-3 text-center">
                                        <span class="text-amber-500 font-bold whitespace-nowrap">
                                            <?= str_repeat('★', (int)$row['rating']) . str_repeat('☆', 5 - (int)$row['rating']); ?>
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase <?= $row['status'] === 'approved' ? 'bg-lime-50 text-lime-700 dark:bg-lime-950/30 dark:text-lime-400' : ($row['status'] === 'rejected' ? 'bg-red-50 text-red-650 dark:bg-red-950/30 dark:text-red-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-950/30 dark:text-amber-400'); ?>">
                                            <?= htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td class="p-3 pr-4 text-slate-400 font-medium"><?= date('d M Y, H:i', strtotime($row['tanggal_dibuat'])); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-8 text-slate-400">Tidak ada ulasan testimonial.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>

                <?php else: ?>
                    <thead>
                        <tr class="bg-slate-100 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-bold uppercase tracking-wider">
                            <th class="p-3 pl-4 rounded-l-lg">No. Pesanan</th>
                            <th class="p-3">Pelanggan</th>
                            <th class="p-3">Metode Bayar</th>
                            <th class="p-3">Tanggal Transaksi</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 pr-4 rounded-r-lg text-right">Jumlah (IDR)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60">
                        <?php if (mysqli_num_rows($data_result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($data_result)): 
                                $total_pendapatan += floatval($row['total_harga']);
                            ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-950/20 transition-colors">
                                    <td class="p-3 pl-4 font-bold text-slate-800 dark:text-slate-200">#HM-<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                    <td class="p-3">
                                        <p class="font-bold text-slate-850 dark:text-slate-200"><?= htmlspecialchars($row['nama']); ?></p>
                                        <p class="text-[9px] text-slate-450 dark:text-slate-500 font-medium"><?= htmlspecialchars($row['email']); ?></p>
                                    </td>
                                    <td class="p-3 text-slate-650 dark:text-slate-400 font-semibold uppercase"><?= htmlspecialchars($row['metode_pembayaran'] ?? '-'); ?></td>
                                    <td class="p-3 text-slate-400 font-medium"><?= date('d M Y, H:i', strtotime($row['tanggal_pesanan'])); ?> WIB</td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold uppercase bg-lime-50 text-lime-700 dark:bg-lime-950/30 dark:text-lime-400">
                                            <?= htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td class="p-3 pr-4 font-bold text-right text-slate-850 dark:text-slate-200">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                            
                            <!-- Financial Summary Row -->
                            <tr class="bg-slate-50 dark:bg-slate-950/60 font-black text-xs border-t-2 border-slate-200 dark:border-slate-800">
                                <td colspan="5" class="p-4 pl-4 text-slate-850 dark:text-white uppercase tracking-wider">Total Pendapatan Keseluruhan</td>
                                <td class="p-4 pr-4 text-right text-lime-600 dark:text-lime-400 text-sm">Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-8 text-slate-400">Tidak ada transaksi keuangan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                <?php endif; ?>
                
            </table>
        </div>

        <!-- Footer Document (Visible on Print) -->
        <div class="mt-12 border-t border-slate-200 dark:border-slate-800 pt-6 flex flex-col sm:flex-row justify-between items-center text-[10px] text-slate-400 gap-4">
            <p>Laporan ini dihasilkan secara otomatis oleh sistem administrasi HandMadura.</p>
            <p>&copy; <?= date('Y'); ?> HandMadura. Hak Cipta Dilindungi.</p>
        </div>

    </div>

    <!-- Theme & Auto Print Scripts -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const tombolTema = document.getElementById('tombol-tema');
        const ikonTema = document.getElementById('ikon-tombol-tema');

        // Menyelaraskan ikon tombol tema dengan mode warna aktif (gelap/terang)
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

        // Auto print trigger and auto close
        setTimeout(() => {
            window.print();
            setTimeout(() => {
                window.close();
            }, 100);
        }, 600);
    });
    </script>
</body>
</html>
