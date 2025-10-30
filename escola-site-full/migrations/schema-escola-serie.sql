-- SCHEMA COMPLETO (fresh install) — modelo escola/série
-- Executar no MySQL (phpMyAdmin → SQL ou mysql CLI)

-- 1) Banco de dados
CREATE DATABASE IF NOT EXISTS escola_site
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE escola_site;

-- 2) Tabela de usuários
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','funcionario') NOT NULL DEFAULT 'funcionario',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) Tabela de leads (novo modelo: nome, telefone, escola, serie)
DROP TABLE IF EXISTS leads;
CREATE TABLE leads (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  telefone VARCHAR(40),
  escola VARCHAR(160),
  serie VARCHAR(80),
  origem VARCHAR(120) DEFAULT 'CSV',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  assigned_to INT UNSIGNED NULL,
  CONSTRAINT fk_leads_user FOREIGN KEY (assigned_to)
    REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_leads_telefone (telefone),
  INDEX idx_leads_escola (escola)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4) Usuário administrador inicial
--    e-mail: admin@escola.com | senha: admin123  (troque após o 1º login)
INSERT INTO users (name, email, password_hash, role) VALUES
('Administrador', 'admin@escola.com', '$2y$10$0C7n4xIh7mZ9lS1sVZQpTuNkR9v7e6fXxw8v6Fv2mJj9tQkQeI.ye', 'admin');
