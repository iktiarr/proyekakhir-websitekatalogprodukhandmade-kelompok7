<section class="py-16 lg:py-24 bg-slate-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4 sm:gap-0 mb-10 sm:mb-12">
      <div>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-800 mb-2">Produk Unggulan</h2>
        <p class="text-slate-600 text-base sm:text-lg">Koleksi terbaik minggu ini yang dikurasi khusus untuk Anda.</p>
      </div>
      
      <a href="katalog.php" class="group flex items-center text-lime-600 font-bold hover:text-lime-700 transition-colors duration-300 text-sm sm:text-base whitespace-nowrap bg-lime-50 hover:bg-lime-100 px-4 py-2 rounded-lg">
        Lihat Semua 
        <i class="fa-solid fa-arrow-right ml-2 text-xs transition-transform duration-300 group-hover:translate-x-1"></i>
      </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8">
      <?php
      $produk = mysqli_query($conn, "SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id LIMIT 4");
      if (mysqli_num_rows($produk) > 0):
        while($row = mysqli_fetch_assoc($produk)):
      ?>
      <div class="group bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-lime-900/5 hover:-translate-y-1 transition-all duration-300 flex flex-col">
        
        <div class="relative h-56 sm:h-64 overflow-hidden bg-slate-100">
          <img 
            src="<?= $row['gambar'] ?: 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=500'; ?>" 
            alt="<?= $row['nama_produk']; ?>" 
            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
          >
          <div class="absolute top-3 left-3">
            <span class="bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-md text-[10px] sm:text-xs font-bold text-slate-700 uppercase tracking-widest shadow-sm">
              <?= $row['nama_kategori']; ?>
            </span>
          </div>
        </div>
        
        <div class="p-5 sm:p-6 flex flex-col flex-grow">
          <h3 class="text-base sm:text-lg font-bold text-slate-800 group-hover:text-lime-600 transition-colors duration-300 line-clamp-1">
            <?= $row['nama_produk']; ?>
          </h3>
          
          <p class="text-slate-500 text-sm mt-2 line-clamp-2 flex-grow">
            <?= $row['deskripsi']; ?>
          </p>
          
          <div class="mt-5 flex justify-between items-center pt-4 border-t border-slate-100">
            <span class="text-lg sm:text-xl font-extrabold text-lime-600">
              Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
            </span>
            
            <a href="produk_detail.php?id=<?= $row['id']; ?>" class="w-10 h-10 flex items-center justify-center bg-slate-50 text-slate-400 rounded-full group-hover:bg-lime-600 group-hover:text-white transition-all duration-300">
              <i class="fa-solid fa-arrow-right text-sm -rotate-45 group-hover:rotate-0 transition-transform duration-300"></i>
            </a>
          </div>
        </div>
        
      </div>
      <?php endwhile; else: ?>
        
        <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-dashed border-slate-200">
          <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300 text-2xl">
            <i class="fa-solid fa-box-open"></i>
          </div>
          <p class="text-slate-500 font-medium">Belum ada produk yang tersedia saat ini.</p>
        </div>
        
      <?php endif; ?>
    </div>
  </div>
</section>