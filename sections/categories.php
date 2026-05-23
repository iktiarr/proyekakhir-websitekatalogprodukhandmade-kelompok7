<section class="py-10 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mb-1">Jelajahi Kategori</h2>
            <p class="text-slate-500 text-sm">Temukan apa yang Anda butuhkan berdasarkan kategori pilihan.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
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
            <a href="katalog.php?kategori=<?= $kat['id']; ?>" class="group flex flex-col items-center bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-100 shadow-sm transition-all duration-300 hover:shadow-md hover:border-lime-200 hover:-translate-y-1">
                
                <div class="w-12 h-12 sm:w-14 sm:h-14 bg-white rounded-xl shadow-sm flex items-center justify-center text-slate-400 group-hover:bg-lime-600 group-hover:text-white transition-colors duration-300 mb-3 text-xl">
                    <i class="fa-solid <?= $icon; ?>"></i>
                </div>
                
                <h3 class="font-bold text-slate-800 text-sm sm:text-base group-hover:text-lime-600 transition-colors duration-300">
                    <?= $kat['nama_kategori']; ?>
                </h3>
                
                <p class="text-xs text-slate-400 mt-1 flex items-center gap-1 group-hover:text-lime-600 transition-colors duration-300">
                    Lihat Koleksi 
                    <i class="fa-solid fa-arrow-right text-[10px] transition-transform duration-300 group-hover:translate-x-0.5"></i>
                </p>
                
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>