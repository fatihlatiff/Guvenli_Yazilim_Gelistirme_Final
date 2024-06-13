<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header('Location: home.php');
    exit();
}

$mysqli = new mysqli('localhost', 'root', 'abc123', 'tarifsepeti');
$result = $mysqli->query("SELECT * FROM users");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kullanıcıları Yönet</title>
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
        <h1>Kullanıcıları Yönet</h1>
        <div class="card-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Kullanıcı Adı</th>
                    <th>Rol</th>
                    <th>İşlemler</th>
                </tr>
                <?php while ($user = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="submit" name="delete_user" value="Sil" class="btn">
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Tüm hakları saklıdır.</p>
    </footer>
</body>
</html>
