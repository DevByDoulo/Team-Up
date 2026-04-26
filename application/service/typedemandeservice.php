<?php
/**
 * Classe TypeDemandeService - Couche service pour la gestion des types de demandes
 * Fait le pont entre le contrôleur et le DAO
 */

require_once dirname(__DIR__) . '/dal/typedemandedao.php';
require_once dirname(__DIR__) . '/models/typedemandeentity.php';

class TypeDemandeService {
    private $typeDemandeDAO;

    /**
     * Constructeur de la classe TypeDemandeService
     * Instancie le DAO pour les opérations sur les types de demandes
     */
    public function __construct() {
        $this->typeDemandeDAO = new TypeDemandeDAO();
    }

    /**
     * Récupère la liste des types de demandes
     * @return array Tableau d'objets typeDemandeEntity
     */
    public function gettypedemandelist() {
        return $this->typeDemandeDAO->gettypedemandelist();
    }
}
?>