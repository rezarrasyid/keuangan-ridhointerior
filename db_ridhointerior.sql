-- ============================================================
-- DATABASE: db_ridhointerior
-- Aplikasi Manajemen Keuangan Ridho Interior
-- Multi-Tenant Architecture (workshop_id di setiap tabel)
-- ============================================================

CREATE DATABASE IF NOT EXISTS `db_ridhointerior` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_ridhointerior`;

-- ------------------------------------------------------------
-- Tabel: workshops
-- ------------------------------------------------------------
CREATE TABLE `workshops` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nama_workshop` VARCHAR(150) NOT NULL,
  `alamat` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `workshops` (`nama_workshop`, `alamat`) VALUES
('Workshop Pusat - Jakarta', 'Jl. Raya Kebon Jeruk No. 10, Jakarta Barat'),
('Workshop Cabang - Bandung', 'Jl. Dago No. 45, Bandung');

-- ------------------------------------------------------------
-- Tabel: users
-- ------------------------------------------------------------
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `workshop_id` INT UNSIGNED NOT NULL,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama_lengkap` VARCHAR(150) NOT NULL,
  `role` ENUM('superadmin','admin') NOT NULL DEFAULT 'admin',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`workshop_id`) REFERENCES `workshops`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Password: password (hashed with PHP password_hash)
INSERT INTO `users` (`workshop_id`, `username`, `password`, `nama_lengkap`, `role`) VALUES
(1, 'superadmin', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77bqFy', 'Super Administrator', 'superadmin'),
(1, 'admin_jakarta', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77bqFy', 'Admin Jakarta', 'admin'),
(2, 'admin_bandung', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77bqFy', 'Admin Bandung', 'admin');


-- ------------------------------------------------------------
-- Tabel: clients
-- ------------------------------------------------------------
CREATE TABLE `clients` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `workshop_id` INT UNSIGNED NOT NULL,
  `nama` VARCHAR(150) NOT NULL,
  `telepon` VARCHAR(30),
  `alamat` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`workshop_id`) REFERENCES `workshops`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clients` (`workshop_id`, `nama`, `telepon`, `alamat`) VALUES
(1, 'Budi Santoso', '081234567890', 'Jl. Merdeka No. 5, Jakarta'),
(1, 'Siti Rahayu', '082345678901', 'Jl. Sudirman No. 12, Jakarta'),
(1, 'Andi Wijaya', '083456789012', 'Jl. Gatot Subroto No. 7, Jakarta');

-- ------------------------------------------------------------
-- Tabel: workers (Tukang)
-- ------------------------------------------------------------
CREATE TABLE `workers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `workshop_id` INT UNSIGNED NOT NULL,
  `nama` VARCHAR(150) NOT NULL,
  `telepon` VARCHAR(30),
  `kategori` ENUM('Senior','Junior','Baru') NOT NULL DEFAULT 'Junior',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`workshop_id`) REFERENCES `workshops`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `workers` (`workshop_id`, `nama`, `telepon`, `kategori`) VALUES
(1, 'Pak Hendra', '085678901234', 'Senior'),
(1, 'Pak Joko', '086789012345', 'Junior'),
(1, 'Pak Rian', '087890123456', 'Baru');

-- ------------------------------------------------------------
-- Tabel: projects
-- ------------------------------------------------------------
CREATE TABLE `projects` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `workshop_id` INT UNSIGNED NOT NULL,
  `client_id` INT UNSIGNED NOT NULL,
  `nama_project` VARCHAR(255) NOT NULL,
  `deskripsi` TEXT,
  `biaya_total` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `status_pembayaran` ENUM('Belum Lunas','Lunas') NOT NULL DEFAULT 'Belum Lunas',
  `status_project` ENUM('Aktif','Selesai','Ditunda') NOT NULL DEFAULT 'Aktif',
  `tgl_mulai` DATE,
  `tgl_selesai` DATE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`workshop_id`) REFERENCES `workshops`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`client_id`) REFERENCES `clients`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `projects` (`workshop_id`, `client_id`, `nama_project`, `deskripsi`, `biaya_total`, `status_pembayaran`, `status_project`, `tgl_mulai`) VALUES
(1, 1, 'Renovasi Ruang Tamu Budi', 'Pemasangan furniture ruang tamu full set', 45000000.00, 'Belum Lunas', 'Aktif', '2026-07-01'),
(1, 2, 'Interior Kamar Tidur Siti', 'Desain dan pemasangan interior kamar tidur utama', 28000000.00, 'Belum Lunas', 'Aktif', '2026-07-10'),
(1, 3, 'Kitchen Set Andi', 'Pembuatan kitchen set custom', 32000000.00, 'Lunas', 'Selesai', '2026-06-15');

-- ------------------------------------------------------------
-- Tabel: project_payments (Termin/Cicilan Proyek)
-- ------------------------------------------------------------
CREATE TABLE `project_payments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `project_id` INT UNSIGNED NOT NULL,
  `jenis` ENUM('DP','Termin') NOT NULL DEFAULT 'DP',
  `nama_pembayaran` VARCHAR(150) NOT NULL COMMENT 'Contoh: DP, Termin 1, Termin 2, Pelunasan',
  `jumlah` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `tgl` DATE NOT NULL,
  `keterangan` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `project_payments` (`project_id`, `jenis`, `nama_pembayaran`, `jumlah`, `tgl`) VALUES
(1, 'DP', 'DP Awal', 15000000.00, '2026-07-01'),
(1, 'Termin', 'Termin 1', 10000000.00, '2026-07-15'),
(2, 'DP', 'DP Awal', 10000000.00, '2026-07-10'),
(3, 'DP', 'DP Awal', 10000000.00, '2026-06-15'),
(3, 'Termin', 'Termin 1', 12000000.00, '2026-06-25'),
(3, 'Termin', 'Pelunasan', 10000000.00, '2026-07-05');

-- ------------------------------------------------------------
-- Tabel: expenses (Pengeluaran)
-- ------------------------------------------------------------
CREATE TABLE `expenses` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `workshop_id` INT UNSIGNED NOT NULL,
  `project_id` INT UNSIGNED NULL COMMENT 'NULL jika pengeluaran operasional umum',
  `kategori` VARCHAR(100) NOT NULL COMMENT 'Contoh: Bahan Baku, Transportasi, Operasional',
  `keterangan` TEXT,
  `jumlah` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `tgl` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`workshop_id`) REFERENCES `workshops`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `expenses` (`workshop_id`, `project_id`, `kategori`, `keterangan`, `jumlah`, `tgl`) VALUES
(1, 1, 'Bahan Baku', 'Pembelian kayu jati untuk proyek Budi', 8000000.00, '2026-07-02'),
(1, 1, 'Transportasi', 'Pengiriman material proyek Budi', 500000.00, '2026-07-03'),
(1, NULL, 'Operasional', 'Listrik workshop bulan Juli', 1200000.00, '2026-07-01'),
(1, 2, 'Bahan Baku', 'Pembelian material interior Siti', 5000000.00, '2026-07-11');

-- ------------------------------------------------------------
-- Tabel: worker_ledgers (Buku Besar Upah Tukang)
-- ------------------------------------------------------------
CREATE TABLE `worker_ledgers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `workshop_id` INT UNSIGNED NOT NULL,
  `worker_id` INT UNSIGNED NOT NULL,
  `project_id` INT UNSIGNED NULL COMMENT 'NULL jika penarikan tunai tanpa proyek spesifik',
  `jenis` ENUM('Hak_Upah','Tarik_Tunai') NOT NULL,
  `keterangan` TEXT,
  `jumlah` DECIMAL(15,2) NOT NULL DEFAULT 0.00,
  `tgl` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`workshop_id`) REFERENCES `workshops`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`worker_id`) REFERENCES `workers`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `worker_ledgers` (`workshop_id`, `worker_id`, `project_id`, `jenis`, `keterangan`, `jumlah`, `tgl`) VALUES
(1, 1, 1, 'Hak_Upah', 'Upah pengerjaan proyek Budi (Ruang Tamu)', 6000000.00, '2026-07-15'),
(1, 1, NULL, 'Tarik_Tunai', 'Penarikan uang muka', 3000000.00, '2026-07-16'),
(1, 2, 1, 'Hak_Upah', 'Upah helper proyek Budi', 3000000.00, '2026-07-15'),
(1, 2, NULL, 'Tarik_Tunai', 'Tarik tunai', 1500000.00, '2026-07-18'),
(1, 3, 2, 'Hak_Upah', 'Upah proyek Siti (Interior Kamar)', 2500000.00, '2026-07-20');

-- ============================================================
-- INDEX untuk performa query
-- ============================================================
ALTER TABLE `clients` ADD INDEX `idx_workshop` (`workshop_id`);
ALTER TABLE `workers` ADD INDEX `idx_workshop` (`workshop_id`);
ALTER TABLE `projects` ADD INDEX `idx_workshop` (`workshop_id`);
ALTER TABLE `projects` ADD INDEX `idx_client` (`client_id`);
ALTER TABLE `project_payments` ADD INDEX `idx_project` (`project_id`);
ALTER TABLE `expenses` ADD INDEX `idx_workshop` (`workshop_id`);
ALTER TABLE `expenses` ADD INDEX `idx_project` (`project_id`);
ALTER TABLE `worker_ledgers` ADD INDEX `idx_workshop` (`workshop_id`);
ALTER TABLE `worker_ledgers` ADD INDEX `idx_worker` (`worker_id`);
ALTER TABLE `worker_ledgers` ADD INDEX `idx_project` (`project_id`);
