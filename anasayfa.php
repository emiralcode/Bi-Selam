<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit();
}

require_once 'header.php';
require_once 'db.php';

$me_id = $_SESSION['kullanici_id'];
$me_ad = $_SESSION['kullanici_adi'];

$stmt = $pdo->prepare("SELECT COUNT(*) FROM mesajlar WHERE alan_id = ? AND okundu_mu = 0");
$stmt->execute([$me_id]);
$okunmamis_mesaj = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM arkadasliklar WHERE kullanici2_id = ? AND durum = 'bekliyor'");
$stmt->execute([$me_id]);
$bekleyen_istek = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM arkadasliklar WHERE (kullanici1_id = ? OR kullanici2_id = ?) AND durum = 'onaylandi'");
$stmt->execute([$me_id, $me_id]);
$toplam_arkadas = $stmt->fetchColumn();

$sql_yeni = "SELECT p.kullanici_id, p.ad_soyad, p.profil_fotografi, p.sehir, k.kullanici_adi 
             FROM profiller p 
             JOIN kullanicilar k ON p.kullanici_id = k.id 
             WHERE p.kullanici_id != ? 
             ORDER BY k.kayit_tarihi DESC LIMIT 4";
$stmt = $pdo->prepare($sql_yeni);
$stmt->execute([$me_id]);
$yeni_uyeler = $stmt->fetchAll(PDO::FETCH_ASSOC);

$saat = date("H");
if ($saat < 12)
    $selam = "Günaydın, harika bir sabah!";
elseif ($saat < 18)
    $selam = "Tünaydın, günün nasıl geçiyor?";
else
    $selam = "İyi akşamlar, hoş geldin";
?>

<style>
    body {
        background-color: #f8faff;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .soft-card {
        background: #ffffff;
        border: none;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(112, 144, 176, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }

    .soft-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 50px rgba(112, 144, 176, 0.12);
    }

    .welcome-card {
        background: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
        color: #fff;
        border: none;
        border-radius: 24px;
        position: relative;
        padding: 40px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(161, 196, 253, 0.4);
    }

    .welcome-text h2 {
        font-weight: 700;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .stat-box {
        padding: 25px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
    }

    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        transition: transform 0.3s;
    }

    .icon-msg {
        background-color: #eef2ff;
        color: #6366f1;
    }

    .icon-req {
        background-color: #fff7ed;
        color: #f97316;
    }

    .icon-frn {
        background-color: #f0fdf4;
        color: #22c55e;
    }

    .soft-card:hover .icon-circle {
        transform: scale(1.1) rotate(5deg);
    }

    .user-item {
        padding: 15px;
        border-radius: 16px;
        transition: background-color 0.2s;
        margin-bottom: 10px;
        background-color: transparent;
    }

    .user-item:hover {
        background-color: #f8faff;
    }

    .avatar-circle {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .btn-action-soft {
        background-color: #fff;
        color: #6366f1;
        border: 1px solid #e0e7ff;
        border-radius: 50px;
        padding: 8px 20px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-action-soft:hover {
        background-color: #6366f1;
        color: #fff;
        border-color: #6366f1;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }

    .menu-link {
        display: block;
        padding: 18px 20px;
        border-radius: 16px;
        color: #555;
        text-decoration: none;
        transition: all 0.2s;
        font-weight: 500;
        margin-bottom: 8px;
        background-color: #fff;
        border: 1px solid transparent;
    }

    .menu-link:hover {
        background-color: #f1f5f9;
        color: #333;
        transform: translateX(5px);
    }

    .menu-icon {
        width: 35px;
        text-align: center;
        display: inline-block;
        margin-right: 10px;
        font-size: 1.2rem;
    }
</style>

<div class="container py-5">

    <div class="row mb-5">
        <div class="col-12">
            <div class="welcome-card d-flex align-items-center justify-content-between flex-wrap">
                <div class="welcome-text">
                    <h2 class="mb-2"><?php echo $selam; ?> <strong><?php echo htmlspecialchars($me_ad); ?></strong></h2>
                    <p class="mb-0 text-white opacity-75 fs-5">Bugün kendini nasıl hissediyorsun?</p>
                </div>
                <div class="d-none d-md-block">
                    <a href="profil_duzenle.php"
                        class="btn btn-light rounded-pill px-4 py-2 text-primary fw-bold shadow-sm"
                        style="opacity: 0.9;">
                        <i class="bi bi-gear-fill me-2"></i> Ayarlar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-5">

        <div class="col-md-4">
            <a href="mesajlarim.php" class="text-decoration-none">
                <div class="soft-card stat-box">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-2 ls-1">Gelen Kutusu</h6>
                        <h2 class="text-dark fw-bold mb-0 display-6"><?php echo $okunmamis_mesaj; ?></h2>
                        <?php if ($okunmamis_mesaj > 0): ?>
                            <small class="text-danger fw-bold mt-1 d-block">Okunmamış mesajın var</small>
                        <?php endif; ?>
                    </div>
                    <div class="icon-circle icon-msg">
                        <i class="bi bi-chat-dots-fill"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="arkadaslarim.php" class="text-decoration-none">
                <div class="soft-card stat-box">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-2 ls-1">İstekler</h6>
                        <h2 class="text-dark fw-bold mb-0 display-6"><?php echo $bekleyen_istek; ?></h2>
                        <?php if ($bekleyen_istek > 0): ?>
                            <small class="text-warning fw-bold mt-1 d-block">Onayını bekleyenler</small>
                        <?php endif; ?>
                    </div>
                    <div class="icon-circle icon-req">
                        <i class="bi bi-person-hearts"></i>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="arkadaslarim.php" class="text-decoration-none">
                <div class="soft-card stat-box">
                    <div>
                        <h6 class="text-muted text-uppercase small fw-bold mb-2 ls-1">Çevren</h6>
                        <h2 class="text-dark fw-bold mb-0 display-6"><?php echo $toplam_arkadas; ?></h2>
                        <small class="text-muted mt-1 d-block">Toplam Arkadaş</small>
                    </div>
                    <div class="icon-circle icon-frn">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4">

        <div class="col-lg-8">
            <div class="soft-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-dark m-0">✨ Aramıza Yeni Katılanlar</h5>
                    <a href="uyeler.php" class="text-decoration-none small text-primary fw-bold">Hepsini Gör</a>
                </div>

                <?php if (empty($yeni_uyeler)): ?>
                    <div class="text-center py-5 text-muted bg-light rounded-3">
                        <i class="bi bi-people display-4 opacity-25"></i>
                        <p class="mt-2 mb-0">Henüz yeni üye yok.</p>
                    </div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-2">
                        <?php foreach ($yeni_uyeler as $uye): ?>
                            <div class="user-item d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <img src="uploads/<?php echo htmlspecialchars($uye['profil_fotografi']); ?>"
                                        class="avatar-circle" alt="Profil">
                                    <div class="ms-3">
                                        <h6 class="fw-bold text-dark mb-0">
                                            <?php echo htmlspecialchars($uye['ad_soyad'] ?: $uye['kullanici_adi']); ?>
                                        </h6>
                                        <small class="text-secondary opacity-75">
                                            <?php echo $uye['sehir'] ? htmlspecialchars($uye['sehir']) : 'Aramıza yeni katıldı'; ?>
                                        </small>
                                    </div>
                                </div>
                                <a href="arkadas_islem.php?action=add&id=<?php echo $uye['kullanici_id']; ?>"
                                    class="btn-action-soft text-decoration-none">
                                    Takip Et
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="soft-card p-4 h-100">
                <h5 class="fw-bold text-dark mb-4">⚡ Hızlı Menü</h5>

                <div class="d-flex flex-column">
                    <a href="uyeler.php" class="menu-link">
                        <span class="menu-icon text-primary"><i class="bi bi-compass"></i></span>
                        Keşfet
                    </a>

                    <a href="profil_duzenle.php" class="menu-link">
                        <span class="menu-icon text-info"><i class="bi bi-camera"></i></span>
                        Profil Fotoğrafı
                    </a>

                    <a href="mesajlarim.php" class="menu-link">
                        <span class="menu-icon text-success"><i class="bi bi-envelope-open-heart"></i></span>
                        Mesajlarım
                    </a>

                    <div class="my-2 border-bottom opacity-25"></div> <a href="cikis_yap.php"
                        class="menu-link text-danger">
                        <span class="menu-icon"><i class="bi bi-box-arrow-right"></i></span>
                        Güvenli Çıkış
                    </a>
                </div>

                <div class="mt-4 p-3 bg-light rounded-4 text-center">
                    <small class="text-muted fst-italic">"İletişim, kalpleri birbirine bağlayan köprüdür."</small>
                </div>
            </div>
        </div>
    </div>

</div>

<?php require_once 'footer.php'; ?>