<?php
/**
 * Fichier de configuration de l'application
 * Point d'entrée vers la configuration sécurisée de la base de données
 */

// Charger la configuration sécurisée de la base de données
require_once __DIR__ . '/config/database.php';

/**
 * Vérification de sécurité au démarrage
 */
if (!DatabaseConfig::isSecure()) {
    $env = $_ENV['APP_ENV'] ?? 'development';
    if ($env === 'production') {
        die('<div style="background-color: #f8d7da; color: #721c24; padding: 20px; border: 1px solid #f5c6cb; border-radius: 4px; margin: 20px;">
            <h3>⚠️ Alerte de sécurité</h3>
            <p>La configuration de la base de données n\'est pas sécurisée pour la production.</p>
            <ul>
                <li>Créez un fichier <code>.env</code> à partir de <code>.env.example</code></li>
                <li>Modifiez le mot de passe par défaut de la base de données</li>
                <li>Évitez d\'utiliser l\'utilisateur root en production</li>
            </ul>
        </div>');
    }
}
?>