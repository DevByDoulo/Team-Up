<?php
/**
 * Script d'initialisation complète pour les tables manquantes
 * Complète le script init_db.php pour créer toutes les tables nécessaires
 */

require_once 'config.php';

$conn = get_db_connection();

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

echo "Initialisation complète des tables...<br><br>";

// Créer la table evenement si elle n'existe pas
$sql_evenement = "CREATE TABLE IF NOT EXISTS evenement (
  id_evenement INT NOT NULL AUTO_INCREMENT,
  evenement_subject VARCHAR(200) NOT NULL,
  evenement_description TEXT,
  evenement_location VARCHAR(200),
  evenement_dtstart DATETIME NOT NULL,
  evenement_dtend DATETIME NOT NULL,
  evenement_tstamp DATETIME,
  evenement_uid VARCHAR(200),
  id_utilisateur INT NOT NULL,
  PRIMARY KEY (id_evenement)
) ENGINE=InnoDB";

if ($conn->query($sql_evenement) === TRUE) {
    echo "✅ Table 'evenement' créée ou déjà existante<br>";
} else {
    echo "❌ Erreur création evenement : " . $conn->error . "<br>";
}

// Créer la table participant si elle n'existe pas
$sql_participant = "CREATE TABLE IF NOT EXISTS participant (
  id_evenement INT NOT NULL,
  id_utilisateur INT NOT NULL,
  PRIMARY KEY (id_evenement, id_utilisateur),
  FOREIGN KEY (id_evenement) REFERENCES evenement(id_evenement) ON DELETE CASCADE,
  FOREIGN KEY (id_utilisateur) REFERENCES utilisateur(id_utilisateur) ON DELETE CASCADE
) ENGINE=InnoDB";

if ($conn->query($sql_participant) === TRUE) {
    echo "✅ Table 'participant' créée ou déjà existante<br>";
} else {
    echo "❌ Erreur création participant : " . $conn->error . "<br>";
}

// Vérifier si la table utilisateur existe, sinon la créer
$sql_utilisateur = "CREATE TABLE IF NOT EXISTS utilisateur (
  id_utilisateur INT NOT NULL AUTO_INCREMENT,
  utilisateur_nom VARCHAR(100) NOT NULL,
  utilisateur_login VARCHAR(100) NOT NULL,
  utilisateur_pwd VARCHAR(100) NOT NULL,
  utilisateur_email VARCHAR(100) NOT NULL,
  utilisateur_creation DATETIME NULL,
  PRIMARY KEY (id_utilisateur)
) ENGINE=InnoDB";

if ($conn->query($sql_utilisateur) === TRUE) {
    echo "✅ Table 'utilisateur' créée ou déjà existante<br>";
} else {
    echo "❌ Erreur création utilisateur : " . $conn->error . "<br>";
}

// Vérifier si la table equipe existe, sinon la créer
$sql_equipe = "CREATE TABLE IF NOT EXISTS equipe (
  id_equipe INT NOT NULL AUTO_INCREMENT,
  equipe_nom VARCHAR(100) NOT NULL,
  PRIMARY KEY (id_equipe)
) ENGINE=InnoDB";

if ($conn->query($sql_equipe) === TRUE) {
    echo "✅ Table 'equipe' créée ou déjà existante<br>";
} else {
    echo "❌ Erreur création equipe : " . $conn->error . "<br>";
}

// Insérer des utilisateurs de test si la table est vide
$result = $conn->query("SELECT COUNT(*) as total FROM utilisateur");
$row = $result->fetch_assoc();

if ($row['total'] == 0) {
    $sql_insert_users = "INSERT INTO utilisateur VALUES 
    (NULL, 'Jean Dupont', 'jdupont', 'password123', 'jean@teamup.com', NOW()),
    (NULL, 'Marie Martin', 'mmartin', 'password123', 'marie@teamup.com', NOW())";

    if ($conn->query($sql_insert_users) === TRUE) {
        echo "✅ 2 utilisateurs de test insérés<br>";
    } else {
        echo "❌ Erreur insertion utilisateurs : " . $conn->error . "<br>";
    }
} else {
    echo "ℹ️ Utilisateurs déjà présents (" . $row['total'] . ")<br>";
}

// Insérer une équipe de test si la table est vide
$result = $conn->query("SELECT COUNT(*) as total FROM equipe");
$row = $result->fetch_assoc();

if ($row['total'] == 0) {
    $sql_insert_team = "INSERT INTO equipe VALUES (NULL, 'Équipe de développement')";

    if ($conn->query($sql_insert_team) === TRUE) {
        echo "✅ Équipe de test insérée<br>";
    } else {
        echo "❌ Erreur insertion équipe : " . $conn->error . "<br>";
    }
} else {
    echo "ℹ️ Équipes déjà présentes (" . $row['total'] . ")<br>";
}

// Insérer un événement de test si la table est vide
$result = $conn->query("SELECT COUNT(*) as total FROM evenement");
$row = $result->fetch_assoc();

if ($row['total'] == 0) {
    $sql_insert_event = "INSERT INTO evenement VALUES 
    (NULL, 'Réunion d''équipe', 'Réunion hebdomadaire pour discuter des projets en cours', 'Salle de réunion A', NOW(), DATE_ADD(NOW(), INTERVAL 2 HOUR), NOW(), 'EVT_' . UNIX_TIMESTAMP(), 1)";

    if ($conn->query($sql_insert_event) === TRUE) {
        echo "✅ Événement de test inséré<br>";
    } else {
        echo "❌ Erreur insertion événement : " . $conn->error . "<br>";
    }
} else {
    echo "ℹ️ Événements déjà présents (" . $row['total'] . ")<br>";
}

echo "<br><b>Initialisation complète terminée !</b><br>";

// Vérification finale
echo "<br><h3>État final des tables :</h3>";
$tables = $conn->query("SHOW TABLES");
while ($table = $tables->fetch_array()) {
    $count = $conn->query("SELECT COUNT(*) as cnt FROM `" . $table[0] . "`");
    $row_count = $count->fetch_assoc();
    echo "- " . htmlspecialchars($table[0]) . " : " . $row_count['cnt'] . " enregistrements<br>";
}

$conn->close();

echo "<br><div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
echo "<h4>🎉 Base de données prête !</h4>";
echo "<p>Vous pouvez maintenant :</p>";
echo "<ul>";
echo "<li><a href='agenda.php' style='color: #155724;'>📅 Voir l'agenda</a></li>";
echo "<li><a href='addevenement.php' style='color: #155724;'>➕ Ajouter un événement</a></li>";
echo "<li><a href='index.php' style='color: #155724;'>🏠 Retour à l'accueil</a></li>";
echo "</ul>";
echo "</div>";
?>
