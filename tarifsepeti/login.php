<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Veritabanı bağlantısı
    $mysqli = new mysqli('localhost', 'root', 'abc123', 'tarifsepeti');

    // Bağlantı hatası kontrolü
    if ($mysqli->connect_error) {
        die("Veritabanı bağlantısı başarısız: " . $mysqli->connect_error);
    }

    $stmt = $mysqli->prepare("SELECT id, password, role FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password, $role);
    $stmt->fetch();

    // Şifre doğrulama
    if (password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        $_SESSION['last_activity'] = time(); // Oturum başlangıç zamanını kaydet
        $_SESSION['session_duration'] = 10 * 60; // 10 dakika
        header('Location: home.php');
        exit();
    } else {
        $login_error = "Kullanıcı adı veya şifre yanlış!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Giriş Yap</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <style>
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
            let duration = <?php echo isset($_SESSION['session_duration']) ? $_SESSION['session_duration'] : 600; ?>;
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
        if (isset($_SESSION['session_duration'])) {
            $minutes = floor($_SESSION['session_duration'] / 60);
            $seconds = $_SESSION['session_duration'] % 60;
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
        <h1>Giriş Yap</h1>
        <div class="card-container">
            <form method="POST" class="card">
                <?php if (isset($login_error)): ?>
                    <p><?php echo $login_error; ?></p>
                <?php endif; ?>
                <label for="username">Kullanıcı Adı:</label>
                <input type="text" id="username" name="username" required><br>
                <label for="password">Şifre:</label>
                <input type="password" id="password" name="password" required><br>
                <input type="submit" value="Giriş Yap">
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Tüm hakları saklıdır.</p>
    </footer>
</body>
</html>
