<?php
require_once 'admin_kontrol.php';
require_once '../header.php';
require_once '../db.php';

$stmt = $pdo->query("SELECT COUNT(*) FROM kullanicilar");
$uye_sayisi = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM mesajlar");
$mesaj_sayisi = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM arkadasliklar WHERE durum = 'bekliyor'");
$bekleyen_sayisi = $stmt->fetchColumn();

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM islem_loglari");
    $log_sayisi = $stmt->fetchColumn();
} catch (Exception $e) {
    $log_sayisi = 0;
}

?>

<style>
    body {
        background-color: #f8faff;
    }

    .admin-card {
        background: #fff;
        border: none;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-decoration: none;
    }

    .admin-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }

    .icon-box {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .theme-blue {
        background-color: #eff6ff;
        color: #3b82f6;
    }

    .theme-orange {
        background-color: #fff7ed;
        color: #f97316;
    }

    .theme-green {
        background-color: #f0fdf4;
        color: #22c55e;
    }

    .theme-dark {
        background-color: #f1f5f9;
        color: #475569;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 1.75rem;
        color: #1e293b;
        font-weight: 700;
        line-height: 1.2;
    }

    .quick-link {
        display: flex;
        align-items: center;
        padding: 15px;
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 12px;
        color: #475569;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s;
        margin-bottom: 10px;
    }

    .quick-link:hover {
        background: #fff;
        border-color: #e2e8f0;
        color: #3b82f6;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
    }

    .quick-link i {
        font-size: 1.2rem;
        margin-right: 12px;
    }
</style>

<div class="container py-4">

    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h3 class="fw-bold text-dark mb-1">Yönetim Paneli</h3>
            <p class="text-muted small mb-0">Sistem durum özeti ve yönetim araçları.</p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <span class="badge bg-dark px-3 py-2 rounded-pill fw-normal">
                <i class="bi bi-shield-lock-fill me-1"></i> <?php echo ucfirst($ADMIN_ROLU); ?> Yetkisi
            </span>
        </div>
    </div>

    <div class="row g-3 mb-5">
        <div class="col-xl-3 col-lg-3 col-md-6">
            <a href="admin_uyeler.php" class="admin-card">
                <div>
                    <div class="stat-label">Toplam Üye</div>
                    <div class="stat-value"><?php echo $uye_sayisi; ?></div>
                </div>
                <div class="icon-box theme-blue">
                    <i class="bi bi-people-fill"></i>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-lg-3 col-md-6">
            <div class="admin-card" style="cursor: default;">
                <div>
                    <div class="stat-label">Bekleyen İstek</div>
                    <div class="stat-value"><?php echo $bekleyen_sayisi; ?></div>
                </div>
                <div class="icon-box theme-orange">
                    <i class="bi bi-hourglass-split"></i>
                </div>
            </div>
        </div>

        <?php if ($ADMIN_ROLU == 'admin'): ?>
            <div class="col-xl-3 col-lg-3 col-md-6">
                <a href="admin_mesajlar.php" class="admin-card">
                    <div>
                        <div class="stat-label">Mesaj Trafiği</div>
                        <div class="stat-value"><?php echo $mesaj_sayisi; ?></div>
                    </div>
                    <div class="icon-box theme-green">
                        <i class="bi bi-chat-dots-fill"></i>
                    </div>
                </a>
            </div>

            <div class="col-xl-3 col-lg-3 col-md-6">
                <a href="admin_log.php" class="admin-card">
                    <div>
                        <div class="stat-label">Sistem Kaydı</div>
                        <div class="stat-value"><?php echo $log_sayisi; ?></div>
                    </div>
                    <div class="icon-box theme-dark">
                        <i class="bi bi-terminal"></i>
                    </div>
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <h6 class="fw-bold text-muted text-uppercase small mb-3 ls-1">Hızlı Erişim</h6>

            <a href="admin_uyeler.php" class="quick-link">
                <i class="bi bi-person-gear text-primary"></i>
                <span>Üye Listesi ve Yetkilendirme</span>
                <i class="bi bi-chevron-right ms-auto text-muted small"></i>
            </a>

            <?php if ($ADMIN_ROLU == 'admin'): ?>
                <a href="admin_mesajlar.php" class="quick-link">
                    <i class="bi bi-envelope-paper text-success"></i>
                    <span>Tüm Mesajları Denetle</span>
                    <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                </a>

                <a href="admin_log.php" class="quick-link">
                    <i class="bi bi-file-earmark-code text-dark"></i>
                    <span>Sistem Loglarını İncele</span>
                    <i class="bi bi-chevron-right ms-auto text-muted small"></i>
                </a>
            <?php endif; ?>

            <a href="../anasayfa.php" class="quick-link text-secondary">
                <i class="bi bi-box-arrow-up-right"></i>
                <span>Site Ana Sayfasına Dön</span>
            </a>
        </div>

        <div class="col-lg-6">
            <div class="p-4 bg-white rounded-4 border border-light h-100 shadow-sm">
                <h6 class="fw-bold text-dark mb-3"><i class="bi bi-info-circle me-2"></i>Admin Notları</h6>
                <ul class="text-muted small ps-3 mb-0" style="line-height: 1.8;">
                    <li><strong>Üye Yönetimi:</strong> Kullanıcıları yasaklamak için "Üye Listesi"ne gidin. Kırmızı
                        buton pasif yapar.</li>
                    <li><strong>Mesaj Silme:</strong> Uygunsuz mesajları "Mesaj Trafiği" sayfasından kalıcı olarak
                        silebilirsiniz.</li>
                    <li><strong>Loglar:</strong> Yapılan her kritik işlem (silme, banlama) loglarda kayıtlıdır.</li>
                </ul>
            </div>
        </div>
    </div>

</div>

<?php require_once '../footer.php'; ?>