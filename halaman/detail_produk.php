<?php
// halaman/detail_produk.php: Halaman rincian detail produk, menampilkan detail deskripsi, kategori, daerah asal, sisa stok, serta form untuk menambahkan ke keranjang belanja.

$awalan = "../";
include '../koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: katalog.php");
    exit();
}

$id_produk = (int) $_GET['id'];
$kueri_produk = kueri("SELECT p.*, k.nama_kategori, d.nama_daerah FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id LEFT JOIN daerah d ON p.id_daerah = d.id WHERE p.id = ?", [$id_produk]);
$data_produk = mysqli_fetch_assoc($kueri_produk);

if (!$data_produk || $data_produk['stok'] <= 0) {
    header("Location: katalog.php");
    exit();
}

if (isset($_POST['tambah_keranjang'])) {
    if (!isset($_SESSION['user']['id'])) {
        header("Location: ../masuk.php");
        exit();
    }

    $id_pengguna = $_SESSION['user']['id'];
    $jumlah = (int) $_POST['jumlah'];

    $cek_keranjang = kueri("SELECT * FROM keranjang WHERE id_pengguna = ? AND id_produk = ?", [$id_pengguna, $id_produk]);
    if ($cek_keranjang && mysqli_num_rows($cek_keranjang) > 0) {
        kueri("UPDATE keranjang SET jumlah = jumlah + ? WHERE id_pengguna = ? AND id_produk = ?", [$jumlah, $id_pengguna, $id_produk]);
    } else {
        kueri("INSERT INTO keranjang (id_pengguna, id_produk, jumlah) VALUES (?, ?, ?)", [$id_pengguna, $id_produk, $jumlah]);
    }
    header("Location: keranjang.php");
    exit();
}
?>

<?php include '../bagian/atas.php'; ?>

<div class="py-12 bg-white dark:bg-slate-900 min-h-[80vh] transition-colors duration-300">
    <div class="max-w-5xl mx-auto px-4">

        <div class="lg:flex lg:gap-10 items-start">
            <div class="lg:w-5/12 w-full mb-8 lg:mb-0 max-w-md mx-auto lg:max-w-none">
                <div
                    class="relative rounded-xl overflow-hidden shadow-sm border border-slate-200/60 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 aspect-square group">
                    <img src="<?= dapatkan_jalur_gambar($data_produk['gambar']); ?>"
                        alt="<?= $data_produk['nama_produk']; ?>"
                        class="w-full h-full object-cover transition-transform duration-700">
                    <div class="absolute top-4 left-4 flex gap-2">
                        <span
                            class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm px-3 py-1.5 rounded-xl text-[10px] font-bold text-slate-700 dark:text-slate-200 shadow-sm border border-slate-200 dark:border-slate-800 uppercase tracking-widest">
                            <?= $data_produk['nama_kategori']; ?>
                        </span>
                        <?php if (!empty($data_produk['nama_daerah'])): ?>
                            <span
                                class="bg-lime-600/95 backdrop-blur-sm px-3 py-1.5 rounded-xl text-[10px] font-bold text-white shadow-sm border border-lime-600 uppercase tracking-widest flex items-center gap-1">
                                <i class="fa-solid fa-location-dot"></i> <?= $data_produk['nama_daerah']; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="lg:w-7/12 flex flex-col pl-0 lg:pl-4">

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800 dark:text-slate-100 leading-tight mb-4">
                    <?= $data_produk['nama_produk']; ?>
                </h1>

                <div class="flex items-center space-x-4 mb-6 border-b border-slate-100 dark:border-slate-800 pb-5">
                    <span class="text-2xl font-extrabold text-lime-600 dark:text-lime-400">
                        Rp <?= number_format($data_produk['harga'], 0, ',', '.'); ?>
                    </span>
                    <div class="h-4 w-px bg-slate-200 dark:bg-slate-800"></div>
                    <span class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 font-medium">
                        Stok Tersedia: <span
                            class="text-slate-800 dark:text-slate-200 font-bold"><?= $data_produk['stok']; ?></span>
                        unit
                    </span>
                </div>

                <div
                    class="bg-slate-50 dark:bg-slate-950/20 rounded-xl p-5 mb-6 border border-slate-200/60 dark:border-slate-800">
                    <h3
                        class="text-xs font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-widest mb-2.5">
                        Deskripsi Kerajinan</h3>
                    <p class="text-slate-600 dark:text-slate-350 leading-relaxed text-xs sm:text-sm">
                        <?= nl2br($data_produk['deskripsi']); ?>
                    </p>
                </div>

                <form action="" method="POST" class="space-y-5">
                    <div>
                        <label
                            class="block text-[10px] font-extrabold text-slate-400 dark:text-slate-500 mb-2 uppercase tracking-widest">Pilih
                            Jumlah</label>
                        <div
                            class="inline-flex items-center border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 p-1 shadow-sm">
                            <button type="button" onclick="ubahJumlah(-1)"
                                class="w-9 h-9 flex items-center justify-center hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-lime-600 dark:hover:text-lime-400 rounded-lg transition-colors text-slate-400 dark:text-slate-500 cursor-pointer">
                                <i class="fa-solid fa-minus text-xs"></i>
                            </button>

                            <input type="number" name="jumlah" id="masukanJumlah" value="1" min="1"
                                max="<?= $data_produk['stok']; ?>"
                                class="w-12 text-center font-extrabold text-slate-800 dark:text-slate-200 bg-transparent border-none focus:ring-0 outline-none p-0 text-sm">

                            <button type="button" onclick="ubahJumlah(1)"
                                class="w-9 h-9 flex items-center justify-center bg-white hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-lime-600 dark:hover:text-lime-400 rounded-lg transition-colors text-slate-400 dark:text-slate-500 cursor-pointer">
                                <i class="fa-solid fa-plus text-xs"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" name="tambah_keranjang"
                        class="group w-full sm:w-auto inline-flex bg-lime-600 text-white py-3.5 px-8 rounded-xl font-bold text-sm hover:bg-lime-700 transition-all duration-300 items-center justify-center cursor-pointer border-none">
                        <i class="fa-solid fa-cart-plus mr-2.5"></i> Tambahkan Ke Keranjang
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    // Menambah atau mengurangi kuantitas pesanan produk dengan batas minimum 1 dan maksimum stok tersedia
    function ubahJumlah(nilaiTambahan) {
        const masukan = document.getElementById('masukanJumlah');
        let nilai = parseInt(masukan.value) + nilaiTambahan;

        if (nilai < 1) nilai = 1;
        if (nilai > <?= $data_produk['stok']; ?>) nilai = <?= $data_produk['stok']; ?>;

        masukan.value = nilai;
    }
</script>

<?php include '../bagian/bawah.php'; ?>