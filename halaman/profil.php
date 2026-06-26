<?php
/**
 * Halaman Profil Saya
 * Berfungsi untuk menampilkan data diri pengguna sesuai database,
 * memproses perubahan data profil dengan validasi di sisi server,
 * dan menyimpan pembaruan nama, email, nomor telepon, alamat, serta kata sandi.
 */
$awalan = "../";
include '../koneksi.php';

// Proteksi halaman: pastikan pengguna sudah masuk
if (!isset($_SESSION['user']['id'])) {
    header("Location: ../masuk.php");
    exit();
}

$id_pengguna = $_SESSION['user']['id'];
$galat = '';
$sukses = '';

// Ambil data profil pengguna terbaru dari database
$kueri_pengguna = kueri("SELECT * FROM pengguna WHERE id = ?", [$id_pengguna]);
$data_pengguna = $kueri_pengguna ? mysqli_fetch_assoc($kueri_pengguna) : null;

if (!$data_pengguna) {
    header("Location: ../keluar.php");
    exit();
}

// Proses pembaruan profil saat form dikirimkan
if (isset($_POST['simpan_profil'])) {
    $nama = trim($_POST['nama']);
    $email = trim($_POST['email']);
    $no_telp = trim($_POST['no_telp']);
    $alamat = trim($_POST['alamat']);
    $kata_sandi_baru = $_POST['password_baru'];
    $konfirmasi_sandi = $_POST['konfirmasi_password'];

    // Validasi server-side
    if (empty($nama) || empty($email) || empty($no_telp)) {
        $galat = "Nama Lengkap, Email, dan Nomor Telepon tidak boleh kosong!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $galat = "Format email tidak valid!";
    } elseif (strlen($no_telp) < 12) {
        $galat = "Nomor Telepon/WhatsApp minimal harus terdiri dari 12 digit!";
    } elseif (!preg_match("/^[0-9]+$/", $no_telp)) {
        $galat = "Nomor Telepon/WhatsApp hanya boleh berisi angka!";
    } else {
        // Periksa apakah email sudah digunakan oleh pengguna lain
        $periksa_email = kueri("SELECT id FROM pengguna WHERE email = ? AND id != ?", [$email, $id_pengguna]);
        if ($periksa_email && mysqli_num_rows($periksa_email) > 0) {
            $galat = "Email ini sudah digunakan oleh akun lain!";
        } else {
            // Tangani pengubahan kata sandi baru (opsional)
            $sandi_hash = null;
            $ganti_sandi = false;

            if (!empty($kata_sandi_baru)) {
                if ($kata_sandi_baru !== $konfirmasi_sandi) {
                    $galat = "Konfirmasi password baru tidak cocok!";
                } elseif (strlen($kata_sandi_baru) < 6) {
                    $galat = "Password baru minimal harus terdiri dari 6 karakter!";
                } else {
                    $sandi_hash = password_hash($kata_sandi_baru, PASSWORD_DEFAULT);
                    $ganti_sandi = true;
                }
            }

            // Jika tidak ada kesalahan validasi, jalankan proses penyimpanan ke database
            if (empty($galat)) {
                if ($ganti_sandi) {
                    $kueri_update = kueri("UPDATE pengguna SET nama = ?, email = ?, no_telp = ?, alamat = ?, password = ? WHERE id = ?", [$nama, $email, $no_telp, $alamat, $sandi_hash, $id_pengguna]);
                } else {
                    $kueri_update = kueri("UPDATE pengguna SET nama = ?, email = ?, no_telp = ?, alamat = ? WHERE id = ?", [$nama, $email, $no_telp, $alamat, $id_pengguna]);
                }
                
                if ($kueri_update) {
                    $sukses = "Profil Anda berhasil diperbarui!";
                    
                    // Perbarui sesi nama pengguna agar header langsung berubah
                    $_SESSION['user']['nama'] = $nama;
                    
                    // Log aktivitas pengguna
                    $keterangan_log = $ganti_sandi ? 'Memperbarui data profil dan kata sandi' : 'Memperbarui data profil diri';
                    kueri("INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan) VALUES (?, ?, 'pengguna', 'edit', ?)", [$id_pengguna, $nama, $keterangan_log]);
                    
                    // Muat ulang data terbaru dari database
                    $kueri_pengguna = kueri("SELECT * FROM pengguna WHERE id = ?", [$id_pengguna]);
                    $data_pengguna = $kueri_pengguna ? mysqli_fetch_assoc($kueri_pengguna) : null;
                } else {
                    $galat = "Terjadi kesalahan sistem saat memperbarui profil.";
                }
            }
        }
    }
}
?>

<?php include '../bagian/atas.php'; ?>

<!-- Kontainer Form Pengaturan Profil -->
<div class="py-12 bg-slate-50 dark:bg-slate-950 min-h-[80vh]">
    <div class="max-w-2xl mx-auto px-4">
        
        <!-- Header Halaman -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Pengaturan Profil</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1.5 text-sm">Kelola informasi data diri dan kata sandi akun Anda secara aman.</p>
        </div>

        <!-- Notifikasi Sukses -->
        <?php if ($sukses): ?>
            <div class="bg-lime-50 dark:bg-lime-950/20 text-lime-800 dark:text-lime-400 p-4.5 rounded-xl mb-6 border border-lime-200 dark:border-lime-900/30 flex items-center">
                <i class="fa-solid fa-circle-check text-xl mr-3 flex-shrink-0 text-lime-600"></i>
                <span class="font-semibold text-sm"><?= $sukses; ?></span>
            </div>
        <?php endif; ?>

        <!-- Notifikasi Galat/Error -->
        <?php if ($galat): ?>
            <div class="bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 p-4.5 rounded-xl mb-6 border border-red-200 dark:border-red-900/30 flex items-center">
                <i class="fa-solid fa-circle-exclamation text-xl mr-3 flex-shrink-0"></i>
                <span class="font-semibold text-sm"><?= $galat; ?></span>
            </div>
        <?php endif; ?>

        <!-- Form Profil -->
        <form action="" method="POST" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 p-6 sm:p-8 space-y-6">
            
            <!-- Rincian Data Diri Utama -->
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-800 dark:text-slate-200 border-b border-slate-100 dark:border-slate-800 pb-2">Informasi Pribadi</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Nama Lengkap</label>
                        <input type="text" name="nama" value="<?= htmlspecialchars($data_pengguna['nama']); ?>" required class="w-full px-4 py-2.5 bg-white rounded-xl border border-slate-200 outline-none text-sm text-slate-800 placeholder-slate-400">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Alamat Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($data_pengguna['email']); ?>" required class="w-full px-4 py-2.5 bg-white rounded-xl border border-slate-200 outline-none text-sm text-slate-800 placeholder-slate-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Nomor Telepon / WhatsApp</label>
                    <input type="tel" name="no_telp" value="<?= htmlspecialchars($data_pengguna['no_telp'] ?? ''); ?>" required minlength="12" pattern="[0-9]{12,}" title="Nomor telepon minimal harus terdiri dari 12 digit angka" class="w-full px-4 py-2.5 bg-white rounded-xl border border-slate-200 outline-none text-sm text-slate-800 placeholder-slate-400" placeholder="Contoh: 081234567890" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Alamat Pengiriman</label>
                    <textarea name="alamat" rows="3" class="w-full px-4 py-2.5 bg-white rounded-xl border border-slate-200 outline-none text-sm text-slate-800 placeholder-slate-400 resize-none" placeholder="Masukkan alamat lengkap Anda"><?= htmlspecialchars($data_pengguna['alamat'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Perubahan Kata Sandi (Opsional) -->
            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                <div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-slate-200">Ubah Password</h3>
                    <p class="text-slate-400 dark:text-slate-500 text-[11px] mt-0.5 font-medium">Kosongkan kolom di bawah jika Anda tidak ingin mengubah kata sandi saat ini.</p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Password Baru</label>
                        <div class="relative">
                            <input type="password" id="password_baru" name="password_baru" class="w-full pl-4 pr-11 py-2.5 bg-white rounded-xl border border-slate-200 outline-none text-sm text-slate-800 placeholder-slate-400" placeholder="••••••••">
                            <button type="button" onclick="tampilkanSandi('password_baru', 'ikon-sandi-baru')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-lime-600 cursor-pointer border-none bg-transparent">
                                <i id="ikon-sandi-baru" class="fa-solid fa-eye-slash text-sm"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Konfirmasi Password</label>
                        <div class="relative">
                            <input type="password" id="konfirmasi_password" name="konfirmasi_password" class="w-full pl-4 pr-11 py-2.5 bg-white rounded-xl border border-slate-200 outline-none text-sm text-slate-800 placeholder-slate-400" placeholder="••••••••">
                            <button type="button" onclick="tampilkanSandi('konfirmasi_password', 'ikon-konfirmasi-sandi')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-lime-600 cursor-pointer border-none bg-transparent">
                                <i id="ikon-konfirmasi-sandi" class="fa-solid fa-eye-slash text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="pt-4 flex justify-end">
                <button type="submit" name="simpan_profil" class="bg-lime-600 text-white px-8 py-3 rounded-xl font-bold text-sm hover:bg-lime-700 cursor-pointer border-none flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk text-xs"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Script Tampilkan/Sembunyikan Kata Sandi -->
<script>
    // Menampilkan atau menyembunyikan teks kata sandi (password visibility toggle)
    function tampilkanSandi(sandiId, ikonId) {
        const masukan = document.getElementById(sandiId);
        const ikon = document.getElementById(ikonId);
        if (masukan.type === 'password') {
            masukan.type = 'text';
            ikon.classList.replace('fa-eye-slash', 'fa-eye');
        } else {
            masukan.type = 'password';
            ikon.classList.replace('fa-eye', 'fa-eye-slash');
        }
    }
</script>

<?php include '../bagian/bawah.php'; ?>
