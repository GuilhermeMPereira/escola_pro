-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 30/10/2025 às 03:01
-- Versão do servidor: 9.1.0
-- Versão do PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `escola_site`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `leads`
--

DROP TABLE IF EXISTS `leads`;
CREATE TABLE IF NOT EXISTS `leads` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `telefone` varchar(40) DEFAULT NULL,
  `escola` varchar(160) DEFAULT NULL,
  `serie` varchar(80) DEFAULT NULL,
  `status` enum('pendente','aceito','interessado','nao_quero') NOT NULL DEFAULT 'pendente',
  `origem` varchar(120) DEFAULT 'CSV',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `assigned_to` int UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_leads_user` (`assigned_to`),
  KEY `idx_leads_telefone` (`telefone`),
  KEY `idx_leads_escola` (`escola`),
  KEY `idx_leads_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `leads`
--

INSERT INTO `leads` (`id`, `nome`, `telefone`, `escola`, `serie`, `status`, `origem`, `created_at`, `assigned_to`) VALUES
(1, 'debora linda', '11940028622', 'fasm', '3º ano', 'pendente', 'CSV', '2025-10-30 02:46:27', 4),
(2, 'sabotage', '11904200420', 'mauro matheus', '4º ano', 'nao_quero', 'CSV', '2025-10-30 02:46:27', 4),
(3, 'Pedro Barbosa', '1191567-3597', 'Black sabah', '6º ano', 'pendente', 'CSV', '2025-10-30 02:51:22', 4),
(4, 'Thais de Lima', '1196874-3741', 'Colégio Evanescencia', '3º ano', 'pendente', 'CSV', '2025-10-30 02:51:22', 4),
(5, 'Guilherme Aparecido', '1197171-4715', 'Twenty one Pilots', '8º ano', 'pendente', 'CSV', '2025-10-30 02:51:22', 4);

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(120) NOT NULL,
  `email` varchar(160) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','funcionario') NOT NULL DEFAULT 'funcionario',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Administrador', 'admin@escola.com', '$2y$10$0C7n4xIh7mZ9lS1sVZQpTuNkR9v7e6fXxw8v6Fv2mJj9tQkQeI.ye', 'admin', '2025-10-30 02:17:55'),
(3, 'Vereador', 'vereador@gmail.com', '$2y$10$NHEGnNtfLfSuP1NyIddVNeiiZHashmQIYrxAwEc2bLarJ94eUgtIi', 'admin', '2025-10-30 02:33:09'),
(4, 'Guilherme', 'guilherme.@gmail.com', '$2y$10$1FDy5Em6r4vZddQjBnW2E.90P57/vXARgUbpBXsRo5C2fKW7JYpa6', 'funcionario', '2025-10-30 02:35:19');

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `fk_leads_user` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
