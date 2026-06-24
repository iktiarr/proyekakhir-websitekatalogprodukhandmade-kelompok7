<?php
// admin/cetak_laporan.php: Halaman khusus cetak laporan (print view) untuk mencetak data keuangan, ulasan, atau pengguna dalam format cetak ramah printer dengan ringkasan statistik yang informatif.

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
    $judul_laporan = "Laporan Analisis & Daftar Pengguna";
    $deskripsi_laporan = "Daftar lengkap seluruh pengguna terdaftar beserta analisis status akun platform HandMadura.";
    $data_result = kueri("SELECT id, nama, email, role, no_telp, alamat, tanggal_dibuat FROM pengguna ORDER BY role ASC, nama ASC");
    
    // Hitung statistik pengguna
    $total_admin = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pengguna WHERE role = 'admin'"))['total'];
    $total_user = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pengguna WHERE role = 'user'"))['total'];
    $total_pengguna = $total_admin + $total_user;
    
    // Hitung pengguna dihapus dari log
    $total_dihapus = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM log_aktivitas WHERE tipe_aktivitas = 'pengguna' AND aksi = 'hapus'"))['total'];
    
    // Hitung kelengkapan data (misal alamat diisi)
    $lengkap_alamat = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pengguna WHERE role = 'user' AND alamat IS NOT NULL AND alamat != ''"))['total'];
    $persen_lengkap = $total_user > 0 ? round(($lengkap_alamat / $total_user) * 100) : 0;

    catat_log('pengguna', 'ekspor', "Mencetak laporan analisis daftar pengguna");

} elseif ($tipe === 'ulasan') {
    $judul_laporan = "Laporan Analisis & Daftar Ulasan";
    $deskripsi_laporan = "Analisis sentimen bintang, kata terpopuler, dan status ulasan testimonial pelanggan platform HandMadura.";
    $data_result = kueri("SELECT t.*, p.email FROM testimonial t JOIN pengguna p ON t.id_pengguna = p.id ORDER BY t.tanggal_dibuat DESC");
    
    // Hitung breakdown status testimonial
    $status_approved = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM testimonial WHERE status = 'approved'"))['total'];
    $status_pending = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM testimonial WHERE status = 'pending'"))['total'];
    $status_rejected = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM testimonial WHERE status = 'rejected'"))['total'];
    $total_testimoni = $status_approved + $status_pending + $status_rejected;
    
    // Hitung jumlah per rating bintang
    $rating_breakdown = [];
    for ($i = 5; $i >= 1; $i--) {
        $rating_breakdown[$i] = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM testimonial WHERE rating = ?", [$i]))['total'];
    }

    // Analisis frekuensi kata yang paling banyak ditulis (mengecualikan stop words)
    $res_isi = kueri("SELECT isi_ulasan FROM testimonial");
    $kata_hitung = [];
    $stop_words = [
        'dan', 'yang', 'di', 'ke', 'dari', 'untuk', 'saya', 'ini', 'itu', 'dengan', 
        'adalah', 'ada', 'bisa', 'tapi', 'yg', 'sangat', 'bagus', 'keren', 'cepat', 
        'baik', 'mantap', 'enak', 'oke', 'ok', 'ya', 'dibuat', 'atau', 'akan', 'pada', 
        'oleh', 'sebagai', 'ia', 'mereka', 'kita', 'kami', 'kamu', 'dia', 'anda', 
        'engkau', 'mu', 'nya', 'tersebut', 'terhadap', 'seperti', 'yaitu', 'yakni', 
        'bagi', 'secara', 'tentang', 'maka', 'bahwa', 'sehingga', 'serta', 'tetapi', 
        'namun', 'melainkan', 'sedangkan', 'tidak', 'tidaklah', 'juga', 'lah', 'kah',
        'sudah', 'telah', 'pernah', 'belum', 'akan', 'ingin', 'mau', 'khas', 'produk',
        'beli', 'harga', 'barang', 'toko', 'pelayanan', 'admin', 'bintang'
    ];

    while ($row_u = mysqli_fetch_assoc($res_isi)) {
        $teks_bersih = preg_replace('/[^a-zA-Z\s]/', '', $row_u['isi_ulasan']);
        $words = explode(' ', strtolower($teks_bersih));
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) > 2 && !in_array($word, $stop_words)) {
                if (isset($kata_hitung[$word])) {
                    $kata_hitung[$word]++;
                } else {
                    $kata_hitung[$word] = 1;
                }
            }
        }
    }
    arsort($kata_hitung);
    $top_words = array_slice($kata_hitung, 0, 5, true);

    catat_log('testimoni', 'ekspor', "Mencetak laporan analisis daftar ulasan");

} else {
    $judul_laporan = "Laporan Analisis Keuangan & Transaksi";
    $deskripsi_laporan = "Laporan rincian omzet, performa metode pembayaran, dan detail log transaksi penjualan.";
    $data_result = kueri("
        SELECT p.*, u.nama, u.email 
        FROM pesanan p 
        JOIN pengguna u ON p.id_pengguna = u.id 
        WHERE p.status IN ('dibayar', 'dikirim', 'selesai') 
        ORDER BY p.tanggal_pesanan DESC
    ");
    
    // Perhitungan total pendapatan
    $total_pendapatan = mysqli_fetch_assoc(kueri("SELECT SUM(total_harga) AS total FROM pesanan WHERE status IN ('dibayar', 'dikirim', 'selesai')"))['total'] ?? 0;
    
    // Statistik pesanan
    $total_transaksi = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pesanan"))['total'];
    $transaksi_sukses = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pesanan WHERE status IN ('dibayar', 'dikirim', 'selesai')"))['total'];
    $transaksi_batal = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pesanan WHERE status = 'dibatalkan'"))['total'];
    $transaksi_menunggu = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pesanan WHERE status = 'menunggu'"))['total'];
    
    $rata_rata_transaksi = $transaksi_sukses > 0 ? ($total_pendapatan / $transaksi_sukses) : 0;

    // Statistik Metode Pembayaran Terpopuler
    $res_metode = kueri("
        SELECT metode_pembayaran, COUNT(*) as jumlah, SUM(total_harga) as total_nominal 
        FROM pesanan 
        WHERE status IN ('dibayar', 'dikirim', 'selesai') 
        GROUP BY metode_pembayaran 
        ORDER BY jumlah DESC
    ");
    $daftar_metode = [];
    while ($row_m = mysqli_fetch_assoc($res_metode)) {
        $daftar_metode[] = $row_m;
    }

    catat_log('keuangan', 'ekspor', "Mencetak laporan analisis keuangan");
}
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
                <button id="tombol-tema" class="text-slate-400 dark:text-slate-550 hover:text-lime-600 dark:hover:text-lime-400 p-2.5 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-850 transition-colors cursor-pointer flex items-center justify-center">
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
        <div class="mb-8">
            <h2 class="text-lg font-extrabold text-slate-850 dark:text-slate-100 tracking-tight"><?= $judul_laporan; ?></h2>
            <p class="text-xs text-slate-500 dark:text-slate-450 mt-1"><?= $deskripsi_laporan; ?></p>
        </div>

        <!-- ========================================== -->
        <!-- 1. TAMPILAN LAPORAN PENGGUNA               -->
        <!-- ========================================== -->
        <?php if ($tipe === 'pengguna'): ?>
            
            <!-- Executive Summary Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 rounded-2xl flex flex-col justify-between">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Pengguna Saat Ini</span>
                    <h3 class="text-xl font-extrabold text-slate-850 dark:text-slate-100 mt-2"><?= $total_user; ?> Akun</h3>
                    <p class="text-[10px] text-slate-500 mt-1">Role akses pengguna biasa (non-admin)</p>
                </div>
                
                <div class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 rounded-2xl flex flex-col justify-between">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Total Admin</span>
                    <h3 class="text-xl font-extrabold text-slate-850 dark:text-slate-100 mt-2"><?= $total_admin; ?> Akun</h3>
                    <p class="text-[10px] text-slate-500 mt-1">Akun pengelola sistem admin</p>
                </div>

                <div class="p-4 bg-red-50/40 dark:bg-red-950/10 border border-red-150 dark:border-red-900/30 rounded-2xl flex flex-col justify-between">
                    <span class="text-[9px] font-bold text-red-550 uppercase tracking-widest">Pengguna Dihapus</span>
                    <h3 class="text-xl font-extrabold text-red-650 dark:text-red-400 mt-2"><?= $total_dihapus; ?> Akun</h3>
                    <p class="text-[10px] text-red-500 mt-1">Total riwayat penghapusan user</p>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 rounded-2xl flex flex-col justify-between">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Kelengkapan Profil</span>
                    <h3 class="text-xl font-extrabold text-slate-850 dark:text-slate-100 mt-2"><?= $persen_lengkap; ?>%</h3>
                    <p class="text-[10px] text-slate-500 mt-1"><?= $lengkap_alamat; ?> dari <?= $total_user; ?> user mengisi alamat</p>
                </div>
            </div>

        <!-- ========================================== -->
        <!-- 2. TAMPILAN LAPORAN ULASAN TESTIMONIAL     -->
        <!-- ========================================== -->
        <?php elseif ($tipe === 'ulasan'): ?>
            
            <!-- Summary stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Status Breakdown Card -->
                <div class="p-5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 rounded-2xl">
                    <h3 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-4">Breakdown Status Testimoni</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-semibold text-slate-600 dark:text-slate-400 flex items-center"><i class="fa-solid fa-circle-check text-lime-500 mr-2 text-[10px]"></i> Disetujui (Approved)</span>
                            <span class="font-extrabold text-slate-800 dark:text-slate-200"><?= $status_approved; ?></span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-semibold text-slate-600 dark:text-slate-400 flex items-center"><i class="fa-solid fa-circle-notch fa-spin text-amber-500 mr-2 text-[10px]"></i> Menunggu (Pending)</span>
                            <span class="font-extrabold text-slate-800 dark:text-slate-200"><?= $status_pending; ?></span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-semibold text-slate-600 dark:text-slate-400 flex items-center"><i class="fa-solid fa-circle-xmark text-red-500 mr-2 text-[10px]"></i> Ditolak (Rejected)</span>
                            <span class="font-extrabold text-slate-800 dark:text-slate-200"><?= $status_rejected; ?></span>
                        </div>
                        <div class="h-px bg-slate-200 dark:bg-slate-800 my-1"></div>
                        <div class="flex justify-between items-center text-xs font-bold">
                            <span>Total Testimonial</span>
                            <span><?= $total_testimoni; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Rating Breakdown Card -->
                <div class="p-5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 rounded-2xl">
                    <h3 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-4">Distribusi Bintang Rating</h3>
                    <div class="space-y-2">
                        <?php foreach ($rating_breakdown as $bintang => $jumlah): 
                            $persen = $total_testimoni > 0 ? round(($jumlah / $total_testimoni) * 100) : 0;
                        ?>
                            <div class="flex items-center text-xs gap-2">
                                <span class="font-bold text-slate-600 dark:text-slate-450 w-8 whitespace-nowrap"><?= $bintang; ?> ★</span>
                                <div class="flex-grow bg-slate-200 dark:bg-slate-800 h-2 rounded-full overflow-hidden">
                                    <div class="bg-amber-500 h-full rounded-full" style="width: <?= $persen; ?>%"></div>
                                </div>
                                <span class="font-bold text-slate-800 dark:text-slate-200 w-8 text-right"><?= $jumlah; ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Word Sentiment / Keyword Frequency Card -->
                <div class="p-5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 rounded-2xl">
                    <h3 class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-4">Kata Terbanyak Ditulis</h3>
                    <div class="space-y-3">
                        <?php if (!empty($top_words)): ?>
                            <?php 
                            $no = 1;
                            foreach ($top_words as $kata => $frek): ?>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="font-semibold text-slate-700 dark:text-slate-350 flex items-center">
                                        <span class="w-4 h-4 rounded bg-lime-100 dark:bg-lime-950/60 text-lime-700 dark:text-lime-400 flex items-center justify-center font-bold text-[9px] mr-2"><?= $no++; ?></span>
                                        "<?= htmlspecialchars($kata); ?>"
                                    </span>
                                    <span class="font-bold text-slate-500 dark:text-slate-400"><?= $frek; ?> kali</span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-xs text-slate-400 py-4 text-center">Belum ada analisis kata yang cukup.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        <!-- ========================================== -->
        <!-- 3. TAMPILAN LAPORAN KEUANGAN & TRANSAKSI   -->
        <!-- ========================================== -->
        <?php else: ?>
            
            <!-- Summary stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 rounded-2xl flex flex-col justify-between">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Total Pendapatan Bersih</span>
                    <h3 class="text-base sm:text-lg font-black text-lime-600 dark:text-lime-400 mt-2">Rp <?= number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                    <p class="text-[10px] text-slate-500 mt-1">Dari transaksi lunas & selesai</p>
                </div>
                
                <div class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 rounded-2xl flex flex-col justify-between">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Rata-rata Nilai Transaksi</span>
                    <h3 class="text-base sm:text-base font-extrabold text-slate-800 dark:text-slate-200 mt-2">Rp <?= number_format($rata_rata_transaksi, 0, ',', '.'); ?></h3>
                    <p class="text-[10px] text-slate-500 mt-1">Rata-rata keranjang belanja</p>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 rounded-2xl flex flex-col justify-between">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Volume Transaksi Sukses</span>
                    <h3 class="text-xl font-extrabold text-slate-850 dark:text-slate-100 mt-2"><?= $transaksi_sukses; ?> Pesanan</h3>
                    <p class="text-[10px] text-slate-500 mt-1">Total pesanan terbayar / dikirim / selesai</p>
                </div>

                <div class="p-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 rounded-2xl flex flex-col justify-between">
                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Rasio Status Transaksi</span>
                    <h3 class="text-xs font-bold text-slate-850 dark:text-slate-100 mt-2">
                        <span class="text-lime-600 font-extrabold"><?= $transaksi_sukses; ?> Sks</span> / 
                        <span class="text-red-500 font-extrabold"><?= $transaksi_batal; ?> Btl</span> / 
                        <span class="text-amber-500 font-extrabold"><?= $transaksi_menunggu; ?> Mng</span>
                    </h3>
                    <p class="text-[10px] text-slate-500 mt-1">Dari total <?= $total_transaksi; ?> pesanan masuk</p>
                </div>
            </div>            <!-- Payment Methods Breakdown Section -->
            <div class="mb-8 p-5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 rounded-3xl max-w-md">
                <h3 class="text-xs font-bold text-slate-800 dark:text-slate-100 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-credit-card text-lime-600"></i> Analisis Performa Metode Pembayaran
                </h3>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 mb-4">Penggunaan metode transaksi di kalangan pembeli HandMadura.</p>
                
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse text-[10px]">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-800 text-slate-400 font-bold uppercase tracking-wider">
                                <th class="py-2 pl-2">Metode Pembayaran</th>
                                <th class="py-2 text-right pr-2">Penggunaan (Persentase)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-850">
                            <?php if (!empty($daftar_metode)): ?>
                                <?php foreach ($daftar_metode as $met): 
                                    $persen_met = $transaksi_sukses > 0 ? round(($met['jumlah'] / $transaksi_sukses) * 100) : 0;
                                ?>
                                    <tr class="hover:bg-slate-100/50 dark:hover:bg-slate-900/30">
                                        <td class="py-2.5 pl-2 font-bold text-slate-700 dark:text-slate-350"><?= htmlspecialchars($met['metode_pembayaran'] ?? 'Lainnya/VA'); ?></td>
                                        <td class="py-2.5 text-right font-black pr-2 text-lime-600 dark:text-lime-400"><?= $persen_met; ?>% <span class="text-slate-400 dark:text-slate-500 font-semibold text-[9px] ml-1">(<?= $met['jumlah']; ?> pesanan)</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-slate-400">Belum ada data transaksi lunas.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php endif; ?>

        <!-- ========================================== -->
        <!-- DETAIL DATA TABLE                          -->
        <!-- ========================================== -->
        <div class="mb-4">
            <h3 class="text-xs font-bold text-slate-800 dark:text-slate-100 mb-3.5 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                <i class="fa-solid fa-list mr-1"></i> Rincian Data Laporan Lengkap
            </h3>
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full text-left border-collapse text-[10px] sm:text-[11px]">
                
                <?php if ($tipe === 'pengguna'): ?>
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-bold uppercase tracking-wider">
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
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-955/20 transition-colors">
                                    <td class="p-3 pl-4 font-bold text-slate-400">#U-<?= str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?></td>
                                    <td class="p-3 font-bold text-slate-800 dark:text-slate-200"><?= htmlspecialchars($row['nama']); ?></td>
                                    <td class="p-3 text-slate-655 dark:text-slate-400 font-medium"><?= htmlspecialchars($row['email']); ?></td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded text-[8px] font-bold uppercase <?= $row['role'] === 'admin' ? 'bg-red-50 text-red-700 dark:bg-red-950/30 dark:text-red-400' : 'bg-blue-50 text-blue-700 dark:bg-blue-950/30 dark:text-blue-400'; ?>">
                                            <?= htmlspecialchars($row['role']); ?>
                                        </span>
                                    </td>
                                    <td class="p-3 text-slate-600 dark:text-slate-400"><?= htmlspecialchars($row['no_telp'] ?? '-'); ?></td>
                                    <td class="p-3 text-slate-600 dark:text-slate-400 max-w-[180px] truncate" title="<?= htmlspecialchars($row['alamat'] ?? ''); ?>"><?= htmlspecialchars($row['alamat'] ?? '-'); ?></td>
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
                        <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-bold uppercase tracking-wider">
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
                                        <p class="text-[8px] text-slate-450 dark:text-slate-500 font-medium"><?= htmlspecialchars($row['email']); ?></p>
                                    </td>
                                    <td class="p-3 text-slate-600 dark:text-slate-400 font-medium"><?= htmlspecialchars($row['pekerjaan'] ?? '-'); ?></td>
                                    <td class="p-3 text-slate-600 dark:text-slate-405 max-w-[220px] whitespace-normal leading-relaxed">"<?= htmlspecialchars($row['isi_ulasan']); ?>"</td>
                                    <td class="p-3 text-center">
                                        <span class="text-amber-500 font-bold whitespace-nowrap">
                                            <?= str_repeat('★', (int)$row['rating']) . str_repeat('☆', 5 - (int)$row['rating']); ?>
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        <span class="px-2 py-0.5 rounded text-[8px] font-bold uppercase <?= $row['status'] === 'approved' ? 'bg-lime-50 text-lime-700 dark:bg-lime-950/30 dark:text-lime-400' : ($row['status'] === 'rejected' ? 'bg-red-50 text-red-650 dark:bg-red-950/30 dark:text-red-400' : 'bg-amber-50 text-amber-600 dark:bg-amber-950/30 dark:text-amber-400'); ?>">
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
                        <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-bold uppercase tracking-wider">
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
                            <?php while ($row = mysqli_fetch_assoc($data_result)): ?>
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-955/20 transition-colors">
                                    <td class="p-3 pl-4 font-mono font-bold text-slate-800 dark:text-slate-200">#HM-<?= str_pad($row['id'], 5, '0', STR_PAD_LEFT); ?></td>
                                    <td class="p-3">
                                        <p class="font-bold text-slate-850 dark:text-slate-200"><?= htmlspecialchars($row['nama']); ?></p>
                                        <p class="text-[8px] text-slate-450 dark:text-slate-500 font-medium"><?= htmlspecialchars($row['email']); ?></p>
                                    </td>
                                    <td class="p-3 text-slate-655 dark:text-slate-400 font-semibold uppercase"><?= htmlspecialchars($row['metode_pembayaran'] ?? '-'); ?></td>
                                    <td class="p-3 text-slate-400 font-medium"><?= date('d M Y, H:i', strtotime($row['tanggal_pesanan'])); ?> WIB</td>
                                    <td class="p-3">
                                        <?php
                                        $warna_badge = '';
                                        if ($row['status'] === 'dibayar') {
                                            $warna_badge = 'bg-blue-50 text-blue-700 dark:bg-blue-950/20 dark:text-blue-400 border border-blue-200 dark:border-blue-900/30';
                                        } elseif ($row['status'] === 'dikirim') {
                                            $warna_badge = 'bg-indigo-50 text-indigo-750 dark:bg-indigo-950/20 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-900/30';
                                        } elseif ($row['status'] === 'selesai') {
                                            $warna_badge = 'bg-lime-50 text-lime-750 dark:bg-lime-950/20 dark:text-lime-400 border border-lime-200 dark:border-lime-900/30';
                                        } elseif ($row['status'] === 'menunggu') {
                                            $warna_badge = 'bg-amber-50 text-amber-600 dark:bg-amber-950/20 dark:text-amber-400 border border-amber-200 dark:border-amber-900/30';
                                        } else {
                                            $warna_badge = 'bg-red-50 text-red-650 dark:bg-red-950/20 dark:text-red-400 border border-red-200 dark:border-red-900/30';
                                        }
                                        ?>
                                        <span class="px-2 py-0.5 rounded text-[8px] font-bold uppercase <?= $warna_badge; ?>">
                                            <?= htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td class="p-3 pr-4 font-bold text-right text-slate-850 dark:text-slate-200">Rp <?= number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                </tr>
                            <?php endwhile; ?>
                            
                            <!-- Financial Summary Row -->
                            <tr class="bg-slate-50 dark:bg-slate-950 font-black border-t-2 border-slate-200 dark:border-slate-800 text-[11px] sm:text-xs">
                                <td colspan="5" class="p-4 pl-4 text-slate-850 dark:text-white uppercase tracking-wider">Total Pendapatan Akumulasi</td>
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
        <div class="mt-12 border-t border-slate-200 dark:border-slate-800 pt-6 flex flex-col sm:flex-row justify-between items-center text-[9px] text-slate-400 gap-4">
            <p>Laporan analisis ini dihasilkan secara otomatis oleh sistem administrasi HandMadura.</p>
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

        // Auto print trigger after style matches and resources load
        setTimeout(() => {
            window.print();
        }, 800);
    });
    </script>
</body>
</html>
