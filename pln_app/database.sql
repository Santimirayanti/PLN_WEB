CREATE DATABASE pln_tracker;
USE pln_tracker;

-- Tabel untuk menyimpan informasi pekerjaan
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    status ENUM('Belum Dimulai', 'Dalam Progress', 'Selesai') NOT NULL DEFAULT 'Belum Dimulai',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel untuk menyimpan file yang diunggah
CREATE TABLE uploads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    project_id INT NOT NULL,
    category VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE
);

-- Tabel untuk menyimpan data pengguna (opsional)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'User') NOT NULL DEFAULT 'User',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Menambahkan contoh data awal
INSERT INTO projects (name, status) VALUES ('Pekerjaan A', 'Belum Dimulai'), ('Pekerjaan B', 'Dalam Progress');
