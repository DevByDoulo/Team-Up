<?php
/**
 * Page d'inscription pour la messagerie Team Up
 */

require_once 'config.php';
require_once 'service/userservice.php';
require_once 'models/userentity.php';

// Traitement du formulaire d'inscription
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = isset($_POST['name']) ? trim($_POST['name']) : '';
    $login = isset($_POST['login']) ? trim($_POST['login']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $password_confirm = isset($_POST['password_confirm']) ? trim($_POST['password_confirm']) : '';
    
    // Validation des champs
    if (empty($name) || empty($login) || empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } elseif (strlen($name) < 2) {
        $error = 'Le nom doit contenir au moins 2 caractères';
    } elseif (strlen($login) < 3) {
        $error = 'Le login doit contenir au moins 3 caractères';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Veuillez entrer une adresse email valide';
    } elseif (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères';
    } elseif ($password !== $password_confirm) {
        $error = 'Les mots de passe ne correspondent pas';
    } else {
        try {
            // Utiliser le service pour créer l'utilisateur
            $userService = new UserService();
            
            // Vérifier si le login existe déjà
            $existingUsers = $userService->getuserlist();
            foreach ($existingUsers as $user) {
                if ($user->utilisateur_login === $login) {
                    $error = 'Ce login est déjà utilisé';
                    break;
                }
                if ($user->utilisateur_email === $email) {
                    $error = 'Cet email est déjà utilisé';
                    break;
                }
            }
            
            if (empty($error)) {
                // Créer l'entité utilisateur
                $newUser = new UserEntity(
                    null, // ID sera auto-généré
                    $name,
                    $login,
                    $password, // Sera haché dans le DAO
                    $email,
                    date('Y-m-d H:i:s')
                );
                
                // Ajouter l'utilisateur
                $inserted_id = $userService->adduser($newUser);
                if ($inserted_id) {
                    // Créer la session pour l'utilisateur nouvellement inscrit
                    session_start();
                    $_SESSION['user_id'] = $inserted_id;
                    $_SESSION['user_name'] = $newUser->utilisateur_nom;
                    $_SESSION['user_login'] = $newUser->utilisateur_login;
                    $_SESSION['logged_in'] = true;
                    $_SESSION['login_time'] = time();
                    
                    $success = 'Inscription réussie ! Redirection vers la messagerie...';
                    
                    // Redirection automatique après 2 secondes vers la messagerie
                    header('refresh:2;url=messages.php');
                } else {
                    $error = 'Erreur lors de l\'inscription. Veuillez réessayer.';
                }
            }
        } catch (Exception $e) {
            $error = 'Erreur : ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Up - Inscription Messagerie</title>
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
            padding: 20px 0;
        }
        .register-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }
        .register-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .register-body {
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
        .btn-outline-secondary {
            border-color: #667eea;
            color: #667eea;
        }
        .btn-outline-secondary:hover {
            background: #667eea;
            border-color: #667eea;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h2><i class="fas fa-user-plus"></i> Team Up</h2>
            <p class="mb-0">Créer votre compte messagerie</p>
        </div>
        <div class="register-body">
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
                <div class="form-group mb-3">
                    <label for="name"><i class="fas fa-user"></i> Nom complet</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           placeholder="Entrez votre nom complet" required
                           value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                </div>

                <div class="form-group mb-3">
                    <label for="login"><i class="fas fa-id-badge"></i> Login</label>
                    <input type="text" class="form-control" id="login" name="login" 
                           placeholder="Choisissez un login (min 3 caractères)" required
                           value="<?php echo htmlspecialchars($_POST['login'] ?? ''); ?>">
                    <small class="text-muted">Ce login sera utilisé pour vous connecter</small>
                </div>

                <div class="form-group mb-3">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="Entrez votre adresse email" required
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>

                <div class="form-group mb-3">
                    <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Choisissez un mot de passe (min 6 caractères)" required>
                    <small class="text-muted">Doit contenir au moins 6 caractères</small>
                </div>

                <div class="form-group mb-4">
                    <label for="password_confirm"><i class="fas fa-lock"></i> Confirmer le mot de passe</label>
                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" 
                           placeholder="Confirmez votre mot de passe" required>
                </div>

                <button type="submit" class="btn btn-primary btn-block mb-3">
                    <i class="fas fa-user-plus"></i> S'inscrire
                </button>
            </form>

            <div class="text-center">
                <a href="login.php" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Retour à la connexion
                </a>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted">
                    <i class="fas fa-shield-alt"></i> 
                    Vos données sont sécurisées avec hachage des mots de passe
                </small>
            </div>
        </div>
    </div>

    <!-- Scripts Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
