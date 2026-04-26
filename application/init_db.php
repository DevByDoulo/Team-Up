<?php
// Script d'initialisation de la base de données
require_once 'config.php';

$conn = get_db_connection();

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

echo "Connexion MySQL OK<br><br>";

// 1. Créer la table type_demande
$sql1 = "CREATE TABLE IF NOT EXISTS type_demande (
  id_type_demande INT NOT NULL AUTO_INCREMENT,
  type_demande_label VARCHAR(100) NOT NULL,
  PRIMARY KEY (id_type_demande)
) ENGINE=InnoDB";

if ($conn->query($sql1) === TRUE) {
    echo "✅ Table 'type_demande' créée ou déjà existante<br>";
} else {
    echo "❌ Erreur création type_demande : " . $conn->error . "<br>";
}

// 2. Insérer les types de demande
$sql2 = "SELECT COUNT(*) as total FROM type_demande";
$result = $conn->query($sql2);
$row = $result->fetch_assoc();

if ($row['total'] == 0) {
    $sql3 = "INSERT INTO type_demande VALUES
        (1, 'Simple demande'),
        (2, 'Rendez-vous'),
        (3, 'Appel'),
        (4, 'Document')";

    if ($conn->query($sql3) === TRUE) {
        echo "✅ 4 types de demande insérés<br>";
    } else {
        echo "❌ Erreur insertion types : " . $conn->error . "<br>";
    }
} else {
    echo "ℹ️ Types de demande déjà présents ({$row['total']})<br>";
}

// 3. Créer la table demande
$sql4 = "CREATE TABLE IF NOT EXISTS demande (
  id_demande INT NOT NULL AUTO_INCREMENT,
  demande_objet VARCHAR(200) NOT NULL,
  demande_texte TEXT,
  demande_date_creation DATETIME,
  demande_date_echeance DATETIME,
  id_type_demande INT,
  id_utilisateur INT NULL,
  PRIMARY KEY (id_demande)
) ENGINE=InnoDB";

if ($conn->query($sql4) === TRUE) {
    echo "✅ Table 'demande' créée ou déjà existante<br>";
} else {
    echo "❌ Erreur création demande : " . $conn->error . "<br>";
}

// 4. Ajouter les clés étrangères à la table demande (si elles n'existent pas)
// Vérifie si la contrainte existe déjà
$fkCheck = $conn->query("SHOW CREATE TABLE demande");
$createTable = $fkCheck->fetch_row()[1];

if (strpos($createTable, 'fk_demande_utilisateur') === false) {
    $sqlfk1 = "ALTER TABLE demande
    ADD CONSTRAINT fk_demande_utilisateur
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE SET NULL";

    if ($conn->query($sqlfk1) === TRUE) {
        echo "✅ Clé étrangère 'fk_demande_utilisateur' ajoutée<br>";
    } else {
        echo "❌ Erreur ajout FK utilisateur : " . $conn->error . "<br>";
    }
} else {
    echo "ℹ️ Clé étrangère 'fk_demande_utilisateur' déjà présente<br>";
}

if (strpos($createTable, 'fk_demande_type') === false) {
    $sqlfk2 = "ALTER TABLE demande
    ADD CONSTRAINT fk_demande_type
    FOREIGN KEY (id_type_demande) REFERENCES type_demande(id_type_demande) ON DELETE SET NULL";

    if ($conn->query($sqlfk2) === TRUE) {
        echo "✅ Clé étrangère 'fk_demande_type' ajoutée<br>";
    } else {
        echo "❌ Erreur ajout FK type_demande : " . $conn->error . "<br>";
    }
} else {
    echo "ℹ️ Clé étrangère 'fk_demande_type' déjà présente<br>";
}

// 5. Vérifier que la table utilisateur_equipe existe
$checkTeamTable = $conn->query("SHOW TABLES LIKE 'utilisateur_equipe'");
if ($checkTeamTable->num_rows == 0) {
    $sqlTeam = "CREATE TABLE IF NOT EXISTS utilisateur_equipe (
      id_utilisateur INT NOT NULL,
      id_equipe INT NOT NULL,
      PRIMARY KEY (id_utilisateur, id_equipe),
      FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE,
      FOREIGN KEY (id_equipe) REFERENCES equipe(id_equipe) ON DELETE CASCADE
    ) ENGINE=InnoDB";

    if ($conn->query($sqlTeam) === TRUE) {
        echo "✅ Table 'utilisateur_equipe' créée<br>";
    } else {
        echo "❌ Erreur création utilisateur_equipe : " . $conn->error . "<br>";
    }
} else {
    echo "ℹ️ Table 'utilisateur_equipe' déjà présente<br>";
}

echo "<br><b>Initialisation terminée !</b><br>";

$conn->close();
?>