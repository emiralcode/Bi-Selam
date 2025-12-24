<?php
require_once 'header.php';
require_once 'db.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit();
}

$me_id = $_SESSION['kullanici_id'];

$sql_istek = "SELECT a.id as arkadaslik_id, k.id as kullanici_id, k.kullanici_adi, 
              p.ad_soyad, p.profil_fotografi, p.sehir, p.bio 
              FROM arkadasliklar a
              JOIN kullanicilar k ON a.kullanici1_id = k.id
              JOIN profiller p ON k.id = p.kullanici_id
              WHERE a.kullanici2_id = ? AND a.durum = 'bekliyor'";
$stmt_istek = $pdo->prepare($sql_istek);
$stmt_istek->execute([$me_id]);
$istekler = $stmt_istek->fetchAll(PDO::FETCH_ASSOC);

$sql_arkadas = "SELECT a.id as arkadaslik_id, k.id as kullanici_id, k.kullanici_adi, 
                p.ad_soyad, p.profil_fotografi, p.sehir, p.bio 
                FROM arkadasliklar a
                JOIN kullanicilar k ON (CASE WHEN a.kullanici1_id = ? THEN a.kullanici2_id ELSE a.kullanici1_id END) = k.id
                JOIN profiller p ON k.id = p.kullanici_id
                WHERE (a.kullanici1_id = ? OR a.kullanici2_id = ?) AND a.durum = 'onaylandi'";
$stmt_arkadas = $pdo->prepare($sql_arkadas);
$stmt_arkadas->execute([$me_id, $me_id, $me_id]);
$arkadaslar = $stmt_arkadas->fetchAll(PDO::FETCH_ASSOC);

?>

<style>
    body {
        background-color: #f8faff;
    }
    
    .request-card {
        background: #fff;
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 5px 20px rgba(112, 144, 176, 0.08);
        border-left: 5px solid #6366f1; 
        transition: transform 0.2s;
        margin-bottom: 20px;
    }
    .request-card:hover {
        transform: translateX(5px);
    }

    .friend-card {
        background: #fff;
        border: none;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(112, 144, 176, 0.08);
        transition: all 0.3s ease;
        position: relative;
    }
    .friend-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(112, 144, 176, 0.15);
    }
    
    .friend-header {
        height: 100px;
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
    }
    .friend-avatar {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        display: block; /* Added for centering fix if needed */
        margin: -45px auto 0 auto; /* Centered horizontally and negative top margin */
        object-fit: cover;
        border: 4px solid #fff;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .btn-accept {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        color: white;
        border: none;
        border-radius: 50px;
        padding: 8px 25px;
        font-weight: 500;
        transition: 0.3s;
    }
    .btn-accept:hover { opacity: 0.9; color: white; box-shadow: 0 5px 15px rgba(79, 70, 229, 0.3); }

    .btn-reject {
        background: #fff;
        color: #ef4444;
        border: 1px solid #fee2e2;
        border-radius: 50px;
        padding: 8px 25px;
        font-weight: 500;
        transition: 0.3s;
    }
    .btn-reject:hover { background: #fee2e2; color: #dc2626; }

    .btn-message-soft {
        background-color: #f0fdf4;
        color: #16a34a;
        border: none;
        border-radius: 12px;
        padding: 10px;
        width: 100%;
        font-weight: 600;
        transition: 0.2s;
    }
    .btn-message-soft:hover { background-color: #16a34a; color: white; }

    .btn-remove-soft {
        background-color: #fef2f2;
        color: #ef4444;
        border: none;
        border-radius: 12px;
        padding: 10px;
        width: 100%;
        font-weight: 600;
        transition: 0.2s;
    }
    .btn-remove-soft:hover { background-color: #ef4444; color: white; }

    .city-badge {
        background: #f8fafc;
        color: #64748b;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        border: 1px solid #e2e8f0;
    }
</style>

<div class="container py-5">
    
    <div class="row mb-5">
        <div class="col-12 text-center">
            <h2 class="fw-bold text-dark mb-2">Sosyal Çevren</h2>
            <p class="text-muted">Bağlantılarını yönet ve yeni arkadaşlık isteklerini kontrol et.</p>
        </div>
    </div>

    <?php if (!empty($istekler)): ?>
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8">
                <h5 class="fw-bold text-primary mb-3"><i class="bi bi-bell-fill"></i> Bekleyen İstekler (<?php echo count($istekler); ?>)</h5>
                
                <?php foreach ($istekler as $istek): ?>
                <div class="request-card d-flex align-items-center flex-wrap justify-content-between">
                    <div class="d-flex align-items-center mb-3 mb-md-0">
                        <img src="uploads/<?php echo htmlspecialchars($istek['profil_fotografi']); ?>" 
                             class="rounded-circle shadow-sm" style="width: 60px; height: 60px; object-fit: cover;">
                        <div class="ms-3">
                            <h5 class="fw-bold text-dark mb-0">
                                <?php echo htmlspecialchars($istek['ad_soyad'] ?: $istek['kullanici_adi']); ?>
                            </h5>
                            <small class="text-muted">Seni arkadaş olarak eklemek istiyor.</small>
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <a href="arkadas_islem.php?action=accept&id=<?php echo $istek['arkadaslik_id']; ?>" class="btn-accept text-decoration-none">
                            <i class="bi bi-check-lg"></i> Kabul Et
                        </a>
                        <a href="arkadas_islem.php?action=reject&id=<?php echo $istek['arkadaslik_id']; ?>" class="btn-reject text-decoration-none" onclick="return confirm('İsteği reddetmek istiyor musunuz?');">
                            <i class="bi bi-x-lg"></i> Reddet
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
                
            </div>
        </div>
        <hr class="text-muted opacity-25 my-5">
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="fw-bold text-dark m-0"><i class="bi bi-people-fill text-primary opacity-50"></i> Arkadaşlarım</h4>
            <span class="badge bg-light text-secondary rounded-pill border px-3 py-2"><?php echo count($arkadaslar); ?> Kişi</span>
        </div>
    </div>

    <?php if (empty($arkadaslar)): ?>
        <div class="text-center py-5 bg-white rounded-4 shadow-sm">
            <div class="mb-3 text-muted opacity-50"><i class="bi bi-emoji-neutral display-1"></i></div>
            <h5 class="fw-light text-secondary">Henüz arkadaş listen boş.</h5>
            <p class="text-muted small">Yeni insanlarla tanışmak için keşfetmeye başla!</p>
            <a href="uyeler.php" class="btn btn-outline-primary rounded-pill px-4 mt-2">İnsanları Keşfet</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($arkadaslar as $arkadas): ?>
            <div class="col-lg-3 col-md-6 col-sm-6">
                
                <div class="friend-card h-100 text-center pb-4">
                    <div class="friend-header"></div>
                    
                    <div class="position-relative d-inline-block">
                        <img src="uploads/<?php echo htmlspecialchars($arkadas['profil_fotografi']); ?>" class="friend-avatar" alt="Profil">
                    </div>
                    
                    <div class="card-body px-4 pt-2">
                        <h5 class="fw-bold text-dark mb-1">
                            <?php echo htmlspecialchars($arkadas['ad_soyad'] ?: $arkadas['kullanici_adi']); ?>
                        </h5>
                        
                        <div class="mb-3">
                            <span class="city-badge">
                                <i class="bi bi-geo-alt-fill text-danger opacity-75"></i> 
                                <?php echo $arkadas['sehir'] ? htmlspecialchars($arkadas['sehir']) : 'Dünya'; ?>
                            </span>
                        </div>
                        
                        <div class="row g-2 mt-4">
                            <div class="col-8">
                                <a href="konusma.php?hedef_id=<?php echo $arkadas['kullanici_id']; ?>" class="btn-message-soft text-decoration-none">
                                    <i class="bi bi-chat-text-fill"></i> Mesaj
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="arkadas_islem.php?action=remove&id=<?php echo $arkadas['arkadaslik_id']; ?>" 
                                   class="btn-remove-soft text-decoration-none" 
                                   title="Arkadaşlıktan Çıkar"
                                   onclick="return confirm('Bu kişiyi arkadaş listenizden çıkarmak istediğinize emin misiniz?');">
                                    <i class="bi bi-person-x-fill"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php require_once 'footer.php'; ?>