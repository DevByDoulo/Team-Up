<?php
require_once 'vendor/autoload.php';

try {
    $client = new \MongoDB\Client("mongodb://localhost:27017");
    $databases = $client->listDatabases();
    
    echo "<h2>✅ Connexion MongoDB réussie !</h2>";
    echo "<h3>📁 Bases de données disponibles :</h3>";
    echo "<ul>";
    foreach ($databases as $db) {
        echo "<li>" . $db['name'] . "</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2>❌ Erreur :</h2>";
    echo $e->getMessage();
}
?>