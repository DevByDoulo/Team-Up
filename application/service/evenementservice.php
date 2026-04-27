<?php
/**
 * Classe EvenementService - Couche service pour la gestion des événements
 * Fait le pont entre le contrôleur et le DAO
 */

require_once dirname(__DIR__) . '/dal/evenementdao.php';
require_once dirname(__DIR__) . '/models/evenemententity.php';

class EvenementService {
    private $evenementDAO;

    /**
     * Constructeur de la classe EvenementService
     * Instancie le DAO pour les opérations sur les événements
     */
    public function __construct() {
        $this->evenementDAO = new EvenementDAO();
    }

    /**
     * Ajoute un nouvel événement via le DAO
     * @param EvenementEntity $ev Objet événement à ajouter
     * @param array $participants Tableau des IDs des participants
     * @return int ID de l'événement inséré
     */
    public function evenement_add($ev, $participants) {
        return $this->evenementDAO->evenement_add($ev, $participants);
    }

    /**
     * Modifie un événement existant via le DAO
     * @param EvenementEntity $ev Objet événement modifié
     * @param array $participants Tableau des IDs des participants
     * @return bool True si la modification réussit, False sinon
     */
    public function evenement_edit($ev, $participants) {
        return $this->evenementDAO->evenement_edit($ev, $participants);
    }

    /**
     * Récupère un événement par son ID via le DAO
     * @param int $id ID de l'événement
     * @return EvenementEntity|null Objet EvenementEntity ou null si non trouvé
     */
    public function evenement_get_by_id($id) {
        return $this->evenementDAO->evenement_get_by_id($id);
    }

    /**
     * Récupère la liste de tous les événements via le DAO
     * @return array Tableau d'objets EvenementEntity
     */
    public function evenement_get_all() {
        return $this->evenementDAO->evenement_get_all();
    }

    /**
     * Récupère la liste des participants d'un événement via le DAO
     * @param int $id ID de l'événement
     * @return array Tableau d'objets ParticipantEntity
     */
    public function evenement_get_participant($id) {
        return $this->evenementDAO->evenement_get_participant($id);
    }
}
?>