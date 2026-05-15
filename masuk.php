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

<div class="max-w-md mx-auto mt-20 p-8 bg-white rounded-3xl shadow-xl shadow-gray-200 border border-gray-100">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Selamat Datang</h1>
        <p class="text-gray-500 mt-2">Silakan masuk ke akun Anda.</p>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm border border-red-100">
            <i class="fa-solid fa-circle-exclamation mr-2"></i> <?= $error; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition" placeholder="nama@email.com">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition" placeholder="••••••••">
        </div>
        <button type="submit" name="masuk" class="w-full bg-amber-600 text-white py-3 rounded-xl font-bold hover:bg-amber-700 transition shadow-lg shadow-amber-200 mt-4">Masuk</button>
    </form>

    <div class="mt-8 pt-8 border-t border-gray-50 text-center">
        <p class="text-gray-500 text-sm">
            Belum punya akun? <a href="daftar.php" class="text-amber-600 font-bold hover:underline">Daftar</a>
        </p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
