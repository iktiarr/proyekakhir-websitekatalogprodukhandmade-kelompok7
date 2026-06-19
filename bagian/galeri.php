<?php
// bagian/galeri.php: Komponen galeri seni, menampilkan portofolio gambar produk/karya berdasarkan kabupaten asal di Madura.

$daftar_daerah = ['Bangkalan', 'Sampang', 'Pamekasan', 'Sumenep'];

$daftar_galeri = [];
$hasil_produk = kueri("
    SELECT p.gambar, d.nama_daerah 
    FROM produk p 
    JOIN daerah d ON p.id_daerah = d.id 
    WHERE p.gambar IS NOT NULL AND p.gambar != '' AND p.stok > 0
");

if ($hasil_produk && mysqli_num_rows($hasil_produk) > 0) {
    while ($baris = mysqli_fetch_assoc($hasil_produk)) {
        $daftar_galeri[] = [
            'daerah' => $baris['nama_daerah'],
            'gambar' => $baris['gambar']
        ];
    }
}

$daerah_bawaan = 'Bangkalan';
?>
<section id="galeri" class="py-16 bg-white dark:bg-slate-900">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Header -->
    <div class="text-center mb-10">
      <h2 class="text-3xl sm:text-4xl font-bold text-slate-800 dark:text-slate-100">
        Galeri <span class="text-lime-600">Madura</span>
      </h2>
      <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm sm:text-base max-w-xl mx-auto leading-relaxed">
        Berikut adalah beberapa karya yang telah dibuat oleh pengrajin kami.
      </p>
    </div>

    <!-- Filter Buttons -->
    <div class="flex flex-wrap items-center justify-center gap-2 mb-10 max-w-2xl mx-auto">
      <?php foreach ($daftar_daerah as $nama_daerah): ?>
      <button onclick="saringDaerah('<?= $nama_daerah; ?>')" id="btn-<?= $nama_daerah; ?>" class="tombol-saring-daerah px-4 py-2 rounded-xl font-bold text-xs sm:text-sm border cursor-pointer <?= $nama_daerah === $daerah_bawaan ? 'bg-lime-600 text-white border-lime-600' : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-800'; ?>">
        <?= $nama_daerah; ?>
      </button>
      <?php endforeach; ?>
    </div>

    <!-- Gallery Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6 max-w-6xl mx-auto items-stretch" id="grid-galeri">
      
      <?php foreach ($daftar_galeri as $item): ?>
      <div class="item-galeri relative aspect-[4/3] rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-800" 
           data-daerah="<?= $item['daerah']; ?>">
        <img 
          src="<?= dapatkan_jalur_gambar($item['gambar']); ?>" 
          alt="Karya Seni Madura" 
          class="w-full h-full object-cover select-none pointer-events-none"
        >
      </div>
      <?php endforeach; ?>
      
    </div>

  </div>
</section>

<script>
  // Menyaring dan menampilkan item galeri seni sesuai dengan kabupaten asal Madura yang dipilih
  function saringDaerah(daerah) {
      const tombol = document.querySelectorAll('.tombol-saring-daerah');
      tombol.forEach(btn => {
          btn.className = "tombol-saring-daerah px-4 py-2 rounded-xl font-bold text-xs sm:text-sm border cursor-pointer bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-800";
      });

      const tombolAktif = document.getElementById(`btn-${daerah}`);
      if (tombolAktif) {
          tombolAktif.className = "tombol-saring-daerah px-4 py-2 rounded-xl font-bold text-xs sm:text-sm border cursor-pointer bg-lime-600 text-white border-lime-600";
      }

      const itemGaleri = document.querySelectorAll('.item-galeri');
      itemGaleri.forEach(item => {
          const itemDaerah = item.getAttribute('data-daerah');
          if (itemDaerah === daerah) {
              item.style.display = 'block';
          } else {
              item.style.display = 'none';
          }
      });
  }

  document.addEventListener('DOMContentLoaded', () => {
      saringDaerah('<?= $daerah_bawaan; ?>');
  });
</script>
