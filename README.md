# 💙 Bi'Selam - Arkadaşlık ve Mesajlaşma Platformu

> 🌐 **Canlı Demo:** Projeyi canlı incelemek için tıklayın: **[biselam.enesemiral.com](https://biselam.enesemiral.com)**

**Bi'Selam**, insanların tanışıp arkadaş olabileceği, mesajlaşabileceği ve profil oluşturabileceği modern ve minimal bir sosyal platformdur. PHP ve MySQL kullanılarak geliştirilmiştir.

## 🌟 Özellikler

-   **Kullanıcı Sistemi**: Kayıt olma, giriş yapma ve güvenli oturum yönetimi.
-   **Profil Yönetimi**: Profil fotoğrafı yükleme, biyografi, yaş, şehir ve meslek bilgileri.
-   **Arkadaşlık Sistemi**: Arkadaş ekleme, isteği kabul etme/reddetme ve arkadaş listesi.
-   **Mesajlaşma**: Anlık mesajlaşma deneyimi (AJAX tabanlı), geçmiş mesajları görüntüleme.
-   **Admin Paneli**: 
    -   Üyeleri yönetme (Yasaklama/Aktif etme/Yetki verme).
    -   Tüm mesaj trafiğini denetleme ve silme.
    -   Sistem loglarını (İşlem kayıtları) detaylı inceleme.
-   **Güvenlik**: PDO kullanımı, XSS koruması, yetki kontrolleri.

## 📂 Proje Yapısı

-   `admin/` - Yönetici paneli dosyaları.
-   `uploads/` - Kullanıcı profil fotoğrafları.
-   `biselam.sql` - Veritabanı kurulum dosyası.
-   `db_sample.php` - Veritabanı bağlantı şablonu.
-   `anasayfa.php`, `uyeler.php`, `mesajlarim.php` - Ana kullanıcı sayfaları.

## 🚀 Kurulum Adımları

Bu projeyi kendi bilgisayarınızda (Localhost) veya bir sunucuda çalıştırmak için aşağıdaki adımları izleyin.

### 1. Dosyaları İndirin
Projeyi bilgisayarınıza indirin veya `git clone` ile çekin.
```bash
git clone [https://github.com/kullaniciadim/biselam.git](https://github.com/kullaniciadim/biselam.git)
