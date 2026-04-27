<?php
/**
 * Script de migration pour hasher les mots de passe existants
 * À exécuter une seule fois pour sécuriser les mots de passe en clair
 */

require_once 'config.php';

$conn = get_db_connection();

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

echo "Début de la migration des mots de passe...<br><br>";

// Récupérer tous les utilisateurs avec des mots de passe non hachés
$stmt = $conn->prepare("SELECT id_utilisateur, utilisateur_login, utilisateur_pwd FROM utilisateur");
$stmt->execute();
$result = $stmt->get_result();

$migrated = 0;
$errors = 0;

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userId = $row['id_utilisateur'];
        $login = $row['utilisateur_login'];
        $password = $row['utilisateur_pwd'];
        
        // Vérifier si le mot de passe est déjà haché
        if (password_get_info($password)['algo'] === 0) {
            // Le mot de passe n'est pas haché, on le hache
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Mettre à jour la base de données
            $updateStmt = $conn->prepare("UPDATE utilisateur SET utilisateur_pwd = ? WHERE id_utilisateur = ?");
            $updateStmt->bind_param("si", $hashedPassword, $userId);
            
            if ($updateStmt->execute()) {
                echo "✅ Utilisateur {$login} (ID: {$userId}) - Mot de passe haché<br>";
                $migrated++;
            } else {
                echo "❌ Utilisateur {$login} (ID: {$userId}) - Erreur: " . $updateStmt->error . "<br>";
                $errors++;
            }
            
            $updateStmt->close();
        } else {
            echo "ℹ️ Utilisateur {$login} (ID: {$userId}) - Mot de passe déjà haché<br>";
        }
    }
} else {
    echo "Aucun utilisateur trouvé dans la base de données.<br>";
}

$stmt->close();
$conn->close();

echo "<br><b>Migration terminée !</b><br>";
echo "✅ {$migrated} mots de passe hachés avec succès<br>";
echo "❌ {$errors} erreurs rencontrées<br>";

if ($migrated > 0) {
    echo "<br><div class='alert alert-warning'>";
    echo "<b>IMPORTANT :</b> Supprimez ce fichier après exécution pour des raisons de sécurité.";
    echo "</div>";
}
?>
