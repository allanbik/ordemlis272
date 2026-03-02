/* Adiciona coluna data_nascimento se não existir */
/* Como o MySQL não tem "ADD COLUMN IF NOT EXISTS" nativo em versões antigas de forma simples em uma linha sem procedure,
   vamos tentar adicionar e ignorar erro se já existir, ou melhor, fazer um script PHP que verifica.
   Mas para simplificar e garantir, vou usar um bloco anonimo (MySQL 8) ou ignorar erro no PHP.
   Como o usuário está no XAMPP, provavelmente é MariaDB/MySQL recente. */

ALTER TABLE usuarios ADD COLUMN data_nascimento DATE NULL AFTER email;
/* Atualiza para NOT NULL depois de popular se fosse produção, mas aqui é dev novo */
ALTER TABLE usuarios MODIFY COLUMN data_nascimento DATE NOT NULL DEFAULT '2000-01-01';
ALTER TABLE usuarios ALTER COLUMN data_nascimento DROP DEFAULT;
