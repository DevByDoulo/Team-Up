<?php
/**
 * Vue pour la page d'accueil
 * Affiche uniquement la présentation, la logique est gérée par le contrôleur
 */

// Charger la configuration des assets
require_once dirname(__DIR__) . '/config/assets.php';

// Démarrer la session pour vérifier l'authentification
session_start();

// Variables attendues du contrôleur
$menu_items = $menu_items ?? array();
$stats = $stats ?? array('users' => 0, 'teams' => 0, 'demands' => 0, 'events' => 0);

// Vérifier si l'utilisateur est connecté
$is_logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user_name = $_SESSION['user_name'] ?? 'Invité';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Up - Accueil</title>
    <!-- Assets locaux/configurables -->
    <?php echo AssetConfig::cssTags(['bootstrap.min.css', 'fontawesome.min.css']); ?>
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

        .welcome-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
            margin-top: 20px;
        }

        .welcome-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .welcome-card .card-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border: none;
            font-weight: 600;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
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

        .stat-card {
            border: none;
            border-radius: 15px;
            transition: all 0.3s ease;
            overflow: hidden;
            position: relative;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.2);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0.1) 100%);
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

        .btn-info {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            box-shadow: 0 2px 4px rgba(23, 162, 184, 0.3);
        }

        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(23, 162, 184, 0.4);
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
        }

        .alert-info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
            border-left: 4px solid #17a2b8;
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
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark <?php echo $navbar_class; ?>">
        <a class="navbar-brand" href="#">Team Up</a>
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
                <?php foreach ($menu_items as $item): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo htmlspecialchars($item['route']); ?>">
                            <?php echo htmlspecialchars($item['label']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="search" placeholder="Rechercher..." aria-label="Search">
                <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Rechercher</button>
            </form>
            <span class="navbar-text ml-3">
                <?php if ($is_logged_in): ?>
                    <span class="mr-2"><i class="fas fa-user"></i> <?php echo htmlspecialchars($user_name); ?></span>
                    <a href="messages.php" class="btn btn-sm btn-outline-light mr-2">
                        <i class="fas fa-comments"></i> Messagerie
                    </a>
                    <a href="logout.php" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-sign-out-alt"></i> Déconnexion
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-sm btn-outline-light mr-2">
                        <i class="fas fa-sign-in-alt"></i> Connexion
                    </a>
                    <a href="register.php" class="btn btn-sm btn-outline-light">
                        <i class="fas fa-user-plus"></i> Inscription
                    </a>
                <?php endif; ?>
            </span>
        </div>
    </nav>

    <div class="main-container fade-in">
        <!-- Carte de bienvenue -->
        <div class="card welcome-card">
            <div class="card-header">
                <h2 class="mb-0 display-5">
                    <i class="fas fa-rocket mr-3"></i>Bienvenue dans Team Up
                </h2>
                <p class="mb-0 mt-2 opacity-75">Votre plateforme de collaboration d'équipe</p>
            </div>
            <div class="card-body">
                <?php if ($is_logged_in): ?>
                    <div class="alert alert-success fade-in">
                        <h4 class="alert-heading">
                            <i class="fas fa-check-circle mr-2"></i>
                            Bienvenue <?php echo htmlspecialchars($user_name); ?> !
                        </h4>
                        <p class="mb-0">Vous êtes connecté et pouvez accéder à toutes les fonctionnalités de Team Up.</p>
                    </div>
                    
                    <!-- Accès rapide -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-comments mr-2"></i>
                                        Messagerie
                                    </h5>
                                </div>
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-comments fa-3x text-primary"></i>
                                    </div>
                                    <h5 class="card-title">Communication en temps réel</h5>
                                    <p class="card-text">Discutez instantanément avec votre équipe et collaborez efficacement</p>
                                    <a href="messages.php" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane mr-2"></i> 
                                        Accéder à la messagerie
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h5 class="mb-0">
                                        <i class="fas fa-users mr-2"></i>
                                        Équipes
                                    </h5>
                                </div>
                                <div class="card-body text-center">
                                    <div class="mb-3">
                                        <i class="fas fa-users fa-3x text-info"></i>
                                    </div>
                                    <h5 class="card-title">Gestion d'équipe</h5>
                                    <p class="card-text">Organisez vos équipes et optimisez vos collaborations</p>
                                    <a href="equipes.php" class="btn btn-info btn-lg">
                                        <i class="fas fa-user-friends mr-2"></i> 
                                        Voir les équipes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center mb-4">
                        <h3 class="display-5 text-primary mb-3">
                            <i class="fas fa-rocket mr-3"></i>
                            Team Up
                        </h3>
                        <p class="lead text-muted">Plateforme de collaboration et de gestion d'équipe</p>
                    </div>
                    
                    <div class="alert alert-info fade-in">
                        <h4 class="alert-heading">
                            <i class="fas fa-users mr-2"></i>
                            Rejoignez notre communauté !
                        </h4>
                        <p class="mb-3">Team Up est votre solution complète pour la gestion d'équipes, la collaboration sur les tâches et le suivi des projets.</p>
                        <hr>
                        <p class="mb-0">Connectez-vous ou inscrivez-vous pour accéder à la messagerie et collaborer avec votre équipe.</p>
                        <div class="mt-4 text-center">
                            <a href="login.php" class="btn btn-primary btn-lg mr-3">
                                <i class="fas fa-sign-in-alt mr-2"></i> 
                                Se connecter
                            </a>
                            <a href="register.php" class="btn btn-success btn-lg">
                                <i class="fas fa-user-plus mr-2"></i> 
                                S'inscrire
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
                
                <!-- Statistiques -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card stat-card text-white bg-info">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                                <h2 class="font-weight-bold"><?php echo $stats['users']; ?></h2>
                                <p class="card-text mb-0">Utilisateurs</p>
                                <small class="opacity-75">Actifs sur la plateforme</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-white bg-success">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="fas fa-users-cog fa-2x"></i>
                                </div>
                                <h2 class="font-weight-bold"><?php echo $stats['teams']; ?></h2>
                                <p class="card-text mb-0">Équipes</p>
                                <small class="opacity-75">Collaborations actives</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-white bg-warning">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="fas fa-tasks fa-2x"></i>
                                </div>
                                <h2 class="font-weight-bold"><?php echo $stats['demands']; ?></h2>
                                <p class="card-text mb-0">Demandes</p>
                                <small class="opacity-75">Tâches en cours</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card stat-card text-white bg-danger">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <i class="fas fa-calendar fa-2x"></i>
                                </div>
                                <h2 class="font-weight-bold"><?php echo $stats['events']; ?></h2>
                                <p class="card-text mb-0">Événements</p>
                                <small class="opacity-75">Planifiés cette semaine</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            </div>

    <!-- Scripts locaux/configurables -->
    <?php echo AssetConfig::jsTags(['jquery.min.js', 'bootstrap.min.js']); ?>
</body>
</html>
