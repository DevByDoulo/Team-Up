<?php
/**
 * Modèle UserEntity
 * Représente un utilisateur dans le système
 */

class UserEntity {
    // Propriétés publiques correspondant aux colonnes de la table utilisateur
    public $id_utilisateur;
    public $utilisateur_nom;
    public $utilisateur_login;
    public $utilisateur_pwd;
    public $utilisateur_email;
    public $utilisateur_creation;

    /**
     * Constructeur de la classe UserEntity
     * @param int|null $id_utilisateur ID de l'utilisateur
     * @param string|null $utilisateur_nom Nom de l'utilisateur
     * @param string|null $utilisateur_login Login de l'utilisateur
     * @param string|null $utilisateur_pwd Mot de passe de l'utilisateur
     * @param string|null $utilisateur_email Email de l'utilisateur
     * @param string|null $utilisateur_creation Date de création
     */
    public function __construct($id_utilisateur = null, $utilisateur_nom = null, $utilisateur_login = null, $utilisateur_pwd = null, $utilisateur_email = null, $utilisateur_creation = null) {
        $this->id_utilisateur = $id_utilisateur;
        $this->utilisateur_nom = $utilisateur_nom;
        $this->utilisateur_login = $utilisateur_login;
        $this->utilisateur_pwd = $utilisateur_pwd;
        $this->utilisateur_email = $utilisateur_email;
        $this->utilisateur_creation = $utilisateur_creation;
    }
}
?>