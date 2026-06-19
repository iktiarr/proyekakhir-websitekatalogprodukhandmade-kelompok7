</main>
    
    <!-- Bagian Footer Aplikasi -->
    <footer class="bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 pt-10 pb-8 mt-auto">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12">
          
          <!-- Identitas HandMadura -->
          <div class="space-y-4">
            <?php 
            $kaki_adalah_admin = isset($_SESSION['admin']['role']) && $_SESSION['admin']['role'] === 'admin';
            if (!isset($awalan)) {
                $awalan = '';
            }
            ?>
            <a href="<?= $kaki_adalah_admin ? $awalan . 'admin/index.php' : $awalan . 'index.php'; ?>" class="inline-block text-2xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">
                Hand<span class="text-lime-600">Madura.</span>
            </a>
            <p class="text-slate-500 dark:text-slate-400 text-sm sm:text-base max-w-sm leading-relaxed">
              Katalog produk kerajinan tangan autentik Madura, unik, dan berkualitas tinggi yang dikurasi langsung dari para maestro pengrajin lokal pilihan.
            </p>
          </div>
          
          <!-- Kontak Hubung Resmi -->
          <div class="md:justify-self-end">
            <h3 class="font-bold text-slate-800 dark:text-slate-200 text-sm sm:text-base mb-4 tracking-wider uppercase">Hubungi Kami</h3>
            <ul class="space-y-3.5 text-slate-500 dark:text-slate-400 text-sm">
              <li>
                <a href="mailto:info@handmadura.com" target="_blank" class="flex items-center hover:text-lime-600 dark:hover:text-lime-400 w-fit cursor-pointer group">
                  <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mr-3 text-slate-400 dark:text-slate-500 group-hover:bg-lime-50 dark:group-hover:bg-lime-950/30 group-hover:text-lime-600 dark:group-hover:text-lime-400">
                    <i class="fa-solid fa-envelope"></i>
                  </div>
                  info@handmadura.com
                </a>
              </li>
              <li>
                <a href="https://wa.me/6281938041535" target="_blank" class="flex items-center hover:text-lime-600 dark:hover:text-lime-400 w-fit cursor-pointer group">
                  <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mr-3 text-slate-400 dark:text-slate-500 group-hover:bg-lime-50 dark:group-hover:bg-lime-950/30 group-hover:text-lime-600 dark:group-hover:text-lime-400">
                    <i class="fa-brands fa-whatsapp text-lg"></i>
                  </div>
                  +62 819 3804 1535
                </a>
              </li>
            </ul>
          </div>
          
        </div>
        
        <!-- Hak Cipta & Footer Bawah -->
        <div class="border-t border-slate-200 dark:border-slate-800 mt-10 pt-6 text-center sm:flex sm:justify-between sm:items-center">
          <p class="text-slate-400 dark:text-slate-500 text-xs sm:text-sm mb-2 sm:mb-0">
            &copy; <?= date('Y'); ?> HandMadura. Hak Cipta Dilindungi.
          </p>
          <p class="text-slate-400 dark:text-slate-500 text-xs sm:text-sm flex items-center justify-center sm:justify-end font-semibold">
            Melestarikan Budaya Nusantara.
          </p>
        </div>
        
      </div>
    </footer>

    <!-- Script Global (Tema Gelang/Terang & Menu Responsif Seluler) -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const elemenTombolTema = document.getElementById('tombol-tema');
        const elemenIkonTombolTema = document.getElementById('ikon-tombol-tema');

        // Mengubah Ikon tema berdasarkan status kelas dark mode
        function perbaruiIkon() {
            if (document.documentElement.classList.contains('dark')) {
                if (elemenIkonTombolTema) {
                    elemenIkonTombolTema.classList.replace('fa-moon', 'fa-sun');
                }
            } else {
                if (elemenIkonTombolTema) {
                    elemenIkonTombolTema.classList.replace('fa-sun', 'fa-moon');
                }
            }
        }

        perbaruiIkon();

        // Mengatur pergantian tema dengan instan tanpa durasi animasi berlebih
        if (elemenTombolTema) {
            elemenTombolTema.addEventListener('click', () => {
                if (document.documentElement.classList.contains('dark')) {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                } else {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                }
                perbaruiIkon();
            });
        }

        // Mengatur buka/tutup Menu Seluler Dropdown (Burger Menu) secara responsif
        const tombolMenuSeluler = document.getElementById('tombol-menu-seluler');
        const menuSeluler = document.getElementById('menu-seluler');
        const ikonMenuSeluler = document.getElementById('ikon-menu-seluler');

        if (tombolMenuSeluler && menuSeluler && ikonMenuSeluler) {
            tombolMenuSeluler.addEventListener('click', () => {
                const apakahTerbuka = !menuSeluler.classList.contains('hidden');
                
                if (apakahTerbuka) {
                    menuSeluler.classList.add('hidden');
                    ikonMenuSeluler.classList.replace('fa-xmark', 'fa-bars');
                    tombolMenuSeluler.setAttribute('aria-expanded', 'false');
                } else {
                    menuSeluler.classList.remove('hidden');
                    ikonMenuSeluler.classList.replace('fa-bars', 'fa-xmark');
                    tombolMenuSeluler.setAttribute('aria-expanded', 'true');
                }
            });
        }
    });
    </script>
  </body>
</html>
