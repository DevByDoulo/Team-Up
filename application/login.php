<?php
/**
 * Page de login pour la messagerie Team Up
 */

require_once 'config.php';
require_once 'service/userservice.php';

// Traitement du formulaire de login
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    
    if (empty($login) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        // Utiliser le service pour authentifier l'utilisateur
        $userService = new UserService();
        $user = $userService->authenticateUser($login, $password);
        
        if ($user) {
            // Authentification réussie - créer la session
            session_start();
            $_SESSION['user_id'] = $user->id_utilisateur;
            $_SESSION['user_name'] = $user->utilisateur_nom;
            $_SESSION['user_login'] = $user->utilisateur_login;
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            // Redirection vers la messagerie
            header('Location: messages.php');
            exit();
        } else {
            $error = 'Login ou mot de passe incorrect';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Up - Connexion Messagerie</title>
    <!-- Bootstrap 4 via CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-body {
            padding: 40px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2><i class="fas fa-comments"></i> Team Up</h2>
            <p class="mb-0">Messagerie d'équipe</p>
        </div>
        <div class="login-body">
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group mb-4">
                    <label for="login"><i class="fas fa-user"></i> Login</label>
                    <input type="text" class="form-control" id="login" name="login" 
                           placeholder="Entrez votre login" required
                           value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>">
                </div>

                <div class="form-group mb-4">
                    <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Entrez votre mot de passe" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Se connecter
                </button>
            </form>

            <div class="text-center mt-4">
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> 
                    Utilisez les identifiants des utilisateurs enregistrés
                </small>
            </div>

            <div class="text-center mt-3">
                <div class="mb-2">
                    <span class="text-muted">Pas encore de compte ?</span>
                    <a href="register.php" class="text-primary font-weight-bold">
                        <i class="fas fa-user-plus"></i> S'inscrire
                    </a>
                </div>
                <a href="index.php" class="text-muted">
                    <i class="fas fa-arrow-left"></i> Retour à l'accueil
                </a>
            </div>
        </div>
    </div>

    <!-- Scripts Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
