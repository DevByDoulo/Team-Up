<?php
/**
 * Modèle MessageEntity
 * Représente un message de chat dans le système
 */

class MessageEntity {
    // Propriétés publiques correspondant aux champs de la collection MongoDB
    public $from;
    public $attendee;
    public $message;
    public $date;

    /**
     * Constructeur de la classe MessageEntity
     * @param string|null $from Expéditeur du message
     * @param string|null $attendee Destinataire du message (vide pour message public)
     * @param string|null $message Contenu du message
     * @param string|null $date Date et heure du message
     */
    public function __construct($from = null, $attendee = null, $message = null, $date = null) {
        $this->from = $from;
        $this->attendee = $attendee;
        $this->message = $message;
        $this->date = $date ?: date('Y-m-d H:i:s');
    }

    /**
     * Convertit l'entité en tableau pour l'insertion MongoDB
     * @return array Représentation tableau du message
     */
    public function toArray() {
        return [
            'from' => $this->from,
            'attendee' => $this->attendee,
            'message' => $this->message,
            'date' => $this->date
        ];
    }

    /**
     * Valide les données du message
     * @return bool True si les données sont valides
     */
    public function isValid() {
        return !empty($this->from) && !empty($this->message);
    }
}
?>
