-- 1. Buat tabel utama untuk menyimpan proyek
CREATE TABLE IF NOT EXISTS `projects` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `team` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `signature` LONGTEXT NULL, -- Menyimpan data gambar TTD Base64
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Buat tabel relasi untuk menyimpan unggahan berkas (Multiple Files)
CREATE TABLE IF NOT EXISTS `project_files` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `project_id` INT NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_size` VARCHAR(50) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `is_doc` TINYINT(1) DEFAULT 0, -- 1 untuk PDF/Doc, 0 untuk Gambar/Mockup
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;