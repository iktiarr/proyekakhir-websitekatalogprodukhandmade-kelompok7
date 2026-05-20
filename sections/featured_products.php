<!-- Featured Products -->
<section class="py-12 sm:py-16 lg:py-20 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-3 sm:gap-0 mb-8 sm:mb-10">
      <div>
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-1">Produk Unggulan</h2>
        <p class="text-gray-600 text-sm sm:text-base">Pilihan terbaik minggu ini untuk Anda.</p>
      </div>
      <a href="katalog.php" class="text-lime-600 font-bold hover:text-lime-700 hover:underline flex items-center transition text-sm whitespace-nowrap">
        Lihat Semua <i class="fa-solid fa-chevron-right ml-1 text-xs"></i>
      </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
      <?php
      $produk = mysqli_query($conn, "SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id LIMIT 4");
      if (mysqli_num_rows($produk) > 0):
        while($row = mysqli_fetch_assoc($produk)):
      ?>
      <div class="group bg-white rounded-xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-500 border-2 border-lime-100 hover:border-lime-300">
        <div class="relative h-48 sm:h-52 overflow-hidden bg-gradient-to-br from-lime-50 to-lime-100">
          <img src="<?= $row['gambar'] ?: 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=500'; ?>" alt="<?= $row['nama_produk']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
          <div class="absolute top-2 sm:top-3 left-2 sm:left-3">
            <span class="bg-white/95 backdrop-blur px-2 py-1 rounded-lg text-xs font-bold text-lime-600 uppercase tracking-wider">
              <?= $row['nama_kategori']; ?>
            </span>
          </div>
        </div>
        <div class="p-4 sm:p-5">
          <h3 class="text-sm sm:text-base font-bold text-gray-900 group-hover:text-lime-600 transition line-clamp-2"><?= $row['nama_produk']; ?></h3>
          <p class="text-gray-600 text-xs sm:text-sm mt-1.5 line-clamp-2"><?= $row['deskripsi']; ?></p>
          <div class="mt-3 sm:mt-4 flex justify-between items-center">
            <span class="text-base sm:text-lg font-extrabold text-gray-900">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></span>
            <a href="produk_detail.php?id=<?= $row['id']; ?>" class="p-2 bg-lime-100 text-lime-600 rounded-lg group-hover:bg-lime-600 group-hover:text-white transition shadow-sm hover:shadow-md duration-300">
              <i class="fa-solid fa-plus text-sm"></i>
            </a>
          </div>
        </div>
      </div>
      <?php endwhile; else: ?>
        <div class="col-span-full py-12 text-center bg-lime-50 rounded-xl border-2 border-dashed border-lime-200">
            <i class="fa-solid fa-box text-3xl text-lime-200 mb-3 block"></i>
            <p class="text-lime-600 font-medium text-sm">Belum ada produk tersedia.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
