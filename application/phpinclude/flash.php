<?php
/**
 * Système de messages flash (notifications temporaires)
 */

session_start();

/**
 * Définit un message flash
 * @param string $type Type du message (success, danger, warning, info)
 * @param string $message Contenu du message
 */
function setFlash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Affiche et supprime le message flash
 * @return string HTML du message ou chaîne vide
 */
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        
        $icons = [
            'success' => 'check-circle',
            'danger' => 'exclamation-circle',
            'warning' => 'exclamation-triangle',
            'info' => 'info-circle'
        ];
        
        $icon = $icons[$flash['type']] ?? 'info-circle';
        
        return '
        <div class="alert alert-' . $flash['type'] . ' alert-dismissible fade show" role="alert">
            <i class="fas fa-' . $icon . '"></i> ' . $flash['message'] . '
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>';
    }
    return '';
}
?>