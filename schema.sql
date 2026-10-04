-- Zero-Knowledge One-Time Secret Sharer Tablo Yapısı
CREATE DATABASE IF NOT EXISTS zk_secret_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE zk_secret_db;

CREATE TABLE IF NOT EXISTS secrets (
    id VARCHAR(64) NOT NULL,
    ciphertext TEXT NOT NULL,
    iv VARCHAR(64) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB;