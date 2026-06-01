/*!40014 SET FOREIGN_KEY_CHECKS=0*/;
/*!40101 SET NAMES binary*/;
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
  KEY `fk_1` (`id_pengguna`),
  CONSTRAINT `fk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin AUTO_INCREMENT=90001;
