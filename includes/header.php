<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/uaspraktikumpbwd/";
// Adjust base_url if needed, for simplicity we'll use relative paths
?>
<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Handmade Katalog</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
  </head>
  <body class="bg-gray-50 text-gray-900">
    <nav class="bg-white/80 backdrop-blur-md sticky top-0 z-50 border-b border-gray-100">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
          <div class="flex items-center">
            <a href="index.php" class="text-2xl font-bold bg-gradient-to-r from-amber-600 to-orange-500 bg-clip-text text-transparent">
              Handmade.
            </a>
          </div>
          
          <div class="hidden md:flex items-center space-x-8">
            <a href="index.php" class="text-gray-600 hover:text-amber-600 transition font-medium">Beranda</a>
            <a href="katalog.php" class="text-gray-600 hover:text-amber-600 transition font-medium">Katalog</a>
            <?php if ($is_logged_in): ?>
              <a href="riwayat.php" class="text-gray-600 hover:text-amber-600 transition font-medium">Riwayat</a>
              <?php if ($is_admin): ?>
                <a href="admin/index.php" class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-sm font-semibold border border-amber-200">Admin Panel</a>
              <?php endif; ?>
            <?php endif; ?>
          </div>

          <div class="flex items-center space-x-4">
            <?php if ($is_logged_in): ?>
              <a href="keranjang.php" class="relative text-gray-600 hover:text-amber-600 transition p-2">
                <i class="fa-solid fa-cart-shopping text-xl"></i>
                <span class="absolute top-0 right-0 bg-red-500 text-white text-[10px] w-4 h-4 flex items-center justify-center rounded-full">0</span>
              </a>
              <div class="h-8 w-[1px] bg-gray-200 mx-2"></div>
              <a href="logout.php" class="text-gray-600 hover:text-red-600 transition font-medium">Keluar</a>
            <?php else: ?>
              <a href="masuk.php" class="text-gray-600 hover:text-amber-600 transition font-medium">Masuk</a>
              <a href="daftar.php" class="bg-amber-600 text-white px-5 py-2 rounded-xl hover:bg-amber-700 transition font-medium shadow-lg shadow-amber-200">Daftar</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </nav>
    <main class="min-h-screen">
