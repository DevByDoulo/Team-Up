<?php
/**
 * Classe TeamService - Couche service pour la gestion des équipes
 * Fait le pont entre le contrôleur et le DAO
 */

require_once dirname(__DIR__) . '/dal/teamdao.php';
require_once dirname(__DIR__) . '/models/teamentity.php';

class TeamService {
    private $teamDAO;

    /**
     * Constructeur de la classe TeamService
     * Instancie le DAO pour les opérations sur les équipes
     */
    public function __construct() {
        $this->teamDAO = new TeamDAO();
    }

    /**
     * Récupère la liste de toutes les équipes
     * @return array Tableau d'objets TeamEntity
     */
    public function getteamlist() {
        return $this->teamDAO->getteamlist();
    }

    /**
     * Ajoute une nouvelle équipe
     * @param TeamEntity $team Objet équipe à ajouter
     * @return bool True si l'ajout réussit, False sinon
     */
    public function addteam(TeamEntity $team) {
        return $this->teamDAO->addteam($team);
    }

    /**
     * Modifie une équipe existante
     * @param TeamEntity $team Objet équipe avec les nouvelles données
     * @return bool True si la modification réussit, False sinon
     */
    public function editteam(TeamEntity $team) {
        return $this->teamDAO->editteam($team);
    }

    /**
     * Récupère la liste des utilisateurs appartenant à une équipe
     * @param int $id_equipe ID de l'équipe
     * @return array Tableau d'utilisateurs
     */
    public function getuserteam($id_equipe) {
        return $this->teamDAO->getuserteam($id_equipe);
    }

    /**
     * Ajoute un utilisateur à une équipe
     * @param int $id_utilisateur ID de l'utilisateur
     * @param int $id_equipe ID de l'équipe
     * @return bool True si l'ajout réussit, False sinon
     */
    public function adduserteam($id_utilisateur, $id_equipe) {
        return $this->teamDAO->adduserteam($id_utilisateur, $id_equipe);
    }

    /**
     * Retire un utilisateur d'une équipe
     * @param int $id_utilisateur ID de l'utilisateur
     * @param int $id_equipe ID de l'équipe
     * @return bool True si le retrait réussit, False sinon
     */
    public function removeuserteam($id_utilisateur, $id_equipe) {
        return $this->teamDAO->removeuserteam($id_utilisateur, $id_equipe);
    }

    /**
     * Récupère la liste des utilisateurs n'appartenant pas à l'équipe
     * @param int $id_equipe ID de l'équipe
     * @return array Tableau d'utilisateurs
     */
    public function getusernotinteam($id_equipe) {
        return $this->teamDAO->getusernotinteam($id_equipe);
    }

    /**
     * Supprime une équipe
     * @param int $id_equipe ID de l'équipe à supprimer
     * @return bool True si la suppression réussit, False sinon
     */
    public function deleteteam($id_equipe) {
        return $this->teamDAO->deleteteam($id_equipe);
    }
}
?>