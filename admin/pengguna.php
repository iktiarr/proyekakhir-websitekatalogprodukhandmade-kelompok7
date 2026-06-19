<?php
// admin/pengguna.php: Halaman kelola pengguna terdaftar, digunakan untuk melihat daftar pengguna dan menghapus akun pengguna (non-admin).

include '../koneksi.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    $cek_pengguna = kueri("SELECT nama, role FROM pengguna WHERE id = ?", [$id_hapus]);
    $data_pengguna = mysqli_fetch_assoc($cek_pengguna);
    
    if ($data_pengguna && $data_pengguna['role'] === 'user') {
        $nama_pengguna_hapus = $data_pengguna['nama'];
        if (kueri("DELETE FROM pengguna WHERE id = ?", [$id_hapus])) {
            catat_log('pengguna', 'hapus', "Menghapus pengguna '$nama_pengguna_hapus'");
        }
    }
    
    header("Location: pengguna.php");
    exit();
}

$kueri_pengguna = kueri("SELECT * FROM pengguna ORDER BY role ASC, nama ASC");
$pembayaran_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pesanan WHERE status = 'dibayar'"))['total'];
$testimoni_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM testimonial WHERE status = 'pending'"))['total'];
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Pengguna - HandMadura Admin</title>
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
            <a href="pengguna.php" class="flex items-center px-2.5 py-1.5 bg-lime-50 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400 rounded-xl font-bold text-xs transition-colors">
                <i class="fa-solid fa-users mr-2 w-4 text-center"></i> Pengguna
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
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
            <div>
                <h1 class="text-xl sm:text-2xl font-extrabold text-slate-800 dark:text-slate-100 tracking-tight">Kelola Pengguna</h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-1">Daftar pengguna dan admin yang terdaftar di situs HandMadura.</p>
            </div>
            <a href="cetak_laporan.php?tipe=pengguna" target="_blank" class="bg-lime-600 hover:bg-lime-700 text-white px-4 py-2.5 rounded-xl font-bold transition-all duration-300 flex items-center cursor-pointer text-xs sm:text-sm shadow-sm flex-shrink-0">
                <i class="fa-solid fa-file-pdf mr-1.5"></i> Cetak Laporan Pengguna
            </a>
        </div>
        


        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm transition-colors duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                <thead class="bg-slate-50 dark:bg-slate-900/50 text-slate-400 dark:text-slate-500 text-[10px] uppercase tracking-wider font-bold border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 pl-6">Pengguna</th>
                        <th class="px-4 py-3.5">Email</th>
                        <th class="px-4 py-3.5">No. Telepon</th>
                        <th class="px-4 py-3.5">Alamat</th>
                        <th class="px-4 py-3.5">Role Akses</th>
                        <th class="px-4 py-3.5">Bergabung Pada</th>
                        <th class="px-4 py-3.5 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 dark:divide-slate-800/60">
                    <?php 
                    if(mysqli_num_rows($kueri_pengguna) > 0):
                        while($baris = mysqli_fetch_assoc($kueri_pengguna)): 
                    ?>
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 text-xs sm:text-sm transition-colors duration-200">
                        <td class="px-4 py-3 pl-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 <?= $baris['role'] === 'admin' ? 'bg-lime-100 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400' : 'bg-slate-100 dark:bg-slate-950 text-slate-500 dark:text-slate-400'; ?> rounded-full flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    <?= strtoupper(substr($baris['nama'], 0, 1)); ?>
                                </div>
                                <span class="font-bold text-slate-800 dark:text-slate-200"><?= $baris['nama']; ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            <?= $baris['email']; ?>
                        </td>
                        <td class="px-4 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            <?= $baris['no_telp'] ?: '<span class="text-slate-300 dark:text-slate-600 italic">-</span>'; ?>
                        </td>
                        <td class="px-4 py-3 text-xs font-medium text-slate-500 dark:text-slate-400 max-w-xs truncate" title="<?= htmlspecialchars($baris['alamat'] ?? ''); ?>">
                            <?= $baris['alamat'] ?: '<span class="text-slate-300 dark:text-slate-600 italic">-</span>'; ?>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-xl text-[9px] font-bold uppercase tracking-widest border whitespace-nowrap <?= $baris['role'] === 'admin' ? 'bg-lime-50 dark:bg-lime-950/20 text-lime-700 dark:text-lime-400 border-lime-200 dark:border-lime-900/30' : 'bg-slate-50 dark:bg-slate-950 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-800'; ?>">
                                <?php if($baris['role'] === 'admin'): ?>
                                    <i class="fa-solid fa-user-shield mr-1"></i>
                                <?php else: ?>
                                    <i class="fa-solid fa-user mr-1"></i>
                                <?php endif; ?>
                                <?= $baris['role']; ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs font-medium text-slate-400 dark:text-slate-500 whitespace-nowrap">
                            <i class="fa-regular fa-calendar-days mr-1.5 text-slate-400"></i><?= date('d M Y', strtotime($baris['tanggal_dibuat'])); ?>
                        </td>
                        <td class="px-4 py-3 pr-6 text-right whitespace-nowrap">
                            <?php if ($baris['role'] === 'user'): ?>
                                <a href="pengguna.php?hapus=<?= $baris['id']; ?>" onclick="return confirm('Hapus akun pengguna ini secara permanen?')" class="w-8 h-8 inline-flex items-center justify-center rounded-xl text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors" title="Hapus Pengguna">
                                    <i class="fa-solid fa-trash-can text-sm"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-slate-300 dark:text-slate-600 text-xs italic">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else:
                    ?>
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-400 dark:text-slate-500">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-users-slash text-2xl mb-2.5 text-slate-300 dark:text-slate-700"></i>
                                <p class="text-xs">Belum ada pengguna terdaftar.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    </main>

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

        // Pengontrol Sidebar Seluler
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        const tombolMenuMobile = document.getElementById('tombol-menu-mobile');
        const tombolTutupSidebar = document.getElementById('tombol-tutup-sidebar');

        // Membuka navigasi sidebar pada mode seluler (mobile)
        function bukaSidebar() {
            if (sidebar && backdrop) {
                sidebar.classList.add('active');
                backdrop.classList.replace('opacity-0', 'opacity-100');
                backdrop.classList.replace('pointer-events-none', 'pointer-events-auto');
                document.body.style.overflow = 'hidden';
            }
        }

        // Menutup navigasi sidebar pada mode seluler (mobile)
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