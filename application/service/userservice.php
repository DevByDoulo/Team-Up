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
     * Ajoute un nouvel utilisateur
     * @param UserEntity $user Objet utilisateur à ajouter
     * @return int|false ID de l'utilisateur inséré ou False en cas d'erreur
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

    /**
     * Authentifie un utilisateur avec son login et mot de passe
     * @param string $login Login de l'utilisateur
     * @param string $password Mot de passe de l'utilisateur
     * @return UserEntity|null Objet UserEntity si authentification réussie, null sinon
     */
    public function authenticateUser($login, $password) {
        return $this->userDAO->authenticateUser($login, $password);
    }
}
?>