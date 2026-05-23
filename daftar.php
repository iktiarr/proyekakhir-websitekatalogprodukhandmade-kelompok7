<?php
include 'koneksi.php';

$error = '';

if (isset($_POST['daftar'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi_password'];

    // Validasi konfirmasi password
    if ($password !== $konfirmasi) {
        $error = "Konfirmasi password tidak cocok!";
    } else {
        // Cek apakah email sudah terdaftar
        $check = mysqli_query($conn, "SELECT id FROM pengguna WHERE email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Email sudah terdaftar!";
        } else {
            // Hash password untuk keamanan
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO pengguna (nama, email, password, role) VALUES ('$nama', '$email', '$hashed', 'user')";
            if (mysqli_query($conn, $query)) {
                // Berhasil daftar, langsung arahkan ke login dengan pesan sukses
                header("Location: masuk.php?registrasi=sukses");
                exit();
            } else {
                $error = "Pendaftaran gagal!";
            }
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="min-h-[70vh] bg-slate-50 flex items-center justify-center py-8 px-4">
    <div class="max-w-md w-full bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm">
        
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Buat Akun</h1>
            <p class="text-slate-500 mt-1 text-sm">Daftar untuk mulai belanja kerajinan tangan</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 px-4 py-2.5 rounded-xl mb-4 text-xs border border-red-100 flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-sm"></i> 
                <span class="font-medium"><?= $error; ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Nama Lengkap</label>
                <input type="text" name="nama" required class="w-full px-3.5 py-2 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-sm text-slate-800" placeholder="Nama Lengkap Anda">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-3.5 py-2 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-sm text-slate-800" placeholder="nama@email.com">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Password</label>
                <div class="relative">
                    <input type="password" id="password" name="password" required class="w-full pl-3.5 pr-10 py-2 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-sm text-slate-800" placeholder="••••••••">
                    <button type="button" onclick="togglePassword('password', 'icon-pw')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-lime-600 transition-colors">
                        <i id="icon-pw" class="fa-solid fa-eye-slash text-sm"></i>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Konfirmasi Password</label>
                <div class="relative">
                    <input type="password" id="konfirmasi_password" name="konfirmasi_password" required class="w-full pl-3.5 pr-10 py-2 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-sm text-slate-800" placeholder="••••••••">
                    <button type="button" onclick="togglePassword('konfirmasi_password', 'icon-kpw')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-lime-600 transition-colors">
                        <i id="icon-kpw" class="fa-solid fa-eye-slash text-sm"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" name="daftar" class="w-full bg-lime-600 text-white py-2.5 rounded-xl font-bold hover:bg-lime-700 hover:shadow-lg hover:shadow-lime-200/40 transition-all text-sm mt-1 cursor-pointer">
                Daftar Sekarang
            </button>
        </form>

        <div class="mt-5 pt-4 border-t border-slate-100 text-center">
            <p class="text-slate-500 text-xs">
                Sudah punya akun? 
                <a href="masuk.php" class="text-lime-600 font-bold hover:text-lime-700 hover:underline transition-colors ml-1">
                    Masuk di sini
                </a>
            </p>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        }
    }
</script>

<?php include 'includes/footer.php'; ?>