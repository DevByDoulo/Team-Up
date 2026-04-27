<?php
/**
 * Modèle EvenementEntity
 * Représente un événement dans le système
 */

class EvenementEntity {
    // Propriétés publiques correspondant aux colonnes de la table evenement
    public $id_evenement;
    public $evenement_subject;
    public $evenement_description;
    public $evenement_location;
    public $evenement_dtstart;
    public $evenement_dtend;
    public $evenement_tstamp;
    public $evenement_uid;
    public $id_utilisateur;
    public $utilisateur_nom;

    /**
     * Constructeur de la classe EvenementEntity
     * @param int|null $id_evenement ID de l'événement
     * @param string|null $evenement_subject Sujet de l'événement
     * @param string|null $evenement_description Description de l'événement
     * @param string|null $evenement_location Lieu de l'événement
     * @param string|null $evenement_dtstart Date/heure de début
     * @param string|null $evenement_dtend Date/heure de fin
     * @param string|null $evenement_tstamp Timestamp de création/modification
     * @param string|null $evenement_uid UID unique de l'événement
     * @param int|null $id_utilisateur ID de l'utilisateur organisateur
     * @param string|null $utilisateur_nom Nom de l'organisateur
     */
    public function __construct($id_evenement = null, $evenement_subject = null, $evenement_description = null, $evenement_location = null, $evenement_dtstart = null, $evenement_dtend = null, $evenement_tstamp = null, $evenement_uid = null, $id_utilisateur = null, $utilisateur_nom = null) {
        $this->id_evenement = $id_evenement;
        $this->evenement_subject = $evenement_subject;
        $this->evenement_description = $evenement_description;
        $this->evenement_location = $evenement_location;
        $this->evenement_dtstart = $evenement_dtstart;
        $this->evenement_dtend = $evenement_dtend;
        $this->evenement_tstamp = $evenement_tstamp;
        $this->evenement_uid = $evenement_uid;
        $this->id_utilisateur = $id_utilisateur;
        $this->utilisateur_nom = $utilisateur_nom;
    }
}
?>