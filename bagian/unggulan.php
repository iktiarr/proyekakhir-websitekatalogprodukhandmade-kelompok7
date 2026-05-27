<section class="py-16 bg-slate-50 dark:bg-slate-950 transition-colors duration-300">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 mb-8">
      <div>
        <h2 class="text-3xl font-extrabold text-slate-800 dark:text-slate-100 mb-1">Produk Unggulan Pilihan</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm">Koleksi kerajinan terbaik Madura yang dikurasi khusus oleh tim ahli kami.</p>
      </div>
      
      <a href="<?= $awalan; ?>halaman/katalog.php" class="group flex items-center text-lime-650 dark:text-lime-400 font-bold hover:text-lime-750 dark:hover:text-lime-300 transition-colors duration-300 text-xs whitespace-nowrap bg-lime-50 dark:bg-lime-950/40 hover:bg-lime-100 dark:hover:bg-lime-950/60 px-4 py-2.5 rounded-xl border border-lime-100 dark:border-lime-900/50">
        Lihat Semua 
        <i class="fa-solid fa-arrow-right ml-1.5 text-[10px] transition-transform duration-300 group-hover:translate-x-0.5"></i>
      </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php
      $data_produk = mysqli_query($koneksi, "SELECT p.*, k.nama_kategori, d.nama_daerah FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id LEFT JOIN daerah d ON p.id_daerah = d.id LIMIT 4");
      if (mysqli_num_rows($data_produk) > 0):
        while($baris = mysqli_fetch_assoc($data_produk)):
      ?>
      <div class="group bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-200/60 dark:border-slate-800 shadow-sm hover:shadow-xl hover:shadow-lime-900/5 hover:-translate-y-1 transition-all duration-300 flex flex-col">
        
        <div class="relative h-48 sm:h-52 overflow-hidden bg-slate-100 dark:bg-slate-800">
          <img 
            src="<?= $baris['gambar']; ?>" 
            alt="<?= $baris['nama_produk']; ?>" 
            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
          >
          <div class="absolute top-2.5 left-2.5 flex gap-1.5">
            <span class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm px-2.5 py-1 rounded text-[9px] font-bold text-slate-700 dark:text-slate-200 uppercase tracking-widest shadow-sm border border-slate-100 dark:border-slate-850">
              <?= $baris['nama_kategori']; ?>
            </span>
            <?php if(!empty($baris['nama_daerah'])): ?>
              <span class="bg-lime-600/95 dark:bg-lime-650/80 backdrop-blur-sm px-2.5 py-1 rounded text-[9px] font-bold text-white uppercase tracking-widest shadow-sm flex items-center gap-0.5">
                <i class="fa-solid fa-location-dot"></i> <?= $baris['nama_daerah']; ?>
              </span>
            <?php endif; ?>
          </div>
        </div>
        
        <div class="p-5 flex flex-col flex-grow">
          <h3 class="text-sm sm:text-base font-bold text-slate-800 dark:text-slate-200 group-hover:text-lime-600 dark:group-hover:text-lime-400 transition-colors duration-300 line-clamp-1">
            <?= $baris['nama_produk']; ?>
          </h3>
          
          <p class="text-slate-500 dark:text-slate-400 text-xs mt-1.5 line-clamp-2 flex-grow leading-relaxed">
            <?= $baris['deskripsi']; ?>
          </p>
          
          <div class="mt-4 pt-3.5 border-t border-slate-100 dark:border-slate-800/80 flex justify-between items-center">
            <span class="text-base font-extrabold text-lime-600 dark:text-lime-400">
              Rp <?= number_format($baris['harga'], 0, ',', '.'); ?>
            </span>
            
            <a href="<?= $awalan; ?>halaman/detail_produk.php?id=<?= $baris['id']; ?>" class="w-9 h-9 flex items-center justify-center bg-slate-50 dark:bg-slate-800 text-slate-400 dark:text-slate-550 rounded-full group-hover:bg-lime-600 group-hover:text-white transition-all duration-300">
              <i class="fa-solid fa-arrow-right text-xs -rotate-45 group-hover:rotate-0 transition-transform duration-300"></i>
            </a>
          </div>
        </div>
        
      </div>
      <?php endwhile; else: ?>
        
        <div class="col-span-full py-16 text-center bg-white dark:bg-slate-900 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
          <div class="w-12 h-12 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 dark:text-slate-600 text-xl">
            <i class="fa-solid fa-box-open"></i>
          </div>
          <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">Belum ada produk Madura yang tersedia saat ini.</p>
        </div>
        
      <?php endif; ?>
    </div>
  </div>
</section>
