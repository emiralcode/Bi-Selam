<?php
session_start();

require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $kullanici_adi = $_POST['kullanici_adi'];
    $sifre = $_POST['sifre'];

    if (empty($kullanici_adi) || empty($sifre)) {
        die("Kullanıcı adı veya şifre boş bırakılamaz.");
    }

    try {
        $sql = "SELECT id, kullanici_adi, sifre, rol, aktif FROM kullanicilar WHERE kullanici_adi = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$kullanici_adi]);

        $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($kullanici && password_verify($sifre, $kullanici['sifre'])) {

            if ($kullanici['aktif'] == 0) {
                die("Hesabınız bir yönetici tarafından askıya alınmıştır.");
            }


            $_SESSION['kullanici_id'] = $kullanici['id'];
            $_SESSION['kullanici_adi'] = $kullanici['kullanici_adi'];
            $_SESSION['rol'] = $kullanici['rol'];

            header("Location: anasayfa.php");
            exit();

        } else {
            header("Location: giris.php?hata=1");
            exit();
        }

    } catch (PDOException $e) {
        die("Veritabanı hatası: " . $e->getMessage());
    }

} else {
    header("Location: giris.html");
    exit();
}
?>