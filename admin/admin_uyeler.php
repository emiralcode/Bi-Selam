<?php
require_once 'admin_kontrol.php';
require_once '../header.php';
require_once '../db.php';

$me_id = $_SESSION['kullanici_id'];

$sql = "SELECT id, kullanici_adi, email, rol, aktif, kayit_tarihi FROM kullanicilar 
        WHERE id != ? 
        ORDER BY kayit_tarihi DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$me_id]);
$uyeler = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        padding: 0;
    }

    .custom-table thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 15px 20px;
        border-bottom: 1px solid #e2e8f0;
    }

    .custom-table tbody td {
        padding: 18px 20px;
        vertical-align: middle;
        color: #334155;
        font-size: 0.9rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .custom-table tbody tr:last-child td {
        border-bottom: none;
    }

    .custom-table tbody tr:hover {
        background-color: #f8faff;
    }

    .badge-soft-success {
        background-color: #dcfce7;
        color: #166534;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .badge-soft-danger {
        background-color: #fee2e2;
        color: #991b1b;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .badge-soft-role {
        background-color: #eff6ff;
        color: #1e40af;
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .btn-action-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: 0.2s;
        border: none;
    }

    .btn-soft-red {
        background-color: #fef2f2;
        color: #ef4444;
    }

    .btn-soft-red:hover {
        background-color: #ef4444;
        color: white;
    }

    .btn-soft-green {
        background-color: #f0fdf4;
        color: #16a34a;
    }

    .btn-soft-green:hover {
        background-color: #16a34a;
        color: white;
    }
</style>

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Üye Yönetimi</h3>
            <p class="text-muted small mb-0">Toplam <?php echo count($uyeler); ?> kayıtlı kullanıcı listeleniyor.</p>
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
                        <th>Kullanıcı</th>
                        <th>E-Posta</th>
                        <th>Kayıt Tarihi</th>
                        <th>Rol</th>
                        <th>Durum</th>
                        <th class="text-end">İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($uyeler as $uye): ?>
                        <tr>
                            <td class="fw-bold text-dark">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary fw-bold me-3"
                                        style="width: 40px; height: 40px;">
                                        <?php echo strtoupper(substr($uye['kullanici_adi'], 0, 1)); ?>
                                    </div>
                                    <?php echo htmlspecialchars($uye['kullanici_adi']); ?>
                                </div>
                            </td>
                            <td class="text-muted"><?php echo htmlspecialchars($uye['email']); ?></td>
                            <td><?php echo date("d.m.Y", strtotime($uye['kayit_tarihi'])); ?></td>

                            <td>
                                <?php if ($ADMIN_ROLU == 'admin' && $uye['rol'] != 'admin'): ?>
                                    <form action="admin_islem.php" method="POST">
                                        <input type="hidden" name="action" value="change_role">
                                        <input type="hidden" name="user_id" value="<?php echo $uye['id']; ?>">
                                        <select name="new_role"
                                            class="form-select form-select-sm border-0 bg-light text-secondary fw-bold"
                                            onchange="this.form.submit()" style="width: auto; cursor: pointer;">
                                            <option value="uye" <?php if ($uye['rol'] == 'uye')
                                                echo 'selected'; ?>>Üye</option>
                                            <option value="alt_yonetici" <?php if ($uye['rol'] == 'alt_yonetici')
                                                echo 'selected'; ?>>Alt Yön.</option>
                                        </select>
                                    </form>
                                <?php else: ?>
                                    <span class="badge-soft-role"><?php echo ucfirst($uye['rol']); ?></span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php if ($uye['aktif']): ?>
                                    <span class="badge-soft-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge-soft-danger">Yasaklı</span>
                                <?php endif; ?>
                            </td>

                            <td class="text-end">
                                <?php
                                $yetkili_mi = ($ADMIN_ROLU == 'alt_yonetici' && ($uye['rol'] == 'admin' || $uye['rol'] == 'alt_yonetici'));
                                $admin_mi = ($ADMIN_ROLU == 'admin' && $uye['rol'] == 'admin');

                                if ($yetkili_mi || $admin_mi) {
                                    echo '<span class="text-muted small fst-italic">Yetki Yok</span>';
                                } else {
                                    if ($uye['aktif']) {
                                        echo '<a href="admin_islem.php?action=set_status&status=0&user_id=' . $uye['id'] . '" class="btn-action-icon btn-soft-red" title="Yasakla"><i class="bi bi-slash-circle"></i></a>';
                                    } else {
                                        echo '<a href="admin_islem.php?action=set_status&status=1&user_id=' . $uye['id'] . '" class="btn-action-icon btn-soft-green" title="Aktif Et"><i class="bi bi-check-lg"></i></a>';
                                    }
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once '../footer.php'; ?>