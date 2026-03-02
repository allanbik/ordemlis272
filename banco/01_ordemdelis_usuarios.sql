/* =========================================================
   Projeto Final 2026 - Grupo Escoteiro Ordem de Lis 272/PR
   MySQL 8+
   Script: cria banco "ordemdelis" e tabela "usuarios"
   ========================================================= */

-- (Opcional) remover banco para recriar do zero
-- DROP DATABASE IF EXISTS ordemdelis;

CREATE DATABASE IF NOT EXISTS ordemdelis
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE ordemdelis;

-- =========================================================
-- Tabela: usuarios
-- Armazena login e dados básicos do responsável/jovem/chefia/adm
-- =========================================================
CREATE TABLE IF NOT EXISTS usuarios (
  id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

  -- Identificação
  cpf               CHAR(11) NOT NULL,               -- somente números
  nome              VARCHAR(150) NOT NULL,
  email             VARCHAR(190) NOT NULL,
  data_nascimento   DATE NOT NULL,

  -- Perfil / atuação
  perfil            ENUM('jovem','chefe','adm') NOT NULL DEFAULT 'jovem',
  ramo              ENUM('lobinho','escoteiro','senior','pioneiro','chefia','adm') NOT NULL DEFAULT 'lobinho',

  -- Autenticação
  senha_hash        VARCHAR(255) NOT NULL,           -- bcrypt/argon2 (gerado no PHP)
  senha_alg         ENUM('bcrypt','argon2id','outro') NOT NULL DEFAULT 'bcrypt',
  senha_updated_at  DATETIME NULL,

  -- Segurança / controle
  ativo             TINYINT(1) NOT NULL DEFAULT 1,
  email_verificado  TINYINT(1) NOT NULL DEFAULT 0,
  tentativas_login  INT UNSIGNED NOT NULL DEFAULT 0,
  bloqueado_ate     DATETIME NULL,
  last_login_at     DATETIME NULL,

  -- Auditoria
  created_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (id),

  -- Unicidade
  UNIQUE KEY uq_usuarios_cpf (cpf),
  UNIQUE KEY uq_usuarios_email (email),

  -- Índices úteis
  KEY ix_usuarios_perfil (perfil),
  KEY ix_usuarios_ramo (ramo),
  KEY ix_usuarios_ativo (ativo)
) ENGINE=InnoDB;

-- =========================================================
-- (Opcional) Usuário administrador padrão (troque depois)
-- CPF: 00000000000
-- Senha: gerar no PHP e substituir abaixo (hash bcrypt)
-- =========================================================
INSERT INTO usuarios (cpf, nome, email, perfil, ramo, senha_hash, senha_alg, email_verificado, ativo)
VALUES
('00000000000', 'Administrador', 'admin@ordemdelis272.local', 'adm', 'adm',
 '$2y$10$YhZ9c0Z2z8cX5jQe9mVg2uPq3m0lYx5fYd2g5yBq7n7aQ8tqBfW4G', -- hash exemplo (substituir)
 'bcrypt', 1, 1)
ON DUPLICATE KEY UPDATE
  nome=VALUES(nome),
  email=VALUES(email),
  perfil=VALUES(perfil),
  ramo=VALUES(ramo),
  ativo=VALUES(ativo);

-- =========================================================
-- Regras/observações:
-- - CPF deve ser gravado SEM máscara (somente números)
-- - senha_hash deve ser gerado no back-end (password_hash no PHP)
-- - ramo "chefia" é usado quando perfil=chefe (e pode manter ramo real também)
-- =========================================================
