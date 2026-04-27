<?php
/**
 * Configuration des assets (CSS, JS, fonts)
 * Permet de basculer entre assets locaux et CDN
 */

class AssetConfig {
    
    /**
     * Configuration des assets
     * @return array Configuration des assets
     */
    public static function getConfig() {
        $env = $_ENV['APP_ENV'] ?? 'development';
        
        // En développement, utiliser CDN pour la rapidité
        // En production, utiliser les fichiers locaux
        $useLocal = ($env === 'production');
        
        return array(
            'use_local' => $useLocal,
            'base_url' => $_ENV['APP_URL'] ?? 'http://localhost/teamup',
            'assets_path' => '/application/assets'
        );
    }
    
    /**
     * Génère l'URL pour un fichier CSS
     * @param string $file Nom du fichier CSS
     * @return string URL complète du fichier CSS
     */
    public static function css($file) {
        $config = self::getConfig();
        
        if ($config['use_local']) {
            return $config['base_url'] . $config['assets_path'] . '/css/' . $file;
        } else {
            // Fallback CDN pour les fichiers connus
            switch ($file) {
                case 'bootstrap.min.css':
                    return 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css';
                case 'fontawesome.min.css':
                    return 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css';
                default:
                    return $config['base_url'] . $config['assets_path'] . '/css/' . $file;
            }
        }
    }
    
    /**
     * Génère l'URL pour un fichier JavaScript
     * @param string $file Nom du fichier JS
     * @return string URL complète du fichier JS
     */
    public static function js($file) {
        $config = self::getConfig();
        
        if ($config['use_local']) {
            return $config['base_url'] . $config['assets_path'] . '/js/' . $file;
        } else {
            // Fallback CDN pour les fichiers connus
            switch ($file) {
                case 'jquery.min.js':
                    return 'https://code.jquery.com/jquery-3.5.1.min.js';
                case 'bootstrap.min.js':
                    return 'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js';
                default:
                    return $config['base_url'] . $config['assets_path'] . '/js/' . $file;
            }
        }
    }
    
    /**
     * Génère les balises CSS pour un tableau de fichiers
     * @param array $files Tableau de fichiers CSS
     * @return string Balises HTML
     */
    public static function cssTags($files) {
        $tags = '';
        foreach ($files as $file) {
            $url = self::css($file);
            $tags .= '<link rel="stylesheet" href="' . htmlspecialchars($url) . '">' . "\n";
        }
        return $tags;
    }
    
    /**
     * Génère les balises JavaScript pour un tableau de fichiers
     * @param array $files Tableau de fichiers JS
     * @return string Balises HTML
     */
    public static function jsTags($files) {
        $tags = '';
        foreach ($files as $file) {
            $url = self::js($file);
            $tags .= '<script src="' . htmlspecialchars($url) . '"></script>' . "\n";
        }
        return $tags;
    }
}
?>
