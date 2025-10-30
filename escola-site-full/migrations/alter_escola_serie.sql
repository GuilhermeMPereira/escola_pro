-- Adiciona as colunas solicitadas no modelo "escola/serie"
USE escola_site;

ALTER TABLE leads
  ADD COLUMN escola VARCHAR(160) NULL AFTER telefone,
  ADD COLUMN serie  VARCHAR(80)  NULL AFTER escola;

-- Opcional: remover colunas antigas que não serão usadas (execute apenas se tiver certeza)
-- ALTER TABLE leads DROP COLUMN email, DROP COLUMN curso;
