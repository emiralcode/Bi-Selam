<?php
require_once 'db.php';
session_start();

if (isset($_SESSION['kullanici_id'])) {
    header("Location: anasayfa.php");
    exit();
}

$hata = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $sifre = trim($_POST['sifre']);

    if (empty($email) || empty($sifre)) {
        $hata = "Lütfen tüm alanları doldurun.";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM kullanicilar WHERE email = ?");
        $stmt->execute([$email]);
        $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($kullanici && password_verify($sifre, $kullanici['sifre'])) {
            if ($kullanici['aktif'] == 0) {
                $hata = "Hesabınız askıya alınmıştır. Yönetici ile iletişime geçin.";
            } else {
                $_SESSION['kullanici_id'] = $kullanici['id'];
                $_SESSION['kullanici_adi'] = $kullanici['kullanici_adi'];
                $_SESSION['rol'] = $kullanici['rol'];
                header("Location: anasayfa.php");
                exit();
            }
        } else {
            $hata = "E-posta veya şifre hatalı.";
        }
    }
}
?>
<!doctype html>
<html lang="tr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Giriş Yap - Bi'Selam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8faff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            background: #fff;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(112, 144, 176, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 450px;
            border: 1px solid #f1f5f9;
        }

        .brand-logo {
            font-size: 2rem;
            font-weight: 800;
            color: #4f46e5;
            letter-spacing: -1px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .form-floating .form-control {
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            background-color: #f8fafc;
        }

        .form-floating .form-control:focus {
            background-color: #fff;
            border-color: #6366f1;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .btn-primary-soft {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            color: white;
            transition: 0.3s;
        }

        .btn-primary-soft:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
            color: white;
        }

        .link-secondary {
            color: #64748b;
            text-decoration: none;
            font-size: 0.9rem;
            transition: 0.2s;
        }

        .link-secondary:hover {
            color: #4f46e5;
        }
    </style>
</head>

<body>

    <div class="auth-card">
        <div class="text-center mb-4">
            <a href="index.php" class="brand-logo">
                <i class="bi bi-balloon-heart-fill"></i> Bi'Selam
            </a>
            <h5 class="fw-bold text-dark mt-3">Tekrar Hoş Geldin! 👋</h5>
            <p class="text-muted small">Hesabına giriş yap ve sohbete başla.</p>
        </div>

        <?php if ($hata): ?>
            <div class="alert alert-danger border-0 rounded-3 d-flex align-items-center mb-4"
                style="background-color: #fef2f2; color: #b91c1c;">
                <i class="bi bi-exclamation-circle-fill me-2"></i> <?php echo $hata; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com"
                    required>
                <label for="email" class="text-secondary">E-Posta Adresi</label>
            </div>

            <div class="form-floating mb-4">
                <input type="password" class="form-control" id="sifre" name="sifre" placeholder="Şifre" required>
                <label for="sifre" class="text-secondary">Şifre</label>
            </div>

            <button type="submit" class="btn-primary-soft mb-3">
                Giriş Yap <i class="bi bi-arrow-right-short"></i>
            </button>

            <div class="text-center">
                <span class="text-muted small">Hesabın yok mu?</span>
                <a href="kayit_ol.php" class="link-secondary fw-bold ms-1">Hemen Kayıt Ol</a>
            </div>
        </form>
    </div>

</body>

</html>