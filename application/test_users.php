<?php
/**
 * Script de test pour vérifier le chargement des utilisateurs
 */

require_once 'service/userservice.php';

echo "<h2>Test de chargement des utilisateurs</h2>";

try {
    $userService = new UserService();
    $utilisateurs = $userService->getuserlist();
    
    echo "<h3>Résultat du UserService :</h3>";
    
    if (empty($utilisateurs)) {
        echo "<div style='color: red;'>❌ Aucun utilisateur trouvé par UserService</div>";
    } else {
        echo "<div style='color: green;'>✅ " . count($utilisateurs) . " utilisateur(s) trouvé(s)</div>";
        echo "<ul>";
        foreach ($utilisateurs as $utilisateur) {
            echo "<li>ID: " . $utilisateur->id_utilisateur . 
                 " - Nom: " . htmlspecialchars($utilisateur->utilisateur_nom) . 
                 " - Login: " . htmlspecialchars($utilisateur->utilisateur_login) . 
                 " - Email: " . htmlspecialchars($utilisateur->utilisateur_email) . "</li>";
        }
        echo "</ul>";
    }
    
    // Test direct avec UserDAO
    echo "<h3>Test direct avec UserDAO :</h3>";
    require_once 'dal/userdao.php';
    $userDAO = new UserDAO();
    $utilisateurs_direct = $userDAO->getuserlist();
    
    if (empty($utilisateurs_direct)) {
        echo "<div style='color: red;'>❌ Aucun utilisateur trouvé par UserDAO</div>";
    } else {
        echo "<div style='color: green;'>✅ " . count($utilisateurs_direct) . " utilisateur(s) trouvé(s) directement</div>";
    }
    
    // Test de connexion directe à la base
    echo "<h3>Test de connexion directe à la base :</h3>";
    require_once 'config.php';
    $conn = get_db_connection();
    
    if ($conn->connect_error) {
        echo "<div style='color: red;'>❌ Erreur de connexion: " . $conn->connect_error . "</div>";
    } else {
        echo "<div style='color: green;'>✅ Connexion réussie</div>";
        
        $result = $conn->query("SELECT * FROM utilisateur ORDER BY utilisateur_nom");
        if ($result && $result->num_rows > 0) {
            echo "<div style='color: green;'>✅ " . $result->num_rows . " enregistrement(s) dans la table utilisateur</div>";
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                echo "<li>ID: " . $row['id_utilisateur'] . 
                     " - Nom: " . htmlspecialchars($row['utilisateur_nom']) . 
                     " - Login: " . htmlspecialchars($row['utilisateur_login']) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<div style='color: red;'>❌ Aucun enregistrement dans la table utilisateur</div>";
        }
        $conn->close();
    }
    
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Exception: " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<br><a href='messages.php'>Tester la page de messagerie</a>";
echo "<br><a href='index.php'>Retour à l'accueil</a>";
?>
