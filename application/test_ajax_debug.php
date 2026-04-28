<?php
/**
 * Script pour tester l'appel AJAX et voir l'erreur exacte
 */

echo "<h1>🔍 Test AJAX Debug</h1>";

// Démarrer la session
session_start();
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'TestUser';
$_SESSION['user_login'] = 'testuser';

// URL à tester
$url = 'controllers/messagerie/index.php?action=recordmessage&attendee=&message=test%20message';

echo "<h2>Test 1: Appel direct au contrôleur</h2>";
echo "<p>URL: " . htmlspecialchars($url) . "</p>";

// Test avec file_get_contents
echo "<h3>Test avec file_get_contents:</h3>";
$context = stream_context_create([
    'http' => [
        'method' => 'GET',
        'header' => 'Content-Type: application/json'
    ]
]);

$response = @file_get_contents($url, false, $context);

if ($response === false) {
    echo "<p style='color: red;'>❌ Erreur avec file_get_contents</p>";
    $error = error_get_last();
    if ($error) {
        echo "<p>Erreur: " . htmlspecialchars($error['message']) . "</p>";
    }
} else {
    echo "<p style='color: green;'>✅ Réponse reçue:</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
}

// Test avec cURL
echo "<h3>Test avec cURL:</h3>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

if ($response === false || !empty($error)) {
    echo "<p style='color: red;'>❌ Erreur cURL: " . htmlspecialchars($error) . "</p>";
} else {
    echo "<p>HTTP Code: $http_code</p>";
    if ($http_code === 200) {
        echo "<p style='color: green;'>✅ Réponse cURL:</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    } else {
        echo "<p style='color: red;'>❌ Erreur HTTP $http_code</p>";
        echo "<pre>" . htmlspecialchars($response) . "</pre>";
    }
}

// Test direct du contrôleur
echo "<h2>Test 2: Appel direct du contrôleur</h2>";

try {
    require_once 'controllers/messagerie/chatController.php';
    $controller = new ChatController();
    
    // Simuler les paramètres GET
    $_GET['action'] = 'recordmessage';
    $_GET['attendee'] = '';
    $_GET['message'] = 'test message';
    
    $result = $controller->recordmessage();
    
    echo "<p style='color: green;'>✅ Contrôleur appelé directement</p>";
    echo "<pre>" . print_r($result, true) . "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception dans le contrôleur:</p>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}

// Test des erreurs PHP
echo "<h2>Test 3: Vérification des erreurs PHP</h2>";
echo "<p>Display errors: " . (ini_get('display_errors') ? 'On' : 'Off') . "</p>";
echo "<p>Error reporting: " . error_reporting() . "</p>";

// Activer temporairement l'affichage des erreurs pour voir le problème
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h3>Tentative d'inclusion des fichiers requis:</h3>";

try {
    require_once 'config.php';
    echo "<p>✅ config.php chargé</p>";
} catch (Exception $e) {
    echo "<p>❌ config.php: " . htmlspecialchars($e->getMessage()) . "</p>";
}

try {
    require_once 'models/messageentity.php';
    echo "<p>✅ messageentity.php chargé</p>";
} catch (Exception $e) {
    echo "<p>❌ messageentity.php: " . htmlspecialchars($e->getMessage()) . "</p>";
}

try {
    require_once 'service/messageservice.php';
    echo "<p>✅ messageservice.php chargé</p>";
} catch (Exception $e) {
    echo "<p>❌ messageservice.php: " . htmlspecialchars($e->getMessage()) . "</p>";
}

try {
    require_once 'controllers/messagerie/chatController.php';
    echo "<p>✅ chatController.php chargé</p>";
} catch (Exception $e) {
    echo "<p>❌ chatController.php: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
