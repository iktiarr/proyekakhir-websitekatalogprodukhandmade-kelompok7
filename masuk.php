<?php
include 'koneksi.php';

$error = '';

if (isset($_POST['masuk'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM pengguna WHERE email = '$email'");
    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['role'] = $user['role'];

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

<div class="min-h-[85vh] bg-slate-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    
    <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-2xl shadow-sm border border-slate-100">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-lime-50 rounded-2xl mb-4 text-lime-600 shadow-sm border border-lime-100">
                <i class="fa-solid fa-arrow-right-to-bracket text-xl"></i>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-800">Selamat Datang</h1>
            <p class="text-slate-500 mt-2 text-sm sm:text-base">Silakan masuk ke akun Anda.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm border border-red-100 flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i> 
                <span><?= $error; ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-5">
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

            <div class="flex justify-end">
                <a href="#" class="text-xs sm:text-sm font-medium text-slate-500 hover:text-lime-600 transition-colors duration-300">Lupa password?</a>
            </div>
            
            <button type="submit" name="masuk" class="w-full bg-lime-600 text-white py-3.5 rounded-xl font-bold hover:bg-lime-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-lime-200/50 transition-all duration-300 mt-2">
                Masuk
            </button>
        </form>

        <div class="mt-8 pt-8 border-t border-slate-100 text-center">
            <p class="text-slate-500 text-sm">
                Belum punya akun? 
                <a href="daftar.php" class="text-lime-600 font-bold hover:text-lime-700 hover:underline transition-colors duration-300">
                    Daftar di sini
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
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }
</script>

<?php include 'includes/footer.php'; ?>