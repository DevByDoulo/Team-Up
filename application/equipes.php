<?php
/**
 * Page de gestion des équipes
 */

require_once 'config.php';
require_once 'service/teamservice.php';
require_once 'service/userservice.php';

// Initialisation des services
$teamService = new TeamService();
$userService = new UserService();

$message = '';
$message_type = '';
$id_equipe_selectionnee = isset($_POST['id_equipe']) ? (int)$_POST['id_equipe'] : 0;

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ajout d'une équipe
    if (isset($_POST['action']) && $_POST['action'] === 'ajouter_equipe') {
        $nom_equipe = trim($_POST['nom_equipe'] ?? '');
        if (!empty($nom_equipe)) {
            $team = new TeamEntity(null, $nom_equipe);
            if ($teamService->addteam($team)) {
                $message = 'Équipe ajoutée avec succès';
                $message_type = 'success';
            } else {
                $message = 'Erreur lors de l\'ajout de l\'équipe';
                $message_type = 'danger';
            }
        } else {
            $message = 'Le nom de l\'équipe est obligatoire';
            $message_type = 'warning';
        }
    }
    
    // Ajout d'un utilisateur à l'équipe
    if (isset($_POST['action']) && $_POST['action'] === 'ajouter_membre') {
        $id_utilisateur = isset($_POST['id_utilisateur']) ? (int)$_POST['id_utilisateur'] : 0;
        if ($id_equipe_selectionnee > 0 && $id_utilisateur > 0) {
            if ($teamService->adduserteam($id_utilisateur, $id_equipe_selectionnee)) {
                $message = 'Utilisateur ajouté à l\'équipe';
                $message_type = 'success';
            } else {
                $message = 'L\'utilisateur est déjà dans cette équipe ou erreur';
                $message_type = 'warning';
            }
        }
    }
    
    // Retrait d'un utilisateur de l'équipe
    if (isset($_POST['action']) && $_POST['action'] === 'retirer_membre') {
        $id_utilisateur = isset($_POST['id_utilisateur']) ? (int)$_POST['id_utilisateur'] : 0;
        if ($id_equipe_selectionnee > 0 && $id_utilisateur > 0) {
            if ($teamService->removeuserteam($id_utilisateur, $id_equipe_selectionnee)) {
                $message = 'Utilisateur retiré de l\'équipe';
                $message_type = 'success';
            } else {
                $message = 'Erreur lors du retrait';
                $message_type = 'danger';
            }
        }
    }
    
    // Modification du nom de l'équipe
    if (isset($_POST['action']) && $_POST['action'] === 'modifier_equipe') {
        $nouveau_nom = trim($_POST['nouveau_nom'] ?? '');
        if ($id_equipe_selectionnee > 0 && !empty($nouveau_nom)) {
            $team = new TeamEntity($id_equipe_selectionnee, $nouveau_nom);
            if ($teamService->editteam($team)) {
                $message = 'Équipe modifiée avec succès';
                $message_type = 'success';
            } else {
                $message = 'Erreur lors de la modification';
                $message_type = 'danger';
            }
        }
    }
}

// Récupération des données
$equipes = $teamService->getteamlist();
$utilisateurs = $userService->getuserlist();
$utilisateurs_equipe = array();
$utilisateurs_hors_equipe = array();

if ($id_equipe_selectionnee > 0) {
    $utilisateurs_equipe = $teamService->getuserteam($id_equipe_selectionnee);
    $utilisateurs_hors_equipe = $teamService->getusernotinteam($id_equipe_selectionnee);
}

// Récupérer le nom de l'équipe sélectionnée
$nom_equipe_selectionnee = '';
foreach ($equipes as $eq) {
    if ($eq->id_equipe == $id_equipe_selectionnee) {
        $nom_equipe_selectionnee = $eq->equipe_nom;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Up - Équipes</title>
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
        .team-card {
            border-left: 4px solid #007bff;
            transition: all 0.3s;
        }
        .team-card:hover {
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .user-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 10px;
            background: #f8f9fa;
        }
        .user-item {
            padding: 8px 12px;
            margin-bottom: 5px;
            background: white;
            border-radius: 4px;
            border: 1px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-remove {
            padding: 2px 8px;
            font-size: 0.875rem;
        }
        .section-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="#"><i class="fas fa-users"></i> Team Up</a>
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
                <li class="nav-item active">
                    <a class="nav-link" href="equipes.php">Équipes</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="content-container">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0"><i class="fas fa-users-cog"></i> Gestion des Équipes</h2>
            </div>

            <!-- Messages -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <div class="row">
                <!-- Colonne gauche : Gestion des équipes -->
                <div class="col-md-4">
                    <div class="card team-card h-100">
                        <div class="card-body">
                            <div class="section-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h4 class="card-title">Équipes</h4>
                            
                            <!-- Formulaire d'ajout d'équipe -->
                            <form method="POST" action="" class="mb-3">
                                <input type="hidden" name="action" value="ajouter_equipe">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="nom_equipe" 
                                           placeholder="Nouvelle équipe" required>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Liste des équipes -->
                            <div class="mb-3">
                                <label class="font-weight-bold">Sélectionner une équipe :</label>
                                <form method="POST" action="">
                                    <select class="form-control" name="id_equipe" onchange="this.form.submit()">
                                        <option value="0">-- Choisir une équipe --</option>
                                        <?php foreach ($equipes as $eq): ?>
                                            <option value="<?php echo $eq->id_equipe; ?>" 
                                                <?php echo ($eq->id_equipe == $id_equipe_selectionnee) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($eq->equipe_nom); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </div>

                            <?php if ($id_equipe_selectionnee > 0): ?>
                                <!-- Formulaire de modification -->
                                <form method="POST" action="" class="mt-3">
                                    <input type="hidden" name="action" value="modifier_equipe">
                                    <input type="hidden" name="id_equipe" value="<?php echo $id_equipe_selectionnee; ?>">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="nouveau_nom" 
                                               placeholder="Nouveau nom" value="<?php echo htmlspecialchars($nom_equipe_selectionnee); ?>">
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-outline-secondary">
                                                <i class="fas fa-save"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Colonne droite : Gestion des membres -->
                <div class="col-md-8">
                    <?php if ($id_equipe_selectionnee > 0): ?>
                        <div class="card h-100">
                            <div class="card-body">
                                <h4 class="card-title">
                                    <i class="fas fa-users"></i> 
                                    Équipe : <?php echo htmlspecialchars($nom_equipe_selectionnee); ?>
                                </h4>
                                <hr>

                                <div class="row">
                                    <!-- Utilisateurs dans l'équipe -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <h5 class="text-success">
                                                <i class="fas fa-check-circle"></i> 
                                                Utilisateurs dans l'équipe
                                            </h5>
                                            <div class="user-list">
                                                <?php if (!empty($utilisateurs_equipe)):?>
                                                    <?php foreach ($utilisateurs_equipe as $user): ?>
                                                        <div class="user-item">
                                                            <span>
                                                                <strong><?php echo htmlspecialchars($user['utilisateur_nom']); ?></strong>
                                                                <br>
                                                                <small class="text-muted">
                                                                    <?php echo htmlspecialchars($user['utilisateur_email']); ?>
                                                                </small>
                                                            </span>
                                                            <form method="POST" action="" style="display: inline;">
                                                                <input type="hidden" name="action" value="retirer_membre">
                                                                <input type="hidden" name="id_equipe" value="<?php echo $id_equipe_selectionnee; ?>">
                                                                <input type="hidden" name="id_utilisateur" value="<?php echo $user['id_utilisateur']; ?>">
                                                                <button type="submit" class="btn btn-danger btn-sm btn-remove">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <p class="text-muted text-center py-2">Aucun utilisateur</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Utilisateurs hors équipe -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <h5 class="text-warning">
                                                <i class="fas fa-user-plus"></i> 
                                                Utilisateurs disponibles
                                            </h5>
                                            <div class="user-list">
                                                <?php if (!empty($utilisateurs_hors_equipe)):?>
                                                    <?php foreach ($utilisateurs_hors_equipe as $user): ?>
                                                        <div class="user-item">
                                                            <span>
                                                                <strong><?php echo htmlspecialchars($user['utilisateur_nom']); ?></strong>
                                                                <br>
                                                                <small class="text-muted">
                                                                    <?php echo htmlspecialchars($user['utilisateur_email']); ?>
                                                                </small>
                                                            </span>
                                                            <form method="POST" action="" style="display: inline;">
                                                                <input type="hidden" name="action" value="ajouter_membre">
                                                                <input type="hidden" name="id_equipe" value="<?php echo $id_equipe_selectionnee; ?>">
                                                                <input type="hidden" name="id_utilisateur" value="<?php echo $user['id_utilisateur']; ?>">
                                                                <button type="submit" class="btn btn-success btn-sm btn-remove">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <p class="text-muted text-center py-2">Tous les utilisateurs sont dans l'équipe</p>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card h-100">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Sélectionnez une équipe</h4>
                                <p class="text-muted">Choisissez une équipe dans la liste pour gérer ses membres</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery et Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
