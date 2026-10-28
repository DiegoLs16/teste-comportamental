-- Execute no phpMyAdmin (aba SQL) ou: mysql -u root < banco.sql
CREATE DATABASE IF NOT EXISTS teste_comportamental
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE teste_comportamental;

CREATE TABLE IF NOT EXISTS usuarios (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome       VARCHAR(60)  NOT NULL,
  email      VARCHAR(120) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  criado_em  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS resultados (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id    INT UNSIGNED NOT NULL,
  nome_usado    VARCHAR(60)  NOT NULL,
  pontos_aguia  TINYINT UNSIGNED NOT NULL,
  pontos_gato   TINYINT UNSIGNED NOT NULL,
  pontos_lobo   TINYINT UNSIGNED NOT NULL,
  pontos_tubarao TINYINT UNSIGNED NOT NULL,
  predominantes VARCHAR(60)  NOT NULL,   -- ex.: "aguia" ou "aguia,lobo" (empate)
  criado_em     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;
