<?php
/**
 * Page d'accueil de l'application Team Up
 */

// Gestion du profil utilisateur (cookie)
$theme = "0"; // Thème par défaut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lst_theme'])) {
    $theme = $_POST['lst_theme'];
    // Sauvegarde dans un cookie (expiration 1 heure = 3600 secondes)
    setcookie('user_profile', $theme, time() + 3600, '/');
} elseif (isset($_COOKIE['user_profile'])) {
    $theme = $_COOKIE['user_profile'];
}

// Détermine la classe de la navbar selon le thème
$navbar_class = "bg-primary";
if ($theme === "2") {
    $navbar_class = "bg-dark";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Up - Accueil</title>
    <!-- Bootstrap 4 via CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            padding-top: 56px;
        }
        .welcome-card {
            margin-top: 50px;
        }
        .theme-form {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <!-- Zone pour la navbar (sera remplacée par navbar.php) -->
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
                <?php
                // Chargement dynamique du menu depuis menu.json
                $menuFile = dirname(__FILE__) . '/phpinclude/menu.json';
                if (file_exists($menuFile)) {
                    $menuJson = file_get_contents($menuFile);
                    $menuItems = json_decode($menuJson, true);
                    if (is_array($menuItems)) {
                        foreach ($menuItems as $item) {
                            echo '<li class="nav-item">';
                            echo '<a class="nav-link" href="' . htmlspecialchars($item['route']) . '">' . htmlspecialchars($item['label']) . '</a>';
                            echo '</li>';
                        }
                    }
                }
                ?>
            </ul>
            <form class="form-inline my-2 my-lg-0">
                <input class="form-control mr-sm-2" type="search" placeholder="Rechercher..." aria-label="Search">
                <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Rechercher</button>
            </form>
            <span class="navbar-text ml-3">
                Invité
            </span>
        </div>
    </nav>

    <div class="container">
        <!-- Contenu de bienvenue Bootstrap -->
        <div class="card welcome-card">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0"><i class="fas fa-users"></i> Bienvenue dans Team Up</h2>
            </div>
            <div class="card-body">
                <p class="lead">Plateforme de collaboration et de gestion d'équipe</p>
                <hr>
                <p class="card-text">Team Up est votre solution complète pour la gestion d'équipes, la collaboration sur les tâches et le suivi des projets.</p>
                
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-users"></i> Utilisateurs</h5>
                                <p class="card-text">Gérez votre équipe et les profils utilisateurs</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-tasks"></i> Tâches</h5>
                                <p class="card-text">Organisez et assignez les tâches à votre équipe</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title"><i class="fas fa-calendar"></i> Agenda</h5>
                                <p class="card-text">Planifiez vos réunions et événements</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire de profil utilisateur avec choix de thème -->
        <div class="theme-form">
            <h4><i class="fas fa-cog"></i> Profil Utilisateur</h4>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="lst_theme">Thème :</label>
                    <select class="form-control" id="lst_theme" name="lst_theme" onchange="this.form.submit()">
                        <option value="0" <?php echo ($theme === "0" ? "selected" : ""); ?>>Thème (0)</option>
                        <option value="1" <?php echo ($theme === "1" ? "selected" : ""); ?>>Clair (1)</option>
                        <option value="2" <?php echo ($theme === "2" ? "selected" : ""); ?>>Foncé (2)</option>
                    </select>
                </div>
                <small class="form-text text-muted">Thème sélectionné : <?php 
                    switch($theme) {
                        case "0": echo "Thème par défaut"; break;
                        case "1": echo "Clair"; break;
                        case "2": echo "Foncé"; break;
                    }
                ?></small>
            </form>
            <?php if (isset($_COOKIE['user_profile'])): ?>
                <div class="alert alert-info mt-2">
                    <small>Cookie sauvegardé. Expiration dans 1 heure.</small>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- jQuery via CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap JS via CDN -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
