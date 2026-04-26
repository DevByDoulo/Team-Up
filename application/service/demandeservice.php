<?php
/**
 * Classe DemandeService - Couche service pour la gestion des demandes
 * Fait le pont entre le contrôleur et le DAO
 */

require_once dirname(__DIR__) . '/dal/demandedao.php';
require_once dirname(__DIR__) . '/models/demandeentity.php';

class DemandeService {
    private $demandeDAO;

    /**
     * Constructeur de la classe DemandeService
     * Instancie le DAO pour les opérations sur les demandes
     */
    public function __construct() {
        $this->demandeDAO = new DemandeDAO();
    }

    /**
     * Récupère la liste des demandes
     * @return array Tableau d'objets demandeEntity
     */
    public function getlistdemandes() {
        return $this->demandeDAO->getlistdemandes();
    }

    /**
     * Récupère une demande par son ID
     * @param int $id ID de la demande
     * @return demandeEntity|null Objet demandeEntity ou null si non trouvé
     */
    public function getdemandebyid($id) {
        return $this->demandeDAO->getdemandebyid($id);
    }

    /**
     * Ajoute une nouvelle demande
     * @param demandeEntity $demande Objet demande à ajouter
     * @return bool True si l'ajout réussit, False sinon
     */
    public function adddemande($demande) {
        return $this->demandeDAO->adddemande($demande);
    }

    /**
     * Modifie une demande existante
     * @param demandeEntity $demande Objet demande avec les nouvelles données
     * @return bool True si la modification réussit, False sinon
     */
    public function editdemande($demande) {
        return $this->demandeDAO->editdemande($demande);
    }
}
?>