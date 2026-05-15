<?php
include '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

$success = '';
$error = '';

// Tambah/Edit Produk
if (isset($_POST['simpan'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = (float)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    $id_kategori = (int)$_POST['id_kategori'];
    
    $gambar = $_POST['old_gambar'];
    if ($_FILES['gambar']['name']) {
        $target_dir = "../uploads/produk/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $file_ext = pathinfo($_FILES["gambar"]["name"], PATHINFO_EXTENSION);
        $filename = "prod_" . time() . "." . $file_ext;
        $target_file = $target_dir . $filename;
        
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            $gambar = "uploads/produk/" . $filename;
        }
    }

    if ($id > 0) {
        $query = "UPDATE produk SET nama_produk='$nama', deskripsi='$deskripsi', harga=$harga, stok=$stok, gambar='$gambar', id_kategori=$id_kategori WHERE id=$id";
    } else {
        $query = "INSERT INTO produk (nama_produk, deskripsi, harga, stok, gambar, id_kategori) VALUES ('$nama', '$deskripsi', $harga, $stok, '$gambar', $id_kategori)";
    }

    if (mysqli_query($conn, $query)) {
        $success = "Produk berhasil disimpan!";
    } else {
        $error = "Gagal menyimpan: " . mysqli_error($conn);
    }
}

// Hapus Produk
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM produk WHERE id=$id");
    header("Location: produk.php");
    exit();
}

$query_produk = mysqli_query($conn, "SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id");
$query_kategori = mysqli_query($conn, "SELECT * FROM kategori");
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Produk - Handmade Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 flex">
    <!-- Sidebar (Same as dashboard) -->
    <aside class="w-64 bg-white min-h-screen border-r border-gray-100 flex flex-col sticky top-0">
        <div class="p-8">
            <a href="../index.php" class="text-2xl font-bold bg-gradient-to-r from-amber-600 to-orange-500 bg-clip-text text-transparent">Handmade Admin.</a>
        </div>
        <nav class="flex-1 px-4 space-y-2">
            <a href="index.php" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-amber-600 rounded-xl font-medium transition">
                <i class="fa-solid fa-chart-line mr-3"></i> Dashboard
            </a>
            <a href="produk.php" class="flex items-center px-4 py-3 bg-amber-50 text-amber-600 rounded-xl font-bold transition">
                <i class="fa-solid fa-box mr-3"></i> Produk
            </a>
            <a href="pembayaran.php" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-amber-600 rounded-xl font-medium transition">
                <i class="fa-solid fa-credit-card mr-3"></i> Pembayaran
            </a>
            <a href="pengguna.php" class="flex items-center px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-amber-600 rounded-xl font-medium transition">
                <i class="fa-solid fa-users mr-3"></i> Pengguna
            </a>
        </nav>
    </aside>

    <main class="flex-1 p-8 lg:p-12">
        <div class="flex justify-between items-center mb-12">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">Kelola Produk</h1>
                <p class="text-gray-500">Tambah, edit, atau hapus produk katalog Anda.</p>
            </div>
            <button onclick="toggleModal()" class="bg-amber-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-amber-700 transition shadow-lg shadow-amber-200">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Produk
            </button>
        </div>

        <?php if ($success): ?>
            <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-8 border border-green-100"><?= $success; ?></div>
        <?php endif; ?>

        <!-- Table -->
        <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-400 text-xs uppercase tracking-widest font-bold">
                    <tr>
                        <th class="px-8 py-6">Produk</th>
                        <th class="px-8 py-6">Kategori</th>
                        <th class="px-8 py-6">Harga</th>
                        <th class="px-8 py-6">Stok</th>
                        <th class="px-8 py-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php while($p = mysqli_fetch_assoc($query_produk)): ?>
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="px-8 py-6">
                            <div class="flex items-center space-x-4">
                                <img src="../<?= $p['gambar'] ?: 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=100'; ?>" class="w-12 h-12 rounded-xl object-cover">
                                <span class="font-bold text-gray-900"><?= $p['nama_produk']; ?></span>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-sm text-gray-500"><?= $p['nama_kategori']; ?></td>
                        <td class="px-8 py-6 font-bold text-gray-900">Rp <?= number_format($p['harga'], 0, ',', '.'); ?></td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 bg-amber-50 text-amber-600 rounded-lg text-xs font-bold"><?= $p['stok']; ?> unit</span>
                        </td>
                        <td class="px-8 py-6 text-right space-x-2">
                            <button onclick='editProduk(<?= json_encode($p); ?>)' class="p-2 text-blue-400 hover:text-blue-600 transition">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <a href="produk.php?hapus=<?= $p['id']; ?>" onclick="return confirm('Hapus produk ini?')" class="p-2 text-red-400 hover:text-red-600 transition">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal Tambah/Edit -->
    <div id="modalProduk" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden">
        <div class="bg-white w-full max-w-2xl rounded-[2.5rem] p-10 shadow-2xl scale-95 transition-all duration-300">
            <div class="flex justify-between items-center mb-8">
                <h2 id="modalTitle" class="text-2xl font-bold text-gray-900">Tambah Produk Baru</h2>
                <button onclick="toggleModal()" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-2 gap-6">
                <input type="hidden" name="id" id="prod_id">
                <input type="hidden" name="old_gambar" id="prod_old_gambar">
                
                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Nama Produk</label>
                    <input type="text" name="nama" id="prod_nama" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-amber-500 outline-none transition">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Harga (Rp)</label>
                    <input type="number" name="harga" id="prod_harga" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-amber-500 outline-none transition">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Stok</label>
                    <input type="number" name="stok" id="prod_stok" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-amber-500 outline-none transition">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Kategori</label>
                    <select name="id_kategori" id="prod_kategori" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-amber-500 outline-none transition">
                        <?php 
                        mysqli_data_seek($query_kategori, 0);
                        while($k = mysqli_fetch_assoc($query_kategori)): ?>
                        <option value="<?= $k['id']; ?>"><?= $k['nama_kategori']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" id="prod_deskripsi" rows="3" class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-amber-500 outline-none transition"></textarea>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Foto Produk</label>
                    <input type="file" name="gambar" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 transition">
                </div>

                <div class="col-span-2 mt-4">
                    <button type="submit" name="simpan" class="w-full bg-amber-600 text-white py-4 rounded-2xl font-bold hover:bg-amber-700 transition shadow-xl shadow-amber-200">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleModal() {
            const modal = document.getElementById('modalProduk');
            modal.classList.toggle('hidden');
            if (modal.classList.contains('hidden')) {
                document.getElementById('modalTitle').innerText = "Tambah Produk Baru";
                document.getElementById('prod_id').value = "";
                document.getElementById('prod_nama').value = "";
                document.getElementById('prod_harga').value = "";
                document.getElementById('prod_stok').value = "";
                document.getElementById('prod_deskripsi').value = "";
                document.getElementById('prod_old_gambar').value = "";
            }
        }

        function editProduk(data) {
            document.getElementById('modalTitle').innerText = "Edit Produk";
            document.getElementById('prod_id').value = data.id;
            document.getElementById('prod_nama').value = data.nama_produk;
            document.getElementById('prod_harga').value = data.harga;
            document.getElementById('prod_stok').value = data.stok;
            document.getElementById('prod_deskripsi').value = data.deskripsi;
            document.getElementById('prod_kategori').value = data.id_kategori;
            document.getElementById('prod_old_gambar').value = data.gambar;
            document.getElementById('modalProduk').classList.remove('hidden');
        }
    </script>
</body>
</html>
