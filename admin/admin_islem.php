<?php
require_once 'admin_kontrol.php';
require_once '../db.php';

if (!isset($_SESSION['rol']) || ($_SESSION['rol'] != 'admin' && $_SESSION['rol'] != 'alt_yonetici')) {
    die("Yetkisiz erişim: Bu işlemi yapmaya yetkiniz yok.");
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($action == 'set_status') {

    $user_id = (int) $_GET['user_id'];
    $status = (int) $_GET['status'];

    $stmt = $pdo->prepare("SELECT kullanici_adi, aktif FROM kullanicilar WHERE id = ?");
    $stmt->execute([$user_id]);
    $eski_veri = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($eski_veri) {
        $sql = "UPDATE kullanicilar SET aktif = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$status, $user_id]);

        $yeni_veri = ['aktif' => $status];
        $islem_adi = ($status == 1) ? 'UYE_AKTIF_EDILDI' : 'UYE_YASAKLANDI';

        logKaydet($pdo, 'kullanicilar', $islem_adi, $user_id, $eski_veri, $yeni_veri);
    }

    header("Location: admin_uyeler.php?msg=status_updated");
    exit();
}

if ($action == 'change_role' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_SESSION['rol'] != 'admin') {
        die("Yetkisiz işlem: Rol değiştirme yetkiniz yok.");
    }

    $user_id = (int) $_POST['user_id'];
    $new_role = $_POST['new_role'];

    $stmt = $pdo->prepare("SELECT kullanici_adi, rol FROM kullanicilar WHERE id = ?");
    $stmt->execute([$user_id]);
    $eski_veri = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($eski_veri) {
        $sql = "UPDATE kullanicilar SET rol = ? WHERE id = ?";
        $pdo->prepare($sql)->execute([$new_role, $user_id]);

        $yeni_veri = ['rol' => $new_role];
        logKaydet($pdo, 'kullanicilar', 'ROL_DEGISTI', $user_id, $eski_veri, $yeni_veri);
    }

    header("Location: admin_uyeler.php?msg=role_updated");
    exit();
}

if ($action == 'mesaj_sil') {
    if ($_SESSION['rol'] != 'admin') {
        die("Yetkisiz işlem: Mesaj silme yetkiniz yok.");
    }

    $mesaj_id = (int) $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM mesajlar WHERE id = ?");
    $stmt->execute([$mesaj_id]);
    $eski_veri = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($eski_veri) {
        $sql = "DELETE FROM mesajlar WHERE id = ?";
        $pdo->prepare($sql)->execute([$mesaj_id]);

        logKaydet($pdo, 'mesajlar', 'MESAJ_SILINDI', $mesaj_id, $eski_veri, null);
    }

    header("Location: admin_mesajlar.php?msg=deleted");
    exit();
}

header("Location: admin_panel.php");
exit();
?>