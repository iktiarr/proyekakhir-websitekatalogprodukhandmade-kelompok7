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

$query_produk = mysqli_query($conn, "SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id ORDER BY p.id DESC");
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
            <a href="produk.php" class="flex items-center px-4 py-3 bg-lime-50 text-lime-700 rounded-xl font-bold transition-colors">
                <i class="fa-solid fa-box-open mr-3 w-5 text-center"></i> Produk
            </a>
            <a href="pembayaran.php" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-xl font-medium transition-colors group">
                <i class="fa-solid fa-credit-card mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i> Pembayaran
            </a>
            <a href="testimonial.php" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-xl font-medium transition-colors group">
                <i class="fa-solid fa-comments mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i> Testimonial
            </a>
            <a href="pengguna.php" class="flex items-center px-4 py-3 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-xl font-medium transition-colors group">
                <i class="fa-solid fa-users mr-3 w-5 text-center group-hover:scale-110 transition-transform"></i> Pengguna
            </a>
        </nav>
    </aside>

    <main class="flex-1 p-8 lg:p-10 max-w-7xl">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-10 gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-800">Kelola Produk</h1>
                <p class="text-slate-500 mt-1">Tambah, edit, atau hapus produk di katalog Anda.</p>
            </div>
            <button onclick="openModalTambah()" class="bg-lime-600 text-white px-6 py-3 rounded-xl font-bold hover:bg-lime-700 hover:-translate-y-0.5 transition-all duration-300 shadow-lg shadow-lime-200/50 flex items-center">
                <i class="fa-solid fa-plus mr-2"></i> Tambah Produk
            </button>
        </div>

        <?php if ($success): ?>
            <div class="bg-lime-50 text-lime-700 p-4 rounded-xl mb-8 border border-lime-100 flex items-center shadow-sm">
                <i class="fa-solid fa-circle-check mr-3"></i> <?= $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-8 border border-red-100 flex items-center shadow-sm">
                <i class="fa-solid fa-circle-exclamation mr-3"></i> <?= $error; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-400 text-[11px] uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-5 pl-8">Produk</th>
                        <th class="px-6 py-5">Kategori</th>
                        <th class="px-6 py-5">Harga</th>
                        <th class="px-6 py-5">Stok</th>
                        <th class="px-6 py-5 pr-8 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php 
                    if(mysqli_num_rows($query_produk) > 0):
                        while($p = mysqli_fetch_assoc($query_produk)): 
                    ?>
                    <tr class="hover:bg-slate-50/80 transition-colors duration-200">
                        <td class="px-6 py-4 pl-8">
    <div class="flex items-center space-x-4">
        
        <?php if (!empty($p['gambar'])): ?>
            <img src="../<?= $p['gambar']; ?>" alt="<?= $p['nama_produk']; ?>" class="w-12 h-12 rounded-xl object-cover border border-slate-100 shadow-sm bg-white">
        <?php else: ?>
            <div class="w-12 h-12 rounded-xl border border-slate-100 shadow-sm bg-slate-50 flex items-center justify-center text-slate-300">
                <i class="fa-solid fa-image"></i>
            </div>
        <?php endif; ?>
        
        <span class="font-bold text-slate-800 line-clamp-2"><?= $p['nama_produk']; ?></span>
    </div>
</td>
                        <td class="px-6 py-4 text-sm font-medium text-slate-500">
                            <?= $p['nama_kategori'] ?: '<span class="text-slate-300 italic">Tanpa Kategori</span>'; ?>
                        </td>
                        <td class="px-6 py-4 font-extrabold text-slate-800 whitespace-nowrap">
                            Rp <?= number_format($p['harga'], 0, ',', '.'); ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-3 py-1 bg-lime-50 text-lime-700 rounded-lg text-xs font-bold border border-lime-100 whitespace-nowrap">
                                <?= $p['stok']; ?> unit
                            </span>
                        </td>
                        <td class="px-6 py-4 pr-8 text-right space-x-1 whitespace-nowrap">
                            <button onclick='editProduk(<?= json_encode($p); ?>)' class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-blue-500 hover:text-blue-700 hover:bg-blue-50 transition-colors" title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <a href="produk.php?hapus=<?= $p['id']; ?>" onclick="return confirm('Hapus produk ini secara permanen?')" class="w-9 h-9 inline-flex items-center justify-center rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus">
                                <i class="fa-solid fa-trash-can"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-box-open text-3xl mb-3 text-slate-300"></i>
                                <p class="text-sm">Belum ada produk di katalog.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <div id="modalProduk" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[60] flex items-center justify-center opacity-0 invisible transition-all duration-300 py-6 px-4">
        
        <div id="modalContent" class="bg-white w-full max-w-2xl rounded-2xl p-8 sm:p-10 shadow-2xl transform scale-95 transition-transform duration-300 max-h-full overflow-y-auto custom-scrollbar">
            
            <div class="flex justify-between items-center mb-8 border-b border-slate-100 pb-4">
                <h2 id="modalTitle" class="text-xl sm:text-2xl font-extrabold text-slate-800">Tambah Produk Baru</h2>
                <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>
            
            <form action="" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <input type="hidden" name="id" id="prod_id">
                <input type="hidden" name="old_gambar" id="prod_old_gambar">
                
                <div class="sm:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama Produk</label>
                    <input type="text" name="nama" id="prod_nama" required class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Harga (Rp)</label>
                    <input type="number" name="harga" id="prod_harga" required class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800">
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Stok (Unit)</label>
                    <input type="number" name="stok" id="prod_stok" required class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Kategori</label>
                    <select name="id_kategori" id="prod_kategori" class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800">
                        <option value="" disabled selected>-- Pilih Kategori --</option>
                        <?php 
                        mysqli_data_seek($query_kategori, 0);
                        while($k = mysqli_fetch_assoc($query_kategori)): ?>
                        <option value="<?= $k['id']; ?>"><?= $k['nama_kategori']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi</label>
                    <textarea name="deskripsi" id="prod_deskripsi" rows="3" required class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800 resize-none"></textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-bold text-slate-700 mb-2">Foto Produk</label>
                    <div class="flex items-center">
                        <input type="file" name="gambar" class="w-full text-sm text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-bold file:bg-lime-50 file:text-lime-700 hover:file:bg-lime-100 transition-colors cursor-pointer border border-slate-200 rounded-xl bg-slate-50 p-1">
                    </div>
                    <p class="text-[11px] text-slate-400 mt-2 italic">*Kosongkan jika tidak ingin mengubah foto (saat edit).</p>
                </div>

                <div class="sm:col-span-2 mt-6 pt-6 border-t border-slate-100 flex gap-4 justify-end">
                    <button type="button" onclick="closeModal()" class="px-6 py-3.5 bg-white text-slate-600 border border-slate-200 rounded-xl font-bold hover:bg-slate-50 transition-colors">Batal</button>
                    <button type="submit" name="simpan" class="px-8 py-3.5 bg-lime-600 text-white rounded-xl font-bold hover:bg-lime-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-lime-200/50 transition-all duration-300">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f8fafc; border-radius: 8px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 8px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>

    <script>
        const modal = document.getElementById('modalProduk');
        const modalContent = document.getElementById('modalContent');

        function openModalTambah() {
            document.getElementById('modalTitle').innerText = "Tambah Produk Baru";
            document.getElementById('prod_id').value = "";
            document.getElementById('prod_nama').value = "";
            document.getElementById('prod_harga').value = "";
            document.getElementById('prod_stok').value = "";
            document.getElementById('prod_kategori').value = "";
            document.getElementById('prod_deskripsi').value = "";
            document.getElementById('prod_old_gambar').value = "";
            
            showModal();
        }

        function editProduk(data) {
            document.getElementById('modalTitle').innerText = "Edit Produk";
            document.getElementById('prod_id').value = data.id;
            document.getElementById('prod_nama').value = data.nama_produk;
            document.getElementById('prod_harga').value = data.harga;
            document.getElementById('prod_stok').value = data.stok;
            document.getElementById('prod_kategori').value = data.id_kategori;
            document.getElementById('prod_deskripsi').value = data.deskripsi;
            document.getElementById('prod_old_gambar').value = data.gambar;
            
            showModal();
        }

        function showModal() {
            modal.classList.remove('opacity-0', 'invisible');
            modalContent.classList.remove('scale-95');
        }

        function closeModal() {
            modalContent.classList.add('scale-95');
            modal.classList.add('opacity-0', 'invisible');
        }
    </script>
</body>
</html>