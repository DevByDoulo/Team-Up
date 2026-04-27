<?php
/**
 * Modèle ParticipantEntity
 * Représente la relation entre un événement et un participant
 */

class ParticipantEntity {
    // Propriétés publiques correspondant aux colonnes de la table participant
    public $id_evenement;
    public $id_utilisateur;

    /**
     * Constructeur de la classe ParticipantEntity
     * @param int|null $id_evenement ID de l'événement
     * @param int|null $id_utilisateur ID de l'utilisateur participant
     */
    public function __construct($id_evenement = null, $id_utilisateur = null) {
        $this->id_evenement = $id_evenement;
        $this->id_utilisateur = $id_utilisateur;
    }
}
?>