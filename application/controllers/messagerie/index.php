<?php
/**
 * Point d'entrée pour le contrôleur de messagerie
 * Route les actions vers les méthodes appropriées du ChatController
 */

// Désactiver l'affichage des erreurs HTML
error_reporting(0);
ini_set('display_errors', 0);

// Démarrer la session pour l'authentification
session_start();

// Désactiver la mise en buffer de sortie
if (ob_get_length()) ob_clean();

// Forcer le content-type JSON
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, must-revalidate');

require_once dirname(dirname(__DIR__)) . '/vendor/autoload.php';
require_once dirname(dirname(__DIR__)) . '/config.php';
require_once dirname(dirname(__DIR__)) . '/models/messageentity.php';
require_once dirname(dirname(__DIR__)) . '/service/messageservice.php';
require_once 'chatController.php';

// Créer une instance du contrôleur
$controller = new ChatController();

// Récupérer l'action demandée
$action = $_GET['action'] ?? '';

// Router l'action
switch ($action) {
    case 'recordmessage':
        $result = $controller->recordmessage();
        echo json_encode($result);
        break;
        
    case 'gethistory':
        $result = $controller->gethistory();
        echo json_encode($result);
        break;
        
    case 'getrecent':
        $result = $controller->getrecent();
        echo json_encode($result);
        break;
        
    case 'clearmessages':
        $result = $controller->clearmessages();
        echo json_encode($result);
        break;
        
    default:
        echo json_encode(['success' => false, 'error' => 'Action non trouvée']);
        break;
}

// Terminer le script proprement
exit();
?>
