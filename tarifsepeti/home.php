<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$role = $_SESSION['role'];

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
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ana Sayfa</title>
    <link rel="stylesheet" type="text/css" href="css/styles.css">
    <style>
        body {
            background-image: url('tarifsepeti/fotoğraflar/tavuk.jpg'); /* Görselin yolu */
            background-size: cover; /* Arka planı tam kaplama */
            background-position: center; /* Ortalayarak yerleştirme */
            background-repeat: no-repeat; /* Tekrarlamadan görseli kullanma */
            background-attachment: fixed; /* Görselin sabit kalmasını sağlar */
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
        .card-container a {
            display: inline-block;
            padding: 10px;
            margin: 10px;
            background-color: #f2f2f2;
            text-decoration: none;
            color: #000;
            border: 1px solid #ccc;
            border-radius: 5px;
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
        <h1>Hoşgeldin, <?php echo $_SESSION['username']; ?>!</h1>
        <p>Rolünüz: <?php echo $role; ?></p>
        
        <div class="card-container">
            <?php if ($role == 'admin') { ?>
                <a href="manage_users.php" class="card">Kullanıcıları Yönet</a>
            <?php } ?>

            <?php if ($role == 'editor' || $role == 'admin') { ?>
                <a href="manage_recipes.php" class="card">Tarifleri Yönet</a>
            <?php } ?>

            <a href="tarifekle.php" class="card">Tarif Ekle</a>
            <a href="tarifler.php" class="card">Tarifleri Gör</a>
            <a href="user_search.php" class="card">Kullanıcı Ara</a>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 Tüm hakları saklıdır.</p>
    </footer>

</body>
</html>
