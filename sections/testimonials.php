<!-- Testimonials Section -->
<section class="py-12 sm:py-16 lg:py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 sm:mb-10">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-900 mb-2">Apa Kata Mereka?</h2>
            <p class="text-gray-600 text-sm sm:text-base max-w-2xl mx-auto mb-4">Kepuasan pelanggan adalah prioritas utama kami.</p>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="add_testimonial.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-lime-600 to-lime-500 text-white px-5 py-2 rounded-lg font-bold text-sm hover:from-lime-700 hover:to-lime-600 transition shadow-lg">
                    <i class="fa-solid fa-plus"></i> Bagikan Ulasan
                </a>
            <?php else: ?>
                <a href="masuk.php" class="inline-flex items-center gap-2 bg-gradient-to-r from-lime-600 to-lime-500 text-white px-5 py-2 rounded-lg font-bold text-sm hover:from-lime-700 hover:to-lime-600 transition shadow-lg">
                    <i class="fa-solid fa-login"></i> Login
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
        ");

        if (mysqli_num_rows($testimonial_query) > 0):
        ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                <?php while($testimonial = mysqli_fetch_assoc($testimonial_query)): ?>
                    <div class="bg-gradient-to-br from-lime-50 to-lime-100/50 p-5 sm:p-6 rounded-xl border-2 border-lime-200 hover:shadow-lg hover:border-lime-300 transition duration-300 relative">
                        <div class="flex gap-0.5 mb-3">
                            <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                <i class="fa-solid fa-star text-lime-500 text-xs"></i>
                            <?php endfor; ?>
                        </div>
                        <i class="fa-solid fa-quote-left text-3xl text-lime-200 absolute -top-1 -left-1 opacity-50"></i>
                        <div class="relative z-10">
                            <p class="text-gray-700 italic mb-4 leading-relaxed text-xs sm:text-sm">"<?= substr($testimonial['isi_ulasan'], 0, 120); ?><?= strlen($testimonial['isi_ulasan']) > 120 ? '...' : ''; ?>"</p>
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-lime-400 to-lime-600 rounded-full flex items-center justify-center font-bold text-white text-sm flex-shrink-0">
                                    <?= strtoupper(substr($testimonial['nama'], 0, 1)); ?>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-xs sm:text-sm"><?= $testimonial['nama']; ?></h4>
                                    <p class="text-xs text-gray-600"><?= $testimonial['pekerjaan'] ?: 'Pelanggan'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-8 bg-lime-50 rounded-xl border-2 border-dashed border-lime-200">
                <i class="fa-solid fa-comments text-3xl text-lime-200 mb-2 block"></i>
                <p class="text-gray-600 font-medium text-sm mb-4">Belum ada ulasan dari pelanggan kami.</p>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="add_testimonial.php" class="inline-flex items-center gap-2 bg-lime-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-lime-700 transition">
                        <i class="fa-solid fa-pen"></i> Tulis Ulasan Pertama
                    </a>
                <?php else: ?>
                    <a href="masuk.php" class="inline-flex items-center gap-2 bg-lime-600 text-white px-4 py-2 rounded-lg font-bold text-sm hover:bg-lime-700 transition">
                        <i class="fa-solid fa-login"></i> Login untuk Menulis Ulasan
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
