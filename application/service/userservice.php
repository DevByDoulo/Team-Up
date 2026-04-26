<?php
/**
 * Classe UserService - Couche service pour la gestion des utilisateurs
 * Fait le pont entre le contrôleur et le DAO
 */

require_once dirname(__DIR__) . '/dal/userdao.php';
require_once dirname(__DIR__) . '/models/userentity.php';

class UserService {
    private $userDAO;

    /**
     * Constructeur de la classe UserService
     * Instancie le DAO pour les opérations sur les utilisateurs
     */
    public function __construct() {
        $this->userDAO = new UserDAO();
    }

    /**
     * Ajoute un nouvel utilisateur via le DAO
     * @param UserEntity $user Objet utilisateur à ajouter
     * @return bool True si l'ajout réussit, False sinon
     */
    public function adduser(UserEntity $user) {
        return $this->userDAO->adduser($user);
    }

    /**
     * Récupère la liste des utilisateurs via le DAO
     * @param string|null $filtrenom Filtre optionnel sur le nom
     * @return array Tableau d'objets UserEntity
     */
    public function getuserlist($filtrenom = null) {
        return $this->userDAO->getuserlist($filtrenom);
    }

    /**
     * Récupère un utilisateur par son ID
     * @param int $id ID de l'utilisateur
     * @return UserEntity|null Objet UserEntity ou null si non trouvé
     */
    public function getuserbyid($id) {
        return $this->userDAO->getuserbyid($id);
    }
}
?>