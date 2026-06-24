<?php
// admin/laporan.php: Halaman kelola laporan kendala dan pengaduan pengguna untuk diverifikasi oleh admin.

include '../koneksi.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

// Proses pembaruan status laporan kendala jika ada request
if (isset($_GET['id']) && isset($_GET['status'])) {
    $id_laporan = (int) $_GET['id'];
    $status_baru = $_GET['status'];

    $valid_status = ['pending', 'diproses', 'selesai'];
    if (in_array($status_baru, $valid_status)) {
        // Ambil data laporan sebelum update untuk keperluan pencatatan log
        $res_l = kueri("SELECT l.*, p.nama FROM laporan_kendala l JOIN pengguna p ON l.id_pengguna = p.id WHERE l.id = ?", [$id_laporan]);
        if ($row_l = mysqli_fetch_assoc($res_l)) {
            $nama_pengirim = $row_l['nama'];
            if (kueri("UPDATE laporan_kendala SET status = ? WHERE id = ?", [$status_baru, $id_laporan])) {
                $status_ket = [
                    'diproses' => 'Memproses laporan kendala',
                    'selesai' => 'Menyelesaikan laporan kendala'
                ];
                $ket_aksi = isset($status_ket[$status_baru]) ? $status_ket[$status_baru] : "Mengubah status laporan #LK-$id_laporan menjadi '$status_baru'";
                catat_log('laporan', $status_baru, "$ket_aksi dari pengguna '$nama_pengirim'");
            }
        }
    }
    header("Location: laporan.php");
    exit();
}

$pembayaran_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pesanan WHERE status = 'dibayar'"))['total'];
$testimoni_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM testimonial WHERE status = 'pending'"))['total'];
$laporan_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM laporan_kendala WHERE status = 'pending'"))['total'];

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';
?>

<!doctype html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Laporan Kendala - HandMadura Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Mobile Sidebar Custom Transitions */
        @media (max-width: 767px) {
            #sidebar {
                transform: translateX(-100%) !important;
                transition: transform 0.3s ease-in-out !important;
            }
            #sidebar.active {
                transform: translateX(0) !important;
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

<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 flex flex-col md:flex-row selection:bg-lime-200 selection:text-lime-900 transition-colors duration-300 min-h-screen">
    
    <!-- Header Seluler (Mobile Navbar) -->
    <header class="md:hidden bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-3 py-2.5 sticky top-0 z-40 flex items-center gap-3 w-full transition-colors duration-300">
        <button id="tombol-menu-mobile" class="p-1.5 -ml-1.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 transition-colors focus:outline-none flex items-center justify-center cursor-pointer">
            <i class="fa-solid fa-bars text-lg"></i>
        </button>
        <a href="../index.php" class="text-lg font-extrabold text-slate-800 dark:text-slate-200 tracking-tight">
            Hand<span class="text-lime-600">Madura.</span>
        </a>
    </header>

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-56 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 flex flex-col transition-all duration-300 md:sticky md:h-screen md:top-0 overflow-y-auto flex-shrink-0 shadow-lg md:shadow-none">
        <div class="p-3 pb-2 flex items-center justify-between">
            <div>
                <a href="../index.php" class="text-lg font-extrabold text-slate-800 dark:text-slate-200 tracking-tight inline-block">
                    Hand<span class="text-lime-600">Madura.</span>
                </a>
                <p class="text-[8px] uppercase tracking-widest text-slate-400 dark:text-slate-550 font-bold mt-0.5">Admin Panel</p>
            </div>
            <button id="tombol-tutup-sidebar" class="md:hidden p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer flex items-center justify-center" title="Tutup Sidebar">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        
        <nav class="flex-1 px-2 space-y-0.5">
            <a href="index.php" class="flex items-center px-2.5 py-1.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-bold text-xs transition-colors group">
                <i class="fa-solid fa-chart-pie mr-2 w-4 text-center"></i> Dasbor
            </a>
            <a href="produk.php" class="flex items-center px-2.5 py-1.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-bold text-xs transition-colors group">
                <i class="fa-solid fa-box-open mr-2 w-4 text-center"></i> Produk
            </a>
            <a href="pembayaran.php" class="flex items-center px-2.5 py-1.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-bold text-xs transition-colors group">
                <i class="fa-solid fa-credit-card mr-2 w-4 text-center"></i> Pembayaran
                <?php if ($pembayaran_tertunda > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full"><?= $pembayaran_tertunda; ?></span>
                <?php endif; ?>
            </a>
            <a href="testimoni.php" class="flex items-center px-2.5 py-1.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-bold text-xs transition-colors group">
                <i class="fa-solid fa-comments mr-2 w-4 text-center"></i> Testimonial
                <?php if ($testimoni_tertunda > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full"><?= $testimoni_tertunda; ?></span>
                <?php endif; ?>
            </a>
            <a href="pengguna.php" class="flex items-center px-2.5 py-1.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-bold text-xs transition-colors group">
                <i class="fa-solid fa-users mr-2 w-4 text-center"></i> Pengguna
            </a>
            <a href="laporan.php" class="flex items-center px-2.5 py-1.5 bg-lime-50 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400 rounded-xl font-bold text-xs transition-colors">
                <i class="fa-solid fa-circle-exclamation mr-2 w-4 text-center"></i> Laporan Kendala
                <?php if ($laporan_tertunda > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full"><?= $laporan_tertunda; ?></span>
                <?php endif; ?>
            </a>
        </nav>
        
        <div class="p-2 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-1">
            <a href="../keluar.php?dari=admin" class="flex items-center px-2.5 py-1.5 text-slate-400 dark:text-slate-550 hover:text-red-655 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl font-bold text-xs transition-colors group flex-grow">
                <i class="fa-solid fa-arrow-right-from-bracket mr-2 w-4 text-center"></i> Keluar
            </a>
            <button id="tombol-tema" class="text-slate-400 hover:text-lime-600 dark:text-slate-400 dark:hover:text-lime-400 p-1.5 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer flex items-center justify-center" title="Ubah Tema">
                <i id="ikon-tombol-tema" class="fa-solid fa-moon text-base"></i>
            </button>
        </div>
    </aside>

    <!-- Latar Buram Seluler (Backdrop Overlay) -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 opacity-0 pointer-events-none transition-opacity duration-300"></div>
    <div class="hidden opacity-100 pointer-events-auto"></div>

    <main class="flex-grow p-4 sm:p-6 w-full max-w-6xl mx-auto overflow-x-hidden">
        
        <div class="mb-6">
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Kelola Laporan Kendala</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">Pantau, tindak lanjuti, dan verifikasi berkas bukti dari laporan kendala transaksi pengguna.</p>
        </div>

        <!-- Tab Penyaringan Status -->
        <div class="flex flex-wrap bg-slate-100/70 dark:bg-slate-900 p-1 rounded-xl mb-6 border border-slate-200/50 dark:border-slate-800 gap-1 sm:gap-0">
            <a href="?status=all" class="px-3.5 py-1.5 rounded-md text-xs font-bold transition-all duration-200 <?= $status_filter === 'all' ? 'bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 hover:bg-slate-200/50 dark:hover:bg-slate-800/40'; ?>">
                Semua Laporan
            </a>
            <a href="?status=pending" class="px-3.5 py-1.5 rounded-md text-xs font-bold transition-all duration-200 flex items-center <?= $status_filter === 'pending' ? 'bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 hover:bg-slate-200/50 dark:hover:bg-slate-800/40'; ?>">
                Pending
                <?php if ($laporan_tertunda > 0): ?>
                    <span class="ml-1.5 bg-red-100 dark:bg-red-950 text-red-700 dark:text-red-400 text-[9px] px-1 py-0.5 rounded-md font-extrabold"><?= $laporan_tertunda; ?></span>
                <?php endif; ?>
            </a>
            <a href="?status=diproses" class="px-3.5 py-1.5 rounded-md text-xs font-bold transition-all duration-200 <?= $status_filter === 'diproses' ? 'bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 hover:bg-slate-200/50 dark:hover:bg-slate-800/40'; ?>">
                Diproses
            </a>
            <a href="?status=selesai" class="px-3.5 py-1.5 rounded-md text-xs font-bold transition-all duration-200 <?= $status_filter === 'selesai' ? 'bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 hover:bg-slate-200/50 dark:hover:bg-slate-800/40'; ?>">
                Selesai
            </a>
        </div>

        <?php
        if ($status_filter === 'all') {
            $kueri_laporan_admin = kueri("
                SELECT l.*, p.nama, p.email, p.no_telp 
                FROM laporan_kendala l 
                JOIN pengguna p ON l.id_pengguna = p.id 
                ORDER BY l.tanggal_dibuat DESC
            ");
        } else {
            $kueri_laporan_admin = kueri("
                SELECT l.*, p.nama, p.email, p.no_telp 
                FROM laporan_kendala l 
                JOIN pengguna p ON l.id_pengguna = p.id 
                WHERE l.status = ?
                ORDER BY l.tanggal_dibuat DESC
            ", [$status_filter]);
        }
        ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php if (mysqli_num_rows($kueri_laporan_admin) > 0): ?>
                <?php while ($lap = mysqli_fetch_assoc($kueri_laporan_admin)): 
                    $kelas_lencana = '';
                    $kelas_ikon = '';
                    if ($lap['status'] === 'selesai') {
                        $kelas_lencana = 'bg-lime-50 dark:bg-lime-900/20 text-lime-700 dark:text-lime-400 border-lime-200 dark:border-lime-900/30';
                        $kelas_ikon = 'fa-circle-check';
                    } elseif ($lap['status'] === 'diproses') {
                        $kelas_lencana = 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-900/30';
                        $kelas_ikon = 'fa-spinner fa-spin';
                    } else {
                        $kelas_lencana = 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-900/30';
                        $kelas_ikon = 'fa-clock';
                    }
                ?>
                    <div class="bg-white dark:bg-slate-900 p-5 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm transition-all duration-300 flex flex-col group justify-between">
                        
                        <div>
                            <!-- Header Laporan -->
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 line-clamp-1"><?= htmlspecialchars($lap['nama']); ?></h3>
                                    <p class="text-[9px] font-semibold text-slate-500 dark:text-slate-400 line-clamp-1"><?= htmlspecialchars($lap['email']); ?></p>
                                    <p class="text-[9px] font-semibold text-slate-500 dark:text-slate-400 line-clamp-1"><?= htmlspecialchars($lap['no_telp'] ?? '-'); ?></p>
                                </div>
                                <span class="px-2 py-0.5 rounded-xl text-[9px] font-bold uppercase tracking-wider border <?= $kelas_lencana; ?> whitespace-nowrap">
                                    <i class="fa-solid <?= $kelas_ikon; ?> mr-0.5"></i> <?= $lap['status']; ?>
                                </span>
                            </div>

                            <div class="h-px bg-slate-100 dark:bg-slate-800 my-2.5"></div>

                            <!-- Detail Deskripsi -->
                            <div class="space-y-1.5 mb-4">
                                <div class="flex items-center gap-1.5 text-[9px] font-extrabold uppercase tracking-wider text-slate-400">
                                    <span>Tipe: <?= htmlspecialchars($lap['tipe_laporan']); ?></span>
                                    <?php if ($lap['id_pesanan']): ?>
                                        <span>•</span>
                                        <span class="text-lime-600 dark:text-lime-400">Pesanan #KM-<?= str_pad($lap['id_pesanan'], 5, '0', STR_PAD_LEFT); ?></span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-slate-600 dark:text-slate-300 text-xs sm:text-sm leading-relaxed italic">
                                    "<?= htmlspecialchars($lap['deskripsi']); ?>"
                                </p>
                            </div>
                        </div>

                        <!-- Footer & Aksi -->
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mt-auto">
                            <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500">
                                <i class="fa-regular fa-calendar-days mr-1 text-[10px]"></i> <?= date('d M Y, H:i', strtotime($lap['tanggal_dibuat'])); ?> WIB
                            </span>

                            <div class="flex items-center gap-2">
                                <!-- Tombol File Bukti -->
                                <a href="../<?= $lap['file_laporan']; ?>" target="_blank" class="w-8 h-8 flex items-center justify-center bg-slate-50 dark:bg-slate-950 text-slate-500 dark:text-slate-400 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-800 hover:text-slate-850 dark:hover:text-slate-250 border border-slate-200 dark:border-slate-800 transition-colors" title="Lihat Berkas Lampiran">
                                    <i class="fa-solid fa-paperclip text-xs"></i>
                                </a>

                                <?php if ($lap['status'] === 'pending'): ?>
                                    <a href="?id=<?= $lap['id']; ?>&status=diproses" class="w-8 h-8 flex items-center justify-center bg-indigo-50 dark:bg-indigo-950/20 text-indigo-600 dark:text-indigo-400 rounded-xl hover:bg-indigo-600 hover:text-white transition-colors border border-indigo-200 dark:border-indigo-900/30" title="Tandai Diproses">
                                        <i class="fa-solid fa-spinner text-xs"></i>
                                    </a>
                                <?php endif; ?>

                                <?php if ($lap['status'] !== 'selesai'): ?>
                                    <a href="?id=<?= $lap['id']; ?>&status=selesai" class="w-8 h-8 flex items-center justify-center bg-lime-50 dark:bg-lime-950/20 text-lime-600 dark:text-lime-400 rounded-xl hover:bg-lime-600 hover:text-white transition-colors border border-lime-200 dark:border-lime-900/30" title="Tandai Selesai / Selesai Verifikasi">
                                        <i class="fa-solid fa-check text-xs"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>

                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-16 bg-white dark:bg-slate-900 rounded-xl border border-dashed border-slate-200 dark:border-slate-800">
                    <div class="w-12 h-12 bg-slate-50 dark:bg-slate-950 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 dark:text-slate-700">
                        <i class="fa-solid fa-circle-exclamation text-xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-0.5">Tidak ada laporan kendala</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Kategori ini belum memiliki data laporan kendala masuk dari pengguna.</p>
                </div>
            <?php endif; ?>
        </div>

    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
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
                    sidebar.classList.add('active');
                    backdrop.classList.replace('opacity-0', 'opacity-100');
                    backdrop.classList.replace('pointer-events-none', 'pointer-events-auto');
                    document.body.style.overflow = 'hidden';
                }
            }

            function tutupSidebar() {
                if (sidebar && backdrop) {
                    sidebar.classList.remove('active');
                    backdrop.classList.replace('opacity-100', 'opacity-0');
                    backdrop.classList.replace('pointer-events-auto', 'pointer-events-none');
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
