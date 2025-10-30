-- Crie o banco de dados (rode apenas uma vez)
CREATE DATABASE IF NOT EXISTS escola_site
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE escola_site;

-- ==========================
-- Tabela de usuários
-- ==========================
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','funcionario') NOT NULL DEFAULT 'funcionario',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================
-- Tabela para dados importados do CSV
-- ==========================
CREATE TABLE IF NOT EXISTS leads (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  email VARCHAR(160),
  telefone VARCHAR(40),
  curso VARCHAR(160),
  origem VARCHAR(120) DEFAULT 'CSV',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  assigned_to INT UNSIGNED NULL,
  CONSTRAINT fk_leads_user FOREIGN KEY (assigned_to)
    REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================
-- Usuário administrador inicial
-- senha: admin123
-- ==========================
INSERT IGNORE INTO users (name, email, password_hash, role)
VALUES (
  'Administrador',
  'admin@escola.com',
  '$2y$10$0C7n4xIh7mZ9lS1sVZQpTuNkR9v7e6fXxw8v6Fv2mJj9tQkQeI.ye',
  'admin'
);
