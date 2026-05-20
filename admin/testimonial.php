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
    <title>Admin Testimonial - Handmade</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex">
    <aside class="w-72 bg-white border-r border-gray-100 min-h-screen sticky top-0">
        <div class="p-6 border-b border-gray-100">
            <a href="index.php" class="text-2xl font-bold bg-gradient-to-r from-lime-600 to-lime-500 bg-clip-text text-transparent">Handmade Admin.</a>
        </div>
        <nav class="p-4 space-y-2">
            <a href="index.php" class="flex items-center gap-3 px-4 py-3 bg-lime-50 text-lime-700 rounded-xl font-semibold transition">
                <i class="fa-solid fa-chart-line"></i>
                Dashboard
            </a>
            <a href="produk.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-lime-700 rounded-xl transition">
                <i class="fa-solid fa-box"></i>
                Produk
            </a>
            <a href="pembayaran.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-lime-700 rounded-xl transition">
                <i class="fa-solid fa-credit-card"></i>
                Pembayaran
            </a>
            <a href="testimonial.php" class="flex items-center gap-3 px-4 py-3 bg-lime-50 text-lime-700 rounded-xl font-semibold transition">
                <i class="fa-solid fa-comments"></i>
                Testimonial
                <?php if ($pendingTestimonial > 0): ?>
                    <span class="ml-auto bg-white text-lime-700 text-[10px] px-2 py-1 rounded-full font-bold"><?= $pendingTestimonial; ?></span>
                <?php endif; ?>
            </a>
            <a href="pengguna.php" class="flex items-center gap-3 px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-lime-700 rounded-xl transition">
                <i class="fa-solid fa-users"></i>
                Pengguna
            </a>
        </nav>
        <div class="p-4 border-t border-gray-100">
            <a href="../logout.php" class="flex items-center gap-3 px-4 py-3 text-red-500 hover:bg-red-50 rounded-xl transition">
                <i class="fa-solid fa-right-from-bracket"></i>
                Keluar
            </a>
        </div>
    </aside>

    <main class="flex-1 p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Kelola Testimonial</h1>
                <p class="text-gray-600 mt-1">Approve atau reject testimonial dari pelanggan.</p>
            </div>
            <a href="index.php" class="text-lime-600 font-semibold hover:text-lime-700">← Kembali ke Dashboard</a>
        </div>

        <!-- Tabs -->
        <div class="flex flex-wrap gap-2 mb-8 border-b border-gray-200">
            <a href="?status=all" class="px-4 py-2 border-b-2 border-lime-600 text-lime-600 font-semibold rounded-t-xl">Semua</a>
            <a href="?status=pending" class="px-4 py-2 border-b-2 border-transparent text-gray-600 hover:text-gray-900 rounded-t-xl">Pending</a>
            <a href="?status=approved" class="px-4 py-2 border-b-2 border-transparent text-gray-600 hover:text-gray-900 rounded-t-xl">Approved</a>
            <a href="?status=rejected" class="px-4 py-2 border-b-2 border-transparent text-gray-600 hover:text-gray-900 rounded-t-xl">Rejected</a>
        </div>

        <?php
        $status = isset($_GET['status']) ? $_GET['status'] : 'all';
        
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

        <div class="grid gap-5">
            <?php if (mysqli_num_rows($query) > 0): ?>
                <?php while ($testimonial = mysqli_fetch_assoc($query)): ?>
                    <div class="bg-white p-5 rounded-xl border border-gray-200 hover:border-lime-300 transition">
                        <div class="flex flex-col sm:flex-row justify-between gap-4 mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900"><?= $testimonial['nama']; ?></h3>
                                <p class="text-sm text-gray-600"><?= $testimonial['pekerjaan'] ?: 'Pelanggan'; ?> • <?= $testimonial['pengguna_nama']; ?></p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                <?php 
                                if ($testimonial['status'] === 'approved') echo 'bg-lime-100 text-lime-700';
                                elseif ($testimonial['status'] === 'rejected') echo 'bg-red-100 text-red-700';
                                else echo 'bg-yellow-100 text-yellow-700';
                                ?>
                            ">
                                <?= ucfirst($testimonial['status']); ?>
                            </span>
                        </div>

                        <div class="mb-4">
                            <div class="flex gap-1 mb-2">
                                <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                    <i class="fa-solid fa-star text-lime-500 text-sm"></i>
                                <?php endfor; ?>
                            </div>
                            <p class="text-gray-700">"<?= $testimonial['isi_ulasan']; ?>"</p>
                        </div>

                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 text-sm text-gray-600">
                            <span><?= date('d M Y H:i', strtotime($testimonial['tanggal_dibuat'])); ?></span>
                            <div class="flex flex-wrap gap-2">
                                <?php if ($testimonial['status'] === 'pending'): ?>
                                    <a href="?approve&id=<?= $testimonial['id']; ?>" class="px-4 py-2 bg-lime-600 text-white rounded-lg hover:bg-lime-700 text-xs font-semibold">Approve</a>
                                    <a href="?reject&id=<?= $testimonial['id']; ?>" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-xs font-semibold">Reject</a>
                                <?php endif; ?>
                                <a href="?delete&id=<?= $testimonial['id']; ?>" onclick="return confirm('Yakin ingin menghapus?')" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-xs font-semibold">Hapus</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-16 bg-gray-50 rounded-xl">
                    <i class="fa-solid fa-inbox text-4xl text-gray-300 mb-4 block"></i>
                    <p class="text-gray-600">Tidak ada testimonial untuk ditampilkan.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
