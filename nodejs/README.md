# Lancement du serveur de messagerie

1. Ouvrir un terminal dans le dossier nodejs/
2. Exécuter : npm install
3. Exécuter : node teamupapp.js
4. Le serveur tourne sur http://localhost:8080

## Architecture
- Le serveur utilise Express et Socket.io
- Le namespace `/teamupchat` gère toutes les communications du chat
- Stockage en mémoire des utilisateurs connectés, statuts et messages en attente

## Fonctionnalités
- Discussion générale en temps réel
- Discussion privée entre deux utilisateurs
- Statuts des utilisateurs (online, donotdisturb, offline)
- Envoi de messages hors-ligne (stockés en attente)
- Notification de nouveaux messages

## Utilisation
1. Démarrer Laragon (Apache + MySQL)
2. Démarrer le serveur Node.js (node teamupapp.js)
3. Accéder à l'application : http://localhost/teamup/application/
4. Ouvrir la page Messages dans deux navigateurs différents pour tester