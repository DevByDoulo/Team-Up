<?php
/**
 * Page d'ajout d'une demande
 */

require_once 'config.php';
require_once 'service/demandeservice.php';
require_once 'service/typedemandeservice.php';
require_once 'service/userservice.php';

// Initialisation des services
$demandeService = new DemandeService();
$typeDemandeService = new TypeDemandeService();
$userService = new UserService();

$message = '';
$message_type = '';

// Récupérer les listes pour les sélecteurs
$types = $typeDemandeService->gettypedemandelist();
$utilisateurs = $userService->getuserlist();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et validation des données
    $objet = trim($_POST['demande_objet'] ?? '');
    $texte = trim($_POST['demande_texte'] ?? '');
    $date_creation = trim($_POST['demande_date_creation'] ?? '');
    $date_echeance = trim($_POST['demande_date_echeance'] ?? '');
    $id_type = isset($_POST['id_type_demande']) ? (int)$_POST['id_type_demande'] : 0;
    $id_utilisateur = isset($_POST['id_utilisateur']) && $_POST['id_utilisateur'] !== '' ? (int)$_POST['id_utilisateur'] : null;
    
    // Validation
    $errors = array();
    
    if (empty($objet)) {
        $errors[] = 'L\'objet de la demande est obligatoire';
    }
    
    if (empty($date_creation)) {
        $errors[] = 'La date de création est obligatoire';
    }
    
    if (empty($date_echeance)) {
        $errors[] = 'La date d\'échéance est obligatoire';
    }
    
    if ($id_type <= 0) {
        $errors[] = 'Le type de demande est obligatoire';
    }
    
    // Vérifier les dates
    if (!empty($date_creation) && !validateDate($date_creation)) {
        $errors[] = 'La date de création n\'est pas valide';
    }
    
    if (!empty($date_echeance) && !validateDate($date_echeance)) {
        $errors[] = 'La date d\'échéance n\'est pas valide';
    }
    
    // Vérifier si l'échéance est après la création
    if (!empty($date_creation) && !empty($date_echeance) && $date_echeance < $date_creation) {
        $errors[] = 'La date d\'échéance doit être supérieure ou égale à la date de création';
    }
    
    if (empty($errors)) {
        // Création de l'entité demande
        $demande = new demandeEntity(
            null,
            $objet,
            $texte,
            $date_creation,
            $date_echeance,
            $id_type,
            $id_utilisateur
        );
        
        // Ajout via le service
        if ($demandeService->adddemande($demande)) {
            $message = 'Demande créée avec succès';
            $message_type = 'success';
            
            // Redirection après 2 secondes
            header('Refresh: 2; URL=demandes.php');
        } else {
            $errors[] = 'Erreur lors de la création de la demande';
        }
    }
    
    if (!empty($errors)) {
        $message = implode('<br>', $errors);
        $message_type = 'danger';
    }
}

// Fonction de validation de date
function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Up - Nouvelle demande</title>
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
            max-width: 900px;
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

        .required:after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
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
        <a class="navbar-brand" href="#"><i class="fas fa-tasks"></i> Team Up</a>
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
                <li class="nav-item">
                    <a class="nav-link" href="demandes.php">Demandes</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="main-container fade-in">
        <div class="text-center mb-4">
            <h1 class="display-3 text-warning mb-3 font-weight-bold">
                <i class="fas fa-plus-circle mr-3"></i>Nouvelle Demande
            </h1>
            <p class="lead text-info mb-0 font-weight-bold">
                Créez une nouvelle demande pour la plateforme
            </p>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="mb-0">
                    <i class="fas fa-clipboard-list mr-2"></i>
                    Formulaire de demande
                </h2>
                <p class="mb-0 mt-2 opacity-75">Remplissez les informations ci-dessous pour créer une nouvelle demande</p>
            </div>
            <div class="card-body">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show fade-in" role="alert">
                        <i class="fas fa-<?php echo $message_type === 'success' ? 'check-circle' : 'exclamation-circle'; ?> mr-2"></i>
                        <strong><?php echo $message_type === 'success' ? 'Succès !' : 'Erreur !'; ?></strong> <?php echo $message; ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="demande_objet" class="form-label required">
                            <i class="fas fa-file-alt mr-2"></i>
                            Objet de la demande
                        </label>
                        <input type="text" class="form-control" id="demande_objet" name="demande_objet" 
                               placeholder="Entrez l'objet de la demande" required
                               value="<?php echo isset($_POST['demande_objet']) ? htmlspecialchars($_POST['demande_objet']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="demande_texte" class="form-label">
                            <i class="fas fa-align-left mr-2"></i>
                            Description
                        </label>
                        <textarea class="form-control" id="demande_texte" name="demande_texte" 
                                  rows="4" placeholder="Détaillez votre demande..."><?php echo isset($_POST['demande_texte']) ? htmlspecialchars($_POST['demande_texte']) : ''; ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="demande_date_creation" class="form-label required">
                                    <i class="fas fa-calendar-plus mr-2"></i>
                                    Date de création
                                </label>
                                <input type="date" class="form-control" id="demande_date_creation" name="demande_date_creation" 
                                       value="<?php echo isset($_POST['demande_date_creation']) ? htmlspecialchars($_POST['demande_date_creation']) : date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="demande_date_echeance" class="form-label required">
                                    <i class="fas fa-calendar-check mr-2"></i>
                                    Date d'échéance
                                </label>
                                <input type="date" class="form-control" id="demande_date_echeance" name="demande_date_echeance" 
                                       value="<?php echo isset($_POST['demande_date_echeance']) ? htmlspecialchars($_POST['demande_date_echeance']) : ''; ?>" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_type_demande" class="form-label required">
                                    <i class="fas fa-tag mr-2"></i>
                                    Type de demande
                                </label>
                                <select class="form-control" id="id_type_demande" name="id_type_demande" required>
                                    <option value="">-- Sélectionnez un type --</option>
                                    <?php foreach ($types as $type): ?>
                                        <option value="<?php echo $type->id_type_demande; ?>"
                                            <?php echo (isset($_POST['id_type_demande']) && $_POST['id_type_demande'] == $type->id_type_demande) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($type->type_demande_label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="id_utilisateur" class="form-label">
                                    <i class="fas fa-user mr-2"></i>
                                    Assignée à
                                </label>
                                <select class="form-control" id="id_utilisateur" name="id_utilisateur">
                                    <option value="">-- Non assignée --</option>
                                    <?php foreach ($utilisateurs as $user): ?>
                                        <option value="<?php echo $user->id_utilisateur; ?>"
                                            <?php echo (isset($_POST['id_utilisateur']) && $_POST['id_utilisateur'] == $user->id_utilisateur) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($user->utilisateur_nom); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Information :</strong> Les champs marqués d'une astérisque (*) sont obligatoires.
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4">
                        <a href="demandes.php" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Retour à la liste
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-save mr-2"></i>
                            Créer la demande
                        </button>
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
