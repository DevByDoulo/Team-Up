<?php
/**
 * Page d'ajout d'utilisateur
 */

require_once 'config.php';
require_once 'models/userentity.php';
require_once 'service/userservice.php';

// Initialisation du service
$userService = new UserService();
$message = '';
$message_type = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération des données du formulaire
    $nom = isset($_POST['utilisateur_nom']) ? trim($_POST['utilisateur_nom']) : '';
    $login = isset($_POST['utilisateur_login']) ? trim($_POST['utilisateur_login']) : '';
    $pwd = isset($_POST['utilisateur_pwd']) ? trim($_POST['utilisateur_pwd']) : '';
    $email = isset($_POST['utilisateur_email']) ? trim($_POST['utilisateur_email']) : '';
    
    // Validation des champs
    if (empty($nom) || empty($login) || empty($pwd) || empty($email)) {
        $message = 'Tous les champs sont obligatoires';
        $message_type = 'danger';
    } else {
        // Création de l'entité utilisateur
        $user = new UserEntity(
            null,
            $nom,
            $login,
            $pwd,
            $email,
            date('Y-m-d H:i:s')
        );
        
        // Ajout via le service
        if ($userService->adduser($user)) {
            $message = 'Utilisateur ajouté avec succès';
            $message_type = 'success';
        } else {
            $message = 'Erreur lors de l\'ajout de l\'utilisateur';
            $message_type = 'danger';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Up - Ajouter un utilisateur</title>
    <!-- Bootstrap 4 via CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            padding-top: 56px;
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
                <li class="nav-item">
                    <a class="nav-link" href="equipes.php">Équipes</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-4"><i class="fas fa-user-plus"></i> Ajouter un utilisateur</h2>
            
            <!-- Message de succès ou d'erreur -->
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <!-- Formulaire d'ajout -->
            <form method="POST" action="">
                <div class="form-group">
                    <label for="utilisateur_nom"><i class="fas fa-user"></i> Nom complet *</label>
                    <input type="text" class="form-control" id="utilisateur_nom" name="utilisateur_nom" 
                           placeholder="Nom de l'utilisateur" required>
                </div>

                <div class="form-group">
                    <label for="utilisateur_login"><i class="fas fa-sign-in-alt"></i> Login *</label>
                    <input type="text" class="form-control" id="utilisateur_login" name="utilisateur_login" 
                           placeholder="Identifiant de connexion" required>
                </div>

                <div class="form-group">
                    <label for="utilisateur_pwd"><i class="fas fa-lock"></i> Mot de passe *</label>
                    <input type="password" class="form-control" id="utilisateur_pwd" name="utilisateur_pwd" 
                           placeholder="Mot de passe" required>
                </div>

                <div class="form-group">
                    <label for="utilisateur_email"><i class="fas fa-envelope"></i> Email *</label>
                    <input type="email" class="form-control" id="utilisateur_email" name="utilisateur_email" 
                           placeholder="Adresse email" required>
                </div>

                <div class="form-group">
                    <small class="text-muted">* Champs obligatoires</small>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Ajouter l'utilisateur
                    </button>
                    <a href="utilisateurs.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- jQuery et Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
