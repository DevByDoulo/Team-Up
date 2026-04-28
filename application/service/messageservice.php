<?php
/**
 * Classe MessageService - Couche service pour la gestion des messages de chat
 * Gère la journalisation des messages dans MongoDB
 */

require_once dirname(__DIR__) . '/models/messageentity.php';

class MessageService {

    /**
     * Enregistre un message dans la collection MongoDB chat
     * @param MessageEntity $msg Objet message à enregistrer
     * @return bool True si succès, False en cas d'erreur
     */
    public static function recordMessage(MessageEntity $msg) {
        try {
            // Vérifier la validité du message
            if (!$msg->isValid()) {
                error_log("MessageService: Message invalide - from ou message vide");
                return false;
            }

            // Connexion à MongoDB
            $client = new \MongoDB\Client("mongodb://localhost:27017");
            
            // Sélectionner la base de données et la collection
            $database = $client->teamup;
            $collection = $database->chat;
            
            // Insérer le document
            $result = $collection->insertOne($msg->toArray());
            
            // Vérifier si l'insertion a réussi
            if ($result->getInsertedCount() === 1) {
                error_log("MessageService: Message enregistré avec succès - ID: " . $result->getInsertedId());
                return true;
            } else {
                error_log("MessageService: Échec de l'insertion du message");
                return false;
            }
            
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            error_log("MessageService: Erreur MongoDB - " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("MessageService: Erreur générale - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère l'historique des messages pour un utilisateur
     * @param string $username Nom d'utilisateur
     * @param int $limit Nombre maximum de messages à récupérer
     * @return array Liste des messages
     */
    public static function getMessageHistory($username, $limit = 50) {
        try {
            $client = new \MongoDB\Client("mongodb://localhost:27017");
            $database = $client->teamup;
            $collection = $database->chat;
            
            // Récupérer les messages où l'utilisateur est expéditeur ou destinataire
            $filter = [
                '$or' => [
                    ['from' => $username],
                    ['attendee' => $username],
                    ['attendee' => null, '$or' => [['from' => $username]]]
                ]
            ];
            
            $options = [
                'sort' => ['date' => -1],
                'limit' => $limit
            ];
            
            $cursor = $collection->find($filter, $options);
            $messages = [];
            
            foreach ($cursor as $document) {
                $messages[] = $document;
            }
            
            return array_reverse($messages); // Ordre chronologique
            
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            error_log("MessageService: Erreur récupération messages - " . $e->getMessage());
            return [];
        }
    }

    /**
     * Récupère tous les messages publics et privés récents
     * @param int $limit Nombre maximum de messages
     * @return array Liste des messages
     */
    public static function getRecentMessages($limit = 100) {
        try {
            $client = new \MongoDB\Client("mongodb://localhost:27017");
            $database = $client->teamup;
            $collection = $database->chat;
            
            $options = [
                'sort' => ['date' => -1],
                'limit' => $limit
            ];
            
            $cursor = $collection->find([], $options);
            $messages = [];
            
            foreach ($cursor as $document) {
                $messages[] = $document;
            }
            
            return array_reverse($messages); // Ordre chronologique
            
        } catch (\MongoDB\Driver\Exception\Exception $e) {
            error_log("MessageService: Erreur récupération messages récents - " . $e->getMessage());
            return [];
        }
    }
}
?>
