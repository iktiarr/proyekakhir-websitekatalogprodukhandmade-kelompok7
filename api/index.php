<?php
$jalur = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$jalur = trim($jalur, '/');

if ($jalur === '') {
    $jalur = 'index.php';
}

if (strpos($jalur, '..') !== false || strpos($jalur, '\\') !== false) {
    http_response_code(403);
    echo "Akses ditolak.";
    exit;
}

$direktori_induk = dirname(__DIR__);
$jalur_lengkap = $direktori_induk . '/' . $jalur;

if (is_dir($jalur_lengkap)) {
    $jalur = rtrim($jalur, '/') . '/index.php';
    $jalur_lengkap = $direktori_induk . '/' . $jalur;
}

if (!file_exists($jalur_lengkap) && file_exists($jalur_lengkap . '.php')) {
    $jalur .= '.php';
    $jalur_lengkap = $direktori_induk . '/' . $jalur;
}

if (file_exists($jalur_lengkap) && !is_dir($jalur_lengkap)) {
    if (pathinfo($jalur_lengkap, PATHINFO_EXTENSION) === 'php') {
        chdir(dirname($jalur_lengkap));
        
        $_SERVER['SCRIPT_FILENAME'] = $jalur_lengkap;
        $_SERVER['SCRIPT_NAME'] = '/' . $jalur;
        $_SERVER['PHP_SELF'] = '/' . $jalur;
        
        require $jalur_lengkap;
        exit;
    } else {
        $tipe_mime = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
        ];
        $ekstensi = strtolower(pathinfo($jalur_lengkap, PATHINFO_EXTENSION));
        $tipe_konten = isset($tipe_mime[$ekstensi]) ? $tipe_mime[$ekstensi] : 'application/octet-stream';
        header("Content-Type: $tipe_konten");
        readfile($jalur_lengkap);
        exit;
    }
}

http_response_code(404);
echo "404 Halaman Tidak Ditemukan";
?>
