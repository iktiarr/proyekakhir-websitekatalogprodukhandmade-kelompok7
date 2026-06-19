<?php
// halaman/katalog.php: Halaman katalog produk lengkap bagi pembeli, menyediakan fitur cari produk serta filter kategori dan asal daerah kabupaten.

$awalan = "../";
include '../koneksi.php';

$id_kategori = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$id_daerah = isset($_GET['daerah']) ? (int)$_GET['daerah'] : 0;
$pencarian = isset($_GET['cari']) ? trim($_GET['cari']) : '';

$hasil_kategori = kueri("SELECT * FROM kategori");
$hasil_daerah = kueri("SELECT * FROM daerah");

$kondisi = ["p.stok > 0"];
$params = [];
if ($id_kategori > 0) {
    $kondisi[] = "p.id_kategori = ?";
    $params[] = $id_kategori;
}
if ($id_daerah > 0) {
    $kondisi[] = "p.id_daerah = ?";
    $params[] = $id_daerah;
}
if (!empty($pencarian)) {
    $kondisi[] = "p.nama_produk LIKE ?";
    $params[] = "%" . $pencarian . "%";
}

$klausa_kondisi = count($kondisi) > 0 ? " WHERE " . implode(" AND ", $kondisi) : "";
$hasil_produk = kueri("SELECT p.*, k.nama_kategori, d.nama_daerah FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id LEFT JOIN daerah d ON p.id_daerah = d.id $klausa_kondisi ORDER BY p.id DESC", $params);
?>

<?php include '../bagian/atas.php'; ?>

<div class="py-10 bg-slate-50 dark:bg-slate-950 min-h-[80vh]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Bagian Pencarian & Filter Katalog -->
        <div class="mb-8 bg-white dark:bg-slate-900 p-5 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
            <!-- Form Cari Produk -->
            <form action="katalog.php#daftar-produk" method="GET" class="flex gap-2 w-full max-w-md mb-6">
                <?php if ($id_kategori > 0): ?>
                    <input type="hidden" name="kategori" value="<?= $id_kategori; ?>">
                <?php endif; ?>
                <?php if ($id_daerah > 0): ?>
                    <input type="hidden" name="daerah" value="<?= $id_daerah; ?>">
                <?php endif; ?>
                <div class="relative flex-grow">
                    <input type="text" name="cari" value="<?= htmlspecialchars($pencarian); ?>" placeholder="Cari nama produk..." class="w-full pl-10 pr-4 py-2 bg-white text-slate-800 rounded-xl border border-slate-200 outline-none text-sm">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                </div>
                <button type="submit" class="bg-lime-600 hover:bg-lime-700 text-white px-5 rounded-xl text-sm font-bold cursor-pointer border-none">
                    Cari
                </button>
                <?php if (!empty($pencarian)): ?>
                    <a href="katalog.php?<?= ($id_kategori > 0 ? 'kategori='.$id_kategori : '') . ($id_daerah > 0 ? ($id_kategori > 0 ? '&' : '').'daerah='.$id_daerah : ''); ?>#daftar-produk" class="bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-200/80 px-4 rounded-xl text-xs sm:text-sm font-bold flex items-center justify-center border border-slate-200/60 dark:border-slate-700">
                        Reset
                    </a>
                <?php endif; ?>
            </form>

            <div class="h-px bg-slate-150 dark:bg-slate-800 w-full mb-6"></div>

            <!-- Grid Filter (Kategori & Kabupaten Asal) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Filter Kategori -->
                <div>
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-1">
                        <i class="fa-solid fa-tags text-lime-600"></i> Kategori
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <a href="katalog.php<?= $id_daerah > 0 ? '?daerah=' . $id_daerah : ''; ?><?= !empty($pencarian) ? ($id_daerah > 0 ? '&' : '?') . 'cari=' . urlencode($pencarian) : ''; ?>" class="<?= $id_kategori == 0 ? 'bg-lime-600 text-white' : 'bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:text-lime-600' ?> px-3.5 py-1.5 rounded-lg font-semibold text-xs">
                            Semua Kategori
                        </a>
                        
                        <?php 
                        mysqli_data_seek($hasil_kategori, 0);
                        while($data_kategori = mysqli_fetch_assoc($hasil_kategori)): ?>
                            <a href="katalog.php?kategori=<?= $data_kategori['id']; ?><?= $id_daerah > 0 ? '&daerah=' . $id_daerah : ''; ?><?= !empty($pencarian) ? '&cari=' . urlencode($pencarian) : ''; ?>" class="<?= $id_kategori == $data_kategori['id'] ? 'bg-lime-600 text-white' : 'bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:text-lime-600' ?> px-3.5 py-1.5 rounded-lg font-semibold text-xs">
                                <?= $data_kategori['nama_kategori']; ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Filter Kabupaten Asal Madura -->
                <div>
                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3 flex items-center gap-1">
                        <i class="fa-solid fa-location-dot text-lime-600"></i> Kabupaten
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <a href="katalog.php<?= $id_kategori > 0 ? '?kategori=' . $id_kategori : ''; ?><?= !empty($pencarian) ? ($id_kategori > 0 ? '&' : '?') . 'cari=' . urlencode($pencarian) : ''; ?>" class="<?= $id_daerah == 0 ? 'bg-lime-600 text-white' : 'bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:text-lime-600' ?> px-3.5 py-1.5 rounded-lg font-semibold text-xs">
                            Semua Kabupaten
                        </a>
                        
                        <?php 
                        mysqli_data_seek($hasil_daerah, 0);
                        while($data_daerah = mysqli_fetch_assoc($hasil_daerah)): ?>
                            <a href="katalog.php?daerah=<?= $data_daerah['id']; ?><?= $id_kategori > 0 ? '&kategori=' . $id_kategori : ''; ?><?= !empty($pencarian) ? '&cari=' . urlencode($pencarian) : ''; ?>" class="<?= $id_daerah == $data_daerah['id'] ? 'bg-lime-600 text-white' : 'bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-800 hover:text-lime-600' ?> px-3.5 py-1.5 rounded-lg font-semibold text-xs">
                                <?= $data_daerah['nama_daerah']; ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid Produk -->
        <div id="daftar-produk" class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
            <?php if (mysqli_num_rows($hasil_produk) > 0): ?>
                <?php while($baris = mysqli_fetch_assoc($hasil_produk)): ?>
                
                <div class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col">
                    
                    <div class="relative w-full aspect-square md:h-52 overflow-hidden bg-slate-100 dark:bg-slate-800">
                        <img 
                            src="<?= dapatkan_jalur_gambar($baris['gambar']); ?>" 
                            alt="<?= $baris['nama_produk']; ?>" 
                            class="w-full h-full object-cover"
                        >
                        <div class="absolute top-2 left-2 flex flex-col gap-1 items-start max-w-[calc(100%-16px)]">
                            <span class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm px-2 py-0.5 rounded text-[8px] sm:text-[9px] font-extrabold text-slate-700 dark:text-slate-200 uppercase tracking-widest shadow-sm border border-slate-100 dark:border-slate-800/80 truncate">
                                <?= $baris['nama_kategori']; ?>
                            </span>
                            <?php if(!empty($baris['nama_daerah'])): ?>
                                <span class="bg-lime-600/90 dark:bg-lime-600/80 backdrop-blur-sm px-2 py-0.5 rounded text-[8px] sm:text-[9px] font-extrabold text-white uppercase tracking-widest shadow-sm flex items-center gap-0.5 truncate">
                                    <i class="fa-solid fa-location-dot text-[7px] sm:text-[8px]"></i> <?= $baris['nama_daerah']; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="p-3 sm:p-5 flex flex-col flex-grow">
                        <h3 class="text-sm sm:text-base font-extrabold text-slate-800 dark:text-slate-200 line-clamp-1">
                            <?= $baris['nama_produk']; ?>
                        </h3>
                        
                        <p class="text-slate-500 dark:text-slate-400 text-[11px] sm:text-xs mt-1 line-clamp-2 flex-grow leading-relaxed">
                            <?= $baris['deskripsi']; ?>
                        </p>
                        
                        <div class="mt-3 sm:mt-4 pt-2.5 sm:pt-3.5 border-t border-slate-100 dark:border-slate-800/80 flex justify-between items-center">
                            <span class="text-sm sm:text-base font-black text-lime-600 dark:text-lime-400">
                                Rp <?= number_format($baris['harga'], 0, ',', '.'); ?>
                            </span>
                            
                            <a href="detail_produk.php?id=<?= $baris['id']; ?>" class="w-7 h-7 sm:w-9 sm:h-9 flex items-center justify-center bg-slate-50 dark:bg-slate-800 text-slate-500 hover:bg-lime-600 hover:text-white rounded-full flex-shrink-0">
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                    
                </div>
                <?php endwhile; ?>
                
            <?php else: ?>
                
                <div class="col-span-full py-16 text-center bg-white dark:bg-slate-900 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 shadow-sm">
                    <div class="max-w-md mx-auto px-4">
                        <div class="w-12 h-12 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-350 dark:text-slate-600 text-xl">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-1">Produk Tidak Ditemukan</h3>
                    </div>
                </div>
                
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const inputCari = document.querySelector('input[name="cari"]');
    const kontainerProduk = document.getElementById('daftar-produk');
    
    if (inputCari && kontainerProduk) {
        inputCari.addEventListener('input', () => {
            const kueri = inputCari.value.toLowerCase().trim();
            const kartuProduk = kontainerProduk.querySelectorAll('.rounded-2xl');
            let adaYangCocok = false;

            kartuProduk.forEach(kartu => {
                const elemenJudul = kartu.querySelector('h3');
                if (elemenJudul) {
                    const namaProduk = elemenJudul.textContent.toLowerCase();
                    if (namaProduk.includes(kueri)) {
                        kartu.style.display = '';
                        adaYangCocok = true;
                    } else {
                        kartu.style.display = 'none';
                    }
                }
            });

            // Pesan kosong
            let elemenKosong = document.getElementById('daftar-kosong-instan');
            if (!adaYangCocok && kueri !== '') {
                if (!elemenKosong) {
                    elemenKosong = document.createElement('div');
                    elemenKosong.id = 'daftar-kosong-instan';
                    elemenKosong.className = 'col-span-full py-16 text-center bg-white dark:bg-slate-900 rounded-xl border border-dashed border-slate-200 dark:border-slate-800 shadow-sm';
                    elemenKosong.innerHTML = `
                        <div class="max-w-md mx-auto px-4">
                            <div class="w-12 h-12 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-350 dark:text-slate-600 text-xl">
                                <i class="fa-solid fa-box-open"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-1">Produk Tidak Ditemukan</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-xs">Maaf, tidak ada produk dengan kata kunci "${inputCari.value}" yang cocok.</p>
                        </div>
                    `;
                    kontainerProduk.appendChild(elemenKosong);
                } else {
                    const teksPenjelasan = elemenKosong.querySelector('p');
                    if (teksPenjelasan) {
                        teksPenjelasan.textContent = `Maaf, tidak ada produk dengan kata kunci "${inputCari.value}" yang cocok.`;
                    }
                }
            } else {
                if (elemenKosong) {
                    elemenKosong.remove();
                }
            }
        });
    }
});
</script>

<?php include '../bagian/bawah.php'; ?>
