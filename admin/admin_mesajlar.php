<?php
require_once 'admin_kontrol.php';
require_once '../header.php';
require_once '../db.php';

if ($ADMIN_ROLU != 'admin')
    die("Yetkisiz Erişim");

$sql = "SELECT m.*, g.kullanici_adi as gonderen_adi, a.kullanici_adi as alan_adi 
        FROM mesajlar m
        JOIN kullanicilar g ON m.gonderen_id = g.id
        JOIN kullanicilar a ON m.alan_id = a.id
        ORDER BY m.tarih DESC LIMIT 100";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$mesajlar = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    body {
        background-color: #f8faff;
    }

    .table-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(112, 144, 176, 0.08);
        border: none;
        overflow: hidden;
    }

    .custom-table thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 15px 20px;
        border-bottom: 1px solid #e2e8f0;
    }

    .custom-table tbody td {
        padding: 15px 20px;
        vertical-align: top;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.9rem;
    }

    .msg-preview {
        background-color: #f1f5f9;
        border-radius: 12px;
        padding: 10px 15px;
        color: #334155;
        font-size: 0.9rem;
        max-width: 400px;
    }

    .btn-icon-soft {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
        text-decoration: none;
    }

    .btn-delete {
        background-color: #fff1f2;
        color: #fb7185;
    }

    .btn-delete:hover {
        background-color: #f43f5e;
        color: white;
    }
</style>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Mesaj Trafiği</h3>
            <p class="text-muted small mb-0">Son 100 mesajın denetimi.</p>
        </div>
        <a href="admin_panel.php" class="btn btn-light text-muted fw-bold shadow-sm rounded-pill px-4">
            <i class="bi bi-arrow-left"></i> Geri
        </a>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2"></i> Mesaj silindi ve loglandı.
        </div>
    <?php endif; ?>

    <div class="table-card">
        <div class="table-responsive">
            <table class="table custom-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 20%;">Kimden / Kime</th>
                        <th style="width: 50%;">İçerik</th>
                        <th style="width: 15%;">Tarih</th>
                        <th style="width: 15%; text-align: right;">Yönet</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($mesajlar as $mesaj): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-primary mb-1"><i class="bi bi-arrow-right-short"></i>
                                    <?php echo htmlspecialchars($mesaj['gonderen_adi']); ?></div>
                                <div class="fw-bold text-success"><i class="bi bi-arrow-left-short"></i>
                                    <?php echo htmlspecialchars($mesaj['alan_adi']); ?></div>
                            </td>
                            <td>
                                <div class="msg-preview">
                                    <?php echo htmlspecialchars($mesaj['icerik']); ?>
                                </div>
                            </td>
                            <td class="text-muted small">
                                <?php echo date("d.m.Y H:i", strtotime($mesaj['tarih'])); ?>
                                <div class="mt-1">
                                    <?php if ($mesaj['okundu_mu']): ?>
                                        <span class="text-primary"><i class="bi bi-check2-all"></i> Okundu</span>
                                    <?php else: ?>
                                        <span class="text-muted"><i class="bi bi-check2"></i> İletildi</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-end align-middle">
                                <a href="admin_islem.php?action=mesaj_sil&id=<?php echo $mesaj['id']; ?>"
                                    class="btn-icon-soft btn-delete"
                                    onclick="return confirm('Bu mesajı silmek istiyor musunuz?');"
                                    title="Mesajı Kalıcı Sil">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>