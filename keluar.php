<?php
// keluar.php: Menghapus sesi pengguna/admin (logout) lalu mengalihkan halaman ke login.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['dari']) && $_GET['dari'] === 'admin') {
    unset($_SESSION['admin']);
} else {
    unset($_SESSION['user']);
}

header("Location: masuk.php");
exit();
?>
