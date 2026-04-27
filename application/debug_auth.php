<?php
/**
 * Script de débogage pour l'authentification
 */

require_once 'config.php';
require_once 'service/userservice.php';

echo "<h2>Débogage de l'authentification</h2>";

// Test direct avec la base de données
echo "<h3>Test direct avec la base de données :</h3>";

$conn = get_db_connection();

if ($conn->connect_error) {
    echo "<div style='color: red;'>❌ Erreur de connexion: " . $conn->connect_error . "</div>";
} else {
    echo "<div style='color: green;'>✅ Connexion réussie</div>";
    
    // Afficher tous les utilisateurs avec leurs mots de passe
    $result = $conn->query("SELECT id_utilisateur, utilisateur_nom, utilisateur_login, utilisateur_pwd, utilisateur_email FROM utilisateur");
    
    if ($result && $result->num_rows > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 20px 0;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>ID</th><th>Nom</th><th>Login</th><th>Mot de passe</th><th>Email</th>";
        echo "</tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id_utilisateur'] . "</td>";
            echo "<td>" . htmlspecialchars($row['utilisateur_nom']) . "</td>";
            echo "<td>" . htmlspecialchars($row['utilisateur_login']) . "</td>";
            echo "<td style='font-family: monospace; font-size: 12px;'>" . htmlspecialchars($row['utilisateur_pwd']) . "</td>";
            echo "<td>" . htmlspecialchars($row['utilisateur_email']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Tester l'authentification avec chaque utilisateur
        echo "<h3>Test d'authentification :</h3>";
        $result = $conn->query("SELECT utilisateur_login, utilisateur_pwd FROM utilisateur");
        
        while ($row = $result->fetch_assoc()) {
            $login = $row['utilisateur_login'];
            $storedPassword = $row['utilisateur_pwd'];
            
            echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ddd;'>";
            echo "<strong>Login:</strong> " . htmlspecialchars($login) . "<br>";
            echo "<strong>Mot de passe stocké:</strong> " . htmlspecialchars($storedPassword) . "<br>";
            
            // Vérifier si le mot de passe est haché
            if (password_verify('password123', $storedPassword)) {
                echo "<span style='color: green;'>✅ password_verify('password123') = TRUE</span><br>";
            } else {
                echo "<span style='color: red;'>❌ password_verify('password123') = FALSE</span><br>";
                
                // Vérifier si c'est un mot de passe en clair
                if ($storedPassword === 'password123') {
                    echo "<span style='color: orange;'>⚠️ Le mot de passe est en clair !</span><br>";
                }
            }
            
            // Tester avec UserService
            $userService = new UserService();
            $user = $userService->authenticateUser($login, 'password123');
            
            if ($user) {
                echo "<span style='color: green;'>✅ UserService::authenticateUser() = SUCCESS</span>";
            } else {
                echo "<span style='color: red;'>❌ UserService::authenticateUser() = FAILED</span>";
            }
            
            echo "</div>";
        }
        
    } else {
        echo "<div style='color: red;'>❌ Aucun utilisateur trouvé dans la base</div>";
    }
    
    $conn->close();
}

echo "<br><div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
echo "<h4>🔍 Diagnostic</h4>";
echo "<p><strong>Solution probable :</strong> Les mots de passe ne sont pas hachés dans la base de données.</p>";
echo "<p><strong>Action requise :</strong> Exécutez le script de migration des mots de passe :</p>";
echo "<a href='migrate_passwords.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>🔐 Migrer les mots de passe</a>";
echo "</div>";

echo "<br><a href='login.php'>Retour à la connexion</a>";
?>
