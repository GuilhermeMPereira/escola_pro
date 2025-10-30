-- Adiciona coluna de status e índice
USE escola_site;

ALTER TABLE leads
  ADD COLUMN status ENUM('pendente','aceito','interessado','nao_quero') NOT NULL DEFAULT 'pendente' AFTER serie,
  ADD KEY idx_leads_status (status);
