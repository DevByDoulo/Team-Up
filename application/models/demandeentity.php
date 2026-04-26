<?php
/**
 * Modèle DemandeEntity
 * Représente une demande dans le système
 */

class demandeEntity {
    // Propriétés publiques correspondant aux colonnes de la table demande
    public $id_demande;
    public $demande_objet;
    public $demande_texte;
    public $demande_date_creation;
    public $demande_date_echeance;
    public $id_type_demande;
    public $id_utilisateur;
    public $utilisateur_nom;  // Nom de l'utilisateur assigné (évite l'erreur de propriété dynamique)

    /**
     * Constructeur de la classe demandeEntity
     * @param int|null $id_demande ID de la demande
     * @param string|null $demande_objet Objet de la demande
     * @param string|null $demande_texte Texte de la demande
     * @param string|null $demande_date_creation Date de création
     * @param string|null $demande_date_echeance Date d'échéance
     * @param int|null $id_type_demande ID du type de demande
     * @param int|null $id_utilisateur ID de l'utilisateur assigné
     * @param string|null $utilisateur_nom Nom de l'utilisateur assigné
     */
    public function __construct($id_demande = null, $demande_objet = null, $demande_texte = null, $demande_date_creation = null, $demande_date_echeance = null, $id_type_demande = null, $id_utilisateur = null, $utilisateur_nom = null) {
        $this->id_demande = $id_demande;
        $this->demande_objet = $demande_objet;
        $this->demande_texte = $demande_texte;
        $this->demande_date_creation = $demande_date_creation;
        $this->demande_date_echeance = $demande_date_echeance;
        $this->id_type_demande = $id_type_demande;
        $this->id_utilisateur = $id_utilisateur;
        $this->utilisateur_nom = $utilisateur_nom;
    }
}
?>