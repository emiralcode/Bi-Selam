<?php
require_once 'header.php';
require_once 'db.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit();
}

$me_id = $_SESSION['kullanici_id'];

$sql = "
SELECT 
    m.id as mesaj_id,
    m.icerik,
    m.tarih,
    m.okundu_mu,
    m.gonderen_id,
    k.id as partner_id,
    k.kullanici_adi,
    p.ad_soyad,
    p.profil_fotografi
FROM mesajlar m
JOIN (
    SELECT MAX(id) as max_id
    FROM mesajlar
    WHERE gonderen_id = ? OR alan_id = ?
    GROUP BY CASE 
        WHEN gonderen_id = ? THEN alan_id 
        ELSE gonderen_id 
    END
) latest ON m.id = latest.max_id
JOIN kullanicilar k ON k.id = (CASE WHEN m.gonderen_id = ? THEN m.alan_id ELSE m.gonderen_id END)
JOIN profiller p ON k.id = p.kullanici_id
ORDER BY m.tarih DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$me_id, $me_id, $me_id, $me_id]);
$konusmalar = $stmt->fetchAll(PDO::FETCH_ASSOC);

function tarihFormatla($tarih)
{
    $zaman = strtotime($tarih);
    $bugun = strtotime('today');
    $dun = strtotime('yesterday');

    if ($zaman >= $bugun) {
        return date("H:i", $zaman);
    } elseif ($zaman >= $dun) {
        return "Dün";
    } else {
        return date("d.m.Y", $zaman);
    }
}
?>

<style>
    body {
        background-color: #fcfdfe;
        font-family: 'Segoe UI', sans-serif;
    }

    .inbox-container {
        max-width: 800px;
        margin: 0 auto;
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        border: 1px solid #f1f5f9;
    }

    .message-item {
        display: flex;
        align-items: center;
        padding: 20px 25px;
        border-bottom: 1px solid #f8fafc;
        transition: all 0.2s ease;
        text-decoration: none;
        color: inherit;
        position: relative;
    }

    .message-item:last-child {
        border-bottom: none;
    }

    .message-item:hover {
        background-color: #f8fafc;
    }

    .message-item.unread {
        background-color: #f0f7ff;
    }

    .message-item.unread:hover {
        background-color: #eef5ff;
    }

    .message-item.unread .preview-text {
        font-weight: 700;
        color: #1e293b;
    }

    .unread-dot {
        width: 10px;
        height: 10px;
        background-color: #3b82f6;
        border-radius: 50%;
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
    }

    .avatar-area {
        position: relative;
        margin-right: 20px;
    }

    .msg-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #f1f5f9;
    }

    .msg-content {
        flex-grow: 1;
        min-width: 0;
    }

    .partner-name {
        font-size: 1.05rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 4px;
    }

    .preview-text {
        color: #64748b;
        font-size: 0.95rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .msg-meta {
        text-align: right;
        min-width: 70px;
        margin-left: 15px;
    }

    .msg-time {
        font-size: 0.8rem;
        color: #94a3b8;
        display: block;
        margin-bottom: 5px;
    }

    .sent-icon {
        font-size: 0.85rem;
        color: #cbd5e1;
    }

    .sent-icon.read {
        color: #3b82f6;
    }

    .btn-compose {
        background-color: #eff6ff;
        color: #3b82f6;
        border: none;
        border-radius: 50px;
        padding: 10px 25px;
        font-weight: 600;
        transition: 0.2s;
    }

    .btn-compose:hover {
        background-color: #3b82f6;
        color: white;
    }
</style>

<div class="container py-5">

    <div class="row mb-4 align-items-end">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-1">Mesajlar</h2>
            <p class="text-muted small mb-0">Son konuşmaların</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <a href="arkadaslarim.php" class="btn-compose text-decoration-none">
                <i class="bi bi-pencil-square me-2"></i> Yeni Mesaj
            </a>
        </div>
    </div>

    <div class="inbox-container">

        <?php if (empty($konusmalar)): ?>
            <div class="text-center py-5">
                <div class="mb-3 opacity-25">
                    <i class="bi bi-chat-square-quote display-1 text-secondary"></i>
                </div>
                <h5 class="fw-light text-secondary">Henüz bir konuşma yok.</h5>
                <p class="text-muted small">Arkadaşlarına bir "Merhaba" diyerek başla.</p>
                <a href="arkadaslarim.php" class="btn btn-link text-decoration-none">Arkadaşlarını Gör</a>
            </div>
        <?php else: ?>

            <?php foreach ($konusmalar as $konusma): ?>
                <?php
                $okunmamis = ($konusma['gonderen_id'] != $me_id && $konusma['okundu_mu'] == 0);
                $rowClass = $okunmamis ? 'unread' : '';

                $ben_attim = ($konusma['gonderen_id'] == $me_id);
                $partner_ad = htmlspecialchars($konusma['ad_soyad'] ?: $konusma['kullanici_adi']);
                ?>

                <a href="konusma.php?hedef_id=<?php echo $konusma['partner_id']; ?>"
                    class="message-item <?php echo $rowClass; ?>">

                    <?php if ($okunmamis): ?>
                        <div class="unread-dot"></div>
                    <?php endif; ?>

                    <div class="avatar-area">
                        <img src="uploads/<?php echo htmlspecialchars($konusma['profil_fotografi']); ?>" class="msg-avatar"
                            alt="Profil">
                    </div>

                    <div class="msg-content">
                        <div class="partner-name">
                            <?php echo $partner_ad; ?>
                        </div>
                        <div class="preview-text">
                            <?php if ($ben_attim): ?>
                                <span class="text-muted me-1">Siz:</span>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($konusma['icerik']); ?>
                        </div>
                    </div>

                    <div class="msg-meta">
                        <span class="msg-time"><?php echo tarihFormatla($konusma['tarih']); ?></span>

                        <?php if ($ben_attim): ?>
                            <?php if ($konusma['okundu_mu']): ?>
                                <i class="bi bi-check2-all sent-icon read" title="Görüldü"></i>
                            <?php else: ?>
                                <i class="bi bi-check2 sent-icon" title="İletildi"></i>
                            <?php endif; ?>
                        <?php elseif ($okunmamis): ?>
                        <?php endif; ?>
                    </div>

                </a>
            <?php endforeach; ?>

        <?php endif; ?>

    </div>
</div>

<?php require_once 'footer.php'; ?>