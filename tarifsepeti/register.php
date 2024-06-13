<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password']; // Şifreyi doğrudan alıyoruz
    $role = $_POST['role'];
    
    $mysqli = new mysqli('localhost', 'root', 'abc123', 'tarifsepeti');
    $stmt = $mysqli->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param('sss', $username, $password, $role);
    
    if ($stmt->execute()) {
        echo "Kayıt başarılı!";
    } else {
        echo "Kayıt başarısız: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kayıt Ol</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
    <!-- Navigasyon Bar -->
    <nav>
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="tarifler.php">Tarifler</a></li>
            <li><a href="tarifekle.php">Tarif Ekle</a></li>
            <li><a href="logout.php">Çıkış Yap</a></li>
        </ul>
    </nav>

    <!-- Ana İçerik -->
    <main>
        <h1>Kayıt Ol</h1>
        <div class="card-container">
            <form method="POST" class="card">
                <label>Kullanıcı Adı:</label>
                <input type="text" name="username" required><br>
                <label>Şifre:</label>
                <input type="password" name="password" required><br>
                <label>Rol:</label>
                <select name="role">
                    <option value="user">User</option>
                    <option value="editor">Editor</option>
                    <option value="admin">Admin</option>
                </select><br>
                <input type="submit" value="Kayıt Ol">
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Tüm hakları saklıdır.</p>
    </footer>
</body>
</html>
