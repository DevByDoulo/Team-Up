<?php
/**
 * Page de liste des utilisateurs
 */

require_once 'config.php';
require_once 'service/userservice.php';

// Initialisation du service
$userService = new UserService();

// Traitement de la recherche
$filtrenom = null;
$users = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['filtrenom'])) {
    $filtrenom = trim($_POST['filtrenom']);
    $users = $userService->getuserlist($filtrenom);
} else {
    $users = $userService->getuserlist();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Up - Utilisateurs</title>
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
        .table-responsive {
            margin-top: 20px;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #007bff;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
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
                <li class="nav-item active">
                    <a class="nav-link" href="utilisateurs.php">Utilisateurs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="equipes.php">Équipes</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="content-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0"><i class="fas fa-users"></i> Gestion des Utilisateurs</h2>
                <a href="adduser.php" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Ajouter un utilisateur
                </a>
            </div>

            <!-- Formulaire de recherche -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-search"></i> Rechercher un utilisateur</h5>
                    <form method="POST" action="">
                        <div class="form-row align-items-end">
                            <div class="col-md-8">
                                <label for="filtrenom">Nom de l'utilisateur</label>
                                <input type="text" class="form-control" id="filtrenom" name="filtrenom" 
                                       placeholder="Entrez un nom pour filtrer..." 
                                       value="<?php echo htmlspecialchars($filtrenom ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-filter"></i> Filtrer
                                </button>
                            </div>
                        </div>
                    </form>
                    <?php if ($filtrenom !== null && $filtrenom !== ''): ?>
                        <small class="text-muted mt-2 d-block">
                            <i class="fas fa-info-circle"></i> Affichage des utilisateurs contenant "<?php echo htmlspecialchars($filtrenom); ?>"
                        </small>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tableau des utilisateurs -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col" style="width: 60px;">#</th>
                            <th scope="col">Utilisateur</th>
                            <th scope="col">Login</th>
                            <th scope="col">Email</th>
                            <th scope="col">Date de création</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $index => $user): ?>
                                <tr>
                                    <td>
                                        <div class="user-avatar">
                                            <?php echo strtoupper(substr($user->utilisateur_nom, 0, 1)); ?>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <strong><?php echo htmlspecialchars($user->utilisateur_nom); ?></strong>
                                    </td>
                                    <td class="align-middle"><?php echo htmlspecialchars($user->utilisateur_login); ?></td>
                                    <td class="align-middle"><?php echo htmlspecialchars($user->utilisateur_email); ?></td>
                                    <td class="align-middle">
                                        <?php 
                                        $date = new DateTime($user->utilisateur_creation);
                                        echo $date->format('d/m/Y H:i');
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                                    <p class="text-muted">Aucun utilisateur trouvé</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Total : <?php echo count($users); ?> utilisateur(s) trouvé(s)
                </small>
                <a href="adduser.php" class="btn btn-outline-primary">
                    <i class="fas fa-plus"></i> Ajouter un nouvel utilisateur
                </a>
            </div>
        </div>
    </div>

    <!-- jQuery et Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
