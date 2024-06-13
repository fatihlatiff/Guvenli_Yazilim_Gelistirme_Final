<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$mysqli = new mysqli('localhost', 'root', 'abc123', 'tarifsepeti');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    // SQL injection açığı kasıtlı olarak bırakıldı
    $query = "SELECT * FROM users WHERE username LIKE '%$username%'";
    $result = $mysqli->query($query);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kullanıcı Ara</title>
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
        <h1>Kullanıcı Ara</h1>
        <div class="card-container">
            <form method="POST" class="card">
                <label>Kullanıcı Adı:</label>
                <input type="text" name="username" required><br>
                <input type="submit" value="Ara">
            </form>
        </div>

        <!-- Sonuçları formun altına yerleştirme -->
        <?php if (isset($result)) { ?>
            <div class="card-container">
                <table>
                    <tr>
                        <th>ID</th>
                        <th>Kullanıcı Adı</th>
                        <th>Rol</th>
                    </tr>
                    <?php while ($user = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['role']; ?></td>
                        </tr>
                    <?php } ?>
                </table>
            </div>
        <?php } ?>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Tüm hakları saklıdır.</p>
    </footer>
</body>
</html>
