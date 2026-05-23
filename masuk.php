<?php
include 'koneksi.php';

$error = '';
$success = '';

// Cek jika dialihkan dari pendaftaran sukses
if (isset($_GET['registrasi']) && $_GET['registrasi'] === 'sukses') {
    $success = "Pendaftaran berhasil! Silakan masuk.";
}

if (isset($_POST['masuk'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Ambil data pengguna berdasarkan email
    $query = mysqli_query($conn, "SELECT * FROM pengguna WHERE email = '$email'");
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        // Verifikasi password terenkripsi
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];

            // Arahkan sesuai role
            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="min-h-[70vh] bg-slate-50 flex items-center justify-center py-8 px-4">
    <div class="max-w-md w-full bg-white p-6 sm:p-8 rounded-2xl border border-slate-100 shadow-sm">
        
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Selamat Datang</h1>
            <p class="text-slate-500 mt-1 text-sm">Masuk untuk melanjutkan belanja Anda</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 px-4 py-2.5 rounded-xl mb-4 text-xs border border-red-100 flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-sm"></i> 
                <span class="font-medium"><?= $error; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-lime-50 text-lime-700 px-4 py-2.5 rounded-xl mb-4 text-xs border border-lime-100 flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-sm"></i> 
                <span class="font-medium"><?= $success; ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-3.5 py-2 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-sm text-slate-800" placeholder="nama@email.com">
            </div>
            
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="block text-xs font-bold text-slate-600">Password</label>
                    <a href="#" class="text-[11px] font-bold text-slate-400 hover:text-lime-600 transition-colors">Lupa?</a>
                </div>
                <div class="relative">
                    <input type="password" id="password" name="password" required class="w-full pl-3.5 pr-10 py-2 bg-slate-50 rounded-xl border border-slate-200 focus:bg-white focus:ring-2 focus:ring-lime-500/20 focus:border-lime-500 outline-none transition-all text-sm text-slate-800" placeholder="••••••••">
                    <button type="button" onclick="togglePassword('password', 'icon-pw')" class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400 hover:text-lime-600 transition-colors">
                        <i id="icon-pw" class="fa-solid fa-eye-slash text-sm"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit" name="masuk" class="w-full bg-lime-600 text-white py-2.5 rounded-xl font-bold hover:bg-lime-700 hover:shadow-lg hover:shadow-lime-200/40 transition-all text-sm mt-1 cursor-pointer">
                Masuk
            </button>
        </form>

        <div class="mt-5 pt-4 border-t border-slate-100 text-center">
            <p class="text-slate-500 text-xs">
                Belum punya akun? 
                <a href="daftar.php" class="text-lime-600 font-bold hover:text-lime-700 hover:underline transition-colors ml-1">
                    Daftar Sekarang
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