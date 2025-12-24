<?php
require_once 'header.php';
require_once 'db.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit();
}

$me_id = $_SESSION['kullanici_id'];

$arama_terimi = "";
$sql_ek = "";
$params = [$me_id, $me_id, $me_id];

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $arama_terimi = trim($_GET['q']);
    $sql_ek = " AND (p.ad_soyad LIKE ? OR k.kullanici_adi LIKE ? OR p.sehir LIKE ?)";
    $term = "%" . $arama_terimi . "%";
    $params[] = $term;
    $params[] = $term;
    $params[] = $term;
}

$sql = "SELECT p.kullanici_id, p.ad_soyad, p.sehir, p.bio, p.profil_fotografi, k.kullanici_adi, k.kayit_tarihi,
               a.id as arkadaslik_id, a.durum, a.kullanici1_id as istek_gonderen_id
        FROM profiller p
        JOIN kullanicilar k ON p.kullanici_id = k.id
        LEFT JOIN arkadasliklar a ON 
            (a.kullanici1_id = ? AND a.kullanici2_id = p.kullanici_id) OR 
            (a.kullanici1_id = p.kullanici_id AND a.kullanici2_id = ?)
        WHERE p.kullanici_id != ? $sql_ek
        ORDER BY k.kayit_tarihi DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$uyeler = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    body {
        background-color: #f9fbfd;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .member-card {
        border: none;
        border-radius: 20px;
        background: #fff;
        box-shadow: 0 10px 25px rgba(112, 144, 176, 0.08);
        transition: all 0.4s ease;
        animation: fadeInUp 0.6s ease-out forwards;
        overflow: hidden;
        position: relative;
    }

    .member-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(112, 144, 176, 0.15);
    }

    .card-banner {
        height: 110px;
        background: linear-gradient(120deg, #a1c4fd 0%, #c2e9fb 100%);
        opacity: 0.8;
    }

    .profile-img-container {
        margin-top: -55px;
        text-align: center;
        position: relative;
    }

    .profile-img-circle {
        width: 110px;
        height: 110px;
        object-fit: cover;
        border: 5px solid #fff;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease;
    }

    .member-card:hover .profile-img-circle {
        transform: scale(1.05);
    }

    .btn-soft-primary {
        background-color: #eef2ff;
        color: #4f46e5;
        border: none;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-soft-primary:hover {
        background-color: #4f46e5;
        color: #fff;
    }

    .btn-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        font-weight: 500;
    }

    .btn-gradient:hover {
        opacity: 0.9;
        color: white;
    }

    .search-container {
        background: white;
        padding: 5px;
        border-radius: 50px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    }

    .search-input {
        border: none;
        background: transparent;
        font-size: 0.95rem;
    }

    .search-input:focus {
        box-shadow: none;
        background: transparent;
    }
</style>

<div class="container py-5">

    <div class="row justify-content-center mb-5 text-center">
        <div class="col-lg-8">
            <h2 class="fw-bold text-dark mb-2" style="font-family: 'Segoe UI', sans-serif;">Topluluğu Keşfet</h2>
            <p class="text-muted mb-4">Yeni arkadaşlıklar kurmak için harika bir gün.</p>

            <form action="" method="GET" class="search-container d-flex mx-auto" style="max-width: 500px;">
                <input class="form-control search-input rounded-pill ps-4" type="search" name="q"
                    placeholder="Kimi arıyorsunuz? (İsim, Şehir...)"
                    value="<?php echo htmlspecialchars($arama_terimi); ?>">
                <button class="btn btn-primary rounded-pill px-4 m-1" type="submit" style="border-radius: 30px;">
                    <i class="bi bi-search"></i>
                </button>
            </form>

            <?php if ($arama_terimi): ?>
                <div class="mt-2">
                    <a href="uyeler.php" class="text-decoration-none text-muted small"><i class="bi bi-x-circle"></i>
                        Aramayı Temizle</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (empty($uyeler)): ?>
        <div class="text-center py-5">
            <div class="mb-3 text-muted" style="opacity: 0.5;">
                <i class="bi bi-people display-1"></i>
            </div>
            <h5 class="fw-light text-secondary">Aradığınız kriterlere uygun kimse bulunamadı.</h5>
            <a href="uyeler.php" class="btn btn-link text-decoration-none">Listeyi Sıfırla</a>
        </div>
    <?php endif; ?>

    <div class="row g-4"> <?php foreach ($uyeler as $uye): ?>
            <div class="col-lg-3 col-md-6">

                <div class="card member-card h-100">
                    <div class="card-banner"></div>

                    <div class="card-body text-center pt-0 d-flex flex-column">
                        <div class="profile-img-container mb-2">
                            <img src="uploads/<?php echo htmlspecialchars($uye['profil_fotografi']); ?>"
                                class="rounded-circle profile-img-circle" alt="Profil">
                        </div>

                        <h5 class="card-title fw-bold mb-1 text-dark">
                            <?php echo htmlspecialchars($uye['ad_soyad'] ?: $uye['kullanici_adi']); ?>
                        </h5>

                        <div class="mb-3">
                            <?php if ($uye['sehir']): ?>
                                <small class="text-muted"><i class="bi bi-geo-alt text-danger opacity-75"></i>
                                    <?php echo htmlspecialchars($uye['sehir']); ?></small>
                            <?php else: ?>
                                <small class="text-muted"><i class="bi bi-globe-americas"></i> Dünya</small>
                            <?php endif; ?>
                        </div>

                        <p class="card-text text-secondary small px-2 flex-grow-1"
                            style="font-size: 0.9rem; line-height: 1.5;">
                            <?php
                            $bio = htmlspecialchars($uye['bio'] ?? '');
                            if (empty($bio)) {
                                echo '<span class="text-muted fst-italic opacity-50">Henüz bir şey yazmamış.</span>';
                            } else {
                                echo (mb_strlen($bio) > 60) ? mb_substr($bio, 0, 60) . '...' : $bio;
                            }
                            ?>
                        </p>

                        <div class="mt-4">
                            <?php
                            if ($uye['arkadaslik_id']) {
                                if ($uye['durum'] == 'onaylandi') {
                                    echo '<a href="konusma.php?hedef_id=' . $uye['kullanici_id'] . '" class="btn btn-soft-primary w-100 rounded-pill py-2"><i class="bi bi-chat-text"></i> Mesaj Gönder</a>';
                                } elseif ($uye['durum'] == 'bekliyor') {
                                    if ($uye['istek_gonderen_id'] == $me_id) {
                                        echo '<button class="btn btn-light text-muted w-100 rounded-pill py-2 disabled border"><i class="bi bi-hourglass"></i> Bekleniyor...</button>';
                                    } else {
                                        echo '<a href="arkadas_islem.php?action=accept&id=' . $uye['arkadaslik_id'] . '" class="btn btn-gradient w-100 rounded-pill py-2 shadow-sm"><i class="bi bi-check2"></i> İsteği Kabul Et</a>';
                                    }
                                }
                            } else {
                                echo '<a href="arkadas_islem.php?action=add&id=' . $uye['kullanici_id'] . '" class="btn btn-gradient w-100 rounded-pill py-2 shadow-sm"><i class="bi bi-person-plus"></i> Takip Et</a>';
                            }
                            ?>
                        </div>
                    </div>
                </div>

            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>