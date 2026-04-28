<?php
/**
 * Script de test pour vérifier la connexion MongoDB et voir les messages
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/service/messageservice.php';

echo "<h1>🔍 Test MongoDB - Messages du Chat</h1>";

try {
    // Test de connexion
    $client = new \MongoDB\Client("mongodb://localhost:27017");
    echo "<p>✅ Connexion à MongoDB réussie</p>";
    
    // Test de base de données
    $database = $client->teamup;
    echo "<p>✅ Base de données 'teamup' accessible</p>";
    
    // Test de collection
    $collection = $database->chat;
    echo "<p>✅ Collection 'chat' accessible</p>";
    
    // Compter les messages
    $count = $collection->countDocuments();
    echo "<p><strong>📊 Nombre total de messages : $count</strong></p>";
    
    if ($count > 0) {
        // Récupérer les 5 derniers messages
        $messages = $collection->find([], [
            'sort' => ['date' => -1],
            'limit' => 5
        ]);
        
        echo "<h2>📝 5 derniers messages :</h2>";
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f0f0f0;'>";
        echo "<th>Date</th><th>De</th><th>À</th><th>Message</th>";
        echo "</tr>";
        
        foreach ($messages as $message) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($message->date) . "</td>";
            echo "<td>" . htmlspecialchars($message->from) . "</td>";
            echo "<td>" . htmlspecialchars($message->attendee ?? 'Public') . "</td>";
            echo "<td>" . htmlspecialchars($message->message) . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        
        // Lien vers l'interface complète
        echo "<p><a href='view_mongo_chat.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>📱 Voir l'interface complète</a></p>";
        
    } else {
        echo "<p>⚠️ Aucun message trouvé. <a href='messages.php'>Envoyez des messages</a> pour tester.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Vérifiez que :</p>";
    echo "<ul>";
    echo "<li>MongoDB est installé et démarré</li>";
    echo "<li>Le service MongoDB écoute sur le port 27017</li>";
    echo "<li>L'extension MongoDB PHP est activée</li>";
    echo "</ul>";
}
?>
