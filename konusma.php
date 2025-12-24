<?php
require_once 'header.php';

if (!isset($_SESSION['kullanici_id'])) {
    header("Location: giris.php");
    exit();
}

require_once 'db.php';

$me_id = $_SESSION['kullanici_id'];

if (!isset($_GET['hedef_id'])) {
    die("Konuşma başlatmak için bir hedef kullanıcı ID'si gereklidir.");
}
$hedef_id = (int) $_GET['hedef_id'];

$sql_hedef = "SELECT p.ad_soyad, p.profil_fotografi FROM profiller p WHERE p.kullanici_id = ?";
$stmt_hedef = $pdo->prepare($sql_hedef);
$stmt_hedef->execute([$hedef_id]);
$hedef_profil = $stmt_hedef->fetch(PDO::FETCH_ASSOC);
$hedef_ad_soyad = $hedef_profil ? htmlspecialchars($hedef_profil['ad_soyad']) : "Bilinmeyen Kullanıcı";
$hedef_foto = $hedef_profil ? htmlspecialchars($hedef_profil['profil_fotografi']) : "default.jpg";

$sql_update_okundu = "UPDATE mesajlar SET okundu_mu = 1 WHERE gonderen_id = ? AND alan_id = ? AND okundu_mu = 0";
$pdo->prepare($sql_update_okundu)->execute([$hedef_id, $me_id]);

$sql_konusma = "SELECT * FROM mesajlar 
                WHERE (gonderen_id = ? AND alan_id = ?) 
                   OR (gonderen_id = ? AND alan_id = ?)
                ORDER BY tarih ASC";
$stmt_konusma = $pdo->prepare($sql_konusma);
$stmt_konusma->execute([$me_id, $hedef_id, $hedef_id, $me_id]);
$mesajlar = $stmt_konusma->fetchAll(PDO::FETCH_ASSOC);

?>

<style>
    .chat-box {
        height: 60vh;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    .mesaj-bubble {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 1.25rem;
        line-height: 1.4;
        word-wrap: break-word;
    }

    .mesaj-gonderen {
        align-self: flex-end;
        background-color: #007bff;
        color: white;
    }

    .mesaj-alan {
        align-self: flex-start;
        background-color: #e9e9eb;
        color: black;
    }
</style>

<div class="row justify-content-center">
    <div class="col-md-10 col-lg-8">

        <div class="card shadow-sm border-0">
            <div class="card-header p-3 d-flex align-items-center">
                <a href="mesajlarim.php" class="btn btn-outline-secondary me-3"><i class="bi bi-arrow-left"></i>
                    Geri</a>
                <img src="uploads/<?php echo $hedef_foto; ?>" alt="Profil" class="rounded-circle me-2"
                    style="width: 40px; height: 40px; object-fit: cover;">
                <h5 class="mb-0"><?php echo $hedef_ad_soyad; ?></h5>
            </div>

            <div class="card-body chat-box p-3" id="chatBox">
                <?php if (empty($mesajlar)): ?>
                    <div class="text-center text-muted mt-auto mb-auto">Konuşma henüz başlamamış. İlk mesajı gönderin!</div>
                <?php endif; ?>

                <?php foreach ($mesajlar as $mesaj): ?>
                    <?php
                    $class = ($mesaj['gonderen_id'] == $me_id) ? 'mesaj-gonderen' : 'mesaj-alan';
                    ?>
                    <div class="mesaj-bubble mb-2 <?php echo $class; ?>">
                        <?php echo htmlspecialchars($mesaj['icerik']); ?>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="card-footer p-3">
                <form id="mesaj-formu" method="POST" class="d-flex">
                    <textarea id="mesaj-icerik" name="icerik" class="form-control" placeholder="Mesajınızı yazın..."
                        rows="1" style="resize: none;" autofocus></textarea>

                    <input type="hidden" id="hedef-id-field" name="hedef_id" value="<?php echo $hedef_id; ?>">

                    <button type="submit" class="btn btn-primary ms-2"><i class="bi bi-send"></i> Gönder</button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    var chatBox = document.getElementById("chatBox");
    var mesajFormu = document.getElementById("mesaj-formu");
    var mesajInput = document.getElementById("mesaj-icerik");
    var hedefIdInput = document.getElementById("hedef-id-field");

    document.addEventListener("DOMContentLoaded", function () {
        chatBox.scrollTop = chatBox.scrollHeight;
    });

    mesajFormu.addEventListener("submit", function (event) {
        event.preventDefault();
        var mesajIcerik = mesajInput.value.trim();
        var hedefId = hedefIdInput.value;
        if (mesajIcerik === "") { return; }

        var formData = new FormData();
        formData.append('icerik', mesajIcerik);
        formData.append('hedef_id', hedefId);

        fetch('mesaj_gonder_ajax.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    var yeniMesaj = document.createElement("div");
                    yeniMesaj.classList.add("mesaj-bubble", "mb-2", "mesaj-gonderen");
                    yeniMesaj.textContent = mesajIcerik;
                    chatBox.appendChild(yeniMesaj);
                    mesajInput.value = "";
                    chatBox.scrollTop = chatBox.scrollHeight;
                } else {
                    alert('Mesaj Gönderilemedi: ' + (data.message || 'Bilinmeyen hata.'));
                }
            })
            .catch(error => {
                console.error('Mesaj Gönderme Hatası:', error);
                alert('Mesaj gönderilirken kritik bir hata oluştu. Konsolu kontrol edin.');
            });
    });

    function fetchNewMessages() {
        var hedefId = hedefIdInput.value;

        if (!hedefId || hedefId == 0) {
            console.error("fetchNewMessages durdu: Hedef ID alanı boş veya 0.");
            return;
        }

        fetch('mesajlari_getir_ajax.php?hedef_id=' + hedefId)
            .then(response => response.json())
            .then(data => {

                if (data.success == false) {
                    console.error('Mesaj Alma Hatası (Sunucu): ' + data.message);
                    return;
                }

                if (data.success && data.messages.length > 0) {
                    data.messages.forEach(function (mesaj) {
                        var yeniMesaj = document.createElement("div");
                        yeniMesaj.classList.add("mesaj-bubble", "mb-2", "mesaj-alan");
                        yeniMesaj.textContent = mesaj.icerik;
                        chatBox.appendChild(yeniMesaj);
                    });
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            })
            .catch(error => {
                console.error('Kritik Mesaj Alma Hatası (AJAX/JSON):', error);
            });
    }

    setInterval(fetchNewMessages, 3000);

</script>

<?php
require_once 'footer.php';
?>