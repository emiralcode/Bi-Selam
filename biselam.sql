SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+03:00";

--
-- Veritabanı: `biselam`
--
CREATE DATABASE IF NOT EXISTS `biselam` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `biselam`;

-- --------------------------------------------------------

--
-- Tablo yapısı: `kullanicilar`
--

CREATE TABLE `kullanicilar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kullanici_adi` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `sifre` varchar(255) NOT NULL,
  `rol` varchar(20) DEFAULT 'uye',
  `aktif` tinyint(1) DEFAULT 1,
  `kayit_tarihi` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `kullanici_adi` (`kullanici_adi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi: `kullanicilar`
-- (Şifre: 123456)
--
INSERT INTO `kullanicilar` (`id`, `kullanici_adi`, `email`, `sifre`, `rol`, `aktif`, `kayit_tarihi`) VALUES
(1, 'admin', 'admin@biselam.com', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', 'admin', 1, '2025-01-01 12:00:00');

-- --------------------------------------------------------

--
-- Tablo yapısı: `profiller`
--

CREATE TABLE `profiller` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kullanici_id` int(11) NOT NULL,
  `ad_soyad` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `sehir` varchar(50) DEFAULT NULL,
  `cinsiyet` varchar(20) DEFAULT 'belirtilmemis',
  `profil_fotografi` varchar(255) DEFAULT 'default.jpg',
  PRIMARY KEY (`id`),
  KEY `kullanici_id` (`kullanici_id`),
  CONSTRAINT `profiller_ibfk_1` FOREIGN KEY (`kullanici_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi: `profiller`
--
INSERT INTO `profiller` (`id`, `kullanici_id`, `ad_soyad`, `bio`, `sehir`, `cinsiyet`, `profil_fotografi`) VALUES
(1, 1, 'Sistem Yöneticisi', 'Bi\'Selam kurucusu ve yöneticisi.', 'İstanbul', 'belirtilmemis', 'default.jpg');

-- --------------------------------------------------------

--
-- Tablo yapısı: `mesajlar`
--

CREATE TABLE `mesajlar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gonderen_id` int(11) NOT NULL,
  `alan_id` int(11) NOT NULL,
  `icerik` text NOT NULL,
  `okundu_mu` tinyint(1) DEFAULT 0,
  `tarih` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `gonderen_id` (`gonderen_id`),
  KEY `alan_id` (`alan_id`),
  CONSTRAINT `mesajlar_ibfk_1` FOREIGN KEY (`gonderen_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mesajlar_ibfk_2` FOREIGN KEY (`alan_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo yapısı: `arkadasliklar`
--

CREATE TABLE `arkadasliklar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kullanici1_id` int(11) NOT NULL,
  `kullanici2_id` int(11) NOT NULL,
  `durum` varchar(20) DEFAULT 'bekliyor',
  `tarih` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `kullanici1_id` (`kullanici1_id`),
  KEY `kullanici2_id` (`kullanici2_id`),
  CONSTRAINT `arkadasliklar_ibfk_1` FOREIGN KEY (`kullanici1_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE,
  CONSTRAINT `arkadasliklar_ibfk_2` FOREIGN KEY (`kullanici2_id`) REFERENCES `kullanicilar` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo yapısı: `islem_loglari`
--

CREATE TABLE `islem_loglari` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tablo_adi` varchar(50) DEFAULT NULL,
  `islem` varchar(50) DEFAULT NULL,
  `kayit_id` int(11) DEFAULT NULL,
  `eski_veri` text DEFAULT NULL,
  `yeni_veri` text DEFAULT NULL,
  `tarih` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

COMMIT;