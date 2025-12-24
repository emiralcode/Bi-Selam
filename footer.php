<style>
    html,
    body {
        height: 100%;
    }

    body {
        display: flex;
        flex-direction: column;
    }

    .footer-custom {
        background-color: #ffffff;
        border-top: 1px solid #f1f5f9;
        margin-top: auto;
        padding-top: 3rem;
        padding-bottom: 2rem;
    }

    .footer-brand {
        font-weight: 800;
        font-size: 1.2rem;
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
        margin-bottom: 1rem;
    }

    .footer-heading {
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.2rem;
        font-size: 0.95rem;
    }

    .footer-link {
        color: #64748b;
        text-decoration: none;
        margin-bottom: 0.8rem;
        display: block;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .footer-link:hover {
        color: #6366f1;
        transform: translateX(3px);
    }

    .social-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #f8fafc;
        color: #64748b;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        transition: all 0.3s;
        text-decoration: none;
        border: 1px solid #e2e8f0;
    }

    .social-btn:hover {
        background-color: #6366f1;
        color: white;
        transform: translateY(-3px);
        border-color: #6366f1;
    }

    .copyright-text {
        font-size: 0.85rem;
        color: #94a3b8;
    }
</style>

<footer class="footer-custom">
    <div class="container">
        <div class="row g-4">

            <div class="col-lg-4 col-md-6">
                <a href="anasayfa.php" class="footer-brand">
                    <i class="bi bi-balloon-heart-fill"></i> Bi'Selam
                </a>
                <p class="text-muted small mb-4" style="line-height: 1.6;">
                    Yeni insanlarla tanışmak, arkadaşlıklar kurmak ve sosyalleşmek hiç bu kadar keyifli olmamıştı.
                    Topluluğumuza katılın!
                </p>
                <div>
                    <a href="#" class="social-btn"><i class="bi bi-twitter"></i></a>
                    <a href="#" class="social-btn"><i class="bi bi-instagram"></i></a>
                    <a href="https://linkedin.com/in/enesemiral" class="social-btn"><i class="bi bi-linkedin"></i></a>
                    <a href="https://github.com/emiralcode" class="social-btn"><i class="bi bi-github"></i></a>
                </div>
            </div>

            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="footer-heading">Keşfet</h6>
                <a href="uyeler.php" class="footer-link">Yeni Üyeler</a>
                <a href="uyeler.php?q=İstanbul" class="footer-link">Şehre Göre Ara</a>
                <a href="arkadaslarim.php" class="footer-link">Arkadaşlarım</a>
            </div>

            <div class="col-lg-2 col-md-3 col-6">
                <h6 class="footer-heading">Hesap</h6>
                <a href="profil_duzenle.php" class="footer-link">Profil Ayarları</a>
                <a href="mesajlarim.php" class="footer-link">Gelen Kutusu</a>
                <a href="cikis_yap.php" class="footer-link text-danger">Çıkış Yap</a>
            </div>

            <div class="col-lg-4 col-md-12">
                <h6 class="footer-heading">Bize Ulaşın</h6>
                <p class="text-muted small mb-2"><i class="bi bi-geo-alt-fill me-2 text-primary"></i> Adana, Türkiye</p>
                <p class="text-muted small mb-2"><i class="bi bi-envelope-fill me-2 text-primary"></i>
                    destek@enesemiral.com</p>

                <div class="mt-3 p-3 bg-light rounded-3 border border-light">
                    <small class="d-block text-muted fw-bold mb-1">Günün Sözü:</small>
                    <small class="text-secondary fst-italic">"Bir fincan kahvenin kırk yıl hatrı vardır."</small>
                </div>
            </div>

        </div>

        <hr class="my-4" style="opacity: 0.05;">

        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <p class="copyright-text mb-0">
                    &copy; <?php echo date("Y"); ?> <strong>Bi'Selam</strong>. Tüm hakları saklıdır.
                </p>
            </div>
            <div class="col-md-6 text-center text-md-end mt-2 mt-md-0">
                <a href="#" class="text-decoration-none text-muted small me-3">Gizlilik Politikası</a>
                <a href="#" class="text-decoration-none text-muted small">Kullanım Şartları</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>