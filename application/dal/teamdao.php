<?php
/**
 * Classe TeamDAO - Data Access Object pour la gestion des équipes
 * Gère les opérations CRUD sur les tables equipe et utilisateur_equipe
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/models/teamentity.php';

class TeamDAO {

    /**
     * Récupère la liste de toutes les équipes
     * @return array Tableau d'objets TeamEntity
     */
    public function getteamlist() {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return array();
        }

        $sql = "SELECT id_equipe, equipe_nom FROM equipe ORDER BY equipe_nom ASC";
        $result = $conn->query($sql);
        $teams = array();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $team = new TeamEntity(
                    $row['id_equipe'],
                    $row['equipe_nom']
                );
                $teams[] = $team;
            }
        }

        return $teams;
    }

    /**
     * Ajoute une nouvelle équipe
     * @param TeamEntity $team Objet équipe à ajouter
     * @return bool True si l'ajout réussit, False sinon
     */
    public function addteam(TeamEntity $team) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return false;
        }

        $stmt = $conn->prepare("INSERT INTO equipe (equipe_nom) VALUES (?)");
        $stmt->bind_param("s", $team->equipe_nom);
        $result = $stmt->execute();
        
        $stmt->close();
        return $result;
    }

    /**
     * Modifie une équipe existante
     * @param TeamEntity $team Objet équipe avec les nouvelles données
     * @return bool True si la modification réussit, False sinon
     */
    public function editteam(TeamEntity $team) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return false;
        }

        $stmt = $conn->prepare("UPDATE equipe SET equipe_nom = ? WHERE id_equipe = ?");
        $stmt->bind_param("si", $team->equipe_nom, $team->id_equipe);
        $result = $stmt->execute();
        
        $stmt->close();
        return $result;
    }

    /**
     * Récupère la liste des utilisateurs appartenant à une équipe
     * @param int $id_equipe ID de l'équipe
     * @return array Tableau d'utilisateurs (tableau associatif)
     */
    public function getuserteam($id_equipe) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return array();
        }

        $sql = "SELECT u.id_utilisateur, u.utilisateur_nom, u.utilisateur_email 
                FROM utilisateur u
                INNER JOIN utilisateur_equipe ue ON u.id_utilisateur = ue.id_utilisateur
                WHERE ue.id_equipe = ?
                ORDER BY u.utilisateur_nom ASC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_equipe);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $users = array();
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }

        $stmt->close();
        return $users;
    }

    /**
     * Ajoute un utilisateur à une équipe
     * @param int $id_utilisateur ID de l'utilisateur
     * @param int $id_equipe ID de l'équipe
     * @return bool True si l'ajout réussit, False sinon
     */
    public function adduserteam($id_utilisateur, $id_equipe) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return false;
        }

        // Vérifier si l'utilisateur n'est pas déjà dans l'équipe
        $check = $conn->prepare("SELECT id_utilisateur FROM utilisateur_equipe WHERE id_utilisateur = ? AND id_equipe = ?");
        $check->bind_param("ii", $id_utilisateur, $id_equipe);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $check->close();
            return false; // Déjà dans l'équipe
        }
        $check->close();

        $stmt = $conn->prepare("INSERT INTO utilisateur_equipe (id_equipe, id_utilisateur) VALUES (?, ?)");
        $stmt->bind_param("ii", $id_equipe, $id_utilisateur);
        $result = $stmt->execute();
        
        $stmt->close();
        return $result;
    }

    /**
     * Retire un utilisateur d'une équipe
     * @param int $id_utilisateur ID de l'utilisateur
     * @param int $id_equipe ID de l'équipe
     * @return bool True si le retrait réussit, False sinon
     */
    public function removeuserteam($id_utilisateur, $id_equipe) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return false;
        }

        $stmt = $conn->prepare("DELETE FROM utilisateur_equipe WHERE id_utilisateur = ? AND id_equipe = ?");
        $stmt->bind_param("ii", $id_utilisateur, $id_equipe);
        $result = $stmt->execute();
        
        $stmt->close();
        return $result;
    }

    /**
     * Récupère la liste des utilisateurs n'appartenant pas à l'équipe
     * @param int $id_equipe ID de l'équipe
     * @return array Tableau d'utilisateurs (tableau associatif)
     */
    public function getusernotinteam($id_equipe) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return array();
        }

        $sql = "SELECT id_utilisateur, utilisateur_nom, utilisateur_email 
                FROM utilisateur 
                WHERE id_utilisateur NOT IN (
                    SELECT id_utilisateur 
                    FROM utilisateur_equipe 
                    WHERE id_equipe = ?
                )
                ORDER BY utilisateur_nom ASC";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id_equipe);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $users = array();
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $users[] = $row;
            }
        }

        $stmt->close();
        return $users;
    }

    /**
     * Supprime une équipe
     * @param int $id_equipe ID de l'équipe à supprimer
     * @return bool True si la suppression réussit, False sinon
     */
    public function deleteteam($id_equipe) {
        $conn = get_db_connection();

        if ($conn->connect_error) {
            return false;
        }

        // D'abord supprimer les associations
        $conn->query("DELETE FROM utilisateur_equipe WHERE id_equipe = " . (int)$id_equipe);
        
        // Puis supprimer l'équipe
        $stmt = $conn->prepare("DELETE FROM equipe WHERE id_equipe = ?");
        $stmt->bind_param("i", $id_equipe);
        $result = $stmt->execute();
        
        $stmt->close();
        return $result;
    }
}
?>