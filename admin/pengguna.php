<?php
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

$query = mysqli_query($conn, "SELECT * FROM pengguna ORDER BY role ASC, nama ASC");
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Pengguna - Handmade Admin</title>
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
            <a href="pembayaran.php" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-amber-600 rounded-xl font-medium transition">
                <i class="fa-solid fa-credit-card mr-3"></i> Pembayaran
            </a>
            <a href="pengguna.php" class="flex items-center px-4 py-3 bg-amber-50 text-amber-600 rounded-xl font-bold transition">
                <i class="fa-solid fa-users mr-3"></i> Pengguna
            </a>
        </nav>
    </aside>

    <main class="flex-1 p-8 lg:p-12">
        <div class="mb-12">
            <h1 class="text-3xl font-extrabold text-gray-900">Kelola Pengguna</h1>
            <p class="text-gray-500">Daftar pengguna dan administrator sistem.</p>
        </div>

        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-400 text-xs uppercase tracking-widest font-bold">
                    <tr>
                        <th class="px-8 py-6">Nama</th>
                        <th class="px-8 py-6">Email</th>
                        <th class="px-8 py-6">Role</th>
                        <th class="px-8 py-6">Bergabung Pada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php while($row = mysqli_fetch_assoc($query)): ?>
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-8 py-6">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 <?= $row['role'] === 'admin' ? 'bg-amber-100 text-amber-600' : 'bg-blue-100 text-blue-600'; ?> rounded-full flex items-center justify-center font-bold">
                                    <?= strtoupper(substr($row['nama'], 0, 1)); ?>
                                </div>
                                <span class="font-bold text-gray-900"><?= $row['nama']; ?></span>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-sm text-gray-500"><?= $row['email']; ?></td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest border <?= $row['role'] === 'admin' ? 'bg-amber-50 text-amber-600 border-amber-100' : 'bg-blue-50 text-blue-600 border-blue-100'; ?>">
                                <?= $row['role']; ?>
                            </span>
                        </td>
                        <td class="px-8 py-6 text-sm text-gray-400">
                            <?= date('d M Y', strtotime($row['tanggal_dibuat'])); ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
