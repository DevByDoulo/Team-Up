<?php
/**
 * Page Ajouter un événement
 */

require_once __DIR__ . '/service/evenementservice.php';
require_once __DIR__ . '/service/userservice.php';

$evenementService = new EvenementService();
$userService = new UserService();

$errors = [];
$success = false;

// Charger tous les utilisateurs pour les listes déroulantes
$utilisateurs = $userService->getuserlist();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation : subject et dates obligatoires
    $subject = trim($_POST['evenement_subject'] ?? '');
    $description = trim($_POST['evenement_description'] ?? '');
    $location = trim($_POST['evenement_location'] ?? '');
    $dtstart = trim($_POST['evenement_dtstart'] ?? '');
    $dtend = trim($_POST['evenement_dtend'] ?? '');
    $id_organisateur = $_POST['id_utilisateur'] ?? null;
    $participants = $_POST['participants'] ?? [];

    if (empty($subject)) {
        $errors[] = 'Le sujet de l\'événement est obligatoire.';
    }

    if (empty($dtstart)) {
        $errors[] = 'La date et l\'heure de début sont obligatoires.';
    }

    if (empty($dtend)) {
        $errors[] = 'La date et l\'heure de fin sont obligatoires.';
    }

    if (!empty($dtstart) && !empty($dtend)) {
        $start_dt = DateTime::createFromFormat('Y-m-d\TH:i', $dtstart);
        $end_dt = DateTime::createFromFormat('Y-m-d\TH:i', $dtend);
        if ($start_dt && $end_dt) {
            $start_mysql = $start_dt->format('Y-m-d H:i:s');
            $end_mysql = $end_dt->format('Y-m-d H:i:s');
            if ($start_dt >= $end_dt) {
                $errors[] = 'La date de début doit être antérieure à la date de fin.';
            }
        } else {
            $errors[] = 'Format de date invalide.';
        }
    } else {
        $start_mysql = null;
        $end_mysql = null;
    }

    if (empty($id_organisateur)) {
        $errors[] = 'Veuillez sélectionner un organisateur.';
    }

    if (empty($errors)) {
        // Créer EvenementEntity
        $ev = new EvenementEntity(
            null,
            $subject,
            $description ?: null,
            $location ?: null,
            $start_mysql,
            $end_mysql,
            null,
            null,
            (int)$id_organisateur,
            null
        );

        // Créer tableau de ParticipantEntity depuis participants[]
        // On inclut toujours l'organisateur comme participant
        $all_participants = array_unique(array_merge([$id_organisateur], $participants));

        // Appeler EvenementService::evenement_add()
        $id = $evenementService->evenement_add($ev, $all_participants);

        if ($id > 0) {
            $success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Up - Nouveau rendez-vous</title>
    <!-- Bootstrap 4 via CDN -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome pour les icônes -->
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

        h1, h2 {
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
    <?php include_once __DIR__ . '/phpinclude/navbar.php'; ?>

    <div class="main-container fade-in">
        <div class="text-center mb-4">
            <h1 class="display-3 text-warning mb-3 font-weight-bold">
                <i class="fas fa-calendar-plus mr-3"></i>Nouveau Rendez-vous
            </h1>
            <p class="lead text-info mb-0 font-weight-bold">
                Créez un nouvel événement pour la plateforme
            </p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show fade-in" role="alert">
                <i class="fas fa-check-circle mr-2"></i>
                <strong>Succès !</strong> L'événement a été créé avec succès.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="text-center mt-4">
                <a href="agenda.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-calendar-alt mr-2"></i> 
                    Retour à l'agenda
                </a>
            </div>
        <?php else: ?>
            <?php if (!empty($errors)):
                foreach ($errors as $error): ?>
                    <div class="alert alert-danger alert-dismissible fade show fade-in" role="alert">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <strong>Erreur !</strong> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endforeach;
            endif; ?>

            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">
                        <i class="fas fa-calendar-plus mr-2"></i>
                        Formulaire d'événement
                    </h2>
                    <p class="mb-0 mt-2 opacity-75">Remplissez les informations ci-dessous pour créer un nouvel événement</p>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="evenement_subject" class="form-label required">
                                <i class="fas fa-file-alt mr-2"></i>
                                Objet de l'événement
                            </label>
                            <input type="text" class="form-control" id="evenement_subject" name="evenement_subject" required
                                   placeholder="Entrez l'objet de l'événement"
                                   value="<?php echo htmlspecialchars($_POST['evenement_subject'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <label for="evenement_description" class="form-label">
                                <i class="fas fa-align-left mr-2"></i>
                                Description
                            </label>
                            <textarea class="form-control" id="evenement_description" name="evenement_description" rows="4"
                                      placeholder="Décrivez l'événement..."><?php
                                echo htmlspecialchars($_POST['evenement_description'] ?? '');
                            ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="evenement_location" class="form-label">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                Lieu
                            </label>
                            <input type="text" class="form-control" id="evenement_location" name="evenement_location"
                                   placeholder="Entrez le lieu de l'événement"
                                   value="<?php echo htmlspecialchars($_POST['evenement_location'] ?? ''); ?>">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="evenement_dtstart" class="form-label required">
                                        <i class="fas fa-play-circle mr-2"></i>
                                        Date et heure de début
                                    </label>
                                    <input type="datetime-local" class="form-control" id="evenement_dtstart" name="evenement_dtstart" required
                                           value="<?php echo htmlspecialchars($_POST['evenement_dtstart'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="evenement_dtend" class="form-label required">
                                        <i class="fas fa-stop-circle mr-2"></i>
                                        Date et heure de fin
                                    </label>
                                    <input type="datetime-local" class="form-control" id="evenement_dtend" name="evenement_dtend" required
                                           value="<?php echo htmlspecialchars($_POST['evenement_dtend'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_utilisateur" class="form-label required">
                                        <i class="fas fa-user mr-2"></i>
                                        Organisateur
                                    </label>
                                    <select class="form-control" id="id_utilisateur" name="id_utilisateur" required>
                                        <option value="">-- Choisir un organisateur --</option>
                                        <?php foreach ($utilisateurs as $utilisateur): ?>
                                            <option value="<?php echo $utilisateur->id_utilisateur; ?>"
                                                <?php echo (isset($_POST['id_utilisateur']) && $_POST['id_utilisateur'] == $utilisateur->id_utilisateur) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($utilisateur->utilisateur_nom); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="participants" class="form-label">
                                        <i class="fas fa-users mr-2"></i>
                                        Participants
                                    </label>
                                    <select class="form-control" id="participants" name="participants[]" multiple size="4">
                                        <?php
                                        $selected_participants = $_POST['participants'] ?? [];
                                        foreach ($utilisateurs as $utilisateur): ?>
                                            <option value="<?php echo $utilisateur->id_utilisateur; ?>"
                                                <?php echo (in_array($utilisateur->id_utilisateur, $selected_participants) ? 'selected' : ''); ?>>
                                                <?php echo htmlspecialchars($utilisateur->utilisateur_nom); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Maintenez Ctrl (ou Cmd) pour sélectionner plusieurs participants.
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Information :</strong> Les champs marqués d'une astérisque (*) sont obligatoires.
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="agenda.php" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times mr-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save mr-2"></i>
                                Créer l'événement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Scripts Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
