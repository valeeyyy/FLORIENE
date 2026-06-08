CREATE DATABASE IF NOT EXISTS floriene_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE floriene_db;

CREATE TABLE IF NOT EXISTS user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nomor_hp VARCHAR(20),
    role ENUM('ADMIN', 'CUSTOMER') DEFAULT 'CUSTOMER',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(255) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_nama (nama)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS produk (
    id_produk INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(255) NOT NULL,
    deskripsi_produk TEXT,
    harga DECIMAL(10, 2) NOT NULL,
    warna VARCHAR(100),
    status ENUM('active', 'inactive') DEFAULT 'active',
    url_foto_produk VARCHAR(500),
    id_kategori INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE SET NULL,
    INDEX idx_nama (nama_produk),
    INDEX idx_status (status),
    INDEX idx_kategori (id_kategori)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pesanan (
    id_pesanan INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    total_harga DECIMAL(12, 2) NOT NULL DEFAULT 0,
    tanggal_pengiriman DATE,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE,
    INDEX idx_user (id_user),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pesanan_detail (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_pesanan INT NOT NULL,
    id_produk INT NOT NULL,
    jumlah INT NOT NULL DEFAULT 1,
    harga_satuan DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(12, 2) NOT NULL,
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id_pesanan) ON DELETE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id_produk) ON DELETE RESTRICT,
    INDEX idx_pesanan (id_pesanan),
    INDEX idx_produk (id_produk)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS loyalty_card (
    id_loyalty_card INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL UNIQUE,
    poin INT DEFAULT 0,
    last_update_poin TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE,
    INDEX idx_poin (poin)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS loyalty_redemption (
    id_redemption INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    poin_digunakan INT NOT NULL,
    warna_bloomies VARCHAR(100),
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE,
    INDEX idx_user (id_user),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO user (nama, email, password, nomor_hp, role)
VALUES ('Admin Floriene', 'admin@floriene.com', '1234567890', '6281234567890', 'ADMIN')
ON DUPLICATE KEY UPDATE id_user = id_user;

INSERT INTO kategori (id_kategori, nama, deskripsi) VALUES
(1, 'Bloomies Keychain', 'Adorable handmade flower keychains made from colorful pipe cleaners'),
(2, 'Everbloom Single Stem', 'Beautiful handcrafted single stem flowers that last forever')
ON DUPLICATE KEY UPDATE id_kategori = id_kategori;

INSERT INTO produk (id_produk, id_kategori, nama_produk, warna, harga, deskripsi_produk, url_foto_produk, status) VALUES
(1, 1, 'Bloomies Lily', 'Pink · Blue · Purple', 15000.00, 'Cute handmade pipe cleaner lily keychain, available in baby pink, baby blue, and lilac purple.', 'img/lbloomies.jpeg', 'active'),
(5, 1, 'Bloomies Sunflower', 'Yellow', 18000.00, 'Sunny and adorable sunflower keychain made from vibrant yellow pipe cleaners', 'img/sbloomies.jpeg', 'active'),
(6, 2, 'Everbloom Tulip (Baby Pink)', 'Baby Pink', 22000.00, 'Sweet and elegant single stem tulip in soft baby pink color', 'img/babypink.jpeg', 'active'),
(7, 2, 'Everbloom Tulip (Hot Pink)', 'Hot Pink', 22000.00, 'Bold and vibrant hot pink tulip single stem that never wilts', 'img/hotpink.jpeg', 'active'),
(8, 2, 'Everbloom Sunflower', 'Yellow', 25000.00, 'Bright and cheerful sunflower single stem, full of life and color', 'img/sunflower.jpeg', 'active'),
(9, 2, 'Everbloom Peony', 'Purple', 30000.00, 'Premium luxurious peony single stem in regal purple - a statement piece', 'img/peony.jpeg', 'active')
ON DUPLICATE KEY UPDATE id_produk = id_produk;
