<?php
include 'koneksi.php';

$error = '';
$success = '';

if (isset($_POST['daftar'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    if ($password !== $konfirmasi_password) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        $checkEmail = mysqli_query($conn, "SELECT * FROM pengguna WHERE email = '$email'");
        if (mysqli_num_rows($checkEmail) > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO pengguna (nama, email, password, role) VALUES ('$nama', '$email', '$hashed_password', 'user')";
            if (mysqli_query($conn, $query)) {
                $success = "Pendaftaran berhasil! Silakan masuk.";
            } else {
                $error = "Gagal mendaftar: " . mysqli_error($conn);
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="min-h-[85vh] bg-slate-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-2xl shadow-sm border border-slate-100">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-lime-50 rounded-2xl mb-4 text-lime-600 shadow-sm border border-lime-100">
                <i class="fa-solid fa-user-plus text-xl"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800">Buat Akun</h1>
            <p class="text-slate-500 mt-2 text-sm sm:text-base">Daftar untuk mulai menjelajahi produk unik.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm border border-red-100 flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i> 
                <span><?= $error; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-lime-50 text-lime-700 p-4 rounded-xl mb-6 text-sm border border-lime-100 flex flex-col items-center text-center gap-2">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check"></i> 
                    <span class="font-medium"><?= $success; ?></span>
                </div>
                <a href="masuk.php" class="inline-block mt-2 bg-lime-600 text-white px-5 py-2 rounded-lg font-bold text-xs hover:bg-lime-700 transition-colors duration-300">
                    Masuk Sekarang
                </a>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                <input type="text" name="nama" required class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800 placeholder-slate-400" placeholder="Masukkan nama Anda">
            </div>
            
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800 placeholder-slate-400" placeholder="nama@email.com">
            </div>
            
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required class="w-full pl-4 pr-12 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800 placeholder-slate-400" placeholder="••••••••">
                    
                    <button type="button" onclick="togglePassword('password', 'icon-pw')" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-lime-600 transition-colors focus:outline-none">
                        <i id="icon-pw" class="fa-solid fa-eye-slash"></i>
                    </button>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-1.5">Konfirmasi Password</label>
                <div class="relative">
                    <input type="password" id="konfirmasi_password" name="konfirmasi_password" required class="w-full pl-4 pr-12 py-3 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all duration-300 text-slate-800 placeholder-slate-400" placeholder="••••••••">
                    
                    <button type="button" onclick="togglePassword('konfirmasi_password', 'icon-kpw')" class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-lime-600 transition-colors focus:outline-none">
                        <i id="icon-kpw" class="fa-solid fa-eye-slash"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" name="daftar" class="w-full bg-lime-600 text-white py-3.5 rounded-xl font-bold hover:bg-lime-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-lime-200/50 transition-all duration-300 mt-2">
                Daftar Sekarang
            </button>
        </form>

        <p class="text-center text-slate-500 mt-8 text-sm">
            Sudah punya akun? 
            <a href="masuk.php" class="text-lime-600 font-bold hover:text-lime-700 hover:underline transition-colors duration-300">
                Masuk di sini
            </a>
        </p>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        
        // Ubah tipe input
        if (input.type === 'password') {
            input.type = 'text';
            // Ubah ikon mata terbuka
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            input.type = 'password';
            // Ubah ikon mata tertutup
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }
</script>

<?php include 'includes/footer.php'; ?>