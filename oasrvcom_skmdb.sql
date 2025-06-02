-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost:3306
-- Üretim Zamanı: 03 Haz 2025, 00:52:55
-- Sunucu sürümü: 10.11.11-MariaDB
-- PHP Sürümü: 8.3.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `oasrvcom_skmdb`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `count_stats`
--

CREATE TABLE `count_stats` (
  `id` int(11) NOT NULL,
  `total_kavram` int(11) DEFAULT 0,
  `total_meal` int(11) DEFAULT 0,
  `last_updated` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kavramlar_sifatlar`
--

CREATE TABLE `kavramlar_sifatlar` (
  `kavram_id` int(11) NOT NULL,
  `kavram_no` int(11) DEFAULT NULL,
  `kavram_adi` text DEFAULT NULL,
  `kavram_text` text DEFAULT NULL,
  `kavram_detay` text DEFAULT NULL,
  `not_1` text DEFAULT NULL,
  `sup_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sup_numbers`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tetikleyiciler `kavramlar_sifatlar`
--
DELIMITER $$
CREATE TRIGGER `after_kavram_delete` AFTER DELETE ON `kavramlar_sifatlar` FOR EACH ROW BEGIN
    UPDATE count_stats 
    SET total_kavram = (SELECT COUNT(*) FROM kavramlar_sifatlar),
        last_updated = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_kavram_insert` AFTER INSERT ON `kavramlar_sifatlar` FOR EACH ROW BEGIN
    UPDATE count_stats 
    SET total_kavram = (SELECT COUNT(*) FROM kavramlar_sifatlar),
        last_updated = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_kavram_update` AFTER UPDATE ON `kavramlar_sifatlar` FOR EACH ROW BEGIN
    UPDATE count_stats 
    SET total_kavram = (SELECT COUNT(*) FROM kavramlar_sifatlar),
        last_updated = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `meal`
--

CREATE TABLE `meal` (
  `id` int(11) NOT NULL,
  `kuran_ayet_no` int(11) DEFAULT NULL,
  `sure_no` int(11) DEFAULT NULL,
  `ayet_no` int(11) DEFAULT NULL,
  `ayet_arapca` text DEFAULT NULL,
  `ayet_ie` text DEFAULT NULL,
  `ayet_ahmed_samira` text DEFAULT NULL,
  `ayet_latin` text DEFAULT NULL,
  `ayet_not` text DEFAULT NULL,
  `not_1` text DEFAULT NULL,
  `not_2` text DEFAULT NULL,
  `not_3` text DEFAULT NULL,
  `sup_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sup_numbers`)),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tetikleyiciler `meal`
--
DELIMITER $$
CREATE TRIGGER `after_meal_delete` AFTER DELETE ON `meal` FOR EACH ROW BEGIN
    UPDATE count_stats 
    SET total_meal = (SELECT COUNT(*) FROM meal),
        last_updated = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_meal_insert` AFTER INSERT ON `meal` FOR EACH ROW BEGIN
    UPDATE count_stats 
    SET total_meal = (SELECT COUNT(*) FROM meal),
        last_updated = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_meal_update` AFTER UPDATE ON `meal` FOR EACH ROW BEGIN
    UPDATE count_stats 
    SET total_meal = (SELECT COUNT(*) FROM meal),
        last_updated = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sure_list`
--

CREATE TABLE `sure_list` (
  `sure_no` int(11) NOT NULL,
  `sure_adi` varchar(100) DEFAULT NULL,
  `ayet_sayisi` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tablo döküm verisi `sure_list`
--

INSERT INTO `sure_list` (`sure_no`, `sure_adi`, `ayet_sayisi`, `created_at`, `updated_at`) VALUES
(1, 'Fatiha', 7, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(2, 'Bakara', 286, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(3, 'Al-i İmran', 200, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(4, 'Nisa', 176, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(5, 'Maide', 120, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(6, 'Enam', 165, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(7, 'Araf', 206, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(8, 'Enfal', 75, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(9, 'Tevbe', 129, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(10, 'Yunus', 109, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(11, 'Hud', 123, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(12, 'Yusuf', 111, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(13, 'Rad', 43, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(14, 'İbrahim', 52, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(15, 'Hicr', 99, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(16, 'Nahl', 128, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(17, 'İsra', 111, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(18, 'Kehf', 110, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(19, 'Meryem', 98, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(20, 'Taha', 135, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(21, 'Enbiya', 112, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(22, 'Hac', 78, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(23, 'Müminun', 118, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(24, 'Nur', 64, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(25, 'Furkan', 77, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(26, 'Şuara', 227, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(27, 'Neml', 93, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(28, 'Kasas', 88, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(29, 'Ankebut', 69, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(30, 'Rum', 60, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(31, 'Lokman', 34, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(32, 'Secde', 30, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(33, 'Ahzab', 73, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(34, 'Sebe', 54, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(35, 'Fatır', 45, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(36, 'Yasin', 83, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(37, 'Saffat', 182, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(38, 'Sad', 88, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(39, 'Zümer', 75, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(40, 'Mümin', 85, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(41, 'Fussilet', 54, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(42, 'Şura', 53, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(43, 'Zuhruf', 89, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(44, 'Duhan', 59, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(45, 'Casiye', 37, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(46, 'Ahkaf', 35, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(47, 'Muhammed', 38, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(48, 'Fetih', 29, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(49, 'Hucurat', 18, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(50, 'Kaf', 45, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(51, 'Zariyat', 60, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(52, 'Tur', 49, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(53, 'Necm', 62, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(54, 'Kamer', 55, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(55, 'Rahman', 78, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(56, 'Vakıa', 96, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(57, 'Hadid', 29, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(58, 'Mücadele', 22, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(59, 'Haşr', 24, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(60, 'Mümtehine', 13, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(61, 'Saf', 14, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(62, 'Cuma', 11, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(63, 'Münafikun', 11, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(64, 'Teğabun', 18, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(65, 'Talak', 12, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(66, 'Tahrim', 12, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(67, 'Mülk', 30, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(68, 'Kalem', 52, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(69, 'Hakka', 52, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(70, 'Mearic', 44, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(71, 'Nuh', 28, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(72, 'Cin', 28, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(73, 'Müzzemmil', 20, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(74, 'Müddessir', 56, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(75, 'Kıyamet', 40, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(76, 'İnsan', 31, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(77, 'Mürselat', 50, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(78, 'Nebe', 40, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(79, 'Naziat', 46, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(80, 'Abese', 42, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(81, 'Tekvir', 29, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(82, 'İnfitar', 19, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(83, 'Mutaffifin', 36, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(84, 'İnşikak', 25, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(85, 'Buruc', 22, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(86, 'Tarık', 17, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(87, 'Ala', 19, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(88, 'Ğaşiye', 26, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(89, 'Fecr', 30, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(90, 'Beled', 20, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(91, 'Şems', 15, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(92, 'Leyl', 21, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(93, 'Duha', 11, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(94, 'İnşirah', 8, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(95, 'Tin', 8, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(96, 'Alak', 19, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(97, 'Kadir', 5, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(98, 'Beyyine', 8, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(99, 'Zilzal', 8, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(100, 'Adiyat', 11, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(101, 'Karia', 11, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(102, 'Tekasür', 8, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(103, 'Asr', 3, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(104, 'Hümeze', 9, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(105, 'Fil', 5, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(106, 'Kureyş', 4, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(107, 'Maun', 7, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(108, 'Kevser', 3, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(109, 'Kafirun', 6, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(110, 'Nasr', 3, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(111, 'Tebbet', 5, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(112, 'İhlas', 4, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(113, 'Felak', 5, '2025-05-09 18:51:27', '2025-05-09 18:51:27'),
(114, 'Nas', 6, '2025-05-09 18:51:27', '2025-05-09 18:51:27');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` text NOT NULL,
  `is_2fa_enabled` tinyint(1) DEFAULT 0,
  `totp_secret` varchar(32) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `version_info`
--

CREATE TABLE `version_info` (
  `id` int(11) NOT NULL,
  `meal_version` varchar(12) DEFAULT NULL,
  `kavram_version` varchar(12) DEFAULT NULL,
  `last_check` timestamp NULL DEFAULT current_timestamp(),
  `last_update` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `count_stats`
--
ALTER TABLE `count_stats`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `kavramlar_sifatlar`
--
ALTER TABLE `kavramlar_sifatlar`
  ADD PRIMARY KEY (`kavram_id`);

--
-- Tablo için indeksler `meal`
--
ALTER TABLE `meal`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `sure_list`
--
ALTER TABLE `sure_list`
  ADD PRIMARY KEY (`sure_no`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Tablo için indeksler `version_info`
--
ALTER TABLE `version_info`
  ADD PRIMARY KEY (`id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `count_stats`
--
ALTER TABLE `count_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `version_info`
--
ALTER TABLE `version_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
