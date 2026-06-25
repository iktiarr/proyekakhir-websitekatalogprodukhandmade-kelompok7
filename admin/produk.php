<?php
// admin/produk.php: Halaman panel admin untuk mengelola katalog produk (tambah, edit, hapus) dengan opsi upload file gambar lokal atau menggunakan tautan URL online.

include '../koneksi.php';

// Memastikan pengguna telah masuk sebagai admin
if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'admin') {
    header("Location: ../masuk.php");
    exit();
}

$berhasil = '';
$galat = '';

// Menangani penyimpanan data produk (Tambah atau Edit)
if (isset($_POST['simpan'])) {
    $id_produk = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $nama = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $harga = (float) $_POST['harga'];
    $stok = (int) $_POST['stok'];
    $id_kategori = (int) $_POST['id_kategori'];
    $id_daerah = isset($_POST['id_daerah']) ? (int) $_POST['id_daerah'] : 0;
    $val_daerah = $id_daerah > 0 ? $id_daerah : null;

    $gambar_mentah = '';

    // 1. Proses upload file gambar jika ada yang diunggah
    if (isset($_FILES['gambar_upload']) && $_FILES['gambar_upload']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['gambar_upload']['tmp_name'];
        $file_nama = $_FILES['gambar_upload']['name'];
        $ekstensi = strtolower(pathinfo($file_nama, PATHINFO_EXTENSION));
        $ekstensi_diperbolehkan = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        if (in_array($ekstensi, $ekstensi_diperbolehkan)) {
            $nama_baru = uniqid('img_', true) . '.' . $ekstensi;
            $folder_tujuan = '../uploads/';

            // Membuat folder uploads jika belum ada
            if (!is_dir($folder_tujuan)) {
                mkdir($folder_tujuan, 0755, true);
            }

            if (move_uploaded_file($file_tmp, $folder_tujuan . $nama_baru)) {
                $gambar_mentah = 'uploads/' . $nama_baru;

                // Hapus file gambar lokal yang lama jika sedang mengedit
                if ($id_produk > 0 && !empty($_POST['gambar_lama'])) {
                    $gambar_lama = trim($_POST['gambar_lama']);
                    if (strpos($gambar_lama, 'uploads/') === 0 && file_exists('../' . $gambar_lama)) {
                        @unlink('../' . $gambar_lama);
                    }
                }
            } else {
                $galat = "Gagal mengunggah file gambar ke server.";
            }
        } else {
            $galat = "Format file tidak valid! Gunakan JPG, JPEG, PNG, GIF, atau WEBP.";
        }
    }

    // 2. Jika tidak ada file baru yang diunggah, periksa metode input link atau fallback gambar lama
    if (empty($gambar_mentah) && empty($galat)) {
        $opsi_sumber = $_POST['sumber_gambar_opsi'];
        if ($opsi_sumber === 'link') {
            $gambar_mentah = trim($_POST['gambar']);

            // Mengubah link share Unsplash menjadi tautan download langsung
            if (stripos($gambar_mentah, 'unsplash.com') !== false && stripos($gambar_mentah, 'images.unsplash.com') === false) {
                $jalur = parse_url($gambar_mentah, PHP_URL_PATH);
                if ($jalur) {
                    $segmen = explode('/', trim($jalur, '/'));
                    $segmen_terakhir = end($segmen);
                    if ($segmen_terakhir) {
                        $sub_segmen = explode('-', $segmen_terakhir);
                        $id_unsplash = end($sub_segmen);
                        if ($id_unsplash) {
                            $gambar_mentah = "https://unsplash.com/photos/" . $id_unsplash . "/download";
                        }
                    }
                }
            }
        } else {
            // Gunakan gambar lama jika mengedit dan memilih tab upload tanpa memilih file baru
            $gambar_mentah = trim($_POST['gambar_lama']);
        }
    }

    if (empty($gambar_mentah) && empty($galat)) {
        $galat = "Gambar produk wajib ditentukan (menggunakan link atau upload file)!";
    }

    // Validasi harga dan stok tidak boleh negatif
    if ($harga < 0 || $stok < 0) {
        $galat = "Gagal menyimpan: Harga dan Stok tidak boleh bernilai negatif!";
    } elseif (empty($galat)) {
        if ($id_produk > 0) {
            $sql = "UPDATE produk SET nama_produk=?, deskripsi=?, harga=?, stok=?, gambar=?, id_kategori=?, id_daerah=? WHERE id=?";
            $params = [$nama, $deskripsi, $harga, $stok, $gambar_mentah, $id_kategori, $val_daerah, $id_produk];
        } else {
            $sql = "INSERT INTO produk (nama_produk, deskripsi, harga, stok, gambar, id_kategori, id_daerah) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $params = [$nama, $deskripsi, $harga, $stok, $gambar_mentah, $id_kategori, $val_daerah];
        }

        if (kueri($sql, $params)) {
            $berhasil = "Produk berhasil disimpan!";
            $aksi_log = $id_produk > 0 ? 'ubah' : 'tambah';
            catat_log('produk', $aksi_log, ($id_produk > 0 ? "Mengubah detail produk " : "Menambahkan produk baru ") . "'$nama'");
        } else {
            $galat = "Gagal menyimpan: " . mysqli_error($koneksi);
        }
    }
}

// Menangani penghapusan produk
if (isset($_GET['hapus'])) {
    $id_produk = (int) $_GET['hapus'];
    $res_p = kueri("SELECT nama_produk, gambar FROM produk WHERE id=?", [$id_produk]);
    if ($row_p = mysqli_fetch_assoc($res_p)) {
        $nama_p = $row_p['nama_produk'];
        $gambar_p = $row_p['gambar'];
        if (kueri("DELETE FROM produk WHERE id=?", [$id_produk])) {
            // Hapus file gambar lokal dari server jika ada
            if (!empty($gambar_p) && strpos($gambar_p, 'uploads/') === 0 && file_exists('../' . $gambar_p)) {
                @unlink('../' . $gambar_p);
            }
            catat_log('produk', 'hapus', "Menghapus produk '$nama_p'");
        }
    }
    header("Location: produk.php");
    exit();
}

$kueri_produk = kueri("SELECT p.*, k.nama_kategori, d.nama_daerah FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id LEFT JOIN daerah d ON p.id_daerah = d.id ORDER BY p.id DESC");
$kueri_kategori = kueri("SELECT * FROM kategori");
$kueri_daerah = kueri("SELECT * FROM daerah");
$pembayaran_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM pesanan WHERE status = 'dibayar'"))['total'];
$testimoni_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM testimonial WHERE status = 'pending'"))['total'];
$laporan_tertunda = mysqli_fetch_assoc(kueri("SELECT COUNT(*) as total FROM laporan_kendala WHERE status = 'pending'"))['total'];
?>

<?php
$halaman_aktif = 'produk';
$judul_halaman = 'Kelola Produk';
include 'bagian/atas.php';
?>

    <main class="flex-grow p-4 sm:p-6 w-full max-w-6xl mx-auto overflow-x-hidden">

        <div class="flex justify-end mb-6">
            <button onclick="bukaModalTambah()"
                class="bg-lime-600 text-white px-4 py-2.5 rounded-xl font-bold hover:bg-lime-700 transition-all duration-300 flex items-center cursor-pointer text-xs sm:text-sm">
                <i class="fa-solid fa-plus mr-1.5"></i> Tambah Produk
            </button>
        </div>

        <?php if ($berhasil): ?>
            <div
                class="bg-lime-50 dark:bg-lime-950/20 text-lime-700 dark:text-lime-400 p-3 rounded-xl mb-6 border border-lime-100 dark:border-lime-900/30 flex items-center shadow-sm text-xs">
                <i class="fa-solid fa-circle-check mr-2"></i> <?= $berhasil; ?>
            </div>
        <?php endif; ?>

        <?php if ($galat): ?>
            <div
                class="bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 p-3 rounded-xl mb-6 border border-red-100 dark:border-red-900/30 flex items-center shadow-sm text-xs">
                <i class="fa-solid fa-circle-exclamation mr-2"></i> <?= $galat; ?>
            </div>
        <?php endif; ?>

        <div
            class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm transition-colors duration-300">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead
                        class="bg-slate-50 dark:bg-slate-900/50 text-slate-400 dark:text-slate-500 text-[10px] uppercase tracking-wider font-bold border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="px-4 py-3.5 pl-6">Produk</th>
                            <th class="px-4 py-3.5">Kategori</th>
                            <th class="px-4 py-3.5">Daerah</th>
                            <th class="px-4 py-3.5">Harga</th>
                            <th class="px-4 py-3.5">Stok</th>
                            <th class="px-4 py-3.5 pr-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-800/60">
                        <?php
                        if (mysqli_num_rows($kueri_produk) > 0):
                            while ($produk = mysqli_fetch_assoc($kueri_produk)):
                                ?>
                                <tr
                                    class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors duration-200 text-xs sm:text-sm">
                                    <td class="px-4 py-3 pl-6">
                                        <div class="flex items-center space-x-3">
                                            <img src="<?= dapatkan_jalur_gambar($produk['gambar']); ?>"
                                                alt="<?= $produk['nama_produk']; ?>"
                                                class="w-10 h-10 rounded-xl object-cover border border-slate-100 dark:border-slate-800 shadow-sm bg-white dark:bg-slate-950 flex-shrink-0">
                                            <span
                                                class="font-bold text-slate-800 dark:text-slate-200 line-clamp-1"><?= $produk['nama_produk']; ?></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-xs font-medium text-slate-500 dark:text-slate-400">
                                        <?= $produk['nama_kategori'] ?: '<span class="text-slate-300 dark:text-slate-600 italic">Tanpa Kategori</span>'; ?>
                                    </td>
                                    <td class="px-4 py-3 text-xs font-medium text-slate-500 dark:text-slate-400">
                                        <?= $produk['nama_daerah'] ?: '<span class="text-slate-300 dark:text-slate-600 italic">-</span>'; ?>
                                    </td>
                                    <td class="px-4 py-3 font-extrabold text-slate-800 dark:text-slate-200 whitespace-nowrap">
                                        Rp <?= number_format($produk['harga'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 bg-lime-50 dark:bg-lime-950/40 text-lime-700 dark:text-lime-400 rounded-xl text-[10px] font-bold border border-lime-100 dark:border-lime-900/40 whitespace-nowrap">
                                            <?= $produk['stok']; ?> unit
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 pr-6 text-right space-x-1 whitespace-nowrap">
                                        <button
                                            onclick="ubahProduk(<?= htmlspecialchars(json_encode($produk), ENT_QUOTES, 'UTF-8'); ?>)"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-xl text-blue-500 hover:text-blue-700 hover:bg-blue-50 dark:hover:bg-blue-950/30 transition-colors cursor-pointer"
                                            title="Edit">
                                            <i class="fa-solid fa-pen-to-square text-sm"></i>
                                        </button>
                                        <a href="produk.php?hapus=<?= $produk['id']; ?>"
                                            onclick="return confirm('Hapus produk ini secara permanen?')"
                                            class="w-8 h-8 inline-flex items-center justify-center rounded-xl text-red-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 transition-colors"
                                            title="Hapus">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php
                            endwhile;
                        else:
                            ?>
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-slate-400 dark:text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <i
                                            class="fa-solid fa-box-open text-2xl mb-2.5 text-slate-300 dark:text-slate-700"></i>
                                        <p class="text-xs">Belum ada produk di katalog.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <div id="modalProduk"
        class="fixed inset-0 bg-slate-900/80 dark:bg-slate-950/80 backdrop-blur-sm z-[60] flex items-center justify-center opacity-0 invisible transition-all duration-300 py-6 px-4">
        <div id="kontenModal"
            class="bg-white dark:bg-slate-900 w-full max-w-md rounded-xl p-4 sm:p-5 shadow-2xl dark:shadow-none border border-slate-100 dark:border-slate-800 transform scale-95 transition-transform duration-300 max-h-full overflow-y-auto custom-scrollbar">

            <div class="flex justify-between items-center mb-4 border-b border-slate-100 dark:border-slate-800 pb-3">
                <h2 id="judulModal" class="text-lg font-extrabold text-slate-800 dark:text-slate-100">Tambah Produk Baru
                </h2>
                <button onclick="tutupModal()"
                    class="w-7 h-7 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                <input type="hidden" name="id" id="produk_id">
                <input type="hidden" name="gambar_lama" id="produk_gambar_lama">
                <input type="hidden" name="sumber_gambar_opsi" id="sumber_gambar_opsi" value="link">

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Nama Produk</label>
                    <input type="text" name="nama" id="produk_nama" required
                        class="w-full px-3 py-2 bg-white text-slate-800 rounded-xl border border-slate-200 outline-none transition-all text-xs"
                        placeholder="Masukkan nama produk">
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Harga
                            (Rp)</label>
                        <input type="number" name="harga" id="produk_harga" min="0" required
                            class="w-full px-3 py-2 bg-white text-slate-800 rounded-xl border border-slate-200 outline-none transition-all text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Stok
                            (Unit)</label>
                        <input type="number" name="stok" id="produk_stok" min="0" required
                            class="w-full px-3 py-2 bg-white text-slate-800 rounded-xl border border-slate-200 outline-none transition-all text-xs">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Kategori</label>
                        <select name="id_kategori" id="produk_kategori" required
                            class="w-full px-3 py-2 bg-white text-slate-800 rounded-xl border border-slate-200 outline-none transition-all text-xs">
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            <?php
                            mysqli_data_seek($kueri_kategori, 0);
                            while ($kategori = mysqli_fetch_assoc($kueri_kategori)): ?>
                                <option value="<?= $kategori['id']; ?>"><?= $kategori['nama_kategori']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Asal
                            Daerah</label>
                        <select name="id_daerah" id="produk_daerah" required
                            class="w-full px-3 py-2 bg-white text-slate-800 rounded-xl border border-slate-200 outline-none transition-all text-xs">
                            <option value="" disabled selected>-- Pilih Daerah --</option>
                            <?php
                            mysqli_data_seek($kueri_daerah, 0);
                            while ($daerah = mysqli_fetch_assoc($kueri_daerah)): ?>
                                <option value="<?= $daerah['id']; ?>"><?= $daerah['nama_daerah']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <!-- Opsi Metode Input Gambar -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Metode Foto
                        Produk</label>
                    <div class="grid grid-cols-2 gap-2 bg-slate-150 dark:bg-slate-850 p-1 rounded-xl">
                        <button type="button" id="tab-link" onclick="setSumberGambar('link')"
                            class="py-1.5 text-xs font-bold rounded-lg text-center transition-all cursor-pointer">Link
                            URL</button>
                        <button type="button" id="tab-upload" onclick="setSumberGambar('upload')"
                            class="py-1.5 text-xs font-bold rounded-lg text-center transition-all cursor-pointer">Upload
                            File</button>
                    </div>
                </div>

                <!-- Input Menggunakan Link URL -->
                <div id="container-link">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Link Foto
                        Produk</label>
                    <input type="url" name="gambar" id="produk_gambar"
                        class="w-full px-3 py-2 bg-white text-slate-800 rounded-xl border border-slate-200 outline-none transition-all text-xs"
                        placeholder="https://example.com/foto.jpg">
                    <p class="text-[9px] text-slate-400 dark:text-slate-500 mt-0.5 italic">*Gunakan link gambar online
                        (misal dari Unsplash, Imgur, dsb.)</p>
                </div>

                <!-- Input Menggunakan Upload File -->
                <div id="container-upload" class="hidden">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Upload File
                        Foto</label>
                    <input type="file" name="gambar_upload" id="produk_gambar_upload" accept="image/*"
                        class="w-full px-3 py-1.5 bg-white text-slate-800 rounded-xl border border-slate-200 outline-none transition-all text-xs file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-lime-50 file:text-lime-700 hover:file:bg-lime-100 cursor-pointer">
                    <p class="text-[9px] text-slate-400 dark:text-slate-550 mt-0.5 italic">*Pilih file gambar dari
                        komputer Anda (Mendukung: JPG, PNG, WEBP)</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Deskripsi</label>
                    <textarea name="deskripsi" id="produk_deskripsi" rows="2" required
                        class="w-full px-3 py-2 bg-white text-slate-800 rounded-xl border border-slate-200 outline-none transition-all text-xs resize-none"
                        placeholder="Tulis deskripsi produk..."></textarea>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex gap-2.5 justify-end">
                    <button type="button" onclick="tutupModal()"
                        class="px-4 py-2 bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800 rounded-xl font-bold hover:bg-slate-50 dark:hover:bg-slate-800/80 transition-colors cursor-pointer text-xs">Batal</button>
                    <button type="submit" name="simpan"
                        class="px-5 py-2 bg-lime-600 text-white rounded-xl font-bold hover:bg-lime-700 transition-all cursor-pointer text-xs">Simpan
                        Produk</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
            border-radius: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 8px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>

    <script>
        const modal = document.getElementById('modalProduk');
        const kontenModal = document.getElementById('kontenModal');

        // Mengatur tab metode gambar (Link URL vs Upload File) secara dinamis
        function setSumberGambar(opsi) {
            const tabLink = document.getElementById('tab-link');
            const tabUpload = document.getElementById('tab-upload');
            const containerLink = document.getElementById('container-link');
            const containerUpload = document.getElementById('container-upload');
            const inputOpsi = document.getElementById('sumber_gambar_opsi');
            const inputGambar = document.getElementById('produk_gambar');
            const inputUpload = document.getElementById('produk_gambar_upload');

            inputOpsi.value = opsi;

            if (opsi === 'link') {
                tabLink.className = "py-1.5 text-xs font-bold rounded-lg text-center transition-all cursor-pointer bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 shadow-sm";
                tabUpload.className = "py-1.5 text-xs font-bold rounded-lg text-center transition-all cursor-pointer text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300";
                containerLink.classList.remove('hidden');
                containerUpload.classList.add('hidden');

                // Set required hanya jika tambah produk baru
                const isEdit = document.getElementById('produk_id').value !== "";
                inputGambar.required = !isEdit;
                inputUpload.required = false;
            } else {
                tabUpload.className = "py-1.5 text-xs font-bold rounded-lg text-center transition-all cursor-pointer bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 shadow-sm";
                tabLink.className = "py-1.5 text-xs font-bold rounded-lg text-center transition-all cursor-pointer text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300";
                containerUpload.classList.remove('hidden');
                containerLink.classList.add('hidden');

                // Set required hanya jika tambah produk baru
                const isEdit = document.getElementById('produk_id').value !== "";
                inputUpload.required = !isEdit;
                inputGambar.required = false;
            }
        }

        // Membuka modal tambah produk baru dan mereset seluruh isian form
        function bukaModalTambah() {
            document.getElementById('judulModal').innerText = "Tambah Produk Baru";
            document.getElementById('produk_id').value = "";
            document.getElementById('produk_gambar_lama').value = "";
            document.getElementById('produk_nama').value = "";
            document.getElementById('produk_harga').value = "";
            document.getElementById('produk_stok').value = "";
            document.getElementById('produk_kategori').value = "";
            document.getElementById('produk_daerah').value = "";
            document.getElementById('produk_deskripsi').value = "";
            document.getElementById('produk_gambar').value = "";
            document.getElementById('produk_gambar_upload').value = "";

            setSumberGambar('link');
            tampilkanModal();
        }

        // Membuka modal edit produk dan mengisikan data produk terpilih ke dalam form
        function ubahProduk(data) {
            document.getElementById('judulModal').innerText = "Edit Produk";
            document.getElementById('produk_id').value = data.id;
            document.getElementById('produk_gambar_lama').value = data.gambar;
            document.getElementById('produk_nama').value = data.nama_produk;
            document.getElementById('produk_harga').value = data.harga;
            document.getElementById('produk_stok').value = data.stok;
            document.getElementById('produk_kategori').value = data.id_kategori || "";
            document.getElementById('produk_daerah').value = data.id_daerah || "";
            document.getElementById('produk_deskripsi').value = data.deskripsi;
            document.getElementById('produk_gambar_upload').value = "";

            if (data.gambar && data.gambar.startsWith('uploads/')) {
                document.getElementById('produk_gambar').value = "";
                setSumberGambar('upload');
            } else {
                document.getElementById('produk_gambar').value = data.gambar;
                setSumberGambar('link');
            }

            tampilkanModal();
        }

        // Menampilkan overlay modal kelola produk
        function tampilkanModal() {
            modal.classList.remove('opacity-0', 'invisible');
            kontenModal.classList.remove('scale-95');
        }

        // Menyembunyikan overlay modal kelola produk
        function tutupModal() {
            kontenModal.classList.add('scale-95');
            modal.classList.add('opacity-0', 'invisible');
        }


        document.addEventListener('DOMContentLoaded', () => {
            // Pengontrol Sidebar Seluler
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebar-backdrop');
            const tombolMenuMobile = document.getElementById('tombol-menu-mobile');
            const tombolTutupSidebar = document.getElementById('tombol-tutup-sidebar');

            // Membuka sidebar panel admin pada mode seluler (mobile)
            function bukaSidebar() {
                if (sidebar && backdrop) {
                    sidebar.classList.add('active');
                    backdrop.classList.replace('opacity-0', 'opacity-100');
                    backdrop.classList.replace('pointer-events-none', 'pointer-events-auto');
                    document.body.style.overflow = 'hidden';
                }
            }

            // Menutup sidebar panel admin pada mode seluler (mobile)
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
<?php include 'bagian/bawah.php'; ?>