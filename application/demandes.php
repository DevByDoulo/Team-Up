<?php
/**
 * Page de liste des demandes
 */

require_once 'config.php';
require_once 'service/demandeservice.php';
require_once 'service/userservice.php';
require_once 'service/typedemandeservice.php';

// Initialisation des services
$demandeService = new DemandeService();
$userService = new UserService();
$typeDemandeService = new TypeDemandeService();

// Récupération des données
$demandes = $demandeService->getlistdemandes();
$types = $typeDemandeService->gettypedemandelist();

// Créer un tableau associatif pour les types (id => label)
$typesAssoc = array();
foreach ($types as $type) {
    $typesAssoc[$type->id_type_demande] = $type->type_demande_label;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Up - Demandes</title>
    <!-- Bootstrap 4 via CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            padding-top: 56px;
            background-color: #f8f9fa;
        }
        .content-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .badge-type {
            font-size: 0.85em;
            padding: 0.5em 0.8em;
        }
        .statut-badge {
            font-size: 0.85em;
        }
        .table th {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="#"><i class="fas fa-tasks"></i> Team Up</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Accueil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="utilisateurs.php">Utilisateurs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="equipes.php">Équipes</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="demandes.php">Demandes</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="content-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0"><i class="fas fa-clipboard-list"></i> Gestion des Demandes</h2>
                <a href="adddemande.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nouvelle demande
                </a>
            </div>

            <?php if (count($demandes) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col" style="width: 60px;">#</th>
                                <th scope="col">Objet</th>
                                <th scope="col">Type</th>
                                <th scope="col">Création</th>
                                <th scope="col">Échéance</th>
                                <th scope="col">Assigné à</th>
                                <th scope="col" style="width: 120px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($demandes as $demande): 
                                $typeLabel = isset($typesAssoc[$demande->id_type_demande]) ? $typesAssoc[$demande->id_type_demande] : 'Inconnu';
                            ?>
                                <tr>
                                    <td class="align-middle"><?php echo $demande->id_demande; ?></td>
                                    <td class="align-middle">
                                        <strong><?php echo htmlspecialchars($demande->demande_objet); ?></strong>
                                        <?php if (!empty($demande->demande_texte)): ?>
                                            <br>
                                            <small class="text-muted">
                                                <?php echo nl2br(htmlspecialchars(substr($demande->demande_texte, 0, 80))) . (strlen($demande->demande_texte) > 80 ? '...' : ''); ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-info badge-type">
                                            <i class="fas fa-tag"></i> <?php echo htmlspecialchars($typeLabel); ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <?php 
                                        $dateCreation = new DateTime($demande->demande_date_creation);
                                        echo $dateCreation->format('d/m/Y H:i');
                                        ?>
                                    </td>
                                    <td class="align-middle">
                                        <?php 
                                        if (!empty($demande->demande_date_echeance)) {
                                            $dateEcheance = new DateTime($demande->demande_date_echeance);
                                            echo $dateEcheance->format('d/m/Y');
                                        } else {
                                            echo '<span class="text-muted">Non définie</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="align-middle">
                                        <?php 
                                        if (!empty($demande->id_utilisateur)) {
                                            echo htmlspecialchars($demande->utilisateur_nom ?: 'Inconnu');
                                        } else {
                                            echo '<span class="text-muted">Non assignée</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="align-middle">
                                        <a href="editdemande.php?id=<?php echo $demande->id_demande; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center py-4">
                    <i class="fas fa-info-circle fa-2x mb-3"></i>
                    <p class="mb-0">Aucune demande n'a été créée pour le moment.</p>
                    <p class="mb-0"><a href="adddemande.php" class="btn btn-primary mt-2">Créer la première demande</a></p>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Total : <?php echo count($demandes); ?> demande(s)
                </small>
            </div>
        </div>
    </div>

    <!-- jQuery et Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
