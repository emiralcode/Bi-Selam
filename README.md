# Bi'Selam - Arkadaşlık ve Mesajlaşma Platformu

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
git clone https://github.com/kullaniciadim/biselam.git
```

### 2. Veritabanını Oluşturun
1.  **phpMyAdmin** veya kullandığınız veritabanı aracını açın.
2.  `biselam` adında yeni bir veritabanı oluşturun (`utf8mb4_general_ci` önerilir).
3.  Ana dizindeki **`biselam.sql`** dosyasını bu veritabanına **içe aktarın (import)**.

### 3. Bağlantı Ayarlarını Yapın
1.  Ana dizindeki **`db_sample.php`** dosyasının adını **`db.php`** olarak değiştirin.
2.  `db.php` dosyasını bir metin düzenleyici ile açın ve veritabanı bilgilerinizi girin:

```php
$host = 'localhost';
$dbname = 'biselam'; // Veritabanı adınız
$username = 'root';  // Veritabanı kullanıcı adınız
$password = '';      // Veritabanı şifreniz
```

### 4. Çalıştırın
Web sunucunuzu (Apache, Nginx vb.) başlatın ve tarayıcıdan projeye erişin:
`http://localhost/biselam`

## 🔑 Admin Girişi

Veritabanını içe aktardığınızda varsayılan bir kullanıcı mevcut değilse:
1.  Siteden yeni bir üyelik oluşturun.
2.  phpMyAdmin'den `kullanicilar` tablosuna gidin.
3.  Üyeliğinizin `rol` sütununu `admin` olarak güncelleyin.
4.  Çıkış yapıp tekrar girdiğinizde menüde **"Yönetim Paneli"** seçeneğini göreceksiniz.

## 📝 Gereksinimler

-   PHP 7.4 veya üzeri
-   MySQL veya MariaDB
-   Web Sunucusu (Apache/Nginx)
-   PDO PHP Eklentisi

---
İyi eğlenceler! 🎈
