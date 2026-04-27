<?php
/**
 * Script de test de connexion à la base de données
 */

require_once 'config.php';

echo "<h2>Test de connexion à la base de données</h2>";

try {
    $conn = get_db_connection();
    
    if ($conn->connect_error) {
        echo "<div style='color: red;'>❌ Erreur de connexion : " . $conn->connect_error . "</div>";
    } else {
        echo "<div style='color: green;'>✅ Connexion réussie à MySQL</div>";
        
        // Vérifier si la base de données teamup existe
        $result = $conn->query("SHOW DATABASES LIKE 'teamup'");
        if ($result->num_rows > 0) {
            echo "<div style='color: green;'>✅ Base de données 'teamup' trouvée</div>";
            
            // Vérifier les tables
            $conn->select_db('teamup');
            $tables = $conn->query("SHOW TABLES");
            echo "<div style='color: blue;'>📋 Tables trouvées : " . $tables->num_rows . "</div>";
            
            while ($table = $tables->fetch_array()) {
                echo "<div style='margin-left: 20px;'>- " . $table[0] . "</div>";
            }
        } else {
            echo "<div style='color: orange;'>⚠️ Base de données 'teamup' non trouvée</div>";
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Exception : " . $e->getMessage() . "</div>";
}

echo "<br><a href='index.php'>Retour à l'accueil</a>";
?>
