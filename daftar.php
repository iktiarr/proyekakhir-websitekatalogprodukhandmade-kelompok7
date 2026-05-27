<?php
$awalan = "";
include 'koneksi.php';

$galat = '';

if (isset($_POST['daftar'])) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $no_telp = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
    $kata_sandi = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi_password'];

    if ($kata_sandi !== $konfirmasi) {
        $galat = "Konfirmasi password tidak cocok!";
    } else {
        $periksa = mysqli_query($koneksi, "SELECT id FROM pengguna WHERE email = '$email'");
        if (mysqli_num_rows($periksa) > 0) {
            $galat = "Email sudah terdaftar!";
        } else {
            $sandi_hash = password_hash($kata_sandi, PASSWORD_DEFAULT);
            $kueri = "INSERT INTO pengguna (nama, email, password, role, alamat, no_telp) VALUES ('$nama', '$email', '$sandi_hash', 'user', '$alamat', '$no_telp')";
            if (mysqli_query($koneksi, $kueri)) {
                $id_baru = mysqli_insert_id($koneksi);
                $nama_escaped = mysqli_real_escape_string($koneksi, $nama);
                mysqli_query($koneksi, "INSERT INTO log_aktivitas (id_pengguna, nama_pengguna, tipe_aktivitas, aksi, keterangan) VALUES ($id_baru, '$nama_escaped', 'pengguna', 'daftar', 'Mendaftar sebagai pengguna baru')");
                header("Location: masuk.php?registrasi=sukses");
                exit();
            } else {
                $galat = "Pendaftaran gagal!";
            }
        }
    }
}
?>

<?php include 'bagian/atas.php'; ?>

<div class="min-h-[75vh] bg-slate-50 dark:bg-slate-950 flex items-center justify-center py-10 px-4 transition-colors duration-300">
    <div class="max-w-md w-full bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-3xl border border-slate-200/60 dark:border-slate-800 shadow-sm">
        
        <div class="text-center mb-6">
            <h1 class="text-2xl font-extrabold text-slate-855 dark:text-slate-100 tracking-tight">Buat Akun Baru</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">Daftar untuk mulai mengoleksi kerajinan HandMadura</p>
        </div>

        <?php if ($galat): ?>
            <div class="bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl mb-4.5 text-xs border border-red-150 dark:border-red-900/30 flex items-center gap-2.5">
                <i class="fa-solid fa-circle-exclamation text-sm flex-shrink-0"></i> 
                <span class="font-semibold"><?= $galat; ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-405 mb-1.5 uppercase tracking-wider">Nama Lengkap</label>
                <input type="text" name="nama" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-250 dark:border-slate-800 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-sm text-slate-855 dark:text-slate-200 placeholder-slate-400" placeholder="Masukkan nama lengkap Anda">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-405 mb-1.5 uppercase tracking-wider">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-250 dark:border-slate-800 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-sm text-slate-855 dark:text-slate-200 placeholder-slate-400" placeholder="nama@email.com">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-405 mb-1.5 uppercase tracking-wider">Nomor Telepon</label>
                <input type="tel" name="no_telp" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-250 dark:border-slate-800 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-sm text-slate-855 dark:text-slate-200 placeholder-slate-400" placeholder="Contoh: 08123456789">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-405 mb-1.5 uppercase tracking-wider">Alamat Lengkap</label>
                <textarea name="alamat" required rows="3" class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-250 dark:border-slate-800 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-sm text-slate-855 dark:text-slate-200 placeholder-slate-400 resize-none" placeholder="Masukkan alamat lengkap pengiriman Anda..."></textarea>
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-405 mb-1.5 uppercase tracking-wider">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required class="w-full pl-4 pr-11 py-3 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-250 dark:border-slate-800 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-sm text-slate-855 dark:text-slate-200 placeholder-slate-400" placeholder="••••••••">
                    <button type="button" onclick="tampilkanSandi('password', 'ikon-sandi')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-lime-600 transition-colors cursor-pointer border-none bg-transparent">
                        <i id="ikon-sandi" class="fa-solid fa-eye-slash text-sm"></i>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-405 mb-1.5 uppercase tracking-wider">Konfirmasi Password</label>
                <div class="relative">
                    <input type="password" id="konfirmasi_password" name="konfirmasi_password" required class="w-full pl-4 pr-11 py-3 bg-slate-50 dark:bg-slate-950 rounded-xl border border-slate-250 dark:border-slate-800 focus:bg-white dark:focus:bg-slate-900 focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-sm text-slate-855 dark:text-slate-200 placeholder-slate-400" placeholder="••••••••">
                    <button type="button" onclick="tampilkanSandi('konfirmasi_password', 'ikon-konfirmasi-sandi')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-lime-600 transition-colors cursor-pointer border-none bg-transparent">
                        <i id="ikon-konfirmasi-sandi" class="fa-solid fa-eye-slash text-sm"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" name="daftar" class="w-full bg-lime-600 text-white py-3 rounded-xl font-bold hover:bg-lime-700 hover:shadow-lg hover:shadow-lime-200/40 transition-all text-sm mt-2 cursor-pointer border-none">
                Daftar Sekarang
            </button>
        </form>

        <div class="mt-6 pt-4 border-t border-slate-150 dark:border-slate-800 text-center">
            <p class="text-slate-500 dark:text-slate-400 text-xs">
                Sudah punya akun HandMadura? 
                <a href="masuk.php" class="text-lime-600 dark:text-lime-400 font-bold hover:text-lime-700 dark:hover:text-lime-300 hover:underline transition-colors ml-1">
                    Masuk di sini
                </a>
            </p>
        </div>
    </div>
</div>

<script>
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

<?php include 'bagian/bawah.php'; ?>