<!-- Bagian Utama / Hero Section -->
<section class="relative py-12 lg:py-20 bg-white dark:bg-slate-900 overflow-hidden">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-12 items-center">
      
      <!-- Kolom Teks Promosi & Deskripsi -->
      <div class="space-y-5 sm:space-y-6 order-2 lg:order-1 text-center lg:text-left">
        <div>
          <h1 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-slate-800 dark:text-slate-100 leading-[1.15] tracking-tight">
            Sentuhan Kerajinan
            <span class="relative inline-block text-lime-600">
              Autentik
              <span class="absolute bottom-1.5 w-full h-2.5 bg-lime-200/40 dark:bg-lime-950/40 -z-10 -rotate-1 rounded-sm"></span>
            </span>
            <br class="hidden sm:block"> Asal Madura.
          </h1>
        </div>
        
        <p class="text-sm sm:text-base text-slate-650 dark:text-slate-400 leading-relaxed max-w-lg mx-auto lg:mx-0">
          Jelajahi keindahan Batik Tulis Gentongan yang legendaris, ukiran Karduluk yang presisi, hingga anyaman pandan serat alam karya maestro pengrajin terbaik di empat kabupaten Madura.
        </p>
        
        <!-- Tombol CTA Navigasi -->
        <div class="flex flex-col sm:flex-row gap-3.5 pt-2 justify-center lg:justify-start">
          <a href="<?= $awalan; ?>halaman/katalog.php" class="inline-flex items-center justify-center bg-lime-600 text-white px-7 py-3 rounded-xl font-bold text-sm hover:bg-lime-700 border-none cursor-pointer">
            Lihat Katalog Madura
            <i class="fa-solid fa-arrow-right ml-1.5"></i>
          </a>
          <a href="#tentang" class="inline-flex items-center justify-center bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-7 py-3 rounded-xl font-bold text-sm hover:bg-slate-50 dark:hover:bg-slate-750 hover:text-lime-600 dark:hover:text-lime-400">
            Kisah Pengrajin
          </a>
        </div>
      </div>

      <!-- Kolom Foto Banner Unggulan -->
      <div class="order-1 lg:order-2 relative w-full h-[280px] sm:h-[360px] lg:h-[450px] rounded-2xl overflow-hidden group">
        <img 
          src="<?= $awalan; ?>uploads/hero.jpg" 
          alt="Batik Tulis Gentongan Madura" 
          class="w-full h-full object-cover"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/20 to-transparent"></div>
      </div>

    </div>
  </div>
</section>
