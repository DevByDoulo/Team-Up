<?php
/**
 * Fichier de configuration de la base de données
 */

// Variables de connexion MySQL
$cx_server = 'localhost';
$cx_login = 'root';
$cx_pwd = 'Naruto2005';
$cx_dbname = 'teamup';

/**
 * Fonction qui retourne un tableau associatif avec les paramètres de connexion par défaut
 * @return array Tableau associatif avec server, login, pwd, dbname
 */
function get_default_connection() {
    return array(
        'server' => $GLOBALS['cx_server'],
        'login' => $GLOBALS['cx_login'],
        'pwd' => $GLOBALS['cx_pwd'],
        'dbname' => $GLOBALS['cx_dbname']
    );
}

/**
 * Fonction pour obtenir une connexion MySQL
 * @return mysqli Connexion MySQL
 */
function get_db_connection() {
    static $conn = null;
    
    if ($conn === null) {
        $config = get_default_connection();
        
        // Désactiver le rapport d'erreurs mysqli pour gérer nous-mêmes
        mysqli_report(MYSQLI_REPORT_OFF);
        
        $conn = @new mysqli(
            $config['server'],
            $config['login'],
            $config['pwd'],
            $config['dbname']
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
                        3306
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