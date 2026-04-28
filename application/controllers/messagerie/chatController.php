<?php
/**
 * Contrôleur pour la messagerie chat
 * Gère les actions liées au chat en temps réel et à la journalisation
 */

require_once dirname(dirname(__DIR__)) . '/config.php';
require_once dirname(dirname(__DIR__)) . '/models/messageentity.php';
require_once dirname(dirname(__DIR__)) . '/service/messageservice.php';

class ChatController {
    
    /**
     * Action pour enregistrer un message dans MongoDB
     * @param mixed $model Modèle de données (non utilisé dans ce cas)
     * @return array Résultat de l'opération
     */
    public function recordmessage($model = null) {
        try {
            // Récupérer l'utilisateur connecté via session
            session_start();
            if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
                error_log("ChatController: Utilisateur non connecté");
                return ['success' => false, 'error' => 'Utilisateur non connecté'];
            }
            
            $from = $_SESSION['user_login'] ?? $_SESSION['user_name'] ?? 'unknown';
            
            // Récupérer les paramètres GET
            $attendee = $_GET['attendee'] ?? null;
            $message = $_GET['message'] ?? '';
            
            // Décoder le message (URL encoded)
            $message = urldecode($message);
            
            // Nettoyer les paramètres
            $attendee = trim($attendee);
            $message = trim($message);
            
            // Vérifier que le message n'est pas vide
            if (empty($message)) {
                error_log("ChatController: Message vide non enregistré");
                return ['success' => false, 'error' => 'Message vide'];
            }
            
            // Créer l'instance de MessageEntity
            $messageEntity = new MessageEntity(
                $from,
                empty($attendee) ? null : $attendee,
                $message,
                date('Y-m-d H:i:s')
            );
            
            // Enregistrer le message via MessageService
            $success = MessageService::recordMessage($messageEntity);
            
            if ($success) {
                error_log("ChatController: Message enregistré avec succès - from: $from, attendee: " . ($attendee ?: 'public'));
                return ['success' => true];
            } else {
                error_log("ChatController: Échec de l'enregistrement du message");
                return ['success' => false, 'error' => 'Échec de l\'enregistrement'];
            }
            
        } catch (Exception $e) {
            error_log("ChatController: Exception - " . $e->getMessage());
            return ['success' => false, 'error' => 'Exception: ' . $e->getMessage()];
        }
    }
    
    /**
     * Action pour récupérer l'historique des messages
     * @param mixed $model Modèle de données
     * @return array Liste des messages au format JSON
     */
    public function gethistory($model = null) {
        try {
            // Récupérer l'utilisateur connecté
            session_start();
            if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
                return [];
            }
            
            $username = $_SESSION['user_login'] ?? $_SESSION['user_name'] ?? 'unknown';
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 50;
            
            // Récupérer l'historique via MessageService
            $messages = MessageService::getMessageHistory($username, $limit);
            
            return ['success' => true, 'messages' => $messages];
            
        } catch (Exception $e) {
            error_log("ChatController: Exception gethistory - " . $e->getMessage());
            return ['success' => false, 'error' => 'Exception: ' . $e->getMessage()];
        }
    }

    /**
     * Action pour récupérer les messages récents (publics et privés)
     * @param mixed $model Modèle de données
     * @return array Liste des messages récents
     */
    public function getrecent($model = null) {
        try {
            session_start();
            if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
                return [];
            }
            
            $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
            
            // Récupérer les messages récents via MessageService
            $messages = MessageService::getRecentMessages($limit);
            
            return ['success' => true, 'messages' => $messages];
            
        } catch (Exception $e) {
            error_log("ChatController: Exception getrecent - " . $e->getMessage());
            return ['success' => false, 'error' => 'Exception: ' . $e->getMessage()];
        }
    }

    /**
     * Action pour vider tous les messages de la collection
     * @param mixed $model Modèle de données
     * @return array Résultat de l'opération
     */
    public function clearmessages($model = null) {
        try {
            session_start();
            if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
                return ['success' => false, 'error' => 'Utilisateur non connecté'];
            }
            
            // Connexion à MongoDB
            $client = new \MongoDB\Client("mongodb://localhost:27017");
            $database = $client->teamup;
            $collection = $database->chat;
            
            // Supprimer tous les documents
            $result = $collection->deleteMany([]);
            
            if ($result->getDeletedCount() > 0) {
                error_log("ChatController: " . $result->getDeletedCount() . " messages supprimés");
                return ['success' => true, 'deleted' => $result->getDeletedCount()];
            } else {
                return ['success' => true, 'deleted' => 0];
            }
            
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            error_log("ChatController: Erreur MongoDB clearmessages - " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("ChatController: Exception clearmessages - " . $e->getMessage());
            return false;
        }
    }
}
?>
