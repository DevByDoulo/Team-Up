<?php
/**
 * Contrôleur pour la page d'accueil
 * Gère la logique métier et sépare la présentation
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/models/userentity.php';
require_once dirname(__DIR__) . '/dal/userdao.php';

class HomeController {
    
    private $userDAO;
    
    public function __construct() {
        $this->userDAO = new UserDAO();
    }
    
    /**
     * Traite la gestion du profil utilisateur (thème)
     * @return string Thème sélectionné
     */
    public function handleUserProfile() {
        $theme = "0"; // Thème par défaut
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lst_theme'])) {
            $theme = $_POST['lst_theme'];
            // Sauvegarde dans un cookie (expiration 1 heure = 3600 secondes)
            setcookie('user_profile', $theme, time() + 3600, '/');
        } elseif (isset($_COOKIE['user_profile'])) {
            $theme = $_COOKIE['user_profile'];
        }
        
        return $theme;
    }
    
    /**
     * Récupère les statistiques pour le dashboard
     * @return array Statistiques de l'application
     */
    public function getDashboardStats() {
        $stats = array();
        
        try {
            $conn = get_db_connection();
            
            if (!$conn || $conn->connect_error) {
                throw new Exception("Erreur de connexion à la base de données");
            }
            
            // Nombre d'utilisateurs
            $result = $conn->query("SELECT COUNT(*) as total FROM utilisateur");
            if ($result && $row = $result->fetch_assoc()) {
                $stats['users'] = $row['total'];
            } else {
                $stats['users'] = 0;
            }
            
            // Nombre d'équipes
            $result = $conn->query("SELECT COUNT(*) as total FROM equipe");
            if ($result && $row = $result->fetch_assoc()) {
                $stats['teams'] = $row['total'];
            } else {
                $stats['teams'] = 0;
            }
            
            // Nombre de demandes
            $result = $conn->query("SELECT COUNT(*) as total FROM demande");
            if ($result && $row = $result->fetch_assoc()) {
                $stats['demands'] = $row['total'];
            } else {
                $stats['demands'] = 0;
            }
            
            // Nombre d'événements
            $result = $conn->query("SELECT COUNT(*) as total FROM evenement");
            if ($result && $row = $result->fetch_assoc()) {
                $stats['events'] = $row['total'];
            } else {
                $stats['events'] = 0;
            }
            
            $conn->close();
        } catch (Exception $e) {
            // En cas d'erreur, retourner des valeurs par défaut
            $stats = array('users' => 0, 'teams' => 0, 'demands' => 0, 'events' => 0);
        }
        
        return $stats;
    }
    
    /**
     * Charge le menu dynamique depuis le fichier JSON
     * @return array Items du menu
     */
    public function getMenuItems() {
        $menuFile = dirname(__DIR__) . '/phpinclude/menu.json';
        
        if (file_exists($menuFile)) {
            $menuJson = file_get_contents($menuFile);
            $menuItems = json_decode($menuJson, true);
            
            if (is_array($menuItems)) {
                return $menuItems;
            }
        }
        
        return array();
    }
    
    /**
     * Détermine la classe CSS pour la navbar selon le thème
     * @param string $theme Thème sélectionné
     * @return string Classe CSS pour la navbar
     */
    public function getNavbarClass($theme) {
        return ($theme === "2") ? "bg-dark" : "bg-primary";
    }
    
    /**
     * Retourne le libellé du thème pour l'affichage
     * @param string $theme Thème sélectionné
     * @return string Libellé du thème
     */
    public function getThemeLabel($theme) {
        switch($theme) {
            case "0": return "Thème par défaut";
            case "1": return "Clair";
            case "2": return "Foncé";
            default: return "Thème par défaut";
        }
    }
}
?>
