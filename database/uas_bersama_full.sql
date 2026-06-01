-- ============================================================
-- Database lengkap: uas_bersama
-- Digabungkan dari file ZIP export tabel schema + data
-- Catatan: file test-schema-create.sql tidak dimasukkan karena hanya membuat database `test` kosong.
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `uas_bersama` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin;
USE `uas_bersama`;

-- Hapus tabel lama agar import ulang tidak bentrok
DROP TABLE IF EXISTS `log_aktivitas`;
DROP TABLE IF EXISTS `testimonial`;
DROP TABLE IF EXISTS `keranjang`;
DROP TABLE IF EXISTS `detail_pesanan`;
DROP TABLE IF EXISTS `pesanan`;
DROP TABLE IF EXISTS `produk`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `pengguna`;
DROP TABLE IF EXISTS `daerah`;
DROP TABLE IF EXISTS `kategori`;

-- ============================================================
-- Struktur tabel
-- ============================================================

-- Struktur tabel `kategori`
CREATE TABLE `kategori` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(50) NOT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=30001;

-- Struktur tabel `daerah`
CREATE TABLE `daerah` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_daerah` varchar(50) NOT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  UNIQUE KEY `nama_daerah` (`nama_daerah`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=30001;

-- Struktur tabel `pengguna`
CREATE TABLE `pengguna` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `tanggal_dibuat` timestamp DEFAULT CURRENT_TIMESTAMP,
  `no_telp` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=210001;

-- Struktur tabel `users`
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=30001;

-- Struktur tabel `produk`
CREATE TABLE `produk` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_produk` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga` decimal(10,2) NOT NULL,
  `stok` int NOT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `id_kategori` int DEFAULT NULL,
  `id_daerah` int DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  KEY `idx_produk_kategori` (`id_kategori`),
  KEY `fk_produk_daerah` (`id_daerah`),
  CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_produk_daerah` FOREIGN KEY (`id_daerah`) REFERENCES `daerah` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=180001;

-- Struktur tabel `pesanan`
CREATE TABLE `pesanan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_pengguna` int DEFAULT NULL,
  `tanggal_pesanan` timestamp DEFAULT CURRENT_TIMESTAMP,
  `total_harga` decimal(10,2) NOT NULL,
  `status` enum('menunggu','dibayar','dikirim','selesai','dibatalkan') DEFAULT 'menunggu',
  `alamat` text DEFAULT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  KEY `idx_pesanan_pengguna` (`id_pengguna`),
  CONSTRAINT `fk_pesanan_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=270001;

-- Struktur tabel `detail_pesanan`
CREATE TABLE `detail_pesanan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_pesanan` int DEFAULT NULL,
  `id_produk` int DEFAULT NULL,
  `jumlah` int NOT NULL,
  `harga` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  KEY `idx_detail_pesanan_pesanan` (`id_pesanan`),
  KEY `idx_detail_pesanan_produk` (`id_produk`),
  CONSTRAINT `fk_detail_pesanan_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_detail_pesanan_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=270001;

-- Struktur tabel `keranjang`
CREATE TABLE `keranjang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_pengguna` int DEFAULT NULL,
  `id_produk` int DEFAULT NULL,
  `jumlah` int NOT NULL,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  KEY `idx_keranjang_pengguna` (`id_pengguna`),
  KEY `idx_keranjang_produk` (`id_produk`),
  CONSTRAINT `fk_keranjang_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_keranjang_produk` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=330001;

-- Struktur tabel `testimonial`
CREATE TABLE `testimonial` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_pengguna` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `pekerjaan` varchar(100) DEFAULT NULL,
  `isi_ulasan` text NOT NULL,
  `rating` int DEFAULT '5',
  `tanggal_dibuat` timestamp DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */,
  KEY `idx_testimonial_pengguna` (`id_pengguna`),
  CONSTRAINT `fk_testimonial_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=90001;

-- Struktur tabel `log_aktivitas`
CREATE TABLE `log_aktivitas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_pengguna` int DEFAULT NULL,
  `nama_pengguna` varchar(100) NOT NULL,
  `tipe_aktivitas` varchar(50) NOT NULL,
  `aksi` varchar(50) NOT NULL,
  `keterangan` text NOT NULL,
  `tanggal_dibuat` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) /*T![clustered_index] CLUSTERED */
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=120001;

-- ============================================================
-- Data tabel
-- ============================================================

-- Data tabel `kategori`
INSERT INTO `kategori` VALUES
(1,'Batik'),
(2,'Anyaman'),
(3,'Aksesoris'),
(4,'Dekorasi'),
(5,'Rajut');

-- Data tabel `daerah`
INSERT INTO `daerah` VALUES
(1,'Sumenep'),
(2,'Pamekasan'),
(3,'Sampang'),
(4,'Bangkalan');

-- Data tabel `pengguna`
INSERT INTO `pengguna` VALUES
(1,'Admin Handmade','admin@handmade.com','$2y$12$wIDWgvzC/M6HrP8Qqs7E.uDtbRw.Sezh.XLKb5pwFYVKQhBdczgaG','admin','2026-05-15 02:28:29',NULL,NULL),
(2,'joko','joko@gmail.com','$2y$12$uMGGTA8NuuQ3frbC1U5GaegTVrZJZXTExW5YCf2zZgfI6XGNLQdSO','user','2026-05-15 02:34:15',NULL,NULL),
(30001,'imam','imam@gmail.com','$2y$12$.frQQSqKOpJ8YGSg9zEe7uB7o6nksiAgx7jGS0ygqGvwvtz.s7dI2','user','2026-05-20 08:00:42',NULL,NULL),
(60001,'adit','adit@gmail.com','$2y$12$3S6ivMkDrQvjC9MVbgO7SuxnSi3Dne9Sj2CHZBvwGTGzQUXCqelp.','user','2026-05-21 15:13:22',NULL,NULL),
(90001,'rama','rama@gmail.com','$2y$12$CKRXH3WpMpyVdgiLEsal7.oFL0uiByfIWcAJLFxh7Vqe2BOvn1JIO','user','2026-05-23 14:52:12','0000000000','jln madura'),
(120001,'tia','tia@gmail.com','$2y$10$y/ZHVWghQUZe9PPspL2AZ.CAGKyF7qA171z7WrbXiWazF5UXBKLMm','user','2026-05-24 04:43:24',NULL,NULL),
(180001,'rian afroni','rian@gmail.com','$2y$12$tuJCbWBEydyuezPnvu9JbOc2JE5iN8Oe2jBcYiM9RACnUkml058Im','user','2026-05-27 03:08:54','087654788877','jln haji umam');

-- Data tabel `users`
INSERT INTO `users` VALUES
(1,'Iktiar','iktiar@example.com'),
(2,'Teman','teman@example.com');

-- Data tabel `produk`
INSERT INTO `produk` VALUES
(1,'Batik Tanjung Bumi','Gelang manik-manik handmade dengan motif etnik khas Nusantara. Cocok untuk aksesoris harian.',80000.00,8,'https://image.indonetwork.co.id/f-webp/products/thumbs/343x343/2013/03/28/3eacf90461810a73194e663abb45b2fe.jpg',1,4),
(2,'Batik Gentongan','Tas rajut tangan (crochet) dengan gaya bohemian. Kuat, stylish, dan ramah lingkungan.',125000.00,5,'https://akcdn.detik.net.id/visual/2022/03/17/proses-pembuatan-batik-gentongan-kaya-akan-ritual-dan-mitosfoto-rumah-bumnid_169.jpeg?w=700&q=90',1,2),
(3,'Batik Indah','Lilin aromaterapi dari soy wax alami dengan essential oil lavender yang menenangkan.',65000.00,12,'https://images.openai.com/static-rsc-4/kbkza1WNhgf9sZ0icWjzpsiPl-RRg_uUKXFBdJryZ1ll7eDnAND77ykKBHkvmHWRWGnSfPEWpIN-uhZDvVrpT4AJ9OD7Eq91SzkyOJL_qaMPBowwFo4VXn_vusmCvxjJPtn2dp9nLA_0mjJia08u0Mh0nPHL_ufsYW2Py_wAvyQrqwNzlQBJfbJo649vpk3l?purpose=fullsize',2,1),
(4,'Tas Anyaman Pandan','Tas handmade dari daun pandan khas Sampang yang ringan, kuat, dan ramah lingkungan.',85000.00,6,'https://images.openai.com/static-rsc-4/k86yYESyUAJqYcsj1ztjz-l02z0WWUIbTcCYnDRhBKwrWzjIOCBh3yUSZpUxJDxdCp13JFKTwu3F-MD1Gh3x0yj5IoM-9yuTJna-tkiSypaVcu5IbQjyO_s0XqZPIPLdcItjF183MoeKKHp0Qh6XoYi1H_YEH335fJK-UhnZ0jE?purpose=inline',2,3),
(5,'Tempat Tisu Anyaman','Tempat tisu handmade dengan motif anyaman khas Madura yang cocok untuk dekorasi meja rumah.\r\n',150000.00,4,'https://images.openai.com/static-rsc-4/LIyk3De6BReLE9C5sqT6i4E9l7rBuk_4HJKQUDv6RFKQ1fExOQrdIT6tCZ6El0b7QDBB3atzY8C2lBUahYCbeP5cTshWBrMjBGyUuFvAlw1CN_INIsy8lOIq5wjMlKt7Qqs3_c-r3HPmAemLmyA340QjCWNI8abT1MsFDZSRJpQ-9F8Q8ioJxo_ejscgO1nw?purpose=fullsize',2,2),
(6,'Kalung Etnik Madura','Kalung handmade dengan perpaduan motif etnik dan warna khas budaya Madura yang elegan digunakan sebagai aksesoris fashion.',95000.00,12,'https://images.openai.com/static-rsc-4/eVr9SgVCiWESurpHMua1idd2E-ttC7GMz-2SQTfMfpypYRiQVAq_d7GMwF4dNsQlja7PeQwMwiPiZmZKw_TFDaljYSe39qbQRo3knwWuW3EghfOZRiE6HniAUfbrOYNGlggREhxvZ_lpl3ewnoA_-mKnUiX81CK9tF2HXdJP_Pw?purpose=inline',3,2),
(7,'Miniatur Kerapan Sapi','Miniatur handmade yang menggambarkan tradisi Karapan Sapi sebagai ikon budaya Madura.',55000.00,29,'https://images.openai.com/static-rsc-4/BsI15qm28lU-gCzZ_Xul_6gRnbmrnG6LSndaWCMOpPAyHQrNHTrsa6Cv29J_r3bgNoC6u1Mrs8MWxK8nEvF_RnqGEiAFEcdCrS_7XATrEfDjLU2qbXRu7OVWGDZ6sThynPA47r_0k5YRuxWEWt7Wp-lwHK1SVrgBZRTs15EbxxXNRrDOoLv56BXEEuiFL5Wn?purpose=fullsize',4,4),
(8,'Tas Rajut Bermotif','Tas rajut handmade dengan kombinasi warna dan motif khas Madura yang fashionable dan unik.\r\n',185000.00,7,'https://images.openai.com/static-rsc-4/DJdL6ENPzrdYshG5Ozubv6f5ClNK-VjI8qMe63m754W5PhDSDaFj7w1yPqg1DjdiDR6Uzyag-kie1tGVblkmR4ogrqx03XuINiDnrf5X5dDfZxWmkqKZN9_kqxGhFyshg-fZnyIaANmB3OD6rS6JgJ0-XljY2p83-bix0kIRe9o?purpose=inline',5,1),
(9,'Ukiran Kayu','Kerajinan ukiran kayu handmade khas Sumenep dengan detail artistik bernilai budaya tinggi.',35000.00,25,'https://images.openai.com/static-rsc-4/_Wy1Ebz0FOwynzxMAKq5XntiGSK-jUzTeFx_4rk57NPcl0vvsQ8WvPTcn7BYi1VuarDGADyaWHrTmTZoVEmADsmFPmYOaAldLqZv8DAyf0842ubnwNYwLx3CsmgFZr_99ZTfMFOudSBAGOAlNg6GiiJOHbpfJQTLmvx7VD9y-pI?purpose=inline',4,1),
(10,'Dompet Rajut Etnik','Dompet handmade berbahan rajut dengan desain etnik tradisional yang praktis dan fashionable.',75000.00,15,'https://images.openai.com/static-rsc-4/asJ9EOaoz_Exq1cGgUyI6MEGEQxl09WeHAhbEINNeYxvRT-TNF5LMsKq77VAUWeGbaLnS5AP04qrN8DyaKDDmVU53kz9m55CgJoGVWw0F4fH5J95vasaW2frHmGxyKYJPSxhpD4gaCqpk2OfyCSOOknJDEtCNkt1dr4D-M2Ix-c?purpose=inline',5,1);

-- Data tabel `pesanan`
INSERT INTO `pesanan` VALUES
(1,2,'2026-05-15 02:39:48',90000.00,'dibayar','jln bahagia','E-Wallet (Gopay/OVO)','uploads/bukti/bukti_1_1778812824.png'),
(30001,60001,'2026-05-21 15:44:09',395000.00,'dibayar','fbdg','Transfer Bank','uploads/bukti/bukti_30001_1779378902.png'),
(60001,90001,'2026-05-23 14:53:15',125000.00,'dibayar','jln kemuning','BCA Virtual Account','8001260001932'),
(90001,90001,'2026-05-23 15:14:31',365000.00,'dibayar','jdwuw','BCA Virtual Account','8001290001932'),
(90002,90001,'2026-05-23 15:17:38',85000.00,'dibayar','efef','BCA Virtual Account','8001290002932'),
(120001,120001,'2026-05-24 04:45:45',150000.00,'dibayar','blablaa','E-Wallet (Gopay/OVO)','PAY120001'),
(150001,90001,'2026-05-24 08:01:15',1.00,'dibatalkan','hufudwf','BCA Virtual Account',NULL),
(150002,90001,'2026-05-24 08:04:46',65000.00,'dibatalkan','yidw','Mandiri Virtual Account',NULL),
(150003,90001,'2026-05-24 08:06:29',45000.00,'dibayar','hvfuy','BCA Virtual Account','80012150003932'),
(150004,90001,'2026-05-24 08:08:13',85000.00,'dibayar','gug','Alfamart','ALFA15000483'),
(150005,120001,'2026-05-24 08:12:11',125000.00,'selesai','hbytrghn','GoPay','GP-9482150005'),
(210001,180001,'2026-05-27 03:39:58',160000.00,'dibayar','jln haji umam | Telp: 087654788877','BCA Virtual Account','80012210001932'),
(240001,90001,'2026-05-31 10:32:27',55000.00,'dibayar','efef | Telp: 087654788877','Mandiri Virtual Account','89022240001854');

-- Data tabel `detail_pesanan`
INSERT INTO `detail_pesanan` VALUES
(1,1,1,2,45000.00),
(30001,30001,1,6,45000.00),
(30002,30001,2,1,125000.00),
(60001,60001,2,1,125000.00),
(90001,90001,1,1,45000.00),
(90002,90001,2,1,125000.00),
(90003,90001,3,3,65000.00),
(90004,90002,4,1,85000.00),
(120001,120001,5,1,150000.00),
(150002,150002,3,1,65000.00),
(150003,150003,1,1,45000.00),
(150004,150004,4,1,85000.00),
(150005,150005,2,1,125000.00),
(210001,210001,1,2,80000.00),
(240001,240001,7,1,55000.00);

-- Data tabel `keranjang`
INSERT INTO `keranjang` VALUES
(180001,120001,2,2),
(210001,90001,1,3),
(210002,90001,2,3),
(240001,90001,3,4);

-- Data tabel `testimonial`
INSERT INTO `testimonial` VALUES
(60001,90001,'rama','guwugc','jbfefiuegfeugfefefgeugfuegfuef',5,'2026-05-24 07:46:54','approved');

-- Data tabel `log_aktivitas`
INSERT INTO `log_aktivitas` VALUES
(1,1,'Admin Handmade','pengguna','daftar','Menginisialisasi sebagai akun Administrator','2026-05-15 02:28:29'),
(2,2,'joko','pengguna','daftar','Mendaftar sebagai pengguna baru','2026-05-15 02:34:15'),
(3,30001,'imam','pengguna','daftar','Mendaftar sebagai pengguna baru','2026-05-20 08:00:42'),
(4,60001,'adit','pengguna','daftar','Mendaftar sebagai pengguna baru','2026-05-21 15:13:22'),
(5,90001,'rama','pengguna','daftar','Mendaftar sebagai pengguna baru','2026-05-23 14:52:12'),
(6,120001,'tia','pengguna','daftar','Mendaftar sebagai pengguna baru','2026-05-24 04:43:24'),
(7,1,'Admin Handmade','produk','tambah','Menambahkan produk baru \'Batik Tanjung Bumi\'','2026-05-27 02:37:54'),
(8,1,'Admin Handmade','produk','tambah','Menambahkan produk baru \'Batik Gentongan\'','2026-05-27 02:37:54'),
(9,1,'Admin Handmade','produk','tambah','Menambahkan produk baru \'Batik Indah\'','2026-05-27 02:37:54'),
(10,1,'Admin Handmade','produk','tambah','Menambahkan produk baru \'Tas Anyaman Pandan\'','2026-05-27 02:37:54'),
(11,1,'Admin Handmade','produk','tambah','Menambahkan produk baru \'Tempat Tisu Anyaman\'','2026-05-27 02:37:54'),
(12,90001,'rama','testimoni','tambah','Menulis ulasan baru','2026-05-24 07:46:54'),
(13,1,'Admin Handmade','testimoni','setujui','Menyetujui testimoni dari \'rama\'','2026-05-24 07:46:54'),
(14,2,'joko','pesanan','tambah','Membuat pesanan baru #HM-00001','2026-05-15 02:39:48'),
(15,1,'Admin Handmade','pesanan','dibayar','Mengonfirmasi pembayaran pesanan #HM-00001 dari \'joko\'','2026-05-15 02:39:48'),
(16,60001,'adit','pesanan','tambah','Membuat pesanan baru #HM-30001','2026-05-21 15:44:09'),
(17,1,'Admin Handmade','pesanan','dibayar','Mengonfirmasi pembayaran pesanan #HM-30001 dari \'adit\'','2026-05-21 15:44:09'),
(18,90001,'rama','pesanan','tambah','Membuat pesanan baru #HM-60001','2026-05-23 14:53:15'),
(19,1,'Admin Handmade','pesanan','dibayar','Mengonfirmasi pembayaran pesanan #HM-60001 dari \'rama\'','2026-05-23 14:53:15'),
(20,90001,'rama','pesanan','tambah','Membuat pesanan baru #HM-90001','2026-05-23 15:14:31'),
(21,1,'Admin Handmade','pesanan','dibayar','Mengonfirmasi pembayaran pesanan #HM-90001 dari \'rama\'','2026-05-23 15:14:31'),
(22,90001,'rama','pesanan','tambah','Membuat pesanan baru #HM-90002','2026-05-23 15:17:38'),
(23,1,'Admin Handmade','pesanan','dibayar','Mengonfirmasi pembayaran pesanan #HM-90002 dari \'rama\'','2026-05-23 15:17:38'),
(30001,180001,'rian afroni','pengguna','daftar','Mendaftar sebagai pengguna baru','2026-05-27 03:08:54'),
(60001,180001,'rian afroni','pesanan','tambah','Membuat pesanan baru #HM-210001','2026-05-27 03:39:58'),
(60002,180001,'rian afroni','pesanan','dibayar','Melakukan konfirmasi pembayaran untuk #HM-210001','2026-05-27 03:42:39'),
(90001,90001,'rama','pesanan','tambah','Membuat pesanan baru #HM-240001','2026-05-31 10:32:27'),
(90002,90001,'rama','pesanan','dibayar','Melakukan konfirmasi pembayaran untuk #HM-240001','2026-05-31 10:32:33'),
(90003,90001,'rama','pengguna','edit','Memperbarui data profil diri','2026-05-31 10:36:40');

SET FOREIGN_KEY_CHECKS = 1;

-- Selesai
