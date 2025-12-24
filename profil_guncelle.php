<?php

session_start();
require_once 'db.php';

if (!isset($_SESSION['kullanici_id'])) {
    die("Bu işlemi yapmak için giriş yapmalısınız.");
}

$kullanici_id = $_SESSION['kullanici_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ad_soyad = trim($_POST['ad_soyad']);
    $bio = trim($_POST['bio']);
    $sehir = trim($_POST['sehir']);

    $profil_foto_adi = null;

    if (isset($_FILES['profil_fotografi']) && $_FILES['profil_fotografi']['error'] == 0) {

        $dosya = $_FILES['profil_fotografi'];
        $hedef_klasor = "uploads/";

        $dosya_uzantisi = pathinfo($dosya['name'], PATHINFO_EXTENSION);
        $profil_foto_adi = $kullanici_id . "_" . time() . "." . $dosya_uzantisi;

        $hedef_dosya = $hedef_klasor . $profil_foto_adi;

        $izinli_turler = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($dosya_uzantisi), $izinli_turler)) {

            if (move_uploaded_file($dosya['tmp_name'], $hedef_dosya)) {
            } else {
                die("Dosya yüklenirken bir hata oluştu.");
            }
        } else {
            die("Sadece JPG, JPEG, PNG & GIF dosyaları yüklenebilir.");
        }
    }

    try {
        if ($profil_foto_adi) {
            $sql = "UPDATE profiller SET ad_soyad = ?, bio = ?, sehir = ?, profil_fotografi = ? WHERE kullanici_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$ad_soyad, $bio, $sehir, $profil_foto_adi, $kullanici_id]);
        } else {
            $sql = "UPDATE profiller SET ad_soyad = ?, bio = ?, sehir = ? WHERE kullanici_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$ad_soyad, $bio, $sehir, $kullanici_id]);
        }

        echo "Profil başarıyla güncellendi!";
        header("Location: profil_duzenle.php");
        exit();

    } catch (PDOException $e) {
        die("Veritabanı güncelleme hatası: " . $e->getMessage());
    }

} else {
    header("Location: profil_duzenle.php");
    exit();
}
?>