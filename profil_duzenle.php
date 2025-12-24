<?php
require_once 'header.php';
require_once 'db.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit();
}

$me_id = $_SESSION['kullanici_id'];
$mesaj = "";
$mesaj_tur = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $ad_soyad = trim($_POST['ad_soyad']);
    $sehir = trim($_POST['sehir']);
    $bio = trim($_POST['bio']);
    $cinsiyet = $_POST['cinsiyet'];

    if (isset($_FILES['profil_foto']) && $_FILES['profil_foto']['error'] == 0) {
        $izin_verilenler = ['jpg', 'jpeg', 'png', 'webp'];
        $dosya_adi = $_FILES['profil_foto']['name'];
        $dosya_tmp = $_FILES['profil_foto']['tmp_name'];
        $uzanti = strtolower(pathinfo($dosya_adi, PATHINFO_EXTENSION));

        if (in_array($uzanti, $izin_verilenler)) {
            $yeni_ad = "profil_" . $me_id . "_" . uniqid() . "." . $uzanti;
            $hedef = "uploads/" . $yeni_ad;

            if (move_uploaded_file($dosya_tmp, $hedef)) {

                $sql = "UPDATE profiller SET ad_soyad=?, sehir=?, bio=?, cinsiyet=?, profil_fotografi=? WHERE kullanici_id=?";
                $pdo->prepare($sql)->execute([$ad_soyad, $sehir, $bio, $cinsiyet, $yeni_ad, $me_id]);

                $mesaj = "Profil fotoğrafın ve bilgilerin başarıyla güncellendi!";
                $mesaj_tur = "success";
            } else {
                $mesaj = "Fotoğraf yüklenirken bir hata oluştu.";
                $mesaj_tur = "danger";
            }
        } else {
            $mesaj = "Sadece JPG, PNG veya WEBP formatında resim yükleyebilirsin.";
            $mesaj_tur = "warning";
        }
    } else {
        $sql = "UPDATE profiller SET ad_soyad=?, sehir=?, bio=?, cinsiyet=? WHERE kullanici_id=?";
        $pdo->prepare($sql)->execute([$ad_soyad, $sehir, $bio, $cinsiyet, $me_id]);

        $mesaj = "Bilgilerin başarıyla kaydedildi.";
        $mesaj_tur = "success";
    }
}

$stmt = $pdo->prepare("SELECT * FROM profiller WHERE kullanici_id = ?");
$stmt->execute([$me_id]);
$profil = $stmt->fetch(PDO::FETCH_ASSOC);

$ad_soyad_val = $profil['ad_soyad'] ?? '';
$sehir_val = $profil['sehir'] ?? '';
$bio_val = $profil['bio'] ?? '';
$cinsiyet_val = $profil['cinsiyet'] ?? 'belirtilmemis';
$foto_val = $profil['profil_fotografi'] ?? 'default.jpg';

?>

<style>
    body {
        background-color: #f8faff;
    }

    .settings-card {
        background: #fff;
        border-radius: 24px;
        box-shadow: 0 10px 40px rgba(112, 144, 176, 0.08);
        border: none;
        overflow: hidden;
    }

    .form-label {
        font-weight: 600;
        color: #475569;
        font-size: 0.9rem;
        margin-bottom: 8px;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 12px 15px;
        font-size: 0.95rem;
        transition: all 0.2s;
        background-color: #f8fafc;
    }

    .form-control:focus,
    .form-select:focus {
        background-color: #fff;
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
    }

    .photo-preview-container {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto 20px auto;
    }

    .photo-preview {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 5px solid #f1f5f9;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s;
    }

    .upload-btn-wrapper {
        position: relative;
        overflow: hidden;
        display: inline-block;
        width: 100%;
    }

    .btn-upload-soft {
        border: 2px dashed #cbd5e1;
        color: #64748b;
        background-color: #f8fafc;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        width: 100%;
        cursor: pointer;
        transition: 0.2s;
    }

    .btn-upload-soft:hover {
        border-color: #6366f1;
        color: #6366f1;
        background-color: #eef2ff;
    }

    .upload-btn-wrapper input[type=file] {
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .btn-save {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        border: none;
        border-radius: 50px;
        padding: 12px 30px;
        font-weight: 600;
        color: white;
        transition: 0.3s;
        width: 100%;
    }

    .btn-save:hover {
        opacity: 0.9;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
    }
</style>

<div class="container py-5">

    <div class="row mb-4">
        <div class="col-12 text-center">
            <h2 class="fw-bold text-dark">Profilini Düzenle</h2>
            <p class="text-muted">Kişisel bilgilerini ve vitrinini güncelle</p>
        </div>
    </div>

    <?php if ($mesaj): ?>
        <div class="row justify-content-center mb-3">
            <div class="col-lg-8">
                <div class="alert alert-<?php echo $mesaj_tur; ?> border-0 shadow-sm rounded-3 d-flex align-items-center">
                    <?php if ($mesaj_tur == 'success'): ?>
                        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                    <?php else: ?>
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                    <?php endif; ?>
                    <div><?php echo $mesaj; ?></div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <div class="row g-4 justify-content-center">

            <div class="col-lg-4">
                <div class="settings-card p-4 text-center h-100">
                    <h5 class="fw-bold text-dark mb-4">Profil Fotoğrafı</h5>

                    <div class="photo-preview-container">
                        <img src="uploads/<?php echo htmlspecialchars($foto_val); ?>" id="preview-img"
                            class="photo-preview" alt="Profil">
                    </div>

                    <p class="text-muted small mb-4">
                        Kare veya yuvarlak, yüzünün net göründüğü bir fotoğraf seçmeni öneririz.
                    </p>

                    <div class="upload-btn-wrapper">
                        <button class="btn-upload-soft" type="button">
                            <i class="bi bi-cloud-arrow-up-fill me-2"></i> Fotoğraf Seç
                        </button>
                        <input type="file" name="profil_foto" id="file-input" accept="image/*"
                            onchange="previewImage(this)">
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="settings-card p-4 h-100">
                    <h5 class="fw-bold text-dark mb-4">Kişisel Bilgiler</h5>

                    <div class="mb-3">
                        <label for="ad_soyad" class="form-label">Adın ve Soyadın</label>
                        <input type="text" class="form-control" name="ad_soyad" id="ad_soyad"
                            value="<?php echo htmlspecialchars($ad_soyad_val); ?>" placeholder="Örn: Ahmet Yılmaz">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sehir" class="form-label">Şehir</label>
                            <input type="text" class="form-control" name="sehir" id="sehir"
                                value="<?php echo htmlspecialchars($sehir_val); ?>" placeholder="Örn: İstanbul">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cinsiyet" class="form-label">Cinsiyet</label>
                            <select class="form-select" name="cinsiyet" id="cinsiyet">
                                <option value="belirtilmemis" <?php if ($cinsiyet_val == 'belirtilmemis')
                                    echo 'selected'; ?>>Belirtmek İstemiyorum</option>
                                <option value="erkek" <?php if ($cinsiyet_val == 'erkek')
                                    echo 'selected'; ?>>Erkek
                                </option>
                                <option value="kadin" <?php if ($cinsiyet_val == 'kadin')
                                    echo 'selected'; ?>>Kadın
                                </option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="bio" class="form-label">Biyografi (Hakkında)</label>
                        <textarea class="form-control" name="bio" id="bio" rows="4"
                            placeholder="Kendinden kısaca bahset... Hobilerin neler?"><?php echo htmlspecialchars($bio_val); ?></textarea>
                        <div class="form-text text-end small">Profilinde görünecek kısa açıklama.</div>
                    </div>

                    <button type="submit" class="btn-save shadow-sm">
                        <i class="bi bi-save2-fill me-2"></i> Değişiklikleri Kaydet
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                document.getElementById('preview-img').src = e.target.result;
            }

            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<?php require_once 'footer.php'; ?>