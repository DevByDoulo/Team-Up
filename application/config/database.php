<?php
/**
 * Configuration sécurisée de la base de données
 * Utilise des variables d'environnement ou un fichier .env
 */

class DatabaseConfig {
    
    /**
     * Charge la configuration depuis les variables d'environnement ou le fichier .env
     * @return array Configuration de la base de données
     */
    public static function getConfig() {
        // Essayer de charger depuis .env
        $envFile = dirname(__DIR__) . '/.env';
        
        if (file_exists($envFile)) {
            self::loadEnvFile($envFile);
        }
        
        // Configuration par défaut (fallback)
        return array(
            'server' => $_ENV['DB_HOST'] ?? 'localhost',
            'port' => $_ENV['DB_PORT'] ?? 3306,
            'login' => $_ENV['DB_USER'] ?? 'root',
            'pwd' => $_ENV['DB_PASSWORD'] ?? 'Naruto2005',
            'dbname' => $_ENV['DB_NAME'] ?? 'teamup'
        );
    }
    
    /**
     * Charge le fichier .env et définit les variables d'environnement
     * @param string $filePath Chemin vers le fichier .env
     */
    private static function loadEnvFile($filePath) {
        if (!file_exists($filePath)) {
            return;
        }
        
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Ignorer les commentaires
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parser la ligne
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Supprimer les guillemets si présents
                $value = trim($value, '"\'');
                
                // Définir la variable d'environnement
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
    
    /**
     * Vérifie si la configuration est sécurisée
     * @return bool True si la configuration semble sécurisée
     */
    public static function isSecure() {
        $config = self::getConfig();
        
        // Vérifications de sécurité basiques
        $isSecure = true;
        
        // Le mot de passe ne doit pas être la valeur par défaut
        if ($config['pwd'] === 'Naruto2005' || $config['pwd'] === 'password' || $config['pwd'] === 'root') {
            $isSecure = false;
        }
        
        // L'utilisateur ne doit pas être root en production
        $env = $_ENV['APP_ENV'] ?? 'development';
        if ($env === 'production' && $config['login'] === 'root') {
            $isSecure = false;
        }
        
        return $isSecure;
    }
}

/**
 * Fonction qui retourne un tableau associatif avec les paramètres de connexion par défaut
 * @return array Tableau associatif avec server, login, pwd, dbname
 */
function get_default_connection() {
    return DatabaseConfig::getConfig();
}

/**
 * Fonction pour obtenir une connexion MySQL sécurisée
 * @return mysqli Connexion MySQL
 */
function get_db_connection() {
    static $conn = null;
    
    if ($conn === null) {
        $config = DatabaseConfig::getConfig();
        
        // Désactiver le rapport d'erreurs mysqli pour gérer nous-mêmes
        mysqli_report(MYSQLI_REPORT_OFF);
        
        $conn = @new mysqli(
            $config['server'],
            $config['login'],
            $config['pwd'],
            $config['dbname'],
            $config['port']
        );
        
        if ($conn->connect_error) {
            // Si échec, essayer avec socket Unix (spécifique Laragon)
            $conn = @new mysqli(
                'localhost',
                $config['login'],
                $config['pwd'],
                $config['dbname'],
                null,
                'C:/laragon/tmp/mysql.sock'
            );
            
            if ($conn->connect_error) {
                // Dernier essai avec 127.0.0.1 et options
                mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
                try {
                    $conn = new mysqli(
                        '127.0.0.1',
                        $config['login'],
                        $config['pwd'],
                        $config['dbname'],
                        $config['port']
                    );
                } catch (mysqli_sql_exception $e) {
                    die("Erreur de connexion à la base de données : " . $e->getMessage() . "<br>Vérifiez que MySQL est bien démarré dans Laragon.");
                }
            }
        }
        
        // Définir le charset
        $conn->set_charset("utf8");
    }
    
    return $conn;
}
?>
