<?php include 'koneksi.php'; ?>
<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="relative py-20 overflow-hidden bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="lg:flex lg:items-center lg:space-x-12">
      <div class="lg:w-1/2">
        <h1 class="text-5xl lg:text-7xl font-extrabold text-gray-900 leading-tight">
          Sentuhan <span class="bg-gradient-to-r from-amber-600 to-orange-500 bg-clip-text text-transparent">Tangan</span>, Karya Abadi.
        </h1>
        <p class="mt-6 text-xl text-gray-500 max-w-lg leading-relaxed">
          Temukan koleksi produk handmade unik yang dibuat dengan penuh ketelitian oleh pengrajin lokal berbakat.
        </p>
        <div class="mt-10 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
          <a href="katalog.php" class="bg-amber-600 text-white px-8 py-4 rounded-2xl font-bold text-lg hover:bg-amber-700 transition shadow-xl shadow-amber-200 flex items-center justify-center">
            Lihat Katalog <i class="fa-solid fa-arrow-right ml-2"></i>
          </a>
          <a href="#tentang" class="bg-white text-gray-700 border-2 border-gray-100 px-8 py-4 rounded-2xl font-bold text-lg hover:bg-gray-50 transition flex items-center justify-center">
            Tentang Kami
          </a>
        </div>
      </div>
      <div class="mt-12 lg:mt-0 lg:w-1/2 relative">
        <div class="absolute -top-10 -left-10 w-72 h-72 bg-amber-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
        <div class="absolute -bottom-10 -right-10 w-72 h-72 bg-orange-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="relative rounded-3xl overflow-hidden shadow-2xl">
          <img src="https://images.unsplash.com/photo-1528698827591-e19ccd7bc23d?auto=format&fit=crop&q=80&w=1000" alt="Handmade Product" class="w-full h-[500px] object-cover transform hover:scale-105 transition duration-700">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Trust Badges Section -->
<section class="py-12 bg-white border-y border-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="flex items-center space-x-4 p-4 rounded-2xl hover:bg-gray-50 transition">
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600 flex-shrink-0">
                    <i class="fa-solid fa-truck-fast text-xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm">Pengiriman Cepat</h4>
                    <p class="text-xs text-gray-400">Seluruh Indonesia</p>
                </div>
            </div>
            <div class="flex items-center space-x-4 p-4 rounded-2xl hover:bg-gray-50 transition">
                <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600 flex-shrink-0">
                    <i class="fa-solid fa-shield-halved text-xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm">Kualitas Terjamin</h4>
                    <p class="text-xs text-gray-400">QC sangat ketat</p>
                </div>
            </div>
            <div class="flex items-center space-x-4 p-4 rounded-2xl hover:bg-gray-50 transition">
                <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600 flex-shrink-0">
                    <i class="fa-solid fa-hands-holding-child text-xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm">100% Handmade</h4>
                    <p class="text-xs text-gray-400">Dibuat dengan cinta</p>
                </div>
            </div>
            <div class="flex items-center space-x-4 p-4 rounded-2xl hover:bg-gray-50 transition">
                <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600 flex-shrink-0">
                    <i class="fa-solid fa-headset text-xl"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900 text-sm">Dukungan 24/7</h4>
                    <p class="text-xs text-gray-400">Siap membantu Anda</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Category Quick Access -->
<section class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900">Jelajahi Kategori</h2>
            <p class="text-gray-500 mt-2">Temukan apa yang Anda butuhkan berdasarkan kategori.</p>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
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
            <a href="katalog.php?kategori=<?= $kat['id']; ?>" class="group bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-2 transition-all duration-300 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-400 group-hover:bg-amber-600 group-hover:text-white transition-all mx-auto mb-6">
                    <i class="fa-solid <?= $icon; ?> text-2xl"></i>
                </div>
                <h3 class="font-bold text-gray-900 group-hover:text-amber-600 transition"><?= $kat['nama_kategori']; ?></h3>
                <p class="text-xs text-gray-400 mt-2">Lihat Koleksi</p>
            </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-24 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-end mb-12">
      <div>
        <h2 class="text-3xl font-bold text-gray-900">Produk Unggulan</h2>
        <p class="text-gray-500 mt-2">Pilihan terbaik minggu ini untuk Anda.</p>
      </div>
      <a href="katalog.php" class="text-amber-600 font-bold hover:underline flex items-center">
        Lihat Semua <i class="fa-solid fa-chevron-right ml-2 text-xs"></i>
      </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
      <?php
      $produk = mysqli_query($conn, "SELECT p.*, k.nama_kategori FROM produk p LEFT JOIN kategori k ON p.id_kategori = k.id LIMIT 4");
      if (mysqli_num_rows($produk) > 0):
        while($row = mysqli_fetch_assoc($produk)):
      ?>
      <div class="group bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-500 border border-gray-100">
        <div class="relative h-64 overflow-hidden">
          <img src="<?= $row['gambar'] ?: 'https://images.unsplash.com/photo-1610701596007-11502861dcfa?auto=format&fit=crop&q=80&w=500'; ?>" alt="<?= $row['nama_produk']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
          <div class="absolute top-4 left-4">
            <span class="bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-amber-600 uppercase tracking-wider">
              <?= $row['nama_kategori']; ?>
            </span>
          </div>
        </div>
        <div class="p-6">
          <h3 class="text-lg font-bold text-gray-900 group-hover:text-amber-600 transition"><?= $row['nama_produk']; ?></h3>
          <p class="text-gray-500 text-sm mt-2 line-clamp-2"><?= $row['deskripsi']; ?></p>
          <div class="mt-6 flex justify-between items-center">
            <span class="text-xl font-extrabold text-gray-900">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></span>
            <a href="produk_detail.php?id=<?= $row['id']; ?>" class="p-3 bg-gray-50 text-amber-600 rounded-xl group-hover:bg-amber-600 group-hover:text-white transition shadow-sm">
              <i class="fa-solid fa-plus"></i>
            </a>
          </div>
        </div>
      </div>
      <?php endwhile; else: ?>
        <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-dashed border-gray-200">
            <p class="text-gray-400">Belum ada produk tersedia.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<!-- About Section -->
<section id="tentang" class="py-24 bg-gray-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-amber-600 rounded-[3rem] overflow-hidden shadow-2xl relative">
      <div class="absolute top-0 right-0 w-64 h-64 bg-amber-500 rounded-full -mr-20 -mt-20 filter blur-3xl opacity-50"></div>
      <div class="p-12 lg:p-20 relative z-10 lg:flex lg:items-center justify-between">
        <div class="lg:w-2/3">
            <h2 class="text-4xl font-extrabold text-white mb-6">Mengapa Handmade?</h2>
            <p class="text-amber-100 text-lg leading-relaxed mb-8 max-w-xl">
                Setiap produk yang kami tawarkan memiliki cerita unik di baliknya. Kami mendukung pengrajin lokal untuk terus berkarya dan melestarikan budaya melalui kerajinan tangan yang berkualitas tinggi.
            </p>
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <h4 class="text-2xl font-bold text-white">100%</h4>
                    <p class="text-amber-100">Lokal & Otentik</p>
                </div>
                <div>
                    <h4 class="text-2xl font-bold text-white">50+</h4>
                    <p class="text-amber-100">Pengrajin Ahli</p>
                </div>
            </div>
        </div>
        <div class="mt-12 lg:mt-0">
            <img src="https://images.unsplash.com/photo-1459749411177-042180ce673c?auto=format&fit=crop&q=80&w=400" alt="Artisan" class="w-64 h-64 object-cover rounded-3xl border-4 border-amber-500/50 shadow-2xl transform rotate-3">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Testimonials Section -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-3xl font-bold text-gray-900">Apa Kata Mereka?</h2>
            <p class="text-gray-500 mt-2">Kepuasan pelanggan adalah prioritas utama kami.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100 relative">
                <i class="fa-solid fa-quote-left text-4xl text-amber-200 absolute top-8 left-8"></i>
                <div class="relative z-10">
                    <p class="text-gray-600 italic mb-8">"Kualitas produknya luar biasa! Tas rajut yang saya beli sangat detail dan kuat. Benar-benar kerajinan tangan yang dibuat dengan hati."</p>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center font-bold text-amber-600">S</div>
                        <div>
                            <h4 class="font-bold text-gray-900">Sarah Wijaya</h4>
                            <p class="text-xs text-gray-400">Pelanggan Setia</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100 relative">
                <i class="fa-solid fa-quote-left text-4xl text-amber-200 absolute top-8 left-8"></i>
                <div class="relative z-10">
                    <p class="text-gray-600 italic mb-8">"Lilin aromaterapinya sangat wangi dan menenangkan. Packaging-nya juga sangat aman dan rapi. Sangat cocok untuk hadiah!"</p>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center font-bold text-green-600">A</div>
                        <div>
                            <h4 class="font-bold text-gray-900">Andini Putri</h4>
                            <p class="text-xs text-gray-400">Pengusaha Muda</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 p-8 rounded-[2.5rem] border border-gray-100 relative">
                <i class="fa-solid fa-quote-left text-4xl text-amber-200 absolute top-8 left-8"></i>
                <div class="relative z-10">
                    <p class="text-gray-600 italic mb-8">"Bangga bisa mendukung pengrajin lokal. Produk-produk di sini unik dan tidak pasaran. Pengirimannya juga sangat cepat."</p>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center font-bold text-blue-600">R</div>
                        <div>
                            <h4 class="font-bold text-gray-900">Rizky Ramadhan</h4>
                            <p class="text-xs text-gray-400">Kolektor Seni</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="py-24 bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-12 lg:p-16 rounded-[3rem] shadow-xl shadow-gray-200 border border-gray-100 text-center">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Bergabung dengan Komunitas Kami</h2>
            <p class="text-gray-500 mb-10 max-w-lg mx-auto">Dapatkan informasi terbaru mengenai produk baru, promo eksklusif, dan cerita di balik setiap kerajinan tangan kami.</p>
            <form class="flex flex-col sm:flex-row gap-4 max-w-md mx-auto">
                <input type="email" placeholder="Masukkan email Anda" class="flex-1 px-6 py-4 rounded-2xl bg-gray-50 border border-gray-100 focus:ring-2 focus:ring-amber-500 outline-none transition">
                <button type="button" class="bg-amber-600 text-white px-8 py-4 rounded-2xl font-bold hover:bg-amber-700 transition shadow-lg shadow-amber-200">
                    Berlangganan
                </button>
            </form>
            <p class="text-xs text-gray-400 mt-6 italic">Kami menghargai privasi Anda. Tidak ada spam, hanya cinta.</p>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
