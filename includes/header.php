<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$is_logged_in = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/uaspraktikumpbwd/";
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
        <div class="flex justify-between h-14 items-center">
          <div class="flex items-center">
            <a href="index.php" class="text-2xl font-bold bg-gradient-to-r from-lime-600 to-lime-500 bg-clip-text text-transparent">
              Handmade.
            </a>
          </div>
          
          <div class="hidden md:flex items-center space-x-6">
            <a href="index.php" class="text-gray-600 hover:text-lime-600 transition font-medium text-sm">Beranda</a>
            <a href="katalog.php" class="text-gray-600 hover:text-lime-600 transition font-medium text-sm">Katalog</a>
            <?php if ($is_logged_in): ?>
              <a href="riwayat.php" class="text-gray-600 hover:text-lime-600 transition font-medium text-sm">Riwayat</a>
              <?php if ($is_admin): ?>
                <a href="../admin/index.php" class="bg-lime-100 text-lime-700 px-3 py-1 rounded-xl text-xs font-semibold border border-lime-200">Admin</a>
              <?php endif; ?>
            <?php endif; ?>
          </div>

          <div class="flex items-center space-x-3">
            <?php if ($is_logged_in): ?>
              <a href="keranjang.php" class="relative text-gray-600 hover:text-lime-600 transition p-2">
                <i class="fa-solid fa-cart-shopping text-lg"></i>
                <span class="absolute top-0 right-0 bg-red-500 text-white text-[10px] w-4 h-4 flex items-center justify-center rounded-full">0</span>
              </a>
              <div class="h-6 w-[1px] bg-gray-200 mx-1"></div>
              <a href="logout.php" class="text-gray-600 hover:text-red-600 transition font-medium">Keluar</a>
            <?php else: ?>
              <a href="masuk.php" class="text-gray-600 hover:text-lime-600 transition font-medium">Masuk</a>
              <a href="daftar.php" class="bg-lime-600 text-white px-5 py-2 rounded-xl hover:bg-lime-700 transition font-medium shadow-lg shadow-lime-200">Daftar</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </nav>
    <main class="min-h-screen">
