<?php
/**
 * Modèle TeamEntity
 * Représente une équipe dans le système
 */

class TeamEntity {
    // Propriétés publiques correspondant aux colonnes de la table equipe
    public $id_equipe;
    public $equipe_nom;

    /**
     * Constructeur de la classe TeamEntity
     * @param int|null $id_equipe ID de l'équipe
     * @param string|null $equipe_nom Nom de l'équipe
     */
    public function __construct($id_equipe = null, $equipe_nom = null) {
        $this->id_equipe = $id_equipe;
        $this->equipe_nom = $equipe_nom;
    }
}
?>