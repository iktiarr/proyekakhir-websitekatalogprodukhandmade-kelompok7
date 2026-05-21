<section class="py-16 lg:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16">
            <span class="inline-block py-1.5 px-3.5 rounded-full bg-lime-100 text-lime-700 text-xs sm:text-sm font-bold tracking-wider mb-4 shadow-sm">
                KATEGORI PRODUK
            </span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 mb-4">Jelajahi Kategori</h2>
            <p class="text-slate-600 text-base sm:text-lg max-w-2xl mx-auto">Temukan apa yang Anda butuhkan berdasarkan kategori pilihan.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
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
            <a href="katalog.php?kategori=<?= $kat['id']; ?>" class="group flex flex-col items-center bg-slate-50 p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm transition-all duration-300 hover:shadow-md hover:border-lime-200 hover:-translate-y-1">
                
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white rounded-2xl shadow-sm flex items-center justify-center text-slate-400 group-hover:bg-lime-600 group-hover:text-white transition-colors duration-300 mb-4 sm:mb-5 text-2xl sm:text-3xl">
                    <i class="fa-solid <?= $icon; ?>"></i>
                </div>
                
                <h3 class="font-bold text-slate-800 text-base sm:text-lg group-hover:text-lime-600 transition-colors duration-300">
                    <?= $kat['nama_kategori']; ?>
                </h3>
                
                <p class="text-sm text-slate-500 mt-2 flex items-center gap-1.5 group-hover:text-lime-600 transition-colors duration-300">
                    Lihat Koleksi 
                    <i class="fa-solid fa-arrow-right text-xs transition-transform duration-300 group-hover:translate-x-1"></i>
                </p>
                
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>