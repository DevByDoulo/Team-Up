<?php
/**
 * Classe EvenementDAO - Data Access Object pour la table evenement
 * Gère les opérations CRUD sur la table evenement et participant
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/models/evenemententity.php';
require_once dirname(__DIR__) . '/models/participantentity.php';

class EvenementDAO {
    /**
     * Constructeur de la classe EvenementDAO
     * La connexion est gérée via la fonction get_db_connection()
     */
    public function __construct() {
        // Connexion gérée par get_db_connection()
    }

    /**
     * Ajoute un nouvel événement dans la base de données
     * @param EvenementEntity $ev Objet événement à ajouter
     * @param array $participants Tableau d'IDs d'utilisateurs participants
     * @return int ID de l'événement inséré
     */
    public function evenement_add(EvenementEntity $ev, array $participants) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return 0;
        }

        // Générer evenement_uid avec uniqid()
        $ev->evenement_uid = uniqid('EVT_', true);
        // evenement_tstamp = date courante
        $ev->evenement_tstamp = date('Y-m-d H:i:s');

        $stmt = $conn->prepare("INSERT INTO evenement (evenement_subject, evenement_description, evenement_location, evenement_dtstart, evenement_dtend, evenement_tstamp, evenement_uid, id_utilisateur) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("sssssssi",
            $ev->evenement_subject,
            $ev->evenement_description,
            $ev->evenement_location,
            $ev->evenement_dtstart,
            $ev->evenement_dtend,
            $ev->evenement_tstamp,
            $ev->evenement_uid,
            $ev->id_utilisateur
        );

        $stmt->execute();
        $id = mysqli_insert_id($conn);
        $stmt->close();

        // Appeler evenement_set_participant() avec les participants
        if ($id > 0) {
            $this->evenement_set_participant($id, $participants);
        }

        // Retourner l'id inséré
        return $id;
    }

    /**
     * Modifie un événement existant dans la base de données
     * @param EvenementEntity $ev Objet événement modifié
     * @param array $participants Tableau d'IDs d'utilisateurs participants
     * @return bool True si la modification réussit, False sinon
     */
    public function evenement_edit(EvenementEntity $ev, array $participants) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return false;
        }

        $stmt = $conn->prepare("UPDATE evenement SET evenement_subject = ?, evenement_description = ?, evenement_location = ?, evenement_dtstart = ?, evenement_dtend = ?, evenement_tstamp = ? WHERE id_evenement = ?");

        if (!$stmt) {
            return false;
        }

        $ev->evenement_tstamp = date('Y-m-d H:i:s');

        $stmt->bind_param("ssssssi",
            $ev->evenement_subject,
            $ev->evenement_description,
            $ev->evenement_location,
            $ev->evenement_dtstart,
            $ev->evenement_dtend,
            $ev->evenement_tstamp,
            $ev->id_evenement
        );

        $result = $stmt->execute();
        $stmt->close();

        // Appeler evenement_set_participant() pour mettre à jour participants
        if ($result) {
            $this->evenement_set_participant($ev->id_evenement, $participants);
        }

        return $result;
    }

    /**
     * Récupère un événement par son ID
     * @param int $id_evenement ID de l'événement
     * @return EvenementEntity|null Objet EvenementEntity ou null si non trouvé
     */
    public function evenement_get_by_id($id_evenement) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return null;
        }

        $sql = "SELECT evenement.*, utilisateur.utilisateur_nom
                FROM evenement
                LEFT JOIN utilisateur ON evenement.id_utilisateur = utilisateur.id_utilisateur
                WHERE id_evenement = ?";

        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param("i", $id_evenement);
        $stmt->execute();
        $result = $stmt->get_result();

        $evenement = null;
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $evenement = new EvenementEntity(
                $row['id_evenement'],
                $row['evenement_subject'],
                $row['evenement_description'],
                $row['evenement_location'],
                $row['evenement_dtstart'],
                $row['evenement_dtend'],
                $row['evenement_tstamp'],
                $row['evenement_uid'],
                $row['id_utilisateur'],
                $row['utilisateur_nom']
            );
        }

        $stmt->close();
        return $evenement;
    }

    /**
     * Récupère la liste de tous les événements
     * @return array Tableau d'objets EvenementEntity
     */
    public function evenement_get_all() {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return array();
        }

        $sql = "SELECT evenement.*, utilisateur.utilisateur_nom
                FROM evenement
                LEFT JOIN utilisateur ON evenement.id_utilisateur = utilisateur.id_utilisateur
                ORDER BY evenement_dtstart DESC";

        $stmt = $conn->prepare($sql);
        
        if (!$stmt) {
            return array();
        }
        
        $stmt->execute();
        $result = $stmt->get_result();

        $evenements = array();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $evenement = new EvenementEntity(
                    $row['id_evenement'],
                    $row['evenement_subject'],
                    $row['evenement_description'],
                    $row['evenement_location'],
                    $row['evenement_dtstart'],
                    $row['evenement_dtend'],
                    $row['evenement_tstamp'],
                    $row['evenement_uid'],
                    $row['id_utilisateur'],
                    $row['utilisateur_nom']
                );
                $evenements[] = $evenement;
            }
        }

        $stmt->close();
        return $evenements;
    }

    /**
     * Met à jour les participants d'un événement
     * @param int $id_evenement ID de l'événement
     * @param array $participants Tableau d'IDs d'utilisateurs participants
     * @return bool True si la mise à jour réussit, False sinon
     */
    public function evenement_set_participant($id_evenement, array $participants) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return false;
        }

        // DELETE FROM participant WHERE id_evenement = $id_evenement
        $stmt = $conn->prepare("DELETE FROM participant WHERE id_evenement = ?");
        $stmt->bind_param("i", $id_evenement);
        $stmt->execute();
        $stmt->close();

        // Puis INSERT pour chaque participant dans le tableau
        if (!empty($participants)) {
            $stmt = $conn->prepare("INSERT INTO participant (id_evenement, id_utilisateur) VALUES (?, ?)");
            foreach ($participants as $id_utilisateur) {
                $stmt->bind_param("ii", $id_evenement, $id_utilisateur);
                $stmt->execute();
            }
            $stmt->close();
        }

        return true;
    }

    /**
     * Récupère la liste des participants d'un événement
     * @param int $id_evenement ID de l'événement
     * @return array Tableau d'objets ParticipantEntity
     */
    public function evenement_get_participant($id_evenement) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return array();
        }

        $sql = "SELECT * FROM participant WHERE id_evenement = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_evenement);
        $stmt->execute();
        $result = $stmt->get_result();

        $participants = array();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $participant = new ParticipantEntity(
                    $row['id_evenement'],
                    $row['id_utilisateur']
                );
                $participants[] = $participant;
            }
        }

        $stmt->close();
        return $participants;
    }
}
?>