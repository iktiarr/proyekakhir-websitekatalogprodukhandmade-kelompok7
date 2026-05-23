<?php
// Get requested URI path
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Normalize path (remove leading and trailing slashes)
$path = trim($path, '/');

// If empty path, default to index.php
if ($path === '') {
    $path = 'index.php';
}

// Security: Prevent directory traversal attacks
if (strpos($path, '..') !== false || strpos($path, '\\') !== false) {
    http_response_code(403);
    echo "Access denied.";
    exit;
}

// Get absolute path to the target file/directory
$baseDir = dirname(__DIR__);
$fullPath = $baseDir . '/' . $path;

// If it's a directory (e.g. "admin"), append "index.php"
if (is_dir($fullPath)) {
    $path = rtrim($path, '/') . '/index.php';
    $fullPath = $baseDir . '/' . $path;
}

// If file doesn't exist, try appending .php (supporting extensionless URLs)
if (!file_exists($fullPath) && file_exists($fullPath . '.php')) {
    $path .= '.php';
    $fullPath = $baseDir . '/' . $path;
}

// Serve the file if it exists
if (file_exists($fullPath) && !is_dir($fullPath)) {
    // Only execute PHP files, serve others as static (fallback)
    if (pathinfo($fullPath, PATHINFO_EXTENSION) === 'php') {
        // Change working directory to the target file's directory so relative paths work
        chdir(dirname($fullPath));
        
        // Define SCRIPT_FILENAME and other standard variables so self-referencing scripts work
        $_SERVER['SCRIPT_FILENAME'] = $fullPath;
        $_SERVER['SCRIPT_NAME'] = '/' . $path;
        $_SERVER['PHP_SELF'] = '/' . $path;
        
        require $fullPath;
        exit;
    } else {
        // Serve static file with correct content type
        $mime_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
        ];
        $ext = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $content_type = isset($mime_types[$ext]) ? $mime_types[$ext] : 'application/octet-stream';
        header("Content-Type: $content_type");
        readfile($fullPath);
        exit;
    }
}

// 404 Not Found
http_response_code(404);
echo "404 Not Found";
?>
