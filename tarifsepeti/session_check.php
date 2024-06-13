<?php
session_start();

// Oturum süresi (saniye cinsinden)
$inactive = 15 * 60; // 15 dakika

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


 