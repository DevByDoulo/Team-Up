<?php
/**
 * Page Sendoffline - Envoi de message hors-ligne
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/service/userservice.php';

// Récupérer $_GET['attendee'] et $_GET['message']
$attendee = $_GET['attendee'] ?? '';
$message = $_GET['message'] ?? '';

// Inclure config.php et service/userservice.php
$userService = new UserService();

// Charger la liste des utilisateurs
$utilisateurs = $userService->getuserlist();

// Trouver l'email de l'expéditeur (utilisateur connecté = "Invité" pour l'instant)
$emailExpediteur = 'invite@teamup.com';
$nomExpediteur = 'Invité';

// Trouver l'email du destinataire
$emailDestinataire = '';
$nomDestinataire = $attendee;
foreach ($utilisateurs as $utilisateur) {
    if ($utilisateur->utilisateur_nom === $attendee) {
        $emailDestinataire = $utilisateur->utilisateur_email;
        $nomDestinataire = $utilisateur->utilisateur_nom;
        break;
    }
}

// Afficher un message "Message envoyé à [attendee]"
$resultat = "Message envoyé à " . htmlspecialchars($nomDestinataire) . " (" . htmlspecialchars($emailDestinataire ?: 'email non trouvé') . ")";

// Retourner ce texte (pas de HTML complet, juste le texte)
echo $resultat;
?>