<?php
/**
 * Classe DemandeDAO - Data Access Object pour la table demande
 * Gère les opérations CRUD sur la table demande
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/models/demandeentity.php';

class DemandeDAO {

    /**
     * Constructeur de la classe DemandeDAO
     * Récupère les paramètres de connexion via get_default_connection()
     */
    public function __construct() {
        // La connexion est gérée via la fonction get_db_connection()
    }

    /**
     * Récupère la liste de toutes les demandes avec les noms d'utilisateurs
     * @return array Tableau d'objets demandeEntity
     */
    public function getlistdemandes() {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return array();
        }

        $sql = "SELECT demande.*, utilisateur.utilisateur_nom 
                FROM demande 
                LEFT JOIN utilisateur 
                ON demande.id_utilisateur = utilisateur.id_utilisateur
                ORDER BY demande.demande_date_creation DESC";
        
        $result = $conn->query($sql);
        $demandes = array();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $demande = new demandeEntity(
                    $row['id_demande'],
                    $row['demande_objet'],
                    $row['demande_texte'],
                    $row['demande_date_creation'],
                    $row['demande_date_echeance'],
                    $row['id_type_demande'],
                    $row['id_utilisateur']
                );
                // Ajout du nom d'utilisateur comme propriété supplémentaire
                $demande->utilisateur_nom = $row['utilisateur_nom'];
                $demandes[] = $demande;
            }
        }

        return $demandes;
    }

    /**
     * Récupère une demande par son ID
     * @param int $id ID de la demande
     * @return demandeEntity|null Objet demandeEntity ou null si non trouvé
     */
    public function getdemandebyid($id) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return null;
        }

        $stmt = $conn->prepare("SELECT demande.*, utilisateur.utilisateur_nom 
                                FROM demande 
                                LEFT JOIN utilisateur 
                                ON demande.id_utilisateur = utilisateur.id_utilisateur
                                WHERE id_demande = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();

        $demande = null;
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $demande = new demandeEntity(
                $row['id_demande'],
                $row['demande_objet'],
                $row['demande_texte'],
                $row['demande_date_creation'],
                $row['demande_date_echeance'],
                $row['id_type_demande'],
                $row['id_utilisateur']
            );
            $demande->utilisateur_nom = $row['utilisateur_nom'];
        }

        $stmt->close();
        return $demande;
    }

    /**
     * Ajoute une nouvelle demande
     * @param demandeEntity $demande Objet demande à ajouter
     * @return bool True si l'insertion réussit, False sinon
     */
    public function adddemande(demandeEntity $demande) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return false;
        }

        $stmt = $conn->prepare("INSERT INTO demande (demande_objet, demande_texte, demande_date_creation, demande_date_echeance, id_type_demande, id_utilisateur) VALUES (?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssii", 
            $demande->demande_objet,
            $demande->demande_texte,
            $demande->demande_date_creation,
            $demande->demande_date_echeance,
            $demande->id_type_demande,
            $demande->id_utilisateur
        );

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }

    /**
     * Modifie une demande existante
     * @param demandeEntity $demande Objet demande avec les nouvelles données
     * @return bool True si la modification réussit, False sinon
     */
    public function editdemande(demandeEntity $demande) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return false;
        }

        $stmt = $conn->prepare("UPDATE demande SET demande_objet = ?, demande_texte = ?, demande_date_creation = ?, demande_date_echeance = ?, id_type_demande = ?, id_utilisateur = ? WHERE id_demande = ?");
        $stmt->bind_param("ssssiii", 
            $demande->demande_objet,
            $demande->demande_texte,
            $demande->demande_date_creation,
            $demande->demande_date_echeance,
            $demande->id_type_demande,
            $demande->id_utilisateur,
            $demande->id_demande
        );

        $result = $stmt->execute();
        $stmt->close();

        return $result;
    }
}
?>