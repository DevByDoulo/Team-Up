<?php
/**
 * Visualiseur des messages du chat dans MongoDB
 */

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/service/messageservice.php';
require_once __DIR__ . '/models/messageentity.php';

// Vérification de l'authentification
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Récupérer les messages depuis MongoDB
$messages = MessageService::getRecentMessages(50);
$messageCount = count($messages);

// Récupérer l'utilisateur connecté
$current_user = [
    'name' => $_SESSION['user_name'] ?? 'Utilisateur'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Up - Messages du Chat (MongoDB)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }
        .message-card {
            border-left: 4px solid #007bff;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }
        .message-card:hover {
            transform: translateX(5px);
        }
        .message-public {
            border-left-color: #28a745;
        }
        .message-private {
            border-left-color: #dc3545;
        }
        .message-meta {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .stats-card {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="text-center mb-4">
                    <i class="fas fa-database me-2"></i>
                    Messages du Chat - MongoDB
                </h1>
                
                <!-- Statistiques -->
                <div class="stats-card">
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h3><?php echo $messageCount; ?></h3>
                            <p class="mb-0">Messages totaux</p>
                        </div>
                        <div class="col-md-4">
                            <h3><?php echo count(array_filter($messages, fn($m) => empty($m->attendee))); ?></h3>
                            <p class="mb-0">Messages publics</p>
                        </div>
                        <div class="col-md-4">
                            <h3><?php echo count(array_filter($messages, fn($m) => !empty($m->attendee))); ?></h3>
                            <p class="mb-0">Messages privés</p>
                        </div>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <a href="messages.php" class="btn btn-primary">
                            <i class="fas fa-comments me-2"></i>Retour au chat
                        </a>
                        <button onclick="location.reload()" class="btn btn-outline-primary ms-2">
                            <i class="fas fa-sync me-2"></i>Actualiser
                        </button>
                    </div>
                    <div>
                        <button onclick="clearMessages()" class="btn btn-outline-danger">
                            <i class="fas fa-trash me-2"></i>Vider la collection
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        <div class="row">
            <div class="col-12">
                <?php if ($messageCount === 0): ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        Aucun message trouvé dans la base MongoDB. 
                        <a href="messages.php">Envoyez des messages</a> pour commencer.
                    </div>
                <?php else: ?>
                    <?php foreach (array_reverse($messages) as $message): ?>
                        <div class="card message-card <?php echo empty($message->attendee) ? 'message-public' : 'message-private'; ?>">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong class="text-primary">
                                            <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($message->from); ?>
                                        </strong>
                                        <?php if (!empty($message->attendee)): ?>
                                            <span class="badge bg-danger ms-2">
                                                <i class="fas fa-lock me-1"></i>Privé
                                            </span>
                                            <span class="text-muted ms-2">
                                                → <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($message->attendee); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success ms-2">
                                                <i class="fas fa-globe me-1"></i>Public
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="message-meta">
                                        <i class="fas fa-clock me-1"></i><?php echo date('d/m/Y H:i:s', strtotime($message->date)); ?>
                                    </small>
                                </div>
                                <div class="message-content">
                                    <?php echo htmlspecialchars($message->message); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function clearMessages() {
            if (confirm('Êtes-vous sûr de vouloir supprimer tous les messages ? Cette action est irréversible.')) {
                fetch('controllers/messagerie/index.php?action=clearmessages', {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Messages supprimés avec succès');
                        location.reload();
                    } else {
                        alert('Erreur lors de la suppression: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la suppression');
                });
            }
        }
    </script>
</body>
</html>
