<?php
include '../koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

$testimoni_tertunda = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM testimonial WHERE status = 'pending'"))['total'];
$pembayaran_tertunda = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pesanan WHERE status = 'dibayar'"))['total'];

$aksi = isset($_GET['action']) ? $_GET['action'] : 'list';
$id_testimoni = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (isset($_GET['approve']) && $id_testimoni > 0) {
    $res_t = mysqli_query($koneksi, "SELECT t.*, p.nama FROM testimonial t JOIN pengguna p ON t.id_pengguna = p.id WHERE t.id = $id_testimoni");
    if ($row_t = mysqli_fetch_assoc($res_t)) {
        $nama_pengulas = mysqli_real_escape_string($koneksi, $row_t['nama']);
        if (mysqli_query($koneksi, "UPDATE testimonial SET status = 'approved' WHERE id = $id_testimoni")) {
            $nama_admin = mysqli_real_escape_string($koneksi, $_SESSION['nama']);
            $id_admin = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NULL';
            mysqli_query($koneksi, "INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan) VALUES ($id_admin, '$nama_admin', 'testimoni', 'setujui', 'Menyetujui testimoni dari \'$nama_pengulas\'')");
        }
    }
    header("Location: testimoni.php");
    exit();
}

if (isset($_GET['reject']) && $id_testimoni > 0) {
    $res_t = mysqli_query($koneksi, "SELECT t.*, p.nama FROM testimonial t JOIN pengguna p ON t.id_pengguna = p.id WHERE t.id = $id_testimoni");
    if ($row_t = mysqli_fetch_assoc($res_t)) {
        $nama_pengulas = mysqli_real_escape_string($koneksi, $row_t['nama']);
        if (mysqli_query($koneksi, "UPDATE testimonial SET status = 'rejected' WHERE id = $id_testimoni")) {
            $nama_admin = mysqli_real_escape_string($koneksi, $_SESSION['nama']);
            $id_admin = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NULL';
            mysqli_query($koneksi, "INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan) VALUES ($id_admin, '$nama_admin', 'testimoni', 'tolak', 'Menolak testimoni dari \'$nama_pengulas\'')");
        }
    }
    header("Location: testimoni.php");
    exit();
}

if (isset($_GET['delete']) && $id_testimoni > 0) {
    $res_t = mysqli_query($koneksi, "SELECT t.*, p.nama FROM testimonial t JOIN pengguna p ON t.id_pengguna = p.id WHERE t.id = $id_testimoni");
    if ($row_t = mysqli_fetch_assoc($res_t)) {
        $nama_pengulas = mysqli_real_escape_string($koneksi, $row_t['nama']);
        if (mysqli_query($koneksi, "DELETE FROM testimonial WHERE id = $id_testimoni")) {
            $nama_admin = mysqli_real_escape_string($koneksi, $_SESSION['nama']);
            $id_admin = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NULL';
            mysqli_query($koneksi, "INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan) VALUES ($id_admin, '$nama_admin', 'testimoni', 'hapus', 'Menghapus testimoni dari \'$nama_pengulas\'')");
        }
    }
    header("Location: testimoni.php");
    exit();
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Testimoni - HandMadura Admin</title>
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
                <a href="../index.php" class="text-xl font-extrabold text-slate-800 dark:text-slate-200 tracking-tight inline-block hover:scale-105 transition-transform">
                    Hand<span class="text-lime-600">Madura.</span>
                </a>
                <p class="text-[9px] uppercase tracking-widest text-slate-400 dark:text-slate-500 font-bold mt-0.5">Admin Panel</p>
            </div>
            <button id="tombol-tutup-sidebar" class="md:hidden p-2 rounded-xl text-slate-450 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer flex items-center justify-center" title="Tutup Sidebar">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>
        
        <nav class="flex-1 px-3 space-y-1">
            <a href="index.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-medium text-sm transition-colors group">
                <i class="fa-solid fa-chart-pie mr-2.5 w-4 text-center group-hover:scale-110 transition-transform"></i> Dasbor
            </a>
            <a href="produk.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-medium text-sm transition-colors group">
                <i class="fa-solid fa-box-open mr-2.5 w-4 text-center group-hover:scale-110 transition-transform"></i> Produk
            </a>
            <a href="pembayaran.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-medium text-sm transition-colors group">
                <i class="fa-solid fa-credit-card mr-2.5 w-4 text-center group-hover:scale-110 transition-transform"></i> Pembayaran
                <?php if ($pembayaran_tertunda > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full"><?= $pembayaran_tertunda; ?></span>
                <?php endif; ?>
            </a>
            <a href="testimoni.php" class="flex items-center px-3.5 py-2.5 bg-lime-50 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400 rounded-xl font-bold text-sm transition-colors">
                <i class="fa-solid fa-comments mr-2.5 w-4 text-center"></i> Testimonial
                <?php if ($testimoni_tertunda > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full shadow-sm"><?= $testimoni_tertunda; ?></span>
                <?php endif; ?>
            </a>
            <a href="pengguna.php" class="flex items-center px-3.5 py-2.5 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-lime-600 dark:hover:text-lime-400 rounded-xl font-medium text-sm transition-colors group">
                <i class="fa-solid fa-users mr-2.5 w-4 text-center group-hover:scale-110 transition-transform"></i> Pengguna
            </a>
        </nav>
        
        <div class="p-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-1">
            <a href="../keluar.php" class="flex items-center px-3.5 py-2.5 text-slate-400 dark:text-slate-500 hover:text-red-650 hover:bg-red-50 dark:hover:bg-red-950/20 rounded-xl font-bold text-sm transition-colors group flex-grow">
                <i class="fa-solid fa-arrow-right-from-bracket mr-2.5 w-4 text-center group-hover:-translate-x-0.5 transition-transform"></i> Keluar
            </a>
            <button id="tombol-tema" class="text-slate-400 hover:text-lime-600 dark:text-slate-400 dark:hover:text-lime-400 p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors cursor-pointer flex items-center justify-center" title="Ubah Tema">
                <i id="ikon-tombol-tema" class="fa-solid fa-moon text-base"></i>
            </button>
        </div>
    </aside>

    <!-- Latar Buram Seluler (Backdrop Overlay) -->
    <div id="sidebar-backdrop" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-40 hidden transition-opacity duration-300 opacity-0"></div>

    <main class="flex-grow p-4 sm:p-6 w-full max-w-7xl mx-auto overflow-x-hidden">
        
        <div class="mb-5">
            <h1 class="text-2xl font-extrabold text-slate-800 dark:text-slate-100">Kelola Testimonial</h1>
            <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Tinjau, setujui, atau tolak ulasan yang dikirimkan oleh pelanggan.</p>
        </div>

        <?php
        $status = isset($_GET['status']) ? $_GET['status'] : 'all';
        ?>

        <div class="flex flex-wrap bg-slate-100/70 dark:bg-slate-900 p-1 rounded-xl mb-6 border border-slate-200/50 dark:border-slate-800 gap-1 sm:gap-0">
            <a href="?status=all" class="px-3.5 py-1.5 rounded-md text-xs font-bold transition-all duration-200 <?= $status === 'all' ? 'bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 hover:bg-slate-200/50 dark:hover:bg-slate-800/40'; ?>">
                Semua Ulasan
            </a>
            <a href="?status=pending" class="px-3.5 py-1.5 rounded-md text-xs font-bold transition-all duration-200 flex items-center <?= $status === 'pending' ? 'bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 hover:bg-slate-200/50 dark:hover:bg-slate-800/40'; ?>">
                Pending
                <?php if ($testimoni_tertunda > 0): ?>
                    <span class="ml-1.5 bg-amber-100 dark:bg-amber-955 text-amber-700 dark:text-amber-400 text-[9px] px-1 py-0.5 rounded-md"><?= $testimoni_tertunda; ?></span>
                <?php endif; ?>
            </a>
            <a href="?status=approved" class="px-3.5 py-1.5 rounded-md text-xs font-bold transition-all duration-200 <?= $status === 'approved' ? 'bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 hover:bg-slate-200/50 dark:hover:bg-slate-800/40'; ?>">
                Disetujui
            </a>
            <a href="?status=rejected" class="px-3.5 py-1.5 rounded-md text-xs font-bold transition-all duration-200 <?= $status === 'rejected' ? 'bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-200 shadow-sm' : 'text-slate-500 hover:text-slate-700 dark:hover:text-slate-350 hover:bg-slate-200/50 dark:hover:bg-slate-800/40'; ?>">
                Ditolak
            </a>
        </div>

        <?php
        if ($status === 'all') {
            $kueri_testimoni = mysqli_query($koneksi, "
                SELECT t.*, p.nama as pengguna_nama, p.email 
                FROM testimonial t 
                JOIN pengguna p ON t.id_pengguna = p.id 
                ORDER BY t.tanggal_dibuat DESC
            ");
        } else {
            $kueri_testimoni = mysqli_query($koneksi, "
                SELECT t.*, p.nama as pengguna_nama, p.email 
                FROM testimonial t 
                JOIN pengguna p ON t.id_pengguna = p.id 
                WHERE t.status = '$status'
                ORDER BY t.tanggal_dibuat DESC
            ");
        }
        ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php if (mysqli_num_rows($kueri_testimoni) > 0): ?>
                <?php while ($testimoni = mysqli_fetch_assoc($kueri_testimoni)): 
                    
                    $kelas_lencana = '';
                    $kelas_ikon = '';
                    if ($testimoni['status'] === 'approved') {
                        $kelas_lencana = 'bg-lime-50 dark:bg-lime-955/20 text-lime-700 dark:text-lime-400 border-lime-200 dark:border-lime-900/30';
                        $kelas_ikon = 'fa-check';
                    } elseif ($testimoni['status'] === 'rejected') {
                        $kelas_lencana = 'bg-red-50 dark:bg-red-955/20 text-red-655 dark:text-red-400 border-red-200 dark:border-red-900/30';
                        $kelas_ikon = 'fa-xmark';
                    } else {
                        $kelas_lencana = 'bg-amber-50 dark:bg-amber-955/20 text-amber-600 dark:text-amber-400 border-amber-200 dark:border-amber-900/30';
                        $kelas_ikon = 'fa-clock';
                    }
                ?>
                    <div class="bg-white dark:bg-slate-900 p-4 sm:p-5 rounded-xl border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 flex flex-col group">
                        
                        <div class="flex justify-between items-start mb-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 flex items-center justify-center font-bold text-slate-500 dark:text-slate-400 shadow-sm text-xs">
                                    <?= strtoupper(substr($testimoni['nama'], 0, 1)); ?>
                                </div>
                                <div>
                                    <h3 class="text-xs font-bold text-slate-800 dark:text-slate-200 line-clamp-1"><?= $testimoni['nama']; ?></h3>
                                    <p class="text-[10px] font-medium text-slate-500 dark:text-slate-400 line-clamp-1"><?= $testimoni['pekerjaan'] ?: 'Pelanggan'; ?></p>
                                </div>
                            </div>
                            <span class="px-2 py-0.5 rounded-xl text-[9px] font-bold uppercase tracking-wider border <?= $kelas_lencana; ?>">
                                <i class="fa-solid <?= $kelas_ikon; ?> mr-0.5"></i> <?= $testimoni['status']; ?>
                            </span>
                        </div>

                        <div class="flex gap-0.5 mb-2">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fa-solid fa-star text-xs <?= $i <= $testimoni['rating'] ? 'text-lime-500' : 'text-slate-200 dark:text-slate-800'; ?>"></i>
                            <?php endfor; ?>
                        </div>

                        <p class="text-slate-600 dark:text-slate-300 text-xs sm:text-sm mb-4 flex-grow italic leading-relaxed">
                            "<?= $testimoni['isi_ulasan']; ?>"
                        </p>

                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mt-auto">
                            <span class="text-[10px] font-medium text-slate-400 dark:text-slate-550">
                                <i class="fa-regular fa-calendar mr-1"></i> <?= date('d M Y, H:i', strtotime($testimoni['tanggal_dibuat'])); ?>
                            </span>
                            
                            <div class="flex flex-wrap gap-1.5">
                                <?php if ($testimoni['status'] === 'pending'): ?>
                                    <a href="?approve&id=<?= $testimoni['id']; ?>" class="w-7 h-7 flex items-center justify-center bg-lime-50 dark:bg-lime-955/40 text-lime-600 dark:text-lime-400 rounded-xl hover:bg-lime-600 hover:text-white transition-colors border border-transparent hover:border-lime-700" title="Setujui">
                                        <i class="fa-solid fa-check text-xs"></i>
                                    </a>
                                    <a href="?reject&id=<?= $testimoni['id']; ?>" class="w-7 h-7 flex items-center justify-center bg-red-50 dark:bg-red-955/40 text-red-550 dark:text-red-400 rounded-xl hover:bg-red-500 hover:text-white transition-colors border border-transparent hover:border-red-600" title="Tolak">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <a href="?delete&id=<?= $testimoni['id']; ?>" onclick="return confirm('Yakin ingin menghapus testimonial ini permanen?')" class="w-7 h-7 flex items-center justify-center bg-slate-50 dark:bg-slate-950 text-slate-450 dark:text-slate-500 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-800 hover:text-slate-700 dark:hover:text-slate-300 transition-colors border border-transparent" title="Hapus Permanen">
                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                </a>
                            </div>
                        </div>
                        
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-16 bg-white dark:bg-slate-900 rounded-xl border border-dashed border-slate-200 dark:border-slate-800">
                    <div class="w-12 h-12 bg-slate-50 dark:bg-slate-950 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 dark:text-slate-700">
                        <i class="fa-solid fa-comments text-xl"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-0.5">Tidak ada ulasan</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Kategori ini belum memiliki data testimonial untuk ditampilkan.</p>
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
