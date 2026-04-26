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
        body {
            padding-top: 56px;
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .required:after {
            content: " *";
            color: #dc3545;
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

    <div class="container">
        <div class="form-container">
            <h2 class="text-center mb-4"><i class="fas fa-plus-circle"></i> Nouvelle demande</h2>
            
            <?php if (!empty($message)): ?>
                <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="demande_objet" class="required">Objet de la demande</label>
                    <input type="text" class="form-control" id="demande_objet" name="demande_objet" 
                           placeholder="Entrez l'objet de la demande" required
                           value="<?php echo isset($_POST['demande_objet']) ? htmlspecialchars($_POST['demande_objet']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="demande_texte">Description</label>
                    <textarea class="form-control" id="demande_texte" name="demande_texte" 
                              rows="4" placeholder="Détaillez votre demande..."><?php echo isset($_POST['demande_texte']) ? htmlspecialchars($_POST['demande_texte']) : ''; ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="demande_date_creation" class="required">Date de création</label>
                            <input type="date" class="form-control" id="demande_date_creation" name="demande_date_creation" 
                                   value="<?php echo isset($_POST['demande_date_creation']) ? htmlspecialchars($_POST['demande_date_creation']) : date('Y-m-d'); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="demande_date_echeance" class="required">Date d'échéance</label>
                            <input type="date" class="form-control" id="demande_date_echeance" name="demande_date_echeance" 
                                   value="<?php echo isset($_POST['demande_date_echeance']) ? htmlspecialchars($_POST['demande_date_echeance']) : ''; ?>" required>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="id_type_demande" class="required">Type de demande</label>
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
                            <label for="id_utilisateur">Assignée à</label>
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

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="demandes.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à la liste
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer la demande
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- jQuery et Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
