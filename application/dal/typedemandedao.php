<?php
/**
 * Classe TypeDemandeDAO - Data Access Object pour la table type_demande
 * Gère les opérations sur les types de demandes
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/models/typedemandeentity.php';

class TypeDemandeDAO {
    private $server;
    private $login;
    private $pwd;
    private $dbname;

    /**
     * Constructeur de la classe TypeDemandeDAO
     * Récupère les paramètres de connexion via get_default_connection()
     */
    public function __construct() {
        // La connexion est gérée via la fonction get_db_connection()
    }

    /**
     * Récupère la liste de tous les types de demandes
     * @return array Tableau d'objets typeDemandeEntity
     */
    public function gettypedemandelist() {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return array();
        }

        $sql = "SELECT id_type_demande, type_demande_label FROM type_demande ORDER BY type_demande_label ASC";
        $result = $conn->query($sql);
        $types = array();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $type = new typeDemandeEntity(
                    $row['id_type_demande'],
                    $row['type_demande_label']
                );
                $types[] = $type;
            }
        }

        return $types;
    }
}
?>