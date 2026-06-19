<!-- Bagian Produk Unggulan: Menampilkan grid produk unggulan/terbaru dari database yang stoknya di atas 0. -->
<section class="py-16 bg-slate-50 dark:bg-slate-950">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Header Bagian Produk Unggulan -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 mb-8">
      <div>
        <h2 class="text-3xl font-bold text-slate-800 dark:text-slate-100 mb-1">Produk Unggulan Pilihan</h2>
        <p class="text-slate-500 dark:text-slate-400 text-sm">Koleksi kerajinan terbaik Madura yang dikurasi khusus oleh tim ahli kami.</p>
      </div>
      
      <a href="<?= $awalan; ?>halaman/katalog.php" class="flex items-center text-lime-600 dark:text-lime-400 font-bold text-xs whitespace-nowrap bg-lime-50 dark:bg-lime-950/40 hover:bg-lime-100 px-4 py-2.5 rounded-xl border border-lime-100 dark:border-lime-900/50">
        Lihat Semua 
        <i class="fa-solid fa-arrow-right ml-1.5 text-[10px]"></i>
      </a>
    </div>

    <!-- Grid Produk Unggulan (2 Kolom di Seluler, 4 Kolom di Desktop) -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
      <?php
      $data_produk = kueri("SELECT p.*, k.nama_kategori, d.nama_daerah FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id LEFT JOIN daerah d ON p.id_daerah = d.id WHERE p.stok > 0 LIMIT 4");
      if (mysqli_num_rows($data_produk) > 0):
        while($baris = mysqli_fetch_assoc($data_produk)):
      ?>
      <div class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-800 flex flex-col">
        
        <!-- Foto Produk dan Badge Daerah -->
        <div class="relative w-full aspect-square md:h-52 overflow-hidden bg-slate-100 dark:bg-slate-800">
          <img 
            src="<?= dapatkan_jalur_gambar($baris['gambar']); ?>" 
            alt="<?= $baris['nama_produk']; ?>" 
            class="w-full h-full object-cover"
          >
          <div class="absolute top-2 left-2 flex flex-col gap-1 items-start max-w-[calc(100%-16px)]">
            <span class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm px-2 py-0.5 rounded text-[8px] sm:text-[9px] font-bold text-slate-700 dark:text-slate-200 uppercase tracking-widest shadow-sm border border-slate-100 dark:border-slate-800/80 truncate">
              <?= $baris['nama_kategori']; ?>
            </span>
            <?php if(!empty($baris['nama_daerah'])): ?>
              <span class="bg-lime-600/90 dark:bg-lime-600/80 backdrop-blur-sm px-2 py-0.5 rounded text-[8px] sm:text-[9px] font-bold text-white uppercase tracking-widest shadow-sm flex items-center gap-0.5 truncate">
                <i class="fa-solid fa-location-dot text-[7px] sm:text-[8px]"></i> <?= $baris['nama_daerah']; ?>
              </span>
            <?php endif; ?>
          </div>
        </div>
        
        <!-- Informasi & Harga Produk -->
        <div class="p-3 sm:p-5 flex flex-col flex-grow">
          <h3 class="text-sm sm:text-base font-bold text-slate-800 dark:text-slate-200 line-clamp-1">
            <?= $baris['nama_produk']; ?>
          </h3>
          
          <p class="text-slate-500 dark:text-slate-400 text-[11px] sm:text-xs mt-1 sm:mt-1.5 line-clamp-2 flex-grow leading-relaxed">
            <?= $baris['deskripsi']; ?>
          </p>
          
          <div class="mt-3 sm:mt-4 pt-2.5 sm:pt-3.5 border-t border-slate-100 dark:border-slate-800/80 flex justify-between items-center">
            <span class="text-sm sm:text-base font-black text-lime-600 dark:text-lime-400">
              Rp <?= number_format($baris['harga'], 0, ',', '.'); ?>
            </span>
            
            <a href="<?= $awalan; ?>halaman/detail_produk.php?id=<?= $baris['id']; ?>" class="w-7 h-7 sm:w-9 sm:h-9 flex items-center justify-center bg-slate-50 dark:bg-slate-800 text-slate-400 hover:bg-lime-600 hover:text-white rounded-full flex-shrink-0">
              <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
          </div>
        </div>
        
      </div>
      <?php endwhile; else: ?>
        
        <!-- Keadaan jika produk kosong -->
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
