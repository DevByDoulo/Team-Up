<?php
/**
 * Page Agenda - Liste des événements
 */

require_once __DIR__ . '/service/evenementservice.php';
require_once __DIR__ . '/service/userservice.php';

// Charger tous les événements via EvenementService::evenement_get_all()
$evenementService = new EvenementService();
$evenements = $evenementService->evenement_get_all();

// Charger le service utilisateur pour afficher les noms des organisateurs
$userService = new UserService();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Up - Agenda</title>
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

        .table-responsive {
            margin-top: 20px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, #343a40 0%, #23272b 100%);
            color: white;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.05);
            transform: scale(1.01);
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

        .btn-outline-primary {
            border: 2px solid #007bff;
            color: #007bff;
            background: transparent;
            border-radius: 20px;
            padding: 8px 16px;
        }

        .btn-outline-primary:hover {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.4);
        }

        .alert {
            border: none;
            border-radius: 10px;
            padding: 20px;
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

        h1, h2 {
            color: #2c3e50;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .date-fr {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: #495057;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
            margin-right: 10px;
            box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
            transition: all 0.3s ease;
        }

        .user-avatar:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.4);
        }

        .section-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .section-icon:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
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
            
            .user-avatar {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
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
                <i class="fas fa-calendar-alt mr-3"></i>Agenda
            </h1>
            <p class="lead text-info mb-0 font-weight-bold">
                Consultez et gérez tous les événements de la plateforme
            </p>
        </div>

        <div class="card mb-4">
            <div class="card-header">
                <h2 class="mb-0">
                    <i class="fas fa-calendar-week mr-2"></i>
                    Événements programmés
                </h2>
                <p class="mb-0 mt-2 opacity-75">Consultez, modifiez et suivez l'agenda des événements</p>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-list mr-2"></i>
                            Événements enregistrés
                        </h4>
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            <?php echo count($evenements); ?> événement(s) au total
                        </small>
                    </div>
                    <a href="addevenement.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus mr-2"></i> 
                        Nouveau rendez-vous
                    </a>
                </div>
            </div>
        </div>

        <?php if (empty($evenements)): ?>
            <div class="alert alert-info text-center py-5 fade-in">
                <div class="section-icon mx-auto mb-4" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h4 class="text-muted font-weight-bold">Aucun événement programmé</h4>
                <p class="text-muted mb-4">Aucun événement n'a été créé pour le moment.</p>
                <a href="addevenement.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus mr-2"></i>
                    Créer le premier événement
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive fade-in">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col" style="width: 80px;">
                                <i class="fas fa-hashtag mr-2"></i>ID
                            </th>
                            <th scope="col">
                                <i class="fas fa-file-alt mr-2"></i>Objet
                            </th>
                            <th scope="col">
                                <i class="fas fa-map-marker-alt mr-2"></i>Lieu
                            </th>
                            <th scope="col">
                                <i class="fas fa-play-circle mr-2"></i>Début
                            </th>
                            <th scope="col">
                                <i class="fas fa-stop-circle mr-2"></i>Fin
                            </th>
                            <th scope="col">
                                <i class="fas fa-user mr-2"></i>Organisateur
                            </th>
                            <th scope="col" style="width: 120px;">
                                <i class="fas fa-cogs mr-2"></i>Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($evenements as $evt): ?>
                            <?php
                            // Formater les dates en français : d/m/Y H:i
                            $date_debut = DateTime::createFromFormat('Y-m-d H:i:s', $evt->evenement_dtstart);
                            $date_fin = DateTime::createFromFormat('Y-m-d H:i:s', $evt->evenement_dtend);
                            $debut_fr = $date_debut ? $date_debut->format('d/m/Y H:i') : $evt->evenement_dtstart;
                            $fin_fr = $date_fin ? $date_fin->format('d/m/Y H:i') : $evt->evenement_dtend;
                            ?>
                            <tr>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar mr-2">
                                            <?php echo $evt->id_evenement; ?>
                                        </div>
                                        <strong class="text-primary">#<?php echo $evt->id_evenement; ?></strong>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <strong class="text-primary"><?php echo htmlspecialchars($evt->evenement_subject); ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-muted mr-2"></i>
                                        <span>
                                            <?php echo htmlspecialchars($evt->evenement_location ?: 'Non spécifié'); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock text-muted mr-2"></i>
                                        <div>
                                            <strong class="date-fr"><?php echo $debut_fr; ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock text-muted mr-2"></i>
                                        <div>
                                            <strong class="date-fr"><?php echo $fin_fr; ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar mr-2">
                                            <?php echo strtoupper(substr($evt->utilisateur_nom, 0, 1)); ?>
                                        </div>
                                        <strong><?php echo htmlspecialchars($evt->utilisateur_nom); ?></strong>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <a href="editevenement.php?id=<?php echo $evt->id_evenement; ?>" 
                                       class="btn btn-outline-primary btn-sm" title="Modifier l'événement">
                                        <i class="fas fa-edit mr-1"></i>
                                        Modifier
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle mr-2"></i>
                <strong>Total :</strong> <?php echo count($evenements); ?> événement(s) programmé(s)
            </div>
            <a href="addevenement.php" class="btn btn-outline-primary btn-lg">
                <i class="fas fa-plus mr-2"></i> 
                Ajouter un événement
            </a>
        </div>
    </div>

    <!-- Scripts Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
