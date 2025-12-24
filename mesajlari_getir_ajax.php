<?php

ob_start();
header('Content-Type: application/json');

$response = [
    'success' => true,
    'messages' => []
];

try {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    require_once 'db.php';

    if (!isset($_SESSION['kullanici_id'])) {
        $response['success'] = false;
        $response['message'] = 'AJAX Hatası (Getir): Oturum bulunamadı.';

    } elseif (!isset($_GET['hedef_id']) || empty((int) $_GET['hedef_id'])) {
        $response['success'] = false;
        $response['message'] = 'AJAX Hatası (Getir): Hedef ID gönderilmedi.';

    } else {
        $me_id = $_SESSION['kullanici_id'];
        $hedef_id = (int) $_GET['hedef_id'];

        $sql_fetch = "SELECT id, icerik FROM mesajlar 
                      WHERE gonderen_id = ? AND alan_id = ? AND okundu_mu = 0 
                      ORDER BY tarih ASC";
        $stmt_fetch = $pdo->prepare($sql_fetch);
        $stmt_fetch->execute([$hedef_id, $me_id]);

        $new_messages = $stmt_fetch->fetchAll(PDO::FETCH_ASSOC);

        if ($new_messages) {
            $sql_update = "UPDATE mesajlar SET okundu_mu = 1 
                           WHERE gonderen_id = ? AND alan_id = ? AND okundu_mu = 0";
            $pdo->prepare($sql_update)->execute([$hedef_id, $me_id]);

            $response['messages'] = $new_messages;
        }
    }

} catch (Throwable $e) {
    $response['success'] = false;
    $error_message = mb_convert_encoding($e->getMessage(), 'UTF-8', 'auto');
    $response['message'] = 'Sunucu Hatası (Getir): ' . $error_message;
}

ob_end_clean();
echo json_encode($response);
exit();
?>