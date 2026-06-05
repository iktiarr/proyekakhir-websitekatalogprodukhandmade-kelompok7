<!-- Bagian Testimonial Pengguna / Kolektor -->
<section class="py-16 bg-white dark:bg-slate-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Bagian Testimonial -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-slate-800 dark:text-slate-100 mb-1">Apa Kata Kolektor?</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm max-w-xl mx-auto mb-5 leading-relaxed">Kepuasan Anda mengoleksi mahakarya kerajinan tradisional Madura adalah kebanggaan terbesar kami.</p>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Tombol Berbagi Ulasan (Untuk User Terkoneksi) -->
                <a href="<?= $awalan; ?>halaman/tambah_ulasan.php" class="inline-flex items-center gap-1.5 bg-lime-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-lime-700">
                    <i class="fa-solid fa-pen-to-square"></i> Bagikan Ulasan Anda
                </a>
            <?php else: ?>
                <!-- Tombol Login untuk Menulis Ulasan (Untuk Pengunjung) -->
                <a href="<?= $awalan; ?>masuk.php" class="inline-flex items-center gap-1.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-lime-600 dark:hover:text-lime-400">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Login untuk Mengulas
                </a>
            <?php endif; ?>
        </div>

        <?php
        // Query untuk memuat data testimonial yang telah disetujui admin
        $kueri_testimoni = mysqli_query($koneksi, "
            SELECT t.*, p.nama 
            FROM testimonial t 
            JOIN pengguna p ON t.id_pengguna = p.id 
            WHERE t.status = 'approved' 
            ORDER BY t.tanggal_dibuat DESC
            LIMIT 6
        ");

        if (mysqli_num_rows($kueri_testimoni) > 0):
        ?>
            <!-- Grid Card Testimoni -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while($data_testimoni = mysqli_fetch_assoc($kueri_testimoni)): ?>
                    
                    <div class="bg-slate-50 dark:bg-slate-950 p-5 sm:p-6 rounded-2xl border border-slate-200 dark:border-slate-800 flex flex-col relative">
                        <div class="relative z-10 flex flex-col flex-grow">
                            <!-- Rating Bintang -->
                            <div class="flex gap-0.5 mb-3">
                                <?php for ($i = 0; $i < $data_testimoni['rating']; $i++): ?>
                                    <i class="fa-solid fa-star text-lime-500 text-xs"></i>
                                <?php endfor; ?>
                            </div>
                            
                            <!-- Isi Pesan Ulasan -->
                            <p class="text-slate-600 dark:text-slate-350 italic mb-5 leading-relaxed text-xs sm:text-sm flex-grow">
                                "<?= substr($data_testimoni['isi_ulasan'], 0, 120); ?><?= strlen($data_testimoni['isi_ulasan']) > 120 ? '...' : ''; ?>"
                            </p>
                            
                            <!-- Keterangan Pemberi Ulasan -->
                            <div class="flex items-center gap-3 pt-3.5 border-t border-slate-200 dark:border-slate-800">
                                <div class="w-10 h-10 bg-lime-100 dark:bg-lime-950/40 rounded-full flex items-center justify-center font-bold text-lime-700 dark:text-lime-400 text-sm flex-shrink-0">
                                    <?= strtoupper(substr($data_testimoni['nama'], 0, 1)); ?>
                                </div>
                                
                                <div>
                                    <h4 class="font-bold text-slate-800 dark:text-slate-200 text-xs sm:text-sm">
                                        <?= $data_testimoni['nama']; ?>
                                    </h4>
                                    <p class="text-[10px] sm:text-xs text-slate-400 dark:text-slate-500">
                                        <?= $data_testimoni['pekerjaan'] ?: 'Kolektor Seni Kerajinan'; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                <?php endwhile; ?>
            </div>
            
        <?php else: ?>
            
            <!-- Tampilan Default Jika Belum Ada Ulasan -->
            <div class="max-w-2xl mx-auto text-center py-12 bg-white dark:bg-slate-900 rounded-2xl border border-dashed border-slate-200 dark:border-slate-800">
                <div class="w-12 h-12 bg-slate-50 dark:bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 dark:text-slate-600 text-lg">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 mb-1">Belum ada ulasan</h3>
                <p class="text-slate-500 dark:text-slate-400 text-xs mb-4 px-4">Jadilah yang pertama untuk membagikan pengalaman Anda mengoleksi kerajinan khas Madura bersama kami.</p>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= $awalan; ?>halaman/tambah_ulasan.php" class="inline-flex items-center gap-1.5 bg-lime-600 text-white px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-lime-700">
                        <i class="fa-solid fa-pen"></i> Tulis Ulasan Pertama
                    </a>
                <?php else: ?>
                    <a href="<?= $awalan; ?>masuk.php" class="inline-flex items-center gap-1.5 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 px-5 py-2.5 rounded-xl font-bold text-xs hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-lime-600 dark:hover:text-lime-400">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Login untuk Menulis Ulasan
                    </a>
                <?php endif; ?>
            </div>
            
        <?php endif; ?>
        
    </div>
</section>
