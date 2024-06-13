<?php
include('session_check.php'); // Oturum kontrolü


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$role = $_SESSION['role'];
$recipe_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$mysqli = new mysqli('localhost', 'root', 'abc123', 'tarifsepeti');

// Tarif bilgilerini al
$stmt = $mysqli->prepare("SELECT title, description FROM recipes WHERE id = ?");
$stmt->bind_param('i', $recipe_id);
$stmt->execute();
$stmt->bind_result($title, $description);
$stmt->fetch();
$stmt->close();

$value = isset($_POST['value']) ? $_POST['value'] : 'false';

if ($value === 'true') {
    $role = 'admin';
    $_SESSION['role'] = 'admin';
    echo "Admin yetkisi verildi!";
} elseif ($role !== 'admin' && $role !== 'editor') {
    header('Location: home.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_title = $_POST['title'];
    $new_description = $_POST['description'];

    $update_stmt = $mysqli->prepare("UPDATE recipes SET title = ?, description = ? WHERE id = ?");
    $update_stmt->bind_param('ssi', $new_title, $new_description, $recipe_id);

    if ($update_stmt->execute()) {
        echo "Tarif başarıyla güncellendi!";
    } else {
        echo "Tarif güncellenirken hata oluştu: " . $update_stmt->error;
    }
    $update_stmt->close();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tarifi Düzenle</title>
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
        <h1>Tarifi Düzenle</h1>
        <div class="card-container">
            <form method="POST" class="card">
                <label>Başlık:</label><input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>" required><br>
                <label>Açıklama:</label><textarea name="description" required><?php echo htmlspecialchars($description); ?></textarea><br>
                <label>Admin Yetkisi:</label>
                <select name="value">
                    <option value="false" <?php echo ($value == 'false') ? 'selected' : ''; ?>>Hayır</option>
                    <option value="true" <?php echo ($value == 'true') ? 'selected' : ''; ?>>Evet</option>
                </select><br>
                <input type="submit" value="Güncelle">
            </form>
            <a href="manage_recipes.php" class="card">Geri Dön</a>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Tüm hakları saklıdır.</p>
    </footer>
</body>
</html>
