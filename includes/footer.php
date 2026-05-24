</main>
    
    <footer class="bg-white border-t border-slate-100 pt-8 pb-6 mt-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
          
          <div class="space-y-4">
            <?php 
            $footer_is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
            ?>
            <a href="<?= $footer_is_admin ? 'admin/index.php' : 'index.php'; ?>" class="inline-block text-2xl font-extrabold text-slate-800 tracking-tight">
                Hand<span class="text-lime-600">made.</span>
            </a>
            <p class="text-slate-500 text-sm sm:text-base max-w-sm leading-relaxed">
              Katalog produk kerajinan tangan terbaik, unik, dan berkualitas tinggi yang dikurasi khusus dari pengrajin lokal pilihan.
            </p>
          </div>
          
          <div class="md:justify-self-end">
            <h3 class="font-bold text-slate-800 text-sm sm:text-base mb-4 tracking-wider uppercase">Hubungi Kami</h3>
            <ul class="space-y-3 text-slate-500 text-sm sm:text-base">
              <li>
                <a href="mailto:info@handmade.com" target="_blank" class="flex items-center hover:text-lime-600 transition-colors duration-300 w-fit cursor-pointer group">
                  <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center mr-3 text-slate-400 group-hover:bg-lime-50 group-hover:text-lime-600 transition-colors">
                    <i class="fa-solid fa-envelope"></i>
                  </div>
                  info@handmade.com
                </a>
              </li>
              <li>
                <a href="https://wa.me/6281938041535" target="_blank" class="flex items-center hover:text-lime-600 transition-colors duration-300 w-fit cursor-pointer group">
                  <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center mr-3 text-slate-400 group-hover:bg-lime-50 group-hover:text-lime-600 transition-colors">
                    <i class="fa-brands fa-whatsapp text-lg"></i>
                  </div>
                  +62 819 3804 1535
                </a>
              </li>
            </ul>
          </div>
          
        </div>
        
        <div class="border-t border-slate-100 mt-10 pt-6 text-center sm:flex sm:justify-between sm:items-center">
          <p class="text-slate-400 text-xs sm:text-sm mb-2 sm:mb-0">
            &copy; <?= date('Y'); ?> Handmade Katalog.
          </p>
          <p class="text-slate-400 text-xs sm:text-sm flex items-center justify-center sm:justify-end">
            Dibuat oleh kami sendiri.
          </p>
        </div>
        
      </div>
    </footer>
  </body>
</html>