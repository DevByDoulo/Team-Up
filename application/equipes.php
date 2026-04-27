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
        /* Styles modernes pour toute l'application */
        body {
            padding-top: 56px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .main-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
            margin: 20px auto;
            max-width: 1200px;
            animation: fadeInUp 0.6s ease-out;
            padding: 30px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border: none;
            font-weight: 600;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }

        .team-card {
            border-left: 4px solid #007bff;
            transition: all 0.3s;
        }

        .team-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 123, 255, 0.3);
            border-left: 6px solid #0056b3;
        }

        .user-list {
            max-height: 350px;
            overflow-y: auto;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            scrollbar-width: thin;
            scrollbar-color: #007bff #f8f9fa;
        }

        .user-list::-webkit-scrollbar {
            width: 8px;
        }

        .user-list::-webkit-scrollbar-track {
            background: #f8f9fa;
            border-radius: 4px;
        }

        .user-list::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border-radius: 4px;
        }

        .user-item {
            padding: 12px 15px;
            margin-bottom: 8px;
            background: white;
            border-radius: 10px;
            border: 2px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .user-item:hover {
            transform: translateX(5px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-color: #007bff;
        }

        .btn-remove {
            padding: 6px 12px;
            font-size: 0.875rem;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .btn-remove:hover {
            transform: scale(1.1);
        }

        .section-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
            transition: all 0.3s ease;
        }

        .section-icon:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(0, 123, 255, 0.4);
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
            transform: translateY(-1px);
        }

        .btn {
            border-radius: 25px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.4);
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4);
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 193, 7, 0.4);
        }

        .btn-outline-secondary {
            border: 2px solid #6c757d;
            color: #6c757d;
            background: transparent;
        }

        .btn-outline-secondary:hover {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(108, 117, 125, 0.4);
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
            color: #856404;
            border-left: 4px solid #ffc107;
        }

        .navbar {
            background: linear-gradient(135deg, #343a40 0%, #23272b 100%) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: #007bff !important;
            transform: translateY(-1px);
        }

        h2, h4, h5 {
            color: #2c3e50;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .text-success {
            color: #28a745 !important;
        }

        .text-warning {
            color: #ffc107 !important;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1rem;
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.4);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                padding: 20px;
                border-radius: 15px;
            }
            
            .card {
                border-radius: 10px;
            }
            
            .section-icon {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }
            
            .user-avatar {
                width: 35px;
                height: 35px;
                font-size: 0.9rem;
            }
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

    <div class="main-container fade-in">
        <div class="text-center mb-4">
            <h1 class="display-3 text-warning mb-3 font-weight-bold">
                <i class="fas fa-users-cog mr-3"></i>Gestion des Équipes
            </h1>
            <p class="lead text-info mb-0 font-weight-bold">
                Créez et gérez les équipes de collaboration
            </p>
        </div>

        <!-- Messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show fade-in" role="alert">
                <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : ($message_type === 'warning' ? 'exclamation-triangle' : 'exclamation-circle'); ?> mr-2"></i>
                <strong><?php echo $message_type === 'success' ? 'Succès !' : ($message_type === 'warning' ? 'Attention !' : 'Erreur !'); ?></strong> <?php echo $message; ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php endif; ?>

            <div class="row">
                <!-- Colonne gauche : Gestion des équipes -->
                <div class="col-md-4">
                    <div class="card team-card h-100 fade-in">
                        <div class="card-header">
                            <h4 class="mb-0">
                                <i class="fas fa-users mr-2"></i>
                                Gestion des Équipes
                            </h4>
                            <p class="mb-0 mt-2 opacity-75">Créez et modifiez les équipes</p>
                        </div>
                        <div class="card-body">
                            <div class="section-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            
                            <!-- Formulaire d'ajout d'équipe -->
                            <div class="mb-4">
                                <h5 class="font-weight-bold">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    Ajouter une équipe
                                </h5>
                                <form method="POST" action="">
                                    <input type="hidden" name="action" value="ajouter_equipe">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="nom_equipe" 
                                               placeholder="Entrez le nom de l'équipe" required>
                                        <div class="input-group-append">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Ajouter
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Liste des équipes -->
                            <div class="mb-4">
                                <h5 class="font-weight-bold">
                                    <i class="fas fa-list mr-2"></i>
                                    Sélectionner une équipe
                                </h5>
                                <form method="POST" action="">
                                    <select class="form-control" name="id_equipe" onchange="this.form.submit()">
                                        <option value="0">-- Choisir une équipe --</option>
                                        <?php foreach ($equipes as $eq): ?>
                                            <option value="<?php echo $eq->id_equipe; ?>" 
                                                <?php echo ($eq->id_equipe == $id_equipe_selectionnee) ? 'selected' : ''; ?>>
                                                <i class="fas fa-users mr-1"></i>
                                                <?php echo htmlspecialchars($eq->equipe_nom); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                            </div>

                            <?php if ($id_equipe_selectionnee > 0): ?>
                                <!-- Formulaire de modification -->
                                <div class="mt-4">
                                    <h5 class="font-weight-bold">
                                        <i class="fas fa-edit mr-2"></i>
                                        Modifier l'équipe
                                    </h5>
                                    <form method="POST" action="">
                                        <input type="hidden" name="action" value="modifier_equipe">
                                        <input type="hidden" name="id_equipe" value="<?php echo $id_equipe_selectionnee; ?>">
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="nouveau_nom" 
                                                   placeholder="Nouveau nom de l'équipe" value="<?php echo htmlspecialchars($nom_equipe_selectionnee); ?>">
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="fas fa-save"></i> Modifier
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Colonne droite : Gestion des membres -->
                <div class="col-md-8">
                    <?php if ($id_equipe_selectionnee > 0): ?>
                        <div class="card h-100 fade-in">
                            <div class="card-header">
                                <h4 class="mb-0">
                                    <i class="fas fa-users mr-2"></i>
                                    Équipe : <?php echo htmlspecialchars($nom_equipe_selectionnee); ?>
                                </h4>
                                <p class="mb-0 mt-2 opacity-75">Gérez les membres de cette équipe</p>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Utilisateurs dans l'équipe -->
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <h5 class="text-success font-weight-bold">
                                                <i class="fas fa-check-circle mr-2"></i>
                                                Membres actuels
                                                <span class="badge badge-success ml-2"><?php echo count($utilisateurs_equipe); ?></span>
                                            </h5>
                                            <div class="user-list">
                                                <?php if (!empty($utilisateurs_equipe)):?>
                                                    <?php foreach ($utilisateurs_equipe as $user): ?>
                                                        <div class="user-item">
                                                            <div class="d-flex align-items-center">
                                                                <div class="user-avatar mr-3">
                                                                    <?php echo strtoupper(substr($user['utilisateur_nom'], 0, 1)); ?>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <strong class="text-primary"><?php echo htmlspecialchars($user['utilisateur_nom']); ?></strong>
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-envelope mr-1"></i>
                                                                        <?php echo htmlspecialchars($user['utilisateur_email']); ?>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            <form method="POST" action="" style="display: inline;">
                                                                <input type="hidden" name="action" value="retirer_membre">
                                                                <input type="hidden" name="id_equipe" value="<?php echo $id_equipe_selectionnee; ?>">
                                                                <input type="hidden" name="id_utilisateur" value="<?php echo $user['id_utilisateur']; ?>">
                                                                <button type="submit" class="btn btn-danger btn-sm btn-remove" title="Retirer de l'équipe">
                                                                    <i class="fas fa-user-minus"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <div class="text-center py-4">
                                                        <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                                                        <p class="text-muted">Aucun membre dans cette équipe</p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Utilisateurs hors équipe -->
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <h5 class="text-warning font-weight-bold">
                                                <i class="fas fa-user-plus mr-2"></i>
                                                Membres disponibles
                                                <span class="badge badge-warning ml-2"><?php echo count($utilisateurs_hors_equipe); ?></span>
                                            </h5>
                                            <div class="user-list">
                                                <?php if (!empty($utilisateurs_hors_equipe)):?>
                                                    <?php foreach ($utilisateurs_hors_equipe as $user): ?>
                                                        <div class="user-item">
                                                            <div class="d-flex align-items-center">
                                                                <div class="user-avatar mr-3" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);">
                                                                    <?php echo strtoupper(substr($user['utilisateur_nom'], 0, 1)); ?>
                                                                </div>
                                                                <div class="flex-grow-1">
                                                                    <strong class="text-primary"><?php echo htmlspecialchars($user['utilisateur_nom']); ?></strong>
                                                                    <br>
                                                                    <small class="text-muted">
                                                                        <i class="fas fa-envelope mr-1"></i>
                                                                        <?php echo htmlspecialchars($user['utilisateur_email']); ?>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            <form method="POST" action="" style="display: inline;">
                                                                <input type="hidden" name="action" value="ajouter_membre">
                                                                <input type="hidden" name="id_equipe" value="<?php echo $id_equipe_selectionnee; ?>">
                                                                <input type="hidden" name="id_utilisateur" value="<?php echo $user['id_utilisateur']; ?>">
                                                                <button type="submit" class="btn btn-success btn-sm btn-remove" title="Ajouter à l'équipe">
                                                                    <i class="fas fa-user-plus"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <div class="text-center py-4">
                                                        <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                                        <p class="text-muted">Tous les utilisateurs sont dans l'équipe</p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card h-100 fade-in">
                            <div class="card-body text-center py-5">
                                <div class="section-icon mx-auto mb-4" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">
                                    <i class="fas fa-users-slash"></i>
                                </div>
                                <h4 class="text-muted font-weight-bold">Sélectionnez une équipe</h4>
                                <p class="text-muted">Choisissez une équipe dans la liste pour gérer ses membres</p>
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <strong>Conseil :</strong> Créez d'abord une équipe, puis sélectionnez-la pour ajouter des membres.
                                </div>
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
