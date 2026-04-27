<?php
/**
 * Classe UserDAO - Data Access Object pour la table utilisateur
 * Gère les opérations CRUD sur la table utilisateur
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/models/userentity.php';

class UserDAO {
    /**
     * Constructeur de la classe UserDAO
     * La connexion est gérée via la fonction get_db_connection()
     */
    public function __construct() {
        // Connexion gérée par get_db_connection()
    }

    /**
     * Ajoute un nouvel utilisateur dans la base de données
     * @param UserEntity $user Objet utilisateur à ajouter
     * @return bool True si l'insertion réussit, False sinon
     */
    public function adduser(UserEntity $user) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return false;
        }

        // Préparation de la requête INSERT
        // Hachage du mot de passe avant insertion en base
        $hashedPassword = password_hash($user->utilisateur_pwd, PASSWORD_DEFAULT);
        $creation = $user->utilisateur_creation ? $user->utilisateur_creation : date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO utilisateur (utilisateur_nom, utilisateur_login, utilisateur_pwd, utilisateur_email, utilisateur_creation) VALUES (?, ?, ?, ?, ?)");

        $stmt->bind_param("sssss", 
            $user->utilisateur_nom,
            $user->utilisateur_login,
            $hashedPassword,
            $user->utilisateur_email,
            $creation
        );

        $result = $stmt->execute();
        
        // Récupérer l'ID de l'utilisateur inséré
        $inserted_id = $result ? $conn->insert_id : false;
        
        $stmt->close();

        return $inserted_id;
    }

    /**
     * Récupère la liste des utilisateurs
     * @param string|null $filtrenom Filtre optionnel sur le nom (recherche partielle)
     * @return array Tableau d'objets UserEntity
     */
    public function getuserlist($filtrenom = null) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return array();
        }

        // Construction de la requête SQL avec prepared statement
        $sql = "SELECT id_utilisateur, utilisateur_nom, utilisateur_login, utilisateur_pwd, utilisateur_email, utilisateur_creation FROM utilisateur";

        // Ajout du filtre si spécifié
        if ($filtrenom !== null && $filtrenom !== '') {
            $sql .= " WHERE utilisateur_nom LIKE ?";
        }

        $sql .= " ORDER BY utilisateur_nom ASC";

        $stmt = $conn->prepare($sql);

        if ($filtrenom !== null && $filtrenom !== '') {
            $likePattern = '%' . $filtrenom . '%';
            $stmt->bind_param("s", $likePattern);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        $users = array();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $user = new UserEntity(
                    $row['id_utilisateur'],
                    $row['utilisateur_nom'],
                    $row['utilisateur_login'],
                    $row['utilisateur_pwd'],
                    $row['utilisateur_email'],
                    $row['utilisateur_creation']
                );
                $users[] = $user;
            }
        }

        $stmt->close();
        return $users;
    }

    /**
     * Récupère un utilisateur par son ID
     * @param int $id ID de l'utilisateur
     * @return UserEntity|null Objet UserEntity ou null si non trouvé
     */
    public function getuserbyid($id) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return null;
        }

        $stmt = $conn->prepare("SELECT id_utilisateur, utilisateur_nom, utilisateur_login, utilisateur_pwd, utilisateur_email, utilisateur_creation FROM utilisateur WHERE id_utilisateur = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $user = null;
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $user = new UserEntity(
                $row['id_utilisateur'],
                $row['utilisateur_nom'],
                $row['utilisateur_login'],
                $row['utilisateur_pwd'],
                $row['utilisateur_email'],
                $row['utilisateur_creation']
            );
        }

        $stmt->close();
        return $user;
    }

    /**
     * Vérifie les identifiants de connexion d'un utilisateur
     * @param string $login Login de l'utilisateur
     * @param string $password Mot de passe en clair à vérifier
     * @return UserEntity|null Objet UserEntity si authentification réussie, null sinon
     */
    public function authenticateUser($login, $password) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return null;
        }

        $stmt = $conn->prepare("SELECT id_utilisateur, utilisateur_nom, utilisateur_login, utilisateur_pwd, utilisateur_email, utilisateur_creation FROM utilisateur WHERE utilisateur_login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        $user = null;
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            // Vérification du mot de passe haché
            if (password_verify($password, $row['utilisateur_pwd'])) {
                $user = new UserEntity(
                    $row['id_utilisateur'],
                    $row['utilisateur_nom'],
                    $row['utilisateur_login'],
                    $row['utilisateur_pwd'],
                    $row['utilisateur_email'],
                    $row['utilisateur_creation']
                );
            }
        }

        $stmt->close();
        return $user;
    }
}
?>