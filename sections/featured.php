<section class="py-10 bg-slate-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 mb-6">
      <div>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mb-1">Produk Unggulan</h2>
        <p class="text-slate-500 text-sm">Koleksi terbaik minggu ini yang dikurasi khusus untuk Anda.</p>
      </div>
      
      <a href="katalog.php" class="group flex items-center text-lime-600 font-bold hover:text-lime-700 transition-colors duration-300 text-xs whitespace-nowrap bg-lime-50 hover:bg-lime-100 px-3.5 py-1.5 rounded-lg border border-lime-100">
        Lihat Semua 
        <i class="fa-solid fa-arrow-right ml-1.5 text-[10px] transition-transform duration-300 group-hover:translate-x-0.5"></i>
      </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <?php
      $produk = mysqli_query($conn, "SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id LIMIT 4");
      if (mysqli_num_rows($produk) > 0):
        while($row = mysqli_fetch_assoc($produk)):
      ?>
      <div class="group bg-white rounded-2xl overflow-hidden border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-lime-900/5 hover:-translate-y-1 transition-all duration-300 flex flex-col">
        
        <div class="relative h-44 sm:h-48 overflow-hidden bg-slate-100">
          <img 
            src="<?= $row['gambar'] ?: 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=500'; ?>" 
            alt="<?= $row['nama_produk']; ?>" 
            class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
            onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=500';"
          >
          <div class="absolute top-2.5 left-2.5">
            <span class="bg-white/90 backdrop-blur-sm px-2 py-1 rounded text-[9px] font-bold text-slate-700 uppercase tracking-widest shadow-sm">
              <?= $row['nama_kategori']; ?>
            </span>
          </div>
        </div>
        
        <div class="p-4 sm:p-4.5 flex flex-col flex-grow">
          <h3 class="text-sm sm:text-base font-bold text-slate-800 group-hover:text-lime-600 transition-colors duration-300 line-clamp-1">
            <?= $row['nama_produk']; ?>
          </h3>
          
          <p class="text-slate-500 text-xs mt-1.5 line-clamp-2 flex-grow">
            <?= $row['deskripsi']; ?>
          </p>
          
          <div class="mt-3 flex justify-between items-center pt-3 border-t border-slate-50">
            <span class="text-base font-extrabold text-lime-600">
              Rp <?= number_format($row['harga'], 0, ',', '.'); ?>
            </span>
            
            <a href="produk_detail.php?id=<?= $row['id']; ?>" class="w-8 h-8 flex items-center justify-center bg-slate-50 text-slate-400 rounded-full group-hover:bg-lime-600 group-hover:text-white transition-all duration-300">
              <i class="fa-solid fa-arrow-right text-xs -rotate-45 group-hover:rotate-0 transition-transform duration-300"></i>
            </a>
          </div>
        </div>
        
      </div>
      <?php endwhile; else: ?>
        
        <div class="col-span-full py-10 text-center bg-white rounded-2xl border border-dashed border-slate-200">
          <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 text-xl">
            <i class="fa-solid fa-box-open"></i>
          </div>
          <p class="text-slate-500 text-sm font-medium">Belum ada produk yang tersedia saat ini.</p>
        </div>
        
      <?php endif; ?>
    </div>
  </div>
</section>