-- ARTE21 - Carte de membre
-- Schéma de base de données

CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username`   VARCHAR(100) NOT NULL UNIQUE,
    `password`   VARCHAR(255) NOT NULL,
    `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mot de passe par défaut : ChangeMe2024!
-- À changer immédiatement après installation
-- Généré avec : password_hash('ChangeMe2024!', PASSWORD_BCRYPT)
INSERT INTO `users` (`username`, `password`) VALUES
('admin', '$2y$12$YwU9xBluHQ.IYDRhP2dn.ebVEoYBkflmEKHBv1.FPaHxVd/moxg/G');

CREATE TABLE IF NOT EXISTS `membres` (
    `id`              INT UNSIGNED   NOT NULL AUTO_INCREMENT,
    `reference`       VARCHAR(50)    NOT NULL UNIQUE,
    `numero_serie`    INT UNSIGNED   NOT NULL,
    `annee_creation`  SMALLINT       NOT NULL,
    `nom`             VARCHAR(100)   NOT NULL,
    `prenom`          VARCHAR(100)   NOT NULL,
    `date_naissance`  DATE           NOT NULL,
    `date_inscription`DATE           NOT NULL,
    `duree_validite`  TINYINT UNSIGNED NOT NULL COMMENT 'Durée en années',
    `date_validite`   DATE           NOT NULL,
    `photo_path`      VARCHAR(255)   DEFAULT NULL,
    `created_at`      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_numero_annee` (`annee_creation`, `numero_serie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
