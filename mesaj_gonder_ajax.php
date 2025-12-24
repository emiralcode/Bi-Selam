<?php

ob_start();
header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => 'Geçersiz istek veya oturum yok (gonder).'
];

try {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    require_once 'db.php';

    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        $response['message'] = 'Sadece POST istekleri kabul edilir.';
    } elseif (!isset($_SESSION['kullanici_id'])) {
        $response['message'] = 'AJAX Hatası (Gonder): Oturum bulunamadı.';
    } else {
        $me_id = $_SESSION['kullanici_id'];
        $hedef_id = (int) ($_POST['hedef_id'] ?? 0);
        $icerik = trim($_POST['icerik'] ?? '');

        if (empty($icerik)) {
            $response['message'] = 'Mesaj içeriği boş olamaz.';
        } elseif ($hedef_id <= 0) {
            $response['message'] = 'AJAX Hatası (Gonder): Hedef ID geçersiz.';
        } else {
            $sql_insert = "INSERT INTO mesajlar (gonderen_id, alan_id, icerik) VALUES (?, ?, ?)";
            $pdo->prepare($sql_insert)->execute([$me_id, $hedef_id, $icerik]);

            $response = ['success' => true];
        }
    }

} catch (Throwable $e) {
    $response['success'] = false;
    $error_message = mb_convert_encoding($e->getMessage(), 'UTF-8', 'auto');
    $response['message'] = 'Sunucu Hatası (Gonder): ' . $error_message;
}

ob_end_clean();
echo json_encode($response);
exit();
?>