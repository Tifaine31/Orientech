USE `course_orientation`;

DROP PROCEDURE IF EXISTS sp_add_col;
DELIMITER $$
CREATE PROCEDURE sp_add_col(IN p_table VARCHAR(64), IN p_col VARCHAR(64), IN p_def TEXT)
BEGIN
  IF NOT EXISTS (
    SELECT 1
    FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = p_table
      AND COLUMN_NAME = p_col
  ) THEN
    SET @sql = CONCAT('ALTER TABLE `', p_table, '` ADD COLUMN ', p_def);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END$$
DELIMITER ;

-- utilisateur compatibility
CALL sp_add_col('utilisateur', 'id_utilisateur', '`id_utilisateur` INT NULL');
CALL sp_add_col('utilisateur', 'password', '`password` VARCHAR(255) NULL');
CALL sp_add_col('utilisateur', 'acreditation', '`acreditation` VARCHAR(50) NULL');
CALL sp_add_col('utilisateur', 'photo_profil', '`photo_profil` VARCHAR(255) NULL');

-- classe compatibility
CALL sp_add_col('classe', 'id_classe', '`id_classe` INT NULL');

-- liaison_classe compatibility
CALL sp_add_col('liaison_classe', 'id_utilisateur', '`id_utilisateur` INT NULL');
CALL sp_add_col('liaison_classe', 'date_debut', '`date_debut` DATE NULL');
CALL sp_add_col('liaison_classe', 'date_fin', '`date_fin` DATE NULL');

-- seance compatibility
CALL sp_add_col('seance', 'id_seance', '`id_seance` INT NULL');
CALL sp_add_col('seance', 'date_heure', '`date_heure` DATETIME NULL');

-- parcours compatibility
CALL sp_add_col('parcours', 'numero_du_parcours', '`numero_du_parcours` INT NULL');
CALL sp_add_col('parcours', 'commentaire', '`commentaire` VARCHAR(255) NULL');
CALL sp_add_col('parcours', 'nb_balises', '`nb_balises` INT NOT NULL DEFAULT 0');

-- boitier compatibility
CALL sp_add_col('boitier', 'numero', '`numero` INT NULL');
CALL sp_add_col('boitier', 'add_mac', '`add_mac` VARCHAR(50) NULL');
CALL sp_add_col('boitier', 'add_reseau', '`add_reseau` VARCHAR(50) NULL');
CALL sp_add_col('boitier', 'balisevalid', '`balisevalid` VARCHAR(50) NULL');

-- localisation compatibility
CALL sp_add_col('localisation', 'id_loc', '`id_loc` INT NULL');
CALL sp_add_col('localisation', 'numero_boitier', '`numero_boitier` INT NULL');
CALL sp_add_col('localisation', 'numerodecarte', '`numerodecarte` INT NULL');

-- resultat compatibility
CALL sp_add_col('resultat', 'noteID', '`noteID` INT NULL');

DROP PROCEDURE IF EXISTS sp_add_col;

-- Legacy tables expected by classic PHP pages
CREATE TABLE IF NOT EXISTS `note` (
  `noteID` INT NOT NULL AUTO_INCREMENT,
  `id_utilisateur` INT NULL,
  `note` DECIMAL(5,2) NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`noteID`),
  KEY `idx_note_user` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `seance_parcours` (
  `id_seance` INT NOT NULL,
  `id_parcours` INT NOT NULL,
  PRIMARY KEY (`id_seance`, `id_parcours`),
  KEY `idx_sp_parcours` (`id_parcours`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `seance_boitier` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `id_seance` INT NOT NULL,
  `id_eleve` INT NULL,
  `numero_boitier` INT NULL,
  `assigned_at` DATETIME NULL,
  `ended_at` DATETIME NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sb_seance` (`id_seance`),
  KEY `idx_sb_boitier` (`numero_boitier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `seance_resultat` (
  `id_resultat` INT NOT NULL AUTO_INCREMENT,
  `id_seance` INT NOT NULL,
  `id_parcours` INT NOT NULL,
  `nb_valides` INT NOT NULL DEFAULT 0,
  `nb_invalides` INT NOT NULL DEFAULT 0,
  `note_auto` DECIMAL(5,2) NOT NULL DEFAULT 0,
  `note_finale` DECIMAL(5,2) NOT NULL DEFAULT 0,
  `use_auto` TINYINT(1) NOT NULL DEFAULT 1,
  `heure_debut` TIME NULL,
  `heure_fin` TIME NULL,
  `duree` VARCHAR(20) NULL,
  PRIMARY KEY (`id_resultat`),
  KEY `idx_sr_seance` (`id_seance`),
  KEY `idx_sr_parcours` (`id_parcours`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `composer` (
  `numero_du_parcours` INT NOT NULL,
  `numerodecarte` INT NOT NULL,
  `ordre` INT NOT NULL DEFAULT 1,
  PRIMARY KEY (`numero_du_parcours`, `numerodecarte`),
  KEY `idx_composer_ordre` (`numero_du_parcours`, `ordre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE OR REPLACE VIEW `balise_RFID` AS
SELECT b.id AS numerodecarte, b.tag AS tagRFID
FROM balise b;

-- Backfill values
UPDATE utilisateur
SET id_utilisateur = id
WHERE id_utilisateur IS NULL OR id_utilisateur <> id;

UPDATE utilisateur
SET password = mdp
WHERE (password IS NULL OR password = '') AND mdp IS NOT NULL;

UPDATE utilisateur
SET mdp = password
WHERE (mdp IS NULL OR mdp = '') AND password IS NOT NULL;

UPDATE utilisateur
SET acreditation = role
WHERE (acreditation IS NULL OR acreditation = '') AND role IS NOT NULL;

UPDATE utilisateur
SET role = acreditation
WHERE (role IS NULL OR role = '') AND acreditation IS NOT NULL;

UPDATE utilisateur
SET photo_profil = photo
WHERE (photo_profil IS NULL OR photo_profil = '') AND photo IS NOT NULL;

UPDATE utilisateur
SET photo = photo_profil
WHERE (photo IS NULL OR photo = '') AND photo_profil IS NOT NULL;

UPDATE classe
SET id_classe = id
WHERE id_classe IS NULL OR id_classe <> id;

UPDATE liaison_classe
SET id_utilisateur = id_eleve
WHERE id_utilisateur IS NULL AND id_eleve IS NOT NULL;

UPDATE liaison_classe
SET id_eleve = id_utilisateur
WHERE id_eleve IS NULL AND id_utilisateur IS NOT NULL;

UPDATE liaison_classe
SET date_debut = CURDATE()
WHERE date_debut IS NULL;

UPDATE seance
SET id_seance = id
WHERE id_seance IS NULL OR id_seance <> id;

UPDATE seance
SET date_heure = date_debut
WHERE date_heure IS NULL AND date_debut IS NOT NULL;

UPDATE seance
SET date_debut = date_heure
WHERE date_debut IS NULL AND date_heure IS NOT NULL;

UPDATE parcours
SET numero_du_parcours = id
WHERE numero_du_parcours IS NULL OR numero_du_parcours <> id;

UPDATE parcours
SET commentaire = description
WHERE (commentaire IS NULL OR commentaire = '') AND description IS NOT NULL;

UPDATE parcours
SET description = commentaire
WHERE (description IS NULL OR description = '') AND commentaire IS NOT NULL;

UPDATE boitier
SET numero = id
WHERE numero IS NULL OR numero <> id;

UPDATE boitier
SET add_mac = mac
WHERE (add_mac IS NULL OR add_mac = '') AND mac IS NOT NULL;

UPDATE boitier
SET mac = add_mac
WHERE (mac IS NULL OR mac = '') AND add_mac IS NOT NULL;

UPDATE boitier
SET add_reseau = reseau
WHERE (add_reseau IS NULL OR add_reseau = '') AND reseau IS NOT NULL;

UPDATE boitier
SET reseau = add_reseau
WHERE (reseau IS NULL OR reseau = '') AND add_reseau IS NOT NULL;

UPDATE localisation
SET id_loc = id
WHERE id_loc IS NULL OR id_loc <> id;

UPDATE localisation
SET numero_boitier = id_boitier
WHERE numero_boitier IS NULL AND id_boitier IS NOT NULL;

UPDATE localisation
SET id_boitier = numero_boitier
WHERE id_boitier IS NULL AND numero_boitier IS NOT NULL;

-- Seed composer from compose_parcours if present
INSERT IGNORE INTO composer (numero_du_parcours, numerodecarte, ordre)
SELECT id_parcours, id_balise, id_balise
FROM compose_parcours;

UPDATE parcours p
SET nb_balises = (
  SELECT COUNT(*)
  FROM composer c
  WHERE c.numero_du_parcours = p.numero_du_parcours
);

-- Triggers to keep legacy/current columns synchronized
DROP TRIGGER IF EXISTS trg_utilisateur_bi;
DROP TRIGGER IF EXISTS trg_utilisateur_bu;
DROP TRIGGER IF EXISTS trg_utilisateur_ai;
DELIMITER $$
CREATE TRIGGER trg_utilisateur_bi BEFORE INSERT ON utilisateur
FOR EACH ROW
BEGIN
  IF NEW.id IS NULL AND NEW.id_utilisateur IS NOT NULL THEN SET NEW.id = NEW.id_utilisateur; END IF;

  IF (NEW.password IS NULL OR NEW.password = '') AND NEW.mdp IS NOT NULL THEN SET NEW.password = NEW.mdp; END IF;
  IF (NEW.mdp IS NULL OR NEW.mdp = '') AND NEW.password IS NOT NULL THEN SET NEW.mdp = NEW.password; END IF;

  IF (NEW.acreditation IS NULL OR NEW.acreditation = '') AND NEW.role IS NOT NULL THEN SET NEW.acreditation = NEW.role; END IF;
  IF (NEW.role IS NULL OR NEW.role = '') AND NEW.acreditation IS NOT NULL THEN SET NEW.role = NEW.acreditation; END IF;

  IF (NEW.photo_profil IS NULL OR NEW.photo_profil = '') AND NEW.photo IS NOT NULL THEN SET NEW.photo_profil = NEW.photo; END IF;
  IF (NEW.photo IS NULL OR NEW.photo = '') AND NEW.photo_profil IS NOT NULL THEN SET NEW.photo = NEW.photo_profil; END IF;
END$$
CREATE TRIGGER trg_utilisateur_bu BEFORE UPDATE ON utilisateur
FOR EACH ROW
BEGIN
  SET NEW.id_utilisateur = NEW.id;

  IF NEW.password IS NOT NULL AND NEW.password <> '' THEN SET NEW.mdp = NEW.password;
  ELSEIF NEW.mdp IS NOT NULL AND NEW.mdp <> '' THEN SET NEW.password = NEW.mdp;
  END IF;

  IF NEW.acreditation IS NOT NULL AND NEW.acreditation <> '' THEN SET NEW.role = NEW.acreditation;
  ELSEIF NEW.role IS NOT NULL AND NEW.role <> '' THEN SET NEW.acreditation = NEW.role;
  END IF;

  IF NEW.photo_profil IS NOT NULL AND NEW.photo_profil <> '' THEN SET NEW.photo = NEW.photo_profil;
  ELSEIF NEW.photo IS NOT NULL AND NEW.photo <> '' THEN SET NEW.photo_profil = NEW.photo;
  END IF;
END$$
CREATE TRIGGER trg_utilisateur_ai AFTER INSERT ON utilisateur
FOR EACH ROW
BEGIN
  UPDATE utilisateur
  SET id_utilisateur = NEW.id
  WHERE id = NEW.id AND (id_utilisateur IS NULL OR id_utilisateur <> NEW.id);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS trg_classe_bi;
DROP TRIGGER IF EXISTS trg_classe_bu;
DROP TRIGGER IF EXISTS trg_classe_ai;
DELIMITER $$
CREATE TRIGGER trg_classe_bi BEFORE INSERT ON classe
FOR EACH ROW
BEGIN
  IF NEW.id IS NULL AND NEW.id_classe IS NOT NULL THEN SET NEW.id = NEW.id_classe; END IF;
END$$
CREATE TRIGGER trg_classe_bu BEFORE UPDATE ON classe
FOR EACH ROW
BEGIN
  SET NEW.id_classe = NEW.id;
END$$
CREATE TRIGGER trg_classe_ai AFTER INSERT ON classe
FOR EACH ROW
BEGIN
  UPDATE classe SET id_classe = NEW.id WHERE id = NEW.id AND (id_classe IS NULL OR id_classe <> NEW.id);
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS trg_liaison_bi;
DROP TRIGGER IF EXISTS trg_liaison_bu;
DELIMITER $$
CREATE TRIGGER trg_liaison_bi BEFORE INSERT ON liaison_classe
FOR EACH ROW
BEGIN
  IF NEW.id_eleve IS NULL AND NEW.id_utilisateur IS NOT NULL THEN SET NEW.id_eleve = NEW.id_utilisateur; END IF;
  IF NEW.id_utilisateur IS NULL AND NEW.id_eleve IS NOT NULL THEN SET NEW.id_utilisateur = NEW.id_eleve; END IF;
  IF NEW.date_debut IS NULL THEN SET NEW.date_debut = CURDATE(); END IF;
END$$
CREATE TRIGGER trg_liaison_bu BEFORE UPDATE ON liaison_classe
FOR EACH ROW
BEGIN
  IF NEW.id_eleve IS NULL AND NEW.id_utilisateur IS NOT NULL THEN SET NEW.id_eleve = NEW.id_utilisateur; END IF;
  IF NEW.id_utilisateur IS NULL AND NEW.id_eleve IS NOT NULL THEN SET NEW.id_utilisateur = NEW.id_eleve; END IF;
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS trg_seance_bi;
DROP TRIGGER IF EXISTS trg_seance_bu;
DROP TRIGGER IF EXISTS trg_seance_ai;
DELIMITER $$
CREATE TRIGGER trg_seance_bi BEFORE INSERT ON seance
FOR EACH ROW
BEGIN
  IF NEW.id IS NULL AND NEW.id_seance IS NOT NULL THEN SET NEW.id = NEW.id_seance; END IF;

  IF NEW.date_heure IS NOT NULL THEN SET NEW.date_debut = NEW.date_heure;
  ELSEIF NEW.date_debut IS NOT NULL THEN SET NEW.date_heure = NEW.date_debut;
  END IF;
END$$
CREATE TRIGGER trg_seance_bu BEFORE UPDATE ON seance
FOR EACH ROW
BEGIN
  SET NEW.id_seance = NEW.id;

  IF NEW.date_heure IS NOT NULL THEN SET NEW.date_debut = NEW.date_heure;
  ELSEIF NEW.date_debut IS NOT NULL THEN SET NEW.date_heure = NEW.date_debut;
  END IF;
END$$
CREATE TRIGGER trg_seance_ai AFTER INSERT ON seance
FOR EACH ROW
BEGIN
  UPDATE seance
  SET id_seance = NEW.id,
      date_heure = COALESCE(date_heure, date_debut),
      date_debut = COALESCE(date_debut, date_heure)
  WHERE id = NEW.id;
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS trg_parcours_bi;
DROP TRIGGER IF EXISTS trg_parcours_bu;
DROP TRIGGER IF EXISTS trg_parcours_ai;
DELIMITER $$
CREATE TRIGGER trg_parcours_bi BEFORE INSERT ON parcours
FOR EACH ROW
BEGIN
  IF NEW.id IS NULL AND NEW.numero_du_parcours IS NOT NULL THEN SET NEW.id = NEW.numero_du_parcours; END IF;
  IF (NEW.commentaire IS NULL OR NEW.commentaire = '') AND NEW.description IS NOT NULL THEN SET NEW.commentaire = NEW.description; END IF;
  IF (NEW.description IS NULL OR NEW.description = '') AND NEW.commentaire IS NOT NULL THEN SET NEW.description = NEW.commentaire; END IF;
END$$
CREATE TRIGGER trg_parcours_bu BEFORE UPDATE ON parcours
FOR EACH ROW
BEGIN
  SET NEW.numero_du_parcours = NEW.id;
  IF NEW.commentaire IS NOT NULL AND NEW.commentaire <> '' THEN SET NEW.description = NEW.commentaire;
  ELSEIF NEW.description IS NOT NULL AND NEW.description <> '' THEN SET NEW.commentaire = NEW.description;
  END IF;
END$$
CREATE TRIGGER trg_parcours_ai AFTER INSERT ON parcours
FOR EACH ROW
BEGIN
  UPDATE parcours
  SET numero_du_parcours = NEW.id,
      commentaire = COALESCE(commentaire, description),
      description = COALESCE(description, commentaire)
  WHERE id = NEW.id;
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS trg_boitier_bi;
DROP TRIGGER IF EXISTS trg_boitier_bu;
DROP TRIGGER IF EXISTS trg_boitier_ai;
DELIMITER $$
CREATE TRIGGER trg_boitier_bi BEFORE INSERT ON boitier
FOR EACH ROW
BEGIN
  IF NEW.id IS NULL AND NEW.numero IS NOT NULL THEN SET NEW.id = NEW.numero; END IF;

  IF (NEW.add_mac IS NULL OR NEW.add_mac = '') AND NEW.mac IS NOT NULL THEN SET NEW.add_mac = NEW.mac; END IF;
  IF (NEW.mac IS NULL OR NEW.mac = '') AND NEW.add_mac IS NOT NULL THEN SET NEW.mac = NEW.add_mac; END IF;

  IF (NEW.add_reseau IS NULL OR NEW.add_reseau = '') AND NEW.reseau IS NOT NULL THEN SET NEW.add_reseau = NEW.reseau; END IF;
  IF (NEW.reseau IS NULL OR NEW.reseau = '') AND NEW.add_reseau IS NOT NULL THEN SET NEW.reseau = NEW.add_reseau; END IF;
END$$
CREATE TRIGGER trg_boitier_bu BEFORE UPDATE ON boitier
FOR EACH ROW
BEGIN
  SET NEW.numero = NEW.id;

  IF NEW.add_mac IS NOT NULL AND NEW.add_mac <> '' THEN SET NEW.mac = NEW.add_mac;
  ELSEIF NEW.mac IS NOT NULL AND NEW.mac <> '' THEN SET NEW.add_mac = NEW.mac;
  END IF;

  IF NEW.add_reseau IS NOT NULL AND NEW.add_reseau <> '' THEN SET NEW.reseau = NEW.add_reseau;
  ELSEIF NEW.reseau IS NOT NULL AND NEW.reseau <> '' THEN SET NEW.add_reseau = NEW.reseau;
  END IF;
END$$
CREATE TRIGGER trg_boitier_ai AFTER INSERT ON boitier
FOR EACH ROW
BEGIN
  UPDATE boitier
  SET numero = NEW.id,
      add_mac = COALESCE(add_mac, mac),
      mac = COALESCE(mac, add_mac),
      add_reseau = COALESCE(add_reseau, reseau),
      reseau = COALESCE(reseau, add_reseau)
  WHERE id = NEW.id;
END$$
DELIMITER ;

DROP TRIGGER IF EXISTS trg_localisation_bi;
DROP TRIGGER IF EXISTS trg_localisation_bu;
DROP TRIGGER IF EXISTS trg_localisation_ai;
DELIMITER $$
CREATE TRIGGER trg_localisation_bi BEFORE INSERT ON localisation
FOR EACH ROW
BEGIN
  IF NEW.id IS NULL AND NEW.id_loc IS NOT NULL THEN SET NEW.id = NEW.id_loc; END IF;
  IF NEW.numero_boitier IS NULL AND NEW.id_boitier IS NOT NULL THEN SET NEW.numero_boitier = NEW.id_boitier; END IF;
  IF NEW.id_boitier IS NULL AND NEW.numero_boitier IS NOT NULL THEN SET NEW.id_boitier = NEW.numero_boitier; END IF;
END$$
CREATE TRIGGER trg_localisation_bu BEFORE UPDATE ON localisation
FOR EACH ROW
BEGIN
  SET NEW.id_loc = NEW.id;
  IF NEW.numero_boitier IS NULL AND NEW.id_boitier IS NOT NULL THEN SET NEW.numero_boitier = NEW.id_boitier; END IF;
  IF NEW.id_boitier IS NULL AND NEW.numero_boitier IS NOT NULL THEN SET NEW.id_boitier = NEW.numero_boitier; END IF;
END$$
CREATE TRIGGER trg_localisation_ai AFTER INSERT ON localisation
FOR EACH ROW
BEGIN
  UPDATE localisation
  SET id_loc = NEW.id,
      numero_boitier = COALESCE(numero_boitier, id_boitier),
      id_boitier = COALESCE(id_boitier, numero_boitier)
  WHERE id = NEW.id;
END$$
DELIMITER ;
