<!-- Category Quick Access -->
<section class="py-12 sm:py-16 lg:py-20 bg-gradient-to-b from-white to-lime-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 sm:mb-10">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Jelajahi Kategori</h2>
            <p class="text-gray-600 text-sm sm:text-base max-w-2xl mx-auto">Temukan apa yang Anda butuhkan berdasarkan kategori pilihan.</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            <?php
            $kategori_icons = [
                'Aksesoris' => 'fa-gem',
                'Dekorasi' => 'fa-couch',
                'Pakaian' => 'fa-shirt',
                'Lainnya' => 'fa-shapes'
            ];
            $res_kat = mysqli_query($conn, "SELECT * FROM kategori");
            while($kat = mysqli_fetch_assoc($res_kat)):
                $icon = $kategori_icons[$kat['nama_kategori']] ?? 'fa-box';
            ?>
            <a href="katalog.php?kategori=<?= $kat['id']; ?>" class="group bg-white p-4 sm:p-5 rounded-xl border-2 border-lime-100 shadow-sm hover:shadow-lg hover:border-lime-300 hover:-translate-y-0.5 transition-all duration-300 text-center">
                <div class="w-14 h-14 sm:w-16 sm:h-16 bg-lime-100 rounded-lg flex items-center justify-center text-lime-600 group-hover:bg-gradient-to-br group-hover:from-lime-600 group-hover:to-lime-500 group-hover:text-white transition-all duration-300 mx-auto mb-2 sm:mb-3 text-xl">
                    <i class="fa-solid <?= $icon; ?>"></i>
                </div>
                <h3 class="font-bold text-gray-900 text-sm sm:text-base group-hover:text-lime-600 transition"><?= $kat['nama_kategori']; ?></h3>
                <p class="text-xs text-gray-600 mt-1 group-hover:text-lime-600 transition">Lihat Koleksi →</p>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>
