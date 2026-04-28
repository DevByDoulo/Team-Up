<?php
/**
 * Script de debug pour tester la journalisation MongoDB
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/service/messageservice.php';
require_once __DIR__ . '/models/messageentity.php';

echo "<h1>🔍 Debug Journalisation MongoDB</h1>";

// Démarrer la session pour simuler un utilisateur connecté
session_start();
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'TestUser';
$_SESSION['user_login'] = 'testuser';

try {
    // Test 1: Créer un message de test
    echo "<h2>Test 1: Création d'un message de test</h2>";
    $testMessage = new MessageEntity(
        'TestUser',
        null,
        'Message de test depuis le script de debug - ' . date('H:i:s'),
        date('Y-m-d H:i:s')
    );
    
    echo "<p>✅ MessageEntity créé</p>";
    echo "<p>From: " . htmlspecialchars($testMessage->from) . "</p>";
    echo "<p>Message: " . htmlspecialchars($testMessage->message) . "</p>";
    echo "<p>Date: " . htmlspecialchars($testMessage->date) . "</p>";
    echo "<p>Valid: " . ($testMessage->isValid() ? 'Oui' : 'Non') . "</p>";
    
    // Test 2: Appel direct à MessageService
    echo "<h2>Test 2: Appel direct à MessageService</h2>";
    $result = MessageService::recordMessage($testMessage);
    
    if ($result) {
        echo "<p style='color: green;'>✅ MessageService::recordMessage() a retourné true</p>";
    } else {
        echo "<p style='color: red;'>❌ MessageService::recordMessage() a retourné false</p>";
    }
    
    // Test 3: Vérification directe dans MongoDB
    echo "<h2>Test 3: Vérification directe dans MongoDB</h2>";
    $client = new MongoDB\Client("mongodb://localhost:27017");
    $database = $client->teamup;
    $collection = $database->chat;
    
    $count = $collection->countDocuments();
    echo "<p>Nombre total de documents dans la collection: $count</p>";
    
    // Récupérer les 3 derniers messages
    $messages = $collection->find([], [
        'sort' => ['date' => -1],
        'limit' => 3
    ]);
    
    echo "<h3>3 derniers messages:</h3>";
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f0f0f0;'><th>Date</th><th>From</th><th>Attendee</th><th>Message</th></tr>";
    
    foreach ($messages as $message) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($message->date) . "</td>";
        echo "<td>" . htmlspecialchars($message->from) . "</td>";
        echo "<td>" . htmlspecialchars($message->attendee ?? 'Public') . "</td>";
        echo "<td>" . htmlspecialchars($message->message) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test 4: Simuler l'appel AJAX comme le ferait le JavaScript
    echo "<h2>Test 4: Simulation de l'appel AJAX</h2>";
    
    // Simuler les paramètres GET
    $_GET['action'] = 'recordmessage';
    $_GET['attendee'] = '';
    $_GET['message'] = 'Message test AJAX - ' . date('H:i:s');
    
    // Appeler le contrôleur
    require_once __DIR__ . '/controllers/messagerie/chatController.php';
    $controller = new ChatController();
    $result = $controller->recordmessage();
    
    echo "<p>Résultat du contrôleur: </p>";
    echo "<pre>" . print_r($result, true) . "</pre>";
    
    // Vérifier à nouveau le nombre de messages
    $newCount = $collection->countDocuments();
    echo "<p>Nombre de documents après l'appel AJAX: $newCount</p>";
    
    if ($newCount > $count) {
        echo "<p style='color: green;'>✅ Un nouveau message a été ajouté via le contrôleur</p>";
    } else {
        echo "<p style='color: red;'>❌ Aucun nouveau message ajouté via le contrôleur</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erreur: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Stack trace:</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>
