CREATE DATABASE IF NOT EXISTS teamup CHARACTER SET utf8 COLLATE utf8_general_ci;
USE teamup;

CREATE TABLE IF NOT EXISTS utilisateur (
  id_utilisateur INT NOT NULL AUTO_INCREMENT,
  utilisateur_nom VARCHAR(100) NOT NULL,
  utilisateur_login VARCHAR(100) NOT NULL,
  utilisateur_pwd VARCHAR(100) NOT NULL,
  utilisateur_email VARCHAR(100) NOT NULL,
  utilisateur_creation DATETIME NULL,
  PRIMARY KEY (id_utilisateur)
) ENGINE=InnoDB;

INSERT INTO utilisateur VALUES 
(NULL, 'Jean Dupont', 'jdupont', 'password123', 'jean@teamup.com', NOW()),
(NULL, 'Marie Martin', 'mmartin', 'password123', 'marie@teamup.com', NOW());

CREATE TABLE IF NOT EXISTS equipe (
  id_equipe INT NOT NULL AUTO_INCREMENT,
  equipe_nom VARCHAR(100) NOT NULL,
  PRIMARY KEY (id_equipe)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS utilisateur_equipe (
  id_utilisateur INT NOT NULL,
  id_equipe INT NOT NULL,
  PRIMARY KEY (id_utilisateur, id_equipe),
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE,
  FOREIGN KEY (id_equipe) REFERENCES equipe(id_equipe) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS type_demande (
  id_type_demande INT NOT NULL AUTO_INCREMENT,
  type_demande_label VARCHAR(100) NOT NULL,
  PRIMARY KEY (id_type_demande)
) ENGINE=InnoDB;

INSERT INTO type_demande VALUES 
(1, 'Simple demande'),
(2, 'Rendez-vous'),
(3, 'Appel'),
(4, 'Document');

CREATE TABLE IF NOT EXISTS demande (
  id_demande INT NOT NULL AUTO_INCREMENT,
  demande_objet VARCHAR(200) NOT NULL,
  demande_texte TEXT,
  demande_date_creation DATETIME,
  demande_date_echeance DATETIME,
  id_type_demande INT,
  id_utilisateur INT NULL,
  PRIMARY KEY (id_demande),
  FOREIGN KEY (id_type_demande) REFERENCES type_demande(id_type_demande) ON DELETE SET NULL,
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE SET NULL
) ENGINE=InnoDB;
