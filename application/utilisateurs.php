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
        }

        .table-responsive {
            margin-top: 20px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, #343a40 0%, #23272b 100%);
            color: white;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
            transform: scale(1.01);
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.4);
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

        .btn-outline-primary {
            border: 2px solid #007bff;
            color: #007bff;
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.4);
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

        h2 {
            color: #2c3e50;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
            
            .user-avatar {
                width: 40px;
                height: 40px;
                font-size: 1rem;
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
                <li class="nav-item active">
                    <a class="nav-link" href="utilisateurs.php">Utilisateurs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="equipes.php">Équipes</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="main-container fade-in">
        <div class="text-center mb-4">
            <h1 class="display-3 text-warning mb-3 font-weight-bold">
                <i class="fas fa-users mr-3"></i>Gestion des Utilisateurs
            </h1>
            <p class="lead text-info mb-0 font-weight-bold">
                Consultez et gérez tous les utilisateurs de la plateforme
            </p>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h2 class="mb-0">
                    <i class="fas fa-users-cog mr-2"></i>
                    Gestion des Utilisateurs
                </h2>
                <p class="mb-0 mt-2 opacity-75">Consultez, recherchez et gérez tous les utilisateurs de la plateforme</p>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-list mr-2"></i>
                            Liste des utilisateurs
                        </h4>
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            <?php echo count($users); ?> utilisateur(s) au total
                        </small>
                    </div>
                    <a href="adduser.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus mr-2"></i> 
                        Ajouter un utilisateur
                    </a>
                </div>
            </div>
        </div>

        <!-- Formulaire de recherche -->
        <div class="card mb-4 fade-in">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-search mr-2"></i>
                    Recherche avancée
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="">
                    <div class="form-row align-items-end">
                        <div class="col-md-8">
                            <label for="filtrenom" class="form-label font-weight-bold">
                                <i class="fas fa-user mr-2"></i>
                                Nom de l'utilisateur
                            </label>
                            <input type="text" class="form-control" id="filtrenom" name="filtrenom" 
                                   placeholder="Entrez un nom pour filtrer les utilisateurs..." 
                                   value="<?php echo htmlspecialchars($filtrenom ?? ''); ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-filter mr-2"></i> 
                                Filtrer
                            </button>
                        </div>
                    </div>
                </form>
                <?php if ($filtrenom !== null && $filtrenom !== ''): ?>
                    <div class="alert alert-info mt-3 fade-in">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Résultats de recherche :</strong> Affichage des utilisateurs contenant "<?php echo htmlspecialchars($filtrenom); ?>"
                    </div>
                <?php endif; ?>
            </div>
        </div>

            <!-- Tableau des utilisateurs -->
            <div class="table-responsive fade-in">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 80px;">
                                <i class="fas fa-hashtag mr-2"></i>Avatar
                            </th>
                            <th scope="col">
                                <i class="fas fa-user mr-2"></i>Utilisateur
                            </th>
                            <th scope="col">
                                <i class="fas fa-key mr-2"></i>Login
                            </th>
                            <th scope="col">
                                <i class="fas fa-envelope mr-2"></i>Email
                            </th>
                            <th scope="col">
                                <i class="fas fa-calendar-alt mr-2"></i>Date de création
                            </th>
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
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <strong class="text-primary"><?php echo htmlspecialchars($user->utilisateur_nom); ?></strong>
                                                <br>
                                                <small class="text-muted">ID: <?php echo $user->id_utilisateur; ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-info">
                                            <i class="fas fa-key mr-1"></i>
                                            <?php echo htmlspecialchars($user->utilisateur_login); ?>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <a href="mailto:<?php echo htmlspecialchars($user->utilisateur_email); ?>" class="text-decoration-none">
                                            <i class="fas fa-envelope mr-1 text-muted"></i>
                                            <?php echo htmlspecialchars($user->utilisateur_email); ?>
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-clock text-muted mr-2"></i>
                                            <div>
                                                <strong><?php 
                                                $date = new DateTime($user->utilisateur_creation);
                                                echo $date->format('d/m/Y'); 
                                                ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo $date->format('H:i'); ?></small>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="fade-in">
                                        <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                        <h4 class="text-muted">Aucun utilisateur trouvé</h4>
                                        <p class="text-muted">
                                            <?php if ($filtrenom !== null && $filtrenom !== ''): ?>
                                                Essayez de modifier vos critères de recherche
                                            <?php else: ?>
                                                Commencez par ajouter des utilisateurs à la plateforme
                                            <?php endif; ?>
                                        </p>
                                        <a href="adduser.php" class="btn btn-primary mt-3">
                                            <i class="fas fa-user-plus mr-2"></i>
                                            Ajouter le premier utilisateur
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle mr-2"></i>
                    <strong>Total :</strong> <?php echo count($users); ?> utilisateur(s) trouvé(s)
                    <?php if ($filtrenom !== null && $filtrenom !== ''): ?>
                        <br>
                        <small>Pour la recherche : "<?php echo htmlspecialchars($filtrenom); ?>"</small>
                    <?php endif; ?>
                </div>
                <a href="adduser.php" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-plus mr-2"></i> 
                    Ajouter un nouvel utilisateur
                </a>
            </div>
        </div>
    </div>

    <!-- jQuery et Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
