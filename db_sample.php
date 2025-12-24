<?php

$host = 'localhost';
$dbname = 'veritabani_adi';
$username = 'kullanici_adi';
$password = 'sifre';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}

function logKaydet($pdo, $tablo, $islem, $kayit_id, $eski_veri, $yeni_veri)
{
    try {
        $sql = "INSERT INTO islem_loglari (tablo_adi, islem, kayit_id, eski_veri, yeni_veri) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $tablo,
            $islem,
            $kayit_id,
            (is_array($eski_veri) ? json_encode($eski_veri, JSON_UNESCAPED_UNICODE) : $eski_veri),
            (is_array($yeni_veri) ? json_encode($yeni_veri, JSON_UNESCAPED_UNICODE) : $yeni_veri)
        ]);
    } catch (Exception $e) {
    }
}
?>