<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$mysqli = new mysqli('localhost', 'root', 'abc123', 'tarifsepeti');
$result = $mysqli->query("SELECT r.id, r.title, r.description, u.username FROM recipes r JOIN users u ON r.user_id = u.id");

// Oturum süresi (saniye cinsinden)
$inactive = 10 * 60; // 10 dakika

// Oturum son etkinlik süresi
if (isset($_SESSION['last_activity'])) {
    $session_life = time() - $_SESSION['last_activity'];
    if ($session_life > $inactive) {
        session_unset();
        session_destroy();
        header("Location: login.php?session_expired=true");
        exit();
    } else {
        // Kalan süre hesaplama
        $remaining_time = $inactive - $session_life;
        $_SESSION['remaining_time'] = $remaining_time;
    }
} else {
    // Eğer oturum yeni başlatıldıysa, başlangıç zamanını kaydet
    $_SESSION['last_activity'] = time();
    $_SESSION['remaining_time'] = $inactive;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yemek Tarifleri</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
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
        .remaining-time {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #fff;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1.2em;
        }
    </style>
    <script>
        function startCountdown() {
            let duration = <?php echo isset($_SESSION['remaining_time']) ? $_SESSION['remaining_time'] : 600; ?>;
            let display = document.querySelector('#time');
            let timer = duration, minutes, seconds;
            setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = "Kalan süre: " + minutes + ":" + seconds;

                if (--timer < 0) {
                    window.location.href = 'login.php?session_expired=true';
                }
            }, 1000);
        }

        window.onload = function () {
            startCountdown();
        };
    </script>
</head>
<body>
    <!-- Kalan Süre Göstergesi -->
    <div class="remaining-time" id="time">
        <?php
        if (isset($_SESSION['remaining_time'])) {
            $minutes = floor($_SESSION['remaining_time'] / 60);
            $seconds = $_SESSION['remaining_time'] % 60;
            echo "Kalan süre: " . sprintf("%02d:%02d", $minutes, $seconds);
        } else {
            echo "Kalan süre: 10:00";
        }
        ?>
    </div>

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
        <h1>Yemek Tarifleri</h1>
        <div class="card-container">
            <table>
                <thead>
                    <tr>
                        <th>Başlık</th>
                        <th>Açıklama</th>
                        <th>Ekleyen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($recipe = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($recipe['title']); ?></td>
                            <td><?php echo htmlspecialchars($recipe['description']); ?></td>
                            <td><?php echo htmlspecialchars($recipe['username']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Tüm hakları saklıdır.</p>
    </footer>
</body>
</html>
