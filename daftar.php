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

<div class="max-w-md mx-auto mt-20 p-8 bg-white rounded-3xl shadow-xl shadow-gray-200 border border-gray-100">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Buat Akun</h1>
        <p class="text-gray-500 mt-2">Daftar untuk mulai belanja produk unik.</p>
    </div>

    <?php if ($error): ?>
        <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm border border-red-100">
            <i class="fa-solid fa-circle-exclamation mr-2"></i> <?= $error; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 text-sm border border-green-100">
            <i class="fa-solid fa-circle-check mr-2"></i> <?= $success; ?>
            <a href="masuk.php" class="font-bold underline ml-1">Masuk sekarang</a>
        </div>
    <?php endif; ?>

    <form action="" method="POST" class="space-y-5">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="nama" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition" placeholder="Masukkan nama Anda">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input type="email" name="email" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition" placeholder="nama@email.com">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input type="password" name="password" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition" placeholder="••••••••">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
            <input type="password" name="konfirmasi_password" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-amber-500 focus:border-transparent outline-none transition" placeholder="••••••••">
        </div>
        <button type="submit" name="daftar" class="w-full bg-amber-600 text-white py-3 rounded-xl font-bold hover:bg-amber-700 transition shadow-lg shadow-amber-200 mt-4">Daftar Sekarang</button>
    </form>

    <p class="text-center text-gray-500 mt-8 text-sm">
        Sudah punya akun? <a href="masuk.php" class="text-amber-600 font-bold hover:underline">Masuk</a>
    </p>
</div>

<?php include 'includes/footer.php'; ?>
