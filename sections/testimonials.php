<section class="py-10 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-6">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-800 mb-1">Apa Kata Mereka?</h2>
            <p class="text-slate-500 text-sm max-w-xl mx-auto mb-4">Kepuasan pelanggan adalah prioritas utama dan motivasi terbesar kami untuk terus berkarya.</p>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="add_testimonial.php" class="inline-flex items-center gap-1.5 bg-lime-600 text-white px-4 py-2 rounded-xl font-bold text-xs hover:bg-lime-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-lime-200/40 transition-all duration-300">
                    <i class="fa-solid fa-pen-to-square"></i> Bagikan Ulasan Anda
                </a>
            <?php else: ?>
                <a href="masuk.php" class="inline-flex items-center gap-1.5 bg-white text-slate-700 border border-slate-200 px-4 py-2 rounded-xl font-bold text-xs hover:bg-slate-50 hover:border-slate-300 hover:text-lime-600 transition-all duration-300">
                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Login untuk Mengulas
                </a>
            <?php endif; ?>
        </div>

        <?php
        $testimonial_query = mysqli_query($conn, "
            SELECT t.*, p.nama 
            FROM testimonial t 
            JOIN pengguna p ON t.id_pengguna = p.id 
            WHERE t.status = 'approved' 
            ORDER BY t.tanggal_dibuat DESC
            LIMIT 6
        ");

        if (mysqli_num_rows($testimonial_query) > 0):
        ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php while($testimonial = mysqli_fetch_assoc($testimonial_query)): ?>
                    
                    <div class="bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-lime-900/5 hover:-translate-y-1 transition-all duration-300 flex flex-col relative group">
                        <div class="relative z-10 flex flex-col flex-grow">
                            <div class="flex gap-0.5 mb-2.5">
                                <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                    <i class="fa-solid fa-star text-lime-500 text-xs"></i>
                                <?php endfor; ?>
                            </div>
                            
                            <p class="text-slate-600 italic mb-4 leading-relaxed text-xs sm:text-sm flex-grow">
                                "<?= substr($testimonial['isi_ulasan'], 0, 120); ?><?= strlen($testimonial['isi_ulasan']) > 120 ? '...' : ''; ?>"
                            </p>
                            
                            <div class="flex items-center gap-3 pt-3 border-t border-slate-200">
                                <div class="w-9 h-9 bg-lime-100 rounded-full flex items-center justify-center font-bold text-lime-700 text-sm flex-shrink-0">
                                    <?= strtoupper(substr($testimonial['nama'], 0, 1)); ?>
                                </div>
                                
                                <div>
                                    <h4 class="font-bold text-slate-800 text-xs sm:text-sm group-hover:text-lime-600 transition-colors duration-300">
                                        <?= $testimonial['nama']; ?>
                                    </h4>
                                    <p class="text-[10px] sm:text-xs text-slate-400">
                                        <?= $testimonial['pekerjaan'] ?: 'Pelanggan Setia'; ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                <?php endwhile; ?>
            </div>
            
        <?php else: ?>
            
            <div class="max-w-2xl mx-auto text-center py-10 bg-white rounded-2xl border border-dashed border-slate-200">
                <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-300 text-lg">
                    <i class="fa-solid fa-comments"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-1">Belum ada ulasan</h3>
                <p class="text-slate-500 text-xs mb-4 px-4">Jadilah yang pertama untuk membagikan pengalaman Anda berbelanja dengan kami.</p>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="add_testimonial.php" class="inline-flex items-center gap-1.5 bg-lime-600 text-white px-4 py-2 rounded-xl font-bold text-xs hover:bg-lime-700 transition">
                        <i class="fa-solid fa-pen"></i> Tulis Ulasan Pertama
                    </a>
                <?php else: ?>
                    <a href="masuk.php" class="inline-flex items-center gap-1.5 bg-white text-slate-700 border border-slate-200 px-4 py-2 rounded-xl font-bold text-xs hover:bg-slate-50 hover:text-lime-600 transition">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Login untuk Menulis Ulasan
                    </a>
                <?php endif; ?>
            </div>
            
        <?php endif; ?>
        
    </div>
</section>