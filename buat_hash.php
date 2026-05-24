<?php
$password = isset($_POST['password']) ? $_POST['password'] : '';
$hash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($password)) {
    $hash = password_hash($password, PASSWORD_DEFAULT);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Hash Password</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f8fafc;
            color: #334155;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            width: 100%;
            max-width: 400px;
        }
        h2 {
            margin-top: 0;
            color: #1e293b;
            font-size: 20px;
        }
        label {
            font-size: 13px;
            font-weight: bold;
            color: #475569;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0 16px 0;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 14px;
            outline: none;
        }
        input[type="text"]:focus {
            border-color: #16a34a;
        }
        button {
            width: 100%;
            background-color: #16a34a;
            color: white;
            border: none;
            padding: 11px;
            border-radius: 6px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background-color: #15803d;
        }
        .result {
            margin-top: 20px;
            padding: 12px;
            background-color: #f1f5f9;
            border-radius: 6px;
            word-break: break-all;
            font-family: monospace;
            font-size: 13px;
            border: 1px solid #e2e8f0;
            color: #0f172a;
        }
    </style>
</head>
<body>
    <div class="card">
        <h2>Password Hash Generator</h2>
        <form method="POST">
            <label for="password">Masukkan Password Plaintext:</label>
            <input type="text" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" required placeholder="Contoh: rahasia123">
            <button type="submit">Buat Hash</button>
        </form>
        <?php if (!empty($hash)): ?>
            <div class="result">
                <strong style="font-family: sans-serif; font-size: 12px; color: #475569;">Hasil Hash (Bcrypt):</strong><br>
                <div style="margin-top: 6px; user-select: all;"><?php echo $hash; ?></div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
