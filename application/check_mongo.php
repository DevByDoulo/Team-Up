<?php
/**
 * Script de diagnostic pour l'extension MongoDB
 */

echo "<h1>🔍 Diagnostic de l'extension MongoDB</h1>";

// 1. Vérifier si l'extension est chargée
echo "<h2>1. Extension MongoDB</h2>";
if (extension_loaded('mongodb')) {
    echo "<p>✅ Extension MongoDB est chargée</p>";
    echo "<p>Version : " . phpversion('mongodb') . "</p>";
} else {
    echo "<p style='color: red;'>❌ Extension MongoDB n'est PAS chargée</p>";
    echo "<h3>Solutions possibles :</h3>";
    echo "<ol>";
    echo "<li>Décommentez la ligne <code>extension=mongodb</code> dans votre php.ini</li>";
    echo "<li>Redémarrez votre serveur web (Apache/Laragon)</li>";
    echo "<li>Vérifiez que l'extension est bien installée</li>";
    echo "</ol>";
}

// 2. Vérifier les informations PHP
echo "<h2>2. Informations PHP</h2>";
echo "<p>Version PHP : " . PHP_VERSION . "</p>";
echo "<p>Fichier php.ini : " . php_ini_loaded_file() . "</p>";

// 3. Vérifier Composer
echo "<h2>3. Vérification Composer</h2>";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "<p>✅ Autoloader Composer trouvé</p>";
    require_once __DIR__ . '/vendor/autoload.php';
    
    // Vérifier si la librairie MongoDB est installée
    $installed = json_decode(file_get_contents(__DIR__ . '/composer.lock'), true);
    $mongodbFound = false;
    
    foreach ($installed['packages'] as $package) {
        if ($package['name'] === 'mongodb/mongodb') {
            echo "<p>✅ Librairie mongodb/mongodb installée : " . $package['version'] . "</p>";
            $mongodbFound = true;
            break;
        }
    }
    
    if (!$mongodbFound) {
        echo "<p style='color: orange;'>⚠️ Librairie mongodb/mongodb non trouvée dans composer.lock</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Autoloader Composer non trouvé</p>";
    echo "<p>Exécutez : <code>composer install</code> dans le dossier application</p>";
}

// 4. Test de connexion si possible
echo "<h2>4. Test de connexion MongoDB</h2>";
if (extension_loaded('mongodb')) {
    try {
        require_once __DIR__ . '/vendor/autoload.php';
        $client = new MongoDB\Client("mongodb://localhost:27017");
        echo "<p>✅ Connexion à MongoDB réussie</p>";
        
        // Lister les bases de données
        $databases = $client->listDatabases();
        echo "<p>Bases de données disponibles :</p>";
        echo "<ul>";
        foreach ($databases as $db) {
            echo "<li>" . htmlspecialchars($db->getName()) . "</li>";
        }
        echo "</ul>";
        
        // Vérifier la base teamup
        $teamupExists = false;
        foreach ($client->listDatabases() as $db) {
            if ($db->getName() === 'teamup') {
                $teamupExists = true;
                break;
            }
        }
        
        if ($teamupExists) {
            echo "<p>✅ Base de données 'teamup' existe</p>";
            
            $teamupDB = $client->teamup;
            $collections = $teamupDB->listCollections();
            
            $collectionList = [];
            foreach ($collections as $collection) {
                $collectionList[] = $collection->getName();
            }
            
            if (!empty($collectionList)) {
                echo "<p>Collections dans teamup :</p>";
                echo "<ul>";
                foreach ($collectionList as $collectionName) {
                    echo "<li>" . htmlspecialchars($collectionName) . "</li>";
                }
                echo "</ul>";
            } else {
                echo "<p>⚠️ Aucune collection dans la base teamup</p>";
            }
        } else {
            echo "<p style='color: orange;'>⚠️ Base de données 'teamup' n'existe pas encore</p>";
            echo "<p>Elle sera créée automatiquement lors du premier message</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p>Vérifiez que :</p>";
        echo "<ul>";
        echo "<li>MongoDB est installé et démarré</li>";
        echo "<li>Le service écoute sur le port 27017</li>";
        echo "</ul>";
    }
} else {
    echo "<p style='color: red;'>❌ Impossible de tester la connexion (classe MongoDB\Client non disponible)</p>";
}

// 5. Instructions pour Laragon
echo "<h2>5. Instructions pour Laragon</h2>";
echo "<p>Pour activer l'extension MongoDB dans Laragon :</p>";
echo "<ol>";
echo "<li>Allez dans Menu → PHP → php.ini</li>";
echo "<li>Cherchez la ligne <code>;extension=mongodb</code></li>";
echo "<li>Décommentez-la (enlevez le point-virgule) : <code>extension=mongodb</code></li>";
echo "<li>Sauvegardez le fichier</li>";
echo "<li>Redémarrez Laragon (Menu → Restart All)</li>";
echo "</ol>";

// 6. Vérifier si MongoDB est en cours d'exécution
echo "<h2>6. Vérification du service MongoDB</h2>";
echo "<p>Pour vérifier si MongoDB fonctionne :</p>";
echo "<ul>";
echo "<li>Ouvrez un terminal et exécutez : <code>netstat -an | findstr 27017</code></li>";
echo "<li>Vous devriez voir une ligne avec LISTENING sur le port 27017</li>";
echo "<li>Sinon, démarrez le service MongoDB</li>";
echo "</ul>";
?>
