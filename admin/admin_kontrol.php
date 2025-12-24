<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: ../giris.html");
    exit();
}

if ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'alt_yonetici') {
    die("Bu sayfaya erişim yetkiniz yok.");
}

$ADMIN_ROLU = $_SESSION['rol'];
?>