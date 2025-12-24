<?php

session_start();
require_once 'db.php';

if (!isset($_SESSION['kullanici_id'])) {
    die("Bu işlem için giriş yapmalısınız.");
}

$me_id = $_SESSION['kullanici_id'];

if (!isset($_GET['id'])) {
    header("Location: anasayfa.php");
    exit();
}
$id = (int) $_GET['id'];
$action = $_GET['action'] ?? '';

try {
    switch ($action) {

        case 'add':
            $target_id = $id;
            if ($target_id == $me_id)
                break;

            $sql_check = "SELECT id FROM arkadasliklar 
                          WHERE (kullanici1_id = ? AND kullanici2_id = ?) 
                          OR (kullanici1_id = ? AND kullanici2_id = ?)";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute([$me_id, $target_id, $target_id, $me_id]);

            if (!$stmt_check->fetch()) {
                $sql_insert = "INSERT INTO arkadasliklar (kullanici1_id, kullanici2_id, durum) VALUES (?, ?, 'bekliyor')";
                $pdo->prepare($sql_insert)->execute([$me_id, $target_id]);
            }
            header("Location: uyeler.php?durum=istek_gonderildi");
            exit();

        case 'accept':
            $arkadaslik_id = $id;
            $sql_accept = "UPDATE arkadasliklar SET durum = 'onaylandi' 
                           WHERE id = ? AND kullanici2_id = ?";
            $pdo->prepare($sql_accept)->execute([$arkadaslik_id, $me_id]);
            header("Location: arkadaslarim.php?durum=onaylandi");
            exit();

        case 'reject':
        case 'remove':
            $arkadaslik_id = $id;
            $sql_delete = "DELETE FROM arkadasliklar 
                           WHERE id = ? AND (kullanici1_id = ? OR kullanici2_id = ?)";
            $pdo->prepare($sql_delete)->execute([$arkadaslik_id, $me_id, $me_id]);
            header("Location: arkadaslarim.php?durum=silindi");
            exit();

        case 'set_type':
            if (!isset($_GET['type']))
                break;
            $arkadaslik_id = $id;
            $tip = $_GET['type'];

            $gecerli_tipler = ['normal', 'flort', 'sevgili_adayi', 'dost'];
            if (!in_array($tip, $gecerli_tipler)) {
                break;
            }

            $sql_type = "UPDATE arkadasliklar SET arkadas_tipi = ? 
                         WHERE id = ? AND (kullanici1_id = ? OR kullanici2_id = ?)";
            $pdo->prepare($sql_type)->execute([$tip, $arkadaslik_id, $me_id, $me_id]);
            header("Location: arkadaslarim.php?durum=tip_guncellendi");
            exit();

        default:
            header("Location: anasayfa.php");
            exit();
    }
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}
?>