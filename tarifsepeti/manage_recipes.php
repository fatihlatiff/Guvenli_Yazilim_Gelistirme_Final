<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$role = $_SESSION['role'];

if ($role !== 'admin' && $role !== 'editor') {
    header('Location: home.php');
    exit();
}

$mysqli = new mysqli('localhost', 'root', 'abc123', 'tarifsepeti');

// Tarifi Silme
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_recipe'])) {
    $recipe_id = $_POST['recipe_id'];
    $stmt = $mysqli->prepare("DELETE FROM recipes WHERE id = ?");
    $stmt->bind_param('i', $recipe_id);
    $stmt->execute();
}

// Tarifleri Listeleme
$recipes = $mysqli->query("SELECT r.id, r.title, r.description, u.username FROM recipes r JOIN users u ON r.user_id = u.id");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tarifleri Yönet</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <style>
        th {
            padding: 0 20px; /* Her iki yanına 20px boşluk ekler */
            text-align: left; /* Başlıkları sola hizalar */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #ddd;
        }
        .btn {
            padding: 10px 15px;
            text-decoration: none;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        form {
            display: inline;
        }
    </style>
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
        <h1>Tarifleri Yönet</h1>
        <div class="card-container">
            <table>
                <tr>
                    <th>Başlık</th>
                    <th>Açıklama</th>
                    <th>Ekleyen</th>
                    <th>İşlemler</th>
                </tr>

                <?php while ($recipe = $recipes->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($recipe['title']); ?></td>
                        <td><?php echo htmlspecialchars($recipe['description']); ?></td>
                        <td><?php echo htmlspecialchars($recipe['username']); ?></td>
                        <td>
                            <?php if ($role == 'admin' || $role == 'editor') { ?>
                                <a href="edit_recipe.php?id=<?php echo $recipe['id']; ?>" class="btn">Düzenle</a>
                                <form method="POST">
                                    <input type="hidden" name="recipe_id" value="<?php echo $recipe['id']; ?>">
                                    <input type="submit" name="delete_recipe" value="Sil" class="btn">
                                </form>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </table>
            <a href="home.php" class="btn">Geri Dön</a>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Tüm hakları saklıdır.</p>
    </footer>
</body>
</html>
