<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

$okunmamis_mesaj = 0;
$bekleyen_istek = 0;
$kullanici_foto = 'default.jpg';
$kullanici_rol = 'uye';

if (isset($_SESSION['kullanici_id'])) {
    $my_id = $_SESSION['kullanici_id'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM mesajlar WHERE alan_id = ? AND okundu_mu = 0");
    $stmt->execute([$my_id]);
    $okunmamis_mesaj = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM arkadasliklar WHERE kullanici2_id = ? AND durum = 'bekliyor'");
    $stmt->execute([$my_id]);
    $bekleyen_istek = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT p.profil_fotografi, k.rol FROM profiller p JOIN kullanicilar k ON p.kullanici_id = k.id WHERE k.id = ?");
    $stmt->execute([$my_id]);
    $bilgi = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($bilgi) {
        $kullanici_foto = $bilgi['profil_fotografi'];
        $kullanici_rol = $bilgi['rol'];

        $_SESSION['rol'] = $kullanici_rol; 
    }
}
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bi'Selam - Kendine Uygun İnsanları Bul!</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 16 16%22><path fill=%22%234f46e5%22 d=%22M8 13.498.496 5.569A4.5 4.5 0 0 1 6.646.646C7.205.105 7.93-.16 8.644.06c.767.237 1.45.723 1.945 1.438.243.348.423.725.53 1.12.18.665.176 1.393-.117 2.05h-.002l-.002.002-.005.005-.015.015a6.408 6.408 0 0 1-.22.208c-.167.151-.408.358-.708.618l-.004.004-.007.006-.027.023c-.312.268-.767.647-1.306 1.115l-.01.008c-.772.67-1.745 1.503-2.658 2.285a22.203 22.203 0 0 1-1.373 1.096L8 13.498Zm-3.98-3.87a1.5 1.5 0 0 0 2.122 0l.218-.215a.5.5 0 0 0-.708-.708L5.435 8.92a.5.5 0 0 0-.707.707l.293.293Z%22/></svg>">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8faff; 
            padding-top: 80px; 
        }

        .navbar-custom {
            background-color: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px); 
            box-shadow: 0 4px 20px rgba(0,0,0,0.03); 
            padding: 12px 0;
            transition: all 0.3s;
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            letter-spacing: -0.5px;
        }

        .nav-link {
            color: #64748b;
            font-weight: 500;
            margin: 0 5px;
            padding: 8px 16px !important;
            border-radius: 50px;
            transition: all 0.2s;
            position: relative; 
        }

        .nav-link:hover, .nav-link.active {
            color: #6366f1; 
            background-color: #f0f7ff;
        }
    
        .nav-link i {
            font-size: 1.1rem;
            margin-right: 5px;
            vertical-align: -2px;
        }

        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: #ef4444; 
            color: white;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 10px;
            border: 2px solid #fff; 
        }

        .user-avatar-nav {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #e2e8f0;
        }
        
        .dropdown-menu {
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 15px;
            padding: 10px;
            margin-top: 15px;
            animation: slideUp 0.3s ease;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 8px 15px;
            color: #475569;
            font-weight: 500;
        }
        .dropdown-item:hover {
            background-color: #f1f5f9;
            color: #6366f1;
        }
        .dropdown-item i {
            margin-right: 10px;
            color: #94a3b8;
        }

        .dropdown-item.admin-link {
            color: #f59e0b; 
        }
        .dropdown-item.admin-link:hover {
            background-color: #fffbeb;
        }

    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom fixed-top">
    <div class="container">
        
        <a class="navbar-brand" href="anasayfa.php">
            <i class="bi bi-balloon-heart-fill text-primary" style="-webkit-text-fill-color: initial;"></i> Bi'Selam
        </a>

        <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#anaMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="anaMenu">
            <?php if (isset($_SESSION['kullanici_id'])): ?>
                
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="anasayfa.php"><i class="bi bi-house-door"></i> Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="uyeler.php"><i class="bi bi-compass"></i> Keşfet</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="arkadaslarim.php">
                            <i class="bi bi-people"></i> Arkadaşlar
                            <?php if ($bekleyen_istek > 0): ?>
                                <span class="notification-badge"><?php echo $bekleyen_istek; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="mesajlarim.php">
                            <i class="bi bi-chat-dots"></i> Mesajlar
                            <?php if ($okunmamis_mesaj > 0): ?>
                                <span class="notification-badge"><?php echo $okunmamis_mesaj; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav align-items-center">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 ps-0 pe-0" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="uploads/<?php echo htmlspecialchars($kullanici_foto); ?>" class="user-avatar-nav" alt="Profil">
                            <span class="d-lg-none">Profil Menüsü</span> </a>
                        
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><h6 class="dropdown-header">Hoş geldin, <?php echo htmlspecialchars($_SESSION['kullanici_adi']); ?></h6></li>
                            
                            <li><a class="dropdown-item" href="profil_duzenle.php"><i class="bi bi-person-gear"></i> Profil Ayarları</a></li>
                            
                            <?php if ($kullanici_rol == 'admin' || $kullanici_rol == 'alt_yonetici'): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item admin-link" href="admin/admin_panel.php">
                                        <i class="bi bi-shield-lock-fill text-warning"></i> Yönetim Paneli
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="cikis_yap.php"><i class="bi bi-box-arrow-right text-danger"></i> Çıkış Yap</a></li>
                        </ul>
                    </li>
                </ul>

            <?php else: ?>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="giris.php">Giriş Yap</a></li>
                    <li class="nav-item"><a class="nav-link" href="kayit.php">Kayıt Ol</a></li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>