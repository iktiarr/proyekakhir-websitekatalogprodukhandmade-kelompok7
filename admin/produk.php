<?php
include '../koneksi.php';


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

$success = '';
$error = '';


if (isset($_POST['simpan'])) {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $harga = (float)$_POST['harga'];
    $stok = (int)$_POST['stok'];
    $id_kategori = (int)$_POST['id_kategori'];
    $id_daerah = isset($_POST['id_daerah']) ? (int)$_POST['id_daerah'] : 0;
    $id_daerah_val = $id_daerah > 0 ? $id_daerah : "NULL";
    
    $gambar_raw = trim($_POST['gambar']);
    
    if (stripos($gambar_raw, 'unsplash.com') !== false && stripos($gambar_raw, 'images.unsplash.com') === false) {
        $path = parse_url($gambar_raw, PHP_URL_PATH);
        if ($path) {
            $segments = explode('/', trim($path, '/'));
            $last_segment = end($segments);
            if ($last_segment) {
                $sub_segments = explode('-', $last_segment);
                $unsplash_id = end($sub_segments);
                if ($unsplash_id) {
                    $gambar_raw = "https://unsplash.com/photos/" . $unsplash_id . "/download";
                }
            }
        }
    }
    $gambar = mysqli_real_escape_string($conn, $gambar_raw);

    if ($harga < 0 || $stok < 0) {
        $error = "Gagal menyimpan: Harga dan Stok tidak boleh bernilai negatif!";
    } else {
        if ($id > 0) {
            $query = "UPDATE produk SET nama_produk='$nama', deskripsi='$deskripsi', harga=$harga, stok=$stok, gambar='$gambar', id_kategori=$id_kategori, id_daerah=$id_daerah_val WHERE id=$id";
        } else {
            $query = "INSERT INTO produk (nama_produk, deskripsi, harga, stok, gambar, id_kategori, id_daerah) VALUES ('$nama', '$deskripsi', $harga, $stok, '$gambar', $id_kategori, $id_daerah_val)";
        }

        if (mysqli_query($conn, $query)) {
            $success = "Produk berhasil disimpan!";
        } else {
            $error = "Gagal menyimpan: " . mysqli_error($conn);
        }
    }
}


if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM produk WHERE id=$id");
    header("Location: produk.php");
    exit();
}


$query_produk = mysqli_query($conn, "SELECT p.*, k.nama_kategori, d.nama_daerah FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id LEFT JOIN daerah d ON p.id_daerah = d.id ORDER BY p.id DESC");
$query_kategori = mysqli_query($conn, "SELECT * FROM kategori");
$query_daerah = mysqli_query($conn, "SELECT * FROM daerah");
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
    
    <!-- Sidebar Admin -->
    <aside class="w-56 bg-white min-h-screen border-r border-slate-200 flex flex-col sticky top-0 shadow-sm z-10">
        <div class="p-5 pb-3">
            <a href="../index.php" class="text-xl font-extrabold text-slate-800 tracking-tight transition-transform hover:scale-105 inline-block">
                Hand<span class="text-lime-600">made.</span>
            </a>
            <p class="text-[9px] uppercase tracking-widest text-slate-400 font-bold mt-0.5">Admin Panel</p>
        </div>
        
        <nav class="flex-1 px-3 space-y-1">
            <a href="index.php" class="flex items-center px-3.5 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-lg font-medium text-sm transition-colors group">
                <i class="fa-solid fa-chart-pie mr-2.5 w-4 text-center group-hover:scale-110 transition-transform"></i> Dasbor
            </a>
            <a href="produk.php" class="flex items-center px-3.5 py-2.5 bg-lime-50 text-lime-700 rounded-lg font-bold text-sm transition-colors">
                <i class="fa-solid fa-box-open mr-2.5 w-4 text-center"></i> Produk
            </a>
            <a href="pembayaran.php" class="flex items-center px-3.5 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-lg font-medium text-sm transition-colors group">
                <i class="fa-solid fa-credit-card mr-2.5 w-4 text-center group-hover:scale-110 transition-transform"></i> Pembayaran
            </a>
            <a href="testimonial.php" class="flex items-center px-3.5 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-lg font-medium text-sm transition-colors group">
                <i class="fa-solid fa-comments mr-2.5 w-4 text-center group-hover:scale-110 transition-transform"></i> Testimonial
            </a>
            <a href="pengguna.php" class="flex items-center px-3.5 py-2.5 text-slate-500 hover:bg-slate-50 hover:text-lime-600 rounded-lg font-medium text-sm transition-colors group">
                <i class="fa-solid fa-users mr-2.5 w-4 text-center group-hover:scale-110 transition-transform"></i> Pengguna
            </a>
        </nav>
        
        <div class="p-3 border-t border-slate-100">
            <a href="../logout.php" class="flex items-center px-3.5 py-2.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg font-bold text-sm transition-colors group">
                <i class="fa-solid fa-arrow-right-from-bracket mr-2.5 w-4 text-center group-hover:-translate-x-1 transition-transform"></i> Keluar
            </a>
        </div>
    </aside>

    <main class="flex-1 p-5 sm:p-6 max-w-7xl">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-800">Kelola Produk</h1>
                <p class="text-slate-500 text-xs mt-0.5">Tambah, edit, atau hapus produk di katalog Anda.</p>
            </div>
            <button onclick="openModalTambah()" class="bg-lime-600 text-white px-4 py-2.5 rounded-lg font-bold hover:bg-lime-700 hover:-translate-y-0.5 transition-all duration-300 shadow-lg shadow-lime-200/50 flex items-center cursor-pointer text-xs sm:text-sm">
                <i class="fa-solid fa-plus mr-1.5"></i> Tambah Produk
            </button>
        </div>

        <?php if ($success): ?>
            <div class="bg-lime-50 text-lime-700 p-3 rounded-lg mb-6 border border-lime-100 flex items-center shadow-sm text-xs">
                <i class="fa-solid fa-circle-check mr-2"></i> <?= $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-lg mb-6 border border-red-100 flex items-center shadow-sm text-xs">
                <i class="fa-solid fa-circle-exclamation mr-2"></i> <?= $error; ?>
            </div>
        <?php endif; ?>

        <!-- Tabel Produk -->
        <div class="bg-white rounded-xl border border-slate-200">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-400 text-[10px] uppercase tracking-wider font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3.5 pl-6">Produk</th>
                        <th class="px-4 py-3.5">Kategori</th>
                        <th class="px-4 py-3.5">Daerah</th>
                        <th class="px-4 py-3.5">Harga</th>
                        <th class="px-4 py-3.5">Stok</th>
                        <th class="px-4 py-3.5 pr-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php 
                    if(mysqli_num_rows($query_produk) > 0):
                        while($p = mysqli_fetch_assoc($query_produk)): 
                    ?>
                    <tr class="hover:bg-slate-50/80 transition-colors duration-200 text-xs sm:text-sm">
                        <td class="px-4 py-3 pl-6">
                            <div class="flex items-center space-x-3">
                                <?php 
                                    $img_src = $p['gambar'];
                                    if (empty($img_src) || strpos($img_src, 'uploads/produk') !== false) {
                                        $img_src = 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=200';
                                    } elseif (strpos($img_src, 'http') !== 0) {
                                        $img_src = '../' . $img_src;
                                    }
                                ?>
                                    <img src="<?= $img_src; ?>" alt="<?= $p['nama_produk']; ?>" class="w-10 h-10 rounded-lg object-cover border border-slate-100 shadow-sm bg-white flex-shrink-0" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=200';">
                                <span class="font-bold text-slate-800 line-clamp-1"><?= $p['nama_produk']; ?></span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs font-medium text-slate-500">
                            <?= $p['nama_kategori'] ?: '<span class="text-slate-300 italic">Tanpa Kategori</span>'; ?>
                        </td>
                        <td class="px-4 py-3 text-xs font-medium text-slate-500">
                            <?= $p['nama_daerah'] ?: '<span class="text-slate-300 italic">-</span>'; ?>
                        </td>
                        <td class="px-4 py-3 font-extrabold text-slate-800 whitespace-nowrap">
                            Rp <?= number_format($p['harga'], 0, ',', '.'); ?>
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 bg-lime-50 text-lime-700 rounded-md text-[10px] font-bold border border-lime-100 whitespace-nowrap">
                                <?= $p['stok']; ?> unit
                            </span>
                        </td>
                        <td class="px-4 py-3 pr-6 text-right space-x-1 whitespace-nowrap">
                            <button onclick='editProduk(<?= json_encode($p); ?>)' class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-blue-500 hover:text-blue-700 hover:bg-blue-50 transition-colors cursor-pointer" title="Edit">
                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                            </button>
                            <a href="produk.php?hapus=<?= $p['id']; ?>" onclick="return confirm('Hapus produk ini secara permanen?')" class="w-8 h-8 inline-flex items-center justify-center rounded-lg text-red-400 hover:text-red-600 hover:bg-red-50 transition-colors" title="Hapus">
                                <i class="fa-solid fa-trash-can text-sm"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-slate-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-box-open text-2xl mb-2.5 text-slate-300"></i>
                                <p class="text-xs">Belum ada produk di katalog.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal Form (Tambah / Edit) -->
    <div id="modalProduk" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[60] flex items-center justify-center opacity-0 invisible transition-all duration-300 py-6 px-4">
        <div id="modalContent" class="bg-white w-full max-w-md rounded-xl p-4 sm:p-5 shadow-2xl transform scale-95 transition-transform duration-300 max-h-full overflow-y-auto custom-scrollbar">
            
            <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-3">
                <h2 id="modalTitle" class="text-lg font-extrabold text-slate-800">Tambah Produk Baru</h2>
                <button onclick="closeModal()" class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            
            <form action="" method="POST" class="space-y-3.5">
                <input type="hidden" name="id" id="prod_id">
                
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Produk</label>
                    <input type="text" name="nama" id="prod_nama" required class="w-full px-3 py-2 bg-slate-50 rounded-lg border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-xs text-slate-800" placeholder="Masukkan nama produk">
                </div>
                
                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga (Rp)</label>
                        <input type="number" name="harga" id="prod_harga" min="0" required class="w-full px-3 py-2 bg-slate-50 rounded-lg border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-xs text-slate-800">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Stok (Unit)</label>
                        <input type="number" name="stok" id="prod_stok" min="0" required class="w-full px-3 py-2 bg-slate-50 rounded-lg border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-xs text-slate-800">
                    </div>
                </div>
 
                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kategori</label>
                        <select name="id_kategori" id="prod_kategori" required class="w-full px-3 py-2 bg-slate-50 rounded-lg border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-xs text-slate-800">
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            <?php 
                            mysqli_data_seek($query_kategori, 0);
                            while($k = mysqli_fetch_assoc($query_kategori)): ?>
                            <option value="<?= $k['id']; ?>"><?= $k['nama_kategori']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Asal Daerah</label>
                        <select name="id_daerah" id="prod_daerah" required class="w-full px-3 py-2 bg-slate-50 rounded-lg border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-xs text-slate-800">
                            <option value="" disabled selected>-- Pilih Daerah --</option>
                            <?php 
                            mysqli_data_seek($query_daerah, 0);
                            while($d = mysqli_fetch_assoc($query_daerah)): ?>
                            <option value="<?= $d['id']; ?>"><?= $d['nama_daerah']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
 
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Link Foto Produk</label>
                    <input type="url" name="gambar" id="prod_gambar" required class="w-full px-3 py-2 bg-slate-50 rounded-lg border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-xs text-slate-800" placeholder="https://example.com/foto.jpg">
                    <p class="text-[9px] text-slate-400 mt-0.5 italic">*Gunakan link gambar online (misal dari Unsplash, Imgur, dsb.)</p>
                </div>
 
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="prod_deskripsi" rows="2" required class="w-full px-3 py-2 bg-slate-50 rounded-lg border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-xs text-slate-800 resize-none" placeholder="Tulis deskripsi produk..."></textarea>
                </div>
 
                <div class="pt-3 border-t border-slate-100 flex gap-2.5 justify-end">
                    <button type="button" onclick="closeModal()" class="px-4 py-2 bg-white text-slate-600 border border-slate-200 rounded-lg font-bold hover:bg-slate-50 transition-colors cursor-pointer text-xs">Batal</button>
                    <button type="submit" name="simpan" class="px-5 py-2 bg-lime-600 text-white rounded-lg font-bold hover:bg-lime-700 hover:shadow-lg hover:shadow-lime-200/40 transition-all cursor-pointer text-xs">Simpan Produk</button>
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
            document.getElementById('prod_daerah').value = "";
            document.getElementById('prod_deskripsi').value = "";
            document.getElementById('prod_gambar').value = "";
            
            showModal();
        }

        function editProduk(data) {
            document.getElementById('modalTitle').innerText = "Edit Produk";
            document.getElementById('prod_id').value = data.id;
            document.getElementById('prod_nama').value = data.nama_produk;
            document.getElementById('prod_harga').value = data.harga;
            document.getElementById('prod_stok').value = data.stok;
            document.getElementById('prod_kategori').value = data.id_kategori;
            document.getElementById('prod_daerah').value = data.id_daerah;
            document.getElementById('prod_deskripsi').value = data.deskripsi;
            document.getElementById('prod_gambar').value = data.gambar;
            
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