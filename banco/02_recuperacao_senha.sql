USE ordemdelis;

CREATE TABLE IF NOT EXISTS recuperacao_senha (
  id              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  usuario_id      BIGINT UNSIGNED NOT NULL,

  token_hash      VARCHAR(255) NOT NULL,        -- hash do token (NUNCA guardar token puro)
  criado_em       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expira_em       DATETIME NOT NULL,
  usado_em        DATETIME NULL,

  ip_solicitacao  VARCHAR(45) NULL,             -- IPv4/IPv6
  user_agent      VARCHAR(255) NULL,

  PRIMARY KEY (id),
  KEY ix_rec_usuario (usuario_id),
  KEY ix_rec_expira (expira_em),
  KEY ix_rec_usado (usado_em),

  CONSTRAINT fk_rec_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON DELETE CASCADE
) ENGINE=InnoDB;
