<?php
// Test pour voir si la table type_demande existe et a des données
require_once 'config.php';

$conn = get_db_connection();

if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

echo "Connexion OK !<br><br>";

// Vérifier si la table existe
$result = $conn->query("SHOW TABLES LIKE 'type_demande'");
if ($result->num_rows > 0) {
    echo "✅ Table 'type_demande' existe<br><br>";
    
    // Compter les lignes
    $count = $conn->query("SELECT COUNT(*) as total FROM type_demande");
    $row = $count->fetch_assoc();
    echo "Nombre de types : " . $row['total'] . "<br><br>";
    
    // Afficher les types
    $types = $conn->query("SELECT * FROM type_demande");
    echo "Types trouvés :<br>";
    while ($t = $types->fetch_assoc()) {
        echo " - ID {$t['id_type_demande']} : {$t['type_demande_label']}<br>";
    }
} else {
    echo "❌ Table 'type_demande' n'existe PAS<br><br>";
    echo "Tables existantes :<br>";
    $tables = $conn->query("SHOW TABLES");
    while ($t = $tables->fetch_array()) {
        echo " - {$t[0]}<br>";
    }
}

$conn->close();
?>