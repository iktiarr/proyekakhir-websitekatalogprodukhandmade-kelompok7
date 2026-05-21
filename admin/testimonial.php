<?php
include '../koneksi.php';

// Cek apakah admin yang akses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

$pendingTestimonial = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM testimonial WHERE status = 'pending'"))['total'];

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle approve
if (isset($_GET['approve']) && $id > 0) {
    mysqli_query($conn, "UPDATE testimonial SET status = 'approved' WHERE id = $id");
    header("Location: testimonial.php");
    exit();
}

// Handle reject
if (isset($_GET['reject']) && $id > 0) {
    mysqli_query($conn, "UPDATE testimonial SET status = 'rejected' WHERE id = $id");
    header("Location: testimonial.php");
    exit();
}

// Handle delete
if (isset($_GET['delete']) && $id > 0) {
    mysqli_query($conn, "DELETE FROM testimonial WHERE id = $id");
    header("Location: testimonial.php");
    exit();
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Testimonial - Handmade Admin</title>
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
            <a href="pembayaran.php" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-xl font-medium transition-colors group">
                <i class="fa-solid fa-credit-card mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i> Pembayaran
            </a>
            <a href="testimonial.php" class="flex items-center px-4 py-3 bg-lime-50 text-lime-700 rounded-xl font-bold transition-colors">
                <i class="fa-solid fa-comments mr-3 w-5 text-center"></i> Testimonial
                <?php if ($pendingTestimonial > 0): ?>
                    <span class="ml-auto bg-lime-600 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm"><?= $pendingTestimonial; ?></span>
                <?php endif; ?>
            </a>
            <a href="pengguna.php" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-xl font-medium transition-colors group">
                <i class="fa-solid fa-users mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i> Pengguna
            </a>
        </nav>
        
        <div class="p-4 border-t border-slate-100">
            <a href="../logout.php" class="flex items-center px-4 py-3 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl font-bold transition-colors group">
                <i class="fa-solid fa-arrow-right-from-bracket mr-3 w-5 text-center group-hover:-translate-x-1 transition-transform"></i> Keluar
            </a>
        </div>
    </aside>

    <main class="flex-1 p-8 lg:p-10 max-w-7xl">
        
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-800">Kelola Testimonial</h1>
            <p class="text-slate-500 mt-1">Tinjau, setujui, atau tolak ulasan yang dikirimkan oleh pelanggan.</p>
        </div>

        <?php
        $status = isset($_GET['status']) ? $_GET['status'] : 'all';
        ?>

        <div class="inline-flex bg-slate-100/70 p-1.5 rounded-xl mb-8 border border-slate-200/50">
            <a href="?status=all" class="px-5 py-2 rounded-lg text-sm font-bold transition-all duration-200 <?= $status === 'all' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'; ?>">
                Semua Ulasan
            </a>
            <a href="?status=pending" class="px-5 py-2 rounded-lg text-sm font-bold transition-all duration-200 flex items-center <?= $status === 'pending' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'; ?>">
                Pending
                <?php if ($pendingTestimonial > 0): ?>
                    <span class="ml-2 bg-amber-100 text-amber-700 text-[10px] px-1.5 py-0.5 rounded-md"><?= $pendingTestimonial; ?></span>
                <?php endif; ?>
            </a>
            <a href="?status=approved" class="px-5 py-2 rounded-lg text-sm font-bold transition-all duration-200 <?= $status === 'approved' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'; ?>">
                Disetujui
            </a>
            <a href="?status=rejected" class="px-5 py-2 rounded-lg text-sm font-bold transition-all duration-200 <?= $status === 'rejected' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-700 hover:bg-slate-200/50'; ?>">
                Ditolak
            </a>
        </div>

        <?php
        if ($status === 'all') {
            $query = mysqli_query($conn, "
                SELECT t.*, p.nama as pengguna_nama, p.email 
                FROM testimonial t 
                JOIN pengguna p ON t.id_pengguna = p.id 
                ORDER BY t.tanggal_dibuat DESC
            ");
        } else {
            $query = mysqli_query($conn, "
                SELECT t.*, p.nama as pengguna_nama, p.email 
                FROM testimonial t 
                JOIN pengguna p ON t.id_pengguna = p.id 
                WHERE t.status = '$status'
                ORDER BY t.tanggal_dibuat DESC
            ");
        }
        ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (mysqli_num_rows($query) > 0): ?>
                <?php while ($testimonial = mysqli_fetch_assoc($query)): 
                    // Menentukan warna badge status
                    $badge_class = '';
                    $icon_class = '';
                    if ($testimonial['status'] === 'approved') {
                        $badge_class = 'bg-lime-50 text-lime-700 border-lime-200';
                        $icon_class = 'fa-check';
                    } elseif ($testimonial['status'] === 'rejected') {
                        $badge_class = 'bg-red-50 text-red-600 border-red-200';
                        $icon_class = 'fa-xmark';
                    } else {
                        $badge_class = 'bg-amber-50 text-amber-600 border-amber-200';
                        $icon_class = 'fa-clock';
                    }
                ?>
                    <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col group">
                        
                        <div class="flex justify-between items-start mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-50 border border-slate-100 flex items-center justify-center font-bold text-slate-500 shadow-sm">
                                    <?= strtoupper(substr($testimonial['nama'], 0, 1)); ?>
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800 line-clamp-1"><?= $testimonial['nama']; ?></h3>
                                    <p class="text-[11px] font-medium text-slate-500 line-clamp-1"><?= $testimonial['pekerjaan'] ?: 'Pelanggan'; ?></p>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border <?= $badge_class; ?>">
                                <i class="fa-solid <?= $icon_class; ?> mr-1"></i> <?= $testimonial['status']; ?>
                            </span>
                        </div>

                        <div class="flex gap-1 mb-3">
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <i class="fa-solid fa-star text-sm <?= $i <= $testimonial['rating'] ? 'text-lime-500' : 'text-slate-200'; ?>"></i>
                            <?php endfor; ?>
                        </div>

                        <p class="text-slate-600 text-sm mb-6 flex-grow italic leading-relaxed">
                            "<?= $testimonial['isi_ulasan']; ?>"
                        </p>

                        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mt-auto">
                            <span class="text-[11px] font-medium text-slate-400">
                                <i class="fa-regular fa-calendar mr-1"></i> <?= date('d M Y, H:i', strtotime($testimonial['tanggal_dibuat'])); ?>
                            </span>
                            
                            <div class="flex flex-wrap gap-2">
                                <?php if ($testimonial['status'] === 'pending'): ?>
                                    <a href="?approve&id=<?= $testimonial['id']; ?>" class="w-8 h-8 flex items-center justify-center bg-lime-50 text-lime-600 rounded-lg hover:bg-lime-600 hover:text-white transition-colors border border-transparent hover:border-lime-700" title="Setujui">
                                        <i class="fa-solid fa-check"></i>
                                    </a>
                                    <a href="?reject&id=<?= $testimonial['id']; ?>" class="w-8 h-8 flex items-center justify-center bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-colors border border-transparent hover:border-red-600" title="Tolak">
                                        <i class="fa-solid fa-xmark"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <a href="?delete&id=<?= $testimonial['id']; ?>" onclick="return confirm('Yakin ingin menghapus testimonial ini permanen?')" class="w-8 h-8 flex items-center justify-center bg-slate-50 text-slate-400 rounded-lg hover:bg-slate-200 hover:text-slate-700 transition-colors border border-transparent" title="Hapus Permanen">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </div>
                        </div>
                        
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-20 bg-white rounded-2xl border border-dashed border-slate-200">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                        <i class="fa-solid fa-comments text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 mb-1">Tidak ada ulasan</h3>
                    <p class="text-sm text-slate-500">Kategori ini belum memiliki data testimonial untuk ditampilkan.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>