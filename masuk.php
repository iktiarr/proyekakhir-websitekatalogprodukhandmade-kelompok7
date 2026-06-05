<?php
/**
 * Halaman Masuk (Login)
 * Berfungsi untuk autentikasi kredensial email & kata sandi pengguna
 * untuk mendapatkan akses ke fitur transaksi dan administrasi.
 */
$awalan = "";
include 'koneksi.php';

$galat = '';
$sukses = '';

if (isset($_GET['registrasi']) && $_GET['registrasi'] === 'sukses') {
    $sukses = "Pendaftaran berhasil! Silakan masuk.";
}

if (isset($_POST['masuk'])) {
    $email = $_POST['email'];
    $kata_sandi = $_POST['password'];

    $kueri = kueri("SELECT * FROM pengguna WHERE email = ?", [$email]);
    if ($kueri && mysqli_num_rows($kueri) > 0) {
        $data_pengguna = mysqli_fetch_assoc($kueri);
        
        if (password_verify($kata_sandi, $data_pengguna['password'])) {
            $_SESSION['user_id'] = $data_pengguna['id'];
            $_SESSION['nama'] = $data_pengguna['nama'];
            $_SESSION['role'] = $data_pengguna['role'];

            header("Location: " . ($data_pengguna['role'] === 'admin' ? "admin/index.php" : "index.php"));
            exit();
        } else {
            $galat = "Password salah!";
        }
    } else {
        $galat = "Email tidak ditemukan!";
    }
}
?>

<?php include 'bagian/atas.php'; ?>

<!-- Panel Form Login Pengguna -->
<div class="min-h-[75vh] bg-slate-50 dark:bg-slate-950 flex items-center justify-center py-10 px-4">
    <div class="max-w-md w-full bg-white dark:bg-slate-900 p-6 sm:p-8 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
        
        <!-- Header Panel Form -->
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 tracking-tight">Selamat Datang</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">Masuk untuk menjelajahi koleksi HandMadura</p>
        </div>

        <!-- Notifikasi Galat/Error -->
        <?php if ($galat): ?>
            <div class="bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl mb-4.5 text-xs border border-red-200 dark:border-red-900/30 flex items-center gap-2.5">
                <i class="fa-solid fa-circle-exclamation text-sm flex-shrink-0"></i> 
                <span class="font-semibold"><?= $galat; ?></span>
            </div>
        <?php endif; ?>

        <!-- Notifikasi Registrasi Sukses -->
        <?php if ($sukses): ?>
            <div class="bg-lime-50 dark:bg-lime-950/20 text-lime-800 dark:text-lime-400 px-4 py-3 rounded-xl mb-4.5 text-xs border border-lime-200 dark:border-lime-900/30 flex items-center gap-2.5">
                <i class="fa-solid fa-circle-check text-sm flex-shrink-0 text-lime-600"></i> 
                <span class="font-semibold"><?= $sukses; ?></span>
            </div>
        <?php endif; ?>

        <!-- Form Autentikasi -->
        <form action="" method="POST" class="space-y-4.5">
            <div>
                <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wider">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-3 bg-white rounded-xl border border-slate-200 outline-none text-sm text-slate-800 placeholder-slate-400" placeholder="nama@email.com">
            </div>
            
            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 uppercase tracking-wider">Password</label>
                    <a href="#" class="text-[11px] font-bold text-slate-400 dark:text-slate-500 hover:text-lime-600 dark:hover:text-lime-400">Lupa Password?</a>
                </div>
                <div class="relative">
                    <input type="password" id="password" name="password" required class="w-full pl-4 pr-11 py-3 bg-white rounded-xl border border-slate-200 outline-none text-sm text-slate-800 placeholder-slate-400" placeholder="••••••••">
                    <button type="button" onclick="tampilkanSandi('password', 'ikon-sandi')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-lime-600 cursor-pointer border-none bg-transparent">
                        <i id="ikon-sandi" class="fa-solid fa-eye-slash text-sm"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" name="masuk" class="w-full bg-lime-600 text-white py-3 rounded-xl font-bold hover:bg-lime-700 text-sm mt-2 cursor-pointer border-none">
                Masuk Ke Akun
            </button>
        </form>

        <!-- Tautan Navigasi ke Registrasi -->
        <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-800 text-center">
            <p class="text-slate-500 dark:text-slate-400 text-xs">
                Belum punya akun HandMadura? 
                <a href="daftar.php" class="text-lime-600 dark:text-lime-400 font-bold hover:text-lime-700 dark:hover:text-lime-300 hover:underline ml-1">
                    Daftar Sekarang
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Script Pembantu Tampilkan/Sembunyikan Sandi -->
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
