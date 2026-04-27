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
            max-width: 800px;
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

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .btn {
            border-radius: 25px;
            padding: 12px 24px;
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

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            box-shadow: 0 2px 4px rgba(108, 117, 125, 0.3);
        }

        .btn-secondary:hover {
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

    <div class="main-container fade-in">
        <div class="text-center mb-4">
            <h1 class="display-3 text-warning mb-3 font-weight-bold">
                <i class="fas fa-user-plus mr-3"></i>Ajouter un Utilisateur
            </h1>
            <p class="lead text-info mb-0 font-weight-bold">
                Créez un nouveau compte utilisateur pour la plateforme
            </p>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">
                    <i class="fas fa-user-cog mr-2"></i>
                    Formulaire d'inscription
                </h2>
                <p class="mb-0 mt-2 opacity-75">Remplissez les informations ci-dessous pour créer un nouvel utilisateur</p>
            </div>
            <div class="card-body">
                <!-- Message de succès ou d'erreur -->
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show fade-in" role="alert">
                        <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?> mr-2"></i>
                        <strong><?php echo $message_type === 'success' ? 'Succès !' : 'Erreur !'; ?></strong> <?php echo $message; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <!-- Formulaire d'ajout -->
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="utilisateur_nom" class="form-label">
                            <i class="fas fa-user mr-2"></i>
                            Nom complet <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="utilisateur_nom" name="utilisateur_nom" 
                               placeholder="Entrez le nom complet de l'utilisateur" required>
                    </div>

                    <div class="form-group">
                        <label for="utilisateur_login" class="form-label">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" id="utilisateur_login" name="utilisateur_login" 
                               placeholder="Choisissez un identifiant de connexion" required>
                    </div>

                    <div class="form-group">
                        <label for="utilisateur_pwd" class="form-label">
                            <i class="fas fa-lock mr-2"></i>
                            Mot de passe <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control" id="utilisateur_pwd" name="utilisateur_pwd" 
                               placeholder="Entrez un mot de passe sécurisé" required>
                    </div>

                    <div class="form-group">
                        <label for="utilisateur_email" class="form-label">
                            <i class="fas fa-envelope mr-2"></i>
                            Email <span class="text-danger">*</span>
                        </label>
                        <input type="email" class="form-control" id="utilisateur_email" name="utilisateur_email" 
                               placeholder="Entrez l'adresse email" required>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Information :</strong> Les champs marqués d'une astérisque (*) sont obligatoires.
                    </div>

                    <div class="d-flex justify-content-between mt-4">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save mr-2"></i>
                            Ajouter l'utilisateur
                        </button>
                        <a href="utilisateurs.php" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Retour à la liste
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- jQuery et Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
