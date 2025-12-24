<?php
require_once 'admin_kontrol.php';
require_once '../header.php';
require_once '../db.php';

if ($ADMIN_ROLU != 'admin')
    die("Yetkisiz");

$sql = "SELECT * FROM islem_loglari ORDER BY tarih DESC LIMIT 200";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$loglar = $stmt->fetchAll(PDO::FETCH_ASSOC);

function veriGoster($json_veri)
{
    if (empty($json_veri))
        return '<span class="text-muted opacity-50">-</span>';
    $veri = json_decode($json_veri, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($veri))
        return htmlspecialchars($json_veri);

    $html = '<div class="d-flex flex-column gap-1">';
    foreach ($veri as $k => $v) {
        $k_clean = ucwords(str_replace('_', ' ', $k));
        $v_clean = (strlen($v) > 40) ? substr($v, 0, 40) . '...' : $v;
        $html .= "<div class='small'><span class='fw-bold text-secondary'>{$k_clean}:</span> <span class='text-dark'>" . htmlspecialchars($v_clean) . "</span></div>";
    }
    $html .= '</div>';
    return $html;
}
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
        font-size: 0.7rem;
        padding: 12px 15px;
        border-bottom: 1px solid #e2e8f0;
    }

    .custom-table tbody td {
        padding: 12px 15px;
        vertical-align: middle;
        font-size: 0.85rem;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }

    .badge-op {
        padding: 5px 10px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.7rem;
        display: inline-block;
        min-width: 80px;
        text-align: center;
    }

    .op-insert {
        background-color: #dcfce7;
        color: #15803d;
    }

    .op-update {
        background-color: #dbeafe;
        color: #1d4ed8;
    }

    .op-delete {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    .op-other {
        background-color: #f3f4f6;
        color: #4b5563;
    }

    .data-box {
        background-color: #f8fafc;
        border-radius: 8px;
        padding: 8px;
        border: 1px solid #f1f5f9;
    }

    .data-box-old {
        border-left: 3px solid #cbd5e1;
    }

    .data-box-new {
        border-left: 3px solid #22c55e;
    }
</style>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Sistem Kayıtları</h3>
            <p class="text-muted small mb-0">Son 200 işlem kaydı.</p>
        </div>
        <a href="admin_panel.php" class="btn btn-light text-muted fw-bold shadow-sm rounded-pill px-4">
            <i class="bi bi-arrow-left"></i> Geri
        </a>
    </div>

    <div class="table-card">
        <div class="table-responsive">
            <table class="table custom-table mb-0">
                <thead>
                    <tr>
                        <th width="5%">#ID</th>
                        <th width="10%">Tablo</th>
                        <th width="10%">İşlem</th>
                        <th width="30%">Eski Veri</th>
                        <th width="30%">Yeni Veri</th>
                        <th width="15%">Tarih</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loglar as $l): ?>
                        <?php
                        $islem = strtoupper($l['islem']);
                        $class = 'op-other';
                        if (strpos($islem, 'INSERT') !== false || strpos($islem, 'EKLE') !== false)
                            $class = 'op-insert';
                        if (strpos($islem, 'UPDATE') !== false || strpos($islem, 'DEGISTI') !== false)
                            $class = 'op-update';
                        if (strpos($islem, 'DELETE') !== false || strpos($islem, 'SIL') !== false)
                            $class = 'op-delete';
                        ?>
                        <tr>
                            <td class="fw-bold text-secondary"><?php echo $l['id']; ?></td>
                            <td class="fw-bold text-primary"><?php echo htmlspecialchars($l['tablo_adi']); ?></td>
                            <td><span class="badge-op <?php echo $class; ?>"><?php echo $islem ?: 'BILINMIYOR'; ?></span>
                            </td>

                            <td>
                                <div class="data-box data-box-old">
                                    <?php echo veriGoster($l['eski_veri']); ?>
                                </div>
                            </td>

                            <td>
                                <div class="data-box data-box-new">
                                    <?php echo veriGoster($l['yeni_veri']); ?>
                                </div>
                            </td>

                            <td class="text-muted small">
                                <?php echo date("d.m H:i", strtotime($l['tarih'])); ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>