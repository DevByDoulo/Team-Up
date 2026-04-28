<?php
/**
 * Script d'initialisation pour MongoDB Chat
 * Crée la base de données teamup et la collection chat si nécessaire
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/service/messageservice.php';
require_once __DIR__ . '/models/messageentity.php';

echo "<h1>🚀 Initialisation MongoDB Chat</h1>";

try {
    // Connexion à MongoDB
    $client = new MongoDB\Client("mongodb://localhost:27017");
    echo "<p>✅ Connexion à MongoDB réussie</p>";
    
    // Créer/Sélectionner la base de données teamup
    $database = $client->teamup;
    echo "<p>✅ Base de données 'teamup' sélectionnée</p>";
    
    // Créer/Sélectionner la collection chat
    $collection = $database->chat;
    echo "<p>✅ Collection 'chat' prête</p>";
    
    // Insérer un message de test
    $testMessage = new MessageEntity(
        'System',
        null,
        'Message de test - Initialisation du chat MongoDB',
        date('Y-m-d H:i:s')
    );
    
    $result = MessageService::recordMessage($testMessage);
    
    if ($result) {
        echo "<p>✅ Message de test inséré avec succès</p>";
        
        // Compter les messages
        $count = $collection->countDocuments();
        echo "<p>📊 Nombre total de messages : $count</p>";
        
        // Afficher les 3 derniers messages
        $messages = $collection->find([], [
            'sort' => ['date' => -1],
            'limit' => 3
        ]);
        
        echo "<h2>📝 Messages récents :</h2>";
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
        
        echo "<div style='margin-top: 20px;'>";
        echo "<a href='view_mongo_chat.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-right: 10px;'>📱 Voir l'interface complète</a>";
        echo "<a href='messages.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>💬 Aller au chat</a>";
        echo "</div>";
        
    } else {
        echo "<p style='color: red;'>❌ Erreur lors de l'insertion du message de test</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
