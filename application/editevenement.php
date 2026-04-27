    <?php
/**
 * Page Modifier un événement
 */

require_once __DIR__ . '/service/evenementservice.php';
require_once __DIR__ . '/service/userservice.php';

$evenementService = new EvenementService();
$userService = new UserService();

// Récupérer id depuis $_GET['id']
$id_evenement = $_GET['id'] ?? 0;

// Charger tous les utilisateurs pour les listes déroulantes
$utilisateurs = $userService->getuserlist();

$errors = [];
$success = false;

// Charger événement via EvenementService::evenement_get_by_id($id)
$evenement = $evenementService->evenement_get_by_id($id_evenement);

if (!$evenement) {
    die('<div class="container mt-4"><div class="alert alert-danger">Événement non trouvé.</div></div>');
}

// Charger participants via EvenementService::evenement_get_participant($id)
$participants_existants = $evenementService->evenement_get_participant($id_evenement);
$ids_participants_existants = array_map(function($p) {
    return $p->id_utilisateur;
}, $participants_existants);

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
            $id_evenement,
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

        // Créer tableau de participants (inclure organisateur)
        $all_participants = array_unique(array_merge([$id_organisateur], $participants));

        // Si POST : appeler EvenementService::evenement_edit()
        $result = $evenementService->evenement_edit($ev, $all_participants);

        if ($result) {
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
    <title>Team Up - Modifier événement</title>
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
            max-width: 1200px;
            animation: fadeInUp 0.6s ease-out;
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

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            box-shadow: 0 2px 4px rgba(220, 53, 69, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.4);
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

        h1 {
            color: #2c3e50;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
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

    <div class="main-container p-4 fade-in">
        <div class="text-center mb-4">
            <h1 class="display-4 text-white mb-3">
                <i class="fas fa-edit mr-3"></i>Team Up - Modifier événement
            </h1>
            <p class="lead text-white-50">
                Modifiez les informations de votre événement
            </p>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success fade-in">
                <i class="fas fa-check-circle mr-2"></i> 
                <strong>Succès !</strong> L'événement a été modifié avec succès.
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
                    <div class="alert alert-danger fade-in">
                        <i class="fas fa-exclamation-circle mr-2"></i> 
                        <strong>Erreur :</strong> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-edit mr-2"></i>
                        Informations de l'événement
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="form-group">
                            <label for="evenement_subject" class="form-label">
                                <i class="fas fa-heading mr-2"></i>
                                Obet de l'événement <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="evenement_subject" name="evenement_subject" required
                                   placeholder="Entrez le nom de l'événement"
                                   value="<?php echo htmlspecialchars($_POST['evenement_subject'] ?? $evenement->evenement_subject); ?>">
                        </div>

                        <div class="form-group">
                            <label for="evenement_description" class="form-label">
                                <i class="fas fa-align-left mr-2"></i>
                                Description
                            </label>
                            <textarea class="form-control" id="evenement_description" name="evenement_description" rows="4"
                                      placeholder="Décrivez votre événement..."><?php
                                echo htmlspecialchars($_POST['evenement_description'] ?? $evenement->evenement_description ?? '');
                            ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="evenement_location" class="form-label">
                                <i class="fas fa-map-marker-alt mr-2"></i>
                                Lieu
                            </label>
                            <input type="text" class="form-control" id="evenement_location" name="evenement_location"
                                   placeholder="Entrez le lieu de l'événement"
                                   value="<?php echo htmlspecialchars($_POST['evenement_location'] ?? $evenement->evenement_location ?? ''); ?>">
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="evenement_dtstart" class="form-label">
                                    <i class="fas fa-calendar-plus mr-2"></i>
                                    Date et heure de début <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" class="form-control" id="evenement_dtstart" name="evenement_dtstart" required
                                       value="<?php
                                       $post_val = $_POST['evenement_dtstart'] ?? '';
                                       if ($post_val) {
                                           echo htmlspecialchars($post_val);
                                       } else {
                                           $dt = DateTime::createFromFormat('Y-m-d H:i:s', $evenement->evenement_dtstart);
                                           echo $dt ? $dt->format('Y-m-d\TH:i') : '';
                                       }
                                       ?>">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="evenement_dtend" class="form-label">
                                    <i class="fas fa-calendar-check mr-2"></i>
                                    Date et heure de fin <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" class="form-control" id="evenement_dtend" name="evenement_dtend" required
                                       value="<?php
                                       $post_val = $_POST['evenement_dtend'] ?? '';
                                       if ($post_val) {
                                           echo htmlspecialchars($post_val);
                                       } else {
                                           $dt = DateTime::createFromFormat('Y-m-d H:i:s', $evenement->evenement_dtend);
                                           echo $dt ? $dt->format('Y-m-d\TH:i') : '';
                                       }
                                       ?>">
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="id_utilisateur" class="form-label">
                                    <i class="fas fa-user-tie mr-2"></i>
                                    Organisateur <span class="text-danger">*</span>
                                </label>
                                <select class="form-control" id="id_utilisateur" name="id_utilisateur" required>
                                    <option value="">-- Choisir un organisateur --</option>
                                    <?php
                                    $current_org_post = $_POST['id_utilisateur'] ?? $evenement->id_utilisateur;
                                    foreach ($utilisateurs as $utilisateur): ?>
                                        <option value="<?php echo $utilisateur->id_utilisateur; ?>"
                                            <?php echo ($current_org_post == $utilisateur->id_utilisateur) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($utilisateur->utilisateur_nom); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="participants" class="form-label">
                                    <i class="fas fa-users mr-2"></i>
                                    Participants
                                </label>
                                <select class="form-control" id="participants" name="participants[]" multiple size="4">
                                    <?php
                                    $current_parts_post = $_POST['participants'] ?? $ids_participants_existants;
                                    foreach ($utilisateurs as $utilisateur):
                                        // Ne pas inclure l'organisateur dans les participants (déjà sélectionné implicitement)
                                        $selected = in_array($utilisateur->id_utilisateur, $current_parts_post) ? 'selected' : '';
                                        ?>
                                        <option value="<?php echo $utilisateur->id_utilisateur; ?>" <?php echo $selected; ?>>
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

                        <div class="form-group text-center mt-4">
                            <button type="submit" class="btn btn-primary btn-lg mr-3">
                                <i class="fas fa-save mr-2"></i> 
                                Enregistrer les modifications
                            </button>
                            <a href="agenda.php" class="btn btn-danger btn-lg">
                                <i class="fas fa-times mr-2"></i> 
                                Annuler
                            </a>
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
