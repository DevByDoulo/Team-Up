<?php
/**
 * Page Messagerie - Chat en temps réel
 */

// Le menu.json est chargé dynamiquement dans les vues, pas ici
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/service/userservice.php';

// Vérification de l'authentification
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Rediriger vers la page de login
    header('Location: login.php');
    exit();
}

// Récupérer les informations de l'utilisateur connecté
$current_user = [
    'id' => $_SESSION['user_id'],
    'name' => $_SESSION['user_name'],
    'login' => $_SESSION['user_login']
];

// Charger la liste des utilisateurs via UserService::getuserlist()
$userService = new UserService();
$utilisateurs = $userService->getuserlist();

// Debug : vérifier si des utilisateurs sont chargés
if (empty($utilisateurs)) {
    echo "<!-- DEBUG: Aucun utilisateur trouvé dans la base de données -->";
} else {
    echo "<!-- DEBUG: " . count($utilisateurs) . " utilisateur(s) trouvé(s) -->";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Up - Messagerie</title>
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
        /* Zone d'affichage des messages : hauteur fixe avec scroll, bordure Bootstrap */
        #messagethread {
            height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            padding: 10px;
            background-color: #f8f9fa;
            margin-bottom: 15px;
        }
        .message-bubble {
            padding: 12px 16px;
            margin-bottom: 12px;
            border-radius: 18px;
            max-width: 75%;
            word-wrap: break-word;
            clear: both;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            position: relative;
        }
        
        .message-bubble:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        
        .message-sender-1 {
            /* Mes messages : fond gris moderne, float left */
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-left: 4px solid #6c757d;
            float: left;
            clear: both;
        }
        
        .message-sender-2 {
            /* Messages reçus (général) : fond vert moderne, float right */
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            border-left: 4px solid #28a745;
            float: right;
        }
        
        .message-sender-3 {
            /* Messages privés reçus : fond bleu moderne, float right */
            background: linear-gradient(135deg, #cce5ff 0%, #b8daff 100%);
            border-left: 4px solid #007bff;
            float: right;
        }
        
        .message-sender-4 {
            /* Messages en attente : fond jaune moderne */
            background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
            border-left: 4px solid #ffc107;
            float: left;
            clear: both;
        }
        
        .message-sender-5 {
            /* Info système (join/quit) : fond moderne, italique, centré */
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #495057;
            font-style: italic;
            text-align: center;
            max-width: 80%;
            clear: both;
            margin: 10px auto;
            border-radius: 25px;
            border: 1px dashed #dee2e6;
            padding: 8px 16px;
        }
        
        .message-sender-5::before {
            content: "📢";
            margin-right: 8px;
        }
        #message {
            resize: vertical;
            min-height: 60px;
        }
        .status-indicator {
            margin-left: 10px;
            font-size: 0.85em;
        }
        #hasmessage {
            color: #dc3545;
            font-weight: bold;
            margin-left: 5px;
        }
        .status-online { 
            color: #28a745; 
            font-weight: bold;
            text-shadow: 0 1px 2px rgba(40, 167, 69, 0.3);
        }
        
        .status-donotdisturb { 
            color: #dc3545; 
            font-weight: bold;
            text-shadow: 0 1px 2px rgba(220, 53, 69, 0.3);
        }
        
        .status-offline { 
            color: #6c757d; 
            font-weight: bold;
        }
        
        /* Améliorations des cartes */
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
        
        /* Améliorations des formulaires */
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
        
        /* Améliorations des boutons */
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
        
        .btn-outline-danger {
            border: 2px solid #dc3545;
            color: #dc3545;
        }
        
        .btn-outline-danger:hover {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
        }
        
        /* Améliorations des alerts */
        .alert {
            border: none;
            border-radius: 10px;
            padding: 15px 20px;
        }
        
        .alert-info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        /* Amélioration du titre */
        h1 {
            color: #2c3e50;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Scrollbar moderne */
        #messagethread::-webkit-scrollbar {
            width: 8px;
        }
        
        #messagethread::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        #messagethread::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-radius: 10px;
        }
        
        #messagethread::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #0056b3 0%, #007bff 100%);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include_once __DIR__ . '/phpinclude/navbar.php'; ?>

    <div class="main-container fade-in">
        <div class="text-center mb-4">
            <h1 class="display-3 text-warning mb-3 font-weight-bold">
                <i class="fas fa-comments mr-3"></i>Messagerie
            </h1>
            <p class="lead text-info mb-0 font-weight-bold">
                Discutez en temps réel avec votre équipe
            </p>
        </div>

        <div class="alert alert-info fade-in">
            <i class="fas fa-info-circle mr-2"></i> 
            <strong>Information :</strong> Le serveur de messagerie doit être démarré sur <strong>localhost:8080</strong> pour que le chat fonctionne.
        </div>

        <!-- Zone d'affichage des messages -->
        <div id="messagethread"></div>

        <!-- Informations utilisateur connecté -->
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="mb-0">
                    <i class="fas fa-user-circle mr-2"></i>
                    Mon profil
                </h2>
                <p class="mb-0 mt-2 opacity-75">Informations et statut de connexion</p>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">
                            <i class="fas fa-user-circle text-primary mr-2"></i> 
                            Connecté en tant que : <strong><?php echo htmlspecialchars($current_user['name']); ?></strong>
                        </h4>
                        <small class="text-muted">Login : <?php echo htmlspecialchars($current_user['login']); ?></small>
                    </div>
                    <div>
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt mr-2"></i> Déconnexion
                        </a>
                    </div>
                </div>
                
                <!-- Sélecteur de statut -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        <label for="statusSelector" class="form-label">
                            <i class="fas fa-circle"></i> Mon statut :
                        </label>
                        <select class="form-control form-control-sm" id="statusSelector">
                            <option value="online">🟢 En ligne</option>
                            <option value="donotdisturb">🔴 Ne pas déranger</option>
                            <option value="offline">⚫ Déconnecté</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="mt-4">
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Votre statut est visible par les autres utilisateurs
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-success mt-3 mb-0">
                    <small><i class="fas fa-shield-alt"></i> Vous êtes authentifié avec votre compte utilisateur de la base de données.</small>
                </div>
            </div>
        </div>

        <!-- Ligne de configuration du chat -->
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="mb-0">
                    <i class="fas fa-cogs mr-2"></i>
                    Configuration du chat
                </h2>
                <p class="mb-0 mt-2 opacity-75">Choisissez le type de discussion et vos préférences</p>
            </div>
            <div class="card-body">
                <!-- Type de discussion -->
                <div class="form-group mb-3">
                    <label class="form-label font-weight-bold">
                        <i class="fas fa-comments mr-2"></i> Type de discussion
                    </label>
                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                        <label class="btn btn-outline-primary active">
                            <input type="radio" name="chatType" id="chkAll" checked autocomplete="off">
                            <i class="fas fa-users"></i> Discussion générale
                        </label>
                        <label class="btn btn-outline-primary">
                            <input type="radio" name="chatType" id="chkPrivate" autocomplete="off">
                            <i class="fas fa-user-lock"></i> Discussion privée
                        </label>
                    </div>
                </div>
                
                <!-- Sélection utilisateur (masqué en mode général, visible en mode privé) -->
                <div id="privateChatSection" style="display: none;">
                    <div class="card border-info">
                        <div class="card-body bg-light">
                            <div class="row">
                                <div class="col-md-8">
                                    <label for="id_utilisateur" class="form-label">
                                        <i class="fas fa-user"></i> Choisir un destinataire
                                    </label>
                                    <select class="form-control" id="id_utilisateur" disabled>
                                        <option value="">-- Choisir un utilisateur --</option>
                                        <?php foreach ($utilisateurs as $utilisateur): ?>
                                            <option value="<?php echo $utilisateur->id_utilisateur; ?>">
                                                <?php echo htmlspecialchars($utilisateur->utilisateur_nom); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">
                                        <i class="fas fa-signal"></i> Statut en ligne
                                    </label>
                                    <div class="mt-2">
                                        <div id="userstatus" class="d-inline-block px-3 py-2 rounded-pill bg-light text-muted border">
                                            <i class="fas fa-circle text-muted mr-2"></i>
                                            <span>--</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Saisie du message -->
        <div class="card mb-3">
            <div class="card-header">
                <h2 class="mb-0">
                    <i class="fas fa-edit mr-2"></i>
                    Nouveau message
                </h2>
                <p class="mb-0 mt-2 opacity-75">Rédigez et envoyez votre message</p>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <textarea class="form-control" id="message" placeholder="Votre message..." rows="3"></textarea>
                </div>
                <div class="form-group">
                    <button id="btnSend" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane mr-2"></i> Envoyer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Socket.io client -->
    <script src="http://localhost:8080/socket.io/socket.io.js"></script>

    <script>
        // Variables JavaScript
        var displayname = '<?php echo addslashes($current_user['name']); ?>';  // Nom de l'utilisateur connecté
        var socket = null;
        var currentAttendee = null;

        // Fonction d'ajout de message dans l'interface
        // sender=1 : fond gris, float left (mes messages)
        // sender=2 : fond vert, float right (messages reçus général)
        // sender=3 : fond bleu clair, float right (messages privés reçus)
        // sender=4 : fond jaune (messages en attente)
        // sender=5 : info système (join/quit)
        function addmessage(displayname, message, sender) {
            var thread = document.getElementById('messagethread');
            var div = document.createElement('div');
            var badge = '';

            if (sender === 5) {
                div.className = 'message-bubble message-sender-5';
                div.innerHTML = message;
            } else {
                if (sender === 1) {
                    badge = '<span class="badge badge-secondary">Moi</span> ';
                    div.className = 'message-bubble message-sender-1';
                } else if (sender === 2) {
                    badge = '<span class="badge badge-success">Public</span> ';
                    div.className = 'message-bubble message-sender-2';
                } else if (sender === 3) {
                    badge = '<span class="badge badge-primary">Privé</span> ';
                    div.className = 'message-bubble message-sender-3';
                } else if (sender === 4) {
                    badge = '<span class="badge badge-warning">En attente</span> ';
                    div.className = 'message-bubble message-sender-4';
                }
                div.innerHTML = '<strong>' + badge + displayname + '</strong><br>' + message;
            }

            thread.appendChild(div);
            // Scroll automatique vers le bas
            thread.scrollTop = thread.scrollHeight;
        }

        // Connexion au serveur Socket.io
        socket = io.connect('http://localhost:8080/teamupchat');

        socket.on('connect', function() {
            console.log('Connecté au serveur de chat');
            // Au chargement : rejoindre le chat
            socket.emit('join-chat', displayname);
            socket.emit('set-status', displayname, 'online');
            socket.emit('has-messages', displayname);
            addmessage('Système', 'Connecté au chat en temps réel', 5);
        });

        socket.on('disconnect', function() {
            console.log('Déconnecté du serveur');
            addmessage('Système', 'Déconnecté du serveur', 5);
        });

        // Réception send-message : afficher dans messagethread
        socket.on('send-message', function(data) {
            addmessage(data.displayname, data.message, 2);
        });

        // Réception join-chat : afficher "X est connecté" en italique
        socket.on('join-chat', function(username) {
            addmessage('Système', username + ' est connecté', 5);
        });

        // Réception private-message : afficher en bleu clair
        socket.on('private-message', function(data) {
            addmessage(data.displayname, data.message, 3);
        });

        // Réception has-messages : si true afficher * rouge dans hasmessage
        socket.on('has-messages', function(hasMsg) {
            var elem = $('#hasmessage');
            if (hasMsg) {
                if (elem.length === 0) {
                    // Ajouter indicateur à la navbar si existe
                    $('.navbar-brand').after('<span id="hasmessage"> *</span>');
                } else {
                    elem.show();
                }
            } else {
                if (elem.length > 0) {
                    elem.hide();
                }
            }
        });

        // Réception get-pending-messages : afficher les messages en attente
        socket.on('get-pending-messages', function(messages) {
            if (messages && messages.length > 0) {
                messages.forEach(function(msg) {
                    addmessage(msg.displayname, msg.message, 4);
                });
            }
        });

        // Réception get-status : afficher dans userstatus
        socket.on('get-status', function(data) {
            console.log('DEBUG: Réponse get-status reçue:', data);
            var statusHtml = '';
            var statusClass = '';
            var statusText = '';
            var statusIcon = '';
            
            if (data.status === 'online') {
                statusClass = 'bg-success text-white';
                statusText = 'En ligne';
                statusIcon = 'fas fa-circle';
                statusHtml = '<div class="d-inline-block px-3 py-2 rounded-pill ' + statusClass + '">' +
                            '<i class="' + statusIcon + ' mr-2"></i>' +
                            '<span>' + statusText + '</span>' +
                            '</div>';
            } else if (data.status === 'donotdisturb') {
                statusClass = 'bg-danger text-white';
                statusText = 'Ne pas déranger';
                statusIcon = 'fas fa-minus-circle';
                statusHtml = '<div class="d-inline-block px-3 py-2 rounded-pill ' + statusClass + '">' +
                            '<i class="' + statusIcon + ' mr-2"></i>' +
                            '<span>' + statusText + '</span>' +
                            '</div>';
            } else {
                statusClass = 'bg-secondary text-white';
                statusText = 'Déconnecté';
                statusIcon = 'fas fa-circle';
                statusHtml = '<div class="d-inline-block px-3 py-2 rounded-pill ' + statusClass + '">' +
                            '<i class="' + statusIcon + ' mr-2"></i>' +
                            '<span>' + statusText + '</span>' +
                            '</div>';
            }
            console.log('DEBUG: Statut HTML généré:', statusHtml);
            $('#userstatus').html(statusHtml);
        });

        // Réception status-update
        socket.on('status-update', function(data) {
            // Optionnel : mettre à jour l'affichage si c'est l'utilisateur sélectionné
        });

        // Clic btnSend
        $('#btnSend').click(function() {
            var message = $('#message').val().trim();
            if (message === '') {
                return;
            }

            var isPrivate = $('#chkPrivate').is(':checked');

            if (!isPrivate) {
                // Discussion générale
                socket.emit('send-message', message);
                // Afficher le message envoyé (côté gauche, fond gris)
                addmessage('Moi', message, 1);
            } else {
                // Discussion privée
                var attendee = $('#id_utilisateur').val();
                var attendeeName = $('#id_utilisateur option:selected').text();
                if (!attendee) {
                    alert('Veuillez choisir un destinataire');
                    return;
                }

                // Vérifier le statut via le serveur avant d'envoyer
                socket.emit('get-status', attendeeName);
                
                // Envoyer le message privé
                var cleanAttendeeName = attendeeName.trim();
                socket.emit('private-message', cleanAttendeeName, message);
                // Afficher le message envoyé (côté gauche, fond gris)
                addmessage('Moi', message, 1);
            }

            // Vider le textarea
            $('#message').val('');
        });

        // Touche Entrée dans le textarea (sans Shift)
        $('#message').keypress(function(e) {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                $('#btnSend').click();
            }
        });

        // Changement liste statut
        $('#status').change(function() {
            var newStatus = $(this).val();
            socket.emit('set-status', displayname, newStatus);
            if (newStatus === 'offline') {
                socket.emit('has-messages', displayname);
            }
        });

        // Changement liste utilisateur : demander son statut
        $('#id_utilisateur').change(function() {
            var attendee = $('#id_utilisateur option:selected').text();
            if (attendee) {
                socket.emit('get-status', attendee);
                currentAttendee = attendee;
            }
        });

        // Basculer entre discussion générale et privée
        $('input[name="chatType"]').change(function() {
            if ($('#chkPrivate').is(':checked')) {
                // Afficher la section de chat privé
                $('#privateChatSection').show();
                $('#id_utilisateur').prop('disabled', false);
                
                // Vérifier le statut de l'utilisateur sélectionné
                var selectedUser = $('#id_utilisateur').val();
                if (selectedUser) {
                    var selectedUserName = $('#id_utilisateur option:selected').text();
                    // Nettoyer le nom des espaces et sauts de ligne
                    selectedUserName = selectedUserName.trim();
                    socket.emit('get-status', selectedUserName);
                }
            } else {
                // Masquer la section de chat privé
                $('#privateChatSection').hide();
                $('#id_utilisateur').prop('disabled', true);
                $('#userstatus').html('<div class="d-inline-block px-3 py-2 rounded-pill bg-light text-muted border">' +
                                   '<i class="fas fa-circle text-muted mr-2"></i>' +
                                   '<span>--</span>' +
                                   '</div>');
            }
        });

        // Vérifier le statut lorsqu'un utilisateur est sélectionné
        $('#id_utilisateur').change(function() {
            var selectedUser = $(this).val();
            if (selectedUser) {
                var selectedUserName = $('#id_utilisateur option:selected').text();
                // Nettoyer le nom des espaces et sauts de ligne
                selectedUserName = selectedUserName.trim();
                console.log('DEBUG: Demande de statut pour:', selectedUserName);
                socket.emit('get-status', selectedUserName);
            } else {
                $('#userstatus').text('--');
            }
        });

        // Gérer le changement de statut
        $('#statusSelector').change(function() {
            var newStatus = $(this).val();
            console.log('DEBUG: Changement de statut vers:', newStatus);
            socket.emit('set-status', displayname, newStatus);
            
            // Si le statut est "offline", déconnecter l'utilisateur
            if (newStatus === 'offline') {
                setTimeout(function() {
                    alert('Vous passez en statut "Déconnecté". Vous pouvez toujours recevoir des messages en attente.');
                }, 500);
            }
        });

        // Plus besoin de gérer le changement de nom - l'utilisateur est authentifié
    </script>
</body>
</html>
