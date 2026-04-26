<?php
/**
 * Modèle TypeDemandeEntity
 * Représente un type de demande dans le système
 */

class typeDemandeEntity {
    // Propriétés publiques correspondant aux colonnes de la table type_demande
    public $id_type_demande;
    public $type_demande_label;

    /**
     * Constructeur de la classe typeDemandeEntity
     * @param int|null $id_type_demande ID du type de demande
     * @param string|null $type_demande_label Libellé du type de demande
     */
    public function __construct($id_type_demande = null, $type_demande_label = null) {
        $this->id_type_demande = $id_type_demande;
        $this->type_demande_label = $type_demande_label;
    }
}
?>