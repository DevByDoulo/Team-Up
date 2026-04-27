// Serveur de messagerie temps réel Team Up
const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const he = require('he');

const app = express();
const server = http.createServer(app);

// Configurer Socket.io avec CORS autorisé pour localhost
const io = socketIo(server, {
  cors: {
    origin: "http://localhost",
    methods: ["GET", "POST"]
  }
});

// Stockage des utilisateurs connectés et de leurs statuts
const users = {};         // displayname -> socket
const statuses = {};      // displayname -> status
const pendingMessages = {}; // displayname -> tableau de messages en attente

// Créer le namespace /teamupchat
const teamupchat = io.of('/teamupchat');

teamupchat.on('connection', (socket) => {
  console.log('Nouvelle connexion au namespace /teamupchat');

  // Gérer l'événement join-chat
  // Stocker le displayname dans socket.displayname
  // Stocker la socket dans users[displayname]
  // Initialiser le statut à "online"
  socket.on('join-chat', (displayname) => {
    socket.displayname = displayname;
    users[displayname] = socket;
    statuses[displayname] = 'online';
    console.log(displayname + ' a rejoint le chat');

    // Broadcaster à tous : join-chat avec le displayname
    teamupchat.emit('join-chat', displayname);
  });

  // Gérer l'événement send-message
  // Encoder le message avec he.encode()
  // Broadcaster à tous les autres : send-message avec { displayname, message }
  socket.on('send-message', (message) => {
    const encodedMessage = he.encode(message);
    const sender = socket.displayname || 'Inconnu';
    console.log(sender + ' : ' + encodedMessage);

    // Broadcaster à tous les autres (broadcast = à tous sauf l'émetteur)
    socket.broadcast.emit('send-message', {
      displayname: sender,
      message: encodedMessage
    });
  });

  // Gérer l'événement private-message
  // Encoder le message
  // Si le destinataire est connecté dans users[], envoyer private-message à sa socket uniquement
  // Sinon stocker dans pendingMessages[attendee]
  socket.on('private-message', (attendee, message) => {
    const encodedMessage = he.encode(message);
    const sender = socket.displayname || 'Inconnu';
    console.log('Message privé de ' + sender + ' à ' + attendee + ' : ' + encodedMessage);

    if (users[attendee]) {
      // Destinataire connecté : envoyer directement
      users[attendee].emit('private-message', {
        displayname: sender,
        message: encodedMessage
      });
    } else {
      // Destinataire non connecté : stocker en attente
      if (!pendingMessages[attendee]) {
        pendingMessages[attendee] = [];
      }
      pendingMessages[attendee].push({
        displayname: sender,
        message: encodedMessage
      });
      console.log('Message en attente pour ' + attendee);
    }
  });

  // Gérer l'événement set-status
  // Mettre à jour statuses[displayname] = status
  // Si status = offline : broadcaster has-messages aux concernés
  socket.on('set-status', (displayname, status) => {
    statuses[displayname] = status;
    console.log(displayname + ' - statut : ' + status);

    if (status === 'offline') {
      // Vérifier si des messages en attente existent
      if (pendingMessages[displayname] && pendingMessages[displayname].length > 0) {
        socket.emit('has-messages', true);
      }
      // Retirer de la liste des connectés
      delete users[displayname];
    }

    // Notifier les autres du changement de statut (optionnel)
    socket.broadcast.emit('status-update', { displayname, status });
  });

  // Gérer l'événement get-status
  // Retourner statuses[attendee] à la socket appelante
  socket.on('get-status', (attendee) => {
    console.log('DEBUG: Recherche de statut pour:', attendee);
    console.log('DEBUG: Utilisateurs connectés:', Object.keys(users));
    console.log('DEBUG: Statuses actuels:', statuses);
    
    const status = statuses[attendee] || 'offline';
    console.log('DEBUG: Statut trouvé pour', attendee, ':', status);
    
    socket.emit('get-status', { attendee, status });
  });

  // Gérer l'événement has-messages
  // Vérifier si pendingMessages[displayname] existe
  // Retourner true/false via has-messages
  socket.on('has-messages', (displayname) => {
    const has = pendingMessages[displayname] && pendingMessages[displayname].length > 0;
    socket.emit('has-messages', has);
  });

  // Gérer l'événement get-pending-messages
  // Envoyer les messages en attente un par un
  // Vider pendingMessages[displayname]
  socket.on('get-pending-messages', (displayname) => {
    if (pendingMessages[displayname] && pendingMessages[displayname].length > 0) {
      pendingMessages[displayname].forEach((msg) => {
        socket.emit('private-message', msg);
      });
      pendingMessages[displayname] = [];
    }
  });

  // Gérer la déconnexion
  socket.on('disconnect', () => {
    if (socket.displayname) {
      console.log(socket.displayname + ' s\'est déconnecté');
      delete users[socket.displayname];
    }
  });
});

// Route GET /index
app.get('/index', (req, res) => {
  res.send('TeamUp Chat Server Running');
});

// Démarrage du serveur
const PORT = 8080;
server.listen(PORT, () => {
  console.log('Serveur TeamUp Chat en écoute sur le port ' + PORT);
  console.log('Namespace /teamupchat actif');
});
