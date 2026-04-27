<?php
/**
 * Script pour vérifier quelles tables existent dans la base de données
 */

require_once 'config.php';

echo "<h2>Vérification des tables de la base de données</h2>";

try {
    $conn = get_db_connection();
    
    if (!$conn || $conn->connect_error) {
        die("<div style='color: red;'>❌ Erreur de connexion : " . ($conn ? $conn->connect_error : "Connexion échouée") . "</div>");
    }
    
    echo "<div style='color: green;'>✅ Connexion réussie</div>";
    
    // Sélectionner la base de données teamup
    $conn->select_db('teamup') or die("<div style='color: red;'>❌ Impossible de sélectionner la base de données teamup</div>");
    
    // Lister toutes les tables
    $tables = $conn->query("SHOW TABLES");
    
    if ($tables->num_rows == 0) {
        echo "<div style='color: orange;'>⚠️ Aucune table trouvée dans la base de données</div>";
        echo "<p>Vous devez exécuter le script d'initialisation :</p>";
        echo "<a href='init_db.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>Initialiser la base de données</a>";
    } else {
        echo "<h3>Tables existantes (" . $tables->num_rows . ") :</h3>";
        echo "<ul>";
        
        $tables_needed = ['utilisateur', 'equipe', 'utilisateur_equipe', 'type_demande', 'demande', 'evenement', 'participant'];
        $tables_found = [];
        
        while ($table = $tables->fetch_array()) {
            $tableName = $table[0];
            echo "<li style='color: green;'>✅ " . htmlspecialchars($tableName) . "</li>";
            $tables_found[] = $tableName;
        }
        
        echo "</ul>";
        
        // Vérifier les tables manquantes
        $missing = array_diff($tables_needed, $tables_found);
        if (!empty($missing)) {
            echo "<h3 style='color: red;'>Tables manquantes :</h3>";
            echo "<ul>";
            foreach ($missing as $table) {
                echo "<li style='color: red;'>❌ " . htmlspecialchars($table) . "</li>";
            }
            echo "</ul>";
            echo "<p><a href='init_db.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>Exécuter l'initialisation</a></p>";
        } else {
            echo "<div style='color: green;'><h3>✅ Toutes les tables requises existent !</h3></div>";
            
            // Vérifier s'il y a des événements
            $events = $conn->query("SELECT COUNT(*) as count FROM evenement");
            $event_count = $events->fetch_assoc()['count'];
            
            echo "<p>Nombre d'événements dans la base : <strong>" . $event_count . "</strong></p>";
            
            if ($event_count == 0) {
                echo "<p style='color: blue;'>ℹ️ Les tables existent mais sont vides. Vous pouvez ajouter des événements via l'interface.</p>";
                echo "<p><a href='addevenement.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>Ajouter un événement</a></p>";
            }
        }
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "<div style='color: red;'>❌ Exception : " . htmlspecialchars($e->getMessage()) . "</div>";
}

echo "<br><a href='index.php'>Retour à l'accueil</a>";
?>
